
<?php

use Qifen\ConsoleSdk\Kernel\Base;
use Qifen\ConsoleSdk\Kernel\Traits\Utils;
use Qifen\ConsoleSdk\Kernel\Exceptions\BadResponseException;

class Application extends Base
{
    use Utils;

    protected $type = 'nsfw';
    protected $extraParams = ['noticeUrl'];

    /**
     * 鉴黄
     *
     * @param string $url
     * @param int $id
     * @return bool
     * @throws BadResponseException
     */
    public function nsfw(string $url, int $id = 0) {
        $json = [
            'id' => $id,
            'nsfw_url' => $url,
            'notice_url' => $this->getConfig('noticeUrl'),
        ];

        $res = $this->request($this->getApi('nsfw'), compact('json'));

        return $this->isSuccess($res);
    }
}