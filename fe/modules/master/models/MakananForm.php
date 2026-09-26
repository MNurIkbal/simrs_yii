<?php

/**
 * @Author: Sigit
 * @Date:   2018-11-28 17:31:57
 */

namespace app\modules\master\models;

use Yii;

/**
 * @property string $makanandiet_kode
 * @property string $makanandiet_nama
 * @property string $makanandiet_keterangan
 * @property string $is_active
 */
class MakananForm extends \yii\base\Model
{
	/**
     * @inheritdoc
     */
	public $makanandiet_kode;
    public $makanandiet_nama;
    public $makanandiet_keterangan;
    public $is_active;

	/**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['makanandiet_kode', 'makanandiet_nama'], 'required'],
            [['makanandiet_kode', 'makanandiet_nama'], 'string', 'max' => 50],
            [['makanandiet_keterangan'], 'string'],
            [['is_active'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'makanandiet_kode' => Yii::t('fe', 'Kode'),
            'makanandiet_nama' => Yii::t('fe', 'Nama Makanan'),
            'makanandiet_keterangan' => Yii::t('fe', 'Keterangan'),
            'is_active' => Yii::t('fe', 'Status'),
        ];
    }
}