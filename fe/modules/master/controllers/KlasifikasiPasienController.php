<?php
/**
 * @author: arief saputra
 * @description: master Klasifikasi PAsien
**/

namespace Doco\master\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\master\models\KlasifikasipasienForm;
use GuzzleHttp\Exception\RequestException;

class KlasifikasiPasienController extends DocoController
{
    protected $_title = 'Klasifikasi Pasien';
    protected $_module = 'klasifikasipasien/';
    protected $_restMaster;

    public function init()
    {
        parent::init();
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        unset($behaviors['access']);
        unset($behaviors['verbs']);
        return $behaviors;
    }

    public function actionIndex()
    {
        $status = $this->_status;
        $title = Yii::t('fe', $this->_title);
        $dataDropdown = [1 => 'Aktif', 0 => 'Tidak Aktif'];

        return $this->render('index', get_defined_vars());
    }

    // public function actionPengambilanAntrian()
    // {
    //     $status = $this->_status;
    //     $title = Yii::t('fe', 'Master Pengambilan Antrian');

    //     return $this->render('pengambilan-antrian', get_defined_vars());
    // }

    public function actionGetData()
    {
        try {
            Yii::$app->response->format = Response::FORMAT_JSON;
            $request = Yii::$app->request;

            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

            if (isset($yiiRestfulParams)) {
                unset($yiiRestfulParams['order']);
            }

            $response = $this->_restMaster->post('klasifikasipasien/index?'. http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(),TRUE);
            $row = [];
            $no = $request->post('start',0);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['klasifikasipasien_id']);
                $value['primary'] = $primaryKey;

                unset($value['klasifikasipasien_id']);

                $value['rowNum'] = $no;
                $value['is_active'] = ($value['is_active']) ? 'Aktif' : 'Tidak Aktif';
                $row[$key] = $value;
            }
            $return = [
                'data' => $row,
                'draw' => $request->post('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount']
            ];
            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        } catch (\Exception $e) {
            return DocoHelpers::dataTabelsException($e->getMessage());
        }
    }

    public function actionView($id)
    {
        $request = Yii::$app->request;
        $title = 'Lihat Data';
        $model = new KlasifikasipasienForm;
        $id = DocoHelpers::decrypt($id);

        $response = $this->_restMaster->get('klasifikasipasien/view?id='.$id);
        $body = json_decode($response->getBody(), TRUE);
        $attributes = $body['response'];
        $model->attributes = $attributes;
        return $this->renderPartial('view', get_defined_vars());
    }

    public function actionCreate()
    {
        try {
            $title = 'Tambah Klasifikasi pasien';
            $model = new KlasifikasipasienForm;
            $status = $this->_status;
            $request = Yii::$app->request;
            $options = ['status'=>1];

            if ($request->post()) {
                //die('masuk sini if');
                $model->load($request->post());
                if ($model->validate()) {
                    $response = $this->_restMaster->request('POST', 'klasifikasipasien/create',[
                                        'form_params' => $model->attributes
                                ]);
                    $response = json_decode($response->getBody(),true);
                    return DocoHelpers::response($response,false,'KlasifikasipasienForm');
                } else {
                    $errors = DocoHelpers::parseError($model->errors,'KlasifikasipasienForm');
                    return DocoHelpers::response([
                            'response' => [
                                'data' => $errors
                            ]
                        ],422);
                }
            } else {
                return $this->renderPartial('form',get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
        // } else {
        //     $response = $this->_restMaster->request('GET', 'klasifikasipasien/get-filtered');
        //     $row = [];
        //     $body = json_decode($response->getBody(),TRUE);
        //     $namaklasifikasi = isset($body['response']['klasifikasipasien_nama']) ? $body['response']['klasifikasipasien_nama'] : [];
        //     $kodeklasifikasi = isset($body['response']['klasifikasipasien_kode']) ? $body['response']['klasifikasipasien_kode'] : [];
        //     return $this->renderPartial('form', get_defined_vars());
        // }
    }

    public function actionUpdate($id = null)
    {
        try {
            $request = Yii::$app->request;
            $title = 'Ubah Data';
            $model = new KlasifikasipasienForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $id = DocoHelpers::decrypt($id);
            $options = ['status'=>1];

            $status = $this->_status;
            
            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    $response = $this->_restMaster->post('klasifikasipasien/update?id='.$id, [
                        'form_params' => $model->attributes
                    ]);
                    $r = json_decode($response->getBody(), true);
                    return DocoHelpers::response($r,false);
                } else {
                    $errors = DocoHelpers::parseError($model->errors, $formName);
                    return DocoHelpers::responseTemplate(422, 'Error', $errors);
                }
            } else {
                $response = $this->_restMaster->get('klasifikasipasien/view?id='.$id);
                $body = json_decode($response->getBody(), TRUE);
                if (isset($body['response']['data'])) {
                    $model->attributes = $body['response']['data'];
                }

                $attributes = $body['response'];

                $model->attributes = $attributes;
                
                return $this->renderPartial('form', get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);

        try {
            $response = $this->_restMaster->delete('klasifikasipasien/delete?id='.$id);
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

    public function actionChangeStatus(){
        $request = Yii::$app->request;
        $model = new LayarantrianForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $post = $request->post();

        if ($post) {

            $id = DocoHelpers::decrypt($post['id']);

            $model->load($request->post());

            try {
                $response = $this->_restMaster->put('klasifikasipasien/update?id='.$id, [
                    'form_params' => ['is_active' => $post['is_active']]
                ]);

                return DocoHelpers::responseJsonString($response->getBody(), $formName);
            } catch (RequestException $e) {
                return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), $formName);
            } catch (\Exception $e) {
                return DocoHelpers::responseTemplate(500, $e->getMessage());
            }
        }
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $url = 'klasifikasipasien/export-excel?'.http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/klasifikasipasien.xlsx";
        // if(!isset($yiiRestfulParams['advanced-filter']['klasifikasipasien_nama'])){
        //     $yiiRestfulParams['advanced-filter']['klasifikasipasien_nama'] = Yii::$app->docoVars->workspace("klasifikasipasien_id");
        //     // $yiiRestfulParams['advanced-filter']['ruangan_nama'] = Yii::$app->docoVars->workspace("ruangan_name");
        // }    
        try {
            // $ruangan_id = Yii::$app->docoVars->workspace("ruangan_id");
            $response = $this->_restMaster->get($url, ['save_to' => $path]);    
            $body = json_decode($response->getBody(), true);
            // return $url;        
            return DocoHelpers::downloadFile($path, true);
           } catch (\Exception $e) {
                $result['error'] = $e->getMessage();
                return $result;
           } catch (RequestException $e){
                $result['error'] = $e->getMessage();
                return $result;
           }  


    }

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/klasifikasi-pasien.pdf";
        try {
            $response = $this->_restMaster->get('klasifikasipasien/cetak-pdf?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);            
            return DocoHelpers::downloadPdf($response,$path);
        } catch (RequestException $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

}
