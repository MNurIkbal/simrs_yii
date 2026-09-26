<?php
namespace app\modules\master\models;

use Yii;

/**
 * @property string $pekerjaan_nama
 * @property string $pekerjaan_namalainnya
 * @property boolean $is_active
*/
class PekerjaanForm extends \yii\base\Model
{

    public $pekerjaan_nama;
    public $pekerjaan_namalainnya;
    public $is_active;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pekerjaan_nama'], 'required'],
            [['is_active'], 'boolean'],
            [['pekerjaan_nama', 'pekerjaan_namalainnya'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pekerjaan_nama' => Yii::t('fe', 'Pekerjaan'),
            'pekerjaan_namalainnya' => Yii::t('fe', 'Nama Lainnya'),
            'is_active' => Yii::t('fe','Status'),            
        ];
    }

}
