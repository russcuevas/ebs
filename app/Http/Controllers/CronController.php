<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class CronController extends Controller
{
    /**
     * Webhook endpoint to trigger overdue & due reminder emails from cron-job.org or external scheduler.
     */
    public function sendDueReminders(Request $request): JsonResponse
    {
        // Optional secret key validation if CRON_SECRET is set in .env
        $configuredSecret = config('app.cron_secret', env('CRON_SECRET'));
        if (!empty($configuredSecret)) {
            $providedSecret = $request->query('key')
                ?? $request->query('token')
                ?? $request->header('X-Cron-Key')
                ?? $request->bearerToken();

            if ($providedSecret !== $configuredSecret) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Unauthorized cron trigger request. Invalid or missing secret key.',
                ], 401);
            }
        }

        try {
            // Run the EBS reminder console command
            Artisan::call('ebs:send-due-reminders');
            $output = trim(Artisan::output());

            Log::info("Cron webhook triggered 'ebs:send-due-reminders':\n" . $output);

            // Extract reminder counts from command output
            preg_match('/Reminders sent:\s*(\d+)/i', $output, $mReminders);
            preg_match('/Overdue notices sent:\s*(\d+)/i', $output, $mOverdue);

            $remindersSent = isset($mReminders[1]) ? (int) $mReminders[1] : 0;
            $overdueSent = isset($mOverdue[1]) ? (int) $mOverdue[1] : 0;
            $totalSent = $remindersSent + $overdueSent;

            $message = $totalSent > 0
                ? "Processed successfully. Dispatched {$remindersSent} reminder(s) and {$overdueSent} overdue notice(s)."
                : "Processed successfully. No due or overdue reminders to send at this time (queue empty).";

            return response()->json([
                'status' => 'success',
                'empty' => $totalSent === 0,
                'message' => $message,
                'data' => [
                    'reminders_sent' => $remindersSent,
                    'overdue_notices_sent' => $overdueSent,
                    'total_sent' => $totalSent,
                ],
                'timestamp' => now()->toDateTimeString(),
                'timezone' => config('app.timezone'),
                'output' => $output,
            ], 200);
        } catch (\Throwable $e) {
            Log::error('Error executing cron reminder trigger: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process reminders: ' . $e->getMessage(),
                'timestamp' => now()->toDateTimeString(),
            ], 500);
        }
    }
}
