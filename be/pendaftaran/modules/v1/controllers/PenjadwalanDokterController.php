<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\Json;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\JadwalDokter;
use app\modules\v1\models\Shift;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Pegawai;
use Doco\components\DocoHelpers;

class PenjadwalanDokterController extends DocoActiveController
{
    
    public $modelClass = 'app\modules\v1\models\JadwalDokter';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["index"] = ["POST", "GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex($instalasi_id=null, $ruangan_id=null, $pegawai_id=null, $hari = null, $jam_mulai=null, $jam_selesai = null)
    {
        $model = new JadwalDokter;
        $conditions = [];
        $conditionsTime = null;
        $conditionHari = null;
        if ($instalasi_id) {
            $conditions['instalasi_id'] = $instalasi_id;
        }
        if ($ruangan_id) {
            $conditions['ruangan_id'] = $ruangan_id;
        }
        if ($pegawai_id) {
            $conditions['pegawai_id'] = $pegawai_id;
        }
        if ($hari) {
            $conditionHari = $hari;
        }
        if ($jam_mulai) {
            $conditionsTime['jadwaldokter_mulai'] = date('H:i:s', strtotime($jam_mulai));
        }
        if ($jam_selesai) {
            $conditionsTime['jadwaldokter_tutup'] = date('H:i:s', strtotime($jam_selesai));
        }
        $query = $this->getData($conditions,$conditionsTime,$conditionHari);
        return $query->asArray()->all();
    }
    
    private function getData($conditions=[],$conditionsTime=null,$conditionHari=null)
    {
        $condition = [];
        $sql = "
            SELECT 
                t.jadwaldokter_id,
                t.ruangan_id,
                ruangan.ruangan_nama,
                t.instalasi_id,
                instalasi.instalasi_nama,
                t.pegawai_id,
                CONCAT(
                    gelarDepan.lookup_value, 
                    ' ',
                    pegawai.nama_pegawai,
                    ' ',
                    gelarBelakang.gelarbelakang_nama 
                ) as nama_pegawai,
                lookup_hari.lookup_name AS hari_nama,
                to_char(t.jadwaldokter_mulai, 'HH24:MI') as jadwaldokter_mulai,
                to_char(t.jadwaldokter_tutup, 'HH24:MI') as jadwaldokter_tutup,
                t.maximumantrian,
                t.kuota_online,
                t.is_active
            FROM jadwaldokter_m t
            JOIN pegawai_m pegawai ON t.pegawai_id = pegawai.pegawai_id
            LEFT JOIN lookup_m gelarDepan ON pegawai.gelardepan::integer = gelarDepan.lookup_id
            LEFT JOIN gelarbelakang_m gelarBelakang ON pegawai.gelarbelakang::integer = gelarBelakang.gelarbelakang_id  
            JOIN ruangan_m ruangan ON t.ruangan_id = ruangan.ruangan_id
            JOIN instalasi_m instalasi ON t.instalasi_id = instalasi.instalasi_id
            JOIN jadwalbukapoli_m bukapoli ON bukapoli.jadwalbukapoli_id = t.jadwalbukapoli_id
            JOIN lookup_m lookup_hari ON lookup_hari.lookup_id = bukapoli.hari
            WHERE t.is_deleted = false
        ";
        if ($conditions) {  
            foreach ($conditions as $field=>$value) {
                $sql .= " AND t.{$field} = :{$field}";
                $condition[':' . $field] = $value;
            }
        }
        if($conditionHari){
            $sql .=" AND bukapoli.hari = :hari_id";
            $condition[':hari_id'] = $conditionHari;
        }
        if($conditionsTime){
            if($conditionsTime['jadwaldokter_mulai'] && $conditionsTime['jadwaldokter_tutup']){
                $sql .= " AND t.jadwaldokter_mulai >= :jadwaldokter_mulai AND t.jadwaldokter_tutup <= :jadwaldokter_tutup";
                $condition[':jadwaldokter_mulai'] = $conditionsTime['jadwaldokter_mulai'];
                $condition[':jadwaldokter_tutup'] = $conditionsTime['jadwaldokter_tutup'];
            }elseif($conditionsTime['jadwaldokter_mulai']){
                $sql .= " AND t.jadwaldokter_mulai >= :jadwaldokter_mulai";
                $condition[':jadwaldokter_mulai'] = $conditionsTime['jadwaldokter_mulai'];
            }elseif($conditionsTime['jadwaldokter_tutup']){
                $sql .= " AND t.jadwaldokter_tutup <= :jadwaldokter_tutup";
                $condition[':jadwaldokter_tutup'] = $conditionsTime['jadwaldokter_tutup'];
            }
        }

         $result = JadwalDokter::findBySql($sql, $condition);
         return $result;
    }

    public function actionCreate()
    {
        return $this->render('create');
    }

    public function actionDelete()
    {
        return $this->render('delete');
    }

    public function actionUpdate()
    {
        return $this->render('update');
    }

    public function actionView()
    {
        return $this->render('view');
    }

    public function actionListShift()
    {
        try {
            $request = Yii::$app->request;

            $data = Shift::find()->asArray()->all();

            return [
                'data' => $data
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

    public function actionAjaxRuangan()
    {
        try {
            $request = Yii::$app->request;

            $data = Ruangan::find()->select([
                "ruangan_id",
                "ruangan_nama",
            ])->asArray()->all();

            return [
                'data' => $data
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

    public function actionAjaxInstalasi()
    {
        try {
            $request = Yii::$app->request;

            $data = Instalasi::find()->select([
                "instalasi_id",
                "instalasi_nama",
            ])->asArray()->all();

            return [
                'data' => $data
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

    public function actionAjaxPegawai()
    {
        try {
            $request = Yii::$app->request;

            $data = Pegawai::find()->select([
                "pegawai_id",
                "nama_pegawai",
            ])->asArray()->all();

            return [
                'data' => $data
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

    public function actionGetListruanganById($id)
    {
        try {
            $request = Yii::$app->request;

            $data = Ruangan::find()->select([
                "ruangan_id",
                "ruangan_nama",
            ])->where(['ruangan_m.instalasi_id' => $id])->asArray()->all();

            return [
                'data' => $data
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

    // Get list ruangan non poli
    public function actionAjaxRuanganNonPoli()
    {
        // Try catch
        try {
            // Define request
            $request = Yii::$app->request;

            // Get data
            $ruangan = Ruangan::find()->all();

            // Return data
            return [
                'ruangan' => $ruangan
            ];
        } catch (\yii\db\Exception $e) {
            // Make status code 500
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Make status code 500
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    // Get list dokter
    public function actionAjaxDokter()
    {
        // Try catch
        try {
            // Define request
            $request = Yii::$app->request;

            // Get data
            $dokter = Pegawai::find()->where(['kelompokpegawai_id' => 1])->all();

            // Return data
            return [
                'dokter' => $dokter
            ];
        } catch (\yii\db\Exception $e) {
            // Make status code 500
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        } catch (\Exception $e) {
            // Make status code 500
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    // Export excel
    public function actionExportExcel()
    {
        // Try catch
        try {
            // Model
            $model = JadwalDokter::find()->asArray()->all();

            // Directory Creation
            $header = array(
                Yii::t('app', "JADWAL DOKTER") => Yii::t('app', "JADWAL DOKTER"),
            );
            
            $filePath = DocoHelpers::exportExcel('Penjadwalan Dokter', $model, $header, array("uploadPath" => "./uploads"),[],[],true);

            $filePath->save('php://output');
            die;
        } catch (Exception $e) {
            // Status code
            \Yii::$app->response->statusCode = 500;

            // Return message
            return [
                'message' => $e->getMessage()
            ];
        }
    }   
}
