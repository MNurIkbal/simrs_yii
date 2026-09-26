<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.lukman@sirs.co.id)
 * A product of PT. Citra Raya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\actions\PurchaseRequisition;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use app\modules\v1\models\KonfigFarmasi;
use app\modules\v1\models\BaseCalRoFnConfigurable;

class GetPemakaianAction extends Action
{
    public function run()
    {
        try {
            $request = Yii::$app->request;
            $item_id = $request->get('oid', false);

            $get_config = KonfigFarmasi::find()->select(['basecalc_config'])->one()->basecalc_config;

            $getDataPemakaian = BaseCalRoFnConfigurable::getData(
                null,
                ArrayHelper::getValue($get_config, 'pemakaian_ruangan', "false"),
                ArrayHelper::getValue($get_config, 'bmhp', "false"),
                ArrayHelper::getValue($get_config, 'mutasi', "false"),
                false
            )
            ->select(['obatalkes_id', 'last_7', 'last_14', 'last_30'])
            ->where(['in','obatalkes_id', $item_id])
            ->asArray()
            ->one();

            if($getDataPemakaian == null) {
                $getDataPemakaian = [
                    'obatalkes_id'  => $item_id,
                    'last_7'        => 0,
                    'last_14'       => 0,
                    'last_30'       => 0
                ];
            }

            return $getDataPemakaian;
        } catch (\Exception $e) {
            return [
                'obatalkes_id'  => $item_id,
                'last_7'        => 0,
                'last_14'       => 0,
                'last_30'       => 0
            ];
        }
    }
}
