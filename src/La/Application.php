<?php

namespace Qifen\ConsoleSdk\La;

use Qifen\ConsoleSdk\Kernel\Base;
use Qifen\ConsoleSdk\Kernel\Traits\Utils;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\Multipart\FormDataPart;
use Qifen\ConsoleSdk\Kernel\Exceptions\BadResponseException;

class Application extends Base
{
    use Utils;
    protected $type = 'la';

    protected $_multiple = 4; //兼容性问题
    protected $_model = 'R-ESRGAN 4x+ Anime6B';

   
    /**
     * 设置服务账号
     */
    public function setMultiple($multiple = 4) {
        $this->_multiple = $multiple;
        return $this;
    }

    public function setModel($model = 'free'){
        $this->_model = $model;
        return $this;
    }

    /**
     * 截图
     *
     * @param string $url
     * @param string $path
     * @param int $width
     * @param int $height
     * @return int
     * @throws \Qifen\ConsoleSdk\Kernel\Exceptions\BadResponseException
     */
    public function losslessAmplification(string $carry, string $filePath) {
        // 创建一个finfo资源
        $finfo = new \finfo(FILEINFO_MIME_TYPE);

        // 获取文件的MIME类型
        $mimeType = $finfo->file($filePath);
        if(!in_array($mimeType, [ 'image/png', 'image/jpeg', 'image/bmp', 'image/webp' ])){
            throw new BadResponseException('图片类型不正确');
        }
        $dataPart = DataPart::fromPath($filePath);
        $formData = [
            'carry' => $carry,
            'file' => $dataPart,
            'model' => $this->_model,
            'notice_url' => $this->getConfig('noticeUrl'),
            'multiple' => strval($this->_multiple)
        ];
        $formDataPart = new FormDataPart($formData);
        $headers = $formDataPart->getPreparedHeaders()->toArray();

        $body = $formDataPart->bodyToString();

        $res = $this->request($this->getApi('la'), compact('headers','body'));

        $data = $this->getResponse($res);

        return $data['clientId'] ?? '';
    }
}