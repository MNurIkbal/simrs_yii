<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;

use yii\helpers\ArrayHelper;
use yii\data\ArrayDataProvider;
use yii\data\ActiveDataProvider;

use app\modules\v1\models\SatuanUnit;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\IntSatuanUnitView;
use app\modules\v1\models\SatuanUnitR;
use Doco\Services\Vendors\OdooService;

class SatuanBarangController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\SatuanUnit';
    
    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["generate-api"] = ["GET"];
        $verbs["delete"] = ["DELETE", "POST"];
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

    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new SatuanUnit;
            $query = $model::find();

            if($request->get('advanced-filter')) {
                $advancedFilter = $request->get('advanced-filter');
                if(isset($advancedFilter['satuanunit_nama'])) {
                    $query->andWhere(['ILIKE', 'satuanunit_nama', $advancedFilter['satuanunit_nama']]);
                }

                if(isset($advancedFilter['satuanunit_namalain'])) {
                    $query->andWhere(['ILIKE', 'satuanunit_namalain', $advancedFilter['satuanunit_namalain']]);
                }

                if(isset($advancedFilter['satuanunit_id'])) {
                    $query->andWhere(['satuanunit_id' => $advancedFilter['satuanunit_id']]);
                }
            }

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $query->orderby([
                            'created_date' => SORT_DESC
                            ]);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        } catch (\yii\db\Exception $e) {
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            return ['message' => $e->getMessage()];
        }
    }

    public function actionCreate()
    {
        $model = new $this->modelClass([
            'scenario' => \yii\base\Model::SCENARIO_DEFAULT
        ]);

        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->save()) {
            $response = Yii::$app->getResponse();
            $response->setStatusCode(201);
            $id = implode(',', array_values($model->getPrimaryKey(true)));

            $dataUom = IntSatuanUnitView::find(true)->where(['sync_id_api'=>$model->getPrimaryKey(),'keterangan_rekap'=>'INSERT'])->asArray()->one();
            (new OdooService)->createUom(['uom'=>$dataUom],function($data,$result)use($dataUom){
                $idUom = ArrayHelper::getValue($dataUom,'id',0);
                $uid = ArrayHelper::getValue($result,'ProcessUID',0);
                Yii::$app->db->createCommand("
                    UPDATE satuanunit_r SET is_sending = true , id_sync_sercon = '{$uid}' WHERE id = '{$idUom}' AND keterangan_rekap = 'INSERT'
                ")->execute();
            });
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return $model;
    }

    public function actionUpdate($id)
    {
        /* @var $model ActiveRecord */
        $modelClass = $this->modelClass;
        $model = $modelClass::findOne($id);

        $model->scenario = \yii\base\Model::SCENARIO_DEFAULT;
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->save() === false && !$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to update the object for unknown reason.');
        }

        $dataUom = IntSatuanUnitView::find(true)->where(['sync_id_api'=>$model->getPrimaryKey(),'keterangan_rekap'=>'UPDATE','is_sending'=>FALSE])->asArray()->one();
        (new OdooService)->editUom(['uom'=>$dataUom],function($data,$result)use($dataUom){
            $idUom = ArrayHelper::getValue($dataUom,'id',0);
            $uid = ArrayHelper::getValue($result,'ProcessUID',0);
            Yii::$app->db->createCommand("
                UPDATE satuanunit_r SET is_sending = true , id_sync_sercon = '{$uid}' WHERE id = '{$idUom}' AND keterangan_rekap = 'UPDATE'
            ")->execute();
        });

        return $model;
    }

    public function actionResend()
    {
        $ids = isset($_POST['id']) ? (array) $_POST['id'] : [];
        $data = IntSatuanUnitView::find(true)->where(['id'=> $ids])->asArray()->all();

        $updateIds = [];
        $createIds = [];
        $stringIds = implode(',', $ids);
        $dataUpdateAll = [];
        $dataCreateAll = [];
        foreach ($data as $row) {
            if ($row['keterangan_rekap'] == 'INSERT') {
                $createIds[] = $row['id'];
                $dataCreateAll[] = $row;
            } else {
                $updateIds[] = $row['id'];
                $dataUpdateAll[] = $row;
            }
        }
        if (!empty($data)) {
            Yii::$app->db->createCommand("
                UPDATE satuanunit_r SET is_sending = false WHERE id in ({$stringIds})
            ")->execute();

            if (!empty($dataUpdateAll)) {
                foreach ($dataUpdateAll as $dataUom) {
                    (new OdooService)->editUom(['uom'=>$dataUom],function($data,$result)use($dataUom){
                        $idUom = ArrayHelper::getValue($dataUom,'id',0);
                        $uid = ArrayHelper::getValue($result,'ProcessUID',0);
                        Yii::$app->db->createCommand("
                            UPDATE satuanunit_r SET is_sending = true , id_sync_sercon = '{$uid}' WHERE id = '{$idUom}' AND keterangan_rekap = 'UPDATE'
                        ")->execute();
                    });
                }
            }

            if (!empty($dataCreateAll)) {
                foreach ($dataCreateAll as $dataUom) {
                    (new OdooService)->createUom(['uom'=>$dataUom],function($data,$result)use($dataUom){
                        $idUom = ArrayHelper::getValue($dataUom,'id',0);
                        $uid = ArrayHelper::getValue($result,'ProcessUID',0);
                        Yii::$app->db->createCommand("
                            UPDATE satuanunit_r SET is_sending = true , id_sync_sercon = '{$uid}' WHERE id = '{$idUom}' AND keterangan_rekap = 'INSERT'
                        ")->execute();
                    });
                }
            }
        }

        return $data;
    }

    public function actionExportExcel()
    {
        try {
            $data = array();
            $header = array();

            $request = Yii::$app->request;

            $model = new SatuanUnit;
            $query = $model::find()->where(['is_deleted' => false]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query)->all();
            if (!empty($query)) {
                $counter = 0;
                foreach ($query as $index => $value) {
                    $data[$counter]['Kode'] = !empty($value->satuanlab_kode)
                        ? $value->satuanlab_kode
                        : '';
                    $data[$counter]['Nama satuan'] = !empty($value->satuanlab_nama)
                    ? $value->satuanlab_nama
                    : '';
                    $counter++;
                }
            }

            $filePath = DocoHelpers::exportExcel('Master Satuan', $data, $header, array("uploadPath" => "./uploads"));
            return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
        } catch (\yii\db\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 500,
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGenerateApi()
    {
        $model = SatuanUnit::find()->where(['is_deleted'=>false])->all();
        $kode = [];
        foreach ($model as $value) {
            if (!empty($value['satuanlab_kode'])) {
                $kode[$value['satuanlab_id']] = $value['satuanlab_kode'];
            }
        }
        return [
            'kode' => $kode,
            'satuan' => ArrayHelper::map($model, 'satuanlab_id', 'satuanlab_nama')
        ];
    }

    public function actionDataSatuan($tipe)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = SatuanUnit::find();
        $column = ($tipe == 1) ? 'satuanunit_nama' : 'satuanunit_namalain';
        if(!empty($post['term'])){
            $term = strtoupper($post['term']);    
            $result->where(['ILIKE', $column, $term]);
        }   
             
        return $result->asArray()->all();
    }

    public function actionDelete() 
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        try {
            $model = ObatAlkes::find()
                ->where(['satuanbesar_id' => $id])
                ->orWhere(['satuansedang_id' => $id])
                ->count();
            
            if($model > 0) {
                $result['status'] = 500;
                $result['text'] = "Tidak bisa menghapus Satuan, data sudah digunakan di master lain.";
            }
            else {
                $model = SatuanUnit::findOne($id);
                if ($model) {
                    $model->is_deleted = true;
                    $model->deleted_date = date('Y-m-d H:i:s');
                    $model->save(false);

                    $dataUom = IntSatuanUnitView::find(true)->where(['sync_id_api'=>$model->getPrimaryKey(),'keterangan_rekap'=>'UPDATE','is_sending'=>FALSE])->asArray()->one();
                    (new OdooService)->editUom(['uom'=>$dataUom],function($data,$result)use($dataUom){
                        $idUom = ArrayHelper::getValue($dataUom,'id',0);
                        $uid = ArrayHelper::getValue($result,'ProcessUID',0);
                        Yii::$app->db->createCommand("
                            UPDATE satuanunit_r SET is_sending = true , id_sync_sercon = '{$uid}' WHERE id = '{$idUom}' AND keterangan_rekap = 'UPDATE'
                        ")->execute();
                    });
                    
                    $transaction->commit();
                    $result = [
                        'status' => 200,
                        'title' => 'Hapus Berhasil',
                        'text' => 'Hapus Satuan Berhasil',
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

    public function actionCheckTransaction()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $check = ObatAlkes::find()
                ->where(['satuanbesar_id' => $id])
                ->orWhere(['satuansedang_id' => $id])
                ->count();

        if ($check == 0) {
            return [
                'status' => 200,
                'title' => 'Proses Berhasil !',
                'text' => 'Data belum ada Transaksi'
            ];
        } 
        return [
            'status' => 422,
            'title' => 'Proses Gagal !',
            'text' => 'Data sudah ada Transaksi'
        ];
    }

}