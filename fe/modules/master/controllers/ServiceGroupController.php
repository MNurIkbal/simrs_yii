<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\master\controllers;

use Yii;
use app\components\DocoHelpers;
use app\components\DocoController;
use GuzzleHttp\Exception\RequestException;

class ServiceGroupController extends DocoController {
    public $_title = "Service Group";
    public $_module = 'master/service-group/';

    public function init() {
        parent::init();
    }

    public function actions() {
        return [
            'delete'        => 'Doco\master\actions\ServiceGroup\DeleteAction',
            'create'        => 'Doco\master\actions\ServiceGroup\CreateAction',
            'index'         => 'Doco\master\actions\ServiceGroup\IndexAction',
            'update'        => 'Doco\master\actions\ServiceGroup\UpdateAction',
            'get-data'      => 'Doco\master\actions\ServiceGroup\GetDataAction'
        ];
    }
}