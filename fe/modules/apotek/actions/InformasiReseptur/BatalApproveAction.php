<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\InformasiReseptur;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class BatalApproveAction extends Action {
    public function run($no_resep) {
        try{            
            $_curl = Yii::$app->docoRest->apotek->post('inf-reseptur/batal-approve',[
                'form_params' => [
                    'no_resep' => DocoHelpers::decrypt($no_resep)
                ]
            ]);
            $_decodeResponse = json_decode($_curl->getBody(),TRUE);
            $meta = ArrayHelper::getValue($_decodeResponse, 'meta');
            if(!empty($meta)) {
                $_decodeResponse['meta'] = [];
            }
            return DocoHelpers::response($_decodeResponse);
        }catch(RequestException $e){
            $response = json_decode($e->getResponse()->getBody(),true);
            return DocoHelpers::response($response);
        }catch(\Exception $e){
            return DocoHelpers::response(['message'=>$e->getMessage()],500);
        };
    }
}