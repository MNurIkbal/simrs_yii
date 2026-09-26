<?php
/**
 * @Author: iqbal@docotel.com
 * @Date:   2018-10-05 11:47:03
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-02-20 14:54:48
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

use app\modules\master\models\PendidikanKualifikasiForm;

trait IdentitasSosialPendidikanKualifikasiTrait 
{
    public function actionPendidikanKualifikasi()
    {
        $pendidikanRequest = $this->_restMaster->get('allow/list-pendidikan');
        $body = json_decode($pendidikanRequest->getBody(),TRUE);
        $ddl_pendidikan = $body['response'];

        $kelompokpegawaiRequest = $this->_restMaster->get('allow/list-kelompokpegawai');
        $body = json_decode($kelompokpegawaiRequest->getBody(),TRUE);
        $ddl_pegawai = $body['response'];

        $status = [1 => 'Aktif', 0 => 'Tidak Aktif'];
        return $this->renderPartial('pendidikan-kualifikasi/index',get_defined_vars());
    }

    public function getDataPendidikanKualifikasi($dataTable)
    {
        $no = 0;
        $data = [];
        foreach ($dataTable as $key => $value) {
            $no++;
            $primaryKey = DocoHelpers::encrypt($value['pendkualifikasi_id']);
            $value['primary'] = $primaryKey;
            unset($value['pendkualifikasi_id']);

            $value['pendidikan_nama'] = $value['pendidikan']['pendidikan_nama'] ;
            $value['kelompokpegawai_id'] = $value['kelompokpegawai']['kelompokpegawai_nama'] ;
            $value['status'] = ($value['is_active']) ? 'Aktif' : 'Tidak Aktif' ;
            $value['rowNum'] = $no;
            $data[$key] = $value;
        }
        return $data;
    }
    
    public function actionCreatePendidikanKualifikasi()
    {
        try {
            $title = Yii::t('fe', 'Tambah Pendidikan Kualifikasi');
            $model = new PendidikanKualifikasiForm;

            $status = $this->_status;
            $request = Yii::$app->request;
            $formName = substr(strrchr(get_class($model), "\\"), 1);

            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'identitas-sosial/create-pendidikan-kualifikasi',[
                                        'form_params' => $model->attributes
                                ]);

                    $body = json_decode($response->getBody(),true);
                    return DocoHelpers::response($body);
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'PendidikanKualifikasiForm');
                    return DocoHelpers::response([
                            'response' => [
                                'data' => $errors
                            ]
                        ],422);
                }
            } else {
                $pendidikanRequest = $this->_restMaster->get('allow/list-pendidikan');
                $body = json_decode($pendidikanRequest->getBody(),TRUE);
                $ddl_pendidikan = $body['response'];

                $kelompokpegawaiRequest = $this->_restMaster->get('allow/list-kelompokpegawai');
                $body = json_decode($kelompokpegawaiRequest->getBody(),TRUE);
                $ddl_pegawai = $body['response'];

                $model->is_active = 1;
                return $this->renderAjax('pendidikan-kualifikasi/form',get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }
    
    public function actionEditPendidikanKualifikasi($id = null)
    {
        $request = Yii::$app->request;
        $title = Yii::t('fe', 'Ubah Pendidikan Kualifikasi');
        $model = new PendidikanKualifikasiForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);
        $status = $this->_status;
        if ($request->post()) {
            $model->load($request->post());
            // echo "<pre>";var_dump($model->attributes);die();
            if ($model->validate()) {
                try {
                    $response = $this->_restMaster->put('identitas-sosial/update-pendidikan-kualifikasi?id='.$id, [
                        'form_params' => $model->attributes
                    ]);
                    $body = json_decode($response->getBody(),true);
                    // return json_encode($body);
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
            $response = $this->_restMaster->get('identitas-sosial/view-pendidikan-kualifikasi?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];

            $pendidikanRequest = $this->_restMaster->get('allow/list-pendidikan');
            $body = json_decode($pendidikanRequest->getBody(),TRUE);
            $ddl_pendidikan = $body['response'];

            $kelompokpegawaiRequest = $this->_restMaster->get('allow/list-kelompokpegawai');
            $body = json_decode($kelompokpegawaiRequest->getBody(),TRUE);
            $ddl_pegawai = $body['response'];


            $model->attributes = $attributes;
            $model->is_active = ($model->is_active == false) ? 0  : 1;            

            return $this->renderAjax('pendidikan-kualifikasi/form',get_defined_vars());
        }
    }
    
    public function actionDeletePendidikanKualifikasi($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restMaster->delete('identitas-sosial/delete-pendidikan-kualifikasi?id='.$id);
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
    
    public function actionExportPdfPendidikanKualifikasi()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/identitas-sosial-pendidikan-kualifikasi.pdf";
        try {
            $response = $this->_restMaster->get('identitas-sosial/export-pdf-pendidikan-kualifikasi?'.http_build_query($yiiRestfulParams),[
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
    
    public function actionExportExcelPendidikanKualifikasi()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/Master-identitas sosial-pendidikan kualifikasi.xlsx";
        try {
            $response = $this->_restMaster->get('identitas-sosial/export-excel-pendidikan-kualifikasi?'.http_build_query($yiiRestfulParams), [
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