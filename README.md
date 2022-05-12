# console-sdk

## 安装
```shell
composer require qifen/console-sdk
```

## 使用

### 全文搜索

```php
use Qifen\ConsoleSdk\Search\Application;

// 配置
$config = [
    'url' => 'http://api.xxx.com/', // 服务地址
    'appId' => '123456790', // appId
    'appSecret' => '123456790xxx',  // appSecret
];

// 1.初始化
$client = new Application($config);

// 2.设置索引，如不设置，会写入默认索引
$client = $client->setIndex('style');

// 3.写入
// 数据为数组格式，一次最多可写入1000条
// 数据项格式随意，但是每一条必须都包含相同的主键，默认主键是 id
// 如果主键为其他值，可在第二个参数中传入
// 写入的索引为通过 setIndex 设置的索引，也可以通过传入第三个参数写入指定索引
$data = [
    ['id' => 1, 'title' => '1', 'tags' => '1'],
    ['id' => 2, 'title' => '2', 'tags' => '2'],
];
// 默认主键和索引
$client->create($data);
// 指定主键
$client->create($data, 'pk');
// 指定主键和索引
$client->create($data， 'pk', 'post');

// 4.更新
// 数据为数组格式，一次最多可更新1000条
// 每一条数据都必须包含主键
// 更新的索引为通过 setIndex 设置的索引，也可以通过传入第二个参数更新指定索引
$data = [
    ['id' => 1, 'title' => '1.0'],
];
// 默认索引
$client->update($data);
// 指定索引
$client->update($data, 'post');

// 5.更新全文搜索可搜索字段
// 数据写入后全文搜索默认会搜索全部字段，如果要指定字段，需要更新可搜索字段
// 字段格式为数组，请确保数组中的字段是数据中存在的字段
$fields = ['title'];
// 默认索引
$client->updateSearchable($fields);
// 指定索引
$client->updateSearchable($fields, 'post');

// 6.更新可查询字段
// 数据写入后默认只能全文搜索，如果要指定查询条件，需要先更新可查询字段
// 字段格式为数组，请确保数组中的字段是数据中存在的字段
$fields = ['id', 'tags'];
// 默认索引
$client->updateFilter($fields);
// 指定索引
$client->updateFilter($fields, 'post');

// 7.更新可排序字段
// 数据写入后默认无法自定义排序规则，如果要指定排序规则，需要先更新可排序字段
// 字段格式为数组，请确保数组中的字段是数据中存在的字段
$fields = ['id', 'tags'];
// 默认索引
$client->updateSort($fields);
// 指定索引
$client->updateSort($fields, 'post');

// 8.删除数据
// 数据格式为数组，即要删除的主键集合
$ids = [1];
// 默认索引
$client->del($ids);
// 指定索引
$client->del($ids, 'post');

// 9.删除索引
// 删除索引和索引内的全部内容，此操作不可撤销，谨慎使用
// 默认索引
$client->clear();
// 指定索引
$client->clear('post');

// 10.搜索
// 参数说明
// $keyword 查询关键词
// $condition 查询条件，写法参考 https://docs.meilisearch.com/learn/advanced/filtering_and_faceted_search.html#using-filters
// $page 页数，默认1， 如果传 0 为不分页
// $limit 每页条数，默认 20
// $order 排序条件，数组格式，数组元素可以为字符串或数组，如果为字符串，即按照该字段 asc 排序，如果为数组，第一个元素为字段，第二个为排序规则，第二个省略则默认为 asc
// $index 指定索引
// 关键词搜索
$res = $client->query('关键词');
// 关键词搜索 + 指定查询条件（需要先更新可查询字段）
$res = $client->query('关键词', 'id = 1 OR tag = "2"');
// 关键词搜索 + 分页
$res = $client->query('关键词', '', 2, 20);
// 关键词搜索 + 不分页
$res = $client->query('关键词', '', 0, 1000);
// 关键词搜索 + 分页 + 排序（需要先更新可排序字段）
$res = $client->query('关键词', '', 1, 20, ["id", ["tag", "desc"]]);
// 关键词搜索 + 分页 + 指定索引
$res = $client->query('关键词', '', 1, 20, [], 'post');
// 返回格式
$res = [
    'list' => [],   // 数据列表
    'total' => 0,   // 查询到的数据总数
    'limit' => 20,  // 分页大小
    'offset' => 0,  // 开始位置
    'keyword' => '关键词', // 搜索的关键词
];
```

