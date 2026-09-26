<?php

/**
 * @author : Anggoro (tri.anggoro@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\apotek\controllers;

use Yii;
use app\components\DocoController;

class InformasiPermintaanBmhpController extends DocoController {
	protected $allowAction = ['*'];

    public function init() {
        parent::init();
    }

    public function actions() {

        return [
        	'index' => 'Doco\apotek\actions\InformasiPermintaanBmhp\IndexAction',
            'get-data' => 'Doco\apotek\actions\InformasiPermintaanBmhp\GetDataAction',
            'detail' => 'Doco\apotek\actions\InformasiPermintaanBmhp\DetailAction',
            'get-detail' => 'Doco\apotek\actions\InformasiPermintaanBmhp\GetDetailAction',
            'approve-bmhp' => 'Doco\apotek\actions\InformasiPermintaanBmhp\ApproveBmhpAction',
        ];
    }
}