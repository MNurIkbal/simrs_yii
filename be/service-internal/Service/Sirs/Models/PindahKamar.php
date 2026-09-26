<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;

/**
 * This is the model class for table "ruangan_m".
 *
 * @property int $pindahkamar_id
 * @property int $kamartempattidur_id
 * @property int $pendaftaran_id
 * @property int $kamarruangan_id
 * @property int $pegawai_id
 * @property int $carabayar_id
 * @property int $ruangan_id
 * @property int $penjamin_id
 * @property int $pasienadmisi_id
 * @property int $kelaspelayanan_id
 * @property int $pasien_id
 * @property string $no_pindahkamar
 * @property string $tgl_pindahkamar
 * @property string $jam_pindahkamar
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
class PindahKamar extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pindahkamar_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
           
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pindahkamar_id' => 'Pindahkamar ID',
            'kamartempattidur_id' => 'Kamartempattidur ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'kamarruangan_id' => 'Kamarruangan ID',
            'pegawai_id' => 'Pegawai ID',
            'carabayar_id' => 'Carabayar ID',
            'ruangan_id' => 'Ruangan ID',
            'penjamin_id' => 'Penjamin ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'pasien_id' => 'Pasien ID',
            'no_pindahkamar' => 'No Pindahkamar',
            'tgl_pindahkamar' => 'Tgl Pindahkamar',
            'jam_pindahkamar' => 'Jam Pindahkamar',
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
