<?php 
namespace Doco\jenazah\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DHtml;
use app\modules\jenazah\models\PasienMasukPenunjangForm;
use app\modules\jenazah\models\AmbilJenazahForm;
use GuzzleHttp\Exception\RequestException;
use kartik\widgets\DatePicker;

class InformasiPasienMeninggalController extends DocoController
{
    protected $_title;
    protected $_restJenazah;
    protected $_restMaster;
    protected $_module = '/jenazah/informasi-pasien-meninggal/';

    public function init()
    {
        parent::init();

        $this->_title = Yii::t('fe', 'Informasi Pasien Meninggal');
        $this->_restJenazah = Yii::$app->docoRest->jenazah;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $title = $this->_title;
        $response = $this->getRequest();

        return $this->render('index', get_defined_vars());
    }

    private function getRequest()
    {
        try {
            $response = $this->_restJenazah->get('informasi-pasien-meninggal/generate-api');
            $response = json_decode($response->getBody(),TRUE);
            $response = $response['response'];
            return $response;
        } catch (RequestException $e) {
            $result['jenisAmbulan'] = [];
            return $result;
        } catch (\Exception $e) {
            $result['jenisAmbulan'] = [];
            return $result;
        }
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        if (isset($yiiRestfulParams['advanced-filter']['tgl_meninggal'])) {
            $tgl_meninggal_range = explode(' - ', $yiiRestfulParams['advanced-filter']['tgl_meninggal']);
            $tgl_awal = $tgl_meninggal_range[0];
            $tgl_akhir = $tgl_meninggal_range[1];
            $tgl_awal_format = date('Y-m-d H:i:s', strtotime($tgl_awal . ' 00:00:00'));
            $tgl_akhir_format = date('Y-m-d H:i:s', strtotime($tgl_akhir . ' 23:59:59'));
            $yiiRestfulParams['advanced-filter']['tgl_meninggal_awal'] = $tgl_awal_format;
            $yiiRestfulParams['advanced-filter']['tgl_meninggal_akhir'] = $tgl_akhir_format;
            unset($yiiRestfulParams['advanced-filter']['tgl_meninggal']);
        }

        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restJenazah->get('informasi-pasien-meninggal/index?'.http_build_query($yiiRestfulParams), ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['primary'] = $primaryKey;
                unset($value['pendaftaran_id']);
                $value['rowNum'] = $no;
                $value['tgl_meninggal'] = date('d M Y', strtotime($value['tgl_meninggal']));
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

    public function actionGetDataTindakanObat()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $pendaftaran_id = $request->get('pendaftaran_id');
        $draw = $request->get('draw', 1);
        $data = [];
        $result = [];
        $result['data'] = $data;
        $result['draw'] = $draw;
        $result['recordsTotal'] = 0;
        $result['recordsTotal'] = 0;
        try {
            $response = $this->_restJenazah->get('informasi-pasien-meninggal/get-tindakan-obat?pendaftaran_id='.$pendaftaran_id, ['form_params' => []]);
            $body = json_decode($response->getBody(), True);
            // return DocoHelpers::response($body);
            $no = $request->get('start',1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['primary'] = $primaryKey;
                unset($value['pendaftaran_id']);
                $value['rowNum'] = $no;
                $value['dilakukan'] = Html::checkbox('Tindakan['.$value['tindakan_obat_id'].'][dilakukan]', false, [
                    'class' => 'chk_tindakan'
                ]);
                $value['tgl_tindakan'] = DatePicker::widget([
                    'name' => 'Tindakan['.$value['tindakan_obat_id'].'][tgl_tindakan]',
                    'type' => DatePicker::TYPE_COMPONENT_PREPEND,
                    'value' => date('d-M-Y'),
                    'options' => [
                        'class' => 'tgl_tindakan',
                    ],
                    'pluginOptions' => [
                        'autoclose'=>true,
                        'format' => 'dd-M-yyyy',
                    ]
                ]);
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

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $url = 'informasi-pasien-meninggal/export-excel?'.http_build_query($yiiRestfulParams);
        $path = Yii::getAlias("@download") . "/informasi-pasien-meninggal.xlsx";
        try {
            $response = $this->_restJenazah->get($url,[
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionCetakBelumDiterima($pendaftaran_id)
    {
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download") . "/serah-terima-jenazah.pdf";
        try {
            $response = $this->_restJenazah->get('informasi-pasien-meninggal/print-belum-diterima', [
                'save_to' => $path,
                'query' => [
                        'pendaftaran_id'=>$pendaftaran_id,
                    ],
            ]);
            $body = json_decode($response->getBody(), true);
            // return DocoHelpers::response($body);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {var_dump($e->getMessage()); die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionSerahTerimaRuangan($id)
    {
        $id = DocoHelpers::decrypt($id);
        $response = $this->getRequest();

        $title = Yii::t('fe', 'Serah Terima Pasien Meninggal Dunia');
        $data = $this->getDataJenazah($id);
        $model = new PasienMasukPenunjangForm;
        $model->attributes = $data;
        $model->umur = $data['tempat_lahir'].' / '.$data['umur'];
        $tanggal_pendaftaran = $data['tgl_pendaftaran'];
        return $this->render('_serah_terima_ruangan', get_defined_vars());
    }

    public function actionSerahTerimaKeluarga($id)
    {
        $id = DocoHelpers::decrypt($id);
        $title = Yii::t('fe', 'Penyerahan Jenazah Kepada Keluarga');
        $data = $this->getDataJenazah($id);
        $response = $this->getRequest();
        $model = new AmbilJenazahForm;
        $model->attributes = $data;
        $model->umur = $data['tempat_lahir'].' / '.$data['umur'];
        $date = date('Y-m-d', strtotime($model->tgl_meninggal));
        $nameOfDay = date('D', strtotime($date)); 
        $listHari = DocoHelpers::$_hari_indo;
        $model->tgl_meninggal = $listHari[$nameOfDay].' '.date('d M Y', strtotime($data['tgl_meninggal'])).' Jam : '.date('H:i:s', strtotime($data['tgl_meninggal']));
        $model->instalasi_asal = $data['instalasi_asal'];

        return $this->render('_serah_terima_keluarga', get_defined_vars());
    }

    public function actionProses($id)
    {
        $id = DocoHelpers::decrypt($id);
        $title = Yii::t('fe', 'Proses Pasien Meninggal Dunia');
        $data = $this->getDataJenazah($id);
        $masukPenunjang = $this->getDataPasienMasukPenunjang($id);
        $model = new PasienMasukPenunjangForm;
        $model->attributes = $data;
        $model->umur = $data['tempat_lahir'].' / '.$data['umur'];
        $model->instalasi_asal = $data['instalasi_asal'];

        return $this->render('_proses', get_defined_vars());
    }


    private function getDataJenazah($id)
    {
        try {
            $response = $this->_restJenazah->get('informasi-pasien-meninggal/get-data-jenazah?id='.$id);
            $response = json_decode($response->getBody(),TRUE);
            $response = $response['response'];
            return $response;
        } catch (RequestException $e) {
            $result = [];
            return $result;
        } catch (\Exception $e) {
            $result = [];
            return $result;
        }
    }

    private function getDataPasienMasukPenunjang($id)
    {
        try {
            $response = $this->_restJenazah->get('informasi-pasien-meninggal/get-data-masuk-penunjang?id='.$id);
            $response = json_decode($response->getBody(),TRUE);
            $response = $response['response'];
            return $response;
        } catch (RequestException $e) {
            $result = [];
            return $result;
        } catch (\Exception $e) {
            $result = [];
            return $result;
        }
    }

    public function actionSearchPegawai()
    {
        $response = [];
        try {
            $request = Yii::$app->request;
            $result = $this->_restMaster->get('allow/list-pegawai2',[
                            'query' => [
                                'pegawai_id' => $request->get('pegawai_id')
                            ]
                        ]);
            $result = json_decode($result->getBody(),true);
            $data = isset($result['response']) ? $result['response'] : [];
            $response = [
                'id' => $data['pegawai_id'],
                'text' => $data['nama_pegawai'],
                'jabatan' => $data['jabatan_nama'],
            ];
            
        } catch (RequestException $e) {
            Yii::info($e->getMessage());
            $response['message'] = $e->getMessage();
        }
        
        return DocoHelpers::response([
            'result' => $response
        ]);
    }

    public function actionSaveSerahTerima()
    {
        $post = Yii::$app->request->post('PasienMasukPenunjangForm');
        $model = new PasienMasukPenunjangForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $model->attributes = $post;
        $model->tglmasukpenunjang = date('Y-m-d', strtotime($post['tglserah_terima']));
        if($model->validate()) {
            try {
                $result = $this->_restJenazah->post('informasi-pasien-meninggal/save',[
                    'form_params' => $post
                ]);
                $result = json_decode($result->getBody(),true);
                return DocoHelpers::response($result, false);
            } catch (RequestException $e) {
                Yii::info($e->getMessage());
                $response['response']['text'] = 'Terjadi kesalah pada sistem';
                $response['response']['message'] = $e->getMessage();
                return DocoHelpers::response($response, 500);
            }
        }
        else {
            $errors = DocoHelpers::parseError($model->errors, $formName);
            return DocoHelpers::responseTemplate(422, 'Error', $errors);
        }
    }

    public function actionSaveSerahTerimaKeluarga()
    {
        $post = Yii::$app->request->post('AmbilJenazahForm');
        $model = new AmbilJenazahForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $model->attributes = $post;
        $model->ruangan_id = 38;
        $model->tgl_lahir = date('Y-m-d', strtotime($post['tgl_lahir']));
        if($model->validate()) {
            try {
                $result = $this->_restJenazah->post('informasi-pasien-meninggal/save-keluarga',[
                    'form_params' => $post
                ]);
                $result = json_decode($result->getBody(),true);
                return DocoHelpers::response($result, false);
            } catch (RequestException $e) {
                Yii::info($e->getMessage());
                $response['response']['text'] = 'Terjadi kesalah pada sistem';
                $response['response']['message'] = $e->getMessage();
                return DocoHelpers::response($response, 500);
            }
        }
        else {
            $errors = DocoHelpers::parseError($model->errors, $formName);
            return DocoHelpers::responseTemplate(422, 'Error', $errors);
        }
    }

    public function actionSaveProses()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        $postPenunjang = $post['PasienMasukPenunjangForm'];
        $model = new PasienMasukPenunjangForm;
        $formName = substr(strrchr(get_class($model), "\\"), 1);
        $model->attributes = $postPenunjang;
        $model->tglmasukpenunjang = $postPenunjang['tglserah_terima'];
        $postPenunjang = array_merge($post, $postPenunjang);
        $postPenunjang['pendaftaran_id'] = $request->get('id');
        if($model->validate()) {
            try {
                $result = $this->_restJenazah->post('informasi-pasien-meninggal/save-proses',[
                    'form_params' => $postPenunjang,

                ]);
                $result = json_decode($result->getBody(),true);
                // return DocoHelpers::response($result);
                return DocoHelpers::response($result, false);
            } catch (RequestException $e) {
                Yii::info($e->getMessage());
                $response['response']['text'] = 'Terjadi kesalah pada sistem';
                $response['response']['message'] = $e->getMessage();
                return DocoHelpers::response($response, 500);
            }
        }
        else {
            $errors = DocoHelpers::parseError($model->errors, $formName);
            return DocoHelpers::responseTemplate(422, 'Error', $errors);
        }
    }

    public function actionCetakSerahTerima($pendaftaran_id)
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download")."/serah-terima.pdf";
        try {
            $response = $this->_restJenazah->get('informasi-pasien-meninggal/print-serah-terima', 
                [
                    'query' => [
                        'pendaftaran_id' => $pendaftaran_id
                    ],
                    'save_to' => $path
                ]
            );
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionCetakDetailSerahTerima($pendaftaran_id)
    {
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download")."/serah-terima.pdf";
        try {
            $response = $this->_restJenazah->get('informasi-pasien-meninggal/print-serah-terima', 
                [
                    'query' => [
                        'pendaftaran_id' => $pendaftaran_id
                    ],
                    'save_to' => $path
                ]
            );
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionCetakSerahTerimaKeluarga($pendaftaran_id)
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download")."/serah-terima-keluarga.pdf";
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        try {
            $response = $this->_restJenazah->get('informasi-pasien-meninggal/print-serah-terima-keluarga', 
                [
                    'query' => [
                        'pendaftaran_id' => $pendaftaran_id
                    ],
                    'save_to' => $path
                ]
            );

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }


    public function actionCetakSerahTerimaKeluarga2($pendaftaran_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download")."/serah-terima-keluarga.pdf";
        try {
            $response = $this->_restJenazah->get('informasi-pasien-meninggal/print-serah-terima-keluarga', 
                [
                    'query' => [
                        'pendaftaran_id' => $pendaftaran_id
                    ],
                    'save_to' => $path
                ]
            );
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionCetakProses($pendaftaran_id)
    {
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download")."/proses-jenazah.pdf";
        $pendaftaran_id = DocoHelpers::decrypt($pendaftaran_id);
        try {
            $response = $this->_restJenazah->get('informasi-pasien-meninggal/print-proses', 
                [
                    'query' => [
                        'pendaftaran_id' => $pendaftaran_id
                    ],
                    'save_to' => $path
                ]
            );

            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionCetakProses2($pendaftaran_id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $path = Yii::getAlias("@download")."/proses-jenazah.pdf";
        try {
            $response = $this->_restJenazah->get('informasi-pasien-meninggal/print-proses', 
                [
                    'query' => [
                        'pendaftaran_id' => $pendaftaran_id
                    ],
                    'save_to' => $path
                ]
            );
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            throw new \yii\web\HttpException(500, $e->getMessage());
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    private function getDataJenazahByNo($no_masukpenunjang)
    {
        try {
            $response = $this->_restJenazah->get('informasi-pasien-meninggal/get-data-jenazah-by-no?no_masukpenunjang='.$no_masukpenunjang);
            $response = json_decode($response->getBody(),TRUE);
            $response = $response['response'];
            return $response;
        } catch (RequestException $e) {
            $result = [];
            return $result;
        } catch (\Exception $e) {
            $result = [];
            return $result;
        }
    }
}