<?php

namespace app\widgets\fisioterapi;

use app\widgets\DHBaseHtmlWidget;

class DHHeaderProgramTerapi extends DHBaseHtmlWidget
{
    public $id;
    public $data;
    public $scheduledetailDoctor;

    public function init()
    {
        parent::init();
        if (!$this->id) $this->id = 'header_program_terapi';
    }

    public function run()
    {
        $id = $this->id;
        $data = $this->data;
        $scheduledetailDoctor = $this->scheduledetailDoctor;
        return $this->render('DHHeaderProgramTerapi/index', get_defined_vars());
    }
}
