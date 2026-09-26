<?php
/**
 * @author : Sulthan Zaidan Fauzi (sulthanzaidan1026@gmail.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use app\modules\v1\models\PenjaminDiskon;
use app\modules\v1\models\PenjaminDiskonView;
use yii\helpers\ArrayHelper;
use Doco\components\DocoMessages;

class PenjaminDiskonController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\PenjaminDiskon';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["update"] = ["POST"];
        $verbs["delete"] = ["DELETE", "POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['view']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['create']);
        return $actions;
    }

    public function actionView($id = null)
    {
        $model = new PenjaminDiskonView;
        $query = $model->find();
        if ($id) $query->andWhere(['penjamindiskon_id' => $id]);
        Yii::error($query->one());
        return $query->one();
    }

    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $post = isset($post['PenjaminDiskonForm']) ? $post['PenjaminDiskonForm'] : $post;

            if (isset($post['penjamin_id']) && isset($post['carabayar_id'])) {
                $data = PenjaminDiskonView::find()
                    ->where(['penjamin_id' => $post['penjamin_id']])
                    ->andWhere(['carabayar_id' => $post['carabayar_id']])
                    ->andWhere(['is_deleted' => false])
                    ->asArray()->one();
                
                if (!empty($data)) {
                    return $this->responseJson(400, 'Data otoritas penjamin sudah ada');    
                }
            } else {
                return $this->responseJson(422, 'Error Request Exception');
            }

            $model = new PenjaminDiskon;
            $model->attributes = $post;

            if($model->validate() && $model->save()){
                return $this->responseJson(200, DocoMessages::SUC_MESSAGE);
            }else{
                throw new \Exception (implode("<br />", ArrayHelper::getColumn($model->errors, 0, false)));
            }
        } catch (\yii\db\Exception $e) {
            $this->logError($e);
			return $this->responseJson(500, $e->getMessage());
        } catch (\Exception $e) {
            $this->logError($e);
            return $this->responseJson(400, $e->getMessage());
        }
    }

    public function actionUpdate($id)
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();

            if (isset($post['penjamin_id']) && isset($post['carabayar_id'])) {
                $data = PenjaminDiskonView::find()
                    ->where(['!=', 'penjamindiskon_id', $id])
                    ->andWhere(['penjamin_id' => $post['penjamin_id']])
                    ->andWhere(['carabayar_id' => $post['carabayar_id']])
                    ->andWhere(['is_deleted' => false])
                    ->asArray()->one();
                
                if (!empty($data)) {
                    return $this->responseJson(400, 'Data otoritas penjamin sudah ada');    
                }
            } else {
                return $this->responseJson(422, 'Error Request Exception');
            }

            $model = PenjaminDiskon::findOne($id);
            $model->attributes = $post;
            
            if($model->validate() && $model->save()){
                return $this->responseJson(200, DocoMessages::SUC_MESSAGE);
            }else{
                throw new \Exception (implode("<br />", ArrayHelper::getColumn($model->errors, 0, false)));
            }
        } catch (\yii\db\Exception $e) {
            $this->logError($e);
			return $this->responseJson(500, $e->getMessage());
        } catch (\Exception $e) {
            $this->logError($e);
            return $this->responseJson(400, $e->getMessage());
        }
    }

    public function actionDelete($id)
    {
        try {
            $model = PenjaminDiskon::findOne($id);
            if($model->delete()){
                return $this->responseJson(200, DocoMessages::SUC_MESSAGE);
            }else{
                throw new \Exception (implode("<br />", ArrayHelper::getColumn($model->errors, 0, false)));
            }
        } catch (\yii\db\Exception $e) {
            $this->logError($e);
			return $this->responseJson(500, $e->getMessage());
        } catch (\Exception $e) {
            $this->logError($e);
            return $this->responseJson(400, $e->getMessage());
        }
    }
}
