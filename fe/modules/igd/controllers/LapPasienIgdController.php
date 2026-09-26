<?php

namespace Doco\igd\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class LapPasienIgdController extends DocoController
{
    protected $_restIgd;
    protected $_title;
    protected $_module;


    public function init()
    {
        parent::init();
        $this->_restIgd = Yii::$app->docoRest->igd;
        $this->_module = 'lap-pasien-igd';
        $this->_title = 'Laporan Pasien Rawat Darurat';
    }

    public function actionIndex()
    {
        // try {
            $title = Yii::t('fe', 'Laporan Pasien Rawat Darurat');
            
            $response = $this->_restIgd->get('allow/get-api-laporan-igd');
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            $resMaster = $response['master'];
            $resLookup = $response['lookup'];
            $resDokter = $response['dokter'];
            $resKasuspenyakit = $response['kasuspenyakit'];
            $cara_bayar = isset($resMaster['carabayar']) ? $resMaster['carabayar'] : [];
            $penjamin = isset($resMaster['penjamin']) ? $resMaster['penjamin'] : [];
            $ruangan = isset($resMaster['ruangan']) ? $resMaster['ruangan'] : [];
            $status_periksa = isset($resLookup['status_periksa']) ? $resLookup['status_periksa'] : [];
            $listStatus = ArrayHelper::map($status_periksa, 'lookup_id', 'lookup_name');
            $jenis_kelamin = isset($resLookup['jenis_kelamin']) ? $resLookup['jenis_kelamin'] : [];
            
           
            return $this->render('index', get_defined_vars());
        // } catch (RequestException $e){
        //     throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        // } catch (\Exception $e){
        //     throw new \yii\web\HttpException(400, Yii::t("fe", "Terdapat kesalahan"));
        // }
    }

