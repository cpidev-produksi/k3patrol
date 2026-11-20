<?php

namespace App\Http\Controllers;

use App\Models\Finding;
use App\Models\FindingVerification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuditorController extends Controller
{
    public function verify(Request $request, Finding $finding): RedirectResponse
    {
        abort_unless(session('active_role') === 'auditor', 403);

        $request->validate(['note' => ['nullable', 'string']]);

        abort_unless(
            $finding->canTransitionTo(Finding::STATUS_CLOSED_VERIFIED),
            422,
            'Temuan harus berstatus Closed sebelum bisa diverifikasi.'
        );

        DB::transaction(function () use ($finding, $request) {
            FindingVerification::create([
                'finding_id' => $finding->id,
                'auditor_id' => Auth::id(),
                'verified_at' => now(),
                'note' => $request->note,
            ]);

            $finding->update(['status' => Finding::STATUS_CLOSED_VERIFIED]);
        });

        return redirect()->route('findings.index')->with('success', "Temuan {$finding->finding_number} telah diverifikasi.");
    }
}
