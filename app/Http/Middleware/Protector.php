<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Lcobucci\JWT\Token\Plain;
use Illuminate\Http\JsonResponse;
use App\Services\AuthTokenService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Contracts\Auth\Authenticatable;

class Protector
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response|JsonResponse|RedirectResponse
    {
        $supportedRoles = Config::get('protector.roles');

        if (!in_array($role, $supportedRoles)) {
            throw new Exception("Unknown user authentication role");
        }

        /** @var string|null */
        $authHeader = $request->header('Authorization');

        if (trim($authHeader ?? "") === "") {
            return response()->json(["message" => "Unauthenticated"], Response::HTTP_UNAUTHORIZED);
        }

        $authHeader = explode(" ", $authHeader ?? "")[1];

        $validation = (new AuthTokenService())->isTokenValid($authHeader);

        if ($validation === false) {
            return response()->json(["message" => "Unauthenticated"], Response::HTTP_UNAUTHORIZED);
        }

        /** @var Plain */
        $validation = $validation;

        $userUuid = $validation->claims()->get('uid');

        $token = User::where("uuid", $userUuid)->first()?->tokens->last();

        if (is_null($token)) {
            return response()->json(["message" => "Unauthenticated"], Response::HTTP_UNAUTHORIZED);
        }

        if (Carbon::parse($token->expires_at) < now()) {
            return response()->json(["message" => "Token expired"], Response::HTTP_UNAUTHORIZED);
        }

        if ($role === "admin" && !$token->user?->is_admin) {
            return response()->json(["message" => "You don't have enough permissions"], Response::HTTP_FORBIDDEN);
        }

        /** @var Authenticatable */
        $loggedInUser = $token->user;

        Auth::setUser($loggedInUser);

        return $next($request);
    }
}
