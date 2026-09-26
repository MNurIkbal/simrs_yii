<?php 
namespace Doco\models\kasir;

use Yii;

class DetailInvoiceBelumBayar extends \Doco\components\DocoActiveRecord
{
	public function populateData()
	{
		$request = Yii::$app->request;
      return $request->get();
	}
}