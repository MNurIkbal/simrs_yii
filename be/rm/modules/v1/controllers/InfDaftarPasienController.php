<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoPrint;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use app\modules\v1\models\InfKunjunganRsView;
use app\modules\v1\models\Lookup;
use Picqer\Barcode\BarcodeGeneratorPNG;
use Da\QrCode\QrCode;
use yii\helpers\ArrayHelper;
use Doco\Services\Cache;

use app\modules\v1\models\Instalasi;
use app\modules\v1\models\Ruangan;
use app\modules\v1\models\Pegawai;

class InfDaftarPasienController extends DocoActiveController
{
    public $modelClass = '';
    const JK = 'jenis_kelamin';
    const KN = 'kn';
    const BG = 'bg';
    const AD = 'ad';
    const STY = 'sty';
    const SB = 'sb';
    const BIN = 'bin';
    const NBG = 'new_bg';
    const KTP = 94;


	public function verbs()
    {
        return parent::verbs();
    }

    public function actions()
    {
        return [
        	'get-data' => 'app\modules\v1\actions\GetDataPendaftaranAction',
            'update-proses' => 'app\modules\v1\actions\UpdateProsesAction',
            'get-status-pendaftaran-baru' => 'app\modules\v1\actions\GetStatusPendaftaranBaruAction',
        ];
    }

    /**
    * @controller actionPrintGelangPasien
    * @attribute #no_rekam_medik# => no rekam medik
    * @attribute #nama_pasien# => nama pasien
    * @attribute #jenis_kelamin# => jenis kelamin pasien
    * @attribute #tanggal_lahir# => tanggal lahir
    * @attribute #nama_depan# => gelar / nama depan pasien
    * @attribute #qrcode# => qrcode
    **/
    public function actionPrintGelangPasien()
    {
        try{
            $model = new InfKunjunganRsView;
            $request = Yii::$app->request;
            $pendaftaran_id = $request->post('pendaftaran_id',null);
            if(!$pendaftaran_id){
                throw new \yii\base\ErrorException("ID Pendaftaran Tidak Ditemukan", 500);
            }

            $kunjungan = $model::find()->where(['pendaftaran_id'=>$pendaftaran_id])->orderBy('tgl_pendaftaran DESC')->one();

            if(!$kunjungan){
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }

            $print = new DocoPrint();
            $no_rekam_medik = isset($kunjungan->no_rekam_medik) ? $kunjungan->no_rekam_medik : '-';
            $qrCode = (new QrCode($no_rekam_medik))
                ->setSize(50)
                ->setMargin(3)
                ->useForegroundColor(0, 0, 0);

            $qrcode = '<img src="data:image/png;base64,' . base64_encode($qrCode->writeString()) . '">';

            $print->attributes = [
                '#no_rekam_medik#' => $no_rekam_medik,
                '#nama_pasien#' => isset($kunjungan->nama_pasien
                )?$kunjungan->nama_pasien:'',
                '#tanggal_lahir#' => isset($kunjungan->tanggal_lahir)
                    ?date('d M Y', strtotime($kunjungan->tanggal_lahir))
                    :'',
                '#jenis_kelamin#' => isset($kunjungan->jenis_kelamin)
                    ? $kunjungan->jenis_kelamin
                    :'',
                '#nama_depan#' => isset($kunjungan->namadepan) ? $kunjungan->namadepan : '',
                '#qrcode#' => $qrcode,
            ];
            $print->Output();
        }catch (Exception $e){
            return ['Error'=>$e->getMessage()];
        }
    }


    /**
    * @controller actionPrintLabelPasien
    * @attribute #no_rekam_medik# => no rekam medik
    * @attribute #nama_depan# => gelar / nama depan pasien
    * @attribute #nama_pasien# => nama pasien
    * @attribute #jk# => jenis kelamin pasien
    * @attribute #tgl_lahir# => tanggal lahir
    * @attribute #barcode# => barcode
    * @attribute #umur# => umur pasien
    **/
    public function actionPrintLabelPasien()
    {
        try{
            $model = new InfKunjunganRsView;
            $request = Yii::$app->request;
            $pendaftaran_id = $request->post('pendaftaran_id',null);
            if(!$pendaftaran_id){
                throw new \yii\base\ErrorException("ID Pendaftaran Tidak Ditemukan", 500);
            }
            $kunjungan = $model::find()->where(['pendaftaran_id'=>$pendaftaran_id])->orderBy('tgl_pendaftaran DESC')->one();
            if(!$kunjungan){
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }
            $print = new DocoPrint();

            $barcodeGen = new BarcodeGeneratorPNG;
            $barcode = '<img style="height: 20px; margin-left: 2px" src="data:image/png;base64,' . base64_encode($barcodeGen->getBarcode($kunjungan->no_rekam_medik, $barcodeGen::TYPE_CODE_128)) . '">';
            $print = new DocoPrint();
            $print->attributes = [
                '#nama_depan#' => isset($kunjungan->namadepan) ? strtoupper($kunjungan->namadepan) : '',
                '#nama_pasien#' => isset($kunjungan->nama_pasien) ? strtoupper($kunjungan->nama_pasien) : '' ,
                '#no_rekam_medik#' => isset($kunjungan->no_rekam_medik) ? $kunjungan->no_rekam_medik : '',
                '#barcode#' => $barcode,
                '#jk#' => isset($kunjungan->jeniskelamin) ? ($kunjungan->jeniskelamin == 15) ? 'L' : 'P' : '' ,
                '#tgl_lahir#' => isset($kunjungan->tanggal_lahir) ? date('d-m-Y', strtotime($kunjungan->tanggal_lahir)) : '' ,
                '#umur#' => isset($kunjungan->umur) ? $kunjungan->umur : '' ,
            ];

            $print->Output();
        }catch (Exception $e){
            return ['Error'=>$e->getMessage()];
        }
    }

