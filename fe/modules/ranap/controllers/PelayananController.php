<?php
// author : yafi.kusnaedi@gmail.com

namespace Doco\ranap\controllers;

use app\components\DocoConstants;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\Response;

use app\modules\ranap\components\traits\PemeriksaanResepturTrait;

class PelayananController extends DocoController
{
    use PemeriksaanResepturTrait;
    
    /*
    * function yang digunakan pada controller ini dari trait
    * 1. PemeriksaanResepturTrait@actionModalHistoryResep
    */

    public function init()
    {
        parent::init();
    }

    public function beforeAction($action)
    {
        return true;
    }
}
