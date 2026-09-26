<?php

/**
 * @author : Anggoro (tri.anggoro@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoConstants;
use Doco\components\DocoRestActiveFilter;

class InformasiPermintaanBmhpController extends DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\KonfigFarmasi';

	public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        return [
            'index'         => 'app\modules\v1\actions\InformasiPermintaanBmhp\IndexAction',
            'detail'        => 'app\modules\v1\actions\InformasiPermintaanBmhp\DetailAction',
            'detail-header' => 'app\modules\v1\actions\InformasiPermintaanBmhp\DetailHeaderAction',
            'approve-bmhp'  => 'app\modules\v1\actions\InformasiPermintaanBmhp\ApproveBmhpAction',
        ];
    }
}