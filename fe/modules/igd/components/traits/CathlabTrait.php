<?php

/**
 * @Author: Aris Munandar
 */

namespace app\modules\igd\components\traits;

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

use app\modules\igd\models\CathlabForm;

trait CathlabTrait 
{
    public function actionCathlab($id,$tipe,$pasienadmisi_id=null) 
    {
        try{
            $pendaftaran_id = DocoHelpers::decrypt($id);

            $data = $this->guzzleExec($this->_restIgd, [
                'url' => 'cathlab',
                'payload' => [
                    'query' => [
                        'pendaftaran_id' => $pendaftaran_id,
                        'tipe'           => $tipe
                    ]
                ]
            ]);

            $linkcetak = Url::to(['cetak-cathlab-pdf', 'id'=>$id, 'tipe'=>$tipe, 'pasienadmisi_id'=>$pasienadmisi_id]);
            
            return $this->renderAjax('cathlab/index', [
                'model'         => new CathlabForm,
                'pendaftaranId' => $pendaftaran_id,
                'tipe'          => $tipe,
                'cathlabData'   => empty($data['data']) ? [] : $data['data'],
                'linkcetak'     => $linkcetak
            ]);
    
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        } catch (\Exception $e) {
            var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(400, Yii::t('fe', 'Terdapat kesalahan'));
        }
    }

    public function actionSaveCathlab()
    {
        $payload                   = Yii::$app->request->post();
        $pendaftaran_id            = Yii::$app->request->get('pendaftaran_id', null);
        $tipe                      = Yii::$app->request->get('tipe', null);
        $payload['pendaftaran_id'] = $pendaftaran_id;
        $payload['tipe']           = $tipe;
        
        $modelValidation = new CathlabForm;
        $modelValidation->attributes = $payload;
        if(!$modelValidation->validate()){
            return $this->helper->macroResponseJson(422, 'Silakan cek kembali inputan.', $this->helper->mapErrorForm($modelValidation->errors, 'CathlabForm'));
        } else {
            return $this->guzzleExec($this->_restIgd, [
                'url' => 'cathlab/save-cathlab',
                'returnResponse' => true,
                'method' => 'POST',
                'payload' => [
                    'form_params' => $payload
                ]
            ]);
        }
        
    }

    public function actionCetakCathlabPdf()
    {
        $params          = Yii::$app->request;
        $pendaftaran_id  = DocoHelpers::decrypt($params->get('id','MA'));
        $tipe            = $params->get('tipe', null);
        $pasienadmisi_id = $params->get('pasienadmisi_id', null);
        $ruangan_id      = Yii::$app->docoVars->workspace('ruangan_id');
        $nama_usercetak  = Yii::$app->session->get('user_identity')['nama'];
        $id_usercetak    = Yii::$app->session->get('user_identity')['id_pegawai'];
        $path            = Yii::getAlias("@download") . "/cathlab.pdf";

        $response       = $this->_restIgd->get('cathlab/cetak-cathlab-pdf',[
            'query' => [
                'pendaftaran_id'  => $pendaftaran_id,
                'tipe'            => $tipe,
                'pasienadmisi_id' => $pasienadmisi_id,
                'nama_usercetak'  => $nama_usercetak,
                'id_usercetak'    => $id_usercetak
            ],
            'save_to' => $path
        ]);
        $body = json_decode($response->getBody(), true);
        return DocoHelpers::previewPdf($path);
    }
    
}