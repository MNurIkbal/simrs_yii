<?php

use yii\db\Migration;

/**
 * Class m190114_042308_create_view_sync_pasien
 */
class m190114_042308_create_view_sync_pasien extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE VIEW sync_pasien
            AS
             SELECT pasien_m.no_rekam_medik AS "kode_CM",
                \'0\' AS "Kode_stsCM",
                pasien_m.namadepan AS "Gelar_Depan",
                pasien_m.nama_pasien AS "Nama",
                NULL::unknown AS "Gelar_Belakang",
                pasien_m.jeniskelamin,
                jk.lookup_kode AS "Kode_JnsKelamin",
                pasien_m.nama_ibu AS "Ibu_kandung",
                pasien_m.tempat_lahir AS "Tempat_lahir",
                pasien_m.tanggal_lahir AS "Tgl_lahir",
                agama.lookup_kode AS "Kode_Agama",
                pasien_m.alamat_pasien AS "Alamat",
                pasien_m.rt AS "RT",
                pasien_m.rw AS "RW",
                NULL::unknown AS "Kode_Kelurahan",
                NULL::unknown AS "Kode_Kecamatan",
                NULL::unknown AS "Kode_Kabupaten",
                NULL::unknown AS "Kode_Propinsi",
                warga_negara.lookup_kode AS "Kode_Negara",
                warga_negara.lookup_kode AS "Warga_Negara",
                pendidikan_m.pendidikan_namalainnya AS "Kode_Pendidikan",
                pekerjaan_m.pekerjaan_namalainnya AS "Kode_Pekerjaan",
                sts_kawin.lookup_kode AS "Kode_stsKawin",
                pasien_m.created_date AS "Tgl_Daftar",
                ( SELECT carabayar_m.carabayar_kode
                    FROM (pendaftaran_t
                        JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
                    WHERE (pasien_m.pasien_id = pendaftaran_t.pasien_id)
                    ORDER BY pendaftaran_t.tgl_pendaftaran DESC
                    LIMIT 1) AS "Kode_StsBayar",
                ( SELECT
                            CASE
                                WHEN (pendaftaran_t.bpjs_id IS NOT NULL) THEN \'1\'::character varying
                                ELSE asalrujukan_m.asalrujukan_kode
                            END AS "Kode_Rujukan"
                    FROM ((pendaftaran_t
                        LEFT JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
                        LEFT JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
                    WHERE (pasien_m.pasien_id = pendaftaran_t.pasien_id)
                    ORDER BY pendaftaran_t.tgl_pendaftaran DESC
                    LIMIT 1) AS "Kode_Rujukan",
                ( SELECT
                            CASE
                                WHEN (pendaftaran_t.bpjs_id IS NOT NULL) THEN bpjs_t.ppkrujukan
                                ELSE rujukandari_m.nama_perujuk
                            END AS "Nama_Perujuk"
                    FROM (((pendaftaran_t
                        LEFT JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
                        LEFT JOIN rujukandari_m ON ((rujukan_t.rujukandari_id = rujukandari_m.rujukandari_id)))
                        LEFT JOIN bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
                    WHERE (pasien_m.pasien_id = pendaftaran_t.pasien_id)
                    ORDER BY pendaftaran_t.tgl_pendaftaran DESC
                    LIMIT 1) AS "Nama_Perujuk",
                ( SELECT penjamin_m.s_kode
                    FROM (pendaftaran_t
                        LEFT JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
                    WHERE (pasien_m.pasien_id = pendaftaran_t.pasien_id)
                    ORDER BY pendaftaran_t.tgl_pendaftaran DESC
                    LIMIT 1) AS "Kode_Perusahaan",
                pasien_m.no_identitas_pasien AS "No_Identitas",
                pasien_m.no_telepon_pasien AS "Telepon",
                ( SELECT pendaftaran_t.tgl_pendaftaran
                    FROM pendaftaran_t
                    WHERE (pasien_m.pasien_id = pendaftaran_t.pasien_id)
                    ORDER BY pendaftaran_t.tgl_pendaftaran DESC
                    LIMIT 1) AS tgl_kunjungan,
                ( SELECT pendaftaran_t.keterangan_pendaftaran
                    FROM pendaftaran_t
                    WHERE (pasien_m.pasien_id = pendaftaran_t.pasien_id)
                    ORDER BY pendaftaran_t.tgl_pendaftaran DESC
                    LIMIT 1) AS "KETERANGAN",
                ( SELECT bpjs_t.nokartuasuransi
                    FROM (pendaftaran_t
                        LEFT JOIN bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
                    WHERE (pasien_m.pasien_id = pendaftaran_t.pasien_id)
                    ORDER BY pendaftaran_t.tgl_pendaftaran DESC
                    LIMIT 1) AS "No_Kartu",
                0 AS retensi_status,
                NULL::unknown AS retensi_nprs,
                NULL::unknown AS retensi_tgl_acuan
            FROM ((((((pasien_m
                LEFT JOIN lookup_m jk ON (((pasien_m.jeniskelamin)::integer = jk.lookup_id)))
                LEFT JOIN lookup_m agama ON (((pasien_m.agama)::integer = agama.lookup_id)))
                LEFT JOIN lookup_m warga_negara ON (((pasien_m.warga_negara)::integer = warga_negara.lookup_id)))
                LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
                LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
                LEFT JOIN lookup_m sts_kawin ON (((pasien_m.statusperkawinan)::integer = sts_kawin.lookup_id)));
        ');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m190114_042308_create_view_sync_pasien cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190114_042308_create_view_sync_pasien cannot be reverted.\n";

        return false;
    }
    */
}
