<?php 

/**
 * @author Randy Vianda Putra
 * @todo Informasi Formulir So
 * @copyright 19 January 2018 aweutist
 */

namespace Doco\apotek\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Doco\apotek\models\InformasiForm;

class InformasiStokController extends DocoController
{
    
    protected $_title = "Informasi Stok dan Ketersediaan Obat Alkes";
    protected $_module = '/apotek/informasi-stok';
    protected $_restApotek;

    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
    }

    
    public function actionObatAlkes()
    {
        $title = $this->_title;
        $model = new InformasiForm;        
        $response = $this->_restApotek->get('instalasi?advanced-filter[is_active]=1');
        $body = json_decode($response->getBody(), TRUE);
        $instalasi = $body['response']['data'];
        
        $response = $this->_restApotek->get('ruangan?advanced-filter[is_active]=1');
        $body = json_decode($response->getBody(), TRUE);
        $ruangan = $body['response']['data'];
        // $cache = Yii::$app->cache;
        // $data = $this->getResep();
        // $temp_cache = [];
        // $no_resep = $cache->get('no_resep');
        // if ($no_resep === false) {
        //     $no = 0;
        //     foreach ($data['data_resep'] as $key => $value) {
        //         $list_cache[$value['noresep']] = [
        //             'reseptur_id' => $value['reseptur_id'],
        //             'no_resep' => $value['noresep'],
        //             'tgl_resep' => $value['tglresep'],
        //             'nama_pasien' => $value['nama_pasien'],
        //             'no_pendaftaran' => $value['no_pendaftaran'],
        //             'nama_dokter' => $value['nama_pegawai'],
        //             'instalasi' => $value['instalasireseptur_nama'],
        //             'ruangan' => $value['ruangan_nama']
        //         ];
        //         $no++;
        //     }
        //     if ($no) {
        //         $listResep = json_encode($list_cache);
        //         Yii::$app->cache->set('no_resep', $listResep, 30);
        //     }
        // }
        // $no_resep = $cache->get('no_resep');

        return $this->render('obat-alkes', get_defined_vars());
    }

    public function actionDetail()
    {
        $title = 'Formulir Stok Opname';
        $model = new InformasiForm;
        
        
        return $this->render('detail', get_defined_vars());
    }
    public function actionGetData(){
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
            $response = $this->_restApotek->get('inf-stok-obat-alkes/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['periodestok_id']);
                unset($value['periodestok_id']);

                $value['detail'] = Html::button('<i class="fa fa-list" aria-hidden="true"></i>', [
                    'class' => 'btn btn-primary btn-xs data-detail',
                    'action' => Url::home().$this->_module.'view?id='.$primaryKey,
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_backdrop',
                    'data-popup' => "tooltip", 'data-placement' => 'bottom',
                    'title' => \Yii::t('fe', 'Rincian Tagihan'),
                ]);

                $value['payment'] = Html::button('<i class="fa fa-shopping-cart" aria-hidden="true"></i>', [
                    'class' => 'btn btn-primary btn-xs data-payment',
                    'action' => Url::home().$this->_module.'view?id='.$primaryKey,
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_backdrop',
                    'data-popup' => "tooltip", 'data-placement' => 'bottom',
                    'title' => \Yii::t('fe', 'Pembayaran'),
                ]);

                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs data-check',
                    'data-value' => $value['obatalkes_namalain'],
                    'title' => \Yii::t('fe', 'Klik'),
                ]);

                $value['tglperiodestok_awal'] = date("j M Y", strtotime($value['tglperiodestok_awal']));
                $value['tglperiodestok_akhir'] = date("j M Y", strtotime($value['tglperiodestok_akhir']));

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
    public function actionGetObatalkes(){
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $response = $this->_restApotek->request('POST', 'inf-stok-obat-alkes/data-obat',[
                            'form_params'=>['term'=>$_GET['q']['term']],
                        ]);            
            $body = json_decode($response->getBody(), true);   
            $data = [];                            
            foreach ($body['response'] as $key => $value) {
                
                $data[] = ['id'=>$value['obatalkes_id'],'text'=>$value['obatalkes_namalain']];
                
            }          
            $total = count($body['response']);      
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];                
            return DocoHelpers::response($return);
        }
    }

    public function actionStokOpname(){
        return $this->render('stok-opname', get_defined_vars());
    }
    public function actionDetailStokOpname($id){        
        return $this->renderPartial('detail-so', get_defined_vars());

    }
    public function actionGetDataStokopname(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        // exit(json_encode($yiiRestfulParams));
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restApotek->get('inf-stok-opname/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            // return $body['response'];
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['stokopname_id']);
                unset($value['stokopname_id']);

                $value['detail'] = Html::button('<i class="fa fa-list" aria-hidden="true"></i>', [
                    'class' => 'btn btn-primary btn-xs data-detail',
                    'action' => 'detail-stok-opname?id='.$primaryKey,
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_backdrop_full',
                    'data-popup' => "tooltip", 'data-placement' => 'bottom',
                    'title' => \Yii::t('fe', 'Pembayaran'),
                ]);

                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs data-check',
                    'data-value' => $value['nostokopname'],
                    'title' => \Yii::t('fe', 'Klik'),
                ]);
                
                $value['tglstokopname'] = date("j M Y", strtotime($value['tglstokopname']));

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
    public function actionGetNostok(){
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $response = $this->_restApotek->request('POST', 'inf-stok-opname/data-nostok',[
                            'form_params'=>['term'=>$_GET['q']['term']],
                        ]);            
            $body = json_decode($response->getBody(), true);   
            $data = [];                            
            foreach ($body['response'] as $key => $value) {
                
                $data[] = ['id'=>$value['nostokopname'],'text'=>$value['nostokopname']];
                
            }          
            $total = count($body['response']);      
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];                
            return DocoHelpers::response($return);
        }
    }
    public function actionGetDetailSo($id){        
        $parent_id = DocoHelpers::decrypt($id);
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
            $response = $this->_restApotek->get('inf-stok-opname/detail?parent_id='.($parent_id).'&'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['obatalkes_m']['tglkadaluarsa'] = date("j M Y", strtotime($value['obatalkes_m']['tglkadaluarsa']));
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
}