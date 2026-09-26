<?php

/**
 * @author : Novia Sukma Sari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\actions\LaporanAdjustment;

use Yii;
use yii\base\Action;
use Doco\components\DocoHelpers;
use app\modules\v1\models\LaporanAdjustmentObatAlkesView;
use Doco\components\DocoRestActiveFilter;
use yii\helpers\ArrayHelper;

class ExportExcelAction extends Action
{
    public function run()
    {
        try {
            $title = 'Laporan Adjustment Obat Alkes';
            $request = Yii::$app->request;
            $advanced_filter = $request->get('advanced-filter');
            $model = new LaporanAdjustmentObatAlkesView;
            $query = $model::find();
            if (count($advanced_filter) > 0) {
                $this->controller->dateFilter($query, $request);
            }
            $query = DocoRestActiveFilter::advancedFilter($model, $query);
            $advancedFilterNullable = [
                'tgl_adjusmen' => ArrayHelper::getValue($advanced_filter, 'tgl_adjusmen', '-'),
                'ruangan_nama' => ArrayHelper::getValue($advanced_filter, 'ruangan_nama', '-'),
                'jenis_adjusmen_nama' => ArrayHelper::getValue($advanced_filter, 'jenis_adjusmen_nama', '-'),
                'jenisobatalkes_nama' => ArrayHelper::getValue($advanced_filter, 'jenisobatalkes_nama', '-')
            ];
            $header = $model->setHeaderExcel($advancedFilterNullable);
            $options = [
                "titleStyle" => [
                    "fontSize" => 11,
                    "alignment" => "left"
                ],
                "customFormatCode" => [
                    [
                        'selectColumn' => 'D',
                        'formatCode' => 'date'
                    ]
                ],
            ];

            $result = $model->mappingDataExcel($query);
            $filePath = DocoHelpers::exportExcel($title, $result, $header, $options, [], [], true);
            $filePath->save('php://output');
            die;
        } catch (\Yii\db\Exception $e) {
            return $e->getMessage();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}
