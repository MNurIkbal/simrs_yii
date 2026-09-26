<?php

namespace Doco\radiologi\controllers;

use Yii;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

use GuzzleHttp\Exception\RequestException;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use yii\helpers\ArrayHelper;

class LapPasienRujukanRadController extends DocoController
{
    protected $_restRad;

    public function init()
    {
        parent::init();
        $this->_restRad = Yii::$app->docoRest->radiologi; 

    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        unset($behaviors['access']);
        unset($behaviors['verbs']);

        return $behaviors;
    }

    // Action index
    public function actionIndex()
    {
        $rujukan = $penjamin = $cara_bayar = $status = $options = $asalRujukan = [];

        $status[0] = array(
            'kode' => 470,
            'status_nama'=> 'BELUM DISETUJUI'
        );
        $status[1] = array(
            'kode' => 471,
            'status_nama' => 'SUDAH DISETUJUI'
        );
        $status[2] = array(
            'kode'=>472,
            'status_nama'=> 'BATAL'
        );

        $status = ArrayHelper::map($status,'status_nama','status_nama');

        try {
            $response = $this->_restRad->get('lap-pasien-rujukan-rad/get-options');
            $body = json_decode($response->getBody(), true);
            $cara_bayar = $body['response']['cara_bayar'];
            $penjamin = $body['response']['penjamin'];
            $rujukan = $body['response']['rujukan'];
            $asalRujukan = $body['response']['asalRujukan'];
            $statusPeriksa = $body['response']['statusPeriksa'];
            $namaRsRujukan = $body['response']['namaRsRujukan'];
            $dokterPerujuk = $body['response']['dokterPerujuk'];
            $jenisPemeriksaanRad = $body['response']['jenisPemeriksaanRad']['response'];

            $rujukan = ArrayHelper::map($rujukan,'ruangan_nama','ruangan_nama');
            $asalRujukan = ArrayHelper::map($asalRujukan,'asalrujukan_id','asalrujukan_nama');
            $statusPeriksa = ArrayHelper::map($statusPeriksa,'lookup_name','lookup_name');
            $namaRsRujukan = ArrayHelper::map($namaRsRujukan,'rujukankeluar_id','rumahsakit_rujukan');
            $jenisPemeriksaanRad = ArrayHelper::map($jenisPemeriksaanRad,'daftartindakan_nama','daftartindakan_nama');
            $dokterPerujuk = ArrayHelper::map($dokterPerujuk,'nama_pegawai','nama_pegawai');
            $jenisRujukan =  [
                1 => 'Rujukan RS',
                2 => 'Rujukan Masuk',
                3 => 'APS',
                4 => 'Rujukan Keluar'
            ];
        } catch (RequestException $e) {
            $cara_bayar = $penjamin = [];
        }
        return $this->render('index', get_defined_vars());
    }

    

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $draw = $request->get('draw', 1);
        $data = [];
        try {
            $response = $this->_restRad->get('lap-pasien-rujukan-rad', [
                'query' => $yiiRestfulParams
            ]);
            $body = json_decode($response->getBody(), True);
            $no = $request->get('start', 1);

            foreach ($body['response']['data'] as $key => $value) {
                // Manage data for datatables
                $no++;

                $value['tgl_rujukan'] = ArrayHelper::getValue($value,'tgl_rujukan');
                $value['tgl_persetujuan'] = ArrayHelper::getValue($value,'tgl_persetujuan');
                $value['rowNum'] = $no;
                $value['tgl_rujukan'] = date('d-M-Y H:i', strtotime($value['tgl_rujukan']));
                $value['tgl_persetujuan'] = date('d-M-Y H:i', strtotime($value['tgl_persetujuan']));
                $data[$key] = $value;
            }

            $return = [
                'data' => $data,
                'draw' => $request->get('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount']
            ];

            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    private function getDataRujukan()
    {
        try {
            $response = $this->_restRad->get('lap-pasien-rujukan-rad/get-options');
            $body = json_decode($response->getBody(), true);

            $result = $body['response']['data'];

            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDataPemeriksaan($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $id = DocoHelpers::decrypt($id);
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['id'] = $id;
        $draw = $request->get('draw', 1);
        $data = [];
        try {
            $response = $this->_restRad->get('lap-pasien-rujukan-rad/get-pemeriksaan-view', [
                'form_params' => [],
                'query' => $yiiRestfulParams
            ]);

            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);

            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pasienkirimkeunitlain_id']);
                $value['primary'] = $primaryKey;
                unset($value['pasienkirimkeunitlain_id']);
                $value['rowNum'] = $no;
                $value['is_checkbox'] = '';
                if($value['is_cyto']){
                    $value['is_checkbox'] = '<input type="checkbox" class="checkbox" checked disabled >';
                }
                $data[$key] = $value;
            }

            $return = [
                'data' => $data,
                'draw' => $request->get('draw'),
                'recordsTotal' => $body['response']['_meta']['totalCount'],
                'recordsFiltered' => $body['response']['_meta']['totalCount']
            ];

            return DocoHelpers::response($return);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDokter($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];
        try {
            $response = $this->_restRad->get('lap-pasien-rujukan-rad/get-dokter?advanced-filter[nama_pegawai]=' . $q);
            $body = json_decode($response->getBody(), true);
            $temp_dokter = array(); // array dokter temp
            foreach ($body['response']['data'] as $value)
                if(!array_key_exists($value['pegawai_id'],$temp_dokter)){
                    $result['results'][] = [
                        'id' => $value['nama_pegawai'],
                        'text' => $value['nama_pegawai']
                    ];
                    $temp_dokter[$value['pegawai_id']] = $value['nama_pegawai'];
                }
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetDokterRuangan($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];
        $ruanganid = Yii::$app->docoVars->workspace("ruangan_id"); 
        try {
            $response = $this->_restRad->get('lap-pasien-rujukan-rad/get-dokter?advanced-filter[nama_pegawai]=' . $q. '&advanced-filter[ruangan_id]='.$ruanganid);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response']['data'] as $value)
                $result['results'][] = [
                'id' => $value['pegawai_id'],
                'text' => $value['nama_pegawai']
            ];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionGetPegawaiRuangan($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;

        $result = [];
        $result['results'] = [];
        $ruanganid = Yii::$app->docoVars->workspace("ruangan_id");
        try {
            $response = $this->_restRad->get('allow/data-pegawai?advanced-filter[nama_pegawai]=' . $q . '&advanced-filter[ruangan_id]=' . $ruanganid);
            $body = json_decode($response->getBody(), true);
            foreach ($body['response']['data'] as $value)
                $result['results'][] = [
                'id' => $value['pegawai_id'],
                'text' => $value['nama_pegawai']
            ];
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
        // Convert to json format
        Yii::$app->response->format = Response::FORMAT_JSON;

        // Get request
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());

        // Try catch
        try {
            $path = Yii::getAlias("@download") . "/laporan-pasien-rujukan-radiologi.xlsx";
            $response = $this->_restRad->get('lap-pasien-rujukan-rad/export-excel',[
                'query' => $yiiRestfulParams,
                'save_to' => $path,
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            // Get message
            $result['error'] = $e->getMessage();

            // Return
            return $result;
        } catch (RequestException $e) {
            // Get message
            $result['error'] = $e->getMessage();

            // Return
            return $result;
        }
    }

    public function actionExportPdf(){
        // Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        try {
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $path = Yii::getAlias("@download") . "/laporan-pasien-rujukan-radiologi.pdf";
            $response = $this->_restRad->get('lap-pasien-rujukan-rad/export-pdf?' . http_build_query($yiiRestfulParams), [
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            // return $path;
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }
    
    public function actionFilters() 
    {
        $request = Yii::$app->request;
        $term = $request->get('term', null);
        $type = $request->get('type', null);
        $page = $request->get('page', 1);
        $additionalPayload = $request->get('additionalPayload', []);
        $response = $this->guzzleExec($this->_restRad, [
            'url' => 'lap-pasien-rujukan-rad/filters',
            'payload' => [
                'query' => [
                    'term' => $term,
                    'type' => $type,
                    'page' => $page,
                    'additionalPayload' => $additionalPayload
                ]
            ],
        ]);
        return $this->responseJson(200, 'Data berhasil diambil!', $response);
    }

    public function actionShowPopupExcel()
    {
        $title = 'Laporan Pasien Rujukan Radiologi';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['jenis_rujukan_nama'] = $request->get('jenis_rujukan_nama', null);
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modal_excel', get_defined_vars());
    }

    public function actionProcessSyncExcel($randString)
    {   
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        $session['randString'] = $randString;
        return $this->guzzleExec($this->_restRad, [
            'url' => "lap-pasien-rujukan-rad/export-excel-bgprocess",
            'payload' => [
                'query' => $session
            ],
        ]);
    }

    public function actionDownloadExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('fileName', null);
        $fileDownloads = 'Laporan Pasien Rujukan Radiologi '. date("dmY") .'.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restRad->get('lap-pasien-rujukan-rad/download-file-excel', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }

    public function actionShowPopupPdf()
    {
        $title = 'Laporan Pasien Rujukan Radiologi';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['advanced-filter']['jenis_rujukan_nama'] = $request->get('jenis_rujukan_nama', null);
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modal_pdf', get_defined_vars());
    }

    public function actionProcessSyncPdf($randString)
    {   
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        $session['randString'] = $randString;
        return $this->guzzleExec($this->_restRad, [
            'url' => "lap-pasien-rujukan-rad/export-pdf-bgprocess",
            'payload' => [
                'query' => $session
            ],
        ]);
    }

    public function actionDownloadPdf()
    {
        $request = Yii::$app->request;
        $filename = $request->get('fileName', null);
        $fileDownloads = 'Laporan Pasien Rujukan Radiologi '. date("dmY") .'.pdf';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restRad->get('lap-pasien-rujukan-rad/download-file-pdf', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }

    public function actionGetAsalRujukan()
    {
        if (isset($_GET['term']) && !empty($_GET['term'])) {
            $response = $this->_restRad->get('lap-pasien-rujukan-rad/get-asal-rujukan', [
                'query' => [
                    'asal_rujukan' => $_GET['term']
                ],
            ]);
            $body = json_decode($response->getBody(), true);
            $result = ArrayHelper::getValue($body, 'response', []);

            return DocoHelpers::response([
                'result' => $result, 
                'total_count' => count($result),
                'incomplete_results' => false
            ]);
        }
    }

    public function actionGetRsRujukan()
    {
        if (isset($_GET['term']) && !empty($_GET['term'])) {
            $response = $this->_restRad->get('lap-pasien-rujukan-rad/get-rujukan-keluar', [
                'query' => [
                    'rs_rujukan' => $_GET['term']
                ],
            ]);
            $body = json_decode($response->getBody(), true);
            $result = ArrayHelper::getValue($body, 'response', []);

            return DocoHelpers::response([
                'result' => $result, 

                'total_count' => count($result),
                'incomplete_results' => false
            ]);
        }
    }
}
