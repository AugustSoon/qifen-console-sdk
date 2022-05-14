<?php

namespace Qifen\ConsoleSdk\Html2pdf;

use Qifen\ConsoleSdk\Kernel\Base;
use Qifen\ConsoleSdk\Kernel\Traits\Utils;

class Application extends Base
{
    use Utils;

    protected $type = 'html2pdf';
    protected $extraParams = ['noticeUrl'];

    protected $displayHeaderFooter = false;
    protected $headerTemplate = '';
    protected $footerTemplate = '';
    protected $printBackground = false;
    protected $pageRanges = '';
    protected $margin = '';
    protected $omitBackground = false;
    protected $timeout = 0;
    protected $header = '';
    protected $openWait = false;
    protected $waitTimeout = 0;
    protected $altitudeCompensation = 0;

    public function setNoticeUrl(string $url) {
        $this->setConfigByKey('noticeUrl', $url);

        return $this;
    }

    public function setDisplayHeaderFooter(bool $displayHeaderFooter) {
        $this->displayHeaderFooter = $displayHeaderFooter;

        return $this;
    }

    public function setHeaderTemplate(string $headerTemplate) {
        $this->headerTemplate = $headerTemplate;

        return $this;
    }

    public function setFooterTemplate(string $footerTemplate) {
        $this->footerTemplate = $footerTemplate;

        return $this;
    }

    public function setPrintBackground(bool $printBackground) {
        $this->printBackground = $printBackground;

        return $this;
    }

    public function setPageRanges(string $pageRanges) {
        $this->pageRanges = $pageRanges;

        return $this;
    }

    public function setMargin(string $margin) {
        $this->margin = $margin;

        return $this;
    }

    public function setOmitBackground(bool $omitBackground) {
        $this->omitBackground = $omitBackground;

        return $this;
    }

    public function setTimeout(int $timeout) {
        $this->timeout = $timeout > 0 ? $timeout : 0;

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

    public function setAltitudeCompensation(int $altitudeCompensation) {
        $this->altitudeCompensation = $altitudeCompensation;

        return $this;
    }

    /**
     * html 生成 pdf
     *
     * @param string $url
     * @param string $path
     * @param string $width
     * @param string $format
     * @return int
     * @throws \Qifen\ConsoleSdk\Kernel\Exceptions\BadResponseException
     */
    public function toPdf(string $url, string $path, string $width = '', string $format = '') {
        $saveType = $this->getConfig('saveType');
        $saveOptions = json_encode($this->getConfig($saveType), JSON_UNESCAPED_UNICODE);

        $json = [
            'url' => $url,
            'file_path' => $path,
            'width' => $width,
            'format' => $format,
            'notice_url' => $this->getConfig('noticeUrl'),
            'save_type' => $saveType,
            'save_options' => $saveOptions,
            'display_header_footer' => $this->displayHeaderFooter,
            'header_template' => $this->headerTemplate,
            'footer_template' => $this->footerTemplate,
            'print_background' => $this->printBackground,
            'page_ranges' => $this->pageRanges,
            'margin' => $this->margin,
            'omit_background' => $this->omitBackground,
            'header' => $this->header,
            'open_wait' => $this->openWait,
            'wait_timeout' => $this->waitTimeout,
        ];

        if ($this->timeout > 0) $json['timeout'] = $this->timeout;
        if ($this->altitudeCompensation > 0) $json['altitude_compensation'] = $this->altitudeCompensation;

        $res = $this->request($this->getApi('htmlToPdf'), compact('json'));

        $data = $this->getResponse($res);

        return $data['queue_id'] ?? 0;
    }
}