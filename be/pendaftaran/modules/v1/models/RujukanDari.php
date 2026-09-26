<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rujukandari_m".
 *
 * @property int $rujukandari_id
 * @property int $asalrujukan_id
 * @property string $nama_perujuk
 * @property string $spesialis
 * @property string $alamatlengkap
 * @property string $no_telp
 * @property string $kode_ppk
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
 *
 * @property RujukanT[] $rujukanTs
 * @property AsalrujukanM $asalrujukan
 */
class RujukanDari extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rujukandari_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['asalrujukan_id', 'nama_perujuk'], 'required'],
            [['asalrujukan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['asalrujukan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['alamatlengkap', 'additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['nama_perujuk', 'no_telp'], 'string', 'max' => 100],
            [['spesialis'], 'string', 'max' => 50],
            [['kode_ppk'], 'string', 'max' => 20],
            [['asalrujukan_id'], 'exist', 'skipOnError' => true, 'targetClass' => AsalrujukanM::className(), 'targetAttribute' => ['asalrujukan_id' => 'asalrujukan_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'rujukandari_id' => 'Rujukandari ID',
            'asalrujukan_id' => 'Asalrujukan ID',
            'nama_perujuk' => 'Nama Perujuk',
            'spesialis' => 'Spesialis',
            'alamatlengkap' => 'Alamatlengkap',
            'no_telp' => 'No Telp',
            'kode_ppk' => 'Kode Ppk',
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRujukanTs()
    {
        return $this->hasMany(RujukanT::className(), ['rujukandari_id' => 'rujukandari_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsalrujukan()
    {
        return $this->hasOne(AsalrujukanM::className(), ['asalrujukan_id' => 'asalrujukan_id']);
    }
}
