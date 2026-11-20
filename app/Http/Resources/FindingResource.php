<?php

namespace App\Http\Resources;

use App\Models\Finding;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FindingResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'finding_number' => $this->finding_number,
            'description' => $this->description,
            'area_detail' => $this->area_detail,
            'risk_level' => $this->risk_level,
            'status' => $this->status,
            'due_at' => $this->due_at?->toIso8601String(),
            'is_overdue' => $this->isOverdue(),
            'is_near_deadline' => $this->isNearDeadline(),
            'created_at' => $this->created_at?->toIso8601String(),
            'auditor' => [
                'id' => $this->auditor?->id,
                'name' => $this->auditor?->name,
            ],
            'department' => [
                'id' => $this->department?->id,
                'name' => $this->department?->name,
            ],
            'category' => $this->category
                ? ['id' => $this->category->id, 'name' => $this->category->name]
                : ['id' => null, 'name' => $this->category_other],
            'photos' => FindingPhotoResource::collection($this->whenLoaded('photos')),
            'response' => $this->whenLoaded('response', fn () => $this->response ? [
                'auditee' => [
                    'id' => $this->response->auditee?->id,
                    'name' => $this->response->auditee?->name,
                ],
                'description_perbaikan' => $this->response->description_perbaikan,
                'status_baru' => $this->response->status_baru,
                'responded_at' => $this->response->responded_at?->toIso8601String(),
            ] : null),
            // gabungan info verifikasi ATAU reopen terakhir (lihat buildVerificationBlock()).
            'verification' => $this->buildVerificationBlock(),
            // field mentah juga disertakan untuk kompatibilitas/keperluan riwayat sederhana.
            'reopen_note' => $this->reopen_note,
            'reopened_at' => $this->reopened_at?->toIso8601String(),
        ];
    }

    protected function buildVerificationBlock(): ?array
    {
        if ($this->status === Finding::STATUS_CLOSED_VERIFIED
            && $this->relationLoaded('verification')
            && $this->verification) {
            return [
                'auditor' => [
                    'id' => $this->verification->auditor?->id,
                    'name' => $this->verification->auditor?->name,
                ],
                'action' => 'verified',
                'verified_at' => $this->verification->verified_at?->toIso8601String(),
                'note' => $this->verification->note,
            ];
        }

        if ($this->reopened_at) {
            return [
                'auditor' => $this->relationLoaded('reopenedBy') ? [
                    'id' => $this->reopenedBy?->id,
                    'name' => $this->reopenedBy?->name,
                ] : null,
                'action' => 'reopened',
                'verified_at' => $this->reopened_at?->toIso8601String(),
                'note' => $this->reopen_note,
            ];
        }

        return null;
    }
}