<?php
/**
 * @author : Anggoro (tri.anggoro@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\gudang\controllers;

use Yii;
use app\components\DocoController;

class MigrasiController extends DocoController {

    protected $allowAction = ['*'];
    protected $_restGudang;

    public function init()
    {
        parent::init();
        $this->_restGudang = Yii::$app->docoRest->gudang;
    }

    public function actions() {
        return [
            'adjusment-obat-alkes' => 'Doco\gudang\actions\migrasi\AdjustmentObatAlkesAction',
            'get-list-ruangan' => 'Doco\gudang\actions\migrasi\GetListRuanganAction',
        ];
    }
}