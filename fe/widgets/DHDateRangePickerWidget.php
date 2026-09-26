<?php

namespace app\widgets;

use app\widgets\DHBaseHtmlWidget;

class DHDateRangePickerWidget extends DHBaseHtmlWidget
{
    public $start_time;
    public $end_time;
    public $name_id;
    public $with_default = null;

    public function init()
    {
        parent::init();
        if ($this->with_default) {
            if (!$this->start_time) $this->start_time = date('d-m-Y', strtotime('-1 month'));
            if (!$this->end_time) $this->end_time = date('d-m-Y');
        }
        if (!$this->name_id) $this->name_id = 'myDateCustom';
    }

    public function run()
    {
        $startTime = $this->start_time;
        $endTime = $this->end_time;
        $nameId = $this->name_id;
        $withDefault = $this->with_default;
        $startId = $nameId . "Start";
        $endId = $nameId . "End";
        return $this->render('DHDateRangePickerWidget/index', get_defined_vars());
    }
}
