<?php


namespace Doco\apotek\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class EndPointController extends DocoController
{


    protected $_restApotek;
    /**
     * @inheritdoc
     */

    public function init()
    {
        $this->_restApotek=Yii::$app->docoRest->apotek;
    }
    public function beforeAction($action)
    {
        return true;
    }

    public function actionGetRuangan($assign_id="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restApotek->get('allow/get-ruangan?instalasi_id='.$parent_label);
            // var_dump('allow/get-ruangan?instalasi_id='.$parent_label);
            // die();
            $body = json_decode($response->getBody(), True);
            // return $parent_label;
            foreach ($body['response']['data'] as $value) 
                $result['output'][] = [
                    'id' => $value['ruangan_id'], 
                    'name' => $value['ruangan_nama']
                ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetInstalasi($assign_id="")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restApotek->get('instalasi?advanced-filter[ruangan_m.ruangan_id]='.$parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['output'][] = [
                    'id' => $value['instalasi_id'],
                    'name' => $value['instalasi_nama']
                ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetPasien()
    {
        $instalasi_id = Yii::$app->docoVars->workspace('instalasi_id');
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $request = Yii::$app->request;
        $keyword = $request->get('q');

        $result = [];
        $result['results'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restApotek->get('allow/get-list-pasien',[
                'query' => [
                    'keyword'=>$keyword['term']
                ]
            ]);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value)
                $result['results'][] = [
                    'id' => $value['pasien_id'],
                    'text' => $value['no_rekam_medik'].' - '.$value['nama_pasien']
                ];

            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            return DocoHelpers::response($result);
        }
    }

    public function actionGetPegawai()
    {
        $request = Yii::$app->request;
        $keyword = $request->get('q');

        $result = [];
        $result['results'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restApotek->get('allow/get-list-pegawai',[
                'query' => [
                    'keyword'=>$keyword['term']
                ]
            ]);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value)
                $result['results'][] = [
                    'id' => $value['pegawai_id'],
                    'text' => $value['nama_pegawai']
                ];

            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            return DocoHelpers::response($result);
        }
    }
}
