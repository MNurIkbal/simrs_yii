<?php

namespace app\modules\v1\actions\LapRekapRadiologi;

use Yii;
use yii\helpers\ArrayHelper;
use yii\base\Action;
use Doco\components\DocoConstants;
use Doco\models\Pegawai;
use app\modules\v1\models\AsalRujukan;
use app\modules\v1\models\RujukanKeluar;
use SirsCore\models\DaftarTindakan;
use Doco\models\KelasPelayanan;
use Doco\models\Lookup;

class FiltersAction extends Action 
{
   public function run() 
   {
      $request = Yii::$app->request;
      $get = $request->get();
      $type = ArrayHelper::getValue($get, 'type');
      $page = ArrayHelper::getValue($get, 'page', 1);
      $limit = ArrayHelper::getValue($get, 'limit', DocoConstants::LIMIT_INFINITY_SCROLL);
      $term = ArrayHelper::getValue($get, 'term');
      $result = [];
      switch ($type) {
         case 'kelas':
            $result = KelasPelayanan::find()
               ->select(['kelaspelayanan_id AS id', 'kelaspelayanan_nama AS text'])
               ->where([
                  'is_active' => true
               ]);
            
            if(!empty($term)) {
               $result->andWhere(['like', 'LOWER(kelaspelayanan_nama)', strtolower($term)]);
            }
            $result->orderBy(['kelaspelayanan_nama' => SORT_ASC]);
            break;

         case 'pemeriksaan':
            $result = DaftarTindakan::find()
               ->select(['daftartindakan_id AS id', 'daftartindakan_nama AS text'])
               ->where([
                  'is_active' => true
               ]);
            
            if(!empty($term)) {
               $result->andWhere(['like', 'LOWER(daftartindakan_nama)', strtolower($term)]);
            }
            $result->orderBy(['daftartindakan_nama' => SORT_ASC]);
            break;
         
         default:
            
            break;
      }

      if(!empty($result)) {
         $result = $result
            ->limit($limit + 1)
            ->offset(($page - 1) * $limit)
            ->asArray()
            ->all();
      }

      return $result;
   }
}
