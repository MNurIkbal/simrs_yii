<?php

namespace app\modules\ranap\components;


use Yii;
use yii\helpers\BaseHtml;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

class AsesmenHtml extends BaseHtml
{
    public static function getModelId($model, $attribute)
    {
        return static::getInputId($model, $attribute);
    }

    public static function getModelName($model, $attribute)
    {
        return static::getInputName($model, $attribute);
    }

    public static function activeRadioListText($model, $attribute, $textAttrib, $items, $options = [])
    {
        return static::activeListInputWithText('radioListWithText', $model, $attribute, $textAttrib, $items, $options);
    }

    public static function activeCheckboxListText($model, $attribute, $textAttrib, $items, $options = [])
    {
        return static::activeListInputWithText('checkboxListWithText', $model, $attribute, $textAttrib, $items, $options);
    }

    protected static function activeListInputWithText($type, $model, $attribute, $textAttrib, $items, $options = [])
    {
        $name = isset($options['name']) ? $options['name'] : static::getInputName($model, $attribute);
        $textName = static::getInputName($model, $textAttrib);

        $selection = isset($options['value']) ? $options['value'] : static::getAttributeValue($model, $attribute);
        if (!array_key_exists('unselect', $options)) {
            $options['unselect'] = '';
        }
        if (!array_key_exists('id', $options)) {
            $options['id'] = static::getInputId($model, $attribute);
        }

        return static::$type($name, $textName, $selection, $items, $options, $model, $textAttrib);
    }

	public static function radioListWithText($radioName, $textName, $selection = null, $items = [], $options = [],$model = false,$attribute = false)
    {
        if (ArrayHelper::isTraversable($selection)) {
            $selection = array_map('strval', (array)$selection);
        }
        $formatter = ArrayHelper::remove($options, 'item');
        $itemOptions = ArrayHelper::remove($options, 'itemOptions', []);
        $encode = ArrayHelper::remove($options, 'encode', true);
        $separator = ArrayHelper::remove($options, 'separator', "\n");
        $tag = ArrayHelper::remove($options, 'tag', 'div');
        $textLabel = ArrayHelper::remove($options, 'textLabel','');

        $textfieldOptions = ArrayHelper::remove($options, 'textfieldOptions', []);
        // add a hidden field so that if the list box has no option being selected, it still submits a value
        $hidden = isset($options['unselect']) ? parent::hiddenInput($radioName, $options['unselect']) : '';
        unset($options['unselect']);

        $lines = [];
        $index = 0;
        foreach ($items as $value => $label) {
            $checked = $selection !== null &&
                (!ArrayHelper::isTraversable($selection) && !strcmp($value, $selection)
                    || ArrayHelper::isTraversable($selection) && ArrayHelper::isIn((string)$value, $selection));
            if ($formatter !== null) {
                $lines[] = call_user_func($formatter, $index, $label, $radioName, $checked, $value);
            } else {
                $lines[] = static::radio($radioName, $checked, array_merge($itemOptions, [
                    'value' => $value,
                    'label' => $encode ? parent::encode($label) : $label,
                ]));
            }
            $index++;
        }

        $textfieldOptions = ArrayHelper::remove($textfieldOptions, 'labelOption', []);
        Html::addCssClass($textfieldOptions, 'form-control');
        if($textName === false){
            $textfieldname = 'textfield-'.$radioName;
        }else{
            $textfieldname = $textName;
            if($model){
                $textfieldId = static::getInputId($model, $attribute);
                // $lines[] = static::activeLabel($model,$attribute,['class'=>'radiotextwidgetfield-'.$textfieldId]);
                $lines[] = static::tag('label',$textLabel.'[opsional]',['class'=>'radiotextwidgetfield-'.$textfieldId]);
                Html::addCssClass($textfieldOptions, 'radiotextwidgetfield-'.$textfieldId);
            }else{
                $lines[] = static::label($textName,$textfieldname);
            }
        }
        if($model){
            $lines[] =  static::activeInput('text',$model,$attribute,$textfieldOptions);
        }else{
            $lines[] =  static::input('text', $textfieldname, null, $textfieldOptions);
        }
        $visibleContent = implode($separator, $lines);

        if ($tag === false) {
            return $hidden . $visibleContent;
        }

        return $hidden . parent::tag($tag, $visibleContent, $options);
    }

