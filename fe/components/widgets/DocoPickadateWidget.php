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

class DocoPickadateWidget extends \yii\widgets\InputWidget
{
	public $options = [];
    
    public $clientOptions = [];

    public $varName = null;

    public function init()
    {
        parent::init();

        if (!isset($this->options['id'])) 
            $this->options['id'] = $this->getId();
    }

    public function run()
    {
        $this->customFormat();

        $this->registerAssets();

        if ($this->hasModel()) {
            echo Html::activeTextInput($this->model, $this->attribute, $this->options);
        } else {
            echo Html::textInput($this->name, $this->value, $this->options);
        }

    }

    public function registerAssets()
    {
        $view = $this->getView();

        $options= Json::encode($this->clientOptions, JSON_NUMERIC_CHECK);

        $js = "$('";
        $js .= '#'.$this->options['id'];
        $js .= "').pickadate({$options});";

        if ($this->varName !== null)
            $js = "var {$this->varName} = {$js}";

        $this->getView()->registerJs($js, \yii\web\View::POS_END);
    }

    public function customFormat()
    {
        /*
        * set date for 'min' from php date to js date
        * replace 'min' paramater if set
        */
    	if(isset($this->clientOptions['startFrom'])){
    		$dateStart = explode('-', date('Y-m-d',strtotime($this->clientOptions['startFrom'])));
    		$dateStart[1] = $dateStart[1] - 1;
    		$this->clientOptions['min'] = $dateStart;
    	}

        /*
        * set initial default value current date if 'onStart' not set
        */
        if(!isset($this->clientOptions['onStart'])){
            $this->clientOptions['onStart'] = new JsExpression('function () {
                var date = new Date();
                this.set("select", [date.getFullYear(), date.getMonth(), date.getDate()]);
            }');
        }
    }
}