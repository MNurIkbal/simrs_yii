<?php

use yii\db\Migration;

/**
 * Class m201119_030522_migrate_mhkn_20201119_lapkunjunganpasienrs_v_3014
 */
class m201119_030522_migrate_mhkn_20201119_lapkunjunganpasienrs_v_3014 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.lapkunjunganpasienrs_v;');
        $this->execute("CREATE VIEW \"public\".\"lapkunjunganpasienrs_v\" AS
             SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pasien_m.jeniskelamin,
    pendaftaran_t.carabayar_id,
    pendaftaran_t.penjamin_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pendaftaran_t.instalasi_id,
    pendaftaran_t.ruangan_id,
    pendaftaran_t.pegawai_id AS dokterdpjp_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    pasien_m.alamat_pasien,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
        CASE
            WHEN (pegawai_m.nama_pegawai IS NULL) THEN pasienadmisi.nama_pegawai
            ELSE pegawai_m.nama_pegawai
        END AS dokterdpjp_nama,
    concat(d_utama.diag_utama_kode, ' - ', d_utama.diag_utama) AS diagnosa_utama,
    d_penyerta.diag_penyerta_gabung AS diagnosa_penyerta,
    pendaftaran_t.status_periksa AS id_status_periksa,
        CASE
            WHEN (pulang_ranap.lookup_name IS NOT NULL) THEN pulang_ranap.lookup_name
            WHEN (carakeluar_m.carakeluar_nama IS NOT NULL) THEN carakeluar_m.carakeluar_nama
            ELSE fgetnamalookup((pendaftaran_t.status_periksa)::integer)
        END AS status_periksa,
        CASE
            WHEN ((pendaftaran_t.status_periksa)::text = (4)::text) THEN 't'::text
            WHEN ((pendaftaran_t.status_periksa)::text = (433)::text) THEN 't'::text
            WHEN (pasienpulang_t.carakeluar_id IS NOT NULL) THEN 't'::text
            ELSE 'f'::text
        END AS is_status_periksa
   FROM (((((((((((((pendaftaran_t
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
     JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN ( SELECT t1.pendaftaran_id,
            diagnosa_m.diagnosa_kode AS diag_utama_kode,
            diagnosa_m.diagnosa_nama AS diag_utama
           FROM ((koreksidiagnosa_t t1
             JOIN ( SELECT koreksidiagnosa_t.pendaftaran_id,
                    max(koreksidiagnosa_t.koreksidiagnosa_id) AS koreksidiagnosa_id
                   FROM koreksidiagnosa_t
                  WHERE (koreksidiagnosa_t.kelompokdiagnosa_id = 2)
                  GROUP BY koreksidiagnosa_t.pendaftaran_id) t2 ON (((t1.koreksidiagnosa_id = t2.koreksidiagnosa_id) AND (t1.pendaftaran_id = t2.pendaftaran_id))))
             JOIN diagnosa_m ON ((t1.diagnosa_id = diagnosa_m.diagnosa_id)))) d_utama ON ((pendaftaran_t.pendaftaran_id = d_utama.pendaftaran_id)))
     LEFT JOIN ( SELECT diagnosa_penyerta.pendaftaran_id,
            string_agg(diagnosa_penyerta.diag_penyerta_gabung, ', '::text) AS diag_penyerta_gabung
           FROM ( SELECT t1.pendaftaran_id,
                    diagnosa_m.diagnosa_kode AS diag_penyerta_kode,
                    diagnosa_m.diagnosa_nama AS diag_penyerta,
                    concat(diagnosa_m.diagnosa_kode, ' - ', diagnosa_m.diagnosa_nama) AS diag_penyerta_gabung
                   FROM (koreksidiagnosa_t t1
                     JOIN diagnosa_m ON ((t1.diagnosa_id = diagnosa_m.diagnosa_id)))
                  WHERE ((t1.kelompokdiagnosa_id = 3) AND (t1.is_deleted = false))) diagnosa_penyerta
          GROUP BY diagnosa_penyerta.pendaftaran_id) d_penyerta ON ((pendaftaran_t.pendaftaran_id = d_penyerta.pendaftaran_id)))
     LEFT JOIN ( SELECT pasienadmisi_t.pendaftaran_id,
            pasienadmisi_t.pegawai_id,
            pasienadmisi_t.status_ranap,
            pegawai_m_1.nama_pegawai
           FROM (pasienadmisi_t
             JOIN pegawai_m pegawai_m_1 ON ((pasienadmisi_t.pegawai_id = pegawai_m_1.pegawai_id)))) pasienadmisi ON ((pendaftaran_t.pendaftaran_id = pasienadmisi.pendaftaran_id)))
     LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pendaftaran_id = pasienpulang_t.pendaftaran_id)))
     LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
     LEFT JOIN lookup_m pulang_ranap ON ((pasienadmisi.status_ranap = pulang_ranap.lookup_id)))
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
        echo "m201119_030522_migrate_mhkn_20201119_lapkunjunganpasienrs_v_3014 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201119_030522_migrate_mhkn_20201119_lapkunjunganpasienrs_v_3014 cannot be reverted.\n";

        return false;
    }
    */
}
