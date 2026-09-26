<?php

/**
 * @author : Novia Sukmasari P (novia.putri@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoSpout;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use app\modules\v1\models\LaporanMutasiObatView;
use app\modules\v1\models\Ruangan;

class KonfigObatRuanganController extends DocoActiveController {
    public $modelClass = '';

    public function actions() {
        return [
            'get-by-ruangan'    => 'app\modules\v1\actions\KonfigObatRuangan\GetByRuanganAction',
            'get-by-id'         => 'app\modules\v1\actions\KonfigObatRuangan\GetByIdAction',
            'update-data'       => 'app\modules\v1\actions\KonfigObatRuangan\UpdateAction'
        ];
    }
}
