<?php

namespace app\components;

use Yii;
use yii\web\Response;
use app\components\DocoHelpers;

class ExportExcelEngine
{
    public function generateRandomString($yiiRestfulParams)
    {
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams['randString'] = $randString;
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);

        return $randString;
    }

	public function processSync($randString, $rest, $backendUrl)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);

        $session['randString'] = $randString;
        return (new DocoHelpers)->guzzleExec($rest, [
            'url' => $backendUrl,
            'payload' => [
                'query' => $session,
            ],
        ]);
    }

    public function downloadFile($key, $title)
    {
        try {
            $baseUrl = Yii::$app->params->goexcelexporter['uri_api'];
            $url = $baseUrl . '/download';
            $client = new \GuzzleHttp\Client();
            $response = $client->request('GET', $url, [
                'query' => ['filename' => $key],
                'stream' => true,
            ]);

            $body = $response->getBody();

            // Clean output buffer to prevent corrupting the file
            while (ob_get_level()) {
                ob_end_clean();
            }

            // Send download headers
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment; filename="' . $title . '.xlsx"');
            header('Cache-Control: no-cache');
            header('Pragma: no-cache');

            // Manually stream the response
            while (!$body->eof()) {
                echo $body->read(8192); // 8 KB chunks
                flush();
            }

            exit;

        } catch (\Exception $e) {
            Yii::$app->response->statusCode = 500;
            Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            return ['error' => $e->getMessage()];
        }
    }
}