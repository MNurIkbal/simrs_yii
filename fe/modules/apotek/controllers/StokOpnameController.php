<?php 

/**
 * @author Randy Vianda Putra
 * @todo Informasi Formulir So
 * @copyright 17 January 2018 aweutist
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
use Doco\apotek\models\SoForm;
use Doco\apotek\models\StokForm;

class StokOpnameController extends DocoController
{
    
    protected $_title = "Stok Opname";
    protected $_module = '/apotek/stok-opname';
    protected $_restApotek;
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restApotek = Yii::$app->docoRest->apotek;
        $this->_restMaster = Yii::$app->docoRest->master;

    }

    
    public function actionIndex()
    {
        $request = Yii::$app->request;
        $title = $this->_title;
        $model = new SoForm;
        $model_form = new StokForm;
        //$instalasi = $this->getInstalasi();
        $instalasi = [];
        $ruangan = [];        
        $jenis = $this->getJenis();
        if($request->post()){            
            $post = $request->post();            
            $post['StokForm']['formuliropname_id'] = DocoHelpers::decrypt($post['StokForm']['formuliropname_id']);
            $response = $this->_restApotek->request('POST','stok-opname/create',['form_params'=>$post]);
            $body = json_decode($response->getBody(), true);

            return json_encode($body['response']);

        }

        return $this->render('index', get_defined_vars());
    }
    public function getInstalasi(){
        $response = $this->_restApotek->get('stok-opname/get-instalasi', []);
        $body = json_decode($response->getBody(), true);

        return $body['response'];
    }

    public function actionGetDataDetail($id){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;        
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());        
        $id = DocoHelpers::decrypt($id);
        $draw = $request->get('draw', 1);        
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restApotek->get('stok-opname/data-formulir-detail', ['query' => ['id'=>$id]]);
            $body = json_decode($response->getBody(), true);            
            $no = $request->get('start',1);            
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['formstokopname_id']);
                unset($value['formstokopname_id']);                
                $batch = !empty($value['nobatch']) ? $value['nobatch'] : null;
                $value['stok_fisik'] = "<input type='text' data-target='total_row_".$no."' data-harga='hargajual_".$no."' class='form-control stokfisik' name='StokForm[stok_fisik][".$no."]'><input type='hidden' value='".$value['obatalkes_id']."' name='StokForm[obatalkes_id][".$no."]'><input type='hidden' value='".$value['stok_sistem']."' name='StokForm[stok_sistem][".$no."]'><input type='hidden' class='hargajual_".$no."' value='".$value['hargajual']."' name='StokForm[harga_satuan][".$no."]'>";
                $value['kondisi'] = Html::dropDownList('StokForm[kondisi]['.$no.']', null, ['1'=>'Baik','2'=>'Buruk'],['class'=>'form-control']);
                $value['tgl_kadaluarsa']  = "<input type='hidden' value='{$batch}' name='StokForm[nobatch][".$no."]'><input type='text' class='form-control daterange-single' name='StokForm[tgl_kadaluarsa][".$no."]'><input type='hidden' class='total_row total_row_".$no."'>";
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }
            $result['data'] = $data;
            $result['draw'] = $request->post('draw');
            $result['recordsTotal'] = $body['response']['count'];
            $result['recordsFiltered'] = $body['response']['count'];
            return $result;
        } catch(RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        } catch(\Exception $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDataFormulir(){
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $response = $this->_restApotek->request('POST', 'stok-opname/data-formulir',[
                            'form_params'=>['term'=>$_GET['q']['term']],
                        ]);            
            $body = json_decode($response->getBody(), true);   
            $data = [];                            
            foreach ($body['response'] as $key => $value) {
                $id = DocoHelpers::encrypt($value['formulirstokopname_id']);
                $data[] = ['id'=>$id,'text'=>$value['noformulir']];
                
            }          
            $total = count($body['response']);      
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];                
            return DocoHelpers::response($return);
        }
    }
    public function actionGetDataSum($id){
        $id = DocoHelpers::decrypt($id);
        $response = $this->_restApotek->request('GET', 'stok-opname/get-sum',['query'=>['id'=>$id]]);            
        $body = json_decode($response->getBody(), true);                  
        return DocoHelpers::response($body['response']);
    
    }
    public function getJenis(){
        $JenisIDRequest = $this->_restMaster->get('lookup/list-lookup-by-type?param=jenis_stokopname');
        $body = json_decode($JenisIDRequest->getBody(),TRUE);
        $ddlJenisID = $body['response'];     
        foreach ($ddlJenisID as $key => $value) {
              $data[$value] = $value;
              $ddlJenisID = $data;
          }  
        return $ddlJenisID;
    }

}