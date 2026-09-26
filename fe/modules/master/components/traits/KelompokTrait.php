<?php

/**
 * @author Randy Vianda Putra
 * @todo Master Kategori
 * @copyright 26 April 2018 aweutist
 */

namespace app\modules\master\components\traits;

use Yii;
use yii\filters\AccessControl;
use GuzzleHttp\Exception\RequestException;
use yii\web\Response;
use yii\base\Exception;

use function GuzzleHttp\json_encode;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\KelompokTindakanForm;

trait KelompokTrait
{
    public function actionKelompok()
    {
        $title = Yii::t('fe', 'Master kategori');
        $status = ['1'=>Yii::t('fe', 'Aktif'), '0'=>Yii::t('fe','Tidak aktif')];
        return $this->renderAjax('components/kelompok/index', get_defined_vars());
    }

    public function actionGetDataKelompok()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restMaster->get('kelompok-tindakan/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['kelompoktindakan_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['status'] = ($value['is_active']) ? Yii::t('fe','Aktif') : Yii::t('fe','Tidak aktif');

                $value['kelompoktindakan_persencyto'] = ($value['kelompoktindakan_persencyto']) ? $value['kelompoktindakan_persencyto'] : 0;
                $value['kelompoktindakan_persendiskon'] = ($value['kelompoktindakan_persendiskon']) ? $value['kelompoktindakan_persendiskon'] : 0;
                $value['detail'] = Html::button("<i class='fa fa-plus-square-o'></i>", [
                    'class' => 'btn btn-sm btn-success', 'data-source'=>"/master/tindakan/detail-tindakan-kelompok?id=".$primaryKey,'onclick'=> 'docoHelper.detail(this)']);
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionCreateKelompok()
    {
        $title = 'Tambah Kelompok';
        try {
            $model = new KelompokTindakanForm;
            $request = Yii::$app->request;
            $post = $request->post();
            if ($post) {
                $formName = substr(strrchr(get_class($model), "\\"), 1);
                $model->attributes = $post['KelompokTindakanForm'];
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'kelompok-tindakan/create',[
                        'form_params' => $post,
                    ]);
                    $response = json_decode($response->getBody(), true);
                    return DocoHelpers::response($response);
                } else {
                    $response = $model->errors;
                    return DocoHelpers::response($response, 422, $formName);
                }
            } else {
                $model->is_active = 1;
                $action = '/master/tindakan/create-kelompok';
                $model->kelompoktindakan_persencyto = 0;
                $model->kelompoktindakan_persendiskon = 0;
                return $this->renderAjax('components/kelompok/form', get_defined_vars());
            }
            
        } catch (Exception $e) {
            $response = [ 
                'response' => [
                    'message'=>$e->getMessage(),
                ]
            ];
            return DocoHelpers::response($response, 500);
        } catch (RequestException $e) {
            $response = [ 
                'response' => [
                    'message'=>$e->getMessage(),
                ]
            ];
            return DocoHelpers::response($response, 500);
        } 
        
    }

    public function actionUpdateKelompok($id)
    {
        $title = 'Ubah Kelompok';
        try {
            $id = DocoHelpers::decrypt($id);
            $model = new KelompokTindakanForm;
            $request = Yii::$app->request;
            $post = $request->post();
            if ($post) {
                $formName = substr(strrchr(get_class($model), "\\"), 1);
                $model->load($post);
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'kelompok-tindakan/update?id='.$_GET['id'], [
                        'form_params' => $post,
                    ]);
                    $response = json_decode($response->getBody(), true);
                    return DocoHelpers::response($response);
                } else {
                    $response = $model->errors;
                    return DocoHelpers::response($response, 422, $formName);
                }
            } else {
                $request = $this->_restMaster->request('GET', 'kelompok-tindakan/view?id='.$id);
                $response = json_decode($request->getBody(), true);
                $attributes = $response['response'];
                $model->attributes = $attributes;
                $action = '/master/tindakan/update-kelompok?id='.$id;
                return $this->renderAjax('components/kelompok/form', get_defined_vars());
            }
        } catch (Exception $e) {
            $response = [ 
                'response' => [
                    'message'=>$e->getMessage(),
                ]
            ];
            return DocoHelpers::response($response, 500);
        } catch (RequestException $e) {
            $response = [ 
                'response' => [
                    'message'=>$e->getMessage(),
                ]
            ];
            return DocoHelpers::response($response, 500);
        }
        
    }

    public function actionDeleteKelompok($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restMaster->POST('kelompok-tindakan/delete', [
                'query'=>['id'=>$id]]);

            $response = json_decode($response->getBody(), true);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];

            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionExportExcelKelompok()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $url = 'kelompok-tindakan/export-excel?'.http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/Master - Tindakan - Kelompok Tindakan.xlsx";
        try {
            $response = $this->_restMaster->get($url,[
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::downloadFile($path, true);
        } catch (RequestException $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportPdfKelompok()
    {
        $request = Yii::$app->request;
        try {
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/kelompok-tindakan.pdf";
            $response = $this->_restMaster->get('kelompok-tindakan/export-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
            // return DocoHelpers::downloadPdf($response,$path);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }

    public function actionDetailTindakanKelompok($id)
    {
        $request = $this->_restMaster->request('GET', 'kelompok-tindakan/view?id='.DocoHelpers::decrypt($id));
        $response = json_decode($request->getBody(), true);
        $attributes = $response['response'];

        return $this->renderAjax('components/kelompok/_detail', get_defined_vars());
    }

    public function actionDataDetailTindakanKelompok()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $id = DocoHelpers::decrypt($request->get('id'));
            $response = $this->_restMaster->get('kelompok-tindakan/detail-tindakan-kelompok?id='.
                $id.'&'.http_build_query($yiiRestfulParams), ['form_params' => []]);

            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['daftartindakan_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $data[$key] = $value;
            }

            $result['data'] = $data;
            $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
            $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }
    
}

    
