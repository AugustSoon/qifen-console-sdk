<?php

namespace Qifen\ConsoleSdk\Screenshot;

use Qifen\ConsoleSdk\Kernel\Base;
use Qifen\ConsoleSdk\Kernel\Traits\Utils;

class Application extends Base
{
    use Utils;

    protected $type = 'screenshot';
    protected $extraParams = ['noticeUrl'];

    protected $screenshotType = 'default';
    protected $selector = '';
    protected $altitudeCompensation = 0;
    protected $header = '';
    protected $openWait = false;
    protected $waitTimeout = 0;
    protected $openWatermark = false;
    protected $watermarkText = '';
    protected $deviceScaleFactor = 1;
    protected $isMobile = true;
    protected $hasTouch = true;
    protected $isLandscape = true;
    protected $isPartition = false;
    protected $partitionCount = 0;

    public function setNoticeUrl(string $url) {
        $this->setConfigByKey('noticeUrl', $url);

        return $this;
    }

    public function setScreenshotType(string $type) {
        $this->screenshotType = $type;

        return $this;
    }

    public function setSelector(string $selector) {
        if ($this->screenshotType === 'selector') {
            $this->selector = $selector;
        }

        return $this;
    }

    public function setAltitudeCompensation(int $altitudeCompensation) {
        $this->altitudeCompensation = $altitudeCompensation;

        return $this;
    }

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

    public function setWatermark(string $text) {
        $this->openWatermark = !empty($text);
        $this->watermarkText = $text;

        return $this;
    }

    public function setDpi(float $dpi) {
        $this->deviceScaleFactor = $dpi;

        return $this;
    }

    public function setMobile(bool $flag) {
        $this->isMobile = $flag;

        return $this;
    }

    public function setTouch(bool $flag) {
        $this->hasTouch = $flag;

        return $this;
    }

    public function setLandscape(bool $flag) {
        $this->isLandscape = $flag;

        return $this;
    }

    public function setPartition(bool $flag){
        $this->isPartition = $flag;
        return $this;
    }

    public function setPartitionCount(int $flag){
        $this->partitionCount = $flag;
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
    public function screenshot(string $url, string $path, int $width, int $height = 0) {
        $saveType = $this->getConfig('saveType');
        $saveOptions = json_encode($this->getConfig($saveType), JSON_UNESCAPED_UNICODE);

        $json = [
            'url' => $url,
            'file_path' => $path,
            'width' => $width,
            'notice_url' => $this->getConfig('noticeUrl'),
            'save_type' => $saveType,
            'save_options' => $saveOptions,
            'screenshot_type' => $this->screenshotType,
            'selector' => $this->selector,
            'altitude_compensation' => $this->altitudeCompensation,
            'header' => $this->header,
            'open_wait' => $this->openWait,
            'wait_timeout' => $this->waitTimeout,
            'open_watermark' => $this->openWatermark,
            'watermark_text' => $this->watermarkText,
            'device_scale_factor' => $this->deviceScaleFactor,
            'is_mobile' => $this->isMobile,
            'has_touch' => $this->hasTouch,
            'is_landscape' => $this->isLandscape,
            'is_partition' => $this->isPartition,
            'partition_count' => $this->partitionCount,
        ];

        if ($height > 0) $json['height'] = $height;

        $res = $this->request($this->getApi('screenshot'), compact('json'));

        $data = $this->getResponse($res);

        return $data['queue_id'] ?? 0;
    }
}