<?php

namespace Qifen\ConsoleSdk\Upload;

use Qifen\ConsoleSdk\Kernel\Base;
use Qifen\ConsoleSdk\Kernel\Traits\Utils;

class Application extends Base
{
    use Utils;

    protected $type = 'upload';

    /**
     * 获取 token
     *
     * @param string $path
     * @return string
     * @throws \Qifen\ConsoleSdk\Kernel\Exceptions\BadResponseException
     */
    public function getUploadToken(string $path) {
        $saveType = $this->getConfig('saveType');
        $saveOptions = json_encode($this->getConfig($saveType), JSON_UNESCAPED_UNICODE);

        $json = [
            'file_path' => $path,
            'save_type' => $saveType,
            'save_options' => $saveOptions,
            'notice_url' => $this->getConfig('noticeUrl'),
        ];

        $res = $this->request($this->getApi('uploadToken'), compact('json'));

        $data = $this->getResponse($res);

        return $data['token'] ?? '';
    }
}