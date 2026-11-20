<?php

namespace App\Http\Controllers\Api;

use App\Events\FindingCreated;
use App\Http\Controllers\Controller;
use App\Http\Requests\RespondFindingRequest;
use App\Http\Requests\StoreFindingRequest;
use App\Http\Resources\FindingResource;
use App\Models\Finding;
use App\Models\FindingPhoto;
use App\Models\FindingResponse;
use App\Models\FindingVerification;
use App\Services\PhotoCompressionService;
use App\Services\PhotoValidationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FindingController extends Controller
{
    public function __construct(
        protected PhotoValidationService $photoValidation,
        protected PhotoCompressionService $photoCompression,
    )
    {
    }

    public function index(Request $request): JsonResponse
    {
        $findings = $this->scopedQuery($request)
            ->filter($request->only(['status', 'risk_level', 'department_id', 'category_id']))
            ->latest()
            ->paginate(20);

        return FindingResource::collection($findings)->response();
    }

    public function show(Finding $finding): JsonResponse
    {
        $finding->load(['department', 'category', 'auditor', 'photos', 'response.auditee', 'verification.auditor', 'reopenedBy']);
        return (new FindingResource($finding))->response();
    }

    public function summary(Request $request): JsonResponse
    {
        $base = $this->scopedQuery($request);

        $counts = (clone $base)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $overdue = (clone $base)->overdue()->count();
        $nearDeadline = (clone $base)->nearDeadline()->count();

        return response()->json([
            'data' => [
                'open' => (int) ($counts[Finding::STATUS_OPEN] ?? 0),
                'on_process' => (int) ($counts[Finding::STATUS_ON_PROCESS] ?? 0),
                'closed' => (int) ($counts[Finding::STATUS_CLOSED] ?? 0),
                'closed_verified' => (int) ($counts[Finding::STATUS_CLOSED_VERIFIED] ?? 0),
                'overdue' => $overdue,
                'near_deadline' => $nearDeadline,
            ],
        ]);
    }

    public function store(StoreFindingRequest $request): JsonResponse
    {
        $photoMeta = $this->photoValidation->validateOrFail($request->file('photo'));

        $finding = DB::transaction(function () use ($request, $photoMeta) {
            $finding = Finding::create([
                ...$request->safe()->except('photo'),
                'auditor_id' => $request->user()->id,
            ]);

            $path = $this->photoCompression->store($request->file('photo'));

            FindingPhoto::create([
                'finding_id' => $finding->id,
                'type' => FindingPhoto::TYPE_TEMUAN,
                'path' => $path,
                'taken_at' => $photoMeta['taken_at'],
                'file_hash' => $photoMeta['hash'],
                'uploaded_by' => $request->user()->id,
            ]);

            return $finding;
        });

        event(new FindingCreated($finding));

        return (new FindingResource($finding->load(['department', 'category', 'auditor', 'photos'])))
            ->response()
            ->setStatusCode(201);
    }

    public function respond(RespondFindingRequest $request, Finding $finding): JsonResponse
    {
        $user = $request->user();
        abort_unless($user->managesDepartment($finding->department_id), 403);
        abort_unless($finding->canTransitionTo($request->status), 422, 'Transisi status tidak valid.');

        $photoMeta = $this->photoValidation->validateOrFail($request->file('photo'), $finding->id);

        DB::transaction(function () use ($request, $finding, $photoMeta, $user) {
            $path = $this->photoCompression->store($request->file('photo'));

            FindingPhoto::create([
                'finding_id' => $finding->id,
                'type' => FindingPhoto::TYPE_PERBAIKAN,
                'path' => $path,
                'taken_at' => $photoMeta['taken_at'],
                'file_hash' => $photoMeta['hash'],
                'uploaded_by' => $user->id,
            ]);

            FindingResponse::create([
                'finding_id' => $finding->id,
                'auditee_id' => $user->id,
                'description_perbaikan' => $request->description,
                'status_baru' => $request->status,
                'responded_at' => now(),
            ]);

            $finding->update(['status' => $request->status]);
        });

        return (new FindingResource($finding->fresh(['department', 'category', 'auditor', 'photos', 'response.auditee'])))->response();
    }

    /**
     * PATCH: sekarang menangani dua aksi:
     *  - action=verify  -> Closed menjadi Closed Verified (final, tercatat di finding_verifications)
     *  - action=reopen  -> Closed dikembalikan ke On Process (tindak lanjut dinilai belum tuntas)
     *
     * Hanya Auditor pembuat temuan yang boleh melakukan ini (dicek lewat
     * middleware active.role.api:auditor + pengecekan kepemilikan di bawah).
     */
    public function verify(Request $request, Finding $finding): JsonResponse
    {
        $request->validate([
            'action' => ['required', Rule::in(['verify', 'reopen'])],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        abort_unless($finding->auditor_id === $request->user()->id, 403, 'Hanya Auditor pembuat temuan ini yang dapat memverifikasi.');

        if ($request->action === 'verify') {
            abort_unless($finding->canTransitionTo(Finding::STATUS_CLOSED_VERIFIED), 422, 'Temuan harus berstatus Closed terlebih dahulu.');

            DB::transaction(function () use ($finding, $request) {
                FindingVerification::create([
                    'finding_id' => $finding->id,
                    'auditor_id' => $request->user()->id,
                    'verified_at' => now(),
                    'note' => $request->note,
                ]);

                $finding->update(['status' => Finding::STATUS_CLOSED_VERIFIED]);
            });
        } else {
            abort_unless($finding->status === Finding::STATUS_CLOSED, 422, 'Hanya temuan berstatus Closed yang dapat dibuka kembali.');

            $finding->update([
                'status' => Finding::STATUS_ON_PROCESS,
                'reopen_note' => $request->note,
                'reopened_at' => now(),
                'reopened_by' => $request->user()->id,
                // PATCH: reset flag notifikasi agar siklus SLA baru bisa memicu
                // notifikasi near-deadline/overdue lagi jika diperlukan.
                'near_deadline_notified_at' => null,
                'overdue_notified_at' => null,
            ]);
        }

        return (new FindingResource(
            $finding->fresh(['department', 'category', 'auditor', 'photos', 'response.auditee', 'verification.auditor', 'reopenedBy'])
        ))->response();
    }

    /**
     * Query dasar yang scoping-nya konsisten dipakai index() & summary().
     */
    protected function scopedQuery(Request $request)
    {
        $user = $request->user();
        $activeRole = $request->header('X-Active-Role');

        $query = Finding::query()->with(['department', 'category', 'auditor', 'photos']);

        if ($activeRole === 'auditee') {
            $departmentIds = $user->departments()->pluck('departments.id')->toArray();
            $query->forDepartments($departmentIds);
        } elseif ($activeRole === 'auditor') {
            $query->where('auditor_id', $user->id);
        }

        return $query;
    }
}