    public static function checkboxListWithText($checkboxName,$textName, $selection = null, $items = [], $options = [], $model = false,$attribute = false)
    {
        if (substr($checkboxName, -2) !== '[]') {
            $checkboxName .= '[]';
        }
        if (ArrayHelper::isTraversable($selection)) {
            $selection = array_map('strval', (array)$selection);
        }

        $formatter = ArrayHelper::remove($options, 'item');
        $itemOptions = ArrayHelper::remove($options, 'itemOptions', []);
        $encode = ArrayHelper::remove($options, 'encode', true);
        $separator = ArrayHelper::remove($options, 'separator', "\n");
        $tag = ArrayHelper::remove($options, 'tag', 'div');


        $textfieldOptions = ArrayHelper::remove($options, 'textfieldOptions', []);
        $lines = [];
        $index = 0;
        foreach ($items as $value => $label) {
            $checked = $selection !== null &&
                (!ArrayHelper::isTraversable($selection) && !strcmp($value, $selection)
                    || ArrayHelper::isTraversable($selection) && ArrayHelper::isIn((string)$value, $selection));
            if ($formatter !== null) {
                $lines[] = call_user_func($formatter, $index, $label, $checkboxName, $checked, $value);
            } else {
                $lines[] = static::checkbox($checkboxName, $checked, array_merge($itemOptions, [
                    'value' => $value,
                    'label' => $encode ? static::encode($label) : $label,
                ]));
            }
            $index++;
        }

        if (isset($options['unselect'])) {
            // add a hidden field so that if the list box has no option being selected, it still submits a value
            $checkboxName2 = substr($checkboxName, -2) === '[]' ? substr($checkboxName, 0, -2) : $checkboxName;
            $hidden = static::hiddenInput($checkboxName2, $options['unselect']);
            unset($options['unselect']);
        } else {
            $hidden = '';
        }

        $textfieldOptions = ArrayHelper::remove($textfieldOptions, 'labelOption', []);
        Html::addCssClass($textfieldOptions, 'form-control');
        if($textName === false){
            $textfieldname = 'textfield-'.$checkboxName;
            
        }else{
            $textfieldname = $textName;
            if($model){
                $textfieldId = static::getInputId($model, $attribute);
                $lines[] = static::activeLabel($model,$attribute,['class'=>'checkboxtextwidgetfield-'.$textfieldId]);
                Html::addCssClass($textfieldOptions, 'checkboxtextwidgetfield-'.$textfieldId);
            }else{
                $lines[] = static::label($textName,$textfieldname);
            }
        }
        $lines[] =  static::input('text', $textfieldname, null, $textfieldOptions);

        $visibleContent = implode($separator, $lines);

        if ($tag === false) {
            return $hidden . $visibleContent;
        }
        return $hidden . static::tag($tag, $visibleContent, $options);
    }

    // public static function activeTextInputPlus($model, $attribute, $options = [])
    // {
    //     // self::normalizeMaxLength($model, $attribute, $options);
    //     return static::activeInputAsesmen('textWithPlus', $model, $attribute, $options);
    // }

    public static function activeTextInputPlus($model, $attribute, $options = [])
    {
        $name = isset($options['name']) ? $options['name'] : static::getInputName($model, $attribute);
        $value = isset($options['value']) ? $options['value'] : static::getAttributeValue($model, $attribute);
        if (!array_key_exists('id', $options)) {
            $options['id'] = static::getInputId($model, $attribute);
        }

        // self::setActivePlaceholder($model, $attribute, $options);

        return static::textWithPlus($name, $value, $options,$model,$attribute,$model,$attribute);
    }

    public static function textWithPlus($name, $value = null, $options = [], $model = false,$attribute = false)
    {
        Html::addCssClass($options, 'form-control');
        $textField = static::input('text', $name, $value, $options);
        $spanOptions = $divOptions = $buttonPlusOptions = [];
        if($model){
            $textfieldId = static::getInputId($model, $attribute);
            $buttonPlusOptions = ['type'=>'button','id'=>'btntextpluswidget-'.$textfieldId];
        }else{
            $buttonPlusOptions = ['type'=>'button'];
        }
        Html::addCssClass($buttonPlusOptions, 'btn btn-default btntextpluswidget');
        $buttonPlus = static::tag('button','+',$buttonPlusOptions);
        Html::addCssClass($spanOptions, 'input-group-btn');
        $button = static::tag('span',$buttonPlus,$spanOptions);
        Html::addCssClass($divOptions, 'input-group');
        return static::tag('div',$textField.$button,$divOptions);
    }
    
}