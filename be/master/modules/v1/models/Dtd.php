<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "dtd_m".
 *
 * @property int $dtd_id
 * @property int $tabularlist_id
 * @property string $dtd_kode
 * @property string $dtd_noterperinci
 * @property string $dtd_nama
 * @property string $dtd_namalainnya
 * @property string $dtd_katakunci
 * @property int $dtd_nourut
 * @property bool $dtd_menular
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
 * @property TabularlistM $tabularlist
 * @property KlasifikasidiagnosaM[] $klasifikasidiagnosaMs
 */
class Dtd extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dtd_m';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tabularlist_id', 'dtd_nourut', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['tabularlist_id', 'dtd_nourut', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['dtd_kode', 'dtd_noterperinci', 'dtd_nama'], 'required'],
            [['dtd_menular', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['dtd_kode'], 'string', 'max' => 10],
            [['dtd_noterperinci'], 'string', 'max' => 400],
            [['dtd_nama'], 'string', 'max' => 255],
            [['dtd_namalainnya'], 'string', 'max' => 100],
            [['dtd_katakunci'], 'string', 'max' => 50],
            [['tabularlist_id'], 'exist', 'skipOnError' => true, 'targetClass' => TabularlistM::className(), 'targetAttribute' => ['tabularlist_id' => 'tabularlist_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'dtd_id' => 'Dtd ID',
            'tabularlist_id' => 'Tabularlist ID',
            'dtd_kode' => 'Dtd Kode',
            'dtd_noterperinci' => 'Dtd Noterperinci',
            'dtd_nama' => 'Dtd Nama',
            'dtd_namalainnya' => 'Dtd Namalainnya',
            'dtd_katakunci' => 'Dtd Katakunci',
            'dtd_nourut' => 'Dtd Nourut',
            'dtd_menular' => 'Dtd Menular',
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
    public function getTabularlist()
    {
        return $this->hasOne(TabularlistM::className(), ['tabularlist_id' => 'tabularlist_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getKlasifikasidiagnosaMs()
    {
        return $this->hasMany(KlasifikasidiagnosaM::className(), ['dtd_id' => 'dtd_id']);
    }
}
