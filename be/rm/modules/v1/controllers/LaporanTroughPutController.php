<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;

use app\modules\v1\models\LaporanThruputFn;

use Doco\components\DocoHelpers;
use yii\data\ArrayDataProvider;
use Doco\Services\InternalService;
use app\modules\v1\models\UploadForm;
use yii\web\UploadedFile;
use app\modules\v1\payload\UploadPayload;



class LaporanTroughPutController extends DocoActiveController
{
    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        return $actions;
    }

    public function actionExportExcel($month, $year)
    {
        $dom = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        $start_date = $year .'-'. $month .'-01';
        $end_date = $year .'-'. $month .'-'. $dom;

        $start_periode = DocoHelpers::convDateTime(date('Y-m-d h:i:s', strtotime($start_date)), false, false);
        $end_periode = DocoHelpers::convDateTime(date('Y-m-d h:i:s', strtotime($end_date)), false, false);

        $periode = $start_periode . ' - ' . $end_periode;

        $data = LaporanThruputFn::getData($start_date, $end_date);

        usort($data, function($a, $b){
            return $a['urutan'] - $b['urutan'];
        });

        try{
            $title = 'DAILY THRUPUT REPORT';
            $result = $this->listData($data, $dom);
            $header = [
                "Periode" => $periode
            ];
            $options = [
                "skipIncrement" => true,
                "customFormatCode" => [
                    [
                        'startRow' => 'B13',
                        'endRow' => 'AG13'
                    ],
                    [
                        'startRow' => 'B15',
                        'endRow' => 'AG15'
                    ],
                    [
                        'startRow' => 'B19',
                        'endRow' => 'AG19'
                    ],
                ],
            ];

            $filePath = DocoHelpers::exportExcel($title, $result, $header, $options, [], [], true);
            $filePath->save('php://output');
            die;
        } catch (RequestException $e) {
            $result['error'] = $e->getMessage();
            return $result;
        } catch (\Exception $e) {
            $result['error'] = $e->getMessage();
            return $result;
        }
    }

    private function listData($data, $dom)
    {
        $listData = [];

        $arrAssoc = $this->transposeData($data);
        $arrFiller = [];

        $arrFiller = [];

        for ($i=1; $i <= $dom; $i++) { 
            $arrFiller[$i] = "";
        }

        $arrFiller = $arrFiller + ["total" => ""];

        foreach ($arrAssoc as $key => $value) {
            $listData[] = ['thruput' => $key] + $arrFiller;

            $list = $this->createList($value, $listData, $arrFiller);
            $listData = $listData + $list;

            $totalName = "Total " . $key;

            $totalArr = [
                'thruput' => $totalName
            ];

            if(isset($value['total']) && !empty($value['total']) && isset($totalName)) {
                $totalVisit = 0;

                foreach ($value['total']['data'] as $item) {
                    $arrFiller[array_keys($item)[0]] += array_values($item)[0];
                    $totalVisit += array_values($item)[0];
                }

                $arrFiller["total"] = $totalVisit;
            }

            $totalArr = $totalArr + $arrFiller;

            array_push($listData, $totalArr);

            $arrFiller = $this->arrFillerReset($arrFiller);
        }

        return $listData;
    }

    private function arrFillerReset($arrFiller)
    {
        foreach ($arrFiller as $key => $value) {
            $arrFiller[$key] = "";
        }

        return $arrFiller;
    }

    private function createList($array, $listData, $arrFiller)
    {
        foreach ($array as $key => $value) {
            if($key == 'data' || $key == 'total') {
                continue;
            }

            if(
                in_array('subtotal', array_keys($value)) &&
                array_keys($value)[count($value)-1] != 'subtotal'
            ) {
                $subtotal = ['subtotal' => array_shift($value)];
                $value = $value + $subtotal;
            }
 
            if(isset($value['data']) && !empty($value['data'])) {
                $rightTotal = 0;
                foreach ($value['data'] as $item) {
                    $arrFiller[array_keys($item)[0]] += array_values($item)[0];
                    $rightTotal += array_values($item)[0];
                }

                $arrFiller["total"] = $rightTotal;
            }

            $listData[] = ['thruput' => $key] + $arrFiller;
            
            if(is_array($value)) {
                $newArr = $this->createList($value, $listData, $arrFiller);
                $listData = $listData + $newArr;
            }

            $arrFiller = $this->arrFillerReset($arrFiller);
        }

        return $listData;
    }

    private function transposeData($data)
    {
        $arrAssoc = [];

        foreach ($data as $item) {
            if(!isset($item['tipe']) || !isset($item['unit'])) {
                continue;
            }

            if(!isset($arrAssoc[$item['tipe']][$item['unit']])) {
                $arrAssoc[$item['tipe']][$item['unit']] = [];
            }

            if(
                isset($arrAssoc[$item['tipe']]) &&
                !isset($arrAssoc[$item['tipe']]['total'])
            ) {
                $arrAssoc[$item['tipe']]['total']['data'] = [];
            }

            if(is_null($item['unit_thruput'])) {
                $dateOrigin = explode('-', $item['tanggal']);
                $dateIndex = (int)$dateOrigin['2'];

                $newArr = [
                    $dateIndex => $item['total']
                ];

                if(!isset($arrAssoc[$item['tipe']][$item['unit']]['data'])) {
                    $arrAssoc[$item['tipe']][$item['unit']]['data'] = [];
                }

                array_push($arrAssoc[$item['tipe']][$item['unit']]['data'], $newArr);
            }

            if(
                isset($item['unit_thruput']) && 
                !empty($item['unit_thruput']) && 
                !isset($arrAssoc[$item['tipe']][$item['unit']][$item['unit_thruput']])
            ) {
                $arrAssoc[$item['tipe']][$item['unit']][$item['unit_thruput']] = [];
            }

            if(is_null($item['detail_thruput'])) {
                $dateOrigin = explode('-', $item['tanggal']);
                $dateIndex = (int)$dateOrigin['2'];
                
                $newArr = [
                    $dateIndex => $item['total']
                ];

                if(
                    isset($arrAssoc[$item['tipe']][$item['unit']][$item['unit_thruput']]) &&
                    !isset($arrAssoc[$item['tipe']][$item['unit']][$item['unit_thruput']]['data'])
                ) {
                    $arrAssoc[$item['tipe']][$item['unit']][$item['unit_thruput']]['data'] = [];
                }

                if(isset($arrAssoc[$item['tipe']][$item['unit']][$item['unit_thruput']])) {
                    array_push($arrAssoc[$item['tipe']][$item['unit']][$item['unit_thruput']]['data'], $newArr);
                }
            }

            if(
                !is_null($item['detail_thruput']) &&
                isset($arrAssoc[$item['tipe']][$item['unit']][$item['unit_thruput']]) &&
                !isset($arrAssoc[$item['tipe']][$item['unit']][$item['unit_thruput']]['subtotal'])
            ) {
                $arrAssoc[$item['tipe']][$item['unit']][$item['unit_thruput']]['subtotal']['data'] = [];
            }

            if(
                isset($item['detail_thruput']) && 
                !empty($item['detail_thruput']) && 
                !isset($arrAssoc[$item['tipe']][$item['unit']][$item['unit_thruput']][$item['detail_thruput']])
            ) {
                $arrAssoc[$item['tipe']][$item['unit']][$item['unit_thruput']][$item['detail_thruput']] = [];
            }

            $dateOrigin = explode('-', $item['tanggal']);
            $dateIndex = (int)$dateOrigin['2'];
            
            $newArr = [
                $dateIndex => $item['total']
            ];

            array_push(
                $arrAssoc[$item['tipe']]['total']['data'], 
                $newArr
            );

            if(
                isset($arrAssoc[$item['tipe']][$item['unit']][$item['unit_thruput']][$item['detail_thruput']]) &&
                !isset($arrAssoc[$item['tipe']][$item['unit']][$item['unit_thruput']][$item['detail_thruput']]['data'])
            ) {
                $arrAssoc[$item['tipe']][$item['unit']][$item['unit_thruput']][$item['detail_thruput']]['data'] = [];
            }

            if(isset($arrAssoc[$item['tipe']][$item['unit']][$item['unit_thruput']][$item['detail_thruput']])) {
                array_push(
                    $arrAssoc[$item['tipe']][$item['unit']][$item['unit_thruput']][$item['detail_thruput']]['data'], 
                    $newArr
                );

                array_push(
                    $arrAssoc[$item['tipe']][$item['unit']][$item['unit_thruput']]['subtotal']['data'], 
                    $newArr
                );
            }
        }

        return $arrAssoc;
    }

    public function actionSyncExportExcel() 
    {
        $request = Yii::$app->request;
        $getData = $request->get();
        $xOwner = $request->getHeaders()->get('X-Owner');
        $auth = $request->getHeaders()->get('Authorization');
        
        if (isset($getData['page'])) unset($getData['page']);
        if (isset($getData['per-page'])) unset($getData['per-page']);
        
        $countData = 100;
        $randString = isset($getData['randString']) ? $getData['randString'] : null;
        $month = isset($getData['month']) ? $getData['month'] : null;
        $year = isset($getData['year']) ? $getData['year'] : null;
        $totalPerPage = 100;

        (new InternalService)->sendTo([
            'Sirs' => [
                'LaporanTroghputExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'filter' => $getData,
                    'year' => $year,
                    'month' => $month
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'ExportExcelTrouhput' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                    'filter' => $getData,
                    'year' => $year,
                    'month' => $month
                ]
            ]
        ], true);

        (new InternalService)->sendTo([
            'Sirs' => [ 
                'UploadLaporanTroghputExcel' => [
                    'token' => $auth,
                    'xOwner' => $xOwner,
                    'unique_str' => $randString,
                    'totalPerPage' => $totalPerPage,
                    'countData' => $countData,
                ]
            ]
        ], true);
        
        return [
            'totalPerPage' => $totalPerPage,
            'randString' => $randString,
            'countData' => $countData,
        ];
    }

    public function actionDropFile()
    {
        $request = Yii::$app->request;
        $model = new UploadForm;
        
        $filePath = $request->get('filePath', null);
        if ($request->isPost) 
        {
            $files = UploadedFile::getInstanceByName('file');
            $fileName = $files->getBaseName();
            $ext = $files->getExtension();
            $model->file = $fileName.'.'.$ext;
            
            $path = "uploads/".$filePath;
            if (!file_exists($path)) mkdir($path, 0755, true);

            $nameFile = $path .'/'. $model->file;
            if ($files->saveAs($nameFile)) {
                return [
                    'path' => $path,
                    'message' => 'upload file berhasil!'
                ];
            }
        }
        return [
            'status' => 422,
            'message' => 'upload file gagal!'
        ];
    }


    public function actionDownloadFile()
    {
        $request = Yii::$app->request;
        $no_request = $request->get('no_request', null);
        $rootPath = './uploads';
        $dir = $rootPath.'/'.$no_request;
        $fileName = $dir.'/Laporan Troughput.xlsx';

        if (file_exists($fileName)) 
        {
            $file = basename($fileName);
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header("Content-Disposition: inline; filename=$file");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            ob_clean();
            flush();
            readfile($fileName);
            die();
        }
    }

}
