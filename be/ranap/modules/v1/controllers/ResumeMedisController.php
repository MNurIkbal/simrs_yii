<?php
//Author: Ardi Pratama

// Namespace
namespace app\modules\v1\controllers;

// Using
use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoPrint;

// Using model
use app\modules\v1\models\ResumeMedisRIT;
use app\modules\v1\models\InfoPasienRiView;
use app\modules\v1\models\Pegawai;
use app\modules\v1\models\AsesmenMedis;
use app\modules\v1\models\HasilPemeriksaanLab;

// Class
class ResumeMedisController extends DocoActiveController
{
	// Model class
	public $modelClass = 'app\modules\v1\models\ResumeMedisRIT';

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

	public function actionGetBundleDataResumeMedis()
	{
		$request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        $pasienadmisi_id = $request->get('pasienadmisi_id');

        $data_resume_medis = ResumeMedisRIT::find()->where(['pendaftaran_id'=>$pendaftaran_id,'pasienadmisi_id'=>$pasienadmisi_id])->one();
    	$data_asesmen_medis = AsesmenMedis::find()->where(['pendaftaran_id'=>$pendaftaran_id, 'pasienadmisi_id'=>$pasienadmisi_id])->one();

        // mengambil data diagnosa_id dalam bentuk array
        $diagnosa = $data_asesmen_medis['diagnosa_id'];
        // if (!empty($data_asesmen_medis)) {
        //     if ($data_asesmen_medis['diagnosa_id'] != '') {
        //         // $diagnosa = DiagnosaView::find()->where(['diagnosa_id' => $data_asesmen_medis['diagnosa_id']])->one();

        //         // if ($diagnosa != '') {
        //         //     $diagnosa = $diagnosa['diagnosa_id'].'_'.$diagnosa['diagnosa_kode'].' - '.$diagnosa['diagnosa_nama'];
        //         // }

        //         // $dataDiag = json_decode($data_asesmen_medis['diagnosa_id'], true);
        //         if (isset($dataDiag['text'])) {
        //             $diagnosa = $dataDiag['text'];
        //         }
        //     }
        // }

        return [
            'data_resume_medis' => $data_resume_medis,
            'list-carakeluar' => $this->getDataCaraKeluar(),
            'list-kondisikeluar' => $this->getDataKondisiKeluar(),
            'cppt_info' => $this->getDataCpptInstruksiPulang($pendaftaran_id,$pasienadmisi_id),
            'data_obat_bawa_pulang' => $this->getObatDibawaPulang($pendaftaran_id,$pasienadmisi_id),
            'data_obat_approved' => $this->getObatApproved($pendaftaran_id,$pasienadmisi_id),
            'data_diagnosa_masuk' => $diagnosa
    	];
	}

