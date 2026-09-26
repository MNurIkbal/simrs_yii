<?php

namespace Doco\fisioterapi\controllers;

use Yii;
use app\components\DocoController;

class PemeriksaanController extends DocoController
{
    protected $_title = "Pemeriksaan Fisioterapi";
    protected $_module = '/fisioterapi/pemeriksaan';
    protected $params;
    protected $_restFisioterapi;
    protected $_ranap;
    protected $allowAction = ['*'];

    public function init()
    {
        parent::init();
        $this->_restFisioterapi = Yii::$app->docoRest->fisioterapi;
        $this->_ranap = Yii::$app->docoRest->ranap;
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        $newActions = [
            'index' => 'Doco\fisioterapi\actions\Pemeriksaan\IndexAction',
            'cppt' => 'Doco\fisioterapi\actions\Pemeriksaan\CpptAction',
            'store-soap' => 'Doco\fisioterapi\actions\Pemeriksaan\StoreSoapAction',
            'update-soap' => 'Doco\fisioterapi\actions\Pemeriksaan\UpdateSoapAction',
            'get-data-cppt' => 'Doco\fisioterapi\actions\Pemeriksaan\GetDataCpptAction',
            'is-create-soap' => 'Doco\fisioterapi\actions\Pemeriksaan\IsCreateSoapAction',
            'cetak-cppt' => 'Doco\fisioterapi\actions\Pemeriksaan\CetakCpptAction',
            'pemulangan-pasien' => 'Doco\fisioterapi\actions\Pemeriksaan\PemulanganPasienAction',
        ];
        $actions = array_merge($actions, $newActions);
        return $actions;
    }
}