    // get data here
    public function actionGetData()
    {
        try {
            $request = Yii::$app->request;
            $filter = DocoDatatableHelper::convertToRestfulParams($request->get());
            // echo '<pre>';
            // print_r($filter); exit;
            $response = $this->_restIgd->request('get', 'lap-pasien-igd/index', [
                'query' => $filter
            ]);
            $row = [];
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $primaryKey = DocoHelpers::encrypt($value['pendaftaran_id']);
                $value['primary'] = $primaryKey;
                $value['rowNum'] = $no;
                $value['tgl_pendaftaran'] = date('d-m-Y H:i:s', strtotime($value['tgl_pendaftaran']));
                $row[$key] = $value;
            }
            $return = [
                'data' => $row,
                'draw' => $request->get('draw'),
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

    public function actionExportPdf()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        
        $path = Yii::getAlias("@download") . "/laporan-pasien-igd.pdf";
        try {
            $response = $this->_restIgd->get('lap-pasien-igd/export-pdf?'.http_build_query($yiiRestfulParams),[
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

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        try {
            $path = Yii::getAlias("@download") . "/laporan-pasien-igd.xlsx";
            $response = $this->_restIgd->get('lap-pasien-igd/export-excel?'.http_build_query($yiiRestfulParams),[
                'save_to' => $path
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }


    public function actionGetInstalasi($assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $params = '';
        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
            $params = '?id='.$parent_label;
        }

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restIgd->get('allow/get-instalasi-pasien'. $params);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value)
                $result['output'][] = [
                    'id' => $value['instalasi_id'],
                    'name' => $value['instalasi_nama']
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

    public function actionGetPenjamin($assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $params = '';
        if ($request->post()) {
            $depdrop_parents = $request->post('depdrop_parents');
            $parent_label = $depdrop_parents[0];
            $params = '?id='.$parent_label;
        }

        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restIgd->get('allow/get-penjamin'. $params);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response']['data'] as $value)
                $result['output'][] = [
                    'id' => $value['penjamin_id'],
                    'name' => $value['penjamin_nama']
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

    public function actionGetCarabayar($assign_id = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $depdrop_parents = $request->post('depdrop_parents');
        $parent_label = $depdrop_parents[0];
        $result = [];
        $result['output'] = [];
        $result['selected'] = '';

        try {
            $response = $this->_restIgd->get('allow/get-carabayar?id='.$parent_label);
            $body = json_decode($response->getBody(), True);
            foreach ($body['response'] as $value)
                $result['output'][] = [
                    'id' => $value['carabayar_id'],
                    'name' => $value['carabayar_nama']
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

    public function actionGetDokter()
    {
        $request = Yii::$app->request;
        $term = $request->get('term');
        try {
            $response = $this->_restIgd->get('allow/get-dokter',[
                'query' => [
                    'term' => $term,
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            $data = [];
            foreach ($response as $value) {
                $data[] = [
                    'id' => $value['pegawai_id'],
                    'text' => $value['nama_pegawai'],
                ];
            }
        } catch (RequestException $e) {
            $data = [
                'messages' => $e->getMessage()
            ];
        }

        return DocoHelpers::response([
            'result' => $data
        ]);
    }

    public function actionGetJenisPenyakit()
    {
        $request = Yii::$app->request;
        $term = $request->get('term');
        try {
            $response = $this->_restIgd->get('allow/get-jenis-penyakit',[
                'query' => [
                    'term' => $term,
                ]
            ]);
            $response = json_decode($response->getBody(),true);
            $response = $response['response'];
            $data = [];
            foreach ($response as $value) {
                $data[] = [
                    'id' => $value['jeniskasuspenyakit_id'],
                    'text' => $value['jeniskasuspenyakit_nama'],
                ];
            }
        } catch (RequestException $e) {
            $data = [
                'messages' => $e->getMessage()
            ];
        }

        return DocoHelpers::response([
            'result' => $data
        ]);
    }

    public function actionShowPopupPdf()
    {
        $request = Yii::$app->request;
        $title = 'Cetak ' . $this->_title;
        $randString = DocoHelpers::generateRandomString();
        // $yiiRestfulParams = DocoDatatableHelper::advancedFilterParam();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['randString'] = $randString;
        $url = '/igd/'. $this->_module . '/';
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_pdf', compact('title','url','randString'));
    }

    public function actionShowPopupExcel()
    {
        $title = 'Cetak ' . $this->_title;
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        // $yiiRestfulParams = DocoDatatableHelper::advancedFilterParam();
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $yiiRestfulParams['randString'] = $randString;
        $url = '/igd/'. $this->_module . '/';
        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_excel', compact('title', 'randString', 'url'));
    }

    public function actionProcessSync($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        $session['unique_str'] = $randString;
        return $this->guzzleExec($this->_restIgd, [
            'url' => $this->_module."/export-pdf-new",
            'payload' => [
                'query' => $session
            ],
        ]);
    }

    public function actionProcessSyncExcel()
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restIgd, [
            'url' => $this->_module."/export-excel-new",
            'payload' => [
                'query' => Yii::$app->session->getFlash($randString)
            ],
        ]);
    }

    public function actionDownloadPdf()
    {
        $request = Yii::$app->request;
        $fileName = $request->get('fileName', null);
        $fileNameDownload = 'LAPORAN PASIEN RAWAT DARURAT.pdf';
        $path = Yii::getAlias("@download").'/'.$fileNameDownload;
        $this->guzzleExec($this->_restIgd, [
            'url' => $this->_module."/download-pdf",
            'payload' => [
                'query' => [
                    'fileName' => $fileName,
                ]
            ],
            'save_to' => $path,
        ]);
        return DocoHelpers::previewPdf($path);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $fileDownloads = 'LAPORAN PASIEN RAWAT DARURAT.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restIgd->get($this->_module.'/download-file-excel', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }

    public function actionFilters()
    {
        return $this->guzzleExec($this->_restIgd, [
            'url' => $this->_module.'/filters',
            'payload' => [
                'query' => Yii::$app->request->get()
            ],
            'returnResponse' => true
        ]);
    }

}