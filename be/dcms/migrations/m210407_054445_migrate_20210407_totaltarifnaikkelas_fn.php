<?php

use yii\db\Migration;

/**
 * Class m210407_054445_migrate_20210407_totaltarifnaikkelas_fn
 */
class m210407_054445_migrate_20210407_totaltarifnaikkelas_fn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
$this->execute('DROP FUNCTION if exists "public"."totaltarifnaikkelas_fn"("xpenjamin_id" int4, "xkelaspelayanan_id" int4, "xtipe" varchar);');

$this->execute("
    CREATE OR REPLACE FUNCTION \"public\".\"totaltarifnaikkelas_fn\"(\"xpenjamin_id\" int4=0, \"xkelaspelayanan_id\" int4=0, \"xtipe\" varchar='pelayanan'::character varying)
  RETURNS TABLE(jenis text, tariftindakan_id int4, ruangan_id int4, ruangan_nama varchar, instalasi_id int4, instalasi_nama varchar, ruanganpaket_id int4, ruanganpaket_nama varchar, perdatarif_id int4, perdanama_sk varchar, kelaspelayanan_id int4, kelaspelayanan_nama varchar, penjamin_id int4, penjamin_nama varchar, kelompoktindakan_id int4, kelompoktindakan_nama varchar, kategoritindakan_id int4, kategoritindakan_nama varchar, daftartindakan_id int4, daftartindakan_nama varchar, tipepaket_id int4, tipepaket_nama varchar, komponentarif_id int4, komponentarif_nama varchar, harga_tariftindakan numeric, persencyto_tindakan numeric, persendiskon_tindakan numeric, is_default bool, is_akomodasi bool, carabayar_id int4, is_konsultasi bool, kamarruangan_nokamar varchar, kamarruangan_id int4, ambulan_id int4, no_polisi varchar, persen_penyulit numeric) AS \$BODY\$ 
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
    
    IF(xtipe = 'pelayanan')
    THEN
        RETURN QUERY 
        SELECT *FROM (
            SELECT 
                'tindakan'::text AS jenis,
                COALESCE( tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
                NULL::integer AS ruangan_id,
                NULL::character varying AS ruangan_nama,
                NULL::integer AS instalasi_id,
                NULL::character varying AS instalasi_nama,
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
                NULL::BOOL is_default,
                daftartindakan_m.is_akomodasi,
                penjamin_m.carabayar_id,
                daftartindakan_m.is_konsultasi,
                NULL::VARCHAR as  kamarruangan_nokamar,
                NULL::int4 as kamarruangan_id,
                NULL::int4 as ambulan_id,
                NULL::VARCHAR as no_polisi,
                COALESCE( tarif_penjamin.persen_penyulit, tarif_kelas.persen_penyulit, tariftindakan_m.persen_penyulit) AS persen_penyulit
            FROM tariftindakan_m
            JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
            JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
            JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
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
                    tariftindakan_m.komponentarif_id,
                    tariftindakan_m.persen_penyulit
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
                    tariftindakan_m.komponentarif_id,
                    tariftindakan_m.persen_penyulit
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
            WHERE komponentarif_m.is_deleted IS FALSE
            AND perdatarif_m.is_active = true 
            AND tariftindakan_m.is_deleted = false 
            AND tariftindakan_m.is_active = true 
            AND tariftindakan_m.tarifparent_id IS NULL
            AND tariftindakan_m.komponentarif_id = 6
            AND tariftindakan_m.penjamin_id = vpenjamin_id
            AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
            UNION ALL
            SELECT 
                'paket'::text AS jenis,
                COALESCE( tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
                NULL::integer AS ruangan_id,
                NULL::character varying AS ruangan_nama,
                NULL::integer AS instalasi_id,
                NULL::character varying AS instalasi_nama,
                NULL::integer AS ruanganpaket_id,
                NULL::character varying ruanganpaket_nama,
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
                NULL::BOOL is_default,
                NULL::boolean AS is_akomodasi,
                penjamin_m.carabayar_id,
                false AS is_konsultasi,
                NULL::VARCHAR as  kamarruangan_nokamar,
                NULL::int4 as kamarruangan_id,
                NULL::int4 as ambulan_id,
                NULL::VARCHAR as no_polisi,
                COALESCE( tarif_penjamin.persen_penyulit, tarif_kelas.persen_penyulit, tariftindakan_m.persen_penyulit) AS persen_penyulit
            FROM tariftindakan_m
            JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
            JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
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
                    tariftindakan_m.persen_penyulit
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
                    tariftindakan_m.komponentarif_id,
                    tariftindakan_m.persen_penyulit
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
            WHERE komponentarif_m.is_deleted IS FALSE   
            AND perdatarif_m.is_active = true 
            AND tariftindakan_m.is_deleted = false 
            AND tariftindakan_m.is_active = true 
            AND tariftindakan_m.tarifparent_id IS NULL
            AND tariftindakan_m.komponentarif_id = 6
            AND tariftindakan_m.penjamin_id = vpenjamin_id
            AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
            UNION ALL
            SELECT 
                'paket'::text AS jenis,
                COALESCE( tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
                NULL::integer AS ruangan_id,
                NULL::character varying AS ruangan_nama,
                NULL::integer AS instalasi_id,
                NULL::character varying AS instalasi_nama,
                NULL::integer AS ruanganpaket_id,
                NULL::character varying ruanganpaket_nama,
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
                NULL::BOOL is_default,
                NULL::boolean AS is_akomodasi,
                penjamin_m.carabayar_id,
                false AS is_konsultasi,
                NULL::VARCHAR as  kamarruangan_nokamar,
                NULL::int4 as kamarruangan_id,
                NULL::int4 as ambulan_id,
                NULL::VARCHAR as no_polisi,
                COALESCE( tarif_penjamin.persen_penyulit, tarif_kelas.persen_penyulit, tariftindakan_m.persen_penyulit) AS persen_penyulit
            FROM tariftindakan_m
            JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
            JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
            JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
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
                    tariftindakan_m.persen_penyulit
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
                    tariftindakan_m.komponentarif_id,
                    tariftindakan_m.persen_penyulit
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
            WHERE komponentarif_m.is_deleted IS FALSE
            AND perdatarif_m.is_active = true 
            AND tariftindakan_m.is_deleted = false 
            AND tariftindakan_m.is_active = true 
            AND tariftindakan_m.tarifparent_id IS NOT NULL
            AND tariftindakan_m.komponentarif_id = 6
            AND tariftindakan_m.penjamin_id = vpenjamin_id
            AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
        ) AS x
        GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi, x.persen_penyulit;
    ELSEIF(xtipe = 'kamar')
    THEN
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
                COALESCE(tarif_penjamin.persen_penyulit, tariftindakan_m.persen_penyulit) AS persen_penyulit
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
                    tariftindakan_m.kamarruangan_id,
                    tariftindakan_m.persen_penyulit
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
                AND tariftindakan_m.penjamin_id = xpenjamin_id
            ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id  AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id AND tariftindakan_m.kamarruangan_id = tarif_penjamin.kamarruangan_id AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id
            WHERE 
            tariftindakan_m.is_deleted = false AND
            tariftindakan_m.is_active = true AND
            tariftindakan_m.tarifparent_id IS NULL AND
            kamarruangan_m.is_deleted = FALSE AND
            kamarruangan_m.is_active = TRUE AND
            perdatarif_m.is_active = true AND
            tariftindakan_m.komponentarif_id = 6
            AND tariftindakan_m.penjamin_id = vpenjamin_id
        ) AS x
        WHERE x.kelaspelayanan_id = xkelaspelayanan_id
        GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi, x.persen_penyulit;
    ELSE
        RETURN QUERY 
        SELECT *FROM (
            SELECT 
                'ambulan'::text AS jenis,
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
                COALESCE(penjamin_m.penjamin_nama, 'Perseorangan')::character varying AS penjamin_nama,
                daftartindakan_m.kelompoktindakan_id kelompoktindakan_id,
                null::VARCHAR as kelompoktindakan_nama,
                daftartindakan_m.kategoritindakan_id as kategoritindakan_id,
                null::VARCHAR as kategoritindakan_nama,
                ambulandetail_m.daftartindakan_id AS daftartindakan_id ,
                daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                NULL::integer AS tipepaket_id,
                NULL::character varying AS tipepaket_nama,
                COALESCE(tariftindakan_m.komponentarif_id, 6) as komponentarif_id,
                COALESCE(komponentarif_m.komponentarif_nama, 'Total Tarif')::character varying as komponentarif_nama,
                COALESCE(tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan , COALESCE((tariftindakan_m.harga_tariftindakan), 0)) AS harga_tariftindakan,
                COALESCE(tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan , COALESCE((tariftindakan_m.persencyto_tindakan), 0)) AS persencyto_tindakan,
                COALESCE(tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan , COALESCE((tariftindakan_m.persendiskon_tindakan), 0)) AS persendiskon_tindakan,
                ambulandetail_m.is_default AS is_default,
                daftartindakan_m.is_akomodasi as is_akomodasi,
                penjamin_m.carabayar_id as carabayar_id,
                daftartindakan_m.is_konsultasi as is_konsultasi,
                NULL::VARCHAR as  kamarruangan_nokamar,
                tariftindakan_m.kamarruangan_id as kamarruangan_id,
                ambulan_m.ambulan_id as ambulan_id,
                ambulan_m.no_polisi as no_polisi,
                COALESCE(tarif_penjamin.persen_penyulit, tarif_kelas.persen_penyulit , COALESCE((tariftindakan_m.persen_penyulit), 0)) AS persen_penyulit
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
                    tariftindakan_m.komponentarif_id,
                    tariftindakan_m.persen_penyulit
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
            tariftindakan_m.komponentarif_id = 6 
            AND tariftindakan_m.penjamin_id = vpenjamin_id
            AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
        ) AS x
        GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi,x.persen_penyulit;
    END IF;
END; 
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;");

$this->execute('ALTER FUNCTION "public"."totaltarifnaikkelas_fn"("xpenjamin_id" int4, "xkelaspelayanan_id" int4, "xtipe" varchar) OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210407_054445_migrate_20210407_totaltarifnaikkelas_fn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210407_054445_migrate_20210407_totaltarifnaikkelas_fn cannot be reverted.\n";

        return false;
    }
    */
}
