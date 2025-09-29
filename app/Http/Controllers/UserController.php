<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Show user profile
     */
    public function show(User $user): View
    {
        $posts = $user->posts()
            ->published()
            ->with(['tags'])
            ->withCount(['likes', 'comments'])
            ->latest()
            ->paginate(12);

        $user->loadCount(['posts', 'followers', 'following']);

        return view('users.show', compact('user', 'posts'));
    }
}
