<?php

namespace Doco\gudang\actions\LaporanStockInventoryBarang;

use Yii;
use yii\base\Action;
use app\components\DHtml;

class IndexAction extends Action
{
	public function run()
	{
        $title = DHtml::getTitleMenu($this->controller->_title);
        $module = $this->controller->_module;

        $ruangan_id = Yii::$app->docoVars->workspace('ruanganid');
        $response = Yii::$app->docoRest->gudang->get('lap-stock-inventory-barang/get-list-jenis-barang');
        $response2 = Yii::$app->docoRest->gudang->get('lap-stock-inventory-barang/get-list-instalasi');
        $ruangan_aktif = [Yii::$app->docoVars->workspace("ruanganid") => Yii::$app->docoVars->workspace("ruangan_name")];
        $body = json_decode($response->getBody(), true);
        $body2 = json_decode($response2->getBody(), true);
        $daftar_jenisbarang = $body['response'];
        $instalasi = $body2['response'];
        $ruangan_aktif = [];

        $is_disabled = false;
        $visibility = false;
        $jenisbarang_aktif = '';
        $instalasi_aktif = '';

		return $this->controller->render('index', get_defined_vars());

	}	
}
