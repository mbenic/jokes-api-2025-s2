<?php
/**
 * Assessment Title: Portfolio Project
 * Cluster:          SaaS: Back End Development ICT50220
 * Qualification:    ICT50220 Diploma of Information Technology (Back End Development)
 *
 * AdminCategoryController.php
 *
 * Admin Controller with methods for managing the admin dashboard and user management.
 *
 * Filename:        AdminController.php
 * Location:        /App/Http/Controllers/Admin 
 * Project:         jokes-api-2025-s2
 * Version:         1.0.0
 * Date Created:    15/07/2025
 *
 * Author:          Adrian Gould <Adrian.Gould@nmtafe.wa.edu.au>
 *                  Maria Benic <j317578@tafe.wa.edu.au>
 */ 


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Number;
use Illuminate\View\View;

class AdminController extends Controller
{
    //
    public function index(): View
    {
        $userCount = User::count();
        $userSuspendedCount = User::where('suspended', 1)->count();

        return view('admin.index')
            ->with('userCount', Number::format($userCount))
            ->with('userSuspendedCount', Number::format($userSuspendedCount));
    }

    //
    public function users(): View
    {
        $users = User::paginate(10);

        return view('admin.users.index')
            ->with('users', $users);
    }
}
