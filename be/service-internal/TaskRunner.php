<?php

require(__DIR__ . '/../vendor/autoload.php');
require (__DIR__ . '/../vendor/yiisoft/yii2/Yii.php');
$config = require __DIR__ . '/Config/config.php';

try {
    $timezone = date_default_timezone_get();
    (new \yii\console\Application($config));
    date_default_timezone_set($timezone);
    $post = isset($_POST['send_to']) ? $_POST['send_to'] : [];
    $date = date('Y-m-d');
    $logName = $date . '-workers.log';
    foreach ($post as $key => $value) {
        $request = str_replace("-", "\\", $key);
        $explode = explode("-", $key);

        $firstKey = isset($config['params']['first_key']) ? $config['params']['first_key'] : null;
        $groupKey = isset($explode[0]) ? strtolower($explode[0]) : null;

        $signature = $firstKey . DIRECTORY_SEPARATOR . $groupKey . DIRECTORY_SEPARATOR . date('Y-m-d');
        $output = hash('sha256', $signature);

        $config['params']['signature'] = $output;
        $config['params']['service'] = $groupKey;

        $class = "\Integrasi\Service\\{$request}";
        $object = new $class($value, $config);
        if ($response = $object->execute()) {
            error_log($response ." \n", 3, __DIR__ . '/Logs/' . $logName );
        } else {
            error_log("Gagal \n", 3, __DIR__ . '/Logs/' . $logName );
        }
    }
} catch (\Throwable $e) {
    $error = [
            'Message' => $e->getMessage(),
            'File' => $e->getFile(),
            'Line' => $e->getLine(),
    ];
    error_log(json_encode($error)."\n", 3, __DIR__ . '/Logs/' . $logName );
}
