<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\gudang\actions\LaporanMutasiObatAlkes;

use Yii;
use yii\base\Action;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use GuzzleHttp\Exception\RequestException;


class IndexAction extends Action
{
	public function run()
	{
		$title = $this->controller->_title;
        $module = $this->controller->_module;
        $ruangan_id = Yii::$app->docoVars->workspace('ruangan_id');

        $response = Yii::$app->docoRest->gudang->get('lap-mutasi-obat-alkes/get-list-ruangan');
        $body = json_decode($response->getBody(), true);
        $daftar_ruangan = $body['response'];

        $is_disabled = false;
        $ruangan_aktif = '';

        if($ruangan_id != DocoConstants::GUDANG_FARMASI) {
            $is_disabled = true;
            $ruangan_aktif = $ruangan_id;
        }

		return $this->controller->render('index', get_defined_vars());
	}	
}
