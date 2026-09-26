<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions\Integration;

use Yii;
use yii\base\Action;
use Doco\components\DocoActiveController;
use app\modules\v1\models\IntReturnOrderLine;

class ReturnOrderLineAction extends Action
{
    public function run()
    {
        $uid = Yii::$app->request->get('uid',null);
        if(is_null($uid)){
            throw new \Exception("UID tidak boleh kosong", 1);
        }
        $roline = IntReturnOrderLine::find()->andWhere([
        	'is_sending' => false
        ])->orderBy([
        	'date_planned' => SORT_ASC
        ])->one();

        if(empty($roline)) {
        	return [
        		'status' => 422,
        		'messages' => 'Tidak ada data yang di proses'
        	];
        }

        $data = $roline->attributes;

        if ($roline->tipe_rekap == 'RPOS' || $roline->tipe_rekap == 'RPOM') {
            $query = Yii::$app->db->createCommand("
            	UPDATE returpenerimaanobatdetail_r SET is_sending = true,id_sync_sercon = '{$uid}' WHERE id = {$roline->id}
            ")->execute();
        }else if($roline->tipe_rekap == 'RPBM' || $roline->tipe_rekap == 'RPBS'){
            $query = Yii::$app->db->createCommand("
                UPDATE returpenerimaanbarangdetail_r SET is_sending = true,id_sync_sercon = '{$uid}' WHERE id = {$roline->id}
            ")->execute();
        }

        return $data;
    }
}
