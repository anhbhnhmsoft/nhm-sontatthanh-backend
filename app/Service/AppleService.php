<?php

namespace App\Service;

use App\Core\Cache\CacheKey;
use App\Core\Cache\Caching;
use App\Core\LogHelper;
use App\Core\Service\BaseService;
use App\Enums\ConfigKey;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;
use Illuminate\Support\Facades\Http;

class AppleService extends BaseService
{
    public function __construct(protected ConfigService $configService)
    {
        parent::__construct();
    }

    /**
     * Verify Apple Identity Token (from Native SDK)
     * @param string $identityToken
     * @return array|null Returns payload if valid, null otherwise
     */
    public function verifyIdentityToken(string $identityToken): ?array
    {
        try {
            // 1. Fetch Apple's Public Keys
            $publicKeys = Caching::getCache(CacheKey::CACHE_KEY_APPLE_PUBLIC_KEYS, null);
            if (!$publicKeys) {
                $response = Http::get('https://appleid.apple.com/auth/keys');
                if (!$response->successful()) {
                    LogHelper::error('Failed to fetch Apple Public Keys');
                    return null;
                }
                $publicKeys = $response->json();
                // Cache for 24 hours as these keys don't change often
                Caching::setCache(CacheKey::CACHE_KEY_APPLE_PUBLIC_KEYS, $publicKeys, null, 60 * 24);
            }
            // 2. Parse JWKS and Decode Token
            try {
                $jwks = JWK::parseKeySet($publicKeys);
                $payload = JWT::decode($identityToken, $jwks);
            } catch (\Exception $e) {
                LogHelper::error('Apple Token Decode Error: ' . $e->getMessage());
                return null;
            }

            // 3. Verify Claims
            if ($payload->iss !== 'https://appleid.apple.com') {
                LogHelper::error('Apple Token invalid Issuer');
                return null;
            }

//            $clientIdConfig = $this->configService->getConfigByKey(ConfigKey::APPLE_CLIENT_ID);
//            if (!$clientIdConfig->isError()) {
//                $expectedAud = $clientIdConfig->getData()['config_value'];
//                if ($payload->aud !== $expectedAud) {
//                    LogHelper::error('Apple Token invalid Audience. Expected: ' . $expectedAud . ', Got: ' . $payload->aud);
//                    return null;
//                }
//            }

            return (array) $payload;
        } catch (\Throwable $e) {
            LogHelper::error('Apple Identity Token Verification Exception: ' . $e->getMessage());
            return null;
        }
    }
}
