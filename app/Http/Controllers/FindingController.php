<?php

namespace App\Http\Controllers;

use App\Events\FindingCreated;
use App\Http\Requests\StoreFindingRequest;
use App\Models\Category;
use App\Models\Department;
use App\Models\Finding;
use App\Models\FindingPhoto;
use App\Services\PhotoCompressionService;
use App\Services\PhotoValidationService;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class FindingController extends Controller
{
    /**
     * Disk tempat foto temuan disimpan. Samakan dengan disk yang dipakai PhotoCompressionService.
     */
    protected const PHOTO_DISK = 'public';

    public function __construct(
        protected PhotoValidationService $photoValidation,
        protected PhotoCompressionService $photoCompression,
    )
    {
    }

    /**
     * Daftar temuan + filter. Auditee hanya melihat temuan dari departemennya.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        /** @var \App\Models\User $user */
        $role = session('active_role');

        $query = Finding::query()->with(['department', 'category', 'auditor']);

        if ($role === 'auditee') {
            $departmentIds = $user->departments()->pluck('departments.id')->toArray();
            $query->forDepartments($departmentIds);
        }

        $findings = $query->filter($request->only([
            'status', 'risk_level', 'department_id', 'category_id', 'date_from', 'date_to',
        ]))->latest()->paginate(20)->withQueryString();

        $departments = Department::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('findings.index', compact('findings', 'departments', 'categories'));
    }

    public function create(): View
    {
        abort_unless(session('active_role') === 'auditor', 403);

        $departments = Department::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('findings.create', compact('departments', 'categories'));
    }

    public function store(StoreFindingRequest $request): RedirectResponse
    {
        abort_unless(session('active_role') === 'auditor', 403);

        $photoMeta = $this->photoValidation->validateOrFail($request->file('photo'));

        $finding = DB::transaction(function () use ($request, $photoMeta) {
            $finding = Finding::create([
                ...$request->safe()->except('photo'),
                'auditor_id' => Auth::id(),
            ]);

            $path = $this->photoCompression->store($request->file('photo'));

            FindingPhoto::create([
                'finding_id' => $finding->id,
                'type' => FindingPhoto::TYPE_TEMUAN,
                'path' => $path,
                'taken_at' => $photoMeta['taken_at'],
                'file_hash' => $photoMeta['hash'],
                'uploaded_by' => Auth::id(),
            ]);

            return $finding;
        });

        event(new FindingCreated($finding));

        return redirect()->route('findings.index')->with('success', "Temuan {$finding->finding_number} berhasil dibuat.");
    }

    /**
     * AJAX endpoint untuk modal detail (dipanggil via fetch, return partial HTML).
     */
    public function detailModal(Finding $finding): string
    {
        $finding->load(['photos', 'response', 'verification', 'department', 'category', 'auditor']);
        return view('findings.partials.detail-modal', compact('finding'))->render();
    }

    /**
     * Hapus temuan — khusus admin (route juga dibatasi middleware active.role:admin).
     * Jika model Finding memakai SoftDeletes, data turunan & file foto dipertahankan sebagai jejak audit.
     */
    public function destroy(Finding $finding): RedirectResponse
    {
        abort_unless(session('active_role') === 'admin', 403);

        $number = $finding->finding_number;
        $softDeletes = in_array(SoftDeletes::class, class_uses_recursive($finding), true);
        $photoPaths = $softDeletes ? [] : $finding->photos()->pluck('path')->filter()->all();

        try {
            DB::transaction(function () use ($finding, $softDeletes) {
                if (! $softDeletes) {
                    $finding->photos()->delete();
                    $finding->response()->delete();
                    $finding->verification()->delete();
                }

                $finding->delete();
            });
        } catch (QueryException $e) {
            report($e);

            return back(fallback: route('findings.index'))
                ->with('error', "Temuan {$number} tidak dapat dihapus karena masih terhubung dengan data lain.");
        }

        // File fisik dihapus setelah transaksi sukses; kegagalan hapus file tidak membatalkan penghapusan data.
        foreach ($photoPaths as $path) {
            try {
                Storage::disk(self::PHOTO_DISK)->delete($path);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back(fallback: route('findings.index'))->with('success', "Temuan {$number} berhasil dihapus.");
    }
}