### 网页截图

```php
use Qifen\ConsoleSdk\Screenshot\Application;

// 配置
$config = [
    'url' => 'http://api.xxx.com/', // 服务地址
    'appId' => '123456790', // appId
    'appSecret' => '123456790xxx',  // appSecret
    'noticeUrl' => 'http://api.xxx.com/notice', // 回调地址
    'saveType' => 'qiniu',  // 保存类型，支持 oss、cos、qiniu
    // oss 参数
    'oss' => [
        'accessId' => '',
        'accessSecret' => '',
        'bucket' => '',
        'endpoint' => '',
    ],
    // cos 参数
    'cos' => [
        'region' => '',
        'app_id' => '',
        'secret_id' => '',
        'secret_key' => '',
        'bucket' => '',
    ],
    // 七牛参数
    'qiniu' => [
        'accessKey' => '',
        'secretKey' => '',
        'bucket' => '',
        'domain' => '',
    ],
];

// 1.初始化
$client = new Application($config);

// 2.截图
// 参数说明
// $url 要截图的 URL 地址
// $path 截图后的保存路径
// $width 图片宽度
$queueId = $client->screenshot('https://www.baidu.com', '/save_path/save_name.png', 800);
// 返回结果为队列ID，需自行保存，处理回调时使用
```

### 网页转PDF

```php
use Qifen\ConsoleSdk\Html2pdf\Application;

// 配置
$config = [
    'url' => 'http://api.xxx.com/', // 服务地址
    'appId' => '123456790', // appId
    'appSecret' => '123456790xxx',  // appSecret
    'noticeUrl' => 'http://api.xxx.com/notice', // 回调地址
    'saveType' => 'qiniu',  // 保存类型，支持 oss、cos、qiniu
    // oss 参数
    'oss' => [
        'accessId' => '',
        'accessSecret' => '',
        'bucket' => '',
        'endpoint' => '',
    ],
    // cos 参数
    'cos' => [
        'region' => '',
        'app_id' => '',
        'secret_id' => '',
        'secret_key' => '',
        'bucket' => '',
    ],
    // 七牛参数
    'qiniu' => [
        'accessKey' => '',
        'secretKey' => '',
        'bucket' => '',
        'domain' => '',
    ],
];

// 1.初始化
$client = new Application($config);

// 2.转PDF
// 参数说明
// $url 要转换的 URL 地址
// $path 转换完成后的保存路径
// $width 宽度，默认120mm
// $format 纸张格式，如果传了该参数，会忽略宽度参数
// 默认宽度
$queueId = $client->toPdf('https://www.baidu.com', '/save_path/save_name.pdf');
// 指定宽度
$queueId = $client->toPdf('https://www.baidu.com', '/save_path/save_name.pdf', '150mm');
// 指定纸张格式
$queueId = $client->toPdf('https://www.baidu.com', '/save_path/save_name.pdf', '', 'a4');
// 返回结果为队列ID，需自行保存，处理回调时使用
```

### 获取上传 token
> 用于 word 转 html 和 PDF 转 html 时上传文件

```php
use Qifen\ConsoleSdk\Upload\Application;

// 配置
$config = [
    'url' => 'http://api.xxx.com/', // 服务地址
    'appId' => '123456790', // appId
    'appSecret' => '123456790xxx',  // appSecret
    'noticeUrl' => 'http://api.xxx.com/notice', // 回调地址
    'saveType' => 'qiniu',  // 保存类型，支持 oss、cos、qiniu
    // oss 参数
    'oss' => [
        'accessId' => '',
        'accessSecret' => '',
        'bucket' => '',
        'endpoint' => '',
        'url' => '',    // 用来替换 word 和 pdf 中的图片地址
    ],
    // cos 参数
    'cos' => [
        'region' => '',
        'app_id' => '',
        'secret_id' => '',
        'secret_key' => '',
        'bucket' => '',
        'url' => '',    // 用来替换 word 和 pdf 中的图片地址
    ],
    // 七牛参数
    'qiniu' => [
        'accessKey' => '',
        'secretKey' => '',
        'bucket' => '',
        'domain' => '',
        'url' => '',    // 用来替换 word 和 pdf 中的图片地址
    ],
];

// 1.初始化
$client = new Application($config);

// 2.获取上传 token
// 参数说明
// $path 转换完成后的保存路径
$token = $client->getUploadToken('/save_path');
```