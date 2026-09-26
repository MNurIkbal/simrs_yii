<?php

/**
 * @author : ilham
 * Powered by Sirs
 */

namespace app\modules\gizi\processes;

use Yii;
use yii\web\Response;
use app\components\DocoDatatableHelper;
use app\components\DocoHelpers;
use GuzzleHttp\Exception\RequestException;
class LapPermintaanMakanProcess extends \app\components\DocoBaseProcessExtension
{
    //readd file, karena pernah hilang
    protected $_title = "Laporan Permintaan Makan";
    protected $_module = 'gizi/laporan-permintaan-makan/'; //buat fe
    protected function processFlow($controller)
    {
        $title = Yii::t('fe', $this->_title);
        $module = $this->_module;
        return $controller->render('index', get_defined_vars());
    }
}
