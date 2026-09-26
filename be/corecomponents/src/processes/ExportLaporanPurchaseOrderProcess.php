<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * Powered by Sirs
 */

namespace Doco\processes;

use Yii;
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanAllPOView;

class ExportLaporanPurchaseOrderProcess extends \Doco\components\DocoBaseProcessExtension
{
    private function getSupplierName($rowDatas)
    {
        // Langsung inject ke index 0 (Karena semua datanya pasti sama. Kalau di filter)
        $supplierName = ArrayHelper::getValue($rowDatas, '0.supplier_name');
        return $supplierName;
    }

    protected function exportExcel()
    {
        try {
            $request = Yii::$app->request;
            $advanced_filter = $request->get('advanced-filter');
            $model = new LaporanAllPOView;
            $query = $model::find();
            if(count($advanced_filter) > 0) {
                $this->dateFilter($query, $request);
            }

            $tglpodefault = ArrayHelper::getValue($request->get(),'advanced-filter.tanggal_po',null);
            if(is_null($tglpodefault)){
                $start = date('Y-m-d 00:00:00');
                $end = date('Y-m-d 23:59:59');
                $query->andWhere(['between', 'tanggal_po', $start, $end]);
            }

            $tipe = $request->get('tipe','OBAT');
            $query->andWhere(['type'=>strtoupper($tipe)]);
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $asArrayRows = $query->asArray()->all();
            if(isset($advanced_filter['supplier_id'])){
                $advanced_filter['supplier_name'] = $this->getSupplierName($asArrayRows);
            }
            $advancedFilterHeaderNullable = [
                'no_po' => ArrayHelper::getValue($advanced_filter, 'no_po', '-'),
                'item_name' => ArrayHelper::getValue($advanced_filter, 'item_name', '-'),
                'status_po' => ArrayHelper::getValue($advanced_filter, 'status_po', '-'),
                'supplier_name' => ArrayHelper::getValue($advanced_filter, 'supplier_name', '-'),
            ];
            $header = $model->setHeaderExcel($advancedFilterHeaderNullable);
            $title = "Laporan Purchase Order " . $tipe;
            $options = [
                "titleStyle" => [
                    "fontSize" => 11,
                    "alignment" => "left"
                ],
                "customFormatCode" => [
                    [
                        'selectColumn' => 'B',
                        'formatCode' => 'general'
                    ],
                    [
                        'selectColumn' => 'C',
                        'formatCode' => 'datetime'
                    ],
                    [
                        'selectColumn' => 'D',
                        'formatCode' => 'general'
                    ],
                    [
                        'selectColumn' => 'E',
                        'formatCode' => 'datetime'
                    ],
                    [
                        'selectColumn' => 'F',
                        'formatCode' => 'datetime'
                    ],
                    [
                        'selectColumn' => 'G',
                        'formatCode' => 'general'
                    ],
                    [
                        'selectColumn' => 'H',
                        'formatCode' => 'general'
                    ],
                    [
                        'selectColumn' => 'I',
                        'formatCode' => 'general'
                    ],
                    [
                        'selectColumn' => 'J',
                        'formatCode' => 'general'
                    ],
                    [
                        'selectColumn' => 'K',
                        'formatCode' => 'general'
                    ],
                    [
                        'selectColumn' => 'L',
                        'formatCode' => 'number'
                    ],
                    [
                        'selectColumn' => 'M',
                        'formatCode' => 'number'
                    ],
                    [
                        'selectColumn' => 'N',
                        'formatCode' => 'number'
                    ],
                    [
                        'selectColumn' => 'O',
                        'formatCode' => 'general'
                    ],
                    [
                        'selectColumn' => 'P',
                        'formatCode' => 'general'
                    ],
                    [
                        'selectColumn' => 'R',
                        'formatCode' => 'general'
                    ],
                    [
                        'selectColumn' => 'S',
                        'formatCode' => 'number'
                    ],
                    [
                        'selectColumn' => 'T',
                        'formatCode' => 'number'
                    ],
                    [
                        'selectColumn' => 'U',
                        'formatCode' => 'number'
                    ],
                    [
                        'selectColumn' => 'V',
                        'formatCode' => 'number'
                    ],
                    [
                        'selectColumn' => 'W',
                        'formatCode' => 'number'
                    ],
                    [
                        'selectColumn' => 'X',
                        'formatCode' => 'number'
                    ],
                    [
                        'selectColumn' => 'X',
                        'formatCode' => 'general'
                    ],
                    [
                        'selectColumn' => 'Y',
                        'formatCode' => 'general',
                        'alignment' => [
                            'wrapText' => true
                        ]
                    ],
                    [
                        'selectColumn' => 'Z',
                        'formatCode' => 'general',
                        'alignment' => [
                            'wrapText' => true
                        ]
                    ],
                    [
                        'selectColumn' => 'AA',
                        'formatCode' => 'general',
                        'alignment' => [
                            'wrapText' => true
                        ]
                    ],
                    [
                        'selectColumn' => 'AC',
                        'formatCode' => 'datetime'
                    ],
                    [
                        'selectColumn' => 'AE',
                        'formatCode' => 'datetime'
                    ]
                ],
            ];

            $result = $model->mappingDataExcel($query);
            $result = $this->convertWraptedDatas($result);

            $filePath = DocoHelpers::exportExcel($title, $result, $header, $options, [], [], true);
            $filePath->save('php://output');
            die;
        } catch (\Yii\db\Exception $e) {
            return $e->getMessage();
        } catch (\Exception $e){
            return $e->getMessage();
        }
    }

    public function dateFilter($query, $request) {
        $advanced_filter = $request->get('advanced-filter');
        $dateKey = ['tanggal_po'];
        foreach ($advanced_filter as $key => $value) {
            if(in_array($key, $dateKey)) {
                $explode = explode(" - ", $advanced_filter[$key]);
                if(count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                $query->andWhere(['between', $key, $start, $end]);
            }
        }
    }

    private function convertWraptedDatas($datas)
    {
        $maxWord = 5;
        $countWord = 0;
        $fieldNames = ["PO Remarks", "Catatan Internal", "Catatan Eksternal"];

        foreach ($datas as $key => $value) {
            foreach ($value as $keyColumn => $valueColumn) {
                if (!in_array($keyColumn, $fieldNames)) {
                    continue;
                }
                
                $catatanDirty = ArrayHelper::getValue($value, $keyColumn);
                $catatanDirty = preg_replace("/\r|\n/", "", $catatanDirty);
                $catatanDirtyArr = explode(" ", $catatanDirty);
                
                $catatan = "";
                foreach ($catatanDirtyArr as $keyPoRemarks => $valuePoRemarks) {
                    $countWord++;
                    if ($countWord == $maxWord) {
                        $countWord = 0;
                        $catatan .= "$valuePoRemarks\n ";
                    } else {
                        $catatan .= "$valuePoRemarks ";
                    }
                }
                ArrayHelper::setValue($datas, "$key.$keyColumn", $catatan);
            }
        }

        return $datas;
    }

    protected function processFlow()
    {
        return $this->exportExcel();
    }
}
