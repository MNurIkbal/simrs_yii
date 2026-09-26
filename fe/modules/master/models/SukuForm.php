<?php
namespace app\modules\master\models;

use Yii;

/**
 *
 * @property string $suku_nama
 * @property string $suku_namalainnya
 * @property boolean $is_active
 */
class SukuForm extends \yii\base\Model
{
    
    public $suku_nama;
    public $suku_namalainnya;
    public $is_active;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['suku_nama'], 'required'],
            [['is_active'], 'boolean'],
            [['suku_nama', 'suku_namalainnya'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'suku_nama' => Yii::t('fe', 'Suku'),
            'suku_namalainnya' => Yii::t('fe', 'Nama Lainnya'),
            'is_active' => 'Status',
        ];
    }
}
