<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pindahkamar_t".
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
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'pindahkamar_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kamartempattidur_id', 'pendaftaran_id', 'kamarruangan_id', 'pegawai_id', 'carabayar_id', 'ruangan_id', 'penjamin_id', 'pasienadmisi_id', 'kelaspelayanan_id', 'pasien_id', 'tgl_pindahkamar', 'jam_pindahkamar'], 'required'],
            [['kamartempattidur_id', 'pendaftaran_id', 'kamarruangan_id', 'pegawai_id', 'carabayar_id', 'ruangan_id', 'penjamin_id', 'pasienadmisi_id', 'kelaspelayanan_id', 'pasien_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['kamartempattidur_id', 'pendaftaran_id', 'kamarruangan_id', 'pegawai_id', 'carabayar_id', 'ruangan_id', 'penjamin_id', 'pasienadmisi_id', 'kelaspelayanan_id', 'pasien_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_pindahkamar', 'jam_pindahkamar', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['additional_data'], 'string'],
            [['is_deleted', 'is_active', 'is_pasientitipan', 'is_stoptitipan'], 'boolean'],
            [['no_pindahkamar'], 'string', 'max' => 50],
        ];
    }

    /**
     * {@inheritdoc}
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

    public static function getKelasTitipan($pendaftaranId)
    {
        $sql = "
        SELECT 
        *
        From pindahkamar_t
        WHERE pendaftaran_id = :pendaftaranId 
        ORDER BY pindahkamar_id DESC
        LIMIT 2";
        return Yii::$app->db->createCommand($sql)
            ->bindValue(':pendaftaranId', $pendaftaranId)
            ->queryAll();
    }
    
}
