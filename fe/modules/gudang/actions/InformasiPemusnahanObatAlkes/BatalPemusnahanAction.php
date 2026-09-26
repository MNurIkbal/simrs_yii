<?php

/**
 * @author : Ardi (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\gudang\actions\InformasiPemusnahanObatAlkes;

use Yii;
use yii\base\Action;
use yii\web\Response;
use app\components\DocoHelpers;
use app\components\DocoDatatableHelper;
use GuzzleHttp\Exception\RequestException;
use Doco\gudang\services\InfPemusnahanObatService;

class BatalPemusnahanAction extends Action {
    public function run($id) {
        $id = DocoHelpers::decrypt($id);
        return InfPemusnahanObatService::postBatalPemusnahan($id);
    }
}