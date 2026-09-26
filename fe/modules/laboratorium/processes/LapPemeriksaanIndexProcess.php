<?php

namespace app\modules\laboratorium\processes;

use Yii;
use yii\helpers\ArrayHelper;

class LapPemeriksaanIndexProcess extends \app\components\DocoBaseProcessExtension
{
    protected $_title = "Laporan Pemeriksaan Laboratorium";
    protected $_master;
    protected $_controller;

    protected function processFlow($controller)
    {
        $this->_master = Yii::$app->docoRest->master;
        $this->_controller = $controller;
        return $this->index();

        // $path = 'index';
        // return $path;
    }

    protected function index()
    {
        $title = $this->_title;
        $response = $this->_master->get('kelas-pelayanan');
        $body = json_decode($response->getBody(), TRUE);
        $body = $body['response'];
        $dataKelasPelayanan = empty($body['data']) ? [] : $body['data'];
        $listkelaspelayanan = ArrayHelper::map($dataKelasPelayanan, 'kelaspelayanan_id', 'kelaspelayanan_nama');
        return $this->_controller->render('index', get_defined_vars());
    }
}