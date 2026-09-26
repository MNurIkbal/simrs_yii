<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "masukkamar_t".
 *
 * @property int $masukkamar_id
 * @property int $ruangan_id
 * @property int $carabayar_id
 * @property int $bookingkamar_id
 * @property int $pasienadmisi_id
 * @property int $penjamin_id
 * @property int $pindahkamar_id
 * @property int $pegawai_id
 * @property int $kelaspelayanan_id
 * @property int $kamartempattidur_id
 * @property int $kamarruangan_id
 * @property string $tgl_masukkamar
 * @property string $no_masukkamar
 * @property string $jam_masukkamar
 * @property string $tgl_keluarkamar
 * @property string $jam_keluarkamar
 * @property int $lamadirawat_kamar
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
class MasukKamar extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'masukkamar_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'carabayar_id', 'pasienadmisi_id', 'penjamin_id', 'kelaspelayanan_id', 'kamartempattidur_id', 'tgl_masukkamar', 'jam_masukkamar'], 'required'],
            [['ruangan_id', 'carabayar_id', 'bookingkamar_id', 'pasienadmisi_id', 'penjamin_id', 'pindahkamar_id', 'pegawai_id', 'kelaspelayanan_id', 'kamartempattidur_id', 'kamarruangan_id', 'lamadirawat_kamar', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'carabayar_id', 'bookingkamar_id', 'pasienadmisi_id', 'penjamin_id', 'pindahkamar_id', 'pegawai_id', 'kelaspelayanan_id', 'kamartempattidur_id', 'kamarruangan_id', 'lamadirawat_kamar', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_masukkamar', 'jam_masukkamar', 'tgl_keluarkamar', 'jam_keluarkamar', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_masukkamar'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'masukkamar_id' => 'Masukkamar ID',
            'ruangan_id' => 'Ruangan ID',
            'carabayar_id' => 'Carabayar ID',
            'bookingkamar_id' => 'Bookingkamar ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'penjamin_id' => 'Penjamin ID',
            'pindahkamar_id' => 'Pindahkamar ID',
            'pegawai_id' => 'Pegawai ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'kamartempattidur_id' => 'Kamartempattidur ID',
            'kamarruangan_id' => 'Kamarruangan ID',
            'tgl_masukkamar' => 'Tgl Masukkamar',
            'no_masukkamar' => 'No Masukkamar',
            'jam_masukkamar' => 'Jam Masukkamar',
            'tgl_keluarkamar' => 'Tgl Keluarkamar',
            'jam_keluarkamar' => 'Jam Keluarkamar',
            'lamadirawat_kamar' => 'Lamadirawat Kamar',
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
