<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\PaketMcuView;
use app\modules\v1\models\PaketRuanganV;
use app\modules\v1\models\TindakanRuanganView;
use app\modules\v1\models\TipePaket;
use app\modules\v1\models\PaketPelayanan;
use Doco\components\DocoMessages;

use yii\helpers\ArrayHelper;

class PaketMcuController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\PaketMcuView';

    // allow sequa blok
    protected $allowAction = [ '*' ];

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
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $model = new PaketMcuView;
        $query = $model::find();
        if (isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tipepaket_nama'])){
                $tipepaket_nama = $_GET['advanced-filter']['tipepaket_nama'];
                $query->andWhere(['ILIKE', 'tipepaket_nama', $tipepaket_nama]);
            }
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionSaveMcu()
    {
        $model = new TipePaket;
        $request = Yii::$app->request;
        
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            if ($request->post()){
                $detailJson = $request->post('detail',"{}");
                $detail = json_decode($detailJson,true);
                $inputHeader = array(
                    'tipepaket_nama' => $request->post('tipepaket_nama'),
                    'tipepaket_kode' => $request->post('tipepaket_kode'),
                    'tipepaket_namalainnya' => $request->post('tipepaket_namalainnya'),
                    'keterangan_tipepaket' => $request->post('keterangan_tipepaket'),
                    'is_mcu' => true,
                    'is_active' => $request->post('is_active')
                );

                $model->attributes = $inputHeader;
                if ($model->save()) {
                    if (!empty($detail)) {
                        $detailInsert = [];
                        foreach ($detail as $key => $value) {
                            $detailInsert[] = [
                                'ruangan_id' => (int)$value['ruangan_id'],
                                'tipepaket_id' => (int)$model->tipepaket_id,
                                'daftartindakan_id' => empty($value['daftartindakan_id']) ? null : (int)$value['daftartindakan_id'],
                                'paketdetail_id' => empty($value['paketdetail_id']) ? null : (int)$value['paketdetail_id']
                            ];
                        }
                        if (!empty($detailInsert)) {
                            PaketPelayanan::batchInsert($detailInsert);
                        }
                        $transaction->commit();
                        return DocoHelpers::callBack(DocoMessages::KEY_SUC_SYSTEM);
                    }else{
                        $transaction->rollBack();
                        return [
                            'title' => 'Proses Gagal!',
                            'text' => 'Mapping Paket/Tindakan tidak boleh kosong!',
                            'status' => 422
                        ];
                    }
                }else{
                    $transaction->rollBack();
                    return DocoHelpers::callBack(DocoMessages::KEY_ERR_SYSTEM, [
                        'data' => $model->errors
                    ]);
                }
            }
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionDelete($id)
    {
        try {
            $result = (new PaketMcuView)->delete($id);

            return $result;
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionDetailPaketMcu()
    {
        $get = \Yii::$app->request->get();
        // Try catch
        try {
            // Find model
            $model = new PaketMcuView();

        // Find model
            $query = $model::find();
                $query->where(['tipepaket_id'=>$get['tipepaket_id']]);

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

    public function actionViewData($id)
    {
        $model = new PaketMcuView;
        $query = $model->find();
        if ($id) {
            $query->andWhere(['tipepaket_id' => $id]);
        }

        return $query->one();
    }

    public function actionUpdate($id)
    {
        try {
            $now = date('Y-m-d H:i:s');
            $request = Yii::$app->request;
            $model = PaketMcuView::findOne($id);
            $post = $request->post();
            if ($model && !empty($model)) {
                $model->attributes = $post;
                if ($model->validate() && $model->save()) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                     return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }
            }
            throw new \yii\db\Exception("Data Tidak Di Temukan");
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionGetTindakan($state = true)
    {
        $request = Yii::$app->request;
        $get = $request->get();
        
        $model = new TindakanRuanganView;
        $query = $model::find();
        if(isset($get['ruangan_id'])) {
            $query->where('ruangan_id = :ruangan_id', ['ruangan_id' => $get['ruangan_id']]);
        }

        if(isset($get['id'])) {
            $query->where('ruangan_id = :ruangan_id', ['ruangan_id' => $get['id']]);
        }
        if($state){
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        }else{
            return $query->asArray()->all();
        }
    }

    public function actionGetPaket($state = true)
    {
        $request = Yii::$app->request;
        $get = $request->get();
        
        $model = new PaketRuanganV;
        $query = $model::find();
        if(isset($get['ruangan_id'])) {
            $query->where('ruangan_id = :ruangan_id', ['ruangan_id' => $get['ruangan_id']]);
        }

        if(isset($get['id'])) {
            $query->where('ruangan_id = :ruangan_id', ['ruangan_id' => $get['id']]);
        }
        if($state){
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            return new ActiveDataProvider([
                'query' => $query,
            ]);
        }else{
            return $query->asArray()->all();
        }
    }

    public function actionExportExcel()
    {
        try {
            $request = Yii::$app->request;
            $model = new PaketMcuView;
            $query = PaketMcuView::find();
            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            $data = [];
            foreach ($query->asArray()->all() as $key => $value) {
                $newData = [];
                $newData['status'] = ($value['is_active'] == true) ? Yii::t('app','Aktif') :  Yii::t('app','Tidak aktif');
                $data[] = $newData;
            }

            $header = [
                Yii::t('app', 'Nama Paket') => isset($_GET['advanced-filter']['tipepaket_nama']) ? $_GET['advanced-filter']['tipepaket_nama'] : '',
            ];
            
            $filePath = DocoHelpers::exportExcel('Paket MCU', $data, $header, array("uploadPath" => "./uploads"));
            return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
        } catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
}