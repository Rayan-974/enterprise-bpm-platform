<?php

namespace App\Services;

use App\Models\User;
use App\Models\WorkflowNotification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    /**
     * Dispatch multi-channel notification (In-App, Email, SMS Simulation, Slack/Webhook).
     */
    public function send(User $user, string $title, string $message, string $type = 'workflow_action', array $metadata = []): WorkflowNotification
    {
        // 1. In-App Notification Record
        $notification = WorkflowNotification::create([
            'user_id' => $user->id,
            'type' => $type,
            'channel' => 'in_app',
            'title' => $title,
            'message' => $message,
            'metadata' => $metadata,
        ]);

        // 2. Email Notification Dispatch
        $this->sendEmail($user, $title, $message);

        // 3. SMS Simulation Dispatch
        $this->sendSms($user, $message);

        // 4. Slack / Webhook Dispatch
        $this->sendSlackWebhook($title, $message, $metadata);

        return $notification;
    }

    /**
     * Email Channel Dispatcher.
     */
    public function sendEmail(User $user, string $title, string $message): void
    {
        Log::info("Notification [EMAIL -> {$user->email}]: {$title} - {$message}");
    }

    /**
     * SMS Channel Simulation Dispatcher.
     */
    public function sendSms(User $user, string $message): void
    {
        $phone = $user->phone ?? '+1-555-0192';
        Log::info("Notification [SMS SIMULATION -> {$phone}]: {$message}");
    }

    /**
     * Slack / Webhook Notification Integration.
     */
    public function sendSlackWebhook(string $title, string $message, array $metadata = []): void
    {
        $webhookUrl = config('services.slack.webhook_url');
        if ($webhookUrl) {
            try {
                Http::post($webhookUrl, [
                    'text' => "*{$title}*\n{$message}",
                ]);
            } catch (\Exception $e) {
                Log::warning("Slack Webhook failed: " . $e->getMessage());
            }
        } else {
            Log::info("Notification [SLACK/WEBHOOK SIMULATION]: *{$title}* - {$message}");
        }
    }

    public function markAsRead(int $notificationId): bool
    {
        $notification = WorkflowNotification::find($notificationId);
        if ($notification) {
            $notification->update(['read_at' => now()]);
            return true;
        }
        return false;
    }
}
