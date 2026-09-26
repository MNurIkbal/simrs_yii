<?php 

require(__DIR__ . '/vendor/autoload.php');

use hollodotme\FastCGI\Client;
use hollodotme\FastCGI\Requests\PostRequest;
use hollodotme\FastCGI\SocketConnections\UnixDomainSocket;

$ini = @parse_ini_file(dirname(__DIR__) . '/../dcms/config/env/.env', true);

$setingRedis = isset($ini['redis']) ? $ini['redis'] : [];
$setingFpm = isset($ini['php-fpm']) ? $ini['php-fpm'] : [];

$redisHost = isset($setingRedis['hostname']) ? $setingRedis['hostname'] : 'localhost';
$redisPort = isset($setingRedis['port']) ? $setingRedis['port'] : 6379;
$chanel = 'integerasi';
$phpFpm = isset($setingFpm['path']) ? $setingFpm['path'] : '';

$redis = new \Redis();
$connected = $redis->connect($redisHost, $redisPort);

if ($connected) {
    $redis->subscribe([$chanel], function (\Redis $redis, string $channel, string $message) use ($phpFpm) {
            $messageArray = json_decode($message, true);
            $body = http_build_query($messageArray);
            $blocking = isset($messageArray['blocking']) ? $messageArray['blocking'] : null;

            $connection = new UnixDomainSocket($phpFpm,300000,300000);
            $fpmClient = new Client($connection);

            $request = new PostRequest(dirname(__DIR__) .'/TaskRunner.php',$body);

            if ($blocking) {
                $response = $fpmClient->sendRequest($request);
                echo "Respon Worker :" . $response->getBody() . "\n";
            } else {
                $processId = $fpmClient->sendAsyncRequest($request);
                echo "Respon Worker ID : {$processId}\n";
            }
        }
    );
} else {
    echo "Tidak ada koneksi redis";
}