<?php

namespace App\Http\Controllers;

use App\Models\Repository;
use Inertia\Inertia;

class RepositoryController extends Controller
{
    public function index()
    {
        $repositories = Repository::withCount('branches')
            ->latest()
            ->get();

        return Inertia::render('Repositories/Index', [
            'repositories' => $repositories,
            'lastSynced' => now()->toISOString(),
        ]);
    }

    public function branches(Repository $repository)
    {
        $branches = $repository->branches()
            ->latest()
            ->get();

        return Inertia::render('Branches/Index', [
            'repository' => $repository,
            'branches' => $branches,
        ]);
    }
}
