<?php
namespace app\modules\v1\controllers;

use Yii;
use Doco\models\ModulExternal;
use Doco\models\SupersetMapping;
use Doco\components\DocoHelpers;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;

class ModulExternalController extends DocoActiveController
{
    public $modelClass = 'Doco\models\ModulExternal';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        return $actions;
    }
    
    public function actionIndex()
    {
        try {
            $model = new ModulExternal;
            $query = $model::find();
            $query->andWhere(['is_external_link' => true]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionUpdate($id)
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $request = Yii::$app->request;
            \Yii::error($request->post());
            $model = ModulExternal::findOne($id);
            if ($request->post() && !empty($model)) {
                $model->attributes = $request->post();
                $model->is_external_link = true;
                if ($model->save()) {
                    $transaction->commit();
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'ModulExternalForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
            throw new Exception("Data Tidak Di Temukan");
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionCreate()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            $request = Yii::$app->request;
            $model = new ModulExternal;
            if ($request->post()) {
                $model->attributes = $request->post();
                $model->modul_key = strtolower($model->modul_nama);
                $model->is_external_link = true;
                if ($model->save()) {
                    $transaction->commit();
                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionDelete($id)
    {
        try {
            $result = ModulExternal::find()->andWhere([
                'modulexternal_id' => $id
            ])->one();
            if (empty($result->modulInstalasi)) {
                $result->delete($id);
                return [
                    "title" => "Proses Berhasil !",
                    "message" => "Data berhasil dihapus",
                ];
            }
            return [
                'status' => 422,
                'message' => 'Tidak dapat menghapus ' . $result->modul_nama,
                "title" => "Proses Gagal !",
            ];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionView($id)
    {
        $res = $this->getData($id)->asArray()->one();
        return $res;
    }

    private function getData($id = null)
    {
        $modul = ModulExternal::find()->select([
                            'modul_id',
                            'modul_nama',
                            'modul_namalainnya',
                            'url_modul',
                            'url_page',
                            'modul_key',
                            'icon_modul',
                            'modul_urutan',
                            'imagemodul',
                            'is_active',
                            'open_newtab'
                        ]);
        if ($id) {
            $modul->where(['modul_id' => $id]);
        }

        return $modul;
    }

    public function actionGetJson() {
        var_dump($this->getData()->asArray()->all());

        exit;
    }
}
