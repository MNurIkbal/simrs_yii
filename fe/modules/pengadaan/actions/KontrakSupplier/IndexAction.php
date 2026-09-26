<?php

/**
 * @author : Muhamad Lukman Hakim (muhamad.hakim@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace Doco\pengadaan\actions\KontrakSupplier;

use Yii;
use yii\base\Action;
use app\components\DHtml;

class IndexAction extends Action
{
	public function run()
	{
		$title = DHtml::getTitleMenu();
        $module = $this->controller->_module;
		$roleImportBtn = DHtml::cekHakAkses('upload-kontrak-supplier');

		return $this->controller->render('index', get_defined_vars());
	}	
}
