<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\DokRekamMedis;
use app\modules\v1\models\PosisidokrmR;
use app\modules\v1\models\WarnaDokRekamMedik;
use app\modules\v1\models\Pasien;
use app\modules\v1\models\LokasiRakRekamMedik;
use app\modules\v1\models\InfoPosisiDokRekamMedik;
use app\modules\v1\models\Subrak;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DokRekamMedisController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\DokRekamMedis';

    public function verbs()
    {
        $verbs = parent::verbs();
        // $verbs["index"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['view']);
        return $actions;
    }

    public function actionIndex()
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'lokasirak_m,subrak_m,pasien_m,warnadokrekammedik_m');
        $model = new DokRekamMedis;
        $query = $model::find()
            ->joinWith(['lokasirakrekammedik' => function($query){
                $query->from('lokasirak_m');
            }])
            ->joinWith(['subrak' => function($query){
                $query->from('subrak_m');
            }])
            ->joinWith(['pasien' => function($query){
                $query->from('pasien_m');
            }])
            ->joinWith(['warnadokrm' => function($query){
                $query->from('warnadokrekammedik_m');
            }]);
        $between = false;
        $start = date('Y-m-01 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tglrekammedis'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglrekammedis']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglrekammedis']); // Unset Advanced Filter  date range
                $between = true;
            }
        }
        if($between) {
            $query->andWhere(['between', 'tglrekammedis', $start, $end]);
        }
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            $model = new DokRekamMedis;
            $post = $request->post('DokRekamMedisForm');

            $mPasien = Pasien::findOne(['no_rekam_medik' => $post['no_rekam_medis']]);
            if ($request->post() && !empty($mPasien)) {
                $post['pasien_id'] = $mPasien->pasien_id;
                $model->attributes = $post;
                
                $model->warnadokrm_id = $this->setWarnaDokumen($post['no_rekam_medis']);
                $model->tglrekammedis = $mPasien->tgl_rekam_medik;
                $model->tglmasukrak = date('Y-m-d');
                $model->statusrekammedis = !empty($mPasien->statusrekammedis)?$mPasien->statusrekammedis:"-";

                if ($model->save()) {

                    $PosisidokrmR = new PosisidokrmR;
                    $PosisidokrmR->dokrekammedis_id = $model->dokrekammedis_id;
                    $PosisidokrmR->ruanganakhir_id = $post['ruangan_id'];
                    $PosisidokrmR->save();

                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'DokRekamMedisForm');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
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

    public function actionUpdate($id)
    {
        try {
            $request = Yii::$app->request;
            $model = DokRekamMedis::findOne($id);
            $post = $request->post('DokRekamMedisForm');

            if ($post) {
                $model->attributes = $post;
                $model->warnadokrm_id = $this->setWarnaDokumen($post['no_rekam_medis']);

                if ($model->save()) {
                    return ['message' => 'Data Berhasil di simpan'];
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'DokRekamMedis');
                    return [
                        'data' => $errors,
                        'status' => 422
                    ];
                }
            }
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

    protected function setWarnaDokumen($norm)
    {
        if(strlen($norm)){
            $first_index = $norm[0];
            $mWarna = WarnaDokRekamMedik::find()->where(['warnadokrm_kodewarna'=>$first_index])->one();
            if ($mWarna) {
              return $mWarna->warnadokrm_id;
            }else {
              return null;
            }
        }else{
          return 1;
        }
    }

    public function actionView($id)
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'lokasirak_m,subrak_m,pasien_m,warnadokrekammedik_m');

        $model = new DokRekamMedis;
        $dokRekamMedik = DokRekamMedis::findOne($id);
        $query[] = $dokRekamMedik;

        //get Pasien
        $sql = "select dokrekammedis_m.pasien_id, pasien_m.no_rekam_medik from dokrekammedis_m
            inner join pasien_m on dokrekammedis_m.pasien_id=pasien_m.pasien_id
            where dokrekammedis_m.is_deleted='false'
            group by dokrekammedis_m.pasien_id, pasien_m.no_rekam_medik
            order by pasien_m.no_rekam_medik asc
        ";
        $items['Pasien'] = Yii::$app->db->createCommand($sql)->queryAll();

        //get LokasiRakRekamMedik
        $dataLokasiRakRekamMedik = LokasiRakRekamMedik::find('lokasirak_id', 'lokasirak_nama')->where(['is_active' => 't', 'is_deleted' => 'f', ]);
        $itemsLokasiRakRekamMedik = $dataLokasiRakRekamMedik->all();
        $items['LokasiRak'] = $itemsLokasiRakRekamMedik;
        //get LokasiRakRekamMedik
        $dataSubrak = Subrak::find('subrak_id', 'subrak_nama')->where(['is_active' => 't', 'is_deleted' => 'f']);
        $dataSelectedSubrak = Subrak::find('subrak_id', 'subrak_nama')->where(['is_active' => 't', 'is_deleted' => 'f', 'lokasirak_id' => $dokRekamMedik->lokasirak_id]);
        $itemsSubrak = $dataSubrak->all();
        $selectedItemsSubrak = $dataSelectedSubrak->all();
        $items['LokasiSubrak'] = $itemsSubrak;
        $items['SelectedLokasiSubrak'] = $selectedItemsSubrak;

        $query[] = $items;

        return $query;
    }

    protected $_title = 'laporan dokumen rekam medik';
    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        try {
            $_GET['expand'] = $request->get('expand', 'lokasirak_m,subrak_m,pasien_m,warnadokrekammedik_m');

            $model = new DokRekamMedis;
            $query = $model::find()
                ->joinWith(['lokasirakrekammedik' => function($query){
                    $query->from('lokasirak_m');
                }])
                ->joinWith(['subrak' => function($query){
                    $query->from('subrak_m');
                }])
                ->joinWith(['pasien' => function($query){
                    $query->from('pasien_m');
                }])
                ->joinWith(['warnadokrm' => function($query){
                    $query->from('warnadokrekammedik_m');
                }]);

            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $result = [];
            foreach ($query->asArray()->all() as $key => $value) {
                $newValue = [];
                $newValue['no_rak'] = $value['lokasirakrekammedik']['lokasirak_nama'];
                $newValue['sub_rak'] = $value['subrak']['subrak_nama'];
                $newValue['no_rekam_medik'] = $value['pasien']['no_rekam_medik'];
                $newValue['warnadokrm'] = $value['warnadokrm']['warnadokrm_namawarna'];
                $result[$key] = $newValue;
            }
            // return $result;
            $header = array(
                Yii::t('app', "No rak") => (@$_GET['advanced-filter']['lokasirak_m.lokasirak_nama']),
                Yii::t('app', "No sub rak") => (@$_GET['advanced-filter']['subrak_nama']),
                Yii::t('app', "No rekam medik") => (@$_GET['advanced-filter']['subrak_nama']),
                Yii::t('app', "Warna dokumen rekam medik") => (@$_GET['advanced-filter']['warnadokrekammedik_m.warnadokrm_namawarna']),
            );

            $filePath = DocoHelpers::exportExcel($this->_title, $result, $header, [],[],[],true);

            $filePath->save('php://output');
            die;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }

    }

    /**
    * @controller actionExportPdf
    * @attribute #table_dokrm# => table
    **/

    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $_GET['expand'] = $request->get('expand', 'lokasirak_m,subrak_m,pasien_m,warnadokrekammedik_m');

        $model = new DokRekamMedis;
        $query = $model::find()
            ->joinWith(['lokasirakrekammedik' => function($query){
                $query->from('lokasirak_m');
            }])
            ->joinWith(['subrak' => function($query){
                $query->from('subrak_m');
            }])
            ->joinWith(['pasien' => function($query){
                $query->from('pasien_m');
            }])
            ->joinWith(['warnadokrm' => function($query){
                $query->from('warnadokrekammedik_m');
            }]);

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $result = [];
        foreach ($query->asArray()->all() as $key => $value) {
            $newValue = [];
            $newValue['no_rak'] = $value['lokasirakrekammedik']['lokasirak_nama'];
            $newValue['sub_rak'] = $value['subrak']['subrak_nama'];
            $newValue['no_rekam_medik'] = $value['pasien']['no_rekam_medik'];
            $newValue['warnadokrm'] = $value['warnadokrm']['warnadokrm_namawarna'];
            $result[$key] = $newValue;
        }
        $header = array(
            Yii::t('app', "No rak") => (@$_GET['advanced-filter']['lokasirak_m.lokasirak_nama']),
            Yii::t('app', "No sub rak") => (@$_GET['advanced-filter']['subrak_nama']),
            Yii::t('app', "No rekam medik") => (@$_GET['advanced-filter']['subrak_nama']),
            Yii::t('app', "Warna dokumen rekam medik") => (@$_GET['advanced-filter']['warnadokrekammedik_m.warnadokrm_namawarna']),
        );
        $print = new DocoPrint();
        $print->attributes = [
            '#table_dokrm#' => $this->renderPartial('index',[
                'filter'=> $header,
                'detail' => $result,
            ]),
        ];
        $print->Output();
    }

    public function actionGetApi()
    {
        try {
            // get lokasi rak
            $lokasi_rak = new LokasiRakRekamMedik;
            $query_rak = $lokasi_rak::find()->where([
                'lokasirak_m.is_deleted' => false
            ]);

            // get subrak
            $sub_rak = new Subrak;
            $query_subrak = $sub_rak::find()
                ->where(['lokasirak_m.is_deleted' => 'f'])
                ->joinWith(['lokasirak' => function($query_subrak){
                    $query_subrak->from('lokasirak_m');
                }
            ]);

            // warna dok rekam medis
            $warna_dok = new WarnaDokRekamMedik;
            $query_warna_dok = $warna_dok::find();

            // instalasi
            $instalasi = new Instalasi;
            $query_instalasi = $instalasi::find();

            // ruangan
            $ruangan = new Ruangan;
            $query_ruangan = $ruangan::find();

            return [
                'data-rak' => $query_rak->all(),
                'data-subrak' => $query_subrak->all(),
                'data-warna-dokumen' => $query_warna_dok->all(),
                'data-instalasi' => $query_instalasi->all(),
                'data-ruangan' => $query_ruangan->all(),
            ];
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


    public function actionInformasi()
    {
        $request = Yii::$app->request;
        $model = new InfoPosisiDokRekamMedik;
        $query = $model::find();
        $between = false;
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if (isset($_GET['advanced-filter'])) {
            if (isset($_GET['advanced-filter']['tglrekammedis'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglrekammedis']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglrekammedis']); // Unset Advanced Filter  date range
                $between = true;
            }
        }
        // if($between) {
            $query->andWhere(['between', 'tglrekammedis', $start, $end]);
        // }
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    // Get lokasi rak
    public function actionGetLokasirak()
    {
        // Try catch
        try {
            // Get id
            $id = Yii::$app->request->get('id');

            // Check id
            if ($id != '') {
                // Define model
                $model = Subrak::find()->where(['subrak_id' => $id, 'is_active' => 't', 'is_deleted' => 'f'])->one();

                // Check model
                if (!empty($model)) {
                    // Assign new model
                    $newModel[] = $model->lokasirak;
                }
            }
            else {
                // Assign model
                $newModel[] = new LokasiRakRekamMedik();
            }

            // Return model
            return ['data' => $newModel];
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

    // Get sub rak
    public function actionGetSubrak()
    {
        // Try catch
        try {
            // Get id
            $id = Yii::$app->request->get('id');

            // Check id
            if ($id != '') {
                // Define model
                $model = Subrak::find()->where(['lokasirak_id' => $id, 'is_active' => 't', 'is_deleted' => 'f'])->all();
            }
            else {
                // Assign model
                $model = new Subrak();
            }

            // Return model
            return ['data' => $model];
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

    public function dataDokRm()
    {
        $data = InfoPosisiDokRekamMedik::find();
        return $data;
    }

    public function actionDataNorm()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = $this->dataDokRm();
        $result->select(['no_rekam_medik','no_rekam_medik']);
        if (!empty($post['term'])) {
            $term = $post['term'];
            $result->where(['ILIKE','LOWER(no_rekam_medik)', $term]);
        }

        return $result->asArray()->all();
    }

    public function actionDataPasien()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $result = $this->dataDokRm();
        $result->select(['nama_pasien','nama_pasien']);
        if (!empty($post['term'])) {
            $term = $post['term'];
            $result->where(['ILIKE','LOWER(nama_pasien)', $term]);
        }

        return $result->asArray()->all();
    }
}
