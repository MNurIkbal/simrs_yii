<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\PurchaseOrderManual;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\pengadaan\models\PoManualForm;
use app\modules\pengadaan\models\PoManualDetailForm;

class IndexAction extends Action {
    public function run() {
        $title = $this->controller->_title;
        $model = new PoManualForm;
        $pegawai_id = Yii::$app->user->identity->id_pegawai;
        $dataUser = [
            'id' => Yii::$app->user->identity->id_pegawai,
            'text' => Yii::$app->user->identity->nama_pegawai
        ];

        try {
            $request = $this->controller->guzzleExec($this->controller->_restPengadaan, [
                'url' => 'purchase-order-manual/generate-api',
                'method' => 'get',
                'payload' => [
                    'query' => [
                        'pegawai_id' => $pegawai_id,
                    ]
                ]
            ]);
        } catch (RequestException $e) {
            $request = [
                'instalasi' => [],
                'pegawai' => [],
                'payterm' => [],
                'pajak' => [],
                'supplier' => [],
                'pegawaiLogin' => '',
                'mapValue' => [],
            ];
        }

        return $this->controller->render('index', get_defined_vars());
    }
}
