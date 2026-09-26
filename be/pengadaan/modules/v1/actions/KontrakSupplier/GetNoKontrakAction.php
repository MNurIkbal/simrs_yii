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
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use app\components\PengadaanComponent;
use app\modules\v1\models\KontrakSupplier;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;

class GetNoKontrakAction extends Action
{
    public function run()
    {
        try {
            $model = KontrakSupplier::find()->where([
                'not', ['kontraksupplier_no' => null, 'kontraksupplier_no' => ""]
            ])->asArray()->all();
            $list_no_kontraksupplier = array_column($model, 'kontraksupplier_no');

            return [
                'data' => $list_no_kontraksupplier
            ];
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }
}
