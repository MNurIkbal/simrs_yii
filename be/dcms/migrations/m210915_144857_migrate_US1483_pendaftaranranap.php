<?php

use yii\db\Migration;

/**
 * Class m210915_144857_migrate_US1483_pendaftaranranap
 */
class m210915_144857_migrate_US1483_pendaftaranranap extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infopasienrujukranap_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infopasienrujukranap_v\" AS
             SELECT 'RJ'::text AS ket,
    pendaftaran_t.pendaftaran_id,
        CASE
            WHEN (prev_admisi.prev_pendaftaran_id IS NULL) THEN pendaftaran_t.pasienadmisi_id
            ELSE prev_admisi.pasienadmisi_id
        END AS pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    look_namadepan.lookup_name AS namadepan_pasien,
    pendaftaran_t.pegawai_id AS dpjp_id,
    pegawai_m.nama_pegawai AS dpjp_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienpulang_t.ruanganakhir_id,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_id,
    concat(kamarruangan_m.kamarruangan_nokamar, ' - ', kamartempattidur_m.no_tempattidur) AS kamarruangan_nokamar,
    pasienpulang_t.tempattidurtujuan_id,
    kamartempattidur_m.no_tempattidur,
    pasienpulang_t.tglpasienpulang AS tglrujukranap,
    pasienpulang_t.carakeluar_id,
    carakeluar_m.carakeluar_nama,
        CASE
            WHEN (prev_admisi.prev_pendaftaran_id IS NULL) THEN (pendaftaran_t.status_periksa)::integer
            ELSE pasienadmisi_t.status_ranap
        END AS status_periksa_id,
        CASE
            WHEN (prev_admisi.prev_pendaftaran_id IS NULL) THEN look_status.lookup_name
            ELSE stat_admisi.lookup_name
        END AS status_periksa_nama,
    prev_admisi.status_periksa AS prev_status_periksa_id,
    look_prev_status.lookup_name AS prev_status_periksa_nama,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pasien_m.jeniskelamin AS jeniskelamin_id,
    lookup_jenkel.lookup_name AS jeniskelamin_nama,
    sedia_ruangan.ruangan_id AS keter_ruangan_id,
    sedia_ruangan.ruangan_nama AS keter_ruangan_nama,
    sedia_kamarruangan.kamarruangan_id AS keter_kamarruangan_id,
    sedia_kamarruangan.kamarruangan_nokamar AS keter_kamarruangan_nokamar,
    sedia_kamartempattidur.kamartempattidur_id AS keter_kamartempattidur_id,
    sedia_kamartempattidur.no_tempattidur AS keter_kamartempattidur_no
   FROM ((((((((((((((((((((((pasienpulang_t
     JOIN ( SELECT a.pendaftaran_id,
            a.pasienpulang_id,
            a.no_pendaftaran,
            a.kelaspelayanan_id,
            a.penjamin_id,
            a.status_periksa,
            a.pasien_id,
            a.instalasi_id,
            a.is_active,
            a.is_deleted,
            a.pasienadmisi_id,
            a.carabayar_id,
            a.jeniskasuspenyakit_id,
            a.pegawai_id
           FROM pendaftaran_t a) pendaftaran_t ON ((pasienpulang_t.pasienpulang_id = pendaftaran_t.pasienpulang_id)))
     JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik,
            a.nama_pasien,
            a.namadepan,
            a.jeniskelamin,
            a.tanggal_lahir
           FROM pasien_m a) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN ( SELECT a.kamarruangan_id,
            a.kamartempattidur_id,
            a.no_tempattidur
           FROM kamartempattidur_m a) kamartempattidur_m ON ((pasienpulang_t.tempattidurtujuan_id = kamartempattidur_m.kamartempattidur_id)))
     LEFT JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar,
            a.ruangan_id
           FROM kamarruangan_m a) kamarruangan_m ON ((kamartempattidur_m.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
     JOIN ( SELECT a.carakeluar_id,
            a.carakeluar_nama
           FROM carakeluar_m a) carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
     JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_status ON (((pendaftaran_t.status_periksa)::integer = look_status.lookup_id)))
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON ((kamarruangan_m.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_namadepan ON (((pasien_m.namadepan)::integer = look_namadepan.lookup_id)))
     LEFT JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
           FROM carabayar_m a) carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN ( SELECT a.jeniskasuspenyakit_id,
            a.jeniskasuspenyakit_nama
           FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) lookup_jenkel ON (((pasien_m.jeniskelamin)::integer = lookup_jenkel.lookup_id)))
     LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.status_periksa,
            a.prev_pendaftaran_id
           FROM pendaftaran_t a) prev_admisi ON ((pendaftaran_t.pendaftaran_id = prev_admisi.prev_pendaftaran_id)))
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_prev_status ON (((prev_admisi.status_periksa)::integer = look_prev_status.lookup_id)))
     LEFT JOIN ( SELECT a.ketersediaankamar_id,
            a.pendaftaran_id,
            a.ruangan_id,
            a.kamarruangan_id,
            a.kamartempattidur_id
           FROM (ketersediaankamar_r a
             JOIN ( SELECT max(b.ketersediaankamar_id) AS ketersediaankamar_id,
                    b.pendaftaran_id
                   FROM ketersediaankamar_r b
                  GROUP BY b.pendaftaran_id) max_ketesediaan ON (((a.ketersediaankamar_id = max_ketesediaan.ketersediaankamar_id) AND (a.pendaftaran_id = max_ketesediaan.pendaftaran_id))))) ketersediaankamar_r ON ((pendaftaran_t.pendaftaran_id = ketersediaankamar_r.pendaftaran_id)))
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) sedia_ruangan ON ((ketersediaankamar_r.ruangan_id = sedia_ruangan.ruangan_id)))
     LEFT JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar
           FROM kamarruangan_m a) sedia_kamarruangan ON ((ketersediaankamar_r.kamarruangan_id = sedia_kamarruangan.kamarruangan_id)))
     LEFT JOIN ( SELECT a.kamartempattidur_id,
            a.no_tempattidur
           FROM kamartempattidur_m a) sedia_kamartempattidur ON ((ketersediaankamar_r.kamartempattidur_id = sedia_kamartempattidur.kamartempattidur_id)))
     LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.status_ranap
           FROM pasienadmisi_t a) pasienadmisi_t ON ((prev_admisi.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) stat_admisi ON ((pasienadmisi_t.status_ranap = stat_admisi.lookup_id)))
  WHERE ((pendaftaran_t.instalasi_id = 1) AND (pendaftaran_t.is_active = true) AND (pendaftaran_t.is_deleted = false) AND (carakeluar_m.carakeluar_id = 5))
UNION ALL
 SELECT 'RD'::text AS ket,
    pendaftaran_t.pendaftaran_id,
        CASE
            WHEN (prev_admisi.prev_pendaftaran_id IS NULL) THEN pendaftaran_t.pasienadmisi_id
            ELSE prev_admisi.pasienadmisi_id
        END AS pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    look_namadepan.lookup_name AS namadepan_pasien,
    pendaftaran_t.pegawai_id AS dpjp_id,
    pegawai_m.nama_pegawai AS dpjp_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienpulang_t.ruanganakhir_id,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_id,
    concat(kamarruangan_m.kamarruangan_nokamar, ' - ', kamartempattidur_m.no_tempattidur) AS kamarruangan_nokamar,
    pasienpulang_t.tempattidurtujuan_id,
    kamartempattidur_m.no_tempattidur,
    pasienpulang_t.tglpasienpulang AS tglrujukranap,
    pasienpulang_t.carakeluar_id,
    carakeluar_m.carakeluar_nama,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN (pendaftaran_t.status_periksa)::integer
            ELSE pasienadmisi_t.status_ranap
        END AS status_periksa_id,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN look_status.lookup_name
            ELSE stat_admisi.lookup_name
        END AS status_periksa_nama,
    prev_admisi.status_periksa AS prev_status_periksa_id,
    look_prev_status.lookup_name AS prev_status_periksa_nama,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pasien_m.jeniskelamin AS jeniskelamin_id,
    lookup_jenkel.lookup_name AS jeniskelamin_nama,
    sedia_ruangan.ruangan_id AS keter_ruangan_id,
    sedia_ruangan.ruangan_nama AS keter_ruangan_nama,
    sedia_kamarruangan.kamarruangan_id AS keter_kamarruangan_id,
    sedia_kamarruangan.kamarruangan_nokamar AS keter_kamarruangan_nokamar,
    sedia_kamartempattidur.kamartempattidur_id AS keter_kamartempattidur_id,
    sedia_kamartempattidur.no_tempattidur AS keter_kamartempattidur_no
   FROM ((((((((((((((((((((((pasienpulang_t
     JOIN ( SELECT a.pendaftaran_id,
            a.pasienpulang_id,
            a.no_pendaftaran,
            a.kelaspelayanan_id,
            a.penjamin_id,
            a.status_periksa,
            a.pasien_id,
            a.instalasi_id,
            a.is_active,
            a.is_deleted,
            a.pasienadmisi_id,
            a.carabayar_id,
            a.jeniskasuspenyakit_id,
            a.pegawai_id
           FROM pendaftaran_t a) pendaftaran_t ON ((pasienpulang_t.pasienpulang_id = pendaftaran_t.pasienpulang_id)))
     JOIN ( SELECT a.pasien_id,
            a.no_rekam_medik,
            a.nama_pasien,
            a.namadepan,
            a.jeniskelamin,
            a.tanggal_lahir
           FROM pasien_m a) pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
     JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama
           FROM penjamin_m a) penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN ( SELECT a.kamarruangan_id,
            a.kamartempattidur_id,
            a.no_tempattidur
           FROM kamartempattidur_m a) kamartempattidur_m ON ((pasienpulang_t.tempattidurtujuan_id = kamartempattidur_m.kamartempattidur_id)))
     LEFT JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar,
            a.ruangan_id
           FROM kamarruangan_m a) kamarruangan_m ON ((kamartempattidur_m.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
     JOIN ( SELECT a.carakeluar_id,
            a.carakeluar_nama
           FROM carakeluar_m a) carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
     JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_status ON (((pendaftaran_t.status_periksa)::integer = look_status.lookup_id)))
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON ((kamarruangan_m.ruangan_id = ruangan_m.ruangan_id)))
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_namadepan ON (((pasien_m.namadepan)::integer = look_namadepan.lookup_id)))
     LEFT JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama
           FROM carabayar_m a) carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN ( SELECT a.jeniskasuspenyakit_id,
            a.jeniskasuspenyakit_nama
           FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) lookup_jenkel ON (((pasien_m.jeniskelamin)::integer = lookup_jenkel.lookup_id)))
     LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.status_periksa,
            a.prev_pendaftaran_id
           FROM pendaftaran_t a) prev_admisi ON ((pendaftaran_t.pendaftaran_id = prev_admisi.prev_pendaftaran_id)))
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) look_prev_status ON (((prev_admisi.status_periksa)::integer = look_prev_status.lookup_id)))
     LEFT JOIN ( SELECT a.ketersediaankamar_id,
            a.pendaftaran_id,
            a.ruangan_id,
            a.kamarruangan_id,
            a.kamartempattidur_id
           FROM (ketersediaankamar_r a
             JOIN ( SELECT max(b.ketersediaankamar_id) AS ketersediaankamar_id,
                    b.pendaftaran_id
                   FROM ketersediaankamar_r b
                  GROUP BY b.pendaftaran_id) max_ketesediaan ON (((a.ketersediaankamar_id = max_ketesediaan.ketersediaankamar_id) AND (a.pendaftaran_id = max_ketesediaan.pendaftaran_id))))) ketersediaankamar_r ON ((pendaftaran_t.pendaftaran_id = ketersediaankamar_r.pendaftaran_id)))
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) sedia_ruangan ON ((ketersediaankamar_r.ruangan_id = sedia_ruangan.ruangan_id)))
     LEFT JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar
           FROM kamarruangan_m a) sedia_kamarruangan ON ((ketersediaankamar_r.kamarruangan_id = sedia_kamarruangan.kamarruangan_id)))
     LEFT JOIN ( SELECT a.kamartempattidur_id,
            a.no_tempattidur
           FROM kamartempattidur_m a) sedia_kamartempattidur ON ((ketersediaankamar_r.kamartempattidur_id = sedia_kamartempattidur.kamartempattidur_id)))
     LEFT JOIN ( SELECT a.pasienadmisi_id,
            a.status_ranap
           FROM pasienadmisi_t a) pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) stat_admisi ON ((pasienadmisi_t.status_ranap = stat_admisi.lookup_id)))
  WHERE ((pendaftaran_t.instalasi_id = 2) AND (pendaftaran_t.is_active = true) AND (pendaftaran_t.is_deleted = false) AND (carakeluar_m.carakeluar_id = 5))
            ;");
        $this->execute('
            ALTER TABLE public.infopasienrujukranap_v OWNER TO postgres;
            ');

        $this->execute('CREATE TABLE IF NOT EXISTS "public"."ketersediaankamar_r" (
          "ketersediaankamar_id" serial8,
          "pendaftaran_id" int4,
          "pasien_id" int4,
          "ruangan_id" int4,
          "kelaspelayanan_id" int4,
          "kamarruangan_id" int4,
          "kamartempattidur_id" int4,
          "additional_data" text COLLATE "pg_catalog"."default",
          "created_date" timestamp(6) NOT NULL DEFAULT (\'now\'::text)::date,
          "created_by" int4,
          "modified_count" int4,
          "last_modified_date" timestamp(6),
          "last_modified_by" int4,
          "is_deleted" bool NOT NULL DEFAULT false,
          "is_active" bool NOT NULL DEFAULT true,
          "deleted_date" timestamp(6),
          "deleted_by" int4,
          CONSTRAINT "ketersediaankamar_r_pkey" PRIMARY KEY ("ketersediaankamar_id")
          );
        ');

        $this->execute('ALTER TABLE "public"."ketersediaankamar_r" 
          OWNER TO "postgres";
        ');

        $this->execute("
            DELETE from lookup_m where lookup_id=1058;
        ");

        $this->execute("INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES (1058, 'status_periksa', 'Batal Rawat Inap', 'Batal Rawat Inap', NULL, NULL, NULL, '2021-09-07 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210915_144857_migrate_US1483_pendaftaranranap cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210915_144857_migrate_US1483_pendaftaranranap cannot be reverted.\n";

        return false;
    }
    */
}
