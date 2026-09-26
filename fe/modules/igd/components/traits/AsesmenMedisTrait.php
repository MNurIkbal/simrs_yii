<?php

/**
 * @Author: Aris Munandar
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
use app\modules\igd\models\AsesmenMedisIgdForm;
use app\components\Services\AksesFormService;

trait AsesmenMedisTrait
{
    /**
     * This function will return new form of asesmen medis
     *
     * @return Json
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionFormAsesmenMedis()
    {
        //extension asesmen_medis, defaultnya ke
        return Yii::$app->docoPlugin->execute($this, 'asesmen_medis');
    }

    /**
     * List dropdown of doctor
     *
     * @param Integer $page
     * @return JSON
     * @author Aris Munandar
     **/
    public function actionDiagnosaList()
    {
        return $this->helper->guzzleExec($this->_restIgd, [
            'url' => 'asesmen-medis/diagnosa-list',
            'returnResponse' => true,
            'payload' => [
                'query' => Yii::$app->request->get('payload', [])
            ],
        ]);
    }

    /**
     * This function will save data from view
     *
     * @return Json
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionSaveAsesmenMedis()
    {
        $model = new AsesmenMedisIgdForm;
        $request = Yii::$app->request->post();

        $request['tgl_pasien_datang'] = (date_create_from_format('d/m/Y H:i:s', $request['tgl_pasien_datang'])) ? date_format(date_create_from_format('d/m/Y H:i:s', $request['tgl_pasien_datang']), 'Y-m-d H:i:s') : $request['tgl_pasien_datang'];
        $request['tgl_asesmen'] = (date_create_from_format('d/m/Y H:i:s', $request['tgl_asesmen'])) ? date_format(date_create_from_format('d/m/Y H:i:s', $request['tgl_asesmen']), 'Y-m-d H:i:s') : $request['tgl_asesmen'];
        $model->attributes = $request;
        $model->tekanandarah = empty($model->tekanandarah) ? trim(str_replace(',', '.', $model->tekanandarah)) : $model->tekanandarah;
        $model->nadi = empty($model->nadi) ? trim(str_replace(',', '.', $model->nadi)) : $model->nadi;
        $model->suhu = empty($model->suhu) ? trim(str_replace(',', '.', $model->suhu)) : $model->suhu;
        $model->saturasi_o2 = empty($model->saturasi_o2) ? trim(str_replace(',', '.', $model->saturasi_o2)) : $model->saturasi_o2;

        // Mapping asesmen allo or auto
        if ($model->allo_or_auto == '0') {
            $model->asesmen_auto = 1;
        } else if ($model->allo_or_auto == '1') {
            $model->asesmen_allo = 1;
        } else {
            $model->asesmen_auto = null;
            $model->asesmen_auto = null;
        }
        $model->allo_or_auto = null;
        // hapus semua cache
        Yii::$app->cache->delete($this->_pegawai_id.'-latest-data-asesmen-medis-igd-'.$model->pendaftaran_id);
        Yii::$app->cache->delete($this->_pegawai_id.'-updated-data-asesmen-medis-igd-'.$model->pendaftaran_id);

        return $this->guzzleExec($this->_restIgd, [
            'url'    => 'asesmen-medis/save-medis',
            'method' => 'POST',
            'payload' => [
                'form_params' => [
                    'formdata' => array_merge($model->attributes, [
                        'pendaftaran_id' => $this->helper->decrypt($model->pendaftaran_id),
                        'anatomi'        => Yii::$app->request->post('periksatubuh')
                    ])
                ]
            ],
            'returnResponse' => true
        ]);
    }

    // Export pdf
    public function actionCetakAsmedRd($id)
    {
        \app\components\EsignHelpers::previewEsign([
            'type' => 'Asemen Medis Rawat Darurat',
            'pendaftaran_id' => DocoHelpers::decrypt($id),
        ]);
        $request = Yii::$app->request;
        $tipe = $request->get('tipe', null);

        if($tipe == 'kramat'){
            $url = 'asesmen-medis/cetak-asmed-rd-kramat';
        }else{
            $url = 'asesmen-medis/cetak-asmed-rd';
        }

        // Download path
        $path = Yii::getAlias("@download") . "/asesmen_medis_igd" . uniqid() . ".pdf";

        // Response
        $response = $this->_restIgd->get($url.'?id=' . $id, [
            'save_to' => $path
        ]);

        $body = json_decode($response->getBody(), true);
        //return DocoHelpers::previewPdf($path);
        return DocoHelpers::previewPdf($path, null, true);
    }

    // set cache ketika form asmed on change
    public function actionSetCacheAsmed() {
        $is_draft = 0;
        $pendaftaran_id = Yii::$app->request->get('pendaftaran_id');
        $updated_asmed = Yii::$app->request->get('AsesmenMedisIgdForm');
        unset($updated_asmed['imt_kategori'], $updated_asmed['imt'], $updated_asmed['bb_ideal']);
        $updated_asmed['diagnosis'] = json_decode(Yii::$app->request->get('diagnosis'));
        $updated_asmed['diagnosis_raw'] = json_decode(Yii::$app->request->get('diagnosis_raw'));
        $updated_asmed['skala_nyeri'] = Yii::$app->request->get('skala_nyeri');
        $updated_asmed['skala_nyeri_anak'] = Yii::$app->request->get('skala_nyeri_anak');

        // default value checkbox
        if (!isset($updated_asmed['asesmen_auto'])) $updated_asmed['asesmen_auto'] = 0;
        if (!isset($updated_asmed['asesmen_allo'])) $updated_asmed['asesmen_allo'] = 0;
        if (!isset($updated_asmed['asesmen_allo_anamnesa'])) $updated_asmed['asesmen_allo_anamnesa'] = '';
        
        // cek perbedaan data pada form
        $asmed_cache = Yii::$app->cache->get($this->_pegawai_id.'-latest-data-asesmen-medis-igd-'.$pendaftaran_id);
        foreach ($asmed_cache as $key => $value) {
            if (isset($updated_asmed[$key])) {
                if (is_null($value) && ($updated_asmed[$key] === '') || ($updated_asmed[$key] === '0')) continue;

                if ($key == 'diagnosis') {
                    if (count($value) != count($updated_asmed[$key])) {
                        $asmed_cache[$key] = $updated_asmed[$key];
                        if (!$is_draft) $is_draft = 1;
                    } elseif (count($value) != 0 && count($updated_asmed[$key]) != 0) {
                        foreach ($value as $k => $v) {
                            if ($v != $updated_asmed[$key][$k]) {
                                $asmed_cache[$key] = $updated_asmed[$key];
                                if (!$is_draft) $is_draft = 1;
                                break;
                            }
                        }
                    }
                } else if ($value != $updated_asmed[$key]) {
                    $asmed_cache[$key] = $updated_asmed[$key];
                    if (!$is_draft) $is_draft = 1;
                }

            }
        }

        $asmed_cache['diagnosis_raw'] = $updated_asmed['diagnosis_raw'];

        // set cache jika ada perubahan, delete cache jika tetap sama
        if ($is_draft) {
            // digunakan di AsesmenMedisProcess actionSetCacheAsmed processFlow()
            Yii::$app->cache->set($this->_pegawai_id.'-updated-data-asesmen-medis-igd-'.$pendaftaran_id, $asmed_cache, DocoConstants::EXPIRED_CACHE);
        } else {
            Yii::$app->cache->delete($this->_pegawai_id.'-updated-data-asesmen-medis-igd-'.$pendaftaran_id);
        }

        return DocoHelpers::response(["is_draft" => $is_draft]);
    }
}
