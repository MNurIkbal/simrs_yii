<?php

/**
 * ? @author : Budi (budi@sirs.co.id)
 * ? Powered by Sirs
 */

namespace Doco\pengadaan\actions\LaporanAnalisaPoNonMedis;

use Yii;
use yii\base\Action;

class DownloadExcelAction extends Action
{
   public function run()
   {
      $request = Yii::$app->request;
      $path = $request->get('data');
      if (!empty($path)) {
         $exp = explode('downloads/', $path);
         $fileName = isset($exp[1]) ? $exp[1] : 'laporan.xlsx';
         header('Content-Description: File Transfer');
         header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
         header('Content-Disposition: attachment; filename="' . $fileName);
         header('Content-Transfer-Encoding: binary');
         header('Expires: 0');
         header('Cache-Control: must-revalidate');
         header('Pragma: public');
         ob_clean();
         flush();
         readfile($path);
         exit;
      }
   }
}
