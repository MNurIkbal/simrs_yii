<?php

/**
 * @Author: Wahyu Saepuloh
 * @Date:   5 November 2019
 */
namespace app\modules\v1\controllers;

use Yii;
use app;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use app\modules\v1\models\TindakanBmhp;
use app\modules\v1\models\TindakanAlkes;
use app\modules\v1\models\TindakanBmhpView;
use app\modules\v1\models\InfoTindakanBmhpView;
use app\modules\v1\models\JenisKegiatanDetailView;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;

class TindakanBmhpController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\TindakanBmhp';
    public $modelClassV = 'app\modules\v1\models\TindakanBmhpView';
    public $modelInfoBmhp = 'app\modules\v1\models\InfoTindakanBmhpView';

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

    // fungsi mengambil data tindakan bmhp
    public function actionIndex()
    {
        $model = new InfoTindakanBmhpView;
        $query = $model::find()->select([
            'DISTINCT ON (daftartindakan_nama) daftartindakan_nama', 
            '*'
        ]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        // $query = $query->orderBy([
        //     'jeniskegiatantindakan_id' => SORT_ASC,
        // ]);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionDetailTindakanBmhp()
    {
        $get = \Yii::$app->request->get();
        // Try catch
        try {
            // Find model
            // $query = PaketDetailV::query();
            $model = new InfoTindakanBmhpView();

        // Find model
            $query = $model::find();
                $query->where(['daftartindakan_id'=>$get['daftartindakan_id']]);

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
    
    // fungsi create multiple tindakan bmhp
    public function actionCreate()
    {
        $model = new TindakanBmhp;
        $request = Yii::$app->request;

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try{
            if ($request->post()) {
                
                $dataJson = $request->post('data',"{}");
                $dataTindakan = $request->post('tindakan');
                $data = json_decode($dataJson,true);
                    
                if(!empty($data)) {
                    $dataInsert = $dataInsertAlkes = [];
                    foreach ($data as $key => $value) {
                        if(!empty($value)) {
                            if(!empty($value['group']) && $value['group'] == DocoConstants::GROUP_JENISOBAT_ALKES){
                                $dataInsertAlkes[] = [
                                    'daftartindakan_id' => $dataTindakan,
                                    'obatalkes_id' => ArrayHelper::getValue($value, 'obatalkes_id', null),
                                    'satuaninput_id' => ArrayHelper::getValue($value, 'satuaninput_id', null),
                                    'satuanunit_id' =>  ArrayHelper::getValue($value, 'satuanunit_id', null),
                                    'nilai_konversi' =>  ArrayHelper::getValue($value, 'nilai_konversi', null),
                                    'qty_input' =>  ArrayHelper::getValue($value, 'qty_input', null),
                                    'qty_konversi' =>  ArrayHelper::getValue($value, 'qty_konversi', null),
                                ];
                            }else{
                                $dataInsert[] = [
                                    'daftartindakan_id' => $dataTindakan,
                                    'obatalkes_id' => ArrayHelper::getValue($value, 'obatalkes_id', null),
                                    'satuaninput_id' => ArrayHelper::getValue($value, 'satuaninput_id', null),
                                    'satuanunit_id' =>  ArrayHelper::getValue($value, 'satuanunit_id', null),
                                    'nilai_konversi' =>  ArrayHelper::getValue($value, 'nilai_konversi', null),
                                    'qty_input' =>  ArrayHelper::getValue($value, 'qty_input', null),
                                    'qty_konversi' =>  ArrayHelper::getValue($value, 'qty_konversi', null),
                                ];
                            }
                        }
                    }
                    if(!empty($dataInsert)){
                        TindakanBmhp::batchInsert($dataInsert);
                    }

                    if(!empty($dataInsertAlkes)){
                        TindakanAlkes::batchInsert($dataInsertAlkes);
                    }
                    $transaction->commit();
                    return [
                        'message' => 'Data berhasil disimpan!',
                        'status' => 200
                    ];
                }
                else {
                    return [
                        // 'data' => $model->errors,
                        'title' => 'Proses Gagal!',
                        'text' => 'Data Obat/Alkes tidak boleh kosong!',
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
        $model = new TindakanBmhp;
        $request = Yii::$app->request;

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try{
            \Yii::$app
            ->db
            ->createCommand()
            ->delete('tindakanbmhp_mp', ['daftartindakan_id' => $id])
            ->execute();

            \Yii::$app
            ->db
            ->createCommand()
            ->delete('tindakanalkes_mp', ['daftartindakan_id' => $id])
            ->execute();
            
            if ($request->post()) {
                $dataJson = $request->post('data',"{}");
                $dataTindakan = $request->post('tindakan');
                $data = json_decode($dataJson,true);
                    
                if(!empty($data)) {
                    $dataInsert = [];
                    foreach ($data as $key => $value) {
                        if(!empty($value)) {
                            if(!empty($value['group']) && $value['group'] == DocoConstants::GROUP_JENISOBAT_ALKES){
                                $dataInsertAlkes[] = [
                                    'daftartindakan_id' => $dataTindakan,
                                    'obatalkes_id' => ArrayHelper::getValue($value, 'obatalkes_id', null),
                                    'satuaninput_id' => ArrayHelper::getValue($value, 'satuaninput_id', null),
                                    'satuanunit_id' =>  ArrayHelper::getValue($value, 'satuanunit_id', null),
                                    'nilai_konversi' =>  ArrayHelper::getValue($value, 'nilai_konversi', null),
                                    'qty_input' =>  ArrayHelper::getValue($value, 'qty_input', null),
                                    'qty_konversi' =>  ArrayHelper::getValue($value, 'qty_konversi', null),
                                ];
                            }else{
                                $dataInsert[] = [
                                    'daftartindakan_id' => $dataTindakan,
                                    'obatalkes_id' => ArrayHelper::getValue($value, 'obatalkes_id', null),
                                    'satuaninput_id' => ArrayHelper::getValue($value, 'satuaninput_id', null),
                                    'satuanunit_id' =>  ArrayHelper::getValue($value, 'satuanunit_id', null),
                                    'nilai_konversi' =>  ArrayHelper::getValue($value, 'nilai_konversi', null),
                                    'qty_input' =>  ArrayHelper::getValue($value, 'qty_input', null),
                                    'qty_konversi' =>  ArrayHelper::getValue($value, 'qty_konversi', null),
                                ];
                            }
                        }
                    }
                    if(!empty($dataInsert)){
                        TindakanBmhp::batchInsert($dataInsert);
                    }

                    if(!empty($dataInsertAlkes)){
                        TindakanAlkes::batchInsert($dataInsertAlkes);
                    }
                    $transaction->commit();
                    return [
                        'message' => 'Data berhasil disimpan!',
                        'status' => 200
                    ];
                }
                else {
                    return [
                        // 'data' => $model->errors,
                        'title' => 'Proses Gagal!',
                        'text' => 'Data Obat/Alkes tidak boleh kosong!',
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
        \Yii::$app
        ->db
        ->createCommand()
        ->delete('tindakanbmhp_mp', ['daftartindakan_id' => $id])
        ->execute();
        return ['message'=>'data berhasil di hapus'];
    }

    public function actionView($id)
    {
        // $count = InfoTindakanBmhpView::find()->where(['daftartindakan_id' => $id])->count();
        $data = $this->getData($id)->asArray()->all();
        return [
            // 'count' => $count,
            'data' => $data
        ];
    }

    private function getData($id = null)
    {
        $model = InfoTindakanBmhpView::find();
        if ($id) {
            $model->where(['daftartindakan_id' => $id]);
        }

        return $model;
    }

    /**
     * @todo Fungsi untuk melakukan pengecekan transaksi kegiatan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    // public function actionCekTransaksiKegiatan()
    // {
    //     $request = Yii::$app->request;
    //     $id = $request->get('id');

    //     $count = DaftarTindakan::find()->where(['jeniskegiatantindakan_id' => $id])->count();

    //     return $count;
    // }

    /**
     * @todo Fungsi untuk mendapatkan detail kegiatan
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    // public function actionGetDataDetailKegiatan()
    // {
    //     $get = \Yii::$app->request->get();
    //     $model = new JenisKegiatanDetailView;

    //     $query = $model::find();
    //     $query->where(['jeniskegiatantindakan_id' => $get['id']]);
    //     $query = DocoRestActiveFilter::advancedFilter($model, $query);

    //     return new ActiveDataProvider([
    //         'query' => $query,
    //     ]);
    // }

    /**
    * @controller actionExportPdf
    * @attribute #table# => Untuk mengganti data di table
    */
    // public function actionExportPdf()
    // {
    //     $title = Yii::t('app', 'Master Kegiatan');
    //     $model = new JenisKegiatanTindakan;
    //     $query = $model::find();
    //     $query = DocoRestActiveFilter::advancedFilter($model, $query);

    //     $dataProvider = new ActiveDataProvider([
    //         'query' => $query,
    //         'pagination' => false,
    //     ]);

    //     $print = new DocoPrint();
    //     $print->attributes = [
    //         '#table#' => $this->renderPartial('pdf', [
    //             'data' => $dataProvider->getModels(),
    //             'title' => $title,
    //         ]),
    //     ];

    //     $print->Output();
    // }

    /**
     * @todo Fungsi untuk melakukan export excel
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    // public function actionExportExcel()
    // {
    //     $title = Yii::t('app', 'Master Kegiatan');
    //     $request = Yii::$app->request;
    //     $model = new JenisKegiatanTindakan;
    //     $query = $model::find();
    //     $header = array();
    //     $footer = array();

    //     if(!is_null($request->get('advanced-filter'))) {
    //         $advancedFilter = $request->get('advanced-filter');

    //         if(isset($advancedFilter['jeniskegiatantindakan_kode'])) {
    //             $query->andFilterWhere(['or',
    //                 ['ILIKE', 'LOWER(jeniskegiatantindakan_kode)', strtolower($advancedFilter['jeniskegiatantindakan_kode'])],
    //             ]);

    //             $header[Yii::t('app', 'Kode Kegiatan')] = $advancedFilter['jeniskegiatantindakan_kode'];
    //         }

    //         if(isset($advancedFilter['jeniskegiatantindakan_nama'])){
    //             $query->andFilterWhere(['or',
    //                 ['ILIKE', 'LOWER(jeniskegiatantindakan_nama)', strtolower($advancedFilter['jeniskegiatantindakan_nama'])],
    //             ]);

    //             $header[Yii::t('app', 'Nama Kegiatan')] = $advancedFilter['jeniskegiatantindakan_nama'];
    //         }

    //         if(isset($advancedFilter['jeniskegiatan_namalainnya'])){
    //             $query->andFilterWhere(['or',
    //                 ['ILIKE', 'LOWER(jeniskegiatan_namalainnya)', strtolower($advancedFilter['jeniskegiatan_namalainnya'])],
    //             ]);

    //             $header[Yii::t('app', 'Nama Lainnya')] = $advancedFilter['jeniskegiatan_namalainnya'];
    //         }

    //         if(isset($advancedFilter['jeniskegiatan_keterangan'])){
    //             $query->andFilterWhere(['or',
    //                 ['ILIKE','LOWER(jeniskegiatan_keterangan)',strtolower($advancedFilter['jeniskegiatan_keterangan'])],
    //             ]);

    //             $header[Yii::t('app', 'Keterangan')] = $advancedFilter['jeniskegiatan_keterangan'];
    //         }
    //     }

    //     $query = DocoRestActiveFilter::advancedFilter($model, $query);
    //     $dataProvider = new ActiveDataProvider([
    //         'query' => $query,
    //         'pagination' => false,
    //     ]);
        
    //     foreach ($dataProvider->getModels() as $key => $value) {
    //         $newValue = [];
    //         $newValue[\Yii::t('app', 'Kode Kegiatan')] = $value['jeniskegiatantindakan_kode'];
    //         $newValue[\Yii::t('app', 'Nama Kegiatan')] = $value['jeniskegiatantindakan_nama'];
    //         $newValue[\Yii::t('app', 'Nama Lainnya')] = $value['jeniskegiatan_namalainnya'];
    //         $newValue[\Yii::t('app', 'Keterangan')] = $value['jeniskegiatan_keterangan'];
    //         $result[$key] = $newValue;
    //     }

    //     $filePath = DocoHelpers::exportExcel($title, $result, $header, [], $footer, [], true);
    //     $filePath->save('php://output');
    //     die;
    // }
}