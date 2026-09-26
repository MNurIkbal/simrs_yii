<?php

namespace app\modules\ranap\components\traits;

use Yii;
use app\components\DocoHelpers;
use yii\web\UploadedFile;
use app\modules\ranap\models\DokumenPasienForm;
use app\components\services\UplodDokumenService;
use app\components\DocoConstants;
use yii\web\Response;
use app\components\DocoDatatableHelper;
use yii\helpers\ArrayHelper;

trait UploadDokumenViewTrait
{
	public function actionTabUploadDokumen($id, $pasienadmisi_id)
	{
		$model = new DokumenPasienForm;
        $data = Yii::$app->docoRest->rm->get('allow/data-dokumen?pendaftaran_id='.$this->_data_pasien['pendaftaran_id']);
        $data = json_decode($data->getBody(), true);
        $dokumen = [];
        if(!empty($data['response']['dokumen'])){
            $dokumen = $data['response']['dokumen'];
        }
        $data = $data['response']['items'];

		if(Yii::$app->request->isPost){
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
            $model->load(Yii::$app->request->post());
            // $model->attachment = UploadedFile::getInstance($model, 'attachment');
            $File = UploadedFile::getInstance($model, 'attachment');
            $path = '/uploads/dokumenpasien/'.$this->_data_pasien['no_rekam_medik'].'/'.$this->_data_pasien['no_pendaftaran'].'/';
            
            $req = Yii::$app->request->post();
            $File = (new UplodDokumenService)->uploadFile($File, $path, $req['DokumenPasienForm']['dokumen_id'],$this->_data_pasien['no_pendaftaran']);

            if($File['status'] == true){
                $model->filename = $File['file_name'];
            }else{
                $result['response']['title'] = $File['title'];
                $result['response']['text'] = $File['text'];
                return DocoHelpers::response($result, 422);
            }
            $model->pasienadmisi_id = $this->_data_pasien['pasienadmisi_id'];
            $model->pendaftaran_id = $this->_data_pasien['pendaftaran_id'];
            $model->dokumen_id = $req['DokumenPasienForm']['dokumen_id'];
            $model->path = $path;

            
            // if(!$model->upload()){
            //     throw new \Exception("Upload Fail", 1);
            // }
            try {
                $response = (new UplodDokumenService)->uploadDokumen($model->attributes);
                return $response;

            } catch (RequestException $e) {
                return DocoHelpers::response(['message' => $e->getMessage()],500);
            } catch (\Exception $e) {
                return DocoHelpers::response(['message' => $e->getMessage()],500);
            }

            
        }
		return $this->renderAjax('upload-dokumen/index',[
			'model' => $model,
            'data' => $data,
            'dokumen' => $dokumen,
            'pendaftaran_id' => $this->_data_pasien['pendaftaran_id']
		]);
	}

    public function actionGetDokumenList(){
        Yii::$app->response->format = Response::FORMAT_JSON;
        $payload = array_merge(DocoDatatableHelper::advancedFilterParam(), [
            'pendaftaran_id' => $this->helper->decrypt( Yii::$app->request->get('id') )
        ]);

        $result = (new UplodDokumenService)->getDokumenList($payload);
        return $result;

    }

    public function actionDeleteUpload($pendaftaran_id,$parent)
    {
        try {
            $result = (new UplodDokumenService)->deleteUpload($pendaftaran_id,$parent);
            return DocoHelpers::response($result);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
}