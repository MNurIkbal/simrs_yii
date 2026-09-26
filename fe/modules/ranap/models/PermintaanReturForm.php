<?php

/**
 * @Author: Sigit
 * @Date:   2018-08-07 13:26:37
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-08-07 13:31:14
 */

namespace app\modules\ranap\models;

use Yii;

/**
 * This is the model class for table "permintaanretur_t".
 *
 * @property int $permintaanretur_id
 * @property string $tgl_permintaanretur
 * @property string $no_permintaanretur
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $ruangan_id
 * @property int $pegawairetur_id
 * @property int $status_permintaanretur 0=belum di proses, 1=sudah di proses
 * @property int $returresep_id
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
class PermintaanReturForm extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public $permintaanretur_id;
    public $pendaftaran_id;
    public $pasienadmisi_id;
    public $ruangan_id;
    public $pegawairetur_id;
    public $status_permintaanretur;
    public $returresep_id;
    public $tgl_permintaanretur;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['permintaanretur_id', 'pendaftaran_id', 'pasienadmisi_id', 'ruangan_id', 'pegawairetur_id', 'status_permintaanretur', 'returresep_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['permintaanretur_id', 'pendaftaran_id', 'pasienadmisi_id', 'ruangan_id', 'pegawairetur_id', 'status_permintaanretur', 'returresep_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_permintaanretur', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_permintaanretur'], 'string', 'max' => 255],
            [['permintaanretur_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'permintaanretur_id' => 'Permintaanretur ID',
            'tgl_permintaanretur' => 'Tgl Permintaanretur',
            'no_permintaanretur' => 'No Permintaanretur',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'ruangan_id' => 'Ruangan ID',
            'pegawairetur_id' => 'Pegawairetur ID',
            'status_permintaanretur' => 'Status Permintaanretur',
            'returresep_id' => 'Returresep ID',
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
