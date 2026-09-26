<?php

namespace app\modules\v1\actions\RujukanBantaran;

use Yii;
use yii\base\Action;
use yii\db\Exception;
use app\modules\v1\models\Cppt;

class RemoveCpptAction extends Action
{
    public function run()
    {
        $request = Yii::$app->request;
        $cppt_id = $request->post('cppt_id');
        
        if (empty($cppt_id)) {
            Yii::$app->response->statusCode = 422;
            return ['message' => 'CPPT ID harus diisi.'];
        }
        
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        
        try {
            // Get user info
            $jwt = !empty(Yii::$app->jwt) ? Yii::$app->jwt->user : null;
            $user_id = !empty(Yii::$app->jwt) ? $jwt->loginpemakai_id : null;
            
            $model = $this->checkIfExists($cppt_id);
            
            if (!$model) {
                $transaction->rollBack();
                Yii::$app->response->statusCode = 404;
                return ['message' => 'Data CPPT tidak ditemukan.'];
            }
            
            $model->is_deleted = true;
            $model->last_modified_by = $user_id;
            $model->last_modified_date = date('Y-m-d H:i:s');
            if (!$model->save()) {
                $transaction->rollBack();
                Yii::error("Gagal menghapus CPPT ID {$cppt_id} oleh user ID {$user_id}.", 'rujukan-bantaran');
                Yii::$app->response->statusCode = 500;
                return ['message' => 'Gagal menghapus data CPPT.'];
            }
            
            $transaction->commit();
            
            return [
                'message' => 'Data CPPT berhasil dihapus.'
            ];
            
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            Yii::error('RemoveCpptAction - Database Error: ' . $e->getMessage(), 'rujukan-bantaran');
            Yii::$app->response->statusCode = 500;
            return ['message' => 'Terjadi kesalahan database: ' . $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::error('RemoveCpptAction - Error: ' . $e->getMessage(), 'rujukan-bantaran');
            Yii::$app->response->statusCode = 500;
            return ['message' => 'Terjadi kesalahan: ' . $e->getMessage()];
        }
    }

    private function checkIfExists($cppt_id)
    {
        return (new Cppt)->find()
            ->andWhere(['cppt_id' => $cppt_id, 'is_deleted' => false])
            ->one();
    }
}
