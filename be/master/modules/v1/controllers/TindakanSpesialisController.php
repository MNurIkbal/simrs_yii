<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;
use app\modules\v1\models\Spesialis;
use app\modules\v1\models\TindakanSpesialis;
use app\modules\v1\models\TindakanSpesialisView;

class TindakanSpesialisController extends \Doco\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\TindakanSpesialis';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["delete"] = ["POST", "DELETE"];
        $verbs["list-layarantrian"] = ["POST", "GET"];
        $verbs["list-type-screen"] = ["POST", "GET"];
        $verbs["list-function-screen"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        return $actions;
    }

    public function actionIndex()
    {
        try {
            $model = new Spesialis;
            $query = $model::find()
                ->select([
                    'spesialis_id',
                    'spesialis_nama',
                    'is_deleted',
                    'is_active'
                ])
                ->where([
                    'is_deleted' => false,
                    'is_active' => true
                ])
                ->groupBy([
                    'spesialis_id',
                    'spesialis_nama',
                    'is_deleted',
                    'is_active'
                ]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
    public function actionView()
    {
        $model = new TindakanSpesialisView;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }
    public function actionViewDetail($id)
    {
        $model = new TindakanSpesialisView;
        $query = $model::find();
        $query->andWhere(['spesialis_id' => $id]);
        $query->orderBy(['created_date' => SORT_ASC]);
        return new ActiveDataProvider([
            'query' => $query,
            'pagination' => false
        ]);
    }

    public function actionGenerateApi()
    {
        // get spesialis
        $querySpesialis = Spesialis::find()
            ->select([
                'spesialis_id',
                'spesialis_nama',
                'is_deleted',
                'is_active'
            ])
            ->where([
                'is_deleted' => false,
                'is_active' => true
            ])
            ->asArray()
            ->all();

        return [
            'spesialis' => $querySpesialis,
        ];
    }

    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            $model = new TindakanSpesialis;
            if ($post = $request->post()) {
                $model->attributes = $post;
                if ($model->validate()) {
                    if ($model->save()) {
                        return ['message' => 'Data Berhasil di simpan'];
                    }
                } else {
                    return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e
            ];
        }
    }

    public function actionDelete()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $query  = "UPDATE tindakanspesialis_mp set is_deleted = true, deleted_by = :pegawai_id, deleted_date = :date_now where spesialis_id = :spesialis_id and daftartindakan_id = :daftartindakan_id";
        Yii::$app->db->createCommand($query)
            ->bindParam(':pegawai_id', Yii::$app->jwt->user->pegawai_id)
            ->bindParam(':date_now', date('Y-m-d H:i:s'))
            ->bindParam(':spesialis_id', $post['spesialis_id'])
            ->bindParam(':daftartindakan_id', $post['daftartindakan_id'])
            ->execute();
        return true;
    }
}
