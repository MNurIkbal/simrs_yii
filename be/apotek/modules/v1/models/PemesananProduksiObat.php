<?php

namespace app\modules\v1\models;

/**
 * This is the model class for table "pemesananproduksiobat_t".
 *
 * @property int $pemesananproduksiobat_id
 * @property int $instalasi_id
 * @property int $ruangan_id
 * @property string $nopemesanan
 * @property string $tglpemesanan
 * @property int $pegawaipemesanan_id
 * @property string $status_pemesanan
 * @property string $tgl_aprove
 * @property string $pegawaiaprove_id
 * @property string $created_date
 * @property int $created_by
 * @property int $modified_count
 * @property string $last_modified_date
 * @property int $last_modified_by
 * @property string $deleted_date
 * @property int $deleted_by
 * @property bool $is_deleted
 * @property bool $is_active
 */
class PemesananProduksiObat extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pemesananproduksiobat_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['tgl_aprove', 'pegawaiaprove_id', 'catatan_bahanbaku', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['instalasi_id', 'ruangan_id', 'pegawaipemesanan_id', 'status_pemesanan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'pegawaiaprove_id'], 'integer'],
            [['tglpemesanan', 'tgl_aprove', 'created_date', 'last_modified_date', 'deleted_date','status_pemesanan'], 'safe'],
            [['nopemesanan', 'catatan_bahanbaku'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pemesananproduksiobat_id' => 'Pemesanan Produksi Obat ID',
            'instalasi_id' => 'Instalasi ID',
            'ruangan_id' => 'Ruangan ID',
            'nopemesanan' => 'Nomor Pemesanan',
            'tglpemesanan' => 'Tanggal Pemesanan',
            'pegawaipemesanan_id' => 'Pegawai Pemesanan ID',
            'status_pemesanan' => 'Status Pemesanan',
            'tgl_aprove' => 'Tanggal Aprove',
            'pegawaiaprove_id' => 'Pegawai Aprove ID',
            'created_date' => 'Created Date',
            'created_by' => 'Created By',
            'modified_count' => 'Modified Count',
            'last_modified_date' => 'Last Modified Date',
            'last_modified_by' => 'Last Modified By',
            'deleted_date' => 'Deleted Date',
            'deleted_by' => 'Deleted By',
            'is_deleted' => 'Is Deleted',
            'is_active' => 'Is Active',
        ];
    }

    public function getInstalasi()
    {
        return $this->hasOne(Instalasi::className(), ['instalasi_id' => 'instalasi_id']);
    }

    public function getRuangan()
    {
        return $this->hasOne(Ruangan::className(), ['ruangan_id' => 'ruangan_id']);
    }
}
