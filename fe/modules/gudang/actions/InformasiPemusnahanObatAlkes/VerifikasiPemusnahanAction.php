<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
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
use Doco\gudang\components\access\VerifikasiPemusnahanAccess as VerifikasiPemusnahan;

class VerifikasiPemusnahanAction extends Action {
    public function run($id) {
        $id = DocoHelpers::decrypt($id);
        return (new VerifikasiPemusnahan)->verify($id);
    }
}