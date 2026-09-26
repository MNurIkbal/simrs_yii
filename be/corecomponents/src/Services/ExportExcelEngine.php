<?php

namespace Doco\Services;

use Yii;

class ExportExcelEngine
{
    public function sendRequest($key = null, $query = null)
    {
        $this->paramValidation($key, $query, false);
        return $this->executeCurl($key, $query);
    }

    public function progressReceiver($key, $status, $message, $percentage)
    {
        Yii::$app->redis->executeCommand('PUBLISH', [
            'channel' => 'export-excel:'. $key,
            'message' => json_encode([
                'status' => $status, 
                'messageProcess' => $message,
                'progress' => $percentage,
                'key' => $key
            ]),
        ]);
    }

    private function paramValidation($key, $query, $file = false)
    {
        if (is_null($key)) {
            throw new \Exception("Key must be set, Please check your param", 1);
        }

        if (is_null($query) && !$file) {
            throw new \Exception("Query must be set, Please check your param", 1);
        }
    }

    private function executeCurl($key, $query = null)
    {
        $baseUrl = Yii::$app->params['goexcelexporter']['uri_api'];
        $url = $baseUrl . '/export';
        $method = 'POST';

        $postData = json_encode([
            'key' => $key,
            'query' => $query
        ]);

        $curl = curl_init();

        $headers = [
            'Content-Type: application/json',
        ];

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_BINARYTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        curl_setopt($curl, CURLOPT_POSTFIELDS, $postData);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);

        if ($err) {
            throw new \Exception("cURL Error: " . $err);
        }

        return $response;
    }
}
