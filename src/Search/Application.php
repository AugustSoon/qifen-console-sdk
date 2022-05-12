<?php

namespace Qifen\ConsoleSdk\Search;

use Qifen\ConsoleSdk\Kernel\Base;
use Qifen\ConsoleSdk\Kernel\Traits\Utils;
use Qifen\ConsoleSdk\Kernel\Exceptions\BadResponseException;

class Application extends Base
{
    use Utils;

    protected $index = null;
    protected $type = 'search';

    /**
     * 获取索引
     *
     * @param string|null $index
     * @return string|null
     */
    public function getIndex(string $index = null) {
        if (is_null($index)) {
            $index = is_null($this->index) ? 'default' : $this->index;
        }

        return $index;
    }

    /**
     * 设置索引
     *
     * @param string $index
     * @return Application
     */
    public function setIndex(string $index) {
        $this->index = $index;

        return $this;
    }

    /**
     * 写入
     *
     * @param array $data
     * @param string $primaryKey
     * @param string|null $index
     * @return array
     * @throws BadResponseException
     */
    public function create(array $data, string $primaryKey = 'id', string $index = null) {
        $res = $this->request($this->getApi('searchCreate'), [
            'json' => [
                'data' => $data,
                'index' => $this->getIndex($index),
                'primary_key' => $primaryKey,
            ],
        ]);

        return $this->getResponse($res);
    }

    /**
     * 更新
     *
     * @param array $data
     * @param string|null $index
     * @return array
     * @throws BadResponseException
     */
    public function update(array $data, string $index = null) {
        $index = $this->getIndex($index);

        $res = $this->request($this->getApi('searchUpdate'), [
            'json' => compact('data', 'index'),
        ]);

        return $this->getResponse($res);
    }

    /**
     * 更新排序字段
     *
     * @param array $fields
     * @param string|null $index
     * @return array
     * @throws BadResponseException
     */
    public function updateSort(array $fields, string $index = null) {
        return $this->updateAttr('Sort', $fields, $index);
    }

    /**
     * 更新查询字段
     *
     * @param array $fields
     * @param string|null $index
     * @return array
     * @throws BadResponseException
     */
    public function updateFilter(array $fields, string $index = null) {
        return $this->updateAttr('Filter', $fields, $index);
    }

    /**
     * 更新搜索字段
     *
     * @param array $fields
     * @param string|null $index
     * @return array
     * @throws BadResponseException
     */
    public function updateSearchable(array $fields, string $index = null) {
        return $this->updateAttr('Searchable', $fields, $index);
    }

    /**
     * 更新属性
     *
     * @param string $type
     * @param array $fields
     * @param string|null $index
     * @return array
     * @throws BadResponseException
     */
    private function updateAttr(string $type, array $fields, string $index = null) {
        $index = $this->getIndex($index);

        $api = $this->getApi('search' . $type);

        $res = $this->request($api, [
            'json' => compact('fields', 'index'),
        ]);

        return $this->getResponse($res);
    }

    /**
     * 删除
     *
     * @param array $ids
     * @param string|null $index
     * @return array
     * @throws BadResponseException
     */
    public function del(array $ids, string $index = null) {
        $index = $this->getIndex($index);

        $res = $this->request($this->getApi('searchDel'), [
            'json' => compact('ids', 'index'),
        ]);

        return $this->getResponse($res);
    }

    /**
     * 删除索引
     *
     * @param string|null $index
     * @return array
     * @throws BadResponseException
     */
    public function clear(string $index = null) {
        $index = $this->getIndex($index);

        $res = $this->request($this->getApi('searchClear'), [
            'json' => compact('index'),
        ]);

        return $this->getResponse($res);
    }

    /**
     * 搜索
     *
     * @param string $keyword
     * @param string $condition
     * @param int $page
     * @param int $limit
     * @param array $order
     * @param string|null $index
     * @return array
     * @throws BadResponseException
     */
    public function query(string $keyword, string $condition = '', int $page = 1, int $limit = 20, array $order = [], string $index = null) {
        $index = $this->getIndex($index);

        $json = compact('keyword', 'condition', 'limit', 'order', 'index');

        if ($page > 0) $json['page'] = $page;

        $res = $this->request($this->getApi('search'), compact('json'));

        return $this->getResponse($res);
    }
}