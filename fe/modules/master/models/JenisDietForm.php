<?php

/**
 * @Author: Sigit
 * @Date:   2018-11-28 10:54:38
 */

namespace app\modules\master\models;

use Yii;

/**
 * @property string $jenisdiet_kode
 * @property string $jenisdiet_nama
 * @property string $jenisdiet_keterangan
 * @property string $is_active
 */
class JenisDietForm extends \yii\base\Model
{
	/**
     * @inheritdoc
     */
	public $jenisdiet_kode;
    public $jenisdiet_nama;
    public $jenisdiet_keterangan;
    public $is_active;

	/**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jenisdiet_kode', 'jenisdiet_nama'], 'required'],
            [['jenisdiet_kode', 'jenisdiet_nama'], 'string', 'max' => 50],
            [['jenisdiet_keterangan'], 'string'],
            [['is_active'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jenisdiet_kode' => Yii::t('fe', 'Kode'),
            'jenisdiet_nama' => Yii::t('fe', 'Nama Jenis Diet'),
            'jenisdiet_keterangan' => Yii::t('fe', 'Keterangan'),
            'is_active' => Yii::t('fe', 'Status'),
        ];
    }
}