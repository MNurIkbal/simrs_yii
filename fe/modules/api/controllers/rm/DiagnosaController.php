<?php

namespace Doco\api\controllers\rm;

use app\components\DocoController;
use Yii;
use app\components\Services\Contracts\DiagnosaInterface;

class DiagnosaController extends DocoController 
{
    protected $allowAction = ['*'];

    protected $service;

    public function __construct($id, $module, $config = [], DiagnosaInterface $service)
    {
        $this->service = $service;
        parent::__construct($id, $module, $config);
    }

    public function actionList($q = '',$page = null,$type = 'diagnosa_masuk', $formatResponse = 0)
    {
        return $this->service->getDiagnosa($q, $page, $type, $formatResponse);
    }
}