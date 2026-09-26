<?php

use yii\db\Migration;

/**
 * Class m190114_042258_create_view_sync_pendaftaran
 */
class m190114_042258_create_view_sync_pendaftaran extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE VIEW sync_pendaftaran
            AS
               SELECT pendaftaran_t.pendaftaran_id,
                pendaftaran_t.no_pendaftaran AS "No_Reg",
                to_char(pendaftaran_t.tgl_pendaftaran, \'YYYY-MM-DD\'::text) AS "Tanggal",
                to_char(pendaftaran_t.tgl_pendaftaran, \'HH:MM:SS\'::text) AS "Jam",
                pasien_m.pasien_id,
                pasien_m.no_rekam_medik AS "Kode_CM",
                ruangan_m.ruangan_id,
                    CASE
                        WHEN (ruangan_m.kode_ruanganpoli IS NULL) THEN \'\'::character varying(3)
                        ELSE ruangan_m.kode_ruanganpoli
                    END AS kode_subunit,
                \'E001\' AS "Kode_Event",
                pendaftaran_t.kunjungan,
                kunjungan.lookup_kode AS "Kode_StsCM",
                pendidikan_m.pendidikan_kode AS "Kode_Pendidikan",
                pekerjaan_m.pekerjaan_kode AS "Kode_Pekerjaan",
                perkawinan.lookup_kode AS "Kode_StsKawin",
                pendaftaran_t.rujukan_id,
                pendaftaran_t.bpjs_id,
                rujukan_t.asalrujukan_id,
                    CASE
                        WHEN (pendaftaran_t.bpjs_id IS NOT NULL) THEN \'1\'::character varying
                        ELSE asalrujukan_m.asalrujukan_kode
                    END AS "Kode_Rujukan",
                rujukan_t.rujukandari_id,
                    CASE
                        WHEN (pendaftaran_t.bpjs_id IS NOT NULL) THEN bpjs_t.ppkpelayanan
                        ELSE rujukandari_m.kode_ppk
                    END AS kode_ppk,
                    CASE
                        WHEN (pendaftaran_t.bpjs_id IS NOT NULL) THEN bpjs_t.ppkrujukan
                        ELSE rujukandari_m.nama_perujuk
                    END AS "Nama_Perujuk",
                carabayar_m.carabayar_kode AS "Kode_StsBayar",
                penanggungjawab_m.penanggungjawab_nama AS "Nama_Pengantar",
                penanggungjawab_m.penanggungjawab_alamat AS "Alamat_Pengantar",
                penanggungjawab_m.penanggungjawab_notelp AS "Telp_Pengantar",
                penanggungjawab_m.penanggungjawab_nohp AS hp_pengantar,
                pengantar.lookup_name AS "Hub_Pengantar",
                penanggungjawab_m.penanggungjawab_nama AS "Nama_Penanggung",
                penanggungjawab_m.penanggungjawab_alamat AS "Alamat_Penanggung",
                penanggungjawab_m.penanggungjawab_notelp AS "Telp_Penanggung",
                penanggungjawab_m.penanggungjawab_nohp AS hp_penanggung,
                pengantar.lookup_name AS "Hub_Penanggung",
                bpjs_t.tglsep AS tgl_commit,
                pendaftaran_t.keterangan_pendaftaran AS "Keterangan",
                NULL::unknown AS "CEK",
                peg_pendaftaran.nomorindukpegawai AS "Petugas",
                dokter.nomorindukpegawai AS "NPRS_PJawab",
                bpjs_t.klsrawat AS "Hak_Kelas",
                    CASE
                        WHEN (bpjs_t.politujuan IS NULL) THEN \'\'::character varying(100)
                        ELSE bpjs_t.politujuan
                    END AS kode_subunitasal,
                bpjs_t.diagnosaawal AS "Diag_Awal",
                0 AS "IS_CLONED",
                penjamin_m.s_kode AS "KODE_PERUSAHAAN",
                bpjs_t.nokartuasuransi AS no_kartu,
                bpjs_t.nosep AS no_sep
            FROM (((((((((((((((((pendaftaran_t
                JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
                LEFT JOIN lookup_m kunjungan ON (((pendaftaran_t.kunjungan)::integer = kunjungan.lookup_id)))
                LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
                LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
                LEFT JOIN lookup_m perkawinan ON (((pasien_m.statusperkawinan)::integer = perkawinan.lookup_id)))
                LEFT JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
                LEFT JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
                LEFT JOIN rujukandari_m ON ((rujukan_t.rujukandari_id = rujukandari_m.rujukandari_id)))
                LEFT JOIN bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
                LEFT JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                LEFT JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
                LEFT JOIN lookup_m pengantar ON (((penanggungjawab_m.pengantar)::integer = pengantar.lookup_id)))
                LEFT JOIN pegawai_m dokter ON ((pendaftaran_t.pegawai_id = dokter.pegawai_id)))
                LEFT JOIN loginpemakai_k ON ((pendaftaran_t.created_by = loginpemakai_k.loginpemakai_id)))
                LEFT JOIN pegawai_m peg_pendaftaran ON ((loginpemakai_k.pegawai_id = peg_pendaftaran.pegawai_id)));
        ');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m190114_042258_create_view_sync_pendaftaran cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190114_042258_create_view_sync_pendaftaran cannot be reverted.\n";

        return false;
    }
    */
}
