<?php
/*
 * @Author: metafiliana 
 * @Date: 2018-01-26 13:27:43 
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2019-02-19 13:12:14
 * @Description: 
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;

use app\modules\v1\models\LaporanMorbiditasView;
use app\modules\v1\models\Penjamin;
use app\modules\v1\models\RuanganPegawai;
use app\modules\v1\models\KelompokDiagnosa;
use app\modules\v1\models\LaporanDiagnosaPasienView;
use app\modules\v1\models\PegawaiView;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;

class LapDiagnosaPasienController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\LaporanDiagnosaPasienView';

    public function verbs()
    {
        $verbs = parent::verbs();
        // additional/ override verbs
        $verbs["index"] = ["GET", "POST"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);

        return $actions;
    }

    private function requestFilter($request, $query){
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');

        $advancedFilters = $request->get('advanced-filter', []);
        if(isset($advancedFilters)){
            if (isset($advancedFilters['tgl_diagnosa']) ) {
                $explode = explode(" - ", $advancedFilters['tgl_diagnosa']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tgl_diagnosa']); // Unset Advanced Filter  date range
            }

        }
        $query->andWhere(['between', 'tgl_diagnosa', $start, $end]);
        return $query;
    }

    /**
    *
    * @see Fungsi override action index
    * @return array, activeQueryRecords data daftar pasien rajal
    *
    */
    public function actionIndex()
    {
        try {
            $request = Yii::$app->request;

            $model = new LaporanDiagnosaPasienView;
            $query = $model::find()->where(['kelompokdiagnosa_nama'=> "Diagnosa Utama"]);
            
            $getQueryFilter = $this->requestFilter($request, $query);
            $resQuery = DocoRestActiveFilter::advancedFilter($model, $getQueryFilter);
            $resQuery->orderby(['tgl_diagnosa'=> SORT_DESC]);

            return new ActiveDataProvider([
                'query' => $resQuery,
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

            $find_kelompokdiagnosa = $this->getKelompokDiagnosa();
            $data_kelompokdiagnosa = $find_kelompokdiagnosa->asArray()->all();

            return [
                'data-kelompokdiagnosa' => $data_kelompokdiagnosa,
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
    * @controller actionExportPdf
    * @attribute #datatable# => Untuk menampilkan data table
    * @attribute #title# => Laporan Diagnosa Pasien
    * @attribute #cetak_oleh# => Di cetak oleh
    * @attribute #kepala# => Nama Kepala 
    * @attribute #kepalanip# => NIP Kepala
    * @attribute #ruangan_nama# => Nama Ruangan
    */
    public function actionExportPdf()
    {
        try {
            $request = Yii::$app->request;
            $title = 'Laporan Diagnosa Pasien';
            $get = $request->get();
            $ruangan_id = $get['ruangan_id'];

            $model = new LaporanDiagnosaPasienView;
            $query = $model::find()->where(['kelompokdiagnosa_nama'=> "Diagnosa Utama"]);
            
            $getQueryFilter = $this->requestFilter($request, $query);

            $resQuery = DocoRestActiveFilter::advancedFilter($model, $getQueryFilter);
            $resQuery->orderby(['tgl_diagnosa'=> SORT_DESC]);
            $resultData = $resQuery->asArray()->all();
            
            $result = [];
            $ruangan_nama = '';
            foreach ($resultData as $key => $value) {
                $value['tgl_diagnosa'] = date('d M Y H:i:s', strtotime($value['tgl_diagnosa']));
                $value['no_pendaftaran_rm'] = $value['no_pendaftaran'].' / '.$value['no_rekam_medik'];
                $expDiagnosaNama =  explode(' - ', $value['diagnosa_nama']);
                $value['exp_nama_diagnosa'] = isset($expDiagnosaNama[1]) ? $expDiagnosaNama[1] : $value['diagnosa_nama'];
                $result[] = $value;
                $ruangan_nama = $value['ruangan_nama'];
            }
            $dataKepala = (PegawaiView::find()->where(['ruangan_id'=>$ruangan_id, 'jabatan_id'=>DocoConstants::VAR_J_K_R])->one()) ? PegawaiView::find()->where(['ruangan_id'=>$ruangan_id, 'jabatan_id'=>DocoConstants::VAR_J_K_R])->one() : '';

            $print = new DocoPrint();
            $print->attributes = [
                '#datatable#' => $this->renderPartial('_cetak_pdf', [
                    'data' => $result,
                    'title' => $title,
                ]),
                '#title#' => $title,
                '#ruangan_nama#' => $ruangan_nama,
                '#cetak_oleh#' => Yii::$app->jwt->user->nama_pemakai,
                '#kepala#'=> ($dataKepala) ? $dataKepala['nama_pegawai'] : '-',
                '#kepalanip#'=> ($dataKepala) ? $dataKepala['nomorindukpegawai'] : '-',
            ];
            $print->Output();
                    
        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

     /**
    * @controller actionExportPdfPasien
    * @attribute #datatable_diagnosa# => Untuk menampilkan data Diagnosa
    * @attribute #title# => Laporan Diagnosa Pasien
    * @attribute #instalasi_nama# => Untuk menampilkan Nama Instalasi
    * @attribute #ruangan_nama# => Untuk menampilkan Nama Ruangan
    * @attribute #no_rekam_medik# => Untuk menampilkan No Rekam Medik
    * @attribute #no_pendaftaran# => Untuk menampilkan No Pendaftaran
    * @attribute #nama_pasien# => Untuk menampilkan Nama Pasien
    * @attribute #tanggal_lahir# => Untuk menampilkan Tanggal Lahir
    * @attribute #jenis_kasus_penyakit# => Untuk menampilkan Jenis Kasus Penyakit
    * @attribute #jenis_kelamin# => Untuk menampilkan Jenis Kelamin
    * @attribute #cetak_oleh# => Untuk menampilkan Di cetak oleh
    * @attribute #kepala# => Untuk menampilkan Nama Kepala 
    * @attribute #kepalanip# => Untuk menampilkan NIP Kepala
    */
    public function actionExportPdfPasien()
    {
        try {
            $request = Yii::$app->request;
            $title = 'Laporan Diagnosa Pasien';
            $get = $request->get();
            $advancedFilters = $request->get('advanced-filter', []);

            $model = new LaporanDiagnosaPasienView;
            $query = $model::find()->where(['kelompokdiagnosa_nama'=> "Diagnosa Utama"]);
            
            $query->andWhere(['pendaftaran_id' => $advancedFilters['pendaftaran_id'] ]);
            $query->andWhere(['ruangan_id' => $advancedFilters['ruangan_id'] ]);            

            // $getQueryFilter = $this->requestFilter($request, $query);
            // $resQuery = DocoRestActiveFilter::advancedFilter($model, $query);

            $query->orderby(['tgl_diagnosa'=> SORT_DESC]);
            $resultData = $query->asArray()->all();
            $result = [];
            $instalasi_nama = '';
            $ruangan_nama = '';
            $no_rekam_medik = '';
            $no_pendaftaran = '';
            $nama_pasien = '';
            $jenis_kelamin = '';
            $tanggal_lahir = '';
            $jenis_kasus_penyakit = '';
            foreach ($resultData as $key => $value) {
                $value['tgl_diagnosa'] = date('d F Y H:i:s', strtotime($value['tgl_diagnosa']));
                $value['no_pendaftaran_rm'] = $value['no_pendaftaran'].' / '.$value['no_rekam_medik'];
                $expDiagnosaNama =  explode(' - ', $value['diagnosa_nama']);
                $value['exp_nama_diagnosa'] = isset($expDiagnosaNama[1]) ? $expDiagnosaNama[1] : $value['diagnosa_nama'];
                // $value['name_diagnosa'] = json_decode($value['diagnosa_nama'])->text;
                $result[] = $value;

                $instalasi_nama = $value['instalasi_nama'];
                $ruangan_nama = $value['ruangan_nama'];
                $no_rekam_medik = $value['no_rekam_medik'];
                $no_pendaftaran = $value['no_pendaftaran'];
                $nama_pasien = $value['nama_pasien'];
                $jenis_kelamin = $value['jenis_kelamin'];
                $tanggal_lahir = date('d F Y', strtotime($value['tanggal_lahir']));;
                $jenis_kasus_penyakit = $value['jeniskasuspenyakit_nama'];
            }
            $advancedFilters = $request->get('advanced-filter', []);
            $ruangan_id = $advancedFilters['ruangan_id'];
            $dataKepala = (PegawaiView::find()->where(['ruangan_id'=>$ruangan_id, 'jabatan_id'=>DocoConstants::VAR_J_K_R])->one()) ? PegawaiView::find()->where(['ruangan_id'=>$ruangan_id, 'jabatan_id'=>DocoConstants::VAR_J_K_R])->one() : '';
            $print = new DocoPrint();
            $print->attributes = [
                '#datatable_diagnosa#' => $this->renderPartial('_cetak_pdf_pasien', [
                    'data' => $result,
                    'title' => $title,
                ]),
                '#title#' => $title,
                '#cetak_oleh#' => Yii::$app->jwt->user->nama_pemakai,
                '#kepala#'=> ($dataKepala) ? $dataKepala['nama_pegawai'] : '-',
                '#kepalanip#'=> ($dataKepala) ? $dataKepala['nomorindukpegawai'] : '-',                
                '#instalasi_nama#' => $instalasi_nama,
                '#ruangan_nama#' => $ruangan_nama,
                '#no_rekam_medik#' => $no_rekam_medik,
                '#no_pendaftaran#' => $no_pendaftaran,
                '#nama_pasien#' => $nama_pasien,
                '#jenis_kelamin#' => $jenis_kelamin,
                '#tanggal_lahir#' => $tanggal_lahir,
                '#jenis_kasus_penyakit#' => $jenis_kasus_penyakit,
            ];
            $print->Output();
                    
        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    public function actionExportExcel()
    {
        try{
            $request = Yii::$app->request;
            $title = $this->_title;
            $get = $request->get();

            $model = new LaporanDiagnosaPasienView;
            $query = $model::find()->where(['kelompokdiagnosa_nama'=> "Diagnosa Utama"]);
            
            $start = date('Y-m-d 00:00:00');
            $end = date('Y-m-d 23:59:59');

            $advancedFilters = $request->get('advanced-filter', []);
            if(isset($advancedFilters)){
                if (isset($advancedFilters['tgl_diagnosa']) ) {
                    $explode = explode(" - ", $advancedFilters['tgl_diagnosa']);
                    if(count($explode) == 2) {
                        $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                        $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                    }
                    unset($_GET['advanced-filter']['tgl_diagnosa']); // Unset Advanced Filter  date range
                }

            }
            $query->andWhere(['between', 'tgl_diagnosa', $start, $end]);

            $resQuery = DocoRestActiveFilter::advancedFilter($model, $query);
            $resQuery->orderby(['tgl_diagnosa'=> SORT_DESC]);
            $resultData = $resQuery->asArray()->all();
         
            $no = 0;
            foreach ($resultData as $key => $value) {
                $no ++;
                $data['Tanggal Diagnosa'] = date('d M Y H:i:s', strtotime($value['tgl_diagnosa']));
                $data['No. Pendaftaran / No. Rekam Medik'] = $value['no_pendaftaran'].' / '.$value['no_rekam_medik'];
                $data['Nama Pasien'] = $value['nama_pasien'];
                $data['Klasifikasi Diagnosa'] = $value['klasifikasidiagnosa_nama'];
                $data['Kode Diagnosa'] = $value['diagnosa_kode'];
                $expDiagnosaNama =  explode(' - ', $value['diagnosa_nama']);
                $data['Nama Diagnosa'] = isset($expDiagnosaNama[1]) ? $expDiagnosaNama[1] : $value['diagnosa_nama'];
                $result[] = $data;
            }

            $header = ['Tanggal Diagnosa'=> date('d F Y', strtotime($start)) . ' - '.date('d F Y', strtotime($end))
                        ];
            $filePath = DocoHelpers::exportExcel($title, $result, $header, array(
                    "uploadPath" => "./uploads"),[],[],true);

            $filePath->save('php://output');
            die;
            
        }catch(\Exception $e){
            \Yii::$app->response->statusCode = 500;
            return ['message' => $e->getMessage()];
        }
    }

    /**
     *
     * export excel laporan diagnosa pasien rajal
     *
     */
    protected $_title = "Laporan Diagnosa Pasien";
    public function actionExportExcelOld()
    {
        $model = new LaporanMorbiditasView;
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
            if(isset($_GET['advanced-filter']['tglmorbiditas'])) {
                $explode = explode(" - ", $_GET['advanced-filter']['tglmorbiditas']);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:00', strtotime($explode[1]));
                }
                unset($_GET['advanced-filter']['tglmorbiditas']); // Unset Advanced Filter  date range
                $between = true;
            }
        }
        // if($between) {
            $query->andWhere(['between', 'tglmorbiditas', $start, $end]);
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
            $value['tglmorbiditas'] = date("j M Y", strtotime($value['tglmorbiditas']));

            $newValue = [];
            $newValue[\Yii::t('app', 'tglmorbiditas')] = $value['tglmorbiditas'];
            $newValue[\Yii::t('app', 'no_pendaftaran')] = $value['no_pendaftaran'];
            $newValue[\Yii::t('app', 'no_rekam_medik')] = $value['no_rekam_medik'];
            $newValue[\Yii::t('app', 'nama_pasien')] = $value['nama_pasien'];
            $newValue[\Yii::t('app', 'kelompokdiagnosa_nama')] = $value['kelompokdiagnosa_nama'];
            $newValue[\Yii::t('app', 'klasifikasidiagnosa_nama')] = $value['klasifikasidiagnosa_nama'];
            $newValue[\Yii::t('app', 'diagnosa_kode')] = $value['diagnosa_kode'];
            $newValue[\Yii::t('app', 'diagnosa_nama')] = $value['diagnosa_nama'];
            $newValue[\Yii::t('app', 'diagnosa_namalainnya')] = $value['diagnosa_namalainnya'];
            $newValue[\Yii::t('app', 'diagnosa_katakunci')] = $value['diagnosa_katakunci'];
            $result[$key] = $newValue;
        }

        // Directory Creation
        $header = array(
            Yii::t("app", "tglmorbiditas") => (($start." - ".$end)),
            Yii::t("app", "no_pendaftaran") => (@$yiiRestfulParams['advanced-filter']['no_pendaftaran']),
            Yii::t("app", "no_rekam_medik") => (@$yiiRestfulParams['advanced-filter']['no_rekam_medik']),
            Yii::t("app", "nama_pasien") => (@$yiiRestfulParams['advanced-filter']['nama_pasien']),
            Yii::t("app", "kelompokdiagnosa_nama") => (@$yiiRestfulParams['advanced-filter']['kelompokdiagnosa_nama']),
            Yii::t("app", "klasifikasidiagnosa_nama") => (@$yiiRestfulParams['advanced-filter']['klasifikasidiagnosa_nama']),
            Yii::t("app", "diagnosa_kode") => (@$yiiRestfulParams['advanced-filter']['diagnosa_kode']),
            Yii::t("app", "diagnosa_nama") => (@$yiiRestfulParams['advanced-filter']['diagnosa_nama']),
            Yii::t("app", "diagnosa_namalainnya") => (@$yiiRestfulParams['advanced-filter']['diagnosa_namalainnya']),
            Yii::t("app", "diagnosa_katakunci") => (@$yiiRestfulParams['advanced-filter']['diagnosa_katakunci']),
        );

        $filePath = DocoHelpers::exportExcel($title, $result, $header, array(
                "uploadPath" => "./uploads",
                "filePrefix" => "rj",
                "subTitle" => $nama_ruangan
        ),[],[],true);

        $filePath->save('php://output');
        die;
    }

    /**
    *
    * @see Fungsi get data penjamin
    * @return array, activeQueryRecords
    *
    */
    private function getKelompokDiagnosa()
    {
        $data = KelompokDiagnosa::find()->select([
                "kelompokdiagnosa_id",
                "kelompokdiagnosa_nama",
            ]);
        
        return $data;

    }
}