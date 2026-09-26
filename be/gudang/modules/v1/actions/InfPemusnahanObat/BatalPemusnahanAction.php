<?php

/**
 * @author : Ardi (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\InfPemusnahanObat;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;
use app\modules\v1\models\PemusnahanObat;
use app\modules\v1\models\PemusnahanObatDetail;

class BatalPemusnahanAction extends Action {
    public function run($pemusnahanobat_id) {
    	try {
            $connection = Yii::$app->db;
            $transaction = $connection->beginTransaction();

            $model = PemusnahanObat::findOne($pemusnahanobat_id);
            $model->is_deleted = true;
            if($model->save()){
            	PemusnahanObatDetail::updateAll(['is_deleted'=>TRUE],'pemusnahanobat_id = :pemusnahanobat_id',[':pemusnahanobat_id'=>$pemusnahanobat_id]);
                $transaction->commit();

                return [
                    'status' => 200,
                    'message' => 'Berhasil',
                    'text' => 'Status batal berhasil di update'
                ];
            }

            return [
                'status' => 422,
                'data' => $model->errors
            ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }
}