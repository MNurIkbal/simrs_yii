<?php

/**
 * @author : Budi (budi@sirs.co.id)
 * Powered by Sirs
 */

namespace app\modules\v1\actions\LaporanAnalisaPoNonMedis;

use Yii;
use yii\base\Action;
use Doco\components\DocoHelpers;
use app\modules\v1\models\LaporanAnalisaPoNonMedisView;
use Doco\components\DocoRestActiveFilter;
use yii\helpers\ArrayHelper;

class ExportExcelAction extends Action
{
   public function run()
   {
      try {
         $title = 'Laporan PO Analisa Non Medis';
         $request = Yii::$app->request;
         $advanced_filter = $request->get('advanced-filter');
         $model = new LaporanAnalisaPoNonMedisView;
         $query = $model::find();
         if (count($advanced_filter) > 0) {
            $this->controller->dateFilter($query, $request);
         }
         $query = DocoRestActiveFilter::advancedFilter($model, $query);
         $advancedFilterNullable = [
             'tgl_pr' => ArrayHelper::getValue($advanced_filter, 'tgl_pr', '-'),
             'tgl_po' => ArrayHelper::getValue($advanced_filter, 'tgl_po', '-'),
             'nama_barang' => ArrayHelper::getValue($advanced_filter, 'nama_barang', '-'),
             'no_po' => ArrayHelper::getValue($advanced_filter, 'no_po', '-')
         ];
         $header = $model->setHeaderExcel($advancedFilterNullable);
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
                    'formatCode' => 'general'
                ],
                [
                    'selectColumn' => 'D',
                    'formatCode' => 'general'
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
                    'selectColumn' => 'M',
                    'formatCode' => 'general'
                ],
                [
                    'selectColumn' => 'O',
                    'formatCode' => 'general'
                ],
                [
                    'selectColumn' => 'U',
                    'formatCode' => 'general'
                ],
                [
                    'selectColumn' => 'X',
                    'formatCode' => 'general'
                ],
               [
                  'selectColumn' => 'E',
                  'formatCode' => 'datetime'
               ],
               [
                  'selectColumn' => 'J',
                  'formatCode' => 'datetime'
               ],
               [
                  'selectColumn' => 'K',
                  'formatCode' => 'datetime'
               ],
               [
                  'selectColumn' => 'L',
                  'formatCode' => 'datetime'
               ],
               [
                  'selectColumn' => 'V',
                  'formatCode' => 'datetime'
               ],
               [
                  'selectColumn' => 'Z',
                  'formatCode' => 'general'
               ],
               [
                  'selectColumn' => 'AA',
                  'formatCode' => 'general'
               ],
               [
                  'selectColumn' => 'AB',
                  'formatCode' => 'datetime'
               ],
                [
                   'selectColumn' => 'AH',
                   'formatCode' => 'general'
                ],
                [
                   'selectColumn' => 'AI',
                   'formatCode' => 'general'
                ],
                [
                   'selectColumn' => 'AJ',
                   'formatCode' => 'general'
                ],
                [
                   'selectColumn' => 'P',
                   'formatCode' => 'number'
                ],
                [
                   'selectColumn' => 'S',
                   'formatCode' => 'number'
                ],
                [
                   'selectColumn' => 'T',
                   'formatCode' => 'number'
                ],
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
