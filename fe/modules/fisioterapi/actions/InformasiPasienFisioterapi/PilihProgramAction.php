<?php

namespace Doco\fisioterapi\actions\InformasiPasienFisioterapi;

use Yii;

class PilihProgramAction extends BaseCurrentAction
{
    public function run()
    {
        $request = Yii::$app->request;
        $pasien_id = $request->get('pasien_id');
        $pendaftaran_id = $request->get('pendaftaran_id');
        $jenis_pelayanan = $request->get('jenis_pelayanan');
        $viewName = 'modal_pilih_program_ranap';
        if ($jenis_pelayanan == 'rajal') $viewName = 'modal_pilih_program_rajal';
        return $this->controller->renderAjax($viewName, get_defined_vars());
    }
}
