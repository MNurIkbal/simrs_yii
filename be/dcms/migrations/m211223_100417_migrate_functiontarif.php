<?php

use yii\db\Migration;

/**
 * Class m211223_100417_migrate_functiontarif
 */
class m211223_100417_migrate_functiontarif extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP FUNCTION if exists "public"."totaltarifnaikkelas_fn";');

        $this->execute("
            CREATE OR REPLACE FUNCTION public.totaltarifnaikkelas_fn(xpenjamin_id int4=0, xkelaspelayanan_id int4=0, xtipe varchar='pelayanan'::character varying)
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

--  IF(xkelaspelayanan_id = vkelaspelayanan_id)
--  THEN
--      xkelaspelayanan_id := 0;
--  END IF;
    
    IF(xtipe = 'pelayanan')
    THEN
        RETURN QUERY 
        SELECT *FROM (
            SELECT 
            'tindakan'::text AS jenis,
             COALESCE( tarif_normal.tariftindakan_id, tarif_penjamin.tariftindakan_id, tarif_kelas.tariftindakan_id, tarif_konfig.tariftindakan_id) AS tariftindakan_id ,
            NULL::INT as ruangan_id,
            NULL::VARCHAR as ruangan_nama,
            NULL::INT as instalasi_id,
            NULL::VARCHAR as instalasi_nama,
            NULL::integer AS ruanganpaket_id,
            NULL::character varying AS ruanganpaket_nama,
            COALESCE(tarif_normal.perdatarif_id, tarif_penjamin.perdatarif_id, tarif_kelas.perdatarif_id,tarif_konfig.perdatarif_id) AS perdatarif_id ,
            COALESCE(tarif_normal.perdanama_sk, tarif_penjamin.perdanama_sk, tarif_kelas.perdanama_sk,tarif_konfig.perdanama_sk) AS perdanama_sk,
            COALESCE(tarif_normal.kelaspelayanan_id, tarif_penjamin.kelaspelayanan_id, tarif_kelas.kelaspelayanan_id,tarif_konfig.kelaspelayanan_id) AS kelaspelayanan_id,
            COALESCE(tarif_normal.kelaspelayanan_nama, tarif_penjamin.kelaspelayanan_nama, tarif_kelas.kelaspelayanan_nama,tarif_konfig.kelaspelayanan_nama) AS kelaspelayanan_nama,
            COALESCE(tarif_normal.penjamin_id, tarif_penjamin.penjamin_id, tarif_kelas.penjamin_id,tarif_konfig.penjamin_id) AS penjamin_id ,
            COALESCE(tarif_normal.penjamin_nama, tarif_penjamin.penjamin_nama, tarif_kelas.penjamin_nama,tarif_konfig.penjamin_nama) AS penjamin_nama,
            daftartindakan_m.kelompoktindakan_id,
            kelompoktindakan_m.kelompoktindakan_nama,
            daftartindakan_m.kategoritindakan_id,
            kategoritindakan_m.kategoritindakan_nama,
            daftartindakan_m.daftartindakan_id ,
            daftartindakan_m.daftartindakan_nama,
            NULL::integer AS tipepaket_id,
            NULL::character varying AS tipepaket_nama,
            COALESCE(tarif_normal.komponentarif_id, tarif_penjamin.komponentarif_id, tarif_kelas.komponentarif_id,tarif_konfig.komponentarif_id) AS komponentarif_id,
            COALESCE(tarif_normal.komponentarif_nama, tarif_penjamin.komponentarif_nama, tarif_kelas.komponentarif_nama,tarif_konfig.komponentarif_nama) AS komponentarif_nama,
            COALESCE(tarif_normal.harga_tariftindakan, tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan,tarif_konfig.harga_tariftindakan) AS harga_tariftindakan ,
            COALESCE(tarif_normal.persencyto_tindakan, tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan,tarif_konfig.persencyto_tindakan) AS persencyto_tindakan ,
            COALESCE(tarif_normal.persendiskon_tindakan, tarif_penjamin.persendiskon_tindakan, 
                             tarif_kelas.persendiskon_tindakan, tarif_konfig.persendiskon_tindakan) AS persendiskon_tindakan ,
            NULL::BOOLEAN is_default,
            daftartindakan_m.is_akomodasi,
            COALESCE(tarif_normal.carabayar_id, tarif_penjamin.carabayar_id, tarif_kelas.carabayar_id,tarif_konfig.carabayar_id) AS carabayar_id,
            daftartindakan_m.is_konsultasi,
            NULL::VARCHAR AS kamarruangan_nokamar,
            NULL::int4 AS kamarruangan_id,
            NULL::int4 AS ambulan_id,
            NULL::VARCHAR AS no_polisi,
      COALESCE(tarif_normal.persen_penyulit, tarif_penjamin.persen_penyulit, tarif_kelas.persen_penyulit,tarif_konfig.persen_penyulit) AS persen_penyulit
         FROM daftartindakan_m           
            ------------------------------tarif normal          
                    LEFT JOIN (
                            SELECT 
                                    'normal' AS tipe,   
                                    tariftindakan_m.daftartindakan_id,
                                    tariftindakan_m.tariftindakan_id,
                                    tariftindakan_m.kelaspelayanan_id,
                                    kelaspelayanan_m.kelaspelayanan_nama,
                                    tariftindakan_m.penjamin_id,
                                    penjamin_m.carabayar_id,
                                    tariftindakan_m.harga_tariftindakan,
                                    tariftindakan_m.persencyto_tindakan,
                                    tariftindakan_m.persendiskon_tindakan,
                                    tariftindakan_m.persen_penyulit,
                                    tariftindakan_m.perdatarif_id,
                                    perdatarif_m.perdanama_sk,
                                    penjamin_m.penjamin_nama,
                                    tariftindakan_m.komponentarif_id,
                                    komponentarif_m.komponentarif_nama,
                                    tariftindakan_m.dokter_id
                            FROM tariftindakan_m
                            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                            JOIN (SELECT * FROM perdatarif_m
                                            WHERE is_deleted=FALSE AND is_active=TRUE   
                                            AND perda_tgl <= now() 
                                            ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                            JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                            WHERE komponentarif_m.is_deleted IS FALSE
                                    AND tariftindakan_m.is_deleted = FALSE 
                                    AND tariftindakan_m.is_active = TRUE 
                                    AND tariftindakan_m.tarifparent_id IS NULL
                                    AND tariftindakan_m.komponentarif_id = 6
                            AND tariftindakan_m.penjamin_id = xpenjamin_id
                            AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id                  
                    ) tarif_normal ON daftartindakan_m.daftartindakan_id = tarif_normal.daftartindakan_id 
            --------------------tarif penjamin          
                    LEFT JOIN (
                            SELECT 
                                    'penjamin' AS tipe,
                                    tariftindakan_m.daftartindakan_id,
                                    tariftindakan_m.tariftindakan_id,
                                    tariftindakan_m.kelaspelayanan_id,
                                    kelaspelayanan_m.kelaspelayanan_nama,
                                    tariftindakan_m.penjamin_id,
                                    penjamin_m.carabayar_id,
                                    tariftindakan_m.harga_tariftindakan,
                                    tariftindakan_m.persencyto_tindakan,
                                    tariftindakan_m.persendiskon_tindakan,
                                    tariftindakan_m.persen_penyulit,
                                    tariftindakan_m.perdatarif_id,
                                    perdatarif_m.perdanama_sk,
                                    penjamin_m.penjamin_nama,
                                    tariftindakan_m.komponentarif_id,
                                    komponentarif_m.komponentarif_nama,
                                    tariftindakan_m.dokter_id
                            FROM tariftindakan_m
                            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                            JOIN (SELECT * FROM perdatarif_m
                                            WHERE is_deleted=FALSE AND is_active=TRUE   
                                            AND perda_tgl <= now() 
                                            ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                            JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                            WHERE komponentarif_m.is_deleted IS FALSE
                                    AND tariftindakan_m.is_deleted = FALSE 
                                    AND tariftindakan_m.is_active = TRUE 
                                    AND tariftindakan_m.tarifparent_id IS NULL
                                    AND tariftindakan_m.komponentarif_id = 6
                                    AND tariftindakan_m.penjamin_id = xpenjamin_id
                                    AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
                    ) tarif_penjamin ON daftartindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                                                     AND tarif_normal.tariftindakan_id IS NULL
                    ------------------tarif kelas
                    LEFT JOIN (
                            SELECT 
                                    'kelas' AS tipe,
                                    tariftindakan_m.daftartindakan_id,
                                    tariftindakan_m.tariftindakan_id,
                                    tariftindakan_m.kelaspelayanan_id,
                                    kelaspelayanan_m.kelaspelayanan_nama,
                                    tariftindakan_m.penjamin_id,
                                    penjamin_m.carabayar_id,
                                    tariftindakan_m.harga_tariftindakan,
                                    tariftindakan_m.persencyto_tindakan,
                                    tariftindakan_m.persendiskon_tindakan,
                                    tariftindakan_m.persen_penyulit,
                                    tariftindakan_m.perdatarif_id,
                                    perdatarif_m.perdanama_sk,
                                    penjamin_m.penjamin_nama,
                                    tariftindakan_m.komponentarif_id,
                                    komponentarif_m.komponentarif_nama,
                                    tariftindakan_m.dokter_id
                            FROM tariftindakan_m
                            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                            JOIN (SELECT * FROM perdatarif_m
                                            WHERE is_deleted=FALSE AND is_active=TRUE   
                                            AND perda_tgl <= now() 
                                            ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                            JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                            WHERE komponentarif_m.is_deleted IS FALSE
                                    AND tariftindakan_m.is_deleted = FALSE 
                                    AND tariftindakan_m.is_active = TRUE 
                                    AND tariftindakan_m.tarifparent_id IS NULL
                                    AND tariftindakan_m.komponentarif_id = 6
                                    AND tariftindakan_m.penjamin_id = vpenjamin_id
                                    AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
                    ) tarif_kelas ON daftartindakan_m.daftartindakan_id = tarif_kelas.daftartindakan_id 
                                                            AND tarif_penjamin.tariftindakan_id IS NULL
                                                            AND tarif_normal.tariftindakan_id IS NULL
                    ------------------ Tarif Default Global
                    LEFT JOIN (
                            SELECT 
                                    'global' AS tipe,
                                    tariftindakan_m.daftartindakan_id,
                                    tariftindakan_m.tariftindakan_id,
                                    tariftindakan_m.kelaspelayanan_id,
                                    kelaspelayanan_m.kelaspelayanan_nama,
                                    tariftindakan_m.penjamin_id,
                                    penjamin_m.carabayar_id,
                                    tariftindakan_m.harga_tariftindakan,
                                    tariftindakan_m.persencyto_tindakan,
                                    tariftindakan_m.persendiskon_tindakan,
                                    tariftindakan_m.persen_penyulit,
                                    tariftindakan_m.perdatarif_id,
                                    perdatarif_m.perdanama_sk,
                                    penjamin_m.penjamin_nama,
                                    tariftindakan_m.komponentarif_id,
                                    komponentarif_m.komponentarif_nama,
                                    tariftindakan_m.dokter_id
                            FROM tariftindakan_m
                            JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                            JOIN (SELECT * FROM perdatarif_m
                                            WHERE is_deleted=FALSE AND is_active=TRUE   
                                            AND perda_tgl <= now() 
                                            ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                            JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                            JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                            WHERE komponentarif_m.is_deleted IS FALSE
                                    AND tariftindakan_m.is_deleted = FALSE 
                                    AND tariftindakan_m.is_active = TRUE 
                                    AND tariftindakan_m.tarifparent_id IS NULL
                                    AND tariftindakan_m.komponentarif_id = 6
                                    AND tariftindakan_m.penjamin_id = vpenjamin_id
                                    AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
                    ) tarif_konfig ON daftartindakan_m.daftartindakan_id = tarif_konfig.daftartindakan_id 
                            AND tarif_penjamin.tariftindakan_id IS NULL
                            AND tarif_normal.tariftindakan_id IS NULL
                            AND tarif_kelas.tariftindakan_id IS NULL
------------------------------------------------------------------------------------------------------------------
                    JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
                    LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id   
                    WHERE daftartindakan_m.is_akomodasi=FALSE
                     AND tarif_normal.tariftindakan_id IS NOT NULL 
                     OR tarif_penjamin.tariftindakan_id IS NOT NULL 
                     OR tarif_kelas.tariftindakan_id IS NOT NULL OR tarif_konfig.tariftindakan_id IS NOT NULL           
    UNION ALL
        SELECT 
            'paket'::text AS jenis,
            COALESCE( tarif_normal.tariftindakan_id, tarif_penjamin.tariftindakan_id, tarif_kelas.tariftindakan_id, tarif_konfig.tariftindakan_id) AS tariftindakan_id ,
            NULL::INT AS ruangan_id,
            NULL::VARCHAR AS ruangan_nama,
            NULL::INT AS instalasi_id,
            NULL::VARCHAR AS instalasi_nama,
            NULL::INT AS ruanganpaket_id,
            NULL::VARCHAR AS ruanganpaket_nama,
            COALESCE(tarif_normal.perdatarif_id, tarif_penjamin.perdatarif_id, tarif_kelas.perdatarif_id,tarif_konfig.perdatarif_id) AS perdatarif_id ,
            COALESCE(tarif_normal.perdanama_sk, tarif_penjamin.perdanama_sk, tarif_kelas.perdanama_sk,tarif_konfig.perdanama_sk) AS perdanama_sk,
            COALESCE(tarif_normal.kelaspelayanan_id, tarif_penjamin.kelaspelayanan_id, tarif_kelas.kelaspelayanan_id,tarif_konfig.kelaspelayanan_id) AS kelaspelayanan_id,
            COALESCE(tarif_normal.kelaspelayanan_nama, tarif_penjamin.kelaspelayanan_nama, tarif_kelas.kelaspelayanan_nama,tarif_konfig.kelaspelayanan_nama) AS kelaspelayanan_nama,
            COALESCE(tarif_normal.penjamin_id, tarif_penjamin.penjamin_id, tarif_kelas.penjamin_id,tarif_konfig.penjamin_id) AS penjamin_id ,
            COALESCE(tarif_normal.penjamin_nama, tarif_penjamin.penjamin_nama, tarif_kelas.penjamin_nama,tarif_konfig.penjamin_nama) AS penjamin_nama,
            NULL::integer AS kelompoktindakan_id,
            NULL::character varying AS kelompoktindakan_nama,
            NULL::integer AS kategoritindakan_id,
            NULL::character varying AS kategoritindakan_nama,
            COALESCE(tarif_normal.daftartindakan_id, tarif_penjamin.daftartindakan_id, tarif_kelas.daftartindakan_id) AS daftartindakan_id,
            NULL::character varying AS daftartindakan_nama,
            tipepaket_m.tipepaket_id,
            tipepaket_m.tipepaket_nama,
            COALESCE(tarif_normal.komponentarif_id, tarif_penjamin.komponentarif_id, tarif_kelas.komponentarif_id,tarif_konfig.komponentarif_id) AS komponentarif_id,
            COALESCE(tarif_normal.komponentarif_nama, tarif_penjamin.komponentarif_nama, tarif_kelas.komponentarif_nama,tarif_konfig.komponentarif_nama) AS komponentarif_nama,
            COALESCE(tarif_normal.harga_tariftindakan, tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan,tarif_konfig.harga_tariftindakan) AS harga_tariftindakan ,
            COALESCE(tarif_normal.persencyto_tindakan, tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan,tarif_konfig.persencyto_tindakan) AS persencyto_tindakan ,
            COALESCE(tarif_normal.persendiskon_tindakan, tarif_penjamin.persendiskon_tindakan, 
                                tarif_kelas.persendiskon_tindakan,tarif_konfig.persendiskon_tindakan) AS persendiskon_tindakan ,
            NULL::BOOLEAN as is_default,
            NULL::boolean AS is_akomodasi,
            COALESCE(tarif_normal.carabayar_id, tarif_penjamin.carabayar_id, tarif_kelas.carabayar_id,tarif_konfig.carabayar_id) AS carabayar_id,
            false AS is_konsultasi,
            NULL::VARCHAR as  kamarruangan_nokamar,
            NULL::int4 as kamarruangan_id,
            NULL::int4 as ambulan_id,
            NULL::VARCHAR as no_polisi,
            COALESCE(tarif_normal.persen_penyulit, tarif_penjamin.persen_penyulit, tarif_kelas.persen_penyulit,tarif_konfig.persen_penyulit) AS persen_penyulit
    FROM tipepaket_m            
------------------------------tarif normal          
        LEFT JOIN (
                SELECT 
                        'normal' AS tipe,   
                        tariftindakan_m.tipepaket_id,
                        tariftindakan_m.tariftindakan_id,
                        tariftindakan_m.kelaspelayanan_id,
                        kelaspelayanan_m.kelaspelayanan_nama,
                        tariftindakan_m.penjamin_id,
                        penjamin_m.carabayar_id,
                        tariftindakan_m.harga_tariftindakan,
                        tariftindakan_m.persencyto_tindakan,
                        tariftindakan_m.persendiskon_tindakan,
                        tariftindakan_m.persen_penyulit,
                        tariftindakan_m.perdatarif_id,
                        perdatarif_m.perdanama_sk,
                        penjamin_m.penjamin_nama,
                        tariftindakan_m.komponentarif_id,
                        komponentarif_m.komponentarif_nama,
                        tariftindakan_m.dokter_id,
                        tariftindakan_m.daftartindakan_id
                FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN (SELECT * FROM perdatarif_m
                                WHERE is_deleted=FALSE AND is_active=TRUE   
                                        AND perda_tgl <= now() 
                                        ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                WHERE komponentarif_m.is_deleted IS FALSE
                        AND tariftindakan_m.is_deleted = FALSE 
                        AND tariftindakan_m.is_active = TRUE 
                        AND tariftindakan_m.tarifparent_id IS NULL
                        AND tariftindakan_m.komponentarif_id = 6
                AND tariftindakan_m.penjamin_id = xpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id                  
        ) tarif_normal ON tipepaket_m.tipepaket_id = tarif_normal.tipepaket_id 
                        
--------------------tarif penjamin          
            LEFT JOIN (
                    SELECT 
                        'penjamin' AS tipe,
                            tariftindakan_m.tipepaket_id,
                            tariftindakan_m.tariftindakan_id,
                            tariftindakan_m.kelaspelayanan_id,
                            kelaspelayanan_m.kelaspelayanan_nama,
                            tariftindakan_m.penjamin_id,
                            penjamin_m.carabayar_id,
                            tariftindakan_m.harga_tariftindakan,
                            tariftindakan_m.persencyto_tindakan,
                            tariftindakan_m.persendiskon_tindakan,
                            tariftindakan_m.persen_penyulit,
                            tariftindakan_m.perdatarif_id,
                            perdatarif_m.perdanama_sk,
                            penjamin_m.penjamin_nama,
                            tariftindakan_m.komponentarif_id,
                            komponentarif_m.komponentarif_nama,
                            tariftindakan_m.dokter_id,
                            tariftindakan_m.daftartindakan_id
                    FROM tariftindakan_m
                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                    JOIN (SELECT * FROM perdatarif_m
                                WHERE is_deleted=FALSE AND is_active=TRUE   
                                        AND perda_tgl <= now() 
                                        ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                    JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                    WHERE komponentarif_m.is_deleted IS FALSE
                            AND tariftindakan_m.is_deleted = FALSE 
                            AND tariftindakan_m.is_active = TRUE 
                            AND tariftindakan_m.tarifparent_id IS NULL
                            AND tariftindakan_m.komponentarif_id = 6
                            AND tariftindakan_m.penjamin_id = xpenjamin_id
                            AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
            ) tarif_penjamin ON tipepaket_m.tipepaket_id = tarif_penjamin.tipepaket_id 
                                                                             AND tarif_normal.tariftindakan_id IS NULL
------------------kelas
        LEFT JOIN (
                SELECT 
                        'kelas' AS tipe,
                        tariftindakan_m.tipepaket_id,
                        tariftindakan_m.tariftindakan_id,
                        tariftindakan_m.kelaspelayanan_id,
                        kelaspelayanan_m.kelaspelayanan_nama,
                        tariftindakan_m.penjamin_id,
                        penjamin_m.carabayar_id,
                        tariftindakan_m.harga_tariftindakan,
                        tariftindakan_m.persencyto_tindakan,
                        tariftindakan_m.persendiskon_tindakan,
                        tariftindakan_m.persen_penyulit,
                        tariftindakan_m.perdatarif_id,
                        perdatarif_m.perdanama_sk,
                        penjamin_m.penjamin_nama,
                        tariftindakan_m.komponentarif_id,
                        komponentarif_m.komponentarif_nama,
                        tariftindakan_m.dokter_id,
                        tariftindakan_m.daftartindakan_id
                FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN (SELECT * FROM perdatarif_m
                                WHERE is_deleted=FALSE AND is_active=TRUE   
                                        AND perda_tgl <= now() 
                                        ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                WHERE komponentarif_m.is_deleted IS FALSE
                        AND tariftindakan_m.is_deleted = FALSE 
                        AND tariftindakan_m.is_active = TRUE 
                        AND tariftindakan_m.tarifparent_id IS NULL
                        AND tariftindakan_m.komponentarif_id = 6
                        AND tariftindakan_m.penjamin_id = vpenjamin_id
                        AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
        ) tarif_kelas ON tipepaket_m.tipepaket_id = tarif_kelas.tipepaket_id 
                AND tarif_penjamin.tariftindakan_id IS NULL
                AND tarif_normal.tariftindakan_id IS NULL
------------------Tarif Global
                LEFT JOIN (
                    SELECT 
                        'global' AS tipe,
                        tariftindakan_m.tipepaket_id,
                        tariftindakan_m.tariftindakan_id,
                        tariftindakan_m.kelaspelayanan_id,
                        kelaspelayanan_m.kelaspelayanan_nama,
                        tariftindakan_m.penjamin_id,
                        penjamin_m.carabayar_id,
                        tariftindakan_m.harga_tariftindakan,
                        tariftindakan_m.persencyto_tindakan,
                        tariftindakan_m.persendiskon_tindakan,
                        tariftindakan_m.persen_penyulit,
                        tariftindakan_m.perdatarif_id,
                        perdatarif_m.perdanama_sk,
                        penjamin_m.penjamin_nama,
                        tariftindakan_m.komponentarif_id,
                        komponentarif_m.komponentarif_nama,
                        tariftindakan_m.dokter_id,
                        tariftindakan_m.daftartindakan_id
                    FROM tariftindakan_m
                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                    JOIN (SELECT * FROM perdatarif_m
                                                    WHERE is_deleted=FALSE AND is_active=TRUE   
                                                            AND perda_tgl <= now() 
                                                            ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                    JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                    WHERE komponentarif_m.is_deleted IS FALSE
                        AND tariftindakan_m.is_deleted = FALSE 
                        AND tariftindakan_m.is_active = TRUE 
                        AND tariftindakan_m.tarifparent_id IS NULL
                        AND tariftindakan_m.komponentarif_id = 6
                        AND tariftindakan_m.penjamin_id = vpenjamin_id
                        AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
                )tarif_konfig ON tipepaket_m.tipepaket_id = tarif_konfig.tipepaket_id 
                    AND tarif_penjamin.tariftindakan_id IS NULL
                    AND tarif_normal.tariftindakan_id IS NULL
                    AND tarif_kelas.tariftindakan_id IS NULL
------------------------------------------------------------------------------------------------------------------
      WHERE tarif_normal.tariftindakan_id IS NOT NULL OR tarif_penjamin.tariftindakan_id IS NOT NULL OR tarif_kelas.tariftindakan_id IS NOT NULL
                         
        ) AS x
        GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi, x.persen_penyulit;
    ELSEIF(xtipe = 'kamar')
    THEN
        RETURN QUERY 
   SELECT *FROM (
        SELECT      
                'kamar'::text AS jenis,
                        COALESCE( tarif_normal.tariftindakan_id, tarif_penjamin.tariftindakan_id, tarif_kelas.tariftindakan_id, tarif_konfig.tariftindakan_id) AS tariftindakan_id ,
                        COALESCE( tarif_normal.ruangan_id, tarif_penjamin.ruangan_id, tarif_kelas.ruangan_id, tarif_konfig.ruangan_id) as ruangan_id,
                        COALESCE( tarif_normal.ruangan_nama, tarif_penjamin.ruangan_nama, tarif_kelas.ruangan_nama, tarif_konfig.ruangan_nama) as ruangan_nama,
                        COALESCE( tarif_normal.instalasi_id, tarif_penjamin.instalasi_id, tarif_kelas.instalasi_id, tarif_konfig.instalasi_id) as instalasi_id,
                        COALESCE( tarif_normal.instalasi_nama, tarif_penjamin.instalasi_nama, tarif_kelas.instalasi_nama, tarif_konfig.instalasi_nama) as instalasi_nama,
                        NULL::integer AS ruanganpaket_id,
                        NULL::character varying AS ruanganpaket_nama,
                        COALESCE(tarif_normal.perdatarif_id, tarif_penjamin.perdatarif_id, tarif_kelas.perdatarif_id,tarif_konfig.perdatarif_id) AS perdatarif_id ,
                        COALESCE(tarif_normal.perdanama_sk, tarif_penjamin.perdanama_sk, tarif_kelas.perdanama_sk,tarif_konfig.perdanama_sk) AS perdanama_sk,
                        COALESCE(tarif_normal.kelaspelayanan_id, tarif_penjamin.kelaspelayanan_id, tarif_kelas.kelaspelayanan_id,tarif_konfig.kelaspelayanan_id) AS kelaspelayanan_id,
                        COALESCE(tarif_normal.kelaspelayanan_nama, tarif_penjamin.kelaspelayanan_nama, tarif_kelas.kelaspelayanan_nama,tarif_konfig.kelaspelayanan_nama) AS kelaspelayanan_nama,
                        COALESCE(tarif_normal.penjamin_id, tarif_penjamin.penjamin_id, tarif_kelas.penjamin_id,tarif_konfig.penjamin_id) AS penjamin_id ,
                        COALESCE(tarif_normal.penjamin_nama, tarif_penjamin.penjamin_nama, tarif_kelas.penjamin_nama,tarif_konfig.penjamin_nama) AS penjamin_nama,
                        daftartindakan_m.kelompoktindakan_id kelompoktindakan_id,
                        kelompoktindakan_m.kelompoktindakan_nama as kelompoktindakan_nama,
                        daftartindakan_m.kategoritindakan_id as kategoritindakan_id,
                        kategoritindakan_m.kategoritindakan_nama as kategoritindakan_nama,
                        daftartindakan_m.daftartindakan_id,
                        daftartindakan_m.daftartindakan_nama,
                        NULL::integer AS tipepaket_id,
                        NULL::character varying AS tipepaket_nama,
                        COALESCE(tarif_normal.komponentarif_id, tarif_penjamin.komponentarif_id, tarif_kelas.komponentarif_id,tarif_konfig.komponentarif_id) AS komponentarif_id,
                        COALESCE(tarif_normal.komponentarif_nama, tarif_penjamin.komponentarif_nama, tarif_kelas.komponentarif_nama,tarif_konfig.komponentarif_nama) AS komponentarif_nama,
                        COALESCE(tarif_normal.harga_tariftindakan, tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan,tarif_konfig.harga_tariftindakan) AS harga_tariftindakan ,
                        COALESCE(tarif_normal.persencyto_tindakan, tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan,tarif_konfig.persencyto_tindakan) AS persencyto_tindakan ,
                        COALESCE(tarif_normal.persendiskon_tindakan, tarif_penjamin.persendiskon_tindakan, 
                                            tarif_kelas.persendiskon_tindakan,tarif_konfig.persendiskon_tindakan) AS persendiskon_tindakan ,
                        NULL::BOOLEAN AS is_default,
                        daftartindakan_m.is_akomodasi as is_akomodasi,
                        COALESCE(tarif_normal.carabayar_id, tarif_penjamin.carabayar_id, tarif_kelas.carabayar_id,tarif_konfig.carabayar_id) AS carabayar_id,
                        daftartindakan_m.is_konsultasi as is_konsultasi,
                        COALESCE(tarif_normal.kamarruangan_nokamar, tarif_penjamin.kamarruangan_nokamar, tarif_kelas.kamarruangan_nokamar) as  kamarruangan_nokamar,
                        COALESCE( tarif_normal.kamarruangan_id, tarif_penjamin.kamarruangan_id, tarif_kelas.kamarruangan_id, tarif_konfig.kamarruangan_id) as kamarruangan_id,
                        NULL::int4 as ambulan_id,
                        NULL::VARCHAR as no_polisi,
                        COALESCE(tarif_normal.persen_penyulit, tarif_penjamin.persen_penyulit, tarif_kelas.persen_penyulit,tarif_konfig.persen_penyulit) AS persen_penyulit
            FROM daftartindakan_m           
    ------------------------------tarif normal          
                LEFT JOIN (
                    SELECT 
                        'normal' AS tipe,   
                        tariftindakan_m.daftartindakan_id,
                        tariftindakan_m.tariftindakan_id,
                        tariftindakan_m.kelaspelayanan_id,
                        kelaspelayanan_m.kelaspelayanan_nama,
                        tariftindakan_m.penjamin_id,
                        penjamin_m.carabayar_id,
                        tariftindakan_m.harga_tariftindakan,
                        tariftindakan_m.persencyto_tindakan,
                        tariftindakan_m.persendiskon_tindakan,
                        tariftindakan_m.persen_penyulit,
                        tariftindakan_m.perdatarif_id,
                        perdatarif_m.perdanama_sk,
                        penjamin_m.penjamin_nama,
                        tariftindakan_m.komponentarif_id,
                        komponentarif_m.komponentarif_nama,
                        tariftindakan_m.dokter_id,
                        tariftindakan_m.kamarruangan_id,
                        kamarruangan_m.ruangan_id,
                        kamarruangan_m.kamarruangan_nokamar,
                        ruangan_m.ruangan_nama,
                        ruangan_m.instalasi_id,
                        instalasi_m.instalasi_nama
                    FROM tariftindakan_m
                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                    JOIN (SELECT * FROM perdatarif_m
                                                        WHERE is_deleted=FALSE AND is_active=TRUE   
                                                                AND perda_tgl <= now() 
                                                                ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                    JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                    JOIN kamarruangan_m ON tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
                    JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
                    JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                    WHERE komponentarif_m.is_deleted IS FALSE
                        AND kamarruangan_m.is_deleted = FALSE 
                        AND kamarruangan_m.is_active = TRUE
                        AND tariftindakan_m.is_deleted = FALSE 
                        AND tariftindakan_m.is_active = TRUE 
                        AND tariftindakan_m.tarifparent_id IS NULL
                        AND tariftindakan_m.kamarruangan_id IS NOT NULL
                        AND tariftindakan_m.komponentarif_id = 6
                    AND tariftindakan_m.penjamin_id = xpenjamin_id
                    AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id                                                                                
                ) tarif_normal ON daftartindakan_m.daftartindakan_id = tarif_normal.daftartindakan_id 
                        
    --------------------tarif penjamin          
                LEFT JOIN (
                    SELECT 
                      'penjamin' AS tipe,
                        tariftindakan_m.daftartindakan_id,
                        tariftindakan_m.tariftindakan_id,
                        tariftindakan_m.kelaspelayanan_id,
                        kelaspelayanan_m.kelaspelayanan_nama,
                        tariftindakan_m.penjamin_id,
                        penjamin_m.carabayar_id,
                        tariftindakan_m.harga_tariftindakan,
                        tariftindakan_m.persencyto_tindakan,
                        tariftindakan_m.persendiskon_tindakan,
                        tariftindakan_m.persen_penyulit,
                        tariftindakan_m.perdatarif_id,
                        perdatarif_m.perdanama_sk,
                        penjamin_m.penjamin_nama,
                        tariftindakan_m.komponentarif_id,
                        komponentarif_m.komponentarif_nama,
                        tariftindakan_m.dokter_id,
                        tariftindakan_m.kamarruangan_id,
                        kamarruangan_m.ruangan_id,
                        kamarruangan_m.kamarruangan_nokamar,
                        ruangan_m.ruangan_nama,
                        ruangan_m.instalasi_id,
                        instalasi_m.instalasi_nama
                    FROM tariftindakan_m
                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                    JOIN (SELECT * FROM perdatarif_m
                                                    WHERE is_deleted=FALSE AND is_active=TRUE   
                                                            AND perda_tgl <= now() 
                                                            ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                    JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                    JOIN kamarruangan_m ON tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
                    JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
                    JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                    WHERE komponentarif_m.is_deleted IS FALSE
                      AND kamarruangan_m.is_deleted = FALSE 
                        AND kamarruangan_m.is_active = TRUE
                        AND tariftindakan_m.is_deleted = FALSE 
                        AND tariftindakan_m.is_active = TRUE 
                        AND tariftindakan_m.tarifparent_id IS NULL
                        AND tariftindakan_m.kamarruangan_id IS NOT NULL
                        AND tariftindakan_m.komponentarif_id = 6
                        AND tariftindakan_m.penjamin_id = xpenjamin_id
                        AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
       ) tarif_penjamin ON daftartindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                        AND tarif_normal.tariftindakan_id IS NULL
------------------kelas
                LEFT JOIN (
                    SELECT 
                        'kelas' AS tipe,
                        tariftindakan_m.daftartindakan_id,
                        tariftindakan_m.tariftindakan_id,
                        tariftindakan_m.kelaspelayanan_id,
                        kelaspelayanan_m.kelaspelayanan_nama,
                        tariftindakan_m.penjamin_id,
                        penjamin_m.carabayar_id,
                        tariftindakan_m.harga_tariftindakan,
                        tariftindakan_m.persencyto_tindakan,
                        tariftindakan_m.persendiskon_tindakan,
                        tariftindakan_m.persen_penyulit,
                        tariftindakan_m.perdatarif_id,
                        perdatarif_m.perdanama_sk,
                        penjamin_m.penjamin_nama,
                        tariftindakan_m.komponentarif_id,
                        komponentarif_m.komponentarif_nama,
                        tariftindakan_m.dokter_id,
                        tariftindakan_m.kamarruangan_id,
                        kamarruangan_m.kamarruangan_nokamar,
                        kamarruangan_m.ruangan_id,
                        ruangan_m.ruangan_nama,
                        ruangan_m.instalasi_id,
                        instalasi_m.instalasi_nama
                    FROM tariftindakan_m
                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                    JOIN (SELECT * FROM perdatarif_m
                                                    WHERE is_deleted=FALSE AND is_active=TRUE   
                                                            AND perda_tgl <= now() 
                                                            ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                    JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                    JOIN kamarruangan_m ON tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
                    JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
                    JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                    WHERE komponentarif_m.is_deleted IS FALSE
                        AND kamarruangan_m.is_deleted = FALSE 
                        AND kamarruangan_m.is_active = TRUE
                        AND tariftindakan_m.is_deleted = FALSE 
                        AND tariftindakan_m.is_active = TRUE 
                        AND tariftindakan_m.tarifparent_id IS NULL
                        AND tariftindakan_m.kamarruangan_id IS NOT NULL
                        AND tariftindakan_m.komponentarif_id = 6
                        AND tariftindakan_m.penjamin_id = vpenjamin_id
                        AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
              ) tarif_kelas ON daftartindakan_m.daftartindakan_id = tarif_kelas.daftartindakan_id 
                                                        AND tarif_penjamin.tariftindakan_id IS NULL
                                                        AND tarif_normal.tariftindakan_id IS NULL
                ------------------ Tarif Default Global
                LEFT JOIN (
                    SELECT 
                        'global' AS tipe,
                        tariftindakan_m.daftartindakan_id,
                        tariftindakan_m.tariftindakan_id,
                        tariftindakan_m.kelaspelayanan_id,
                        kelaspelayanan_m.kelaspelayanan_nama,
                        tariftindakan_m.penjamin_id,
                        penjamin_m.carabayar_id,
                        tariftindakan_m.harga_tariftindakan,
                        tariftindakan_m.persencyto_tindakan,
                        tariftindakan_m.persendiskon_tindakan,
                        tariftindakan_m.persen_penyulit,
                        tariftindakan_m.perdatarif_id,
                        perdatarif_m.perdanama_sk,
                        penjamin_m.penjamin_nama,
                        tariftindakan_m.komponentarif_id,
                        komponentarif_m.komponentarif_nama,
                        tariftindakan_m.dokter_id,
                        tariftindakan_m.kamarruangan_id,
                        kamarruangan_m.kamarruangan_nokamar,
                        kamarruangan_m.ruangan_id,
                        ruangan_m.ruangan_nama,
                        ruangan_m.instalasi_id,
                        instalasi_m.instalasi_nama
                    FROM tariftindakan_m
                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                    JOIN (SELECT * FROM perdatarif_m
                                                    WHERE is_deleted=FALSE AND is_active=TRUE   
                                                        AND perda_tgl <= now() 
                                                        ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                    JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                    JOIN kamarruangan_m ON tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
                    JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
                    JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                    WHERE komponentarif_m.is_deleted IS FALSE
                        AND kamarruangan_m.is_deleted = FALSE 
                        AND kamarruangan_m.is_active = TRUE
                        AND tariftindakan_m.is_deleted = FALSE 
                        AND tariftindakan_m.is_active = TRUE 
                        AND tariftindakan_m.tarifparent_id IS NULL
                        AND tariftindakan_m.kamarruangan_id IS NOT NULL
                        AND tariftindakan_m.komponentarif_id = 6
                        AND tariftindakan_m.penjamin_id = vpenjamin_id
                        AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
                ) tarif_konfig ON daftartindakan_m.daftartindakan_id = tarif_konfig.daftartindakan_id 
                    AND tarif_penjamin.tariftindakan_id IS NULL
                    AND tarif_normal.tariftindakan_id IS NULL
                    AND tarif_kelas.tariftindakan_id IS NULL
------------------------------------------------------------------------------------------------------------------
                JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
                LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
                            WHERE daftartindakan_m.is_akomodasi=TRUE  
                                                        
        ) AS x
        WHERE x.kelaspelayanan_id = xkelaspelayanan_id
        GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi, x.persen_penyulit;
    ELSE
        RETURN QUERY 
    SELECT *FROM (
        SELECT 
            'ambulan'::text AS jenis,
            COALESCE( tarif_normal.tariftindakan_id, tarif_penjamin.tariftindakan_id, tarif_kelas.tariftindakan_id, tarif_konfig.tariftindakan_id) AS tariftindakan_id ,
            NULL::integer as ruangan_id,
            NULL::character varying as ruangan_nama,
            NULL::integer as instalasi_id,
            NULL::character varying as instalasi_nama,
            NULL::integer AS ruanganpaket_id,
            NULL::character varying AS ruanganpaket_nama,
            COALESCE(tarif_normal.perdatarif_id, tarif_penjamin.perdatarif_id, tarif_kelas.perdatarif_id,tarif_konfig.perdatarif_id) AS perdatarif_id ,
            COALESCE(tarif_normal.perdanama_sk, tarif_penjamin.perdanama_sk, tarif_kelas.perdanama_sk,tarif_konfig.perdanama_sk) AS perdanama_sk,
            COALESCE(tarif_normal.kelaspelayanan_id, tarif_penjamin.kelaspelayanan_id, tarif_kelas.kelaspelayanan_id,tarif_konfig.kelaspelayanan_id) AS kelaspelayanan_id,
            COALESCE(tarif_normal.kelaspelayanan_nama, tarif_penjamin.kelaspelayanan_nama, tarif_kelas.kelaspelayanan_nama,tarif_konfig.kelaspelayanan_nama) AS kelaspelayanan_nama,
            COALESCE(tarif_normal.penjamin_id, tarif_penjamin.penjamin_id, tarif_kelas.penjamin_id,tarif_konfig.penjamin_id) AS penjamin_id ,
            COALESCE(tarif_normal.penjamin_nama, tarif_penjamin.penjamin_nama, tarif_kelas.penjamin_nama,tarif_konfig.penjamin_nama) AS penjamin_nama,
            daftartindakan_m.kelompoktindakan_id kelompoktindakan_id,
            kelompoktindakan_m.kelompoktindakan_nama,
            daftartindakan_m.kategoritindakan_id as kategoritindakan_id,
            kategoritindakan_m.kategoritindakan_nama,
            daftartindakan_m.daftartindakan_id AS daftartindakan_id ,
            daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
            NULL::integer AS tipepaket_id,
            NULL::character varying AS tipepaket_nama,
            COALESCE(tarif_normal.komponentarif_id, tarif_penjamin.komponentarif_id, tarif_kelas.komponentarif_id,tarif_konfig.komponentarif_id) AS komponentarif_id,
            COALESCE(tarif_normal.komponentarif_nama, tarif_penjamin.komponentarif_nama, tarif_kelas.komponentarif_nama,tarif_konfig.komponentarif_nama) AS komponentarif_nama,
            COALESCE(tarif_normal.harga_tariftindakan, tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan,tarif_konfig.harga_tariftindakan) AS harga_tariftindakan ,
            COALESCE(tarif_normal.persencyto_tindakan, tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan,tarif_konfig.persencyto_tindakan) AS persencyto_tindakan ,
            COALESCE(tarif_normal.persendiskon_tindakan, tarif_penjamin.persendiskon_tindakan, 
                                tarif_kelas.persendiskon_tindakan,tarif_konfig.persendiskon_tindakan) AS persendiskon_tindakan ,
            ambulandetail_m.is_default AS is_default,
            daftartindakan_m.is_akomodasi as is_akomodasi,
            COALESCE(tarif_normal.carabayar_id, tarif_penjamin.carabayar_id, tarif_kelas.carabayar_id,tarif_konfig.carabayar_id) AS carabayar_id,
            daftartindakan_m.is_konsultasi as is_konsultasi,
            NULL::VARCHAR as  kamarruangan_nokamar,
            COALESCE(tarif_normal.kamarruangan_id, tarif_penjamin.kamarruangan_id, tarif_kelas.kamarruangan_id) as kamarruangan_id,
            ambulan_m.ambulan_id as ambulan_id,
            ambulan_m.no_polisi as no_polisi,
      COALESCE(tarif_normal.persen_penyulit, tarif_penjamin.persen_penyulit, tarif_kelas.persen_penyulit,tarif_konfig.persen_penyulit) AS persen_penyulit
    FROM daftartindakan_m           
------------------------------tarif normal          
            LEFT JOIN (
                SELECT 
                    'normal' AS tipe,   
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    kelaspelayanan_m.kelaspelayanan_nama,
                    tariftindakan_m.penjamin_id,
                    penjamin_m.carabayar_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.persen_penyulit,
                    tariftindakan_m.perdatarif_id,
                    perdatarif_m.perdanama_sk,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id,
                    komponentarif_m.komponentarif_nama,
                    tariftindakan_m.dokter_id,
                    tariftindakan_m.kamarruangan_id
                FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN (SELECT * FROM perdatarif_m
                                                WHERE is_deleted=FALSE AND is_active=TRUE   
                                                        AND perda_tgl <= now() 
                                                        ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                WHERE komponentarif_m.is_deleted IS FALSE 
                    AND tariftindakan_m.is_deleted = FALSE 
                    AND tariftindakan_m.is_active = TRUE 
                    AND tariftindakan_m.tarifparent_id IS NULL
                    AND tariftindakan_m.komponentarif_id = 6
                AND tariftindakan_m.penjamin_id = xpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id                  
            ) tarif_normal ON daftartindakan_m.daftartindakan_id = tarif_normal.daftartindakan_id 
    --------------------tarif penjamin          
            LEFT JOIN (
                SELECT 
                  'penjamin' AS tipe,
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    kelaspelayanan_m.kelaspelayanan_nama,
                    tariftindakan_m.penjamin_id,
                    penjamin_m.carabayar_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.persen_penyulit,
                    tariftindakan_m.perdatarif_id,
                    perdatarif_m.perdanama_sk,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id,
                    komponentarif_m.komponentarif_nama,
                    tariftindakan_m.dokter_id,
                    tariftindakan_m.kamarruangan_id
                FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN (SELECT * FROM perdatarif_m
                                            WHERE is_deleted=FALSE AND is_active=TRUE   
                                                    AND perda_tgl <= now() 
                                                    ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                WHERE komponentarif_m.is_deleted IS FALSE
                    AND tariftindakan_m.is_deleted = FALSE 
                    AND tariftindakan_m.is_active = TRUE 
                    AND tariftindakan_m.tarifparent_id IS NULL
                    AND tariftindakan_m.komponentarif_id = 6
                    AND tariftindakan_m.penjamin_id = xpenjamin_id
                    AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
            ) tarif_penjamin ON daftartindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                 AND tarif_normal.tariftindakan_id IS NULL
------------------kelas
            LEFT JOIN (
                SELECT 
                    'kelas' AS tipe,
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    kelaspelayanan_m.kelaspelayanan_nama,
                    tariftindakan_m.penjamin_id,
                    penjamin_m.carabayar_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.persen_penyulit,
                    tariftindakan_m.perdatarif_id,
                    perdatarif_m.perdanama_sk,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id,
                    komponentarif_m.komponentarif_nama,
                    tariftindakan_m.dokter_id,
                    tariftindakan_m.kamarruangan_id
                FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN (SELECT * FROM perdatarif_m
                                            WHERE is_deleted=FALSE AND is_active=TRUE   
                                                    AND perda_tgl <= now() 
                                                    ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                WHERE komponentarif_m.is_deleted IS FALSE
                    AND tariftindakan_m.is_deleted = FALSE 
                    AND tariftindakan_m.is_active = TRUE 
                    AND tariftindakan_m.tarifparent_id IS NULL
                    AND tariftindakan_m.komponentarif_id = 6
                    AND tariftindakan_m.penjamin_id = vpenjamin_id
                    AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_kelas ON daftartindakan_m.daftartindakan_id = tarif_kelas.daftartindakan_id 
                                        AND tarif_penjamin.tariftindakan_id IS NULL
                                        AND tarif_normal.tariftindakan_id IS NULL
            ------------------ Tarif Default Global
            LEFT JOIN (
                SELECT 
                    'global' AS tipe,
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    kelaspelayanan_m.kelaspelayanan_nama,
                    tariftindakan_m.penjamin_id,
                    penjamin_m.carabayar_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.persen_penyulit,
                    tariftindakan_m.perdatarif_id,
                    perdatarif_m.perdanama_sk,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id,
                    komponentarif_m.komponentarif_nama,
                    tariftindakan_m.dokter_id
                FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN (SELECT * FROM perdatarif_m
                                            WHERE is_deleted=FALSE AND is_active=TRUE   
                                                    AND perda_tgl <= now() 
                                                    ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                WHERE komponentarif_m.is_deleted IS FALSE
                    AND tariftindakan_m.is_deleted = FALSE 
                    AND tariftindakan_m.is_active = TRUE 
                    AND tariftindakan_m.tarifparent_id IS NULL
                    AND tariftindakan_m.komponentarif_id = 6
                    AND tariftindakan_m.penjamin_id = vpenjamin_id
                    AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
            ) tarif_konfig ON daftartindakan_m.daftartindakan_id = tarif_konfig.daftartindakan_id 
                AND tarif_penjamin.tariftindakan_id IS NULL
                AND tarif_normal.tariftindakan_id IS NULL
                AND tarif_kelas.tariftindakan_id IS NULL
------------------------------------------------------------------------------------------------------------------
            JOIN ambulandetail_m ON daftartindakan_m.daftartindakan_id = ambulandetail_m.daftartindakan_id
                                                     AND ambulandetail_m.is_deleted = FALSE
            JOIN ambulan_m ON ambulandetail_m.ambulan_id = ambulan_m.ambulan_id
            LEFT JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
            LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
            WHERE tarif_normal.tariftindakan_id IS NOT NULL OR tarif_penjamin.tariftindakan_id IS NOT NULL OR tarif_kelas.tariftindakan_id IS NOT NULL
                        
        ) AS x
        GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi,x.persen_penyulit;
    END IF;
END; 
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;");

        $this->execute('DROP FUNCTION if exists "public"."komponentarifnaikkelas_fn";');

        $this->execute("
            CREATE OR REPLACE FUNCTION public.komponentarifnaikkelas_fn(xpenjamin_id int4=0, xkelaspelayanan_id int4=0, xtipe varchar='pelayanan'::character varying)
  RETURNS TABLE(jenis text, tariftindakan_id int4, ruangan_id int4, ruangan_nama varchar, instalasi_id int4, instalasi_nama varchar, ruanganpaket_id int4, ruanganpaket_nama varchar, perdatarif_id int4, perdanama_sk varchar, kelaspelayanan_id int4, kelaspelayanan_nama varchar, penjamin_id int4, penjamin_nama varchar, kelompoktindakan_id int4, kelompoktindakan_nama varchar, kategoritindakan_id int4, kategoritindakan_nama varchar, daftartindakan_id int4, daftartindakan_nama varchar, tipepaket_id int4, tipepaket_nama varchar, komponentarif_id int4, komponentarif_nama varchar, harga_tariftindakan numeric, persencyto_tindakan numeric, persendiskon_tindakan numeric, is_default bool, is_akomodasi bool, carabayar_id int4, is_konsultasi bool, kamarruangan_nokamar varchar, kamarruangan_id int4, ambulan_id int4, no_polisi varchar, persen_penyulit numeric) AS \$BODY\$ 
            DECLARE 
                vpenjamin_id int4;
                vkelaspelayanan_id int4;
                vkelas_id int4;
                vkelas_nama VARCHAR;
                    vcekpenjamin_id int4;
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
                
                    vcekpenjamin_id := xpenjamin_id;
                    
                IF(xpenjamin_id = vpenjamin_id)
                THEN
                    xpenjamin_id := 0;
                END IF;

            --  IF(xkelaspelayanan_id = vkelaspelayanan_id)
            --  THEN
            --      xkelaspelayanan_id := 0;
            --  END IF;
                
                DROP TABLE IF EXISTS temp_komponen_naik_kelas;
                
                IF(xtipe = 'pelayanan')
                THEN
            --      RETURN QUERY 
                    CREATE TEMPORARY TABLE temp_komponen_naik_kelas AS
    SELECT *FROM (
    SELECT 
        'tindakan'::text AS jenis,
        COALESCE( tarif_normal.tariftindakan_id, tarif_penjamin.tariftindakan_id, tarif_kelas.tariftindakan_id, tarif_konfig.tariftindakan_id) AS tariftindakan_id ,
                NULL::INT as ruangan_id,
                NULL::VARCHAR as ruangan_nama,
                NULL::INT as instalasi_id,
                NULL::VARCHAR as instalasi_nama,
                NULL::integer AS ruanganpaket_id,
                NULL::character varying AS ruanganpaket_nama,
        COALESCE(tarif_normal.perdatarif_id, tarif_penjamin.perdatarif_id, tarif_kelas.perdatarif_id,tarif_konfig.perdatarif_id) AS perdatarif_id ,
        COALESCE(tarif_normal.perdanama_sk, tarif_penjamin.perdanama_sk, tarif_kelas.perdanama_sk,tarif_konfig.perdanama_sk) AS perdanama_sk,
        COALESCE(tarif_normal.kelaspelayanan_id, tarif_penjamin.kelaspelayanan_id, tarif_kelas.kelaspelayanan_id,tarif_konfig.kelaspelayanan_id) AS kelaspelayanan_id,
        COALESCE(tarif_normal.kelaspelayanan_nama, tarif_penjamin.kelaspelayanan_nama, tarif_kelas.kelaspelayanan_nama,tarif_konfig.kelaspelayanan_nama) AS kelaspelayanan_nama,
        COALESCE(tarif_normal.penjamin_id, tarif_penjamin.penjamin_id, tarif_kelas.penjamin_id,tarif_konfig.penjamin_id) AS penjamin_id ,
        COALESCE(tarif_normal.penjamin_nama, tarif_penjamin.penjamin_nama, tarif_kelas.penjamin_nama,tarif_konfig.penjamin_nama) AS penjamin_nama,
        daftartindakan_m.kelompoktindakan_id,
        kelompoktindakan_m.kelompoktindakan_nama,
        daftartindakan_m.kategoritindakan_id,
        kategoritindakan_m.kategoritindakan_nama,
        daftartindakan_m.daftartindakan_id ,
        daftartindakan_m.daftartindakan_nama,
        NULL::integer AS tipepaket_id,
        NULL::character varying AS tipepaket_nama,
        COALESCE(tarif_normal.komponentarif_id, tarif_penjamin.komponentarif_id, tarif_kelas.komponentarif_id,tarif_konfig.komponentarif_id) AS komponentarif_id,
        COALESCE(tarif_normal.komponentarif_nama, tarif_penjamin.komponentarif_nama, tarif_kelas.komponentarif_nama,tarif_konfig.komponentarif_nama) AS komponentarif_nama,
        COALESCE(tarif_normal.harga_tariftindakan, tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan,tarif_konfig.harga_tariftindakan) AS harga_tariftindakan ,
        COALESCE(tarif_normal.persencyto_tindakan, tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan,tarif_konfig.persencyto_tindakan) AS persencyto_tindakan ,
        COALESCE(tarif_normal.persendiskon_tindakan, tarif_penjamin.persendiskon_tindakan, 
                            tarif_kelas.persendiskon_tindakan,tarif_konfig.persendiskon_tindakan) AS persendiskon_tindakan ,
        NULL::BOOLEAN is_default,
        daftartindakan_m.is_akomodasi,
        COALESCE(tarif_normal.carabayar_id, tarif_penjamin.carabayar_id, tarif_kelas.carabayar_id,tarif_konfig.carabayar_id) AS carabayar_id,
        daftartindakan_m.is_konsultasi,
        NULL::VARCHAR AS kamarruangan_nokamar,
        NULL::int4 AS kamarruangan_id,
        NULL::int4 AS ambulan_id,
        NULL::VARCHAR AS no_polisi,
    COALESCE(tarif_normal.persen_penyulit, tarif_penjamin.persen_penyulit, tarif_kelas.persen_penyulit,tarif_konfig.persen_penyulit) AS persen_penyulit
  FROM daftartindakan_m           
 ------------------------------tarif normal          
                LEFT JOIN (
                        SELECT 
                                'normal' AS tipe,   
                                tariftindakan_m.daftartindakan_id,
                                tariftindakan_m.tariftindakan_id,
                                tariftindakan_m.kelaspelayanan_id,
                                kelaspelayanan_m.kelaspelayanan_nama,
                                tariftindakan_m.penjamin_id,
                                penjamin_m.carabayar_id,
                                tariftindakan_m.harga_tariftindakan,
                                tariftindakan_m.persencyto_tindakan,
                                tariftindakan_m.persendiskon_tindakan,
                                tariftindakan_m.persen_penyulit,
                                tariftindakan_m.perdatarif_id,
                                perdatarif_m.perdanama_sk,
                                penjamin_m.penjamin_nama,
                                tariftindakan_m.komponentarif_id,
                                komponentarif_m.komponentarif_nama,
                                tariftindakan_m.dokter_id
                        FROM tariftindakan_m
                        JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                        JOIN (SELECT * FROM perdatarif_m
                                        WHERE is_deleted=FALSE AND is_active=TRUE   
                                                AND perda_tgl <= now() 
                                                ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                        JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                        JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                        WHERE komponentarif_m.is_deleted IS FALSE
                                AND tariftindakan_m.is_deleted = FALSE 
                                AND tariftindakan_m.is_active = TRUE 
                                AND tariftindakan_m.tarifparent_id IS NULL
                                AND tariftindakan_m.komponentarif_id <> 6
                        AND tariftindakan_m.penjamin_id = xpenjamin_id
                        AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id                  
                ) tarif_normal ON daftartindakan_m.daftartindakan_id = tarif_normal.daftartindakan_id 
                                
--------------------tarif penjamin          
        LEFT JOIN (
                SELECT 
                        'penjamin' AS tipe,
                        tariftindakan_m.daftartindakan_id,
                        tariftindakan_m.tariftindakan_id,
                        tariftindakan_m.kelaspelayanan_id,
                        kelaspelayanan_m.kelaspelayanan_nama,
                        tariftindakan_m.penjamin_id,
                        penjamin_m.carabayar_id,
                        tariftindakan_m.harga_tariftindakan,
                        tariftindakan_m.persencyto_tindakan,
                        tariftindakan_m.persendiskon_tindakan,
                        tariftindakan_m.persen_penyulit,
                        tariftindakan_m.perdatarif_id,
                        perdatarif_m.perdanama_sk,
                        penjamin_m.penjamin_nama,
                        tariftindakan_m.komponentarif_id,
                        komponentarif_m.komponentarif_nama,
                        tariftindakan_m.dokter_id
                FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN (SELECT * FROM perdatarif_m
                                WHERE is_deleted=FALSE AND is_active=TRUE   
                                        AND perda_tgl <= now() 
                                        ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                WHERE komponentarif_m.is_deleted IS FALSE
                        AND tariftindakan_m.is_deleted = FALSE 
                        AND tariftindakan_m.is_active = TRUE 
                        AND tariftindakan_m.tarifparent_id IS NULL
                        AND tariftindakan_m.komponentarif_id <> 6
                        AND tariftindakan_m.penjamin_id = vpenjamin_id
                        AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
        ) tarif_penjamin ON daftartindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                                         AND tarif_normal.tariftindakan_id IS NULL
 ------------------tarif kelas
                LEFT JOIN (
                        SELECT 
                                'kelas' AS tipe,
                                tariftindakan_m.daftartindakan_id,
                                tariftindakan_m.tariftindakan_id,
                                tariftindakan_m.kelaspelayanan_id,
                                kelaspelayanan_m.kelaspelayanan_nama,
                                tariftindakan_m.penjamin_id,
                                penjamin_m.carabayar_id,
                                tariftindakan_m.harga_tariftindakan,
                                tariftindakan_m.persencyto_tindakan,
                                tariftindakan_m.persendiskon_tindakan,
                                tariftindakan_m.persen_penyulit,
                                tariftindakan_m.perdatarif_id,
                                perdatarif_m.perdanama_sk,
                                penjamin_m.penjamin_nama,
                                tariftindakan_m.komponentarif_id,
                                komponentarif_m.komponentarif_nama,
                                tariftindakan_m.dokter_id
                        FROM tariftindakan_m
                        JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                        JOIN (SELECT * FROM perdatarif_m
                                        WHERE is_deleted=FALSE AND is_active=TRUE   
                                                AND perda_tgl <= now() 
                                                ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                        JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                        JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                        WHERE komponentarif_m.is_deleted IS FALSE
                                AND tariftindakan_m.is_deleted = FALSE 
                                AND tariftindakan_m.is_active = TRUE 
                                AND tariftindakan_m.tarifparent_id IS NULL
                                AND tariftindakan_m.komponentarif_id <> 6
                                AND tariftindakan_m.penjamin_id = vpenjamin_id
                                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
                ) tarif_kelas ON daftartindakan_m.daftartindakan_id = tarif_kelas.daftartindakan_id 
                                                                        AND tarif_penjamin.tariftindakan_id IS NULL
                                                                        AND tarif_normal.tariftindakan_id IS NULL
 ------------------ Tarif Default Global
            LEFT JOIN (
                    SELECT 
                            'global' AS tipe,
                            tariftindakan_m.daftartindakan_id,
                            tariftindakan_m.tariftindakan_id,
                            tariftindakan_m.kelaspelayanan_id,
                            kelaspelayanan_m.kelaspelayanan_nama,
                            tariftindakan_m.penjamin_id,
                            penjamin_m.carabayar_id,
                            tariftindakan_m.harga_tariftindakan,
                            tariftindakan_m.persencyto_tindakan,
                            tariftindakan_m.persendiskon_tindakan,
                            tariftindakan_m.persen_penyulit,
                            tariftindakan_m.perdatarif_id,
                            perdatarif_m.perdanama_sk,
                            penjamin_m.penjamin_nama,
                            tariftindakan_m.komponentarif_id,
                            komponentarif_m.komponentarif_nama,
                            tariftindakan_m.dokter_id
                    FROM tariftindakan_m
                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                    JOIN (SELECT * FROM perdatarif_m
                                        WHERE is_deleted=FALSE AND is_active=TRUE   
                                                AND perda_tgl <= now() 
                                                ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                    JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                    WHERE komponentarif_m.is_deleted IS FALSE
                            AND tariftindakan_m.is_deleted = FALSE 
                            AND tariftindakan_m.is_active = TRUE 
                            AND tariftindakan_m.tarifparent_id IS NULL
                            AND tariftindakan_m.komponentarif_id <> 6
                            AND tariftindakan_m.penjamin_id = vpenjamin_id
                            AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
            ) tarif_konfig ON daftartindakan_m.daftartindakan_id = tarif_konfig.daftartindakan_id 
                    AND tarif_penjamin.tariftindakan_id IS NULL
                    AND tarif_normal.tariftindakan_id IS NULL
                    AND tarif_kelas.tariftindakan_id IS NULL
 ------------------------------------------------------------------------------------------------------------------
                JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
                LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id   
     WHERE tarif_normal.tariftindakan_id IS NOT NULL OR tarif_penjamin.tariftindakan_id IS NOT NULL OR tarif_kelas.tariftindakan_id IS NOT NULL 
 UNION ALL
     SELECT 
                'paket'::text AS jenis,
                COALESCE( tarif_normal.tariftindakan_id, tarif_penjamin.tariftindakan_id, tarif_kelas.tariftindakan_id, tarif_konfig.tariftindakan_id) AS tariftindakan_id ,
                                NULL::INT AS ruangan_id,
                                NULL::VARCHAR AS ruangan_nama,
                                NULL::INT AS instalasi_id,
                                NULL::VARCHAR AS instalasi_nama,
                                NULL::INT AS ruanganpaket_id,
                                NULL::VARCHAR AS ruanganpaket_nama,
                COALESCE(tarif_normal.perdatarif_id, tarif_penjamin.perdatarif_id, tarif_kelas.perdatarif_id,tarif_konfig.perdatarif_id) AS perdatarif_id ,
                COALESCE(tarif_normal.perdanama_sk, tarif_penjamin.perdanama_sk, tarif_kelas.perdanama_sk,tarif_konfig.perdanama_sk) AS perdanama_sk,
                COALESCE(tarif_normal.kelaspelayanan_id, tarif_penjamin.kelaspelayanan_id, tarif_kelas.kelaspelayanan_id,tarif_konfig.kelaspelayanan_id) AS kelaspelayanan_id,
                COALESCE(tarif_normal.kelaspelayanan_nama, tarif_penjamin.kelaspelayanan_nama, tarif_kelas.kelaspelayanan_nama,tarif_konfig.kelaspelayanan_nama) AS kelaspelayanan_nama,
                COALESCE(tarif_normal.penjamin_id, tarif_penjamin.penjamin_id, tarif_kelas.penjamin_id,tarif_konfig.penjamin_id) AS penjamin_id ,
                COALESCE(tarif_normal.penjamin_nama, tarif_penjamin.penjamin_nama, tarif_kelas.penjamin_nama,tarif_konfig.penjamin_nama) AS penjamin_nama,
                NULL::integer AS kelompoktindakan_id,
                NULL::character varying AS kelompoktindakan_nama,
                NULL::integer AS kategoritindakan_id,
                NULL::character varying AS kategoritindakan_nama,
                COALESCE(tarif_normal.daftartindakan_id, tarif_penjamin.daftartindakan_id, tarif_kelas.daftartindakan_id) AS daftartindakan_id,
                NULL::character varying AS daftartindakan_nama,
                tipepaket_m.tipepaket_id,
                tipepaket_m.tipepaket_nama,
                COALESCE(tarif_normal.komponentarif_id, tarif_penjamin.komponentarif_id, tarif_kelas.komponentarif_id) AS komponentarif_id,
                COALESCE(tarif_normal.komponentarif_nama, tarif_penjamin.komponentarif_nama, tarif_kelas.komponentarif_nama) AS komponentarif_nama,
                COALESCE(tarif_normal.harga_tariftindakan, tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan) AS harga_tariftindakan ,
                COALESCE(tarif_normal.persencyto_tindakan, tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan) AS persencyto_tindakan ,
                COALESCE(tarif_normal.persendiskon_tindakan, tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan) AS persendiskon_tindakan ,
                NULL::BOOLEAN as is_default,
                NULL::boolean AS is_akomodasi,
                COALESCE(tarif_normal.carabayar_id, tarif_penjamin.carabayar_id, tarif_kelas.carabayar_id,tarif_konfig.carabayar_id) AS carabayar_id,
                false AS is_konsultasi,
                NULL::VARCHAR as  kamarruangan_nokamar,
                NULL::int4 as kamarruangan_id,
                NULL::int4 as ambulan_id,
                NULL::VARCHAR as no_polisi,
                COALESCE(tarif_normal.persen_penyulit, tarif_penjamin.persen_penyulit, tarif_kelas.persen_penyulit,tarif_konfig.persen_penyulit) AS persen_penyulit
     FROM tipepaket_m            
    ------------------------------tarif normal          
                LEFT JOIN (
                    SELECT 
                        'normal' AS tipe,   
                        tariftindakan_m.tipepaket_id,
                        tariftindakan_m.tariftindakan_id,
                        tariftindakan_m.kelaspelayanan_id,
                        kelaspelayanan_m.kelaspelayanan_nama,
                        tariftindakan_m.penjamin_id,
                        penjamin_m.carabayar_id,
                        tariftindakan_m.harga_tariftindakan,
                        tariftindakan_m.persencyto_tindakan,
                        tariftindakan_m.persendiskon_tindakan,
                        tariftindakan_m.persen_penyulit,
                        tariftindakan_m.perdatarif_id,
                        perdatarif_m.perdanama_sk,
                        penjamin_m.penjamin_nama,
                        tariftindakan_m.komponentarif_id,
                        komponentarif_m.komponentarif_nama,
                        tariftindakan_m.dokter_id,
                        tariftindakan_m.daftartindakan_id
                    FROM tariftindakan_m
                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                    JOIN (SELECT * FROM perdatarif_m
                                                            WHERE is_deleted=FALSE AND is_active=TRUE   
                                                                    AND perda_tgl <= now() 
                                                                    ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                    JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                    WHERE komponentarif_m.is_deleted IS FALSE
                        AND tariftindakan_m.is_deleted = FALSE 
                        AND tariftindakan_m.is_active = TRUE 
                        AND tariftindakan_m.tarifparent_id IS NOT NULL
                        AND tariftindakan_m.komponentarif_id <> 6
                    AND tariftindakan_m.penjamin_id = xpenjamin_id
                    AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id                  
                ) tarif_normal ON tipepaket_m.tipepaket_id = tarif_normal.tipepaket_id 
                        
    --------------------tarif penjamin          
                LEFT JOIN (
                    SELECT 
                      'penjamin' AS tipe,
                        tariftindakan_m.tipepaket_id,
                        tariftindakan_m.tariftindakan_id,
                        tariftindakan_m.kelaspelayanan_id,
                        kelaspelayanan_m.kelaspelayanan_nama,
                        tariftindakan_m.penjamin_id,
                        penjamin_m.carabayar_id,
                        tariftindakan_m.harga_tariftindakan,
                        tariftindakan_m.persencyto_tindakan,
                        tariftindakan_m.persendiskon_tindakan,
                        tariftindakan_m.persen_penyulit,
                        tariftindakan_m.perdatarif_id,
                        perdatarif_m.perdanama_sk,
                        penjamin_m.penjamin_nama,
                        tariftindakan_m.komponentarif_id,
                        komponentarif_m.komponentarif_nama,
                        tariftindakan_m.dokter_id,
                                                tariftindakan_m.daftartindakan_id
                    FROM tariftindakan_m
                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                    JOIN (SELECT * FROM perdatarif_m
                                                        WHERE is_deleted=FALSE AND is_active=TRUE   
                                                                AND perda_tgl <= now() 
                                                                ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                    JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                    WHERE komponentarif_m.is_deleted IS FALSE
                        AND tariftindakan_m.is_deleted = FALSE 
                        AND tariftindakan_m.is_active = TRUE 
                        AND tariftindakan_m.tarifparent_id IS NOT NULL
                        AND tariftindakan_m.komponentarif_id <> 6
                        AND tariftindakan_m.penjamin_id = vpenjamin_id
                        AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
                ) tarif_penjamin ON tipepaket_m.tipepaket_id = tarif_penjamin.tipepaket_id 
                                                 AND tarif_normal.tariftindakan_id IS NULL
------------------kelas
                LEFT JOIN (
                    SELECT 
                        'kelas' AS tipe,
                        tariftindakan_m.tipepaket_id,
                        tariftindakan_m.tariftindakan_id,
                        tariftindakan_m.kelaspelayanan_id,
                        kelaspelayanan_m.kelaspelayanan_nama,
                        tariftindakan_m.penjamin_id,
                        penjamin_m.carabayar_id,
                        tariftindakan_m.harga_tariftindakan,
                        tariftindakan_m.persencyto_tindakan,
                        tariftindakan_m.persendiskon_tindakan,
                        tariftindakan_m.persen_penyulit,
                        tariftindakan_m.perdatarif_id,
                        perdatarif_m.perdanama_sk,
                        penjamin_m.penjamin_nama,
                        tariftindakan_m.komponentarif_id,
                        komponentarif_m.komponentarif_nama,
                        tariftindakan_m.dokter_id,
                                                tariftindakan_m.daftartindakan_id
                    FROM tariftindakan_m
                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                    JOIN (SELECT * FROM perdatarif_m
                                                        WHERE is_deleted=FALSE AND is_active=TRUE   
                                                                AND perda_tgl <= now() 
                                                                ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                    JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                    WHERE komponentarif_m.is_deleted IS FALSE
                        AND tariftindakan_m.is_deleted = FALSE 
                        AND tariftindakan_m.is_active = TRUE 
                        AND tariftindakan_m.tarifparent_id IS NOT NULL
                        AND tariftindakan_m.komponentarif_id <> 6
                        AND tariftindakan_m.penjamin_id = vpenjamin_id
                        AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
                ) tarif_kelas ON tipepaket_m.tipepaket_id = tarif_kelas.tipepaket_id 
                                            AND tarif_penjamin.tariftindakan_id IS NULL
                                            AND tarif_normal.tariftindakan_id IS NULL
----------------------------global                                                                                      
                LEFT JOIN (
                    SELECT 
                        'global' AS tipe,
                        tariftindakan_m.tipepaket_id,
                        tariftindakan_m.tariftindakan_id,
                        tariftindakan_m.kelaspelayanan_id,
                        kelaspelayanan_m.kelaspelayanan_nama,
                        tariftindakan_m.penjamin_id,
                        penjamin_m.carabayar_id,
                        tariftindakan_m.harga_tariftindakan,
                        tariftindakan_m.persencyto_tindakan,
                        tariftindakan_m.persendiskon_tindakan,
                        tariftindakan_m.persen_penyulit,
                        tariftindakan_m.perdatarif_id,
                        perdatarif_m.perdanama_sk,
                        penjamin_m.penjamin_nama,
                        tariftindakan_m.komponentarif_id,
                        komponentarif_m.komponentarif_nama,
                        tariftindakan_m.dokter_id,
                        tariftindakan_m.daftartindakan_id
                    FROM tariftindakan_m
                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                    JOIN (SELECT * FROM perdatarif_m
                                                        WHERE is_deleted=FALSE AND is_active=TRUE   
                                                                AND perda_tgl <= now() 
                                                                ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                    JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                    WHERE komponentarif_m.is_deleted IS FALSE
                        AND tariftindakan_m.is_deleted = FALSE 
                        AND tariftindakan_m.is_active = TRUE 
                        AND tariftindakan_m.tarifparent_id IS NULL
                        AND tariftindakan_m.komponentarif_id <> 6
                        AND tariftindakan_m.penjamin_id = vpenjamin_id
                        AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
                )tarif_konfig ON tipepaket_m.tipepaket_id = tarif_konfig.tipepaket_id 
                    AND tarif_penjamin.tariftindakan_id IS NULL
                    AND tarif_normal.tariftindakan_id IS NULL
                    AND tarif_kelas.tariftindakan_id IS NULL
------------------------------------------------------------------------------------------------------------------
    WHERE tarif_normal.tariftindakan_id IS NOT NULL OR tarif_penjamin.tariftindakan_id IS NOT NULL OR tarif_kelas.tariftindakan_id IS NOT NULL
  ) AS x
  GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi, x.persen_penyulit;
                ELSEIF(xtipe = 'kamar')
                THEN
            --      RETURN QUERY 
    CREATE TEMPORARY TABLE temp_komponen_naik_kelas AS
    SELECT *FROM (
        SELECT      
            'kamar'::text AS jenis,
            COALESCE( tarif_normal.tariftindakan_id, tarif_penjamin.tariftindakan_id, tarif_kelas.tariftindakan_id, tarif_konfig.tariftindakan_id) AS tariftindakan_id ,
            COALESCE( tarif_normal.ruangan_id, tarif_penjamin.ruangan_id, tarif_kelas.ruangan_id, tarif_konfig.ruangan_id) as ruangan_id,
            COALESCE( tarif_normal.ruangan_nama, tarif_penjamin.ruangan_nama, tarif_kelas.ruangan_nama, tarif_konfig.ruangan_nama) as ruangan_nama,
            COALESCE( tarif_normal.instalasi_id, tarif_penjamin.instalasi_id, tarif_kelas.instalasi_id, tarif_konfig.instalasi_id) as instalasi_id,
            COALESCE( tarif_normal.instalasi_nama, tarif_penjamin.instalasi_nama, tarif_kelas.instalasi_nama, tarif_konfig.instalasi_nama) as instalasi_nama,
            NULL::integer AS ruanganpaket_id,
            NULL::character varying AS ruanganpaket_nama,
            COALESCE(tarif_normal.perdatarif_id, tarif_penjamin.perdatarif_id, tarif_kelas.perdatarif_id,tarif_konfig.perdatarif_id) AS perdatarif_id ,
            COALESCE(tarif_normal.perdanama_sk, tarif_penjamin.perdanama_sk, tarif_kelas.perdanama_sk,tarif_konfig.perdanama_sk) AS perdanama_sk,
            COALESCE(tarif_normal.kelaspelayanan_id, tarif_penjamin.kelaspelayanan_id, tarif_kelas.kelaspelayanan_id,tarif_konfig.kelaspelayanan_id) AS kelaspelayanan_id,
            COALESCE(tarif_normal.kelaspelayanan_nama, tarif_penjamin.kelaspelayanan_nama, tarif_kelas.kelaspelayanan_nama,tarif_konfig.kelaspelayanan_nama) AS kelaspelayanan_nama,
            COALESCE(tarif_normal.penjamin_id, tarif_penjamin.penjamin_id, tarif_kelas.penjamin_id,tarif_konfig.penjamin_id) AS penjamin_id ,
            COALESCE(tarif_normal.penjamin_nama, tarif_penjamin.penjamin_nama, tarif_kelas.penjamin_nama,tarif_konfig.penjamin_nama) AS penjamin_nama,
            daftartindakan_m.kelompoktindakan_id kelompoktindakan_id,
            kelompoktindakan_m.kelompoktindakan_nama as kelompoktindakan_nama,
            daftartindakan_m.kategoritindakan_id as kategoritindakan_id,
            kategoritindakan_m.kategoritindakan_nama as kategoritindakan_nama,
            daftartindakan_m.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            NULL::integer AS tipepaket_id,
            NULL::character varying AS tipepaket_nama,
            COALESCE(tarif_normal.komponentarif_id, tarif_penjamin.komponentarif_id, tarif_kelas.komponentarif_id,tarif_konfig.komponentarif_id) AS komponentarif_id,
            COALESCE(tarif_normal.komponentarif_nama, tarif_penjamin.komponentarif_nama, tarif_kelas.komponentarif_nama,tarif_konfig.komponentarif_nama) AS komponentarif_nama,
            COALESCE(tarif_normal.harga_tariftindakan, tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan,tarif_konfig.harga_tariftindakan) AS harga_tariftindakan ,
            COALESCE(tarif_normal.persencyto_tindakan, tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan,tarif_konfig.persencyto_tindakan) AS persencyto_tindakan ,
            COALESCE(tarif_normal.persendiskon_tindakan, tarif_penjamin.persendiskon_tindakan, 
                                tarif_kelas.persendiskon_tindakan,tarif_konfig.persendiskon_tindakan) AS persendiskon_tindakan ,
            NULL::BOOLEAN AS is_default,
            daftartindakan_m.is_akomodasi as is_akomodasi,
            COALESCE(tarif_normal.carabayar_id, tarif_penjamin.carabayar_id, tarif_kelas.carabayar_id,tarif_konfig.carabayar_id) AS carabayar_id,
            daftartindakan_m.is_konsultasi as is_konsultasi,
            COALESCE(tarif_normal.kamarruangan_nokamar, tarif_penjamin.kamarruangan_nokamar, tarif_kelas.kamarruangan_nokamar) as  kamarruangan_nokamar,
            COALESCE( tarif_normal.kamarruangan_id, tarif_penjamin.kamarruangan_id, tarif_kelas.kamarruangan_id, tarif_konfig.kamarruangan_id) as kamarruangan_id,
            NULL::int4 as ambulan_id,
            NULL::VARCHAR as no_polisi,
            COALESCE(tarif_normal.persen_penyulit, tarif_penjamin.persen_penyulit, tarif_kelas.persen_penyulit,tarif_konfig.persen_penyulit) AS persen_penyulit
    FROM daftartindakan_m           
    ------------------------------tarif normal          
            LEFT JOIN (
                SELECT 
                    'normal' AS tipe,   
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    kelaspelayanan_m.kelaspelayanan_nama,
                    tariftindakan_m.penjamin_id,
                    penjamin_m.carabayar_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.persen_penyulit,
                    tariftindakan_m.perdatarif_id,
                    perdatarif_m.perdanama_sk,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id,
                    komponentarif_m.komponentarif_nama,
                    tariftindakan_m.dokter_id,
                    tariftindakan_m.kamarruangan_id,
                    kamarruangan_m.ruangan_id,
                    kamarruangan_m.kamarruangan_nokamar,
                    ruangan_m.ruangan_nama,
                    ruangan_m.instalasi_id,
                    instalasi_m.instalasi_nama
                FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN (SELECT * FROM perdatarif_m
                                                WHERE is_deleted=FALSE AND is_active=TRUE   
                                                        AND perda_tgl <= now() 
                                                        ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                JOIN kamarruangan_m ON tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
                JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
                JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                WHERE komponentarif_m.is_deleted IS FALSE
                    AND kamarruangan_m.is_deleted = FALSE 
                    AND kamarruangan_m.is_active = TRUE
                    AND tariftindakan_m.is_deleted = FALSE 
                    AND tariftindakan_m.is_active = TRUE 
                    AND tariftindakan_m.tarifparent_id IS NULL
                    AND tariftindakan_m.kamarruangan_id IS NOT NULL
                    AND tariftindakan_m.komponentarif_id <> 6
                AND tariftindakan_m.penjamin_id = xpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id                  
            ) tarif_normal ON daftartindakan_m.daftartindakan_id = tarif_normal.daftartindakan_id 
                    
--------------------tarif penjamin          
            LEFT JOIN (
                SELECT 
                  'penjamin' AS tipe,
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    kelaspelayanan_m.kelaspelayanan_nama,
                    tariftindakan_m.penjamin_id,
                    penjamin_m.carabayar_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.persen_penyulit,
                    tariftindakan_m.perdatarif_id,
                    perdatarif_m.perdanama_sk,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id,
                    komponentarif_m.komponentarif_nama,
                    tariftindakan_m.dokter_id,
                    tariftindakan_m.kamarruangan_id,
                    kamarruangan_m.ruangan_id,
                    kamarruangan_m.kamarruangan_nokamar,
                    ruangan_m.ruangan_nama,
                    ruangan_m.instalasi_id,
                    instalasi_m.instalasi_nama
                FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN (SELECT * FROM perdatarif_m
                                                WHERE is_deleted=FALSE AND is_active=TRUE   
                                                        AND perda_tgl <= now() 
                                                        ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                JOIN kamarruangan_m ON tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
                JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
                JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                WHERE komponentarif_m.is_deleted IS FALSE
                  AND kamarruangan_m.is_deleted = FALSE 
                    AND kamarruangan_m.is_active = TRUE
                    AND tariftindakan_m.is_deleted = FALSE 
                    AND tariftindakan_m.is_active = TRUE 
                    AND tariftindakan_m.tarifparent_id IS NULL
                    AND tariftindakan_m.kamarruangan_id IS NOT NULL
                    AND tariftindakan_m.komponentarif_id <> 6
                    AND tariftindakan_m.penjamin_id = vpenjamin_id
                    AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_penjamin ON daftartindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                             AND tarif_normal.tariftindakan_id IS NULL
------------------kelas
            LEFT JOIN (
                SELECT 
                    'kelas' AS tipe,
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    kelaspelayanan_m.kelaspelayanan_nama,
                    tariftindakan_m.penjamin_id,
                    penjamin_m.carabayar_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.persen_penyulit,
                    tariftindakan_m.perdatarif_id,
                    perdatarif_m.perdanama_sk,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id,
                    komponentarif_m.komponentarif_nama,
                    tariftindakan_m.dokter_id,
                    tariftindakan_m.kamarruangan_id,
                    kamarruangan_m.kamarruangan_nokamar,
                    kamarruangan_m.ruangan_id,
                    ruangan_m.ruangan_nama,
                    ruangan_m.instalasi_id,
                    instalasi_m.instalasi_nama
                FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN (SELECT * FROM perdatarif_m
                                                WHERE is_deleted=FALSE AND is_active=TRUE   
                                                        AND perda_tgl <= now() 
                                                        ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                JOIN kamarruangan_m ON tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
                JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
                JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                WHERE komponentarif_m.is_deleted IS FALSE
                    AND kamarruangan_m.is_deleted = FALSE 
                    AND kamarruangan_m.is_active = TRUE
                    AND tariftindakan_m.is_deleted = FALSE 
                    AND tariftindakan_m.is_active = TRUE 
                    AND tariftindakan_m.tarifparent_id IS NULL
                    AND tariftindakan_m.kamarruangan_id IS NOT NULL
                    AND tariftindakan_m.komponentarif_id <> 6
                    AND tariftindakan_m.penjamin_id = vpenjamin_id
                    AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_kelas ON daftartindakan_m.daftartindakan_id = tarif_kelas.daftartindakan_id 
                                        AND tarif_penjamin.tariftindakan_id IS NULL
                                        AND tarif_normal.tariftindakan_id IS NULL
            ------------------ Tarif Default Global
                LEFT JOIN (
                    SELECT 
                        'global' AS tipe,
                        tariftindakan_m.daftartindakan_id,
                        tariftindakan_m.tariftindakan_id,
                        tariftindakan_m.kelaspelayanan_id,
                        kelaspelayanan_m.kelaspelayanan_nama,
                        tariftindakan_m.penjamin_id,
                        penjamin_m.carabayar_id,
                        tariftindakan_m.harga_tariftindakan,
                        tariftindakan_m.persencyto_tindakan,
                        tariftindakan_m.persendiskon_tindakan,
                        tariftindakan_m.persen_penyulit,
                        tariftindakan_m.perdatarif_id,
                        perdatarif_m.perdanama_sk,
                        penjamin_m.penjamin_nama,
                        tariftindakan_m.komponentarif_id,
                        komponentarif_m.komponentarif_nama,
                        tariftindakan_m.dokter_id,
                        tariftindakan_m.kamarruangan_id,
                        kamarruangan_m.kamarruangan_nokamar,
                        kamarruangan_m.ruangan_id,
                        ruangan_m.ruangan_nama,
                        ruangan_m.instalasi_id,
                        instalasi_m.instalasi_nama
                    FROM tariftindakan_m
                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                    JOIN (SELECT * FROM perdatarif_m
                                                        WHERE is_deleted=FALSE AND is_active=TRUE   
                                                                AND perda_tgl <= now() 
                                                                ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                    JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                    JOIN kamarruangan_m ON tariftindakan_m.kamarruangan_id = kamarruangan_m.kamarruangan_id
                    JOIN ruangan_m ON kamarruangan_m.ruangan_id = ruangan_m.ruangan_id
                    JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                    WHERE komponentarif_m.is_deleted IS FALSE
                        AND kamarruangan_m.is_deleted = FALSE 
                        AND kamarruangan_m.is_active = TRUE
                        AND tariftindakan_m.is_deleted = FALSE 
                        AND tariftindakan_m.is_active = TRUE 
                        AND tariftindakan_m.tarifparent_id IS NULL
                        AND tariftindakan_m.kamarruangan_id IS NOT NULL
                        AND tariftindakan_m.komponentarif_id <> 6
                        AND tariftindakan_m.penjamin_id = vpenjamin_id
                        AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
                ) tarif_konfig ON daftartindakan_m.daftartindakan_id = tarif_konfig.daftartindakan_id 
                    AND tarif_penjamin.tariftindakan_id IS NULL
                    AND tarif_normal.tariftindakan_id IS NULL
                    AND tarif_kelas.tariftindakan_id IS NULL
------------------------------------------------------------------------------------------------------------------
            JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
            LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
                        
                    ) AS x
                    WHERE x.kelaspelayanan_id = xkelaspelayanan_id
                    GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi, x.persen_penyulit;
                ELSE
--      RETURN QUERY 
    CREATE TEMPORARY TABLE temp_komponen_naik_kelas AS
    SELECT *FROM (
        SELECT 
            'ambulan'::text AS jenis,
            COALESCE( tarif_normal.tariftindakan_id, tarif_penjamin.tariftindakan_id, tarif_kelas.tariftindakan_id, tarif_konfig.tariftindakan_id) AS tariftindakan_id ,
            NULL::integer as ruangan_id,
            NULL::character varying as ruangan_nama,
            NULL::integer as instalasi_id,
            NULL::character varying as instalasi_nama,
            NULL::integer AS ruanganpaket_id,
            NULL::character varying AS ruanganpaket_nama,
            COALESCE(tarif_normal.perdatarif_id, tarif_penjamin.perdatarif_id, tarif_kelas.perdatarif_id,tarif_konfig.perdatarif_id) AS perdatarif_id ,
            COALESCE(tarif_normal.perdanama_sk, tarif_penjamin.perdanama_sk, tarif_kelas.perdanama_sk,tarif_konfig.perdanama_sk) AS perdanama_sk,
            COALESCE(tarif_normal.kelaspelayanan_id, tarif_penjamin.kelaspelayanan_id, tarif_kelas.kelaspelayanan_id,tarif_konfig.kelaspelayanan_id) AS kelaspelayanan_id,
            COALESCE(tarif_normal.kelaspelayanan_nama, tarif_penjamin.kelaspelayanan_nama, tarif_kelas.kelaspelayanan_nama,tarif_konfig.kelaspelayanan_nama) AS kelaspelayanan_nama,
            COALESCE(tarif_normal.penjamin_id, tarif_penjamin.penjamin_id, tarif_kelas.penjamin_id,tarif_konfig.penjamin_id) AS penjamin_id ,
            COALESCE(tarif_normal.penjamin_nama, tarif_penjamin.penjamin_nama, tarif_kelas.penjamin_nama,tarif_konfig.penjamin_nama) AS penjamin_nama,
            daftartindakan_m.kelompoktindakan_id kelompoktindakan_id,
            kelompoktindakan_m.kelompoktindakan_nama,
            daftartindakan_m.kategoritindakan_id as kategoritindakan_id,
            kategoritindakan_m.kategoritindakan_nama,
            daftartindakan_m.daftartindakan_id AS daftartindakan_id ,
            daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
            NULL::integer AS tipepaket_id,
            NULL::character varying AS tipepaket_nama,
            COALESCE(tarif_normal.komponentarif_id, tarif_penjamin.komponentarif_id, tarif_kelas.komponentarif_id,tarif_konfig.komponentarif_id) AS komponentarif_id,
            COALESCE(tarif_normal.komponentarif_nama, tarif_penjamin.komponentarif_nama, tarif_kelas.komponentarif_nama,tarif_konfig.komponentarif_nama) AS komponentarif_nama,
            COALESCE(tarif_normal.harga_tariftindakan, tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan,tarif_konfig.harga_tariftindakan) AS harga_tariftindakan ,
            COALESCE(tarif_normal.persencyto_tindakan, tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan,tarif_konfig.persencyto_tindakan) AS persencyto_tindakan ,
            COALESCE(tarif_normal.persendiskon_tindakan, tarif_penjamin.persendiskon_tindakan, 
                                tarif_kelas.persendiskon_tindakan,tarif_konfig.persendiskon_tindakan) AS persendiskon_tindakan ,
            ambulandetail_m.is_default AS is_default,
            daftartindakan_m.is_akomodasi as is_akomodasi,
            COALESCE(tarif_normal.carabayar_id, tarif_penjamin.carabayar_id, tarif_kelas.carabayar_id,tarif_konfig.carabayar_id) AS carabayar_id,
            daftartindakan_m.is_konsultasi as is_konsultasi,
            NULL::VARCHAR as  kamarruangan_nokamar,
            COALESCE(tarif_normal.kamarruangan_id, tarif_penjamin.kamarruangan_id, tarif_kelas.kamarruangan_id) as kamarruangan_id,
            ambulan_m.ambulan_id as ambulan_id,
            ambulan_m.no_polisi as no_polisi,
            COALESCE(tarif_normal.persen_penyulit, tarif_penjamin.persen_penyulit, tarif_kelas.persen_penyulit,tarif_konfig.persen_penyulit) AS persen_penyulit
   FROM daftartindakan_m           
    ------------------------------tarif normal          
            LEFT JOIN (
                SELECT 
                    'normal' AS tipe,   
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    kelaspelayanan_m.kelaspelayanan_nama,
                    tariftindakan_m.penjamin_id,
                    penjamin_m.carabayar_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.persen_penyulit,
                    tariftindakan_m.perdatarif_id,
                    perdatarif_m.perdanama_sk,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id,
                    komponentarif_m.komponentarif_nama,
                    tariftindakan_m.dokter_id,
                    tariftindakan_m.kamarruangan_id
                FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN (SELECT * FROM perdatarif_m
                                                    WHERE is_deleted=FALSE AND is_active=TRUE   
                                                            AND perda_tgl <= now() 
                                                            ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                WHERE komponentarif_m.is_deleted IS FALSE 
                    AND tariftindakan_m.is_deleted = FALSE 
                    AND tariftindakan_m.is_active = TRUE 
                    AND tariftindakan_m.tarifparent_id IS NULL
                    AND tariftindakan_m.komponentarif_id <> 6
                AND tariftindakan_m.penjamin_id = xpenjamin_id
                AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id                  
            ) tarif_normal ON daftartindakan_m.daftartindakan_id = tarif_normal.daftartindakan_id 
                    
--------------------tarif penjamin          
            LEFT JOIN (
                SELECT 
                  'penjamin' AS tipe,
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    kelaspelayanan_m.kelaspelayanan_nama,
                    tariftindakan_m.penjamin_id,
                    penjamin_m.carabayar_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.persen_penyulit,
                    tariftindakan_m.perdatarif_id,
                    perdatarif_m.perdanama_sk,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id,
                    komponentarif_m.komponentarif_nama,
                    tariftindakan_m.dokter_id,
                    tariftindakan_m.kamarruangan_id
                FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN (SELECT * FROM perdatarif_m
                                                WHERE is_deleted=FALSE AND is_active=TRUE   
                                                        AND perda_tgl <= now() 
                                                        ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                WHERE komponentarif_m.is_deleted IS FALSE
                    AND tariftindakan_m.is_deleted = FALSE 
                    AND tariftindakan_m.is_active = TRUE 
                    AND tariftindakan_m.tarifparent_id IS NULL
                    AND tariftindakan_m.komponentarif_id <> 6
                    AND tariftindakan_m.penjamin_id = vpenjamin_id
                    AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
            ) tarif_penjamin ON daftartindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                             AND tarif_normal.tariftindakan_id IS NULL
------------------kelas
            LEFT JOIN (
                SELECT 
                    'kelas' AS tipe,
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    kelaspelayanan_m.kelaspelayanan_nama,
                    tariftindakan_m.penjamin_id,
                    penjamin_m.carabayar_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.persen_penyulit,
                    tariftindakan_m.perdatarif_id,
                    perdatarif_m.perdanama_sk,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id,
                    komponentarif_m.komponentarif_nama,
                    tariftindakan_m.dokter_id,
                    tariftindakan_m.kamarruangan_id
                FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN (SELECT * FROM perdatarif_m
                                            WHERE is_deleted=FALSE AND is_active=TRUE   
                                                    AND perda_tgl <= now() 
                                                    ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                WHERE komponentarif_m.is_deleted IS FALSE
                    AND tariftindakan_m.is_deleted = FALSE 
                    AND tariftindakan_m.is_active = TRUE 
                    AND tariftindakan_m.tarifparent_id IS NULL
                    AND tariftindakan_m.komponentarif_id <> 6
                    AND tariftindakan_m.penjamin_id = vpenjamin_id
                    AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
            ) tarif_kelas ON daftartindakan_m.daftartindakan_id = tarif_kelas.daftartindakan_id 
                                        AND tarif_penjamin.tariftindakan_id IS NULL
                                        AND tarif_normal.tariftindakan_id IS NULL
            ------------------ Tarif Default Global
            LEFT JOIN (
                SELECT 
                    'global' AS tipe,
                    tariftindakan_m.daftartindakan_id,
                    tariftindakan_m.tariftindakan_id,
                    tariftindakan_m.kelaspelayanan_id,
                    kelaspelayanan_m.kelaspelayanan_nama,
                    tariftindakan_m.penjamin_id,
                    penjamin_m.carabayar_id,
                    tariftindakan_m.harga_tariftindakan,
                    tariftindakan_m.persencyto_tindakan,
                    tariftindakan_m.persendiskon_tindakan,
                    tariftindakan_m.persen_penyulit,
                    tariftindakan_m.perdatarif_id,
                    perdatarif_m.perdanama_sk,
                    penjamin_m.penjamin_nama,
                    tariftindakan_m.komponentarif_id,
                    komponentarif_m.komponentarif_nama,
                    tariftindakan_m.dokter_id
                FROM tariftindakan_m
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                JOIN (SELECT * FROM perdatarif_m
                                                WHERE is_deleted=FALSE AND is_active=TRUE   
                                                        AND perda_tgl <= now() 
                                                        ORDER BY perda_tgl DESC LIMIT 1) perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                WHERE komponentarif_m.is_deleted IS FALSE
                    AND tariftindakan_m.is_deleted = FALSE 
                    AND tariftindakan_m.is_active = TRUE 
                    AND tariftindakan_m.tarifparent_id IS NULL
                    AND tariftindakan_m.komponentarif_id <> 6
                    AND tariftindakan_m.penjamin_id = vpenjamin_id
                    AND tariftindakan_m.kelaspelayanan_id = vkelaspelayanan_id
            ) tarif_konfig ON daftartindakan_m.daftartindakan_id = tarif_konfig.daftartindakan_id 
                AND tarif_penjamin.tariftindakan_id IS NULL
                AND tarif_normal.tariftindakan_id IS NULL
                AND tarif_kelas.tariftindakan_id IS NULL
------------------------------------------------------------------------------------------------------------------
            JOIN ambulandetail_m ON daftartindakan_m.daftartindakan_id = ambulandetail_m.daftartindakan_id
                                                     AND ambulandetail_m.is_deleted = FALSE
            JOIN ambulan_m ON ambulandetail_m.ambulan_id = ambulan_m.ambulan_id
            LEFT JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
            LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
            WHERE tarif_normal.tariftindakan_id IS NOT NULL OR tarif_penjamin.tariftindakan_id IS NOT NULL OR tarif_kelas.tariftindakan_id IS NOT NULL
                    ) AS x
                    GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi,x.persen_penyulit;
                END IF;
                
                IF EXISTS (
                    SELECT 1  
                    FROM temp_komponen_naik_kelas
                    WHERE temp_komponen_naik_kelas.penjamin_id = vcekpenjamin_id
                    LIMIT 1
                )
                THEN
                            IF(xtipe = 'pelayanan')
                            THEN
                                RETURN QUERY
                                SELECT *FROM temp_komponen_naik_kelas
                                WHERE temp_komponen_naik_kelas.penjamin_id = vcekpenjamin_id;
                            ELSE
                                RETURN QUERY
                    SELECT *FROM temp_komponen_naik_kelas;
                            END IF;
                ELSE
                    RETURN QUERY
                    SELECT *FROM temp_komponen_naik_kelas;
                END IF;
                
                DROP TABLE IF EXISTS temp_komponen_naik_kelas;
            END; 
            \$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211223_100417_migrate_functiontarif cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211223_100417_migrate_functiontarif cannot be reverted.\n";

        return false;
    }
    */
}
