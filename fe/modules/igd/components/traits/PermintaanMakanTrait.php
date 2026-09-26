<?php

namespace app\modules\igd\components\traits;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;


trait PermintaanMakanTrait {
    public function actionPermintaanMakan()
    {
        $pendaftaran_id = Yii::$app->request->get('id');
        if( Yii::$app->request->post() ){
            return $this->helper->guzzleExec($this->_restIgd, [
                'url' => 'permintaan-makan/simpan-permintaan-makan',
                'method' => 'POST',
                'payload' => [
                    'form_params' => [
                        'pendaftaran_id' => $this->helper->decrypt($pendaftaran_id),
                        'peg_pemesan_id' => Yii::$app->docoVars->user('id'),
                        'kelaspelayanan_id' => $this->_data_pasien['kelaspelayanan_id'],
                        'penjamin_id' => $this->_data_pasien['penjamin_id'],
                        'detail_diet' => Yii::$app->request->post('detaildiet', [])
                    ]
                ],
                'returnResponse' => true
            ]);
        }
        $getApi = $this->helper->guzzleExec($this->_restIgd, [
            'url' => 'permintaan-makan/api-form',
            'method' => 'GET'
        ]);
        $jenisDiet = isset($getApi['jenis-diet']) ? $getApi['jenis-diet'] : [];
        $waktuDiet = isset($getApi['waktu-diet']) ? $getApi['waktu-diet'] : [];
        return $this->renderAjax('permintaan-makan/_form', compact('jenisDiet', 'waktuDiet'));
    }

    public function actionGetMenuDiet()
    {
        return $this->helper->guzzleExec($this->_restIgd, [
            'url' => 'permintaan-makan/get-menu-diet',
            'method' => 'get',
            'payload' => [
                'query' => [
                    'jenisdiet_id' => Yii::$app->request->get('jenisdiet_id', null),
                    'kelaspelayanan_id' => $this->_data_pasien['kelaspelayanan_id'],
                    'penjamin_id' => $this->_data_pasien['penjamin_id']
                ]
            ],
            'returnResponse' => true
        ]);
    }

}