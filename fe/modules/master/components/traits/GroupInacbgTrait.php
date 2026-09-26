<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-04-27 09:53:52
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-03-26 16:44:03
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
use app\modules\master\models\GroupInaCbgForm;
trait GroupInacbgTrait
{
    
    public function actionGroupInacbg()
    {
        $title = Yii::t('fe', 'Master kategori');
        $status = ['1'=>Yii::t('fe', 'Aktif'), '0'=>Yii::t('fe','Tidak aktif')];
        return $this->renderAjax('components/group-inacbg/index', get_defined_vars());
    }

    public function actionGetDataGroupInacbg()
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
            $response = $this->_restMaster->get('group-ina-cbg/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['groupinacbg_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['status'] = ($value['is_active']) ? Yii::t('fe', 'Aktif') : Yii::t('fe','Tidak aktif');
                $value['detail'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                    'class' => 'btn btn-sm btn-success', 'data-source'=>"/master/tindakan/detail-tindakan-inacbg?id=".$primaryKey,'onclick'=> 'docoHelper.detail(this)']);
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

    public function actionCreateGroupInacbg()
    {
        $title = 'Tambah Group INA CBGS';
        $model = new GroupInaCbgForm;
        $request = Yii::$app->request;
        $post = $request->post();
        $session_id = Yii::$app->docoVars->user("id");
        Yii::$app->cache->delete("cache_tampung_tindakan_inacbg_".$session_id);
        $model->is_active = 1;
        $model->is_obat = 0;
        return $this->renderAjax('components/group-inacbg/form', get_defined_vars());
    }

