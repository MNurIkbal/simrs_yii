<?php

namespace app\modules\ranap\models;

use Yii;

class PermintaanMakanForm extends \yii\base\Model
{
    public $jenis_diet;
    public $menu_diet;
    public $waktu_diet;

	/**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['jenis_diet','menu_diet'], 'required']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
        	'jenis_diet' => 'Jenis Diet',
        	'menu_diet' => 'Menu Diet',
        	'waktu_diet' => 'Waktu Diet'
        ];
    }
}