<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\actions;

use Yii;
use yii\base\Action;
use yii\data\ActiveDataProvider;
use Doco\components\DocoRestActiveFilter;
use app\modules\v1\models\InfoDaftarPasienV;
use app\modules\v1\models\Instalasi;

class GetStatusPendaftaranBaruAction extends Action
{
    public function run()
    {
        return Yii::$app->docoPlugin->execute('rm_get_status_pendaftaran_baru');
    }
}