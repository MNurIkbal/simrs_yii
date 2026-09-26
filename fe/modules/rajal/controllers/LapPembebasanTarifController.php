<?php 
// Author : Ardi Pratama

namespace Doco\rajal\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;

class LapPembebasanTarifController extends DocoController
{
	protected $_title;
    protected $_restRajal;
    protected $_restMaster;
    protected $_module = '/rm/lap-pembebasan-tarif/';

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Laporan Pembebasan Tarif');
        $this->_restRajal = Yii::$app->docoRest->rajal;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
    	$title = $this->_title;

        $KelasPelayananRequest = $this->_restMaster->get('kelas-pelayanan/list-kelas-pelayanan');
        $body = json_decode($KelasPelayananRequest->getBody(),TRUE);
        $ddlKelasPelayanan = ArrayHelper::map($body['response'],'kelaspelayanan_id','kelaspelayanan_nama');

        $JenisKasusPenyakitRequest = $this->_restMaster->get('jenis-kasus-penyakit/allow-list-penyakit');
        $body = json_decode($JenisKasusPenyakitRequest->getBody(),TRUE);
        $ddlJenisKasusPenyakit = $body['response'];

        $DokterRequest = $this->_restMaster->get('pegawai/allow-list-dokter-rajal');
        $body = json_decode($DokterRequest->getBody(),TRUE);
        $ddlDokter = $body['response'];

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        if (isset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran'])) {
            $tgl_pendaftaran_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
            $tgl_awal = $tgl_pendaftaran_range[0];
            $tgl_akhir = $tgl_pendaftaran_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_pendaftaran_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_pendaftaran']);
        }

        $draw = $request->get('draw', 1);
        $data = [];

        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;

        try {
            $response = $this->_restRajal->get('lap-pembebasan-tarif/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pembebasantarif_id']);
                unset($value['pembebasantarif_id']);
                

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
}