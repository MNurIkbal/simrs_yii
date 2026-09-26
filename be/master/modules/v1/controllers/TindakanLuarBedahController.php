<?php

/**
 * @Author: Ripan
 * @Date:   19 Mei 2022
 */
namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\DaftarTindakan;
use app\modules\v1\models\TindakanLuarBedah;
use app\modules\v1\models\TindakanLuarBedahView;
use app\modules\v1\models\TindakanRuanganView;
use Doco\components\DocoConstants;
use yii\helpers\ArrayHelper;

class TindakanLuarBedahController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\TindakanLuarBedah';
    public $modelClassV = 'app\modules\v1\models\TindakanLuarBedahView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["view"] = ["POST", "GET"];
        $verbs["create"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "GET"];
        $verbs["delete"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        return $actions;
    }

    public function actionIndex()
    {
        try{
            $model = new TindakanLuarBedahView;
            $query = $model::find()->select([
                'daftartindakan_id',
                'daftartindakan_kode',
                'daftartindakan_nama'
            ])
            ->distinct()
            ->orderBy([
                'daftartindakan_kode' => SORT_ASC,
                'daftartindakan_nama' => SORT_ASC
            ]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            Yii::error( $e->getMessage());
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

    public function actionDetailTindakanLuarBedah()
    {
        $get = \Yii::$app->request->get();

        // Try catch
        try {
            // Find model
            $model = new TindakanLuarBedahView();

            // Find model
            $query = $model::find();
            $query->where([
                'daftartindakan_id'=> $get['daftartindakan_id']
            ])->orderBy([
                'tindakanluarbedah_kode' => SORT_ASC,
                'tindakanluarbedah_nama' => SORT_ASC
            ]);

            // Doco active filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            // Return data
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Change status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }
    
    // fungsi create multiple tindakan luar bedah
    public function actionCreate()
    {
        $request = Yii::$app->request;

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();

        try{
            if ($request->post()) {
                $dataJson = $request->post('data',"{}");
                $dataTindakan = $request->post('tindakan', null);
                $data = json_decode($dataJson, true);
                $errorData = 0;
                    
                if(!empty($data)) {
                    $dataInsert = [];
                    foreach ($data as $value) {
                        if(!empty($value)) {
                            $tindakanluarbedah_id = ArrayHelper::getValue($value, 'tindakanluarbedah_id', 0);

                            $errorData += TindakanLuarBedahView::find()
                                ->where([
                                    'daftartindakan_id' => $dataTindakan,
                                    'tindakanluarbedah_id' => $tindakanluarbedah_id
                                ])
                                ->count();

                            $dataInsert[] = [
                                'daftartindakan_id'    => $dataTindakan,
                                'tindakanluarbedah_id' => $tindakanluarbedah_id,
                                'qty'                  => ArrayHelper::getValue($value, 'qty', 0),
                                'is_ditagihkan'           => ArrayHelper::getValue($value, 'is_ditagihkan', false),
                                'created_date'         => date('Y-m-d H:i:s')
                            ];
                        }
                    }

                    if ($errorData < 1) {
                        $columns = ['daftartindakan_id', 'tindakanluarbedah_id', 'qty', 'is_ditagihkan', 'created_date'];
                        $connection->createCommand()->batchInsert('tindakanluarbedah_mp', $columns, $dataInsert)->execute();
                        // TindakanLuarBedah::batchInsert($dataInsert);
                        $transaction->commit();
                        return [
                            'message' => 'Data berhasil disimpan!',
                            'status' => 200
                        ];
                    } else {
                        return [
                            'title' => 'Proses Gagal!',
                            'text' => 'Data Tindakan Di Luar Bedah ada yang sudah ditambahkan!',
                            'status' => 422
                        ];
                    }
                } else {
                    return [
                        'title' => 'Proses Gagal!',
                        'text' => 'Data Tindakan Di Luar Bedah tidak boleh kosong!',
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

    public function actionUpdate($id)
    {
        $request = Yii::$app->request;

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try{
            $connection->createCommand()
            ->delete('tindakanluarbedah_mp', ['daftartindakan_id' => $id])
            ->execute();
            
            if ($request->post()) {
                $dataJson = $request->post('data', "{}");
                $dataTindakan = $request->post('tindakan', null);
                $data = json_decode($dataJson, true);

                if(!empty($data)) {
                    $dataInsert = [];
                    foreach ($data as $value) {
                        if(!empty($value)) {
                            $dataInsert[] = [
                                'daftartindakan_id'    => $dataTindakan,
                                'tindakanluarbedah_id' => ArrayHelper::getValue($value, 'tindakanluarbedah_id', 0),
                                'qty'                  => ArrayHelper::getValue($value, 'qty', 0),
                                'is_ditagihkan'           => ArrayHelper::getValue($value, 'is_ditagihkan', false),
                                'created_date'         => date('Y-m-d H:i:s')
                            ];
                        }
                    }
                    $columns = ['daftartindakan_id', 'tindakanluarbedah_id', 'qty', 'is_ditagihkan', 'created_date'];
                    $connection->createCommand()->batchInsert('tindakanluarbedah_mp', $columns, $dataInsert)->execute();
                    // TindakanBmhp::batchInsert($dataInsert);
                    $transaction->commit();
                    return [
                        'message' => 'Data berhasil disimpan!',
                        'status' => 200
                    ];
                }
                else {
                    return [
                        'title' => 'Proses Gagal!',
                        'text' => 'Data Obat/Alkes tidak boleh kosong!',
                        'status' => 422
                    ];
                }
            }
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            Yii::error($e->getMessage());
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
        \Yii::$app
        ->db
        ->createCommand()
        ->delete('tindakanluarbedah_mp', ['daftartindakan_id' => $id])
        ->execute();

        return ['message'=>'data berhasil di hapus'];
    }

    public function actionView($id)
    {
        return [
            'tindakan' => TindakanLuarBedahView::find()->select(['daftartindakan_id', 'daftartindakan_kode', 'daftartindakan_nama'])->where(['daftartindakan_id' => $id])->distinct()->one(),
            'data'     => TindakanLuarBedahView::find()->where(['daftartindakan_id' => $id])->all()
        ];
    }


    public function actionListTindakanLuarBedah()
    {
        $params = Yii::$app->request;
        $term = $params->get('term');
        $page = $params->get('page',0);
        $limit = $params->get('limit',5);
        $offset = $params->get('offset',0);

        $model = new TindakanRuanganView();
        $query = $model::find()->where(['instalasi_id' => DocoConstants::INST_ID_BEDAH]);
        if($term){
            $query->where(['ILIKE','LOWER(daftartindakan_nama)',$term]);
            $query->orWhere(['ILIKE','LOWER(daftartindakan_kode)',$term]);
        }

        $query->select(['daftartindakan_id', 'daftartindakan_kode', 'daftartindakan_nama'])->distinct();
        $query->orderBy([
            'daftartindakan_kode' => SORT_ASC,
            'daftartindakan_nama' => SORT_ASC
        ]);

        return $query->offset($offset)->limit($limit)->asArray()->all();
    }
}