<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\GitHubWebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GitHubWebhookController extends Controller
{
    public function __construct(
        protected GitHubWebhookService $webhookService
    ) {}

    /**
     * Handle project-specific GitHub webhook endpoint.
     * Route: POST /api/projects/{project}/github/webhook
     */
    public function handleProject(Request $request, Project $project): JsonResponse
    {
        $rawPayload = $request->getContent();
        $signatureHeader = $request->header('X-Hub-Signature-256');
        $event = $request->header('X-GitHub-Event', 'pull_request');

        // Verify HMAC signature if project has secret configured
        if (!$this->webhookService->verifySignature($rawPayload, $signatureHeader, $project->github_webhook_secret)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid GitHub webhook signature.',
            ], 403);
        }

        $payload = $request->json()->all();

        $result = $this->webhookService->handleWebhook($payload, $event, $project);

        $statusCode = isset($result['code']) ? $result['code'] : ($result['success'] ? 200 : 400);

        return response()->json($result, $statusCode);
    }

    /**
     * Handle global GitHub webhook endpoint.
     * Route: POST /api/github/webhook
     */
    public function handleGlobal(Request $request): JsonResponse
    {
        $payload = $request->json()->all();
        $repoFullName = $payload['repository']['full_name'] ?? null;
        $event = $request->header('X-GitHub-Event', 'pull_request');

        if (!$repoFullName) {
            return response()->json([
                'success' => false,
                'message' => 'Repository full_name is required in payload.',
            ], 400);
        }

        $project = Project::where('github_repo', $repoFullName)->first();

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => "No project linked to repository: {$repoFullName}",
            ], 404);
        }

        $rawPayload = $request->getContent();
        $signatureHeader = $request->header('X-Hub-Signature-256');

        if (!$this->webhookService->verifySignature($rawPayload, $signatureHeader, $project->github_webhook_secret)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid GitHub webhook signature.',
            ], 403);
        }

        $result = $this->webhookService->handleWebhook($payload, $event, $project);

        $statusCode = isset($result['code']) ? $result['code'] : ($result['success'] ? 200 : 400);

        return response()->json($result, $statusCode);
    }
}
