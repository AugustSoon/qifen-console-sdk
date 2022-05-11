<?php

namespace Qifen\ConsoleSdk\Kernel;


use Qifen\ConsoleSdk\Kernel\Exceptions\BadResponseException;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpClient\HttpClient;
use Qifen\ConsoleSdk\Kernel\Traits\Utils;

class AccessToken
{
    use Utils;

    private $appId;
    private $appSecret;

    protected $cache;
    protected $httpClient;

    const ACCESS_TOKEN_KEY = 'access_token';
    const REFRESH_TOKEN_KEY = 'refresh_token';

    public function __construct(string $url, string $appId, string $appSecret) {
        $this->appId = $appId;
        $this->appSecret = $appSecret;

        $this->httpClient = HttpClient::create(['base_uri' => $url]);
        $this->cache = new FilesystemAdapter('qifen_tools', 3600);
    }

    /**
     * 获取token
     *
     * @return string
     * @throws BadResponseException
     */
    public function getToken() {
        $token = $this->cache->getItem(self::ACCESS_TOKEN_KEY);

        if ($token->isHit()) return $token->get();

        return $this->refresh();
    }

    /**
     * 获取新token
     *
     * @return string
     * @throws BadResponseException
     */
    public function refresh() {
        try {
            $refreshToken = null;
            $refreshTokenItem = $this->cache->getItem(self::REFRESH_TOKEN_KEY);

            if ($refreshTokenItem->isHit()) $refreshToken = $refreshTokenItem->get();

            if ($refreshToken) {
                $method = 'GET';

                $api = $this->getApi('refreshToken');

                $options = $this->buildOptions([], $refreshToken);
            } else {
                $method = 'POST';

                $api = $this->getApi('login');

                $options = [
                    'query' => [
                        'app_id' => $this->appId,
                        'app_secret' => $this->appSecret,
                    ],
                ];
            }

            $res = $this->httpClient->request($method, $api, $options)->toArray(false);

            $data = $this->getResponse($res);

            $token = $data[self::ACCESS_TOKEN_KEY] ?? null;

            if (!$token) throw new BadResponseException('failed to get token');

            if (!$refreshToken) {
                $refreshTokenItem->set($data[self::REFRESH_TOKEN_KEY]);
                $refreshTokenItem->expiresAfter(2592000);
                $this->cache->save($refreshTokenItem);
            }

            $tokenItem = $this->cache->getItem(self::ACCESS_TOKEN_KEY);
            $tokenItem->set($token);
            $tokenItem->expiresAfter($data['expires_in'] ?? 7200);
            $this->cache->save($tokenItem);

            return $token;
        } catch (\Throwable $exception) {
            throw new BadResponseException($exception->getMessage());
        }
    }
}