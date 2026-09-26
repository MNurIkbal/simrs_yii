<?php

namespace app\modules\v1\actions\InfRetur;

use Yii;
use yii\base\Action;
use app\modules\v1\models\PasienReturV;

class GetDataPasienRetur extends Action {
    public function run()
    {
        $request = Yii::$app->request->get();
        $identifier = $request['identifier'];
        $status_bayar = null;
        $history = null;

        $detail = PasienReturV::find()
            ->where(['nama_pasien' => $identifier])
            ->orWhere(['no_pendaftaran' => $identifier])
            ->orWhere(['no_rekam_medik' => $identifier])
            ->asArray()
            ->all();

        if(empty($detail)) {
            return [
                'status' => 422,
                'message' => 'Data Pasien dengan nama pasien / no. pendaftaran / no.rm ' . $identifier . ' tidak ditemukan.'
            ];
        }

        return [
            'status' => 200,
            'detail' => $detail,
        ];
    }
}