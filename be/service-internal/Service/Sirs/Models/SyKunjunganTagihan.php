<?php

namespace Integrasi\Service\Sirs\Models;

use Yii;

/**
 * This is the model class for table "sy_kunjungantagihan".
 *
 * @property int $kunjungantagihan_id
 * @property int $kunjungan_id
 * @property string $no_pendaftaran
 * @property string $no_rekammedik
 * @property string $layanan_kode
 * @property string $layanan_nama
 * @property double $layanan_qty
 * @property double $layanan_tarif

 * @property string $tindakan_kode
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
 * @property string $ruangan_kode
 * @property string $ruangan_nama
 * @property double $jasa_rs
 * @property double $jasa_dokter
 * @property string $dokter_kode
 * @property string $dokter_nama
 * @property string $kode_nota
 * @property string $kel_report
 * @property double $tarifrs_akt
 * @property string $no_buktitrans
 * @property string $kelas_kode
 * @property string $tgl_pendaftaran
 * @property double $total_adjust
 * @property string $kode_adjust
 * @property string $status_bayar
 * @property int $groupinacbg_id
 * @property string $groupinacbg_nama
 * @property string $groupinacbg_kode
 */

class SyKunjunganTagihan extends \Integrasi\Components\ActiveRepositories
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'sy_kunjungantagihan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kunjungan_id', 'no_pendaftaran', 'no_rekammedik'], 'required'],
            [['kunjungan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by', 'groupinacbg_id'], 'default', 'value' => null],
            [['kunjungan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['layanan_qty', 'layanan_tarif', 'jasa_rs', 'jasa_dokter', 'tarifrs_akt', 'total_adjust'], 'number'],
            [['additional_data'], 'string'],
            [['created_date', 'last_modified_date', 'deleted_date', 'tgl_pendaftaran'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
            [['no_pendaftaran', 'no_rekammedik', 'tindakan_kode', 'dokter_kode', 'dokter_nama', 'groupinacbg_nama', 'groupinacbg_kode', 'groupinacbg_id'], 'string', 'max' => 150],
            [['kode_nota', 'kode_adjust'], 'string', 'max' => 100],
            [['kelas_kode'], 'string', 'max' => 100],
            [['status_bayar'], 'string', 'max' => 10],
            [['layanan_kode', 'layanan_nama', 'ruangan_kode', 'ruangan_nama', 'kel_report', 'no_buktitrans'], 'string', 'max' => 255],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'kunjungantagihan_id' => 'Kunjungantagihan ID',
            'kunjungan_id' => 'Kunjungan ID',
            'no_pendaftaran' => 'No Pendaftaran',
            'no_rekammedik' => 'No Rekammedik',
            'layanan_kode' => 'Layanan Kode',
            'layanan_nama' => 'Layanan Nama',
            'layanan_qty' => 'Layanan Qty',
            'layanan_tarif' => 'Layanan Tarif',
            'tindakan_kode' => 'Tindakan Kode',
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
            'ruangan_kode' => 'Ruangan Kode',
            'ruangan_nama' => 'Ruangan Nama',
            'jasa_rs' => 'Jasa Rumah Sakit',
            'jasa_dokter' => 'Jasa Dokter',
            'dokter_kode' => 'Kode Dokter',
            'dokter_nama' => 'Nama Dokter',
            'kode_nota' => 'Kode Nota',
            'kel_report' => 'Kelompok Laporan',
            'tarifrs_akt' => 'Tarif RS Aktual',
            'no_buktitrans' => 'No Bukti Transaksi',
            'tgl_pendaftaran' => 'Tgl Pendaftaran',
            'total_adjust' => 'Total Adjusment',
            'kode_adjust' => 'Kode Adjusment',
            'status_bayar' => 'Status Bayar',
            'groupinacbg_id' => "ID Group Inacbg",
            'groupinacbg_nama' => "Nama Group Inacbg",
            'groupinacbg_kode' => 'Kode Group Inacbg'
        ];
    }
}
