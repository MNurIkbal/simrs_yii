<?php

/**
 * @Author: Ardi Pratama Septiadi
 */

namespace app\modules\igd\components\traits;

use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;

use function GuzzleHttp\json_encode;
use GuzzleHttp\Exception\RequestException;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

use app\modules\igd\models\AsesmenMedisRDForm;

trait AsesmenDokterTrait
{
    public function actionAsesmenDokter($id)
    {
        try{
            $pendaftaran_id = DocoHelpers::decrypt($id);
            $data_pasien = $this->_data_pasien;
            $userIdentity = $this->_user_identity;

            $dokter_jaga_id = isset($data_pasien['dokter_jaga_id']) ? $data_pasien['dokter_jaga_id'] : 0;
            $dokter_jaga = isset($data_pasien['dokter_jaga']) ? $data_pasien['dokter_jaga'] : '';

            $model = new AsesmenMedisRDForm;
            $model->dokter_id = $dokter_jaga_id;
            $model->dokter_jaga = $dokter_jaga;
            $model->pendaftaran_id = $pendaftaran_id;
            $response = $this->_restIgd->get('asesmen-dokter/bundle-data-asesmen-dokter', ['query'=>['pendaftaran_id'=>$pendaftaran_id, 'pasien_id' => $data_pasien['pasien_id']]]);
            $databundle = json_decode($response->getBody(), true);
            $databundle = $databundle['response'];

            $model->attributes = $databundle['data-asesmen-dokter'];

            $text_diagnosakerja_id = '';
            // var_dump($databundle['data-asesmen-dokter']['riwayat_dahulu']); die();
            if(isset($databundle['data-asesmen-dokter']['diagnosakerja_id'])){
                // $arr_diagnosakerja_id = json_decode($databundle['data-asesmen-dokter']['diagnosakerja_id'],TRUE);
                if (isset($databundle['data-asesmen-dokter']['diagnosakerja_id']['id'])) {
                    $model->diagnosakerja_id = $databundle['data-asesmen-dokter']['diagnosakerja_id']['id'].'_'.$databundle['data-asesmen-dokter']['diagnosakerja_id']['text'];
                }else{
                    $model->diagnosakerja_id = $databundle['data-asesmen-dokter']['diagnosakerja_id']['text'];
                }
                // $model->diagnosakerja_id = @$arr_diagnosakerja_id['id'].'_'.@$arr_diagnosakerja_id['text'];
                $text_diagnosakerja_id = $databundle['data-asesmen-dokter']['diagnosakerja_id']['text'];
            }
            if(isset($databundle['data-asesmen-dokter']['riwayat_dahulu'])){
                    $valRiwayatDahulu = [];
                    // $arr_riwayat_dahulu = json_decode($databundle['data-asesmen-dokter']['riwayat_dahulu'],TRUE);
                    $arr_riwayat_dahulu = $databundle['data-asesmen-dokter']['riwayat_dahulu'];
                    $keyRiwayatDahulu = [];
                    foreach ($arr_riwayat_dahulu as $valDiag) {
                        if(isset($valRiwayatDahulu['id'])){
                            $keyRiwayatDahulu[] = $valDiag['id'].'_'.$valDiag['text'];
                            $valRiwayatDahulu[] = [$valDiag['id'].'_'.$valDiag['text'] => $valDiag['text']];
                        }else{
                            $valRiwayatDahulu[] = [$valDiag['text']=>$valDiag['text']];
                            $keyRiwayatDahulu[] = $valDiag['text'];
                        }
                    }
                    $model->riwayat_dahulu = $keyRiwayatDahulu;
                    unset($databundle['data-asesmen-dokter']['riwayat_dahulu']);
                }
            $model->tgl_asesmen = isset($databundle['data-asesmen-dokter']['tgl_asesmen']) ? date('d F y H:i:s',strtotime($databundle['data-asesmen-dokter']['tgl_asesmen'])) : date('d F y H:i:s');
            $data_gcs = ( isset($databundle['data-gcs']) && !empty($databundle['data-gcs']) ) ? $databundle['data-gcs'] : [];
            $data_listgcs = ( isset($databundle['data-listgcs']) && !empty($databundle['data-listgcs']) ) ? $databundle['data-listgcs'] : [];
            $data_gcsEye = ( isset($data_listgcs['eye']) && !empty($data_listgcs['eye']) ) ? $data_listgcs['eye'] : [];
            $data_gcsVerbal = ( isset($data_listgcs['verbal']) && !empty($data_listgcs['verbal']) ) ? $data_listgcs['verbal'] : [];
            $data_gcsMotorik = ( isset($data_listgcs['motorik']) && !empty($data_listgcs['motorik']) ) ? $data_listgcs['motorik'] : [];
            $gcsEyeOptions = [];
            $gcsVerbalOptions = [];
            $gcsMotorikOptions = [];
            if($data_gcsEye){
                foreach ($data_gcsEye as $keyEye => $valueEye) {
                    $gcsEyeOptions[$valueEye['metodegcs_id']]['data-nilai'] = $valueEye['metodegcs_nilai'];
                    $data_gcsEye[$keyEye]['nama_and_nilai'] = $valueEye['metodegcs_nama'].' - '.$valueEye['metodegcs_nilai'];
                }
            }
            if($data_gcsVerbal){
                foreach ($data_gcsVerbal as $keyVerbal => $valueVerbal) {
                    $gcsVerbalOptions[$valueVerbal['metodegcs_id']]['data-nilai'] = $valueVerbal['metodegcs_nilai'];
                    $data_gcsVerbal[$keyVerbal]['nama_and_nilai'] = $valueVerbal['metodegcs_nama'].' - '.$valueVerbal['metodegcs_nilai'];
                }
            }
            if($data_gcsMotorik){
                foreach ($data_gcsMotorik as $keyMotorik => $valueMotorik) {
                    $gcsMotorikOptions[$valueMotorik['metodegcs_id']]['data-nilai'] = $valueMotorik['metodegcs_nilai'];
                    $data_gcsMotorik[$keyMotorik]['nama_and_nilai'] = $valueMotorik['metodegcs_nama'].' - '.$valueMotorik['metodegcs_nilai'];
                }
            }

            $data_bagiantubuh = ( isset($databundle['data-bagiantubuh']) && !empty($databundle['data-bagiantubuh']) ) ? $databundle['data-bagiantubuh'] : [];
            $data_detailbagiantubuh = ( isset($databundle['data-detailbagiantubuh']) && !empty($databundle['data-detailbagiantubuh']) ) ? $databundle['data-detailbagiantubuh'] : [];

            $data_anatomiPasien = isset($databundle['data-anatomi']) ? $databundle['data-anatomi'] : [];
            $jsonAnatomi = json_encode($data_anatomiPasien,JSON_FORCE_OBJECT);
            $counter = count($data_anatomiPasien) + 1;

            $list_jenis_asmenperawat = isset($databundle['data-jenisasesmen']) ? $databundle['data-jenisasesmen'] : [];

            $historyPenyakit = [];
            if (isset($databundle['history-asesmen-dokter']) && !empty($databundle['history-asesmen-dokter'])) {
                foreach ($databundle['history-asesmen-dokter'] as $key => $value) {
                    $value = json_decode($value['riwayat_dahulu'], true);

                    if (!in_array($value, $historyPenyakit)) {
                        $historyPenyakit[] = $value;
                    }
                }
            }

            if(isset($userIdentity['kelompokpegawai_id']) && $userIdentity['kelompokpegawai_id'] == DocoConstants::KELOMPOK_KEPERAWATAN){
                $hide = 'hide()';
                $status_disabled = true;
            } else {
                $hide = 'show()';
                $status_disabled = false;
            }

            return $this->renderAjax('asesmen-dokter/index', get_defined_vars());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        }
    }

