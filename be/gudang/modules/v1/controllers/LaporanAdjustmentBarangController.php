<?php


namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoActiveController;

class LaporanAdjustmentBarangController extends DocoActiveController {
    public $modelClass = '';
    public $namespace = 'app\modules\v1\actions\LaporanAdjustmentBarang';

    public function actions() {
        return [
            'get-data' => $this->namespace . '\GetDataAction',
            'filters' => $this->namespace . '\FiltersAction',
            'export-excel' => $this->namespace . '\ExportExcelAction',
        ];
    }

    public function dateFilter($query, $request)
    {
        $advancedFilter = $request->get('advanced-filter');
        $dateKey = ['tgl_adjusmen'];
        foreach ($advancedFilter as $key => $value) {
            if (in_array($key, $dateKey)) {
                $explode = explode(" - ", $advancedFilter[$key]);
                if (count($explode) == 2) {
                    $start = date('Y-m-d 00:00:00', strtotime($explode[0]));
                    $end = date('Y-m-d 23:59:59', strtotime($explode[1]));
                }
                $query->andWhere(['between', $key, $start, $end]);
            }
        }
    }
}
