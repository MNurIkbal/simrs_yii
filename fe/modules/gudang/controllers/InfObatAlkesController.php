<?php
// Author : Budi

namespace Doco\gudang\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\modules\gudang\models\TerimaMutasiBarangForm;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class InfObatAlkesController extends DocoController
{
    protected $_title = "Informasi Obat Alkes";
    protected $_module = 'gudang/inf-obat-alkes/';
    protected $_restGudang; 
    protected $_restMaster;
    protected $_ruangan_id;
    protected $_nama_ruangan;

    public function init()
    {
        parent::init();
        $this->_restGudang = Yii::$app->docoRest->gudang; 
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_ruangan_id = Yii::$app->docoVars->workspace('ruangan_id') ? Yii::$app->docoVars->workspace('ruangan_id') : 1;
        $this->_nama_ruangan = Yii::$app->docoVars->workspace('ruangan_name') ? Yii::$app->docoVars->workspace('ruangan_name') : '';
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
        $title = $this->_title;

        $api = $this->_restGudang->get('inf-obat-alkes/generate-api');
        $api = json_decode($api->getBody(), TRUE);
        $api = $api['response'];
        
        $module = $this->_module;

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        $ruangan_id = $this->_ruangan_id;
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
            $response = $this->_restGudang->get('inf-obat-alkes/index?' . http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            // return DocoHelpers::response($body);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['obatalkes_id']);
                unset($value['obatalkes_id']);
                $value['rowNum'] = $no; 
                $value['primary'] = $primaryKey;
                $value['harganetto'] = DocoHelpers::formatNumber($value['harganetto']);
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

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restGudang->delete('inf-obat-alkes/delete', 
                [
                    'form_params' => [],
                    'query' => ['id' => $id]
                ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()]);
        } catch (\Exception $e){
            return DocoHelpers::response(['message' => $e->getMessage()]);
        }
    }

    public function actionView($id)
    {
        return Yii::$app->runAction('/master/obat-alkes/detail',[
            'id' => $id,
            'is_flag' => 1
        ]);
    }
}
