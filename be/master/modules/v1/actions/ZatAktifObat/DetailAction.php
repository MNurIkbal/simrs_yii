<?php

/**
 * @author: Lukman Hakim (muhamad.lukman@sirs.co.id)
 * A product of PT Citra Raya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\actions\ZatAktifObat;

use Yii;
use yii\base\Action;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfoZatAktifObatView;

class DetailAction extends Action {
    public function run($id) {
        $model = new InfoZatAktifObatView;
        $query = $model->find()
            ->select([
                'obatalkes_nama', 
                'obatalkes_kode', 
                'jenisobatalkes_nama'
            ])->distinct();
        $query->where(['obatalkes_id' => $id]);
        $result = $query->asArray()->one();

        return $result;
    }
}
