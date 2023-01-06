<?php

namespace Qifen\ConsoleSdk\Kernel\Traits;

use Qifen\ConsoleSdk\Kernel\Exceptions\BadResponseException;

trait Utils
{
    private $apis = [
        'login' => 'login',
        'refreshToken' => 'ref-token',

        'uploadToken' => 'upload-token-word-or-pdf',

        'screenshot' => 'screen-shot/create',
        'htmlToPdf' => 'html-to-pdf/create',
        'collection' => 'collection/create',
        'chatgpt'   => 'chatgpt/create',

        'invoice'   => 'invoice',

        'search' => 'search/query',
        'searchCreate' => 'search/create',
        'searchUpdate' => 'search/update',
        'searchSort' => 'search/update/sort',
        'searchFilter' => 'search/update/filter',
        'searchRanking' => 'search/update/ranking',
        'searchSearchable' => 'search/update/searchable',
        'searchDel' => 'search/del',
        'searchClear' => 'search/clear',

        'nsfw' => 'nsfw',
        'nsfwBatch' => 'nsfw_batch',

        'correct' => 'correct',
    ];

    /**
     * 获取 API
     *
     * @param string $key
     * @return string
     */
    public function getApi(string $key) {
        return $this->apis[$key] ?? '';
    }

    /**
     * 验证并获取响应数据
     *
     * @param array $response
     * @return array
     * @throws BadResponseException
     */
    public function getResponse(array $response) {
        if (($response['code'] ?? 1) == 0) {
            return $response['data'] ?? [];
        }

        $msg = $response['msg'] ?? 'response error';

        throw new BadResponseException($msg);
    }

    /**
     * 验证响应
     *
     * @param array $response
     * @return bool
     */
    public function isSuccess(array $response) {
        return ($response['code'] ?? 1) == 0;
    }

    /**
     * 组装请求参数
     *
     * @param array $data
     * @param string $token
     * @return array
     */
    public function buildOptions(array $data, string $token) {
        if (!isset($data['headers'])) $data['headers'] = [];

        $data['headers']['Authorization'] = 'Bearer ' . $token;

        return $data;
    }
}