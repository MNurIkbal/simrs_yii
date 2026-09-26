<?php


namespace Doco\gizi\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class EndPointController extends DocoController
{
    protected $_restGizi;
    /**
     * @inheritdoc
     */

    public function init()
    {
        $this->_restGizi=Yii::$app->docoRest->gizi;
    }
    public function beforeAction($action)
    {
        return true;
    }

    public function actionFilterPenjamin()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];
        
        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restGizi->get('allow/get-penjamin?carabayar_id='.$parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value)
                $result['output'][] = [
                    'id' =>$value['penjamin_id'],
                    'name' => $value['penjamin_nama']
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

    public function actionFilterCaraBayar()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $params = '';
        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
            $params = '?penjamin_id='.$parent_label;
        }

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restGizi->get('allow/get-cara-bayar' . $params);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value)
                $result['output'][] = [
                    'id' => $value['carabayar_id'],
                    'name' => $value['carabayar_nama']
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

    public function actionFilterKamar()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];
        
        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restGizi->get('allow/get-kamar-ruangan?ruangan_id='.$parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value)
                $result['output'][] = [
                    'id' =>$value['kamarruangan_id'],
                    'name' => $value['kamarruangan_nokamar']
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

    public function actionFilterRuangan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $params = '';
        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
            $params = '?kamarruangan_id='.$parent_label;
        }

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restGizi->get('allow/get-ruangan' . $params);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value)
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

    /**
     * @todo Method untuk mendapatkan data diagnosa berdasarkan versi tabular list
     * @author Rizal Faidin <rizal@docotel.com>
     */
    public function actionGetDiagnosa($q = '', $type = '', $all_text = 0)
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $result = [];
            $result['results'] = [];

            $request = $this->_restGizi->get('allow/get-diagnosa?q='.$q.'&type='.$type);
            $response = json_decode($request->getBody(), true);

            if ($all_text == 1) {
                foreach ($response['response'] as $value) {
                    $result['results'][] = [
                        'id' => $value['diagnosa_kode'].' - '.$value['diagnosa_nama'],
                        'text' => $value['diagnosa_kode'].' - '.$value['diagnosa_nama']
                    ];
                }
            } else{
                foreach ($response['response'] as $value) {
                    $result['results'][] = [
                        'id' => $value['diagnosa_id'],
                        'text' => $value['diagnosa_kode'].' - '.$value['diagnosa_nama']
                    ];
                }
            }

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
