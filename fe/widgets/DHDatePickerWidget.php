<?php

namespace app\widgets;

use app\widgets\DHBaseHtmlWidget;

class DHDatePickerWidget extends DHBaseHtmlWidget
{
    public $with_default = null;
    public $default_value = null;
    public $name_id;
    public $min_date;
    public $max_date;
    public $defaultYears;

    public function init()
    {
        parent::init();
        if ($this->with_default) {
            if (!$this->default_value) $this->default_value = date('d-m-Y');
        }
        if (!$this->name_id) {
            $this->name_id = 'myDateCustom';
        }
    }

    public function run()
    {
        $defaultValue = $this->default_value;
        $nameId = $this->name_id;
        $targetClass = $this->name_id . "Target";
        $btnNameId = $this->name_id . "Button";
        $minDate = $this->min_date;
        $maxDate = $this->max_date;
        $defaultYears = isset($this->defaultYears) ? $this->defaultYears : 10;
        return $this->render('DHDatePickerWidget/index', get_defined_vars());
    }
}
