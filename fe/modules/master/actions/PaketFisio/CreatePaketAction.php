<?php

namespace Doco\master\actions\PaketFisio;

use Yii;
use app\modules\master\models\PaketFisioForm;

class CreatePaketAction extends BaseCurrentAction
{
    public function run()
    {
        $title = 'Tambah Paket Fisioterapi';
        $model = new PaketFisioForm;
        $request = Yii::$app->request;
        $post = $request->post();
        $sessionId = Yii::$app->docoVars->user("id");
        return $this->controller->renderAjax('components/paket-fisio/form', get_defined_vars());
    }
}
