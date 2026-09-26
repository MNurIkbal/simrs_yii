<?php

/**
 * @Author: Aris Munandar
 */

namespace app\modules\gizi\components\traits;

use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;

use function GuzzleHttp\json_encode;
use GuzzleHttp\Exception\RequestException;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;

use app\modules\gizi\models\PagtForm;
use app\modules\gizi\models\PagtMonevForm;

trait PagtTrait
{
    /**
     * This function will return new form of pagt
     * 
     * @return Json
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionPagt()
    {
        $pendaftaran_id = $this->helper->decrypt(Yii::$app->request->get('id'));
        $data = $this->guzzleExec($this->_restGizi, [
            'url' => 'pagt',
            'payload' => [
                'query' => [
                    'pendaftaran_id' => $this->helper->decrypt(Yii::$app->request->get('id'))
                ]
            ]
        ]);

        $historyData = $this->guzzleExec($this->_restGizi, [
            'url' => 'pagt/get-history-monev',
            'payload' => [
                'query' => compact('pendaftaran_id')
            ]
        ]);
        
        $model = new PagtForm;
        $model->attributes = $data['pagt'];
        $data['pagt']['pendaftaran_id'] = Yii::$app->request->get('id');
        $modelMonev = new PagtMonevForm;
        $arrayConfig = $this->getConfig('pagt');
        // return $this->renderAjax('asesmen-keperawatan/__form', compact('model', 'arrayConfig', 'modelResiko', 'data', 'pendaftaran_id'));
        return $this->renderAjax('pagt/__form', compact('model', 'modelMonev', 'data', 'arrayConfig', 'pendaftaran_id', 'historyData'));
    }

    /**
     * This function will save data from view
     * 
     * @return Json
     * @author : Aris Munandar (aris.m@docotel.com)
     * A product of PT. Docotel Teknologi
     * Powered by Sirs
     */
    public function actionSavePagt()
    {
        $model = new PagtForm;
        $model->attributes = Yii::$app->request->post();
        if (!$model->validate()) {
            return $this->responseJson(422, 'Silakan cek kembali inputan.', $this->mapErrorForm($model->errors, 'PagtForm'));
        } else {
            $arrayPayload = [];
            foreach ($model->attributes as $fieldName => $valueField) {
                $arrayPayload[$fieldName] = is_null($valueField) ? '' : DocoHelpers::purifyText($valueField);
            }
            return $this->guzzleExec($this->_restGizi, [
                'url' => 'pagt/save-pagt',
                'method' => 'POST',
                'payload' => [
                    'form_params' => [
                        'formdata' => array_merge($arrayPayload, [
                            'pendaftaran_id' => $this->helper->decrypt($model->pendaftaran_id),
                            'pagt_monev' => Yii::$app->request->post('pagt_monev')
                        ])
                    ]
                ],
                'returnResponse' => true
            ]);
        }
    }

}