	public function actionCreateResumeMedis()
	{
        $request = Yii::$app->request;
		try{
			$datapost = $request->post('ResumeMedisForm',[]);
			if(!$datapost){
				throw new \yii\base\Exception("Error Processing Request", 1);		
			}
            $pendaftaran_id = $datapost['pendaftaran_id'];
            $pasienadmisi_id = $datapost['pasienadmisi_id'];
			$model = ResumeMedisRIT::find()->where(['pendaftaran_id'=>$datapost['pendaftaran_id'],'pasienadmisi_id'=>$datapost['pasienadmisi_id']])->one();
            $isNew = false;
            if(is_null($model)){
                $model = new ResumeMedisRIT;
                $isNew = true;
            }

            if(isset($datapost['diag_penyerta']) && is_array($datapost['diag_penyerta'])){
            	$valueDiagPenyerta = [];
            	foreach ($datapost['diag_penyerta'] as $key_diag) {
            		$valDiag = [];
            		$splitVal = explode('_', $key_diag);
            		if($splitVal && count($splitVal)>1){
	            		$valDiag['id'] = $splitVal[0];
                        // $data_diag = Diagnosa::find()->where(['diagnosa_id'=>$splitVal[0]])->one();
                        $splitPenyerta = explode('-', $splitVal[1]);
                        // if(isset($data_diag['diagnosa_kode'])){
                        if(count($splitPenyerta)>1){
                            $valDiag['kode'] = $splitPenyerta[0];
                            $valDiag['nama'] = $splitPenyerta[1];
                        }
	            		$valDiag['text'] = $splitVal[1];
	            	}else{
	            		$valDiag['text'] = $key_diag; 
	            	}
            		$valueDiagPenyerta[] = $valDiag;
            	}
                $model->diag_penyerta = null;
            	$model->diag_penyerta = ($valueDiagPenyerta);
            	unset($datapost['diag_penyerta']);
            }

            if(isset($datapost['diag_utama'])){
            	$valDiag= [];
            	$splitVal = explode('_', $datapost['diag_utama']);
            	if($splitVal && count($splitVal)>1){
            		$valDiag['id'] = $splitVal[0];
                    // $data_diag = Diagnosa::find()->where(['diagnosa_id'=>$splitVal[0]])->one();
                    $splitUtama = explode('-', $splitVal[1]);
                    if(count($splitUtama)>1){
                        $valDiag['kode'] = $splitUtama[0];
                        $valDiag['nama'] = $splitUtama[1];
                    }
            		$valDiag['text'] = $splitVal[1];
            	}else{
                    $valDiag['text'] = $datapost['diag_utama'];
                }
            	$model->diag_utama = ($valDiag);
            	unset($datapost['diag_utama']);
             }

            if(isset($datapost['diag_masuk'])){
            	$valDiag= [];
            	$splitVal = explode('_', $datapost['diag_masuk']);
            	if($splitVal && count($splitVal)>1){
            		$valDiag['id'] = $splitVal[0];
                    // $data_diag = Diagnosa::find()->where(['diagnosa_id'=>$splitVal[0]])->one();
                    $splitMasuk = explode('-', $splitVal[1]);
                    if(count($splitMasuk)>1){
                        $valDiag['kode'] = $splitMasuk[0];
                        $valDiag['nama'] = $splitMasuk[1];
                    }
                    $valDiag['text'] = $splitVal[1];
            	}else{
                    $valDiag['text'] = $datapost['diag_masuk'];
                }
            	$model->diag_masuk = ($valDiag);
            	unset($datapost['diag_masuk']);
             }

            if(isset($datapost['prosedur_diag']) && is_array($datapost['prosedur_diag'])){
                $valueProdDiag = [];
                foreach ($datapost['prosedur_diag'] as $key_diag) {
                    $valDiag = [];
                    $splitVal = explode('_', $key_diag);
                    if($splitVal && count($splitVal)>1){
                        $valDiag['id'] = $splitVal[0];
                        // $data_diag = Diagnosa::find()->where(['diagnosa_id'=>$splitVal[0]])->one();
                        $splitPros = explode('-', $splitVal[1]);
                        if(count($splitPros)>1){
                            $valDiag['kode'] = $splitPros[0];
                            $valDiag['nama'] = $splitPros[1];
                        }
                        $valDiag['text'] = $splitVal[1];
                    }else{
                        $valDiag['text'] = $key_diag; 
                    }
                    $valueProdDiag[] = $valDiag;
                }
                $model->prosedur_diag = null;
                $model->prosedur_diag = ($valueProdDiag);
                unset($datapost['prosedur_diag']);
            }
            if(isset($datapost['tgl_masuk'])){
                $model->tgl_masuk = date('Y-m-d H:i:s',strtotime($datapost['tgl_masuk']));
                unset($datapost['tgl_masuk']);
            }
            if(isset($datapost['tgl_keluar'])){
                $model->tgl_keluar = date('Y-m-d H:i:s',strtotime($datapost['tgl_keluar']));
                unset($datapost['tgl_keluar']);
            }
            $model->attributes = $datapost;
            // $model->pendaftaran_id = 
            // $model->pasienadmisi_id = 
            // $model->tgl_masuk = 
            // $model->tgl_keluar
            // $model->diag_masuk
            // $model->diag_utama
            // $model->diag_penyerta
            // $model->a_f_bermakna
            // $model->prosedur_diag
            // $model->tatalaksana_obat
            // $model->is_rotd
            // $model->obat_rotd
            // $model->kondisipulang_id
            // $model->kondisi_lain
            // $model->obat_pulang
            // $model->kontrol_ke
            // $model->tgl_kontrol
            // $model->rencana_tindak_lanjut
            $data_obat_bawa_pulang = $this->getObatDibawaPulang($pendaftaran_id,$pasienadmisi_id);
            if($data_obat_bawa_pulang && count($data_obat_bawa_pulang)>0){
                $model->obat_pulang = json_encode($data_obat_bawa_pulang);
            }
            $data_obat_tatalaksana = $this->getObatApproved($pendaftaran_id,$pasienadmisi_id);
            if($data_obat_tatalaksana && count($data_obat_tatalaksana)>0){
                $model->tatalaksana_obat = json_encode($data_obat_tatalaksana);
            }

            if(!$model->validate()){
				\Yii::$app->response->statusCode = 422;
                return [
                    'data' => $model->errors,
                    'status' => 422,
                ];
            }
            if($isNew){
                if(!$model->save()){
            		\Yii::$app->response->statusCode = 422;
	                return [
	                    'data' => $model->errors,
	                    'status' => 422,
	                ];
                }
            }else{
                if(!$model->update()){
            		\Yii::$app->response->statusCode = 422;
	                return [
	                    'data' => $model->errors,
	                    'status' => 422,
	                ];
                }
            }

            return [
                'message'=>'Proses Berhasil!',
                'text' => 'Data telah disimpan ',
            ];
		} catch (\yii\db\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        } catch (\yii\base\Exception $e) {
            \Yii::$app->response->statusCode = 500;
            return [
                'message' => $e->getMessage()
            ];
        }
	}

