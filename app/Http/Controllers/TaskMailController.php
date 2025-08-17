<?php

namespace App\Http\Controllers;

use App\Models\BasicApplication;
use App\Models\ResumePrompt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class TaskMailController extends Controller
{
    public function sendByApplication($applicationOrId): array
    {
        try {
            $application = $applicationOrId instanceof BasicApplication
                ? $applicationOrId
                : BasicApplication::findOrFail($applicationOrId);

            Log::info('[TASK_MAIL] Start', [
                'app_id'      => $application->id ?? null,
                'candidate_id'=> $application->candidate_id,
                'email'       => $application->email,
                'job_id'      => $application->job_id,
                'job_role'    => $application->job_role,
            ]);

            if (!filled($application->email) || !filled($application->job_id)) {
                Log::warning('[TASK_MAIL] Missing email or job_id', [
                    'email'  => $application->email,
                    'job_id' => $application->job_id,
                ]);
                throw ValidationException::withMessages([
                    'application' => 'Missing email or job_id on this application.',
                ]);
            }

            // Fetch resume prompt for job
            $rp = ResumePrompt::where('job_id', $application->job_id)->first();
            Log::info('[TASK_MAIL] ResumePrompt fetched', [
                'found'     => (bool) $rp,
                'task_link' => $rp?->task_link,
                'paths'     => $rp?->task_file_path,
                'names'     => $rp?->task_file_name,
            ]);

            // Build input_files (relative public paths)
            $inputFiles = [];
            if ($rp) {
                $items = $rp->fileItems();
                Log::info('[TASK_MAIL] fileItems()', ['count' => count($items)]);

                foreach ($items as $idx => $item) {
                    $urlPath  = parse_url($item['url'] ?? '', PHP_URL_PATH) ?? '';
                    if (! $urlPath) {
                        Log::warning('[TASK_MAIL] Skip file: empty URL path', ['item' => $item]);
                        continue;
                    }

                    $relativeStorage = ltrim(str_replace('/storage/', '', $urlPath), '/');
                    $src = storage_path('app/public/' . $relativeStorage);

                    if (! is_file($src)) {
                        Log::warning('[TASK_MAIL] Skip file: source missing', ['src' => $src]);
                        continue;
                    }

                    $destDir = public_path('static/TaskFilesList/' . $application->job_id);
                    if (! is_dir($destDir)) {
                        File::makeDirectory($destDir, 0755, true);
                        Log::info('[TASK_MAIL] Created destination directory', ['destDir' => $destDir]);
                    }

                    $destName = basename($src);
                    $dest     = $destDir . DIRECTORY_SEPARATOR . $destName;
                    File::copy($src, $dest);
                    Log::info('[TASK_MAIL] Copied file', ['src' => $src, 'dest' => $dest]);

                    $publicPath = 'static/TaskFilesList/' . $application->job_id . '/' . $destName;
                    $inputFiles[] = ['path' => $publicPath];

                    Log::info('[TASK_MAIL] File added to payload', [
                        'index'      => $idx,
                        'publicPath' => $publicPath,
                    ]);
                }
            }

              // Build submission link (env-aware)
            $submissionLink = app()->environment('local')
                ? 'http://127.0.0.1:8000/candidate/submit-task'
                : rtrim(env('FRONTEND_URL', config('app.url')), '/') . '/candidate/submit-task';

            // Build payload
            $payload = [
                'email'        => (string) $application->email,
                'job_role'     => (string) ($application->job_role ?? ''),
                'candidate_id' => (string) ($application->candidate_id ?? ''),
                'input_files'  => $inputFiles,
                'link'         => $rp?->task_link ?: null,
                'submission_link'  => $submissionLink,            // NEW: candidate task submission page
            ];

            Log::info('[TASK_MAIL] Final payload preview', [
                'endpoint'    => config('services.task_mail.url', 'https://aicew.fibrebondindustries.com/send-task-mail'),
                'input_files' => $inputFiles,
                'link'        => $payload['link'],
                'submission_link'  => $submissionLink,

            ]);

            // Send to API
            $headers = [];
            if ($token = config('services.task_mail.token')) {
                $headers['Authorization'] = 'Bearer ' . $token;
            }

            $response = Http::withOptions(['verify' => false])
                ->withHeaders($headers)
                ->post(config('services.task_mail.url', 'https://aicew.fibrebondindustries.com/send-task-mail'), $payload);

            Log::info('[TASK_MAIL] API response', [
                'status' => $response->status(),
                'ok'     => $response->successful(),
                'body'   => mb_substr($response->body(), 0, 1000),
            ]);

            if ($response->successful()) {
                // Mark mail as sent
                $application->update(['mail_sent' => true]);
                Log::info('[TASK_MAIL] Done OK');
                return ['ok' => true, 'payload' => $payload, 'data' => $response->json()];
            }

            throw ValidationException::withMessages([
                'api' => 'Send mail API failed. HTTP ' . $response->status() . ' — ' . mb_substr($response->body(), 0, 600),
            ]);
        } catch (\Throwable $e) {
            Log::error('[TASK_MAIL] Exception', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);
            throw $e;
        }
    }

    public function send(Request $request)
    {
        $request->validate([
            'application_id' => ['required', 'integer', 'exists:basic_applications,id'],
        ]);

        $result = $this->sendByApplication((int) $request->application_id);

        return response()->json([
            'status'  => $result['ok'] ? 'success' : 'error',
            'message' => $result['ok'] ? 'Email sent successfully.' : 'Failed.',
            'data'    => $result,
        ]);
    }
}
