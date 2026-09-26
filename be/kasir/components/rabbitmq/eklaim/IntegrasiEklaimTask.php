<?php

namespace app\components\rabbitmq\eklaim;

use Yii;
use Doco\rabbitmq\task\IntegrasiTask;
use GuzzleHttp\Client;

class IntegrasiEklaimTask extends IntegrasiTask
{
    public function prosesSync()
    {
        if($this->type_sinkron == 'sinkron') {
            $this->IntegrasiEklaim();
        } 

        if($this->type_sinkron == 'hapus') {
            $this->IntegrasiHapusEklaim();
        }
    }

    private function IntegrasiEklaim()
    {
        $params = Yii::$app->params;
        $urlBackend = isset($params['url_backend']) ? $params['url_backend'] : 'http://web:8858/';
        $guzzle = new Client([
            'base_uri' => $urlBackend.'penjaminasuransi/v1/',
            'verify' => false,
            'headers' => [
                'user-agent' => 'cli',
            ]
        ]);

        $result = $guzzle->post('single-sync/integrasi-eklaim', [
            'query' => [
                'pendaftaran_id' => $this->pendaftaran_id,
                'pembayaran_id' => $this->pembayaran_id,
                'instalasi' => $this->instalasi
            ]
        ]);

        $result = json_decode($result->getBody(), true);
        Yii::error(json_encode($result));
    }

    private function IntegrasiHapusEklaim()
    {
        $params = Yii::$app->params;
        $urlBackend = isset($params['url_backend']) ? $params['url_backend'] : 'http://web:8858/';
        $guzzle = new Client([
            'base_uri' => $urlBackend.'penjaminasuransi/v1/',
            'verify' => false,
            'headers' => [
                'user-agent' => 'cli',
            ]
        ]);

        $result = $guzzle->post('single-sync/integrasi-hapus-eklaim', [
            'query' => [
                'pendaftaran_id' => $this->pendaftaran_id,
                'pembayaran_id' => $this->pembayaran_id,
            ]
        ]);
        
        $result = json_decode($result->getBody(), true);
        Yii::error(json_encode($result));
    }
}