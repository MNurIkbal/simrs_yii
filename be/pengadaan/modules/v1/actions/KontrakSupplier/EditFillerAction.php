<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\KontrakSupplier;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use app\modules\v1\models\KontrakSupplierHeaderView as KontrakSupplierHeader;
use app\modules\v1\models\KontrakSupplierView as KontrakSupplierDetail;
use app\modules\v1\models\Payterm;
use app\modules\v1\models\InfoSatuanKonversi;
use Doco\components\DocoRestActiveFilter;
use yii\helpers\ArrayHelper;

class EditFillerAction extends Action {
    public function run($id) {
        try{
            $header = KontrakSupplierHeader::find()
                        ->where(['kontraksupplier_id' => $id])
                        ->asArray()
                        ->one();
            if(is_null($header))
                throw new \Exception("Data dengan id:{$id} tidak ditemukan", 1);

            $detail = KontrakSupplierDetail::find()
                        ->where(['kontraksupplier_id' => $id])
                        ->asArray()
                        ->all();
            $payterm = Payterm::find()->select([
                'payterm_id',
                'payterm_nama',
            ])->where([
                'is_active' => true,
            ])->asArray()
            ->all();
            return [
                'data' => [
                    'header' => $header,
                    'detail' => $detail,
                    'payterm' => $payterm
                ]
            ];
        }catch(\Exception $e){
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}