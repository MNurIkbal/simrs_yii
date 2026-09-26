<?php
/**
 * @author: [Budi][budi@sirs.co.id]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\kasir\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LaporanRevenueController extends DocoController
{
    const COVID_PCR = '*     PCR_COVID19';
    const COVID_NONPCR = '*     NON_PCR';
    protected $_title = "Laporan Revenue";
    protected $_module = 'kasir/laporan-revenue/';
    protected $_restKasir;

    public function init()
    {
        parent::init();
        $this->_restKasir = Yii::$app->docoRest->kasir;
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
        $title = $this->_title;
        $kategori = $this->getKategori();
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
        $yiiRestfulParams['jenis_periode'] = $request->get('jenis_periode', "date_range");
        $yiiRestfulParams['range_tanggal'] = $request->get('range_tanggal', null);
        $yiiRestfulParams['range_bulan'] = $request->get('range_bulan', null);
        $yiiRestfulParams['kategori'] = $request->get('kategori', null);
        $yiiRestfulParams['unit'] = $request->get('unit', null);
        try {
            $response = $this->_restKasir->get('laporan-revenue/index?'.http_build_query($yiiRestfulParams));
            $body = json_decode($response->getBody(), True);
            $responseData = isset($body['response']) ? $body['response'] : [];
            $newData = $this->mappingData($responseData);
            $result['data'] = $newData['data'];
            $result['recordsTotal'] = $newData['count'];
            $result['recordsFiltered'] = $newData['count'];
            return $result;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    private function getHeader()
    {
        return [
            [
                'code' => '',
                'title' => 'REVENUE PER LINE',
                'is_group' => true,
                'is_kategori' => false,
                'is_total' => false,
                'parent' => ''
            ],
            [
                'code' => 'LOB',
                'title' => 'LINE OF BUSINESS (LOB)',
                'is_group' => true,
                'is_kategori' => true,
                'is_total' => false,
                'parent' => ''
            ],
            [
                'code' => 'OPD',
                'title' => 'OPD',
                'is_group' => false,
                'is_kategori' => false,
                'is_total' => false,
                'parent' => 'LOB'
            ],
            [
                'code' => 'IPD',
                'title' => 'IPD',
                'is_group' => false,
                'is_kategori' => false,
                'is_total' => false,
                'parent' => 'LOB'
            ],
            [
                'code' => 'EMERGENCY',
                'title' => 'EMERGENCY',
                'is_group' => false,
                'is_kategori' => false,
                'is_total' => false,
                'parent' => 'LOB'
            ],
            [
                'code' => 'MCU',
                'title' => 'MCU',
                'is_group' => false,
                'is_kategori' => false,
                'is_total' => false,
                'parent' => 'LOB'
            ],
            [
                'code' => 'total_lob',
                'title' => 'TOTAL GOR OF LOB',
                'is_group' => true,
                'is_kategori' => false,
                'is_total' => true,
                'parent' => 'LOB'
            ],
            [
                'code' => 'LOS',
                'title' => 'LINE OF SERVICES (LOS)',
                'is_group' => true,
                'is_kategori' => true,
                'is_total' => false,
                'parent' => ''
            ],
            [
                'code' => 'PHARMACY',
                'title' => 'PHARMACY',
                'is_group' => false,
                'is_kategori' => false,
                'is_total' => false,
                'parent' => 'LOS'
            ],
            [
                'code' => 'LABORATORY',
                'title' => 'LABORATORY',
                'is_group' => false,
                'is_kategori' => false,
                'is_total' => false,
                'parent' => 'LOS'
            ],
            [
                'code' => self::COVID_NONPCR,
                'title' => 'NON PCR',
                'is_group' => false,
                'is_kategori' => false,
                'is_total' => false,
                'parent' => 'LOS'
            ],
            [
                'code' => self::COVID_PCR,
                'title' => 'PCR - COVID19',
                'is_group' => false,
                'is_kategori' => false,
                'is_total' => false,
                'parent' => 'LOS'
            ],
            [
                'code' => 'RADIOLOGY',
                'title' => 'RADIOLOGY',
                'is_group' => false,
                'is_kategori' => false,
                'is_total' => false,
                'parent' => 'LOS'
            ],
            [
                'code' => 'HAEMODIALYSIS',
                'title' => 'HAEMODIALYSIS',
                'is_group' => false,
                'is_kategori' => false,
                'is_total' => false,
                'parent' => 'LOS'
            ],
            [
                'code' => 'MEDICAL_REHABILITATION',
                'title' => 'MEDICAL REHABILITATION',
                'is_group' => false,
                'is_kategori' => false,
                'is_total' => false,
                'parent' => 'LOS'
            ],
            [
                'code' => 'DIAGNOSTIC',
                'title' => 'DIAGNOSTIC',
                'is_group' => false,
                'is_kategori' => false,
                'is_total' => false,
                'parent' => 'LOS'
            ],
            [
                'code' => 'total_los',
                'title' => 'TOTAL GOR OF LOS',
                'is_group' => true,
                'is_kategori' => false,
                'is_total' => true,
                'parent' => 'LOS'
            ],
            [
                'code' => 'discount',
                'title' => 'DISCOUNT',
                'is_group' => true,
                'is_kategori' => false,
                'is_total' => false,
                'parent' => ''
            ],
            [
                'code' => 'total_revenue',
                'title' => 'TOTAL GROSS OF REVENUE',
                'is_group' => true,
                'is_kategori' => false,
                'is_total' => true,
                'parent' => ''
            ],
            [
                'code' => 'PAYER',
                'title' => 'REVENUE PER PAYER',
                'is_group' => true,
                'is_kategori' => true,
                'is_total' => false,
                'parent' => ''
            ],
            [
                'code' => 'PRIVATE',
                'title' => 'PRIVATE',
                'is_group' => false,
                'is_kategori' => false,
                'is_total' => false,
                'parent' => 'PAYER'
            ],
            [
                'code' => 'CORPORATE',
                'title' => 'CORPORATE',
                'is_group' => false,
                'is_kategori' => false,
                'is_total' => false,
                'parent' => 'PAYER'
            ],
            [
                'code' => 'INSURANCE',
                'title' => 'INSURANCE',
                'is_group' => false,
                'is_kategori' => false,
                'is_total' => false,
                'parent' => 'PAYER'
            ],
            [
                'code' => 'BPJS KESEHATAN',
                'title' => 'BPJS KESEHATAN',
                'is_group' => false,
                'is_kategori' => false,
                'is_total' => false,
                'parent' => 'PAYER'
            ],
            [
                'code' => 'GOVERNMENT',
                'title' => 'GOVERNMENT',
                'is_group' => false,
                'is_kategori' => false,
                'is_total' => false,
                'parent' => 'PAYER'
            ],
            [
                'code' => 'EMPLOYEE',
                'title' => 'EMPLOYEE',
                'is_group' => false,
                'is_kategori' => false,
                'is_total' => false,
                'parent' => 'PAYER'
            ],
            [
                'code' => 'OTHERS',
                'title' => 'OTHERS',
                'is_group' => false,
                'is_kategori' => false,
                'is_total' => false,
                'parent' => 'PAYER'
            ],
            [
                'code' => 'total_payer',
                'title' => 'TOTAL GOR OF PAYER',
                'is_group' => true,
                'is_kategori' => false,
                'is_total' => true,
                'parent' => 'PAYER'
            ],
        ];
    }

    private function mappingData($responseData)
    {
        $data = [];
        $dataLob = [];
        $listHeader = $this->getHeader();
        $count = 1;
        foreach ($responseData as $key => $value) {
            $tanggal = $value['tanggal'];
            $exp = explode("-", $tanggal);
            $str = $exp[0].'-'.$exp[1].'-';
            $date = $exp[2];
            if(!empty($value['unit'])) {
                $newData[$value['unit']][$date] = $value['total'];
                if($value['unit'] == self::COVID_NONPCR || $value['unit'] == self::COVID_PCR) {
                    $value['total'] = 0;
                }
                $groupedData[$date][$value['tipe']][$value['unit']] = $value['total'];
            }
        }
        
        foreach ($listHeader as $kHeader => $vHeader) {
            $row['code'] = $vHeader['code'];
            $row['title'] = $vHeader['title'];
            $row['unit'] = $vHeader['title'];
            $row['is_group'] = $vHeader['is_group'];
            $row['is_kategori'] = $vHeader['is_kategori'];
            $row['is_total'] = $vHeader['is_total'];

            for($j=1; $j <= 31; $j++) {
                $k = ($j < 10) ? "0".$j : $j;
                if($vHeader['is_group']) {
                    $total = "";
                    if($vHeader['code'] == "total_lob") {
                        $totalLob = "";
                        if(isset($groupedData[$k][$vHeader['parent']])) {
                            $summaryLob = $groupedData[$k][$vHeader['parent']];
                            foreach ($summaryLob as $vSumLob) {
                                $totalLob += $vSumLob;
                            }
                        }
                        $total = $totalLob; // total LOB
                        $groupedData[$k]['summaryLob'] = $total;
                    }
                    elseif($vHeader['code'] == 'total_los') {
                        $totalLos = "";
                        if(isset($groupedData[$k][$vHeader['parent']])) {
                            $summaryLos = $groupedData[$k][$vHeader['parent']];
                            foreach ($summaryLos as $vSumLos) {
                                $totalLos += $vSumLos;
                            }
                        }
                        $total = $totalLos; // total LOS
                        $groupedData[$k]['summaryLos'] = $total;
                    }
                    elseif($vHeader['code'] == 'discount') {
                        $totalDiscount = 0;
                        $total = $totalDiscount; // discount
                        if($total == 0) {
                            $total = "-";
                        }
                    }
                    elseif($vHeader['code'] == 'total_revenue') {
                        $totalRevenue = (($groupedData[$k]['summaryLob'] + $groupedData[$k]['summaryLos']));
                        $total = $totalRevenue; // total revenue
                        $groupedData[$k]['summaryLobLos'] = $total;
                        if($total == 0) {
                            $total = "-";
                        }
                    }
                    elseif($vHeader['code'] == 'total_payer') {
                        $totalPayer = "";
                        if(isset($groupedData[$k][$vHeader['parent']])) {
                            $summaryPayer = $groupedData[$k][$vHeader['parent']];
                            foreach ($summaryPayer as $vSumPayer) {
                                $totalPayer += $vSumPayer;
                            }
                        }
                        $total = $totalPayer; // total PAYER
                        $groupedData[$k]['summaryPayer'] = $total;
                    }
                }
                else {
                    $total = "-";
                    if(isset($newData[$vHeader['code']][$k])) {
                        $total = $newData[$vHeader['code']][$k];
                    }
                }

                $row[$k] = $total;
            }

            $data[$kHeader] = $row;
            $count++;
        }
        
        return [
            'data' => $data,
            'count' => $count,
        ];
    }

    private function getKategori() 
    {
        return [
            'LOB' => 'LOB',
            'LOS' => 'LOS',
            'PAYER' => 'PAYER',
        ];
    }

    public function actionGetUnitRevenue()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $post = $request->post();
        $tipeKategori = isset($post['depdrop_parents'][0]) ? $post['depdrop_parents'][0] : null;
        $header = $this->getHeader();
        $data = [];
        $result = [];
        $result['output'] = [];
        $result['selected'] = '';
        foreach ($header as $key => $value) {
            if($value['is_kategori']) {
                if($tipeKategori == "LOB") {
                    $child = [
                        'OPD' => 'OPD',
                        'IPD' => 'IPD',
                        'EMERGENCY' => 'EMERGENCY',
                        'MCU' => 'MCU',
                    ];
                }
                elseif($tipeKategori == "LOS") {
                    $child = [
                        'PHARMACY' => 'PHARMACY',
                        'LABORATORY' => 'LABORATORY',
                        self::COVID_NONPCR => 'NON PCR',
                        self::COVID_PCR => 'PCR - COVID19',
                        'RADIOLOGY' => 'RADIOLOGY',
                        'HAEMODIALYSIS' => 'HAEMODIALYSIS',
                        'MEDICAL_REHABILITATION' => 'MEDICAL REHABILITATION',
                        'DIAGNOSTIC' => 'DIAGNOSTIC',
                    ];
                }
                else {
                    $child = [
                        'PRIVATE' => 'PRIVATE',
                        'CORPORATE' => 'CORPORATE',
                        'INSURANCE' => 'INSURANCE',
                        'BPJS KESEHATAN' => 'BPJS KESEHATAN',
                        'GOVERNMENT' => 'GOVERNMENT',
                        'EMPLOYEE' => 'EMPLOYEE',
                        'OTHERS' => 'OTHERS'
                    ];
                }
                $data = $child;
            }
        }
        $result = $data;
        if(!empty($result)) {
            foreach ($result as $key => $value) {
                $result['output'][] = [
                    'id' => $key, 
                    'name' => $value
                ];
            }
        }
        return $result;
    }

    public function actionExportExcel()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $listHeader = $this->getHeader();
        $_GET['listHeader'] = $listHeader;
        try {
            $path = Yii::getAlias("@download") . "/laporan-revenue.xlsx";
            $response = $this->_restKasir->get('laporan-revenue/export-excel', [
                'query' => $request->get(),
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            $response = $body['response'];
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionExportExcelDetail()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $request = Yii::$app->request;
        $listHeader = $this->getHeader();
        $_GET['listHeader'] = $listHeader;
        try {
            $path = Yii::getAlias("@download") . "/laporan-revenue-detail-".date("dmY").".xlsx";
            $response = $this->_restKasir->get('laporan-revenue/export-excel-detail', [
                'query' => $request->get(),
                'save_to' => $path,
            ]);
            $body = json_decode($response->getBody(), true);
            $response = $body['response'];
            return DocoHelpers::downloadFile($path,true);
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (RequestException $e){
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    public function actionShowPopup()
    {
        $title = 'Laporan Excel Revenue Detail';
        $request = Yii::$app->request;
        $randString = DocoHelpers::generateRandomString();
        $get = DocoDatatableHelper::convertToRestfulParams($request->get());
        $get['range_tanggal'] = $request->get('range_tanggal', null);
        $get['start_date'] = $request->get('startDate', null);
        $get['end_date'] = $request->get('endDate', null);
        $get['periode_tanggal'] = $request->get('periode_tanggal', null);
        $get['kategori'] = $request->get('kategori', null);
        $get['unit'] = $request->get('unit', null);
        $get['periode_bulan'] = $request->get('periode_bulan', null);
        Yii::$app->session->setFlash($randString, $get);
        return $this->renderAjax('_modal', get_defined_vars());
    }
    public function actionProcessSync($randString)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $session = Yii::$app->session->getFlash($randString);
        $session['randString'] = $randString;
        return $this->guzzleExec($this->_restKasir, [
            'url' => "laporan-revenue/export-excel-bgprocess",
            'payload' => [
                'query' => $session
            ],
        ]);
    }
    
    public function actionDownloadExcel()
    {
        $request = Yii::$app->request;
        $filename = $request->get('fileName', null);

        $fileDownloads = 'Laporan Revenue Detail Excel '.date("dmY").'.xlsx';

        $path = Yii::getAlias("@download").'/'.$fileDownloads;
        $response = $this->_restKasir->get('laporan-revenue/download-file', [
            'query' => [
                'no_request' => $filename,
            ],
            'save_to' => $path,
        ]);
        return DocoHelpers::downloadFile($path,true);
    }
}