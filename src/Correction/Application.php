<?php

namespace Qifen\ConsoleSdk\Correction;

use Qifen\ConsoleSdk\Kernel\Base;
use Qifen\ConsoleSdk\Kernel\Exceptions\InvalidArgumentException;
use Qifen\ConsoleSdk\Kernel\Traits\Utils;

class Application extends Base
{
    use Utils;

    protected $type = 'correct';
    protected $extraParams = ['noticeUrl'];

    /**
     * 纠错
     *
     * @param array $content
     * @param $id
     * @return bool
     * @throws InvalidArgumentException
     * @throws \Qifen\ConsoleSdk\Kernel\Exceptions\BadResponseException
     */
    public function correction(array $content, $id = 0) {
        if (empty($content)) {
            throw new InvalidArgumentException('content is empty');
        }

        $json = [
            'id' => $id,
            'content' => $content,
            'notice_url' => $this->getConfig('noticeUrl'),
        ];

        $res = $this->request($this->getApi('correct'), compact('json'));

        return $this->isSuccess($res);
    }
}