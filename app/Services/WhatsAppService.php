<?php

namespace App\Services;

use App\Models\Issue;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Standardize phone number to WhatsApp international format (e.g. 6281234567890).
     */
    public function formatPhoneNumber(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // Strip non-digit characters
        $digits = preg_replace('/[^\d]/', '', $phone);

        if (empty($digits)) {
            return null;
        }

        // Convert Indonesian local formats (08... or 8...) to 628...
        if (str_starts_with($digits, '08')) {
            $digits = '628' . substr($digits, 2);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '628' . substr($digits, 1);
        }

        // Validate length (typical international phone is between 10 and 15 digits)
        if (strlen($digits) < 9 || strlen($digits) > 16) {
            return null;
        }

        return $digits;
    }

    /**
     * Generate 1-Click WhatsApp Direct Link (wa.me)
     */
    public function generateWhatsAppUrl(string $phone, string $message): string
    {
        $cleanPhone = $this->formatPhoneNumber($phone) ?? preg_replace('/[^\d]/', '', $phone);
        return 'https://wa.me/' . $cleanPhone . '?text=' . rawurlencode($message);
    }

    /**
     * Build contextual template messages for project follow-ups.
     */
    public function buildMessage(User $assignee, Issue $issue, string $type = 'reminder'): string
    {
        $projectName = $issue->project ? $issue->project->name : 'Proyek';
        $dueDateText = $issue->due_date ? $issue->due_date->format('d M Y') : 'Belum ditentukan';
        $statusText = $issue->status ? $issue->status->label() : 'To Do';
        $priorityText = $issue->priority ? $issue->priority->label() : 'Medium';

        $baseUrl = config('app.url', url('/'));
        $issueUrl = $issue->project
            ? "{$baseUrl}/projects/{$issue->project->code}/board"
            : $baseUrl;

        return match ($type) {
            'assigned' => "Halo *{$assignee->name}* 👋\n\nAnda baru saja ditugaskan pada issue baru di proyek *{$projectName}*:\n📌 *[{$issue->issue_key}] {$issue->title}*\n🎯 Prioritas: *{$priorityText}*\n📅 Tenggat Waktu: *{$dueDateText}*\n\nSilakan cek papan kerja Anda:\n🔗 {$issueUrl}\n\nSelamat bekerja dan sukses selalu!",

            'overdue' => "Halo *{$assignee->name}* 👋\n\nKami mendeteksi tugas Anda di proyek *{$projectName}* sudah melewati estimasi tenggat waktu namun statusnya masih *{$statusText}*:\n📌 *[{$issue->issue_key}] {$issue->title}*\n📅 Batas Selesai: *{$dueDateText}*\n\nApakah ada kendala teknis atau blocker yang membutuhkan bantuan tim? Mohon konfirmasinya ya.\n\nTerima kasih atas kerja samanya! 🙏",

            'unresponsive' => "Halo *{$assignee->name}* 🙏\n\nMohon konfirmasi dan kabarnya terkait tugas *[{$issue->issue_key}] {$issue->title}* di *{$projectName}*.\nKami mencoba menghubungi Anda untuk pembaruan status proyek. Jika ada kendala, jangan ragu untuk berbagi dengan tim ya. Mohon respon saat luang, terima kasih!",

            default => "Halo *{$assignee->name}* 👋\n\nPengingat tenggat waktu untuk tugas Anda di *{$projectName}*:\n📌 *[{$issue->issue_key}] {$issue->title}*\n⏱ Status Saat Ini: *{$statusText}*\n📅 Jatuh Tempo: *{$dueDateText}*\n\nMohon lakukan pembaruan status jika ada progres pengerjaan ya. Semangat!\n🔗 {$issueUrl}",
        };
    }

    /**
     * Send message automatically via WhatsApp Gateway if configured.
     */
    public function sendViaGateway(string $phone, string $message): array
    {
        $cleanPhone = $this->formatPhoneNumber($phone);
        if (!$cleanPhone) {
            return [
                'success' => false,
                'message' => 'Nomor WhatsApp tidak valid atau kosong.',
            ];
        }

        $settings = $this->getSettings();

        if (empty($settings['whatsapp_gateway_enabled']) || empty($settings['whatsapp_api_token'])) {
            return [
                'success' => false,
                'message' => 'WhatsApp Gateway belum diaktifkan atau API token belum diisi di Pengaturan Aplikasi.',
            ];
        }

        $provider = $settings['whatsapp_gateway_provider'] ?? 'fonnte';
        $token = $settings['whatsapp_api_token'];

        try {
            if ($provider === 'fonnte') {
                $response = Http::timeout(10)->withHeaders([
                    'Authorization' => $token,
                ])->post('https://api.fonnte.com/send', [
                    'target' => $cleanPhone,
                    'message' => $message,
                    'countryCode' => '62',
                ]);

                $json = $response->json();
                $isSuccess = $response->successful() && is_array($json) && !empty($json['status']);

                if ($isSuccess) {
                    return [
                        'success' => true,
                        'message' => 'Pesan WhatsApp berhasil dikirim via Fonnte.',
                        'data' => $json,
                    ];
                }

                $reason = is_array($json) ? ($json['reason'] ?? $json['message'] ?? json_encode($json)) : $response->body();
                return [
                    'success' => false,
                    'message' => 'Gagal mengirim pesan via Fonnte: ' . $reason,
                    'data' => is_array($json) ? $json : null,
                ];
            }

            if ($provider === 'custom_webhook') {
                $webhookUrl = $settings['whatsapp_webhook_url'] ?? '';
                if (empty($webhookUrl)) {
                    return [
                        'success' => false,
                        'message' => 'Webhook URL belum diisi.',
                    ];
                }

                $response = Http::timeout(10)->withHeaders([
                    'Authorization' => 'Bearer ' . $token,
                    'Content-Type' => 'application/json',
                ])->post($webhookUrl, [
                    'target' => $cleanPhone,
                    'phone' => $cleanPhone,
                    'message' => $message,
                ]);

                return [
                    'success' => $response->successful(),
                    'message' => $response->successful() ? 'Pesan terkirim ke Webhook.' : 'Webhook error: ' . $response->body(),
                ];
            }

            return [
                'success' => false,
                'message' => "Provider '{$provider}' tidak didukung.",
            ];
        } catch (\Throwable $e) {
            Log::error('WhatsApp Gateway Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Gagal menghubungi WhatsApp Gateway: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Retrieve WhatsApp settings from storage.
     */
    protected function getSettings(): array
    {
        $settingsFile = storage_path('app/app_settings.json');
        if (file_exists($settingsFile)) {
            $data = json_decode(file_get_contents($settingsFile), true);
            if (is_array($data)) {
                return $data;
            }
        }
        return [];
    }
}
