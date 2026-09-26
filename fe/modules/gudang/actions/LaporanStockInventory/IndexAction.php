<?php

namespace Doco\gudang\actions\LaporanStockInventory;

use Yii;
use yii\base\Action;

class IndexAction extends Action
{
	public function run()
	{
		$title = $this->controller->_title;
        $module = $this->controller->_module;
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_name');
        
        $response = Yii::$app->docoRest->gudang->get('lap-stock-inventory/get-list-ruangan');
        $response2 = Yii::$app->docoRest->gudang->get('lap-stock-inventory/get-list-jenis-obat');
        $body = json_decode($response->getBody(), true);
        $body2 = json_decode($response2->getBody(), true);
        $daftar_ruangan = $body['response'];    
        $daftar_jenisobat = $body2['response'];

        $is_disabled = false;
        $ruangan_aktif = '';
        $jenisobat_aktif = '';

		return $this->controller->render('index', get_defined_vars());
	}	
}
