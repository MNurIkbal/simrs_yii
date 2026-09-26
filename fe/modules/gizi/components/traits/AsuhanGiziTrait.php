<?php

/**
 * @Author: Rizal Faidin
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

use app\modules\gizi\models\AsuhanGiziForm;

trait AsuhanGiziTrait 
{
    public function actionAsuhanGizi()
    {
        try{
            $request = Yii::$app->request;
            $id = $request->get('id');
            $pendaftaran_id = DocoHelpers::decrypt($id);

            $req = $this->_restGizi->get('allow/get-bundle-asuhan?id=' . $pendaftaran_id);
            $response = json_decode($req->getBody(), true);
            $response = $response["response"];
            $lookup = $response['lookup'];
            $master = $response['master'];
            $dataAsesmen = $response['data_model'];

            if (isset($dataAsesmen['bb_biasanya']) && $dataAsesmen['bb_biasanya'] != '') {
                if(strpos($dataAsesmen['bb_biasanya'], '.') !== false) {
                    $dataAsesmen['bb_biasanya'] = str_replace('.', ',', $dataAsesmen['bb_biasanya']);
                } else {
                    $dataAsesmen['bb_biasanya'] = $dataAsesmen['bb_biasanya'].',00';
                }
            } else {
                $dataAsesmen['bb_biasanya'] = '00,00';
            }
            
            $model = new AsuhanGiziForm;
            $model->pendaftaran_id = $pendaftaran_id;
            return $this->renderAjax('asuhan-gizi/index', get_defined_vars());
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        }
    }

    public function actionSimpanAsuhanGizi()
    {
        try {
            $request = Yii::$app->request;
            $model = new AsuhanGiziForm;
            $model->load($request->post());
            $model->bb_biasanya = str_replace(',', '.', $model->bb_biasanya);
            $model->bb_saatini  = str_replace(',', '.', $model->bb_saatini);
            $model->imt  = str_replace(',', '.', $model->imt);
            if ($model->validate()) {

                $response = $this->_restGizi->post('asuhan-gizi/simpan-asuhan-gizi', [
                    'form_params' => $request->post()
                ]);
                $response = json_decode($response->getBody(), true);
                return DocoHelpers::response($response);
            } else {
                $formName = substr(strrchr(get_class($model), "\\"), 1);
                $response = $model->errors;
                return DocoHelpers::response($response, 422, $formName);
            }
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        }
    }

    public function actionCetakAsuhanGizi()
    {
        // Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/Asuhan Gizi.pdf";
        try {
            $asuhangizi_id = DocoHelpers::decrypt($request->get('asuhangizi_id'));
            $response = $this->_restGizi->get('asuhan-gizi/cetak-asuhan-gizi?',[
                'query' => ['asuhangizi_id'=>$asuhangizi_id],
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }
}