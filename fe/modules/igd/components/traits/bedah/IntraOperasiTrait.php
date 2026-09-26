<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-09 13:28:54
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-07 10:51:34
 */

namespace app\modules\igd\components\traits\bedah;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\components\DocoHelpers;

use Doco\igd\models\IntraOperasiForm;
use Doco\igd\models\IntraPegawaiOperasiForm;
use Doco\igd\models\IntraItemOperasiForm;
use Doco\igd\models\IntraPenggunaanCairanForm;
use Doco\igd\models\IntraAlatDitubuhForm;
use Doco\igd\models\IntraPemeriksaanPelengkapForm;
use Doco\igd\models\IntraKonsulTindakanForm;
use Doco\igd\models\IntraPenggunaanBmhpForm;

trait IntraOperasiTrait
{
    public function actionIntraOperasi($id)
    {
        $request = Yii::$app->request;
        $model = new IntraOperasiForm;
        $unique = '';
        $options = [];
        $opsi['set_instrumen'] = [];
        $opsi['penunjang_khusus'] = [];
        $opsi['penerima'] = [];
        $data = $options = $infopasien = $infopasiendetail = [];
        $cache = Yii::$app->cache;
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        if($request->post()){
            $post = $request->post();
            $model->load($post);
            if($model->validate()){
                $response = $this->_restBedah->post('intra-operasi/save', ['form_params'=>['data'=>$model->attributes]]);
                $body = json_decode($response->getBody(), true);
                return DocoHelpers::response($body['response']);
            }else{
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }
        try {
            $listRequest = ['keadaan_kulit', 'posisi_operasi', 'pencucian_operasi', 'posisi_elektroda'];
            $response = $this->_restBedah->get('allow/pack-intra', 
                [
                    'query'=>['id'=>$id],
                    'form_params'=>$listRequest
                ]);
            $body = json_decode($response->getBody(), true);
            $data = $body['response']['data'];
            $options = $body['response']['lookup'];
            $infopasien = $body['response']['infopasien']['data'];
            $infopasiendetail = $body['response']['infopasien']['detail'];
            $model->attributes = $data;
            $model->is_surgicalsavety = ($model->is_surgicalsavety == true) ? 1 : 0;
            $model->is_diathermy = ($model->is_diathermy == true) ? 1 : 0;
            $model->is_diathermy = ($model->is_diathermy == true) ? 1 : 0;
            $model->is_jaringantubuh = ($model->is_jaringantubuh == true) ? 1 : 0;
            $model->is_diserahkan = ($model->is_diserahkan == true) ? 1 : 0;
            $opsi['set_instrumen'] = (!empty($data['setInstrumen'])) ? $data['setInstrumen'] : [];
            $opsi['penunjang_khusus'] = (!empty($data['setPenunjang'])) ? $data['setPenunjang'] : [];
            $opsi['penerima'] = (!empty($data['pegawaiPenerima'])) ? $data['pegawaiPenerima'] : [];
            $unique = DocoHelpers::encrypt($model->inpostoperasi_id).'-'.DocoHelpers::encrypt($model->pasienmasukpenunjang_id);
            if(!$cache->get('itemoperasi-'.$unique)){
                if(count($infopasiendetail) > 0){
                    $arrItemOperasi = [];
                    $bmhp = [];
                    $daftartindakanId = [];
                    foreach ($infopasiendetail as $key => $value) {
                        $arrItemOperasi['inpostoperasi_id'] = $model->inpostoperasi_id;
                        $arrItemOperasi['pasienmasukpenunjang_id'] = $model->pasienmasukpenunjang_id;
                        $arrItemOperasi['daftartindakan_id'] = $value['daftartindakan_id'];
                        $arrItemOperasi['daftartindakan_nama'] = $value['daftartindakan_nama'];
                        $arrItemOperasi['is_cyto'] = $value['cyto_tindakan'];
                        $arrItemOperasi['tarif_satuan'] = $value['tarif_satuan'];
                        $arrItemOperasi['tarif_tindakan'] = $value['tarif_tindakan'];
                        $arrItemOperasi['tarif_cyto'] = empty($value['tarifcyto_tindakan']) ? 0 : $value['tarifcyto_tindakan'];
                        $arrItemOperasi['cyto'] = ($value['cyto_tindakan']) ? '✓' : '';
                        $arrItemOperasi['operasi_id'] = $value['operasi_id'];
                        $arrItemOperasi['jenis_luka'] = '';
                        $arrItemOperasi['jenis_luka_nama'] = '';
                        $arrItemOperasi['golonganoperasi_id'] = $value['golonganoperasi_id'];
                        $arrItemOperasi['golonganoperasi_nama'] = $value['golonganoperasi_nama'];
                        $arrItemOperasi['jenisanastesi_id'] = '';
                        $arrItemOperasi['jenisanastesi_nama'] = '';
                        $arrItemOperasi['default'] = 1;
                        if(!in_array($value['daftartindakan_id'], $daftartindakanId)){
                            $daftartindakanId[] = $value['daftartindakan_id'];
                        }
                    }
                    $this->saveToCache('itemoperasi-'.$unique, $arrItemOperasi);
                    try {
                        $bmhpResponse = $this->_restBedah->get('allow/compare-bmhp-ruangan', ['form_params'=>['daftartindakan_id'=>$daftartindakanId, 'ruangan_id'=>$ruangan_id]]);
                        $bmhpRes = json_decode($bmhpResponse->getBody(), true);
                        foreach ($bmhpRes['response'] as $key => $value) {
                            $newData = [];
                            $newData['pasienmasukpenunjang_id'] = $model->pasienmasukpenunjang_id;
                            $newData['inpostoperasi_id'] = $model->inpostoperasi_id;
                            $newData['daftartindakan_id'] = $value['daftartindakan_id'];
                            $newData['obatalkes_id'] = $value['obatalkes_id'];
                            $newData['obatalkes_nama'] = $value['obatalkes_namalain'];
                            $newData['persediaan'] = $value['qty_pemakaian'];
                            $newData['tambahan'] = 0;
                            $newData['terpakai'] = 0;
                            $newData['sisa'] = $value['qty_pemakaian'];
                            $newData['ditagihkan'] = '';
                            $newData['is_ditagihkan'] = '0';
                            $newData['is_available'] = ($value['is_available']) ? 1 : 0;
                            $newData['msg'] = $value['msg'];
                            $bmhp[] = $newData;
                        }
                    } catch (\RequestException $e) {
                        $bmhp = [];
                    }
                    $this->saveToCache('penggunaanbmhp-'.$unique, $bmhp);
                }
            }
        } catch (Exception $e) {
            $model->attributes = [];
        }
        return $this->renderAjax('detail-partial/_intraoperasi',get_defined_vars());
    }
    public function actionIntraTambahPegawai()
    {
        $model = new IntraPegawaiOperasiForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $title = Yii::t('fe', 'Tambah anggota tim operasi');
        $request = Yii::$app->request;
        if($request->post()){
            $post = $request->post();
            $model->load($post);
            if($model->validate()){
                $inpostid = DocoHelpers::encrypt($model->inpostoperasi_id);
                $pasienpenunjangid = DocoHelpers::encrypt($model->pasienmasukpenunjang_id);
                $cacheName = 'pegawaioperasi-'.$inpostid.'-'.$pasienpenunjangid;
                $data = $model->attributes;
                return DocoHelpers::response($this->saveToCache($cacheName, $data));
            }else{
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }
        $options = [];
        try {
            $response = $this->_restBedah->get('allow/get-tim-operasi');
            $body = json_decode($response->getBody(), true);
            $options = ArrayHelper::map($body['response'], 'lookup_id', 'lookup_name');
        } catch (Exception $e) {
            $model->attributes = [];
        }
        return $this->renderAjax('detail-partial/intraoperasi/_pegawaioperasi', get_defined_vars());
    }
    public function actionIntraTambahItemOperasi($key = null, $unique = null)
    {
        $model = new IntraItemOperasiForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $title = Yii::t('fe', 'Tambah list tindakan operasi');
        $request = Yii::$app->request;
        $options['jenisluka'] = [];
        $options['jenisanastesi'] = [];
        $opsi['daftartindakan'] = '';
        $opsi['golonganoperasi'] = [];
        $cache = Yii::$app->cache;
        $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
        if($request->post()){
            $post = $request->post();
            $model->load($post);
            if($model->validate()){
                $bmhp = [];
                $inpostid = DocoHelpers::encrypt($model->inpostoperasi_id);
                $pasienpenunjangid = DocoHelpers::encrypt($model->pasienmasukpenunjang_id);
                $cacheName = 'itemoperasi-'.$inpostid.'-'.$pasienpenunjangid;
                $cacheData = $cache->get($cacheName);
                try {
                    $response = $this->_restBedah->get('allow/compare-bmhp-ruangan', ['form_params'=>['daftartindakan_id'=>$model->daftartindakan_id, 'ruangan_id'=>$ruangan_id]]);
                    $body = json_decode($response->getBody(), true);
                    foreach ($body['response'] as $k => $value) {
                        $newData = [];
                        $newData['pasienmasukpenunjang_id'] = $model->pasienmasukpenunjang_id;
                        $newData['inpostoperasi_id'] = $model->inpostoperasi_id;
                        $newData['obatalkes_id'] = $value['obatalkes_id'];
                        $newData['daftartindakan_id'] = $value['daftartindakan_id'];
                        $newData['obatalkes_nama'] = $value['obatalkes_namalain'];
                        $newData['persediaan'] = $value['qty_pemakaian'];
                        $newData['tambahan'] = 0;
                        $newData['terpakai'] = 0;
                        $newData['sisa'] = $value['qty_pemakaian'];
                        $newData['ditagihkan'] = '';
                        $newData['is_ditagihkan'] = '0';
                        $newData['is_available'] = ($value['is_available']) ? 1 : 0;
                        $newData['msg'] = $value['msg'];
                        $newData['operasi_key'] = count($cacheData);
                        $bmhp[] = $newData;
                    }
                } catch (\RequestException $e) {
                    $bmhp = [];
                }
                $data = $model->attributes;
                if($key != ''){
                    return DocoHelpers::response($this->updateCache($cacheName, $data, $key));
                }
                if(count($bmhp) > 0){
                    $this->saveToCache('penggunaanbmhp-'.$inpostid.'-'.$pasienpenunjangid, $bmhp);
                }
                return DocoHelpers::response($this->saveToCache($cacheName, $data));
            }else{
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }
        try {
            $response = $this->_restBedah->get('allow/get-pack-item-operasi');
            $body = json_decode($response->getBody(), true);
            $options['jenisluka'] = ArrayHelper::map($body['response']['jenisluka'], 'lookup_id', 'lookup_name');
            $options['jenisanastesi'] = ArrayHelper::map($body['response']['jenisanastesi'], 'jenisanastesi_id', 'jenisanastesi_nama');
        } catch (Exception $e) {
            $options['jenisluka'] = [];
            $options['jenisanastesi'] = [];
            $model->attributes = [];
        }
        $model->default = 0;
        if($key != ''){
            $data = $cache->get($unique);
            $model->attributes = $data[$key];
            $opsi['daftartindakan'] = json_encode(['id'=>$data[$key]['daftartindakan_id'], 'name'=>$data[$key]['daftartindakan_nama']]);
            $opsi['golonganoperasi'] = [$data[$key]['golonganoperasi_id'] => $data[$key]['golonganoperasi_nama']];
        }
        return $this->renderAjax('detail-partial/intraoperasi/_itemoperasi', get_defined_vars());
    }
    public function actionIntraTambahPenggunaanCairan()
    {
        $model = new IntraPenggunaanCairanForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $title = Yii::t('fe', 'Tambah penggunaan cairan');
        $request = Yii::$app->request;
        if($request->post()){
            $post = $request->post();
            $model->load($post);
            if($model->validate()){
                $inpostid = DocoHelpers::encrypt($model->inpostoperasi_id);
                $pasienpenunjangid = DocoHelpers::encrypt($model->pasienmasukpenunjang_id);
                $cacheName = 'penggunaancairan-'.$inpostid.'-'.$pasienpenunjangid;
                $data = $model->attributes;
                return DocoHelpers::response($this->saveToCache($cacheName, $data));
            }else{
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }
        $options = [];

        return $this->renderAjax('detail-partial/intraoperasi/_penggunaancairan', get_defined_vars());
    }
    public function actionIntraTambahPenggunaanBmhp($key = null, $unique = null)
    {
        $model = new IntraPenggunaanBmhpForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $title = Yii::t('fe', 'Tambah penggunaan bmhp');
        $request = Yii::$app->request;
        $cache = Yii::$app->cache;
        $opsi = '';
        if($request->post()){
            $post = $request->post();
            $model->load($post);
            if($model->validate()){
                $inpostid = DocoHelpers::encrypt($model->inpostoperasi_id);
                $pasienpenunjangid = DocoHelpers::encrypt($model->pasienmasukpenunjang_id);
                $cacheName = 'penggunaanbmhp-'.$inpostid.'-'.$pasienpenunjangid;
                $data = $model->attributes;
                if($key != ''){
                    return DocoHelpers::response($this->updateCache($cacheName, $data, $key));
                }
                return DocoHelpers::response($this->saveToCache($cacheName, $data));
            }else{
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }
        $options = [];
        if($key != ''){
            $data = $cache->get($unique);
            $model->attributes = $data[$key];
            $opsi = json_encode(['id'=>$data[$key]['obatalkes_id'], 'name'=>$data[$key]['obatalkes_nama']]);
        }
        return $this->renderAjax('detail-partial/intraoperasi/_penggunaanbmhp', get_defined_vars());
    }
    public function actionIntraTambahPemeriksaanPelengkap()
    {
        $model = new IntraPemeriksaanPelengkapForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $title = Yii::t('fe', 'Tambah pemeriksaan pelengkap');
        $request = Yii::$app->request;
        if($request->post()){
            $post = $request->post();
            $model->load($post);
            if($model->validate()){
                $model->is_cyto = false;
                $model->tarif_cyto = 0;
                $inpostid = DocoHelpers::encrypt($model->inpostoperasi_id);
                $pasienpenunjangid = DocoHelpers::encrypt($model->pasienmasukpenunjang_id);
                $cacheName = 'pemeriksaanpelengkap-'.$inpostid.'-'.$pasienpenunjangid;
                $data = $model->attributes;
                return DocoHelpers::response($this->saveToCache($cacheName, $data));
            }else{
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }
        $options = [];

        return $this->renderAjax('detail-partial/intraoperasi/_pemeriksaanpelengkap', get_defined_vars());
    }
    public function actionIntraTambahAlatDitubuh()
    {
        $model = new IntraAlatDitubuhForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $title = Yii::t('fe', 'Tambah alat yang sengaja ditinggal dalam tubuh');
        $request = Yii::$app->request;
        if($request->post()){
            $post = $request->post();
            $model->load($post);
            if($model->validate()){
                $inpostid = DocoHelpers::encrypt($model->inpostoperasi_id);
                $pasienpenunjangid = DocoHelpers::encrypt($model->pasienmasukpenunjang_id);
                $cacheName = 'alatditubuh-'.$inpostid.'-'.$pasienpenunjangid;
                $data = $model->attributes;
                return DocoHelpers::response($this->saveToCache($cacheName, $data));
            }else{
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }
        $options = [];

        return $this->renderAjax('detail-partial/intraoperasi/_alatditubuh', get_defined_vars());
    }
    public function actionIntraTambahKonsulTindakan()
    {
        $model = new IntraKonsulTindakanForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $title = Yii::t('fe', 'Tambah konsultasi tindakan');
        $request = Yii::$app->request;
        if($request->post()){
            $post = $request->post();
            $model->load($post);
            if($model->validate()){
                $inpostid = DocoHelpers::encrypt($model->inpostoperasi_id);
                $pasienpenunjangid = DocoHelpers::encrypt($model->pasienmasukpenunjang_id);
                $cacheName = 'konsultindakan-'.$inpostid.'-'.$pasienpenunjangid;
                $data = $model->attributes;
                return DocoHelpers::response($this->saveToCache($cacheName, $data));
            }else{
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        }
        $options = [];

        return $this->renderAjax('detail-partial/intraoperasi/_konsultindakan', get_defined_vars());
    }
}