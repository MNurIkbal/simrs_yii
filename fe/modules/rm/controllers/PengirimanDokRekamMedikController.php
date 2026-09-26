<?php
// Author : Ardi Pratama

namespace Doco\rm\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\rm\models\PengirimanRmForm;
use app\modules\rm\models\DokRekamMedisForm;
use GuzzleHttp\Exception\RequestException;

class PengirimanDokRekamMedikController extends DocoController
{
    protected $_title = "Rm :: Pengiriman Dokumen Rekam Medik";
    protected $_module = 'rm/pengiriman-dok-rekam-medik/';
    protected $_restRm;

    public function init()
    {
        parent::init();
        $this->_restRm = Yii::$app->docoRest->rm;
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
        try {
            $request = Yii::$app->request;
            $title = 'Tambah Data';
            $model = new PengirimanRmForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            
            if ($request->post()) {
                $model->load($request->post());
                $model->tgl_pengirimanrm = date('Y-m-d');
                if ($model->validate()) {
                    try {
                        foreach ($model->attributes as $key => $value) {
                            if(substr($key,0,4) == 'idx_'){
                                $decrypt_key = DocoHelpers::decrypt($value);
                                $model->{$this->unChangeKey($key)} = is_null($value)?null:$decrypt_key;
                            }
                        }
                        $response = $this->_restRm->post('pengiriman-dok-rekam-medik/create', [
                            'form_params' => $model->attributes
                        ]);
                        $response = json_decode($response->getBody(),true);

                        return DocoHelpers::response($response,false,true);
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
                $response = $this->_restRm->get('pengiriman-dok-rekam-medik/list-pasien');
                $body = json_decode($response->getBody(), TRUE);
                $pasien = [];
                foreach ($body['response']['data'] as $value) {
                    $primaryKey = DocoHelpers::encrypt($value['pasien_id']);
                    $value['idx_pasien'] = $primaryKey;
                    unset($value['pasien_id']);
                    array_push($pasien, $value); 
                }
                $response = $this->_restRm->get('instalasi');
                $body = json_decode($response->getBody(), TRUE);
                $instalasi = [];
                foreach ($body['response']['data'] as $value) {
                    $primaryKey = DocoHelpers::encrypt($value['instalasi_id']);
                    $value['idx_instalasi'] = $primaryKey;
                    unset($value['instalasi_id']);
                    array_push($instalasi, $value); 
                }
                $response = $this->_restRm->get('ruangan');
                $body = json_decode($response->getBody(), TRUE);
                $ruangan = [];
                foreach ($body['response']['data'] as $value) {
                    $primaryKey = DocoHelpers::encrypt($value['ruangan_id']);
                    $value['idx_ruangan'] = $primaryKey;
                    unset($value['ruangan_id']);
                    array_push($ruangan, $value); 
                }
                $response = $this->_restRm->get('pegawai');
                $body = json_decode($response->getBody(), TRUE);
                $pegawai = [];
                foreach ($body['response']['data'] as $value) {
                    $primaryKey = DocoHelpers::encrypt($value['pegawai_id']);
                    $value['idx_pegawai'] = $primaryKey;
                    unset($value['pegawai_id']);
                    array_push($pegawai, $value); 
                }
                // $response = $this->_restRm->get('pengiriman-dok-rekam-medik/form-data');
                // $body = json_decode($response->getBody(), TRUE);
                // $data_form = [];
                // foreach ($body['response']['data'] as $value) {
                //     foreach ($value as $key => $val) {
                //         if(substr($key, -3) == '_id'){
                //             $encrypt_key = DocoHelpers::encrypt($val);
                //             $value[$this->changeKey($key)] = is_null($val)?null:$encrypt_key;
                //             unset($value[$key]);
                //         }
                //     }
                //     array_push($data_form, $value); 
                // }
                return $this->render('index', get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionGetData()
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
            $response = $this->_restRm->get('pengiriman-dok-rekam-medik', 
                [
                    'query' => $yiiRestfulParams
                ]
            );
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['kirimdokrm_id']);
                unset($value['kirimdokrm_id']);

                $value['rowNum'] = $no;
                $value['primary'] = $primaryKey;
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


    // public function actionGetData()
    // {
    //     Yii::$app->response->format = Response::FORMAT_JSON;
    //     $request = Yii::$app->request;
    //     $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
    //     $draw = $request->get('draw', 1);
    //     $data = [];

    //     $result = [];
    //     $result['data'] = $data;
    //     $result['draw'] = $draw;
    //     $result['recordsTotal'] = 0;
    //     $result['recordsTotal'] = 0;
    //     try {
    //         $response = $this->_restRm->get('pengiriman-dok-rekam-medik/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
    //         $body = json_decode($response->getBody(), True);
    //         $no = $request->get('start',1);
    //         foreach ($body['response']['data'] as $key => $value) {
    //             $no++;
    //             $primaryKey = DocoHelpers::encrypt($value['pengirimanrm_id']);
    //             unset($value['pengirimanrm_id']);

    //             $value['aksi']  = Html::button(
    //                 'Lihat', [
    //                     'class' => 'btn btn-info btn-xs btn-block data-view',
    //                     'action' => Url::home().$this->_module.'view?id='.$primaryKey,
    //                     'data-toggle' => 'modal',
    //                     'data-target' => '#modal_backdrop'
    //                 ]
    //             );
    //             // $value['aksi'] .= Html::a(
    //                 // 'Export', '#', [
    //                     // 'class' => 'btn btn-success btn-xs btn-block data-export',
    //                     // 'action' => Url::home().$this->_module.'export?id='.$primaryKey
    //                 // ]
    //             // );
    //             // $value['aksi'] .= Html::a(
    //             //     'Cetak', '#', [
    //             //         'class' => 'btn btn-warning btn-xs btn-block data-print',
    //             //         'action' => Url::home().$this->_module.'print?id='.$primaryKey
    //             //     ]
    //             // );
    //             $value['aksi'] .= Html::button(
    //                 'Ubah', [
    //                     'class' => 'btn btn-primary btn-xs btn-block data-update',
    //                     'action' => Url::home().$this->_module.'update?id='.$primaryKey,
    //                     'data-toggle' => 'modal',
    //                     'data-target' => '#modal_backdrop'
    //                 ]
    //             );
    //             $value['aksi'] .= Html::a(
    //                 'Hapus', '#', [
    //                     'class' => 'btn btn-danger btn-xs btn-block data-delete',
    //                     'action' => Url::home().$this->_module.'delete?id='.$primaryKey
    //                 ]
    //             );

    //             $value['rowNum'] = $no;
    //             $data[$key] = $value;
    //         }

    //         $result['data'] = $data;
    //         $result['recordsTotal'] = $body['response']['_meta']['totalCount'];
    //         $result['recordsFiltered'] = $body['response']['_meta']['totalCount'];
    //         return $result;
    //     } catch (RequestException $e) {
    //         $result['error'] = $e->getMessage();
    //         return $result;
    //     } catch (\Exception $e) {
    //         $result['error'] = $e->getMessage();
    //         return $result;
    //     }
    // }

    public function actionView($id)
    {
        $header = $detail = [];
        try {
            $request = Yii::$app->request;
            $title = 'Lihat Data';
            $id = DocoHelpers::decrypt($id);

            $response = $this->_restRm->get('pengiriman-dok-rekam-medik/view?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $header = $attributes['header'];
            $detail = $attributes['detail'];
        } catch (RequestException $e) {

        } catch (\Exception $e) {

        }
        return $this->render('view', get_defined_vars());
    }

    public function actionCreate()
    {
        try {
            $request = Yii::$app->request;
            $title = 'Tambah Data';
            $model = new PengirimanRmForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            
            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    try {
                        $idx = DocoHelpers::decrypt($model->row_id);
                        $response = $this->_restRm->put('pengiriman-dok-rekam-medik/simpan-pembaharuan?id='.$idx, [
                            'form_params' => $model->attributes
                        ]);

                        return DocoHelpers::responseJsonString($response->getBody(), $formName);
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
                $response = $this->_restRm->get('pengiriman-dok-rekam-medik/list-pasien');
                $body = json_decode($response->getBody(), TRUE);
                $pasien = [];
                foreach ($body['response']['data'] as $value) {
                    $primaryKey = DocoHelpers::encrypt($value['pasien_id']);
                    $value['row_id'] = $primaryKey;
                    unset($value['pasien_id']);
                    array_push($pasien, $value); 
                }
                return $this->renderPartial('form', get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionUpdate($id = null)
    {
        $request = Yii::$app->request;
        $title = 'Ubah Data';
        $model = new DokRekamMedisForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $id = DocoHelpers::decrypt($id);
        
        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restRm->put('pengiriman-dok-rekam-medik/update?id='.$id, [
                        'form_params' => $model->attributes
                    ]);

                    return DocoHelpers::responseJsonString($response->getBody(), $formName);
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
            $response = $this->_restRm->get('pasien');
            $body = json_decode($response->getBody(), TRUE);
            $pasien = [];
            foreach ($body['response']['data'] as $value) {
                $primaryKey = DocoHelpers::encrypt($value['pasien_id']);
                $value['row_id'] = $primaryKey;
                unset($value['pasien_id']);
                array_push($pasien, $value); 
            }

            $response = $this->_restRm->put('pengiriman-dok-rekam-medik/update?id='.$id);
            $body = json_decode($response->getBody(), TRUE);
            $attributes = $body['response'];
            $attributes['row_id'] = DocoHelpers::encrypt($attributes['pasien_id']);
            unset($attributes['dokrekammedis_id']);
            unset($attributes['pasien_id']);
            unset($attributes['pasien_m']['pasien_id']);
            $model->attributes = $attributes;
            return $this->renderPartial('form', get_defined_vars());
        }
    }

    public function actionTerimaDokumen($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restRm->post('pengiriman-dok-rekam-medik/terima?id='.$id);
            $response = json_decode($response->getBody(),true);
            return DocoHelpers::response(['response' => [
                'text' => 'Dokumen berhasil diterima',
                'title' => 'Proses Berhasil !'
            ]]);
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()],500);
        }
    }

    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restRm->delete('pengiriman-dok-rekam-medik/delete?id='.$id);
            return DocoHelpers::responseTemplate(
                $response->getStatusCode(), 
                "OK", [
            ]);
        } catch (RequestException $e) {
            var_dump($e);exit;
            return DocoHelpers::responseTemplate(
                $e->getResponse()->getStatusCode(), 
                json_decode($e->getResponse()->getBody()->getContents())->message, [
            ]);
        } catch (\Exception $e) {
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionExport($id)
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionPrint($id)
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionExportAll()
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionPrintAll()
    {
        return $this->render('index', get_defined_vars());
    }

    public function actionSetPencatatan($id)
    {
        $request = Yii::$app->request;
        $title = 'Lihat Data';
        $model = new DokRekamMedisForm;
        $id = DocoHelpers::decrypt($id);
        $response = $this->_restRm->get('pengiriman-dok-rekam-medik/get-list?id='.$id);
        $body = json_decode($response->getBody(), TRUE);
        $attributes = $body['response'];
        $attributes['row_id'] = DocoHelpers::encrypt($attributes['pasien_id']);
        unset($attributes['dokrekammedis_id']);
        unset($attributes['pasien_id']);
        unset($attributes['pasien_m']['pasien_id']);

        if(isset($attributes['pasien_m']['no_rekam_medik'])){
            $split_no_rm = str_split($attributes['pasien_m']['no_rekam_medik']);
            $attributes['nomorprimer'] = $split_no_rm[0].$split_no_rm[1];
            $attributes['nomorsekunder'] = $split_no_rm[2].$split_no_rm[3];
            $attributes['nomortertier'] = $split_no_rm[4].$split_no_rm[5];
        }
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        return $attributes;
    }

    private function changeKey($key)
    {
        $new_key = explode("_", $key);
        return 'idx_'.$new_key[0];
    }

    private function unChangeKey($key)
    {
        $new_key = explode("_", $key);
        return $new_key[1].'_id';
    }

    public function actionSearchNomor()
    {
        $response = [];
        $request = Yii::$app->request;
        try {
            $res = $this->_restRm->get('pengiriman-dok-rekam-medik/list-nomor',[
                'query' => $request->get()
            ]);
            $bod = json_decode($res->getBody(), TRUE);
            $response = $bod['response'];
        } catch (RequestException $e) {

        } catch (\Exception $e) {

        }
        return DocoHelpers::response($response);
    }

    public function actionInformasi()
    {
        $ruangan = $instalasi = $status = [];
        try {
            $res = $this->_restRm->get('pengiriman-dok-rekam-medik/get-options');
            $bod = json_decode($res->getBody(), TRUE);
            $ruangan = $bod['response']['ruangan'];
            $instalasi = $bod['response']['instalasi'];
            $status = $bod['response']['status_kirim'];
        } catch (RequestException $e) {

        } catch (\Exception $e) {

        }
        return $this->render('informasi', get_defined_vars());
    }
}