    public function actionSaveInacbg($groupinacbg_id = null)
    {
        $request = Yii::$app->request;
        $groupinacbg_id = DocoHelpers::decrypt($groupinacbg_id);
        $model = new GroupInaCbgForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        if ($request->post()) {
            $post = $request->post();
            $model->load($request->post());
            if ($model->validate()) {
                $form = $post['GroupInaCbgForm'];
                $form_params = array(
                    'groupinacbg_id' => ($groupinacbg_id) ? $groupinacbg_id : null,
                    'groupinacbg_nama' => $form['groupinacbg_nama'],
                    'groupinacbg_kode' => $form['groupinacbg_kode'],
                    'groupinacbg_namalainnya' => $form['groupinacbg_namalainnya'],
                    'catatan' => $form['catatan'],
                    'is_active' => $form['is_active'],
                    'is_obat' => $form['is_obat'],
                );
                $form_params['listdata'] = [];
                $session_id = Yii::$app->docoVars->user("id");
                $cache = Yii::$app->cache;
                $cache_list = $cache->get("cache_tampung_list_inacbg_".$session_id);
                if (!empty($cache_list)) {
                    $form_params['listdata'] = json_encode($cache_list);
                }

                try {
                    $response = $this->_restMaster->post('group-ina-cbg/simpan-inacbg', [
                        'form_params' => $form_params
                    ]);
                    $r = json_decode($response->getBody(), true);
                    Yii::$app->cache->set("cache_tampung_list_inacbg_".$session_id, []);
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
            return $this->renderPartial('components/group-inacbg/form_update', get_defined_vars());
        }
    }

    public function actionUpdateGroupInacbg($id)
    {
        $title = 'Ubah Group INA CBGS';
        $model = new GroupInaCbgForm;
        $request = Yii::$app->request;
        $post = $request->post();
        $id_encrypt = $id;
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restMaster->get('group-ina-cbg/view?id=' . $id);
            $body = json_decode($response->getBody(), true);
            $attributes = $body['response'];
        } catch (Exception $e) {
            $attributes = [];
        }
        
        $model->attributes = $attributes;
        $model->is_obat = empty($model->is_obat) ? 0 : $model->is_obat;
        $session_id = Yii::$app->docoVars->user("id");
        Yii::$app->cache->delete("cache_tampung_list_inacbg_".$session_id);
        return $this->renderAjax('components/group-inacbg/form_update', get_defined_vars());
    }

    public function actionDeleteGroupInacbg($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restMaster->POST('group-ina-cbg/delete', [
                'query'=>['id'=>$id]]);

            $response = json_decode($response->getBody(), true);
            // $response['response'] = [
            //     'title' => 'Proses Berhasil !',
            //     'text' => 'Data berhasil dihapus'
            // ];

            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionExportExcelGroupInacbg()
    {
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $url = 'group-ina-cbg/export-excel?'.http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/Master- Group INACBG.xlsx";
        try {
            $response = $this->_restMaster->get($url,[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::downloadFile($path, true);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportPdfGroupInacbg()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/group-ina-cbg.pdf";
            $response = $this->_restMaster->get('group-ina-cbg/export-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path, true);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }

    public function actionDetailTindakanInacbg($id)
    {
        $type = 0;
        try {
            $request = $this->_restMaster->request('GET', 'group-ina-cbg/view?id='.DocoHelpers::decrypt($id));
            $response = json_decode($request->getBody(), true);
            $attributes = $response['response'];
            $type = empty($attributes['is_obat']) ? 0 : $attributes['is_obat'];
        } catch (Exception $e) {
            $attributes = [];
        }

        return $this->renderAjax('components/group-inacbg/_detail', get_defined_vars());
    }

    public function actionDataDetailTindakanInacbg()
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
            $type =$request->get('type');
            $response = $this->_restMaster->get('group-ina-cbg/detail-tindakan-inacbg?id='.
                $id.'&type='.$type.'&'.http_build_query($yiiRestfulParams), ['form_params' => []]);

            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                if($type == 0){
                    $primaryKey = DocoHelpers::encrypt($value['daftartindakan_id']);
                    $value['text'] = $value['daftartindakan_nama'];
                }else{
                    $primaryKey = DocoHelpers::encrypt($value['obatalkes_id']);
                    $value['text'] = $value['obatalkes_nama'];
                }
                $value['primary'] = $primaryKey;
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

    public function actionCacheTindakanInacbg() 
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = \Yii::$app->request->post();
        $session_id = Yii::$app->docoVars->user("id");
        $cache_tindakan = Yii::$app->cache->get("cache_tampung_list_inacbg_".$session_id);
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
            $tampung_tindakan = $request['tampung_data'];
            $tampung_tindakan['groupinacbg_id'] = 0;
            $tampung_tindakan['no'] = count($result['data'])+1;
            $tampung_tindakan['hapus'] = Html::button('<i class="fa fa-trash"></i>', ['class' => 'btn btn-sm btn-danger','onclick'=> 'hapusTindakan('.$tampung_tindakan['id'].')']);
            $tampung_data = $result['data'];
            $response = [];
            if(empty($tampung_data)){
                $result['data'][] = $tampung_tindakan;
                $response = [
                    'status' => 200,
                    'title' => 'Proses Berhasil',
                    'text' => 'Input Tindakan Berhasil',
                ];
            }
            if(!empty($tampung_data)){
                foreach ($tampung_data as $k => $v) {

                    if($v['id'] == $tampung_tindakan['id'] ){
                        $response = [
                            'status' => 500,
                            'title' => 'Proses Gagal',
                            'text' => 'Tindakan sudah Pernah Di Input',
                        ];
                        break;
                    }
                    if($v['id'] != $tampung_tindakan['id']){
                        $response = [
                            'status' => 200,
                            'title' => 'Proses Berhasil',
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
            Yii::$app->cache->set("cache_tampung_list_inacbg_".$session_id, $result);
            
            return $response;
        }

        if ($request['status_chache'] == "delete") {
            $response = [];
            try{
                $id = $request['daftartindakan_id'];
                $response = $this->_restMaster->POST('group-ina-cbg/delete-list', [
                'form_params'=>
                    [
                        'id'=>$id,
                        'type' => $request['type'],
                    ]
                ]);
                $response = json_decode($response->getBody(), true);
                return DocoHelpers::response($response, false);
            } catch (RequestException $e) {
                return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
            } catch (\Exception $e) {
                return DocoHelpers::responseTemplate(500, $e->getMessage());
            }
        }
    }

    public function actionGetCacheTindakanInacbg($reset = false) 
    {
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
        if(!empty($request_get['groupinacbg_id'])){
            $groupinacbg_id = DocoHelpers::decrypt($request_get['groupinacbg_id']);
            $type = $request_get['type'];
            $yiiRestfulParams['id'] = $groupinacbg_id;
            $yiiRestfulParams['type'] = $type;
            try{
                $response = $this->_restMaster->get('group-ina-cbg/detail-tindakan-inacbg', ['query' => $yiiRestfulParams]);
                $body = json_decode($response->getBody(), true);
                if($body['metadata']['status'] == 200){
                    $dataInacbg = $body['response']['data'];
                    foreach($dataInacbg as $k => $v){
                        $id = isset($v['daftartindakan_id']) ? $v['daftartindakan_id'] : $v['obatalkes_id'];
                        $text = isset($v['daftartindakan_nama']) ? $v['daftartindakan_nama'] : $v['obatalkes_nama'];
                        $no_urut++;
                        $tampung_tindakan = [
                            'id'=> $id,
                            'no'=>$no_urut,
                            'text'=> $text,
                            'hapus'=> Html::button('<i class="fa fa-trash"></i>', ['class' => 'btn btn-sm btn-danger', 'onclick' => 'hapusTindakanInacbg(' . $id . ','. $v['groupinacbg_id'] .','.$type.')']),
                            'selected'=>'true',
                            'groupinacbg_id'=>$v['groupinacbg_id'],
                        ];

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
        if($reset){
            Yii::$app->cache->set("cache_tampung_list_inacbg_".$session_id, []);
        }
        $cache_tindakan = Yii::$app->cache->get("cache_tampung_list_inacbg_".$session_id);
        if (!empty($cache_tindakan)) {
            foreach($cache_tindakan['data'] as $k => $v) {
                $no_urut++;
                $tampung_tindakan_cache = array(
                    'id'=>$v['id'],
                    'no'=>$no_urut,
                    'text'=>$v['text'],
                    'hapus'=> Html::button('<i class="fa fa-trash"></i>', ['class' => 'btn btn-sm btn-danger', 'onclick' => 'hapusTindakan(' . $v['id'] . ')']),
                    'selected'=>'true',
                    'groupinacbg_id'=>$v['groupinacbg_id'],
                    'color' => isset($request_get['groupinacbg_id']) ? '#ffec8b' : ''
                );

                $result['data'][] = $tampung_tindakan_cache;
            }
            $result['recordsTotal'] = $no_urut;
            $result['recordsFiltered'] = $no_urut;
        }
        $result['draw'] = $request_get['draw'];
        return $result;
    }

    public function actionHapusCacheTindakanInacbg($id) 
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session_id = Yii::$app->docoVars->user("id");
        $cache_list = Yii::$app->cache->get("cache_tampung_list_inacbg_".$session_id);
        if (!empty($cache_list)) {
            $result = $cache_list;
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
            Yii::$app->cache->set("cache_tampung_list_inacbg_".$session_id, $result);

        }

        return $result;
    }
}
