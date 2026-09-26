<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-13 11:30:26
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-10-16 13:05:31
 */

namespace app\modules\igd\components\traits\bedah;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\components\DocoHelpers;

trait ApiTrait 
{
    public function actionGetPegawai()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
            $response = $this->_restBedah->request('POST', 'allow/get-pegawai',[
                            'form_params'=>['term'=>$_GET['q']['term'], 'ruangan_id'=>$ruangan_id],
                        ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id'=>$value['pegawai_id'],'text'=>$value['nama_pegawai']];
            }
            $total = count($body['response']);
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        }
    }
    public function actionGetTindakan()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $penjamin_id = $_GET['penjamin'];
            $kelaspelayanan_id = $_GET['kelaspelayanan'];
            $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
            $response = $this->_restBedah->request('POST', 'allow/get-tindakan',[
                            'form_params'=>['term'=>$_GET['q']['term'], 'ruangan_id'=>$ruangan_id, 'penjamin_id'=>$penjamin_id, 'kelaspelayanan_id'=>$kelaspelayanan_id],
                        ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = [
                    'id'=>$value['daftartindakan_id'],
                    'text'=>$value['daftartindakan_nama'],
                    'harga_tariftindakan'=>$value['harga_tariftindakan'],
                    'persencyto_tindakan'=>$value['persencyto_tindakan']
                ];
            }
            $total = count($body['response']);
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        }
    }
    public function actionGetJenisOperasi()
    {
        // Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restBedah->get('allow/list-jenis-operasi', ['query'=>['daftartindakan_id'=>$parent_label]]);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $key => $value)
                $result['output'][] = [
                    'id' => $value['golonganoperasi_id'],
                    'name' => $value['golonganoperasi_nama'],
                    'operasi_id'=>$value['operasi_id']
                ];
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);

        }
    }
    public function actionGetJenisAlat()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $response = $this->_restBedah->post('allow/get-jenis-alat', 
                [
                    'form_params'=>[
                        'obatalkes_nama'=>$_GET['q']['term']
                    ]
                ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = [
                    'id'=>$value['obatalkes_id'],
                    'text'=>$value['obatalkes_nama'],
                ];
            }
            $total = count($body['response']);
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        }
    }
    public function actionGetDaftarTindakan()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $response = $this->_restBedah->request('POST', 'allow/get-daftar-tindakan',[
                            'form_params'=>['daftartindakan_nama'=>$_GET['q']['term'] ],
                        ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = [
                    'id'=>$value['daftartindakan_id'],
                    'text'=>$value['daftartindakan_nama'],
                ];
            }
            $total = count($body['response']);
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        }
    }
    public function actionGetTindakanTarif()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $penjamin_id = $_GET['penjamin'];
            $kelaspelayanan_id = $_GET['kelaspelayanan'];
            $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
            $response = $this->_restBedah->request('POST', 'allow/get-tindakan-tarif',[
                            'form_params'=>['term'=>$_GET['q']['term'], 'ruangan_id'=>$ruangan_id, 'penjamin_id'=>$penjamin_id, 'kelaspelayanan_id'=>$kelaspelayanan_id],
                        ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = [
                    'id'=>$value['daftartindakan_id'],
                    'text'=>$value['daftartindakan_nama'],
                    'harga_tariftindakan'=>$value['harga_tariftindakan'],
                ];
            }
            $total = count($body['response']);
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        }
    }
    public function actionGetDokter()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $response = $this->_restBedah->request('POST', 'allow/get-dokter',[
                            'form_params'=>['nama_pegawai'=>$_GET['q']['term'] ],
                        ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = [
                    'id'=>$value['pegawai_id'],
                    'text'=>$value['nama_pegawai'],
                ];
            }
            $total = count($body['response']);
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        }
    }
    public function actionGetBmhp()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
            $response = $this->_restBedah->request('POST', 'allow/get-bmhp',[
                            'form_params'=>['obatalkes_nama'=>$_GET['q']['term'], 'ruangan_id'=>$ruangan_id]
                        ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = [
                    'id'=>$value['obatalkes_id'],
                    'text'=>$value['obatalkes_nama'],
                    'qty_tersedia'=>$value['qty_tersedia'],
                ];
            }
            $total = count($body['response']);
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        }
    }
    public function saveToCache($cacheName, $data)
    {
        $cache = Yii::$app->cache;
        $cacheData = $cache->get($cacheName);
        if(count($data) < 1){
            return true;
        }
        if($cacheData == false){
            if(!isset($data[0])){
                $data = [$data];
            }
            $cache->set($cacheName, $data);
        }else{
            $arr = $cacheData;
            if(isset($data[0])){
                $arr = array_merge($arr, $data);
            }else{
                array_push($arr, $data);
            }
            $cache->set($cacheName, $arr);
        }
        return true;
    }
    public function updateCache($cacheName, $data, $key){
        $cache = Yii::$app->cache;
        $cacheData = $cache->get($cacheName);
        if($cacheData){
            $cacheData[$key] = $data;
            $arr = $cacheData;
            $newArr = [];
            foreach ($arr as $key => $value) {
                $newArr[] = $value;
            }
            $cache->set($cacheName, $newArr);
            return true;
        }
        return true;
    }
    public function actionGetCache($cacheName)
    {
        $cache = Yii::$app->cache;
        $cacheData = $cache->get($cacheName);
        $request = Yii::$app->request;
        $result = [];
        $data = [];
        $draw = $request->get('draw', 1);
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        
        if(!$cacheData){
            return DocoHelpers::response($result);
        }
        $no = 0;
        $explodeCache = explode('-', $cacheName);
        foreach ($cacheData as $key => $value) {
            $no++;
            $value['rowNum'] = $no;
            $value['aksi'] = '';
            if($explodeCache[0] == 'penggunaanbmhp'){
                if(isset($value['msg']) && $value['msg'] == ''){
                    $value['aksi'] .= '<a href="'.Url::to(['intra-tambah-penggunaan-bmhp', 'key'=>$key, 'unique'=>$cacheName]).'" class="btn btn-info btn-xs" data-toggle="modal" data-target="#modal_backdrop"><i class="fa fa-edit"></i></a>';
                }
                $value['aksi'] .= ' <button class="btn btn-danger btn-xs delete-item" data-key="'.$key.'" data-cache="'.$cacheName.'"><i class="fa fa-trash"></i></button>';
            }else if($explodeCache[0] == 'itemoperasi'){
                if(!isset($value['default'])){
                    $value['default'] = 0;
                }
                if($value['default'] == 0){
                    $value['aksi'] .= ' <button class="btn btn-danger btn-xs delete-item" data-key="'.$key.'" data-cache="'.$cacheName.'"><i class="fa fa-trash"></i></button>';
                }else{
                    $value['aksi'] .= '<a href="'.Url::to(['intra-tambah-item-operasi', 'key'=>$key, 'unique'=>$cacheName]).'" class="btn btn-info btn-xs" data-toggle="modal" data-target="#modal_backdrop"><i class="fa fa-edit"></i></a>';
                    $value['aksi'] .= ' <button class="btn btn-danger btn-xs delete-item" data-key="'.$key.'" data-cache="'.$cacheName.'"><i class="fa fa-trash"></i></button>';
                }
            }else{
                $value['aksi'] .= ' <button class="btn btn-danger btn-xs delete-item" data-key="'.$key.'" data-cache="'.$cacheName.'"><i class="fa fa-trash"></i></button>';
            }
            
            $data[$key] = $value;
        }
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = count($data);
        $result['recordsTotal'] = count($data);
        return DocoHelpers::response($result);
    }
    public function actionUnsetCache($key, $cacheName)
    {
        $cache = Yii::$app->cache;
        $cacheData = $cache->get($cacheName);
        $explode = explode('-', $cacheName);
        if($cacheData){
            if($explode[0] == 'itemoperasi'){
                $this->unsetCacheBmhp($key, 'penggunaanbmhp-'.$explode[1].'-'.$explode[2]);
            }
            $arr = $cacheData;
            unset($arr[$key]);
            $newArr = [];
            foreach ($arr as $key => $value) {
                $newArr[] = $value;
            }
            $cache->set($cacheName, $newArr);
            return DocoHelpers::response(['response'=>['title'=>'Proses berhasil!', 'text'=>'Data berhasil dihapus']]);
        }
        return DocoHelpers::response(['response'=>['title'=>'Proses berhasil!', 'text'=>'Data berhasil dihapus']]);
    }
    public function unsetCacheBmhp($key, $cacheName){
        $cache = Yii::$app->cache;
        $cacheData = $cache->get($cacheName);
        if($cacheData){
            foreach ($cacheData as $k => $v) {
                if(isset($v['operasi_key']) && $v['operasi_key'] == intval($key)){
                    unset($cacheData[$k]);
                }
            }
            $cache->set($cacheName, $cacheData);
            return true;
        }
        return true;
    }
    public function actionGetView($type, $id)
    {
        $request = Yii::$app->request;
        $result = [];
        $data = [];
        $draw = $request->get('draw', 1);
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restBedah->request('GET', 'intra-operasi/get-view',[
                                'query'=>['type'=>$type, 'id'=>$id],
                            ]);
            $response = json_decode($response->getBody(), true);
            $response = $response['response'];
            
            $no = 0;
            foreach ($response as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                if($type == 'pelayanan-operasi-view'){
                    $value['cyto'] = ($value['cyto_tindakan']) ? '✓' : '';
                }else if($type == 'bmhp-operasi-view'){
                    $value['ditagihkan'] = ($value['is_ditagihkan']) ? '✓' : '';
                }else if($type == 'pasang-infus-view'){
                    $value['tgl_pemasangan'] = date('d M Y H:i:s', strtotime($value['tgl_pemasangan']));
                }
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['draw'] = $draw;
            $result['recordsTotal'] = count($data);
            $result['recordsTotal'] = count($data);
            return DocoHelpers::response($result);
        } catch (Exception $e) {
            return DocoHelpers::response($result);
        }
    }
}