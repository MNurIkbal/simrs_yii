<?php

/**
 * @author : iqbal.rukmana@sirs.co.id
 * Powered by Sirs
 */

namespace app\modules\v1\actions\LaporanAnalisaPoNonMedis;

use Yii;
use yii\base\Action;
use Doco\components\DocoHelpers;
use Doco\Services\InternalService;
use app\modules\v1\models\LaporanAnalisaPoNonMedisView;
use Doco\components\DocoRestActiveFilter;
use yii\helpers\ArrayHelper;

class SyncExportExcelAction extends Action
{
   public function run()
   {
      try {
            $request = Yii::$app->request;
            $getData = $request->get();
            $xOwner = $request->getHeaders()->get('X-Owner');
            $auth = $request->getHeaders()->get('Authorization');
            
            if (isset($getData['page'])) unset($getData['page']);
            if (isset($getData['per-page'])) unset($getData['per-page']);
            
            $fetchLimit = 50;
            $data = $this->controller->getDataLaporanExcel($request);

            $countData = $data['count'];
            $randString = isset($getData['randString']) ? $getData['randString'] : null;
            $totalPerPage = ceil($countData/$fetchLimit);
            
            (new InternalService)->sendTo([
                'Sirs' => [
                    'LaporanAnalisaPONonMedisExcel' => [
                        'token' => $auth,
                        'xOwner' => $xOwner,
                        'unique_str' => $randString,
                        'filter' => $getData,
                        'totalPerPage' => $totalPerPage,
                    ]
                ]
            ], true);

            (new InternalService)->sendTo([
                'Sirs' => [ 
                    'ExportAnalisaPONonMedis' => [
                        'token' => $auth,
                        'xOwner' => $xOwner,
                        'unique_str' => $randString,
                        'totalPerPage' => $totalPerPage,
                        'countData' => $countData,
                        'filter' => $getData,
                    ]
                ]
            ], true);

            (new InternalService)->sendTo([
                'Sirs' => [ 
                    'UploadAnalisaPONonMedisExcel' => [
                        'token' => $auth,
                        'xOwner' => $xOwner,
                        'unique_str' => $randString,
                        'totalPerPage' => $totalPerPage,
                        'countData' => $countData,
                    ]
                ]
            ], true);
            
            return [
                'totalPerPage' => $totalPerPage,
                'randString' => $randString,
                'countData' => $countData,
        ];
      } catch (\Yii\db\Exception $e) {
         return $e->getMessage();
      } catch (\Exception $e) {
         return $e->getMessage();
      }
   }
}
