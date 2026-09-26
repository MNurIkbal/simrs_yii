<?php

namespace app\modules\penjaminasuransi\models;

class PengajuanKlaimForm extends \yii\base\Model
{
    public $pengajuanklaim_id;
    public $carabayar_id;
    public $penjamin_id;
    public $tgl_pengajuanklaim;
    public $no_pengajuanklaim;
    public $tgl_jatuhtempo;
    public $tgl_pelayanansampai;
    public $tgl_pelayanandari;
    public $tgl_keluarsampai;
    public $tgl_keluardari;
    public $total_piutang;
    public $total_terbayar;
    public $total_sisapiutang;
    public $alamat_penjamin;
    public $npwp;
    public $totalbiaya_obat;
    public $totalbiaya_tindakan;
    public $pegawaimengetahui_id;
    public $catatan;
    public $status_pengajuanklaim;
    public $additional_data;
    public $created_date;
    public $created_by;
    public $modified_count;
    public $last_modified_date;
    public $last_modified_by;
    public $is_deleted;
    public $is_active;
    public $deleted_date;
    public $deleted_by;
    public $tanggal_masuk;
    public $tanggal_keluar;
    public $carabayar_nama;
    public $penjamin_nama;
    public $instalasi_nama;
    public $instalasi_id;
    public $ruangan_nama;
    public $ruangan_id;
    public $tanggal_pengajuan;
    public $total_pengajuan;
    public $pendaftaran_id;
    public $pembayaranpelayanan_id;

    public static function tableName()
    {
        return 'pengajuanklaim_t';
    }

    public function rules()
    {
        return [
            [
                [
                    'carabayar_id',
                    'penjamin_id',
                    'no_pengajuanklaim',
                    'tgl_jatuhtempo',
                ], 'required' // REQUIRED
            ],
            [
                [
                    'carabayar_id',
                    'penjamin_id',
                    'pegawaimengetahui_id',
                    'status_pengajuanklaim',
                    'created_by',
                    'modified_count',
                    'last_modified_by',
                    'deleted_by'
                ], 'default', 'value' => null // DEFAULT VALUE NULL
            ],
            [
                [
                    'carabayar_id',
                    'penjamin_id',
                    'pegawaimengetahui_id',
                    'status_pengajuanklaim',
                    'created_by',
                    'modified_count',
                    'last_modified_by',
                    'deleted_by'
                ], 'integer' // INTEGER
            ],
            [
                [
                    'pendaftaran_id',
                    'tgl_pengajuanklaim',
                    'tgl_jatuhtempo',
                    'tgl_pelayanansampai',
                    'tgl_pelayanandari',
                    'created_date',
                    'last_modified_date',
                    'deleted_date',
                    'instalasi_id',
                    'ruangan_id',
                    'tgl_keluarsampai',
                    'tgl_keluardari'
                ], 'safe' // SAFE
            ],
            [
                [
                    'total_piutang',
                    'total_terbayar',
                    'total_sisapiutang',
                    'totalbiaya_obat',
                    'totalbiaya_tindakan'
                ], 'number' // NUMBER
            ],
            [
                [
                    'alamat_penjamin',
                    'catatan',
                    'additional_data'
                ], 'string' // STRING
            ],
            [
                [
                    'is_deleted',
                    'is_active'
                ], 'boolean' // BOOLEAN
            ],
            [
                [
                    'no_pengajuanklaim'
                ], 'string', 'max' => 255 // STRING MAX 255
            ],
            [
                [
                    'no_pengajuanklaim'
                ], 'string', 'min' => 3 // STRING MIN 3
            ],
            [
                [
                    'npwp'
                ], 'string', 'max' => 100 // STRING MAX 100
            ],
            [
                [
                    'catatan'
                ], 'string', 'max' => 200 // STRING MAX 200
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'pengajuanklaim_id' => 'Pengajuanklaim ID',
            'carabayar_id' => 'Cara Bayar',
            'penjamin_id' => 'Penjamin',
            'tgl_pengajuanklaim' => 'Tanggal Pengajuan',
            'no_pengajuanklaim' => 'No Pengajuan Klaim',
            'tgl_jatuhtempo' => 'Tanggal Jatuh Tempo',
            'tgl_pelayanansampai' => 'Tgl Pelayanansampai',
            'tgl_pelayanandari' => 'Tgl Pelayanandari',
            'tgl_keluarsampai' => 'Tgl Keluar Sampai',
            'tgl_keluardari' => 'Tgl Keluar Dari',
            'total_piutang' => 'Total Piutang',
            'total_terbayar' => 'Total Terbayar',
            'total_sisapiutang' => 'Total Sisapiutang',
            'alamat_penjamin' => 'Alamat Penjamin',
            'npwp' => 'Npwp',
            'totalbiaya_obat' => 'Totalbiaya Obat',
            'totalbiaya_tindakan' => 'Totalbiaya Tindakan',
            'pegawaimengetahui_id' => 'Pegawaimengetahui ID',
            'catatan' => 'Catatan',
            'status_pengajuanklaim' => 'Status Pengajuanklaim',
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
