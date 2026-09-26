<?php

/**
 * @Author: [Wahyu Saepuloh][wahyu.saepuloh@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\penatajasa\controllers;

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
use app\components\DocoSelect2Trait;

use app\modules\penatajasa\models\TagihanPasienForm;
use app\modules\penatajasa\models\HapusTindakanForm;
use app\modules\penatajasa\models\TindakanForm;
use app\modules\penatajasa\components\traits\TagihanTindakanTrait;
use app\modules\penatajasa\components\traits\PenjaminTrait;

class InfTagihanPasienController extends DocoController
{
    use TagihanTindakanTrait;
    use PenjaminTrait;
    use DocoSelect2Trait;

    protected $allowAction = ['*'];

    protected $_title = "Informasi Tagihan Pasien";
    protected $_module = '/penatajasa/inf-tagihan-pasien';
    protected $_controller = '/penatajasa/inf-tagihan-pasien';
    protected $_penataJasa;
    protected $_restApotek;


    public function init(){
        parent::init();
        $this->_penataJasa = Yii::$app->docoRest->penatajasa;
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_restApotek = Yii::$app->docoRest->apotek;
    }

    public function behaviors(){
      $behaviors = parent::behaviors();
      unset($behaviors['access']);
      unset($behaviors['verbs']);
      return $behaviors;
    }

    public function actionIndex(){
        $title = $this->_title;
        return $this->render('index', get_defined_vars());
    }

    public function actionDetail(){
        $request = Yii::$app->request;
        $session = Yii::$app->session;
        $noPendaftaran = $request->get('no_pendaftaran'); 
        $pendaftaranID = $request->get('pendaftaran_id'); 
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaranID);
        $title = $this->_title;
        $model = new TagihanPasienForm;
        $data_pasien = $this->getPasien($pendaftaran_id);
        $pendaftaran_id = $data_pasien['pendaftaran_id'];
        return $this->render('_detail', get_defined_vars());
    }

    private function getPasien($pendaftaran_id)
    {
        try {
            $request = $this->_penataJasa->get('informasi-pasien/get-pasien',[
                'query' => ['pendaftaran_id' => $pendaftaran_id ]
            ]);
            $response = json_decode($request->getBody(), true);
            $attributes = $response['response'];
            return $attributes;
        } catch (\RequestException $e) {
            return [];
        }
    }

    public function actionDataAudit($id = null)
    {
        try {
            $pendaftaran_id = DocoHelpers::encrypt($id);
            $session = Yii::$app->session;
            $tagihanTindakan = $session->get('tagihanTindakan-pasien-'.$id);
            $tagihanPenjamin = $session->get('list-penjamin-data-'.$id);
            $param['tindakanpelayanan_id'] = array();
            $param['pendaftaranpenjamin_id'] = array();
            
            if(!empty($tagihanTindakan)) {
                foreach($tagihanTindakan as $key => $value){
                    if($value['tindakanpelayanan_id'] != null && $value['is_deleted'] == true){
                        $param['tindakanpelayanan_id'][$key] = null;
                    }
                    $param['tindakanpelayanan_id'][$key] = isset($value['tindakanpelayanan_id']) ? $value['tindakanpelayanan_id'] : null;
                }
            }
            
            if(!empty($tagihanPenjamin)) {
                foreach($tagihanPenjamin as $index => $value){
                    if($value['pendaftaranpenjamin_id'] != null && $value['is_deleted'] == true){
                        $param['pendaftaranpenjamin_id'][$index] = null;
                    }
                    $param['pendaftaranpenjamin_id'][$index] = isset($value['pendaftaranpenjamin_id']) ? $value['pendaftaranpenjamin_id'] : null;
                }
            }
            
            // return json_encode($param);
            if((in_array(null, $param['tindakanpelayanan_id']) == 1) || (in_array(null, $param['pendaftaranpenjamin_id']) == 1)){
                return true;
            } else {
                return false;
            }
        } catch (\RequestException $e) {
            return [];
        }
    }

    public function actionLogActivity()
    {
        $title = 'Log Activity';
        $request = Yii::$app->request;
        $pendaftaran_id = $request->get('pendaftaran_id');
        $isKasir = $request->get('is_kasir');
        try {
            $dataLog = $this->getDataLog($pendaftaran_id);
        } catch (RequestException $e) {
            $dataLog = [];
        }
        return $this->renderAjax('__log_activity',get_defined_vars());
    }

    private function getDataLog($pendaftaran_id)
    {
        try {
            $request = $this->_penataJasa->get('informasi-pasien/get-log-activity',[
                'query' => ['pendaftaran_id' => $pendaftaran_id ]
            ]);
            $response = json_decode($request->getBody(), true);
            $attributes = $response['response'];
            return $attributes;
        } catch (\RequestException $e) {
            return [];
        }
    }

    public function actionGetTarif()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $get = $request->get();
        if(!$get['daftartindakan_id']){
            return null;
        }
        return $this->guzzleExec($this->_penataJasa, [
            'url' => 'informasi-pasien/get-tarif',
            'payload' => [
                'query' => $get,
            ]
        ]);
    }
    public function actionGetTindakanRuangan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $get = $request->get();
        if(!$get['daftartindakan_id']){
            return null;
        }
        return $this->guzzleExec($this->_penataJasa, [
            'url' => 'informasi-pasien/get-tarif',
            'payload' => [
                'query' => $get,
            ]
        ]);
    }

    public function actionGetDataLogActivity()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $payload = DocoDatatableHelper::advancedFilterParam();
        $payload['pendaftaran_id'] = $request->get('pendaftaran_id');
        $payload['is_kasir'] = $request->get('is_kasir');
        $response = $this->guzzleExec($this->_penataJasa, [
            'url' => "informasi-pasien/get-data-log-activity",
            'payload' => [
                'query' => $payload,
            ]
        ]);
        foreach ($response['data'] as $key => $value) {
            $response['data'][$key]['waktu'] = date("j F Y H:i:s", strtotime($value['waktu']));
        }
        $response['recordsTotal'] = $response['_meta']['totalCount'];
        $response['recordsFiltered'] = $response['_meta']['totalCount'];
        return $response;

    }
}
