<?php

namespace app\widgets;

use Yii;
use yii\helpers\ArrayHelper;
use app\widgets\DHBaseHtmlWidget;
use app\components\Traits\ControllerHelperTrait;
use yii\web\JsExpression;

class DHInfiniteTableWidget extends DHBaseHtmlWidget
{
    use ControllerHelperTrait;

    public $id;
    public $name;
    public $ajax;
    public $limit;
    public $columns;
    public $sorting;
    public $formFilters;
    public $functions;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $id = $this->id;
        $name = $this->name;
        $ajax = $this->ajax;
        $limit = $this->limit;
        $thead = self::thead($this->columns);
        $columnsLength = count($this->columns);
        if (isset($this->functions)) {
            foreach ($this->functions as $event => $handler) {
                if (!empty($handler)) {
                    $function = $handler instanceof JsExpression ? null : new JsExpression($handler);
                    $this->functions[$event] = "({$function});\n";
                } else {
                    $this->functions[$event] = null;
                }
            }
        }
        $payload = [
            'id' => $id,
            'name' => $name,
            'ajax' => $ajax,
            'limit' => $limit,
            'columns' => $this->columns,
            'sorting' => $this->sorting,
            'thead' => $thead,
            'columnsLength' => $columnsLength,
            'formFilters' => $this->formFilters,
            'functions' => $this->functions
        ];

        return $this->render('DHInfiniteTable/index', $payload);
    }

    private function thead($columns){
        $thead = '<tr class="bg-inverse">';
        foreach ($columns as $key => $value) {
            if (isset($value['title'])) {
                $thead .='<th>' . $value['title'] . '</th>';
            }
        }
        $thead .= '</tr>';
        return $thead;
    }
}
