<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rujukan_t".
 *
 * @property int $rujukan_id
 * @property int $asalrujukan_id
 * @property int $rujukandari_id
 * @property int $diagnosa_id
 * @property string $no_rujukan
 * @property string $nama_perujuk
 * @property string $tanggal_rujukan
 * @property string $kodediagnosa_rujukan
 * @property string $keluhan_rujukan
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
 * @property PendaftaranT[] $pendaftaranTs
 * @property AsalrujukanM $asalrujukan
 * @property DiagnosaM $diagnosa
 * @property RujukandariM $rujukandari
 */
class Rujukan extends \Doco\components\DocoActiveRecord
{
    protected $xssProtected = [
        'no_rujukan',
        'nama_perujuk',
    ];

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rujukan_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['asalrujukan_id', 'no_rujukan'], 'required'],
            [['asalrujukan_id', 'rujukandari_id', 'diagnosa_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['asalrujukan_id', 'rujukandari_id', 'diagnosa_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tanggal_rujukan', 'created_date', 'last_modified_date', 'deleted_date','rujukan_id'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_rujukan'], 'string', 'max' => 20],
            [['nama_perujuk', 'kodediagnosa_rujukan'], 'string', 'max' => 50],
            [['keluhan_rujukan'], 'string', 'max' => 200],
            // [['asalrujukan_id'], 'exist', 'skipOnError' => true, 'targetClass' => AsalRujukan::className(), 'targetAttribute' => ['asalrujukan_id' => 'asalrujukan_id']],
            [['diagnosa_id'], 'exist', 'skipOnError' => true, 'targetClass' => Diagnosa::className(), 'targetAttribute' => ['diagnosa_id' => 'diagnosa_id']],
            //[['rujukandari_id'], 'exist', 'skipOnError' => true, 'targetClass' => Rujukandari::className(), 'targetAttribute' => ['rujukandari_id' => 'rujukandari_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'rujukan_id' => 'Rujukan ID',
            'asalrujukan_id' => 'Asalrujukan ID',
            'rujukandari_id' => 'Rujukandari ID',
            'diagnosa_id' => 'Diagnosa ID',
            'no_rujukan' => 'No Rujukan',
            'nama_perujuk' => 'Nama Perujuk',
            'tanggal_rujukan' => 'Tanggal Rujukan',
            'kodediagnosa_rujukan' => 'Kodediagnosa Rujukan',
            'keluhan_rujukan' => 'Keluhan Rujukan',
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
    public function getPendaftaranTs()
    {
        return $this->hasMany(Pendaftaran::className(), ['rujukan_id' => 'rujukan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAsalrujukan()
    {
        return $this->hasOne(AsalRujukan::className(), ['asalrujukan_id' => 'asalrujukan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDiagnosa()
    {
        return $this->hasOne(Diagnosa::className(), ['diagnosa_id' => 'diagnosa_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRujukandari()
    {
        return $this->hasOne(Rujukandari::className(), ['rujukandari_id' => 'rujukandari_id']);
    }
}
