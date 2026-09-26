<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-04-03 10:44:43
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-04-09 09:12:05
 */

namespace Doco\gudang\controllers;
use Yii;
use yii\filters\AccessControl; 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

use Doco\gudang\models\UpdatePemakaianForm;
use yii\helpers\ArrayHelper;

class InformasiPemakaianBarangController extends DocoController{
	protected $_title = "Informasi Pemakaian Barang";
    protected $_module = 'gudang/informasi-pemakaian-barang/'; //buat fe
    protected $_backUrl = 'inf-pemakaian-barang/'; //buat be
    protected $_restGudang;    
    protected $_cacheName;

    public function init()
    {
        parent::init();
        $this->_restGudang = Yii::$app->docoRest->gudang;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }
    public function actionIndex()
    {
    	$title = Yii::t('fe', $this->_title);
    	$module = $this->_module;
    	
    	return $this->render('index', get_defined_vars());
    }
    public function actionView($id){    	
    	$model = new UpdatePemakaianForm;
    	$title = Yii::t('fe', 'Detail pemakaian barang'); 
    	Yii::$app->session->set('_prefix', $id.Yii::$app->docoVars->user('id_pegawai'));      	
    	try {    		
    		$getHeader = $this->_restGudang->get($this->_backUrl.'get-header', ['query'=>['id'=>DocoHelpers::decrypt($id)]]);
    		$response = json_decode($getHeader->getBody(), true);
    		$data = $response['response'];
    		$result = $this->_restGudang->get('allow/set-cache-konvert-satuan', []);    		
	        $result = json_decode($result->getBody(), true);
	        $cacheSatuan = isset($result['response']) ? $result['response'] : [];
	        Yii::$app->cache->set('konvert-satuan', $cacheSatuan, 3600);	
    	} catch (RequestException $e) {
    		Yii::info($e->getMessage());
            $cacheSatuan = [];
        }

        $cache = json_encode($cacheSatuan);
        $id_pegawai = Yii::$app->docoVars->user("id_pegawai");

    	return $this->render('view', get_defined_vars());
    }

    public function actionGetData() {
    	Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        // init ruangan
        if(!isset($yiiRestfulParams['advanced-filter']['ruangan_id'])){
            $yiiRestfulParams['advanced-filter']['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
        }  
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $infoPemakaian = $this->guzzleExec($this->_restGudang, [
                'url' => $this->_backUrl.'index',
                'method' => 'post',
                'payload' => [
                    'query' => $yiiRestfulParams
                ],
                'returnResponse' => true
            ]);
            
            $infoPemakaian = ArrayHelper::getValue($infoPemakaian, 'data', []);
            $no = $request->get('start',1);
            foreach ($infoPemakaian['data'] as $key => $value) {
                $no++;                
                $primaryKey = DocoHelpers::encrypt($value['pemakaianbarang_id']);
                unset($value['pemakaianbarang_id']);
                $value['tgl_pemakaianbarang'] = date("j M Y", strtotime($value['tgl_pemakaianbarang']));
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no; 
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $infoPemakaian['_meta']['totalCount'];
            $result['recordsFiltered'] = $infoPemakaian['_meta']['totalCount'];

            return $result;
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionGetNopemakaian()
    {
    	if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $response = $this->_restGudang->request('POST', $this->_backUrl.'get-nopemakaian',[
                            'form_params'=>['term'=>$_GET['q']['term']],
                        ]);            
            $body = json_decode($response->getBody(), true);   
            $data = [];                            
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id'=>$value['no_pemakaianbarang'],'text'=>$value['no_pemakaianbarang']];
            }          
            $total = count($body['response']);      
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];                
            return DocoHelpers::response($return);
        }
    }

