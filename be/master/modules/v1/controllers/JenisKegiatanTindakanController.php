<?php

/**
 * @author Rizal Faidin
 * @todo Master Jenis Kegiatan Tindakan
 * @copyright 2018-04-26 10:41:08 sandwing_
 */
namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use app\modules\v1\models\JenisKegiatanTindakan;
use app\modules\v1\models\DaftarTindakan;
use app\modules\v1\models\JenisKegiatanDetailView;

class JenisKegiatanTindakanController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\JenisKegiatanTindakan';

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
        $model = new JenisKegiatanTindakan;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        // $query = $query->orderBy([
        //     'jeniskegiatantindakan_id' => SORT_ASC,
        // ]);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionView($id)
    {
        $model = new JenisKegiatanTindakan;
        $model = $model->find()->andWhere(['jeniskegiatantindakan_id'=>$id]);
        $model = $model->asArray()->one();
        
        return $model;
    }

    public function actionListDaftarTindakan($id=null)
    {
        $models = DaftarTindakan::find();
        if ($id) {
            $models = $models->andWhere([
                'jeniskegiatantindakan_id'=>$id
            ]);
        }
        $models = $models->asArray()->all();
        return $models;
    }

    public function actionCreate()
    {
        $model = new JenisKegiatanTindakan;
        $request = Yii::$app->request;

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try{
            if ($request->post()) {
                $model->attributes = $request->post();
                // $model->daftartindakan_ids = $request->post()['daftartindakan_ids'];
                $model->daftartindakan_ids = null;
                if ($model->validate()) {
                    if ($model->save(false)) {
                        if ($model->daftartindakan_ids) {
                            $listTindakan = json_decode($model->daftartindakan_ids);
                            $tindakan = DaftarTindakan::find()
                                ->andWhere(['daftartindakan_id'=>$listTindakan])
                                ->all();
                            foreach ($tindakan as $each)
                            {
                                $each->jeniskegiatantindakan_id = $model->jeniskegiatantindakan_id;
                                $each->save(false);
                            }
                        }
                        $transaction->commit();
                        return ['message'=>'data berhasil di simpan'];
                    }
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'JenisKegiatanForm');
                    return [
                        'data' => $errors,
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
        $model = new JenisKegiatanTindakan;
        $request = Yii::$app->request;

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try{
            $model = $model->findOne($id);
            // cari dan remove daftar tindakan
            $tindakan = DaftarTindakan::find()
                ->andWhere(['jeniskegiatantindakan_id'=>$id])
                ->all();
            foreach ($tindakan as $each)
            {
                $each->jeniskegiatantindakan_id = null;
                $each->save(false);
            }
            // end
            
            if ($request->post()) {
                $model->attributes = $request->post();
                // $model->daftartindakan_ids = $request->post()['daftartindakan_ids'];
                $model->daftartindakan_ids = null;
                if ($model->validate()) {
                    if ($model->save(false)) {
                        if ($model->daftartindakan_ids) {
                            $listTindakan = json_decode($model->daftartindakan_ids);
                            $tindakan = DaftarTindakan::find()
                                ->andWhere(['daftartindakan_id'=>$listTindakan])
                                ->all();
                            foreach ($tindakan as $each)
                            {
                                $each->jeniskegiatantindakan_id = $model->jeniskegiatantindakan_id;
                                $each->save(false);
                            }
                        }
                        $transaction->commit();
                        return ['message'=>'data berhasil di ubah'];
                    }
                } else {
                    $errors = DocoHelpers::parseError($model->errors, 'JenisKegiatanForm');
                    return [
                        'data' => $errors,
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
        $model = new JenisKegiatanTindakan;
        $request = Yii::$app->request;

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try{
            $model = $model->findOne($id);
            // cari dan remove daftar tindakan
            $tindakan = DaftarTindakan::find()
                ->andWhere(['jeniskegiatantindakan_id'=>$id])
                ->all();

            if (!empty($tindakan)) {
                foreach ($tindakan as $each) {
                    $each->jeniskegiatantindakan_id = null;
                    $each->save(false);
                }
            }
            // end
            
            // if ($request->post()) {
            //     $model->attributes = $request->post();
            //     $model->daftartindakan_ids = $request->post()['daftartindakan_ids'];
            //     if ($model->delete()) {
            //         $transaction->commit();
            //         return ['message'=>'data berhasil di hapus'];
            //     } else {
            //         return $model->getErrors();
            //     }
            // }
            
            if ($model->delete()) {
                $transaction->commit();
                return ['message'=>'data berhasil di hapus'];
            } else {
                return $model->getErrors();
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

    /**
     * @todo Fungsi untuk melakukan pengecekan transaksi kegiatan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionCekTransaksiKegiatan()
    {
        $request = Yii::$app->request;
        $id = $request->get('id');

        $count = DaftarTindakan::find()->where(['jeniskegiatantindakan_id' => $id])->count();

        return $count;
    }

    /**
     * @todo Fungsi untuk mendapatkan detail kegiatan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionGetDataDetailKegiatan()
    {
        $get = \Yii::$app->request->get();
        $model = new JenisKegiatanDetailView;

        $query = $model::find();
        $query->where(['jeniskegiatantindakan_id' => $get['id']]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
    * @controller actionExportPdf
    * @attribute #table# => Untuk mengganti data di table
    */
    public function actionExportPdf()
    {
        $title = Yii::t('app', 'Master Kegiatan');
        $model = new JenisKegiatanTindakan;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $print = new DocoPrint();
        $print->attributes = [
            '#tanggal#' => date('d M Y'),
            '#datatable#' => $this->renderPartial('pdf', [
                'data' => $dataProvider->getModels(),
                'title' => $title,
            ]),
        ];

        $print->Output();
    }

    /**
     * @todo Fungsi untuk melakukan export excel
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionExportExcel()
    {
        $title = Yii::t('app', 'Master Kegiatan');
        $request = Yii::$app->request;
        $model = new JenisKegiatanTindakan;
        $query = $model::find();
        $header = array();
        $footer = array();

        if(!is_null($request->get('advanced-filter'))) {
            $advancedFilter = $request->get('advanced-filter');

            if(isset($advancedFilter['jeniskegiatantindakan_kode'])) {
                $query->andFilterWhere(['or',
                    ['ILIKE', 'LOWER(jeniskegiatantindakan_kode)', strtolower($advancedFilter['jeniskegiatantindakan_kode'])],
                ]);

                $header[Yii::t('app', 'Kode Kegiatan')] = $advancedFilter['jeniskegiatantindakan_kode'];
            }

            if(isset($advancedFilter['jeniskegiatantindakan_nama'])){
                $query->andFilterWhere(['or',
                    ['ILIKE', 'LOWER(jeniskegiatantindakan_nama)', strtolower($advancedFilter['jeniskegiatantindakan_nama'])],
                ]);

                $header[Yii::t('app', 'Nama Kegiatan')] = $advancedFilter['jeniskegiatantindakan_nama'];
            }

            if(isset($advancedFilter['jeniskegiatan_namalainnya'])){
                $query->andFilterWhere(['or',
                    ['ILIKE', 'LOWER(jeniskegiatan_namalainnya)', strtolower($advancedFilter['jeniskegiatan_namalainnya'])],
                ]);

                $header[Yii::t('app', 'Nama Lainnya')] = $advancedFilter['jeniskegiatan_namalainnya'];
            }

            if(isset($advancedFilter['jeniskegiatan_keterangan'])){
                $query->andFilterWhere(['or',
                    ['ILIKE','LOWER(jeniskegiatan_keterangan)',strtolower($advancedFilter['jeniskegiatan_keterangan'])],
                ]);

                $header[Yii::t('app', 'Keterangan')] = $advancedFilter['jeniskegiatan_keterangan'];
            }
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
        
        foreach ($dataProvider->getModels() as $key => $value) {
            $newValue = [];
            $newValue[\Yii::t('app', 'Kode Kegiatan')] = $value['jeniskegiatantindakan_kode'];
            $newValue[\Yii::t('app', 'Nama Kegiatan')] = $value['jeniskegiatantindakan_nama'];
            $newValue[\Yii::t('app', 'Nama Lainnya')] = $value['jeniskegiatan_namalainnya'];
            $newValue[\Yii::t('app', 'Keterangan')] = $value['jeniskegiatan_keterangan'];
            $result[$key] = $newValue;
        }

        $filePath = DocoHelpers::exportExcel($title, $result, $header, [], $footer, [], true);
        $filePath->save('php://output');
        die;
    }
}