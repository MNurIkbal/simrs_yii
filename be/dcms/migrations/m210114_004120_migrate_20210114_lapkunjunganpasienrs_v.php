<?php

use yii\db\Migration;

/**
 * Class m210114_004120_migrate_20210114_lapkunjunganpasienrs_v
 */
class m210114_004120_migrate_20210114_lapkunjunganpasienrs_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.lapkunjunganpasrs_v;');
        $this->execute('DROP VIEW if exists public.lapkunjunganpasienrs_v;');
        $this->execute("CREATE VIEW \"public\".\"lapkunjunganpasienrs_v\" AS
             SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pasien_m.jeniskelamin,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.carabayar_id
            ELSE pasienadmisi_t.carabayar_id
        END AS carabayar_id,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.penjamin_id
            ELSE pasienadmisi_t.penjamin_id
        END AS penjamin_id,
    pendaftaran_t.jeniskasuspenyakit_id,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.instalasi_id
            ELSE ruang_admisi.instalasi_id
        END AS instalasi_id,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.ruangan_id
            ELSE pasienadmisi_t.ruangan_id
        END AS ruangan_id,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.pegawai_id
            ELSE pasienadmisi_t.pegawai_id
        END AS dokterdpjp_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.instalasi_id AS instalasi_asal,
    pasien_m.no_rekam_medik,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    pasien_m.alamat_pasien,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN carabayar_m.carabayar_nama
            ELSE cb_admisi.carabayar_nama
        END AS carabayar_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN penjamin_m.penjamin_nama
            ELSE pj_admisi.penjamin_nama
        END AS penjamin_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN instalasi_m.instalasi_nama
            ELSE instalasi_admisi.instalasi_nama
        END AS instalasi_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruangan_m.ruangan_nama
            ELSE ruang_admisi.ruangan_nama
        END AS ruangan_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pegawai_m.nama_pegawai
            ELSE peg_admisi.nama_pegawai
        END AS dokterdpjp_nama,
    concat(d_utama.diag_utama_kode, '. ', d_utama.diag_utama) AS diagnosa_utama,
    d_utama.diag_utama_id AS diagnosa_utama_id,
    concat(d_penyerta.diag_penyerta_kode, '. ', d_penyerta.diag_penyerta) AS diagnosa_penyerta,
    d_penyerta.diag_penyerta_id AS diagnosa_penyerta_id,
    pendaftaran_t.status_periksa AS id_status_periksa,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN fgetnamalookup((pendaftaran_t.status_periksa)::integer)
            ELSE fgetnamalookup(pasienadmisi_t.status_ranap)
        END AS status_periksa,
        CASE
            WHEN ((pendaftaran_t.status_periksa)::text = (4)::text) THEN 't'::text
            WHEN ((pendaftaran_t.status_periksa)::text = (433)::text) THEN 't'::text
            ELSE 'f'::text
        END AS is_status_periksa
   FROM (((((((((((((((pendaftaran_t
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN carabayar_m cb_admisi ON ((pasienadmisi_t.carabayar_id = cb_admisi.carabayar_id)))
     LEFT JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN penjamin_m pj_admisi ON ((pasienadmisi_t.penjamin_id = pj_admisi.penjamin_id)))
     LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN ruangan_m ruang_admisi ON ((pasienadmisi_t.ruangan_id = ruang_admisi.ruangan_id)))
     LEFT JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN instalasi_m instalasi_admisi ON ((ruang_admisi.instalasi_id = instalasi_admisi.instalasi_id)))
     LEFT JOIN ( SELECT koreksidiagnosa_t.pendaftaran_id,
            diagnosa_m.diagnosa_id AS diag_utama_id,
            diagnosa_m.diagnosa_kode AS diag_utama_kode,
            diagnosa_m.diagnosa_nama AS diag_utama
           FROM ((koreksidiagnosa_t
             JOIN ( SELECT max(pk.koreksidiagnosa_id) AS koreksidiagnosa_id,
                    pk.pendaftaran_id,
                    pk.diagnosa_id
                   FROM koreksidiagnosa_t pk
                  WHERE ((pk.kelompokdiagnosa_id = 2) AND (pk.is_deleted = false))
                  GROUP BY pk.pendaftaran_id, pk.diagnosa_id) max_pk ON (((koreksidiagnosa_t.koreksidiagnosa_id = max_pk.koreksidiagnosa_id) AND (koreksidiagnosa_t.pendaftaran_id = max_pk.pendaftaran_id))))
             JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
          WHERE (koreksidiagnosa_t.kelompokdiagnosa_id = 2)) d_utama ON ((pendaftaran_t.pendaftaran_id = d_utama.pendaftaran_id)))
     LEFT JOIN ( SELECT koreksidiagnosa_t.pendaftaran_id,
            diagnosa_m.diagnosa_id AS diag_penyerta_id,
            diagnosa_m.diagnosa_kode AS diag_penyerta_kode,
            diagnosa_m.diagnosa_nama AS diag_penyerta
           FROM (koreksidiagnosa_t
             JOIN diagnosa_m ON ((koreksidiagnosa_t.diagnosa_id = diagnosa_m.diagnosa_id)))
          WHERE ((koreksidiagnosa_t.kelompokdiagnosa_id = 3) AND (koreksidiagnosa_t.is_deleted = false))
          GROUP BY koreksidiagnosa_t.pendaftaran_id, diagnosa_m.diagnosa_id, diagnosa_m.diagnosa_kode, diagnosa_m.diagnosa_nama) d_penyerta ON ((pendaftaran_t.pendaftaran_id = d_penyerta.pendaftaran_id)))
     LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN pegawai_m peg_admisi ON ((pasienadmisi_t.pegawai_id = peg_admisi.pegawai_id)))
  ORDER BY pendaftaran_t.tgl_pendaftaran DESC
            ;");
            $this->execute('ALTER TABLE public.lapkunjunganpasienrs_v
    OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210114_004120_migrate_20210114_lapkunjunganpasienrs_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210114_004120_migrate_20210114_lapkunjunganpasienrs_v cannot be reverted.\n";

        return false;
    }
    */
}
