<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\LapMutasiObatAlkes;

use Yii;
use yii\base\Action;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use app\modules\v1\models\Ruangan;

class GetListRuanganAction extends Action {
    public function run() {
        try {
            return ArrayHelper::map(Ruangan::find()->all(), 'ruangan_id', 'ruangan_nama');
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
