<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\InfRetur;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\PendaftaranObatView;
use app\modules\v1\models\DetailPendaftaranRetur;
use app\modules\v1\models\ReturResep;
use Doco\components\DocoConstants;

class GetPendaftaranObatAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $pendaftaranId = $request->get('pendaftaran_id');
        $retur_id = $request->get('retur_id');
        $type = $request->get('type');
        $detail = [];

        $retur = ReturResep::find()->select(['returresep_id', 'status_retur'])->where(['returresep_id' => $retur_id])->asArray()->one();
        if($type == 'tambah'){
            $model = new DetailPendaftaranRetur;
            $query = $model::find()->andWhere(['pendaftaran_id' => $pendaftaranId]);
        }else{
            $model = new PendaftaranObatView;
            $query = $model::find()->andWhere(['pendaftaran_id' => $pendaftaranId, 'returresep_id' => $retur_id]);
            if($retur['status_retur'] == DocoConstants::RETUR_VERIFIKASI) {
                $query->andWhere(['>', 'qty_retur', '0']);
            }
        }
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $returHeader = ReturResep::find()->select(['returresep_id', 'penjualanresep_id'])->where(['returresep_id' => $retur_id])->asArray()->one();

        return [
            'data' => $query->asArray()->all(),
            'is_retur_pendaftaran' => is_null($returHeader['penjualanresep_id']) ? true : false
        ];
    }
}