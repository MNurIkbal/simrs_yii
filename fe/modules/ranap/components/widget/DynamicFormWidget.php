<?php
// author : Ardi Pratama

namespace app\modules\ranap\components\widget;

use yii\helpers\Html;
use yii\base\Widget;
use yii\base\InvalidConfigException;
use app\modules\ranap\components\AsesmenHtml;
use yii\web\View;
use app\modules\ranap\components\helpers\DynamicFormHelpers;

class DynamicFormWidget extends Widget
{
	public $id;
    public $model;
    public $form;
    public $dataform;
    public $datainit;

    public function init()
    {
        parent::init();
    }

    public function run()
    {
         // Register AssetBundle
        // SkoringWidgetAsset::register($this->getView());
        echo DynamicFormHelpers::generateForm($this->id,
                    $this->dataform,$this->form,$this->model,$this->datainit
                );
        $this->getView()->registerCss(".hidden-level { display: none; }");
        $this->getView()->registerJs($this->render('js'.DIRECTORY_SEPARATOR.'_dynamicformwidget.js',
            [
                'id'=>$this->id,
                // 'radioName'=>$radioName,
                // 'textfieldId'=>$textfieldId,
                // 'textfieldName'=>$textfieldName,
                // 'dependsVal'=>$dependsVal
        ]), View::POS_END);
        // return $this->render('_skoring', ['product' => $this->model]);
    }
}