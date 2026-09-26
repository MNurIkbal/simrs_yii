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
use app\modules\v1\models\IntReturnOrder;

class ReturnOrderAction extends Action
{
    public function run()
    {
        $uid = Yii::$app->request->get('uid',null);
        if(is_null($uid)){
            throw new \Exception("UID tidak boleh kosong", 1);
        }
        $ro = IntReturnOrder::find()->andWhere([
        	'is_sending' => false
        ])->orderBy([
        	'date_order' => SORT_ASC
        ])->one();

        if(empty($ro)) {
        	return [
        		'status' => 422,
        		'messages' => 'Tidak ada data yang di proses'
        	];
        }

        $data = $ro->attributes;

        if ($ro->tipe_rekap == 'RPOS' || $ro->tipe_rekap == 'RPOM') {
            $query = Yii::$app->db->createCommand("
            	UPDATE returpenerimaanobat_r SET is_sending = true,id_sync_sercon = '{$uid}' WHERE id = {$ro->id}
            ")->execute();
        }else if($ro->tipe_rekap == 'RPBM' || $ro->tipe_rekap == 'RPBS'){
            $query = Yii::$app->db->createCommand("
                UPDATE returpenerimaanbarang_r SET is_sending = true,id_sync_sercon = '{$uid}' WHERE id = {$ro->id}
            ")->execute();
        }

        return $data;
    }
}
