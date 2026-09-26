<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "rujukbalik_t".
 *
 * @property int $rujukbalik_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property string $tgl_rujukbalik
 * @property string $alamat
 * @property string $email
 * @property string $kode_dpjp
 * @property string $nama_dokter
 * @property string $saran
 * @property string $diagnosa
 * @property int $parent_id
 * @property string $data_reseptur
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
class RujukBalik extends \Doco\components\DocoActiveRecord
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'rujukbalik_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'no_srb'], 'required'],
            [['pendaftaran_id', 'pasienadmisi_id', 'tgl_rujukbalik', 'alamat', 'email', 'kode_dpjp', 'nama_dokter', 'saran', 'diagnosa', 'parent_id', 'data_reseptur'], 'default', 'value' => null],
            [['rujukbalik_id', 'pendaftaran_id', 'pasienadmisi_id', 'parent_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['pasienadmisi_id', 'tgl_rujukbalik', 'alamat', 'email', 'kode_dpjp', 'nama_dokter', 'saran', 'diagnosa', 'parent_id', 'data_reseptur', 'additional_data', 'created_by', 'modified_count', 'last_modified_by', 'is_deleted', 'is_active', 'deleted_date', 'deleted_by'], 'safe'],
            [['tgl_rujukbalik', 'alamat', 'email', 'kode_dpjp', 'nama_dokter', 'saran', 'diagnosa', 'data_reseptur', 'no_srb'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'rujukbalik_id' => 'Rujukbalik Id',
            'pendaftaran_id' => 'Pendaftaran Id',
            'pasienadmisi_id' => 'Pasienadmisi Id',
            'tgl_rujukbalik' => 'Tgl Rujukbalik',
            'alamat' => 'Alamat',
            'email' => 'Email',
            'kode_dpjp' => 'Kode Dpjp',
            'nama_dokter' => 'Nama Dokter',
            'saran' => 'Saran',
            'diagnosa' => 'Diagnosa',
            'parent_id' => 'Parent Id',
            'data_reseptur' => 'Data Reseptur',
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
