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

class ServiceCategoryController extends DocoController {
    public $_title = "Service Category";
    public $_module = 'master/service-category/';

    public function init() {
        parent::init();
    }

    public function actions() {
        return [
            'delete'        => 'Doco\master\actions\ServiceCategory\DeleteAction',
            'create'        => 'Doco\master\actions\ServiceCategory\CreateAction',
            'index'         => 'Doco\master\actions\ServiceCategory\IndexAction',
            'update'        => 'Doco\master\actions\ServiceCategory\UpdateAction',
            'get-data'      => 'Doco\master\actions\ServiceCategory\GetDataAction'
        ];
    }
}