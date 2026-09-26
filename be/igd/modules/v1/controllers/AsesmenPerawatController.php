<?php
//Author: Ardi Pratama

// Namespace
namespace app\modules\v1\controllers;

// Using
use Yii;
use app\modules\v1\models\CpptView;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use Doco\components\DocoConstants;
use yii\db\Expression;

// Using model

use app\modules\v1\models\Cppt;
use app\modules\v1\models\RiwayatInstruksiTindakanView;
use app\modules\v1\models\InfoInstruksiView;
use app\modules\v1\models\InfoImplementasiView;
use app\modules\v1\models\Instruksi;
use app\modules\v1\models\InstruksiTindakan;
use app\modules\v1\models\InstruksiTindakanBmhp;
use app\modules\v1\models\Implementasi;
use app\modules\v1\models\TindakanPelayanan;
use app\modules\v1\models\ObatAlkesPasien;
use app\modules\v1\models\InfoStokObatAlkesView;
use app\modules\v1\models\RuanganPegawai;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\PaketDetailView;
use app\modules\v1\models\InfoPasienRdV;
use app\modules\v1\models\Gcs;
use app\modules\v1\models\MetodeGcs;
use app\modules\v1\models\RiwayatPemeriksaanFisik;
use app\modules\v1\models\AsesmenMedisRD;
use app\modules\v1\models\AsesmenPerawatRD;
use app\modules\v1\models\PeriksaTubuh;
use app\modules\v1\models\BagianTubuh;
use app\modules\v1\models\BagianTubuhDetail;
use app\modules\v1\models\Diagnosa;
use app\modules\v1\models\Lookup;
use app\modules\v1\models\BodyMassIndex;
use app\modules\v1\models\LookupKeperawatan;
use app\modules\v1\businessLogic\StokObatAlkes as LogicStokObatAlkes;
use app\modules\v1\payload\AskepPayload;

// Class
class AsesmenPerawatController extends DocoActiveController
{
	// Model class
	public $modelClass = 'app\modules\v1\models\AsesmenPerawatRD';

	// Verbs
	public function verbs()
	{
		// Parent
		$verbs = parent::verbs();

		// Return
		return $verbs;
	}

	// Acions
	public function actions()
	{
		// Parent
		$actions = parent::actions();

		// Unset actions
		unset($actions['index']);

		// Return
		return $actions;
	}

