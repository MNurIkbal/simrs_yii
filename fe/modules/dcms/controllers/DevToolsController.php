<?php

namespace Doco\dcms\controllers;

use Yii;
use yii\base\DynamicModel;
use app\components\DocoController;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;

class DevToolsController extends DocoController
{
    public function actionIndex()
    {
        return $this->render('index',get_defined_vars());
    }

    private function processPost($cid)
    {
        return call_user_func(array($this, $cid));
    }

    private function getCompactData($_cid)
    {
        switch ($_cid) {
            case '_pindahbilling':
                $model = new DynamicModel();
                $model->defineAttribute('pendaftaran_sumber');
                $model->defineAttribute('pendaftaran_tujuan');
                $model->addRule(['pendaftaran_sumber','pendaftaran_tujuan'],'safe');
                $model->addRule(['pendaftaran_sumber','pendaftaran_tujuan'],'required');
                return compact('model');
                break;
            case '_resendorderlis':
                $model = new DynamicModel();
                $model->defineAttribute('reference');
                $model->addRule(['reference'],'safe');
                $model->addRule(['reference'],'required');
                return compact('model');
                break;
            
            default:
                return get_defined_vars();
                break;
        }
    }

    public function actionRenderView($_cid)
    {
        if(Yii::$app->request->post()){
            return $this->processPost($_cid);
        }
        return $this->renderAjax('partial/'.$_cid,$this->getCompactData($_cid));
    }

    public function actionPindahBilling()
    { 
        $model = new DynamicModel();
        $model->defineAttribute('pendaftaran_sumber');
        $model->defineAttribute('pendaftaran_tujuan');
        $model->addRule(['pendaftaran_sumber','pendaftaran_tujuan'],'safe');
        $model->addRule(['pendaftaran_sumber','pendaftaran_tujuan'],'required');
        $model->load(Yii::$app->request->post());
        $rest = Yii::$app->docoRest->dcms->post('dev-tools/pindah-billing',[
            'json' => $model->attributes
        ]);
        $response = json_decode($rest->getBody(),true);
        return DocoHelpers::response($response);
    }

    public function actionResendOrderLis()
    {
        $pasienmasukpenunjang_id = Yii::$app->request->get('pasienmasukpenunjang_id',0);

        $request = Yii::$app->docoRest->lis->post('api-roche/sync-integerasi',['json'=>['pasienmasukpenunjang_id'=>$pasienmasukpenunjang_id]]);
        $response = json_decode($request->getBody(), true);

        return DocoHelpers::response($response);
    }

    public function actionGetPasienPenunjang($q = '',$page = null) {
        try {
            $limit = 10;
            $offset = ($page-1)*10;
            Yii::$app->response->format = Response::FORMAT_JSON;
            $result = [];
            $result['results'] = [];

            $request = Yii::$app->docoRest->dcms->get('dev-tools/get-pasien-penunjang',['query'=>['keyword'=>$q,'page'=>$page,'offset'=>$offset,'limit'=>$limit]]);
            $response = json_decode($request->getBody(), true);

            $list = $response['response'];
            if(is_array($list)){
                foreach ($list as $value) {
                    $result['results'][] = [
                        'id' => $value['pasienmasukpenunjang_id'],
                        'text' => $value['reference']
                    ];
                }
            }

            $result['pagination'] = [ 'more' => count($list)>1?true:false ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetRiwayatIntegrasiLis()
    {
        $pasienmasukpenunjang_id = Yii::$app->request->get('pasienmasukpenunjang_id',0);

        $request = Yii::$app->docoRest->dcms->get('dev-tools/get-riwayat-integrasi-lis',['query'=>['pasienmasukpenunjang_id'=>$pasienmasukpenunjang_id]]);
        $response = json_decode($request->getBody(), true);

        return DocoHelpers::response($response);
    }
    
}