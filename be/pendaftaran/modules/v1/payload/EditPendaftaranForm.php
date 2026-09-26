<?php
/**
 * @author: [Setyabudi Dwisandi Arifin][setyabudi@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

namespace app\modules\v1\payload;

use Yii;

class EditPendaftaranForm extends \Doco\components\DocoBaseModel
{

    const RANAP_PERIKSA = 'ranap-periksa';
    const ST_YUSUP = 'st-yusup';
    const ST_YUSUP_RANAP = 'st-yusup-ranap';
    const RAJAL_EDIT = 'rajal-edit';
    const RAJAL_EDIT_NOMOR_URUT = 'rajal-edit-nomor-urut';

    public $no_rekam_medik;
    public $nama_pasien;
    public $photopasien;
    public $no_mobile_pasien;
    public $no_pendaftaran;
    public $tgl_pendaftaran;
    public $kelaspelayanan_nama;
    public $carabayar_nama;
    public $penjamin_nama;
    public $tgl_admisi;
    public $jeniskasuspenyakit_id;
    public $kelaspelayanan_id;
    public $carabayar_id;
    public $penjamin_id;
    public $nosep;
    public $keterangan;
    public $pegawai_id;
    public $jenis;
    public $pendaftaran_id;
    public $pasienadmisi_id;
    public $ruangan_id;
    public $kamarruangan_id;
    public $penanggungbiaya_id;
    public $is_pasientitipan;
    public $is_aps;
    public $kamartempattidur_id;
    public $allow_bpjs;
    public $group_carabayar;
    public $asuransi;
    public $kamarruangan_nokamar;
    public $nomor_urut;
    public $limit_tagihan;
    public $styrujukaninstalasi_id;
    public $instalasiasal_id;
    public $dokterpengganti_id;
    public $dokterpengirim_id;
    public $dokterkonsul_id;
    public $hakkelas_id;
    public $kelaspermintaan_id;
    public $prosedurmasuk_id;
    public $diagnosa_awal;
    public $referal;

    protected $xssProtected = [
        'keterangan',
        'nosep',
    ];
    /**
     * @inheritdoc
     */
    public function rules()
    {
       return [
            [[
                'keterangan',
                'pegawai_id',
                'carabayar_id',
                'penjamin_id',
                'kelaspelayanan_id',
                'kamarruangan_nokamar',
                'jeniskasuspenyakit_id',
                'kamarruangan_id',
                'kamartempattidur_id',
                'pendaftaran_id',
            ], 'required', 'on' => 'default'],
            [[
                'keterangan',
                'pegawai_id',
                'carabayar_id',
                'penjamin_id',
                'pendaftaran_id',
                'pasienadmisi_id',
            ], 'required', 'on' => self::RANAP_PERIKSA],
            [[
                'jeniskasuspenyakit_id',
                'keterangan',
                // 'pegawai_id',
                'carabayar_id',
                'penjamin_id',
            ], 'required', 'on' => self::ST_YUSUP],
            [[
                'keterangan',
                // 'pegawai_id',
                'jeniskasuspenyakit_id',
                'carabayar_id',
                'penjamin_id',
                'styrujukaninstalasi_id',
                'ruangan_id',
                'dokterpengirim_id',
            ], 'required', 'on' => 'st-yusup-penunjang'],
            [[
                'keterangan',
                'pegawai_id',
                'carabayar_id',
                'penjamin_id',
                'kelaspelayanan_id',
                'kamarruangan_nokamar',
                'jeniskasuspenyakit_id',
                'kamarruangan_id',
                'kamartempattidur_id',
                'pendaftaran_id',
                'dokterpengirim_id',
            ], 'required', 'on' => self::ST_YUSUP_RANAP],
            [[
                'jeniskasuspenyakit_id',
                'keterangan',
                'pegawai_id',
                'carabayar_id',
                'penjamin_id',
            ], 'required', 'on' => self::RAJAL_EDIT],
            [[
                'jeniskasuspenyakit_id',
                'keterangan',
                'pegawai_id',
                'carabayar_id',
                'penjamin_id',
                'nomor_urut',
            ], 'required', 'on' => self::RAJAL_EDIT_NOMOR_URUT],
            [[
                'no_rekam_medik',
                'nama_pasien',
                'photopasien',
                'no_mobile_pasien',
                'no_pendaftaran',
                'tgl_pendaftaran',
                'kelaspelayanan_nama',
                'carabayar_nama',
                'penjamin_nama',
                'tgl_admisi',
                'jeniskasuspenyakit_id',
                'kelaspelayanan_id',
                'carabayar_id',
                'penjamin_id',
                'nosep',
                'keterangan',
                'pegawai_id',
                'jenis',
                'pendaftaran_id',
                'group_carabayar',
                'pasienadmisi_id',
                'ruangan_id',
                'penanggungbiaya_id',
                'kamarruangan_id',
                'is_pasientitipan',
                'is_aps',
                'kamartempattidur_id',
                'allow_bpjs',
                'asuransi',
                'kamarruangan_nokamar',
                'nomor_urut',
                'limit_tagihan',
                'styrujukaninstalasi_id',
                'instalasiasal_id',
                'dokterpengganti_id',
                'dokterpengirim_id',
                'dokterkonsul_id',
                'hakkelas_id',
                'kelaspermintaan_id',
                'prosedurmasuk_id',
                'diagnosa_awal',
                'referal',
            ],'safe'],
            [[
                'pendaftaran_id',
                'carabayar_id',
                'penjamin_id',
                'kelaspelayanan_id',
                'ruangan_id',
                'pasienadmisi_id',
                'kamarruangan_id',
            ], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'no_rekam_medik' => 'No Rekam Medik',
            'pegawai_id' => 'Dokter DPJP',
            'kamartempattidur_id' => 'Tempat tidur',
            'penjamin_id' => 'Penjamin',
            'kelaspelayanan_id' => 'Kelas Pelayanan',
            'jeniskasuspenyakit_id' => 'Jenis Kasus Penyakit',
            'kamarruangan_nokamar' => 'No. Tempat Tidur',
            'styrujukaninstalasi_id' => 'Rujukan Dari',
            'instalasiasal_id' => 'Penunjang Medis',
            'ruangan_id' => 'Ruangan',
            'dokterpengirim_id' => 'Dokter Pengirim',
        ];
    }

}
