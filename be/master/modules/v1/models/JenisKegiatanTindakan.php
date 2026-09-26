<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "jeniskegiatantindakan_m".
 *
 * @property int $jeniskegiatantindakan_id
 * @property string $jeniskegiatantindakan_kode
 * @property string $jeniskegiatantindakan_nama
 * @property string $jeniskegiatan_keterangan
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
 * @property DaftartindakanM[] $daftartindakanMs
 */
class JenisKegiatanTindakan extends \Doco\components\DocoActiveRecord
{
    public $daftartindakan_ids;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jeniskegiatantindakan_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['jeniskegiatantindakan_kode', 'jeniskegiatantindakan_nama'], 'required'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'daftartindakan_ids'], 'safe'],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['jeniskegiatantindakan_kode'], 'string', 'max' => 25],
            [['jeniskegiatantindakan_nama'], 'string', 'max' => 100],
            [['jeniskegiatan_namalainnya'], 'string', 'max' => 100],
            [['jeniskegiatan_keterangan'], 'string'],
            [['jeniskegiatantindakan_nama', 'jeniskegiatantindakan_kode'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'jeniskegiatantindakan_id' => Yii::t('app', 'ID'),
            'jeniskegiatantindakan_kode' => Yii::t('app', 'Kode Kegiatan'),
            'jeniskegiatantindakan_nama' => Yii::t('app', 'Nama kegiatan'),
            'jeniskegiatan_keterangan' => Yii::t('app', 'Keterangan'),
            'additional_data' => 'Additional Data',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'is_deleted' => 'Is Deleted',
            'is_active' => Yii::t('app', 'Status'),
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDaftarTindakan()
    {
        return $this->hasMany(DaftartindakanM::className(), ['jeniskegiatantindakan_id' => 'jeniskegiatantindakan_id']);
    }
}
