<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-15 17:35:35
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-23 16:55:10
 */

namespace Doco\rm\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use app\modules\rm\models\PemakaianObatAlkesForm;
use app\modules\rm\models\PemakaianObatAlkesFilterForm;

class PemakaianObatAlkesController extends DocoController
{
	protected $_title = "Rm :: Pemakaian Obat Alkes";
    protected $_module = 'rm/pemakaian-obat-alkes/';
    protected $_restRm;

    public function init()
    {
        parent::init();
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionCreate()
    {        
        $request = Yii::$app->request;
        $model = new PemakaianObatAlkesForm;
        if($request->post()){
            $post = $request->post();
            $pegawai_id = Yii::$app->user->identity->pegawai_id;
            $ruangan_id = $_SESSION['active_workspace']['ruangan_id'];
            $isi_data = $post['isidata'];
            $tgl_pemakaian = $post['tgl_pemakaian'];
            $return = [
                'pegawai_id'=>$pegawai_id,
                'ruangan_id'=>$ruangan_id,
                'isi_data'=>$isi_data,
                'tgl_pemakaian'=>$tgl_pemakaian,

            ];
            $response = $this->_restRm->request('POST', 'tra-pemakaian-obatalkes/create',[
                                'form_params'=>$return,
                            ]); 
            $response = json_decode($response->getBody(), true);                
            return DocoHelpers::response($response, false, true);
            
        }
        return $this->render('create', get_defined_vars());
    }
    public function actionInformasi()
    {
        
        $module = 'rm/pemesanan-obat-alkes/';
        $model = new PemakaianObatAlkesFilterForm;
        return $this->render('informasi', get_defined_vars());  
    }
    public function actionLaporan()
    {        
        $model = new PemakaianObatAlkesFilterForm;
        return $this->render('laporan', get_defined_vars());
    }

    public function actionListObat(){
    	return $this->renderPartial('list-obat', get_defined_vars());	
    }
    public function actionListKodeObat(){
        return $this->renderPartial('list-kode-obat', get_defined_vars());      
    }

    public function actionGetDataOpsi()
    {
        try {
            $response = $this->_restRm->request('GET','tra-pemakaian-obatalkes/list-data');
            $data = json_decode($response->getBody(), true);
            $return = [
                'data_obat'=>$data['response']['data_obat'],
                'data_satuan'=>$data['response']['data_satuan'],
            ];

            return DocoHelpers::response($return);
        } catch (Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } 

    }
}
