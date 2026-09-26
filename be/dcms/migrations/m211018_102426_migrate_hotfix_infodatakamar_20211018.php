<?php

use yii\db\Migration;

/**
 * Class m211018_102426_migrate_hotfix_infodatakamar_20211018
 */
class m211018_102426_migrate_hotfix_infodatakamar_20211018 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DROP FUNCTION if exists public.tariftotalkamarrs_fn;
        ");
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"tariftotalkamarrs_fn\"(\"xruangan_id\" int4=0, \"xpenjamin_id\" int4=0, \"xkelaspelayanan_id\" int4=0, \"xtipe\" varchar='kamar'::character varying)
  RETURNS TABLE(\"jenis\" text, \"tariftindakan_id\" int4, \"ruangan_id\" int4, \"ruangan_nama\" varchar, \"instalasi_id\" int4, \"instalasi_nama\" varchar, \"ruanganpaket_id\" int4, \"ruanganpaket_nama\" varchar, \"perdatarif_id\" int4, \"perdanama_sk\" varchar, \"kelaspelayanan_id\" int4, \"kelaspelayanan_nama\" varchar, \"penjamin_id\" int4, \"penjamin_nama\" varchar, \"kelompoktindakan_id\" int4, \"kelompoktindakan_nama\" varchar, \"kategoritindakan_id\" int4, \"kategoritindakan_nama\" varchar, \"daftartindakan_id\" int4, \"daftartindakan_nama\" varchar, \"tipepaket_id\" int4, \"tipepaket_nama\" varchar, \"komponentarif_id\" int4, \"komponentarif_nama\" varchar, \"harga_tariftindakan\" numeric, \"persencyto_tindakan\" numeric, \"persendiskon_tindakan\" numeric, \"is_default\" bool, \"is_akomodasi\" bool, \"carabayar_id\" int4, \"is_konsultasi\" bool, \"kamarruangan_nokamar\" varchar, \"kamarruangan_id\" int4, \"ambulan_id\" int4, \"no_polisi\" varchar, \"kelompokpemeriksaanlab_id\" int4, \"nama_kelompok\" varchar, \"jenispemeriksaanlab_id\" int4, \"jenispemeriksaanlab_nama\" varchar, \"pemeriksaanlab_id\" int4, \"pemeriksaanlab_nama\" varchar, \"persen_penyulit\" numeric, \"kamarruangan_jenis\" int4, \"kamarruangan_jenis_nama\" varchar, \"status_isi\" bool, \"jeniskasuspenyakit_id\" int4, \"jeniskasuspenyakit_nama\" varchar, \"kettempattidur_id\" int4, \"kettempattidur_nama\" varchar, \"kode_warna\" varchar, \"kettempattidur_warna\" varchar, \"kamartempattidur_id\" int4, \"no_tempattidur\" varchar, \"isi_jk\" int4) AS \$BODY\$ 
DECLARE vpenjamin_id int4;
BEGIN
    IF(xruangan_id = 0)
    THEN
        xruangan_id := NULL;
    END IF;
    
    IF(xkelaspelayanan_id = 0)
    THEN
        xkelaspelayanan_id := NULL;
    END IF;
    
    SELECT default_penjamin INTO vpenjamin_id
    FROM konfigtarif_k
    WHERE konfigtarif_id = 1;
    
    IF(xpenjamin_id = vpenjamin_id)
    THEN
        xpenjamin_id := 0;
    END IF;
    
    RETURN QUERY 
    SELECT *FROM (
        SELECT      
            'kamar'::text AS jenis,
            COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
            kamarruangan_m.ruangan_id as ruangan_id,
            ruangan_m.ruangan_nama as ruangan_nama,
            ruangan_m.instalasi_id as instalasi_id,
            instalasi_m.instalasi_nama as instalasi_nama,
            NULL::integer AS ruanganpaket_id,
            NULL::character varying AS ruanganpaket_nama,
            COALESCE(tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id,
             perdatarif_m.perdanama_sk as perdanama_sk,
            kelaspelayanan_m.kelaspelayanan_id as kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
            COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id) AS penjamin_id,
            COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama) AS penjamin_nama,
            daftartindakan_m.kelompoktindakan_id kelompoktindakan_id,
            kelompoktindakan_m.kelompoktindakan_nama as kelompoktindakan_nama,
            daftartindakan_m.kategoritindakan_id as kategoritindakan_id,
            kategoritindakan_m.kategoritindakan_nama as kategoritindakan_nama,
            COALESCE(tarif_penjamin.daftartindakan_id, tariftindakan_m.daftartindakan_id) AS daftartindakan_id,
            daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
            NULL::integer AS tipepaket_id,
            NULL::character varying AS tipepaket_nama,
            tariftindakan_m.komponentarif_id as komponentarif_id,
            komponentarif_m.komponentarif_nama as komponentarif_nama,
            COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan,
            COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan,
            COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan,
            NULL::BOOLEAN AS is_default,
            daftartindakan_m.is_akomodasi as is_akomodasi,
            penjamin_m.carabayar_id as carabayar_id,
            daftartindakan_m.is_konsultasi as is_konsultasi,
            kamarruangan_m.kamarruangan_nokamar as  kamarruangan_nokamar,
            tariftindakan_m.kamarruangan_id as kamarruangan_id,
            NULL::int4 as ambulan_id,
            NULL::VARCHAR as no_polisi,
            NULL::integer as kelompokpemeriksaanlab_id,
            NULL::character varying AS nama_kelompok,
            NULL::integer AS jenispemeriksaanlab_id,
            NULL::character varying AS jenispemeriksaanlab_nama,
            NULL::integer AS pemeriksaanlab_id,
            NULL::character varying AS pemeriksaanlab_nama,
            COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit ,
            kamarruangan_m.kamarruangan_jenis,
            fgetnamalookup(kamarruangan_m.kamarruangan_jenis) AS kamarruangan_jenis_nama,
            kamartempattidur_m.status_isi,
            jeniskasuspenyakit_m.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            kettempattidur_m.kettempattidur_id,
            kettempattidur_m.kettempattidur_nama,
            kettempattidur_m.kode_warna,
            kettempattidur_m.kettempattidur_warna,
            kamartempattidur_m.kamartempattidur_id,
            kamartempattidur_m.no_tempattidur,
            ( SELECT pasien_m.jeniskelamin
           FROM ((pendaftaran_t
             JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
             JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
          WHERE ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id) AND (pasienadmisi_t.pasienpulang_id IS NULL) AND (pasienadmisi_t.status_ranap = ANY (ARRAY[440, 441])))
         LIMIT 1)::int4 AS isi_jk
         FROM tariftindakan_m
         JOIN kamarruangan_m ON tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
         JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
         JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
         LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
         JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
         JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
--          JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
         JOIN kelaspelayanan_m ON kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id AND tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
         JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
         JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
         JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
         JOIN kamartempattidur_m ON kamarruangan_m.kamarruangan_id = kamartempattidur_m.kamarruangan_id
         LEFT JOIN jeniskasuspenyakit_m ON kamarruangan_m.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
         LEFT JOIN kettempattidur_m ON kamartempattidur_m.kettempattidur_id = kettempattidur_m.kettempattidur_id
         LEFT JOIN (
                SELECT tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    tariftindakan_m.penjamin_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.perdatarif_id,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id,
                    tariftindakan_m.kamarruangan_id
                FROM tariftindakan_m
             JOIN kamarruangan_m ON tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
             JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
             LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
             JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
             JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
--              JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                        JOIN kelaspelayanan_m ON kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id AND tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
             JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                WHERE 
                tariftindakan_m.is_deleted = false AND
                tariftindakan_m.is_active = true AND
                tariftindakan_m.tarifparent_id IS NULL AND
                kamarruangan_m.is_deleted = FALSE AND
                kamarruangan_m.is_active = TRUE AND
                perdatarif_m.is_active = true AND
                tariftindakan_m.komponentarif_id = 6
                AND ruangan_m.is_deleted IS FALSE
                AND tariftindakan_m.penjamin_id = xpenjamin_id
         ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND kelaspelayanan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id AND tariftindakan_m.kamarruangan_id = tarif_penjamin.kamarruangan_id
        WHERE 
            tariftindakan_m.is_deleted = false AND
            tariftindakan_m.is_active = true AND
            tariftindakan_m.tarifparent_id IS NULL AND
            kamarruangan_m.is_deleted = FALSE AND
            kamarruangan_m.is_active = TRUE AND
            perdatarif_m.is_active = true AND
            tariftindakan_m.komponentarif_id = 6
            AND ruangan_m.is_deleted IS FALSE
            AND kamartempattidur_m.is_active = TRUE
            AND kamartempattidur_m.is_deleted IS FALSE
            AND tariftindakan_m.penjamin_id = vpenjamin_id
    ) AS x
    WHERE x.ruangan_id = COALESCE(xruangan_id, x.ruangan_id)
    AND x.kelaspelayanan_id = COALESCE(xkelaspelayanan_id, x.kelaspelayanan_id)
    GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit, x.kamarruangan_jenis, x.kamarruangan_jenis_nama , x.status_isi, x.jeniskasuspenyakit_id, x.jeniskasuspenyakit_nama, x.kettempattidur_id, x.kettempattidur_nama, x.kode_warna, x.kettempattidur_warna, x.kamartempattidur_id  , x.no_tempattidur , x.isi_jk;
END; 
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000
        ");

$this->execute("
            DROP FUNCTION if exists public.fgetketersediaankamar_det;
        ");
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"fgetketersediaankamar_det\"()
  RETURNS TABLE(\"ket\" varchar, \"kamarruangan_id\" int4, \"kamartempattidur_id\" int4, \"ruangan_id\" int4, \"kelaspelayanan_id\" int4, \"kamarruangan_nokamar\" varchar, \"no_tempattidur\" varchar, \"ruangan_nama\" varchar, \"kelaspelayanan_nama\" varchar, \"harga_tariftindakan\" float8, \"kamarruangan_jenis\" varchar, \"status_isi\" varchar, \"kode_warna\" varchar, \"kettempattidur_warna\" varchar, \"additional_data\" varchar, \"kettempattidur_id\" int4, \"kettempattidur_nama\" varchar, \"pendaftaran_id\" int4, \"pasienadmisi_id\" int4, \"no_rekam_medik\" varchar, \"nama_pasien\" varchar, \"jeniskelamin_id\" int4, \"jeniskelamin_nama\" varchar, \"tanggal_lahir\" date, \"umur\" varchar, \"tgl_admisi\" date, \"pegawai_id\" int4, \"nama_pegawai\" varchar, \"ruang_sebelum_id\" int4, \"ruang_sebelum_nama\" varchar, \"is_stopakomodasi\" varchar, \"status_ranap_id\" int4, \"status_ranap_nama\" varchar, \"total_isi\" float8, \"total_kosong\" float8) AS \$BODY\$ 
BEGIN

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
--                         JOIN tariftindakan_m ON kamarruangan_m.kamarruangan_id = tariftindakan_m.kamarruangan_id AND tariftindakan_m.komponentarif_id=6 and tariftindakan_m.is_deleted=false
--                         JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
--                         JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id AND kelaspelayanan_m.is_deleted = false
                     LEFT JOIN kelaspelayanan_m ON kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id AND kelaspelayanan_m.is_deleted = false 
                     LEFT JOIN tariftindakan_m ON kamarruangan_m.kamarruangan_id = tariftindakan_m.kamarruangan_id AND kelaspelayanan_m.kelaspelayanan_id = tariftindakan_m.kelaspelayanan_id AND tariftindakan_m.komponentarif_id=6 and tariftindakan_m.is_deleted=false
                        LEFT JOIN (SELECT
                  kamartempattidur_m.kamarruangan_id,
                  count(kamartempattidur_m.kamartempattidur_id) as total_isi
            FROM kamartempattidur_m
                        JOIN pasienadmisi_t ON kamartempattidur_m.kamartempattidur_id = pasienadmisi_t.kamartempattidur_id AND pasienadmisi_t.pasienpulang_id IS NULL 
            WHERE kamartempattidur_m.is_deleted=FALSE and kamartempattidur_m.status_isi=true and kamartempattidur_m.is_active = TRUE AND pasienadmisi_t.status_ranap IN (441,440)
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
                        pasienadmisi_t.status_ranap IN (441,440) 
--                      AND kamartempattidur_m.kamarruangan_id = 108;
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
--                         JOIN tariftindakan_m ON kamarruangan_m.kamarruangan_id = tariftindakan_m.kamarruangan_id AND tariftindakan_m.komponentarif_id=6 and tariftindakan_m.is_deleted=false
--                         JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
--                         JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id AND kelaspelayanan_m.is_deleted = false
                     LEFT JOIN kelaspelayanan_m ON kamarruangan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id AND kelaspelayanan_m.is_deleted = false 
                     LEFT JOIN tariftindakan_m ON kamarruangan_m.kamarruangan_id = tariftindakan_m.kamarruangan_id AND kelaspelayanan_m.kelaspelayanan_id = tariftindakan_m.kelaspelayanan_id AND tariftindakan_m.komponentarif_id=6 and tariftindakan_m.is_deleted=false
                        LEFT JOIN (SELECT
                  kamartempattidur_m.kamarruangan_id,
                  count(kamartempattidur_m.kamartempattidur_id) as total_isi
            FROM kamartempattidur_m
                        JOIN pasienadmisi_t ON kamartempattidur_m.kamartempattidur_id = pasienadmisi_t.kamartempattidur_id AND pasienadmisi_t.pasienpulang_id IS NULL 
            WHERE kamartempattidur_m.is_deleted=FALSE and kamartempattidur_m.status_isi=true and kamartempattidur_m.is_active = TRUE AND pasienadmisi_t.status_ranap IN (441,440)
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
            WHERE kamartempattidur_m.is_deleted=FALSE and  kamartempattidur_m.status_isi=false and kamartempattidur_m.is_active = TRUE;

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
        echo "m211018_102426_migrate_hotfix_infodatakamar_20211018 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211018_102426_migrate_hotfix_infodatakamar_20211018 cannot be reverted.\n";

        return false;
    }
    */
}
