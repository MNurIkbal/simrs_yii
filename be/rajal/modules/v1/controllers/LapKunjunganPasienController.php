<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-03-02 11:33:31
 * @Last Modified by:   afil
 * @Last Modified time: 2018-03-02 17:09:50
 * @Description: 
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use app\modules\v1\models\infoKunjunganRajal;
use app\modules\v1\models\RuanganPegawai;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\BuatJanjiPoli;
use app\modules\v1\models\Pendaftaran;
use app\modules\v1\models\CaraBayar;

class LapKunjunganPasienController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\infoKunjunganRajal';

    public function verbs()
    {
        $verbs = parent::verbs();

        // additional/ override verbs
        $verbs["index"] = ["GET", "POST"];
        $verbs["get-list-data"] = ["GET"];
        $verbs["buat-janji-poli"] = ["POST"];
        $verbs["ubah-dokter"] = ["POST"];


        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();

        // unset default action
        unset($actions['index']);
        

        return $actions;
    }

    /**
    *
    * @see Fungsi get list data kunjungan rajal
    * @return activedataprovider
    *
    */
    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;
            $model = new InfoKunjunganRajal;
            $query = InfoKunjunganRajal::find()
                ->where([
                    'ruangan_id' => $request->get('ruangan_id', null),
                ])
                ->orWhere([
                    'konsulpoli_id' => $request->get('ruangan_id', null),
                ]);

            /**
             * Begin Special Condition date range
             * DocoRestActiveFilter cannot handle
            **/
            $between = false;
            $start = date('Y-m-01 00:00:00');
            $end = date('Y-m-d 23:59:00');

            if(isset($_GET['advanced-filter'])) {
                if(isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                    $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_pendaftaran']); // Unset Advanced Filter  date range
                    $between = true;
                }
            }
            // if($between) {
                $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
            // }
            /**
             * End Special Condition date range
            **/

            $query = DocoRestActiveFilter::advancedFilter($model, $query);

            return new ActiveDataProvider([
                'query' => $query,
            ]);
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
    *
    * @see Fungsi get list data
    * @return array
    *
    */
    public function actionGetListData()
    {
        try {
            $request = Yii::$app->request;

            $find_statusperiksa = $this->getStatusPeriksa();
            $data_statusperiksa = $find_statusperiksa->asArray()->all();

            $find_pegawai = $this->getPegawaiRuangan($request->get('id_ruangan'));
            $data_pegawai = $find_pegawai->asArray()->all();

            $find_penjamin = $this->getPenjamin();
            $data_penjamin = $find_penjamin->asArray()->all();

            $find_carabayar = $this->getCaraBayar();
            $data_carabayar = $find_carabayar->asArray()->all();

            return [
                'data-statusperiksa' => $data_statusperiksa,
                'data-pegawai' => $data_pegawai,
                'data-penjamin' => $data_penjamin,
                'data-carabayar' => $data_carabayar,
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
     *
     * export excel laporan kunjungan pasien rajal
     *
     */
    protected $_title = "Laporan kunjungan pasien rawat jalan";
    public function actionExportExcelKunjunganRajal()
    {
        $model = new InfoKunjunganRajal;
        $query = $model::find(true);
        $title = $this->_title;

        // get subtitle
        $request = Yii::$app->request;
        $nama_ruangan = DocoHelpers::decrypt($request->get('ruangan_name', ''));

        /**
         * Begin Special Condition date range
         * DocoRestActiveFilter cannot handle
        **/
        $between = false;
        $start = date('Y-m-01 00:00:00');
        $end = date('Y-m-d 23:59:00');

        if(isset($_GET['advanced-filter'])) {
            if(isset($_GET['advanced-filter']['tgl_pendaftaran'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tgl_pendaftaran']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_pendaftaran']); // Unset Advanced Filter  date range
                $between = true;
            }
        }
        // if($between) {
            $query->andWhere(['between', 'tgl_pendaftaran', $start, $end]);
        // }
        /**
         * End Special Condition date range
        **/

        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $result = [];

        foreach ($dataProvider->getModels() as $key => $value) {
            // Data Selection
            $value['tgl_pendaftaran'] = date("j M Y", strtotime($value['tgl_pendaftaran']));

            $newValue = [];
            $newValue[\Yii::t('app', 'no_antrian')] = $value['no_antrian'];
            $newValue[\Yii::t('app', 'tgl_pendaftaran')] = $value['tgl_pendaftaran'];
            $newValue[\Yii::t('app', 'ruanganasal_nama')] = $value['ruanganasal_nama'];
            $newValue[\Yii::t('app', 'no_pendaftaran')] = $value['no_pendaftaran'];
            $newValue[\Yii::t('app', 'no_rekam_medik')] = $value['no_rekam_medik'];
            $newValue[\Yii::t('app', 'nama_pasien')] = $value['nama_pasien'];
            $newValue[\Yii::t('app', 'jenis_kelamin')] = $value['jenis_kelamin'];
            $newValue[\Yii::t('app', 'carabayar_nama')] = $value['carabayar_nama'];
            $newValue[\Yii::t('app', 'penjamin_nama')] = $value['penjamin_nama'];
            $newValue[\Yii::t('app', 'nama_pegawai')] = $value['nama_pegawai'];
            $result[$key] = $newValue;
        }

        // Directory Creation
        $header = array(
            Yii::t("app", "tgl_pendaftaran") => (($start." - ".$end)),
            Yii::t("app", "nama_pegawai") => (@$yiiRestfulParams['advanced-filter']['nama_pegawai']),
            Yii::t("app", "ruanganasal_nama") => (@$yiiRestfulParams['advanced-filter']['ruanganasal_nama']),
            Yii::t("app", "carabayar_nama") => (@$yiiRestfulParams['advanced-filter']['carabayar_nama']),
            Yii::t("app", "penjamin_nama") => (@$yiiRestfulParams['advanced-filter']['penjamin_nama']),
        );

        $filePath = DocoHelpers::exportExcel($title, $result, $header, array(
            "uploadPath" => "./uploads", // Optional, default folder "uploads" di root app & root advanced app
            "filePrefix" => "rj",
            "subTitle" => $nama_ruangan,
        ));
        
        return str_replace("/v1/./", "/", \yii\helpers\Url::to([$filePath], true));
    }

    /**
    *
    * @see Fungsi get data penjamin
    * @return array, activeQueryRecords
    *
    */
    private function getPenjamin()
    {
        $penjamin = Penjamin::find()->select([
                "penjamin_id",
                "penjamin_nama",
            ]);
        
        return $penjamin;

    }

    /**
    *
    * @see Fungsi get data pegawai ruangan
    * @return array, activeQueryRecords
    *
    */
    private function getPegawaiRuangan($id_ruangan)
    {
        $sql = 'SELECT ruanganpegawai_mp.pegawai_id as pegawai_id, pegawai_m.nama_pegawai as nama_pegawai FROM ruanganpegawai_mp JOIN pegawai_m ON pegawai_m.pegawai_id = ruanganpegawai_mp.pegawai_id WHERE ruanganpegawai_mp.ruangan_id=:ruangan_id';
        $result = RuanganPegawai::findBySql($sql, [':ruangan_id' => $id_ruangan]);

        return $result;

    }

    /**
    *
    * @see Fungsi get data status periksa
    * @return array, activeQueryRecords
    *
    */
    private function getStatusPeriksa()
    {
        $sql = "SELECT lookup_id, lookup_name, lookup_name as status_periksa1 FROM lookup_m WHERE lookup_type = 'status_periksa'";
        $result = Lookup::findBySql($sql);

        return $result;

    }

    /**
    *
    * @see Fungsi get data carabayar
    * @return array, activeQueryRecords
    *
    */
    private function getCaraBayar()
    {
        $sql = "SELECT carabayar_id, carabayar_nama, carabayar_namalainnya, metode_pembayaran FROM carabayar_m WHERE is_deleted = FALSE";
        $result = CaraBayar::findBySql($sql);

        return $result;

    }

    /**
    *
    * @see Fungsi get data pasien joins
    * @var params array
    * @return array, activeQueryRecords
    *
    */
    private function getPasienDetail($id = null)
    {
        $condition = [];
        $sql = "
                SELECT
                    pendaftaran_id,
                    antrian_id,
                    pendaftaran_t.pegawai_id as pegawai_id,
                    pasien_m.no_rekam_medik AS no_rekam_medik,
                    pendaftaran_t.no_pendaftaran AS no_pendaftaran,
                    pasien_m.nama_pasien AS nama_pasien,
                    pasien_m.pasien_id AS pasien_id
                FROM
                    pendaftaran_t
                JOIN pasien_m ON pasien_m.pasien_id = pendaftaran_t.pasien_id
                WHERE
                    pendaftaran_t.is_deleted = FALSE
            ";

        // filter
        if ($id){
            $sql .= " AND pendaftaran_id = :pendaftaran_id";
            $condition[':pendaftaran_id'] = $id;
        }

        $result = Pendaftaran::findBySql($sql, $condition);
        // var_dump($result->createCommand()->getRawSql());die; //dumping raw sql

        return $result;
    }
}