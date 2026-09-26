<?php
// Author : Ramdhan Nurrachman

namespace Doco\kasir\controllers;

use app\components\DHtml;
use app\components\DocoConstants;
use Yii;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use app\modules\kasir\models\PelayananJasaDokter;
use GuzzleHttp\Exception\RequestException;
use yii\helpers\ArrayHelper;

class LapRekapJasaDokterController extends DocoController
{
    protected $_title = "Laporan Rekapitulasi Jasa Dokter";
    protected $_module = 'kasir/lap-rekap-jasa-dokter/';
    protected $_restKasir; protected $_restMaster;
    protected $_ext;

    public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir; 
        $this->_restMaster = Yii::$app->docoRest->master;
        $this->_ext = Yii::$app->docoPlugin->execute($this, 'laporan_jasa_medis');
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
        $title = DHtml::getTitleMenu();
        $title = !empty($title) ? $title : $this->_title;
        $getDataMaster = $this->_restKasir->get('master-api/get-data-kategori-trx');
        $getDataMaster = json_decode($getDataMaster->getBody(), True);
        $getDataMaster = isset($getDataMaster['response']) ? $getDataMaster['response'] : [];
        $request = $this->_restKasir->get('lap-rekap-jasa-dokter/list-request');
        $request = json_decode($request->getBody(), True);
        $request = isset($request['response']) ? $request['response'] : [];    
        $status_biling = isset($request['get_status_biling']) ? $request['get_status_biling'] : [];
        $ruangan = isset($request['ruangan']) ? $request['ruangan'] : null;
        $cara_bayar = isset($request['cara_bayar']) ? $request['cara_bayar'] : null;
        $penjamin = isset($request['penjamin']) ? $request['penjamin'] : null;
        $pegawai = isset($request['pegawai']) ? $request['pegawai'] : null;
        $pelayanan = isset($request['pelayanan']) ? $request['pelayanan'] : null;
        $kelas_pelayanan = isset($request['kelas_pelayanan']) ? $request['kelas_pelayanan'] : [];
        $dataMaster = ArrayHelper::map($getDataMaster , 'kategoritransaksi_nama', function ($model) {
            return $model['kategoritransaksi_kode'] . '-' . $model['kategoritransaksi_nama'];
        });
        // $status_bayar = array_merge($status_biling, $dataMaster);
        $status_bayar = [
            DocoConstants::STATUS_PERMINTAAN_KONSUL_DEFAULT => '-',
            DocoConstants::STAT_BAYAR_LUNAS => 'Lunas',
            DocoConstants::STAT_BAYAR_BLM_LUNAS => 'Belum Lunas'
        ];
        $status_jasa = [
            1 => 'Sudah Dibayarkan',
            'Belum Dibayarkan'
        ];
        return $this->render($this->_ext['path'], get_defined_vars());
    }

    public function actionGetData()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $type = $request->get('type');
        $payload = DocoDatatableHelper::advancedFilterParam();
        $payload['advanced-filter']['type'] = $type;
        $response = $this->guzzleExec($this->_restKasir, [
            'url' => $this->_ext['endPoint'],
            'payload' => [
                'query' => $payload,
            ]
        ]);
        foreach ($response['data'] as $key => $value) {
            $response['data'][$key]['primary'] = DocoHelpers::encrypt($value['pendaftaran_id']);
            if( strpos($value['no_pendaftaran'], "JD") !== false) {
                $response['data'][$key]['primary'] = DocoHelpers::encrypt($value['pelayananjasadokter_id']);
            }
            $response['data'][$key]['tarif_tindakan'] = isset($value['tarif_tindakan']) ? $value['tarif_tindakan'] : 0;
            $response['data'][$key]['kondisi'] = isset($value['kondisi']) ? $value['kondisi'] : '-';
            $response['data'][$key]['flag_jasdok'] = "-";
            if( $value['status_bayar_id'] != 9999) {
                $response['data'][$key]['flag_jasdok'] = !empty($value['tgl_flag']) ? "Sudah Dibayarkan" : "Belum Dibayarkan";
            }
        }
        $response['recordsTotal'] = $response['_meta']['totalCount'];
        $response['recordsFiltered'] = $response['_meta']['totalCount'];
        return $response;
    }

    public function actionExportExcel()
    {        
        $payload = DocoDatatableHelper::advancedFilterParam();
        try {
            $path = Yii::getAlias("@download") . "/Laporan Rekap Jasa Dokter.xlsx";
            $response = $this->_restKasir->get('lap-rekap-jasa-dokter/export-excel', [
                'query' => $payload,
                'save_to' => $path
            ]);
            return DocoHelpers::downloadFile($path,true);
        } catch (RequestException $e) {
            throw new \yii\web\NotFoundHttpException();
            return $e;
        } catch (\Exception $e) {
            throw new \yii\web\NotFoundHttpException();
            return $e;
        }
    }
    public function actionExportPdf()
    {
        try {
            $filters = DocoDatatableHelper::advancedFilterParam();
            $path = Yii::getAlias("@download") . "/laporan-rekap-jasa-dokter.pdf";
            $response = $this->_restKasir->get('lap-rekap-jasa-dokter/export-pdf', [
                'save_to' => $path,
                'query' => $filters
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return DocoHelpers::response($result);
        }
    }

    public function actionCetakRincianJasdok()
    {
        $filters = DocoDatatableHelper::advancedFilterParam();
        $path = Yii::getAlias("@download") . "/laporan-rincian-jasa-dokter.pdf";
        $response = $this->_restKasir->get('lap-rekap-jasa-dokter/cetak-rincian-jasdok', [
            'save_to' => $path,
            'query' => $filters
        ]);
        $body = json_decode($response->getBody(), true);
        return DocoHelpers::previewPdf($path);
    }

    public function actionGetDataDokter()
    {
        if(isset($_GET['q']['term']) && !empty($_GET['q']['term'])){
            $response = $this->_restKasir->request('POST', 'allow/get-dokter',[
                            'form_params'=>['nama_pegawai'=>$_GET['q']['term']],
                        ]);
            $body = json_decode($response->getBody(), true);
            $data = [];
            foreach ($body['response'] as $key => $value) {
                $data[] = ['id'=>$value['nama_pegawai'],'text'=>$value['nama_pegawai']];
            }
            $total = count($body['response']);
            $return = ['result'=>$data,'total_count'=>$total,'incomplete_results'=>false];
            return DocoHelpers::response($return);
        }
    }
    
    private function downloadFile($filename)
    {
        $file = basename($filename);
        $fp = fopen($file, 'w');
        $ch = curl_init($filename);
        curl_setopt($ch, CURLOPT_FILE, $fp);
        $data = curl_exec($ch);
        curl_close($ch);
        fclose($fp);
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$file.'".xlsx');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        ob_clean();
        flush();
        readfile($file);
        exit;
    }

    /**
     * =====================================
     * 
     * TRANSACTION REKAP JASA DOKTER
     * 
     * =====================================
     */

    public function actionCreate()
    {
        $model = new PelayananJasaDokter();
        $request = Yii::$app->request;
        $model->tgl_transaksi =  date('j M Y');
        $titleform = Yii::t('fe', 'Tambah') .' '. $this->_title;
        if ($post = $request->post()) {
            $model->load($post);
            $action = 'create';
            $response = $this->_restKasir->post("tra-jasa-dokter/create", [
                'form_params' => $model->attributes
            ]);
            $body = json_decode($response->getBody(), true);
            return DocoHelpers::response($body, false, 'PelayananJasaDokter');
        }
        return $this->renderAjax('form', get_defined_vars());
    }
    
    public function actionDelete($id)
    {
        $id = DocoHelpers::decrypt($id);
        try {
            $response = $this->_restKasir->delete('tra-jasa-dokter/delete?id='.$id);
            $response = json_decode($response->getBody(),true);
            $response['response'] = [
                'title' => 'Proses Berhasil !',
                'text' => 'Data berhasil dihapus'
            ];

            return DocoHelpers::response($response);
        } catch (RequestException $e) {
            // Return
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            // Return
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }

    public function actionFilters()
    {
        $response = $this->guzzleExec($this->_restKasir, [
            'url' => 'lap-rekap-jasa-dokter/filters',
            'payload' => [
                'query' => [
                    'term' => Yii::$app->request->get('term'),
                    'type' => Yii::$app->request->get('type'),
                    'page' => Yii::$app->request->get('page', 1),
                ]
            ],
        ]);
        return $this->responseJson(200, 'Data berhasil diambil!', $response);
    }

    public function actionDetail($id,$dokterpenanggungjawab_id)
    {
        $id = DocoHelpers::decrypt($id);
        $response = $this->guzzleExec($this->_restKasir, [
            'url' => 'lap-rekap-jasa-dokter/detail',
            'payload' => [
                'query' => [
                    'id' => $id,
                    'dokter_id' => $dokterpenanggungjawab_id
                ]
            ],
        ]);
        $header = $response;
        $title = "Detail Rekap Pasien ".$header['nama_pasien'];
        return $this->renderAjax("@app/extensions/kasir/views/partial/_detail", get_defined_vars());
    }

    public function actionGetDataDetail()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $id = $request->get('id', null);
        $dokter_id = $request->get('dokter_id', null);
        $payload = DocoDatatableHelper::advancedFilterParam();
        $payload['id'] = $id;
        $payload['dokter_id'] = $dokter_id;
        $response = $this->guzzleExec($this->_restKasir, [
            'url' => "lap-rekap-jasa-dokter/get-data-detail",
            'payload' => [
                'query' => $payload,
            ]
        ]);
        // dump($response);die;
        foreach ($response['data'] as $key => $value) {
            $response['data'][$key]['total'] = ($value['total_jasadokter'] - $value['diskon']);
        }
        $response['recordsTotal'] = $response['_meta']['totalCount'];
        $response['recordsFiltered'] = $response['_meta']['totalCount'];
        return $response;
    }

    public function actionShowPopupExcel()
    {
        $title = 'Download Laporan Rekapitulasi Jasa Dokter';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $yiiRestfulParams = DocoDatatableHelper::advancedFilterParam($request->get());
        $yiiRestfulParams['randString'] = $randString;

        Yii::$app->session->setFlash($randString, $yiiRestfulParams);
        return $this->renderAjax('_modalExcel', get_defined_vars());
    }

    public function actionProcessSyncExcel()
    {
        $request = Yii::$app->request;
        $randString = $request->get('randString');

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $this->guzzleExec($this->_restKasir, [
            'url' => "lap-rekap-jasa-dokter/sync-export-excel",
            'payload' => ['query' => Yii::$app->session->getFlash($randString)],
        ]);
    }

    public function actionDownloadFileExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('filename', null);
        $date = date('dmY');
        $fileDownloads = 'Laporan Rekapitulasi Jasa Dokter '.$date.'.xlsx';
        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restKasir->get('lap-rekap-jasa-dokter/download-file-excel', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);

        return DocoHelpers::downloadFile($path,true);
    }

    public function actionFlagBayar() {
        $request = Yii::$app->request;
        try {

            $response = [];
            $response['response'] = [
                'title' => 'Proses Gagal !',
                'text' => 'Data gagal diproses',
                'status' => 'warning'
            ];

            if($request->post()) {
                $response = $this->_restKasir->post("lap-rekap-jasa-dokter/flag-jasa-dokter", [
                    'form_params' => $request->post("data_flag")
                ]);

                $response = json_decode($response->getBody(),true);
                if( !empty($response["metadata"]) ) {
                    if( $response["metadata"]["status"] == 200 ) {
                        $response['response'] = [
                            'title' => 'Proses Berhasil !',
                            'text' => 'Data berhasil diproses',
                            'status' => 'success'
                        ];
                    }
                }
            }

            return DocoHelpers::response($response);

        } catch (RequestException $e) {
            // Return
            return DocoHelpers::responseJsonString($e->getResponse()->getBody()->getContents(), true);
        } catch (\Exception $e) {
            // Return
            return DocoHelpers::responseTemplate(500, $e->getMessage());
        }
    }
}
