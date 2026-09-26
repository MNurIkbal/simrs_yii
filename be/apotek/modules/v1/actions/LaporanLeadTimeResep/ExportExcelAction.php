<?php

namespace app\modules\v1\actions\LaporanLeadTimeResep;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanLeadTimeResepView;

class ExportExcelAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $model = new LaporanLeadTimeResepView;
        $query = $model::find(true);
        $this->controller->dateFilter($query, $request);
        $query = DocoRestActiveFilter::advancedFilter($model, $query);
        $query->orderBy(['tgl_resep' => SORT_ASC, 'no_resep' => SORT_ASC]);
        $title = 'Laporan Lead Time Resep';
        $advanced_filter = $request->get('advanced-filter');

        if(!is_null($advanced_filter)) {
            $header = $model->setHeaderExcel($advanced_filter);
        } else {
            $header = [];
        }

        $result = $model->mappingDataExcel($query);

        $options = [
            "customFormatCode" => [
                [
                    'selectColumn' => 'C',
                    'formatCode' => 'date'
                ],
                [
                    'startRow' => 'F4',
                    'endRow' => 'F' .  (count($result) + 4)
                ],
                [
                    'startRow' => 'H4',
                    'endRow' => 'H' . (count($result) + 4)
                ],
            ],
        ];

        $filePath = DocoHelpers::exportExcel($title, $result, $header, $options, [], [], true);
        $filePath->save('php://output');
        die;
    }
}
