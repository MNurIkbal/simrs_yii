<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-09-10 11:10:22
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-09-10 13:25:56
 */
namespace Doco\penjaminasuransi\models;

class FormKoreksi extends \yii\base\Model
{
    public $koreksidiagnosa_id;
    public $is_inacbg;
    public $is_icdprimer;

    public function rules()
    {
        return [
            [['koreksidiagnosa_id', 'is_inacbg', 'is_icdprimer'], 'safe'],
        ];
    }

}