    public function actionGetListItem($id) {
        Yii::$app->response->format = Response::FORMAT_JSON;
    	$sessName = Yii::$app->session->get('_prefix');
    	$cacheName = 'detail_nopemakaian_'.$sessName;
    	$request = Yii::$app->request;
    	$decId = $id;
    	$draw = $request->get('draw', 1);

    	$result['data'] = '';
    	$result['draw'] = $draw;
    	$result['recordsTotal'] = 0;
    	$result['recordsFiltered'] = 0;
    	$decId = DocoHelpers::decrypt($id);
        $get = $request->get();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($get);
        $yiiRestfulParams['id'] = $decId;
    	try {    		    	    	
            $detailPemakaian = $this->guzzleExec($this->_restGudang, [
                'url' => $this->_backUrl.'detail-nopemakaian',
                'method' => 'post',
                'payload' => [
                    'query' => $yiiRestfulParams
                ],
                'returnResponse' => true
            ]);

    		$cache = ArrayHelper::getValue($detailPemakaian, 'data', []);
	    	$data = [];
	    	$no = $request->get('start', 0);
	    	foreach ($cache['data'] as $key => $value) {
	    		$no++;                
                $primaryKey = DocoHelpers::encrypt($value['barang_id']);
                unset($value['barang_id']);                
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;                 
                $value['aksi'] = !empty($value['aksi']) ? Html::button(
                        "<i class='fa fa-trash'></i>",
                        [
                            'style' => 'margin-right:5px',
                            'class' => 'btn btn-danger btn-xs delete',
                            'style' => 'margin-right:5px; padding-left:10px !important;',
                            'action' => Url::to(['delete-cache', 'id' =>$no-1]),
                        ]
                    ) : null;                
                $data[$key] = $value;
	    	}
	    	$result['data'] = $data;
            $result['recordsTotal'] = $cache['_meta']['totalCount'];
            $result['recordsFiltered'] = $cache['_meta']['totalCount'];
            return $result;
    	}  catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }

    }
    public function actionSaveCache()
    {
    	$sessName = Yii::$app->session->get('_prefix');
    	$request = Yii::$app->request;       
    	if($request->post()){
    		$post = $request->post();    	    		
    		$updateForm = $post['UpdatePemakaianForm'];    		
    		$model = new UpdatePemakaianForm;
    		$formName = substr(strrchr(get_class($model), "\\"), 1);
    		$cacheKonv = Yii::$app->cache->get('konvert-satuan');
    		$model->load($post);
    		if($model->validate()){    			    			
	    		$barang_id = $post['id'];	    		
	    		$barang_nama = explode(' - ', $post['text'])[1];    		
	    		$pemakaianbarang_id = $model->pemakaianbarang_id;
	    		$instalasi_id = Yii::$app->docoVars->workspace('instalasi_id');
	    		$instalasi = Yii::$app->docoVars->workspace('instalasi_nama');
	    		$ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
	    		$ruangan = Yii::$app->docoVars->workspace('ruangan_name');      		  	
	    		$satuanbesar_id = $post['satuanbesar_id'];
	    		$satuanbesar_nama = $post['satuanbesar_nama'];
	    		$satuankecil_id = $post['satuankecil_id'];
	    		$satuankecil_nama = $post['satuankecil_nama'];
	    		$cacheBarang = \Yii::$app->cache->get('detail_nopemakaian_'.$sessName);      	
	    		$jumlah_input = $model->qty;	    		
	    		if (isset($cacheKonv[$model->satuan][$satuankecil_id])) {	    			    			
	                $jumlah_input = ($cacheKonv[$model->satuan][$satuankecil_id] * $model->qty);
	            }	            
	    		$newBarang = [
	    			'aksi'=>'hapus',
	    			'barang_id'=>$barang_id,
	    			'barang_nama'=>$barang_nama,
	    			'harga_netto'=>$post['harga_netto'],
	    			'instalasi_id'=>$instalasi_id,
	    			'instalasi_nama'=>$instalasi,
	    			'jumlah_input'=>$jumlah_input,
	    			'jumlah_pakai'=>$model->qty,
	    			'keteranganpakai'=>$model->keterangan,
	    			'nama_pegawai'=>Yii::$app->docoVars->user('nama'),
	    			'no_pemakaianbarang'=>$model->no_pemakaian,
	    			'pegawai_id'=>Yii::$app->docoVars->user('id_pegawai'),
	    			'pemakaianbarang_id'=>$pemakaianbarang_id,
	    			'pemakaianbarangdetail_id'=>'',	    			    		
	    			'ruangan_id'=>$ruangan_id,
	    			'ruangan_name'=>$ruangan,
	    			'satuankecil_id'=>$satuankecil_id,
	    			'satuanbesar_id'=>$satuanbesar_id,
	    			'satuan_besar'=>$satuanbesar_nama,
	    			'satuan_kecil'=>$satuankecil_nama,
	    			'tgl_pemakaianbarang'=>date('Y-m-d'), 
	    			'ppn'=>$post['ppn'],
	    			'harga_jual'=>$post['harga_jual'],
	    			'harga_max'=>$post['harga_max'],
	    			'harga_min'=>$post['harga_min'],
	    		];    		
	    		array_push($cacheBarang, $newBarang);	 	    			
	    		\Yii::$app->cache->set('detail_nopemakaian_'.$sessName, $cacheBarang);
	    		return DocoHelpers::response(['response'=>'done']);	
    		}else{
    			 $response = $model->errors;
            	 return DocoHelpers::response($response, 422, $formName);
    		}
    		
    	}
    }
    public function actionResetCache()
    {
    	try{
    		$sessName = Yii::$app->session->get('_prefix');
    		\Yii::$app->cache->delete('detail_nopemakaian_'.$sessName);
    		$response = ['message'=>'done'];
    	} catch (RequestException $e) {
    		$response = ['message'=>DocoHelpers::dataTabelsException($e->getMessage())];            
        } catch (\Exception $e) {
        	$response = ['message'=>DocoHelpers::dataTabelsException($e->getMessage())];            
        }
        return DocoHelpers::response($response);
    }    
    public function actionSaveData(){
    	try {
    		$sessName = Yii::$app->session->get('_prefix');
    		$cacheBarang = \Yii::$app->cache->get('detail_nopemakaian_'.$sessName);
    		$data = [];
    		foreach ($cacheBarang as $key => $value) {    			
				if(!empty($cacheBarang[$key]['aksi'])){
					$data[$key] = $value;
				}    			
    		}    		            
            if(count($data) > 0){                                
                $response = $this->_restGudang->post($this->_backUrl.'save-data', ['form_params'=>$data]);                
                $body = json_decode($response->getBody(), true);        
                \Yii::$app->cache->delete('detail_nopemakaian_'.$sessName);
            }else{                
                $body['response'] = 'done';
            }            
    		return DocoHelpers::response($body['response']);
    	} catch (RequestException $e) {
    		$response = ['message'=>DocoHelpers::dataTabelsException($e->getMessage())];            
        } catch (\Exception $e) {
        	$response = ['message'=>DocoHelpers::dataTabelsException($e->getMessage())];            
        }
        return DocoHelpers::response($response);
    }
    public function actionDeleteCache($id)
    {
    	try{
    		$sessName = Yii::$app->session->get('_prefix');
    		$cache = \Yii::$app->cache->get('detail_nopemakaian_'.$sessName);
    		unset($cache[$id]);
    		\Yii::$app->cache->set('detail_nopemakaian_'.$sessName, $cache);
    		return DocoHelpers::response(['message'=>'Hapus data berhasil']);
    		$response = ['message'=>'done'];
    	} catch (RequestException $e) {
    		$response = ['message'=>DocoHelpers::dataTabelsException($e->getMessage())];            
        } catch (\Exception $e) {
        	$response = ['message'=>DocoHelpers::dataTabelsException($e->getMessage())];            
        }
        return DocoHelpers::response($response);
    }
    public function actionGetBarang()
    {
    	$request = Yii::$app->request;
    	$response = [];
    	$ruangan = Yii::$app->docoVars->workspace('ruangan_id');
    	$instalasi = Yii::$app->docoVars->workspace('instalasi_id');    	
    	
    	try {
    		if(isset($_GET['q']['term'])){
    			$term = $_GET['q']['term'];    			
    			$response = $this->_restGudang->get('allow/list-stok-barang', ['query'=>['ruangan_id'=>$ruangan, 'instalasi_id'=>$instalasi,'term'=>$term]]);
    			$body = json_decode($response->getBody(), true);
    			$result = [];
    			foreach ($body['response']['data'] as $key => $value) {
    				$satuan = [];
	                if (isset($value['satuankecil_id'])) {
	                    $satuan[$value['satuankecil_id']] = $value['satuankecil_nama'];
	                }

	                if (isset($value['satuanbesar_id'])) {
	                    $satuan[$value['satuanbesar_id']] = $value['satuanbesar_nama'];
	                }
    				$result[] = [
    					'id'=>$value['barang_id'], 
    					'text'=>$value['barang_kode'] . ' - ' . $value['barang_nama'],
    					'stok'=>$value['qty_tersedia'],
    					'stok' => $value['qty_tersedia'],
	                    'satuankecil_id' => $value['satuankecil_id'],
	                    'satuankecil_nama' => $value['satuankecil_nama'],
	                    'satuanbesar_id' => $value['satuanbesar_id'],
	                    'satuanbesar_nama' => $value['satuanbesar_nama'],
	                    'instalasi_id' => $value['instalasi_id'],
	                    'ruangan_id' => $value['ruangan_id'],
	                    'satuan' => $satuan,
	                    'harga_netto'=>$value['harga_netto'],
	                    'harga_jual'=>$value['harga_jual'],
	                    'harga_max'=>$value['harga_max'],
	                    'harga_min'=>$value['harga_min'],
	                    'ppn'=>$value['ppn'],

    				];
    			}
    			$response = $result;
    			
    		}    		
    	} catch (RequestException $e) {
    		$response = ['message'=>DocoHelpers::dataTabelsException($e->getMessage())];            
        } catch (\Exception $e) {
        	$response = ['message'=>DocoHelpers::dataTabelsException($e->getMessage())];            
        }
        return DocoHelpers::response(['result'=>$result]);
    }

    public function actionPrintDetail($id, $nopemakaian){
        $id = DocoHelpers::decrypt($id);
        $path = Yii::getAlias("@download") . "/scetak-resep-".$nopemakaian.".pdf";
        $this->guzzleExec($this->_restGudang, [
            'url' => 'inf-pemakaian-barang/print-detail',
            'method' => 'get',
            'payload' => [
                'save_to' => $path,
                'query' => [
                    'id' => $id,
                    'nopemakaian' => $nopemakaian
                ]
            ]
        ]);
     
        return DocoHelpers::previewPdf($path);
    }

    public function actionDelete($id){
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restGudang->post('inf-pemakaian-barang/delete-detail',[                
                'query' => [
                        'id'=>$id,
                ],
            ]);
            $body = json_decode($response->getBody(), true);                                    
            return DocoHelpers::response($body['response']);
        } catch (RequestException $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

}