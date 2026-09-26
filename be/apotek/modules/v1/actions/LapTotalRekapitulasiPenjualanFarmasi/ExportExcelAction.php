<?php

namespace app\modules\v1\actions\LapTotalRekapitulasiPenjualanFarmasi;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanRekapPenjualanFarmasiFn;

class ExportExcelAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $advanced_filter = $request->get('advanced-filter');
        
        $start = date('Y-m-d 00:00:00');
        $end   = date('Y-m-d 23:59:59');

        if(isset($advanced_filter) && isset($advanced_filter['tgl_pelayanan'])) {
            $explode = explode(" - ", $advanced_filter['tgl_pelayanan']);
            if(count($explode) == 2) {
                $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
            }
        }

        $model = new LaporanRekapPenjualanFarmasiFn;
        $query = $model::getData($start, $end);

        if(isset($advanced_filter['jenisobatalkes_nama'])){
            $jenisobatalkes_id = $advanced_filter['jenisobatalkes_nama'];
            $query->andWhere(['jenisobatalkes_id' => $jenisobatalkes_id]);
        }

        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        $title = 'Laporan Total Rekapitulasi Penjualan Farmasi';
        
        if(!is_null($advanced_filter)) {
            if(!isset($advanced_filter['tgl_pelayanan'])){
                $advanced_filter['tgl_pelayanan'] = date('d-M-Y').' - '.date('d-M-Y');
            }
            $header = ["Periode Pelayanan" => $advanced_filter['tgl_pelayanan']] + $model->setHeaderExcel($advanced_filter);
        } else {
            $header = [];
        }

        $result = $model->mappingDataExcel($query);

        $options = [
            "titleStyle" => [
                "fontSize" => 11,
                "alignment" => "left"
            ],
            "customFormatCode" => [
                [
                    'selectColumn' => 'B',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'C',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'D',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'E',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'F',
                    'formatCode' => 'general',
                ],
                [
                    'selectColumn' => 'G',
                    'formatCode' => 'number',
                ],
                [
                    'selectColumn' => 'H',
                    'formatCode' => 'number',
                ],
            ],
        ];

        $filePath = DocoHelpers::exportExcel($title, $result, $header, $options, [], [], true);
        $filePath->save('php://output');
        die;
    }
}
