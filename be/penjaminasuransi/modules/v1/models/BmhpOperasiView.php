<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "inpostoperasi3_v".
 *
 * @property int $inpostoperasi_id
 * @property int $pasienmasukpenunjang_id
 * @property int $bmhpoperasi_id
 * @property int $obatalkes_id
 * @property string $jenis_alat_bmhp
 * @property int $persediaan
 * @property int $tambahan
 * @property int $terpakai
 * @property int $sisa
 * @property bool $is_ditagihkan
 */
class BmhpOperasiView extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'inpostoperasi3_v';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['inpostoperasi_id', 'pasienmasukpenunjang_id', 'bmhpoperasi_id', 'obatalkes_id', 'persediaan', 'tambahan', 'terpakai', 'sisa'], 'default', 'value' => null],
            [['inpostoperasi_id', 'pasienmasukpenunjang_id', 'bmhpoperasi_id', 'obatalkes_id', 'persediaan', 'tambahan', 'terpakai', 'sisa'], 'integer'],
            [['is_ditagihkan'], 'boolean'],
            [['jenis_alat_bmhp'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'inpostoperasi_id' => 'Inpostoperasi ID',
            'pasienmasukpenunjang_id' => 'Pasienmasukpenunjang ID',
            'bmhpoperasi_id' => 'Bmhpoperasi ID',
            'obatalkes_id' => 'Obatalkes ID',
            'jenis_alat_bmhp' => 'Jenis Alat Bmhp',
            'persediaan' => 'Persediaan',
            'tambahan' => 'Tambahan',
            'terpakai' => 'Terpakai',
            'sisa' => 'Sisa',
            'is_ditagihkan' => 'Is Ditagihkan',
        ];
    }
}
