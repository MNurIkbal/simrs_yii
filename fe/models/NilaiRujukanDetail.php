<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "nilairujukandetail_m".
 *
 * @property int $nilairujukandetail_id
 * @property int $nilairujukan_id
 * @property int $jenis_kelamin lookup_type='jenis_kelamin'
 * @property int $golonganumur_id
 * @property double $nilai_min
 * @property double $nilai_max
 * @property int $satuan_hasillab lookup_type='satuan_hasillab'
 * @property string $text_value
 * @property int $positif_negatif 0=negatif, 1=positif
 * @property string $keterangan
 * @property string $additional_data
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property bool $is_deleted
 * @property bool $is_active
 * @property string $deleted_date
 * @property int $deleted_by
 */
class NilaiRujukanDetail extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'nilairujukandetail_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nilairujukan_id', 'jenis_kelamin', 'golonganumur_id', 'nilai_min', 'nilai_max'], 'required'],
            [['nilairujukan_id', 'jenis_kelamin', 'golonganumur_id', 'satuan_hasillab', 'positif_negatif', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['nilairujukan_id', 'jenis_kelamin', 'golonganumur_id', 'satuan_hasillab', 'positif_negatif', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['nilai_min', 'nilai_max'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['text_value', 'keterangan'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'nilairujukandetail_id' => 'Nilairujukandetail ID',
            'nilairujukan_id' => 'Nilairujukan ID',
            'jenis_kelamin' => 'Jenis Kelamin',
            'golonganumur_id' => 'Golonganumur ID',
            'nilai_min' => 'Nilai Min',
            'nilai_max' => 'Nilai Max',
            'satuan_hasillab' => 'Satuan Hasillab',
            'text_value' => 'Text Value',
            'positif_negatif' => 'Positif Negatif',
            'keterangan' => 'Keterangan',
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }
}
