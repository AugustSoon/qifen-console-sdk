<?php

namespace Qifen\ConsoleSdk\Chatgpt;

use Qifen\ConsoleSdk\Kernel\Base;
use Qifen\ConsoleSdk\Kernel\Traits\Utils;

class Application extends Base
{
    use Utils;

    protected ?string $_conversation_id = null;
    protected ?string $_parent_message_id = null;
    protected mixed $_server = null;


    /**
     * 设置消息ID
     */
    public function setConversationId(?string $conversationId = null) {
        $this->_conversation_id = $conversationId;
        return $this;
    }

    /**
     * 设置消息ID
     */
    public function setParentMessageId(?string $parentMessageId = null) {
        $this->_parent_message_id = $parentMessageId;
        return $this;
    }

   
    /**
     * 设置服务账号
     */
    public function setServer(mixed $server = null) {
        $this->_server = $server;
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
    public function sendMessage(string $carry, string $subject) {
        $json = [
            'carry' => $carry,
            'subject' => $subject,
            'notice_url' => $this->getConfig('noticeUrl'),
            'conversation_id' => $this->_conversation_id,
            'parent_message_id' => $this->_parent_message_id,
            'server' => $this->_server
        ];

        $res = $this->request($this->getApi('chatgpt'), compact('json'));

        $data = $this->getResponse($res);

        return $data['carry'] ?? '';
    }
}