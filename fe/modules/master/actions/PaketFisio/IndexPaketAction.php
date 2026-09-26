<?php

namespace Doco\master\actions\PaketFisio;

class IndexPaketAction extends BaseCurrentAction
{
    public function run()
    {
        $title = 'Master Paket Fisioterapi';
        $status = [
            'true' => 'Aktif',
            'false' => 'Tidak Aktif'
        ];
        return $this->controller->renderAjax('components/paket-fisio/index', get_defined_vars());
    }
}
