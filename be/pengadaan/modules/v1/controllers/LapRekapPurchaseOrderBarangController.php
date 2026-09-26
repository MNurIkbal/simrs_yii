<?php 

/**
 * @author : Bambang Hermawan (bambang.hermawan@sirs.co.id)
 * Powered by Sirs
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use Doco\components\DocoConstants;
use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoPrint;

class LapRekapPurchaseOrderBarangController extends DocoActiveController
{
    public $modelClass = '';

    public function verbs()
    {
        $verbs = parent::verbs();
        return $verbs;
    }

    public function actions()
    {
        return [
            'get-data' => 'app\modules\v1\actions\LapRekapPurchaseOrderBarang\GetDataAction',
            'get-data-filter' => 'app\modules\v1\actions\LapRekapPurchaseOrderBarang\GetDataFilterAction',
            'export-excel' => 'app\modules\v1\actions\LapRekapPurchaseOrderBarang\ExportExcelAction'
        ];
    }
}