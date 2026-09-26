<?php


namespace Doco\gudang\actions\LaporanAdjustmentBarang;

use Yii;
use yii\base\Action;
use app\components\DHtml;
use app\components\Traits\ControllerHelperTrait;

class IndexAction extends Action
{
    use ControllerHelperTrait;
    public function run()
    {
        $module = $this->controller->_module;
        $titleMenu = DHtml::getTitleMenu();
        $title = !empty($titleMenu) ? $titleMenu : $this->controller->_title;
        $filters = $this->guzzleExec(Yii::$app->docoRest->gudang, [
            'url' => 'laporan-adjustment-barang/filters',
            'payload' => [
                'query' => [
                    'types' => [
                        'instalasi_ruangan',
                        'jenis_adjustment'
                    ]
                ]
            ]
        ]);
        return $this->controller->render('index', get_defined_vars());
    }
}
