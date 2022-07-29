<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Admin\CreateAdminRequest;
use App\Http\Requests\Admin\ListUsersRequest;
use App\Http\Requests\Admin\LoginAdminRequest;
use App\Services\AdminUserService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{

    public function __construct(protected AdminUserService $adminUserService)
    {
        $this->middleware('protector:admin')->except(['login', 'store']);
    }

    public function login(LoginAdminRequest $request)
    {
        try {
            $token = $this->adminUserService->loginAdminUser($request->dto);

            return response()->json(["message" => "Login successful", "token" => $token]);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], $e->getCode());
        }
    }
    public function logout()
    {
        try {
            $this->adminUserService->logoutUser(Auth::user());

            return response()->json(["message" => "Logged out successfully"]);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], $e->getCode());
        }
    }

    public function listUsers(ListUsersRequest $request)
    {
        try {
            $users = $this->adminUserService->listNormalUsers($request->dto)->paginate($request->dto->limit, [
                'uuid',
                'first_name',
                'last_name',
                'email',
                'avatar',
                'address',
                'phone_number',
                'created_at',
                'last_login_at'
            ], 'page', $request->dto->page);

            return response()->json($users);
        } catch (Exception $e) {
            return response()->json(["message" => $e->getMessage()], $e->getCode());
        }
    }

    public function store(CreateAdminRequest $request)
    {
        try {
            $this->adminUserService->createAdminUser($request->dto);

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
