<?php

/**
 * @author : ilham
 * Powered by Sirs
 */

namespace app\extensions\gizi;

use Yii;
use yii\web\Response;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
class LapPermintaanMakanMhg extends \app\components\DocoBaseProcessExtension
{
    protected $_title = "Laporan Permintaan Makan MHG";
    protected $_module = 'gizi/laporan-permintaan-makan/'; //buat fe
    protected function processFlow($controller)
    {
        $title = Yii::t('fe', $this->_title);
        $module = $this->_module;
        return $controller->render('@app/extensions/gizi/views/index', get_defined_vars());
    }
}
