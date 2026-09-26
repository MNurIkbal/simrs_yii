<?php

/**
 * @author Chacha Nurholis (chacha@sirs.co.id)
 * A Product of PT Citraraya Nusatama
 * Powered by Sirs
 * 
 * DHSelectTenagaMedis adalah widget
 * untuk kebutuhan filter data pegawai tenaga medis
 */

namespace app\widgets\filters\DropdownTenagaMedis;

use app\widgets\DHBaseHtmlWidget;

class DHSelectTenagaMedis extends DHBaseHtmlWidget
{
    public $id;

    public function init()
    {
        parent::init();
        if (!$this->id) $this->id = 'select_widget_tenaga_medis';
    }

    public function run()
    {
        $id = $this->id;
        return $this->render('DHSelectTenagaMedis/index', get_defined_vars());
    }
}
