<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Admin\CreateAdminRequest;
use App\Services\AdminUserService;
use Exception;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function login()
    {
    }
    public function logout()
    {
    }

    public function listUsers()
    {
    }

    public function store(CreateAdminRequest $request, AdminUserService $adminUserService)
    {
        try {
            $adminUserService->createAdminUser($request->dto);

            return response()->json(["message" => "Admin user created successfully"]);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], $e->getCode());
        }
    }

    public function update()
    {
    }
    public function destroy()
    {
    }
}
