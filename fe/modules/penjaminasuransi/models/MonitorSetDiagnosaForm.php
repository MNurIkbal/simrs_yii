<?php

namespace app\modules\penjaminasuransi\models;

 use Yii;

class MonitorSetDiagnosaForm extends \yii\base\Model
{
     public $monitorsetdiagnosa_id;
     public $pendaftaran_id;
     public $pasienadmisi_id;
     public $diag_utama_id;
     public $diag_penyerta;
     public $diag_tindakan;
     public $hak_kelas;
     public $kelaspelayanan_id;
     public $kelaspelayanan_nama;
     public $total;
     public $tambahan_biaya;
     public $persen_tambahan;
     public $total_naikkelas;
     public $total_kelaspelayanan;
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

     public $tgl_masuk;
     public $tgl_keluar;
     public $dokter_dpjp;
     public $diag_utama_kode;
     public $no_rekam_medik;
     public $nama_pasien;
     public $jeniskelamin;
     public $tanggal_lahir;
     public $no_sep;
     public $no_kartu;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['pendaftaran_id', 'pasienadmisi_id', 'diag_utama_id'], 'required'],
            [['pendaftaran_id', 'pasienadmisi_id', 'diag_utama_id', 'hak_kelas', 'kelaspelayanan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasienadmisi_id', 'diag_utama_id', 'hak_kelas', 'kelaspelayanan_id', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            [['diag_penyerta', 'diag_tindakan', 'additional_data'], 'string'],
            [['total', 'tambahan_biaya', 'persen_tambahan', 'total_naikkelas', 'total_kelaspelayanan'], 'number'],
            [['created_date', 'last_modified_date', 'deleted_date', 'no_sep', 'no_kartu'], 'safe'],
            [['is_deleted', 'is_active'], 'boolean'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'monitorsetdiagnosa_id' => 'Monitorsetdiagnosa ID',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasienadmisi_id' => 'Pasienadmisi ID',
            'diag_utama_id' => 'Diagnosa Utama',
            'diag_penyerta' => 'Diag Penyerta',
            'diag_tindakan' => 'Diag Tindakan',
            'hak_kelas' => 'Hak Kelas',
            'kelaspelayanan_id' => 'Kelaspelayanan ID',
            'total' => 'Total',
            'tambahan_biaya' => 'Tambahan Biaya',
            'persen_tambahan' => 'Persen Tambahan',
            'total_naikkelas' => 'Total Naikkelas',
            'total_kelaspelayanan' => 'Total Kelaspelayanan',
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
