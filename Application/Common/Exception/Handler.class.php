<?php
namespace Common\Exception;

use Common\Exception;

class Handler
{

    /**
     *
     * @param \Common\Exception\EBase $e            
     */
    public static function handle($e)
    {
        $isAjax = false;
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
            if ('xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                $isAjax = true;
            }
        }
        if ($isAjax) {
            $ret = array(
                '_c' => $e->getCode(),
                '_m' => $e->getMessage()
            );
            if (empty($ret['_m'])) {
                $ret['_m'] = '';
            }
            $data = $e->get();
            if (! empty($data)) {
                foreach ($data as $k => $v) {
                    $ret[$k] = $v;
                }
            }
            echo json_encode($ret);
        } else {
            if ($e instanceof MError) {
                self::showPage($e->getTemplate(), array(
                    'message' => $e->getMessage()
                ));
            }
            if ($e instanceof AdminError) {
                self::showPage($e->getTemplate(), array(
                    'message' => $e->getMessage()
                ));
            }
            self::showPage($e->getTemplate(), array(
                'message' => $e->getMessage()
            ));
        }
    }

    protected static function showPage($tpl, $data = null)
    {
        if (is_array($data) && ! empty($data)) {
            extract($data, true);
        }
        include WEB_ROOT . 'Application/Common/Exception/Page/' . $tpl . '.php';
        exit();
    }
}