<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-04-20 15:07:47
 * @Last Modified by:   afil
 * @Last Modified time: 2018-04-20 15:09:22
 * @Description: tabel temporary formstokopname_t (clone)
 */

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "formstokopname_r".
 *
 * @property int $formstokopnamer_id
 * @property int $formstokopname_id
 * @property int $stokopnamedetail_id
 * @property int $obatalkes_id
 * @property int $formulirstokopname_id
 * @property double $volume_stok
 * @property int $periodestok_id
 * @property int $ruangan_id
 * @property string $nobatch
 */
class FormStokOpnameR extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'formstokopname_r';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['formstokopnamer_id', 'ruangan_id'], 'required'],
            [['formstokopnamer_id', 'formstokopname_id', 'stokopnamedetail_id', 'obatalkes_id', 'formulirstokopname_id', 'periodestok_id', 'ruangan_id'], 'default', 'value' => null],
            [['formstokopnamer_id', 'formstokopname_id', 'stokopnamedetail_id', 'obatalkes_id', 'formulirstokopname_id', 'periodestok_id', 'ruangan_id'], 'integer'],
            [['volume_stok'], 'number'],
            [['nobatch'], 'string', 'max' => 100],
            [['formstokopnamer_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'formstokopnamer_id' => 'Formstokopnamer ID',
            'formstokopname_id' => 'Formstokopname ID',
            'stokopnamedetail_id' => 'Stokopnamedetail ID',
            'obatalkes_id' => 'Obatalkes ID',
            'formulirstokopname_id' => 'Formulirstokopname ID',
            'volume_stok' => 'Volume Stok',
            'periodestok_id' => 'Periodestok ID',
            'ruangan_id' => 'Ruangan ID',
            'nobatch' => 'Nobatch',
        ];
    }
}
