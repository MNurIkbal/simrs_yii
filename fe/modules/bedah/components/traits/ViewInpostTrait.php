<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-09-04 11:06:38
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-05 17:30:05
 */

namespace app\modules\bedah\components\traits;

use Yii;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\components\DocoHelpers;

use Doco\bedah\models\IntraOperasiForm;
use Doco\bedah\models\PostOperasiForm;

trait ViewInpostTrait
{
    public function actionIntraOperasiView($id){
        $model = new IntraOperasiForm;
        try {
            $response = $this->_restBedah->get('intra-operasi/inpost-view', 
                [
                    'query'=>['id'=>$id],
                ]);
            $body = json_decode($response->getBody(), true);
            $data = $body['response'];
        } catch (\Exception $e) {
            $data = [];
        } catch(\RequestException $e){
            $data = [];
        }
        return $this->renderAjax('detail-partial/inpost-view/_intraoperasiview',get_defined_vars());
    }
    public function actionPostOperasiView($id){
        $model = new PostOperasiForm;
        try {
            $response = $this->_restBedah->get('intra-operasi/inpost-view', 
                [
                    'query'=>['id'=>$id],
                ]);
            $body = json_decode($response->getBody(), true);
            $data = $body['response'];
        } catch (\Exception $e) {
            $data = [];
        } catch(\RequestException $e){
            $data = [];
        }
        return $this->renderAjax('detail-partial/inpost-view/_postoperasiview',get_defined_vars());
    }
}