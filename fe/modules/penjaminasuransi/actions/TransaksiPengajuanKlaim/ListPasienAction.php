<?php

namespace Doco\penjaminasuransi\actions\TransaksiPengajuanKlaim;

use Yii;
use yii\validators\Validator;
use app\components\Services\Penjamin\GetInitFilterService;

class ListPasienAction extends BaseCurrentAction
{
    protected $_validator;

    public function init()
    {
        parent::init();
        $this->_validator = new Validator();
    }

    public function run()
    {
        $title = $this->_title;
        $cachePenjamin = Yii::$app->cache->get('pengajuan_klaim_penjamin');
        $response = (new GetInitFilterService)->execute();
        $response['ruangan'] = [];
        return Yii::$app->controller->render('list_pasien', get_defined_vars());
    }
}
