<?php

namespace app\modules\ambulan\models;

use Yii;

class PesanAmbulanForm extends \yii\base\Model
{
     
     const SCENARIO_LUAR = 'luar';
     const SCENARIO_RS = 'rs';

     public $pesanambulan_id;
     public $no_pesanambulan;
     public $tgl_pesanambulan;
     public $tgl_pesanambulan_rs;
     public $pendaftaran_id;
     public $pasien_id;
     public $ambulan_id;
     public $pemakaianambulan_id;
     public $pemesan;
     public $jenis_kelamin;
     public $tempat_lahir;
     public $tgl_lahir;
     public $umur;
     public $asal_pasien;
     public $keluhan;
     public $is_sadar;
     public $is_nafas;
     public $is_nadi;
     public $nama_pj;
     public $kontak_pj;
     public $tujuan_pasien;
     public $kesadaran;
     public $tanda_vital;
     public $td_systolic;
     public $td_diastolic;
     public $detaknadi;
     public $respirasi;
     public $saturasi;
     public $estimasi_biaya;
     public $keterangan;
     public $ruangan_id;
     public $status_ambulan;
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
     public $is_emergency;
     public $qty;
     public $instalasi_asal;
     public $ruangan_asal;
     public $diagnosa_pasien;
     public $jenis;
     public $no_polisi;
     public $jenis_ambulan;
     public $carabayar_nama;
     public $penjamin_nama;
     public $kelaspelayanan_nama;
     public $no_rekam_medik;
     public $nama_pemesan;
     public $tujuan;
     public $kelaspelayanan_id;
     public $penjamin_id;
     public $carabayar_id;
     
    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_LUAR] = [
            'tgl_pesanambulan', 
            'pemesan', 
            'jenis_kelamin', 
            'tgl_lahir', 
            'asal_pasien', 
            'kontak_pj', 
            'nama_pj',
            'no_polisi',
            'jenis_ambulan',
            'keluhan',
            'is_nadi',
            'is_sadar',
            'is_nafas',
            'tempat_lahir',
            'ambulan_id'
        ];
        $scenarios[self::SCENARIO_RS] = [
            'tgl_pesanambulan', 
            'tgl_pesanambulan_rs', 
            'pasien_id', 
            'tujuan_pasien',
            'no_polisi',
            'jenis_ambulan',
            'carabayar_nama',
            'penjamin_nama',
            'kelaspelayanan_nama',
            'pendaftaran_id',
            'pesanambulan_id',
            'no_pesanambulan',
            'pemakaianambulan_id',
            'jenis_kelamin', 
            'tgl_lahir', 
            'kesadaran', 
            'tanda_vital', 
            'td_diastolic', 
            'detaknadi', 
            'respirasi', 
            'td_systolic', 
            'saturasi', 
            'no_rekam_medik', 
            'nama_pemesan', 
            'ambulan_id', 
            'ruangan_asal', 
            'instalasi_asal', 
            'tempat_lahir', 
        ];
        return $scenarios;
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [[
                'jenis', 
                'detaknadi', 
                'tgl_pesanambulan', 
                'tgl_pesanambulan_rs', 
                'tgl_lahir', 
                'created_date', 
                'last_modified_date', 
                'deleted_date',
                'no_polisi',
                'pendaftaran_id',
                'no_rekam_medik',
                'nama_pemesan',
                'pasien_id'
            ], 'safe'],
            [['pendaftaran_id', 'pasien_id', 'ambulan_id', 'pemakaianambulan_id', 'jenis_kelamin', 'umur', 'kontak_pj', 'td_systolic', 'td_diastolic', 'detaknadi', 'respirasi', 'saturasi', 'ruangan_id', 'status_ambulan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'default', 'value' => null],
            [['pendaftaran_id', 'pasien_id', 'ambulan_id', 'pemakaianambulan_id', 'jenis_kelamin', 'umur', 'kontak_pj', 'td_systolic', 'td_diastolic', 'detaknadi', 'respirasi', 'saturasi', 'ruangan_id', 'status_ambulan', 'created_by', 'modified_count', 'last_modified_by', 'deleted_by'], 'integer'],
            
           /* [[
                'tgl_pesanambulan', 
                'pemesan', 
                'jenis_kelamin', 
                'tgl_lahir', 
                'asal_pasien', 
                'nama_pj', 
                'kontak_pj',
                'ambulan_id'
            ], 'required', 'on' => self::SCENARIO_LUAR],*/
            // [['tgl_pesanambulan'], 'required'],
            [[
                'pasien_id', 
                'tujuan_pasien', 
                'ambulan_id'
            ], 'required', 'on' => self::SCENARIO_RS],
            [[
                'kontak_pj', 
                'ambulan_id'
            ], 'number', 'on' => self::SCENARIO_LUAR],
            [[
                'asal_pasien', 
                'keluhan', 
                'tujuan_pasien', 
                'keterangan', 
                'additional_data'
            ], 'string'],
            [[
                'is_sadar', 
                'is_nafas', 
                'is_nadi', 
                'is_deleted', 
                'is_active'
            ], 'boolean'],
            [['estimasi_biaya'], 'number'],
            [['no_pesanambulan', 'tempat_lahir', 'nama_pj'], 'string', 'max' => 100],
            [['pemesan', 'kesadaran', 'tanda_vital'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'pesanambulan_id' => 'Pesanambulan ID',
            'no_pesanambulan' => 'No Pesanambulan',
            'tgl_pesanambulan' => 'Tanggal Pemesanan',
            'tgl_pesanambulan_rs' => 'Tanggal Pemesanan',
            'pendaftaran_id' => 'Pendaftaran ID',
            'pasien_id' => 'No Rekam Medik',
            'ambulan_id' => 'Ambulan',
            'pemakaianambulan_id' => 'Pemakaianambulan ID',
            'pemesan' => 'Nama Pasien / Pemesan',
            'jenis_kelamin' => 'Jenis Kelamin',
            'tempat_lahir' => 'Tempat Lahir',
            'tgl_lahir' => 'Tanggal Lahir',
            'umur' => 'Umur',
            'asal_pasien' => 'Asal Pasien',
            'keluhan' => 'Keluhan',
            'is_sadar' => 'Is Sadar',
            'is_nafas' => 'Is Nafas',
            'is_nadi' => 'Is Nadi',
            'nama_pj' => 'Nama Penanggung Jawab',
            'kontak_pj' => 'No Telepon',
            'tujuan_pasien' => 'Tujuan Pasien',
            'kesadaran' => 'Kesadaran',
            'tanda_vital' => 'Tanda Vital',
            'td_systolic' => 'Td Systolic',
            'td_diastolic' => 'Tekanan Darah Pasien',
            'detaknadi' => 'Detak Nadi',
            'respirasi' => 'Respirasi',
            'saturasi' => 'Saturasi',
            'estimasi_biaya' => 'Estimasi Biaya',
            'keterangan' => 'Keterangan',
            'ruangan_id' => 'Ruangan ID',
            'status_ambulan' => 'Status Ambulan',
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
            'carabayar_nama' => 'Cara bayar / Penjamin',
            'penjamin_nama' => 'Penjamin',
            'kelaspelayanan_nama' => 'Kelas Pelayanan',
        ];
    }
}
