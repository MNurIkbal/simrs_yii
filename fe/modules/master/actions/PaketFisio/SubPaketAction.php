<?php

namespace Doco\master\actions\PaketFisio;

use Yii;

class SubPaketAction extends BaseCurrentAction
{
    public function run()
    {
        $title = 'Master Paket Fisioterapi';
        $status = [
            '0' => 'Tidak Aktif',
            '1' => 'Aktif',
        ];
        $request = Yii::$app->request;
        $id = $request->get('id');
        $isMcu = $request->get('is_mcu');
        $isMcu = ($isMcu == 1);
        $idEnc = $id;
        $viewName = $isMcu ? 'components/paket-fisio/subpaket_mcu' : 'components/paket-fisio/subpaket';
        return $this->controller->renderAjax($viewName, get_defined_vars());
    }
}
