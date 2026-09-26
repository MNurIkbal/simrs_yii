<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use app\modules\v1\models\InfKunjunganRsView;
use app\modules\v1\models\InfKunjunganRsTracerView;
use app\modules\v1\models\InfoKunjunganRiView;
use app\modules\v1\models\InfoKunjunganRd;
use app\modules\v1\models\InfPasienPenunjang;
use app\modules\v1\models\PasienV;
use app\modules\v1\models\KeluargaPasienV;
use app\modules\v1\models\Bpjs;
use app\modules\v1\models\Laporanr2kV;
use app\modules\v1\models\Laporanr2mkV;
use app\modules\v1\models\KonfigSystem;
use app\modules\v1\models\LoginPemakaiView;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\SySuratreferalV;
use app\modules\v1\models\SyPasienView;
use app\modules\v1\models\SyKeluargaPasienView;
use app\modules\v1\models\Antrian;

use Picqer\Barcode\BarcodeGeneratorPNG;

use app\modules\v1\cache\Cache;
use Da\QrCode\QrCode;
use Doco\components\PendaftaranHelpers;
use Doco\models\Pendaftaran;
use Doco\models\pendaftaran\InfoPendaftaranOnlineView;
use Doco\models\pendaftaran\PendaftaranOnline;

class InfDaftarSepuluhTerakhirController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfKunjunganRsView';
    const JK = 'jenis_kelamin';
    const KN = 'kn';
    const BG = 'bg';
    const AD = 'ad';
    const STY = 'sty';
    const KTP = 94;
    const PEND_ID = 'pendaftaran_id';

    public function verbs()
    {
        $verbs = parent::verbs();
        $verbs['print-karcis'] = ["POST"];
        $verbs['print-status-pasien'] = ["POST"];
        $verbs['print-kartu-pasien'] = ["POST", "GET"];
        $verbs['print-sep'] = ["POST", 'GET'];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionIndex()
    {
        try{
            $condition = [];

            $request = Yii::$app->request;
            $user_id = $request->get('user_id',null);
            $jenis = $request->get('jenis',null);
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            $instalasi_id = [];
            switch ($jenis) {
                case 'rajal':
                    $instalasi_id = DocoConstants::INST_ID_RJ;
                    break;

                case 'igd':
                    $instalasi_id = DocoConstants::INST_ID_RD;
                    break;

                case 'penunjang':
                    $instalasi_id = DocoConstants::INST_ID_PENUNJANG;
                    break;

                case 'ranap':
                    $instalasi_id = DocoConstants::INST_ID_RI;
                    break;

                case 'mcu':
                    $instalasi_id = DocoConstants::INST_ID_MCU;
                    break;

                default:
                    $instalasi_id = null;
                    break;
            }
            if ($jenis != 'ranap' && $jenis != 'penunjang') {
                $kunjunganRs = InfKunjunganRsView::find()->select([
                    'pendaftaran_id',
                    'tgl_pendaftaran',
                    'no_pendaftaran',
                    'no_rekam_medik',
                    'nama_pasien',
                    'umur',
                    'jenis_kelamin',
                    'ruangan_nama',
                    'nama_pegawai',
                    'carabayar_nama',
                    'penjamin_nama',
                    'nosep',
                    'pasien_id',
                    'nosep',
                    'carabayar_id',
                    'kelaspelayanan_id',
                    'bpjs_id',
                    'pasienadmisi_id',
                    'antrian_id'
                ]);
            } else if ($jenis == 'penunjang') {
                $kunjunganRs = InfPasienPenunjang::find();
            } else {
                $kunjunganRs = InfoKunjunganRiView::find()->select([
                    'pendaftaran_id',
                    'tgl_pendaftaran',
                    'no_pendaftaran',
                    'no_rekam_medik',
                    'nama_pasien',
                    'umur',
                    'jenis_kelamin',
                    'ruangan_nama',
                    'nama_pegawai',
                    'carabayar_nama',
                    'penjamin_nama',
                    'nosep',
                    'pasien_id',
                    'kamarruangan_nokamar',
                    'no_tempattidur',
                    'nosep',
                    'carabayar_id',
                    'klsrawat',
                    'is_aps',
                    'bpjs_kelas',
                    'kelaspelayanan_id',
                    'is_pasientitipan',
                    'tgl_admisi',
                    'bpjs_id',
                    'pasienadmisi_id',
                ]);
            }
            $kunjunganRs = $kunjunganRs->andWhere(['created_by' => $user_id]);
            if ($instalasi_id && $instalasi_id == DocoConstants::INST_ID_PENUNJANG) {
                $kunjunganRs = $kunjunganRs->orWhere(['instalasi_id' => DocoConstants::INST_ID_BEDAH]);
            } else if (!empty($instalasi_id)) {
                $kunjunganRs = $kunjunganRs->andwhere(['instalasi_id' => $instalasi_id]);
            }

            if($pendaftaran_id != null) {
                $kunjunganRs = $kunjunganRs->andwhere(['pendaftaran_id' => $pendaftaran_id]);
            }

            if($jenis != 'ranap' && $jenis != 'penunjang') {
                $kunjunganRs = $kunjunganRs->orderBy(['tgl_pendaftaran' => SORT_DESC])->limit(10);
            } else if ($jenis == 'penunjang') {
                $kunjunganRs = $kunjunganRs->orderBy(['tglmasukpenunjang' => SORT_DESC])->limit(10);
            } else {
                $kunjunganRs = $kunjunganRs->orderBy(['pasienadmisi_id' => SORT_DESC])->limit(10);
            }
            $data = $kunjunganRs->asArray()->all();
            return ['data'=>$data,'count'=>count($data)];
        } catch(\Exception $e){
            return ['Error'=>$e->getMessage()];
        }
    }

    /**
    * @controller actionPrintKarcis
    * @attribute #no_rekam_medik# => no rekam medik
    * @attribute #tanggal_pendaftaran# => Tanggal Pendaftaran
    * @attribute #nama_depan# => gelar / nama depan pasien
    * @attribute #nama_pasien# => nama pasien
    * @attribute #nama_ruangan# => nama ruangan
    * @attribute #nama_dokter# => nama dokter
    * @attribute #antrian_dokter# => antrian dokter
    * @attribute #keterangan# => Menampilkan keterangan pembayaran
    * @attribute #tabel_tarif_karcis# => tabel tarif karcis
    * @attribute #antrian_kasir# => info no antrian kasir
    **/
    public function actionPrintKarcis()
    {
        try{
            $model = new InfKunjunganRsView;
            $request = Yii::$app->request;
            $pasien_id = $request->post('pasien_id',null);
            if(!$pasien_id){
                throw new \yii\base\ErrorException("ID Pasien Tidak Ditemukan", 500);
            }
            $pendaftaran_id = $request->post('pendaftaran_id',null);
            if(!$pendaftaran_id){
                throw new \yii\base\ErrorException("ID Pendaftaran Tidak Ditemukan", 500);
            }
            $kunjungan = $model::find()->where(['pendaftaran_id'=>$pendaftaran_id])->orderBy('tgl_pendaftaran DESC')->one();
            if(!$kunjungan){
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }
            $detail = Yii::$app->db->createCommand("SELECT * FROM
                infopasienkarcisdetail_v WHERE pendaftaran_id = {$pendaftaran_id}")->queryAll();
            $caraBayar = !empty($kunjungan->carabayar_id) ? $kunjungan->carabayar_id : null;
            $show = true;
            if ($caraBayar == DocoConstants::VAR_UMUM) {
                if (!$kunjungan->is_karcis) {
                    $show = false;
                }
            } else {
                $show = false;
            }

            // get antrian kasir
            $jns_ksr = DocoConstants::VAR_JA_KSR;
            $antr_kasir = Yii::$app->db->createCommand("SELECT no_antrian FROM
                antrian_t WHERE pendaftaran_id = {$pendaftaran_id} AND jenisantrian_id = {$jns_ksr}")->queryScalar();
            $kodeDoc = 'print-karcis';
            $payloadPost = Yii::$app->request->post();
            if (isset($payloadPost['tipe']) && $payloadPost['tipe'] === 'ranap') {
                $kodeDoc = 'print-karcis-ranap';
            }
            $print = new DocoPrint($kodeDoc);
            $print->attributes = [
                '#no_rekam_medik#' => isset($kunjungan->no_rekam_medik)?$kunjungan->no_rekam_medik:'',
                '#tanggal_pendaftaran#'=> isset($kunjungan->tgl_pendaftaran) ?
                        date('d-M-Y H:i:s',strtotime($kunjungan->tgl_pendaftaran)):'',
                '#nama_depan#' => isset($kunjungan->namadepan) ? $kunjungan->namadepan : '',
                '#nama_pasien#'=>isset($kunjungan->nama_pasien) ? $kunjungan->nama_pasien:'',
                '#nama_depan#'=>isset($kunjungan->nama_depan) ? $kunjungan->nama_depan:'',
                '#nama_ruangan#'=>isset($kunjungan->ruangan_nama)?$kunjungan->ruangan_nama:'',
                '#nama_dokter#'=>isset($kunjungan->nama_pegawai)?$kunjungan->nama_pegawai:'',
                '#antrian_dokter#'=> !empty($kunjungan->no_antrian) ? $kunjungan->no_antrian : null,
                '#tabel_tarif_karcis#' => $this->renderPartial('tabel_tarif_karcis',get_defined_vars()),
                '#keterangan#' => $show ? 'Silahkan Menyelesaikan Pembayaran di Kasir' : null,
                '#antrian_kasir#' => $antr_kasir && empty($kunjungan->no_antrian) ? $antr_kasir : null,
            ];
            $print->Output();
        }catch (Exception $e){
            return ['Error'=>$e->getMessage()];
        }
    }

    /**
    * @controller actionPrintStatusPasien
    * @attribute #tgl_pendaftaran# => tanggal pendaftaran
    * @attribute #no_rekam_medik# => no rekam medik
    * @attribute #nama_depan# => gelar / nama depan pasien
    * @attribute #nama_pasien# => nama pasien
    * @attribute #cara_bayar# => cara bayar
    * @attribute #penjamin# => penjamin
    * @attribute #nama_dokter# => nama dokter
    * @attribute #nama_ruangan# => nama ruangan
    * @attribute #barcode# => kode barcode
    * @attribute #jk# => jenis kelamin pasien
    * @attribute #tgl_lahir# => tgl lahir pasien
    * @attribute #umur# => umur pasien
    * @attribute #jam_kunjungan# => jam kunjungan
    * @attribute #unit# => unit
    * @attribute #no_pendaftaran# => No Pendaftaran
    * @attribute #layout_print# => layout print tracer

    **/
    public function actionPrintStatusPasien()
    {
        try{
            $request = Yii::$app->request;
            $jenis = $request->post('param', null);
            $is_from = $request->post('is_from', null);
            $ktp = '-';
            $pasien_id = $request->post('pasien_id',null);
            $pendaftaran_id = $request->post('pendaftaran_id',null);
            $pendaftaranol_id  = $request->post('pendaftaranol_id', null);

            $pendaftaran_id = !is_null( $pendaftaran_id) ? explode(",", $pendaftaran_id) : []; // set jadi array
            $pasien_id = !is_null($pasien_id) ? explode(",", $pasien_id) : []; // set jadi array
            $pendaftaranol_id = !is_null($pendaftaranol_id) ? explode(",", $pendaftaranol_id) : []; // set jadi array

            if(!$pasien_id && $jenis != 'reservasi'){
                throw new \yii\base\ErrorException("ID Pasien Tidak Ditemukan", 500);
            }
            if(!$pendaftaran_id && $jenis != 'reservasi'){
                throw new \yii\base\ErrorException("ID Pendaftaran Tidak Ditemukan", 500);
            }

            if ( !$pendaftaranol_id && $jenis == 'reservasi' ) {
                throw new \yii\base\ErrorException("PendaftaranOnline ID Tidak Ditemukan", 500);
            }

            if ($jenis != 'ranap' && $jenis != 'penunjang' && $jenis != 'reservasi' ) {
                $kunjungans = (new InfKunjunganRsTracerView)->find();
            } else if ($jenis == 'penunjang') {
                $kunjungans = (new InfPasienPenunjang)->find();
            } else if ($jenis == 'ranap'){
                $kunjungans = (new InfoKunjunganRiView)->find();
            } else if ($jenis == 'reservasi') {
                $kunjungans = (new InfoPendaftaranOnlineView)->find()->select([
                    'pasien_id',
                    'pendaftaranol_id as pendaftaran_id',
                    'no_pendaftaranol as no_pendaftaran',
                    'COALESCE(
                        tgl_didaftarkan :: text,
                        CONCAT(
                            to_char( tgl_kunjungan,\'YYYY-MM-DD\' ), \' \',
                            split_part(split_part(jam_kunjungan, \' - \', 1), \'-\', 1)
                        ) :: text
                    ) AS tgl_pendaftaran',
                    'jeniskelamin',
                    'jk',
                    'COALESCE(nama_depan, namadepan_ol) as namadepan',
                    'COALESCE(nama_pasien, nama_pasien_ol) as nama_pasien',
                    'COALESCE(tanggal_lahir, tanggal_lahir_ol) as tanggal_lahir',
                    'no_rekam_medik',
                    'nama_pegawai',
                    'ruangan_nama',
                    'no_antrian',
                    'status_pasien',
                    'carabayar_nama',
                    'no_identitas_pasien_ol',
                    'is_cetaktracer',
                ]);
            } else {
                $kunjungans = (new InfKunjunganRsTracerView)->find();
            }

            if($is_from == 'rekam_medik') {
                $defaultParams = [
                    'pendaftaran_id'=>$pendaftaran_id
                ];
            } else {
                $defaultParams = [
                    'pasien_id'=>$pasien_id,
                    'pendaftaran_id'=>$pendaftaran_id
                ];
            }
            if ( !empty($pendaftaranol_id) && $jenis == 'reservasi' ) {
                $defaultParams = [
                    'pendaftaranol_id' => $pendaftaranol_id
                ];
            }

            $kunjungans->where($defaultParams);

            if($jenis == 'penunjang'){
                $kunjungans = $kunjungans->orderBy('tglmasukpenunjang DESC')->all();
            } else if($jenis == 'igd') {
                $kunjungans = $kunjungans->orderBy('tgl_pendaftaran ASC')->all();
            } else{
                $kunjungans = $kunjungans->orderBy('tgl_pendaftaran DESC')->all();
            }

            if(!$kunjungans){
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }

            $data_print = [];

            $this->updateTracer();

            foreach ($kunjungans as $key => $kunjungan) {
                $barcodeGen = new BarcodeGeneratorPNG;
                $barcode_value = isset($kunjungan->no_rekam_medik) ? base64_encode($barcodeGen->getBarcode($kunjungan->no_rekam_medik, $barcodeGen::TYPE_CODE_128)) : '';
                $barcode = '<img style="height: 20px; margin-left: 2px" src="data:image/png;base64,' . $barcode_value . '">';

                if($jenis == "ranap"){
                    $strtotime_tanggal_kunjungan = strtotime(isset($kunjungan->tgl_admisi)?$kunjungan->tgl_admisi:'');
                    // $tanggal_pendaftaran_convert = DocoHelpers::getTanggalIndonesia(date('Y-m-d', $strtotime_tanggal_kunjungan));
                    $tanggal_pendaftaran_convert = DocoHelpers::convDateTime($kunjungan->tgl_admisi, true, false);
                    $jamKunjungan = date('H:i:s',$strtotime_tanggal_kunjungan);
                    $tanggal_pendaftaran_convert = is_array($tanggal_pendaftaran_convert)
                                                    ? date('d',$strtotime_tanggal_kunjungan) ." ". $tanggal_pendaftaran_convert['bulan']. " " .$tanggal_pendaftaran_convert['tahun'] ." ".date('H:i:s',$strtotime_tanggal_kunjungan)
                                                    : $tanggal_pendaftaran_convert;
                }else if($jenis == "penunjang"){
                    $strtotime_tanggal_kunjungan = strtotime(isset($kunjungan->tglmasukpenunjang    )?$kunjungan->tglmasukpenunjang :'');
                    // $tanggal_pendaftaran_convert = DocoHelpers::getTanggalIndonesia(date('Y-m-d', $strtotime_tanggal_kunjungan));
                    $tanggal_pendaftaran_convert = DocoHelpers::convDateTime($kunjungan->tglmasukpenunjang, true, false);
                    $jamKunjungan = date('H:i:s',$strtotime_tanggal_kunjungan);
                    $tanggal_pendaftaran_convert = is_array($tanggal_pendaftaran_convert)
                                                    ? date('d',$strtotime_tanggal_kunjungan) ." ". $tanggal_pendaftaran_convert['bulan']. " " .$tanggal_pendaftaran_convert['tahun'] ." ".date('H:i:s',$strtotime_tanggal_kunjungan)
                                                    : $tanggal_pendaftaran_convert;
                }else{
                    $strtotime_tanggal_kunjungan = strtotime(isset($kunjungan->tgl_pendaftaran)?$kunjungan->tgl_pendaftaran:'');
                    // $tanggal_pendaftaran_convert = DocoHelpers::getTanggalIndonesia(date('Y-m-d', $strtotime_tanggal_kunjungan));
                    $tanggal_pendaftaran_convert = DocoHelpers::convDateTime($kunjungan->tgl_pendaftaran, true, false);
                    $jamKunjungan = date('H:i:s',$strtotime_tanggal_kunjungan);
                    $tanggal_pendaftaran_convert = is_array($tanggal_pendaftaran_convert)
                                                    ? date('d',$strtotime_tanggal_kunjungan) ." ". $tanggal_pendaftaran_convert['bulan']. " " .$tanggal_pendaftaran_convert['tahun'] ." ".date('H:i:s',$strtotime_tanggal_kunjungan)
                                                    : $tanggal_pendaftaran_convert;
                }
                // Override 'mcu' nomor_antrian value, must be null
                if($jenis == 'mcu'){
                    $kunjungan->no_antrian = null;
                }
                $listJk = $this->getLookupByType(self::JK)->all();

                $jk = isset($kunjungan->jenis_kelamin) ? $kunjungan->jenis_kelamin : '';
                if ( $jenis == 'reservasi') {
                    $jk = isset($kunjungan->jk) ? $kunjungan->jk : '';
                }

                $name =  isset($kunjungan->nama_pasien
                )?$kunjungan->nama_pasien:'';
                $displayName = DocoHelpers::cutSentence($name);

                $dpjp = isset($kunjungan->nama_pegawai
                )?$kunjungan->nama_pegawai:'';
                $displayDpjp = $dpjp;

                $jwt = Yii::$app->jwt->user;
                $loginpemakai_id = !empty($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : '1';

                if (isset($kunjungan->status_pasien)) {
                    $statusPasien = Lookup::findOne($kunjungan->status_pasien);
                }

                if ($loginpemakai_id) {
                    $loginPemakai = LoginPemakaiView::find()->where(['loginpemakai_id' => $loginpemakai_id])->one();
                }

                if ( isset($kunjungan->no_identitas_pasien_ol) ) {
                    $ktp = $kunjungan->no_identitas_pasien_ol;
                }
                if (isset($kunjungan->additional_pasien) && !empty($kunjungan->additional_pasien)) {
                    $additionalPasien = json_decode($kunjungan->additional_pasien);

                    if (!empty($additionalPasien)) {
                        foreach ($additionalPasien as $key => $value) {
                            if (isset($value->jenisidentitas) && $value->jenisidentitas == DocoConstants::CONS_ID_KTP) {
                                $ktp = $value->no_identitas_pasien;
                            }
                        }
                    }
                }

                $umur = '';
                if (isset($kunjungan->umur) && $kunjungan->umur != '') {
                    $kunjungan->umur = str_replace("Tahun", "thn,", $kunjungan->umur);
                    $kunjungan->umur = str_replace("Bulan", "bln,", $kunjungan->umur);
                    $kunjungan->umur = str_replace("Hari", "hr", $kunjungan->umur);
                } else {
                    $umur = (new DocoHelpers)->getUmur($kunjungan->tanggal_lahir);
                }

                array_push($data_print, [
                    'tgl_pendaftaran' => $tanggal_pendaftaran_convert,
                    'no_rekam_medik' => isset($kunjungan->no_rekam_medik)?$kunjungan->no_rekam_medik:'-',
                    'nama_pasien' => $displayName,
                    'cara_bayar' => isset($kunjungan->carabayar_nama)?$kunjungan->carabayar_nama:'-',
                    'penjamin' => isset($kunjungan->penjamin_nama)?$kunjungan->penjamin_nama:'-',
                    'nama_dokter'=> $displayDpjp,
                    'nama_ruangan'=>isset($kunjungan->ruangan_nama) ? $kunjungan->ruangan_nama : '',
                    'nama_depan' => isset($kunjungan->namadepan) ? (($jenis == "ranap") ? $kunjungan->nama_depan : $kunjungan->namadepan) : '',
                    'barcode' => $barcode,
                    'jk' => $jk,
                    'tgl_lahir' => isset($kunjungan->tanggal_lahir) ? date('d-M-Y', strtotime($kunjungan->tanggal_lahir)) : '-' ,
                    'umur' => isset($kunjungan->umur) ? $kunjungan->umur : ( $umur != '' ? $umur : '-'),
                    'no_antrian' => isset($kunjungan->no_antrian)?$kunjungan->no_antrian:'-',
                    'jam_kunjungan' => isset($jamKunjungan) ? $jamKunjungan : '',
                    'unit' => isset($kunjungan->ruangan_nama) ? DocoHelpers::cutSentence($kunjungan->ruangan_nama, 15) : '-',
                    'status_pasien' => isset($statusPasien->lookup_name) ? $statusPasien->lookup_name : '-',
                    'loginpemanai_nama' => isset($loginPemakai->nama_pegawai) ? DocoHelpers::cutSentence($loginPemakai->nama_pegawai) : '-',
                    'nik' => $ktp,
                    'no_pendaftaran' => isset($kunjungan->no_pendaftaran) ? $kunjungan->no_pendaftaran : '-',
                ]);
            }

            // End of Line
            $print = new DocoPrint();
            /* di uncomment karena digunakan di site Kramat */
            $print->attributes = [
                '#layout_print#' => $this->renderPartial('print_tracer', [
                        'datas' => $data_print
                    ]),
                '#tgl_pendaftaran#' => $tanggal_pendaftaran_convert,
                '#no_rekam_medik#' => isset($kunjungan->no_rekam_medik)?$kunjungan->no_rekam_medik:'-',
                '#nama_pasien#' => $displayName,
                '#cara_bayar#' => isset($kunjungan->carabayar_nama)?$kunjungan->carabayar_nama:'-',
                '#penjamin#' => isset($kunjungan->penjamin_nama)?$kunjungan->penjamin_nama:'-',
                '#nama_dokter#'=> $displayDpjp,
                '#nama_ruangan#'=>isset($kunjungan->ruangan_nama) ? $kunjungan->ruangan_nama : '',
                '#nama_depan#' => isset($kunjungan->namadepan) ? (($jenis == "ranap") ? $kunjungan->nama_depan : $kunjungan->namadepan) : '',
                '#barcode#' => $barcode,
                '#jk#' => $jk,
                '#tgl_lahir#' => isset($kunjungan->tanggal_lahir) ? date('d-M-Y', strtotime($kunjungan->tanggal_lahir)) : '-' ,
                '#umur#' => isset($kunjungan->umur) ? $kunjungan->umur : ( $umur != '' ? $umur : '-'),
                '#no_antrian#' => isset($kunjungan->no_antrian)?$kunjungan->no_antrian:'-',
                '#jam_kunjungan#' => isset($jamKunjungan) ? $jamKunjungan : '',
                '#unit#' => isset($kunjungan->ruangan_nama) ? DocoHelpers::cutSentence($kunjungan->ruangan_nama, 15) : '-',
                '#status_pasien#' => isset($statusPasien->lookup_name) ? $statusPasien->lookup_name : '-',
                '#loginpemanai_nama#' => isset($loginPemakai->nama_pegawai) ? DocoHelpers::cutSentence($loginPemakai->nama_pegawai) : '-',
                '#nik#' => $ktp,
                '#no_pendaftaran#' => isset($kunjungan->no_pendaftaran) ? $kunjungan->no_pendaftaran : '-',
            ];
            $print->Output();
        }catch (\Exception $e){
            return ['Error'=>$e->getMessage()];
        }
    }

    public function updateTracer()
    {

        return Yii::$app->docoPlugin->execute('update_status_tracer');
    }

    public function actionPrintKartuPasien()
    {
        return Yii::$app->docoPlugin->execute('cetak_kartu_pasien');
    }

    /**
    * @controller actionPrintIdentitasPasien
    * @attribute #tgl_pendaftaran# => tanggal pendaftaran
    * @attribute #jam_pendaftaran# => jam pendaftaran
    * @attribute #no_pendaftaran# => no_pendaftaran
    * @attribute #no_rekam_medik# => no rekam medik
    * @attribute #namadepan# => nama depan pasien
    * @attribute #nama_pasien# => nama pasien
    * @attribute #jenis_kelamin# => jk pasien
    * @attribute #alamat_pasien# => alamat pasien
    * @attribute #kelurahan_nama# => kelurahan pasien
    * @attribute #kecamatan_nama# => kecamatan pasien
    * @attribute #kode_pos# => kode pos pasien
    * @attribute #no_telepon_pasien# => no telepon pasien
    * @attribute #pekerjaan_nama# => pekerjaan pasien
    * @attribute #tahun_lahir# => tahun lahir
    * @attribute #bulan_lahir# => bulan lahir
    * @attribute #tanggal_lahir# => tanggal lahir
    * @attribute #tahun_umur# => tahun umur
    * @attribute #bulan_umur# => bulan umur
    * @attribute #tanggal_umur# => hari umur
    * @attribute #status_perkawinan# => status perkawinan pasien
    * @attribute #agama# => agama
    * @attribute #kabupaten_nama# => kabupaten pasien
    * @attribute #suku_nama# => suku
    * @attribute #warga_negara# => warga negara
    * @attribute #golongan_darah# => golongan darah
    * @attribute #ruangan_nama# => nama ruangan
    * @attribute #kelaspelayanan_nama# => kelas pelayanan
    * @attribute #nama_pegawai# => nama dokter
    * @attribute #pj_namadepan_nama# => nama depan pj
    * @attribute #penanggungjawab_nama# => nama pj
    * @attribute #penanggungjawab_alamat# => alamat pj
    * @attribute #penanggungjawab_notelp# => no telepon pj
    * @attribute #pj_pekerjaan_nama# => pekerjaan pj
    * @attribute #pj_kelurahan_nama# => kelurahan pj
    * @attribute #pj_kecamatan_nama# => kecamatan pj
    * @attribute #keluarga_namadepan_nama# => nama depan keluarga
    * @attribute #keluarga_nama# => nama keluarga
    * @attribute #keluarga_alamat# => alamat keluarga
    * @attribute #keluarga_no_telepon# => no telepon keluarga
    * @attribute #keluarga_pekerjaan_nama# => pekerjaan keluarga
    * @attribute #keluarga_kelurahan_nama# => kelurahan keluarga
    * @attribute #keluarga_kecamatan_nama# => kecamatan keluarga
    **/
    public function actionPrintIdentitasPasien()
    {
        return Yii::$app->docoPlugin->execute('print_identitas_pasien');

    }

    /**
    * @controller actionPrintSuratKematian
    * @attribute #no_rekam_medik# => no rekam medik
    * @attribute #namadepan# => nama depan pasien
    * @attribute #nama_pasien# => nama pasien
    * @attribute #tempat_lahir# => tempat lahir pasien
    * @attribute #tanggal_lahir# => tanggal lahir pasien
    * @attribute #alamat_depan# => alamat depan
    * @attribute #alamat_pasien# => alamat pasien
    * @attribute #tahun_umur# => tahun umur
    * @attribute #no_telepon# => no telepon pasien
    * @attribute #keluarga_nama# => nama keluarga
    * @attribute #keluarga_namadepan# => nama depan keluarga
    * @attribute #keluarga_alamat# => alamat keluarga
    * @attribute #keluarga_alamatdepan# => alamat depan keluarga
    **/
    public function actionPrintSuratKematian()
    {
        try{
            $request = Yii::$app->request;
            $jenis = $request->get('param', null);
            $model = new SyPasienView;
            $pasien_id = $request->get('pasien_id',null);
            if(!$pasien_id){
                throw new \yii\base\ErrorException("ID Pasien Tidak Ditemukan", 500);
            }
            $pasien = $model::find()->where(['pasien_id' => $pasien_id])->one();

            if(!$pasien){
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }

            $keluarga_nama = $keluarga_namadepan = $keluarga_alamat = $keluarga_alamatdepan = '';
            $modelKp = new SyKeluargaPasienView;
            $keluargaPasien = $modelKp::find()->where(['pasien_id' => $pasien_id])->one();

            if(!empty($keluargaPasien)){
                $keluarga_nama = isset($keluargaPasien->keluarga_nama)?$keluargaPasien->keluarga_nama:'';
                $keluarga_namadepan = isset($keluargaPasien->keluarga_namadepan)?$keluargaPasien->keluarga_namadepan:'';
                $keluarga_alamat = isset($keluargaPasien->keluarga_alamat)?$keluargaPasien->keluarga_alamat:'';
                $keluarga_alamatdepan = isset($keluargaPasien->alamat_depan)?$keluargaPasien->alamat_depan:'';
            }

            $tanggal_lahir = strtotime(isset($pasien->tanggal_lahir)?$pasien->tanggal_lahir:'');
            $today = date('Y-m-d');
            $diff = date_diff(date_create(date('Y-m-d', $tanggal_lahir)), date_create($today));
            $umur_th = (strlen($diff->y) > 1)?$diff->y:'0'.$diff->y;

            $print = new DocoPrint();
            $print->attributes = [
                '#namadepan#' => isset($pasien->namadepan
                )?$pasien->namadepan.' ':'',
                '#nama_pasien#' => $pasien->nama_pasien,
                '#tempat_lahir#' => isset($pasien->tempat_lahir
                )?$pasien->tempat_lahir:'',
                '#alamat_depan#' => isset($pasien->alamat_depan
                )?$pasien->alamat_depan:'',
                '#alamat_pasien#' => isset($pasien->alamat_pasien
                )?$pasien->alamat_pasien:'',
                '#tahun_umur#' => $umur_th,
                '#no_telepon#' => isset($pasien->no_telepon_pasien
                )?$pasien->no_telepon_pasien:'',
                '#no_rekam_medik#' => isset($pasien->no_rekam_medik
                )?$pasien->no_rekam_medik:'',
                '#tanggal_lahir#' => isset($pasien->tanggal_lahir
                )?date('d-m-Y', strtotime($pasien->tanggal_lahir)):'',
                '#keluarga_nama#' => $keluarga_nama,
                '#keluarga_namadepan#' => $keluarga_namadepan,
                '#keluarga_alamat#' => $keluarga_alamat,
                '#keluarga_alamatdepan#' => $keluarga_alamatdepan,
            ];
            $print->Output();
        }catch (Exception $e){
            return ['Error'=>$e->getMessage()];
        }
    }

    /**
    * @controller actionPrintGelangPasien
    * @attribute #no_rekam_medik# => no rekam medik
    * @attribute #nama_pasien# => nama pasien
    * @attribute #jenis_kelamin# => jenis kelamin pasien
    * @attribute #tanggal_lahir# => tanggal lahir
    * @attribute #nama_depan# => gelar / nama depan pasien
    * @attribute #penjamin_nama# => nama penjamin
    * @attribute #umur# => umur pasien
    * @attribute #qrcode# => qrcode
    * @attribute #no_ktp# => No KTP
    **/
    public function actionPrintGelangPasien()
    {
        try{
            $model = new InfKunjunganRsView;
            $konfig = $this->getKonfigSystem()->asArray()->one();
            $ktp = '-';

            $request = Yii::$app->request;
            $pasien_id = $request->post('pasien_id',null);
            if(!$pasien_id){
                throw new \yii\base\ErrorException("ID Pasien Tidak Ditemukan", 500);
            }
            $pendaftaran_id = $request->post('pendaftaran_id',null);
            if(!$pendaftaran_id){
                throw new \yii\base\ErrorException("ID Pendaftaran Tidak Ditemukan", 500);
            }
            $kunjungan = $model::find()->where(['pasien_id'=>$pasien_id,'pendaftaran_id'=>$pendaftaran_id])->orderBy('tgl_pendaftaran DESC')->one();
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

            $nama_pasien = DocoHelpers::cutSentence(isset($kunjungan->nama_pasien
            )? $kunjungan->nama_pasien:'', 20);

            if (isset($kunjungan->additional_pasien) && !empty($kunjungan->additional_pasien)) {
                $additionalPasien = json_decode($kunjungan->additional_pasien);

                if (!empty($additionalPasien)) {
                    foreach ($additionalPasien as $key => $value) {
                        if (isset($value->jenisidentitas) && $value->jenisidentitas == DocoConstants::CONS_ID_KTP) {
                            $ktp = $value->no_identitas_pasien;
                        }
                    }
                }
            }

            $umur = isset($kunjungan->umur) ? $kunjungan->umur : '';
            if($umur && $konfig['jenis_label_pendaftaran'] == self::AD) {
                $tmpUmur = explode(" ", $umur);
                $tmpUmur[1] = 'Thn';
                $tmpUmur[3] = 'Bln';
                $tmpUmur[5] = 'Hr';
                $umur = implode(" ", $tmpUmur);
            }
            $print->attributes = [
                '#no_rekam_medik#' => $no_rekam_medik,
                '#nama_pasien#' => $nama_pasien,
                '#tanggal_lahir#' => isset($kunjungan->tanggal_lahir)
                    ?date('d M Y', strtotime($kunjungan->tanggal_lahir))
                    :'',
                '#jenis_kelamin#' => isset($kunjungan->jenis_kelamin)
                    ? $kunjungan->jenis_kelamin
                    :'',
                '#nama_depan#' => isset($kunjungan->namadepan) ? $kunjungan->namadepan : '',
                '#penjamin_nama#' => isset($kunjungan->penjamin_nama) ? $kunjungan->penjamin_nama : '',
                '#umur#' => $umur,
                '#qrcode#' => $qrcode,
                '#no_ktp#' => $ktp,
            ];
            $print->Output();
        }catch (\Exception $e){
            return ['Error'=>$e->getMessage()];
        }
    }

    /**
    * @controller actionPrintAsesmen
    * @attribute #no_rekam_medik# => no rekam medik
    * @attribute #nik# => nik
    * @attribute #tanggal_lahir# => tangal lahir pasien
    * @attribute #umur# => umur pasien
    * @attribute #namadepan# => nama depan pasien
    * @attribute #nama_pasien# => nama pasien
    * @attribute #jenis_kelamin# => jenis kelamin pasien
    * @attribute #status_perkawinan# => status perkawinan pasien
    * @attribute #alamat_pasien# => alamat pasien
    * @attribute #agama# => agama
    * @attribute #pendidikan_nama# => pendidikan pasien
    * @attribute #pekerjaan_nama# => pekerjaan pasien
    * @attribute #suku_nama# => suku
    * @attribute #warga_negara# => warga negara
    * @attribute #bahas_sehari# => bahasa sehari hari
    * @attribute #pj_namadepan# => nama depan pj
    * @attribute #penanggungjawab_nama# => nama pj
    * @attribute #penanggungjawab_alamat# => alamat pj
    * @attribute #no_telepon_pasien# => no telepon pasien
    **/
    public function actionPrintAsesmen()
    {
        try{
            $request = Yii::$app->request;
            $jenis = $request->get('param', null);
            if ($jenis != 'ranap' && $jenis != 'penunjang') {
                $model = new InfKunjunganRsView;
            } else if ($jenis == 'penunjang') {
                $model = new InfPasienPenunjang;
            } else if ($jenis == 'ranap'){
                $model = new InfoKunjunganRiView;
            } else {
                $model = new InfKunjunganRsView;
            }
            $pasien_id = $request->get('pasien_id',null);
            if(!$pasien_id){
                throw new \yii\base\ErrorException("ID Pasien Tidak Ditemukan", 500);
            }
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            if(!$pendaftaran_id){
                throw new \yii\base\ErrorException("ID Pendaftaran Tidak Ditemukan", 500);
            }
            $kunjungan = $model::find()->where(['pasien_id'=>$pasien_id,'pendaftaran_id'=>$pendaftaran_id]);

            if($jenis == 'penunjang'){
                $kunjungan = $kunjungan->orderBy('tglmasukpenunjang DESC')->one();
            } else if($jenis == 'igd') {
                $kunjungan = $kunjungan->orderBy('tgl_pendaftaran ASC')->one();
            } else{
                $kunjungan = $kunjungan->orderBy('tgl_pendaftaran DESC')->one();
            }
            if(!$kunjungan){
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }

            $modelPasien = new PasienV;
            $modelPasien = $modelPasien::find()->where(['pasien_id'=>$pasien_id])->one();

            $nik = ($modelPasien->jenisidentitas == 94)? $modelPasien->no_identitas_pasien : '';
            if (!empty($modelPasien->additional_pasien)) {
                $additionalPasien = json_decode($modelPasien->additional_pasien);

                foreach ($additionalPasien as $key => $value) {
                    if (isset($value->no_identitas_pasien)) {
                        $nik = $value->no_identitas_pasien;
                    }
                }
            }
            $tanggal_lahir = strtotime(isset($kunjungan->tanggal_lahir)?$kunjungan->tanggal_lahir:'');
            $today = date('Y-m-d');
            $diff = date_diff(date_create(date('Y-m-d', $tanggal_lahir)), date_create($today));
            $umur_th = (strlen($diff->y) > 1)?$diff->y:'0'.$diff->y;
            $umur_bl = (strlen($diff->m) > 1)?$diff->m:'0'.$diff->m;
            $umur_hr = (strlen($diff->d) > 1)?$diff->d:'0'.$diff->d;
            $umur = $umur_th."TH ".$umur_bl."BL ".$umur_hr."HR";
            $tanggal_lahir_convert = date('d-m-Y', $tanggal_lahir);

            $print = new DocoPrint();
            $print->attributes = [
                '#no_rekam_medik#' => isset($kunjungan->no_rekam_medik
                )?$kunjungan->no_rekam_medik:'',
                '#nik#' => $nik,
                '#tanggal_lahir#' => $tanggal_lahir_convert,
                '#umur#' => $umur,
                '#namadepan#' => isset($kunjungan->namadepan
                )?$kunjungan->namadepan:'',
                '#nama_pasien#' => isset($kunjungan->nama_pasien
                )?$kunjungan->nama_pasien:'',
                '#jenis_kelamin#' => isset($kunjungan->jenis_kelamin
                )?substr($kunjungan->jenis_kelamin, 0, 1):'',
                '#status_perkawinan#' => isset($modelPasien->status_perkawinan
                )?$modelPasien->status_perkawinan:'',
                '#alamat_pasien#' => isset($kunjungan->alamat_pasien
                )?$kunjungan->alamat_pasien:'',
                '#agama#' => isset($modelPasien->agama_pasien
                )?$modelPasien->agama_pasien:'',
                '#pendidikan_nama#' => isset($kunjungan->pendidikan_nama
                )?$kunjungan->pendidikan_nama.' / ':'',
                '#pekerjaan_nama#' => isset($kunjungan->pekerjaan_nama
                )?$kunjungan->pekerjaan_nama:'',
                '#suku_nama#' => isset($kunjungan->suku_nama
                )?$kunjungan->suku_nama.' / ':'',
                '#warga_negara#' => isset($modelPasien->warganegara
                )?$modelPasien->warganegara:'',
                '#bahasa_sehari#' => isset($kunjungan->bahasa_sehari_nama
                )?$kunjungan->bahasa_sehari_nama:'',
                '#pj_namadepan#' => isset($kunjungan->pj_namadepan_nama
                )?$kunjungan->pj_namadepan_nama:'',
                '#penanggungjawab_nama#' => isset($kunjungan->penanggungjawab_nama
                )?$kunjungan->penanggungjawab_nama:'',
                '#penanggungjawab_alamat#' => isset($kunjungan->penanggungjawab_alamat
                )?$kunjungan->penanggungjawab_alamat:'',
                '#no_telepon_pasien#' => isset($kunjungan->no_telepon_pasien
                )?$kunjungan->no_telepon_pasien:'',
            ];
            $print->Output();
        }catch (Exception $e){
            return ['Error'=>$e->getMessage()];
        }
    }

    /**
    * @controller actionPrintTriase
    * @attribute #no_rekam_medik# => no rekam medik
    * @attribute #nik# => nik
    * @attribute #tanggal_lahir# => tangal lahir pasien
    * @attribute #umur# => umur pasien
    * @attribute #namadepan# => nama depan pasien
    * @attribute #nama_pasien# => nama pasien
    * @attribute #jenis_kelamin# => jenis kelamin pasien
    * @attribute #status_perkawinan# => status perkawinan pasien
    * @attribute #alamat_pasien# => alamat pasien
    * @attribute #agama# => agama
    * @attribute #pendidikan_nama# => pendidikan pasien
    * @attribute #pekerjaan_nama# => pekerjaan pasien
    * @attribute #suku_nama# => suku
    * @attribute #warga_negara# => warga negara
    * @attribute #bahasa_sehari# => bahasa sehari hari
    * @attribute #pj_namadepan# => nama depan pj
    * @attribute #penanggungjawab_nama# => nama pj
    * @attribute #penanggungjawab_alamat# => alamat pj
    * @attribute #pj_pekerjaan_nama# => pekerjaan nama
    * @attribute #no_telepon_pasien# => no telepon pasien
    * @attribute #tanggal_pendaftaran# => Tanggal Pendaftaran
    * @attribute #jam_pendaftaran# => Jam Pendaftaran
    **/
    public function actionPrintTriase()
    {
        try{
            $request = Yii::$app->request;
            $jenis = $request->get('param', null);
            if ($jenis != 'ranap' && $jenis != 'penunjang') {
                $model = new InfKunjunganRsView;
            } else if ($jenis == 'penunjang') {
                $model = new InfPasienPenunjang;
            } else if ($jenis == 'ranap'){
                $model = new InfoKunjunganRiView;
            } else {
                $model = new InfKunjunganRsView;
            }
            $pasien_id = $request->get('pasien_id',null);
            if(!$pasien_id){
                throw new \yii\base\ErrorException("ID Pasien Tidak Ditemukan", 500);
            }
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            if(!$pendaftaran_id){
                throw new \yii\base\ErrorException("ID Pendaftaran Tidak Ditemukan", 500);
            }
            $kunjungan = $model::find()->where(['pasien_id'=>$pasien_id,'pendaftaran_id'=>$pendaftaran_id]);

            if($jenis == 'penunjang'){
                $kunjungan = $kunjungan->orderBy('tglmasukpenunjang DESC')->one();
            } else if($jenis == 'igd') {
                $kunjungan = $kunjungan->orderBy('tgl_pendaftaran ASC')->one();
            } else{
                $kunjungan = $kunjungan->orderBy('tgl_pendaftaran DESC')->one();
            }
            if(!$kunjungan){
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }

            $modelPasien = new PasienV;
            $modelPasien = $modelPasien::find()->where(['pasien_id'=>$pasien_id])->one();

            $nik = ($modelPasien->jenisidentitas == 94)? $modelPasien->no_identitas_pasien : '';
            if (!empty($modelPasien->additional_pasien)) {
                $additionalPasien = json_decode($modelPasien->additional_pasien);

                foreach ($additionalPasien as $key => $value) {
                    if (isset($value->no_identitas_pasien)) {
                        $nik = $value->no_identitas_pasien;
                    }
                }
            }
            $tanggal_lahir = strtotime(isset($kunjungan->tanggal_lahir)?$kunjungan->tanggal_lahir:'');
            $today = date('Y-m-d');
            $diff = date_diff(date_create(date('Y-m-d', $tanggal_lahir)), date_create($today));
            $umur_th = (strlen($diff->y) > 1)?$diff->y:'0'.$diff->y;
            $umur_bl = (strlen($diff->m) > 1)?$diff->m:'0'.$diff->m;
            $umur_hr = (strlen($diff->d) > 1)?$diff->d:'0'.$diff->d;
            $umur = $umur_th."TH ".$umur_bl."BL ".$umur_hr."HR";
            $tanggal_lahir_convert = date('d-m-Y', $tanggal_lahir);

            $print = new DocoPrint();
            $print->attributes = [
                '#no_rekam_medik#' => isset($kunjungan->no_rekam_medik
                )?$kunjungan->no_rekam_medik:'',
                '#nik#' => $nik,
                '#tanggal_lahir#' => $tanggal_lahir_convert,
                '#umur#' => $umur,
                '#namadepan#' => isset($kunjungan->namadepan
                )?$kunjungan->namadepan:'',
                '#nama_pasien#' => isset($kunjungan->nama_pasien
                )?$kunjungan->nama_pasien:'',
                '#jenis_kelamin#' => isset($kunjungan->jenis_kelamin
                )?substr($kunjungan->jenis_kelamin, 0, 1):'',
                '#status_perkawinan#' => isset($modelPasien->status_perkawinan
                )?$modelPasien->status_perkawinan:'',
                '#alamat_pasien#' => isset($kunjungan->alamat_pasien
                )?$kunjungan->alamat_pasien:'',
                '#agama#' => isset($modelPasien->agama_pasien
                )?$modelPasien->agama_pasien:'',
                '#pendidikan_nama#' => isset($kunjungan->pendidikan_nama
                )?$kunjungan->pendidikan_nama.' / ':'',
                '#pekerjaan_nama#' => isset($kunjungan->pekerjaan_nama
                )?$kunjungan->pekerjaan_nama:'',
                '#suku_nama#' => isset($kunjungan->suku_nama
                )?$kunjungan->suku_nama.' / ':'',
                '#warga_negara#' => isset($modelPasien->warganegara
                )?$modelPasien->warganegara:'',
                '#bahasa_sehari#' => isset($kunjungan->bahasa_sehari_nama
                )?$kunjungan->bahasa_sehari_nama:'',
                '#pj_namadepan#' => isset($kunjungan->pj_namadepan_nama
                )?$kunjungan->pj_namadepan_nama:'',
                '#penanggungjawab_nama#' => isset($kunjungan->penanggungjawab_nama
                )?$kunjungan->penanggungjawab_nama:'',
                '#penanggungjawab_alamat#' => isset($kunjungan->penanggungjawab_alamat
                )?$kunjungan->penanggungjawab_alamat:'',
                '#pj_pekerjaan_nama#' => isset($kunjungan->pj_pekerjaan_nama
                )?$kunjungan->pj_pekerjaan_nama:'',
                '#no_telepon_pasien#' => isset($kunjungan->no_telepon_pasien
                )?$kunjungan->no_telepon_pasien:'',
                '#tanggal_pendaftaran#'=> isset($kunjungan->tgl_pendaftaran) ?
                        date('d-M-Y',strtotime($kunjungan->tgl_pendaftaran)):'',
                '#jam_pendaftaran#'=> isset($kunjungan->tgl_pendaftaran) ?
                        date('H:i:s',strtotime($kunjungan->tgl_pendaftaran)):'',
            ];
            $print->Output();
        }catch (Exception $e){
            return ['Error'=>$e->getMessage()];
        }
    }

    /**
    * @controller actionPrintGelangPasienAnak
    * @attribute #no_rekam_medik# => no rekam medik
    * @attribute #nama_pasien# => nama pasien
    * @attribute #jenis_kelamin# => jenis kelamin pasien
    * @attribute #tanggal_lahir# => tanggal lahir
    * @attribute #nama_depan# => gelar / nama depan pasien
    * @attribute #penjamin_nama# => nama penjamin
    * @attribute #umur# => umur pasien
    * @attribute #qrcode# => qrcode
    **/
    public function actionPrintGelangPasienAnak()
    {
        return Yii::$app->docoPlugin->execute('print_gelang_anak');
    }

    /**
    * @controller actionPrintLabelPasien (dipake gelang di kramat)
    * @attribute #no_rekam_medik# => no rekam medik
    * @attribute #nama_depan# => gelar / nama depan pasien
    * @attribute #nama_pasien# => nama pasien
    * @attribute #jk# => jenis kelamin pasien
    * @attribute #tanggal_lahir# => tanggal lahir
    * @attribute #barcode# => barcode
    **/
    public function actionPrintLabelPasien()
    {
        try{
            $model = new InfKunjunganRsView;

            $request = Yii::$app->request;
            $pasien_id = $request->post('pasien_id',null);
            if(!$pasien_id){
                throw new \yii\base\ErrorException("ID Pasien Tidak Ditemukan", 500);
            }
            $pendaftaran_id = $request->post('pendaftaran_id',null);
            if(!$pendaftaran_id){
                throw new \yii\base\ErrorException("ID Pendaftaran Tidak Ditemukan", 500);
            }
            $kunjungan = $model::find()->where(['pasien_id'=>$pasien_id,'pendaftaran_id'=>$pendaftaran_id])->orderBy('tgl_pendaftaran DESC')->one();
            if(!$kunjungan){
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }
            $jk = $this->getLookupByType(self::JK)->all();
            $displayJk = isset($kunjungan->jeniskelamin) ? $kunjungan->jeniskelamin : "";
            if(!empty($displayJk)) {
                foreach($jk as $k => $v) {
                    if($v['lookup_id'] == $displayJk) {
                        $displayJk = $v['lookup_kode'];
                    }
                }
            }
            $nama_pasien = isset($kunjungan->nama_pasien) ? strtoupper($kunjungan->nama_pasien) : '';
            $nama_pasien = DocoHelpers::cutSentence($nama_pasien, 20);
            $barcodeGen = new BarcodeGeneratorPNG;
            $barcode = '<img style="height: 20px; margin-left: 2px" src="data:image/png;base64,' . base64_encode($barcodeGen->getBarcode($kunjungan->no_rekam_medik, $barcodeGen::TYPE_CODE_128)) . '">';
            $print = new DocoPrint('krmt-gelang');
            $print->attributes = [
                '#nama_depan#' => isset($kunjungan->namadepan) ? strtoupper($kunjungan->namadepan) : '',
                '#nama_pasien#' => $nama_pasien,
                '#no_rekam_medik#' => isset($kunjungan->no_rekam_medik) ? $kunjungan->no_rekam_medik : '',
                '#barcode#' => $barcode,
                '#jk#' => $displayJk,
                '#tgl_lahir#' => isset($kunjungan->tanggal_lahir) ? date('d-m-Y', strtotime($kunjungan->tanggal_lahir)) : '' ,
                '#umur#' => isset($kunjungan->umur) ? $kunjungan->umur : '' ,
            ];
            $print->Output();
        }catch (\Exception $e){
            return ['Error'=>$e->getMessage()];
        }
    }

    /**
    * @controller actionPrintLabelPasienBaru
    * @attribute #data# => data
    **/
    public function actionPrintLabelPasienBaru()
    {
        return Yii::$app->docoPlugin->execute('print_label_pasien');
    }

    /**
    * @controller actionPrintVoucherBerkas

    * @attribute #no_rekam_medik# => no rekam medik
    * @attribute #nama_depan# => gelar / nama depan pasien
    * @attribute #nama_pasien# => nama pasien
    * @attribute #jk# => jenis kelamin pasien
    * @attribute #tgl_lahir# => tgl lahir pasien
    * @attribute #umur# => umur pasien
    * @attribute #nama_pasien_reformated# => nama pasien, reformated, cut sentence
    **/
    public function actionPrintVoucherBerkas()
    {
        try{
            $request = Yii::$app->request;
            $jenis = $request->post('param', null);
            if ($jenis != 'ranap' && $jenis != 'penunjang') {
                $model = new InfKunjunganRsView;
            } else if ($jenis == 'penunjang') {
                $model = new InfPasienPenunjang;
            } else if ($jenis == 'ranap'){
                $model = new InfoKunjunganRiView;
            } else {
                $model = new InfKunjunganRsView;
            }
            $pasien_id = $request->post('pasien_id',null);
            if(!$pasien_id){
                throw new \yii\base\ErrorException("ID Pasien Tidak Ditemukan", 500);
            }
            $pendaftaran_id = $request->post('pendaftaran_id',null);
            if(!$pendaftaran_id){
                throw new \yii\base\ErrorException("ID Pendaftaran Tidak Ditemukan", 500);
            }
            $kunjungan = $model::find()->where(['pasien_id'=>$pasien_id,'pendaftaran_id'=>$pendaftaran_id]);

            if($jenis == 'penunjang'){
                $kunjungan = $kunjungan->orderBy('tglmasukpenunjang DESC')->one();
            } else if($jenis == 'igd') {
                $kunjungan = $kunjungan->orderBy('tgl_pendaftaran ASC')->one();
            } else{
                $kunjungan = $kunjungan->orderBy('tgl_pendaftaran DESC')->one();
            }
            if(!$kunjungan){
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }

            $nama_depan = isset($kunjungan->namadepan) ? (($jenis == "ranap") ? $kunjungan->nama_depan : $kunjungan->namadepan) : '';
            $nama_pasien = isset($kunjungan->nama_pasien) ? $kunjungan->nama_pasien : '';
            $nama_pasien = $nama_depan . $nama_pasien;
            $nama_pasien = DocoHelpers::cutSentence($nama_pasien);

            $barcodeGen = new BarcodeGeneratorPNG;
            $barcode = '<img src="data:image/png;base64,' . base64_encode($barcodeGen->getBarcode($kunjungan->no_rekam_medik, $barcodeGen::TYPE_CODE_128)) . '" width="120px">';

            $print = new DocoPrint();
            $print->attributes = [
                '#no_rekam_medik#' => isset($kunjungan->no_rekam_medik)?$kunjungan->no_rekam_medik:'',
                '#nama_pasien#' => isset($kunjungan->nama_pasien
                )?$kunjungan->nama_pasien:'',
                '#nama_depan#' => isset($kunjungan->namadepan) ? (($jenis == "ranap") ? $kunjungan->nama_depan : $kunjungan->namadepan) : '',
                '#jk#' => isset($kunjungan->jeniskelamin) ? ($kunjungan->jeniskelamin == 15) ? 'Laki-Laki' : 'Perempuan' : '' ,
                '#tgl_lahir#' => isset($kunjungan->tanggal_lahir) ? DocoHelpers::convDateTime($kunjungan->tanggal_lahir . " 00:00:00", false, false) : '' ,
                '#umur#' => isset($kunjungan->umur) ? $kunjungan->umur : '',
                '#nama_pasien_reformated#' => $nama_pasien,
                '#barcode#' => $barcode
            ];
            $print->Output();
        }catch (Exception $e){
            return ['Error'=>$e->getMessage()];
        }
    }

    public function actionGetTemplateLabel()
    {
        $konfig = $this->getKonfigSystem()->asArray()->one();

        return [
            'jenis_template' => $konfig['jenis_label_pendaftaran'],
            'jumlah_template' => $konfig['jumlah_label_pendaftaran'],
        ];
    }

    public function actionGetTemplateKartu()
    {
        $konfig = $this->getKonfigSystem()->asArray()->one();

        return [
            'jenis_template' => $konfig['jenis_kartu_pendaftaran'],
        ];
    }

    /**
    * @controller actionPrintLabelBed
    * @attribute #no_rekam_medik# => no rekam medik
    * @attribute #nama_depan# => gelar / nama depan pasien
    * @attribute #nama_pasien# => nama pasien
    * @attribute #barcode# => barcode
    * @attribute #dpjp# => Dokter Penanggung Jawab
    * @attribute #tgl_daftar# => Tgl Masuk Rs
    * @attribute #no_reg# => No Regis
    * @attribute #no_ktp# => No KTP
    **/
    public function actionPrintLabelBed()
    {
        try{
            $model = new InfKunjunganRsView;
            $ktp = '-';

            $request = Yii::$app->request;
            $pasien_id = $request->post('pasien_id',null);
            if(!$pasien_id){
                throw new \yii\base\ErrorException("ID Pasien Tidak Ditemukan", 500);
            }
            $pendaftaran_id = $request->post('pendaftaran_id',null);
            if(!$pendaftaran_id){
                throw new \yii\base\ErrorException("ID Pendaftaran Tidak Ditemukan", 500);
            }
            $kunjungan = $model::find()->where(['pasien_id'=>$pasien_id,'pendaftaran_id'=>$pendaftaran_id])->orderBy('tgl_pendaftaran DESC')->one();
            if(!$kunjungan){
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }

            $nama_pasien = isset($kunjungan->nama_pasien) ? strtoupper($kunjungan->nama_pasien) : '';
            $nama_pasien = DocoHelpers::cutSentence($nama_pasien, 20);
            $barcodeGen = new BarcodeGeneratorPNG;
            $barcode = '<img style="height: 20px; margin-left: 2px" src="data:image/png;base64,' . base64_encode($barcodeGen->getBarcode($kunjungan->no_rekam_medik, $barcodeGen::TYPE_CODE_128)) . '">';
            $tglDaftar = isset($kunjungan->tgl_pendaftaran) ? $kunjungan->tgl_pendaftaran : '';
            $tglMasukRs = date('d-m-Y', strtotime($tglDaftar));

            if (isset($kunjungan->additional_pasien) && !empty($kunjungan->additional_pasien)) {
                $additionalPasien = json_decode($kunjungan->additional_pasien);

                if (!empty($additionalPasien)) {
                    foreach ($additionalPasien as $key => $value) {
                        if (isset($value->jenisidentitas) && $value->jenisidentitas == DocoConstants::CONS_ID_KTP) {
                            $ktp = $value->no_identitas_pasien;
                        }
                    }
                }
            }
            $print = new DocoPrint();
            $print->attributes = [
                '#nama_depan#' => isset($kunjungan->namadepan) ? strtoupper($kunjungan->namadepan) : '',
                '#nama_pasien#' => $nama_pasien,
                '#no_rekam_medik#' => isset($kunjungan->no_rekam_medik) ? $kunjungan->no_rekam_medik : '',
                '#barcode#' => $barcode,
                '#dpjp#' => isset($kunjungan->nama_pegawai) ? $kunjungan->nama_pegawai : '',
                '#tgl_daftar#' => $tglMasukRs,
                '#no_ktp#' => $ktp,
                '#no_reg#' => isset($kunjungan->no_pendaftaran) ? $kunjungan->no_pendaftaran : '',
            ];
            $print->Output();
        }catch (\Exception $e){
            return ['Error'=>$e->getMessage()];
        }
    }

    private function getKonfigSystem()
    {
        return KonfigSystem::find();
    }

    /**
    * @controller actionPrintR2k
    * @attribute #title# => Judul
    * @attribute #data# => Data
    **/
    public function actionPrintR2k()
    {
        try{
            $model = new Laporanr2kV;
            $title = 'RINGKASAN RIWAYAT IGD';
            $request = Yii::$app->request;
            $ttl = $ktp = $hubungan = $alergi = '';
            $pasien_id = $request->post('pasien_id',null);
            if(!$pasien_id){
                throw new \yii\base\ErrorException("ID Pasien Tidak Ditemukan", 500);
            }
            $pendaftaran_id = $request->post('pendaftaran_id',null);
            if(!$pendaftaran_id){
                throw new \yii\base\ErrorException("ID Pendaftaran Tidak Ditemukan", 500);
            }
            $kunjungan = $model::find()->where(['pendaftaran_id'=>$pendaftaran_id])->orderBy('tgl_registrasi DESC')->one();
            if(!$kunjungan){
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }
            if($kunjungan->tempat_lahir) {
                $ttl = $kunjungan->tempat_lahir;
            }

            if($kunjungan->tanggal_lahir) {
                $ttl = $ttl .', '. date('j M Y', strtotime($kunjungan->tanggal_lahir));
            }

            if($kunjungan->hubungankeluarga) {
                $lookHubungan = $this->getLookupByType('hubungan_keluarga')->all();
                foreach($lookHubungan as $k => $v) {
                    if($v['lookup_id'] == $kunjungan->hubungankeluarga) {
                        $hubungan = $v['lookup_value'];
                    }
                }
            }

            if($kunjungan->no_identitas_pasien && self::isJson($kunjungan->no_identitas_pasien)) {
                $tmpIdentitas = json_decode($kunjungan->no_identitas_pasien, true);
                foreach($tmpIdentitas as $k => $v) {
                    if($v['jenisidentitas'] == self::KTP) {
                        $ktp = $v['no_identitas_pasien'];
                    }
                }
            } else {
                $ktp = $kunjungan->no_identitas_pasien;
            }

            if($kunjungan->alergi_obat) {
                $alergi = $kunjungan->alergi_obat;
            }

            if($kunjungan->alergi_lainnya) {
                $alergi = $alergi . ' / ' . $kunjungan->alergi_lainnya;
            }

            $tgl_cetak = date('d M Y');

            $print = new DocoPrint();
            $print->attributes = [
                '#title#' => $title,
                '#tgl_cetak#' => $tgl_cetak,
                '#data#' => $this->renderPartial('print_r2k', [
                    'data' => $kunjungan,
                    'ktp' => $ktp,
                    'hubungan' => $hubungan,
                    'alergi' => $alergi,
                    'title' => $title,
                    'ttl' => $ttl,
                    'tgl_cetak' => $tgl_cetak,
                ]),
            ];
            $print->Output();
        }catch (\Exception $e){
            return ['Error'=>$e->getMessage()];
        }
    }

    /**
    * @controller actionPrintR2mk
    * @attribute #title# => Judul
    * @attribute #data# => Data
    **/
    public function actionPrintR2mk()
    {
        try{
            $model = new Laporanr2mkV;
            $title = 'RINGKASAN RIWAYAT MASUK & KELUAR';
            $request = Yii::$app->request;
            $ttl = $ktp = $hubungan = $alergi = $dokterPengirim = '';
            $additionalData = $pj = [];
            $pasien_id = $request->post('pasien_id',null);
            if(!$pasien_id){
                throw new \yii\base\ErrorException("ID Pasien Tidak Ditemukan", 500);
            }
            $pendaftaran_id = $request->post('pendaftaran_id',null);
            if(!$pendaftaran_id){
                throw new \yii\base\ErrorException("ID Pendaftaran Tidak Ditemukan", 500);
            }
            $kunjungan = $model::find()->where(['pendaftaran_id'=>$pendaftaran_id])->orderBy('tgl_registrasi DESC')->one();

            if(!$kunjungan){
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }

            if(!empty($kunjungan->additional_data) && self::isJson($kunjungan->additional_data)) {
                $additionalData = json_decode($kunjungan->additional_data, true);
            }

            if($kunjungan->tempat_lahir) {
                $ttl = $kunjungan->tempat_lahir;
            }

            if($kunjungan->tanggal_lahir) {
                $ttl = $ttl .', '. date('j M Y', strtotime($kunjungan->tanggal_lahir));
            }

            if($kunjungan->hubungankeluarga) {
                $lookHubungan = $this->getLookupByType('hubungan_keluarga')->all();
                foreach($lookHubungan as $k => $v) {
                    if($v['lookup_id'] == $kunjungan->hubungankeluarga) {
                        $hubungan = $v['lookup_value'];
                    }
                }
            }

            if($kunjungan->instalasi_id != DocoConstants::INST_ID_RD) {
                if(!empty($additionalData)) {
                    $asalPendaftaran = (array_key_exists('pendaftaranasal_id', $additionalData) ? $additionalData['pendaftaranasal_id'] : null);
                    if(!empty($asalPendaftaran)) {
                        $kunjunganSebelumnya = InfKunjunganRsView::find()
                        ->where([self::PEND_ID => (int) $asalPendaftaran])
                        ->one();
                        if(!empty($kunjunganSebelumnya)) {
                            $dokterPengirim = $kunjunganSebelumnya->nama_pegawai;
                        }
                        $kunjungan->dokter_pengirim = $dokterPengirim;
                    }
                }
            } else {
                if(!empty($additionalData)) {
                    $pj = (array_key_exists('penanggung_jawab', $additionalData) ? $additionalData['penanggung_jawab'] : null);
                    if(!empty($pj)) {
                        $kunjungan->penanggungjawab_nama = $pj['pj_nama'];
                        $kunjungan->penanggungjawab_alamat = $pj['pj_alamat'];
                    }
                }
            }

            if($kunjungan->no_identitas_pasien && self::isJson($kunjungan->no_identitas_pasien)) {
                $tmpIdentitas = json_decode($kunjungan->no_identitas_pasien, true);
                foreach($tmpIdentitas as $k => $v) {
                    if($v['jenisidentitas'] == self::KTP) {
                        $ktp = $v['no_identitas_pasien'];
                    }
                }
            } else {
                $ktp = $kunjungan->no_identitas_pasien;
            }

            if($kunjungan->alergi_obat) {
                $alergi = $kunjungan->alergi_obat;
            }

            if($kunjungan->alergi_lainnya) {
                $alergi = $alergi . ' / ' . $kunjungan->alergi_lainnya;
            }

            if (!empty($kunjungan->is_pasientitipan_pk)) {
                if($kunjungan->is_pasientitipan_pk == true && $kunjungan->is_stoppasientitipan == false){
                    $kunjungan->kelas = $kunjungan->kelas_ditagihkan_nama;
                }
            } else if (empty($kunjungan->is_pasientitipan_pk)) {
                if($kunjungan->is_pasientitipan  == true && $kunjungan->is_stoppasientitipan == false){
                    $kunjungan->kelas = $kunjungan->kelas_ditagihkan_nama;
                }
            }

            $profile = $this->getProfileRs();
            $kota = $profile['kota'];
            $namaRs = $profile['namaRs'];
            $tgl_cetak = date('d M Y');

            $print = new DocoPrint();
            $print->attributes = [
                '#title#' => $title,
                '#tgl_cetak#' => $tgl_cetak,
                '#data#' => $this->renderPartial('print_r2mk', [
                    'data' => $kunjungan,
                    'ktp' => $ktp,
                    'hubungan' => $hubungan,
                    'alergi' => $alergi,
                    'title' => $title,
                    'tgl_cetak' => $tgl_cetak,
                    'ttl' => $ttl,
                    'lokasi' => $kota. ', ' .date('d M Y'),
                    'nama_rs' => $namaRs
                ]),
            ];
            $print->Output();
        }catch (\Exception $e){
            return ['Error'=>$e->getMessage()];
        }
    }

    private static function isJson($string)
    {
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }

    private function getProfileRs()
    {
        $profilRs = Cache::getProfileRs();
        $kota = '-';
        $namaRs = '-';
        $url = '-';

        if (!empty($profilRs['nama_rumahsakit'])) {
            $namaRs = $profilRs['nama_rumahsakit'];
        }

        if (!empty($profilRs['kota'])) {
            if($match = preg_match("/KOTA ADM. /i", $profilRs['kota'])) {
                $pattern = "KOTA ADM. ";
            }
            elseif($match = preg_match("/KAB. ADM. /i", $profilRs['kota'])) {
                $pattern = "KAB. ADM. ";
            }
            elseif($match = preg_match("/KAB. /i", $profilRs['kota'])) {
                $pattern = "KAB. ";
            }
            elseif($match = preg_match("/KOTA /i", $profilRs['kota'])) {
                $pattern = "KOTA ";
            }

            $kota = str_replace($pattern,"", $profilRs['kota']);
        }

        $path = empty($profilRs['path_logorumahsakit']) ? null : $profilRs['path_logorumahsakit'];
        $logo = empty($profilRs['logo_rumahsakit']) ? null : $profilRs['logo_rumahsakit'];

        $url = $path.$logo;

        return [
            'namaRs' => $namaRs,
            'kota' => $kota,
            'logoRs' => $url
        ];
    }

    /**
    * @controller actionPrintSuratReferal
    * @attribute #no_rekam_medik# => no rekam medik
    * @attribute #nama_pasien# => nama pasien
    * @attribute #jenis_kelamin# => jenis kelamin pasien
    * @attribute #tanggal_lahir# => tanggal lahir
    * @attribute #nama_depan# => gelar / nama depan pasien
    * @attribute #umur# => umur pasien
    * @attribute #keluarga_dari# => Keluarga Dari
    * @attribute #nama_depan_keluarga# => nm depan keluarga
    * @attribute #alamat# =>alamat
    **/
    public function actionPrintSuratReferal()
    {
        try{
            $request = Yii::$app->request;
            $model = new SySuratreferalV;
            $pasien_id = $request->post('pasien_id',null);
            $pendaftaran_id = $request->post('pendaftaran_id',null);

            if(!$pasien_id){
                throw new \yii\base\ErrorException("ID Pasien Tidak Ditemukan", 500);
            }

            if(!$pendaftaran_id){
                throw new \yii\base\ErrorException("ID Pendaftaran Tidak Ditemukan", 500);
            }

            $kunjungan = $model::find()->where(['pendaftaran_id'=>$pendaftaran_id,'pasien_id'=>$pasien_id])->one();
            $kunjungan_diagnosa = $model::find()->where(['pendaftaran_id'=>$pendaftaran_id,'pasien_id'=>$pasien_id])->asArray()->one();
            if(!$kunjungan){
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }

            $print = new DocoPrint('surat-referal-sty', 'l');
            $no_rekam_medik = isset($kunjungan->no_rekam_medik) ? $kunjungan->no_rekam_medik : '-';

            $nama_pasien = DocoHelpers::cutSentence(isset($kunjungan->nama_pasien
            ) ? $kunjungan->nama_pasien:'', 20);

            $keluargaDari = DocoHelpers::cutSentence(isset($kunjungan->keluarga_nama
            ) ? $kunjungan->keluarga_nama:'');

            $umur = isset($kunjungan->umur) ? $kunjungan->umur : '';
            $print->attributes = [
                '#no_rekam_medik#' => $no_rekam_medik,
                '#nama_pasien#' => $nama_pasien,
                '#tanggal_lahir#' => isset($kunjungan->tanggal_lahir)
                    ?date('d M Y', strtotime($kunjungan->tanggal_lahir))
                    :'',
                '#tanggal_pendaftaran#' => isset($kunjungan->tgl_pendaftaran)
                    ?date('d M Y', strtotime($kunjungan->tgl_pendaftaran))
                    :'',
                '#jenis_kelamin#' => isset($kunjungan->pasienjeniskelamin)
                    ? $kunjungan->pasienjeniskelamin
                    :'',
                '#nama_depan#' => isset($kunjungan->namadepan) ? $kunjungan->namadepan : '',
                '#umur#' => $umur,
                '#keluarga_dari#' => $keluargaDari,
                '#nama_depan_keluarga#' => isset($kunjungan->keluarga_namadepan) ? $kunjungan->keluarga_namadepan : '',
                '#alamat#' => isset($kunjungan->alamat_pasien) ? $kunjungan->alamat_pasien : '',
                '#diagnosa#' => isset($kunjungan_diagnosa['diagnosa_dari']) ? $kunjungan_diagnosa['diagnosa_dari'] : '',
                '#dokter_pengirim#' =>isset($kunjungan->dokter_pengirim) ? $kunjungan->dokter_pengirim : '-',
            ];
            $print->Output();
        }catch (\Exception $e){
            Yii::error(['Error'=>$e->getMessage()]);
            return ['Error'=>$e->getMessage()];
        }
    }


    /**
    * @controller actionPrintSuratKeterangan
    * @attribute #nama_pasien# => nama pasien
    * @attribute #nama_depan# => gelar / nama depan pasien
    * @attribute #umur# => umur pasien
    * @attribute #pekerjaan_nama# => pekerjaan pasien
    * @attribute #alamat# => alamat pasien
    **/
    public function actionPrintSuratKeterangan()
    {
        try{
            $request = Yii::$app->request;
            $model = new InfKunjunganRsView;
            $pasien_id = $request->post('pasien_id',null);
            $pendaftaran_id = $request->post('pendaftaran_id',null);

            if(!$pasien_id){
                throw new \yii\base\ErrorException("ID Pasien Tidak Ditemukan", 500);
            }

            if(!$pendaftaran_id){
                throw new \yii\base\ErrorException("ID Pendaftaran Tidak Ditemukan", 500);
            }

            $kunjungan = $model::find()->where(['pendaftaran_id'=>$pendaftaran_id,'pasien_id'=>$pasien_id])->one();

            if(!$kunjungan){
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }

            $print = new DocoPrint('surat-keterangan-sty', 'l');
            error_reporting(0);
            $nama_pasien = DocoHelpers::cutSentence(isset($kunjungan->nama_pasien
            ) ? $kunjungan->nama_pasien:'', 20);

            $umur = isset($kunjungan->umur) ? $kunjungan->umur : '';
            $print->attributes = [
                '#nama_pasien#' => $nama_pasien,
                '#umur#' => $umur,
                '#pekerjaan_nama#' => isset($kunjungan->pekerjaan_nama) ? $kunjungan->pekerjaan_nama : '',
                '#nama_depan#' => isset($kunjungan->namadepan) ? $kunjungan->namadepan : '',
                '#alamat#' => isset($kunjungan->alamat_pasien) ? $kunjungan->alamat_pasien : '',
                '#nama_rs#' => 'RS Santo Yusup',
                '#tgl_masuk_ttd#' => isset($kunjungan->tgl_pendaftaran) ? date('d - M - Y', strtotime($kunjungan->tgl_pendaftaran)) : '',
                '#tgl_masuk#' => isset($kunjungan->tgl_pendaftaran) ? date('d m Y', strtotime($kunjungan->tgl_pendaftaran)) : '',
            ];
            $print->Output();
        }catch (\Exception $e){
            return ['Error'=>$e->getMessage()];
        }
    }

    /**
    * @controller actionPrintLabelPenunjang
    * @attribute #no_rekam_medik# => no rekam medik
    * @attribute #no_masukpenunjang# => no masuk penunjang
    * @attribute #namadepan# => nama depan pasien
    * @attribute #nama_pasien# => nama pasien
    * @attribute #umur# => umur pasien
    * @attribute #tanggal_lahir# => tangal lahir pasien
    * @attribute #alamat_pasien# => alamat pasien
    * @attribute #tanggal_pendaftaran# => Tanggal Pendaftaran
    * @attribute #pemeriksaan# => Pemeriksaan
    * @attribute #pemeriksa# => Pemeriksa
    **/
    public function actionPrintLabelPenunjang()
    {
        try{
            $request = Yii::$app->request;
            $jenis = $request->get('param', null);
            if ($jenis != 'ranap' && $jenis != 'penunjang') {
                $model = new InfKunjunganRsView;
            } else if ($jenis == 'penunjang') {
                $model = new InfPasienPenunjang;
            } else if ($jenis == 'ranap'){
                $model = new InfoKunjunganRiView;
            } else {
                $model = new InfKunjunganRsView;
            }
            $pasien_id = $request->get('pasien_id',null);
            if(!$pasien_id){
                throw new \yii\base\ErrorException("ID Pasien Tidak Ditemukan", 500);
            }
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            if(!$pendaftaran_id){
                throw new \yii\base\ErrorException("ID Pendaftaran Tidak Ditemukan", 500);
            }
            $kunjungan = $model::find()->where(['pasien_id'=>$pasien_id,'pendaftaran_id'=>$pendaftaran_id]);

            if($jenis == 'penunjang'){
                $kunjungan = $kunjungan->orderBy('tglmasukpenunjang DESC')->one();
            } else if($jenis == 'igd') {
                $kunjungan = $kunjungan->orderBy('tgl_pendaftaran ASC')->one();
            } else{
                $kunjungan = $kunjungan->orderBy('tgl_pendaftaran DESC')->one();
            }
            if(!$kunjungan){
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }
            // Yii::error($kunjungan);
            // return true;
            $tanggal_lahir = strtotime(isset($kunjungan->tanggal_lahir)?$kunjungan->tanggal_lahir:'');
            $today = date('Y-m-d');
            $diff = date_diff(date_create(date('Y-m-d', $tanggal_lahir)), date_create($today));
            $umur_th = (strlen($diff->y) > 1)?$diff->y:'0'.$diff->y;
            $umur_bl = (strlen($diff->m) > 1)?$diff->m:'0'.$diff->m;
            $umur_hr = (strlen($diff->d) > 1)?$diff->d:'0'.$diff->d;
            $umur = $umur_th."TH ".$umur_bl."BL ".$umur_hr."HR";
            $tanggal_lahir_convert = date('d-m-Y', $tanggal_lahir);
            if(!is_null($kunjungan->dokter_pengganti)) {
                $pemeriksa = $kunjungan->dokter_pengganti;
            }else {
                $pemeriksa = isset($kunjungan->nama_pegawai
                )?$kunjungan->nama_pegawai:'';
            }

            $print = new DocoPrint();
            $print->attributes = [
                '#no_rekam_medik#' => isset($kunjungan->no_rekam_medik
                )?$kunjungan->no_rekam_medik:'',
                '#no_masukpenunjang#' => isset($kunjungan->no_masukpenunjang
                )?$kunjungan->no_masukpenunjang:'',
                '#no_pendaftaran#' => isset($kunjungan->no_pendaftaran
                )?$kunjungan->no_pendaftaran:'',
                '#namadepan#' => isset($kunjungan->nama_depan
                )?$kunjungan->nama_depan:'',
                '#nama_pasien#' => isset($kunjungan->nama_pasien
                )?$kunjungan->nama_pasien:'',
                '#umur#' => $umur,
                '#tanggal_lahir#' => $tanggal_lahir_convert,
                '#alamat_pasien#' => isset($kunjungan->alamat_pasien
                )?$kunjungan->alamat_pasien:'',
                '#tanggal_pendaftaran#'=> isset($kunjungan->tgl_pendaftaran) ?
                        date('d-M-Y',strtotime($kunjungan->tgl_pendaftaran)):'',
                '#pemeriksaan#' => isset($kunjungan->pemeriksaan
                )?$kunjungan->pemeriksaan:'',
                '#pemeriksa#' => $pemeriksa
            ];
            $print->Output();
        }catch (Exception $e){
            return ['Error'=>$e->getMessage()];
        }
    }

    /**
    * @controller actionPrintLabelPenunjangKecil
    * @attribute #no_rekam_medik# => no rekam medik
    * @attribute #no_masukpenunjang# => no masuk penunjang
    * @attribute #namadepan# => nama depan pasien
    * @attribute #nama_pasien# => nama pasien
    * @attribute #umur# => umur pasien
    * @attribute #tanggal_lahir# => tangal lahir pasien
    * @attribute #alamat_pasien# => alamat pasien
    * @attribute #tanggal_pendaftaran# => Tanggal Pendaftaran
    * @attribute #pemeriksaan# => Pemeriksaan
    * @attribute #pemeriksa# => Pemeriksa
    **/
    public function actionPrintLabelPenunjangKecil()
    {
        try{
            $request = Yii::$app->request;
            $jenis = $request->get('param', null);
            if ($jenis != 'ranap' && $jenis != 'penunjang') {
                $model = new InfKunjunganRsView;
            } else if ($jenis == 'penunjang') {
                $model = new InfPasienPenunjang;
            } else if ($jenis == 'ranap'){
                $model = new InfoKunjunganRiView;
            } else {
                $model = new InfKunjunganRsView;
            }
            $pasien_id = $request->get('pasien_id',null);
            if(!$pasien_id){
                throw new \yii\base\ErrorException("ID Pasien Tidak Ditemukan", 500);
            }
            $pendaftaran_id = $request->get('pendaftaran_id',null);
            if(!$pendaftaran_id){
                throw new \yii\base\ErrorException("ID Pendaftaran Tidak Ditemukan", 500);
            }
            $kunjungan = $model::find()->where(['pasien_id'=>$pasien_id,'pendaftaran_id'=>$pendaftaran_id]);

            if($jenis == 'penunjang'){
                $kunjungan = $kunjungan->orderBy('tglmasukpenunjang DESC')->one();
            } else if($jenis == 'igd') {
                $kunjungan = $kunjungan->orderBy('tgl_pendaftaran ASC')->one();
            } else{
                $kunjungan = $kunjungan->orderBy('tgl_pendaftaran DESC')->one();
            }
            if(!$kunjungan){
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }
            // Yii::error($kunjungan);
            // return true;
            $tanggal_lahir = strtotime(isset($kunjungan->tanggal_lahir)?$kunjungan->tanggal_lahir:'');
            $today = date('Y-m-d');
            $diff = date_diff(date_create(date('Y-m-d', $tanggal_lahir)), date_create($today));
            $umur_th = (strlen($diff->y) > 1)?$diff->y:'0'.$diff->y;
            $umur_bl = (strlen($diff->m) > 1)?$diff->m:'0'.$diff->m;
            $umur_hr = (strlen($diff->d) > 1)?$diff->d:'0'.$diff->d;
            $umur = $umur_th."TH ".$umur_bl."BL ".$umur_hr."HR";
            $tanggal_lahir_convert = date('d-m-Y', $tanggal_lahir);
            if(!is_null($kunjungan->dokter_pengganti)) {
                $pemeriksa = $kunjungan->dokter_pengganti;
            }else {
                $pemeriksa = isset($kunjungan->nama_pegawai
                )?$kunjungan->nama_pegawai:'';
            }

            $print = new DocoPrint();
            $print->attributes = [
                '#no_rekam_medik#' => isset($kunjungan->no_rekam_medik
                )?$kunjungan->no_rekam_medik:'',
                '#no_masukpenunjang#' => isset($kunjungan->no_masukpenunjang
                )?$kunjungan->no_masukpenunjang:'',
                '#no_pendaftaran#' => isset($kunjungan->no_pendaftaran
                )?$kunjungan->no_pendaftaran:'',
                '#namadepan#' => isset($kunjungan->nama_depan
                )?$kunjungan->nama_depan:'',
                '#nama_pasien#' => isset($kunjungan->nama_pasien
                )?$kunjungan->nama_pasien:'',
                '#umur#' => $umur,
                '#tanggal_lahir#' => $tanggal_lahir_convert,
                '#alamat_pasien#' => isset($kunjungan->alamat_pasien
                )?$kunjungan->alamat_pasien:'',
                '#tanggal_pendaftaran#'=> isset($kunjungan->tgl_pendaftaran) ?
                        date('d-M-Y',strtotime($kunjungan->tgl_pendaftaran)):'',
                '#pemeriksaan#' => isset($kunjungan->pemeriksaan
                )?$kunjungan->pemeriksaan:'',
                '#pemeriksa#' => $pemeriksa
            ];
            $print->Output();
        }catch (Exception $e){
            return ['Error'=>$e->getMessage()];
        }
    }

    /**
    * @controller actionPrintAntrianPoli
    * @attribute #tgl_pendaftaran# => tanggal pendaftaran
    * @attribute #no_rekam_medik# => no rekam medik
    * @attribute #nama_depan# => gelar / nama depan pasien
    * @attribute #nama_pasien# => nama pasien
    * @attribute #cara_bayar# => cara bayar
    * @attribute #penjamin# => penjamin
    * @attribute #nama_dokter# => nama dokter
    * @attribute #nama_ruangan# => nama ruangan
    * @attribute #barcode# => kode barcode
    * @attribute #jk# => jenis kelamin pasien
    * @attribute #tgl_lahir# => tgl lahir pasien
    * @attribute #umur# => umur pasien
    * @attribute #jam_kunjungan# => jam kunjungan
    * @attribute #unit# => unit
    * @attribute #no_pendaftaran# => No Pendaftaran
    * @attribute #layout_print# => layout print tracer

    **/

    public function actionPrintAntrianPoli()
    {
        try{
            $request = Yii::$app->request;
            $pendaftaran_id = $request->post('pendaftaran_id',null);
            $pendaftaranol_id  = $request->post('pendaftaranol_id', null);
            $jenis = $request->post('param', null);

            if(!$pendaftaran_id && $jenis != 'reservasi'){
                throw new \yii\base\ErrorException("ID Pendaftaran Tidak Ditemukan", 500);
            }

            if ( !$pendaftaranol_id && $jenis == 'reservasi' ) {
                throw new \yii\base\ErrorException("PendaftaranOnline ID Tidak Ditemukan", 500);
            }

            if ($jenis != 'ranap' && $jenis != 'penunjang' && $jenis != 'reservasi' ) {
                $kunjungans = (new InfKunjunganRsTracerView)->find();
            } else if ($jenis == 'penunjang') {
                $kunjungans = (new InfPasienPenunjang)->find();
            } else if ($jenis == 'ranap'){
                $kunjungans = (new InfoKunjunganRiView)->find();
            } else if ($jenis == 'reservasi') {
                $kunjungans = (new InfoPendaftaranOnlineView)->find()->select([
                    'pasien_id',
                    'pendaftaranol_id as pendaftaran_id',
                    'no_pendaftaranol as no_pendaftaran',
                    'COALESCE(
                        tgl_didaftarkan :: text,
                        CONCAT(
                            to_char( tgl_kunjungan,\'YYYY-MM-DD\' ), \' \',
                            split_part(split_part(jam_kunjungan, \' - \', 1), \'-\', 1)
                        ) :: text
                    ) AS tgl_pendaftaran',
                    'jeniskelamin',
                    'jk',
                    'COALESCE(nama_depan, namadepan_ol) as namadepan',
                    'COALESCE(nama_pasien, nama_pasien_ol) as nama_pasien',
                    'COALESCE(tanggal_lahir, tanggal_lahir_ol) as tanggal_lahir',
                    'no_rekam_medik',
                    'nama_pegawai',
                    'ruangan_nama',
                    'no_antrian',
                    'status_pasien',
                    'carabayar_nama',
                    'no_identitas_pasien_ol',
                    'is_cetaktracer',
                ]);
            } else {
                $kunjungans = (new InfKunjunganRsTracerView)->find();
            }

            $defaultParams = [
                'pendaftaran_id'=>$pendaftaran_id
            ];

            if ( !empty($pendaftaranol_id) && $jenis == 'reservasi' ) {
                $defaultParams = [
                    'pendaftaranol_id' => $pendaftaranol_id
                ];
            }

            $kunjungans->where($defaultParams);
            $kunjungans = $kunjungans->orderBy('tgl_pendaftaran DESC')->limit(1)->all();

            if(!$kunjungans){
                throw new \yii\web\NotFoundHttpException("Data Tidak Ditemukan", 404);
            }

            $data_print = [];

            foreach ($kunjungans as $key => $kunjungan) {
                $barcodeGen = new BarcodeGeneratorPNG;
                $barcode_value = isset($kunjungan->no_rekam_medik) ? base64_encode($barcodeGen->getBarcode($kunjungan->no_rekam_medik, $barcodeGen::TYPE_CODE_128)) : '';
                $barcode = '<img style="height: 20px; margin-left: 2px" src="data:image/png;base64,' . $barcode_value . '">';

                if($jenis == "ranap"){
                    $strtotime_tanggal_kunjungan = strtotime(isset($kunjungan->tgl_admisi)?$kunjungan->tgl_admisi:'');
                    // $tanggal_pendaftaran_convert = DocoHelpers::getTanggalIndonesia(date('Y-m-d', $strtotime_tanggal_kunjungan));
                    $tanggal_pendaftaran_convert = DocoHelpers::convDateTime($kunjungan->tgl_admisi, true, false);
                    $jamKunjungan = date('H:i:s',$strtotime_tanggal_kunjungan);
                    $tanggal_pendaftaran_convert = is_array($tanggal_pendaftaran_convert)
                                                    ? date('d',$strtotime_tanggal_kunjungan) ." ". $tanggal_pendaftaran_convert['bulan']. " " .$tanggal_pendaftaran_convert['tahun'] ." ".date('H:i:s',$strtotime_tanggal_kunjungan)
                                                    : $tanggal_pendaftaran_convert;
                }else if($jenis == "penunjang"){
                    $strtotime_tanggal_kunjungan = strtotime(isset($kunjungan->tglmasukpenunjang    )?$kunjungan->tglmasukpenunjang :'');
                    // $tanggal_pendaftaran_convert = DocoHelpers::getTanggalIndonesia(date('Y-m-d', $strtotime_tanggal_kunjungan));
                    $tanggal_pendaftaran_convert = DocoHelpers::convDateTime($kunjungan->tglmasukpenunjang, true, false);
                    $jamKunjungan = date('H:i:s',$strtotime_tanggal_kunjungan);
                    $tanggal_pendaftaran_convert = is_array($tanggal_pendaftaran_convert)
                                                    ? date('d',$strtotime_tanggal_kunjungan) ." ". $tanggal_pendaftaran_convert['bulan']. " " .$tanggal_pendaftaran_convert['tahun'] ." ".date('H:i:s',$strtotime_tanggal_kunjungan)
                                                    : $tanggal_pendaftaran_convert;
                }else{
                    $strtotime_tanggal_kunjungan = strtotime(isset($kunjungan->tgl_pendaftaran)?$kunjungan->tgl_pendaftaran:'');
                    // $tanggal_pendaftaran_convert = DocoHelpers::getTanggalIndonesia(date('Y-m-d', $strtotime_tanggal_kunjungan));
                    $tanggal_pendaftaran_convert = DocoHelpers::convDateTime($kunjungan->tgl_pendaftaran, true, false);
                    $jamKunjungan = date('H:i:s',$strtotime_tanggal_kunjungan);
                    $tanggal_pendaftaran_convert = is_array($tanggal_pendaftaran_convert)
                                                    ? date('d',$strtotime_tanggal_kunjungan) ." ". $tanggal_pendaftaran_convert['bulan']. " " .$tanggal_pendaftaran_convert['tahun'] ." ".date('H:i:s',$strtotime_tanggal_kunjungan)
                                                    : $tanggal_pendaftaran_convert;
                }
                // Override 'mcu' nomor_antrian value, must be null
                if($jenis == 'mcu'){
                    $kunjungan->no_antrian = null;
                }
                $listJk = $this->getLookupByType(self::JK)->all();

                $jk = isset($kunjungan->jenis_kelamin) ? $kunjungan->jenis_kelamin : '';
                if ( $jenis == 'reservasi') {
                    $jk = isset($kunjungan->jk) ? $kunjungan->jk : '';
                }

                $name =  isset($kunjungan->nama_pasien
                )?$kunjungan->nama_pasien:'';
                $displayName = DocoHelpers::cutSentence($name);

                $dpjp = isset($kunjungan->nama_pegawai
                )?$kunjungan->nama_pegawai:'';
                $displayDpjp = $dpjp;

                $jwt = Yii::$app->jwt->user;
                $loginpemakai_id = !empty($jwt->loginpemakai_id) ? $jwt->loginpemakai_id : '1';

                if (isset($kunjungan->status_pasien)) {
                    $statusPasien = Lookup::findOne($kunjungan->status_pasien);
                }

                if ($loginpemakai_id) {
                    $loginPemakai = LoginPemakaiView::find()->where(['loginpemakai_id' => $loginpemakai_id])->one();
                }

                if ( isset($kunjungan->no_identitas_pasien_ol) ) {
                    $ktp = $kunjungan->no_identitas_pasien_ol;
                }
                if (isset($kunjungan->additional_pasien) && !empty($kunjungan->additional_pasien)) {
                    $additionalPasien = json_decode($kunjungan->additional_pasien);

                    if (!empty($additionalPasien)) {
                        foreach ($additionalPasien as $key => $value) {
                            if (isset($value->jenisidentitas) && $value->jenisidentitas == DocoConstants::CONS_ID_KTP) {
                                $ktp = $value->no_identitas_pasien;
                            }
                        }
                    }
                }

                $umur = '';
                if (isset($kunjungan->umur) && $kunjungan->umur != '') {
                    $kunjungan->umur = str_replace("Tahun", "thn,", $kunjungan->umur);
                    $kunjungan->umur = str_replace("Bulan", "bln,", $kunjungan->umur);
                    $kunjungan->umur = str_replace("Hari", "hr", $kunjungan->umur);
                } else {
                    $umur = (new DocoHelpers)->getUmur($kunjungan->tanggal_lahir);
                }

                array_push($data_print, [
                    'tgl_pendaftaran' => $tanggal_pendaftaran_convert,
                    'no_rekam_medik' => isset($kunjungan->no_rekam_medik)?$kunjungan->no_rekam_medik:'-',
                    'nama_pasien' => $displayName,
                    'cara_bayar' => isset($kunjungan->carabayar_nama)?$kunjungan->carabayar_nama:'-',
                    'penjamin' => isset($kunjungan->penjamin_nama)?$kunjungan->penjamin_nama:'-',
                    'nama_dokter'=> $displayDpjp,
                    'nama_ruangan'=>isset($kunjungan->ruangan_nama) ? $kunjungan->ruangan_nama : '',
                    'nama_depan' => isset($kunjungan->namadepan) ? (($jenis == "ranap") ? $kunjungan->nama_depan : $kunjungan->namadepan) : '',
                    'barcode' => $barcode,
                    'jk' => $jk,
                    'tgl_lahir' => isset($kunjungan->tanggal_lahir) ? date('d-M-Y', strtotime($kunjungan->tanggal_lahir)) : '-' ,
                    'umur' => isset($kunjungan->umur) ? $kunjungan->umur : ( $umur != '' ? $umur : '-'),
                    'no_antrian' => isset($kunjungan->no_antrian)?$kunjungan->no_antrian:'-',
                    'jam_kunjungan' => isset($jamKunjungan) ? $jamKunjungan : '',
                    'unit' => isset($kunjungan->ruangan_nama) ? DocoHelpers::cutSentence($kunjungan->ruangan_nama, 15) : '-',
                    'status_pasien' => isset($statusPasien->lookup_name) ? $statusPasien->lookup_name : '-',
                    'loginpemanai_nama' => isset($loginPemakai->nama_pegawai) ? DocoHelpers::cutSentence($loginPemakai->nama_pegawai) : '-',
                    'nik' => "ktp",
                    'no_pendaftaran' => isset($kunjungan->no_pendaftaran) ? $kunjungan->no_pendaftaran : '-',
                ]);
            }

            $profile = $this->getProfileRs();
            $kota = $profile['kota'];
            $namaRs = $profile['namaRs'];
            $logoRs = $profile['logoRs'];

            $waktu_estimasi = $this->getEstimasiTimeAntrian(compact('pendaftaran_id'));
            // End of Line
            $print = new DocoPrint();
            /* di uncomment karena digunakan di site Kramat */
            $print->attributes = [
                '#layout_print#' => $this->renderPartial('print_antrian_poli', [
                        'datas' => $data_print
                    ]),
                '#tgl_pendaftaran#' => $tanggal_pendaftaran_convert,
                '#no_rekam_medik#' => isset($kunjungan->no_rekam_medik)?$kunjungan->no_rekam_medik:'-',
                '#nama_pasien#' => $displayName,
                '#cara_bayar#' => isset($kunjungan->carabayar_nama)?$kunjungan->carabayar_nama:'-',
                '#penjamin#' => isset($kunjungan->penjamin_nama)?$kunjungan->penjamin_nama:'-',
                '#nama_dokter#'=> $displayDpjp,
                '#nama_ruangan#'=>isset($kunjungan->ruangan_nama) ? $kunjungan->ruangan_nama : '',
                '#nama_depan#' => isset($kunjungan->namadepan) ? (($jenis == "ranap") ? $kunjungan->nama_depan : $kunjungan->namadepan) : '',
                '#barcode#' => $barcode,
                '#jk#' => $jk,
                '#tgl_lahir#' => isset($kunjungan->tanggal_lahir) ? date('d-M-Y', strtotime($kunjungan->tanggal_lahir)) : '-' ,
                '#umur#' => isset($kunjungan->umur) ? $kunjungan->umur : ( $umur != '' ? $umur : '-'),
                '#no_antrian#' => isset($kunjungan->no_antrian)?$kunjungan->no_antrian:'-',
                '#jam_kunjungan#' => isset($jamKunjungan) ? $jamKunjungan : '',
                '#unit#' => isset($kunjungan->ruangan_nama) ? DocoHelpers::cutSentence($kunjungan->ruangan_nama, 15) : '-',
                '#status_pasien#' => isset($statusPasien->lookup_name) ? $statusPasien->lookup_name : '-',
                '#loginpemanai_nama#' => isset($loginPemakai->nama_pegawai) ? DocoHelpers::cutSentence($loginPemakai->nama_pegawai) : '-',
                '#nik#' => "ktp",
                '#no_pendaftaran#' => isset($kunjungan->no_pendaftaran) ? $kunjungan->no_pendaftaran : '-',
                '#lokasi#' => $kota,
                '#nama_rs#' => $namaRs,
                '#logo#' => $logoRs,
                '#waktu_estimasi_mulai' => ArrayHelper::getValue($waktu_estimasi, 'waktuestimasi_mulai'),
                '#waktu_estimasi_berakhir' => ArrayHelper::getValue($waktu_estimasi, 'waktuestimasi_berakhir'),
            ];
            $print->Output();
        }catch (\Exception $e){
            return ['Error'=>$e->getMessage()];
        }
    }

    public function actionGetEstimasiTimeAntrian()
    {
        $request = Yii::$app->request;
        return $this->getEstimasiTimeAntrian($request->get());
    }

    private function getEstimasiTimeAntrian($array_request)
    {
        $pendaftaran_id = ArrayHelper::getValue($array_request, 'pendaftaran_id');
        $ruangan_id = ArrayHelper::getValue($array_request, 'ruangan_id');
        $pegawai_id = ArrayHelper::getValue($array_request, 'pegawai_id');
        $tgl_pendaftaran = ArrayHelper::getValue($array_request, 'tgl_pendaftaran');
        $antrian_id = ArrayHelper::getValue($array_request, 'antrian_id');

        $today_id = DocoHelpers::getIdHariIni();

        $antrian =  Antrian::find()->select(['antrian_id', 'jadwaldokter_id', 'jadwalbukapoli_id', 'tgl_antrian','groupcarabayar_id','carabayar_id'])
                      ->Where(['jenisantrian_id' => DocoConstants::VAR_JA_P]);

        /* Condition Antrian */
        if (empty($antrian_id) || is_null($antrian_id) || $antrian_id=='null') {
            $antrian = $antrian->where(['pendaftaran_id' => $pendaftaran_id]);
        } else {
            $antrian = $antrian->where(['antrian_id' => $antrian_id]);
        }

        $antrian = $antrian->asArray()->one();

        if (($tgl_pendaftaran == null || $ruangan_id == null || $pegawai_id == null) && $pendaftaran_id) {
            $pendaftaran = Pendaftaran::find()->select(['pendaftaran_id', 'tgl_pendaftaran', 'ruangan_id', 'pegawai_id'])->where(['pendaftaran_id' => $pendaftaran_id])->asArray()->one();
            $tgl_pendaftaran = ArrayHelper::getValue($pendaftaran, 'tgl_pendaftaran');
            $ruangan_id = ArrayHelper::getValue($pendaftaran, 'ruangan_id');
            $pegawai_id = ArrayHelper::getValue($pendaftaran, 'pegawai_id');
        }

        $tgl_pendaftaran = $tgl_pendaftaran != null ? $tgl_pendaftaran : ArrayHelper::getValue($antrian, 'tgl_antrian');

        $query_jadwal_dokter = "select
              d.jadwaldokter_id ,
              d.jadwaldokter_mulai ,
              d.jadwaldokter_tutup ,
              d.jumlah_loaddokter as estimasidilayani,
              d.kuota_total,
              COALESCE(d.kuota_bpjs_offline,0) as kuota_bpjs_offline,
              COALESCE(d.kuota_bpjs_online,0) as kuota_bpjs_online,
              COALESCE(d.kuota_nonbpjs_offline,0) as kuota_nonbpjs_offline,
              COALESCE(d.kuota_nonbpjs_online,0) as kuota_nonbpjs_online
          from jadwalbukapoli_m j
          RIGHT JOIN jadwaldokter_m d ON d.jadwalbukapoli_id = j.jadwalbukapoli_id
          RIGHT JOIN pegawai_m p ON p.pegawai_id = d.pegawai_id";

        if (ArrayHelper::getValue($antrian, 'jadwaldokter_id') != null) {
            $kondisi_jadwal_dokter = "where d.jadwaldokter_id = '".ArrayHelper::getValue($antrian, 'jadwaldokter_id')."'
                      and d.is_deleted = false and d.is_active = true
                      and p.is_deleted = false and p.is_active = true";
        } else {
            $kondisi_jadwal_dokter = "where j.ruangan_id = {$ruangan_id}
                      and j.hari = {$today_id}
                      and p.pegawai_id = {$pegawai_id}
                      and d.is_deleted = false and d.is_active = true
                      and p.is_deleted = false and p.is_active = true";
        }
        $jadwal_dokter = Yii::$app->db->createCommand($query_jadwal_dokter .' '. $kondisi_jadwal_dokter)->queryOne();
        if ($jadwal_dokter) {
            $start_date = date('Y-m-d', strtotime(ArrayHelper::getValue($antrian, 'tgl_antrian'))); // untuk query hari tgl antrian saja
            /*
            $query_antrian = "select count(distinct(at.antrian_id)) as posisi_antrian from
                                (select antrian_id from antrian_t at2
                                    WHERE at2.jadwaldokter_id = '".ArrayHelper::getValue($jadwal_dokter, 'jadwaldokter_id')."'
                                    AND at2.jenisantrian_id = '".DocoConstants::VAR_JA_P."'
                                    AND at2.tgl_antrian::date = '".$start_date."'
                                    AND at2.antrian_id <= '".ArrayHelper::getValue($antrian, 'antrian_id')."'
                                    ORDER BY at2.antrian_id DESC
                                ) as at";
            */
            $dataAntrian = Yii::$app->db->createCommand("
                SELECT 
                    count(antrian_id) filter (where is_online is true and groupcarabayar_id=418) as jumlah_antrian_bpjs_online,
                    count(antrian_id) filter (where is_online is false and groupcarabayar_id=418) as jumlah_antrian_bpjs_offline,
                    count(antrian_id) filter (where is_online is true and groupcarabayar_id<>418) as jumlah_antrian_nonbpjs_online,  
                    count(antrian_id) filter (where is_online is false and groupcarabayar_id<>418) as jumlah_antrian_nonbpjs_offline
                FROM antrian_t 
                WHERE jenisantrian_id = :jenisantrian_id
                AND antrian_t.jadwaldokter_id = :jadwaldokter_id
                AND tgl_antrian::date = :start_date
                AND antrian_id <> :antrian_id
                AND antrian_t.is_deleted is false 
            ")
            ->bindValue(':jenisantrian_id',DocoConstants::VAR_JA_P)
            ->bindValue(':jadwaldokter_id',ArrayHelper::getValue($jadwal_dokter, 'jadwaldokter_id'))
            ->bindValue(':start_date',$start_date)
            ->bindValue(':antrian_id',ArrayHelper::getValue($antrian, 'antrian_id'));
            $dataAntrian = $dataAntrian->queryOne();
            $kuota_bpjs_offline = ArrayHelper::getValue($jadwal_dokter,'kuota_bpjs_offline',0);
            $kuota_bpjs_online= ArrayHelper::getValue($jadwal_dokter,'kuota_bpjs_online',0);
            $kuota_nonbpjs_offline= ArrayHelper::getValue($jadwal_dokter,'kuota_nonbpjs_offline',0);
            $kuota_nonbpjs_online= ArrayHelper::getValue($jadwal_dokter,'kuota_nonbpjs_online',0);
            $kuotajkn = $kuota_bpjs_online + $kuota_bpjs_offline;
            $kuotanonjkn = $kuota_nonbpjs_offline + $kuota_nonbpjs_online;
            $jumlah_antrian_bpjs_online = ArrayHelper::getValue($dataAntrian,'jumlah_antrian_bpjs_online',0);
            $jumlah_antrian_bpjs_offline = ArrayHelper::getValue($dataAntrian,'jumlah_antrian_bpjs_offline',0);
            $jumlah_antrian_nonbpjs_online = ArrayHelper::getValue($dataAntrian,'jumlah_antrian_nonbpjs_online',0);
            $jumlah_antrian_nonbpjs_offline = ArrayHelper::getValue($dataAntrian,'jumlah_antrian_nonbpjs_offline',0);
            $sisakuotanonjkn = $kuotanonjkn - ($jumlah_antrian_nonbpjs_online + $jumlah_antrian_nonbpjs_offline);
            $sisakuotajkn = $kuotajkn - ($jumlah_antrian_bpjs_online + $jumlah_antrian_bpjs_offline);
            // $posisi_antrian = Yii::$app->db->createCommand($query_antrian)->queryScalar();

            // gak pake array helper, gak tau kenapa gak masuk ke third parameter jika null
            $tglestimasi = date('Y-m-d', strtotime($tgl_pendaftaran));
            $jamestimasi = isset($jadwal_dokter['jadwaldokter_mulai']) ? date('H:i', strtotime($jadwal_dokter['jadwaldokter_mulai'])) : date('H:i', strtotime('00:00'));
            // $totalAntrian = (isset($jadwal_dokter['kuotajkn']) ? $jadwal_dokter['kuotajkn'] : 0) + (isset($jadwal_dokter['kuotanonjkn']) ? $jadwal_dokter['kuotanonjkn'] : 0);
            // $sisaAntrian = (isset($jadwal_dokter['sisakuotanonjkn']) ? $jadwal_dokter['sisakuotanonjkn'] : 0) + (isset($jadwal_dokter['sisakuotajkn']) ? $jadwal_dokter['sisakuotajkn'] : 0);
            $groupcarabayar_antrian = ArrayHelper::getValue($antrian,'groupcarabayar_id');
            $carabayar_antrian = ArrayHelper::getValue($antrian,'carabayar_id');
            if($groupcarabayar_antrian == DocoConstants::GROUP_BPJS || $carabayar_antrian == DocoConstants::CARA_BAYAR_BPJS){
                $totalAntrian = $kuotajkn;
                $sisaAntrian = $sisakuotajkn;
            }else{
                $totalAntrian = $kuotanonjkn;
                $sisaAntrian = $sisakuotanonjkn;
            }
            $spm = (isset($jadwal_dokter['estimasidilayani']) ? $jadwal_dokter['estimasidilayani'] : 6);
            $tgl_jam_mulai_dokter = $tglestimasi . " " . $jamestimasi;
            $noUrut = ($totalAntrian - $sisaAntrian);

            $jamSekarang = date('H:i:s');
            $tglSekarang = date('Y-m-d');
            if(strtotime($tglestimasi) > strtotime($tglSekarang)){
                $tglJamSekarang = $tglSekarang . ' ' . $jamSekarang;
            }else{
                $tglJamSekarang = $tglestimasi . ' ' . $jamSekarang;
            }
            $waktuestimasi = $tglestimasi . ' ' . $jamestimasi;
            $urutanAntrianDiambil = $noUrut>0 ? $noUrut-1 : $noUrut;
            $currentTimeSlot = (date_create($waktuestimasi)->getTimestamp()) + (
                (($spm * $urutanAntrianDiambil) * 60)
            );

            $waktuestimasi_mulai_timestamp = (date_create($waktuestimasi)->getTimestamp()) + (
                (($spm * $noUrut) * 60)
            );

            $waktuestimasi_berakhir_timestamp = $waktuestimasi_mulai_timestamp + ($spm * 60);
            

            if(strtotime($tglJamSekarang) > strtotime($waktuestimasi)){
                if($currentTimeSlot < strtotime($tglJamSekarang)){
                    $interval = date_diff(date_create($waktuestimasi),date_create($tglJamSekarang));
                    $urutanSekarang = ceil(((($interval->h * 60)+$interval->i) / $spm));

                    $waktuestimasi_mulai_timestamp = (date_create($waktuestimasi)->getTimestamp()) + (
                        (($spm * $urutanSekarang) * 60)
                    );
            
                    $waktuestimasi_berakhir_timestamp = $waktuestimasi_mulai_timestamp + ($spm * 60);
                }
            }

            return [
                'waktuestimasi_mulai' => date('H:i', $waktuestimasi_mulai_timestamp),
                'waktuestimasi_mulai_timestamp' => $waktuestimasi_mulai_timestamp,
                'waktuestimasi_berakhir' => date('H:i', $waktuestimasi_berakhir_timestamp),
                'waktuestimasi_berakhir_timestamp' => $waktuestimasi_berakhir_timestamp,
                'posisi_antrian' => $noUrut+1,
                'kuota_total' => isset($jadwal_dokter['kuota_total']) ? $jadwal_dokter['kuota_total'] : 0 ,
            ];
        }

        return [
            'waktuestimasi_mulai' => date('H:i', strtotime('00:00')),
            'waktuestimasi_mulai_timestamp' => 0,
            'waktuestimasi_berakhir' => date('H:i', strtotime('00:00')),
            'waktuestimasi_berakhir_timestamp' => 0,
            'posisi_antrian' => 0,
            'kuota_total' => 0,
        ];

    }
}
