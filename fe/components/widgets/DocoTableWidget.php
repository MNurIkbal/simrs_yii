<?php

/**
 * @author : Ardi Pratama (ardi@docotel.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\components\widgets;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\JsExpression;

class DocoTableWidget extends \yii\base\Widget
{
	public $source;

	public $options = [];

	public $columns = [];
    
    public $tableOptions = ["class"=>"table datatable-basic table-striped table-hover dataTable no-footer","cellspacing"=>"0", "width"=>"100%"];

    public $clientOptions = []; 

    public $headerRowOptions = ['class'=>'bg-inverse'];
    
    public function init()
    {
        parent::init();
        
        //the table id must be set
        if (!isset($this->tableOptions['id'])) {
            $this->tableOptions['id'] = 'datatables_'.$this->getId();
        }
    }

    public function run()
    {
        $this->customGenerator();
        $clientOptions = $this->getClientOptions();
        $view = $this->getView();
        $id = $this->tableOptions['id'];

        $options = Json::encode($clientOptions);
        $view->registerJs("jQuery('#$id').docoTabel($options);");
        
        $content = $this->renderHead().$this->renderBody();
        echo Html::tag('table', $content, $this->tableOptions);
    }

    protected function getClientOptions()
    {
        return $this->clientOptions;
    }

    protected function renderHead()
    {
    	$columns = '';
    	if(is_array($this->columns)){
    		foreach ($this->columns as $column) {
    			$columns .= '<th>';
    			$columns .= $column['title'];
    			$columns .= '</th>';
    		}
    	}
	    $contentHead = Html::tag('tr',$columns,['class'=>'bg-inverse']);
        return Html::tag('thead',$contentHead);
    }

    protected function renderBody()
    {
    	return '<tbody></tbody>';
    }

    protected function customGenerator()
    {
    	$this->clientOptions['displayLength'] = 10;
    	$this->clientOptions['processing'] = true;
    	$this->clientOptions['serverSide'] = true;
    	$this->clientOptions['ajax'] = $this->source;
    	$this->clientOptions['columns'] = $this->columns;
    }
}