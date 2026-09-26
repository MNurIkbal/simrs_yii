<?php

/**
 * @Author: Ragnar-Lothbroc
 * @Date:   2018-07-19 16:41:49
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2018-07-19 19:30:25
 */

namespace Doco\laboratorium\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Response;
use yii\helpers\Html;
use yii\helpers\Url;
use GuzzleHttp\Exception\RequestException;
use app\components\DocoController;
use app\components\DocoConstants;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;

class LaporanPasienLabController extends DocoController
{
    protected $_title = "Laporan Pasien Laboratorium";
    protected $_module = '/laboratorium/laporan-pasien-lab';
    protected $_restLab;
    protected $_restKasir;
    protected $_restMaster;
    protected $_typeFiture = "report-patients";


    public function init()
    {
        parent::init();
        $this->_restLab = Yii::$app->docoRest->laboratorium;
        $this->_restKasir = Yii::$app->docoRest->kasir;
        $this->_restMaster = Yii::$app->docoRest->master;
    }

    public function actionIndex()
    {
        $title = $this->_title;

        $cara_bayar = $penjamin = [];

        $status = DocoConstants::$status_lab;
        unset($status[476]);
        try {
            $response = $this->_restKasir->get('penjualan-resep/get-options');
            $body = json_decode($response->getBody(), true);
            $cara_bayar = $body['response']['cara_bayar'];
            $penjamin = $body['response']['penjamin'];
        } catch (RequestException $e) {
            $cara_bayar = $penjamin = [];
        }

        return $this->render('index', get_defined_vars());
    }

    public function actionGetData()
    {
        try {
            $request = Yii::$app->request;
            $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
            $yiiRestfulParams['advanced-filter']['type'] = $this->_typeFiture;
            if ($request->get('ruangan_default') != "") {
                $yiiRestfulParams['advanced-filter']['ruangan_id'] = $request->get('ruangan_default');
            }
            $response = $this->_restLab->request('get', 'inf-pasien-lab/get-data-laporan-pasien-lab?' . http_build_query($yiiRestfulParams), [
                'form_params' => []
            ]);

            $row = [];
            $body = json_decode($response->getBody(), true);
            $no = $request->get('start', 1);
            foreach ($body['response']['data'] as $key => $value) {
                $no++;
                $value['rowNum'] = $no;
                $value['tglmasukpenunjang'] = date('d M Y', strtotime($value['tglmasukpenunjang']));
                $value['status_periksa'] = isset($value['status_periksa']) ? DocoConstants::$status_lab[$value['status_periksa']] : null;
                $value['tglmasukpenunjang'] = DocoHelpers::convertTo224($value['tglmasukpenunjang'], true);
                $value['tanggal_lahir'] = DocoHelpers::convDateTime(date('Y-m-d H:i:s', strtotime($value['tanggal_lahir'])), false, false);
                $value['harga'] = isset($value['harga']) ? number_format($value['harga']) : 0;
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

    private function getAksiListPasien($data)
    {
        $pendaftaran_id = DocoHelpers::encrypt($data['pendaftaran_id']);

        $return_data = '';
        if (!empty($data['no_antrian'])) {
            $return_data .= Html::button(
                '<i class="fa fa-volume-up"></i> ' . $data['no_antrian'],
                [
                    'class' => 'btn btn-turquoise btn-md antrian',
                    'data-tooltip' => "tooltip",
                    'data-original-title' => Yii::t('fe', 'Panggil antrian'),
                    'data-id' => $data['pendaftaran_id'],
                    'data-antrian' => $data['no_antrian'],
                ]
            );
        }
        $return_data .= '&nbsp;&nbsp;';

        return $return_data;
    }

    public function actionGetAsalRujukan2($q = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $get = $request->get();

        $result = [];
        $result['results'] = [];
        try {
            if ($get['asal_1'] == "order") {
                $response = $this->_restLab->get('inf-pasien-lab/list-instalasi');
                $body = json_decode($response->getBody(), true);
                // $temp_dokter = array(); // array dokter temp
                foreach ($body['response'] as $k => $value) {
                    $result['results'][] = [
                        'id' => $k,
                        'text' => $value
                    ];
                }
                return $result;
            }
            if ($get['asal_1'] == "rujukanRS") {
                $response = $this->_restLab->get('inf-pasien-lab/list-asal-rujukan');
                $body = json_decode($response->getBody(), true);
                // $temp_dokter = array(); // array dokter temp
                foreach ($body['response'] as $k => $value) {
                    $result['results'][] = [
                        'id' => $k,
                        'text' => $value
                    ];
                }
                return $result;
            }
            // return $request->get();
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }


    public function actionGetAsalRujukan3($q = "", $q2 = "")
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $get = $request->get();

        $result = [];
        $result['results'] = [];
        try {
            if ($get['asal_1'] == "order") {
                $response = $this->_restLab->get('inf-pasien-lab/list-ruangan?instalasi_id=' . $get['asal_2']);
                $body = json_decode($response->getBody(), true);
                // $temp_dokter = array(); // array dokter temp
                foreach ($body['response'] as $k => $value) {
                    $result['results'][] = [
                        'id' => $k,
                        'text' => $value
                    ];
                }
                return $result;
            }
            if ($get['asal_1'] == "rujukanRS") {
                $response = $this->_restLab->get('inf-pasien-lab/list-asal-rujukan-dari?asalrujukan_id=' . $get['asal_2']);
                $body = json_decode($response->getBody(), true);
                // $temp_dokter = array(); // array dokter temp
                foreach ($body['response'] as $k => $value) {
                    $result['results'][] = [
                        'id' => $k,
                        'text' => $value
                    ];
                }
                return $result;
            }
            // return $request->get();
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionExportPdf()
    {
        $request = Yii::$app->request;
        $yiiRestfulParams = DocoDatatableHelper::convertToRestfulParams($request->get());
        $path = Yii::getAlias("@download") . "/laporan-pasien-lab.pdf";
        try {
            $response = $this->_restLab->get('inf-pasien-lab/cetak-pdf?' . http_build_query($yiiRestfulParams), [
                'save_to' => $path
            ]);
            $body = json_decode($response->getBody(), true);
            // return DocoHelpers::response($body);
            return DocoHelpers::previewPdf($path);
        } catch (RequestException $e) {
            var_dump($e->getMessage());
            exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            var_dump($e->getMessage());
            exit();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionExportExcel()
    {
        $request = Yii::$app->request;
        $data = DocoDatatableHelper::convertToRestfulParams($request->get());

        $result = [];
        $url = "";
        try {
            $path = Yii::getAlias("@download") . "/laporan-pasien-laboratorium.xlsx";
            $response = $this->_restLab->get('inf-pasien-lab/export-excel', [
                'query' => $data,
                'save_to' => $path
            ]);
            return DocoHelpers::downloadFile($path, true);
        } catch (RequestException $e) {
            var_dump($e->getMessage());
            die();
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        } catch (\Exception $e) {
            throw new \yii\web\HttpException(500, 'Terjadi Kesalahan pada server.');
        }
    }

    public function actionGetFilters()
    {
        return $this->guzzleExec($this->_restLab, [
            'url' => 'inf-pasien-lab/get-filters',
            'payload' => [
                'query' => Yii::$app->request->get()
            ],
            'returnResponse' => true
        ]);
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
        header('Content-Disposition: attachment; filename="' . $file . '".xlsx');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        ob_clean();
        flush();
        readfile($file);
        exit;
    }
}
