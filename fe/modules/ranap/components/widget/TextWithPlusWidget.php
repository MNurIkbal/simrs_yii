<?php
// author : Ardi Pratama

namespace app\modules\ranap\components\widget;

use yii\helpers\Html;
use yii\widgets\InputWidget;
use yii\base\InvalidConfigException;
use app\modules\ranap\components\AsesmenHtml;
use yii\web\View;

class TextWithPlusWidget extends InputWidget
{
    public $model;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        // $radioId = AsesmenHtml::getModelId($this->model,$this->attribute);
        // $radioName = AsesmenHtml::getModelName($this->model,$this->attribute);
        $textfieldId = AsesmenHtml::getModelId($this->model,$this->attribute);
        $textfieldName = AsesmenHtml::getModelName($this->model,$this->attribute);
        // $dependsVal = '';
        // if($this->textfieldDepends){
        //     $dependsVal = $this->textfieldDepends;
        // }
        echo AsesmenHtml::activeTextInputPlus($this->model,$this->attribute,$this->options);
        $this->getView()->registerJs($this->render('js'.DIRECTORY_SEPARATOR.'_textwithplus.js',
            [
                // 'radioId'=>$radioId,
                // 'radioName'=>$radioName,
                'textfieldId'=>$textfieldId,
                'textfieldName'=>$textfieldName,
                // 'dependsVal'=>$dependsVal
        ]), View::POS_END);
    }
}
?>