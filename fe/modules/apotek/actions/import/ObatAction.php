<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\actions\import;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\web\UploadedFile;
use Doco\apotek\models\ImportObatForm;

class ObatAction extends Action {
    public function run() {
        $title = 'Migrasi Obat';
        $model = new ImportObatForm;
        if(Yii::$app->request->isPost){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $model->load(Yii::$app->request->post());
            $model->attachment = UploadedFile::getInstance($model, 'attachment');
            if(!$model->upload()){
                throw new \Exception("Upload Fail", 1);
            }
            $response = Yii::$app->docoRest->apotek->post('import/obat', [
                'form_params' => [
                    'attachment' => json_encode($model->attachment)
                ]
            ]);
            $result = json_decode($response->getBody(), true);

            return $result;
        }

        return $this->controller->render('obat',get_defined_vars());
    }
}