    private function getDataCaraKeluar()
    {
        $sql = "select carakeluar_id, carakeluar_nama from carakeluar_m where is_deleted = false and is_active = true
            order by carakeluar_id asc 
        "; 
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        return $data;
    }

    private function getDataKondisiKeluar()
    {
        $sql = "select kondisikeluar_id, kondisikeluar_nama, carakeluar_id from kondisikeluar_m where is_deleted = false and is_active = true
        group by kondisikeluar_id
        order by kondisikeluar_id asc 
        "; 
        $data = Yii::$app->db->createCommand($sql)->queryAll();
        return $data;
    }

    private function getDataCpptInstruksiPulang($pendaftaran_id,$pasienadmisi_id)
    {
        $sqlExist = "SELECT EXISTS( SELECT *
                FROM cppt_t 
                WHERE is_instruksi_pulang = TRUE 
                AND is_instruksi_pulang IS NOT NULL 
                AND pasienadmisi_id = '{$pasienadmisi_id}' AND pendaftaran_id = '{$pendaftaran_id}' 
                ORDER BY cppt_id DESC) AS is_exist
        "; 
        $is_instruksi_pulang = Yii::$app->db->createCommand($sqlExist)->queryOne();
        $sql = "SELECT *
                FROM cppt_t 
                WHERE is_instruksi_pulang = TRUE 
                AND is_instruksi_pulang IS NOT NULL 
                AND pasienadmisi_id = '{$pasienadmisi_id}' AND pendaftaran_id = '{$pendaftaran_id}' 
                ORDER BY created_date DESC LIMIT 1
        "; 
        $data = null;
        if($is_instruksi_pulang == TRUE){
            $data = Yii::$app->db->createCommand($sql)->queryOne();
        }
        return [
            'is_instruksi_pulang' => $is_instruksi_pulang['is_exist'],
            'data_cppt' => $data
        ];
    }

    public function actionObatDibawaPulang($pendaftaran_id,$pasienadmisi_id)
    {
        return $this->getObatDibawaPulang($pendaftaran_id,$pasienadmisi_id);
    }

    private function getObatDibawaPulang($pendaftaran_id,$pasienadmisi_id)
    {
        $data_reseptur = [];
        $sqlExist = "SELECT EXISTS( SELECT *
                FROM cppt_t 
                WHERE is_instruksi_pulang = TRUE 
                AND is_instruksi_pulang IS NOT NULL 
                AND pasienadmisi_id = '{$pasienadmisi_id}' AND pendaftaran_id = '{$pendaftaran_id}' 
                ORDER BY cppt_id DESC) AS is_exist
        "; 
        $is_instruksi_pulang = Yii::$app->db->createCommand($sqlExist)->queryOne();

        if($is_instruksi_pulang['is_exist'] == TRUE){
            $data_reseptur = (new \yii\db\Query())
                    ->select([
                        'inforesepturdetail_v.racikan_nama',
                        'inforesepturdetail_v.rke',
                        'inforesepturdetail_v.obatalkes_nama',
                        'inforesepturdetail_v.satuan_kecil',
                        'inforesepturdetail_v.signa_nama',
                        'inforesepturdetail_v.qty_reseptur',
                    ])
                    ->from('infoinstruksi_v')
                    ->leftJoin('cppt_t','cppt_t.cppt_id = infoinstruksi_v.cppt_id')
                    ->leftJoin('inforesepturdetail_v','inforesepturdetail_v.resepturdetail_id = infoinstruksi_v.instruksitindakan_id')
                    ->where([
                        'cppt_t.is_instruksi_pulang' => TRUE,
                        'cppt_t.pasienadmisi_id' => $pasienadmisi_id,
                        'cppt_t.pendaftaran_id' => $pendaftaran_id,
                        'infoinstruksi_v.grouping_tipe' => 'RESEPTUR',
                        'infoinstruksi_v.is_telah_implementasi' => TRUE,
                        'infoinstruksi_v.tindakan_deleted' => FALSE,
                        'infoinstruksi_v.instruksi_deleted' => FALSE
                    ])
                    ->all();
        }

        return $data_reseptur;
    }

    public function actionObatApprove($pendaftaran_id,$pasienadmisi_id)
    {
        return $this->getObatApproved($pendaftaran_id,$pasienadmisi_id);
    }

    public function getObatApproved($pendaftaran_id,$pasienadmisi_id)
    {
        $data_reseptur = (new \yii\db\Query())
            ->select([
                'inforesepturdetail_v.obatalkes_nama',
            ])
            ->from('infoinstruksi_v')
            ->leftJoin('cppt_t','cppt_t.cppt_id = infoinstruksi_v.cppt_id')
            ->leftJoin('inforesepturdetail_v','inforesepturdetail_v.resepturdetail_id = infoinstruksi_v.instruksitindakan_id')
            ->leftJoin('reseptur_t','reseptur_t.reseptur_id = inforesepturdetail_v.reseptur_id')
            ->where([
                'cppt_t.pasienadmisi_id' => $pasienadmisi_id,
                'cppt_t.pendaftaran_id' => $pendaftaran_id,
                'infoinstruksi_v.grouping_tipe' => 'RESEPTUR',
                'infoinstruksi_v.tindakan_deleted' => FALSE,
                'infoinstruksi_v.instruksi_deleted' => FALSE,
                'reseptur_t.status_reseptur' => 347
            ])
            ->groupBy(['inforesepturdetail_v.obatalkes_nama'])
            ->all();
            return $data_reseptur;
    }

    /**
    * @controller actionCetakPdfResumeMedis
    * @attribute #resume_tanggalmasuk# => Resume Medis: Tanggal Masuk
    * @attribute #resume_tanggalkeluar# => Resume Medis: Tanggal Keluar
    * @attribute #resume_diagmasuk# => Resume Medis: Diagnosa Masuk
    * @attribute #resume_diagutama# => Resume Medis: Diagnosa Utama
    * @attribute #resume_diagpenyerta# => Resume Medis: Diagnosa Penyerta
    * @attribute #resume_afbermakna# => Resume Medis: A F bermakna
    * @attribute #resume_prosedurdiag# => Resume Medis: Prosedur Diagnosa
    * @attribute #resume_kondisi_pulang# => Resume Medis: Kondisi Pulang
    * @attribute #resume_kontrolke# => Resume Medis: Kontrol Ke
    * @attribute #resume_tglkontrol# => Resume Medis: Tanggal Kontrol
    * @attribute #resume_rencanatindaklanjut# => Resume Medis: Rencana Tindak Lanjut
    * @attribute #resume_tbl_obatdibawapulang# => Resume Medis: Tabel Obat Dibawa Pulang
    * @attribute #resume_tbl_tatalaksana# => Resume Medis: Tabel Penatalaksanaan Obat Selama di RS
    * @attribute #no_pendaftaran# => no_pendaftaran
    * @attribute #no_rekam_medik# => no rekam medik
    * @attribute #nama_pasien# => nama_pasien
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
    * @attribute #inf_dokterdpjp# => Informasi Pasien: Dokter DPJP
    * @attribute #inf_kelaspelayanan# => Informasi Pasien: Kelas Pelayanan
    * @attribute #inf_nokamar# => Informasi Pasien: No Kamar
    * @attribute #inf_nobed# => Informasi Pasien: No Bed
    * @attribute #inf_penjamin# => Informasi Pasien: Penjamin
    * @attribute #inf_carabayar# => Informasi Pasien: Cara Bayar
    * @attribute #inf_ruangan# => Informasi Pasien: Ruangan
    **/
    public function actionCetakPdfResumeMedis()
    {
        $connection = Yii::$app->db;
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id',0);
        $ruangan_id = $request->get('ruangan_id',0);
        $pasienadmisi_id = $request->get('pasienadmisi_id',0);
        $nama_user = $request->get('nama_user','');
        $modelHeader = new InfoPasienRiView;
        $queryHeader = $modelHeader::find()
            ->andWhere([
                'pendaftaran_id'=>$request->get('pendaftaran_id'),
                'pasienadmisi_id'=>$request->get('pasienadmisi_id'),
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

        $data_resume_medis = ResumeMedisRIT::find(true)->where(['pendaftaran_id'=>$pendaftaran_id,'pasienadmisi_id'=>$pasienadmisi_id])->one();

        $data_diagnosa = false;
        if(is_null($data_resume_medis)){
            $data_resume_medis = new ResumeMedisRIT;
        }else{
            $query_diagnosa = "SELECT 
                diag_masuk::json->>'text' AS diagnosa_masuk, 
                diag_utama::json->>'text' AS diagnosa_utama,
                -- ambil data diag_penyerta dengan format json tanpa di format dulu
                diag_penyerta AS diagnosa_penyerta,
                -- query lama
                -- CASE WHEN diag_penyerta::json->>'text' IS NULL THEN array_to_json(null::character varying[]) ELSE
                -- array_to_json(ARRAY(select json_array_elements(diag_penyerta::json)->>'text' from resumemedisri_t inresume where inresume.resumemedisri_id = resumemedisri_t.resumemedisri_id and inresume.diag_penyerta::json->>'text' != '' and inresume.diag_penyerta::json->>'text' is not null)) END AS diagnosa_penyerta,
                -- ambil data prosedur_diag dengan format json tanpa di format dulu
                prosedur_diag AS prosedur_diag,
                -- query lama
                -- CASE WHEN prosedur_diag::json->>'text' IS NULL THEN array_to_json(null::character varying[]) ELSE
                -- array_to_json(ARRAY(select json_array_elements(prosedur_diag::json)->>'text' from resumemedisri_t inresume where inresume.resumemedisri_id = resumemedisri_t.resumemedisri_id and inresume.prosedur_diag::json->>'text' != '' and inresume.prosedur_diag::json->>'text' is not null)) END AS prosedur_diag,
                kondisipulang_id,
                kondisi_lain,
                kondisikeluar_m.kondisikeluar_nama AS kondisi_pulang
                FROM resumemedisri_t
                LEFT JOIN kondisikeluar_m ON resumemedisri_t.kondisipulang_id = kondisikeluar_m.kondisikeluar_id
                WHERE pendaftaran_id = '{$pendaftaran_id}' AND pasienadmisi_id = '{$pasienadmisi_id}'
                -- AND resumemedisri_t.diag_utama::json->>'text' != ''
                -- AND resumemedisri_t.diag_utama::json->>'text' IS NOT NULL
                -- AND resumemedisri_t.diag_masuk::json->>'text' != ''
                -- AND resumemedisri_t.diag_masuk::json->>'text' IS NOT NULL
                -- AND resumemedisri_t.prosedur_diag::json->>'text' != ''
                -- AND resumemedisri_t.prosedur_diag::json->>'text' IS NOT NULL
            ";
            $data_diagnosa = Yii::$app->db->createCommand($query_diagnosa)->queryOne();

            $data_resume_medis->is_print = true;
            if(!$data_resume_medis->save()){
                throw new yii\base\Exception("Error Processing Request", 1);
                
            }
        }
        $resume_kondisi_pulang = '';
        if(isset($data_diagnosa['kondisi_pulang'])){
            $resume_kondisi_pulang .= @$data_diagnosa['kondisi_pulang'];
            if($data_diagnosa['kondisipulang_id'] == 2 || $data_diagnosa['kondisipulang_id'] == 6){
                $resume_kondisi_pulang .= ', Kondisi Lain: '.@$data_diagnosa['kondisi_lain'];
            }
        }
        $list_diagnosa_penyerta = '';
        // if(isset($data_diagnosa['diagnosa_penyerta'])){
            $diagpeny = json_decode($data_diagnosa['diagnosa_penyerta'],TRUE);
            if(is_array($diagpeny) && count($diagpeny)>0){
                $list_diagnosa_penyerta = '<ul>';
                foreach ($diagpeny as $peny) {
                    // ambil index array text
                    $list_diagnosa_penyerta .= '<li>'.@$peny['text'].'</li>';
                }
                $list_diagnosa_penyerta .= '</ul>';
            }
        // }
        $listprosedurdiag = '';
        // if(isset($data_diagnosa['prosedur_diag'])){
            $prosedur = json_decode($data_diagnosa['prosedur_diag'],TRUE);
            if(is_array($prosedur) && count($prosedur)>0){
                $listprosedurdiag = '<ul>';
                foreach ($prosedur as $diag) {
                    // ambil index array text
                    $listprosedurdiag .= '<li>'.@$diag['text'].'</li>';
                }
                $listprosedurdiag .= '</ul>';
            }
        // }
        $obat_dibawa_pulang = isset($data_resume_medis->obat_pulang) ? json_decode($data_resume_medis->obat_pulang,TRUE): [];
        $obat_tatalaksana = isset($data_resume_medis->tatalaksana_obat) ? json_decode($data_resume_medis->tatalaksana_obat,TRUE): [];
        $print = new DocoPrint();
        $print->attributes = [
            '#resume_tanggalmasuk#' => @$data_resume_medis->tgl_masuk,
            '#resume_tanggalkeluar#' => @$data_resume_medis->tgl_keluar,
            '#resume_diagmasuk#' => isset($data_diagnosa['diagnosa_masuk']) ? @$data_diagnosa['diagnosa_masuk'] : '',
            '#resume_diagutama#' => isset($data_diagnosa['diagnosa_utama']) ? @$data_diagnosa['diagnosa_utama'] : '',
            '#resume_diagpenyerta#' => isset($data_diagnosa['diagnosa_penyerta']) ? @$list_diagnosa_penyerta : '',
            '#resume_prosedurdiag#' => isset($data_diagnosa['prosedur_diag']) ? $listprosedurdiag : '',
            '#resume_afbermakna#' => @$data_resume_medis->a_f_bermakna,
            '#resume_kondisi_pulang#' => @$resume_kondisi_pulang,
            '#resume_kontrolke#' => @$data_resume_medis->kontrol_ke,
            '#resume_tglkontrol#' => @$data_resume_medis->tgl_kontrol,
            '#resume_rencanatindaklanjut#' => @$data_resume_medis->rencana_tindaklanjut,
            '#resume_tbl_obatdibawapulang#' => $this->renderPartial('cetakan_tbl_obatdibawapulang',['data'=>$obat_dibawa_pulang]),
            '#resume_tbl_tatalaksana#' => $this->renderPartial('cetakan_tbl_tatalaksana',['data'=>$obat_tatalaksana]),
            '#inf_norekammedik#' => $resultHeader ? $resultHeader['no_rekam_medik'] : '',
            '#inf_tglpendaftaran#' => $resultHeader ? ($resultHeader['tgl_pendaftaran'] ? date('d-m-Y', strtotime($resultHeader['tgl_pendaftaran'])) : '') : '',
            '#inf_nopendaftaran#' => $resultHeader ? $resultHeader['no_pendaftaran'] : '',
            '#inf_namapasien#' => $resultHeader ? $resultHeader['nama_pasien'] : '',
            '#inf_jeniskelamin#' => $resultHeader ? $resultHeader['jenis_kelamin'] : '',
            '#inf_kasuspenyakit#' => $resultHeader ? $resultHeader['jeniskasuspenyakit_nama'] : '',
            '#inf_tgllahir#' => $resultHeader ? ($resultHeader['tanggal_lahir'] ? date('d-m-Y', strtotime($resultHeader['tanggal_lahir'])) : '') : '',
            '#inf_umur#' => $resultHeader ? $resultHeader['umur'] : '',
            '#inf_dokterdpjp#' => $resultHeader ? $resultHeader['dokter_admisi'] : '',
            '#inf_kelaspelayanan#' => $resultHeader ? $resultHeader['kelas_pelayanan'] : '',
            '#inf_nokamar#' => $resultHeader ? $resultHeader['kamarruangan_nokamar'] : '',
            '#inf_nobed#' => $resultHeader ? $resultHeader['no_tempattidur'] : '',
            '#inf_penjamin#' => $resultHeader ? $resultHeader['penjamin_nama'] : '',
            '#inf_carabayar#' => $resultHeader ? $resultHeader['carabayar_nama'] : '',
            '#inf_ruangan#' => $resultHeader ? $resultHeader['ruangan_nama'] : '',
            '#no_rekam_medik#' => $resultHeader ? $resultHeader['no_rekam_medik'] : '',
            '#no_pendaftaran#' => $resultHeader ? $resultHeader['no_pendaftaran'] : '',
            '#nama_pasien#' => $resultHeader ? $resultHeader['nama_pasien'] : '',
            '#nama_user#' => $nama_user,
            '#tgl_cetak#' => date('d F Y H:i:s'),
        ];
        $print->Output();
    }
}