	public function actionBundleDataAsesmenPerawat()
	{
		try{
			$params = Yii::$app->request;
			$pendaftaran_id = $params->get('pendaftaran_id',0);
			$no_rm = $params->get('no_rm',null);
            $asesmen = $this->actionGetAsesment($pendaftaran_id);
            $data_rajal = $this->actionGetDataRajal($no_rm);
            $data_bmi = $this->getOrSetCache(DocoConstants::VAR_CACHE_BMI, BodyMassIndex::find());

            $gcs = $this->getDataGcs();
            $arr_lookupkeperawatan_type = ['jenis_asmen_perawat','keadaan_umum','asmen_nyeri'];
            $m_lookupkeperawatan = LookupKeperawatan::find()->where(['IN','lookup_type',$arr_lookupkeperawatan_type])->asArray()->all();
            $arr_lookup_type = ['pengantar'];
            $m_lookup= Lookup::find()->where(['IN','lookup_type',$arr_lookup_type])->asArray()->all();
            $data_lookup_keperawatan = $data_lookup = [];
            foreach ($m_lookupkeperawatan as $val_lookupkeperawatan) {
                $data_lookup_keperawatan[$val_lookupkeperawatan['lookup_type']][] = $val_lookupkeperawatan;
            }
            foreach ($m_lookup as $val_lookup) {
                $data_lookup[$val_lookup['lookup_type']][] = $val_lookup;
            }
			$result =  [
                'data-asesmen-perawat' => $asesmen,
                'data-rajal' => $data_rajal,
                'data-bmi' => $data_bmi,
                'data-jenisasesmen' => ArrayHelper::map($data_lookup_keperawatan['jenis_asmen_perawat'],'lookupkeperawatan_id','lookup_name'),
                'data-keadaanumum' => ArrayHelper::map($data_lookup_keperawatan['keadaan_umum'],'lookupkeperawatan_id','lookup_name'),
                'data-asmennyeri' => ArrayHelper::map($data_lookup_keperawatan['asmen_nyeri'],'lookupkeperawatan_id','lookup_name'),
                'data-pengantar' => ArrayHelper::map($data_lookup['pengantar'],'lookup_id','lookup_name'),
			];

			$result = array_merge($result,$gcs);
		} catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
            $result = [];
        } catch (\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
            $result = [];
        }
        return $result;
	}

    public function getDataGcs()
    {
        $data_gcs = $this->getOrSetCache(DocoConstants::VAR_CACHE_GCS_MASTER, Gcs::find()->orderBy(['gcs_nilaimin'=>SORT_ASC]));
        $data_metodegcs = $this->getOrSetCache(DocoConstants::VAR_CACHE_GCS_METODE, MetodeGcs::find());
        $gcsindicator_eye = DocoConstants::GCS_LIST_EYE;
        $gcsindicator_verbal = DocoConstants::GCS_LIST_VERBAL;
        $gcsindicator_motorik = DocoConstants::GCS_LIST_MOTORIK;
        $data_bagiantubuh = $this->getOrSetCache(DocoConstants::VAR_CACHE_BAGIANTUBUH, BagianTubuh::find());
        $data_getdetailbagiantubuh = $this->getOrSetCache(DocoConstants::VAR_CACHE_DETAILBAGIANTUBUH, BagianTubuhDetail::find());
        $data_listgcs = [];
        $data_detailbagiantubuh = [];

        foreach ($data_metodegcs as $key => $value) {
            if (!$value['metodegcs_nilai']){
                continue;
            }

            $value['nama_and_nilai'] = $value['metodegcs_nama'] . ' - ' . $value['metodegcs_nilai'];
            if ($value['metodegcs_singkatan'] == $gcsindicator_eye){
                $data_listgcs['eye'][] = $value;
            }elseif ($value['metodegcs_singkatan'] == $gcsindicator_verbal){
                $data_listgcs['verbal'][] = $value;
            }elseif ($value['metodegcs_singkatan'] == $gcsindicator_motorik){
                $data_listgcs['motorik'][] = $value;
            }
        }
        foreach ($data_getdetailbagiantubuh as $k => $v) {
            $data_detailbagiantubuh[$v['bagiantubuh_id']][$v['bagiantubuhdetail_id']] = $v['nama_bagiantubuh'];
        }

        return [
            'data-gcs' => $data_gcs,
            'data-listgcs' => $data_listgcs,
            'data-bagiantubuh' => $data_bagiantubuh,
            'data-detailbagiantubuh' => $data_detailbagiantubuh
        ];
    }

    public function actionSaveAsesmenPerawat()
    {
    	$request = Yii::$app->request;
        $post = $request->post();
        $asesmen = $post['AsesmenPerawatRDForm'];
        $result = [];
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
			$model = AsesmenPerawatRD::find()->where(['asesmenperawatrd_id'=>$asesmen['asesmenperawatrd_id']])->one();
            if(is_null($model)){
            	$model = new AsesmenPerawatRD();
            }
            $model->attributes = $asesmen;
            if(isset($asesmen['diagnosakerja_id'])){
            	$valDiag= [];
            	$splitVal = explode('_', $asesmen['diagnosakerja_id']);
            	if(count($splitVal)>1){
            		$valDiag['id'] = $splitVal[0];
                    $data_diag = Diagnosa::find()->where(['diagnosa_id'=>$splitVal[0]])->one();
                    if(isset($data_diag['diagnosa_kode'])){
                        $valDiag['kode'] = $data_diag['diagnosa_kode'];
                    }
            		$valDiag['text'] = $splitVal[1];
            	}
            	$model->diagnosakerja_id = json_encode($valDiag);
            	unset($asesmen['diagnosakerja_id']);
             }
            if(isset($asesmen['riwayat_dahulu']) && is_array($asesmen['riwayat_dahulu'])){
            	$valueRiwayatDahulu = [];
            	foreach ($asesmen['riwayat_dahulu'] as $key_riwayat) {
            		$valDahulu = [];
            		$splitVal = explode('_', $key_riwayat);
            		if(count($splitVal)>1){
	            		$valDahulu['id'] = $splitVal[0];
                        $data_diag = Diagnosa::find()->where(['diagnosa_id'=>$splitVal[0]])->one();
                        if(isset($data_diag['diagnosa_kode'])){
                            $valDahulu['kode'] = $data_diag['diagnosa_kode'];
                        }
	            		$valDahulu['text'] = $splitVal[1];
	            	}else{
	            		$valDahulu['text'] = $key_riwayat; 
	            	}
            		$valueRiwayatDahulu[] = $valDahulu;
            	}
                $model->riwayat_dahulu = null;
            	$model->riwayat_dahulu = json_encode($valueRiwayatDahulu);
            	unset($asesmen['riwayat_dahulu']);
            }
            if(!$model->validate()){
                throw new \yii\db\Exception('Gagal Validasi', $model->getErrors(),422);
            	
            }
            if(!$model->save()){
                throw new \yii\db\Exception('Gagal Simpan', $model->getErrors(),422);
            	
            }

            $transaction->commit();
            return [
                'message' => 'Data Berhasil di simpan',
                'status' => 200
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

            // \Yii::$app->response->statusCode = 422;
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
    * @controller actionCetakPdfAsesmenPerawat
    * @attribute #tgl_cetak# => tgl_cetak
    * @attribute #nama_user# => nama_user
    * @attribute #inf_norekammedik# => Informasi Pasien: No Rekam Medik
    * @attribute #inf_tglpendaftaran# => Informasi Pasien: Tanggal Pendaftaran
    * @attribute #inf_nopendaftaran# => Informasi Pasien: No Pendaftaran
    * @attribute #inf_namapasien# => Informasi Pasien: Nama Pasien
    * @attribute #inf_jeniskelamin# => Informasi Pasien: Jenis Kelamin
    * @attribute #inf_kasuspenyakit# => Informasi Pasien: Kasus Penyakit
    * @attribute #inf_tgllahir# => Informasi Pasien: Tanggal Lahir
    * @attribute #inf_umur# => Informasi Pasien: Umur
    * @attribute #inf_dokterjaga# => Informasi Pasien: Dokter Jaga
    * @attribute #inf_kelaspelayanan# => Informasi Pasien: Kelas Pelayanan
    * @attribute #inf_nokamar# => Informasi Pasien: No Kamar
    * @attribute #inf_nobed# => Informasi Pasien: No Bed
    * @attribute #inf_penjamin# => Informasi Pasien: Penjamin
    * @attribute #inf_carabayar# => Informasi Pasien: Cara Bayar
    * @attribute #inf_ruangan# => Informasi Pasien: Ruangan
    **/
    public function actionCetakPdfAsesmenPerawat()
    {
        $connection = Yii::$app->db;
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id',0);
        $ruangan_id = $request->get('ruangan_id',0);
        $modelHeader = new InfoPasienRdV;
        $queryHeader = $modelHeader::find()
            ->andWhere([
                'pendaftaran_id'=>$request->get('pendaftaran_id')
            ]);
        $resultHeader = $queryHeader->asArray()->one();

        $nama_usercetak = $request->get('nama_usercetak','');
        $id_usercetak = $request->get('id_usercetak',0);

        $nama_user = '';
        $mNamaPegawai = Pegawai::find(true)->where(['pegawai_id'=>$id_usercetak])->asArray()->one();
        
        if(is_null($mNamaPegawai)){
            $nama_user = $nama_usercetak;
        }else{
            $nama_user = @$mNamaPegawai['nama_pegawai'];
        }


        $diagnosa_kerja = new Expression("
                diagnosakerja_id::json->>'text' AS diagnosa_kerja");
        $riwayat_dahulu = new Expression("CASE WHEN riwayat_dahulu IS NULL THEN array_to_json(null::character varying[]) ELSE
                array_to_json(ARRAY(select json_array_elements(riwayat_dahulu::json)->>'text' from asesmenmedisrd_t inasesmen where inasesmen.asesmenmedisrd_id = asesmenmedisrd_t.asesmenmedisrd_id) ) END AS riwayat_dahulu");
        $queryCetak = (new \yii\db\Query())
                ->select([
                    'asesmenmedisrd_t.asesmenmedisrd_id',
                    'pegawai_m.nama_pegawai AS dokter_nama',
                    'asesmenmedisrd_t.tgl_asesmen',
                    'asesmenmedisrd_t.riwayat',
                    'gcseye.metodegcs_nama AS gcseye_nama',
                    'gcsverbal.metodegcs_nama AS gcsverbal_nama',
                    'gcsmotorik.metodegcs_nama AS gcsmotorik_nama',
                    'asesmenmedisrd_t.hasil_gcs',
                    'jenisasesmen.lookup_name AS jenisasesmen_nama',
                    'asesmenmedisrd_t.is_kapitis',
                    $diagnosa_kerja,
                    $riwayat_dahulu,
                ])
                ->from('asesmenmedisrd_t')
                ->leftJoin('pegawai_m','asesmenmedisrd_t.dokter_id = pegawai_m.pegawai_id')
                ->leftJoin('metodegcs_m gcseye','gcseye.metodegcs_id = asesmenmedisrd_t.gcseye_id')
                ->leftJoin('metodegcs_m gcsverbal','gcsverbal.metodegcs_id = asesmenmedisrd_t.gcsverbal_id')
                ->leftJoin('metodegcs_m gcsmotorik','gcsmotorik.metodegcs_id = asesmenmedisrd_t.gcsmotorik_id')
                ->leftJoin('lookupkeperawatan_m jenisasesmen','jenisasesmen.lookupkeperawatan_id = asesmenmedisrd_t.jenis_asmenperawat')
                ->where(['asesmenmedisrd_t.pendaftaran_id' => $pendaftaran_id]);
        $asesmen_dokter = $queryCetak->one();

        $print = new DocoPrint();
        $print->attributes = [
            '#asesmen_dokternama#' => $asesmen_dokter ? @$asesmen_dokter['dokter_nama'] : '',
            '#asesmen_tglasesmen#' => $asesmen_dokter ? @$asesmen_dokter['tgl_asesmen'] : '',
            '#asesmen_jenisasesmen#' => $asesmen_dokter ? @$asesmen_dokter['jenisasesmen_nama'] : '',
            '#asesmen_riwayat#' => $asesmen_dokter ? @$asesmen_dokter['riwayat'] : '',
            '#asesmen_riwayatdahulu#' => $asesmen_dokter ? @$asesmen_dokter['riwayat_dahulu'] : '',
            '#asesmen_diagnosakerja#' => $asesmen_dokter ? @$asesmen_dokter['diagnosa_kerja'] : '',
            '#gcs_eye#' => $asesmen_dokter ? @$asesmen_dokter['gcseye_nama'] : '',
            '#gcs_verbal#' => $asesmen_dokter ? @$asesmen_dokter['gcsverbal_nama'] : '',
            '#gcs_motorik#' => $asesmen_dokter ? @$asesmen_dokter['gcsmotorik_nama'] : '',
            '#gcs_hasil#' => $asesmen_dokter ? @$asesmen_dokter['hasil_gcs'] : '',
            '#inf_norekammedik#' => $resultHeader ? $resultHeader['no_rekam_medik'] : '',
            '#inf_tglpendaftaran#' => $resultHeader ? ($resultHeader['tgl_pendaftaran'] ? date('d-m-Y', strtotime($resultHeader['tgl_pendaftaran'])) : '') : '',
            '#inf_nopendaftaran#' => $resultHeader ? $resultHeader['no_pendaftaran'] : '',
            '#inf_namapasien#' => $resultHeader ? $resultHeader['nama_pasien'] : '',
            '#inf_jeniskelamin#' => $resultHeader ? $resultHeader['jenis_kelamin'] : '',
            '#inf_kasuspenyakit#' => $resultHeader ? $resultHeader['jeniskasuspenyakit_nama'] : '',
            '#inf_tgllahir#' => $resultHeader ? ($resultHeader['tanggal_lahir'] ? date('d-m-Y', strtotime($resultHeader['tanggal_lahir'])) : '') : '',
            '#inf_umur#' => $resultHeader ? $resultHeader['umur'] : '',
            '#inf_dokterjaga#' => $resultHeader ? $resultHeader['dokter_jaga'] : '',
            '#inf_kelaspelayanan#' => $resultHeader ? $resultHeader['kelaspelayanan_nama'] : '',
            '#inf_penjamin#' => $resultHeader ? $resultHeader['penjamin_nama'] : '',
            '#inf_carabayar#' => $resultHeader ? $resultHeader['carabayar_nama'] : '',
            '#inf_ruangan#' => $resultHeader ? $resultHeader['ruangan_nama'] : '',
            '#nama_user#' => $nama_user,
            '#tgl_cetak#' => date('d F Y H:i:s'),
        ];
        $print->Output();
    }

    public function actionGetAsesment($pendaftaran_id)
    {
        try {
            $result = [];
            $request = Yii::$app->request;
            if($pendaftaran_id) {
                $result = AsesmenPerawatRD::find()
                    ->where(['pendaftaran_id' => $pendaftaran_id])->one();
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

        return $result;
    }

    private function actionGetDataRajal($no_rm)
    {
        try {
            $result = [];
            $request = Yii::$app->request;
            if($no_rm) {
                $result = RiwayatPemeriksaanFisik::find()
                    ->where(['no_rekam_medik' => $no_rm])->one();
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
        return $result;
    }

    public function actionSave()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $asesment = isset($post['AsesmenPerawatRDForm']) ? $post['AsesmenPerawatRDForm'] : $post;
        $pendaftaran_id = isset($asesment['pendaftaran_id']) ? $asesment['pendaftaran_id'] : null;
        $ruangan_id = isset($asesment['ruangan_id']) ? $asesment['ruangan_id'] : null;
        $perawat_id = isset($asesment['perawat_id']) ? $asesment['perawat_id'] : null;

        $asesment['pendaftaran_id'] = $pendaftaran_id;
        $asesment['ruangan_id'] = $ruangan_id;
        $asesment['tgl_asesmen'] = !empty($asesment['tgl_asesmen']) ? date('Y-m-d H:i:s', strtotime($asesment['tgl_asesmen'])) : null;
        $asesment['tgl_pendaftaran'] = !empty($asesment['tgl_pendaftaran']) ? date('Y-m-d H:i:s', strtotime($asesment['tgl_pendaftaran'])) : null;
        $asesment['tgl_datang'] = !empty($asesment['tgl_datang']) ? date('Y-m-d H:i:s', strtotime($asesment['tgl_datang'])) : null;

        $asesment['perawat_id'] = $perawat_id;
        
        $result = [];
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $modelPayload = new AskepPayload;
            $modelPayload->attributes = $asesment;
            // return $modelPayload->skala_nyeri;
            // return $modelPayload->validate();
            if($modelPayload->validate()) {
                $model = AsesmenPerawatRD::find()->where(['pendaftaran_id' => $pendaftaran_id])->one();
                
                if(is_null($model)){
                    $model = new AsesmenPerawatRD();
                }
                
                $model->attributes = $asesment;

                // formulir triage
                $tgl_pendaftaran = isset($asesment['tgl_pendaftaran']) ? date('Y-m-d H:i:s', strtotime($asesment['tgl_pendaftaran'])) : null;
                $tgl_datang = isset($asesment['tgl_datang']) ? date('Y-m-d H:i:s', strtotime($asesment['tgl_datang'])) : null;
                $prioritas_triage = isset($asesment['prioritas_triage']) ? $asesment['prioritas_triage'] : null;
                $pasien_datang = isset($asesment['pasien_datang']) ? $asesment['pasien_datang'] : null;
                $jenis_asmenperawat = isset($asesment['jenis_asmenperawat']) ? $asesment['jenis_asmenperawat'] : null;
                $alasan_kunjungan = isset($asesment['alasan_kunjungan']) ? $asesment['alasan_kunjungan'] : null;
                $keadaan_umum = isset($asesment['keadaan_umum']) ? $asesment['keadaan_umum'] : null;
                $is_alergi = isset($asesment['is_alergi']) ? $asesment['is_alergi'] : null;
                $is_nyeri = isset($asesment['is_nyeri']) ? $asesment['is_nyeri'] : null;
                $skala_nyeri = isset($asesment['skala_nyeri']) ? $asesment['skala_nyeri'] : null;
                $metode_nyeri = isset($asesment['metode_nyeri']) ? $asesment['metode_nyeri'] : null;
                $is_resikojatuh = isset($asesment['is_resikojatuh']) ? $asesment['is_resikojatuh'] : null;

                $formulir_triage = [
                    'tgl_pendaftaran' => $tgl_pendaftaran,
                    'tgl_datang' => $tgl_datang,
                    'prioritas_triage' => $prioritas_triage,
                    'pasien_datang' => $pasien_datang,
                    'jenis_asmenperawat' => $jenis_asmenperawat,
                    'alasan_kunjungan' => $alasan_kunjungan,
                    'keadaan_umum' => $keadaan_umum,
                    'is_alergi' => $is_alergi,
                    'is_nyeri' => $is_nyeri,
                    'skala_nyeri' => $skala_nyeri,
                    'metode_nyeri' => $metode_nyeri,
                    'is_resikojatuh' => $is_resikojatuh,
                ];
                
                // formulir fisik
                $keluhan = isset($asesment['keluhan']) ? $asesment['keluhan'] : null;
                $lama_sakit = isset($asesment['lama_sakit']) ? $asesment['lama_sakit'] : null;
                $r_penyakitdahulu = isset($asesment['r_penyakitdahulu']) ? $asesment['r_penyakitdahulu'] : null;

                $valueRiwayatDahulu = [];
                if(!empty($r_penyakitdahulu) && is_array($r_penyakitdahulu)){
                    foreach ($r_penyakitdahulu as $key_riwayat) {
                        $valDahulu = [];
                        $splitVal = explode('_', $key_riwayat);
                        if(count($splitVal)>1){
                            $valDahulu['id'] = $splitVal[0];
                            $data_diag = Diagnosa::find()->where(['diagnosa_id'=>$splitVal[0]])->one();
                            if(isset($data_diag['diagnosa_kode'])){
                                $valDahulu['kode'] = $data_diag['diagnosa_kode'];
                                $valDahulu['nama'] = $data_diag['diagnosa_nama'];
                            }
                            $valDahulu['text'] = $splitVal[1];
                        }else{
                            $valDahulu['text'] = $key_riwayat; 
                        }
                        $valueRiwayatDahulu[] = $valDahulu;
                    }
                    $model->r_penyakitdahulu = json_encode($valueRiwayatDahulu);
                    unset($asesment['r_penyakitdahulu']);
                }
                
                $r_penyakitkeluarga = isset($asesment['r_penyakitkeluarga']) ? $asesment['r_penyakitkeluarga'] : null;
                $catatan_asesmen = isset($asesment['catatan_asesmen']) ? $asesment['catatan_asesmen'] : null;
                $gcseye_id = isset($asesment['gcseye_id']) ? $asesment['gcseye_id'] : null;
                $gcsverbal_id = isset($asesment['gcsverbal_id']) ? $asesment['gcsverbal_id'] : null;
                $gcsmotorik_id = isset($asesment['gcsmotorik_id']) ? $asesment['gcsmotorik_id'] : null;
                $nilai_gcs = isset($asesment['nilai_gcs']) ? $asesment['nilai_gcs'] : null;
                $is_kapitis = isset($asesment['is_kapitis']) ? $asesment['is_kapitis'] : null;
                $hasil_gcs = isset($asesment['hasil_gcs']) ? $asesment['hasil_gcs'] : null;
                $tekanan_darah = isset($asesment['tekanan_darah']) ? $asesment['tekanan_darah'] : null;
                $td_systolic = isset($asesment['td_systolic']) ? $asesment['td_systolic'] : null;
                $td_diastolic = isset($asesment['td_diastolic']) ? $asesment['td_diastolic'] : null;
                $hasil_td = isset($asesment['hasil_td']) ? $asesment['hasil_td'] : null;
                $detak_nadi = isset($asesment['detak_nadi']) ? $asesment['detak_nadi'] : null;
                $pernapasan = isset($asesment['pernapasan']) ? $asesment['pernapasan'] : null;
                $suhu_tubuh = isset($asesment['suhu_tubuh']) ? $asesment['suhu_tubuh'] : null;
                $tinggi_badan = isset($asesment['tinggi_badan']) ? $asesment['tinggi_badan'] : null;
                $berat_badan = isset($asesment['berat_badan']) ? $asesment['berat_badan'] : null;
                $bb_ideal = isset($asesment['bb_ideal']) ? $asesment['bb_ideal'] : null;
                $imt = isset($asesment['imt']) ? $asesment['imt'] : null;
                $ket_imt = isset($asesment['ket_imt']) ? $asesment['ket_imt'] : null;
                $spo2 = isset($asesment['spo2']) ? $asesment['spo2'] : null;
                $kelaianan_tubuh = isset($asesment['kelaianan_tubuh']) ? $asesment['kelaianan_tubuh'] : null;

                $formulir_fisik = [
                    'keluhan' => $keluhan,
                    'lama_sakit' => $lama_sakit,
                    'r_penyakitdahulu' => $valueRiwayatDahulu,
                    'r_penyakitkeluarga' => $r_penyakitkeluarga,
                    'catatan_asesmen' => $catatan_asesmen,
                    'gcseye_id' => $gcseye_id,
                    'gcsverbal_id' => $gcsverbal_id,
                    'gcsmotorik_id' => $gcsmotorik_id,
                    'nilai_gcs' => $nilai_gcs,
                    'is_kapitis' => $is_kapitis,
                    'hasil_gcs' => $hasil_gcs,
                    'tekanan_darah' => $tekanan_darah,
                    'td_systolic' => $td_systolic,
                    'td_diastolic' => $td_diastolic,
                    'hasil_td' => $hasil_td,
                    'detak_nadi' => $detak_nadi,
                    'pernapasan' => $pernapasan,
                    'suhu_tubuh' => $suhu_tubuh,
                    'tinggi_badan' => $tinggi_badan,
                    'berat_badan' => $berat_badan,
                    'bb_ideal' => $bb_ideal,
                    'imt' => $imt,
                    'ket_imt' => $ket_imt,
                    'spo2' => $spo2,
                    'kelaianan_tubuh' => $kelaianan_tubuh,
                ];

                $model->formulir_triage = json_encode($formulir_triage);
                $model->formulir_fisik = json_encode($formulir_fisik);
                // return $model->validate();
                if(!$model->validate()) {
                    \Yii::$app->response->statusCode = 422;
                    return [
                        'data' => $model->errors,
                        'status' => 422,
                    ];
                }

                $model->save();
                $transaction->commit();
                return [
                    'message' => 'Data Berhasil di simpan',
                    'status' => 200
                ];
            }
            else {
                return [
                    'data' => $modelPayload->errors,
                    'status' => 422,
                ];
            }
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        } catch (\yii\db\Exception $e){
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            return [
                'message'=>$e->getMessage()
            ];
        }
    }
}