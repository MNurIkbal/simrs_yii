<?php

/**
 * @Author: rizal@docotel.com
 */

namespace app\modules\mcu\components\traits;

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
use app\modules\mcu\models\ResumeMedisForm;

trait KesimpulanTrait 
{
    public function actionKesimpulan()
    {
        $title = Yii::t('fe', 'Hasil MCU');
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('id', null);
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        $pasien_id = $request->get('pasien_id', null);
        $pasien_id = DocoHelpers::decrypt($pasien_id);
        $ruangan_id = $request->get('ruangan_id', null);
        $ruangan_id = DocoHelpers::decrypt($ruangan_id);
        $pegawai_id = Yii::$app->user->identity->id_pegawai;
        $pegawai_nama = Yii::$app->user->identity->nama_pegawai;
        $data = $this->_restMcu->get('pemeriksaan/data-mcu-prima?id=' . $pendaftaran_id);
        $data = json_decode($data->getBody(), true);
        $data = $data["response"];
        $dataFisik = isset($data['pemeriksaanFisik']['pemeriksaan_fisik']) ? json_decode($data['pemeriksaanFisik']['pemeriksaan_fisik'], true) : [];
        $dataRiwayat = isset($data['riwayat']['additional_data']) ? json_decode($data['riwayat']['additional_data'], true) : [];
        $dataResume = isset($data['resume']['resume_pemeriksaan']) ? json_decode($data['resume']['resume_pemeriksaan'], true) : [];
        $dKesimpulan = $this->_restMcu->get('allow/kesimpulan?id='.$pendaftaran_id);
        $dKesimpulan = json_decode($dKesimpulan->getBody(), true);
        $dKesimpulan = $dKesimpulan["response"];
        if(isset($dataRiwayat['pasien_phr']) || isset($dataFisik['keadaan_umum']) || isset($dataFisik['resume_hasil_pemeriksaan'])){
            $isCetakMcu = true;
        }else{
            $isCetakMcu = $dKesimpulan["isCetak"];
        }
        $dKesimpulan = $dKesimpulan["data_kesimpulan"];
        $modelKesimpulan = new ResumeMedisForm;
        $modelKesimpulan->scenario = 'pemeriksaan-mcu';
        $formName = substr(strrchr(get_class($modelKesimpulan), "\\"), 1);
        if($post = $request->post()) {
            $data = $post['ResumeMedisForm'];
            $modelKesimpulan->attributes = $data;
            $modelKesimpulan->pendaftaran_id = $pendaftaran_id;
            $modelKesimpulan->pasien_id = $pasien_id;
            $modelKesimpulan->ruanganterakhir_id = $ruangan_id;
            $modelKesimpulan->pegawai_id = $pegawai_id;
            $modelKesimpulan->tgl_resume = date('Y-m-d H:i:s');
            if($modelKesimpulan->validate()) {
                if (!empty($dKesimpulan)) {
                    $request = $this->_restMcu->post('pemeriksaan/update-kesimpulan?id='.DocoHelpers::encrypt($pendaftaran_id), [
                        'form_params' => $modelKesimpulan->attributes
                    ]);
                }else{
                    $request = $this->_restMcu->post('pemeriksaan/save-kesimpulan', [
                        'form_params' => $modelKesimpulan->attributes
                    ]);
                }
                $response = json_decode($request->getBody(), true);
                return DocoHelpers::response($response);
            }
            else {
                $response = $modelKesimpulan->errors;
                return DocoHelpers::response($response, 422, $formName);
            }
        }
        else {
            $ikhtisar_singkat = isset($dKesimpulan["ikhtisar_singkat"]) ? $dKesimpulan["ikhtisar_singkat"] : null;
            $kesimpulan = isset($dKesimpulan["kesimpulan"]) ? $dKesimpulan["kesimpulan"] : null;
            $saran = isset($dKesimpulan["saran"]) ? $dKesimpulan["saran"] : null;
            $catatan = isset($dKesimpulan["catatan"]) ? $dKesimpulan["catatan"] : null;
            $modelKesimpulan->pegawai_nama = $pegawai_nama;
            $modelKesimpulan->ikhtisar_singkat = $ikhtisar_singkat;
            $modelKesimpulan->kesimpulan = $kesimpulan;
            $modelKesimpulan->saran = $saran;
            $modelKesimpulan->catatan = $catatan;

            $response = $this->_restMcu->get('allow/get-api');
            $response = json_decode($response->getBody(), true);
            $response = $response["response"];
            $data_saran = isset($response['data_saran']) ? $response['data_saran'] : null;

            return $this->renderAjax('__kesimpulan', get_defined_vars());
        }
    }

    public function actionCetakReport($id)
    {
        $request = Yii::$app->request;
        $id = DocoHelpers::decrypt($id);
        $path = Yii::getAlias("@download")."/cetak-report-mcu.pdf";
        $userIdentity = Yii::$app->session->get('user_identity');
        $response = $this->_restMcu->get('pemeriksaan/cetak-report-mcu', [
            'query' => [
                'id' => $id,
                'nama_pegawai' => $userIdentity['nama_pegawai']
            ],
            'save_to' => $path
        ]);

        $body = json_decode($response->getBody(), true);
        // dump($body);die;
        return DocoHelpers::previewPdf($path);
    }
}