<?php

namespace app\models;

use Yii;
use yii\base\Model;

class BatalInstruksiForm extends Model
{
    const SCENARIO_INSTRUKSI = 'instruksi';
    const SCENARIO_TINDAKANRJ = 'tindakanrj';
    const SCENARIO_BMHPRJ = 'bmhprj';
    const SCENARIO_PENUNJANG = 'penunjang';
    const SCENARIO_RESEPTUR = 'reseptur';
    const SCENARIO_PENUNJANGRJ = 'penunjangrj';

    public $instruksitindakan_id;
    public $instruksi_id;
    public $tindakanpelayanan_id;
    public $obatalkespasien_id;
    public $deleted_by;
    public $deleted_date;
    public $alasan_pembatalan;
    public $tipe_instruksi;
    public $deleted_by_password;
    public $pendaftaran_id;
    public $pasienadmisi_id;
    public $type;
    public $jenis;
    public $instalasi_id;
    public $noresep;
    public $permintaankepenunjang_id;

    public function scenarios()
    {
        return [
            self::SCENARIO_INSTRUKSI => ['instruksi_id','instruksitindakan_id', 'alasan_pembatalan', 'deleted_by_password', 'jenis', 'pendaftaran_id'],
            self::SCENARIO_TINDAKANRJ => ['instruksi_id','tindakanpelayanan_id', 'pendaftaran_id', 'deleted_by_password', 'alasan_pembatalan'],
            self::SCENARIO_BMHPRJ => ['instruksi_id','obatalkespasien_id', 'pendaftaran_id', 'deleted_by_password', 'alasan_pembatalan'],
            self::SCENARIO_PENUNJANG => ['instruksi_id','instruksitindakan_id', 'alasan_pembatalan', 'deleted_by_password', 'jenis', 'pendaftaran_id', 'tipe_instruksi', 'instalasi_id'],
            self::SCENARIO_RESEPTUR => ['instruksi_id','pendaftaran_id', 'noresep', 'deleted_by_password', 'alasan_pembatalan', 'tipe_instruksi'],
            self::SCENARIO_PENUNJANGRJ => ['instruksi_id','permintaankepenunjang_id', 'alasan_pembatalan', 'deleted_by_password', 'jenis', 'pendaftaran_id', 'tipe_instruksi', 'instalasi_id']
        ];
    }

    public function rules()
    {
        return [
            [
                [
                    'instruksitindakan_id', 'alasan_pembatalan', 'deleted_by_password'
                ], 'required', 'message' => '{attribute} tidak boleh kosong', 'on' => self::SCENARIO_INSTRUKSI
            ],
            [
                [
                    'tindakanpelayanan_id', 'alasan_pembatalan', 'deleted_by_password'
                ], 'required', 'message' => '{attribute} tidak boleh kosong', 'on' => self::SCENARIO_TINDAKANRJ
            ],
            [
                [
                    'obatalkespasien_id', 'alasan_pembatalan', 'deleted_by_password'
                ], 'required', 'message' => '{attribute} tidak boleh kosong', 'on' => self::SCENARIO_BMHPRJ
            ],
            [
                [
                    'instruksitindakan_id', 'alasan_pembatalan', 'deleted_by_password', 'instalasi_id'
                ], 'required', 'message' => '{attribute} tidak boleh kosong', 'on' => self::SCENARIO_PENUNJANG
            ],
            [
                [
                    'pendaftaran_id', 'noresep', 'alasan_pembatalan', 'deleted_by_password', 'instalasi_id'
                ], 'required', 'message' => '{attribute} tidak boleh kosong', 'on' => self::SCENARIO_RESEPTUR
            ],
            [
                [
                    'permintaankepenunjang_id', 'alasan_pembatalan', 'deleted_by_password', 'instalasi_id'
                ], 'required', 'message' => '{attribute} tidak boleh kosong', 'on' => self::SCENARIO_PENUNJANGRJ
            ],
            [
                [
                    'instruksitindakan_id','instruksi_id', 'deleted_by', 'deleted_date', 'alasan_pembatalan', 'tipe_instruksi', 'tindakanpelayanan_id', 'obatalkespasien_id',
                    'deleted_by_password', 'pendaftaran_id', 'pasienadmisi_id', 'type', 'jenis', 'instalasi_id', 'permintaankepenunjang_id'
                ], 'safe'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'instruksitindakan_id' => 'ID Instruksi Tindakan',
            'deleted_by' => 'Pegawai Pembatalan',
            'deleted_date' => 'Tanggal Pembatalan',
            'deleted_by_password' => 'Kata Sandi',
            'alasan_pembatalan' => 'Alasan Pembatalan'
        ];
    }
}
