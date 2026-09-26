<?php

/**
 * @author : Ardi Pratama (ardi@sirs.co.id)
 * A product of PT. Citraraya Nusatama
 * Powered by Sirs
 */

namespace app\components\widgets;

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\web\JsExpression;
use app\assets\SirsTableAsset;

class SirsTableWidget extends \yii\base\Widget
{
	public $source;

	public $options = [];

	public $columns = [];
    
    public $tableOptions = ["class"=>"table datatable-basic table-striped table-hover dataTable no-footer","cellspacing"=>"0", "width"=>"100%"];

    public $clientOptions = []; 

    public $headerRowOptions = ['class'=>'bg-inverse'];

    public $isRegister = true;
    
    public function init()
    {
        parent::init();
        
        //the table id must be set
        if (!isset($this->tableOptions['id'])) {
            $this->tableOptions['id'] = 'sirsngtables_'.$this->getId();
        }
    }

    public function run()
    {
        $id = $this->tableOptions['id'];
        $view = $this->getView();
        if($this->isRegister){
            SirsTableAsset::register($view);
        }
        $arrCols = $this->columns;
        $tableUrl = $this->source;
        echo "<sirs-ng-table id='".$id."' table-url=$tableUrl></sirs-ng-table>";
        echo Html::script("
            document.querySelector('sirs-ng-table#".$id."').tableCols = ".json_encode($arrCols).";
        ",['type'=>'text/javascript']);
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