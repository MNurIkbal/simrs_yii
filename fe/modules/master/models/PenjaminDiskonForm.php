<?php
/**
 * @author : Sulthan Zaidan Fauzi (sulthanzaidan1026@gmail.com)
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\master\models;
use app\components\DocoBaseModel;

/**
 *
 * @property integer $penjamin_id
 * @property integer $carabayar_id
 * @property float $diskon_otomatis
 * @property boolean $is_active
 */
class PenjaminDiskonForm extends DocoBaseModel
{
    public $penjamin_id;
    public $carabayar_id;
    public $diskon_otomatis;
    public $is_active;

    /**
     * @inheritdoc
     */
    protected $xssProtected = [
        'diskon_otomatis',
    ];

    /**
     * @inheritdoc
     */

    public function rules()
    {
        return [
            [['penjamin_id', 'carabayar_id', 'diskon_otomatis', 'is_active'], 'required', 'message' => '{attribute} tidak boleh kosong'],
            [['penjamin_id', 'carabayar_id'], 'integer'],
            [['diskon_otomatis'], 'number', 'min' => 1, 'max' => 100, 
            'tooSmall' => 'Nilai diskon harus lebih besar atau sama dengan 1', 
            'tooBig' => 'Nilai diskon harus kurang dari atau sama dengan 100'],
            [['is_active'], 'boolean']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'penjamin_id'          => \Yii::t('fe','Penjamin'),
            'carabayar_id'         => \Yii::t('fe','Cara Bayar'),
            'diskon_otomatis'      => \Yii::t('fe','Diskon Otomatis'),
            'is_active'            => \Yii::t('fe','Status'),
        ];
    }
}
