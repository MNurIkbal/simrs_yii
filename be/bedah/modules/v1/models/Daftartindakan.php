<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "daftartindakan_m".
 *
 * @property int $daftartindakan_id
 * @property int $kategoritindakan_id
 * @property int $kelompoktindakan_id
 * @property int $komponenunit_id
 * @property int $jeniskegiatantindakan_id
 * @property string $daftartindakan_kode
 * @property string $daftartindakan_nama
 * @property string $tindakanmedis_nama
 * @property string $daftartindakan_namalainnya
 * @property string $daftartindakan_katakunci
 * @property bool $daftartindakan_karcis
 * @property bool $daftartindakan_visite
 * @property bool $daftartindakan_konsul
 * @property bool $daftartindakan_akomodasi
 * @property bool $daftartindakan_tindakan
 * @property bool $daftartindakan_observasi
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
 */
class DaftarTindakan extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'daftartindakan_m';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['kategoritindakan_id', 'kelompoktindakan_id', 'komponenunit_id', 'daftartindakan_nama'], 'required'],
            [['kategoritindakan_id', 'kelompoktindakan_id', 'komponenunit_id', 'jeniskegiatantindakan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['kategoritindakan_id', 'kelompoktindakan_id', 'komponenunit_id', 'jeniskegiatantindakan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['daftartindakan_karcis', 'daftartindakan_visite', 'daftartindakan_konsul', 'daftartindakan_akomodasi', 'daftartindakan_tindakan', 'daftartindakan_observasi', 'is_deleted', 'is_active'], 'boolean'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['daftartindakan_kode'], 'string', 'max' => 20],
            [['daftartindakan_nama', 'tindakanmedis_nama', 'daftartindakan_namalainnya'], 'string', 'max' => 200],
            [['daftartindakan_katakunci'], 'string', 'max' => 30],
            [['jeniskegiatantindakan_id'], 'exist', 'skipOnError' => true, 'targetClass' => JeniskegiatantindakanM::className(), 'targetAttribute' => ['jeniskegiatantindakan_id' => 'jeniskegiatantindakan_id']],
            [['kategoritindakan_id'], 'exist', 'skipOnError' => true, 'targetClass' => KategoritindakanM::className(), 'targetAttribute' => ['kategoritindakan_id' => 'kategoritindakan_id']],
            [['kelompoktindakan_id'], 'exist', 'skipOnError' => true, 'targetClass' => KelompoktindakanM::className(), 'targetAttribute' => ['kelompoktindakan_id' => 'kelompoktindakan_id']],
            [['komponenunit_id'], 'exist', 'skipOnError' => true, 'targetClass' => KomponenunitM::className(), 'targetAttribute' => ['komponenunit_id' => 'komponenunit_id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'daftartindakan_id' => 'Daftartindakan ID',
            'kategoritindakan_id' => 'Kategoritindakan ID',
            'kelompoktindakan_id' => 'Kelompoktindakan ID',
            'komponenunit_id' => 'Komponenunit ID',
            'jeniskegiatantindakan_id' => 'Jeniskegiatantindakan ID',
            'daftartindakan_kode' => 'Daftartindakan Kode',
            'daftartindakan_nama' => 'Daftartindakan Nama',
            'tindakanmedis_nama' => 'Tindakanmedis Nama',
            'daftartindakan_namalainnya' => 'Daftartindakan Namalainnya',
            'daftartindakan_katakunci' => 'Daftartindakan Katakunci',
            'daftartindakan_karcis' => 'Daftartindakan Karcis',
            'daftartindakan_visite' => 'Daftartindakan Visite',
            'daftartindakan_konsul' => 'Daftartindakan Konsul',
            'daftartindakan_akomodasi' => 'Daftartindakan Akomodasi',
            'daftartindakan_tindakan' => 'Daftartindakan Tindakan',
            'daftartindakan_observasi' => 'Daftartindakan Observasi',
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