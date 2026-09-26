<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pesanbarang_t".
 *
 * @property int $pesanbarang_id
 * @property int $mutasibarang_id
 * @property int $pegawaipemesan_id
 * @property int $pegawaimengetahui_id
 * @property int $ruanganpemesan_id
 * @property string $no_pemesanan
 * @property string $tgl_pesanbarang
 * @property string $tgl_mintadikirim
 * @property string $keterangan_pesan
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
 * @property int $ruangantujuan_id
 * @property string $statuspesan
 */
class PesanBarang extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pesanbarang_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['pegawaipemesan_id', 'pegawaimengetahui_id', 'ruanganpemesan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'ruangantujuan_id'], 'default', 'value' => null],
            [['pegawaipemesan_id', 'pegawaimengetahui_id', 'ruanganpemesan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'ruangantujuan_id'], 'integer'],
            [['pegawaipemesan_id', 'ruanganpemesan_id', 'tgl_pesanbarang'], 'required'],
            [['tgl_pesanbarang', 'tgl_mintadikirim', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['keterangan_pesan', 'additional_data'], 'string'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_pemesanan'], 'string', 'max' => 50],
            // [['statuspesan'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pesanbarang_id' => Yii::t('app', 'Pesan barang'),
            'mutasibarang_id' => Yii::t('app', 'Mutasi barang'),
            'pegawaipemesan_id' => Yii::t('app', 'Pegawai pemesan'),
            'pegawaimengetahui_id' => Yii::t('app', 'Pegawai mengetahui'),
            'ruanganpemesan_id' => Yii::t('app', 'Ruangan pemesan'),
            'no_pemesanan' => Yii::t('app', 'No pemesanan'),
            'tgl_pesanbarang' => Yii::t('app', 'Tanggal pesan barang'),
            'tgl_mintadikirim' => Yii::t('app', 'Tanggal minta dikirim'),
            'keterangan_pesan' => Yii::t('app', 'Keterangan pesan'),
            'additional_data' => Yii::t('app', 'Additional Data'),
            'created_date' => Yii::t('app', 'Created Date'),
            'created_by' => Yii::t('app', 'Created By'),
            'modified_count' => Yii::t('app', 'Modified Count'),
            'last_modified_date' => Yii::t('app', 'Last Modified Date'),
            'last_modified_by' => Yii::t('app', 'Last Modified By'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
            'is_active' => Yii::t('app', 'Is Active'),
            'deleted_date' => Yii::t('app', 'Deleted Date'),
            'deleted_by' => Yii::t('app', 'Deleted By'),
            'ruangantujuan_id' => Yii::t('app', 'Ruangantujuan ID'),
            'statuspesan' => Yii::t('app', 'Statuspesan'),
        ];
    }


    public function getDataPemesananById($id)
    {
        $connection = Yii::$app->db;
        $sql = " SELECT
                p.pesanbarang_id,
                p.no_pemesanan,
                p.tgl_pesanbarang,
                p.tgl_mintadikirim,
                p.ruangantujuan_id,
                r.ruangan_nama,
                p.keterangan_pesan,
                i.instalasi_nama
            FROM
                pesanbarang_t p
            JOIN ruangan_m r ON r.ruangan_id = p.ruangantujuan_id
            JOIN instalasi_m i ON i.instalasi_id = r.instalasi_id
            WHERE
                p.pesanbarang_id = :id
        ";
        $command = $connection->createCommand($sql);
        $command->bindValue(':id', $id);
        $data = $command->queryOne();

        return ($data) ? $data : [];
    }

    public function getDetailPemesananById($id)
    {
        $connection = Yii::$app->db;
        $sql = " SELECT
                o.barang_nama,
                p.qty_pesan as qty_kecil,
                s.satuanunit_nama as satuan_kecil,
                p.jumlah_input as qty_besar,
                sb.satuanunit_nama as satuan_besar
            FROM
                pesanbarangdetail_t p
                JOIN barang_m o ON o.barang_id = p.barang_id
                JOIN satuanunit_m s ON s.satuanunit_id = p.satuankecil_id
                JOIN satuanunit_m sb ON sb.satuanunit_id = p.satuanbesar_id
            WHERE
                pesanbarang_id = :id
            ORDER BY o.barang_nama ASC
        ";
        $command = $connection->createCommand($sql);
        $command->bindValue(':id', $id);
        $data = $command->queryAll();

        return ($data) ? $data : [];
    }
}
