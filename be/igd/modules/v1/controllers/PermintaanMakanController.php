<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\Services\GiziService;
use app\modules\v1\models\JenisDiet;
use app\modules\v1\models\MenuDietView;

class PermintaanMakanController extends DocoActiveController {
    public $modelClass = 'app\modules\v1\models\ruangan';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        return $actions;
    }

    /**
     * Function for get jenis-diet and waktu-diet for permintaan makan form
     * 
     * @return JSON
     * @author : Rizqi Fitrianto (rizqi@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionApiForm()
    {
        return [
            'jenis-diet' => ArrayHelper::map(JenisDiet::find()->select([
                'jenisdiet_id as id',
                'jenisdiet_nama as text',
            ])->asArray()->all(), 'id', 'text'),
            'waktu-diet' => ArrayHelper::map($this->getLookupByType('waktu')->select(['lookup_id as id', 'lookup_value as text'])->asArray()->all(), 'id', 'text'),
        ];
    }

    /**
     * Function get menu diet based on jenisdiet_id
     * 
     * @param Integer jenisdiet_id
     * @return JSON
     * @author : Rizqi Fitrianto (rizqi@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionGetMenuDiet()
    {
        return (new GiziService)->getMenuDiet([
            'jenisdiet_id' =>  Yii::$app->request->get('jenisdiet_id', null),
            'kelaspelayanan_id' =>  Yii::$app->request->get('kelaspelayanan_id', null),
            'penjamin_id' =>  Yii::$app->request->get('penjamin_id', null),
            'ruangan_id' => Yii::$app->jwt->ruangan_id
        ]);
    }

    public function actionSimpanPermintaanMakan()
    {
        $post = Yii::$app->request->post();
        $payloadPermintaanMakan = [
            'pendaftaran_id' => $post['pendaftaran_id'],
            'peg_pemesan_id' => $post['peg_pemesan_id'],
            'tgl_permintaanmakan' => date('Y-m-d H:i:s'),
            'penjamin_id' => $post['penjamin_id'],
            'kelaspelayanan_id' => $post['kelaspelayanan_id'],
            'detailPermintaanMakan' => $post['detail_diet'],
        ];
        (new GiziService)->permintaanMakan($payloadPermintaanMakan);
        return $this->responseJson(200, 'Simpan Permintaan Makan Berhasil!');
    }
}

