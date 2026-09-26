<?php
/**
 * @Author: Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * @Date:   2021-01-22 10:51:53
 */
namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use app\modules\v1\models\Manufaktur;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\Barang;

class ManufakturController extends \Doco\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\Manufaktur';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["update"] = ["POST", "PUT"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionGetData()
    {
        try {
            $model = new Manufaktur;
            $query = $model::find()->where([
                            'is_deleted' => false, 
                            // 'is_active' => true
                            ]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderBy(['created_date' => SORT_DESC]);

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

    public function actionCreate()
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try {
            $model = new Manufaktur;
            $post = $request->post();
            $model->scenario = 'default';
            $model->attributes = $post;

            $isExist = Manufaktur::find()->where(['kode' => $model->attributes['kode']])->all();

            if($isExist != []) {
                $transaction->rollBack();
                $data = [
                        'text' => 'Kode '. $model->attributes['kode'] . ' sudah digunakan.',
                        'title' => 'Proses gagal !',
                        'code' => 422,
                        'status' => 422
                    ];
                    
                return $data;
            }

            if($model->validate()){
                if ($model->save()) {
                    $transaction->commit();
                    $return = [
                        'text' => 'Data Berhasil di simpan',
                        'title' => 'Proses berhasil !',
                        'code' => 200
                    ];
                    
                    return $return;
                } else {
                    $transaction->rollBack();
                    $errors = DocoHelpers::parseError($model->errors,'ManufakturForm');
                    return ['data' => $errors,'status' => 422];
                }
            }else{
                $transaction->rollBack();
                $errors = DocoHelpers::parseError($model->errors, 'ManufakturForm');
                return [
                    'data' => $errors,
                    'status' => 422
                ];
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $transaction->rollBack();
            return ['message' => $e->getMessage()];
        }
    }


    public function actionUpdate()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        try {
            $model = Manufaktur::findOne($id);
            $model->scenario = 'default';
            $model->attributes = $request->post();
            if($model->validate()){
                if ($model->update()) { 
                    $return = [
                        'text' => 'Data Berhasil di ubah',
                        'title' => 'Proses berhasil !',
                        'code' => 200
                    ];
                    
                    return $return;
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'ManufakturForm');
                    return ['data' => $errors,'status' => 422];
                }
            }else{
                $errors = DocoHelpers::parseError($model->errors, 'ManufakturForm');
                return [
                    'data' => $errors,
                    'status' => 422
                ];
            }
        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionDelete()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $getData = Manufaktur::find()
            ->where(['manufaktur_id' => $id])->all();
        
        $valPrimaryKey = [];
        foreach ($getData as $key => $value) {
            $valPrimaryKey[] = $value['manufaktur_id'];
        }

        $checkObat = ObatAlkes::find()
            ->where([
                'IN', 'manufaktur_id', $valPrimaryKey
            ])->all();

        // $checkBarang = Barang::find()
        //     ->where([
        //         'IN', 'manufaktur_id', $valPrimaryKey
        //     ])->all();

        // if ((empty($checkObat)) && (empty($checkBarang)) ) {
        if ((empty($checkObat))) {
            $delete = (new Manufaktur)->delete($id);
            return [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];
        } 
        return [
            'status' => 422,
            'title' => 'Proses Hapus Gagal !',
            'text' => 'Data sudah ini di pakai'
        ];
    }
}
?>
