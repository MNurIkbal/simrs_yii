<?php 
/**
 * @author : Budi
 * @description : Controller Informasi Tarif Tindakan
 * @date : 15 Januari 2018 
 */

namespace Doco\informasi\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class TarifTindakanController extends DocoController
{
	protected $_title;
    protected $restInformasi;
    protected $_controllerService = 'tarif-tindakan/';

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Informasi Tarif Tindakan');
        $this->restInformasi = Yii::$app->docoRest->informasi;
    }

    public function actionIndex()
    {
    	$title = $this->_title;
        $api = $this->restInformasi->get($this->_controllerService.'generate-api');
        $api = json_decode($api->getBody(), True);

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
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

        try {
            $response = $this->restInformasi->get($this->_controllerService.'index?'.http_build_query($yiiRestfulParams), [
                'form_params' => []]);

            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                
                // $value['instalasi'] = $value['instalasi_nama'].' / '.$value['ruangan_nama'];

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