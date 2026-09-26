<?php

namespace Doco\penjaminasuransi\actions\InformasiPasienRajalBpjs;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class IndexAction extends BaseCurrentAction
{
    private function getDataFilter()
    {
        $queryParams = [];
        $helper = new DocoHelpers();
        $result = $helper->guzzleExec($this->_restPenjamin, [
            'url' => 'inf-pasien-rajal-bpjs/get-request',
            'payload' => [
                'query' => $queryParams
            ]
        ]);
        $result = ArrayHelper::getValue($result, 'data');
        return $result;
    }

    public function run()
    {
        $helper = new DocoHelpers();
        $title = $this->_title;
        $status = $instalasi = $ruangan = [];
        try {
            $queryParams = [];
            $result = $this->getDataFilter();
            $ruangan = ArrayHelper::getValue($result, 'ruangan');
            $status = ArrayHelper::getValue($result, 'status_kunjungan');
            $carabayar = ArrayHelper::getValue($result, 'carabayar');
            $sessionId = Yii::$app->docoVars->user("id");
            $cacheDiagnosa = Yii::$app->cache->get("cache_diagnosa_" . $sessionId);
            if (!$cacheDiagnosa) {
                $response = $this->_restPenjamin->get('inf-pasien-rajal-bpjs/get-list-diagnosa');
                $response = json_decode($response->getBody(), true);
                $diagnosa = $response['response']['diagnosa'];
                Yii::$app->cache->set("cache_diagnosa_" . $sessionId, $diagnosa);
            }
            return $this->controller->render('index', get_defined_vars());
        } catch (RequestException $e) {
            var_dump($e->getMessage());
            die();
        }
    }
}
