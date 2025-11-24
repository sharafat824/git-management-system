<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Repository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GitHubWebhookController extends Controller
{
    public function handle(Request $request): JsonResponse
    {
        $event = $request->header('X-GitHub-Event');
        $payload = $request->all();

        Log::info("=== GITHUB WEBHOOK RECEIVED ===");
        Log::info("Event: {$event}");
        Log::info("Action: " . ($payload['action'] ?? 'N/A'));

        try {
            switch ($event) {
                case 'ping':
                    $this->handlePingEvent($payload);
                    break;
                case 'repository':
                    $this->handleRepositoryEvent($payload);
                    break;
                case 'create':
                    $this->handleCreateEvent($payload);
                    break;
                case 'push':
                    $this->handlePushEvent($payload);
                    break;
                default:
                    Log::warning("Unhandled GitHub event type: {$event}");
            }

            return response()->json(['status' => 'success', 'message' => 'Webhook processed']);

        } catch (\Exception $e) {
            Log::error("Webhook processing failed: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    private function handlePingEvent(array $payload): void
    {
        Log::info("Ping event received - Webhook is active");

        // Auto-create repository from ping event if it doesn't exist
        if (isset($payload['repository'])) {
            $repo = $payload['repository'];
            Log::info("Repository info in ping: {$repo['full_name']}");

            $this->syncRepository($repo);
            Log::info("Repository auto-added from ping: {$repo['full_name']}");
        }
    }

    private function handleRepositoryEvent(array $payload): void
    {
        $action = $payload['action'] ?? 'unknown';
        Log::info("Repository event - Action: {$action}");

        if ($action === 'created') {
            $this->syncRepository($payload['repository']);
        } else {
            Log::info("Repository action '{$action}' not handled");
        }
    }

    private function handleCreateEvent(array $payload): void
    {
        $refType = $payload['ref_type'] ?? 'unknown';
        Log::info("Create event - Ref Type: {$refType}");

        if ($refType === 'branch') {
            $repoId = $payload['repository']['id'] ?? null;
            $branchName = $payload['ref'] ?? 'unknown';

            Log::info("Branch creation - Repo ID: {$repoId}, Branch: {$branchName}");

            // First, ensure the repository exists
            $repository = $this->ensureRepositoryExists($payload['repository']);

            if ($repository) {
                Branch::updateOrCreate(
                    [
                        'repository_id' => $repository->id,
                        'name' => $branchName,
                    ],
                    [
                        'github_sha' => $payload['master_commit'] ?? $payload['before'] ?? 'unknown',
                        'created_by' => $payload['sender']['login'] ?? 'unknown',
                        'source_branch' => $this->determineSourceBranch($payload),
                        'linked_task' => $this->extractLinkedTask($branchName),
                    ]
                );

                Log::info("Branch created successfully: {$branchName}");
            } else {
                Log::warning("Failed to create repository for GitHub ID: {$repoId}");
            }
        }
    }

    private function handlePushEvent(array $payload): void
    {
        $ref = $payload['ref'] ?? 'unknown';
        Log::info("Push event - Ref: {$ref}");

        if (str_starts_with($ref, 'refs/heads/')) {
            $branchName = str_replace('refs/heads/', '', $ref);
            $repoId = $payload['repository']['id'] ?? null;

            Log::info("Branch push - Repo ID: {$repoId}, Branch: {$branchName}");

            // First, ensure the repository exists
            $repository = $this->ensureRepositoryExists($payload['repository']);

            if ($repository) {
                $branch = Branch::where('repository_id', $repository->id)
                    ->where('name', $branchName)
                    ->first();

                if ($branch) {
                    $branch->update([
                        'github_sha' => $payload['after'] ?? 'unknown',
                    ]);
                    Log::info("Branch updated: {$branchName}");
                } else {
                    // Create the branch if it doesn't exist
                    Branch::create([
                        'repository_id' => $repository->id,
                        'name' => $branchName,
                        'github_sha' => $payload['after'] ?? 'unknown',
                        'created_by' => $payload['pusher']['name'] ?? $payload['sender']['login'] ?? 'unknown',
                        'source_branch' => $this->determineSourceBranch($payload),
                        'linked_task' => $this->extractLinkedTask($branchName),
                    ]);
                    Log::info("Branch created from push: {$branchName}");
                }
            }
        }
    }

    /**
     * Ensure repository exists in database, create if it doesn't
     */
    private function ensureRepositoryExists(array $repoData): ?Repository
    {
        $repository = Repository::where('github_id', $repoData['id'])->first();

        if (!$repository) {
            Log::info("Repository not found, creating: {$repoData['full_name']}");
            $repository = $this->syncRepository($repoData);
        }

        return $repository;
    }

    /**
     * Sync repository data to database
     */
    private function syncRepository(array $repo): Repository
    {
        Log::info("Syncing repository: {$repo['full_name']}");

        $repository = Repository::updateOrCreate(
            ['github_id' => $repo['id']],
            [
                'name' => $repo['name'],
                'full_name' => $repo['full_name'],
                'url' => $repo['html_url'],
                'description' => $repo['description'] ?? null,
                'private' => $repo['private'] ?? false,
                'default_branch' => $repo['default_branch'],
                'owner_info' => $repo['owner'] ?? [],
            ]
        );

        Log::info("Repository saved - ID: {$repository->id}, Name: {$repository->full_name}");
        return $repository;
    }

    private function determineSourceBranch(array $payload): ?string
    {
        return $payload['base_ref'] ?? null;
    }

    private function extractLinkedTask(string $branchName): ?string
    {
        if (preg_match('/([A-Z]+-\d+)/', $branchName, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
