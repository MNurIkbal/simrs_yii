<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Kategori
 * @copyright 26 April 2018 aweutist
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
use app\modules\master\models\KategoriTindakanForm;

trait KategoriTrait
{
    public function actionKategori()
    {
        $title = Yii::t('fe', 'Master kategori');
        $status = ['1'=>Yii::t('fe', 'Aktif'), '0'=>Yii::t('fe','Tidak aktif')];
        return $this->renderAjax('components/kategori/index', get_defined_vars());
    }
    public function actionGetDataKategori()
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
        try {
            $response = $this->_restMaster->get('kategori-tindakan/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['kategoritindakan_id']);
                $value['primary'] = $primaryKey;
                $value['status'] = ($value['is_active']) ? Yii::t('fe', 'Aktif') : Yii::t('fe','Tidak aktif');

                $value['detail'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                    'class' => 'btn btn-sm btn-success', 'data-source'=>"/master/tindakan/detail-tindakan-kategori?id=".$primaryKey,'onclick'=> 'docoHelper.detail(this)']);
                $value['rowNum'] = $no;
                $data[$key] = $value;
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

    public function actionCreateKategori()
    {
        $title = 'Tambah Kategori';
        $model = new KategoriTindakanForm;
        $request = Yii::$app->request;
        $post = $request->post();
        $session_id = Yii::$app->docoVars->user("id");
        Yii::$app->cache->delete("cache_tampung_tindakan_kategori_".$session_id);
        $model->is_active = 1;
        return $this->renderAjax('components/kategori/form', get_defined_vars());
    }

    public function actionUpdateKategori($id)
    {
        $title = 'Ubah Kategori';
        $model = new KategoriTindakanForm;
        $request = Yii::$app->request;
        $post = $request->post();
        $id_encrypt = $id;
        $id = DocoHelpers::decrypt($id);
        $response = $this->_restMaster->get('kategori-tindakan/view?id=' . $id);
        $body = json_decode($response->getBody(), true);
        $attributes = $body['response'];
        $model->attributes = $attributes;
        $session_id = Yii::$app->docoVars->user("id");
        Yii::$app->cache->delete("cache_tampung_tindakan_kategori_".$session_id);
        return $this->renderAjax('components/kategori/form_update', get_defined_vars());
    }

    public function actionSaveKategori($kategoritindakan_id = null)
    {
        $request = Yii::$app->request;
        $kategoritindakan_id = DocoHelpers::decrypt($kategoritindakan_id);
        $model = new KategoriTindakanForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        if ($request->post()) {
            $post = $request->post();
            $model->load($request->post());
            if ($model->validate()) {
                $form = $post['KategoriTindakanForm'];
                $form_params = array(
                    'kategoritindakan_id' => ($kategoritindakan_id) ? $kategoritindakan_id : null,
                    'kategoritindakan_nama' => $form['kategoritindakan_nama'],
                    'kategori_kode' => $form['kategori_kode'],
                    'kategoritindakan_namalainnya' => $form['kategoritindakan_namalainnya'],
                    'catatan' => $form['catatan'],
                    'is_active' => $form['is_active'],
                );
                $form_params['daftarTindakan'] = [];
                $session_id = Yii::$app->docoVars->user("id");
                $cache = Yii::$app->cache;
                $cache_tindakan = $cache->get("cache_tampung_tindakan_kategori_".$session_id);
                if (!empty($cache_tindakan)) {
                    $form_params['daftarTindakan'] = json_encode($cache_tindakan);
                }

                try {
                    $response = $this->_restMaster->post('kategori-tindakan/simpan-kategori', [
                        'form_params' => $form_params
                    ]);
                    $r = json_decode($response->getBody(), true);
                    // return DocoHelpers::response($r);
                    return DocoHelpers::response($r, false, $formName);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            return $this->renderPartial('components/kategori/form_update', get_defined_vars());
        }
    }

    public function actionDeleteKategori($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restMaster->POST('kategori-tindakan/delete', [
                'query'=>['id'=>$id]]);

            $response = json_decode($response->getBody(), true);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];

            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionExportExcelKategori()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $url = 'kategori-tindakan/export-excel?'.http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/Master - Tindakan - Kategori Tindakan.xlsx";
        try {
            $response = $this->_restMaster->get($url,[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::downloadFile($path, true);
        } catch (RequestException $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportPdfKategori()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/kategori-tindakan.pdf";
            $response = $this->_restMaster->get('kategori-tindakan/export-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
            // return DocoHelpers::downloadPdf($response,$path);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }

    public function actionDetailTindakanKategori($id)
    {
        $request = $this->_restMaster->request('GET', 'kategori-tindakan/view?id='.DocoHelpers::decrypt($id));
        $response = json_decode($request->getBody(), true);
        $attributes = $response['response'];

        return $this->renderAjax('components/kategori/_detail', get_defined_vars());
    }

    public function actionDataDetailTindakanKategori()
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
        try {
            $id = DocoHelpers::decrypt($request->get('id'));
            $response = $this->_restMaster->get('kategori-tindakan/detail-tindakan-kategori?id='.
                $id.'&'.http_build_query($yiiRestfulParams), ['form_params' => []]);

            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['daftartindakan_id']);
                $value['primary'] = $primaryKey;
                $value['jeniskegiatantindakan_nama'] = isset($value['jeniskegiatantindakan']['jeniskegiatantindakan_nama']) ? $value['jeniskegiatantindakan']['jeniskegiatantindakan_nama'] : null;
                $value['kategoritindakan_nama'] = isset($value['kategoritindakan']['kategoritindakan_nama']) ? $value['kategoritindakan']['kategoritindakan_nama'] : null;
                $value['kelompoktindakan_nama'] = isset($value['kelompoktindakan']['kelompoktindakan_nama']) ? $value['kelompoktindakan']['kelompoktindakan_nama'] : null;
                $value['groupinacbg_nama'] = isset($value['groupinacbg']['groupinacbg_nama']) ? $value['groupinacbg']['groupinacbg_nama'] : null;
                $value['rowNum'] = $no;
                $data[$key] = $value;
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

    public function actionCacheTindakanKategori(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = \Yii::$app->request->post();
        $session_id = Yii::$app->docoVars->user("id");
        $cache_tindakan = Yii::$app->cache->get("cache_tampung_tindakan_kategori_".$session_id);
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
            $tampung_tindakan['kategoritindakan_id'] = 0;
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
                        $response = [
                            'status' => 500,
                            'title' => 'Input Gagal',
                            'text' => 'Tindakan sudah Pernah Di Input',
                        ];
                        break;
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
            Yii::$app->cache->set("cache_tampung_tindakan_kategori_".$session_id, $result);
            
            return $response;
        }

        if ($request['status_chache'] == "delete") {
            $response = [];
            try{
                $id = $request['daftartindakan_id'];
                $response = $this->_restMaster->POST('kategori-tindakan/delete-tindakan', [
                'form_params'=>['id'=>$id]]);

                $response = json_decode($response->getBody(), true);
                return DocoHelpers::response($response, false);
            } catch (RequestException $e) {
                return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
            } catch (\Exception $e) {
                return DocoHelpers::responseTemplate(500, $e->getMessage());
            }
        }
    }

    public function actionGetCacheTindakanKategori(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request_get = \Yii::$app->request->get();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request_get);

        $result = [];
        $data = [];
        $result['data'] = $data;
        $result['recordsTotal'] = 0;
        $result['recordsFiltered'] = 0;
        $no_urut = 0;
        $tampung_tindakan = [];
        if(!empty($request_get['kategoritindakan_id'])){
            $kategoritindakan_id = DocoHelpers::decrypt($request_get['kategoritindakan_id']);
            $yiiRestfulParams['id'] = $kategoritindakan_id;
            try{
                $response = $this->_restMaster->get('kategori-tindakan/detail-tindakan-kategori', ['query' => $yiiRestfulParams]);
                $body = json_decode($response->getBody(), true);
                if($body['metadata']['status'] == 200){
                    $dataKategori = $body['response']['data'];
                    foreach($dataKategori as $k => $v){
                        $no_urut++;
                        $tampung_tindakan = array(
                            'id'=>$v['daftartindakan_id'],
                            'no'=>$no_urut,
                            'text'=>$v['daftartindakan_nama'],
                            'hapus'=> Html::button('<i class="fa fa-trash"></i>', ['class' => 'btn btn-sm btn-danger', 'onclick' => 'hapusTindakanKategori(' . $v['daftartindakan_id'] . ')']),
                            'selected'=>'true',
                            'kategoritindakan_id'=>$v['kategoritindakan_id']
                        );

                        $result['data'][] = $tampung_tindakan;
                    }
                    $result['recordsTotal'] = $no_urut;
                    $result['recordsFiltered'] = $no_urut;
                }
            } catch (RequestException $e) {
                $result['error'] = $e->getMessage();
                return $result;
            } catch (\Exception $e) {
                $result['error'] = $e->getMessage();
                return $result;
            }
        }
        $session_id = Yii::$app->docoVars->user("id");
        $cache_tindakan = Yii::$app->cache->get("cache_tampung_tindakan_kategori_".$session_id);
        if (!empty($cache_tindakan)) {
            foreach($cache_tindakan['data'] as $k => $v) {
                $no_urut++;
                $tampung_tindakan_cache = array(
                    'id'=>$v['id'],
                    'no'=>$no_urut,
                    'text'=>$v['text'],
                    'hapus'=> Html::button('<i class="fa fa-trash"></i>', ['class' => 'btn btn-sm btn-danger', 'onclick' => 'hapusTindakan(' . $v['id'] . ')']),
                    'selected'=>'true',
                    'kategoritindakan_id'=>$v['kategoritindakan_id']
                );

                $result['data'][] = $tampung_tindakan_cache;
            }
            $result['recordsTotal'] = $no_urut;
            $result['recordsFiltered'] = $no_urut;
        }
        $result['draw'] = $request_get['draw'];
        return $result;
        
    }

    public function actionHapusCacheTindakanKategori($id){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session_id = Yii::$app->docoVars->user("id");
        $cache_tindakan = Yii::$app->cache->get("cache_tampung_tindakan_kategori_".$session_id);
        if (!empty($cache_tindakan)) {
            $result = $cache_tindakan;
            foreach($result['data'] as $k => $v){
                if($v['id'] == $id){
                    unset($result['data'][$k]);
                }
            }
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
            $session_id = Yii::$app->docoVars->user("id");
            Yii::$app->cache->set("cache_tampung_tindakan_kategori_".$session_id, $result);

        }

        return $result;
    }
}
