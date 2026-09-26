<?php
/**
 * @author: arief saputra
 * @description: master untuk CRUD Kategori Tindakan
**/

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataFilter;
use yii\data\ActiveDataProvider;
use app\modules\v1\models\DaftarTindakan;
use app\modules\v1\models\KategoriTindakan;
use Doco\components\DocoHelpers;
use yii\web\HttpException;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;

class KategoriTindakanController extends \Doco\components\DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\KategoriTindakan';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["create"] = ["POST", "GET"];
        $verbs["view"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "GET"];
        $verbs["delete"] = ["DELETE","POST"];
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
        $request = Yii::$app->request;
        $model = new KategoriTindakan;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionSimpanKategori() {
        $post = \Yii::$app->request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        $kategoritindakan_id = isset($post['kategoritindakan_id']) ? $post['kategoritindakan_id'] : null;
        $model = is_null($kategoritindakan_id) ? new KategoriTindakan : KategoriTindakan::findOne($post['kategoritindakan_id']);
        
        try {
            if($post['is_active'] == 0 && !is_null($kategoritindakan_id)){
                $find = DaftarTindakan::find()->where(['kategoritindakan_id' => $kategoritindakan_id])->count();
                if($find > 0){
                    return [
                            'status' => 422,
                            'title' => 'Edit Data Gagal!',
                            'text' => 'Data yang masih Tindakan tidak bisa di non aktifkan!'
                    ];
                }
            }
            $input = array(
                'kategoritindakan_id' => $kategoritindakan_id,
                'kategoritindakan_nama' => $post['kategoritindakan_nama'],
                'kategori_kode' => $post['kategori_kode'],
                'kategoritindakan_namalainnya' => $post['kategoritindakan_namalainnya'],
                'catatan' => $post['catatan'],
                'is_active' => $post['is_active'],
            );
            $model->attributes = $input;
            
            if($model->save()){
                if(!empty($post['daftarTindakan'])){
                    $daftarTindakan = json_decode($post['daftarTindakan'], true);
                    $arrTindakan = array();
                    foreach($daftarTindakan['data'] as $k => $v){
                        $arrTindakan[] = $v['id'];
                    }
                    $arrTindakan = implode($arrTindakan, ",");
                    $sql = 'update daftartindakan_m SET kategoritindakan_id = '.$model->kategoritindakan_id.' where daftartindakan_id IN ('.$arrTindakan.')';

                    $updateTindakan = Yii::$app->db->createCommand($sql)->execute();
                }
                $transaction->commit();
                $result = [
                    'status' => 200,
                    'title' => 'Proses Berhasil!',
                    'text' => (is_null($kategoritindakan_id) ? 'Tambah Kategori Berhasil!' : 'Edit Kategori Berhasil!'),
                ];
            }else{
                $transaction->rollBack();
                $result['status'] = 422;
                $result['data'] = $model->getErrors();
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

    public function actionDetailTindakanKategori($id)
    {
        $model = new DaftarTindakan;
        $query = $model::find();

        $query->where(['kategoritindakan_id' => $id]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        $data = [
            'query' => $query,
        ];

        if($_GET['per-page'] < 0) {
            $data['pagination'] = false;
        }

        return new ActiveDataProvider($data);
    }

    private function getData($filter = null)
    {
        $returnData = KategoriTindakan::find();

        if($filter) {
            $returnData->where($filter);
        }

        return $returnData;
    }

    public function actionDelete() 
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        try {
            $model = KategoriTindakan::findOne($id);
            if ($model) {
                $model->is_deleted = true;
                $model->deleted_date = date('Y-m-d H:i:s');
                if($model->save()) {
                    DaftarTindakan::updateAll([
                        'kategoritindakan_id' => null,
                    ], 'kategoritindakan_id = '.$id.'');

                    $transaction->commit();
                    $result = [
                        'status' => 200,
                        'title' => 'Hapus Berhasil',
                        'text' => 'Hapus Tindakan Berhasil',
                    ];
                }
            } else {
                $transaction->rollBack();
                $result['status'] = 422;
                $result['text'] = "Gagal Menghapus Data";
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

    public function actionDeleteTindakan($id = null) 
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        
        try {
            $model = DaftarTindakan::findOne($request->post('id'));
            $model->kategoritindakan_id = null;
            if ($model->save()) {
                $transaction->commit();
                $result = [
                    'status' => 200,
                    'title' => 'Hapus Berhasil',
                    'text' => 'Hapus Tindakan Berhasil',
                ];
            } else {
                $transaction->rollBack();
                $result['status'] = 422;
                $result['text'] = "Gagal Menghapus Data";
                $result['text'] = $model->getErrors();
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

    /**
    * @controller actionExportPdf 
    * @attribute #table# => table data
    **/
    // public function actionExportPdf()
    // {
    //     try {
    //         $request = Yii::$app->request;
    //         $id = $request->get('id');
    //         $modelKategoriTindakan = new KategoriTindakan;
    //         $data_kategoriTindakan = $modelKategoriTindakan::find()->andWhere(['kategoritindakan_id' => $id])->asArray()->all();
    //         $title = Yii::t('app', 'Master Kelompok');

    //         // $data = [
    //         //     'KategoriTindakan' => $data_kategoriTindakan,
    //         // ];

    //         // return $data;

    //         $header = [];
    //         $print = new DocoPrint();
    //         $print->attributes = [
    //             '#table#' => $this->renderPartial('index',[
    //                 'title'=> $title,
    //                 'header'=> $header,
    //                 'data' => $data_kategoriTindakan,
    //             ]),
    //         ];
    //         $print->Output();

    //         // return true;
    //     }catch(\Exception $e){
    //         \Yii::$app->response->statusCode = 500;
    //         return [
    //             'message' => $e->getMessage()
    //         ];
    //     }
    // }

    protected $_title = "Kategori Tindakan";
    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $model = new KategoriTindakan;
        $query = $model::find();

        $header = $footer = [];
        if(!is_null($request->get('advanced-filter'))) {
            $advancedFilter = $request->get('advanced-filter');
            if(isset($advancedFilter['kategori_kode'])){
                $header['Kode Kategori'] = $advancedFilter['kategori_kode'];
            }
            if(isset($advancedFilter['kategoritindakan_nama'])){
                $header['Nama Kategori'] = $advancedFilter['kategoritindakan_nama'];
            }
            if(isset($advancedFilter['kategoritindakan_namalainnya'])){
                $header['Nama Lain Kategori'] = $advancedFilter['kategoritindakan_namalainnya'];
            }
            if(isset($advancedFilter['catatan'])){
                $header['Catatan'] = $advancedFilter['catatan'];
            }
            if(isset($advancedFilter['is_active'])) {
                $is_active = $advancedFilter['is_active'];
                $query->andWhere(['is_active' => $is_active]);
                $header['Status'] = ($is_active) ? 'Aktif' : 'Tidak Aktif';
            }
        }

        $query->orderBy($request->get('order'));
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);
        
        foreach ($dataProvider->getModels() as $key => $value) {
            $newValue = [];
            $newValue[\Yii::t('app', 'Kode Kategori')] = $value['kategori_kode'];
            $newValue[\Yii::t('app', 'Nama Kategori')] = $value['kategoritindakan_nama'];
            $newValue[\Yii::t('app', 'Nama Lainnya')] = $value['kategoritindakan_namalainnya'];
            $newValue[\Yii::t('app', 'Status')] = ($value['is_active'] == true) ? 'Aktif' : 'Tidak Aktif';
            $newValue[\Yii::t('app', 'Catatan')] = $value['catatan'];
            $result[$key] = $newValue;
        }

        $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, [], $footer, [], true);
        $filePath->save('php://output');
        die();
    }

    /**
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk mengganti data di table
    * @attribute #tanggal# => tanggal sekarang
    */
    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $title = 'Data Master Kategori Tindakan';
        $get = $request->get();
        $model = new KategoriTindakan;
        $query = $model::find();
        // if(!is_null($request->get('advanced-filter'))) {
        //     $advancedFilter = $request->get('advanced-filter');
        //     if(isset($advancedFilter['status'])) {
        //         $is_active = $advancedFilter['status'];
        //         $query->andWhere(['is_active' => $is_active]);
                
        //     }
        // }

        $query->orderBy($request->get('order'));
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $print = new DocoPrint();
        $print->attributes = [
            '#tanggal#' => date('d M Y'),
            '#datatable#' => $this->renderPartial('_cetak', [
                'data' => $dataProvider->getModels(),
                'title' => $title,
            ]),
        ];

        $print->Output();
    }
}