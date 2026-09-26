<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\gudang\actions\migrasi;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\web\UploadedFile;
use Doco\gudang\models\ImportAdjustmentForm;
use yii\helpers\ArrayHelper;

class AdjustmentObatAlkesAction extends Action {
    public function run() {
        if(Yii::$app->request->isPost){
            $model->load(Yii::$app->request->post());
            $model->attachment = UploadedFile::getInstance($model, 'attachment');
            if(!$model->upload()){
                throw new \Exception("Upload Fail", 1);
            }
            $response = Yii::$app->docoRest->gudang->post('migrasi/adjusment-obat-alkes', [
                'form_params' => [
                    'attachment' => json_encode($model->attachment)
                ]
            ]);
            $result = json_decode($response->getBody(), true);

            return DocoHelpers::response($result);
        }
        $title = 'Migrasi Adjustment Obat Alkes';
        $model = new ImportAdjustmentForm;

        $_restGudang = Yii::$app->docoRest->gudang;
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');

        $model->ruangan_id = $ruangan_id;
        try {
            $response = $_restGudang->get('allow/get-ruangan');
            $body = json_decode($response->getBody(), true);

            $ruangan = isset($body['response']['data']) ?
                ArrayHelper::map($body['response']['data'], 'ruangan_id', 'ruangan_nama') :
                [];

            return $this->controller->render('adjusment-obat-alkes',get_defined_vars());

        } catch (\Exception $e) {
            echo json_encode($e->getMessage()); die;

        }
    }
}