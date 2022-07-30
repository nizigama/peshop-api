<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\AuthTokenService;
use Carbon\Carbon;
use Closure;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Lcobucci\JWT\Token\Plain;

class Protector
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|JsonResponse|RedirectResponse|Response
     */
    public function handle(Request $request, Closure $next, string $role)
    {

        $supportedRoles = Config::get('protector.roles');

        if(!in_array($role, $supportedRoles)){
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

        if(is_null($token)){
            return response()->json(["message" => "Unauthenticated"], Response::HTTP_UNAUTHORIZED);
        }

        if (Carbon::parse($token->expires_at) < now()) {
            return response()->json(["message" => "Token expired"], Response::HTTP_UNAUTHORIZED);
        }

        if($role === "admin" && !$token->user?->is_admin){
            return response()->json(["message" => "You don't have enough permissions"], Response::HTTP_FORBIDDEN);
        }
        
        /** @var Authenticatable */
        $loggedInUser = $token->user;
        
        Auth::setUser($loggedInUser);

        return $next($request);
    }
}
