<?php

/**
 * @author : Ilham Pramono
 * Powered by Sirs
 */

namespace app\extensions\gudang;

use Yii;
use yii\filters\AccessControl;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\Response;
use app\components\DocoController;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;

class LapPemakaianBarangKeramat extends \app\components\DocoBaseProcessExtension
{
    protected $_title = "LapPemakaianBarangKeramat";
    protected $_module = 'gudang/laporan-pemakaian-barang/';

    protected function processFlow($controller)
    {
        Yii::error("udah masuk extensison");
        $title = Yii::t('fe', $this->_title);
        $module = $this->_module;
        return $controller->render('@app/extensions/gudang/views/index', get_defined_vars());
    }

}