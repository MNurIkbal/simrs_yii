<?php
// Author : Budi

namespace Doco\rm\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\modules\rm\models\PasienForm;
use app\modules\rm\models\DokRekamMedisForm;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;
class InfDokumenController extends DocoController
{
    protected $_title;
    protected $_restRm;
    protected $_restMaster;
    protected $_module = '/rm/inf-dokumen/';

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Dokumen Rekam Medik');
        $this->_restRm = Yii::$app->docoRest->rm;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');
        $data = $this->getData();
        $data_rak = !empty($data['data_rak'])
            ? ArrayHelper::map($data['data_rak'], 'lokasirak_id', 'lokasirak_nama')
            : [];
        $data_subrak = !empty($data['data_subrak'])
            ? ArrayHelper::map($data['data_subrak'], 'subrak_id', 'subrak_nama')
            : [];
        $data_warna_dokumen = !empty($data['data_warna_dokumen'])
            ? ArrayHelper::map($data['data_warna_dokumen'], 'warnadokrm_id', 'warnadokrm_namawarna')
            : [];
        $data_instalasi = !empty($data['data_instalasi'])
            ? $data['data_instalasi']
            : [];
        $data_ruangan = !empty($data['data_ruangan'])
            ? $data['data_ruangan']
            : [];

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        // $yiiRestfulParams['advanced-filter']['ruanganakhir_id'] = Yii::$app->docoVars->workspace('ruangan_id');
        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restRm->get('dok-rekam-medis/informasi?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);

            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['dokrekammedis_id']);

                $value['tglrekammedis'] = DocoHelpers::display_label($value['tglrekammedis'], true,
                    date('d M Y', strtotime($value['tglrekammedis'])));

                $value['primary'] = $primaryKey;
                $value['no_rekam_medik'] = DocoHelpers::display_label($value['no_rekam_medik']);
                $value['nama_pasien'] = DocoHelpers::display_label($value['nama_pasien']);
                $value['warnadokrm_namawarna'] = DocoHelpers::display_label($value['warnadokrm_namawarna']);
                unset($value['dokrekammedis_id']);
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

    public function actionGetDataPasien()
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
            $response = $this->_restRm->get('pasien/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['check'] = Html::button('<i class="fa fa fa-check-square-o" aria-hidden="true"></i>', [
                    'class' => 'btn btn-success btn-xs data-check',
                    'data-value' => $value['no_rekam_medik'],
                    'data-nama' => $value['nama_pasien'],
                    'title' => \Yii::t('fe', 'Pilih'),
                ]);
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

    public function actionSearch($tipe = NULL)
    {

        return $this->renderPartial('search', get_defined_vars());
    }

    public function actionView($id)
    {
        $request = Yii::$app->request;
        $title = 'Lihat Data';
        $model = new DokRekamMedisForm;
        $id = DocoHelpers::decrypt($id);

        $response = $this->_restRm->get('dok-rekam-medis/view?id='.$id);
        $body = json_decode($response->getBody(), TRUE);
        $attributes = $body['response'];
        $model->attributes = $attributes;
        return $this->renderPartial('view', get_defined_vars());
    }

    public function actionUpdate($id)
    {
        $module = $this->_module;
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Ubah').' '.\Yii::t('fe', $this->_title);
        $model = new DokRekamMedisForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);

        if ($request->post()) {
            $model->load($request->post());
            if ($model->validate()) {
                try {
                    $response = $this->_restRm->put('dok-rekam-medis/update?id='.$id, [
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
                $response = $this->_restRm->get('lokasi-rak-rekam-medik');
                $body = json_decode($response->getBody(), TRUE);
                $lokasirak = $body['response']['data'];

                $response = $this->_restRm->get('sub-rak-rekam-medik');
                $body = json_decode($response->getBody(), TRUE);
                $lokasisubrak = $body['response']['data'];

                $response = $this->_restRm->get('warna-dok-rekam-medik');
                $body = json_decode($response->getBody(), TRUE);
                $warnadok = $body['response']['data'];

                $response = $this->_restRm->get('pasien');
                $body = json_decode($response->getBody(), TRUE);
                $pasien = $body['response']['data'];

                $response = $this->_restRm->get('dok-rekam-medis/view?id='.$id);
                $body = json_decode($response->getBody(), TRUE);
                $attributes = $body['response'];
                $model->attributes = $attributes;
                $model->tglrekammedis = date('d M Y', strtotime($model->tglrekammedis));
                return $this->renderAjax('form', get_defined_vars());
        }
    }

    private function getData()
    {
        try {
            $response = $this->_restRm->request('GET', 'dok-rekam-medis/get-api');
            $body = json_decode($response->getBody(),TRUE);

            $return = [
                'data_rak' => $body['response']['data-rak'],
                'data_subrak' => $body['response']['data-subrak'],
                'data_warna_dokumen' => $body['response']['data-warna-dokumen'],
                'data_instalasi' => $body['response']['data-instalasi'],
                'data_ruangan' => $body['response']['data-ruangan'],
            ];

            return $return;
        } catch (RequestException $e) {
            echo $e->getMessage();
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }

    public function actionGetNoRekamMedik()
    {
        if (isset($_GET['q']['term']) && !empty($_GET['q']['term'])) {
            $response = $this->_restRm->request('POST', 'dok-rekam-medis/data-norm',[
                'form_params'=>['term'=>$_GET['q']['term']],
            ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id' => $value['no_rekam_medik'], 'text' => $value['no_rekam_medik']];
            }
            $total = count($body['response']);
            $return = ['result' => $data, 'total_count' => $total, 'incomplete_results' => false];

            return DocoHelpers::response($return);
        }
    }

    public function actionGetPasien()
    {
        if (isset($_GET['q']['term']) && !empty($_GET['q']['term'])) {
            $response = $this->_restRm->request('POST', 'dok-rekam-medis/data-pasien',[
                'form_params'=>['term'=>$_GET['q']['term']],
            ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id' => $value['nama_pasien'], 'text' => $value['nama_pasien']];
            }
            $total = count($body['response']);
            $return = ['result' => $data, 'total_count' => $total, 'incomplete_results' => false];

            return DocoHelpers::response($return);
        }
    }
}