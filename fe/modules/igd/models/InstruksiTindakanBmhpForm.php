<?php
//author: Ardi Pratama

namespace app\modules\igd\models;

use Yii;

/**
 * This is the model class for table "instruksitindakanbmhp_t".
 *
 * @property int $instruksitindakanbmhp_id
 * @property int $instruksi_id
 * @property int $instruksitindakan_id
 * @property int $pendaftaran_id
 * @property int $pasienadmisi_id
 * @property int $pasien_id
 * @property int $kelaspelayanan_id
 * @property int $carabayar_id
 * @property int $penjamin_id
 * @property int $instalasi_id
 * @property int $ruangan_id
 * @property int $jeniskasuspenyakit_id
 * @property string $tgl_pelayanan
 * @property int $daftartindakan_id
 * @property int $tipepaket_id
 * @property int $obatalkes_id
 * @property int $satuankecil_id
 * @property int $qty
 * @property double $harga_jualsatuan
 * @property double $harga_netto
 * @property double $harga_jumlah
 * @property bool $is_ditagihkan
 * @property int $dokter_id
 * @property int $perawat1_id
 * @property int $perawat2_id
 * @property string $status_implementasi
 * @property string $catatan
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
class InstruksiTindakanBmhpForm extends \yii\base\Model
{
    public $instruksi_id;
    public $instruksitindakan_id;
    public $pendaftaran_id;
    public $pasienadmisi_id;
    public $pasien_id;
    public $kelaspelayanan_id;
    public $carabayar_id;
    public $penjamin_id;
    public $instalasi_id;
    public $ruangan_id;
    public $jeniskasuspenyakit_id;
    public $tgl_pelayanan;
    public $daftartindakan_id;
    public $tipepaket_id;
    public $obatalkes_id;
    public $satuankecil_id;
    public $qty;
    public $harga_jualsatuan;
    public $harga_netto;
    public $harga_jumlah;
    public $is_ditagihkan;
    public $dokter_id;
    public $obat;
    public $perawat1_id;
    public $perawat2_id;
    public $obatalkes_nama;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['qty', 'obatalkes_id'], 'required'],
            [[
                'instruksi_id',
                'instruksitindakan_id',
                'pendaftaran_id',
                'pasienadmisi_id',
                'pasien_id',
                'kelaspelayanan_id',
                'carabayar_id',
                'penjamin_id',
                'instalasi_id',
                'ruangan_id',
                'jeniskasuspenyakit_id',
                'tgl_pelayanan',
                'daftartindakan_id',
                'tipepaket_id',
                'obatalkes_id',
                'satuankecil_id',
                'qty',
                'harga_jualsatuan',
                'harga_netto',
                'harga_jumlah',
                'is_ditagihkan',
                'dokter_id',
                'obat',
                'perawat1_id',
                'perawat2_id',
                'obatalkes_nama',
            ], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'daftartindakan_id' => 'Tindakan',
            'obatalkes_id' => 'Obat / Alkes',
            'perawat1_id' => 'Perawat 1',
            'perawat2_id' => 'Perawat 2',
        ];
    }
}
