<?php

namespace app\modules\master\models;

use Yii;

class MarginKhususDetailForm extends \yii\base\Model
{
	public $margin;
	public $jenisobat_id;

	/**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['margin', 'jenisobat_id'], 'required','message'=>'{attribute} Tidak boleh kosong']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'margin' => 'Margin',
            'jenisobat_id' => 'Jenis Obat'
        ];
    }
}