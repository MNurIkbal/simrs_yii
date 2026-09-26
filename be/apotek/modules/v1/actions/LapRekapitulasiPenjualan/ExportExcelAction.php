<?php

namespace app\modules\v1\actions\LapRekapitulasiPenjualan;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanRekapitulasiPenjualanView;

class ExportExcelAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $model = new LaporanRekapitulasiPenjualanView;
        $query = $model::find(true);
        $this->controller->dateFilter($query, $request);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $title = 'Laporan Rekapitulasi Penjualan Farmasi';
        $advanced_filter = $request->get('advanced-filter');
        if(!is_null($advanced_filter)) {
            $header = $model->setHeaderExcel($advanced_filter);
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
                    'formatCode' => 'date',
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
