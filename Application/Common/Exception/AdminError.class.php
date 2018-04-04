<?php
namespace Common\Exception;

use Common\Exception\EBase;

class AdminError extends EBase
{

    const MSG_BUSING = "服务器忙，请稍后再试";

    const MSG_ILLEGAL = "您非法访问";

    const MSG_NO_RIGHT = "您没有权限访问，请联系管理员";

    public function getTemplate()
    {
        return 'admin_error';
    }
}