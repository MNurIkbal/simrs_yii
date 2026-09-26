<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\actions\LapResponTimeAnalisis;

use Yii;
use yii\base\Action;
use GuzzleHttp\Exception\RequestException;
use app\modules\v1\models\LaporanResponTimeAnalisisFn;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoSpout;

class ExportExcelAction extends Action {
    public function run($tipe = 'obat') {
        try {
            $titleType = $tipe == 'obat' ? 'Medis' : 'Non Medis';
            $title = 'Laporan Respon Time Analisis '.$titleType;
            $request = Yii::$app->request;
            $advanced_filter = $request->get('advanced-filter');
            $model = new LaporanResponTimeAnalisisFn;

            $start_date = $request->get('start_date', null);
            $end_date = $request->get('end_date', null);

            if(is_null($start_date) && is_null($end_date)) {
                $start_date = $end_date = date('Y-m-d');
            } else {
                $start_date = date('Y-m-d',strtotime($start_date));
                $end_date = date('Y-m-d',strtotime($end_date));
            }

            $query = $model::getData($tipe, $start_date, $end_date);
            $query = DocoRestActiveFilter::advancedFilter($model,$query);
            $result = $model->toExcel($query);

            $header = array(
                'Period' => (date('d M Y',strtotime($start_date))." - ".date('d M Y',strtotime($end_date)))
            );

            $filePath = DocoSpout::exportExcel($title, $result, $header, [],[],[],true);
            $filePath->close();
            die;
        } catch (\Yii\db\Exception $e) {
            return $e->getMessage();
        } catch (\Exception $e){
            return $e->getMessage();
        }
    }
}