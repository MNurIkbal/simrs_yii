<?php

namespace app\modules\kasir\models;

use Yii;

class JasaDokter extends \yii\base\Model
{
    public $jasadokter_kode;
    public $jasadokter_nama;
    public $is_active;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jasadokter_kode', 'jasadokter_nama', 'is_active'], 'required'],
            [['jasadokter_kode', 'jasadokter_nama'], 'safe'],
            [['is_active'], 'boolean'],
            [['jasadokter_kode'], 'string', 'max' => 10],
            [['jasadokter_nama'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jasadokter_kode' => 'Kode',
            'jasadokter_nama' => 'Nama Transaksi',
            'is_active' => 'Status',
        ];
    }
}
