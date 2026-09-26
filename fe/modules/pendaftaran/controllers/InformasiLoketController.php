<?php

/**
 * @author Randy Vianda Putra
 * @todo Informasi Loket
 * @copyright 12 Desember 2018 aweutist
 */


namespace Doco\pendaftaran\controllers;

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

class InformasiLoketController extends DocoController
{
    protected $_restPendaftaran;
    protected $_module = 'pendaftaran/informasi-loket/';

    public function init()
    {
        parent::init();
        $this->_restPendaftaran = Yii::$app->docoRest->pendaftaran;

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
        try {

            return $this->render('index', get_defined_vars());
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionGetDataLoket()
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
        try{
            $response = $this->_restPendaftaran->request('GET', 'inf-loket/index');

            $body = json_decode($response->getBody(),TRUE);
            $data_loket = $body['response'];
            $no = $request->get('start',1);
            
            foreach ($data_loket as $key => $val) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($val['loket_id']);
                $val['primary'] = $primaryKey;
                $val['rowNum'] = $no;
                $data[$key] = $val;
            }

        	$result['data'] = $data;
            $result['recordsTotal'] = 0;
            $result['recordsFiltered'] = 0;

            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionUnsetLoket($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restPendaftaran->request('GET', 'inf-loket/unset-loket?id=' . $id);
            $response = json_decode($response->getBody(), true);

            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}
