<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "infoformulirstokopname_v".
 *
 * @property int $formulirstokopname_id
 * @property string $tglformulir
 * @property string $noformulir
 */
class InfoFormulirStokOpnameView extends \yii\db\ActiveRecord
{
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
            'formulirstokopname_id' => 'Formulirstokopname ID',
            'tglformulir' => 'Tglformulir',
            'noformulir' => 'Noformulir',
        ];
    }
}
