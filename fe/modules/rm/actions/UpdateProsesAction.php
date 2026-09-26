<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\rm\actions;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;

class UpdateProsesAction extends Action
{
    /*$id = pendaftaran_id*/
    public function run($id)
    {
        $_curl = Yii::$app->docoRest->rm->get('inf-daftar-pasien/update-proses',[
            'query' => [
                'id' => $id
            ]
        ]);
        $_decodeResponse = json_decode($_curl->getBody(),TRUE);
        return DocoHelpers::response($_decodeResponse);
    }
}