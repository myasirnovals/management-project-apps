<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

abstract class Controller
{
    /**
     * Check if the currently authenticated user is a Project Manager.
     */
    protected function isProjectManager(): bool
    {
        /** @var User|null $user */
        $user = Auth::user();

        return (bool) $user?->isProjectManager();
    }
}
