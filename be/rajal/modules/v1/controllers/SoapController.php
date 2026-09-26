<?php
/*
@author: Ardi Pratama
*/

// Namespace
namespace app\modules\v1\controllers;

// Using
use Yii;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoPrint;
use Doco\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use Doco\components\DocoConstants;

// Using model
use app\modules\v1\models\SoapRj;
use app\modules\v1\models\BodyMassIndex;
use app\modules\v1\models\CpptRjV;
use app\modules\v1\models\InfoKunjunganRajal;
use app\modules\v1\models\PasienMorbiditas;
use app\modules\v1\models\Diagnosa;
use app\modules\v1\models\PemeriksaanFisik;
use app\modules\v1\models\PasienSatuSehat;
use app\modules\v1\models\IntegrasiSatuSehat;
use Integrasi\Service\Satusehat\Models\LaporanKunjunganRjView;

// Class
class SoapController extends DocoActiveController
{
    // Model class
    public $modelClass = 'app\modules\v1\models\SoapRj';
    
    // Verbs
    public function verbs()
    {
        // Parent
        $verbs = parent::verbs();

        // Return
        return $verbs;
    }

    public $messageBroker = [
        'create-soap' => [
            'services' => [
                'Sirs' => [
                    'StatusUpdateJkn' => [
                        'result' => true,
                        'successProcess'=>true,
                        'taskid' => '3',
                        'update_from' => 'save_soap_pelayanan'
                    ],
                ],
                'Satusehat' => [
                    'Condition' => [
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'create' 
                    ],
                    'ConditionSecondary' => [
                        'result' => true,
                        'successProcess' => true,
                        'state' => 'create'
                    ]
                ],
            ]
        ],
    ];

    // Acions
    public function actions()
    {
        // Parent
        $actions = parent::actions();

        // Unset actions
        unset($actions['index']);
        unset($actions['delete']);

        // Return
        return $actions;
    }

