<?php

/*
* @Author: Sunarko / Master Tempat Tidur
* @Date:   2018-07-24 10:00:15
* @Last Modified by:  
* @Last Modified time: 
*/

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;
use Doco\Repositories\LookUpTransaksiRepositories;
// model
use app\modules\v1\models\TempatTidurView;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\KamarRuangan;
use app\modules\v1\models\KamarTempatTidur;
use app\modules\v1\models\KetTempatTidur;
use app\modules\v1\models\PasienAdmisi;
use app\modules\v1\models\MasukKamar;
use Doco\models\bpjs\BpjsAplicare;


class TempatTidurController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\TempatTidurView';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        $verbs["ajax"] = ["POST", "GET"];
        $verbs["update"] = ["POST", "PUT"];
        $verbs["list-display-antrian"] = ["POST", "GET"];
        $verbs["list-type-screen"] = ["POST", "GET"];
        $verbs["list-function-screen"] = ["POST", "GET"];
        $verbs["get-tempat-tidur-ranap"] = ["GET"];
        $verbs["get-tempat-tidur-ranap-dep"] = ["GET"];
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

    public function actionIndex() {
        $model = new TempatTidurView;
        $query = $model::find();
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);
    }

    /**
    *
    * @todo Fungsi get data ruangan
    * @return array, activeQueryRecords
    *
    */
    private function getRuangan()
    {
        // Query
        $sql = "SELECT ruangan_id, ruangan_nama
            FROM ruangan_m 
            WHERE is_deleted = false AND is_active = true
            ORDER BY ruangan_nama ASC";
        $result = Ruangan::findBySql($sql);

        return $result;
    }

    public function actionDataNamaRuangan()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        
        $term = strtoupper($post['term']);
        
        $sql = "select ruangan_nama from ruangan_m where UPPER( ruangan_nama ) LIKE '%{$term}%' and is_deleted = false
            group by ruangan_nama
            order by ruangan_nama asc limit 50
        "; 
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    /**
    *
    * @todo Fungsi get data kamar
    * @return array, activeQueryRecords
    *
    */
    private function getKamar($id = null)
    {
        $sql = "SELECT kamarruangan_id, kamarruangan_nokamar
            FROM kamarruangan_m
            WHERE is_deleted = false AND is_active = true
            ORDER BY kamarruangan_nokamar ASC";
        if($id){
            $sql .= "AND ruangan_id={$id}";
        }
        
        $result = KamarRuangan::findBySql($sql);
        return $result;
    }

    // /**
    // *
    // * @todo Fungsi get data status
    // * @return array, activeQueryRecords
    // *
    // */
    // private function getKeterangan()
    // {
    //     // Query
    //     $sql = "SELECT kettempattidur_id, kettempattidur_nama
    //         FROM kettempattidur_m
    //     ";
        
    //     // Result
    //     $result = KetTempatTidur::findBySql($sql);

    //     // Return
    //     return $result;
    // }

    public function actionDataNamaKamar()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        
        $term = strtoupper($post['term']);
        
        $sql = "select kamarruangan_nokamar from kamarruangan_m where UPPER( kamarruangan_nokamar ) LIKE '%{$term}%' and is_deleted = false
            group by kamarruangan_nokamar
            order by kamarruangan_nokamar asc limit 50
        "; 
        $data = Yii::$app->db->createCommand($sql)->queryAll();

        return $data;
    }

    public function actionGenerateApi($id='')
    {
        try {
            $request = Yii::$app->request;

            // Get ruangan
            $modelRuangan = $this->getRuangan();
            $dataRuangan = $modelRuangan->asArray()->all();

            // Get jenis status
            // $modelStatus = $this->getKeterangan();
            // $dataStatus = $modelStatus->asArray()->all();
            
            if ($id) {
                $modelKamar = $this->getKamar($id);
                $dataKamar = $modelKamar->asArray()->all();
            }else{
                $modelKamar = $this->getKamar();
                $dataKamar = $modelKamar->asArray()->all();
            }
            
            return [
                'data-ruangan' => $dataRuangan,
                // 'data-status' => $dataStatus,
                'data-kamar' => $dataKamar,
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

    /**
    * @controller actionCetakPdf
    * @attribute #tempattidur_table# => table
    * @attribute #nama_rs# => nama rumah sakit
    * @attribute #ruangan_nama# => Filter Nama Ruangan
    * @attribute #nama_kamar# => Filter Nama Kamar
    * @attribute #status# => Filter Status
    **/
    public function actionCetakPdf()
    {
        ini_set('memory_limit', '512M');
        $request = Yii::$app->request;
        $nama_rs = '';
        if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['nama_rs'])) {
                    $nama_rs = $_GET['advanced-filter']['nama_rs'];
                }
                if (isset($_GET['advanced-filter']['ruangan_nama'])) {
                    $header['Ruangan Nama'] = $_GET['advanced-filter']['ruangan_nama'];
                }
                if (isset($_GET['advanced-filter']['kamarruangan_nokamar'])) {
                    $header['Nama Kamar'] = $_GET['advanced-filter']['kamarruangan_nokamar'];
                }
                if (isset($_GET['advanced-filter']['is_active'])) {
                    $header['Status'] = ($_GET['advanced-filter']['is_active'] == 'true') ? 'Aktif' : 'Tidak Aktif';
                }
            }
        // Define model
        $model = new TempatTidurView;

        // Query
        $query = $model::find();

        // Doco active filter
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $data = $query->all();
        
        $print = new DocoPrint();
        $print->attributes = [
            '#tempattidur_table#' => $this->renderPartial('index', [
                'detail' => $data
            ]),
            '#nama_rs#' => $nama_rs,
            '#ruangan_nama#' => isset($header['Ruangan Nama']) ? $header['Ruangan Nama'] : '',
            '#nama_kamar#' => isset($header['Nama Kamar']) ? $header['Nama Kamar'] : '',
            '#status#' => isset($header['Status']) ? $header['Status'] : '',
        ];
        $print->Output();
    }

    protected $_title = 'MASTER TEMPAT TIDUR';
    public function actionExportExcel()
    {
        try {
           // Declare emty data
            $data = [];
            $header = $footer = [];

            $nama_rs = '';
            if (isset($_GET['advanced-filter'])) {
                if (isset($_GET['advanced-filter']['nama_rs'])) {
                    $nama_rs = $_GET['advanced-filter']['nama_rs'];
                }
                if (isset($_GET['advanced-filter']['ruangan_nama'])) {
                    $header['Ruangan Nama'] = $_GET['advanced-filter']['ruangan_nama'];
                }
                if (isset($_GET['advanced-filter']['kamarruangan_nokamar'])) {
                    $header['Nama Kamar'] = $_GET['advanced-filter']['kamarruangan_nokamar'];
                }
                if (isset($_GET['advanced-filter']['is_active'])) {
                    $header['Status'] = ($_GET['advanced-filter']['is_active'] == 'true') ? 'Aktif' : 'Tidak Aktif';
                }
            }

            // Define model
            $model = new TempatTidurView;
            $query = $model::find();
            // Doco active filter
            $query = DocoRestActiveFilter::advancedFilter($model, $query)->all();
            // Assign data
            if (!empty($query)) {
                // Loop
                foreach ($query as $index => $value) {
                    $newdata = [];
                    // Assign data
                    $newdata['Ruangan'] = $value->ruangan_nama;
                    $newdata['Nama Kamar'] = $value->kamarruangan_nokamar;
                    $newdata['No Tempat Tidur'] = $value->no_tempattidur;
                    // $newdata['Status'] = $value->kettempattidur_nama;
                    $newdata['Status'] = ($value->is_active) ? 'Aktif' : 'Tidak Aktif' ;
                    
                    $data[] = $newdata;
                }
            }

            $filePath = DocoHelpers::exportExcel($this->_title . " " . strtoupper($nama_rs), $data, $header, [], $footer, [], true);
            $filePath->save('php://output');
            die;

            return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
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

    public function actionDelete($id)
    {
        try {
            $tempatTidur = KamarTempatTidur::find()->where(['kamartempattidur_id' => $id])->one();
            $cekAvailable = $this->cekTransaksi($id);
            if($cekAvailable){
                $response = [
                    'title' => 'Proses Gagal !',
                    'text' => 'Data yang sudah pernah di transaksikan tidak dapat dihapus!',
                    'status' => 422
                ];
                return $response;
            }
            $result = (new KamarTempatTidur)->delete($id);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];
            $integrasi = (new BpjsAplicare)->createOrUpdateAplicare($tempatTidur->kamarruangan_id, DocoConstants::TYPE_UPDATE_APLICARE);
            return $response;
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    public function actionSaveData()
    {
        $model = new KamarTempatTidur;
        $request = Yii::$app->request;
        $post = $request->post();
        try {
            $model->attributes = $post;
            $getKeterangan = KamarRuangan::find()->joinWith(['kettempattidur' => function($model){
                $model->andWhere(['is_kosong' => true]);
            }])->where(['kamarruangan_id' => $model->kamarruangan_id])->asArray()->one();
            if(empty($getKeterangan) || !isset($getKeterangan['kettempattidur']['kettempattidur_id'])){
                throw new \Exception("Data Tidak Ditemukan!");
            }
            $model->kettempattidur_id = $getKeterangan['kettempattidur']['kettempattidur_id'];
            $model->kamartempattidur_kode = ! empty($getKeterangan['kamarruangan_kode']) ? $getKeterangan['kamarruangan_kode'] : null; 
            if ($model->validate() && $model->save()) {
                /** integrasi aplikasi   */
                if (!empty($model->is_rekapkinerjaprofesi)) {
                    $integrasi = (new BpjsAplicare)->createOrUpdateAplicare($model->kamarruangan_id, DocoConstants::TYPE_UPDATE_APLICARE);
                }
                return ['message' => 'Data Berhasil di simpan'];
            }else {
                return [
                    'data' => $model->errors,
                    'status' => 422
                ];
            }
        } catch (\yii\db\Exception $e) {
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
    }
    public function actionChangeStatus($id)
    {
        $request = Yii::$app->request;
        $status = $request->post('status', true);
        try {
            if($status == 0){
                $cekAvailable = $this->cekTransaksi($id);
                if($cekAvailable){
                    $response = [
                        'title' => 'Proses Gagal !',
                        'text' => 'Data yang sudah pernah di transaksikan tidak dapat di non aktifkan!',
                        'status' => 422
                    ];
                    return $response;
                }
            }
            $getData = KamarTempatTidur::find()->where(['kamartempattidur_id' => $id])->one();
            if(!$getData){
                throw new \Exception("Data Tidak Tersedia", 1);
            }
            $getData->is_active = $status;
            if (!$getData->is_active) {
                $getData->is_rekapkinerjaprofesi = false;
                $getData->is_terisi = false;
            }
            if(!$getData->save()){
                throw new \Exception("Data Terjadi Kesalahan", 1);
            }
            $integrasi = (new BpjsAplicare)->createOrUpdateAplicare($getData->kamarruangan_id, DocoConstants::TYPE_UPDATE_APLICARE);
            return ['title' => 'Proses Berhasil!', 'text' => 'Status Berhasil Diubah'];
        } catch (Exception $e) {
            return $e->getMessage();
        } catch (\yii\db\Exception $e) {
            return $e->getMessage();
        }
        
    }
    public function cekTransaksi($kamartempattidur_id){
        try {
            $findAdmisi = PasienAdmisi::find()->where(['kamartempattidur_id' => $kamartempattidur_id])->asArray()->one();
            if($findAdmisi){
                return true;
            }
            $findMasukKamar = MasukKamar::find()->where(['kamartempattidur_id' => $kamartempattidur_id])->asArray()->one();
            if($findMasukKamar){
                return true;
            }
            return false;
        } catch (\yii\db\Exception $e) {
            return true;
        } catch(\Exception $e){
            return true;
        }
    }
    public function actionViewData($id)
    {
        // $model = new KamarTempatTidur;
        // $query = $model->find();
        // if ($id) {
        //     $query->andWhere(['kamartempattidur_id' => $id]);
        // }
        // return $query->one();
        // Query
        $sql = "SELECT km.* , kr.ruangan_id
            FROM kamartempattidur_m km
            INNER JOIN kamarruangan_m kr ON  kr.kamarruangan_id = km.kamarruangan_id
            WHERE kamartempattidur_id={$id}
        "; 
        $data = Yii::$app->db->createCommand($sql)->queryOne();

        return $data;
    }

    public function actionGetDataKamar()
    {
        $request = Yii::$app->request;
        $ruangan_id = $request->get('ruangan_id');
        
        $model = new KamarRuangan;
        $query = $model::find();
        $query->orderBy(['kamarruangan_nokamar' => SORT_ASC]);
        $query->Where(['ruangan_id' => $ruangan_id]);

        $result = $query->asArray()->all();
        return $result;

        /*$query = DocoRestActiveFilter::advancedFilter($model, $query);
        return new ActiveDataProvider([
            'query' => $query,
        ]);*/
    }

    public function actionUpdate($id,$act)
    {
        try {
            $now = date('Y-m-d H:i:s');
            $request = Yii::$app->request;
            $model = KamarTempatTidur::findOne($id);
            $modalV = new TempatTidurView;
            $post = $request->post();
            if ($model && !empty($model)) {
                $model->attributes = $post;
                $modalV->attributes = $post;
                $model->is_active = $act;
                
                if ($model->validate() && $model->save()) {
                    return [
                        'message' => 'Data Berhasil di simpan',
                    ];
                } else {
                    // $errors = DocoHelpers::parseError($model->errors,'KamarTempatTidur');
                    return [
                        'data' => $model->errors,
                        'status' => 422
                    ];
                }
            }
            throw new \Exception("Data Tidak Di Temukan");
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

    /**
     * @author Andri Amirul (andri.amirul@sirs.co.id)
     * @method getRuanganRanap (Mengambil Data Ruangan Ranap)
     * @param String $term
     * @param Integer $page
     * @return Object
     */
    public function actionGetTempatTidurRanap() 
    {
        $request = Yii::$app->request;
        $term = $request->get('term', null);
        $page = $request->get('page', 1);
        $limit = DocoConstants::LIMIT_INFINITY_SCROLL;
        $instalasiRanapId = (new LookUpTransaksiRepositories)->getInstalasiIdRi();
        $query = new \yii\db\Query();
        $kamarRuanganQuery = $query->from('kamartempattidur_m')
            ->innerJoin('kamarruangan_m', 'kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id')
            ->innerJoin('ruangan_m', 'kamarruangan_m.ruangan_id = ruangan_m.ruangan_id')
            ->andWhere(['instalasi_id' => $instalasiRanapId]);
        if (!empty($term)) {
            $kamarRuanganQuery->andWhere(['ILIKE', 'LOWER(no_tempattidur)', strtolower($term)]);
        }
        return $kamarRuanganQuery->limit($limit)->offset(($page - 1) * $limit)->all();
    }

    /**
     * @author Andri Amirul (andri.amirul@sirs.co.id)
     * @method getRuanganRanapDep (Mengambil Data Tempat Tidur Yang Dependency Dengan Kamar Tanpa Infinity Scroll)
     * @param Integer $kamarruangan_id
     * @return Array
     */

    public function actionGetTempatTidurRanapDep($kamarruangan_id = null)
    {
        $model = KamarTempatTidur::find(true);
        if($kamarruangan_id){
            $model->where(['kamarruangan_id'=>$kamarruangan_id]);
        }
        return $model->asArray()->all();
    }

    public function actionChangeStatusIntegrasiBpjs()
    {
        $request = Yii::$app->request;
        $status = $request->post('status', true);
        $id = $request->get('id');
        try {
            $model = KamarTempatTidur::find()->where(['kamartempattidur_id' => $id])->one();
            if (empty($model)) {
                return ['title' => 'Proses Gagal !','text' => 'Data Tidak Tersedia','status' => 422];
            }
            $model->is_rekapkinerjaprofesi = $status;
            if (!$model->is_rekapkinerjaprofesi) {
                $model->is_terisi = false;
            }
            if(!$model->save()){
                return ['title' => 'Proses Gagal !', 'text' => 'Data Terjadi Kesalahan', 'status' => 422];
            }
            $integrasi = (new BpjsAplicare)->createOrUpdateAplicare($model->kamarruangan_id, DocoConstants::TYPE_UPDATE_APLICARE);
            return ['title' => 'Proses Berhasil!', 'text' => 'Status Berhasil Diubah'];
        } catch (\yii\db\Exception $e) {
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
        
    }

    public function actionChangeStatusOccupied()
    {
        $request = Yii::$app->request;
        $status = $request->post('status', true);
        $id = $request->get('id');
        try {
            $model = KamarTempatTidur::find()->where(['kamartempattidur_id' => $id])->one();
            if (empty($model)) {
                return ['title' => 'Proses Gagal !','text' => 'Data Tidak Tersedia','status' => 422];
            }
            $model->is_terisi = $status;
            if(!$model->save()){
                return ['title' => 'Proses Gagal !', 'text' => 'Data Terjadi Kesalahan', 'status' => 422];
            }
            $integrasi = (new BpjsAplicare)->createOrUpdateAplicare($model->kamarruangan_id, DocoConstants::TYPE_UPDATE_APLICARE);
            return ['title' => 'Proses Berhasil!', 'text' => 'Status Berhasil Diubah'];
        } catch (\yii\db\Exception $e) {
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        } catch (\Exception $e) {
            $this->logError($e);
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
        
    }
}