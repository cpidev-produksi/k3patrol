<?php

namespace App\Listeners;

use App\Events\FindingCreated;
use App\Events\FindingNearDeadline;
use App\Events\FindingOverdue;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FcmNotification;

class SendFindingNotification implements ShouldQueue
{
    public function __construct(protected Messaging $messaging)
    {
    }

    public function handleFindingCreated(FindingCreated $event): void
    {
        $finding = $event->finding;

        $auditees = User::query()
            ->where('is_auditee', true)
            ->whereHas('departments', fn ($q) => $q->where('departments.id', $finding->department_id))
            ->whereNotNull('fcm_token')
            ->get();

        $this->sendToUsers(
            $auditees,
            'Temuan Baru: ' . $finding->finding_number,
            "Level risiko {$finding->risk_level} — segera tindak lanjuti.",
            ['finding_id' => (string) $finding->id, 'type' => 'finding_created']
        );
    }

    public function handleFindingOverdue(FindingOverdue $event): void
    {
        $finding = $event->finding;

        $this->sendToUsers(
            $this->recipientsFor($finding),
            'Temuan Overdue: ' . $finding->finding_number,
            'Temuan ini sudah melewati target penyelesaian.',
            ['finding_id' => (string) $finding->id, 'type' => 'finding_overdue']
        );
    }

    public function handleFindingNearDeadline(FindingNearDeadline $event): void
    {
        $finding = $event->finding;

        $this->sendToUsers(
            $this->recipientsFor($finding),
            'Mendekati Deadline: ' . $finding->finding_number,
            'Temuan ini akan segera jatuh tempo, mohon segera ditindaklanjuti.',
            ['finding_id' => (string) $finding->id, 'type' => 'finding_near_deadline']
        );
    }

    protected function recipientsFor($finding)
    {
        return User::query()
            ->where(function ($q) use ($finding) {
                $q->where('id', $finding->auditor_id)
                  ->orWhereHas('departments', fn ($sub) => $sub->where('departments.id', $finding->department_id));
            })
            ->whereNotNull('fcm_token')
            ->get();
    }

    protected function sendToUsers($users, string $title, string $body, array $data): void
    {
        foreach ($users as $user) {
            try {
                $message = CloudMessage::new()
                    ->withToken($user->fcm_token)
                    ->withNotification(FcmNotification::create($title, $body))
                    ->withData($data);

                $this->messaging->send($message);
            } catch (\Throwable $e) {
                report($e);
            }
        }
    }

    /**
     * Daftarkan event -> handler mana yang dipanggil.
     */
    public function subscribe($events): array
    {
        return [
            FindingCreated::class => 'handleFindingCreated',
            FindingOverdue::class => 'handleFindingOverdue',
            // PATCH: daftarkan event near-deadline yang baru.
            FindingNearDeadline::class => 'handleFindingNearDeadline',
        ];
    }
}