<?php
/**
 * @Author: iqbal@docotel.com
 * @Date:   2018-10-05 11:47:03
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-02-20 14:50:27
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

use app\modules\master\models\PekerjaanForm;

trait IdentitasSosialPekerjaanTrait 
{
    public function actionPekerjaan()
    {
        $status = [1 => 'Aktif', 0 => 'Tidak Aktif'];
        return $this->renderPartial('pekerjaan/index',get_defined_vars());
    }

    public function getDataPekerjaan($dataTable)
    {
        $no = 0;
        $data = [];
        foreach ($dataTable as $key => $value) {
            $no++;
            $primaryKey = DocoHelpers::encrypt($value['pekerjaan_id']);
            $value['primary'] = $primaryKey;
            unset($value['pekerjaan_id']);

            $value['status'] = ($value['is_active']) ? 'Aktif' : 'Tidak Aktif' ;
            $value['rowNum'] = $no;
            $data[$key] = $value;
        }
        return $data;
    }

    public function actionCreatePekerjaan()
    {
        try {
            $title = Yii::t('fe', 'Tambah Pekerjaan');
            $model = new PekerjaanForm;

            $status = $this->_status;
            $request = Yii::$app->request;
            $formName = substr(strrchr(get_class($model), "\\"), 1);

            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'identitas-sosial/create-pekerjaan',[
                                        'form_params' => $model->attributes
                                ]);

                    $body = json_decode($response->getBody(),true);
                    return DocoHelpers::response($body);
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'PekerjaanForm');
                    return DocoHelpers::response([
                            'response' => [
                                'data' => $errors
                            ]
                        ],422);
                }
            } else {
                $model->is_active = 1;
                return $this->renderAjax('pekerjaan/form',get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionEditPekerjaan($id = null)
    {
        $request = Yii::$app->request;
        $title = Yii::t('fe', 'Ubah Pekerjaan');
        $model = new PekerjaanForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);
        $status = $this->_status;
       
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->put('identitas-sosial/update-pekerjaan?id='.$id, [
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
            $response = $this->_restMaster->get('identitas-sosial/view-pekerjaan?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $model->attributes = $attributes;
            $model->is_active = ($model->is_active == false) ? 0  : 1;
            
            return $this->renderAjax('pekerjaan/form',get_defined_vars());
        }
    }
    
    public function actionDeletePekerjaan($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restMaster->delete('identitas-sosial/delete-pekerjaan?id='.$id);
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

    public function actionExportPdfPekerjaan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/identitas-sosial-pekerjaan.pdf";
        try {
            $response = $this->_restMaster->get('identitas-sosial/export-pdf-pekerjaan?'.http_build_query($yiiRestfulParams),[
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

    public function actionExportExcelPekerjaan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/Master-identitas sosial-pekerjaan.xlsx";
        try {
            $response = $this->_restMaster->get('identitas-sosial/export-excel-pekerjaan?'.http_build_query($yiiRestfulParams), [
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