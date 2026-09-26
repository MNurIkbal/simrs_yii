<?php
// author : Ardi Pratama

namespace app\modules\ranap\components\widget;

use yii\helpers\Html;
use yii\widgets\InputWidget;
use yii\base\InvalidConfigException;
use app\modules\ranap\components\AsesmenHtml;
use yii\web\View;

class RadioTextWidget extends InputWidget
{
    public $model;
    public $data;
    public $textfieldAttribute;
    public $textfieldOptions = [];
    public $textfieldDepends = false;
    public $widgetOptions = '';

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        if (empty($this->textfieldAttribute)) {
            throw new InvalidConfigException("The 'textfieldAttribute' property has not been set.");
        }

        if (empty($this->data)) {
            throw new InvalidConfigException("The 'data' property has not been set.");
        }
        $radioId = AsesmenHtml::getModelId($this->model,$this->attribute);
        $radioName = AsesmenHtml::getModelName($this->model,$this->attribute);
        $textfieldId = AsesmenHtml::getModelId($this->model,$this->textfieldAttribute);
        $textfieldName = AsesmenHtml::getModelName($this->model,$this->textfieldAttribute);
        $dependsVal = '';
        if($this->textfieldDepends){
            $dependsVal = $this->textfieldDepends;
        }
        $optional = [];
        if($this->widgetOptions != ''){
            $optional= $this->widgetOptions;
        }
        echo AsesmenHtml::activeRadioListText($this->model,$this->attribute,$this->textfieldAttribute,$this->data,$optional);
        $this->getView()->registerJs($this->render('js'.DIRECTORY_SEPARATOR.'_radiotext.js',['radioId'=>$radioId,'radioName'=>$radioName,'textfieldId'=>$textfieldId,'textfieldName'=>$textfieldName,'dependsVal'=>$dependsVal]), View::POS_END);
    }
}
?>