    public function actionSimpanAsesmenDokter()
    {
        try{
            $data = Yii::$app->request->post();
            $data['AsesmenMedisRDForm']['tgl_asesmen'] = date('Y-m-d H:i:s');
            $model = new AsesmenMedisRDForm;

            if (isset($data['key_riwayat_dahulu']) && $data['key_riwayat_dahulu'] != '') {
                $keyRiwayatDahulu = json_decode($data['key_riwayat_dahulu']);

                $newText = [];
                if ($data['AsesmenMedisRDForm']['riwayat_dahulu']) {
                    foreach ($data['AsesmenMedisRDForm']['riwayat_dahulu'] as $newRiwayat) {
                        $temp = explode('_', $newRiwayat);
                        if ($temp && count($temp) > 1) {
                            $newText[$newRiwayat] = $temp[1];
                        } else {
                            $newText[$newRiwayat] = $temp[0];
                        }
                    }
                }

                $existText = [];
                if ($keyRiwayatDahulu) {
                    if (!empty($keyRiwayatDahulu)) {
                        foreach ($keyRiwayatDahulu as $key => $value) {
                            foreach ($value as $each) {
                                $existText[] = $each->text;
                            }
                        }
                    }
                }

                $filterText = array_keys(array_diff($newText, $existText));

                $data['AsesmenMedisRDForm']['riwayat_dahulu'] = $filterText ? : [];
            }

            $model->load($data);
            if ($model->validate()) {
                $post = $this->_restIgd->post('asesmen-dokter/save-asesmen-dokter', [
                    'form_params' => $data
                ]);
                $rest = json_decode($post->getBody(), true);
                return DocoHelpers::response($rest['response'],$rest['metadata']['status'],substr(strrchr(get_class($model), "\\"), 1));
            } else {
                $response = $model->errors;
                return DocoHelpers::response($response, 422, substr(strrchr(get_class($model), "\\"), 1));
            }
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        }
    }

    public function actionCetakPdfAsesmenDokter()
    {
        $params = Yii::$app->request;
        $pendaftaran_id = DocoHelpers::decrypt($params->get('id','MA'));
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $nama_usercetak = Yii::$app->session->get('user_identity')['nama'];
        $id_usercetak = Yii::$app->session->get('user_identity')['id_pegawai'];
        $path = Yii::getAlias("@download") . "/asesmen-dokter.pdf";
        try {
            $response = $this->_restIgd->get('asesmen-dokter/cetak-pdf-asesmen-dokter',[
                'query' => [
                    'pendaftaran_id'=>$pendaftaran_id,
                    'ruangan_id' => $ruangan_id,
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