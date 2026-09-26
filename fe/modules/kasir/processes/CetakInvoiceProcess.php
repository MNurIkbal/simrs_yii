<?php

/**
 * @author : Budi (budi@docotel.com)
 * Powered by Sirs
 */

namespace app\modules\kasir\processes;

use Yii;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;
use app\components\DocoConstants;

class CetakInvoiceProcess extends \app\components\DocoBaseProcessExtension
{
	protected function processFlow($controller)
	{
		$request = Yii::$app->request;
        $id = $request->get('id');
        $invoice_id = $request->get('invoice_id');
        if(!is_numeric($id)) {
            $id = DocoHelpers::decrypt($id);
        }
        if(!is_numeric($invoice_id)) {
            $invoice_id = DocoHelpers::decrypt($invoice_id);
        }
        $userIdentity = Yii::$app->session->get('user_identity');
        $uid = !empty($userIdentity['id_pegawai']) ?  '&uid=' . $userIdentity['id_pegawai'] : ''; 
        $params = 'id=' . $id . '&invoice_id=' . $invoice_id . $uid;
        return Yii::$app->report->exec('summary-sudah-bayar?'.$params);
	}
}