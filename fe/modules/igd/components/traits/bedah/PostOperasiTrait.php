<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-09 13:38:03
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-10 17:23:58
 */

namespace app\modules\igd\components\traits\bedah;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;

use Doco\igd\models\PostOperasiForm;
use Doco\igd\models\PostTambahInfusForm;

trait PostOperasiTrait
{
    public function actionPostOperasi($id)
    {
        $request = Yii::$app->request;
        $model = new PostOperasiForm;
        $unique = '';
        $options = [];
        $ruangan = [];
        $cache = Yii::$app->cache;
        $jamselesaiop = '';
        if($request->post()){
            $post = $request->post();
            $model->load($post);
            $model->perawat_datang = !empty($model->perawat_datang) ? DocoHelpers::convertIndoToEnglish($model->perawat_datang, true, true) : '';
            $model->pemberitahu_perawat =!empty($model->pemberitahu_perawat) ? DocoHelpers::convertIndoToEnglish($model->pemberitahu_perawat, true, true) : '';
            if($model->validate()){
                $unique = DocoHelpers::encrypt($model->inpostoperasi_id).'-'.DocoHelpers::encrypt($model->pasienmasukpenunjang_id);
                $formdata = $model->attributes;
                $formdata['is_post'] = true;
                $listCache = ['pegawaioperasi', 'itemoperasi', 'penggunaanbmhp', 'penggunaancairan', 'pemeriksaanpelengkap', 'alatditubuh', 'konsultindakan', 'pemasanganinfus'];
                $dataCache = [];
                foreach ($listCache as $k => $v) {
                    $dataCache[$v] = ($cache->get($v.'-'.$unique) == false) ? [] : $cache->get($v.'-'.$unique);
                }
                if(isset($dataCache['pemasanganinfus'])){
                    $newData = [];
                    foreach ($dataCache['pemasanganinfus'] as $x => $y) {
                        $y['tgl_pemasangan'] = DocoHelpers::convertIndoToEnglish($y['tgl_pemasangan'], true, true);
                        $newData[] = $y;
                    }
                    $dataCache['pemasanganinfus'] = $newData;
                }
                $response = $this->_restBedah->post('intra-operasi/save', ['form_params'=>['data'=>$formdata,'additional'=>$dataCache]]);
                $body = json_decode($response->getBody(), true);
                return DocoHelpers::response($body);
            }else{
                $formName = substr(strrchr(get_class($model), "\\"), 1);
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }
        try {
            $listRequest = ['kesadaran_umum', 'tingkat_kesadaran', 'jalan_napas', 'terapi_oksigen', 'keadaan_kulit', 'sirkulasi_badan', 'metode_nyeri'];
            $response = $this->_restBedah->get('allow/pack-post', 
                [
                    'query'=>['id'=>$id],
                    'form_params'=>$listRequest
                ]);
            $body = json_decode($response->getBody(), true);
            $data = $body['response']['data'];
            $jamselesaiop = $data['selesai_operasi'];
            $options = $body['response']['lookup'];
            $ruangan = $body['response']['ruangan'];
            $model->attributes = $data;
            $unique = DocoHelpers::encrypt($model->inpostoperasi_id).'-'.DocoHelpers::encrypt($model->pasienmasukpenunjang_id);
        } catch (Exception $e) {
            $model->attributes = [];
        }
        $model->is_recovery = empty($model->is_recovery) ? 1 : 0;
        $model->is_skrining_nyeri = empty($model->is_skrining_nyeri) ? 1 : 0;
        $model->is_pasanginfus = empty($model->is_pasanginfus) ? 1 : 0;
        if($model->is_recovery){
            $model->jam_masuk_rec = empty($model->jam_masuk_rec) ? $jamselesaiop : $model->jam_masuk_rec;
            $model->jam_keluar_rec = empty($model->jam_keluar_rec) ? $jamselesaiop : $model->jam_keluar_rec;
        }
        return $this->renderAjax('detail-partial/_postoperasi',get_defined_vars());
    }
    public function actionPostTambahInfus()
    {
        $model = new PostTambahInfusForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $title = Yii::t('fe', 'Tambah cairan infus');
        $request = Yii::$app->request;
        if($request->post()){
            $post = $request->post();
            $model->load($post);
            if($model->validate()){
                $inpostid = DocoHelpers::encrypt($model->inpostoperasi_id);
                $pasienpenunjangid = DocoHelpers::encrypt($model->pasienmasukpenunjang_id);
                $cacheName = 'pemasanganinfus-'.$inpostid.'-'.$pasienpenunjangid;
                $data = $model->attributes;
                return DocoHelpers::response($this->saveToCache($cacheName, $data));
            }else{
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }
        $options = [];

        return $this->renderAjax('detail-partial/postoperasi/_tambahinfus', get_defined_vars());
    }

}