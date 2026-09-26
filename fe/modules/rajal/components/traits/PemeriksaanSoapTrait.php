<?php
/*
@author: Ardi Pratama
*/

namespace app\modules\rajal\components\traits;

use Yii;
use yii\filters\AccessControl;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;
use yii\base\Exception;

use function GuzzleHttp\json_encode;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

use app\modules\rajal\models\SoapRjForm;

trait PemeriksaanSoapTrait 
{
    public function actionSoap()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id') ? DocoHelpers::decrypt($request->get('id')) : null;
        $encryptedPendaftaranId = DocoHelpers::encrypt($pendaftaran_id);
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        try{
            $modelSoap = new SoapRjForm;
            $is_dokter = false;
            $userIdentity = Yii::$app->session->get('user_identity');
            if(isset($userIdentity['kelompokpegawai_id']) && $userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS) {
                $is_dokter = true;
            }
            if($is_dokter == true){
                $modelSoap->scenario = SoapRjForm::SOAP_DOKTER;
            }else{
                $modelSoap->scenario = SoapRjForm::SOAP_PERAWAT;
            }
            $modelSoap->pendaftaran_id = $pendaftaran_id;
            $modelSoap->ruangan_id = $ruangan_id;

            $data_jk['kode_jk'] = '-';
            $data_jk['nama_jk'] = '-';
            $jk = $this->_jeniskelamin;
            if(isset($jk) && $jk == "15"){
                $data_jk['kode_jk'] = 'L';
                $data_jk['nama_jk'] = 'Laki - Laki';
            }else if(isset($jk) && $jk == "14"){
                $data_jk['kode_jk'] = 'P';
                $data_jk['nama_jk'] = 'Perempuan';
            }

            $valDiagPenyerta = [];
            $callbackDiagPenyerta = [];
            $text_diag_utama = '';
            $data_soap = [];
            $temp = [];
            $listValue = [];
            $data_kategori_imt = [];
            $keyDiagPenyerta = [];
            $valDiagPenyerta = [];

            try {
                $res_bundledata = $this->_restRajal->get('soap/bundle-data-soap',['query'=>['pendaftaran_id'=>$pendaftaran_id]]);
                $preprocessing_bundle = json_decode($res_bundledata->getBody(), true);

                if(isset($preprocessing_bundle['response']['data_soap'])){
                    $data_soap = $preprocessing_bundle['response']['data_soap'];
                    $modelSoap->attributes = $data_soap;
                    if(isset($data_soap['a_diag_utama'])){
                        $arr_diag_utama = json_decode($data_soap['a_diag_utama'],TRUE);
                        $modelSoap->a_diag_utama = @$arr_diag_utama['id'].'_'.@$arr_diag_utama['text'];
                        $text_diag_utama = @$arr_diag_utama['text'];
                        unset($data_soap['a_diag_utama']);
                    }

                    if(isset($data_soap['a_diag_penyerta'])){
                        $arr_diag_penyerta = json_decode($data_soap['a_diag_penyerta'],TRUE);
                        foreach ($arr_diag_penyerta as $valDiag) {
                            if(isset($valDiag['id'])){
                                $keyDiagPenyerta[] = $valDiag['id'].'_'.$valDiag['text'];
                                $valDiagPenyerta[] = [$valDiag['id'].'_'.$valDiag['text'] => $valDiag['text']];
                                $callbackDiagPenyerta[] = ['id' => $valDiag['id'],
                                                            'text' => $valDiag['text'] ];
                            }else{
                                $valDiagPenyerta[] = [$valDiag['text']=>$valDiag['text']];
                                $keyDiagPenyerta[] = $valDiag['text'];
                                $callbackDiagPenyerta[] = ['id' => $valDiag['text'],
                                                            'text' => $valDiag['text'] ];
                            }
                        }
                        $modelSoap->a_diag_penyerta = $keyDiagPenyerta;
                        unset($data_soap['a_diag_penyerta']);
                    }
                    if(isset($data_soap['imt'])){
                        $pattern = '/\./';
                        $replacement = ',';
                        $data_soap['imt'] = preg_replace($pattern, $replacement, $data_soap['imt']);
                        $modelSoap->imt = $data_soap['imt'];
                    }
                    if(isset($data_soap['suhutubuh'])){
                        $pattern = '/\./';
                        $replacement = ',';
                        $data_soap['suhutubuh'] = preg_replace($pattern, $replacement, $data_soap['suhutubuh']);
                        $modelSoap->suhutubuh = $data_soap['suhutubuh'];
                    }

                    if(isset($data_soap['is_nyeri']) && $data_soap['is_nyeri'] == TRUE){
                        $modelSoap->is_nyeri = 1;
                    }else if(isset($data_soap['is_nyeri']) && $data_soap['is_nyeri'] == FALSE){
                        $modelSoap->is_nyeri = 0;
                    }
                    if(isset($data_soap['is_resikojatuh']) && $data_soap['is_resikojatuh'] == TRUE){
                        $modelSoap->is_resikojatuh = 1;
                    }else if(isset($data_soap['is_resikojatuh']) && $data_soap['is_resikojatuh'] == FALSE){
                        $modelSoap->is_resikojatuh = 0;
                    }
                }
                
                if(isset($preprocessing_bundle['response']['data_bmi'])){
                    $data_kategori_imt = $preprocessing_bundle['response']['data_bmi'];
                }
                if(isset($preprocessing_bundle['response']['data_kunjungan'])){
                    $data_kunjungan = $preprocessing_bundle['response']['data_kunjungan'];
                    $modelSoap->pasien_id = $data_kunjungan['pasien_id'];
                    $modelSoap->pegawai_id = $data_kunjungan['pegawai_id'];

                }
            } catch (Exception $e) {
                $modelSoap          = [];
                $data_jk            = [];
                $text_diag_utama    = [];
                $valDiagPenyerta    = [];
                $data_kategori_imt  = [];
                $encryptedPendaftaranId = 0;
                $is_dokter          = [];
                $temp               = [];
                $listValue          = [];
                $keyDiagPenyerta    = [];
                $callbackDiagPenyerta   = [];
            }           
        }catch(\Exception $e) {
            $modelSoap          = [];
            $data_jk            = [];
            $text_diag_utama    = [];
            $valDiagPenyerta    = [];
            $data_kategori_imt  = [];
            $encryptedPendaftaranId = 0;
            $is_dokter          = [];
            $temp               = [];
            $listValue          = [];
            $keyDiagPenyerta    = [];
            $callbackDiagPenyerta   = [];
        }
         return $this->renderAjax('soap/__soap',[
                        'modelSoap'=>$modelSoap,
                        'data_jk'=>$data_jk,
                        'text_diag_utama'=>$text_diag_utama,
                        'valDiagPenyerta'=>$valDiagPenyerta,
                        'data_kategori_imt'=>$data_kategori_imt,
                        'encryptedPendaftaranId'=>$encryptedPendaftaranId,
                        'is_dokter' => $is_dokter,
                        'temp' => $temp,
                        'listValue' => $listValue,
                        'keyDiagPenyerta' => $keyDiagPenyerta,
                        'callbackDiagPenyerta' => $callbackDiagPenyerta,
                        'status_periksa' => $this->_statusPeriksa,
                    ]);
    }

    public function viewSoap($pendaftaran_id,$data_soap)
    {
        $encryptedPendaftaranId = DocoHelpers::encrypt($pendaftaran_id);
        $is_dokter = false;
        $userIdentity = Yii::$app->session->get('user_identity');
        if(isset($userIdentity['kelompokpegawai_id']) && $userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_MEDIS) {
            $is_dokter = true;
        }
        return $this->renderAjax('soap/__viewsoap',['encryptedPendaftaranId'=>$encryptedPendaftaranId,'data_soap'=>$data_soap,'is_dokter'=>$is_dokter ]);
    }

    public function actionUbahSoap()
    {
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id') ? DocoHelpers::decrypt($request->get('id')) : null;
        $encryptedPendaftaranId = DocoHelpers::encrypt($pendaftaran_id);
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        try{
            $modelSoap = new SoapRjForm;

            $modelSoap->pendaftaran_id = $pendaftaran_id;
            $modelSoap->ruangan_id = $ruangan_id;

            $data_jk['kode_jk'] = '-';
            $data_jk['nama_jk'] = '-';
            $jk = $this->_jeniskelamin;
            if(isset($jk) && $jk == "15"){
                $data_jk['kode_jk'] = 'L';
                $data_jk['nama_jk'] = 'Laki - Laki';
            }else if(isset($jk) && $jk == "14"){
                $data_jk['kode_jk'] = 'P';
                $data_jk['nama_jk'] = 'Perempuan';
            }

            $valDiagPenyerta = [];
            $text_diag_utama = '';
            $res_bundledata = $this->_restRajal->get('soap/bundle-data-soap',['query'=>['pendaftaran_id'=>$pendaftaran_id]]);
            $preprocessing_bundle = json_decode($res_bundledata->getBody(), true);

            $data_soap = [];
            if(isset($preprocessing_bundle['response']['data_soap'])){
                $data_soap = $preprocessing_bundle['response']['data_soap'];

                $modelSoap->attributes = $data_soap;
                if(isset($data_soap['a_diag_utama'])){
                    $arr_diag_utama = json_decode($data_soap['a_diag_utama'],TRUE);
                    $modelSoap->a_diag_utama = @$arr_diag_utama['id'].'_'.@$arr_diag_utama['text'];
                    $text_diag_utama = @$arr_diag_utama['text'];
                    unset($data_soap['a_diag_utama']);
                }

                if(isset($data_soap['a_diag_penyerta'])){
                    $valDiagPenyerta = [];
                    $arr_diag_penyerta = json_decode($data_soap['a_diag_penyerta'],TRUE);
                    $keyDiagPenyerta = [];
                    foreach ($arr_diag_penyerta as $valDiag) {
                        if(isset($valDiag['id'])){
                            $keyDiagPenyerta[] = $valDiag['id'].'_'.$valDiag['text'];
                            $valDiagPenyerta[] = [$valDiag['id'].'_'.$valDiag['text'] => $valDiag['text']];
                        }else{
                            $valDiagPenyerta[] = [$valDiag['text']=>$valDiag['text']];
                            $keyDiagPenyerta[] = $valDiag['text'];
                        }
                    }
                    $modelSoap->a_diag_penyerta = $keyDiagPenyerta;
                    unset($data_soap['a_diag_penyerta']);
                }
            }
            $data_kategori_imt = [];
            if(isset($preprocessing_bundle['response']['data_bmi'])){
                $data_kategori_imt = $preprocessing_bundle['response']['data_bmi'];
            }

            return $this->renderAjax('soap/__soap',['modelSoap'=>$modelSoap,'data_jk'=>$data_jk,'text_diag_utama'=>$text_diag_utama,'valDiagPenyerta'=>$valDiagPenyerta,'data_kategori_imt'=>$data_kategori_imt,'encryptedPendaftaranId'=>$encryptedPendaftaranId]);
        }catch(\Exception $e) {
            // Error
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    public function actionCreateSoap()
    {
        $params = Yii::$app->request;
        $encryptedPendaftaranId = $params->get('id','MQ');
        $pendaftaran_id = DocoHelpers::decrypt($encryptedPendaftaranId);
        $is_dokter = $params->get('is_dokter',0);
        $pegawai_id = Yii::$app->session->get('user_identity')['id_pegawai'];

        $post = $params->post('SoapRjForm');
        $model = new SoapRjForm;
        if($is_dokter == 1){
            $model->scenario = SoapRjForm::SOAP_DOKTER;
        }else{
            $model->scenario = SoapRjForm::SOAP_PERAWAT;
        }
        $formName = substr(strrchr(get_class($model), "\\"), 1);

        $model->attributes = $post;
        $model->beratbadan_kg = str_replace(',','.', $model->beratbadan_kg);
        $model->tinggibadan_cm = str_replace(',','.', $model->tinggibadan_cm);
        $model->imt = str_replace(',','.', $model->imt);
        $model->td_systolic = str_replace(',','.', $model->td_systolic);
        $model->td_diastolic = str_replace(',','.', $model->td_diastolic);
        $model->pernapasan = str_replace(',','.', $model->pernapasan);
        $model->detaknadi = str_replace(',','.', $model->detaknadi);
        $model->suhutubuh = str_replace(',','.', $model->suhutubuh);
        if(!isset($post['a_diag_penyerta'])){
            $model->a_diag_penyerta = [];
        }

        if(!is_array($model->a_diag_penyerta)){
            $model->a_diag_penyerta = [];
        }

        if (in_array($model->a_diag_utama, $model->a_diag_penyerta)) {
            return DocoHelpers::responseTemplate(
                422, 
                'Error', 
                [], 
                [
                    'title' => Yii::t('fe', 'Peringatan!'), 
                    'text' => Yii::t('fe', 'Diagnosa utama dan diagnosa penyerta tidak boleh sama.'),
                    'message' => Yii::t('fe', 'Diagnosa utama dan diagnosa penyerta tidak boleh sama.'),
                ]
            );
        }
        if($model->validate()) {
            $request = $this->_restRajal->post('soap/create-soap', [
                'query' => ['pendaftaran_id'=>$pendaftaran_id,'is_dokter'=>$is_dokter,'pegawai_id'=>$pegawai_id],
                'form_params' => $params->post()
            ]);
            $response = json_decode($request->getBody(), true);
            return DocoHelpers::response($response);
        } else {
            $response = $model->errors;
            return DocoHelpers::response($response, 422, $formName);
        }
    }
}