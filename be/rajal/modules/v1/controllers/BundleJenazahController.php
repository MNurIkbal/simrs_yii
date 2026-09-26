<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2019-02-27 14:09:21
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-02-27 14:12:42
 */

namespace app\modules\v1\controllers;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

use Doco\components\DocoActiveController;
use Doco\components\DocoRestActiveFilter;
use Doco\components\DocoHelpers;
use Doco\components\DocoConstants;
use Doco\components\DocoPrint;

class BundleJenazahController extends DocoActiveController
{
    public $modelClass = 'app\modules\v1\models\InfoRiwayatPasienView';

    public function verbs()
    {
        $verbs = parent::verbs();

        // additional/ override verbs
        $verbs["get-pasien"] = ["GET"];
        $verbs["proses-anamnesa"] = ["GET", "POST"];
        $verbs["export-pdf-periksa-fisik"] = ["GET", "POST"];
        $verbs["get-pemeriksaan-fisik"] = ["GET", "POST"];
        $verbs["update-pegawai"] = ["GET", "POST"];

        return $verbs;
    }

    public function actions()
    {
        $actions = parent::actions();

        // unset default action
        unset($actions['index']);


        return $actions;
    }
}