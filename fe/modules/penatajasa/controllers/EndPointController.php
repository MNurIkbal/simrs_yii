<?php

namespace Doco\penatajasa\controllers;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoSelect2Trait;
use app\models\LoginForm;

class EndPointController extends DocoController{

    use DocoSelect2Trait;

    protected $_restPenataJasa;

    public function init()
    {
        $this->_restPenataJasa = Yii::$app->docoRest->penatajasa;
    }

    public function beforeAction($action)
    {
        return true;
    }

    public function actionGetPenjamin($selected = null)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $penjamin = [];
        $carabayar_id = isset($post['depdrop_parents'][0]) ? $post['depdrop_parents'][0] : null;
        try{
            $rest = $this->_restPenataJasa->get('allow/get-penjamin', [
                'query' => [
                    'carabayar_id' => $carabayar_id
                ]
            ]);
            $result = json_decode($rest->getBody(), true);
            if(!is_null($result)){
                foreach ($result['response'] as $key => $value) {
                    $penjamin[] = [
                        'id' => $key,
                        'name' => $value
                    ];
                }
            }
        } catch(\Exception $e){
            $penjamin = [];
        } catch(\RequestException $e){
            $penjamin = [];
        }
        $data = [ 'output' => $penjamin, 'selected' => $selected ];
        return DocoHelpers::response($data);
    }

    public function actionGetSatuan($selected = null)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $listSatuan = [];
        $obatalkes_id = isset($post['depdrop_parents'][0]) ? $post['depdrop_parents'][0] : null;

        try{
            $rest = $this->_restPenataJasa->get('allow/get-satuan', [
                'query' => [
                    'obatalkes_id' => $obatalkes_id
                ]
            ]);
            $result = json_decode($rest->getBody(), true);
            
            if(!is_null($result)){
                foreach ($result['response'] as $key => $value) {
                    if((int)$value['nilai_konversi'] == 1){
                        $listSatuan[] = [
                            'id' => $value['satuanbesar_id'],
                            'name' => $value['satuan_besar'],
                            'satuankecil_id' => $value['satuankecil_id'],
                        ];
                        break;
                    }                    
                }
            }
        } catch(\Exception $e){
            $listSatuan = [];
        } catch(\RequestException $e){
            $listSatuan = [];
        }

        if(!empty($listSatuan)) {
            $selected = $listSatuan[0]['satuankecil_id'];
        }
        $data = [ 'output' => $listSatuan, 'selected' => $selected];
        return DocoHelpers::response($data);
    }

    public function actionGetSatuanBmhp($selected = null)
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $listSatuan = [];
        $obatalkes_id = isset($post['res']) ? (int)$post['res'] : null;

        if($obatalkes_id){
            try{
                $rest = $this->_restPenataJasa->get('allow/get-satuan', [
                    'query' => [
                        'obatalkes_id' => $obatalkes_id
                    ]
                ]);
                $result = json_decode($rest->getBody(), true);
                if(!is_null($result)){
                    foreach ($result['response'] as $key => $value) {
                        if((int)$value['nilai_konversi'] == 1){
                            $listSatuan[] = [
                                'id' => $value['satuanbesar_id'],
                                'name' => $value['satuan_besar'],
                                'satuankecil_id' => $value['satuankecil_id'],
                            ];
                            break;
                        }                    
                    }
                }
            } catch(\Exception $e){
                $listSatuan = [];
            } catch(\RequestException $e){
                $listSatuan = [];
            }
            if(!empty($listSatuan)) {
                $selected = $listSatuan[0]['satuankecil_id'];
            }
            return DocoHelpers::response($listSatuan[0]);
        }
    }

    public function actionGetNilaiKonversi($satuanbesar_id, $obatalkes_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        try {
            $response = $this->_restPenataJasa->request('GET', 'allow/get-nilai-konversi?satuanbesar_id=' . $satuanbesar_id. '&obatalkes_id='.$obatalkes_id);
            $body = json_decode($response->getBody(),TRUE);
            return DocoHelpers::response($body['response']);
        } catch (RequestException $e) {
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionCheckAuthorization()
    {
        $urlRef = Yii::$app->request->referrer;
        $pattern = preg_replace("/(http[s]?:\/\/)?([^\/\s]+)(.*)/",'$3',$urlRef);
        $pattern2 = preg_replace("/(\/(?:.(?!\/))+$)/",'',$pattern);

        $request = Yii::$app->request;
        $akses = $request->post('akses');
        $modul = Yii::$app->docoRest->modul_id;
        if ($response = $this->getAuthorization()) {
            $menus = isset($response['response']['menus']) ? $response['response']['menus'] : [];
            $uid = isset($response['response']['uid']) ? $response['response']['uid'] : null;
            return DocoHelpers::response([
                'response' => [
                    'message' => true,
                    'verify_uid' => $uid
                ]]);
        } else {
            return DocoHelpers::response([
                'response' => [
                    'text' => 'user/password tidak valid.'
                ]
            ], 422);
        }
    }
    
    private function getAuthorization()
    {
        $request = Yii::$app->request;
        $username = $request->post('nama_pemakai');
        $password = $request->post('katakunci_pemakai');
        $modul_id = $request->post('modul_id');
        $client = Yii::$app->docoRest->dcms;
        try {
            $response = $client->post('auth/check-authorization', [
                'form_params' => [
                    'username' => $username, 
                    'password' => $password,
                    'modul_id' => $modul_id
                ],
            ]);
            $responseBody = json_decode($response->getBody(), true);
            if ($responseBody['metadata']['status'] == 422) {
                return false;
            }
        } catch (RequestException $e) {
            return false;
        }

        return $responseBody;
    }
}