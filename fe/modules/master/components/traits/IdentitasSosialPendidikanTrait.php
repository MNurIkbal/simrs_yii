<?php
/**
 * @Author: iqbal@docotel.com
 * @Date:   2018-10-05 11:47:03
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-02-20 14:56:36
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

use app\modules\master\models\PendidikanForm;
use app\modules\master\models\Indexing;

trait IdentitasSosialPendidikanTrait 
{
    public function actionPendidikan()
    {
        $getDataIndexing = $this->_restMaster->get('allow/list-indexing', 
                    ['form_params'=>[]
                    ]);
        $bodyIndex = json_decode($getDataIndexing->getBody(), true);
        $dataIndexing = $bodyIndex['response'];
        
        // $status = $this->_status;
        $status = [1 => 'Aktif', 0 => 'Tidak Aktif'];
        return $this->renderPartial('pendidikan/index',get_defined_vars());
    }

    public function actionCreatePendidikan()
    {
        try {
            $title = Yii::t('fe', 'Tambah Pendidikan');
            $model = new PendidikanForm;

            $status = $this->_status;
            $request = Yii::$app->request;
            $formName = substr(strrchr(get_class($model), "\\"), 1);

            $indexingRequest = $this->_restMaster->get('allow/list-indexing');
            $body = json_decode($indexingRequest->getBody(),TRUE);
            $ddl_indexing = $body['response'];

            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'identitas-sosial/create-pendidikan',[
                                        'form_params' => $model->attributes
                                ]);

                    $body = json_decode($response->getBody(),true);
                    return DocoHelpers::response($body);
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'PendidikanForm');
                    return DocoHelpers::response([
                            'response' => [
                                'data' => $errors
                            ]
                        ],422);
                }
            } else {
                $model->is_active = 1;
                return $this->renderAjax('pendidikan/form',get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionEditPendidikan($id = null)
    {
        $request = Yii::$app->request;
        $title = Yii::t('fe', 'Ubah Pendidikan');
        $model = new PendidikanForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);

        $status = $this->_status;

        $indexingRequest = $this->_restMaster->get('allow/list-indexing');
        $body = json_decode($indexingRequest->getBody(),TRUE);
        $ddl_indexing = $body['response'];
        
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->put('identitas-sosial/update-pendidikan?id='.$id, [
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
            $response = $this->_restMaster->get('identitas-sosial/view-pendidikan?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $model->attributes = $attributes;
            $model->is_active = ($model->is_active == false) ? 0  : 1;
            
            return $this->renderAjax('pendidikan/form',get_defined_vars());
        }
    }


    public function getDataPendidikan($dataTable)
    {
        $data = [];
        $no = 0;
        foreach ($dataTable as $key => $value) {
            $no++;
            $primaryKey = DocoHelpers::encrypt($value['pendidikan_id']);
            $value['primary'] = $primaryKey;
            unset($value['pendidikan_id']);

            // $value['status'] = DocoHelpers::isActive($value['is_active']);
            $value['status'] = ($value['is_active']) ? 'Aktif' : 'Tidak Aktif' ;
            $value['indexing_nama'] = $value['indexing']['indexing_nama'];
            $value['rowNum'] = $no;
            $data[$key] = $value;
        }
        return $data;
    }

    public function actionDeletePendidikan($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restMaster->get('identitas-sosial/delete-pendidikan?id='.$id);
            return DocoHelpers::responseTemplate(
            200, 'OK', [], [
                'title' => Yii::t('fe', 'Sukses'), 
                'text' => 'Data Berhasil Dihapus',
                'message' => 'Data Berhasil Dihapus',
            ]);
        } catch (RequestException $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionExportPdfPendidikan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/identitas-sosial-pendidikan.pdf";
        try {
            $response = $this->_restMaster->get('identitas-sosial/export-pdf-pendidikan?'.http_build_query($yiiRestfulParams),[
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

    public function actionExportExcelPendidikan()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/Master - identitas sosial - Pendidikan.xlsx";
        try {
            $response = $this->_restMaster->get('identitas-sosial/export-excel-pendidikan?'.http_build_query($yiiRestfulParams), [
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