     /**
    * @controller actionPrintLabelPasienBaru
    * @attribute #data# => data
    **/
    public function actionPrintLabelPasienBaru()
    {
        try{
            $request = Yii::$app->request;
            $pasien_id = $request->post('pasien_id', null);
            $no_pendaftaran = $request->post('no_pendaftaran', null);
            $template = $request->post('template', 'default');
            $jumlah = $request->post('jumlah', 1);

            $model = new InfKunjunganRsView;
            $request = Yii::$app->request;
            $pendaftaran_id = $request->post('pendaftaran_id',null);
            if(!$pendaftaran_id){
                throw new \yii\base\ErrorException("ID Pendaftaran Tidak Ditemukan", 500);
            }

            $pendaftaran_id = explode(',', $pendaftaran_id);

            $kunjungan = $model::find()->where(['pendaftaran_id' => $pendaftaran_id])->orderBy('tgl_pendaftaran DESC')->asArray()->all();
            if(!$kunjungan){
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }

            $arr_kunjungan = array();
            foreach ($kunjungan as $key => $value) {
                for ($i = 0; $i < $jumlah; $i++) { 
                    $arr_kunjungan[] = $value;
                }
            }
            
            $print = new DocoPrint();
            switch ($template) {
                case self::KN:
                    $loc = 'print_label_pasien';
                    break;
                case self::BG:
                    $loc = 'print_label_pasien_bg';
                    break;
                case self::NBG:
                    $loc = 'print_label_pasien_new_bg';
                    break;
                case self::AD:
                    $loc = 'print_label_pasien_ad';
                    break;
                case self::STY:
                    $loc = 'print_label_pasien_sty';
                    break;
                case self::SB:
                    $loc = 'print_label_pasien_sb';
                    break;
                case self::BIN:
                    $loc = 'print_label_pasien_bin';
                    break;
                default:
                    $loc = 'print_label_pasien';
                    break;
            }

            $print = new DocoPrint();
            $print->attributes = [
                '#data#' => $this->renderPartial($loc, [
                    'data' => $arr_kunjungan,
                    'jumlah_data' => $jumlah,
                    self::JK => Cache::getLookupByType(self::JK),
                    'self_ktp' => self::KTP
                ])
            ];
            
            return $print->OutputHtml();
        } catch (\Exception $e) {
            return ['Error'=>$e->getMessage()];
        }
    }

    /**
    * @controller actionPrintBelumProses
    * @attribute #data# => data
    **/

    public function actionPrintBelumProses()
    {
        // kode doc = rm-print-belum-proses
        try{
            ini_set('memory_limit', '-1'); // set memori lebih banyak untuk kebutuhan cetak data yang banyak
            ini_set("pcre.backtrack_limit", "500000000"); // menambah large code size untuk mpdf, default limit = 1000000
    
            $model = new InfKunjunganRsView;
            $request = Yii::$app->request;
            $kunjungan = $model::find()->select([
                'status_konfirmasirm',
                'nama_pasien',
                'no_rekam_medik',
                'ruangan_nama',
                'instalasi_nama',
                'nama_pegawai',
                'tgl_pendaftaran'
            ]);

            $tgl_awal = '';
            $tgl_akhir = '';
            $advancedFilters = $request->get('advanced-filter', []);
            if (isset($advancedFilters['tgl_pendaftaran_awal'])   && isset($advancedFilters['tgl_pendaftaran_akhir'])) {
            $tgl_awal = $advancedFilters['tgl_pendaftaran_awal'];
            $tgl_akhir = $advancedFilters['tgl_pendaftaran_akhir'];
            $kunjungan->andWhere(['between', 'tgl_pendaftaran', $tgl_awal, $tgl_akhir]);
            }
            $kunjungan->andWhere([ 'status_konfirmasirm_id' => 664]);
            $periode = '-';
            if(!empty($tgl_awal) && !empty($tgl_akhir)) {
            
            $awal = date('d M Y', strtotime($tgl_awal));
            $akhir = date('d M Y', strtotime($tgl_akhir));
            $periode = $awal.' - '.$akhir;
            }
            $kunjungan = $kunjungan->orderBy('tgl_pendaftaran DESC')->asArray()->all();
       
            if(!$kunjungan){
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }
            $print = new DocoPrint();
            $print->attributes = [
                '#table#' => $this->renderPartial('index', [
                    'detail' => $kunjungan
                ]),
                '#periode#' => 'Tanggal : ' .$periode,
               
            ];
            //return $print->attributes;
            $print->Output();
        }catch (Exception $e){
            return ['Error'=>$e->getMessage()];
        }
    }

