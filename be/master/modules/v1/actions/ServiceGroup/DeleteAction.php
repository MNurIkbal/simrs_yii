<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\ServiceGroup;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use app\modules\v1\models\ServiceGroup;
use app\modules\v1\models\JenisObatAlkes;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;

class DeleteAction extends Action {
    public function run($id) {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        try {
            $model = JenisObatAlkes::find()
                ->where(['servicegroup_id' => $id])
                ->count();

            if($model > 0) {
                $result['status'] = 500;
                $result['text'] = "Tidak bisa menghapus Service Group, data sudah digunakan di master lain.";
            } else {
                $model = ServiceGroup::findOne($id);
                if ($model) {
                    $model->is_deleted = true;
                    $model->deleted_date = date('Y-m-d H:i:s');
                    $model->save(false);
                    Yii::$app->cache->delete(DocoConstants::CACHE_SERVICE_GROUP);
                    $transaction->commit();
                    $result = [
                        'status' => 200,
                        'title' => 'Hapus Berhasil',
                        'text' => 'Hapus Service Group Berhasil',
                    ];
                } else {
                    $transaction->rollBack();
                    $result['status'] = 422;
                    $result['text'] = "Gagal Menghapus Data";
                }
            }
            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage(),
            ];
        }
    }
}