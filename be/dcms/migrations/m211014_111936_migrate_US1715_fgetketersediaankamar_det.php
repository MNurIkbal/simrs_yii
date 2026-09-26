<?php

use yii\db\Migration;

/**
 * Class m211014_111936_migrate_US1715_fgetketersediaankamar_det
 */
class m211014_111936_migrate_US1715_fgetketersediaankamar_det extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"fgetketersediaankamar_det\"(\"xruangan_id\" int4=0, \"xkelaspelayanan_id\" int4=0, \"xkamarruangan_id\" int4=0, \"xno_rekam_medik\" varchar='kosong'::character varying, \"xnama_pasien\" varchar='kosong'::character varying, \"xstatus_ranap\" int4=0)
  RETURNS TABLE(\"ket\" varchar, \"kamarruangan_id\" int4, \"kamartempattidur_id\" int4, \"ruangan_id\" int4, \"kelaspelayanan_id\" int4, \"kamarruangan_nokamar\" varchar, \"no_tempattidur\" varchar, \"ruangan_nama\" varchar, \"kelaspelayanan_nama\" varchar, \"harga_tariftindakan\" float8, \"kamarruangan_jenis\" varchar, \"status_isi\" varchar, \"kode_warna\" varchar, \"kettempattidur_warna\" varchar, \"additional_data\" varchar, \"kettempattidur_id\" int4, \"kettempattidur_nama\" varchar, \"pendaftaran_id\" int4, \"pasienadmisi_id\" int4, \"no_rekam_medik\" varchar, \"nama_pasien\" varchar, \"jeniskelamin_id\" int4, \"jeniskelamin_nama\" varchar, \"tanggal_lahir\" date, \"umur\" varchar, \"tgl_admisi\" date, \"pegawai_id\" int4, \"nama_pegawai\" varchar, \"ruang_sebelum_id\" int4, \"ruang_sebelum_nama\" varchar, \"is_stopakomodasi\" varchar, \"status_ranap_id\" int4, \"status_ranap_nama\" varchar, \"total_isi\" float8, \"total_kosong\" float8) AS \$BODY\$ 
BEGIN
    IF(xruangan_id = 0)
    THEN
        xruangan_id := NULL;
    END IF;
    
    IF(xkelaspelayanan_id = 0)
    THEN
        xkelaspelayanan_id := NULL;
    END IF;
    
    IF(xkamarruangan_id = 0)
    THEN
        xkamarruangan_id := NULL;
    END IF;
        
        IF(xno_rekam_medik = 'kosong')
    THEN
        xno_rekam_medik := NULL;
    END IF;
        
        IF(xnama_pasien = 'kosong')
    THEN
        xnama_pasien := NULL;
    END IF;
        
        IF(xstatus_ranap = 0)
    THEN
        xstatus_ranap := NULL;
    END IF;

RETURN QUERY
                        SELECT
                                    'ISI'::varchar AS ket,
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
                        JOIN kamarruangan_m ON kamartempattidur_m.kamarruangan_id = kamarruangan_m.kamarruangan_id AND kamarruangan_m.is_deleted = false AND kamarruangan_m.is_active = true
                        JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.is_deleted = false
                        JOIN tariftindakan_m ON kamarruangan_m.kamarruangan_id = tariftindakan_m.kamarruangan_id AND tariftindakan_m.komponentarif_id=6 and tariftindakan_m.is_deleted=false
                        JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                        JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id AND kelaspelayanan_m.is_deleted = false
--                      JOIN kelaspelayanan_m ON kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id AND kelaspelayanan_m.is_deleted = false 
--                      JOIN tariftindakan_m ON kamarruangan_m.kamarruangan_id = tariftindakan_m.kamarruangan_id AND tariftindakan_m.komponentarif_id=6 and tariftindakan_m.is_deleted=false
                        LEFT JOIN (SELECT
                  kamartempattidur_m.kamarruangan_id,
                  count(kamartempattidur_m.kamartempattidur_id) as total_isi
            FROM kamartempattidur_m
                        JOIN pasienadmisi_t ON kamartempattidur_m.kamartempattidur_id = pasienadmisi_t.kamartempattidur_id AND pasienadmisi_t.pasienpulang_id IS NULL 
            WHERE kamartempattidur_m.is_deleted=FALSE and kamartempattidur_m.status_isi=true and kamartempattidur_m.is_active = TRUE AND pasienadmisi_t.status_ranap = 440
            GROUP BY kamartempattidur_m.kamarruangan_id) isi ON kamartempattidur_m.kamarruangan_id = isi.kamarruangan_id
                        LEFT JOIN (SELECT
                  kamartempattidur_m.kamarruangan_id,
                  count(kamartempattidur_m.kamartempattidur_id) as total_kosong
            FROM kamartempattidur_m
            WHERE kamartempattidur_m.is_deleted=FALSE and  kamartempattidur_m.status_isi=false and kamartempattidur_m.is_active = TRUE
            GROUP BY kamartempattidur_m.kamarruangan_id) kosong ON kamartempattidur_m.kamarruangan_id = kosong.kamarruangan_id 
                        
                        LEFT JOIN kettempattidur_m ON kamartempattidur_m.kettempattidur_id = kettempattidur_m.kettempattidur_id 
                        JOIN pasienadmisi_t ON kamartempattidur_m.kamartempattidur_id = pasienadmisi_t.kamartempattidur_id AND pasienadmisi_t.pasienpulang_id IS NULL 
                        JOIN pasien_m ON pasienadmisi_t.pasien_id = pasien_m.pasien_id
                        JOIN pendaftaran_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
                        LEFT JOIN ( SELECT a.masukkamar_id,
                            a.ruangan_id,
                            a.carabayar_id,
                            a.pasienadmisi_id,
                            a.penjamin_id,
                            a.pindahkamar_id,
                            a.kamartempattidur_id
                    FROM masukkamar_t a
                    JOIN ( SELECT  max(mk.masukkamar_id) AS masukkamar_id,
                                    mk.pasienadmisi_id
                                FROM masukkamar_t mk 
                                WHERE pindahkamar_id IS NOT NULL
                                GROUP BY mk.pasienadmisi_id) max_mk ON a.masukkamar_id = max_mk.masukkamar_id AND a.pasienadmisi_id = max_mk.pasienadmisi_id
                    ) masukkamar_t ON pasienadmisi_t.pasienadmisi_id = masukkamar_t.pasienadmisi_id
                        LEFT JOIN ruangan_m ruang_sebelum ON masukkamar_t.ruangan_id = ruang_sebelum.ruangan_id
                        JOIN lookup_m look_statusranap ON pasienadmisi_t.status_ranap = look_statusranap.lookup_id
                        JOIN pegawai_m dokter_ranap ON pasienadmisi_t.pegawai_id = dokter_ranap.pegawai_id
                        JOIN lookup_m look_jenkel ON pasien_m.jeniskelamin::integer = look_jenkel.lookup_id
            WHERE kamartempattidur_m.is_deleted=FALSE and kamartempattidur_m.status_isi=true and kamartempattidur_m.is_active = TRUE AND 
                        pasienadmisi_t.status_ranap = 440 AND
--                      AND kamartempattidur_m.kamarruangan_id = 108
                        kamarruangan_m.ruangan_id = COALESCE(xruangan_id, kamarruangan_m.ruangan_id) AND 
                        kelaspelayanan_m.kelaspelayanan_id = COALESCE(xkelaspelayanan_id, kelaspelayanan_m.kelaspelayanan_id) AND 
                        kamartempattidur_m.kamarruangan_id = COALESCE(xkamarruangan_id, kamartempattidur_m.kamarruangan_id)
                        AND pasien_m.no_rekam_medik = COALESCE(xno_rekam_medik, pasien_m.no_rekam_medik) AND
                        pasien_m.nama_pasien = COALESCE(xnama_pasien, pasien_m.nama_pasien) AND 
                        pasienadmisi_t.status_ranap = COALESCE(xstatus_ranap, pasienadmisi_t.status_ranap)
                        UNION ALL
                        SELECT
                                    'KOSONG'::varchar AS ket,
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
                        JOIN kamarruangan_m ON kamartempattidur_m.kamarruangan_id = kamarruangan_m.kamarruangan_id AND kamarruangan_m.is_deleted = false AND kamarruangan_m.is_active = true
                        JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id AND ruangan_m.is_deleted = false
                        JOIN tariftindakan_m ON kamarruangan_m.kamarruangan_id = tariftindakan_m.kamarruangan_id AND tariftindakan_m.komponentarif_id=6 and tariftindakan_m.is_deleted=false
                        JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                        JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id AND kelaspelayanan_m.is_deleted = false
--                      JOIN kelaspelayanan_m ON kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id AND kelaspelayanan_m.is_deleted = false 
--                      JOIN tariftindakan_m ON kamarruangan_m.kamarruangan_id = tariftindakan_m.kamarruangan_id AND tariftindakan_m.komponentarif_id=6 and tariftindakan_m.is_deleted=false
                        LEFT JOIN (SELECT
                  kamartempattidur_m.kamarruangan_id,
                  count(kamartempattidur_m.kamartempattidur_id) as total_isi
            FROM kamartempattidur_m
                        JOIN pasienadmisi_t ON kamartempattidur_m.kamartempattidur_id = pasienadmisi_t.kamartempattidur_id AND pasienadmisi_t.pasienpulang_id IS NULL 
            WHERE kamartempattidur_m.is_deleted=FALSE and kamartempattidur_m.status_isi=true and kamartempattidur_m.is_active = TRUE AND pasienadmisi_t.status_ranap = 440
            GROUP BY kamartempattidur_m.kamarruangan_id) isi ON kamartempattidur_m.kamarruangan_id = isi.kamarruangan_id
                        LEFT JOIN (SELECT
                  kamartempattidur_m.kamarruangan_id,
                  count(kamartempattidur_m.kamartempattidur_id) as total_kosong
            FROM kamartempattidur_m
            WHERE kamartempattidur_m.is_deleted=FALSE and  kamartempattidur_m.status_isi=false and kamartempattidur_m.is_active = TRUE
            GROUP BY kamartempattidur_m.kamarruangan_id) kosong ON kamartempattidur_m.kamarruangan_id = kosong.kamarruangan_id 
                        
                        JOIN kettempattidur_m ON kamartempattidur_m.kettempattidur_id = kettempattidur_m.kettempattidur_id 
--                      JOIN pasienadmisi_t ON kamartempattidur_m.kamartempattidur_id = pasienadmisi_t.kamartempattidur_id
--                      JOIN pasien_m ON pasienadmisi_t.pasien_id = pasien_m.pasien_id
            WHERE kamartempattidur_m.is_deleted=FALSE and  kamartempattidur_m.status_isi=false and kamartempattidur_m.is_active = TRUE AND
--                      AND kamartempattidur_m.kamarruangan_id = 108
                        kamarruangan_m.ruangan_id = COALESCE(xruangan_id, kamarruangan_m.ruangan_id) AND 
                        kelaspelayanan_m.kelaspelayanan_id = COALESCE(xkelaspelayanan_id, kelaspelayanan_m.kelaspelayanan_id) AND 
                        kamartempattidur_m.kamarruangan_id = COALESCE(xkamarruangan_id, kamartempattidur_m.kamarruangan_id) ;
--                      AND pasien_m.no_rekam_medik = COALESCE(xno_rekam_medik, pasien_m.no_rekam_medik) AND
--                      pasien_m.nama_pasien = COALESCE(xnama_pasien, pasien_m.nama_pasien) AND 
--                      pasienadmisi_t.status_ranap = COALESCE(xstatus_ranap, pasienadmisi_t.status_ranap);

END; 
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211014_111936_migrate_US1715_fgetketersediaankamar_det cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211014_111936_migrate_US1715_fgetketersediaankamar_det cannot be reverted.\n";

        return false;
    }
    */
}
