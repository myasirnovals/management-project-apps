<?php

namespace App\Http\Middleware;

use App\Models\Project;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProjectMember
{
    public function handle(Request $request, Closure $next): Response
    {
        $project = $request->route('project');

        if ($project instanceof Project && ! $project->isAccessibleBy($request->user())) {
            abort(403, 'Anda bukan anggota proyek ini.');
        }

        return $next($request);
    }
}

