<?php
$env = \Common\Common\Util::detectEnv();
$conf = [
    'DEFAULT_FILTER' => 'htmlspecialchars,trim'
];
$erp = include dirname(__FILE__) . '/erp.php';
return array_merge(
    $erp
);