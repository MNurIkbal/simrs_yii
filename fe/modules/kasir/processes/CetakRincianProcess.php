<?php

/**
 * @author : Budi (budi@docotel.com)
 * Powered by Sirs
 */

namespace app\modules\kasir\processes;

use Yii;
use app\components\DocoHelpers;

class CetakRincianProcess extends \app\components\DocoBaseProcessExtension
{
	protected function processFlow($controller)
	{
		$request = Yii::$app->request;
      $id = $request->get('id', null);
      $jenis_invoice =$request->get('jenis_invoice',null); 
      $penjamin_id =$request->get('penjamin_id',null);   
      if(!is_numeric($id)) {
         $id = DocoHelpers::decrypt($id);
      }
      $uid = Yii::$app->user->identity->loginpemakai_id;
      $params = 'id=' . $id . '&loginpemakai_id=' . $uid;
      if(!empty($jenis_invoice)){
         $params .= '&jenis_invoice='.$jenis_invoice;
      }
      if(!empty($penjamin_id)){
         $params .= '&penjamin_id='.$penjamin_id;
      }
      return Yii::$app->report->exec('new-summary-sementara?'.$params);
	}
}