<?php

namespace SirsCore\models;

use Yii;

/**
 * This is the model class for table "returresep_t".
 *
 * @property int $returresep_id
 * @property int $ruangan_id
 * @property int $penjualanresep_id
 * @property int $pendaftaran_id
 * @property int $pasien_id
 * @property int $pasienadmisi_id
 * @property string $tgl_retur
 * @property string $no_returresep
 * @property string $alasan_retur
 * @property string $keterangan_retur
 * @property int $pegawaimengetahui_id
 * @property int $pegawairetur_id
 * @property double $total_retur
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
 * @property PenjualanresepT[] $penjualanresepTs
 * @property ReturbayarpelayananT[] $returbayarpelayananTs
 * @property PasienM $pasien
 * @property PasienadmisiT $pasienadmisi
 * @property PegawaiM $pegawaimengetahui
 * @property PegawaiM $pegawairetur
 * @property PendaftaranT $pendaftaran
 * @property PenjualanresepT $penjualanresep
 * @property RuanganM $ruangan
 */
class ReturResep extends \Doco\components\DocoActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'returresep_t';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ruangan_id', 'penjualanresep_id', 'pendaftaran_id', 'pasien_id', 'pasienadmisi_id', 'pegawaimengetahui_id', 'pegawairetur_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['ruangan_id', 'penjualanresep_id', 'pendaftaran_id', 'pasien_id', 'pasienadmisi_id', 'pegawaimengetahui_id', 'pegawairetur_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_retur', 'created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['no_returresep', 'alasan_retur', 'keterangan_retur', 'additional_data'], 'string'],
            [['total_retur'], 'number'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_returresep'], 'unique']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'returresep_id' => 'Returresep ID',
            'ruangan_id' => 'Ruangan ID',
            'penjualanresep_id' => 'Penjualanresep ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasien_id' => 'Pasien ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'tgl_retur' => 'Tgl Retur',
            'no_returresep' => 'No Returresep',
            'alasan_retur' => 'Alasan Retur',
            'keterangan_retur' => 'Keterangan Retur',
            'pegawaimengetahui_id' => 'Pegawaimengetahui ID',
            'pegawairetur_id' => 'Pegawairetur ID',
            'total_retur' => 'Total Retur',
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

    public function getDataReturById($id)
    {
        $connection = Yii::$app->db;
        $sql = " SELECT
                r.no_returresep,
                r.tgl_retur,
                pr.jenispenjualan,
                fgetnamalookup(pr.jenispenjualan::integer) AS jenis_penjualan,
                pr.noresep,
                pas.nama_pasien,
                pr.nama_pembeli,
                karyawan.nama_pegawai AS nama_karyawan,
                pr.tglpenjualan
            FROM
                returresep_t r
            LEFT JOIN penjualanresep_t pr ON pr.penjualanresep_id = r.penjualanresep_id
            LEFT JOIN pasien_m pas ON pas.pasien_id = pr.pasien_id
            LEFT JOIN pegawai_m karyawan ON pr.karyawan_id = karyawan.pegawai_id
            WHERE
                r.returresep_id = :id
        ";
        $command = $connection->createCommand($sql);
        $command->bindValue(':id', $id);
        $data = $command->queryOne();

        return ($data) ? $data : [];
    }

    public function getDetailReturById($id)
    {
        $connection = Yii::$app->db;
        $sql = " SELECT
                o.obatalkes_nama as nama_obat,
                i.hargasatuan_oa,
                i.hargajual_oa,
                o.ppn_persen,
                i.qty_oa,
                r.qty_retur,
                r.obatalkespasien_id,
                s.tglkadaluarsa
            FROM returresepdetail_t r
            JOIN stokobatalkes_t s ON s.obatalkespasien_id = r.obatalkespasien_id
            JOIN obatalkespasien_t i ON i.obatalkespasien_id = r.obatalkespasien_id
            JOIN obatalkes_m o ON o.obatalkes_id = i.obatalkes_id
            JOIN (
                SELECT
                    obatalkespasien_id,
                    SUM(qty_retur) as qty_retur
                FROM returresepdetail_t
                GROUP BY obatalkespasien_id
            ) total_retur ON total_retur.obatalkespasien_id = r.obatalkespasien_id
            WHERE
                r.returresep_id = :id
            GROUP BY
                o.obatalkes_nama,
                i.hargasatuan_oa,
                i.hargajual_oa,
                o.ppn_persen,
                i.qty_oa,
                r.qty_retur,
                r.obatalkespasien_id,
                s.tglkadaluarsa,
                total_retur.qty_retur
        ";
        $command = $connection->createCommand($sql);
        $command->bindValue(':id', $id);
        $data = $command->queryAll();

        return ($data) ? $data : [];
    }
}
