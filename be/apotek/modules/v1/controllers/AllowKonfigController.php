<?php

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoConstants;
use app\components\ApotekComponent;

class AllowKonfigController extends DocoActiveController
{
	public $modelClass = 'app\modules\v1\models\KonfigFarmasi';

	public function verbs()
    {
        $verbs = parent::verbs();
        $verbs["verifikasi-pemesanan"] = ["GET"];
        $verbs["verifikasi-penerimaan"] = ["GET"];
        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['delete']);
        unset($actions['view']);
        unset($actions['create']);
        unset($actions['update']);
        $actions['is-verifikasi-pemesanan'] = 'app\modules\v1\actions\AllowKonfig\IsVerifikasiPemesananAction';
        $actions['is-verifikasi-penerimaan'] = 'app\modules\v1\actions\AllowKonfig\IsVerifikasiPenerimaanAction';
        $actions['is-verifikasi-stok-opname'] = 'app\modules\v1\actions\AllowKonfig\IsVerifikasiStokOpnameAction';
        $actions['batal-pesan-by'] = 'app\modules\v1\actions\AllowKonfig\BatalPemesananByAction';
        return $actions;
    }
}