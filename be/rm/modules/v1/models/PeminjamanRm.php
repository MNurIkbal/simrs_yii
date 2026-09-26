<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "peminjamanrm_t".
 *
 * @property int $peminjamanrm_id
 * @property int $pengirimanrm_id
 * @property int $dokrekammedis_id
 * @property int $pasien_id
 * @property int $pendaftaran_id
 * @property int $kembalirm_id
 * @property int $instalasi_id
 * @property int $ruangan_id
 * @property int $pegawaipeminjam_id
 * @property string $nourut_pinjam
 * @property string $tglpeminjamanrm
 * @property string $untuk_kepentingan
 * @property string $keterangan_peminjaman
 * @property string $tglakan_dikembalikan
 * @property bool $print_peminjaman
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
 * @property PengirimanrmT[] $pengirimanrmTs
 */
class PeminjamanRm extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'peminjamanrm_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pengirimanrm_id', 'dokrekammedis_id', 'pasien_id', 'pendaftaran_id', 'kembalirm_id', 'instalasi_id', 'ruangan_id', 'pegawaipeminjam_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pengirimanrm_id', 'dokrekammedis_id', 'pasien_id', 'pendaftaran_id', 'kembalirm_id', 'instalasi_id', 'ruangan_id', 'pegawaipeminjam_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['dokrekammedis_id', 'pasien_id', 'ruangan_id', 'nourut_pinjam', 'tglpeminjamanrm'], 'required'],
            [['tglpeminjamanrm', 'tglakan_dikembalikan', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['keterangan_peminjaman', 'additional_data'], 'string'],
            [['print_peminjaman', 'is_deleted', 'is_active'], 'boolean'],
            [['nourut_pinjam'], 'string', 'max' => 5],
            [['untuk_kepentingan'], 'string', 'max' => 50],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'peminjamanrm_id' => 'Peminjamanrm ID',
            'pengirimanrm_id' => 'Pengirimanrm ID',
            'dokrekammedis_id' => 'Dokrekammedis ID',
            'pasien_id' => 'Pasien ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'kembalirm_id' => 'Kembalirm ID',
            'instalasi_id' => 'Instalasi ID',
            'ruangan_id' => 'Ruangan ID',
            'pegawaipeminjam_id' => 'Pegawaipeminjam ID',
            'nourut_pinjam' => 'Nourut Pinjam',
            'tglpeminjamanrm' => 'Tglpeminjamanrm',
            'untuk_kepentingan' => 'Untuk Kepentingan',
            'keterangan_peminjaman' => 'Keterangan Peminjaman',
            'tglakan_dikembalikan' => 'Tglakan Dikembalikan',
            'print_peminjaman' => 'Print Peminjaman',
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
        return $this->hasMany(PendaftaranT::className(), ['peminjamanrm_id' => 'peminjamanrm_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPengirimanrmTs()
    {
        return $this->hasMany(PengirimanrmT::className(), ['peminjamanrm_id' => 'peminjamanrm_id']);
    }

    public function afterSave()
    {
        $mPengiriman = PengirimanRm::findOne($this->pengirimanrm_id);
        $mPengiriman->peminjamanrm_id = $this->peminjamanrm_id;
        $mPengiriman->save();
    }
}
