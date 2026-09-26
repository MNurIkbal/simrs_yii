<?php

namespace app\widgets;

use app\widgets\DHBaseHtmlWidget;

class DHAnatomiWidget extends DHBaseHtmlWidget
{
    public $name_id;
    public $image;
    public $option;
    public $dataAnatomi;
    public $counter_data;
    public $size_image;
    public $size_table;

    public function init()
    {
        parent::init();
        if (!$this->name_id) {
          $this->name_id = "Image";
        }
    }

    public function run()
    {
        $name_id = [
          'lower' => strtolower($this->name_id),
          'ucWord' => ucwords($this->name_id)
        ];
        $image = $this->image;
        $option = $this->option;
        $dataAnatomi = $this->dataAnatomi;
        $counter_data = $this->counter_data;
        $size_image = $this->size_image;
        $size_table = $this->size_table;
        return $this->render('DHAnatomiWidget/index', get_defined_vars());
    }
}
