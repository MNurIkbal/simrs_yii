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
use app\modules\v1\models\ZatAktifObat;

class RemoveAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $post = $request->post();

        $model = ZatAktifObat::deleteAll([
                'and',
                    ['obatalkes_id' => $post['obatalkes_id']],
                    ['in', 'zataktif_id', $post['zataktif_id']],
                    ['is_primary' => false]
                ]);

        return [
            'status' => 200,
            'message' => 'Data berhasil dihapus.'
        ];
    }
}
