<?php

/**
 * @author : Novia Sukmasari P (novia.putri@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\InfoPurchaseRequisition;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;

class BarangAction extends Action {
    public function run() {
        $title = $this->controller->_title;
        $module = $this->controller->_module;
        $additional_button = null;
        if(Yii::$app->docoVars->workspace('ruangan_id') == DocoConstants::RUANGAN_PENGADAAN){
            $additional_button = [
                'generate-po' => [
                    'type' => 'button',
                    'title' => 'Generate PO',
                    'icon' => 'fa fa-cogs',
                    'method' => '#',
                    'attributes' => [
                        'id' => 'btn-generate-po',
                        'data-options'=>'click'
                    ]
                ]
            ];
            return $this->controller->render('index', get_defined_vars());
        }else{
            $additional_button = [
                'edit' => [
                    'type' => 'link',
                    'title' => \Yii::t('fe', 'Edit'),
                    'icon' => 'fa fa-edit',
                    'method' => '#',
                    'attributes' => [
                        'id' => 'btn-edit',
                        'disabled' => true,
                        'data-target' => '/pengadaan/purchase-requisition/edit?id='
                    ]
                ]
            ];
            return $this->controller->render('index_barang', get_defined_vars());
        }
    }
}
