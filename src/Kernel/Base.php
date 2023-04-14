<?php

namespace Qifen\ConsoleSdk\Kernel;

use Qifen\ConsoleSdk\Kernel\Exceptions\BadResponseException;
use Qifen\ConsoleSdk\Kernel\Exceptions\InvalidArgumentException;
use Symfony\Component\HttpClient\HttpClient;

class Base
{
    protected $config = [];
    protected $type = null;
    protected $httpClient = null;
    protected $accessToken = null;

    protected $extraParams = [];

    private $thoseTypesNeedToCheckFileSystemParams = ['screenshot', 'html2pdf', 'upload'];

    private $fileSystemParams = [
        'oss' => [
            'accessId',
            'accessSecret',
            'bucket',
            'endpoint',
        ],
        'cos' => [
            'region',
            'app_id',
            'secret_id',
            'secret_key',
            'bucket',
        ],
        'qiniu' => [
            'accessKey',
            'secretKey',
            'bucket',
            'domain',
        ],
    ];

    public function __construct(array $config) {
        $this->setConfig($config);

        $this->httpClient = HttpClient::create(['base_uri' => $this->getConfig('url')]);
    }

    public function getConfig(string $key = '', $default = null) {
        if (empty($key)) return $this->config;

        return $this->config[$key] ?? $default;
    }

    protected function setConfigByKey(string $key, $value) {
        if (isset($this->config[$key])) {
            $this->config[$key] = $value;
        }
    }

    public function setConfig(array $config) {
        $keys = ['url', 'appId', 'appSecret'];

        foreach ($keys as $key) {
            if (!array_key_exists($key, $config)) {
                throw new InvalidArgumentException('config init error. ' . $key . ' is required.');
            }
        }

        $this->checkExtraParams($config);

        $this->config = $config;

        return $this;
    }

    private function checkExtraParams(array $config) {
        if (!empty($this->extraParams)) {
            foreach ($this->extraParams as $extraParam) {
                if (!array_key_exists($extraParam, $config)) {
                    throw new InvalidArgumentException('config init error. ' . $extraParam . ' is required.');
                }
            }
        }

        if (!in_array($this->type, $this->thoseTypesNeedToCheckFileSystemParams)) {
            return;
        }

        if (!isset($config['saveType'])) {
            throw new InvalidArgumentException('config init error. saveType is required.');
        }

        $type = $config['saveType'];

        if (!array_key_exists($type, $this->fileSystemParams)) {
            throw new InvalidArgumentException('config init error. ' . $type . ' is unknown.');
        }

        if (!isset($config[$type])) {
            throw new InvalidArgumentException('config init error. ' . $type . ' is required.');
        }

        $keys = $this->fileSystemParams[$type];

        if ($this->type === 'upload') $keys[] = 'url';

        foreach ($keys as $key) {
            if (!array_key_exists($key, $config[$type])) {
                throw new InvalidArgumentException('config init error. ' . $key . ' is required.');
            }
        }
    }

    public function getAccessToken() {
        if (!$this->accessToken) {
            $this->accessToken = new AccessToken(
                $this->getConfig('url'),
                $this->getConfig('appId'),
                $this->getConfig('appSecret')
            );
        }

        return $this->accessToken;
    }

    /**
     * 发起请求
     *
     * @param string $api
     * @param array $options
     * @param string $method
     * @return array
     * @throws BadResponseException
     */
    public function request(string $api, array $options, string $method = 'POST') {
        try {
            if (!isset($options['headers'])) $options['headers'] = [];
            $options['headers'][] = 'Authorization: Bearer ' . $this->getAccessToken()->getToken();

            return $this->httpClient->request($method, $api, $options)->toArray(false);
        } catch (\Throwable $exception) {
            throw new BadResponseException($exception->getMessage());
        }
    }
}