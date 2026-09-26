<?php

namespace app\modules\v1\models;

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
class InstruksiTindakanBmhp extends \Doco\components\DocoActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'instruksitindakanbmhp_t';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['instruksi_id', 'pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'kelaspelayanan_id', 'carabayar_id', 'penjamin_id', 'instalasi_id', 'ruangan_id', 'jeniskasuspenyakit_id', 'tgl_pelayanan', 'obatalkes_id', 'qty'], 'required'],
            [['instruksi_id', 'instruksitindakan_id', 'pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'kelaspelayanan_id', 'carabayar_id', 'penjamin_id', 'instalasi_id', 'ruangan_id', 'jeniskasuspenyakit_id', 'daftartindakan_id', 'tipepaket_id', 'obatalkes_id', 'satuankecil_id', 'qty', 'dokter_id', 'perawat1_id', 'perawat2_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['instruksitindakanbmhp_id', 'instruksi_id', 'instruksitindakan_id', 'pendaftaran_id', 'pasienadmisi_id', 'pasien_id', 'kelaspelayanan_id', 'carabayar_id', 'penjamin_id', 'instalasi_id', 'ruangan_id', 'jeniskasuspenyakit_id', 'daftartindakan_id', 'tipepaket_id', 'obatalkes_id', 'satuankecil_id', 'dokter_id', 'perawat1_id', 'perawat2_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['tgl_pelayanan', 'instruksitindakan_id','created_date', 'last_modified_date', 'deleted_date'], 'safe'],
            [['harga_jualsatuan', 'harga_netto', 'harga_jumlah'], 'number'],
            [['is_ditagihkan', 'is_deleted', 'is_active'], 'boolean'],
            [['catatan', 'additional_data'], 'string'],
            [['status_implementasi'], 'string', 'max' => 100],
            [['instruksitindakanbmhp_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'instruksitindakanbmhp_id' => 'Instruksitindakanbmhp ID',
            'instruksi_id' => 'Instruksi ID',
            'instruksitindakan_id' => 'Instruksitindakan ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'pasien_id' => 'Pasien ID',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'carabayar_id' => 'Carabayar ID',
            'penjamin_id' => 'Penjamin ID',
            'instalasi_id' => 'Instalasi ID',
            'ruangan_id' => 'Ruangan ID',
            'jeniskasuspenyakit_id' => 'Jeniskasuspenyakit ID',
            'tgl_pelayanan' => 'Tgl Pelayanan',
            'daftartindakan_id' => 'Daftartindakan ID',
            'tipepaket_id' => 'Tipepaket ID',
            'obatalkes_id' => 'Obatalkes ID',
            'satuankecil_id' => 'Satuankecil ID',
            'qty' => 'Qty',
            'harga_jualsatuan' => 'Harga Jualsatuan',
            'harga_netto' => 'Harga Netto',
            'harga_jumlah' => 'Harga Jumlah',
            'is_ditagihkan' => 'Is Ditagihkan',
            'dokter_id' => 'Dokter ID',
            'perawat1_id' => 'Perawat1 ID',
            'perawat2_id' => 'Perawat2 ID',
            'status_implementasi' => 'Status Implementasi',
            'catatan' => 'Catatan',
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
