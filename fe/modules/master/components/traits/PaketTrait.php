<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-04-27 11:26:13
 * @Last Modified by:   Sigit
 * @Last Modified time: 2019-03-22 15:02:07
 */

namespace app\modules\master\components\traits;

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
use app\modules\master\models\TipePaketForm;


trait PaketTrait
{
    
    public function actionPaket()
    {
        $title = Yii::t('fe', 'Master kategori');
        $status = ['true'=>Yii::t('fe', 'Aktif'), 'false'=>Yii::t('fe','Tidak aktif')];
        return $this->renderAjax('components/paket/index', get_defined_vars());
    }

    public function actionDelete($id = null){
         // Decrypt id
        $id = DocoHelpers::decrypt($id);

        // Try catch
        try {
            // Get response
            $response = $this->_restMaster->POST('tipe-paket/delete?id=' . $id);
            $response = json_decode($response->getBody(), true);
            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            // Return
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            // Return
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionGetDataPaket()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        // try {
            $response = $this->_restMaster->get('tipe-paket/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['tipepaket_id']);
                $value['primary'] = $primaryKey;
                $value['is_active'] = $value['is_active'];
                $value['status_label'] = ($value['is_active']) ? Yii::t('fe','Aktif') : Yii::t('fe','Tidak aktif');
                $value['catatan'] = '';
                $value['rowNum'] = $no;
                $value['detail'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                    'class' => 'btn btn-sm btn-success', 'data-source'=> "/master/tindakan/sub-paket?id=".$primaryKey.'&is_mcu='.$value['is_mcu'],'onclick'=> 'docoHelper.detail(this)']);
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        // } catch (RequestException $e) {
        //     $result['error'] = $e->getMessage();
        //     return $result;
        // } catch (\Exception $e) {
        //     $result['error'] = $e->getMessage();
        //     return $result;
        // }
    }

    public function actionCreatePaketA()
    {
        $title = 'Tambah Paket Tindakan';
        $model = new TipePaketForm;
        $request = Yii::$app->request;
        $post = $request->post();
        Yii::$app->cache->delete("cache_tampung_tindakan");

        $session_id = Yii::$app->docoVars->user("id");
        Yii::$app->cache->delete("cache_tampung_tindakan_".$session_id);
        $tipepaket_id = null;
        $disable = false;

        return $this->renderAjax('components/paket/form', get_defined_vars());
    }

    public function actionSimpanPaket(){
        // Get request
        $request = Yii::$app->request;
        $model = new TipePaketForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        // Check post
        if ($request->post()) {
            $postData = $request->post('TipePaketForm');
            $model->load($postData);
            $model->attributes = $postData;
            $model->tipepaket_id = DocoHelpers::decrypt($postData['tipepaket_id']);
            $session_id = Yii::$app->docoVars->user("id");
            $cache_tindakan = Yii::$app->cache->get("cache_tampung_tindakan_".$session_id);

            // Validate model
            if ($model->validate()) {
                $formData = $request->post(); // tampung formdata
                $form_params = array(
                    'tipepaket_id' => DocoHelpers::decrypt($formData['TipePaketForm']['tipepaket_id']),
                    'tipepaket_nama' => $formData['TipePaketForm']['tipepaket_nama'],
                    'tipepaket_kode' => $formData['TipePaketForm']['tipepaket_kode'],
                    'tipepaket_namalainnya' => $formData['TipePaketForm']['tipepaket_namalainnya'],
                    'keterangan_tipepaket' => $formData['TipePaketForm']['keterangan_tipepaket'],
                    'is_active' => $formData['TipePaketForm']['is_active'],
                    'is_mcu' => $formData['TipePaketForm']['is_mcu'],
                );
                $form_params['daftarTindakan'] = [];
                if(!empty($cache_tindakan)){
                    $form_params['daftarTindakan'] = json_encode($cache_tindakan);
                }

                $response = $this->_restMaster->post('tipe-paket/simpan-paket', [
                    'form_params' => $form_params
                ]);

                $body = json_decode($response->getBody(),true);
                return DocoHelpers::response($body,false, $formName);
            } else {
                // Errors
                $errors = DocoHelpers::parseError($model->errors, $formName);

                // Return
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }

        } else {
            // Return form
            return $this->renderPartial('components/paket/form', get_defined_vars());
        }
    }

    public function actionViewPaketA($id = null)
    {
        $title = 'Ubah Paket Tindakan';
        $model = new TipePaketForm;
        $request = Yii::$app->request;
        $post = $request->post();
        $id_encrypt = $id;
        $id = DocoHelpers::decrypt($id);
        $response = $this->_restMaster->get('tipe-paket/view?id=' . $id);
        $body = json_decode($response->getBody(), true);
        $attributes = $body['response']['data'];
        $model->attributes = $attributes;
        $model->tipepaket_id = $id_encrypt;
        $dataMaping = $body['response']['data_maping'];
        $session_id = Yii::$app->docoVars->user("id");
        Yii::$app->cache->delete("cache_tampung_tindakan_".$session_id);
        $tipepaket_id = $id;

        $disable = ($dataMaping) ? true : false;
        return $this->renderAjax('components/paket/form', get_defined_vars());
    }


    public function actionUpdatePaket($tipepaket_id = null)
    {
        // Get request
        $request = Yii::$app->request;
        $tipepaket_id = DocoHelpers::decrypt($tipepaket_id);
        $model = new TipePaketForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);

        // Check post
        if ($request->post()) {
            $model->load($request->post());

            // Validate model
            if ($model->validate()) {
                $formData = $request->post(); // tampung formdata
                $form_params = array(
                    'tipepaket_nama' => $formData['TipePaketForm']['tipepaket_nama'],
                    'tipepaket_kode' => $formData['TipePaketForm']['tipepaket_kode'],
                    'tipepaket_namalainnya' => $formData['TipePaketForm']['tipepaket_namalainnya'],
                    'keterangan_tipepaket' => $formData['TipePaketForm']['keterangan_tipepaket'],
                    'is_active' => $formData['TipePaketForm']['is_active'],
                );
                $form_params['daftarTindakan'] = [];
                $session_id = Yii::$app->docoVars->user("id");
                $cache_tindakan = Yii::$app->cache->get("cache_tampung_tindakan_".$session_id);
                if (!empty($cache_tindakan)) {
                    $form_params['daftarTindakan'] = json_encode($cache_tindakan);
                }
                // Try catch
                try {
                    $response = $this->_restMaster->post('tipe-paket/update-paket?tipepaket_id=' . $tipepaket_id, [
                        'form_params' => $form_params
                    ]);
                    $r = json_decode($response->getBody(), true);
                    
                    // Return
                    return DocoHelpers::response($r, false, $formName);
                } catch (RequestException $e) {
                    // Return
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    // Return
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                // Errors
                $errors = DocoHelpers::parseError($model->errors, $formName);
                // Return
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            // Return form
            return $this->renderPartial('components/paket/update', get_defined_vars());
        }
    }

    // view sub packet
    public function actionSubPaket($id = null, $is_mcu)
    {
        $title = Yii::t('fe', 'Master Paket');
        $status = ['1' => Yii::t('fe', 'Aktif'), '0' => Yii::t('fe', 'Tidak aktif')];
        $id_encrypt = $id;
        $id = DocoHelpers::decrypt($id);

        $render = ($is_mcu == 1) ? 'components/paket/subpaket_mcu' : 'components/paket/subpaket';
        return $this->renderAjax($render, get_defined_vars());
    }
    // view sub packet
    // datatable viewpaket
    public function actionGetDataPaketDetail($id = null, $is_mcu = null)
    {
        $id = DocoHelpers::decrypt($id);
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        $dataPaket = [];
        $yiiRestfulParams['tipepaket_id'] = $id;
        $yiiRestfulParams['is_mcu'] = $is_mcu;
        $yiiRestfulParams['is_paging'] = 1;
        try {
            $response = $this->_restMaster->get('tipe-paket/detail-paket-tindakan?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            if(isset($body['response']['data'])) {
                $dataPaket = $body['response']['data'];
            }

            if(!empty($dataPaket)) {
                foreach ($dataPaket as $key => $value) {
                    $is_mcu = ($is_mcu == 1) ? true : false;
                    $no++;
                    $primaryKey = $value['tindakan_paket_id'];
                    $value['primary'] = $primaryKey;
                    $value['rowNum'] = $no;
                    $value['instalasi_ruangan'] = $value['instalasi_nama'].' / '.$value['ruangan_nama'];
                    $data[$key] = $value;
                }
            }
            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    // datatable viewpaket

    public function actionCacheTindakan(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = \Yii::$app->request->post();
        $session_id = Yii::$app->docoVars->user("id");
        $cache_tindakan = Yii::$app->cache->get("cache_tampung_tindakan_".$session_id);
        $result = [];
        $data = [];
        $result['data'] = $data;
        $result['draw'] = 0;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        if(!empty($cache_tindakan)){
            $result = $cache_tindakan;
        }
        if($request['status_chache'] == "insert"){
            $tampung_tindakan = $request['tampung_tindakan'];
            $pecah = explode(' - ',$tampung_tindakan['text']);
            $tampung_tindakan['id'] = $tampung_tindakan['id'];
            $tampung_tindakan['text'] = $pecah[0];
            $tampung_tindakan['tipepaket_id'] = 0;
            $tampung_tindakan['kelompoktindakan_nama'] = $tampung_tindakan['kelompoktindakan_nama'];
            $tampung_tindakan['ruangan_id'] = $tampung_tindakan['ruangan_id'];
            $tampung_tindakan['ruangan_nama'] = $tampung_tindakan['ruangan_nama'];
            $tampung_tindakan['instalasi_id'] = $tampung_tindakan['instalasi_id'];
            $tampung_tindakan['instalasi_nama'] = $tampung_tindakan['instalasi_nama'];
            $tampung_tindakan['instalasi_ruangan'] = $tampung_tindakan['instalasi_nama'].' / '.$tampung_tindakan['ruangan_nama'];
            $tampung_tindakan['no'] = count($result['data'])+1;
            $tampung_tindakan['hapus'] = Html::button('<i class="fa fa-trash"></i>', ['class' => 'btn btn-sm btn-danger','onclick'=> 'hapusTindakan('.$tampung_tindakan['id'].')']);
            $tampung_data = $result['data'];

            $response = [];
            if(empty($tampung_data)){
                $result['data'][] = $tampung_tindakan;
                $response = [
                    'status' => 200,
                    'title' => 'Input Berhasil',
                    'text' => 'Input Tindakan Berhasil',
                ];
            }
            if(!empty($tampung_data)){
                foreach ($tampung_data as $k => $v) {
                    if($v['id'] == $tampung_tindakan['id'] ){
                        if($v['ruangan_id'] == $tampung_tindakan['ruangan_id'] ) {
                            $response = [
                                'status' => 500,
                                'title' => 'Input Gagal',
                                'text' => 'Tindakan sudah Pernah Di Input',
                            ];
                            break;
                        } else {
                            $response = [
                                'status' => 200,
                                'title' => 'Input Berhasil',
                                'text' => 'Input Tindakan Berhasil',
                            ];
                        }
                        
                    }
                    if($v['id'] != $tampung_tindakan['id']){
                        $response = [
                            'status' => 200,
                            'title' => 'Input Berhasil',
                            'text' => 'Input Tindakan Berhasil',
                        ];
                    }
                }

                if($response['status'] == 200){
                    $result['data'][] = $tampung_tindakan;
                }
            }
            
            $result['draw'] = 1;
            $result['recordsTotal'] = count($result['data']);
            $result['recordsFiltered'] = count($result['data']);
            $session_id = Yii::$app->docoVars->user("id");
            Yii::$app->cache->set("cache_tampung_tindakan_".$session_id, $result);
        }
        if ($request['status_chache'] == "delete") {
            $response = [];
            $form_params['daftartindakan_id'] = $request['daftartindakan_id'];
            $form_params['tipepaket_id'] = $request['tipepaket_id'];
            $this->actionHapusCacheTindakan($form_params);
        }

        return $response;
    }

    public function actionGetCacheTindakan(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request_get = \Yii::$app->request->get();
        $result = [];
        $data = [];
        $result['data'] = $data;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        $disabled = $request_get['disabled'];
        $disabled = ($disabled == "true") ? "disabled" : "";

        if(!empty($request_get['tipepaket_id'])){
            $tipepaket_id = DocoHelpers::decrypt($request_get['tipepaket_id']);
            try{
                $response = $this->_restMaster->get('tipe-paket/detail-paket-tindakan?tipepaket_id=' . $tipepaket_id, ['form_params' => []]);
                $body = json_decode($response->getBody(), true);
                if($body['metadata']['status'] == 200){
                    $datapaket = $body['response']['data'];
                    $no_urut = 0;
                    if(!empty($datapaket)) {
                        foreach($datapaket as $k => $v){
                            $instalasi_ruangan = '';
                            $instalasi = isset($v['instalasi_nama']) ? $v['instalasi_nama'] : '';
                            $ruangan = isset($v['ruangan_nama']) ? $v['ruangan_nama'] : '';
                            if(!empty($instalasi) && !empty($ruangan)) {
                                $instalasi_ruangan = $instalasi.' / '.$ruangan;
                            }
                            
                            $no_urut++;
                            $tampung_tindakan = array(
                                'id' => $v['tindakan_paket_id'],
                                'no' => $no_urut,
                                'text' => $v['tindakan_paket_nama'],
                                'ruangan_id' => $v['ruangan_id'],
                                'ruangan_nama' => $v['ruangan_nama'],
                                'instalasi_id' => $v['instalasi_id'],
                                'instalasi_nama' => $v['instalasi_nama'],
                                'kelompoktindakan_nama' => $v['kelompoktindakan_nama'],
                                'hapus' => '<button type="button" class="btn btn-sm btn-danger" onclick="hapusTindakanPaket('.$v['tindakan_paket_id'] . ','. $v['tipepaket_id'].')" '.$disabled.'><i class="fa fa-trash"></i></button>',
                                'selected'=>'true',
                                'tipepaket_id' => $v['tipepaket_id'],
                                'instalasi_ruangan' => $instalasi_ruangan,
                            );
                            $result['data'][] = $tampung_tindakan;
                        }
                    }
                    
                    $result['recordsTotal'] = $no_urut;
                    $result['recordsFiltered'] = $no_urut;
                }
            }catch(\Exception $e){

            }
        }
        $session_id = Yii::$app->docoVars->user("id");
        $cache_tindakan = Yii::$app->cache->get("cache_tampung_tindakan_".$session_id);

        if (!empty($cache_tindakan)) {
            $result = $cache_tindakan;
        }
        $result['draw'] = $request_get['draw'];
        Yii::$app->cache->set("cache_tampung_tindakan_".$session_id, $result); 
        return $result;
    }

    public function actionHapusCacheTindakan($params){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session_id = Yii::$app->docoVars->user("id");
        $daftartindakan_id = isset($params['daftartindakan_id']) ? $params['daftartindakan_id'] : $params;
        $cache_tindakan = Yii::$app->cache->get("cache_tampung_tindakan_".$session_id);
        if (!empty($cache_tindakan)) {
            $result = $cache_tindakan;
            // proses delete id
            
            foreach($result['data'] as $k => $v){
                if($v['id'] == $daftartindakan_id){
                    unset($result['data'][$k]);
                }
            }
            // proses delete id
            // pembuatan no urut
            $no_urut = 0;
            $tmp_array = $result['data'];
            $result['data'] = [];
            foreach ($tmp_array as $k => $v) {
                $no_urut++;
                $v['no'] = $no_urut;
                $result['data'][] = $v;
                
            }
            $result['recordsTotal'] = $no_urut;
            $result['recordsFiltered'] = $no_urut;
            // pembuatan no urut

            $response = [
                'status' => 200,
                'title' => 'Hapus Berhasil',
                'text' => 'Hapus Tindakan Berhasil',
            ];
            
            // Yii::$app->cache->delete("cache_tampung_tindakan");
            Yii::$app->cache->set("cache_tampung_tindakan_".$session_id, $result);

        }

         return $response;
    }


    public function actionExportPdfTipePaket()
    {
        // Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/paket-ruangaxxxn.pdf";
            $response = $this->_restMaster->get('tipe-paket/export-pdf?' . http_build_query($yiiRestfulParams), [
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }

    public function actionExportExcelTipePaket()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $url = 'tipe-paket/export-excel?' . http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/Master - Tindakan - Tipe Paket.xlsx";
        try {
            $response = $this->_restMaster->get($url, [
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::downloadFile($path, true);
        } catch (RequestException $e) {
            var_dump($e->getMessage());
            exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    /**
     * @todo Fungsi untuk melakukan pengecekan transaksi paket
     * @author Sigit Arif Munandar <sigit@docotel.com>
     */
    public function actionCekTransaksiPaket()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;
            $id = DocoHelpers::decrypt($request->get('id'));

            $count = 0;
            $restMaster = $this->_restMaster->get('tipe-paket/cek-transaksi-paket', [
                'query' => ['id' => $id]
            ]);
            $body = json_decode($restMaster->getBody(), true);
            $body = $body['response'];
            $count = $body['count'];

            return DocoHelpers::response($count);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionInstalasiRuangan()
    {
        $api = 'allow/get-data-instalasi-ruangan'; 
        $data_id = 'ruangan_id';  
        $data_name = [
            'instalasi_nama',
            'ruangan_nama'
        ];     
        $getRest = $this->_restMaster; 
        return DocoHelpers::paginationSelec2($api, $data_id, $data_name, $getRest, null);
    }
}
