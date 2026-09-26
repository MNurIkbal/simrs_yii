<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2019-02-27 14:00:00
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-03-01 14:17:43
 */

namespace app\modules\rajal\components\traits;

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

trait PemulanganPasienTrait 
{

    public function actionCariTindakanJenazah()
    {
        $request = Yii::$app->request;
        $page = $request->get('page');
        $response = [];
        $limit = 5;
        $offset = ($page-1)*5;
        try {
            $result = $this->_restRajal->get('tra-pemeriksaan/cari-tindakan-jenazah',[
                'query' => [
                    'ruangan_id' => DocoConstants::VAR_RUANGAN_JNZ,
                    'pendaftaran_id' => DocoHelpers::decrypt($request->get('id')),
                    'term' => $request->get('term',null),
                    'page'=>$page,
                    'offset'=>$offset,
                    'limit'=>$limit
                ]
            ]);

            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['daftartindakan_id'],
                    'text' => $value['daftartindakan_nama'],
                    'tarif_satuan' => $value['harga_tariftindakan'],
                    'persen_cyto' => $value['persencyto_tindakan'],
                    'list_komponen' => $value['list_komponen']
                ];
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }
        
        return DocoHelpers::response([
            'result' => $response,
            'pagination' => [ 'more' => !empty($data)?true:false ]
        ]);
    }

    public function actionCariObatJenazah()
    {
        $request = Yii::$app->request;
        $page = $request->get('page');
        $response = [];
        $limit = 5;
        $offset = ($page-1)*5;
        $dataObat = [];
        try {
            $result = $this->_restRajal->get('tra-pemeriksaan/cari-obat-jenazah',[
                'query' => [
                    'ruangan_id' => DocoConstants::VAR_RUANGAN_JNZ,
                    'term' => $request->get('term',null),
                    'page'=>$page,
                    'offset'=>$offset,
                    'limit'=>$limit
                ]
            ]);

            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['obatalkes_id'],
                    'text' => $value['obatalkes_nama'],
                    'harga' => $value['hargajual'],
                    'qty_tersedia' => $value['qty_tersedia'],
                    'harganetto' => $value['harganetto'],
                    'persendiscount' => $value['persendiscount'],
                    'persenppn' => $value['persenppn'],
                    'persenmargin' => $value['persenmargin'],
                    'jmldiscount' => $value['jmldiscount'],
                    'jmlmargin' => $value['jmlmargin'],
                    'jmlppn' => $value['jmlppn'],
                    'satuankecil_id' => $value['satuankecil_id'],
                    'satuankecil_nama' => $value['satuankecil_nama'],
                ];
                $dataObat[$value['obatalkes_id']] = $value;
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }
        
        return DocoHelpers::response([
            'result' => $response,
            'pagination' => [ 'more' => !empty($data)?true:false ],
            'dataObat' => $dataObat
        ]);
    }

    public function actionCariLinenJenazah()
    {
        $request = Yii::$app->request;
        $page = $request->get('page');
        $response = [];
        $limit = 5;
        $offset = ($page-1)*5;
        try {
            $result = $this->_restRajal->get('tra-pemeriksaan/cari-linen-jenazah',[
                'query' => [
                    'term' => $request->get('term',null),
                    'page'=>$page,
                    'offset'=>$offset,
                    'limit'=>$limit
                ]
            ]);

            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['barang_id'],
                    'text' => $value['barang_nama'],
                ];
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }
        
        return DocoHelpers::response([
            'result' => $response,
            'pagination' => [ 'more' => !empty($data)?true:false ]
        ]);
    }

    public function actionCariAlatJenazah()
    {
        $request = Yii::$app->request;
        $page = $request->get('page');
        $response = [];
        $limit = 5;
        $offset = ($page-1)*5;
        try {
            $result = $this->_restRajal->get('tra-pemeriksaan/cari-alat-jenazah',[
                'query' => [
                    'term' => $request->get('term',null),
                    'page'=>$page,
                    'offset'=>$offset,
                    'limit'=>$limit
                ]
            ]);

            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [];
            foreach ($data as $key => $value) {
                $response[] = [
                    'id' => $value['obatalkes_id'],
                    'text' => $value['obatalkes_nama'],
                ];
            }
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }
        
        return DocoHelpers::response([
            'result' => $response,
            'pagination' => [ 'more' => !empty($data)?true:false ]
        ]);
    }

    public function actionSaveCacheJenazah($id, $type)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $cache = Yii::$app->cache;
        try {
            $listdata = [];
            $cacheName = $id.'-'.$type;
            $getCache = $cache->get($cacheName);
            if($getCache){
                $listdata = $getCache;
            }
            $post['is_deleted'] = false;
            $keychange = null;
            foreach ($listdata as $key => $value) {
                if($type == 'tindakan'){
                    if($value['daftartindakan_id'] == $post['daftartindakan_id']){
                        $keychange = $key;
                    }
                }
                if($type == 'obat'){
                    if($value['obatalkes_id'] == $post['obatalkes_id']){
                        $keychange = $key;
                    }
                }
                if($type == 'linen' && ($value['barang_id'] == $post['barang_id'])){
                    $keychange = $key;
                }
                if($type == 'alat' && ($value['obatalkes_id'] == $post['obatalkes_id'])){
                    $keychange = $key;
                }
            }
            if($keychange === null){
                $listdata[] = $post;
            }else{
                if($type == 'tindakan'){
                    $listdata[$key]['qty_tindakan'] += $post['qty_tindakan'];
                    $listdata[$key]['tarif_tindakan'] = $listdata[$key]['qty_tindakan'] * $post['tarif_satuan'];
                }
                if($type == 'obat'){
                    $listdata[$key]['qty'] += $post['qty'];
                    if($listdata[$key]['qty'] > $listdata[$key]['qty_tersedia']){
                        return DocoHelpers::responseTemplate(
                            422, 
                            'Error', 
                            [], 
                            [
                                'title' => Yii::t('fe', 'Terjadi Kesalahan'), 
                                'text' => 'Qty yang dipesan tidak boleh melebihi stok tersedia',
                                'message' => 'Qty yang dipesan tidak boleh melebihi stok tersedia',
                            ]
                        );
                    }
                    $listdata[$key]['obat_harga'] = $listdata[$key]['qty'] * $post['hargajual'];
                }
                if($type == 'linen' || $type == 'alat'){
                    $listdata[$key]['qty'] += $post['qty'];
                }
            }
            $cache->set($cacheName, $listdata);
            return DocoHelpers::response(['title' => 'Berhasil', 'message' => 'Data Berhasil Disimpan']);
        } catch (Exception $e) {
            return DocoHelpers::response($e->getMessage(), 500);
        }
    }
    public function actionResetCacheJenazah($id, $type){
        $cache = Yii::$app->cache;
        $cacheName = $id.'-'.$type;
        try {
            if($type == 'all'){
                $cache->set($id.'-linen', []);
                $cache->set($id.'-obat', []);
                $cache->set($id.'-tindakan', []);
                $cache->set($id.'-alat', []);
            }else{
                $cache->set($cacheName, []);
            }
            return true;
        } catch (Exception $e) {
            return DocoHelpers::response($e->getMessage(), 500);
        }
    }

    public function actionGetCacheJenazah($cachetype, $id)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $cache = Yii::$app->cache;
        try {
            $listdata = [];
            $cacheName = $id.'-'.$cachetype;
            $getCache = $cache->get($cacheName);
            if($getCache){
                $listdata = $getCache;
            }
            if(!$listdata){
                $return = [
                    'data' => [],
                    'draw' => $request->post('draw'),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0
                ];
                return DocoHelpers::response($return);
            }
            $data_tables = [];
            $no = 1;
            foreach ($listdata as $key => $value) {
                if(!$value['is_deleted']){
                    $value['rownum'] = $no;
                    $value['aksi'] = Html::button(
                        '<i class="fa fa-times"></i>',
                        [
                            'class' => 'btn btn-danger btn-xs delete-data',
                            'action' => 'delete-cache-jenazah?id='.$id.'&key='. $key.'&type='.$cachetype,
                            'data-confirm-message' => Yii::t('fe', 'confirm_batal'),
                        ]
                    );

                    array_push($data_tables, $value);
                    $no++;
                }
            }

            $return = [
                'data' => $data_tables,
                'draw' => $request->post('draw'),
                'recordsTotal' => count($data_tables),
                'recordsFiltered' => count($data_tables)
            ];
            return DocoHelpers::response($return);
        } catch (Exception $e) {
            $return = [
                    'data' => [],
                    'draw' => $request->post('draw'),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0
                ];
            return DocoHelpers::response($return);
        } catch(\RequestException $e){
            $return = [
                    'data' => [],
                    'draw' => $request->post('draw'),
                    'recordsTotal' => 0,
                    'recordsFiltered' => 0
                ];
            return DocoHelpers::response($return);
        }
    }
    public function actionDeleteCacheJenazah(){
        $request = Yii::$app->request;
        $get = $request->get();
        $id = $get['id'];
        $type = $get['type'];
        $key = $get['key'];
        $cache = Yii::$app->cache;
        $cacheName = $id.'-'.$type;
        try {
            $getCache = $cache->get($cacheName);
            if(isset($getCache[$key])){
                $listdata = $getCache;
                unset($listdata[$key]);
                $cache->set($cacheName, $listdata);
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function actionCetakPersetujuanJenazah($id)
    {
        $path = Yii::getAlias("@download") . "/persetujuan-jenazah.pdf";
        $pendaftaran_id = DocoHelpers::decrypt($id);
        // Try catch
        try {
            // Request
            $request = $this->_restJenazah->get('informasi-pasien-meninggal/print-belum-diterima?pendaftaran_id='.$pendaftaran_id, [
                'save_to' => $path,
            ]);

            // Download pdf
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            var_dump(json_decode($e->getResponse()->getBody()));exit;
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
    
    public function actionCetakSpri($id)
    {
        $userIdentity = Yii::$app->session->get('user_identity');
        $path = Yii::getAlias("@download") . "/SPRI.pdf";
        $pendaftaran_id = DocoHelpers::decrypt($id);
        $nama_usercetak = Yii::$app->session->get('user_identity')['nama'];
        $id_usercetak = Yii::$app->session->get('user_identity')['id_pegawai'];
        try {
            $request = $this->_restRajal->get('tra-pemeriksaan/cetak-spri', [
                'query' => [
                    'pendaftaran_id' => $pendaftaran_id,
                    'ruangan_id' => $this->_id_ruangan,
                    'pegawai_id' => $this->_pegawai_id,
                    'kelompokpegawai_id' => $userIdentity['kelompokpegawai_id'],
                    'nama_usercetak' => $nama_usercetak,
                    'id_usercetak' => $id_usercetak
                ],
                'save_to' => $path,
            ]);
            $body = json_decode($request->getBody(), true);
            // Download pdf
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}