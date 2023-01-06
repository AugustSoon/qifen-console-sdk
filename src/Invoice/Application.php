<?php

namespace Qifen\ConsoleSdk\Invoice;

use Qifen\ConsoleSdk\Kernel\Base;
use Qifen\ConsoleSdk\Kernel\Exceptions\InvalidArgumentException;
use Qifen\ConsoleSdk\Kernel\Traits\Utils;

class Application extends Base
{
    use Utils;

    protected $type = 'invoice';

    /**
     * @var string 纳税人名称
     */
    protected $name = '';

    /**
     * @var string 纳税人识别号
     */
    protected $number = '';

    /**
     * @var string 公司注册地址
     */
    protected $address = '';

    /**
     * @var string 公司电话
     */
    protected $tel = '';

    /**
     * @var string 开户行
     */
    protected $bank = '';

    /**
     * @var string 银行账号
     */
    protected $account = '';

    /**
     * @var string 手机号码
     */
    protected $phone = '';

    /**
     * @var string 接收邮箱
     */
    protected $email = '';

    /**
     * 设置纳税人名称
     *
     * @param string $value
     * @return $this
     */
    public function setName(string $value): Application {
        $this->name = $value;

        return $this;
    }

    /**
     * 设置纳税人识别号
     *
     * @param string $value
     * @return $this
     */
    public function setNumber(string $value): Application {
        $this->number = $value;

        return $this;
    }

    /**
     * 设置公司注册地址
     *
     * @param string $value
     * @return $this
     */
    public function setAddress(string $value): Application {
        $this->address = $value;

        return $this;
    }

    /**
     * 设置公司电话
     *
     * @param string $value
     * @return $this
     */
    public function setTel(string $value): Application {
        $this->tel = $value;

        return $this;
    }

    /**
     * 设置开户行
     *
     * @param string $value
     * @return $this
     */
    public function setBank(string $value): Application {
        $this->bank = $value;

        return $this;
    }

    /**
     * 设置银行账号
     *
     * @param string $value
     * @return $this
     */
    public function setAccount(string $value): Application {
        $this->account = $value;

        return $this;
    }

    /**
     * 设置手机号码
     *
     * @param string $value
     * @return $this
     */
    public function setPhone(string $value): Application {
        $this->phone = $value;

        return $this;
    }

    /**
     * 设置接收邮箱
     *
     * @param string $value
     * @return $this
     */
    public function setEmail(string $value): Application {
        $this->email = $value;

        return $this;
    }

    /**
     * 获取参数
     *
     * @param array $data
     * @return array
     * @throws InvalidArgumentException
     */
    private function getParams(array $data = []): array {
        $params = [
            'name' => $data['name'] ?? $this->name,
            'number' => $data['number'] ?? $this->number,
            'address' => $data['address'] ?? $this->address,
            'tel' => $data['tel'] ?? $this->tel,
            'bank' => $data['bank'] ?? $this->bank,
            'account' => $data['account'] ?? $this->account,
            'phone' => $data['phone'] ?? $this->phone,
            'email' => $data['email'] ?? $this->email,
        ];

        if (empty($params['name'])) {
            throw new InvalidArgumentException('name is required');
        }

        if (empty($params['number'])) {
            throw new InvalidArgumentException('number is required');
        }

        return $params;
    }

    /**
     * 发票提交
     *
     * @param array $data
     * @return bool
     * @throws InvalidArgumentException
     * @throws \Qifen\ConsoleSdk\Kernel\Exceptions\BadResponseException
     */
    public function submit(array $data = []): bool {
        $json = $this->getParams($data);

        $res = $this->request($this->getApi('invoice'), compact('json'));

        return $this->isSuccess($res);
    }
}