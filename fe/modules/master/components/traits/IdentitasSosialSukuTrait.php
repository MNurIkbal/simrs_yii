<?php
/**
 * @Author: iqbal@docotel.com
 * @Date:   2018-10-05 11:47:03
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-02-20 15:02:31
 */
namespace app\modules\master\components\traits;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

use app\modules\master\models\SukuForm;

trait IdentitasSosialSukuTrait 
{
    public function actionSuku()
    {
        $status = [1 => 'Aktif', 0 => 'Tidak Aktif'];
        return $this->renderPartial('suku/index',get_defined_vars());
    }

    public function getDataSuku($dataTable)
    {
        $no = 0;
        $data = [];
        foreach ($dataTable as $key => $value) {
            $no++;
            $primaryKey = DocoHelpers::encrypt($value['suku_id']);
            $value['primary'] = $primaryKey;
            unset($value['suku_id']);

            $value['status'] = ($value['is_active']) ? 'Aktif' : 'Tidak Aktif' ;
            $value['rowNum'] = $no;
            $data[$key] = $value;
        }
        return $data;
    }

    public function actionCreateSuku()
    {
        try {
            $title = Yii::t('fe', 'Tambah Suku');
            $model = new SukuForm;

            $status = $this->_status;
            $request = Yii::$app->request;
            $formName = substr(strrchr(get_class($model), "\\"), 1);

            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'identitas-sosial/create-suku',[
                                        'form_params' => $model->attributes
                                ]);

                    $body = json_decode($response->getBody(),true);
                    return DocoHelpers::response($body);
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'SukuForm');
                    return DocoHelpers::response([
                            'response' => [
                                'data' => $errors
                            ]
                        ],422);
                }
            } else {
                $model->is_active = 1;
                return $this->renderAjax('suku/form',get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
    
    public function actionEditSuku($id = null)
    {
        $request = Yii::$app->request;
        $title = Yii::t('fe', 'Ubah Suku');
        $model = new SukuForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);
        $status = $this->_status;
       
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->put('identitas-sosial/update-suku?id='.$id, [
                        'form_params' => $model->attributes
                    ]);
                    $body = json_decode($response->getBody(),true);
                    return DocoHelpers::response($body);
                } catch (RequestException $e) {
                    return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
                } catch (\Exception $e) {
                    return DocoHelpers::responseTemplate(500, $e->getMessage());
                }
            } else {
                $errors = DocoHelpers::parseError($model->errors, $formName);
                return DocoHelpers::responseTemplate(422, 'Error', $errors);
            }
        } else {
            $response = $this->_restMaster->get('identitas-sosial/view-suku?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $model->attributes = $attributes;
            $model->is_active = ($model->is_active == false) ? 0  : 1;            

            return $this->renderAjax('suku/form',get_defined_vars());
        }
    }
    
    public function actionDeleteSuku($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restMaster->delete('identitas-sosial/delete-suku?id='.$id);
            return DocoHelpers::responseTemplate(
            200, 'OK', [], [
                                'title' => Yii::t('fe', 'Sukses'), 
                                'text' => 'Data Berhasil Dihapus',
                                'message' => 'Data Berhasil Dihapus',
                          ]);
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(), 
                json_decode($e->getResponse()->getBody()->getContents())->message, [
            ]);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }
   
    public function actionExportPdfSuku()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/identitas-sosial-suku.pdf";
        try {
            $response = $this->_restMaster->get('identitas-sosial/export-pdf-suku?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
    
    public function actionExportExcelSuku()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/Master - identitas sosial - Suku.xlsx";
        try {
            $response = $this->_restMaster->get('identitas-sosial/export-excel-suku?'.http_build_query($yiiRestfulParams), [
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            $url = $body['response'];
           
            return DocoHelpers::downloadFile($path, true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
}