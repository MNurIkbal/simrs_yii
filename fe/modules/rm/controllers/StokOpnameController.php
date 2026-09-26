<?php 
/**
 * @author : Budi
 * @description : Controller RM Insert Formulir Stok Opname
 * @date : 15 Januari 2018 
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

class StokOpnameController extends DocoController
{
	protected $_title;
    protected $_restRm;

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Formulir Stok Opname');
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function actionIndex()
    {
    	$title = $this->_title;
        
        $instalasi = [];
        $ruangan = [];
        
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
            // $response = $this->_restMaster->get('cara-bayar/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            // $body = json_decode($response->getBody(), True);
            $body = [];
            $no = $request->get('start',1);
            // foreach ($body as $key => $value) {
                $no++;
                // $primaryKey = DocoHelpers::encrypt($value['carabayar_id']);
                // unset($value['carabayar_id']);

                $value['nama_obat'] = 'AA';
	        	$value['stok_sistem'] = '100';
	        	$value['stok_fisik'] = Html::textInput('stok_fisik',null, [
                    'class' => 'form-control',
                    'placeholder' => Yii::t('fe', 'Stok Fisik')
                ]);

	        	$value['kondisi'] = Html::textInput('kondisi',null, [
                    'class' => 'form-control',
                    'placeholder' => Yii::t('fe', 'Kondisi')
                ]);

	        	$value['tanggal_kadaluarsa'] = Html::textInput('tanggal_kadaluarsa',null, [
                    'class' => 'form-control pickadate',
                    'placeholder' => Yii::t('fe', 'Tanggal Kadaluarsa')
                ]);

                $value['rowNum'] = 1;
                // $data[$key] = $value;
                $data[] = $value;
            // }

            $result['data'] = $data;
            // $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            // $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            $result['recordsTotal'] = 1;
            $result['recordsFiltered'] = 1;
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