    public function actionGetBundleData()
    {
        $results = $this->getLookUp('status_riwayat_rm');
        $jenisReservasi = $this->getLookUp('jenis_reservasi');
        return [
            'results' => $results,
            'jenisReservasi' => $jenisReservasi
        ];
    }

    private function getLookUp($types)
    {
        $getLookup = Lookup::find()->select([
            'lookup_id',
            'lookup_type',
            'lookup_name',
        ])->where([
            'lookup_type' => $types, 
            'is_active' => true, 
            'is_deleted' => false
        ])->orderBy([
            'lookup_type' => SORT_ASC, 
            'lookup_urutan' => SORT_ASC,
            'lookup_name' => SORT_ASC,
        ])->asArray()->all();

        foreach ($getLookup as $lookup) {
            $results[$lookup['lookup_type']][] = $lookup;
        }

        return $results;
    }

    public function actionFilters()
    {
        $request = Yii::$app->request;
        $result = $resultData = [];
        $term = $request->get('term', null);
        $page = $request->get('page', 1);
        $type = $request->get('type', []);
        $additionalPayload = $request->get('additionalPayload', []);
        $carabayar_id = $instalasi_id = null;
        if(isset($additionalPayload['carabayar_id']) && !empty($additionalPayload['carabayar_id'])) {
            $carabayar_id = $additionalPayload['carabayar_id'];
        }
        if(isset($additionalPayload['instalasi_id']) && !empty($additionalPayload['instalasi_id'])) {
            $instalasi_id = $additionalPayload['instalasi_id'];
        }
        $limit = $request->get('limit', DocoConstants::LIMIT_INFINITY_SCROLL);
        switch ($type) {
            case 'instalasi':
                $listInstalasi = [DocoConstants::INST_ID_RJ, DocoConstants::INST_ID_RD, DocoConstants::INST_ID_RI];
                $result = Instalasi::find()
                    ->select(['instalasi_id as id', 'instalasi_nama as text'])
                    ->where(['is_pelayanan' => true, 'is_active' => true]);

                if(!empty($term)) {
                    $result->andWhere(['like', 'LOWER(instalasi_nama)', strtolower($term)]);
                }
                $result->orderBy(['instalasi_nama' => SORT_ASC]);
                break;
            
            case 'ruangan': 
                $result = Ruangan::find()
                    ->select(['ruangan_id as id', 'ruangan_nama as text'])
                    ->where(['instalasi_id' => $instalasi_id, 'is_active' => true]);

                    if(!empty($term)) {
                        $result->andWhere(['like', 'LOWER(ruangan_nama)', strtolower($term)]);
                    }
                    $result->orderBy(['ruangan_nama' => SORT_ASC]);
                break;

            case 'dokter': 
                $kelompokPegawai = [
                    DocoConstants::KELOMPOK_PEGAWAI_DOKTER,
                    DocoConstants::KELOMPOK_PEGAWAI_PERAWAT,
                    DocoConstants::KELOMPOK_PEGAWAI_BIDAN
                ];

                $result = Pegawai::find()
                    ->select(['pegawai_id as id', 'nama_pegawai as text'])
                    ->where(['kelompokpegawai_id' => $kelompokPegawai, 'is_active' => true]);

                    if(!empty($term)) {
                        $result->andWhere(['like', 'LOWER(nama_pegawai)', strtolower($term)]);
                    }
                    $result->orderBy(['nama_pegawai' => SORT_ASC]);
                break;
            case 'status_rm':
                $result = Lookup::find()
                    ->select(['lookup_id as id', 'lookup_name as text'])
                    ->where(['lookup_type' => 'status_konfirmasirm']);

                    if(!empty($term)) {
                        $result->andWhere(['like', 'LOWER(lookup_name)', strtolower($term)]);
                    }
                    $result->orderBy(['lookup_name' => SORT_ASC]);
                break;
            case 'jenis_pendaftaran':
                $result = Lookup::find()
                    ->select(['(lookup_id::text) as id', 'lookup_name as text'])
                    ->union("SELECT 'Pendaftaran Langsung' as id, 'Pendaftaran Langsung' as text")
                    ->where(['lookup_type' => 'jenis_reservasi']);

                    if(!empty($term)) {
                        $result->andWhere(['like', 'LOWER(lookup_name)', strtolower($term)]);
                    }
                    $result->orderBy(['lookup_name' => SORT_ASC]);
                break;
        }
        $resultData = $result->asArray()->all();
        return $resultData;
    }
}