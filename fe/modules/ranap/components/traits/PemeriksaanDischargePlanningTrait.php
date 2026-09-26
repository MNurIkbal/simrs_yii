<?php

/**
 * @Author: Sunarko
 * @Date:   2018-07-09 13:37:31
 * @Last Modified by:   Doconb-Bandung
 * @Last Modified time: 2018-07-11 09:30:57
 */

// Namespace
namespace app\modules\ranap\components\traits;

// Using Yii
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;

// Using Guzzles
use function GuzzleHttp\json_encode;
use GuzzleHttp\Exception\RequestException;

// Using components
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

// Using model
use app\modules\ranap\models\RencanaPulangForm;
use app\modules\ranap\models\RencanaPulangDetailForm;


// Trait
trait PemeriksaanDischargePlanningTrait 
{
    // DischargePlanning
    public function actionDischargePlanning()
    {
        $request = Yii::$app->request;
        $title = 'Discharge Planning';
        $model = new RencanaPulangForm;
        $modelDetail = new RencanaPulangDetailForm;
        $tampDetail = [];
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $disabled = (!empty($this->_data_pasien['pasienpulang_id']) || $this->_data_pasien['is_stopakomodasi'] == true) ? true : false;
        // try {
        if($model->load($request->post())) {
           
            $modelDetail->load($request->post());
            $postDetail = Yii::$app->request->post('RencanaPulangDetailForm');
            $postrencana = Yii::$app->request->post('RencanaPulangForm');
            
            if(isset($postDetail['edukasi_kesehatan'])) {
                $parent = $postDetail['edukasi_kesehatan'];
                foreach ($parent as $key => $value) {
                    if ($key < 22) {
                        $datacount = count($postDetail['pemberi_edukasi'][$key]);
                        $datacekPE = $postDetail['pemberi_edukasi'][$key];
                        $datacekTGL = $postDetail['tgl_edukasi'][$key];
                        $datacekPPA = $postDetail['ppa'][$key];
                            if($datacekPE == ''){
                                $modelDetail->addError('pemberi_edukasi['.$key.']','Penerima Edukasi Harus di isi');
                            }
                            if($datacekTGL == ''){
                                $modelDetail->addError('tgl_edukasi['.$key.']','Tanggal Edukasi Harus di isi');
                            }
                            if($datacekPPA == ''){
                                $modelDetail->addError('ppa['.$key.']','PPA Harus di isi');
                            }
                    }else {
                        if($postDetail['pemberi_edukasi'][$key] == "") {
                            DocoHelpers::multipleParseError($modelDetail,'Penerima Edukasi Harus di isi','pemberi_edukasi', $key);
                        }else{
                            $string_len = strlen($postDetail['pemberi_edukasi'][$key]);
                            if ( $string_len > 255) {
                                DocoHelpers::multipleParseError($modelDetail,'Penerima Edukasi  must be no greater than 255.','pemberi_edukasi', $key);
                            }
                        }
                        if($postDetail['ppa'][$key] == "") {
                            DocoHelpers::multipleParseError($modelDetail,'PPA Harus di isi','ppa', $key);
                        }
                        if($postDetail['tgl_edukasi'][$key] == "") {
                            DocoHelpers::multipleParseError($modelDetail,'Tanggal Edukasi Harus di isi','tgl_edukasi', $key);
                        }
                        
                        if(isset($postDetail['pemberi_edukasi_opsional']) && is_array($postDetail['pemberi_edukasi_opsional'])){
                            foreach($postDetail['pemberi_edukasi_opsional'] as $key => $item){
                                $string_length = strlen($item);
                                if($item == ''){
                                    $modelDetail->addError('pemberi_edukasi_opsional['.$key.']','Penerima Edukasi Harus di isi');
                                }else{
                                    if ( $string_length > 255) {
                                        $modelDetail->addError('pemberi_edukasi_opsional['.$key.']','Penerima Edukasi  must be no greater than 255.');
                                    }
                                }
                            }
                        }
                        if(isset($postDetail['tgl_edukasi_opsional']) && is_array($postDetail['tgl_edukasi_opsional'])){
                            foreach($postDetail['tgl_edukasi_opsional'] as $key => $item){
                                if($item == ''){
                                    $modelDetail->addError('tgl_edukasi_opsional['.$key.']','Tanggal Edukasi Harus di isi');
                                }
                            }
                        }
                        if(isset($postDetail['ppa_opsional']) && is_array($postDetail['ppa_opsional'])){
                            foreach($postDetail['ppa_opsional'] as $key => $item){
                                if($item == ''){
                                    $modelDetail->addError('ppa_opsional['.$key.']','PPA Harus di isi');
                                }
                            }
                        }
                    }
                }
            }
            $responseError = DocoHelpers::response($modelDetail->errors, 422, 'RencanaPulangDetailForm');
            if($model->validate() && empty($responseError['response']['data'])) {
                $response = $this->_restRanap->post('discharge-planning/create', [
                    'form_params' => [
                        'rencana_pulang' => $postrencana,
                        'rencana_pulang_detail' => $postDetail,
                    ]
                ]);
                // print_r(json_decode($response->getBody())); die;
                return DocoHelpers::responseJsonString($response->getBody(), $formName);
            }else {
                if($model->validate()){
                    return $responseError;
                }else{
                    $errors = DocoHelpers::parseError($model->errors,'RencanaPulangForm');
                    return DocoHelpers::response([
                        'response' => [
                            'data' => $errors
                        ]
                    ],422);
                }
            }
        }else {
            $pasienadmisi_id = $this->_data_pasien['pasienadmisi_id'];
            $pendaftaran_id = $this->_data_pasien['pendaftaran_id'];
            $tgl_pasienadmisi =  date('d-m-Y H:i:s',strtotime($this->_data_pasien['tgl_admisi']));
            $tgl_pasienadmisi_day = date('d F Y H:i:s',strtotime($this->_data_pasien['tgl_admisi']));
            $tgl_pasienadmisi_valid = date('d F Y H:i:s', strtotime('-1 days', strtotime($tgl_pasienadmisi_day)));
            $idUser = Yii::$app->docoVars->user("id_pegawai");
            $idDokter = $this->_data_pasien['dokter_admisi_id'];
            $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
            $dataRencanaPulang = "";
            
            $response = $this->_restRanap->get('discharge-planning/get-request', ['query' => [
                'type' => 'edukasi_kesehatan',
                'ruangan_id' => $ruangan_id,
                'pasienadmisi_id' => $pasienadmisi_id
            ]]);
            
            $body = json_decode($response->getBody(), TRUE);
            $response = $body['response'];
            
            if($response['header']) {
                $model->attributes = $response['header'];
                $model->rencanapulang_id = $response['header']['rencanapulang_id'];
            }
            $model->rencana_pulang = date('d/m/Y H:i:s', strtotime($model->rencana_pulang));
            
            if ($model->rencanapulang_id) {
                $dataRencanaPulang = $model->rencana_pulang;
            }else{
                $dataRencanaPulang = date('d/m/Y H:i:s');
            }

            if($response['detail'] != '') {
                $modelDetail->attributes = $response['detail'];
                foreach ($response['detail'] as $key => $value) {
                    if($value['edukasi_kesehatan'] == 31) {
                        $modelDetail->edukasi_kesehatan[$value['edukasi_kesehatan']][] = (int) $value['edukasi_kesehatan'];
                        $modelDetail->pemberi_edukasi[$value['edukasi_kesehatan']][] = $value['pemberi_edukasi'];
                        $modelDetail->tgl_edukasi[$value['edukasi_kesehatan']][] = date('d-m-Y', strtotime($value['tgl_edukasi']));
                        $modelDetail->ppa[$value['edukasi_kesehatan']][] = $value['ppa'];
                    }else {
                        $modelDetail->edukasi_kesehatan[$value['edukasi_kesehatan']] = (int) $value['edukasi_kesehatan'];
                        $modelDetail->pemberi_edukasi[$value['edukasi_kesehatan']] = $value['pemberi_edukasi'];
                        $modelDetail->tgl_edukasi[$value['edukasi_kesehatan']] = date('d-m-Y', strtotime($value['tgl_edukasi']));
                        $modelDetail->ppa[$value['edukasi_kesehatan']] = $value['ppa'];
                    }
                    $tampDetail[] = (int) $value['edukasi_kesehatan'];
                }
            }
            $hide = 'show()';
            $status_disabled = $this->getStatusPeriksa($this->_data_pasien['pendaftaran_id']);
            if($status_disabled == true){
                $hide = 'hide()';
            }else {
                $status_disabled = 'false';
            }

            if (array_key_exists('lanjut_discharge', $response)) {
                if ($response['lanjut_discharge'] != true) {
                    $status_disabled = true;
                }
            }
            return $this->renderAjax('__discharge_planning', get_defined_vars());
        }
    }

    public function actionPrintPdf($pasienadmisi_id)
    {   
        $pasienadmisi_id = DocoHelpers::decrypt($pasienadmisi_id);
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/discharge-planning.pdf";
        try {
            $post = $request->post();
            $response = $this->_restRanap
                        ->post('discharge-planning/print-pdf',
                        [
                            'query' => ['id'=>$pasienadmisi_id],
                            'form_params' => $post,
                            'save_to' => $path
                        ]);

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            var_dump(json_decode($e->getResponse()->getBody()));exit();     
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionDeleteDischarge($rencanapulang_id)
    {
        try {
            $response = $this->_restRanap->request('GET', 'discharge-planning/delete-discharge',[
                            'query' => ['rencanapulang_id' => $rencanapulang_id ]
                        ]);
            $response = json_decode($response->getBody(),true);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
   
}
?>
