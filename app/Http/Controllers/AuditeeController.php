<?php

namespace App\Http\Controllers;

use App\Http\Requests\RespondFindingRequest;
use App\Models\Finding;
use App\Models\FindingPhoto;
use App\Models\FindingResponse;
use App\Services\PhotoCompressionService;
use App\Services\PhotoValidationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuditeeController extends Controller
{
    public function __construct(
        protected PhotoValidationService $photoValidation,
        protected PhotoCompressionService $photoCompression,
    )
    {
    }

    public function respond(RespondFindingRequest $request, Finding $finding): RedirectResponse
    {
        abort_unless(session('active_role') === 'auditee', 403);

        $user = Auth::user();
        /** @var \App\Models\User $user */
        abort_unless($user->managesDepartment($finding->department_id), 403, 'Departemen Anda tidak bertanggung jawab atas temuan ini.');

        abort_unless(
            $finding->canTransitionTo($request->status),
            422,
            "Status tidak bisa berubah dari {$finding->status} ke {$request->status}."
        );

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

        return redirect()->route('findings.index')->with('success', 'Tindak lanjut berhasil disimpan.');
    }
}
