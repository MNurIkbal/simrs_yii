<?php

namespace app\modules\v1\controllers;

use Yii;
use Doco\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\LapRekapTindakanRadView;


class LapRekapRadiologiController extends DocoActiveController
{
   protected $allowAction = [ '*' ];
   public $modelClass = '';
   public $_endpoint = 'lap-rekap-radiologi/';

   public function actions() 
   {
      $path = 'app\modules\v1\actions\LapRekapRadiologi';
      return [
         'index' => $path . '\IndexAction',
         'unduh-file' => $path . '\UnduhFileAction',
         'drop-file' => $path . '\DropFileAction',
         'download-file' => $path . '\DownloadFileAction',
         'filters' => $path . '\FiltersAction',
      ];
   }

   public function dateFilter($request)
   {
      $advancedFilter = $request->get('advanced-filter');
      $model = new LapRekapTindakanRadView;
      $query = $model::find();
      $start = date('Y-m-d');
      $end = date('Y-m-d');
      if(isset($advancedFilter['tgl_persetujuan']) && !empty($advancedFilter['tgl_persetujuan'])) {
         $rangeDate = DocoHelpers::parsingRangeDate($advancedFilter['tgl_persetujuan']);
         $start = ArrayHelper::getValue($rangeDate, 'startDate');
         $start = date('Y-m-d', strtotime($start));
         $end = ArrayHelper::getValue($rangeDate, 'endDate');
         $end = date('Y-m-d', strtotime($end));
         unset($_GET['advanced-filter']['tgl_persetujuan']);
      }

      $query = DocoRestActiveFilter::advancedFilter($model, $query);
      $query->andWhere(['between', 'tgl_persetujuan', $start, $end]);
      return [
         'query' => $query,
         'periode' => [
            'start' => $start,
            'end' => $end,
         ]
      ];
   }

   public function actionGetObjectData() 
   {
      $request = Yii::$app->request;
      $getData = $request->get();
      $tipe = ArrayHelper::getValue($getData, 'tipe', 1);
      $data = $this->dateFilter($request);
      $query = ArrayHelper::getValue($data, 'query');
      $periode = ArrayHelper::getValue($data, 'periode');
      $model = $query->asArray()->all();
      $result = $model;
      if($tipe == 2) {
         $periode = date('d M Y', strtotime(ArrayHelper::getValue($periode, 'start'))).' - '.date('d M Y', strtotime(ArrayHelper::getValue($periode, 'end')));
         $attributes = [
            '#datatable#' => $this->renderPartial('index', [
               'data' => $model,
            ]),
            '#periode#' => $periode,
            '#cetak_oleh#' => Yii::$app->jwt->user->nama_pemakai,
            '#tanggal#' => date('d F Y H:i:s'),
        ];
        $result = [
            'periode' => $periode,
            'attributes' => $attributes,
            'countData' => count($model),
        ];
      }
      return $result;
   }
}
