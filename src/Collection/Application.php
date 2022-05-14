<?php

namespace Qifen\ConsoleSdk\Collection;

use Qifen\ConsoleSdk\Kernel\Base;
use Qifen\ConsoleSdk\Kernel\Traits\Utils;

class Application extends Base
{
    use Utils;

    protected $type = 'collection';
    protected $extraParams = ['noticeUrl'];

    protected $header = '';
    protected $openWait = false;
    protected $waitTimeout = 0;

    public function setHeader(string $header) {
        $this->header = $header;

        return $this;
    }

    public function setWait(int $timeout) {
        if ($timeout < 0) $timeout = 0;

        $this->openWait = $timeout > 0;
        $this->waitTimeout = $timeout;

        return $this;
    }

    /**
     * 采集
     *
     * @param string $url
     * @param string $type
     * @return int|mixed
     * @throws \Qifen\ConsoleSdk\Kernel\Exceptions\BadResponseException
     */
    public function collection(string $url, string $type = 'chrome') {
        $json = [
            'url' => $url,
            'type' => $type,
            'header' => $this->header,
            'open_wait' => $this->openWait,
            'wait_timeout' => $this->waitTimeout,
            'notice_url' => $this->getConfig('noticeUrl'),
        ];

        $res = $this->request($this->getApi('collection'), compact('json'));

        $data = $this->getResponse($res);

        return $data['queue_id'] ?? 0;
    }
}