<?php

/**
 * @author Rizal Faidin
 * @todo Master Group InaCbg
 * @copyright 2018-04-26 18:47:42 sandwing_

 */
namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use app\modules\v1\models\GroupInaCbg;
use app\modules\v1\models\DaftarTindakan;
use app\modules\v1\models\ObatAlkes;
use app\modules\v1\models\Lookup;
use yii\helpers\ArrayHelper;

class GroupInaCbgController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\GroupInaCbg';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["view"] = ["POST", "GET"];
        $verbs["create"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "GET"];
        $verbs["delete"] = ["DELETE","POST"];
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
        $model = new GroupInaCbg;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionView($id)
    {
        $model = new GroupInaCbg;
        $model = $model->find()->andWhere(['groupinacbg_id'=>$id]);
        $model = $model->asArray()->one();
        
        return $model;
    }

    /**
     * @author Budi
     */
    public function actionDetailTindakanInacbg($id, $type)
    {
        if($type == 0){
            $model = new DaftarTindakan;
        }else{
            $model = new ObatAlkes;
        }
        $query = $model::find();
        $query->where(['groupinacbg_id' => $id]);
        $query = DocoRestActiveFilter::advancedFilter($model, $query->asArray());

        $data = [
            'query' => $query,
        ];

        if($_GET['per-page'] < 0) {
            $data['pagination'] = false;
        }

        return new ActiveDataProvider($data);
    }

    public function actionListDaftarTindakan($id=null)
    {
        $models = DaftarTindakan::find();
        if ($id) {
            $models = $models->andWhere([
                'groupinacbg_id'=>$id
            ]);
        }
        $models = $models->asArray()->all();
        return $models;
    }

    public function actionDelete() 
    {
        $request = Yii::$app->request;
        $id = $request->get('id');
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        try {
            $model = GroupInaCbg::findOne($id);
            if($model->is_obat){
                $find = ObatAlkes::find()->where(['groupinacbg_id' => $id])->count();
            }else{
                $find = DaftarTindakan::find()->where(['groupinacbg_id' => $id])->count();
            }
            if($find > 0){
                return [
                        'status' => 422,
                        'title' => 'Gagal Menghapus Data',
                        'text' => 'Data Masih Memiliki Obat / Tindakan!'
                ];
            }
            if ($model) {
                $model->is_deleted = true;
                $model->deleted_date = date('Y-m-d H:i:s');
                if($model->save(false)) {
                    DaftarTindakan::updateAll([
                        'groupinacbg_id' => null,
                    ], 'groupinacbg_id = '.$id.'');

                    $transaction->commit();
                    $result = [
                        'status' => 200,
                        'title' => 'Hapus Berhasil',
                        'text' => 'Hapus Group Inacbg Berhasil',
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

    public function actionDeleteList($id = null) 
    {
        $request = Yii::$app->request;
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        
        try {
            if($request->post('type') == 0){
                $model = DaftarTindakan::findOne($request->post('id'));
            }else{
                $model = ObatAlkes::findOne($request->post('id'));
            }
            $model->groupinacbg_id = null;
            if ($model->save()) {
                $transaction->commit();
                $result = [
                    'status' => 200,
                    'title' => 'Hapus Berhasil',
                    'text' => ($request->post('type') == 0) ? 'Hapus Tindakan Berhasil' : 'Hapus Obat Berhasil' ,
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
     * @edited By : Budi
     */
    public function actionSimpanInacbg() 
    {
        $post = \Yii::$app->request->post();
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        $result = array();
        $groupinacbg_id = isset($post['groupinacbg_id']) ? $post['groupinacbg_id'] : null;
        $model = is_null($groupinacbg_id) ? new GroupInaCbg : GroupInaCbg::findOne($post['groupinacbg_id']);
        
        try {
            if($post['is_active'] == 0 && !is_null($groupinacbg_id)){
                if($post['is_obat']){
                    $find = ObatAlkes::find()->where(['groupinacbg_id' => $groupinacbg_id])->count();
                }else{
                    $find = DaftarTindakan::find()->where(['groupinacbg_id' => $groupinacbg_id])->count();
                }
                if($find > 0){
                    return [
                            'status' => 422,
                            'title' => 'Edit Data Gagal!',
                            'text' => 'Data yang masih memiliki Obat / Tindakan tidak bisa di non aktifkan!'
                    ];
                }
            }
            
            $input = [
                'groupinacbg_id' => $groupinacbg_id,
                'groupinacbg_nama' => $post['groupinacbg_nama'],
                'groupinacbg_kode' => $post['groupinacbg_kode'],
                'groupinacbg_namalainnya' => $post['groupinacbg_namalainnya'],
                'catatan' => $post['catatan'],
                'is_active' => $post['is_active'],
                'is_obat' => $post['is_obat'],
            ];
            
            $model->attributes = $input;
            if($model->save()){
                if(!empty($post['listdata'])){
                    $listdata = json_decode($post['listdata'], true);
                    $arrlist = [];
                    if(!empty($listdata['data'])) {
                        foreach($listdata['data'] as $k => $v){
                            $arrlist[] = $v['id'];
                        }

                        $arrlist = implode($arrlist, ",");
                        if($post['is_obat'] == 0){
                            $sql = 'update daftartindakan_m SET groupinacbg_id = '.$model->groupinacbg_id.' where daftartindakan_id IN ('.$arrlist.')';
                        }else{
                            $sql = 'update obatalkes_m SET groupinacbg_id = '.$model->groupinacbg_id.' where obatalkes_id IN ('.$arrlist.')';
                        }

                        $updateList = Yii::$app->db->createCommand($sql)->execute();
                    }
                }
                $transaction->commit();
                $result = [
                    'status' => 200,
                    'title' => 'Proses Berhasil',
                    'text' => is_null($groupinacbg_id) ? 'Tambah Group InaCbgs Berhasil' : 'Edit Group InaCbgs Berhasil',
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

    protected $_title = "Group INA CBGS";
    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        
        try {
            $model = new GroupInaCbg;
            $query = $model::find();
            $header = $footer = [];
            if(!is_null($request->get('advanced-filter'))) {
                $advancedFilter = $request->get('advanced-filter');
                if(isset($advancedFilter['groupinacbg_kode'])) {
                    $header['Group INACBG Kode'] = $advancedFilter['groupinacbg_kode'];
                }
                if(isset($advancedFilter['groupinacbg_nama'])) {
                    $header['Group INACBG Nama'] = $advancedFilter['groupinacbg_nama'];
                }
                if(isset($advancedFilter['groupinacbg_namalainnya'])) {
                    $header['Group INACBG Nama Lain'] = $advancedFilter['groupinacbg_namalainnya'];
                }
                if(isset($advancedFilter['catatan'])) {
                    $header['Catatan'] = $advancedFilter['catatan'];
                }
                if(isset($advancedFilter['is_active'])) {
                    $is_active = $advancedFilter['is_active'];
                    $header['Status'] = $is_active ? 'Aktif' : 'Tidak Aktif';
                    $query->andWhere(['is_active' => $is_active]);
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
                $newValue[\Yii::t('app', 'Kode Group INA CBGS')] = $value['groupinacbg_kode'];
                $newValue[\Yii::t('app', 'Nama Group INA CBGS')] = $value['groupinacbg_nama'];
                $newValue[\Yii::t('app', 'Nama Lainnya')] = $value['groupinacbg_namalainnya'];
                $newValue[\Yii::t('app', 'Status')] = ($value['is_active'] == true) ? 'Aktif' : 'Tidak Aktif';
                $newValue[\Yii::t('app', 'Catatan')] = $value['catatan'];
                $result[$key] = $newValue;
            }

            $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, [] , $footer, [], true);
            $filePath->save('php://output');
            die;
        } catch (Exception $e) {
            return $e->getMesasge();
        }
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
        $model = new GroupInaCbg;
        $query = $model::find();
        $query->orderBy($request->get('order'));
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->all();
        $print = new DocoPrint();
        $print->attributes = [
            '#tanggal#' => date('d M Y'),
            '#datatable#' => $this->renderPartial('_cetak', [
                'data' => $data,
                'title' => $title,
            ]),
        ];

        $print->Output();
    }

    public function actionGetRequest()
    {
        $lookup = GroupInaCbg::find()
            ->where(['is_active' => 1])
            ->all();

        return ArrayHelper::map($lookup, 'groupinacbg_id', 'groupinacbg_nama');
    }
}
