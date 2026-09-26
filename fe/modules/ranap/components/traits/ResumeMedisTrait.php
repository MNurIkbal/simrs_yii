<?php
/**
 * @Author: Ardi
 * @Date:   2018-07-31 17:46:52
 */

// Namespace
namespace app\modules\ranap\components\traits;

// Using Yii
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\Response;

// Using components
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;

// Models
use app\modules\ranap\models\ResumeMedisForm;

// Trait
trait ResumeMedisTrait 
{
    public function actionResumemedis()
    {
        try {
            $info_ri = $this->_data_pasien;
            $model = new ResumeMedisForm;
            $model->pendaftaran_id = @$info_ri['pendaftaran_id'];
            $model->pasienadmisi_id = @$info_ri['pasienadmisi_id'];
            
            $response = $this->_restRanap->get('resume-medis/get-bundle-data-resume-medis',[
                'query' => ['pendaftaran_id'=>@$info_ri['pendaftaran_id'],'pasienadmisi_id'=>@$info_ri['pasienadmisi_id']]
            ]);
            $bodydata = json_decode($response->getBody(), true);
            $text_diag_masuk = '';
            $text_diag_utama = '';
            $text_prosedur_diag = '';
            $cppt_info = $bodydata['response']['cppt_info'];

            $tanggal_masuk = isset($this->_data_pasien['tgl_admisi']) ? date('d-m-Y H:i:s',strtotime($this->_data_pasien['tgl_admisi'])) : date('d-m-Y H:i:s');
            $model->tgl_masuk = $tanggal_masuk;
            $model->tgl_keluar= date('d-m-Y H:i:s');
            $data_obat_bawa_pulang = $bodydata['response']['data_obat_bawa_pulang'];
            $data_obat_approved = $bodydata['response']['data_obat_approved'];
            if($cppt_info['is_instruksi_pulang'] == true && isset($cppt_info['data_cppt']['a_diag_utama'])){
                $arr_diag_utama = json_decode($cppt_info['data_cppt']['a_diag_utama'],TRUE);
                $model->diag_utama = @$arr_diag_utama['id'].'_'.@$arr_diag_utama['text'];
                $text_diag_utama = @$arr_diag_utama['text'];
            } 
            $valDiagPenyerta = [];
            if($cppt_info['is_instruksi_pulang'] == true && isset($cppt_info['data_cppt']['a_diag_penyerta'])){
                $arr_diag_penyerta = json_decode($cppt_info['data_cppt']['a_diag_penyerta'],TRUE);
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
                $model->diag_penyerta = $keyDiagPenyerta;
            }
            // var_dump($bodydata['response']['data_diagnosa_masuk']['id']); echo "<br/>";
            // var_dump($cppt_info['data_cppt']['a_diag_penyerta']); die();
            $text_diag_masuk = '';
            if(!empty($bodydata['response']['data_diagnosa_masuk'])) {
                if (isset($bodydata['response']['data_diagnosa_masuk']['id'])) {
                    $diagnosa = $bodydata['response']['data_diagnosa_masuk']['id'].'_'.$bodydata['response']['data_diagnosa_masuk']['text'];
                }else{
                    $diagnosa = $bodydata['response']['data_diagnosa_masuk']['text'];
                }
                // $exploded = explode('_', $diagnosa);
                // $text_diag_masuk = [$diagnosa => isset($exploded[1]) ? $exploded[1] : $exploded[0]];
                $text_diag_masuk = $bodydata['response']['data_diagnosa_masuk']['text'];
                $model->diag_masuk = $diagnosa;
            } 
            $valProdDiag = [];
            $is_update_resumemedis = false;
            if(isset($bodydata['response']['data_resume_medis'])){
                $is_update_resumemedis = true;
                $data_resume_medis = $bodydata['response']['data_resume_medis'];
                if(isset($data_resume_medis['diag_utama'])){
                    // $arr_diag_utama = json_decode($data_resume_medis['diag_utama'],TRUE);
                    $arr_diag_utama = $data_resume_medis['diag_utama'];
                    $model->diag_utama = @$arr_diag_utama['id'].'_'.@$arr_diag_utama['text'];
                    $text_diag_utama = @$arr_diag_utama['text'];
                    unset($data_resume_medis['diag_utama']);
                }
                if(isset($data_resume_medis['diag_masuk'])){
                    // $arr_diag_masuk = json_decode($data_resume_medis['diag_masuk'],TRUE);
                    $arr_diag_masuk = $data_resume_medis['diag_masuk'];
                    unset($data_resume_medis['diag_masuk']);
                    $model->diag_masuk = @$arr_diag_masuk['id'].'_'.@$arr_diag_masuk['text'];
                    $text_diag_masuk = @$arr_diag_masuk['text'];
                }
                if(isset($data_resume_medis['prosedur_diag'])){
                    $valProdDiag = [];
                    // $arr_prod_diag = json_decode($data_resume_medis['prosedur_diag'],TRUE);
                    $arr_prod_diag = $data_resume_medis['prosedur_diag'];
                    $keyProdDiag = [];
                    foreach ($arr_prod_diag as $valDiag) {
                        if(isset($valDiag['id'])){
                            $keyProdDiag[] = $valDiag['id'].'_'.$valDiag['text'];
                            $valProdDiag[] = [$valDiag['id'].'_'.$valDiag['text'] => $valDiag['text']];
                        }else{
                            $valProdDiag[] = [$valDiag['text']=>$valDiag['text']];
                            $keyProdDiag[] = $valDiag['text'];
                        }
                    }
                    $model->prosedur_diag = $keyProdDiag;
                    unset($data_resume_medis['prosedur_diag']);
                }
                if(isset($data_resume_medis['diag_penyerta'])){
                    $valDiagPenyerta = [];
                    // $arr_diag_penyerta = json_decode($data_resume_medis['diag_penyerta'],TRUE);
                    $arr_diag_penyerta = $data_resume_medis['diag_penyerta'];
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
                    $model->diag_penyerta = $keyDiagPenyerta;
                    unset($data_resume_medis['diag_penyerta']);
                }
                if(is_null($data_resume_medis['tgl_masuk'])){
                    unset($data_resume_medis['tgl_masuk']);
                }
                if(is_null($data_resume_medis['tgl_keluar'])){
                    unset($data_resume_medis['tgl_keluar']);
                }
                if(isset($data_resume_medis['obat_pulang'])){
                    $data_obat_bawa_pulang = json_decode($data_resume_medis['obat_pulang'],TRUE);
                    unset($data_resume_medis['obat_pulang']);
                }
                if(isset($data_resume_medis['tgl_masuk'])){
                    $model->tgl_masuk = date('d-m-Y H:i:s',strtotime($data_resume_medis['tgl_masuk']));
                    unset($data_resume_medis['tgl_masuk']);
                }
                if(isset($data_resume_medis['tgl_keluar'])){
                    $model->tgl_keluar = date('d-m-Y H:i:s',strtotime($data_resume_medis['tgl_keluar']));
                    unset($data_resume_medis['tgl_keluar']);
                }
                $model->attributes = $data_resume_medis;
            }
            $data_cara_keluar = $bodydata['response']['list-carakeluar'];
            $data_kondisi_keluar = $bodydata['response']['list-kondisikeluar'];
            $kondisikeluar_options = [];
            foreach ($data_kondisi_keluar as $index => $kondisikeluar) {
                $key_index = (int) $index + 1;
                $kondisikeluar_options[$key_index] = ['data-carakeluar_id'=>$kondisikeluar['carakeluar_id']];
            }
            $cara_keluar = ArrayHelper::map($data_cara_keluar, 'carakeluar_id', 'carakeluar_nama');
            $kondisi_keluar = ArrayHelper::map($data_kondisi_keluar, 'kondisikeluar_id', 'kondisikeluar_nama');

            $hide = 'show()';
            $status_disabled = $this->getStatusPeriksa(@$info_ri['pendaftaran_id']);
            if($status_disabled == true){
                $hide = 'hide()';
            }else {
                $status_disabled = false;
            }

            $disabled = (!empty($this->_data_pasien['pasienpulang_id']) || $this->_data_pasien['is_stopakomodasi'] == true) ? true : false;
            $status_disabled = ($status_disabled || $disabled) ? true : false;
            return $this->renderAjax('resume-medis/index', get_defined_vars());         
        } catch (RequestException $e) {
            var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (\Exception $e) {
            var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        }
    }

    public function actionResumemedisSimpan()
    {
        try {
            $request = Yii::$app->request;
            $model = new ResumeMedisForm;
            $post = $request->post();              
            // $model->scenario = 'form';
            $model->attributes = $post['ResumeMedisForm'];
            if (!$post['ResumeMedisForm']['diag_penyerta']) {
                $model->diag_penyerta = [];
            }
            if (in_array($model->diag_utama, $model->diag_penyerta)) {
                return DocoHelpers::responseTemplate(
                    422, 
                    'Error', 
                    [], 
                    [
                        'title' => Yii::t('fe', 'Peringatan!'), 
                        'text' => Yii::t('fe', 'Diagnosa utama dan diagnosa pnyerta tidak boleh sama.'),
                        'message' => Yii::t('fe', 'Diagnosa utama dan diagnosa penyerta tidak boleh sama.'),
                    ]
                );
            }

            $formName = substr(strrchr(get_class($model), "\\"), 1);
            if($model->validate()){
                $request = $this->_restRanap->post('resume-medis/create-resume-medis', [
                    'form_params' => $post
                ]);
                $response = json_decode($request->getBody(), true);
                return DocoHelpers::response($response);
            } else {
                $response = $model->errors;
                return DocoHelpers::response($response, 422, $formName);
            }
            
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        } 
    }

    public function actionCetakPdfResumeMedis()
    {
        $params = Yii::$app->request;
        $pendaftaran_id = DocoHelpers::decrypt($params->get('id','MA'));
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $pasienadmisi_id = $this->_pasienadmisi_id;
        $nama_usercetak = Yii::$app->session->get('user_identity')['nama'];
        $id_usercetak = Yii::$app->session->get('user_identity')['id_pegawai'];
        $path = Yii::getAlias("@download") . "/resume-medis.pdf";
        try {
            $response = $this->_restRanap->get('resume-medis/cetak-pdf-resume-medis',[
                'query' => [
                    'pendaftaran_id'=>$pendaftaran_id,
                    'ruangan_id' => $ruangan_id,
                    'pasienadmisi_id' => $pasienadmisi_id,
                    'id_usercetak' => $id_usercetak,
                    'nama_usercetak' => $nama_usercetak
                ],
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);   
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}
?>