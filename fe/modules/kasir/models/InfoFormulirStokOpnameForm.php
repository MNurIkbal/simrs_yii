<?php

namespace app\modules\kasir\models;

use Yii;

/**
 * This is the model class for table "infoformulirstokopname_v".
 *
 * @property int $formulirstokopname_id
 * @property string $tglformulir
 * @property string $noformulir
 */
class InfoFormulirStokOpnameForm extends \yii\base\Model
{
    public $formulirstokopname_id;
    public $tglformulir;
    public $noformulir;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'infoformulirstokopname_v';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['formulirstokopname_id'], 'default', 'value' => null],
            [['formulirstokopname_id'], 'integer'],
            [['tglformulir'], 'safe'],
            [['noformulir'], 'string'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'formulirstokopname_id' => Yii::t('fe', 'Formulir stok opname'),
            'tglformulir' => Yii::t('fe', 'Tanggal formulir'),
            'noformulir' => Yii::t('fe', 'No formulir'),
        ];
    }
}
