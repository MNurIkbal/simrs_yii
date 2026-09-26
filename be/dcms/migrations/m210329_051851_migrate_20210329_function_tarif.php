<?php

use yii\db\Migration;

/**
 * Class m210329_051851_migrate_20210329_function_tarif
 */
class m210329_051851_migrate_20210329_function_tarif extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP FUNCTION if exists "public"."tariftotalrs_fn"("xruangan_id" int4, "xpenjamin_id" int4, "xkelaspelayanan_id" int4, "xtipe" varchar);');

        $this->execute('CREATE OR REPLACE FUNCTION "public"."tariftotalrs_fn"("xruangan_id" int4=0, "xpenjamin_id" int4=0, "xkelaspelayanan_id" int4=0, "xtipe" varchar=\'pelayanan\'::character varying)
  RETURNS TABLE("jenis" text, "tariftindakan_id" int4, "ruangan_id" int4, "ruangan_nama" varchar, "instalasi_id" int4, "instalasi_nama" varchar, "ruanganpaket_id" int4, "ruanganpaket_nama" varchar, "perdatarif_id" int4, "perdanama_sk" varchar, "kelaspelayanan_id" int4, "kelaspelayanan_nama" varchar, "penjamin_id" int4, "penjamin_nama" varchar, "kelompoktindakan_id" int4, "kelompoktindakan_nama" varchar, "kategoritindakan_id" int4, "kategoritindakan_nama" varchar, "daftartindakan_id" int4, "daftartindakan_nama" varchar, "tipepaket_id" int4, "tipepaket_nama" varchar, "komponentarif_id" int4, "komponentarif_nama" varchar, "harga_tariftindakan" numeric, "persencyto_tindakan" numeric, "persendiskon_tindakan" numeric, "is_default" bool, "is_akomodasi" bool, "carabayar_id" int4, "is_konsultasi" bool, "kamarruangan_nokamar" varchar, "kamarruangan_id" int4, "ambulan_id" int4, "no_polisi" varchar, "kelompokpemeriksaanlab_id" int4, "nama_kelompok" varchar, "jenispemeriksaanlab_id" int4, "jenispemeriksaanlab_nama" varchar, "pemeriksaanlab_id" int4, "pemeriksaanlab_nama" varchar, "persen_penyulit" numeric, "kode" varchar) AS $BODY$ 
DECLARE 
    vpenjamin_id int4;
    vkelaspelayanan_id int4;
    vkelas_id int4;
    vkelas_nama VARCHAR;
BEGIN
    SELECT default_kelas INTO vkelaspelayanan_id
    FROM konfigtarif_k
    WHERE konfigtarif_id = 1;
    
    IF(xkelaspelayanan_id <> 0)
    THEN
        SELECT kelaspelayanan_m.kelaspelayanan_id::int4, kelaspelayanan_m.kelaspelayanan_nama::VARCHAR
        INTO vkelas_id, vkelas_nama
        FROM kelaspelayanan_m
        WHERE kelaspelayanan_m.kelaspelayanan_id = xkelaspelayanan_id;
    ELSE
        SELECT kelaspelayanan_m.kelaspelayanan_id::int4, kelaspelayanan_m.kelaspelayanan_nama::VARCHAR
        INTO vkelas_id, vkelas_nama
        FROM kelaspelayanan_m
        WHERE kelaspelayanan_m.kelaspelayanan_id = vkelaspelayanan_id;
    END IF;
    
    IF(xruangan_id = 0)
    THEN
        SELECT default_ruangan INTO xruangan_id
        FROM konfigtarif_k
        WHERE konfigtarif_id = 1;
    END IF;

    IF(xkelaspelayanan_id = 0)
    THEN
        SELECT default_kelas INTO xkelaspelayanan_id
        FROM konfigtarif_k
        WHERE konfigtarif_id = 1;
    END IF;

    SELECT default_penjamin INTO vpenjamin_id
    FROM konfigtarif_k
    WHERE konfigtarif_id = 1;
    
    IF(xpenjamin_id = vpenjamin_id)
    THEN
        xpenjamin_id := 0;
    END IF;

    IF(xkelaspelayanan_id = vkelaspelayanan_id)
    THEN
        xkelaspelayanan_id := 0;
    END IF;
    
    IF(xtipe = \'pelayanan\')
    THEN
        RETURN QUERY 
        SELECT *FROM (
            SELECT \'tindakan\'::text AS jenis,
                COALESCE( tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
                tindakanruangan_mp.ruangan_id,
                r_tindakan.ruangan_nama,
                r_tindakan.instalasi_id,
                ins_tindakan.instalasi_nama,
                NULL::integer AS ruanganpaket_id,
                NULL::character varying AS ruanganpaket_nama,
                COALESCE( tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id ,
                perdatarif_m.perdanama_sk,
                COALESCE(vkelas_id, tariftindakan_m.kelaspelayanan_id) AS kelaspelayanan_id,
                COALESCE(vkelas_nama, kelaspelayanan_m.kelaspelayanan_nama) AS kelaspelayanan_nama,
                COALESCE( tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id) AS penjamin_id ,
                COALESCE( tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama) AS penjamin_nama,
                daftartindakan_m.kelompoktindakan_id,
                kelompoktindakan_m.kelompoktindakan_nama,
                daftartindakan_m.kategoritindakan_id,
                kategoritindakan_m.kategoritindakan_nama,
                COALESCE( tarif_penjamin.daftartindakan_id, tariftindakan_m.daftartindakan_id) AS daftartindakan_id ,
                daftartindakan_m.daftartindakan_nama,
                NULL::integer AS tipepaket_id,
                NULL::character varying AS tipepaket_nama,
                tariftindakan_m.komponentarif_id,
                komponentarif_m.komponentarif_nama,
                COALESCE( tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                COALESCE( tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                COALESCE( tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
                tindakanruangan_mp.is_default,
                daftartindakan_m.is_akomodasi,
                penjamin_m.carabayar_id,
                daftartindakan_m.is_konsultasi,
                NULL::VARCHAR as  kamarruangan_nokamar,
                NULL::int4 as kamarruangan_id,
                NULL::int4 as ambulan_id,
                NULL::VARCHAR as no_polisi,
                NULL::integer as kelompokpemeriksaanlab_id,
                NULL::character varying AS nama_kelompok,
                NULL::integer AS jenispemeriksaanlab_id,
                NULL::character varying AS jenispemeriksaanlab_nama,
                NULL::integer AS pemeriksaanlab_id,
                NULL::character varying AS pemeriksaanlab_nama,
                COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit,
                daftartindakan_m.daftartindakan_kode as kode
            FROM tariftindakan_m
            JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
            JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
            JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
            JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id
            JOIN ruangan_m r_tindakan ON tindakanruangan_mp.ruangan_id = r_tindakan.ruangan_id
            JOIN instalasi_m ins_tindakan ON r_tindakan.instalasi_id = ins_tindakan.instalasi_id
            LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id           
            LEFT JOIN (
                SELECT 
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    tariftindakan_m.penjamin_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.perdatarif_id,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id
                FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                WHERE komponentarif_m.is_deleted IS FALSE
                AND perdatarif_m.is_active = true 
                AND tariftindakan_m.is_deleted = false 
                AND tariftindakan_m.is_active = true 
                AND tariftindakan_m.tarifparent_id IS NULL
                AND tariftindakan_m.komponentarif_id = 6
                AND tariftindakan_m.penjamin_id = xpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
            LEFT JOIN (
                SELECT 
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    tariftindakan_m.penjamin_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.perdatarif_id,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id
                FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                WHERE komponentarif_m.is_deleted IS FALSE
                AND perdatarif_m.is_active = true 
                AND tariftindakan_m.is_deleted = false 
                AND tariftindakan_m.is_active = true 
                AND tariftindakan_m.tarifparent_id IS NULL
                AND tariftindakan_m.komponentarif_id = 6
                AND tariftindakan_m.penjamin_id = vpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_kelas ON tariftindakan_m.daftartindakan_id = tarif_kelas.daftartindakan_id AND tariftindakan_m.komponentarif_id = tarif_kelas.komponentarif_id
            WHERE tindakanruangan_mp.is_deleted = false 
            AND komponentarif_m.is_deleted IS FALSE
            AND perdatarif_m.is_active = true 
            AND tariftindakan_m.is_deleted = false 
            AND tariftindakan_m.is_active = true 
            AND tariftindakan_m.tarifparent_id IS NULL
            AND tariftindakan_m.komponentarif_id = 6
            AND tariftindakan_m.penjamin_id = vpenjamin_id
            AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
            UNION ALL
            SELECT 
                \'paket\'::text AS jenis,
                COALESCE( tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
                paketruangan_mp.ruangan_id,
                r_paket.ruangan_nama,
                r_paket.instalasi_id,
                ins_paket.instalasi_nama,
                paketruangan_mp.ruangan_id AS ruanganpaket_id,
                r_paket.ruangan_namalainnya AS ruanganpaket_nama,
                COALESCE( tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id ,
                perdatarif_m.perdanama_sk,
                COALESCE(vkelas_id, tariftindakan_m.kelaspelayanan_id) AS kelaspelayanan_id,
                COALESCE(vkelas_nama, kelaspelayanan_m.kelaspelayanan_nama) AS kelaspelayanan_nama,
                COALESCE( tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id) AS penjamin_id ,
                COALESCE( tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama) AS penjamin_nama,
                NULL::integer AS kelompoktindakan_id,
                NULL::character varying AS kelompoktindakan_nama,
                NULL::integer AS kategoritindakan_id,
                NULL::character varying AS kategoritindakan_nama,
                NULL::integer AS daftartindakan_id,
                NULL::character varying AS daftartindakan_nama,
                tariftindakan_m.tipepaket_id,
                tipepaket_m.tipepaket_nama,
                tariftindakan_m.komponentarif_id,
                komponentarif_m.komponentarif_nama,
                COALESCE( tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                COALESCE( tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                COALESCE( tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
                paketruangan_mp.is_default,
                NULL::boolean AS is_akomodasi,
                penjamin_m.carabayar_id,
                false AS is_konsultasi,
                NULL::VARCHAR as  kamarruangan_nokamar,
                NULL::int4 as kamarruangan_id,
                NULL::int4 as ambulan_id,
                NULL::VARCHAR as no_polisi,
                NULL::integer as kelompokpemeriksaanlab_id,
                NULL::character varying AS nama_kelompok,
                NULL::integer AS jenispemeriksaanlab_id,
                NULL::character varying AS jenispemeriksaanlab_nama,
                NULL::integer AS pemeriksaanlab_id,
                NULL::character varying AS pemeriksaanlab_nama,
                COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit,
                tipepaket_m.tipepaket_kode as kode
            FROM tariftindakan_m
            JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
            AND tipepaket_m.is_deleted= FALSE
            AND tipepaket_m.is_active = TRUE
            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
            JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
            AND perdatarif_m.is_active = TRUE 
            JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id
            AND paketruangan_mp.is_deleted = FALSE
            JOIN ruangan_m r_paket ON paketruangan_mp.ruangan_id = r_paket.ruangan_id
            JOIN instalasi_m ins_paket ON r_paket.instalasi_id = ins_paket.instalasi_id
            LEFT JOIN (
                SELECT 
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    tariftindakan_m.penjamin_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.perdatarif_id,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id,
                    tariftindakan_m.tipepaket_id
                FROM tariftindakan_m
                JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id AND tipepaket_m.is_deleted= FALSE AND tipepaket_m.is_active = TRUE
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_active = TRUE 
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                WHERE tariftindakan_m.is_deleted = FALSE
                AND tariftindakan_m.is_active = TRUE 
                AND tariftindakan_m.tarifparent_id IS NULL
                AND tariftindakan_m.komponentarif_id = 6
                AND tariftindakan_m.penjamin_id = xpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_penjamin ON tariftindakan_m.tipepaket_id = tarif_penjamin.tipepaket_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
            LEFT JOIN (
                SELECT 
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    tariftindakan_m.penjamin_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.perdatarif_id,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id,
                    tariftindakan_m.tipepaket_id
                FROM tariftindakan_m
                JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id AND tipepaket_m.is_deleted= FALSE AND tipepaket_m.is_active = TRUE
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_active = TRUE 
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                WHERE tariftindakan_m.is_deleted = FALSE
                AND tariftindakan_m.is_active = TRUE 
                AND tariftindakan_m.tarifparent_id IS NULL
                AND tariftindakan_m.komponentarif_id = 6
                AND tariftindakan_m.penjamin_id = vpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_kelas ON tariftindakan_m.tipepaket_id = tarif_kelas.tipepaket_id AND tariftindakan_m.komponentarif_id = tarif_kelas.komponentarif_id
            WHERE tariftindakan_m.is_deleted = false 
            AND tariftindakan_m.is_active = true 
            AND tariftindakan_m.tarifparent_id IS NULL
            AND tariftindakan_m.komponentarif_id = 6
            AND tariftindakan_m.penjamin_id = vpenjamin_id
            AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
            UNION ALL
            SELECT 
                \'paket\'::text AS jenis,
                COALESCE( tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
                paketruangan_mp.ruangan_id,
                r_paket.ruangan_nama,
                r_paket.instalasi_id,
                ins_paket.instalasi_nama,
                paketruangan_mp.ruangan_id AS ruanganpaket_id,
                r_paket.ruangan_namalainnya AS ruanganpaket_nama,
                COALESCE( tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id ,
                perdatarif_m.perdanama_sk,
                COALESCE(vkelas_id, tariftindakan_m.kelaspelayanan_id) AS kelaspelayanan_id,
                COALESCE(vkelas_nama, kelaspelayanan_m.kelaspelayanan_nama) AS kelaspelayanan_nama,
                COALESCE( tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id) AS penjamin_id ,
                COALESCE( tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama) AS penjamin_nama,
                NULL::integer AS kelompoktindakan_id,
                NULL::character varying AS kelompoktindakan_nama,
                NULL::integer AS kategoritindakan_id,
                NULL::character varying AS kategoritindakan_nama,
                NULL::integer AS daftartindakan_id,
                NULL::character varying AS daftartindakan_nama,
                tariftindakan_m.tipepaket_id,
                tipepaket_m.tipepaket_nama,
                tariftindakan_m.komponentarif_id,
                komponentarif_m.komponentarif_nama,
                COALESCE( tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                COALESCE( tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                COALESCE( tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
                paketruangan_mp.is_default,
                NULL::boolean AS is_akomodasi,
                penjamin_m.carabayar_id,
                false AS is_konsultasi,
                NULL::VARCHAR as  kamarruangan_nokamar,
                NULL::int4 as kamarruangan_id,
                NULL::int4 as ambulan_id,
                NULL::VARCHAR as no_polisi,
                NULL::integer as kelompokpemeriksaanlab_id,
                NULL::character varying AS nama_kelompok,
                NULL::integer AS jenispemeriksaanlab_id,
                NULL::character varying AS jenispemeriksaanlab_nama,
                NULL::integer AS pemeriksaanlab_id,
                NULL::character varying AS pemeriksaanlab_nama,
                COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit,
                tipepaket_m.tipepaket_kode as kode
            FROM tariftindakan_m
            JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id AND tipepaket_m.is_deleted= FALSE AND tipepaket_m.is_active = TRUE
            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
            JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
            JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id
            JOIN ruangan_m r_paket ON paketruangan_mp.ruangan_id = r_paket.ruangan_id AND paketruangan_mp.is_deleted = FALSE 
            JOIN instalasi_m ins_paket ON r_paket.instalasi_id = ins_paket.instalasi_id
            LEFT JOIN (
                SELECT 
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tipepaket_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    tariftindakan_m.penjamin_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.perdatarif_id,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id
                FROM tariftindakan_m
                JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id AND tipepaket_m.is_deleted= FALSE AND tipepaket_m.is_active = TRUE
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_deleted= FALSE AND perdatarif_m.is_active = TRUE
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                WHERE tariftindakan_m.is_deleted = false 
                AND tariftindakan_m.is_active = true 
                AND tariftindakan_m.tarifparent_id IS NULL
                AND tariftindakan_m.komponentarif_id = 6
                AND tariftindakan_m.penjamin_id = xpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_penjamin ON tariftindakan_m.tipepaket_id = tarif_penjamin.tipepaket_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
            LEFT JOIN (
                SELECT 
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tipepaket_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    tariftindakan_m.penjamin_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.perdatarif_id,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id
                FROM tariftindakan_m
                JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id AND tipepaket_m.is_deleted= FALSE AND tipepaket_m.is_active = TRUE
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_deleted= FALSE AND perdatarif_m.is_active = TRUE
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                WHERE tariftindakan_m.is_deleted = false 
                AND tariftindakan_m.is_active = true 
                AND tariftindakan_m.tarifparent_id IS NULL
                AND tariftindakan_m.komponentarif_id = 6
                AND tariftindakan_m.penjamin_id = vpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_kelas ON tariftindakan_m.tipepaket_id = tarif_penjamin.tipepaket_id AND tariftindakan_m.komponentarif_id = tarif_kelas.komponentarif_id
            WHERE tariftindakan_m.is_deleted = false 
            AND tariftindakan_m.is_active = true 
            AND tariftindakan_m.tarifparent_id IS NOT NULL
            AND tariftindakan_m.komponentarif_id = 6
            AND tariftindakan_m.penjamin_id = vpenjamin_id
            AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
        ) AS x
        WHERE x.ruangan_id = xruangan_id
        GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit, x.kode;
    ELSEIF(xtipe = \'kamar\')
    THEN
        RETURN QUERY 
        SELECT *FROM (
            SELECT      
                \'kamar\'::text AS jenis,
                COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
                kamarruangan_m.ruangan_id as ruangan_id,
                ruangan_m.ruangan_nama as ruangan_nama,
                ruangan_m.instalasi_id as instalasi_id,
                instalasi_m.instalasi_nama as instalasi_nama,
                NULL::integer AS ruanganpaket_id,
                NULL::character varying AS ruanganpaket_nama,
                COALESCE(tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id,
                perdatarif_m.perdanama_sk as perdanama_sk,
                COALESCE(vkelas_id, tariftindakan_m.kelaspelayanan_id) AS kelaspelayanan_id,
                COALESCE(vkelas_nama, kelaspelayanan_m.kelaspelayanan_nama) AS kelaspelayanan_nama,
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
                COALESCE( tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                COALESCE( tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                COALESCE( tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
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
                COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit,
                daftartindakan_m.daftartindakan_kode as kode
            FROM tariftindakan_m
            JOIN kamarruangan_m ON tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
            JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
            JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
            LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
            JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
            JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
            JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
            LEFT JOIN (
                SELECT 
                    tariftindakan_m.daftartindakan_id,
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
                JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
                JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                WHERE tariftindakan_m.is_deleted = false 
                AND tariftindakan_m.is_active = true 
                AND tariftindakan_m.tarifparent_id IS NULL 
                AND kamarruangan_m.is_deleted = FALSE 
                AND kamarruangan_m.is_active = TRUE 
                AND perdatarif_m.is_active = true 
                AND tariftindakan_m.komponentarif_id = 6
                AND tariftindakan_m.penjamin_id = xpenjamin_id
             ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id AND tariftindakan_m.kamarruangan_id = tarif_penjamin.kamarruangan_id AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id
            WHERE tariftindakan_m.is_deleted = false 
            AND tariftindakan_m.is_active = true 
            AND tariftindakan_m.tarifparent_id IS NULL 
            AND kamarruangan_m.is_deleted = FALSE 
            AND kamarruangan_m.is_active = TRUE 
            AND perdatarif_m.is_active = true 
            AND tariftindakan_m.komponentarif_id = 6
            AND tariftindakan_m.penjamin_id = vpenjamin_id
        ) AS x
        WHERE x.ruangan_id = xruangan_id
        AND x.kelaspelayanan_id = xkelaspelayanan_id
        GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit, x.kode;
    ELSEIF(xtipe = \'ambulan\')
    THEN
        RETURN QUERY 
        SELECT *FROM (
            SELECT 
                \'ambulan\'::text AS jenis,
                COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id,
                NULL::integer as ruangan_id,
                NULL::character varying as ruangan_nama,
                NULL::integer as instalasi_id,
                NULL::character varying as instalasi_nama,
                NULL::integer AS ruanganpaket_id,
                NULL::character varying AS ruanganpaket_nama,
                tariftindakan_m.perdatarif_id AS perdatarif_id ,
                perdatarif_m.perdanama_sk as perdanama_sk,
                COALESCE(vkelas_id, tariftindakan_m.kelaspelayanan_id) AS kelaspelayanan_id,
                COALESCE(vkelas_nama, kelaspelayanan_m.kelaspelayanan_nama) AS kelaspelayanan_nama,
                COALESCE(tariftindakan_m.penjamin_id, 1)::integer AS penjamin_id ,
                COALESCE(penjamin_m.penjamin_nama, \'Perseorangan\')::character varying AS penjamin_nama,
                daftartindakan_m.kelompoktindakan_id kelompoktindakan_id,
                null::VARCHAR as kelompoktindakan_nama,
                daftartindakan_m.kategoritindakan_id as kategoritindakan_id,
                null::VARCHAR as kategoritindakan_nama,
                ambulandetail_m.daftartindakan_id AS daftartindakan_id ,
                daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                NULL::integer AS tipepaket_id,
                NULL::character varying AS tipepaket_nama,
                 COALESCE(tariftindakan_m.komponentarif_id, 6) as komponentarif_id,
                COALESCE(komponentarif_m.komponentarif_nama, \'Total Tarif\')::character varying as komponentarif_nama,
                COALESCE( tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                COALESCE( tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                COALESCE( tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
                ambulandetail_m.is_default AS is_default,
                daftartindakan_m.is_akomodasi as is_akomodasi,
                penjamin_m.carabayar_id as carabayar_id,
                daftartindakan_m.is_konsultasi as is_konsultasi,
                NULL::VARCHAR as  kamarruangan_nokamar,
                tariftindakan_m.kamarruangan_id as kamarruangan_id,
                ambulan_m.ambulan_id as ambulan_id,
                ambulan_m.no_polisi as no_polisi,
                NULL::integer as kelompokpemeriksaanlab_id,
                NULL::character varying AS nama_kelompok,
                NULL::integer AS jenispemeriksaanlab_id,
                NULL::character varying AS jenispemeriksaanlab_nama,
                NULL::integer AS pemeriksaanlab_id,
                NULL::character varying AS pemeriksaanlab_nama,
                COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit,
                daftartindakan_m.daftartindakan_kode as kode
            FROM ambulan_m
            JOIN ambulandetail_m ON ambulan_m.ambulan_id = ambulandetail_m.ambulan_id
            JOIN daftartindakan_m ON ambulandetail_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
            LEFT JOIN tariftindakan_m ON ambulandetail_m.daftartindakan_id = tariftindakan_m.daftartindakan_id
            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
            LEFT JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
            LEFT JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
            LEFT JOIN (
                SELECT 
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    tariftindakan_m.penjamin_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.perdatarif_id,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id
                FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                WHERE komponentarif_m.is_deleted IS FALSE
                AND perdatarif_m.is_active = true 
                AND tariftindakan_m.is_deleted = false 
                AND tariftindakan_m.is_active = true 
                AND tariftindakan_m.tarifparent_id IS NULL
                AND tariftindakan_m.komponentarif_id = 6
                AND tariftindakan_m.penjamin_id = xpenjamin_id
            AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
            LEFT JOIN (
                SELECT 
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    tariftindakan_m.penjamin_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.perdatarif_id,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id
                FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                WHERE komponentarif_m.is_deleted IS FALSE
                AND perdatarif_m.is_active = true 
                AND tariftindakan_m.is_deleted = false 
                AND tariftindakan_m.is_active = true 
                AND tariftindakan_m.tarifparent_id IS NULL
                AND tariftindakan_m.komponentarif_id = 6
                AND tariftindakan_m.penjamin_id = vpenjamin_id
            AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_kelas ON tariftindakan_m.daftartindakan_id = tarif_kelas.daftartindakan_id AND tariftindakan_m.komponentarif_id = tarif_kelas.komponentarif_id
            WHERE 
            ambulan_m.is_deleted = false AND
            ambulan_m.is_active = true AND
            ambulandetail_m.is_deleted = false AND
            ambulandetail_m.is_active = true AND
            tariftindakan_m.is_deleted=false AND
            tariftindakan_m.is_active=true AND
            perdatarif_m.is_active = true AND 
            perdatarif_m.is_deleted = false AND
            tariftindakan_m.komponentarif_id = 6 AND
            tariftindakan_m.penjamin_id = vpenjamin_id
            AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
        ) AS x
        WHERE x.kelaspelayanan_id = xkelaspelayanan_id
        GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit, x.kode;
    ELSEIF(xtipe = \'penunjang\')
    THEN
        RETURN QUERY
        SELECT *FROM (
            SELECT
                \'lab\'::text AS jenis,
                COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                tindakanruangan_mp.ruangan_id as ruangan_id,
                ruangan_m.ruangan_nama as ruangan_nama,
                ruangan_m.instalasi_id as instalasi_id,
                null::character varying as instalasi_nama,
                NULL::integer AS ruanganpaket_id,
                NULL::character varying AS ruanganpaket_nama,
                tariftindakan_m.perdatarif_id AS perdatarif_id ,
                perdatarif_m.perdanama_sk as perdanama_sk,
                COALESCE(vkelas_id, tariftindakan_m.kelaspelayanan_id) AS kelaspelayanan_id,
                COALESCE(vkelas_nama, kelaspelayanan_m.kelaspelayanan_nama) AS kelaspelayanan_nama,
                COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
                COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
                NULL::integer as kelompoktindakan_id,
                NULL::character varying as kelompoktindakan_nama,
                NULL::integer as kategoritindakan_id,
                NULL::character varying kategoritindakan_nama,
                tariftindakan_m.daftartindakan_id AS daftartindakan_id ,
                daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                NULL::integer AS tipepaket_id,
                NULL::character varying AS tipepaket_nama,
                tariftindakan_m.komponentarif_id as komponentarif_id,
                komponentarif_m.komponentarif_nama as komponentarif_nama,
                COALESCE( tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                COALESCE( tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                COALESCE( tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
                tindakanruangan_mp.is_default AS is_default,
                daftartindakan_m.is_akomodasi as is_akomodasi,
                penjamin_m.carabayar_id as carabayar_id,
                daftartindakan_m.is_konsultasi as is_konsultasi,
                NULL::character varying as  kamarruangan_nokamar,
                NULL::integer as kamarruangan_id,
                NULL::integer as ambulan_id,
                NULL::character varying as no_polisi,
                pemeriksaanlab_m.kelompokpemeriksaanlab_id::int4,
                kelompokpemeriksaanlab_m.nama_kelompok::character varying ,
                pemeriksaanlab_m.jenispemeriksaanlab_id::int4,
                jenispemeriksaanlab_m.jenispemeriksaanlab_nama::character varying ,
                pemeriksaanlab_m.pemeriksaanlab_id::int4,
                pemeriksaanlab_m.pemeriksaanlab_nama::character varying ,
                COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit,
                daftartindakan_m.daftartindakan_kode as kode                
            FROM tariftindakan_m
            JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
            JOIN pemeriksaanlab_m ON tariftindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id 
            AND pemeriksaanlab_m.is_deleted = FALSE
            JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
            JOIN kelompokpemeriksaanlab_m ON pemeriksaanlab_m.kelompokpemeriksaanlab_id = kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id
            JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id  AND perdatarif_m.is_deleted = FALSE  AND perdatarif_m.is_active = TRUE
            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
            JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id AND tindakanruangan_mp.is_deleted = FALSE
            JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
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
                    tariftindakan_m.komponentarif_id
                FROM tariftindakan_m
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_active = TRUE AND perdatarif_m.is_deleted = FALSE
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                WHERE tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND tariftindakan_m.tarifparent_id IS NULL
                AND tariftindakan_m.komponentarif_id = 6
                AND tariftindakan_m.penjamin_id = xpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
             ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
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
                        tariftindakan_m.komponentarif_id
                    FROM tariftindakan_m
                    JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_active = TRUE AND perdatarif_m.is_deleted = FALSE
                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                    WHERE tariftindakan_m.is_deleted = FALSE 
                    AND tariftindakan_m.is_active = TRUE 
                    AND tariftindakan_m.tarifparent_id IS NULL
                    AND tariftindakan_m.komponentarif_id = 6
                    AND tariftindakan_m.penjamin_id = vpenjamin_id
                    AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
             ) tarif_kelas ON tariftindakan_m.daftartindakan_id = tarif_kelas.daftartindakan_id AND tariftindakan_m.komponentarif_id = tarif_kelas.komponentarif_id
            WHERE tariftindakan_m.is_deleted = FALSE 
            AND tariftindakan_m.is_active = TRUE 
            AND tariftindakan_m.tarifparent_id IS NULL 
            AND tariftindakan_m.komponentarif_id = 6
            AND tariftindakan_m.penjamin_id = vpenjamin_id
            AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
            UNION ALL
            SELECT  
                \'rad\'::text AS jenis,
                COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                tindakanruangan_mp.ruangan_id as ruangan_id,
                ruangan_m.ruangan_nama as ruangan_nama,
                ruangan_m.instalasi_id as instalasi_id,
                NULL::character varying as instalasi_nama,
                NULL::integer AS ruanganpaket_id,
                NULL::character varying AS ruanganpaket_nama,
                tariftindakan_m.perdatarif_id AS perdatarif_id ,
                perdatarif_m.perdanama_sk as perdanama_sk,
                COALESCE(vkelas_id, tariftindakan_m.kelaspelayanan_id) AS kelaspelayanan_id,
                COALESCE(vkelas_nama, kelaspelayanan_m.kelaspelayanan_nama) AS kelaspelayanan_nama,
                COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
                COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
                NULL::integer as kelompoktindakan_id,
                NULL::character varying as kelompoktindakan_nama,
                NULL::integer as kategoritindakan_id,
                NULL::character varying as kategoritindakan_nama,
                tariftindakan_m.daftartindakan_id AS daftartindakan_id ,
                daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                NULL::integer AS tipepaket_id,
                NULL::character varying AS tipepaket_nama,
                tariftindakan_m.komponentarif_id as komponentarif_id,
                komponentarif_m.komponentarif_nama as komponentarif_nama,
                COALESCE( tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                COALESCE( tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                COALESCE( tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
                tindakanruangan_mp.is_default AS is_default,
                daftartindakan_m.is_akomodasi as is_akomodasi,
                penjamin_m.carabayar_id as carabayar_id,
                daftartindakan_m.is_konsultasi as is_konsultasi,
                NULL::character varying as  kamarruangan_nokamar,
                NULL::integer as kamarruangan_id,
                NULL::integer as ambulan_id,
                NULL::character varying as no_polisi,
                pemeriksaanrad_m.kelompokpemeriksaanrad_id as kelompokpemeriksaanlab_id,
                kelompokpemeriksaanrad_m.nama_kelompok as nama_kelompok,
                pemeriksaanrad_m.jenispemeriksaanrad_id as jenispemeriksaanlab_id,
                jenispemeriksaanrad_m.jenispemeriksaanrad_nama as jenispemeriksaanlab_nama,
                pemeriksaanrad_m.pemeriksaanradiologi_id as pemeriksaanlab_id,
                pemeriksaanrad_m.pemeriksaanrad_nama  as pemeriksaanlab_nama,
                COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit,
                daftartindakan_m.daftartindakan_kode    as kode         
            FROM tariftindakan_m
            JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
            JOIN pemeriksaanrad_m ON tariftindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id 
            AND pemeriksaanrad_m.is_deleted = FALSE
            JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
            JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id
            JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id  AND perdatarif_m.is_deleted = FALSE  AND perdatarif_m.is_active = TRUE
            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
            JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id AND tindakanruangan_mp.is_deleted = FALSE
            JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
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
                    tariftindakan_m.komponentarif_id
                FROM tariftindakan_m
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                AND perdatarif_m.is_active = TRUE
                AND perdatarif_m.is_deleted = FALSE
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                WHERE tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND tariftindakan_m.tarifparent_id IS NULL
                AND tariftindakan_m.komponentarif_id = 6
                AND tariftindakan_m.penjamin_id = xpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
            AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
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
                    tariftindakan_m.komponentarif_id
                FROM tariftindakan_m
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                AND perdatarif_m.is_active = TRUE
                AND perdatarif_m.is_deleted = FALSE
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                WHERE tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND tariftindakan_m.tarifparent_id IS NULL
                AND tariftindakan_m.komponentarif_id = 6
                AND tariftindakan_m.penjamin_id = vpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_kelas ON tariftindakan_m.daftartindakan_id = tarif_kelas.daftartindakan_id 
            AND tariftindakan_m.komponentarif_id = tarif_kelas.komponentarif_id
            WHERE tariftindakan_m.is_deleted = FALSE 
            AND tariftindakan_m.is_active = TRUE 
            AND tariftindakan_m.tarifparent_id IS NULL 
            AND tariftindakan_m.komponentarif_id = 6
            AND tariftindakan_m.penjamin_id = vpenjamin_id
            AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
            UNION ALL
            SELECT 
                \'operasi\'::text AS jenis,
                COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                tindakanruangan_mp.ruangan_id as ruangan_id,
                ruangan_m.ruangan_nama as ruangan_nama,
                ruangan_m.instalasi_id as instalasi_id,
                NULL::character varying instalasi_nama,
                NULL::integer AS ruanganpaket_id,
                NULL::character varying AS ruanganpaket_nama,
                tariftindakan_m.perdatarif_id AS perdatarif_id ,
                perdatarif_m.perdanama_sk as perdanama_sk,
                COALESCE(vkelas_id, tariftindakan_m.kelaspelayanan_id) AS kelaspelayanan_id,
                COALESCE(vkelas_nama, kelaspelayanan_m.kelaspelayanan_nama) AS kelaspelayanan_nama,
                COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
                COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
                null::integer as kelompoktindakan_id,
                NULL::character varying as kelompoktindakan_nama,
                null::integer as kategoritindakan_id,
                NULL::character varying as kategoritindakan_nama,
                tariftindakan_m.daftartindakan_id AS daftartindakan_id ,
                daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                NULL::integer AS tipepaket_id,
                NULL::character varying AS tipepaket_nama,
                tariftindakan_m.komponentarif_id as komponentarif_id,
                komponentarif_m.komponentarif_nama as komponentarif_nama,
                COALESCE( tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                COALESCE( tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                COALESCE( tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
                tindakanruangan_mp.is_default AS is_default,
                daftartindakan_m.is_akomodasi as is_akomodasi,
                penjamin_m.carabayar_id as carabayar_id,
                daftartindakan_m.is_konsultasi as is_konsultasi,
                NULL::character varying as  kamarruangan_nokamar,
                null::integer as kamarruangan_id,
                null::integer as ambulan_id,
                NULL::character varying as no_polisi,
                operasi_m.golonganoperasi_id as kelompokpemeriksaanlab_id,
                golonganoperasi_m.golonganoperasi_nama as nama_kelompok,
                operasi_m.kegiatanoperasi_id as jenispemeriksaanlab_id,
                kegiatanoperasi_m.kegiatanoperasi_nama as jenispemeriksaanlab_nama,
                operasi_m.operasi_id as pemeriksaanlab_id,
                operasi_m.operasi_nama  as pemeriksaanlab_nama,
                COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit,
                daftartindakan_m.daftartindakan_kode    as kode             
            FROM ((((((((((tariftindakan_m
            JOIN daftartindakan_m ON ((tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
            JOIN operasi_m ON (((tariftindakan_m.daftartindakan_id = operasi_m.daftartindakan_id) AND (operasi_m.is_deleted = false))))
            JOIN golonganoperasi_m ON ((operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id)))
            JOIN kegiatanoperasi_m ON ((operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id)))
            JOIN kelaspelayanan_m ON ((tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN perdatarif_m ON (((tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id) AND (perdatarif_m.is_deleted = false) AND (perdatarif_m.is_active = true))))
            JOIN penjamin_m ON ((tariftindakan_m.penjamin_id = penjamin_m.penjamin_id)))
            JOIN komponentarif_m ON ((tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id)))
            JOIN tindakanruangan_mp ON (((tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id) AND (tindakanruangan_mp.is_deleted = false))))
            JOIN ruangan_m ON ((tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id)))
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
                tariftindakan_m.komponentarif_id
                FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                WHERE komponentarif_m.is_deleted IS FALSE
                AND perdatarif_m.is_active = true 
                AND tariftindakan_m.is_deleted = false 
                AND tariftindakan_m.is_active = true 
                AND tariftindakan_m.tarifparent_id IS NULL
                AND tariftindakan_m.komponentarif_id = 6
                AND tariftindakan_m.penjamin_id = xpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
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
                tariftindakan_m.komponentarif_id
                FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                WHERE komponentarif_m.is_deleted IS FALSE
                AND perdatarif_m.is_active = true 
                AND tariftindakan_m.is_deleted = false 
                AND tariftindakan_m.is_active = true 
                AND tariftindakan_m.tarifparent_id IS NULL
                AND tariftindakan_m.komponentarif_id = 6
                AND tariftindakan_m.penjamin_id = vpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_kelas ON tariftindakan_m.daftartindakan_id = tarif_kelas.daftartindakan_id AND tariftindakan_m.komponentarif_id = tarif_kelas.komponentarif_id
            WHERE  tariftindakan_m.is_deleted = false AND
            tariftindakan_m.is_active = true AND
            tariftindakan_m.tarifparent_id IS NULL AND
            perdatarif_m.is_active = true AND
            tariftindakan_m.komponentarif_id = 6
            AND tariftindakan_m.penjamin_id = vpenjamin_id
            AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
            UNION ALL
            SELECT 
                CASE
                    WHEN (ruangan_m.instalasi_id = 4) THEN \'paket_lab\'
                    WHEN (ruangan_m.instalasi_id = 5) THEN \'paket_rad\'
                    WHEN (ruangan_m.instalasi_id = 21) THEN \'paket_mcu\'
                    ELSE \'paket_operasi\'
                END AS jenis,
                COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                paketruangan_mp.ruangan_id as ruangan_id,
                ruangan_m.ruangan_nama as ruangan_nama,
                ruangan_m.instalasi_id as instalasi_id,
                null::character varying AS instalasi_nama,
                NULL::integer AS ruanganpaket_id,
                NULL::character varying AS ruanganpaket_nama,
                tariftindakan_m.perdatarif_id AS perdatarif_id ,
                perdatarif_m.perdanama_sk as perdanama_sk,
                COALESCE(vkelas_id, tariftindakan_m.kelaspelayanan_id) AS kelaspelayanan_id,
                COALESCE(vkelas_nama, kelaspelayanan_m.kelaspelayanan_nama) AS kelaspelayanan_nama,
                COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
                COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
                null::integer as kelompoktindakan_id,
                null::character varying  as kelompoktindakan_nama,
                null::integer as kategoritindakan_id,
                null::character varying  as kategoritindakan_nama,
                tariftindakan_m.tipepaket_id  AS daftartindakan_id ,
                tipepaket_m.tipepaket_nama as daftartindakan_nama,
                tariftindakan_m.tipepaket_id  AS tipepaket_id,
                tipepaket_m.tipepaket_nama AS tipepaket_nama,
                tariftindakan_m.komponentarif_id as komponentarif_id,
                komponentarif_m.komponentarif_nama as komponentarif_nama,
                COALESCE( tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                COALESCE( tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                COALESCE( tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
                paketruangan_mp.is_default AS is_default,
                null::boolean as is_akomodasi,
                penjamin_m.carabayar_id as carabayar_id,
                null::boolean as is_konsultasi,
                null::character varying  as  kamarruangan_nokamar,
                null::integer as kamarruangan_id,
                null::integer as ambulan_id,
                null::character varying  as no_polisi,
                null as kelompokpemeriksaanlab_id,
                null AS nama_kelompok,
                null AS jenispemeriksaanlab_id,
                null AS jenispemeriksaanlab_nama,
                null AS pemeriksaanlab_id,
                null AS pemeriksaanlab_nama,
                COALESCE(tariftindakan_m.persen_penyulit, 0::numeric) AS persen_penyulit,
                tipepaket_m.tipepaket_kode  as kode 
            FROM tariftindakan_m
            JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id                 
            JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id 
            AND paketruangan_mp.is_deleted = FALSE
            JOIN ruangan_m ON paketruangan_mp.ruangan_id = ruangan_m.ruangan_id
            JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
            AND perdatarif_m.is_deleted = FALSE 
            AND perdatarif_m.is_active = TRUE
            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
            LEFT JOIN (
                SELECT 
                    tariftindakan_m.tipepaket_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    tariftindakan_m.penjamin_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.perdatarif_id,
                    tariftindakan_m.komponentarif_id,
                    penjamin_m.penjamin_nama
                FROM tariftindakan_m
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                AND perdatarif_m.is_deleted = FALSE 
                AND perdatarif_m.is_active = TRUE
                WHERE tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND tariftindakan_m.komponentarif_id = 6
                AND tariftindakan_m.penjamin_id = xpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_penjamin ON tariftindakan_m.tipepaket_id = tarif_penjamin.tipepaket_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
            LEFT JOIN (
                SELECT 
                    tariftindakan_m.tipepaket_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    tariftindakan_m.penjamin_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.perdatarif_id,
                    tariftindakan_m.komponentarif_id,
                    penjamin_m.penjamin_nama
                FROM tariftindakan_m
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                AND perdatarif_m.is_deleted = FALSE 
                AND perdatarif_m.is_active = TRUE
                WHERE tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND tariftindakan_m.komponentarif_id = 6
                AND tariftindakan_m.penjamin_id = vpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_kelas ON tariftindakan_m.tipepaket_id = tarif_kelas.tipepaket_id AND tariftindakan_m.komponentarif_id = tarif_kelas.komponentarif_id
            WHERE tariftindakan_m.is_deleted = FALSE 
            AND tariftindakan_m.is_active = TRUE 
            AND perdatarif_m.is_active = TRUE 
            AND tariftindakan_m.komponentarif_id = 6
            AND tariftindakan_m.penjamin_id = vpenjamin_id          
            AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
        ) x
        WHERE x.ruangan_id = xruangan_id
        GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit, x.kode;
    
    ELSEIF(xtipe = \'makanan\')
    THEN
        RETURN QUERY 
        SELECT *FROM (
            SELECT
                \'makanan\'::text AS jenis,
                COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                tindakanruangan_mp.ruangan_id as ruangan_id,
                ruangan_m.ruangan_nama as ruangan_nama,
                ruangan_m.instalasi_id as instalasi_id,
                null::character varying as instalasi_nama,
                NULL::integer AS ruanganpaket_id,
                NULL::character varying AS ruanganpaket_nama,
                tariftindakan_m.perdatarif_id AS perdatarif_id ,
                perdatarif_m.perdanama_sk as perdanama_sk,
                COALESCE(vkelas_id, tariftindakan_m.kelaspelayanan_id) AS kelaspelayanan_id,
                COALESCE(vkelas_nama, kelaspelayanan_m.kelaspelayanan_nama) AS kelaspelayanan_nama,
                COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
                COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
                NULL::integer as kelompoktindakan_id,
                NULL::character varying as kelompoktindakan_nama,
                NULL::integer as kategoritindakan_id,
                NULL::character varying kategoritindakan_nama,
                tariftindakan_m.daftartindakan_id AS daftartindakan_id ,
                daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                NULL::integer AS tipepaket_id,
                NULL::character varying AS tipepaket_nama,
                tariftindakan_m.komponentarif_id as komponentarif_id,
                komponentarif_m.komponentarif_nama as komponentarif_nama,
                COALESCE( tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                COALESCE( tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                COALESCE( tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
                tindakanruangan_mp.is_default AS is_default,
                daftartindakan_m.is_akomodasi as is_akomodasi,
                penjamin_m.carabayar_id as carabayar_id,
                daftartindakan_m.is_konsultasi as is_konsultasi,
                NULL::character varying as  kamarruangan_nokamar,
                NULL::integer as kamarruangan_id,
                NULL::integer as ambulan_id,
                NULL::character varying as no_polisi,
                makanandiet_m.jenisdiet_id::int4 AS kelompokpemeriksaanlab_id,
                jenisdiet_m.jenisdiet_nama::character varying nama_kelompok ,
                makanandiet_m.jenisdiet_id::int4 AS jenispemeriksaanlab_id,
                jenisdiet_m.jenisdiet_nama::character varying AS jenispemeriksaanlab_nama ,
                makanandiet_m.makanandiet_id::int4 AS pemeriksaanlab_id,
                makanandiet_m.makanandiet_nama::character varying AS pemeriksaanlab_nama ,
                COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit,
                daftartindakan_m.daftartindakan_kode as kode
            FROM tariftindakan_m
            JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
            JOIN makanandiet_m ON tariftindakan_m.daftartindakan_id = makanandiet_m.daftartindakan_id AND makanandiet_m.is_deleted = FALSE
            JOIN jenisdiet_m ON makanandiet_m.jenisdiet_id = jenisdiet_m.jenisdiet_id
            JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id  AND perdatarif_m.is_deleted = FALSE  AND perdatarif_m.is_active = TRUE
            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
            JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id  AND tindakanruangan_mp.is_deleted = FALSE
            JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
            LEFT JOIN (
                SELECT 
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    tariftindakan_m.penjamin_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.perdatarif_id,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id
                FROM tariftindakan_m
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_active = TRUE AND perdatarif_m.is_deleted = FALSE 
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                WHERE tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND tariftindakan_m.tarifparent_id IS NULL
                AND tariftindakan_m.komponentarif_id = 6
                AND tariftindakan_m.penjamin_id = xpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id  AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
            LEFT JOIN (
                SELECT 
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    tariftindakan_m.penjamin_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.perdatarif_id,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id
                FROM tariftindakan_m
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_active = TRUE AND perdatarif_m.is_deleted = FALSE 
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                WHERE tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND tariftindakan_m.tarifparent_id IS NULL
                AND tariftindakan_m.komponentarif_id = 6
                AND tariftindakan_m.penjamin_id = vpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_kelas ON tariftindakan_m.daftartindakan_id = tarif_kelas.daftartindakan_id  AND tariftindakan_m.komponentarif_id = tarif_kelas.komponentarif_id
            WHERE tariftindakan_m.is_deleted = FALSE 
            AND tariftindakan_m.is_active = TRUE 
            AND tariftindakan_m.tarifparent_id IS NULL 
            AND tariftindakan_m.komponentarif_id = 6
            AND tariftindakan_m.penjamin_id = vpenjamin_id
            AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
        ) AS x
        WHERE  x.ruangan_id = xruangan_id
        GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit, x.kode;
    END IF;
END; 
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;');

        $this->execute('ALTER FUNCTION "public"."tariftotalrs_fn"("xruangan_id" int4, "xpenjamin_id" int4, "xkelaspelayanan_id" int4, "xtipe" varchar) OWNER TO "postgres";');

        $this->execute('DROP FUNCTION if exists "public"."tariftotalkamarrs_fn"("xruangan_id" int4, "xpenjamin_id" int4, "xkelaspelayanan_id" int4, "xtipe" varchar);');

        $this->execute('CREATE OR REPLACE FUNCTION "public"."tariftotalkamarrs_fn"("xruangan_id" int4=0, "xpenjamin_id" int4=0, "xkelaspelayanan_id" int4=0, "xtipe" varchar=\'kamar\'::character varying)
  RETURNS TABLE("jenis" text, "tariftindakan_id" int4, "ruangan_id" int4, "ruangan_nama" varchar, "instalasi_id" int4, "instalasi_nama" varchar, "ruanganpaket_id" int4, "ruanganpaket_nama" varchar, "perdatarif_id" int4, "perdanama_sk" varchar, "kelaspelayanan_id" int4, "kelaspelayanan_nama" varchar, "penjamin_id" int4, "penjamin_nama" varchar, "kelompoktindakan_id" int4, "kelompoktindakan_nama" varchar, "kategoritindakan_id" int4, "kategoritindakan_nama" varchar, "daftartindakan_id" int4, "daftartindakan_nama" varchar, "tipepaket_id" int4, "tipepaket_nama" varchar, "komponentarif_id" int4, "komponentarif_nama" varchar, "harga_tariftindakan" numeric, "persencyto_tindakan" numeric, "persendiskon_tindakan" numeric, "is_default" bool, "is_akomodasi" bool, "carabayar_id" int4, "is_konsultasi" bool, "kamarruangan_nokamar" varchar, "kamarruangan_id" int4, "ambulan_id" int4, "no_polisi" varchar, "kelompokpemeriksaanlab_id" int4, "nama_kelompok" varchar, "jenispemeriksaanlab_id" int4, "jenispemeriksaanlab_nama" varchar, "pemeriksaanlab_id" int4, "pemeriksaanlab_nama" varchar, "persen_penyulit" numeric, "kamarruangan_jenis" int4, "kamarruangan_jenis_nama" varchar, "status_isi" bool, "jeniskasuspenyakit_id" int4, "jeniskasuspenyakit_nama" varchar, "kettempattidur_id" int4, "kettempattidur_nama" varchar, "kode_warna" varchar, "kettempattidur_warna" varchar, "kamartempattidur_id" int4, "no_tempattidur" varchar, "isi_jk" int4) AS $BODY$ 
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
            \'kamar\'::text AS jenis,
            COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
            kamarruangan_m.ruangan_id as ruangan_id,
            ruangan_m.ruangan_nama as ruangan_nama,
            ruangan_m.instalasi_id as instalasi_id,
            instalasi_m.instalasi_nama as instalasi_nama,
            NULL::integer AS ruanganpaket_id,
            NULL::character varying AS ruanganpaket_nama,
            COALESCE(tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id,
             perdatarif_m.perdanama_sk as perdanama_sk,
            tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
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
         JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
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
             JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
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
         ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id AND tariftindakan_m.kamarruangan_id = tarif_penjamin.kamarruangan_id
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
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;');

        $this->execute('ALTER FUNCTION "public"."tariftotalkamarrs_fn"("xruangan_id" int4, "xpenjamin_id" int4, "xkelaspelayanan_id" int4, "xtipe" varchar) OWNER TO "postgres";');


        $this->execute('DROP FUNCTION if exists "public"."tarifkomponenrs_fn"("xruangan_id" int4, "xpenjamin_id" int4, "xkelaspelayanan_id" int4, "xtipe" varchar);');

        $this->execute('CREATE OR REPLACE FUNCTION "public"."tarifkomponenrs_fn"("xruangan_id" int4=0, "xpenjamin_id" int4=0, "xkelaspelayanan_id" int4=0, "xtipe" varchar=\'pelayanan\'::character varying)
  RETURNS TABLE("jenis" text, "tariftindakan_id" int4, "ruangan_id" int4, "ruangan_nama" varchar, "instalasi_id" int4, "instalasi_nama" varchar, "ruanganpaket_id" int4, "ruanganpaket_nama" varchar, "perdatarif_id" int4, "perdanama_sk" varchar, "kelaspelayanan_id" int4, "kelaspelayanan_nama" varchar, "penjamin_id" int4, "penjamin_nama" varchar, "kelompoktindakan_id" int4, "kelompoktindakan_nama" varchar, "kategoritindakan_id" int4, "kategoritindakan_nama" varchar, "daftartindakan_id" int4, "daftartindakan_nama" varchar, "tipepaket_id" int4, "tipepaket_nama" varchar, "komponentarif_id" int4, "komponentarif_nama" varchar, "harga_tariftindakan" numeric, "persencyto_tindakan" numeric, "persendiskon_tindakan" numeric, "is_default" bool, "is_akomodasi" bool, "carabayar_id" int4, "is_konsultasi" bool, "kamarruangan_nokamar" varchar, "kamarruangan_id" int4, "ambulan_id" int4, "no_polisi" varchar, "kelompokpemeriksaanlab_id" int4, "nama_kelompok" varchar, "jenispemeriksaanlab_id" int4, "jenispemeriksaanlab_nama" varchar, "pemeriksaanlab_id" int4, "pemeriksaanlab_nama" varchar, "persen_penyulit" numeric) AS $BODY$ 

DECLARE 
    vpenjamin_id int4;
    vkelaspelayanan_id int4;
    vkelas_id int4;
    vkelas_nama VARCHAR;
BEGIN
    SELECT default_kelas INTO vkelaspelayanan_id
    FROM konfigtarif_k
    WHERE konfigtarif_id = 1;
    
    IF(xkelaspelayanan_id <> 0)
    THEN
        SELECT kelaspelayanan_m.kelaspelayanan_id::int4, kelaspelayanan_m.kelaspelayanan_nama::VARCHAR
        INTO vkelas_id, vkelas_nama
        FROM kelaspelayanan_m
        WHERE kelaspelayanan_m.kelaspelayanan_id = xkelaspelayanan_id;
    ELSE
        SELECT kelaspelayanan_m.kelaspelayanan_id::int4, kelaspelayanan_m.kelaspelayanan_nama::VARCHAR
        INTO vkelas_id, vkelas_nama
        FROM kelaspelayanan_m
        WHERE kelaspelayanan_m.kelaspelayanan_id = vkelaspelayanan_id;
    END IF;
    
    IF(xruangan_id = 0)
    THEN
        SELECT default_ruangan INTO xruangan_id
        FROM konfigtarif_k
        WHERE konfigtarif_id = 1;
    END IF;
    
    IF(xkelaspelayanan_id = 0)
    THEN
        SELECT default_kelas INTO xkelaspelayanan_id
        FROM konfigtarif_k
        WHERE konfigtarif_id = 1;
    END IF;
    
    SELECT default_penjamin INTO vpenjamin_id
    FROM konfigtarif_k
    WHERE konfigtarif_id = 1;
    
    IF(xpenjamin_id = vpenjamin_id)
    THEN
        xpenjamin_id := 0;
    END IF;

    IF(xkelaspelayanan_id = vkelaspelayanan_id)
    THEN
        xkelaspelayanan_id := 0;
    END IF;
    
    
    IF(xtipe = \'pelayanan\')
    THEN
        RETURN QUERY 
        SELECT * FROM 
            (SELECT \'tindakan\'::text AS jenis,
            COALESCE( tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
            tindakanruangan_mp.ruangan_id,
            r_tindakan.ruangan_nama,
            r_tindakan.instalasi_id,
            ins_tindakan.instalasi_nama,
            NULL::integer AS ruanganpaket_id,
            NULL::character varying AS ruanganpaket_nama,
            COALESCE( tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id ,
            perdatarif_m.perdanama_sk,
            COALESCE(vkelas_id, tariftindakan_m.kelaspelayanan_id) AS kelaspelayanan_id,
            COALESCE(vkelas_nama, kelaspelayanan_m.kelaspelayanan_nama) AS kelaspelayanan_nama,
            COALESCE( tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id) AS penjamin_id ,
            COALESCE( tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama) AS penjamin_nama,
            daftartindakan_m.kelompoktindakan_id,
            kelompoktindakan_m.kelompoktindakan_nama,
            daftartindakan_m.kategoritindakan_id,
            kategoritindakan_m.kategoritindakan_nama,
            COALESCE( tarif_penjamin.daftartindakan_id, tariftindakan_m.daftartindakan_id) AS daftartindakan_id ,
            daftartindakan_m.daftartindakan_nama,
            NULL::integer AS tipepaket_id,
            NULL::character varying AS tipepaket_nama,
            tariftindakan_m.komponentarif_id,
            komponentarif_m.komponentarif_nama,
            COALESCE( tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
            COALESCE( tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
            COALESCE( tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
            tindakanruangan_mp.is_default,
            daftartindakan_m.is_akomodasi,
            penjamin_m.carabayar_id,
            daftartindakan_m.is_konsultasi,
            NULL::VARCHAR as  kamarruangan_nokamar,
            NULL::int4 as kamarruangan_id,
            NULL::int4 as ambulan_id,
            NULL::VARCHAR as no_polisi,
            NULL::integer as kelompokpemeriksaanlab_id,
            NULL::character varying AS nama_kelompok,
            NULL::integer AS jenispemeriksaanlab_id,
            NULL::character varying AS jenispemeriksaanlab_nama,
            NULL::integer AS pemeriksaanlab_id,
            NULL::character varying AS pemeriksaanlab_nama,
            COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit 
        FROM tariftindakan_m
        JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
        JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
        JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
        JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
        JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
        JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
        JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id
        JOIN ruangan_m r_tindakan ON tindakanruangan_mp.ruangan_id = r_tindakan.ruangan_id
        JOIN instalasi_m ins_tindakan ON r_tindakan.instalasi_id = ins_tindakan.instalasi_id
        LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id          
        LEFT JOIN (
            SELECT 
                tariftindakan_m.daftartindakan_id,
                tariftindakan_m.tariftindakan_id,
                tariftindakan_m.kelaspelayanan_id,
                tariftindakan_m.penjamin_id,
                tariftindakan_m.harga_tariftindakan,
                tariftindakan_m.persencyto_tindakan,
                tariftindakan_m.persendiskon_tindakan,
                tariftindakan_m.perdatarif_id,
                penjamin_m.penjamin_nama,
                tariftindakan_m.komponentarif_id
                FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                WHERE komponentarif_m.is_deleted IS FALSE
                AND perdatarif_m.is_active = TRUE 
                AND tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND tariftindakan_m.tarifparent_id IS NULL
                AND tariftindakan_m.komponentarif_id <> 6
                AND tariftindakan_m.penjamin_id = xpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
        ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
        LEFT JOIN (
            SELECT 
                tariftindakan_m.daftartindakan_id,
                tariftindakan_m.tariftindakan_id,
                tariftindakan_m.kelaspelayanan_id,
                tariftindakan_m.penjamin_id,
                tariftindakan_m.harga_tariftindakan,
                tariftindakan_m.persencyto_tindakan,
                tariftindakan_m.persendiskon_tindakan,
                tariftindakan_m.perdatarif_id,
                penjamin_m.penjamin_nama,
                tariftindakan_m.komponentarif_id
                FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                WHERE komponentarif_m.is_deleted IS FALSE
                AND perdatarif_m.is_active = TRUE 
                AND tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND tariftindakan_m.tarifparent_id IS NULL
                AND tariftindakan_m.komponentarif_id <> 6
                AND tariftindakan_m.penjamin_id = vpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
        ) tarif_kelas ON tariftindakan_m.daftartindakan_id = tarif_kelas.daftartindakan_id AND tariftindakan_m.komponentarif_id = tarif_kelas.komponentarif_id
        WHERE tindakanruangan_mp.is_deleted = false 
        AND komponentarif_m.is_deleted IS FALSE
        AND perdatarif_m.is_active = true 
        AND tariftindakan_m.is_deleted = false 
        AND tariftindakan_m.is_active = true 
        AND tariftindakan_m.tarifparent_id IS NULL
        AND tariftindakan_m.komponentarif_id <> 6
        AND tariftindakan_m.penjamin_id = vpenjamin_id
        AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
        UNION ALL
        SELECT 
            \'paket\'::text AS jenis,
            COALESCE( tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
            paketruangan_mp.ruangan_id,
            r_paket.ruangan_nama,
            r_paket.instalasi_id,
            ins_paket.instalasi_nama,
            paketruangan_mp.ruangan_id AS ruanganpaket_id,
            r_paket.ruangan_namalainnya AS ruanganpaket_nama,
            COALESCE( tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id ,
            perdatarif_m.perdanama_sk,
            COALESCE(vkelas_id, tariftindakan_m.kelaspelayanan_id) AS kelaspelayanan_id,
            COALESCE(vkelas_nama, kelaspelayanan_m.kelaspelayanan_nama) AS kelaspelayanan_nama,
            COALESCE( tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id) AS penjamin_id ,
            COALESCE( tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama) AS penjamin_nama,
            NULL::integer AS kelompoktindakan_id,
            NULL::character varying AS kelompoktindakan_nama,
            NULL::integer AS kategoritindakan_id,
            NULL::character varying AS kategoritindakan_nama,
            tariftindakan_m.daftartindakan_id AS daftartindakan_id,
            NULL::character varying AS daftartindakan_nama,
            tariftindakan_m.tipepaket_id,
            tipepaket_m.tipepaket_nama,
            tariftindakan_m.komponentarif_id,
            komponentarif_m.komponentarif_nama,
            COALESCE( tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
            COALESCE( tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
            COALESCE( tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
            paketruangan_mp.is_default,
            NULL::boolean AS is_akomodasi,
            penjamin_m.carabayar_id,
            false AS is_konsultasi,
            NULL::VARCHAR as  kamarruangan_nokamar,
            NULL::int4 as kamarruangan_id,
            NULL::int4 as ambulan_id,
            NULL::VARCHAR as no_polisi,
            NULL::integer as kelompokpemeriksaanlab_id,
            NULL::character varying AS nama_kelompok,
            NULL::integer AS jenispemeriksaanlab_id,
            NULL::character varying AS jenispemeriksaanlab_nama,
            NULL::integer AS pemeriksaanlab_id,
            NULL::character varying AS pemeriksaanlab_nama,
            COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit 
        FROM tariftindakan_m
        JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
        JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
        JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
        JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
        JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_active = TRUE AND perdatarif_m.is_deleted = FALSE
        JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id AND paketruangan_mp.is_deleted = FALSE 
        JOIN ruangan_m r_paket ON paketruangan_mp.ruangan_id = r_paket.ruangan_id
        JOIN instalasi_m ins_paket ON r_paket.instalasi_id = ins_paket.instalasi_id
        LEFT JOIN   (
            SELECT 
                tariftindakan_m.tipepaket_id,
                tariftindakan_m.daftartindakan_id,
                tariftindakan_m.tariftindakan_id,
                tariftindakan_m.kelaspelayanan_id,
                tariftindakan_m.penjamin_id,
                tariftindakan_m.harga_tariftindakan,
                tariftindakan_m.persencyto_tindakan,
                tariftindakan_m.persendiskon_tindakan,
                tariftindakan_m.perdatarif_id,
                penjamin_m.penjamin_nama,
                tariftindakan_m.komponentarif_id
            FROM tariftindakan_m
            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
            and perdatarif_m.is_active = TRUE
            and perdatarif_m.is_deleted = FALSE
            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
            WHERE tariftindakan_m.is_deleted = FALSE 
            AND tariftindakan_m.is_active = TRUE 
            AND tariftindakan_m.komponentarif_id <> 6
            AND tariftindakan_m.penjamin_id = xpenjamin_id
            AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
        ) tarif_penjamin ON tariftindakan_m.tipepaket_id = tarif_penjamin.tipepaket_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id 
        LEFT JOIN   (
            SELECT 
                tariftindakan_m.tipepaket_id,
                tariftindakan_m.daftartindakan_id,
                tariftindakan_m.tariftindakan_id,
                tariftindakan_m.kelaspelayanan_id,
                tariftindakan_m.penjamin_id,
                tariftindakan_m.harga_tariftindakan,
                tariftindakan_m.persencyto_tindakan,
                tariftindakan_m.persendiskon_tindakan,
                tariftindakan_m.perdatarif_id,
                penjamin_m.penjamin_nama,
                tariftindakan_m.komponentarif_id
            FROM tariftindakan_m
            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
            and perdatarif_m.is_active = TRUE
            and perdatarif_m.is_deleted = FALSE
            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
            WHERE tariftindakan_m.is_deleted = FALSE 
            AND tariftindakan_m.is_active = TRUE 
            AND tariftindakan_m.komponentarif_id <> 6
            AND tariftindakan_m.penjamin_id = vpenjamin_id
            AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
        ) tarif_kelas ON tariftindakan_m.tipepaket_id = tarif_kelas.tipepaket_id AND tariftindakan_m.komponentarif_id = tarif_kelas.komponentarif_id 
        WHERE tariftindakan_m.is_deleted = false 
        AND tariftindakan_m.is_active = true 
        AND tariftindakan_m.komponentarif_id <> 6
        AND tariftindakan_m.penjamin_id = vpenjamin_id
        AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
    ) AS x
    WHERE x.ruangan_id = xruangan_id
    GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;

    ELSEIF(xtipe = \'kamar\')
    THEN
        RETURN QUERY 
        SELECT * FROM (
            SELECT 
                \'kamar\'::text AS jenis,
                COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
                kamarruangan_m.ruangan_id as ruangan_id,
                ruangan_m.ruangan_nama as ruangan_nama,
                ruangan_m.instalasi_id as instalasi_id,
                instalasi_m.instalasi_nama as instalasi_nama,
                NULL::integer AS ruanganpaket_id,
                NULL::character varying AS ruanganpaket_nama,
                COALESCE(tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id,
                perdatarif_m.perdanama_sk as perdanama_sk,
                COALESCE(vkelas_id, tariftindakan_m.kelaspelayanan_id) AS kelaspelayanan_id,
                COALESCE(vkelas_nama, kelaspelayanan_m.kelaspelayanan_nama) AS kelaspelayanan_nama,
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
                COALESCE( tarif_penjamin.harga_tariftindakan,  tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                COALESCE( tarif_penjamin.persencyto_tindakan,  tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                COALESCE( tarif_penjamin.persendiskon_tindakan,  tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
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
                COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit 
            FROM tariftindakan_m
            JOIN kamarruangan_m ON tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
            JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
            JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
            LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
            JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
            JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
            JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
            LEFT JOIN (
                SELECT 
                    tariftindakan_m.daftartindakan_id,
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
                JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
                JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                WHERE tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND tariftindakan_m.tarifparent_id IS NULL 
                AND kamarruangan_m.is_deleted = FALSE 
                AND kamarruangan_m.is_active = TRUE 
                AND perdatarif_m.is_active = true 
                AND tariftindakan_m.komponentarif_id <> 6
                AND tariftindakan_m.penjamin_id = xpenjamin_id
            ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id AND tariftindakan_m.kamarruangan_id = tarif_penjamin.kamarruangan_id AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id
            WHERE tariftindakan_m.is_deleted = FALSE 
            AND tariftindakan_m.is_active = TRUE 
            AND tariftindakan_m.tarifparent_id IS NULL
            AND kamarruangan_m.is_deleted = FALSE 
            AND kamarruangan_m.is_active = TRUE 
            AND perdatarif_m.is_active = TRUE 
            AND tariftindakan_m.komponentarif_id <> 6
            AND tariftindakan_m.penjamin_id = vpenjamin_id
        ) AS x
        WHERE x.ruangan_id = xruangan_id
        AND x.kelaspelayanan_id = xkelaspelayanan_id
        GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;
    
    ELSEIF(xtipe = \'ambulan\')
    THEN
        RETURN QUERY 
        SELECT * FROM (
            SELECT 
                \'ambulan\'::text AS jenis,
                COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id,
                NULL::integer as ruangan_id,
                NULL::character varying as ruangan_nama,
                NULL::integer as instalasi_id,
                NULL::character varying as instalasi_nama,
                NULL::integer AS ruanganpaket_id,
                NULL::character varying AS ruanganpaket_nama,
                tariftindakan_m.perdatarif_id AS perdatarif_id ,
                perdatarif_m.perdanama_sk as perdanama_sk,
                COALESCE(vkelas_id, tariftindakan_m.kelaspelayanan_id) AS kelaspelayanan_id,
                COALESCE(vkelas_nama, kelaspelayanan_m.kelaspelayanan_nama) AS kelaspelayanan_nama,
                COALESCE(tariftindakan_m.penjamin_id, 1)::integer AS penjamin_id ,
                COALESCE(penjamin_m.penjamin_nama, \'Perseorangan\')::character varying AS penjamin_nama,
                daftartindakan_m.kelompoktindakan_id kelompoktindakan_id,
                null::VARCHAR as kelompoktindakan_nama,
                daftartindakan_m.kategoritindakan_id as kategoritindakan_id,
                null::VARCHAR as kategoritindakan_nama,
                ambulandetail_m.daftartindakan_id AS daftartindakan_id ,
                daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                NULL::integer AS tipepaket_id,
                NULL::character varying AS tipepaket_nama,
                COALESCE(tariftindakan_m.komponentarif_id, 6) as komponentarif_id,
                COALESCE(komponentarif_m.komponentarif_nama, \'Total Tarif\')::character varying as komponentarif_nama,
                COALESCE( tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                COALESCE( tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                COALESCE( tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
                ambulandetail_m.is_default AS is_default,
                daftartindakan_m.is_akomodasi as is_akomodasi,
                penjamin_m.carabayar_id as carabayar_id,
                daftartindakan_m.is_konsultasi as is_konsultasi,
                NULL::VARCHAR as  kamarruangan_nokamar,
                tariftindakan_m.kamarruangan_id as kamarruangan_id,
                ambulan_m.ambulan_id as ambulan_id,
                ambulan_m.no_polisi as no_polisi,
                NULL::integer as kelompokpemeriksaanlab_id,
                NULL::character varying AS nama_kelompok,
                NULL::integer AS jenispemeriksaanlab_id,
                NULL::character varying AS jenispemeriksaanlab_nama,
                NULL::integer AS pemeriksaanlab_id,
                NULL::character varying AS pemeriksaanlab_nama,
                COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit 
            FROM ambulan_m
            JOIN ambulandetail_m ON ambulan_m.ambulan_id = ambulandetail_m.ambulan_id
            JOIN daftartindakan_m ON ambulandetail_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
            LEFT JOIN tariftindakan_m ON ambulandetail_m.daftartindakan_id = tariftindakan_m.daftartindakan_id
            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
            LEFT JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
            LEFT JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
            LEFT JOIN (
                SELECT 
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    tariftindakan_m.penjamin_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.perdatarif_id,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id
                FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                WHERE komponentarif_m.is_deleted IS FALSE
                AND perdatarif_m.is_active = TRUE 
                AND tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND tariftindakan_m.tarifparent_id IS NULL
                AND tariftindakan_m.komponentarif_id <> 6
                AND tariftindakan_m.penjamin_id = xpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id 
            LEFT JOIN (
                SELECT 
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    tariftindakan_m.penjamin_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.perdatarif_id,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id
                FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                WHERE komponentarif_m.is_deleted IS FALSE
                AND perdatarif_m.is_active = TRUE 
                AND tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND tariftindakan_m.tarifparent_id IS NULL
                AND tariftindakan_m.komponentarif_id <> 6
                AND tariftindakan_m.penjamin_id = vpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_kelas ON tariftindakan_m.daftartindakan_id = tarif_kelas.daftartindakan_id AND tariftindakan_m.komponentarif_id = tarif_kelas.komponentarif_id 
            WHERE ambulan_m.is_deleted = FALSE 
            AND ambulan_m.is_active = TRUE
            AND ambulandetail_m.is_deleted = FALSE 
            AND ambulandetail_m.is_active = TRUE 
            AND tariftindakan_m.is_deleted = FALSE 
            AND tariftindakan_m.is_active = TRUE 
            AND perdatarif_m.is_active = TRUE 
            AND perdatarif_m.is_deleted = FALSE 
            AND tariftindakan_m.komponentarif_id <> 6 
            AND tariftindakan_m.penjamin_id = vpenjamin_id
            AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
        ) AS x
        GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;

    ELSEIF(xtipe = \'penunjang\')
    THEN
    RETURN QUERY
        SELECT * FROM (
            SELECT 
                \'lab\'::text AS jenis,
                COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                tindakanruangan_mp.ruangan_id as ruangan_id,
                ruangan_m.ruangan_nama as ruangan_nama,
                ruangan_m.instalasi_id as instalasi_id,
                null::character varying as instalasi_nama,
                NULL::integer AS ruanganpaket_id,
                NULL::character varying AS ruanganpaket_nama,
                tariftindakan_m.perdatarif_id AS perdatarif_id ,
                perdatarif_m.perdanama_sk as perdanama_sk,
                COALESCE(vkelas_id, tariftindakan_m.kelaspelayanan_id) AS kelaspelayanan_id,
                COALESCE(vkelas_nama, kelaspelayanan_m.kelaspelayanan_nama) AS kelaspelayanan_nama,
                COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
                COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
                NULL::integer as kelompoktindakan_id,
                NULL::character varying as kelompoktindakan_nama,
                NULL::integer as kategoritindakan_id,
                NULL::character varying kategoritindakan_nama,
                tariftindakan_m.daftartindakan_id AS daftartindakan_id ,
                daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                NULL::integer AS tipepaket_id,
                NULL::character varying AS tipepaket_nama,
                tariftindakan_m.komponentarif_id as komponentarif_id,
                komponentarif_m.komponentarif_nama as komponentarif_nama,
                COALESCE( tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                COALESCE( tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                COALESCE( tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
                tindakanruangan_mp.is_default AS is_default,
                daftartindakan_m.is_akomodasi as is_akomodasi,
                penjamin_m.carabayar_id as carabayar_id,
                daftartindakan_m.is_konsultasi as is_konsultasi,
                NULL::character varying as  kamarruangan_nokamar,
                NULL::integer as kamarruangan_id,
                NULL::integer as ambulan_id,
                NULL::character varying as no_polisi,
                pemeriksaanlab_m.kelompokpemeriksaanlab_id::int4,
                kelompokpemeriksaanlab_m.nama_kelompok::character varying ,
                pemeriksaanlab_m.jenispemeriksaanlab_id::int4,
                jenispemeriksaanlab_m.jenispemeriksaanlab_nama::character varying ,
                pemeriksaanlab_m.pemeriksaanlab_id::int4,
                pemeriksaanlab_m.pemeriksaanlab_nama::character varying ,
                COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit    
            FROM tariftindakan_m
            JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
            JOIN pemeriksaanlab_m ON tariftindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id  AND pemeriksaanlab_m.is_deleted = FALSE
            JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
            JOIN kelompokpemeriksaanlab_m ON pemeriksaanlab_m.kelompokpemeriksaanlab_id = kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id
            JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_deleted = FALSE AND perdatarif_m.is_active = TRUE
            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
            JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id AND tindakanruangan_mp.is_deleted = FALSE
            JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
            LEFT JOIN (
                SELECT 
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    tariftindakan_m.penjamin_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.perdatarif_id,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id
                FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                WHERE komponentarif_m.is_deleted IS FALSE
                AND perdatarif_m.is_active = TRUE 
                AND tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND tariftindakan_m.komponentarif_id <> 6
                AND tariftindakan_m.penjamin_id = xpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id  AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
            LEFT JOIN (
                SELECT 
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    tariftindakan_m.penjamin_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.perdatarif_id,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id
                FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                WHERE komponentarif_m.is_deleted IS FALSE
                AND perdatarif_m.is_active = TRUE 
                AND tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND tariftindakan_m.komponentarif_id <> 6
                AND tariftindakan_m.penjamin_id = vpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_kelas ON tariftindakan_m.daftartindakan_id = tarif_kelas.daftartindakan_id  AND tariftindakan_m.komponentarif_id = tarif_kelas.komponentarif_id
            WHERE  tariftindakan_m.is_deleted = FALSE 
            AND tariftindakan_m.is_active = TRUE 
            AND tariftindakan_m.tarifparent_id IS NULL 
            AND perdatarif_m.is_active = TRUE 
            AND tariftindakan_m.komponentarif_id <> 6
            AND tariftindakan_m.penjamin_id = vpenjamin_id
            AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
            UNION ALL         
            SELECT 
                \'paket_lab\'::text AS jenis,
                COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                paketruangan_mp.ruangan_id as ruangan_id,
                ruangan_m.ruangan_nama as ruangan_nama,
                ruangan_m.instalasi_id as instalasi_id,
                null::character varying AS instalasi_nama,
                NULL::integer AS ruanganpaket_id,
                NULL::character varying AS ruanganpaket_nama,
                tariftindakan_m.perdatarif_id AS perdatarif_id ,
                perdatarif_m.perdanama_sk as perdanama_sk,
                COALESCE(vkelas_id, tariftindakan_m.kelaspelayanan_id) AS kelaspelayanan_id,
                COALESCE(vkelas_nama, kelaspelayanan_m.kelaspelayanan_nama) AS kelaspelayanan_nama,
                COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
                COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
                null::integer as kelompoktindakan_id,
                null::character varying  as kelompoktindakan_nama,
                null::integer as kategoritindakan_id,
                null::character varying  as kategoritindakan_nama,
                tariftindakan_m.daftartindakan_id  AS daftartindakan_id ,
                daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                tariftindakan_m.tipepaket_id  AS tipepaket_id,
                tipepaket_m.tipepaket_nama AS tipepaket_nama,
                tariftindakan_m.komponentarif_id as komponentarif_id,
                komponentarif_m.komponentarif_nama as komponentarif_nama,
                COALESCE( tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                COALESCE( tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                COALESCE( tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
                paketruangan_mp.is_default AS is_default,
                null::boolean as is_akomodasi,
                penjamin_m.carabayar_id as carabayar_id,
                null::boolean as is_konsultasi,
                null::character varying  as  kamarruangan_nokamar,
                null::integer as kamarruangan_id,
                null::integer as ambulan_id,
                null::character varying  as no_polisi,
                kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id as kelompokpemeriksaanlab_id,
                kelompokpemeriksaanlab_m.nama_kelompok AS nama_kelompok,
                pemeriksaanlab_m.jenispemeriksaanlab_id AS jenispemeriksaanlab_id,
                jenispemeriksaanlab_m.jenispemeriksaanlab_nama AS jenispemeriksaanlab_nama,
                pemeriksaanlab_m.pemeriksaanlab_id AS pemeriksaanlab_id,
                pemeriksaanlab_m.pemeriksaanlab_nama AS pemeriksaanlab_nama,
                COALESCE(tariftindakan_m.persen_penyulit, 0::numeric) AS persen_penyulit  
            FROM tariftindakan_m
            JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
            JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
            JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id AND paketruangan_mp.is_deleted = FALSE
            JOIN ruangan_m ON paketruangan_mp.ruangan_id = ruangan_m.ruangan_id
            JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_deleted = FALSE AND perdatarif_m.is_active = TRUE
            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
            JOIN pemeriksaanlab_m ON tariftindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id and pemeriksaanlab_m.is_deleted = FALSE
            LEFT JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
            LEFT JOIN kelompokpemeriksaanlab_m ON pemeriksaanlab_m.kelompokpemeriksaanlab_id = kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id
            LEFT JOIN (
                SELECT 
                    tariftindakan_m.tipepaket_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    tariftindakan_m.penjamin_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.perdatarif_id,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id,
                    pemeriksaanlab_m.kelompokpemeriksaanlab_id,
                    pemeriksaanlab_m.jenispemeriksaanlab_id,
                    pemeriksaanlab_m.pemeriksaanlab_id
                FROM tariftindakan_m
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                AND perdatarif_m.is_deleted = FALSE 
                AND perdatarif_m.is_active = TRUE
                JOIN pemeriksaanlab_m ON tariftindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id 
                AND pemeriksaanlab_m.is_deleted = FALSE
                WHERE tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND perdatarif_m.is_active = TRUE 
                AND tariftindakan_m.komponentarif_id <> 6
                AND tariftindakan_m.penjamin_id = xpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_penjamin ON tariftindakan_m.tipepaket_id = tarif_penjamin.tipepaket_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id AND pemeriksaanlab_m.pemeriksaanlab_id = tarif_penjamin.pemeriksaanlab_id
            LEFT JOIN (
                SELECT 
                    tariftindakan_m.tipepaket_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    tariftindakan_m.penjamin_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.perdatarif_id,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id,
                    pemeriksaanlab_m.kelompokpemeriksaanlab_id,
                    pemeriksaanlab_m.jenispemeriksaanlab_id,
                    pemeriksaanlab_m.pemeriksaanlab_id
                FROM tariftindakan_m
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                AND perdatarif_m.is_deleted = FALSE 
                AND perdatarif_m.is_active = TRUE
                JOIN pemeriksaanlab_m ON tariftindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id 
                AND pemeriksaanlab_m.is_deleted = FALSE
                WHERE tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND perdatarif_m.is_active = TRUE 
                AND tariftindakan_m.komponentarif_id <> 6
                AND tariftindakan_m.penjamin_id = vpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_kelas ON tariftindakan_m.tipepaket_id = tarif_kelas.tipepaket_id AND tariftindakan_m.komponentarif_id = tarif_kelas.komponentarif_id AND pemeriksaanlab_m.pemeriksaanlab_id = tarif_kelas.pemeriksaanlab_id
            WHERE tariftindakan_m.is_deleted = FALSE 
            AND tariftindakan_m.is_active = TRUE 
            AND perdatarif_m.is_active = TRUE 
            AND tariftindakan_m.komponentarif_id <> 6 
            AND tariftindakan_m.penjamin_id = vpenjamin_id 
            AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
            UNION ALL
            SELECT  \'rad\'::text AS jenis,
                COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                tindakanruangan_mp.ruangan_id as ruangan_id,
                ruangan_m.ruangan_nama as ruangan_nama,
                ruangan_m.instalasi_id as instalasi_id,
                NULL::character varying as instalasi_nama,
                NULL::integer AS ruanganpaket_id,
                NULL::character varying AS ruanganpaket_nama,
                tariftindakan_m.perdatarif_id AS perdatarif_id ,
                perdatarif_m.perdanama_sk as perdanama_sk,
                COALESCE(vkelas_id, tariftindakan_m.kelaspelayanan_id) AS kelaspelayanan_id,
                COALESCE(vkelas_nama, kelaspelayanan_m.kelaspelayanan_nama) AS kelaspelayanan_nama,
                COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
                COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
                NULL::integer as kelompoktindakan_id,
                NULL::character varying as kelompoktindakan_nama,
                NULL::integer as kategoritindakan_id,
                NULL::character varying as kategoritindakan_nama,
                tariftindakan_m.daftartindakan_id AS daftartindakan_id ,
                daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                NULL::integer AS tipepaket_id,
                NULL::character varying AS tipepaket_nama,
                tariftindakan_m.komponentarif_id as komponentarif_id,
                komponentarif_m.komponentarif_nama as komponentarif_nama,
                COALESCE( tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                COALESCE( tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                COALESCE( tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
                tindakanruangan_mp.is_default AS is_default,
                daftartindakan_m.is_akomodasi as is_akomodasi,
                penjamin_m.carabayar_id as carabayar_id,
                daftartindakan_m.is_konsultasi as is_konsultasi,
                NULL::character varying as  kamarruangan_nokamar,
                NULL::integer as kamarruangan_id,
                NULL::integer as ambulan_id,
                NULL::character varying as no_polisi,
                pemeriksaanrad_m.kelompokpemeriksaanrad_id as kelompokpemeriksaanlab_id,
                kelompokpemeriksaanrad_m.nama_kelompok as nama_kelompok,
                pemeriksaanrad_m.jenispemeriksaanrad_id as jenispemeriksaanlab_id,
                jenispemeriksaanrad_m.jenispemeriksaanrad_nama as jenispemeriksaanlab_nama,
                pemeriksaanrad_m.pemeriksaanradiologi_id as pemeriksaanlab_id,
                pemeriksaanrad_m.pemeriksaanrad_nama  as pemeriksaanlab_nama,
                COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit    
            FROM tariftindakan_m
            JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
            JOIN pemeriksaanrad_m ON tariftindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id AND pemeriksaanrad_m.is_deleted = FALSE
            JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
            JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id
            JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_deleted = FALSE AND perdatarif_m.is_active = TRUE
            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
            JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id AND tindakanruangan_mp.is_deleted = FALSE
            JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
            LEFT JOIN (
                SELECT 
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    tariftindakan_m.penjamin_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.perdatarif_id,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id
                FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                WHERE komponentarif_m.is_deleted IS FALSE
                AND perdatarif_m.is_active = TRUE 
                AND tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND tariftindakan_m.tarifparent_id IS NULL
                AND tariftindakan_m.komponentarif_id <> 6
                AND tariftindakan_m.penjamin_id = xpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
            LEFT JOIN (
                SELECT 
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    tariftindakan_m.penjamin_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.perdatarif_id,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id
                FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                WHERE komponentarif_m.is_deleted IS FALSE
                AND perdatarif_m.is_active = TRUE 
                AND tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND tariftindakan_m.tarifparent_id IS NULL
                AND tariftindakan_m.komponentarif_id <> 6
                AND tariftindakan_m.penjamin_id = vpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_kelas ON tariftindakan_m.daftartindakan_id = tarif_kelas.daftartindakan_id AND tariftindakan_m.komponentarif_id = tarif_kelas.komponentarif_id
            WHERE tariftindakan_m.is_deleted = FALSE 
            AND tariftindakan_m.is_active = TRUE 
            AND perdatarif_m.is_active = TRUE 
            AND tariftindakan_m.komponentarif_id <> 6
            AND tariftindakan_m.penjamin_id = vpenjamin_id
            AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
            UNION ALL
            SELECT  
                \'paket_rad\'::text AS jenis,
                COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                paketruangan_mp.ruangan_id as ruangan_id,
                ruangan_m.ruangan_nama as ruangan_nama,
                ruangan_m.instalasi_id as instalasi_id,
                null::VARCHAR as instalasi_nama,
                NULL::INTEGER AS ruanganpaket_id,
                NULL::character varying AS ruanganpaket_nama,
                tariftindakan_m.perdatarif_id AS perdatarif_id ,
                perdatarif_m.perdanama_sk as perdanama_sk,
                COALESCE(vkelas_id, tariftindakan_m.kelaspelayanan_id) AS kelaspelayanan_id,
                COALESCE(vkelas_nama, kelaspelayanan_m.kelaspelayanan_nama) AS kelaspelayanan_nama,
                COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
                COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
                null::INTEGER as kelompoktindakan_id,
                null::VARCHAR as kelompoktindakan_nama,
                null::INTEGER as kategoritindakan_id,
                null::VARCHAR as kategoritindakan_nama,
                tariftindakan_m.daftartindakan_id  AS daftartindakan_id ,
                tipepaket_m.tipepaket_nama as daftartindakan_nama,
                tariftindakan_m.tipepaket_id  AS tipepaket_id,
                tipepaket_m.tipepaket_nama AS tipepaket_nama,
                tariftindakan_m.komponentarif_id as komponentarif_id,
                komponentarif_m.komponentarif_nama as komponentarif_nama,
                COALESCE( tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                COALESCE( tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                COALESCE( tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
                paketruangan_mp.is_default AS is_default,
                null::BOOLEAN as is_akomodasi,
                penjamin_m.carabayar_id as carabayar_id,
                null::BOOLEAN as is_konsultasi,
                null::VARCHAR as  kamarruangan_nokamar,
                null::INTEGER as kamarruangan_id,
                null::INTEGER as ambulan_id,
                null::VARCHAR as no_polisi,
                kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id as kelompokpemeriksaanlab_id,
                kelompokpemeriksaanrad_m.nama_kelompok AS nama_kelompok,
                pemeriksaanrad_m.jenispemeriksaanrad_id AS jenispemeriksaanlab_id,
                jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS jenispemeriksaanlab_nama,
                pemeriksaanrad_m.pemeriksaanradiologi_id AS pemeriksaanlab_id,
                pemeriksaanrad_m.pemeriksaanrad_nama AS pemeriksaanlab_nama,
                COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit      
            FROM tariftindakan_m
            JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
            JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
            JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id AND paketruangan_mp.is_deleted = FALSE
            JOIN ruangan_m ON paketruangan_mp.ruangan_id = ruangan_m.ruangan_id
            JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_deleted = FALSE AND perdatarif_m.is_active = TRUE
            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
            JOIN pemeriksaanrad_m ON tariftindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id 
            AND pemeriksaanrad_m.is_deleted = FALSE
            LEFT JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
            LEFT JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id   
            LEFT JOIN (
                SELECT 
                    tariftindakan_m.tipepaket_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    tariftindakan_m.penjamin_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.perdatarif_id,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id,
                    pemeriksaanrad_m.kelompokpemeriksaanrad_id,
                    pemeriksaanrad_m.jenispemeriksaanrad_id,
                    pemeriksaanrad_m.pemeriksaanradiologi_id
                FROM tariftindakan_m
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                AND perdatarif_m.is_deleted = FALSE 
                AND perdatarif_m.is_active = TRUE
                JOIN pemeriksaanrad_m ON tariftindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id 
                AND pemeriksaanrad_m.is_deleted = FALSE
                WHERE tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND perdatarif_m.is_active = TRUE 
                AND tariftindakan_m.komponentarif_id <> 6 
                AND tariftindakan_m.penjamin_id = xpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_penjamin ON tariftindakan_m.tipepaket_id = tarif_penjamin.tipepaket_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id AND pemeriksaanrad_m.pemeriksaanradiologi_id = tarif_penjamin.pemeriksaanradiologi_id
            LEFT JOIN (
                SELECT 
                    tariftindakan_m.tipepaket_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    tariftindakan_m.penjamin_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.perdatarif_id,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id,
                    pemeriksaanrad_m.kelompokpemeriksaanrad_id,
                    pemeriksaanrad_m.jenispemeriksaanrad_id,
                    pemeriksaanrad_m.pemeriksaanradiologi_id
                FROM tariftindakan_m
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                AND perdatarif_m.is_deleted = FALSE 
                AND perdatarif_m.is_active = TRUE
                JOIN pemeriksaanrad_m ON tariftindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id 
                AND pemeriksaanrad_m.is_deleted = FALSE
                WHERE tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND perdatarif_m.is_active = TRUE 
                AND tariftindakan_m.komponentarif_id <> 6 
                AND tariftindakan_m.penjamin_id = vpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_kelas ON tariftindakan_m.tipepaket_id = tarif_kelas.tipepaket_id AND tariftindakan_m.komponentarif_id = tarif_kelas.komponentarif_id AND pemeriksaanrad_m.pemeriksaanradiologi_id = tarif_kelas.pemeriksaanradiologi_id
            WHERE tariftindakan_m.is_deleted = FALSE 
            AND tariftindakan_m.is_active = TRUE 
            AND perdatarif_m.is_active = TRUE 
            AND tariftindakan_m.komponentarif_id <> 6 
            AND tariftindakan_m.penjamin_id = vpenjamin_id        
            AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id  
            UNION ALL
            SELECT 
                \'operasi\'::text AS jenis,
                COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                tindakanruangan_mp.ruangan_id as ruangan_id,
                ruangan_m.ruangan_nama as ruangan_nama,
                ruangan_m.instalasi_id as instalasi_id,
                NULL::character varying instalasi_nama,
                NULL::integer AS ruanganpaket_id,
                NULL::character varying AS ruanganpaket_nama,
                tariftindakan_m.perdatarif_id AS perdatarif_id ,
                perdatarif_m.perdanama_sk as perdanama_sk,
                COALESCE(vkelas_id, tariftindakan_m.kelaspelayanan_id) AS kelaspelayanan_id,
                COALESCE(vkelas_nama, kelaspelayanan_m.kelaspelayanan_nama) AS kelaspelayanan_nama,
                COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
                COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
                null::integer as kelompoktindakan_id,
                NULL::character varying as kelompoktindakan_nama,
                null::integer as kategoritindakan_id,
                NULL::character varying as kategoritindakan_nama,
                tariftindakan_m.daftartindakan_id AS daftartindakan_id ,
                daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                NULL::integer AS tipepaket_id,
                NULL::character varying AS tipepaket_nama,
                tariftindakan_m.komponentarif_id as komponentarif_id,
                komponentarif_m.komponentarif_nama as komponentarif_nama,
                COALESCE( tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                COALESCE( tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                COALESCE( tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
                tindakanruangan_mp.is_default AS is_default,
                daftartindakan_m.is_akomodasi as is_akomodasi,
                penjamin_m.carabayar_id as carabayar_id,
                daftartindakan_m.is_konsultasi as is_konsultasi,
                NULL::character varying as  kamarruangan_nokamar,
                null::integer as kamarruangan_id,
                null::integer as ambulan_id,
                NULL::character varying as no_polisi,
                operasi_m.golonganoperasi_id as kelompokpemeriksaanlab_id,
                golonganoperasi_m.golonganoperasi_nama as nama_kelompok,
                operasi_m.kegiatanoperasi_id as jenispemeriksaanlab_id,
                kegiatanoperasi_m.kegiatanoperasi_nama as jenispemeriksaanlab_nama,
                operasi_m.operasi_id as pemeriksaanlab_id,
                operasi_m.operasi_nama  as pemeriksaanlab_nama,
                COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit     
            FROM tariftindakan_m
            JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
            JOIN operasi_m ON tariftindakan_m.daftartindakan_id = operasi_m.daftartindakan_id  AND operasi_m.is_deleted = FALSE
            JOIN golonganoperasi_m ON operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id
            JOIN kegiatanoperasi_m ON operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id
            JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_deleted = FALSE AND perdatarif_m.is_active = TRUE
            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
            JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id AND tindakanruangan_mp.is_deleted = FALSE
            JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
            LEFT JOIN (
                SELECT 
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    tariftindakan_m.penjamin_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.perdatarif_id,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id
                FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                WHERE komponentarif_m.is_deleted IS FALSE
                AND perdatarif_m.is_active = TRUE 
                AND tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND tariftindakan_m.tarifparent_id IS NULL
                AND tariftindakan_m.komponentarif_id <> 6
                AND tariftindakan_m.penjamin_id = xpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
            LEFT JOIN (
                SELECT 
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    tariftindakan_m.penjamin_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.perdatarif_id,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id
                FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                WHERE komponentarif_m.is_deleted IS FALSE
                AND perdatarif_m.is_active = TRUE 
                AND tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND tariftindakan_m.tarifparent_id IS NULL
                AND tariftindakan_m.komponentarif_id <> 6
                AND tariftindakan_m.penjamin_id = vpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_kelas ON tariftindakan_m.daftartindakan_id = tarif_kelas.daftartindakan_id AND tariftindakan_m.komponentarif_id = tarif_kelas.komponentarif_id
            WHERE tariftindakan_m.is_deleted = FALSE 
            AND tariftindakan_m.is_active = TRUE 
            AND perdatarif_m.is_active = TRUE 
            AND tariftindakan_m.komponentarif_id <> 6
            AND tariftindakan_m.penjamin_id = vpenjamin_id
            AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
            UNION ALL
            SELECT  
                \'paket_operasi\'::text AS jenis,
                COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                paketruangan_mp.ruangan_id as ruangan_id,
                ruangan_m.ruangan_nama as ruangan_nama,
                ruangan_m.instalasi_id as instalasi_id,
                null::VARCHAR as instalasi_nama,
                NULL::integer AS ruanganpaket_id,
                NULL::character varying AS ruanganpaket_nama,
                tariftindakan_m.perdatarif_id AS perdatarif_id ,
                perdatarif_m.perdanama_sk as perdanama_sk,
                COALESCE(vkelas_id, tariftindakan_m.kelaspelayanan_id) AS kelaspelayanan_id,
                COALESCE(vkelas_nama, kelaspelayanan_m.kelaspelayanan_nama) AS kelaspelayanan_nama,
                COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
                COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
                null::INTEGER as kelompoktindakan_id,
                null::VARCHAR as kelompoktindakan_nama,
                null::INTEGER as kategoritindakan_id,
                null::VARCHAR as kategoritindakan_nama,
                null::INTEGER AS daftartindakan_id ,
                null::VARCHAR as daftartindakan_nama,
                tariftindakan_m.daftartindakan_id  AS tipepaket_id,
                tipepaket_m.tipepaket_nama AS tipepaket_nama,
                tariftindakan_m.komponentarif_id as komponentarif_id,
                komponentarif_m.komponentarif_nama as komponentarif_nama,
                COALESCE( tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                COALESCE( tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                COALESCE( tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
                paketruangan_mp.is_default AS is_default,
                null::BOOLEAN as is_akomodasi,
                penjamin_m.carabayar_id as carabayar_id,
                null::BOOLEAN as is_konsultasi,
                null::VARCHAR as  kamarruangan_nokamar,
                null::INTEGER as kamarruangan_id,
                null::INTEGER as ambulan_id,
                null::VARCHAR as no_polisi,
                operasi_m.golonganoperasi_id as kelompokpemeriksaanlab_id,
                golonganoperasi_m.golonganoperasi_nama AS nama_kelompok,
                operasi_m.kegiatanoperasi_id AS jenispemeriksaanlab_id,
                kegiatanoperasi_m.kegiatanoperasi_nama AS jenispemeriksaanlab_nama,
                operasi_m.operasi_id AS pemeriksaanlab_id,
                operasi_m.operasi_nama AS pemeriksaanlab_nama,
                COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit 
            FROM tariftindakan_m
            JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
            JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
            JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id AND paketruangan_mp.is_deleted = FALSE
            JOIN ruangan_m ON paketruangan_mp.ruangan_id = ruangan_m.ruangan_id
            JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_deleted = FALSE AND perdatarif_m.is_active = TRUE
            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
            JOIN operasi_m ON tariftindakan_m.daftartindakan_id = operasi_m.daftartindakan_id AND operasi_m.is_deleted = FALSE
            LEFT JOIN kegiatanoperasi_m ON operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id
            LEFT JOIN golonganoperasi_m ON operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id          
            LEFT JOIN (
                SELECT 
                    tariftindakan_m.tipepaket_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    tariftindakan_m.penjamin_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.perdatarif_id,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id,
                    operasi_m.golonganoperasi_id,
                    operasi_m.kegiatanoperasi_id,
                    operasi_m.operasi_id
                FROM tariftindakan_m
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                AND perdatarif_m.is_deleted = FALSE 
                AND perdatarif_m.is_active = TRUE
                JOIN operasi_m ON tariftindakan_m.daftartindakan_id = operasi_m.daftartindakan_id 
                AND operasi_m.is_deleted = FALSE
                WHERE   tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND perdatarif_m.is_active = TRUE 
                AND tariftindakan_m.komponentarif_id <> 6
                AND tariftindakan_m.penjamin_id = xpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_penjamin ON tariftindakan_m.tipepaket_id = tarif_penjamin.tipepaket_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id AND operasi_m.operasi_id = tarif_penjamin.operasi_id
            LEFT JOIN (
                SELECT 
                    tariftindakan_m.tipepaket_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    tariftindakan_m.penjamin_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.perdatarif_id,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id,
                    operasi_m.golonganoperasi_id,
                    operasi_m.kegiatanoperasi_id,
                    operasi_m.operasi_id
                FROM tariftindakan_m
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                AND perdatarif_m.is_deleted = FALSE 
                AND perdatarif_m.is_active = TRUE
                JOIN operasi_m ON tariftindakan_m.daftartindakan_id = operasi_m.daftartindakan_id 
                AND operasi_m.is_deleted = FALSE
                WHERE   tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND perdatarif_m.is_active = TRUE 
                AND tariftindakan_m.komponentarif_id <> 6
                AND tariftindakan_m.penjamin_id = vpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_kelas ON tariftindakan_m.tipepaket_id = tarif_kelas.tipepaket_id AND tariftindakan_m.komponentarif_id = tarif_kelas.komponentarif_id AND operasi_m.operasi_id = tarif_kelas.operasi_id
            WHERE tariftindakan_m.is_deleted = FALSE 
            AND tariftindakan_m.is_active = TRUE 
            AND perdatarif_m.is_active = TRUE 
            AND tariftindakan_m.komponentarif_id <> 6 
            AND tariftindakan_m.penjamin_id = vpenjamin_id 
            AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
        ) x
        WHERE x.ruangan_id = xruangan_id
        GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;
    ELSEIF(xtipe = \'makanan\')
    THEN
        RETURN QUERY 
        SELECT *FROM (
            SELECT
                \'makanan\'::text AS jenis,
                COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                tindakanruangan_mp.ruangan_id as ruangan_id,
                ruangan_m.ruangan_nama as ruangan_nama,
                ruangan_m.instalasi_id as instalasi_id,
                null::character varying as instalasi_nama,
                NULL::integer AS ruanganpaket_id,
                NULL::character varying AS ruanganpaket_nama,
                tariftindakan_m.perdatarif_id AS perdatarif_id ,
                perdatarif_m.perdanama_sk as perdanama_sk,
                COALESCE(vkelas_id, tariftindakan_m.kelaspelayanan_id) AS kelaspelayanan_id,
                COALESCE(vkelas_nama, kelaspelayanan_m.kelaspelayanan_nama) AS kelaspelayanan_nama,
                COALESCE(tarif_penjamin.penjamin_id, tariftindakan_m.penjamin_id ) AS penjamin_id ,
                COALESCE(tarif_penjamin.penjamin_nama, penjamin_m.penjamin_nama ) AS penjamin_nama,
                NULL::integer as kelompoktindakan_id,
                NULL::character varying as kelompoktindakan_nama,
                NULL::integer as kategoritindakan_id,
                NULL::character varying kategoritindakan_nama,
                tariftindakan_m.daftartindakan_id AS daftartindakan_id ,
                daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                NULL::integer AS tipepaket_id,
                NULL::character varying AS tipepaket_nama,
                tariftindakan_m.komponentarif_id as komponentarif_id,
                komponentarif_m.komponentarif_nama as komponentarif_nama,
                COALESCE( tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                COALESCE( tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                COALESCE( tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
                tindakanruangan_mp.is_default AS is_default,
                daftartindakan_m.is_akomodasi as is_akomodasi,
                penjamin_m.carabayar_id as carabayar_id,
                daftartindakan_m.is_konsultasi as is_konsultasi,
                NULL::character varying as  kamarruangan_nokamar,
                NULL::integer as kamarruangan_id,
                NULL::integer as ambulan_id,
                NULL::character varying as no_polisi,
                makanandiet_m.jenisdiet_id::int4 AS kelompokpemeriksaanlab_id,
                jenisdiet_m.jenisdiet_nama::character varying nama_kelompok ,
                makanandiet_m.jenisdiet_id::int4 AS jenispemeriksaanlab_id,
                jenisdiet_m.jenisdiet_nama::character varying AS jenispemeriksaanlab_nama ,
                makanandiet_m.makanandiet_id::int4 AS pemeriksaanlab_id,
                makanandiet_m.makanandiet_nama::character varying AS pemeriksaanlab_nama ,
                COALESCE(tariftindakan_m.persen_penyulit, (0)::numeric) AS persen_penyulit     
            FROM tariftindakan_m
            JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
            JOIN makanandiet_m ON tariftindakan_m.daftartindakan_id = makanandiet_m.daftartindakan_id AND makanandiet_m.is_deleted = FALSE
            JOIN jenisdiet_m ON makanandiet_m.jenisdiet_id = jenisdiet_m.jenisdiet_id
            JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id AND perdatarif_m.is_deleted = FALSE AND perdatarif_m.is_active = TRUE
            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
            JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id AND tindakanruangan_mp.is_deleted = FALSE
            JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
            LEFT JOIN (
                SELECT 
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    tariftindakan_m.penjamin_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.perdatarif_id,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id
                FROM tariftindakan_m
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                AND perdatarif_m.is_active = TRUE 
                AND perdatarif_m.is_deleted = FALSE
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                WHERE tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND tariftindakan_m.tarifparent_id IS NULL
                AND tariftindakan_m.komponentarif_id <> 6
                AND tariftindakan_m.penjamin_id = xpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
            LEFT JOIN (
                SELECT 
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    tariftindakan_m.penjamin_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.perdatarif_id,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id
                FROM tariftindakan_m
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                AND perdatarif_m.is_active = TRUE 
                AND perdatarif_m.is_deleted = FALSE
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                WHERE tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND tariftindakan_m.tarifparent_id IS NULL
                AND tariftindakan_m.komponentarif_id <> 6
                AND tariftindakan_m.penjamin_id = vpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_kelas ON tariftindakan_m.daftartindakan_id = tarif_kelas.daftartindakan_id AND tariftindakan_m.komponentarif_id = tarif_kelas.komponentarif_id
            WHERE tariftindakan_m.is_deleted = FALSE 
            AND tariftindakan_m.is_active = TRUE 
            AND tariftindakan_m.tarifparent_id IS NULL 
            AND tariftindakan_m.komponentarif_id <> 6
            AND tariftindakan_m.penjamin_id = vpenjamin_id
            AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
        ) AS x
        WHERE x.ruangan_id = xruangan_id
        GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;
    END IF;
END; 
$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;');

        $this->execute('ALTER FUNCTION "public"."tarifkomponenrs_fn"("xruangan_id" int4, "xpenjamin_id" int4, "xkelaspelayanan_id" int4, "xtipe" varchar) OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210329_051851_migrate_20210329_function_tarif cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210329_051851_migrate_20210329_function_tarif cannot be reverted.\n";

        return false;
    }
    */
}
