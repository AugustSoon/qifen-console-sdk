<?php

namespace Qifen\ConsoleSdk\Nsfw;

use Qifen\ConsoleSdk\Kernel\Base;
use Qifen\ConsoleSdk\Kernel\Exceptions\InvalidArgumentException;
use Qifen\ConsoleSdk\Kernel\Traits\Utils;
use Qifen\ConsoleSdk\Kernel\Exceptions\BadResponseException;

class Application extends Base
{
    use Utils;

    protected $type = 'nsfw';
    protected $extraParams = ['noticeUrl'];

    /**
     * 验证是否为 URL
     *
     * @param $url
     * @return bool
     */
    private function validateURL($url) {
        return !(filter_var($url, FILTER_VALIDATE_URL) === false);
    }

    /**
     * 鉴黄
     *
     * @param string $url
     * @param int $id
     * @return bool
     * @throws BadResponseException
     */
    public function identify(string $url, int $id = 0) {
        if (!$this->validateURL($url)) {
            throw new InvalidArgumentException('url is invalid');
        }

        $json = [
            'id' => $id,
            'nsfw_url' => $url,
            'notice_url' => $this->getConfig('noticeUrl'),
        ];

        $res = $this->request($this->getApi('nsfw'), compact('json'));

        return $this->isSuccess($res);
    }

    /**
     * 鉴黄-批量模式
     *
     * @param array $data
     * @return bool
     * @throws BadResponseException
     * @throws InvalidArgumentException
     */
    public function identifyBatch(array $data) {
        $list = [];

        foreach ($data as $datum) {
            $url = $datum['url'] ?? '';

            if ($this->validateURL($url)) {
                $list[] = [
                    'url' => $url,
                    'id' => $datum['id'] ?? 0,
                ];
            }
        }

        if (count($list) == 0) {
            throw new InvalidArgumentException('list is invalid');
        }

        $json = [
            'list' => $list,
            'notice_url' => $this->getConfig('noticeUrl'),
        ];

        $res = $this->request($this->getApi('nsfwBatch'), compact('json'));

        return $this->isSuccess($res);
    }
}