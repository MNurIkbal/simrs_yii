<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use yii\helpers\ArrayHelper;
use GuzzleHttp\Exception\RequestException;
use yii\base\Exception;
use yii\db\Expression;
use yii\data\ArrayDataProvider;

use app\modules\v1\models\InfoPasienRanap;
use Doco\models\SoapRsView;
use app\modules\v1\models\InfoInstruksiView;
use app\modules\v1\models\AsesmenMedis;
use app\modules\v1\models\RujukanPulang;
use app\modules\v1\models\InfoResepturDetailView;
use app\modules\v1\models\KonfigSystem;

use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoMessages;
use Doco\components\DocoConstansId;
use Doco\models\Pegawai;
use Doco\models\ProfilRsView;
use Doco\models\bpjs\Bpjs;

class InfPasienRujukanRanapController extends DocoActiveController
{

    public $modelClass = 'app\modules\v1\models\InfPasienRujukanRanap';
    protected $_title = 'Rujukan Pasien';
    public $konfigCpptKosong;

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        //unset($actions['update']);
        return $actions;
    }

    public function init() {
        parent::init();
        $this->konfigSystemCppt();
    }

    public function konfigSystemCppt() {
        $konfigCppt = KonfigSystem::find()->select(['is_hide_cppt_kosong'])->asArray()->one();
        $this->konfigCpptKosong = $konfigCppt['is_hide_cppt_kosong'];
    }

    public function actionGetData() 
    {
        $request = Yii::$app->request;
        $pasienadmisi_id = $request->get('pasienadmisi_id');
        $pendaftaran_id = $request->get('pendaftaran_id');
        $intalasi = !empty($request->get('intalasi')) ? $request->get('intalasi') : null;
        $dataRujukan = RujukanPulang::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
        
        $diagnosaAwal = $this->getDataPasienRanap($pasienadmisi_id, $pendaftaran_id)->asArray()->one(); // 
        $diagnosaKeluar = $this->getDataSoaprsRanap($pasienadmisi_id, $pendaftaran_id, $intalasi)->asArray()->one(); // mapping lagi 
        $asesmen = $this->getAsesmen($pasienadmisi_id, $pendaftaran_id)->asArray()->one();
        $intruksiPenunjang = $this->getInfoIntruksi($pasienadmisi_id, $pendaftaran_id, "PENUNJANG")->asArray()->all();
        $intruksiTindakan = $this->getInfoIntruksi($pasienadmisi_id, $pendaftaran_id, "BMHP")->asArray()->all();
        $intruksiTerapi = $this->getInfoIntruksi($pasienadmisi_id, $pendaftaran_id, "RESEPTUR")->asArray()->all();

        $dataBpjs = Bpjs::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
        $listDpjp = [];
        $listDiagnosa = [];
        if(!empty($dataBpjs)) {
            $tglsep = strtotime($dataBpjs->tglsep);
            $result = $dataBpjs->referensiDokter($dataBpjs->jnspelayanan, date('Y-m-d', $tglsep), $dataBpjs->politujuan);
            $listDpjp = isset($result['response']['list']) && !empty($result['response']['list']) ? $result['response']['list'] : [];

            $result = $dataBpjs->diagnosaprb();
            $listDiagnosa = isset($result['response']['list']) && !empty($result['response']['list']) ? $result['response']['list'] : [];
        }
        return [
            'dataRujukan' => !empty($dataRujukan) ? $dataRujukan->attributes : null,
            'diagnosaAwal' => $diagnosaAwal,
            'diagnosaKeluar' => $diagnosaKeluar,
            'asesmen' => $asesmen,
            'intruksiPenunjang' => $intruksiPenunjang,
            'intruksiTindakan' => $intruksiTindakan,
            'intruksiTerapi' => $intruksiTerapi,
            'list-pegawai' => $this->getDataPegawai(),
            'list-dpjp-bpjs' => $listDpjp,
            'list-diagnosa' => $listDiagnosa,
        ];
    }

    // diagnosa awal
    private function getDataPasienRanap($pasienadmisi_id, $pendaftaran_id)
    {
        $pasienranap = InfoPasienRanap::find()
            ->select([
                "pasienadmisi_id",
                new Expression("diagnosa_nama ->> 'nama' as diagnosa_awal"),
                new Expression("diagnosa_nama ->> 'kode' as diagnosa_kode"),
            ]);
        if (!empty($pasienadmisi_id)) $pasienranap->andWhere(['pasienadmisi_id' => $pasienadmisi_id]);
        if (!empty($pendaftaran_id)) $pasienranap->andWhere(['pendaftaran_id' => $pendaftaran_id]); 
        return $pasienranap;
    }

    // diagnosa keluar
    private function getDataSoaprsRanap($pasienadmisi_id, $pendaftaran_id, $intalasi = null)
    {
        $soap = SoapRsView::find()
            ->select([
                "pasienadmisi_id",
                "tgl_soaprj",
                new Expression("a_diag_utama ->> 'text' as diagnosa_keluar"),
            ]);
        if (!empty($pasienadmisi_id)) $soap->andWhere(['pasienadmisi_id' => $pasienadmisi_id]);
        if (!empty($pendaftaran_id)) $soap->andWhere(['pendaftaran_id' => $pendaftaran_id]);
        if (!empty($intalasi)) $soap->andWhere(['instruksi' => $intalasi]);

        if($this->konfigCpptKosong == TRUE) {
            $soap->andWhere("(subject <> '-'::text OR object <> '-'::text OR planning <> '-'::text OR (a_diag_utama ->> 'text'::text) <> '-'::text OR (a_diag_utama ->> 'id'::text) IS NOT NULL)");
        }

        $soap->orderBy(['tgl_soaprj' => SORT_DESC]);
        
        return $soap;
    }

    public function getAsesmen($pasienadmisi_id, $pendaftaran_id){
        $data = AsesmenMedis::find()
            ->select([
                "keluhan_utama", 
                "r_penyakitsekarang", 
		        "r_penyakitdahulu", 
                new Expression("hasil_td as tensi"),
                new Expression("suhu_tubuh as suhu"),
                new Expression("detak_nadi as nadi"),
                new Expression("gcs_eye + gcs_verbal + gcs_motorik as jumlah_gcs"),
                "pernapasan",
                "pasienadmisi_id"
            ]);
        if (!empty($pasienadmisi_id)) $data->andWhere(['pasienadmisi_id' => $pasienadmisi_id]);
        if (!empty($pendaftaran_id)) $data->andWhere(['pendaftaran_id' => $pendaftaran_id]);    
        return $data;
    }

    public function getInfoIntruksi($pasienadmisi_id, $pendaftaran_id, $grouping_tipe = null){
        $data = InfoInstruksiView::find()
            ->select([
                "pasienadmisi_id",
                "tgl_instruksi",
                "instruksi",
                "grouping_tipe",
                "pendaftaran_id"
            ]);
            // ->where(['pasienadmisi_id'=>$pasienadmisi_id]);
            if (!empty($pasienadmisi_id)) $data->andWhere(['pasienadmisi_id' => $pasienadmisi_id]); 
            if (!empty($pendaftaran_id)) $data->andWhere(['pendaftaran_id' => $pendaftaran_id]); 
            if (!empty($grouping_tipe)) $data->andWhere(['grouping_tipe' => $grouping_tipe]);
            // ->andWhere(['in', 'grouping_tipe', ['PENUNJANG', 'BMHP', 'RESEPTUR']]);
        
        return $data;
    }

    private function getDataPegawai()
    {
        $sql = "select pegawai_id, nama_pegawai from pegawai_m where is_deleted = false and is_active = true
            order by pegawai_id asc 
        "; 
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        $items = ArrayHelper::map($data, 'pegawai_id', 'nama_pegawai');
        return $items;
    }

    private function getDataPegawaiByid($id)
    { 
        $data = Pegawai::find()
            ->select([
                "pegawai_id", 
                "nama_pegawai"
            ])->where(['pegawai_id'=>$id])->one();
        return $data;
    }

    public function actionSave() {
        $request = Yii::$app->request;
        $post = $request->post();
        $pendaftaran_id = !empty($post['pendaftaran_id']) ? $post['pendaftaran_id'] : null;
        $result = [];

        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $model = RujukanPulang::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
            if (is_null($model)) {
                $model = new RujukanPulang();
            }
            $model->attributes = $post;
            if(!$model->validate()){
                throw new \yii\db\Exception('Gagal Validasi', $model->getErrors(),422);
            }
            if(!$model->save()){
                throw new \yii\db\Exception('Gagal Simpan', $model->getErrors(),422);
            }
            $transaction->commit();
            return [
                'message' => 'Data Berhasil di simpan',
                'status' => 200,
                'data' => DocoHelpers::encrypt($model->pendaftaran_id),
            ];
        } catch (Exception $e) {
            \Yii::$app->response->statusCode = 422;
            $transaction->rollBack();
            $result = [
                    'message' => 'Terjadi Kesalahan2!',
                    'status' => 500,
                ];
        } catch (\yii\db\Exception $e) {
            $transaction->rollBack();
            $result = [
                'message' => $e->getMessage(),
                'text' => 'Gagal Validasi Data',
                'errorInfo'=> $e->errorInfo,
                'status' => 422
            ];
        }
        return $result;
    }

    /**
    * @controller actionPrintPdfRujukan
    * @attribute #nama_pasien# => Nama Pasien
    * @attribute #no_rekam_medik# => No rekam medik
    * @attribute #tanggal_lahir# => Tgl Lahir
    * @attribute #jenis_kelamin# => Jenis Kelamin
    * @attribute #nama_pegawai# => Nama Pegawai
    * @attribute #diagnosa_masuk# => diagnosa masuk
    * @attribute #pic_rujukan_dituju# => diagnosa keluar
    * @attribute #anamnesis_keluhan_utama# => Keluhan Utama
    * @attribute #riwayat_penyakit_sekarang# => Riwayat Penyakit Sekarang
    * @attribute #riwayat_penyakit_dahulu# => Riwayat Penyakit Dahulu
    * @attribute #ku# => anamnesis Keluhan Utama
    * @attribute #anamnesis_kesadaran# => anamnesis Kesadaran
    * @attribute #anamnesis_saturasi_o2# => anamnesis SpO2
    * @attribute #anamnesis_tensi# => anamnesis tensi
    * @attribute #anamnesis_suhu# => anamnesis suhu
    * @attribute #anamnesis_nadi# => anamnesis nadi
    * @attribute #anamnesis_pernafasan# => anamnesis Pernafasan
    * @attribute #alasan_dirujuk# => Alasan di rujuk
    * @attribute #pemeriksaan_penunjang# => Pemeriksaan Penunjang
    * @attribute #tindakan_terapi# => Tindakan Medis
    * @attribute #tindakan_lainnya# => pemberian Terapi
    * @attribute #derajat_0# => derajat 0
    * @attribute #derajat_1# => derajat 1
    * @attribute #derajat_2# => derajat 2
    * @attribute #derajat_3# => derajat 3
    * @attribute #derajat_0# => derajat 0
    * @attribute #tanggal_rujukan# => Tgl Rujukan
    * @attribute #jam# => Jam Rujukan
    * @attribute #keadaan_umum# => Keadaan Umum
    * @attribute #kesadaran# => Kesadaran
    * @attribute #tensi# => Tensi
    * @attribute #suhu# => Suhu
    * @attribute #saturasi_o2# => SpO2
    * @attribute #nadi# => Nadi
    * @attribute #suhu# => Suhu
    * @attribute #catatan_penting# => catatan penting
    * @attribute #petugas# => petugas
    **/
    public function actionPrintPdfRujukan()
    {
        $title = 'Formulir Rujukan Pasien Antar Rumah Sakit';
        $request = Yii::$app->request;
        $id = $request->get('pendaftaran_id');

        $data_pasien = isset($_GET['data_pasien']) ? $_GET['data_pasien'] : null;
        $data =  RujukanPulang::find()->where(['pendaftaran_id' => $id])->one();
        $pegawai_id = !empty($data) ? $data->pegawai_id : null;
        $petugas =  $this->getDataPegawaiByid($pegawai_id);
        
        $nama_pasien = !empty($data_pasien) && !empty($data_pasien['nama_pasien']) ? $data_pasien['nama_pasien'] : null;
        $no_rekam_medik = !empty($data_pasien) && !empty($data_pasien['no_rekam_medik']) ? $data_pasien['no_rekam_medik'] : null;
        $tanggal_lahir = !empty($data_pasien) && !empty($data_pasien['tanggal_lahir']) ? date('d-m-Y', strtotime($data_pasien['tanggal_lahir'])) : '-';
        $jenis_kelamin = !empty($data_pasien) && !empty($data_pasien['jenis_kelamin']) ? $data_pasien['jenis_kelamin'] : null;
        $nama_pegawai = !empty($data_pasien) && !empty($data_pasien['nama_pegawai']) ? $data_pasien['nama_pegawai'] : null;

        $profilRs = $this->getProfileRs();

        $print = new DocoPrint();

        try {
            $print->attributes = [
                '#title#' => $title,
    
                '#nama_pasien#' => $nama_pasien,
                '#no_rekam_medik#' => $no_rekam_medik,
                '#tanggal_lahir#' => $tanggal_lahir,
                '#jenis_kelamin#' => $jenis_kelamin,
                '#nama_pegawai#' => $nama_pegawai,
                
                '#rujukan_dituju#' => !empty($data) ? $data->rujukan_dituju : null,
                '#pic_rujukan_dituju#' => !empty($data) ? $data->pic_rujukan_dituju : null,
                '#diagnosa_masuk#' => !empty($data) ? $data->diagnosa_masuk : null,
                '#diagnosa_keluar#' => !empty($data) ? $data->diagnosa_keluar : null,
    
                '#anamnesis_keluhan_utama#' => !empty($data) ? $data->anamnesis_keluhan_utama : null,
                '#riwayat_penyakit_sekarang#' => !empty($data) ? $data->riwayat_penyakit_sekarang : null,
                '#riwayat_penyakit_dahulu#' => !empty($data) ? $data->riwayat_penyakit_dahulu : null,
                '#anamnesis_kesadaran#' => !empty($data) ? $data->anamnesis_kesadaran : null,
                '#ku#' => !empty($data) ? $data->anamnesis_keluhan_utama : null,
                '#anamnesis_saturasi_o2#' => !empty($data) ? $data->anamnesis_saturasi_o2 : null,
                '#anamnesis_tensi#' => !empty($data) ? $data->anamnesis_tensi : null,
                '#anamnesis_suhu#' => !empty($data) ? $data->anamnesis_suhu : null,
                '#anamnesis_nadi#' => !empty($data) ? $data->anamnesis_nadi : null,
                '#anamnesis_pernafasan#' => !empty($data) ? $data->anamnesis_pernafasan : null,
                '#alasan_dirujuk#' => !empty($data) ? $this->generateTextNewLine($data->alasan_dirujuk) : null,
                    
                '#pemeriksaan_penunjang#' => !empty($data) ? $this->generateTextNewLine($data->pemeriksaan_penunjang) : null,
                '#tindakan_medis#' => !empty($data) ? $this->generateTextNewLine($data->tindakan_medis) : null,
                '#tindakan_terapi#' => !empty($data) ? $this->generateTextNewLine($data->tindakan_terapi) : null,
                '#tindakan_lainnya#' => !empty($data) ? $this->generateTextNewLine($data->tindakan_lainnya) : null,
                
                '#derajat_0#' => !empty($data) ? $data->derajat_0 : null,
                '#derajat_1#' => !empty($data) ? $data->derajat_1 : null,
                '#derajat_2#' => !empty($data) ? $data->derajat_2 : null,
                '#derajat_3#' => !empty($data) ? $data->derajat_3 : null,
                
                '#tanggal_rujukan#' =>  !empty($data) ? date('d M Y', strtotime($data->tanggal_rujukan)) : null,
                '#jam#' =>  !empty($data) ? date('H:i', strtotime($data->tanggal_rujukan)) : null,
                '#keadaan_umum#' => !empty($data) ? $data->keadaan_umum : null,
                '#kesadaran#' => !empty($data) ? $data->kesadaran : null,
                '#tensi#' => !empty($data) ? $data->tensi : null,
                '#suhu#' => !empty($data) ? $data->suhu : null,
                '#saturasi_o2#' => !empty($data) ? $data->saturasi_o2 : null,
                '#nadi#' => !empty($data) ? $data->nadi : null,
                '#pernafasan#' => !empty($data) ? $data->pernafasan : null,
                '#catatan_penting#' => !empty($data) ? $this->generateTextNewLine($data->catatan_penting) : null,
                
                '#petugas#' => !empty($petugas) ? $petugas->nama_pegawai : null,
                '#nama_rumahsakit#' => $profilRs['namaRs'],
            ];
            $print->Output();
        } catch (\yii\db\Exception $e) {
            throw new \Exception($e->getMessage(), 1);
        } catch (\Exception $e) {
            return $e->getMessage();
            throw new \Exception($e->getMessage(), 1);
        }
    }

    private function getProfileRs()
    {
        $profilRs = Yii::$app->cache->getOrSet('profile-rs' , function ($cache) {
            return ProfilRsView::find()->asArray()->one();
        });

        $kota = $namaRs = '';
        $alamat = isset($profilRs['alamatlokasi_rumahsakit']) ? $profilRs['alamatlokasi_rumahsakit'] : '';
        $nomor_tlp = isset($profilRs['no_telp_profilrs']) ? $profilRs['no_telp_profilrs'] : '';

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
          
        return [
          'namaRs' => $namaRs,
          'kota' => $kota,
          'alamat' => $alamat,
          'nomor_tlp' => $nomor_tlp,
        ];
    }

    private function generateTextNewLine($string, $delimiter = null)
    {
        $newString = '';
        $delimiter = is_null($delimiter) ? "\r" : $delimiter;
        $decodeText = explode($delimiter, $string);
        for ($i=0; $i < count($decodeText); $i++) { 
            $string = trim(preg_replace('/\s\s+/', ' ', $decodeText[$i]));
            $tmp[$i] = "<p>". $string ."</p>\n";
        }
        $newString = implode($tmp);
        return $newString;
    }

    public function actionGetDataObatPrb() {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        $limit = $request->get('per-page', 10);
        $page = $request->get('page', 1);

        $bpjs = new Bpjs;


        $profilRs = Yii::$app->cache->getOrSet('profile-rs' , function ($cache) {
            return ProfilRsView::find()->asArray()->one();
        });

        $model = new InfoResepturDetailView;
        $query = $model::find()
            ->where([
                'pendaftaran_id' => $pendaftaran_id,
                'status_reseptur_id' => DocoConstants::RESEPTUR_DISERAHKAN,
            ]);
        $totalRecord = $query->count();

        if($limit == 0) {
            $listReseptuDetail = $query->asArray()->all();
        } else {
            $listReseptuDetail = $query->offset(($page - 1) * $limit)->limit($limit)->asArray()->all();
        }
        
        foreach ($listReseptuDetail as $index => $resepturDetail) {
            $refObat = $bpjs->refObatPRB($resepturDetail['obatalkes_nama']);
            $resepturDetail['is_bpjs'] = isset($refObat['response']['list']) && !empty($refObat['response']['list']); 
            $dataRef = isset($refObat['response']['list']) && !empty($refObat['response']['list']) ? 
                reset($refObat['response']['list']) : ['kode' => null];

            $resepturDetail['kode_bpjs'] = $dataRef['kode'];
            $listReseptuDetail[$index] = $resepturDetail;
        }

        return [
            'recordsFiltered' => $totalRecord,
            'recordsTotal' => $totalRecord,
            'data' => $listReseptuDetail,
        ];;
    }
}