    public function actionBundleDataSoap()
    {
        try{
            $params = Yii::$app->request;
            $pendaftaran_id = $params->get('pendaftaran_id',0);
            $ruangan_id = $params->get('ruangan_id',0);
            $data_kunjungan = InfoKunjunganRajal::find()->where(['pendaftaran_id'=>$pendaftaran_id])->one();
            $data_bmi = BodyMassIndex::find()->select(['bodymassindex_id','bmi_range','bmi_minimum','bmi_maksimum','bmi_sign','bmi_defenisi','bmi_pesan'])->asArray()->all();
            $data_soap = CpptRjV::find()->where(['pendaftaran_id'=>$pendaftaran_id])->asArray()->one();
            $bmi_def  = '';
            if(isset($data_soap['imt']) && count($data_bmi)>0){
                foreach ($data_bmi as $k_bmi => $v_bmi) {
                    $bmi_maks = $bmi_min = $angka_bmi = 0;
                    $bmi_maks =floatval($v_bmi['bmi_maksimum']);
                    $bmi_min = floatval($v_bmi['bmi_minimum']);
                    $angka_bmi = floatval($data_soap['imt']);
                    if($angka_bmi >= $bmi_min && $angka_bmi <= $bmi_maks){
                        $bmi_def = $v_bmi['bmi_defenisi'];
                    }
                }
                $data_soap['kategori_imt'] = $bmi_def;
            }
            $keterangan_nyeri = '';
            if(isset($data_soap['is_nyeri'])){
                if($data_soap['is_nyeri'] == TRUE){
                    $keterangan_nyeri = 'Ya';
                    if(isset($data_soap['skala_nyeri'])){
                        $keterangan_nyeri += ", ".$data_soap['skala_nyeri'];
                    }
                    $data_soap['keterangan_nyeri'] = $keterangan_nyeri;
                }else{
                    $keterangan_nyeri = 'Tidak';
                    $data_soap['keterangan_nyeri'] = $keterangan_nyeri;
                }
            }
            if(isset($data_soap['is_resikojatuh'])){
                if($data_soap['is_resikojatuh'] == TRUE){
                    $data_soap['keterangan_resikojatuh'] = 'Ya';
                }else{
                    $data_soap['keterangan_resikojatuh'] = 'Tidak';
                }
            }
            if(isset($data_soap['a_diag_utama'])){
                $arr_diag_utama = json_decode($data_soap['a_diag_utama'],TRUE);
                $data_soap['diagnosa_utama'] = @$arr_diag_utama['text'];
            }
            if(isset($data_soap['a_diag_penyerta'])){
                $arr_diag_penyerta = json_decode($data_soap['a_diag_penyerta'],TRUE);
                $keyDiagPenyerta = [];
                foreach ($arr_diag_penyerta as $valDiag) {
                    $keyDiagPenyerta[] = $valDiag['text'];
                }
                $data_soap['list_diagnosa_penyerta'] = $keyDiagPenyerta;
            }

            $model = PemeriksaanFisik::find()->select('td_systolic, td_diastolic, detaknadi, suhutubuh, beratbadan_kg, tinggibadan_cm, pernapasan')->where([
                    'pendaftaran_id' => $pendaftaran_id,
                ])->one();
            if(!isset($data_soap['td_systolic'])){
                $data_soap['td_systolic'] = $model["td_systolic"];
            }
            if(!isset($data_soap['td_diastolic'])){
                $data_soap['td_diastolic'] = $model["td_diastolic"];
            }
            if(!isset($data_soap['detaknadi'])){
                $data_soap['detaknadi'] = $model["detaknadi"];
            }
            if(!isset($data_soap['suhutubuh'])){
                $data_soap['suhutubuh'] = $model["suhutubuh"];
            }
            if(!isset($data_soap['beratbadan_kg'])){
                $data_soap['beratbadan_kg'] = $model["beratbadan_kg"];
            }
            if(!isset($data_soap['tinggibadan_cm'])){
                $data_soap['tinggibadan_cm'] = $model["tinggibadan_cm"];
            }
            if(!isset($data_soap['pernapasan'])){
                $data_soap['pernapasan'] = $model["pernapasan"];
            }

            if($data_soap['beratbadan_kg'] == 0 || $data_soap['tinggibadan_cm'] == 0){
                $total_imt = 0;
            }
            else{
                $total_imt = $data_soap['beratbadan_kg'] / (($data_soap['tinggibadan_cm']/100) * ($data_soap['tinggibadan_cm']/100));
            }

            $total_imt = number_format($total_imt,1);
            $bmi_def   = '';
            if(isset($total_imt) && count($data_bmi)>0){
                foreach ($data_bmi as $k_bmi => $v_bmi) {
                    $bmi_maks  = $bmi_min = $angka_bmi = 0;
                    $bmi_maks  = floatval($v_bmi['bmi_maksimum']);
                    $bmi_min   = floatval($v_bmi['bmi_minimum']);
                    $angka_bmi = floatval($total_imt);
                    if($angka_bmi >= $bmi_min && $angka_bmi <= $bmi_maks){
                        $bmi_def = $v_bmi['bmi_defenisi'];
                    }
                }
                $data_soap['imt']          = $total_imt;
                $data_soap['kategori_imt'] = $bmi_def;
            }

            return [
                'data_kunjungan' => $data_kunjungan,
                'data_bmi' => $data_bmi,
                'data_soap' => $data_soap
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

    public function actionCreateSoap()
    {
        // Try
        $connection = Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $request = Yii::$app->request;
            $pendaftaran_id = $request->get('pendaftaran_id',0);
            $konsulpoli_id = $request->get('konsulpoli_id', 0);

            $is_dokter = $request->get('is_dokter',0);
            $ruangan_asal_id = $request->get('ruangan_asal_id',null);
            $loginpemakai_id = Yii::$app->jwt->user->loginpemakai_id;
            $pegawai_id =  Yii::$app->jwt->user->pegawai_id;
            $post = $request->post('SoapRjForm');
            $ruangan_id = !empty($ruangan_asal_id) ? $ruangan_asal_id : Yii::$app->jwt->ruangan_id;
            if(isset($post['a_diag_utama'])){
                $valUtama= [];
                $splitVal = explode('_', $post['a_diag_utama']);
                if(count($splitVal)>1){
                    $valUtama['id'] = $splitVal[0];
                    $splitText = explode('-', $splitVal[1]);
                    if (count($splitText)>1) {
                        $valUtama['kode'] = $splitText[0];
                        $valUtama['nama'] = $splitText[1];
                    }
                    $valUtama['text'] = $splitVal[1];
                }else{
                    $valUtama['text'] = $post['a_diag_utama'];
                }
            }
            $valueDiagPenyerta = [];
            if(isset($post['a_diag_penyerta']) && is_array($post['a_diag_penyerta'])){
                foreach ($post['a_diag_penyerta'] as $key_diag) {
                    $valDiag = [];
                    $splitVal = explode('_', $key_diag);
                    if(count($splitVal)>1){
                        $valDiag['id'] = $splitVal[0];
                        $splitText = explode('-', $splitVal[1]);
                        if (count($splitText)>1) {
                            $valDiag['kode'] = $splitText[0];
                            unset($splitText[0]);
                            $txtDiag = '';
                            foreach ($splitText as $keyTxt => $valueTxt) {
                                $txtDiag .= $valueTxt.'-';
                            }
                            $valDiag['nama'] = rtrim($txtDiag, '-');
                        }
                        $valDiag['text'] = $splitVal[1];
                    }else{
                        $valDiag['text'] = $key_diag; 
                    }
                    $valueDiagPenyerta[] = $valDiag;
                }
            }
            $soapDate = isset($post['tgl_soaprj']) ? date("Y-m-d H:i:s", strtotime($post['tgl_soaprj'])) : date("Y-m-d H:i:s");
            $data_kunjungan = InfoKunjunganRajal::find()
                ->select(['pasien_id', 'nama_pasien', 'ruangan_id', 'golonganumur_id', 'jeniskasuspenyakit_id', 'instalasi_id', 'ruangan_id', 'tgl_pendaftaran', 'pendaftaran_id'])
                ->where(['pendaftaran_id'=>$pendaftaran_id])
                ->asArray()
                ->one();
            if (empty($data_kunjungan)) {
                return $this->responseJson(400, 'Data pendaftaran tidak ditemukan');
            } else if (date("Y-m-d H:i:s", strtotime($data_kunjungan['tgl_pendaftaran'])) > $soapDate) {
                return $this->responseJson(400, 'Tanggal SOAP harus lebih dari tanggal pendaftaran.');
            }
            if ( empty($post['soaprj_id']) ) {
                unset($post['soaprj_id']);
            }else{
                $getDiagnosa = SoapRj::find(true)->select(['subject','object','planning','a_diag_utama','a_diag_penyerta','catatan_dokter','instruksi'])->andWhere(['soaprj_id' => $post['soaprj_id']])->asArray()->one();
                $getDiagnosaText = json_decode($getDiagnosa['a_diag_utama'], true);
                $getDiagnosaPenyertaText = isset($getDiagnosa['a_diag_penyerta']) ? json_decode($getDiagnosa['a_diag_penyerta'], true) : null;
                if(isset($getDiagnosaPenyertaText) && !empty($valueDiagPenyerta)){
                    $key = 0;
                    $getDiagnosaPenyertaText = ArrayHelper::getColumn($getDiagnosaPenyertaText, function ($val) use (&$key,$valueDiagPenyerta) {
                        $getText = ArrayHelper::getValue($val, 'text', '-');
                        $val['text'] = DocoHelpers::crossOutDifferences($getText, isset($valueDiagPenyerta[$key]['text']) ? $valueDiagPenyerta[$key]['text'] : '');
                        $key++;
                        return $val;
                    });
                }else{
                    if(isset($getDiagnosaPenyertaText)){
                        $getDiagnosaPenyertaText = ArrayHelper::getColumn($getDiagnosaPenyertaText, function ($val) {
                            $getText = ArrayHelper::getValue($val, 'text', '-');
                            $val['text'] = DocoHelpers::crossOutDifferences($getText, '');
                            return $val;
                        });
                    }
                }
                $subject_strike_text = DocoHelpers::crossOutDifferences($getDiagnosa['subject'], $post['subject']);
                $object_strike_text = DocoHelpers::crossOutDifferences($getDiagnosa['object'], $post['object']);
                $planning_strike_text = DocoHelpers::crossOutDifferences($getDiagnosa['planning'], $post['planning']);
                $getDiagnosaText['text'] = DocoHelpers::crossOutDifferences($getDiagnosaText['text'], $valUtama['text']);
                $instruksi_strike_text = isset($post['instruksi']) ? DocoHelpers::crossOutDifferences($getDiagnosa['instruksi'], $post['instruksi']) : $getDiagnosa['instruksi'];
                $catatan_dokter_strike_text = isset($post['catatan_dokter']) ? DocoHelpers::crossOutDifferences($getDiagnosa['catatan_dokter'], $post['catatan_dokter']) : $getDiagnosa['catatan_dokter'];
                $model = SoapRj::updateAll([
                    'is_deleted' => true,
                    'last_modified_by' => $loginpemakai_id,
                    'deleted_by' => $loginpemakai_id,
                    'deleted_date' =>  date("Y-m-d H:i:s"),
                    'created_date' =>  date("Y-m-d H:i:s"),
                    'subject' => $subject_strike_text,
                    'object' => $object_strike_text,
                    'planning' => $planning_strike_text,
                    'a_diag_utama' => $getDiagnosaText ,
                    'a_diag_penyerta' => $getDiagnosaPenyertaText,
                    'instruksi' => $instruksi_strike_text,
                    'catatan_dokter' => $catatan_dokter_strike_text 
                ], [
                    'soaprj_id' => $post['soaprj_id']
                ]);
            }
            $isNew = true;
            $tempDiagUtama_lama = null;
            $tempDiagPenyerta_lama = null;
            $model = new SoapRj;
            if($is_dokter == 1){
                $model->scenario = SoapRj::SOAP_DOKTER;
            }else{
                $model->scenario = SoapRj::SOAP_PERAWAT;
            }
            $conditions = [
                'pendaftaran_id'=>$pendaftaran_id,
                'ruangan_id' => $data_kunjungan['ruangan_id'],
                'pegawai_id' => $pegawai_id,
            ];
            if ( isset($post['soaprj_id']) && !empty($post['soaprj_id']) ) {
                $conditions['soaprj_id'] = $post['soaprj_id'];
                $mSoap = SoapRj::find()->where($conditions)->one();
                if ( !is_null($mSoap) ){
                    $model = $mSoap;
                    $tempDiagUtama_lama = $mSoap->a_diag_utama;
                    $tempDiagPenyerta_lama = $mSoap->a_diag_penyerta;   
                }
            }
            if(isset($post['imt'])){
                $pattern = '/\,/';
                $replacement = '.';
                $post['imt'] = preg_replace($pattern, $replacement, $post['imt']);
            }
            if(isset($post['a_diag_utama'])){
                $model->a_diag_utama = ($valUtama);
                unset($post['a_diag_utama']);
            }
            if(isset($post['a_diag_penyerta'])){
                $model->a_diag_penyerta = ($valueDiagPenyerta);
                unset($post['a_diag_penyerta']);
            }
            $model->attributes = $post;
            $model->is_icd_x = $post['is_icd_x'];
            $model->subject = $post['subject'];
            $model->object = $post['object'];
            $model->planning = $post['planning'];
            $model->instruksi = $post['instruksi'];
            $model->beratbadan_kg = isset($post['beratbadan_kg']) ? str_replace(',','.', $post['beratbadan_kg']): null;
            $model->tinggibadan_cm = isset($post['tinggibadan_cm']) ? str_replace(',','.', $post['tinggibadan_cm']): null;
            $model->imt = isset($post['imt']) ? str_replace(',','.', $post['imt']): null;
            $model->td_systolic = isset($post['td_systolic']) ? str_replace(',','.', $post['td_systolic']): null;
            $model->td_diastolic = isset($post['td_diastolic']) ? str_replace(',','.', $post['td_diastolic']): null;
            $model->pernapasan = isset($post['pernapasan']) ? str_replace(',','.', $post['pernapasan']): null;
            $model->detaknadi = isset($post['detaknadi']) ? str_replace(',','.', $post['detaknadi']): null;
            $model->suhutubuh = isset($post['suhutubuh']) ? str_replace(',','.', $post['suhutubuh']): null;
            $model->catatan_dokter = isset($post['catatan_dokter']) ? $post['catatan_dokter'] : null;
            
            $model->tgl_soaprj = $soapDate;
            $model->pendaftaran_id = $pendaftaran_id;
            $model->pasien_id = isset($data_kunjungan['pasien_id']) ? $data_kunjungan['pasien_id'] : null;
            $model->ruangan_id = $ruangan_id;
            $model->pegawai_id = Yii::$app->jwt->user->pegawai_id;
            $model->final = isset($post['final']) ? $post['final'] : 0;
            $additional_data = $model->additional_data;
            if(!is_array($additional_data)) {
                $additional_data = json_decode($additional_data, true);
            }
            $additional_data['via_soap'] = true;
            $model->additional_data = json_encode($additional_data);

            if(!$model->validate()){
                $response = $model->getErrors();
                return DocoHelpers::responseTemplate(422,$response);
            }

            if(!$model->save()){
                $errors = DocoHelpers::parseError($model->errors,'SoapRjForm');
                return [
                    'data' => $errors,
                    'status' => 422
                ];
            }

            if ( empty($post['soaprj_id']) || $post['soaprj_id'] == null ) {
                $data_morbid['pendaftaran_id'] = $pendaftaran_id;
                $data_morbid['pasien_id'] = $data_kunjungan['pasien_id'];
                $data_morbid['golonganumur_id'] = $data_kunjungan['golonganumur_id'];
                $data_morbid['jeniskasuspenyakit_id'] = $data_kunjungan['jeniskasuspenyakit_id'];
                $data_morbid['instalasi_id'] = $data_kunjungan['instalasi_id'];
                $data_morbid['ruangan_id'] = $data_kunjungan['ruangan_id'];
                $data_morbid['pegawai_id'] = $pegawai_id;
                $data_morbid['tglmorbiditas'] = date('Y-m-d H:i:s');

                if(isset($model->a_diag_utama) && $tempDiagUtama_lama != $model->a_diag_utama && $is_dokter){
                    $findMorbidUtama = PasienMorbiditas::updateAll(['is_deleted'=>TRUE],['pendaftaran_id'=>$pendaftaran_id,'kelompokdiagnosa_id'=>DocoConstants::VAR_KELOMPOK_DIAGNOSA_UTAMA]);
                    $diag_utama = $model->a_diag_utama;
                    if(count($diag_utama) !== 0 && isset($diag_utama['text'])){
                        $data_morbid['diagnosa_pasien'] = $model->a_diag_utama;
                        $data_morbid['cek_diagnosa'] = $diag_utama['text'];
                        $data_morbid['kelompokdiagnosa_id'] = DocoConstants::VAR_KELOMPOK_DIAGNOSA_UTAMA;
                        if ($data_morbid){
                            $cekKasus = PasienMorbiditas::find()
                                        ->where(['pasien_id'=>$data_morbid['pasien_id']])
                                        ->andWhere("diagnosa_pasien->>'text' IS NOT NULL")
                                        ->andWhere("diagnosa_pasien->>'text' != ''")
                                        ->andWhere("diagnosa_pasien->>'text' = '".@str_replace("'", "''", $data_morbid['cek_diagnosa'])."' ")
                                        ->one();
                            if(is_null($cekKasus)){
                                $data_morbid['kasusdiagnosa'] = DocoConstants::DIAGNOSA_KASUS_BARU;
                            }else{
                                $data_morbid['kasusdiagnosa'] = DocoConstants::DIAGNOSA_KASUS_LAMA;
                            }
                            if(isset($data_morbid['cek_diagnosa'])){
                                unset($data_morbid['cek_diagnosa']);
                            }
                            $modelMorbid = new PasienMorbiditas;
                            $modelMorbid->attributes = $data_morbid;
                            if(!$modelMorbid->save()){
                                $transaction->rollBack();
                                \Yii::$app->response->statusCode = 500;
                                return ['message'=>'Gagal di a_diag_utama'];
                            }
                        }
                    }
                }

                if ($model->a_diag_penyerta == null && $is_dokter) {
                    $findMorbidPenyerta = PasienMorbiditas::updateAll(['is_deleted'=>TRUE],['pendaftaran_id'=>$pendaftaran_id,'kelompokdiagnosa_id'=>DocoConstants::VAR_KELOMPOK_DIAGNOSA_PENYERTA]);
                }

                if(isset($model->a_diag_penyerta) && $tempDiagPenyerta_lama != $model->a_diag_penyerta && $is_dokter){
                    $findMorbidPenyerta = PasienMorbiditas::updateAll(['is_deleted'=>TRUE],['pendaftaran_id'=>$pendaftaran_id,'kelompokdiagnosa_id'=>DocoConstants::VAR_KELOMPOK_DIAGNOSA_PENYERTA]);
                    $diag_penyerta = $model->a_diag_penyerta;
                    if(count($diag_penyerta) > 0){
                        foreach ($diag_penyerta as $v_diag_penyerta) {
                            if(isset($v_diag_penyerta['text'])){
                                $data_morbid['diagnosa_pasien'] = ($v_diag_penyerta);
                                $data_morbid['cek_diagnosa'] = $v_diag_penyerta['text'];
                                $data_morbid['kelompokdiagnosa_id'] = DocoConstants::VAR_KELOMPOK_DIAGNOSA_PENYERTA;
                                $data_morbid['kasusdiagnosa'] = null;
                                if ($data_morbid){
                                    $cekKasus = PasienMorbiditas::find()
                                                ->where(['pasien_id'=>$data_morbid['pasien_id']])
                                                ->andWhere("diagnosa_pasien->>'text' IS NOT NULL")
                                                ->andWhere("diagnosa_pasien->>'text' != ''")
                                                ->andWhere("diagnosa_pasien->>'text' = '".@str_replace("'", "''", $data_morbid['cek_diagnosa'])."' ")
                                                ->one();
                                    if(is_null($cekKasus)){
                                        $data_morbid['kasusdiagnosa'] = DocoConstants::DIAGNOSA_KASUS_BARU;
                                    }else{
                                        $data_morbid['kasusdiagnosa'] = DocoConstants::DIAGNOSA_KASUS_LAMA;
                                    }
                                    if(isset($data_morbid['cek_diagnosa'])){
                                        unset($data_morbid['cek_diagnosa']);
                                    }
                                    $modelMorbid = new PasienMorbiditas;
                                    $modelMorbid->attributes = $data_morbid;
                                    if(!$modelMorbid->save()){
                                    $transaction->rollBack();
                                        \Yii::$app->response->statusCode = 500;
                                        return ['message'=>'Gagal di a_diag_penyerta'];
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $transaction->commit();
            if ( !isset($conditions['soaprj_id']) ) {
                $getId = SoapRj::find()->select(['soaprj_id'])->andWhere($conditions)->orderBy([
                    'tgl_soaprj' => SORT_DESC
                ])->asArray()->one();
            }

            //Apabila data berhasil disimpan, persiapan data untuk Satusehat
            $namaPasien = !empty($data_kunjungan['nama_pasien']) ? $data_kunjungan['nama_pasien'] : '';
            return [
                'message'=>'Berhasil',
                'data' => [
                    'a_diag_utama' => $model->a_diag_utama,
                    'pendaftaran_id' => $pendaftaran_id, // untuk kebutuhan service internal request
                    'is_created_by_dokter' => $is_dokter, // untuk kebutuhan service internal request
                    'a_diag_penyerta' => $model->a_diag_penyerta,
                    'konsulpoli_id' => $konsulpoli_id,
                    'result' => $model->attributes,
                    'nama_pasien' => $namaPasien,
                    'soaprj_id' => isset($conditions['soaprj_id']) ? $conditions['soaprj_id'] : $getId['soaprj_id']
                ]
            ];

        } catch (\yii\db\Exception $e) {
            $exception = preg_match('/(?<=SQLSTATE[42883]:  )(.*)/',$e->getMessage(),$out);
            $message = $e->getMessage();
            if (isset($out[1])) {
                $message = $out[1];
            }
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
            return [
                'status' => 422,
                'title' => 'Proses Gagal!',
                'text' => $message
            ];
        } catch (\Exception $e) {
            $transaction->rollBack();
            \Yii::$app->response->statusCode = 500;
            $this->logError($e);
            return [
                'message' => $e->getMessage()
            ];
        }
    }

    private function insertMorbiditas($data = [])
    {
        if ($data){
            $cekKasus = PasienMorbiditas::find()->where(['pasien_id'=>$data['pasien_id'],'diagnosa_pasien IS NOT NULL',"diagnosa_pasien != ''","diagnosa_pasien::json->>'text' = '".@$data['cek_diagnosa']."' "])->one();
            if(is_null($cekKasus)){
                $data['kasusdiagnosa'] = DocoConstants::DIAGNOSA_KASUS_BARU;
            }else{
                $data['kasusdiagnosa'] = DocoConstants::DIAGNOSA_KASUS_LAMA;
            }
            if(isset($data['cek_diagnosa'])){
                unset($data['cek_diagnosa']);
            }
            $modelMorbid = new PasienMorbiditas;
            $modelMorbid->attributes = $data;
            if(!$modelMorbid->save()){
                return false;
            }
            return true;
        }
    }

}