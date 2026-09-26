<?php
namespace app\modules\v1\controllers;

use Yii;
use Doco\models\Modul;
use Doco\models\SupersetMapping;
use Doco\components\DocoHelpers;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use yii\data\ActiveDataProvider;

class ModulController extends DocoActiveController
{
    public $modelClass = 'Doco\models\Modul';

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
            $model = new Modul;
            $query = $model::find();
            $query->andWhere(['is_external_link' => false]);
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
            $model = Modul::findOne($id);
            if ($request->post() && !empty($model)) {
                $model->attributes = $request->post();
                if ($model->save()) {
                    if (!empty($model->dashboard_id) && $model->url_page == '__dashboard__') {
                        $map = SupersetMapping::find()->where(['mapping_key' => $model->modul_key])->one();
                        if (!$map) {
                            $map = new SupersetMapping();
                            $map->mapping_key = $model->modul_key;
                        }
                        $map->mapping_identity = $model->dashboard_id;
                        $map->save();
                    } else {
                        Yii::$app->db->createCommand()->delete('superset_mapping_m', ['mapping_key' => $model->modul_key])->execute();
                    }
                    $transaction->commit();
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'ModulForm');
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
            $model = new Modul;
            if ($request->post()) {
                $model->attributes = $request->post();
                $model->modul_key = strtolower($model->modul_nama);
                if ($model->save()) {
                    if (!empty($model->dashboard_id) && $model->url_page == '__dashboard__') {
                        $map = new SupersetMapping();
                        $map->mapping_key = $model->modul_key;
                        $map->mapping_identity = $model->dashboard_id;
                        $map->save();
                    }
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
            $result = Modul::find()->joinWith(['modulInstalasi'])->where([
                'modul_k.modul_id' => $id
            ])->one();
            if (empty($result->modulInstalasi)) {
                $result->delete($id);
                Yii::$app->db->createCommand()->delete('superset_mapping_m', ['mapping_key' => $result->modul_key])->execute();
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
        if ($res) {
            if ($map = SupersetMapping::find()->where(['mapping_key' => $res['modul_key']])->one()) {
                $res['dashboard_id'] = $map->mapping_identity;
            }
        }
        return $res;
    }

    private function getData($id = null)
    {
        $modul = Modul::find()->select([
                            'modul_id',
                            'modul_nama',
                            'modul_namalainnya',
                            'url_modul',
                            'url_page',
                            'modul_key',
                            'icon_modul',
                            'modul_urutan',
                            'imagemodul',
                            'is_active'
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
