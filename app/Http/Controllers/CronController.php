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

            Log::info("Cron-job.org triggered 'ebs:send-due-reminders' successfully:\n" . $output);

            return response()->json([
                'status' => 'success',
                'message' => 'Due reminder and overdue notifications processed successfully.',
                'timestamp' => now()->toDateTimeString(),
                'timezone' => config('app.timezone'),
                'output' => $output,
            ]);
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
