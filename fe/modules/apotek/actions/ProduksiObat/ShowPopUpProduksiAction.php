<?php

/**
 * @author : Asri Nurul M
 * Powered by Sirs
 */

namespace Doco\apotek\actions\ProduksiObat;

use Yii;
use yii\base\Action;
use yii\base\View;
use app\components\DocoHelpers; 
use GuzzleHttp\Exception\RequestException;
use Doco\apotek\models\ApprovalProduksiObatForm;

class ShowPopUpProduksiAction extends Action {
    public function run($id) {
        $title = 'Approval Produksi Obat';
        $model = new ApprovalProduksiObatForm;
        $id_produksi = $id;
        
        return $this->controller->renderAjax('__modal_produksi', get_defined_vars());
    }
}
