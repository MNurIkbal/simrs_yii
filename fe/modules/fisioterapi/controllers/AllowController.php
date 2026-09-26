<?php

namespace Doco\fisioterapi\controllers;

use Yii;
use app\components\DocoController;
use yii\helpers\ArrayHelper;
use app\components\DocoConstants;
use yii\web\Response;
use yii\base\Exception;

class AllowController extends DocoController
{
    protected $allowAction = ['*'];
    protected $restFisioterapi;
    public function init()
    {
        parent::init();
        $this->restFisioterapi = Yii::$app->docoRest->fisioterapi;
    }

    public function actionGetDiagnosa($q = '', $type = 'diagnosa_masuk', $all_text = 0, $id_with_text = 0, $is_perawat = 0, $page = null)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $result = ['results' => []];
        try {
            $limit = 10;
            $offset = ($page - 1) * $limit;
            $typeMap = [
                'diagnosa_fungsi'   => DocoConstants::VAR_KELOMPOK_DIAGNOSA_FUNGSI,
                'prosedur_kerja'    => DocoConstants::VAR_KELOMPOK_DIAGNOSA_KERJA,
            ];
            $type = $typeMap[$type] ? $typeMap[$type] : DocoConstants::VAR_KELOMPOK_DIAGNOSA_AWALAN;
            
            $response = $this->guzzleExec($this->restFisioterapi, [
                'url' => 'allow/get-diagnosa',
                'method' => 'get',
                'payload' => [
                    'query' => [
                        'q' => $q,
                        'type' => $type,
                        'page' => $page,
                        'offset' => $offset,
                        'limit' => $limit,
                        'is_perawat' => $is_perawat
                    ]
                ]
            ]);
            foreach ($response as $value) {
                $result['results'][] = $this->formatDiagnosaItem($value, $all_text, $id_with_text);
            }

            $result['pagination'] = ['more' => !empty($response)];
        } catch (\Throwable $e) {
            $result['error'] = $e->getMessage();
        }

        return $result;
    }

    private function formatDiagnosaItem($value, $all_text, $id_with_text)
    {
        $diagnosaId = ArrayHelper::getValue($value, 'diagnosa_id');
        $diagnosaKode = ArrayHelper::getValue($value, 'diagnosa_kode');
        $diagnosaNama = ArrayHelper::getValue($value, 'diagnosa_nama');

        $text = $diagnosaKode . ' - ' . $diagnosaNama;

        if ($all_text) {
            return ['id' => $text, 'text' => $text];
        }

        if ($id_with_text) {
            return ['id' => $diagnosaId . '_' . $text, 'text' => $text];
        }

        return ['id' => $diagnosaId, 'text' => $text];
    }
}

