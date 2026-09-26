<?php

/**
 * @author : Muhamad Lukman Hakim (hakim.lukman@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\LapPemakaianBmhpPasien;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LaporanPemakaianBmhpPasienView;

class ExportExcelAction extends Action {
    public function run() {
        $request = Yii::$app->request;
        $model = new LaporanPemakaianBmhpPasienView;
        $query = $model::find(true);
        $model->daterangeFilter($query, $request, 'tgl_transaksi');
        $query = DocoRestActiveFilter::advancedFilter($model, $query);

        $advanced_filter = $request->get('advanced-filter');
        $title = 'Laporan Pemakaian BMHP Pasien';

        if(!is_null($advanced_filter)) {
            $header = $model->setHeaderExcel($advanced_filter);
        } else {
            $header = [];
        }

        $result = $model->mappingDataExcel($query);

        $filePath = DocoHelpers::exportExcel($title, $result, $header, [], [], [], true);
        $filePath->save('php://output');
        die;
    }
}
