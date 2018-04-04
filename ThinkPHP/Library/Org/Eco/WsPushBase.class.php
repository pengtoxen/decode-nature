<?php
namespace Org\Eco;

class WsPushBase
{
    use \Common\Traits\Singleton;

    protected $wsuri = array();

    protected $crypt = array();

    protected function __construct()
    {}

    public function tokenWs()
    {
        $data = [
            'time' => time()
        ];
        $data = $this->crypt['push']->encrypt(json_encode($data));
        return $this->wsuri['ws']['uri'] . '?ty=bs&auth=' . urlencode($data);
    }

    public function send($data)
    {
        $message = $this->crypt['push']->encrypt(json_encode($data));
        $message = str_pad('bs', 6, "-", STR_PAD_LEFT) . '-' . $message;
        $data = $this->send_tcp_message($message);
        $data = json_decode($data, true);
        return isset($data['c']) && $data['c'] === '0';
    }

    protected function send_tcp_message($message)
    {
        $message = $message . "\r\n";
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        socket_connect($socket, $this->wsuri['push']['domain'], $this->wsuri['push']['port']);
        $num = 0;
        $length = strlen($message);
        do {
            $buffer = substr($message, $num);
            $ret = socket_write($socket, $buffer);
            $num += $ret;
        } while ($num < $length);
        
        $ret = '';
        do {
            $buffer = socket_read($socket, 1024, PHP_BINARY_READ);
            $ret .= $buffer;
        } while (strlen($buffer) == 1024);
        
        socket_close($socket);
        return $ret;
    }
}