<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-09 13:38:03
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-10 17:23:58
 */

namespace app\modules\bedah\components\traits;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;

use Doco\bedah\models\PostOperasiForm;
use Doco\bedah\models\PostTambahInfusForm;

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
        $model->last = $request->get('last', false);
        if($request->post()){
            $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
            $post = $request->post();
            $model->load($post);
            $model->perawat_datang = !empty($model->perawat_datang) ? DocoHelpers::convertIndoToEnglish($model->perawat_datang, true, true) : '';
            $model->pemberitahu_perawat =!empty($model->pemberitahu_perawat) ? DocoHelpers::convertIndoToEnglish($model->pemberitahu_perawat, true, true) : '';
            if($model->validate()){
                $unique = DocoHelpers::encrypt($model->inpostoperasi_id).'-'.DocoHelpers::encrypt($model->pasienmasukpenunjang_id);
                $formdata = $model->attributes;
                $formdata['is_post'] = true;
                $listCache = ['pemasanganinfus'];
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
                $response = $this->_restBedah->post('intra-operasi/save', [
                    'form_params'=>[
                        'data'=> $formdata,
                        'additional'=> $dataCache,
                        'ruangan_id' => $ruangan_id
                    ]
                ]);
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
            $jamselesaiop = isset($data['selesai_operasi']) ? $data['selesai_operasi'] : null;
            $options = $body['response']['lookup'];
            $ruangan = $body['response']['ruangan'];
            $model->attributes = $data;
            $unique = DocoHelpers::encrypt($model->inpostoperasi_id).'-'.DocoHelpers::encrypt($model->pasienmasukpenunjang_id);
        } catch (Exception $e) {
            $model->attributes = [];
        }
        $model->is_recovery = !is_null($model->is_recovery) ? !$model->is_recovery ? 0 : 1 : 1;
        $model->is_skrining_nyeri = !is_null($model->is_skrining_nyeri) ? !$model->is_skrining_nyeri ? 0 : 1 : 1;
        $model->is_pasanginfus = !is_null($model->is_pasanginfus) ? !$model->is_pasanginfus ? 0 : 1 : 1;
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