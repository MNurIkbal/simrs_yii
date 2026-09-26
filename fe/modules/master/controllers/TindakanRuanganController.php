<?php
/**
 * @author: arief saputra
 * @description: master layar antrian
**/

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\TindakanRuanganForm;
use GuzzleHttp\Exception\RequestException;

class TindakanRuanganController extends DocoController
{
	protected $_title = 'Tindakan Ruangan';
    protected $_module = 'tindakan-ruangan/';
    protected $_restMaster;
    protected $_ruangan_id;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
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
        $status = $this->_status;
        $title = Yii::t('fe', $this->_title);

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        try {
            $request = Yii::$app->request;
            $post = $request->post();
            $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");

            $columns = $post['columns'];
            $advancedFilters = [];
            $filters = [];
            if(count($columns)) {
                foreach($columns as $column) {
                    if ($column['searchable'] == 'true') {
                        $filters[] = $column['name'] ? $column['name'] : $column['data'];
                        if (preg_match("/.+/i", $column['search']['value'])) {
                            if ($column['name']) {
                                $advancedFilters[$column['name']] = $column['search']['value'];
                            } else {
                                $advancedFilters[$column['data']] = $column['search']['value'];
                            }
                        }
                    }
                }
            }
            $response = $this->_restMaster->request('POST', 'tindakan-ruangan',[
                            'query' => ['ruangan_id'=>$ruangan_id],
                            'form_params' => ['advanced-filter'=>$advancedFilters]
                        ]);
            $row = [];
            $body = json_decode($response->getBody(),TRUE);
            // return DocoHelpers::response($body);
            $no = $request->post('start',0);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;

                $primary = json_encode([$value['daftartindakan_id'],$value['ruangan_id']]);
                $primaryKey = DocoHelpers::encrypt($primary);

                $value['primary'] = $primaryKey;
                unset($value['tindakanruangan_id']);
                
                $value['aksi'] = Html::button('<i class="fa fa-pencil" aria-hidden="true"></i>',[
                    'class' => 'btn btn-success btn-xs',
                    'style' => 'margin-right:5px',
                    'data-toggle' => 'modal',
                    'action' => Url::to([$this->_module .'update-pengambilan-antrian','id' => $primaryKey]),
                    'data-target' => '#modal_backdrop',
                    'data-popup' => "tooltip",
                    'title' => "Update"
                ]);
                $value['aksi'] .= Html::button('<i class="fa fa-trash" aria-hidden="true"></i>',[
                    'class' => 'btn btn-danger btn-xs delete',
                    'style' => 'margin-right:5px',
                    'data-popup' => "tooltip",
                    'action' => Url::to([$this->_module .'delete-pengambilan-antrian','id' => $primaryKey]),
                    'title' => "Hapus"
                ]);

                $value['rowNum'] = $no;
                $row[$key] = $value;
            }
            $return = [
                'data' => $row,
                'draw' => $request->post('draw'),
                'recordsTotal' => $body['response']['count'],
                'recordsFiltered' => $body['response']['count']
            ];
            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionGetDaftarTindakan()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){        

            $response = $this->_restMaster->request('POST', 'tindakan-ruangan/list-daftar-tindakan',[
                            'form_params'=>['term'=>$_GET['q']['term']],
                        ]);            
            $body = json_decode($response->getBody(), true);   
            $data = [];                                
            foreach ($body['response'] as $key => $value) {                
                $data[] = ['id'=>$value['daftartindakan_id'],'text'=>$value['daftartindakan_kode']];                
            }          
            $total = count($body['response']);      
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];                
            return DocoHelpers::response($return);
        }
    }

    public function actionGetDaftarTindakanNama()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){        

            $response = $this->_restMaster->request('POST', 'tindakan-ruangan/list-daftar-tindakan-nama',[
                            'form_params'=>['term'=>$_GET['q']['term']],
                        ]);            
            $body = json_decode($response->getBody(), true);   
            $data = [];                                
            foreach ($body['response'] as $key => $value) {                
                $data[] = ['id'=>$value['daftartindakan_id'],'text'=>$value['daftartindakan_nama']];                
            }          
            $total = count($body['response']);      
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];                
            return DocoHelpers::response($return);
        }
    }

    public function actionGetKelompokTindakan()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){        

            $response = $this->_restMaster->request('POST', 'tindakan-ruangan/list-kelompok-tindakan',[
                            'form_params'=>['term'=>$_GET['q']['term']],
                        ]);            
            $body = json_decode($response->getBody(), true);   
            $data = [];                                
            foreach ($body['response'] as $key => $value) {                
                $data[] = ['id'=>$value['kelompoktindakan_id'],'text'=>$value['kelompoktindakan_nama']];                
            }          
            $total = count($body['response']);      
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];                
            return DocoHelpers::response($return);
        }
    }

    public function actionGetKategoriTindakan()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){        

            $response = $this->_restMaster->request('POST', 'tindakan-ruangan/list-kategori-tindakan',[
                            'form_params'=>['term'=>$_GET['q']['term']],
                        ]);            
            $body = json_decode($response->getBody(), true);   
            $data = [];                                
            foreach ($body['response'] as $key => $value) {                
                $data[] = ['id'=>$value['kategoritindakan_id'],'text'=>$value['kategoritindakan_nama']];                
            }          
            $total = count($body['response']);      
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];                
            return DocoHelpers::response($return);
        }
    }

    public function actionGetJenisKegiatanTindakan()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){        

            $response = $this->_restMaster->request('POST', 'tindakan-ruangan/list-jenis-kegiatan-tindakan',[
                            'form_params'=>['term'=>$_GET['q']['term']],
                        ]);            
            $body = json_decode($response->getBody(), true);   
            $data = [];                                
            foreach ($body['response'] as $key => $value) {                
                $data[] = ['id'=>$value['jeniskegiatantindakan_id'],'text'=>$value['jeniskegiatantindakan_nama']];
            }          
            $total = count($body['response']);      
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];                
            return DocoHelpers::response($return);
        }
    }

    public function actionCreate()
    {   
        try{
            $request = Yii::$app->request;
            $title = "Tambah Tindakan Ruangan";

            $model = new TindakanRuanganForm;
            if($request->post()){
                $post = $request->post();
                // dump($post);die;
                $post['user_id'] = Yii::$app->docoVars->user("id");
                $response = $this->_restMaster->request('POST', 'tindakan-ruangan/create', ['form_params'=>$post]);
                $body = json_decode($response->getBody(), true);
                return DocoHelpers::response($body);

            }
        } catch (RequestException $e) {
            $error = json_decode($e->getResponse()->getBody(),true);
            return DocoHelpers::response($error,false,true);
            // return DocoHelpers::responseTemplate(500,$e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }

        $ruangan_id = $this->_ruangan_id;
        return $this->render('form', get_defined_vars());
    }

    public function actionDetail($id)
    {
        $title = "Detail Tindakan Ruangan";
        $request = Yii::$app->request;        
        $id = json_decode(DocoHelpers::decrypt($id));
        $daftartindakan_id = $id[0];
        $ruangan_id = $id[1];

        $model = new TindakanRuanganForm;

        $response = $this->_restMaster->request('GET', 'tindakan-ruangan/view',
            [
                'query' => ['ruangan_id' => $ruangan_id, 'daftartindakan_id' => $daftartindakan_id]
            ]
        );

        $data = json_decode($response->getBody(), true);
        $data = $data['response'];
    
        $data['is_active'] = $data['is_active'];
        $model->attributes = $data;

        if($request->post()){
            $post = $request->post();
            $post['user_id'] = Yii::$app->docoVars->user("id");
            $response = $this->_restMaster->request('POST', 'tindakan-ruangan/update', ['form_params'=>$post]);
            $body = json_decode($response->getBody(), true);
            $return = ['response'=>$body['response']];
            return DocoHelpers::response($return);
        }
		return $this->render('detail', get_defined_vars());
    }
}