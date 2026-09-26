<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\actions\Allow;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\Rak;
use yii\db\Expression;

class GetListRakAction extends Action {
    public function run() {
        try {
            $request = Yii::$app->request;
            $ruangan_id = $request->get('ruangan_id', null);

            // $model = new Rak;
            // $query = $model::find()->where([
            //     'ruangan_id' => $ruangan_id,
            // ]);

            $returnData = (new \yii\db\Query())
            ->select([
                'laci.rakobat_id',
                'laci.ruangan_id',
                new Expression("concat_ws(' / ', 'Rak :' || case when rak.rakobat_id is null then laci.rakobat_nama else rak.rakobat_nama end, 
                'Locator :' || case when rak.rakobat_id is not null then laci.rakobat_nama else null end) as nama_rak_laci")
            ])
            ->from('rakobat_m laci')
            ->join('LEFT JOIN','rakobat_m rak','rak.rakobat_id = laci.parentrakobat_id AND rak.is_deleted = false')
            ->where(['laci.ruangan_id' => $ruangan_id])
            ->andWhere(['laci.is_deleted' => false]);
            
            return $returnData->all();
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
