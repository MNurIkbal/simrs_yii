<?php

use yii\db\Migration;

/**
 * Class m230303_154634_migrate_GM24_function_fgetketersediaankamar_det
 */
class m230303_154634_migrate_GM24_function_fgetketersediaankamar_det extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP FUNCTION IF EXISTS fgetketersediaankamar_det();
        ');

        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."fgetketersediaankamar_det"()
  RETURNS TABLE("ket" varchar, "kamarruangan_id" int4, "kamartempattidur_id" int4, "ruangan_id" int4, "kelaspelayanan_id" int4, "kamarruangan_nokamar" varchar, "no_tempattidur" varchar, "ruangan_nama" varchar, "kelaspelayanan_nama" varchar, "harga_tariftindakan" float8, "kamarruangan_jenis" varchar, "status_isi" varchar, "kode_warna" varchar, "kettempattidur_warna" varchar, "additional_data" varchar, "kettempattidur_id" int4, "kettempattidur_nama" varchar, "pendaftaran_id" int4, "pasienadmisi_id" int4, "no_rekam_medik" varchar, "nama_pasien" varchar, "jeniskelamin_id" int4, "jeniskelamin_nama" varchar, "tanggal_lahir" date, "umur" varchar, "tgl_admisi" date, "pegawai_id" int4, "nama_pegawai" varchar, "ruang_sebelum_id" int4, "ruang_sebelum_nama" varchar, "is_stopakomodasi" varchar, "status_ranap_id" int4, "status_ranap_nama" varchar, "total_isi" float8, "total_kosong" float8) AS $BODY$ 
      BEGIN
      
      RETURN QUERY
                              SELECT
              \'ISI\'::varchar AS ket,
              kamartempattidur_m.kamarruangan_id::int4,
              kamartempattidur_m.kamartempattidur_id::int4,
              kamarruangan_m.ruangan_id::int4,
              kelaspelayanan_m.kelaspelayanan_id::int4,
              kamarruangan_m.kamarruangan_nokamar::varchar,
              kamartempattidur_m.no_tempattidur::varchar,
              ruangan_m.ruangan_nama::varchar,
              kelaspelayanan_m.kelaspelayanan_nama::varchar,
              tariftindakan_m.harga_tariftindakan::float8,
              kamarruangan_m.kamarruangan_jenis::varchar,
              kamartempattidur_m.status_isi::varchar,
              kettempattidur_m.kode_warna::varchar,
              kettempattidur_m.kettempattidur_warna::varchar,
              kettempattidur_m.additional_data::varchar,
              kettempattidur_m.kettempattidur_id::int4,
              kettempattidur_m.kettempattidur_nama::varchar,
              pendaftaran_t.pendaftaran_id::int4,
              pasienadmisi_t.pasienadmisi_id::int4,
              pasien_m.no_rekam_medik::varchar,
              pasien_m.nama_pasien::varchar,
              pasien_m.jeniskelamin::int4 AS jeniskelamin_id,
              look_jenkel.lookup_name::varchar AS jeniskelamin_nama,
              pasien_m.tanggal_lahir::date,
              pendaftaran_t.umur::varchar,
              pasienadmisi_t.tgl_admisi::date,
              pasienadmisi_t.pegawai_id::int4,
              dokter_ranap.nama_pegawai::varchar,
              ruang_sebelum.ruangan_id::int4 AS ruang_sebelum_id,
              ruang_sebelum.ruangan_nama::varchar AS ruang_sebelum_nama,
              pendaftaran_t.is_stopakomodasi::varchar,
              pasienadmisi_t.status_ranap::int4 AS status_ranap_id,
              look_statusranap.lookup_name::varchar AS status_ranap_nama,
              COALESCE(isi.total_isi::float8, 0::int) as total_isi,
              COALESCE(kosong.total_kosong::float8, 0::int) as total_kosong
      FROM kamartempattidur_m
              JOIN (SELECT
                              a.kamarruangan_id,
                              a.ruangan_id,
                              a.kelaspelayanan_id,
                              a.kamarruangan_nokamar,
                              a.kamarruangan_jenis
                          FROM kamarruangan_m a
                          WHERE a.is_deleted = false AND a.is_active = true)kamarruangan_m ON kamartempattidur_m.kamarruangan_id = kamarruangan_m.kamarruangan_id 
              JOIN (SELECT
                              a.ruangan_id,
                              a.ruangan_nama                        
                          FROM ruangan_m a
                          WHERE a.is_deleted=FALSE)ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id 
      --                         JOIN tariftindakan_m ON kamarruangan_m.kamarruangan_id = tariftindakan_m.kamarruangan_id AND tariftindakan_m.komponentarif_id=6 and tariftindakan_m.is_deleted=false
      --                         JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
      --                         JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id AND kelaspelayanan_m.is_deleted = false
              LEFT JOIN (SELECT
                                      a.kelaspelayanan_id,
                                      a.kelaspelayanan_nama
                                   FROM kelaspelayanan_m a
                                   WHERE a.is_deleted=FALSE)kelaspelayanan_m ON kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id 
              LEFT JOIN (SELECT
                                          a.kamarruangan_id,
                                          a.kelaspelayanan_id,
                                          a.harga_tariftindakan,
                                          a.is_active
                                   FROM tariftindakan_m a
                                   WHERE a.is_deleted=FALSE and a.komponentarif_id=6
                                                                     AND penjamin_id = 1)tariftindakan_m ON kamarruangan_m.kamarruangan_id = tariftindakan_m.kamarruangan_id 
                                                                                                                                                                       AND kelaspelayanan_m.kelaspelayanan_id = tariftindakan_m.kelaspelayanan_id 
              LEFT JOIN (SELECT
                                          kamartempattidur_m.kamarruangan_id,
                                          count(kamartempattidur_m.kamartempattidur_id) as total_isi
                                      FROM kamartempattidur_m
                                          JOIN pasienadmisi_t ON kamartempattidur_m.kamartempattidur_id = pasienadmisi_t.kamartempattidur_id AND pasienadmisi_t.pasienpulang_id IS NULL 
                                      WHERE kamartempattidur_m.is_deleted=FALSE 
                                          AND kamartempattidur_m.status_isi=true 
                                          AND kamartempattidur_m.is_active = TRUE 
                                          AND pasienadmisi_t.status_ranap IN (441,440)
                                      GROUP BY kamartempattidur_m.kamarruangan_id) isi ON kamartempattidur_m.kamarruangan_id = isi.kamarruangan_id
              LEFT JOIN (SELECT
                                          kamartempattidur_m.kamarruangan_id,
                                          count(kamartempattidur_m.kamartempattidur_id) as total_kosong
                                  FROM kamartempattidur_m
                                  WHERE kamartempattidur_m.is_deleted= FALSE 
                                      AND kamartempattidur_m.status_isi= FALSE 
                                      AND kamartempattidur_m.is_active = TRUE
                                  GROUP BY kamartempattidur_m.kamarruangan_id) kosong ON kamartempattidur_m.kamarruangan_id = kosong.kamarruangan_id 
              LEFT JOIN (SELECT
                                      a.kettempattidur_id,
                                      a.kode_warna,
                                      a.kettempattidur_warna,
                                      a.additional_data,
                                      a.kettempattidur_nama
                                   FROM kettempattidur_m a)kettempattidur_m ON kamartempattidur_m.kettempattidur_id = kettempattidur_m.kettempattidur_id 
              JOIN (SELECT
                              a.kamartempattidur_id,
                              a.pasien_id,
                              a.pasienadmisi_id,
                              a.status_ranap,
                              a.pegawai_id,
                              a.tgl_admisi
                          FROM pasienadmisi_t a
                          WHERE a.pasienpulang_id IS NULL)pasienadmisi_t ON kamartempattidur_m.kamartempattidur_id = pasienadmisi_t.kamartempattidur_id 
              JOIN (SELECT
                              a.pasien_id,
                              a.jeniskelamin,
                              a.no_rekam_medik,
                              a.nama_pasien,
                              a.tanggal_lahir
                          FROM pasien_m a)pasien_m ON pasienadmisi_t.pasien_id = pasien_m.pasien_id
              JOIN (SELECT
                              a.pasienadmisi_id,
                              a.pendaftaran_id,
                              a.umur,
                              a.is_stopakomodasi
                          FROM pendaftaran_t a)pendaftaran_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
              LEFT JOIN ( SELECT 
                                          a.masukkamar_id,
                                          a.ruangan_id,
                                          a.carabayar_id,
                                          a.pasienadmisi_id,
                                          a.penjamin_id,
                                          a.pindahkamar_id,
                                          a.kamartempattidur_id
                                      FROM masukkamar_t a
                                      JOIN (SELECT  
                                                      max(mk.masukkamar_id) AS masukkamar_id,
                                                      mk.pasienadmisi_id
                                                  FROM masukkamar_t mk 
                                                  WHERE pindahkamar_id IS NOT NULL
                                                  GROUP BY mk.pasienadmisi_id) max_mk ON a.masukkamar_id = max_mk.masukkamar_id 
                                                                                                                       AND a.pasienadmisi_id = max_mk.pasienadmisi_id
                                      ) masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id
              LEFT JOIN (SELECT
                                          a.ruangan_id,
                                          a.ruangan_nama
                                      FROM ruangan_m a) ruang_sebelum ON masukkamar_t.ruangan_id = ruang_sebelum.ruangan_id
              JOIN (SELECT
                              a.lookup_id,
                              a.lookup_name
                          FROM lookup_m a) look_statusranap ON pasienadmisi_t.status_ranap = look_statusranap.lookup_id
              JOIN (SELECT
                              a.pegawai_id,
                              a.nama_pegawai
                          FROM pegawai_m a) dokter_ranap ON pasienadmisi_t.pegawai_id = dokter_ranap.pegawai_id
              JOIN (SELECT
                              a.lookup_id,
                              a.lookup_name
                          FROM lookup_m a) look_jenkel ON pasien_m.jeniskelamin::integer = look_jenkel.lookup_id
      WHERE kamartempattidur_m.is_deleted= FALSE 
          AND kamartempattidur_m.status_isi= TRUE 
          AND kamartempattidur_m.is_active = TRUE 
          AND pasienadmisi_t.status_ranap IN (441,440) 
          AND tariftindakan_m.is_active = true
      --                      AND kamartempattidur_m.kamarruangan_id = 108;
      UNION ALL
      SELECT
              \'KOSONG\'::varchar AS ket,
              kamartempattidur_m.kamarruangan_id::int4,
              kamartempattidur_m.kamartempattidur_id::int4,
              kamarruangan_m.ruangan_id::int4,
              kelaspelayanan_m.kelaspelayanan_id::int4,
              kamarruangan_m.kamarruangan_nokamar::varchar,
              kamartempattidur_m.no_tempattidur::varchar,
              ruangan_m.ruangan_nama::varchar,
              kelaspelayanan_m.kelaspelayanan_nama::varchar,
              tariftindakan_m.harga_tariftindakan::float8,
              kamarruangan_m.kamarruangan_jenis::varchar,
              kamartempattidur_m.status_isi::varchar,
              kettempattidur_m.kode_warna::varchar,
              kettempattidur_m.kettempattidur_warna::varchar,
              kettempattidur_m.additional_data::varchar,
              kettempattidur_m.kettempattidur_id::int4,
              kettempattidur_m.kettempattidur_nama::varchar,
              NULL AS pendaftaran_id,
              NULL AS pasienadmisi_id,
              NULL AS no_rekam_medik,
              NULL AS nama_pasien,
              NULL AS jeniskelamin,
              NULL AS lookup_name,
              NULL AS tanggal_lahir,
              NULL AS umur,
              NULL AS tgl_admisi,
              NULL AS pegawai_id,
              NULL AS nama_pegawai,
              NULL AS ruang_sebelum_id,
              NULL AS ruang_sebelum_nama,
              NULL AS is_stopakomodasi,
              NULL AS status_ranap_id,
              NULL AS status_ranap_nama,
              COALESCE(isi.total_isi::float8, 0::int) as total_isi,
              COALESCE(kosong.total_kosong::float8, 0::int) as total_kosong
      FROM kamartempattidur_m
              JOIN (SELECT
                              b.kamarruangan_id,
                              b.ruangan_id,
                              b.kelaspelayanan_id,
                              b.kamarruangan_nokamar,
                              b.kamarruangan_jenis                      
                          FROM kamarruangan_m b
                          WHERE b.is_deleted=FALSE AND b.is_active=TRUE)kamarruangan_m ON kamartempattidur_m.kamarruangan_id = kamarruangan_m.kamarruangan_id 
              JOIN (SELECT
                              b.ruangan_id,
                              b.ruangan_nama
                          FROM ruangan_m b
                          WHERE b.is_deleted=FALSE)ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id 
      --                         JOIN tariftindakan_m ON kamarruangan_m.kamarruangan_id = tariftindakan_m.kamarruangan_id AND tariftindakan_m.komponentarif_id=6 and tariftindakan_m.is_deleted=false
      --                         JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
      --                         JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id AND kelaspelayanan_m.is_deleted = false
              LEFT JOIN (SELECT
                                      b.kelaspelayanan_id,
                                      b.kelaspelayanan_nama
                                   FROM kelaspelayanan_m b
                                   WHERE b.is_deleted=FALSE)kelaspelayanan_m ON kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id 
              LEFT JOIN (SELECT
                                      b.kamarruangan_id,
                                      b.kelaspelayanan_id,
                                      b.harga_tariftindakan,
                                      b.is_active
                                   FROM tariftindakan_m b
                                   WHERE b.is_deleted=FALSE
                                       AND b.komponentarif_id=6 AND penjamin_id = 1)tariftindakan_m ON kamarruangan_m.kamarruangan_id = tariftindakan_m.kamarruangan_id AND kelaspelayanan_m.kelaspelayanan_id = tariftindakan_m.kelaspelayanan_id                       
              LEFT JOIN (SELECT
                                          kamartempattidur_m.kamarruangan_id,
                                          count(kamartempattidur_m.kamartempattidur_id) as total_isi
                                   FROM kamartempattidur_m
                                      JOIN pasienadmisi_t ON kamartempattidur_m.kamartempattidur_id = pasienadmisi_t.kamartempattidur_id AND pasienadmisi_t.pasienpulang_id IS NULL 
                                      WHERE kamartempattidur_m.is_deleted=FALSE 
                                          AND kamartempattidur_m.status_isi=TRUE 
                                          AND kamartempattidur_m.is_active =TRUE 
                                          AND pasienadmisi_t.status_ranap IN (441,440)
                                      GROUP BY kamartempattidur_m.kamarruangan_id) isi ON kamartempattidur_m.kamarruangan_id = isi.kamarruangan_id
              LEFT JOIN (SELECT
                                          kamartempattidur_m.kamarruangan_id,
                                          count(kamartempattidur_m.kamartempattidur_id) as total_kosong
                                      FROM kamartempattidur_m
                                      WHERE kamartempattidur_m.is_deleted=FALSE 
                                          AND kamartempattidur_m.status_isi=FALSE 
                                          AND kamartempattidur_m.is_active =TRUE
                                      GROUP BY kamartempattidur_m.kamarruangan_id) kosong ON kamartempattidur_m.kamarruangan_id = kosong.kamarruangan_id 
              JOIN (SELECT
                              b.kettempattidur_id,
                              b.kode_warna,
                              b.kettempattidur_warna,
                              b.additional_data,
                              b.kettempattidur_nama
                          FROM kettempattidur_m b)kettempattidur_m ON kamartempattidur_m.kettempattidur_id = kettempattidur_m.kettempattidur_id 
      --                      JOIN pasienadmisi_t ON kamartempattidur_m.kamartempattidur_id = pasienadmisi_t.kamartempattidur_id
      --                      JOIN pasien_m ON pasienadmisi_t.pasien_id = pasien_m.pasien_id
      WHERE kamartempattidur_m.is_deleted=FALSE 
          AND kamartempattidur_m.status_isi=FALSE 
          AND kamartempattidur_m.is_active =TRUE
          AND tariftindakan_m.is_active = true;
      
      END; 
      $BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230303_154634_migrate_GM24_function_fgetketersediaankamar_det cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230303_154634_migrate_GM24_function_fgetketersediaankamar_det cannot be reverted.\n";

        return false;
    }
    */
}
