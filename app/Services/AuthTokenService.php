<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\JWT_Token;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Ramsey\Uuid\Uuid;

class AuthTokenService
{

    public function createUserToken(User $user): string
    {
        // Since it's for development purpose these keys will be pushed to Git
        $publicKey = <<<EOD
        -----BEGIN PUBLIC KEY-----
        MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQC8kGa1pSjbSYZVebtTRBLxBz5H
        4i2p/llLCrEeQhta5kaQu/RnvuER4W8oDH3+3iuIYW4VQAzyqFpwuzjkDI+17t5t
        0tyazyZ8JXw+KgXTxldMPEL95+qVhgXvwtihXC1c5oGbRlEDvDF6Sa53rcFVsYJ4
        ehde/zUxo6UvS7UrBQIDAQAB
        -----END PUBLIC KEY-----
        EOD;
        
        $configuration = Configuration::forAsymmetricSigner(
            new Sha256(),
            InMemory::file(storage_path('app/private/key.pem')),
            InMemory::plainText($publicKey)
        );


        $tokenIdentifier = Uuid::uuid4()->toString();
        $issuer = Config::get("app.url");
        $issueTime = now()->toDateTimeImmutable();
        $expireTime = now()->addMinutes(Config::get('app.jwt_expires_after_minutes'))->toDateTimeImmutable();

        $token = $configuration->builder()
        // Configures the issuer (iss claim)
        ->issuedBy($issuer)
        // Configures the id (jti claim)
        ->identifiedBy($tokenIdentifier)
        // Configures the time that the token was issue (iat claim)
        ->issuedAt($issueTime)
        // Configures the expiration time of the token (exp claim)
        ->expiresAt($expireTime)
        // Configures a new claim, called "uid"
        ->withClaim('uid', $user->uuid)
        // Builds a new token
        ->getToken($configuration->signer(), $configuration->signingKey());

        JWT_Token::create([
            "unique_id" => $tokenIdentifier, 
            "user_id" => $user->id, 
            "token_title" =>"ADMIN LOGIN TOKEN", 
            "expires_at" => $expireTime
        ]);

        return $token->toString();
    }
}
