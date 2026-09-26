<?php

namespace Doco\pengadaan\actions\KontrakSupplier;

use Yii;
use yii\base\Action;
use app\components\DHtml;

class ImportDataAction extends Action
{
        public function run() {
                $title = DHtml::getTitleMenu(Yii::t('fe', 'Import Data Kontrak Supplier'));
                $user_login = Yii::$app->user->identity->loginpemakai_id;
                $request = Yii::$app->request;
                $getData = Yii::$app->cache->get("upload-kontrak-supplier-".$user_login);
                if ($getData) Yii::$app->cache->delete("upload-kontrak-supplier-".$user_login);
                
                return $this->controller->renderAjax('_import_form',get_defined_vars());
	}	
}
