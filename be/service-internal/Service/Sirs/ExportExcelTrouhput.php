<?php
namespace Integrasi\Service\Sirs;

use Yii;
use yii\db\Expression;
use yii\db\Query;
use Integrasi\Components\DocoHelpers;

class ExportExcelTrouhput extends \Integrasi\Contracts\DocoImplement
{
    const ROW_IDX_TOT_LOB = 14;

	public function execute()
    {
        ini_set('memory_limit', '-1');
        $totalPerPage = $this->totalPerPage; 
        $cacheFiles = Yii::$app->cacheFiles;
        
        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengekstrak data Pasien',
                'progress' => 80
            ]),
        ]);

        $data = $cacheFiles->get($this->unique_str);
        $cacheFiles->delete($this->unique_str);

        
        $dom = date('t', strtotime(sprintf('%04d-%02d-01', $this->year, $this->month)));

        $start_date = $this->year .'-'. $this->month .'-01';
        $end_date = $this->year .'-'. $this->month .'-'. $dom;

        usort($data, function($a, $b){
            return $a['urutan'] - $b['urutan'];
        });

        $result = $this->listData($data, $dom);
        $start_periode = DocoHelpers::convDateTime(date('Y-m-d h:i:s', strtotime($start_date)), false, false);
        $end_periode = DocoHelpers::convDateTime(date('Y-m-d h:i:s', strtotime($end_date)), false, false);

        $periode = $start_periode . ' - ' . $end_periode;

        $title = 'DAILY THRUPUT REPORT';
        $header = [
            "Periode" => $periode
        ];
        $options = [
            "skipIncrement" => true,
            "customFormatCode" => [
                // [
                //     'startRow' => 'B13',
                //     'endRow' => 'AG13'
                // ],
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

        $jenis = array_column($result, 'thruput');
        // $val = array_search(preg_replace('/\s+/', '', 'Total LOB'), $jenis);

        if(isset($result[self::ROW_IDX_TOT_LOB])) {
            unset($result[self::ROW_IDX_TOT_LOB]);
        }
        $path = 'uploads/'. $this->unique_str .'.xlsx';

        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Sedang mengimport data ke dalam excel',
                'progress' => 85
            ]),
        ]);
        
        $filePath = DocoHelpers::exportExcel($title, $result, $header, $options, [], [], true);
        $filePath->save($path);
        Yii::$app->redis->executeCommand('PUBLISH', [
           'channel' => 'export-excel:'.$this->unique_str,
           'message' => json_encode([
                'status' => 'finish', 
                'messageProcess' => 'Proses import excel berhasil',
                'progress' => 90
            ]),
        ]);


        return json_encode([
            'service' => 'Sirs-ExportExcelTrouhput',
            'payload' => $this->attributes,
            'timestamp' => date('Y-m-d H:i:s'),
        ]);
    }

    private function listData($data, $dom)
    {
        $listData = [];

        $arrAssoc = $this->transposeData($data);
        $arrFiller = [];

        $arrFiller = [];

        for ($i=1; $i <= $dom; $i++) { 
            $arrFiller[$i] = null;
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
            $arrFiller[$key] = null;
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
}
