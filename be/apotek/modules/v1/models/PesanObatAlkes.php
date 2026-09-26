<?php

namespace app\modules\v1\models;

use Yii;

/**
 * This is the model class for table "pesanobatalkes_t".
 *
 * @property int $pesanobatalkes_id
 * @property int $ruangan_id
 * @property int $mutasiobatruangan_id
 * @property string $tglpemesanan
 * @property string $nopemesanan
 * @property string $statuspesan
 * @property string $tglmintadikirim
 * @property string $keterangan_pesan
 * @property int $ruanganpemesan_id
 * @property int $pegawaipemesan_id
 * @property int $pegawaimengetahui_id
 * @property bool $iskirim
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
class PesanObatAlkes extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pesanobatalkes_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'mutasiobatruangan_id', 'ruanganpemesan_id', 'pegawaipemesan_id', 'pegawaimengetahui_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'mutasiobatruangan_id', 'ruanganpemesan_id', 'pegawaipemesan_id', 'pegawaimengetahui_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'status_verifikasi'], 'integer'],
            [['tglpemesanan', 'tglmintadikirim', 'created_date', 'last_modified_date', 'deleted_date', 'status_verifikasi'], 'safe'],
            [['nopemesanan', 'keterangan_pesan', 'additional_data'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'pesanobatalkes_id' => 'Pesanobatalkes ID',
            'ruangan_id' => 'Ruangan ID',
            'mutasiobatruangan_id' => 'Mutasiobatruangan ID',
            'tglpemesanan' => 'Tglpemesanan',
            'nopemesanan' => 'Nopemesanan',
            'statuspesan' => 'Statuspesan',
            'tglmintadikirim' => 'Tglmintadikirim',
            'keterangan_pesan' => 'Keterangan Pesan',
            'ruanganpemesan_id' => 'Ruanganpemesan ID',
            'pegawaipemesan_id' => 'Pegawaipemesan ID',
            'pegawaimengetahui_id' => 'Pegawaimengetahui ID',
            'iskirim' => 'Iskirim',
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
            'status_verifikasi' => 'Status Verifikasi'
        ];
    }

    public function getRuangan()
    {
        return $this->hasOne(Ruangan::className(), ['ruangan_id' => 'ruangan_id']);
    }

    public function getDataPemesananById($id)
    {
        $connection = Yii::$app->db;
        $sql = " SELECT
                p.pesanobatalkes_id,
                p.nopemesanan,
                p.tglpemesanan,
                p.tglmintadikirim,
                p.keterangan_pesan,
                p.ruangan_id,
                r.ruangan_nama,
                m.ruangan_nama as ruangan_pemesan
            FROM
                pesanobatalkes_t p
            JOIN ruangan_m r ON r.ruangan_id = p.ruangan_id
            join ruangan_m m on m.ruangan_id = p.ruanganpemesan_id
            WHERE
                p.pesanobatalkes_id = :id
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
                o.obatalkes_nama,
                p.jumlah_pesan as qty_kecil,
                p.satuan_pemesanan,
                p.satuanbesar_id,
                s.satuanunit_nama as satuan_kecil,
                p.jumlah_input as qty_besar,
                sb.satuanunit_nama as satuan_besar
            FROM
                pesanobatdetail_t p
                JOIN obatalkes_m o ON o.obatalkes_id = p.obatalkes_id
                JOIN satuanunit_m s ON s.satuanunit_id = p.satuankecil_id
                JOIN satuanunit_m sb ON sb.satuanunit_id = p.satuanbesar_id
            WHERE
                pesanobatalkes_id = :id
            ORDER BY o.obatalkes_nama
        ";
        $command = $connection->createCommand($sql);
        $command->bindValue(':id', $id);
        $data = $command->queryAll();

        return ($data) ? $data : [];
    }
}
