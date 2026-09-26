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

class InfPasienController extends DocoController
{
	protected $_title;
    protected $_restRm;
    protected $_module = '/rm/inf-pasien/';

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Informasi Pasien');
        $this->_restRm = Yii::$app->docoRest->rm;
    }

    public function actionIndex()
    {
    	$title = $this->_title;
        $this->setPasienEditLinkBefore('/rm/inf-pasien');
        $api_pasien = $this->_restRm->get('pasien/index');
        $pasien = json_decode($api_pasien->getBody(), True);
        return $this->render('index', get_defined_vars());
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
            $response = $this->_restRm->get('pasien/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                
                $primaryKey = DocoHelpers::encrypt($value['pasien_id']);
                $value['primary'] = $primaryKey;

                $value['tgl_rekam_medik'] = DocoHelpers::display_label($value['tgl_rekam_medik'], true, 
                    date('d M Y', strtotime($value['tgl_rekam_medik'])));

                $value['jeniskelamin'] = DocoHelpers::display_label(($value['jenis_kelamin'])); 
                $value['umur'] = DocoHelpers::getUmur($value['tanggal_lahir'], true).' tahun';
                $value['aksi'] = Html::a(
                    '<i class="fa fa-pencil" aria-hidden="true"></i>', '/pendaftaran/informasi-pencarian-pasien/update?id='.$primaryKey, [
                        'class' => 'btn btn-dark-turquise btn-xs',
                        'data-popup' => "tooltip",
                        'title' => \Yii::t('fe', 'Edit'),
                    ]
                );

                $value['status_dokumen_rekam_medis'] = Html::button('Belum dibuat', [
                    'class' => 'btn btn-success btn-xs data-check',
                    'data-value' => $value['no_rekam_medik'],
                    'data-nama' => $value['nama_pasien'],
                    'data-toggle' => 'modal',
                    'data-target' => '#modal_backdrop',
                    'action' => '/rm/inf-pasien/create-dokumen?no_rekam_medik='. $value['no_rekam_medik'],
                    'title' => \Yii::t('fe', 'Belum dibuat'),
                ]);
                if ($value['dokrekammedis_id'] > 0) {
                    $value['status_dokumen_rekam_medis'] = 'Sudah dibuat';
                    
                }

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

    public function actionUpdate($id)
    {
        $module = $this->_module;
        $request = Yii::$app->request;
        $title = \Yii::t('fe', 'Ubah').' '.\Yii::t('fe', $this->_title);
        $model = new PasienForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        
        $response = $this->_restRm->get('pasien/view?id='.$id);
        $body = json_decode($response->getBody(), TRUE);

        $attributes = $body['response'];
        $model->attributes = $attributes;
        $model->tanggal_lahir = date('d M Y', strtotime($attributes['tanggal_lahir']));
        $model->umur = DocoHelpers::getUmur($attributes['tanggal_lahir'], true).' tahun';
        
        $penanggungjawab_nama = DocoHelpers::display_label($attributes['penanggungJawab']['penanggungjawab_nama']);
        $penanggungjawab_alamat = DocoHelpers::display_label($attributes['penanggungJawab']['penanggungjawab_alamat']);
        $no_identitas = DocoHelpers::display_label($attributes['penanggungJawab']['no_identitas']);
        $penanggungjawab_notelp = DocoHelpers::display_label($attributes['penanggungJawab']['penanggungjawab_notelp']);
        $hubungankeluarga = DocoHelpers::display_label($attributes['penanggungJawab']['hubungankeluarga']);

        if ($request->post()) {
            $model->load($request->post());
            $model->no_rekam_medik = $attributes['no_rekam_medik'];
            $model->tgl_rekam_medik = $attributes['tgl_rekam_medik'];
            $model->kelompokumur_id = $attributes['kelompokumur_id'];

            if ($model->validate()) {
                try {
                    $response = $this->_restRm->put('pasien/update?id='.$id, [
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
	        $pasien = $body['response']['data'];

	        $response = $this->_restRm->get('lookup', ['query' => ['lookup_type' => 'jenis_kelamin']]);
	        $body = json_decode($response->getBody(), TRUE);
	        $jenis_kelamin = $body['response']['data'];

	        $response = $this->_restRm->get('lookup', ['query' => ['lookup_type' => 'nama_depan']]);
	        $body = json_decode($response->getBody(), TRUE);
	        $nama_depan = $body['response']['data'];

	        $response = $this->_restRm->get('lookup', ['query' => ['lookup_type' => 'status_perkawinan']]);
	        $body = json_decode($response->getBody(), TRUE);
	        $statusperkawinan = $body['response']['data'];

	        $response = $this->_restRm->get('pendidikan?per-page=1000');
	        $body = json_decode($response->getBody(), TRUE);
	        $pendidikan = $body['response']['data'];

	        $response = $this->_restRm->get('pekerjaan?per-page=1000');
	        $body = json_decode($response->getBody(), TRUE);
	        $pekerjaan = $body['response']['data'];

	        $response = $this->_restRm->get('lookup', ['query' => ['lookup_type' => 'agama']]);
	        $body = json_decode($response->getBody(), TRUE);
	        $agama = $body['response']['data'];

	        $response = $this->_restRm->get('propinsi');
	        $body = json_decode($response->getBody(), TRUE);
	        $propinsi = $body['response']['data'];

	        $response = $this->_restRm->get('kelurahan');
	        $body = json_decode($response->getBody(), TRUE);
	        $kelurahan = $body['response']['data'];

	        $response = $this->_restRm->get('kecamatan');
	        $body = json_decode($response->getBody(), TRUE);
	        $kecamatan = $body['response']['data'];

            return $this->render('form', get_defined_vars());
        }
    }

    public function actionCreateDokumen($no_rekam_medik)
    {
        try {
            $request = Yii::$app->request;
            $title = 'Tambah Data';
            $model = new DokRekamMedisForm;
            $formName = substr(strrchr(get_class($model), "\\"), 1);
            $selectedListLokasiSubrak = [];
            if ($request->post()) {
                $model->load($request->post());
                if ($model->validate()) {
                    try {
                        $forms = $request->post();
                        $forms['DokRekamMedisForm']['ruangan_id'] = Yii::$app->docoVars->workspace("ruangan_id");
                        $response = $this->_restRm->post('dok-rekam-medis/create', [
                            'form_params' => $forms
                        ]);
                        $response = json_decode($response->getBody(), true);

                        return DocoHelpers::response($response, false, true);
                    } catch (RequestException $e) {
                        return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), 'DokRekamMedisForm');
                    } catch (\Exception $e) {
                        return DocoHelpers::responseTemplate(500, $e->getMessage());
                    }
                } else {
                    $errors = DocoHelpers::parseError($model->errors, $formName);
                    return DocoHelpers::responseTemplate(422, 'Error', $errors);
                }
            } else {
                $response = $this->_restRm->get('lokasi-rak-rekam-medik');
                $body = json_decode($response->getBody(), true);
                $listLokasiRak = $body['response']['data'];

                $response = $this->_restRm->get('sub-rak-rekam-medik');
                $body = json_decode($response->getBody(), true);
                $listLokasiSubrak = $body['response']['data'];

                $response = $this->_restRm->get('pasien');
                $body = json_decode($response->getBody(), true);
                $pasien = $body['response']['data'];

                $LokrakRequest = $this->_restRm->get('lokasi-rak-rekam-medik/list-rak');
                $body = json_decode($LokrakRequest->getBody(), true);
                $lokrak = $body['response'];

                return $this->renderAjax('form-modal', get_defined_vars());
            }
        } catch (RequestException $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        } catch (\Exception $e) {
            return DocoHelpers::response(['message' => $e->getMessage()], 500);
        }
    }

    private function setPasienEditLinkBefore($link){
        $session = Yii::$app->session;
        $tmpData = ['link_before' => $link];
        $session->set('edit_pasien',$tmpData);
      }
}