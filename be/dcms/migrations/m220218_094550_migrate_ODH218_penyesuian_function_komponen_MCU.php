<?php

use yii\db\Migration;

/**
 * Class m220218_094550_migrate_ODH218_penyesuian_function_komponen_MCU
 */
class m220218_094550_migrate_ODH218_penyesuian_function_komponen_MCU extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP FUNCTION IF EXISTS public.tarifkomponenrs_fn(integer, integer, integer, character varying, integer);
        ');

        $this->execute('
            CREATE OR REPLACE FUNCTION public.tarifkomponenrs_fn(
                xruangan_id integer DEFAULT 0,
                xpenjamin_id integer DEFAULT 0,
                xkelaspelayanan_id integer DEFAULT 0,
                xtipe character varying DEFAULT \'pelayanan\'::character varying,
                xspesialis_id integer DEFAULT 0)
                RETURNS TABLE(jenis text, tariftindakan_id integer, ruangan_id integer, ruangan_nama character varying, instalasi_id integer, instalasi_nama character varying, ruanganpaket_id integer, ruanganpaket_nama character varying, perdatarif_id integer, perdanama_sk character varying, kelaspelayanan_id integer, kelaspelayanan_nama character varying, penjamin_id integer, penjamin_nama character varying, kelompoktindakan_id integer, kelompoktindakan_nama character varying, kategoritindakan_id integer, kategoritindakan_nama character varying, daftartindakan_id integer, daftartindakan_nama character varying, tipepaket_id integer, tipepaket_nama character varying, komponentarif_id integer, komponentarif_nama character varying, harga_tariftindakan numeric, persencyto_tindakan numeric, persendiskon_tindakan numeric, is_default boolean, is_akomodasi boolean, carabayar_id integer, is_konsultasi boolean, kamarruangan_nokamar character varying, kamarruangan_id integer, ambulan_id integer, no_polisi character varying, kelompokpemeriksaanlab_id integer, nama_kelompok character varying, jenispemeriksaanlab_id integer, jenispemeriksaanlab_nama character varying, pemeriksaanlab_id integer, pemeriksaanlab_nama character varying, persen_penyulit numeric, kode character varying, dokter_id integer, ruangan_tarif_id integer) 
                LANGUAGE \'plpgsql\'

                COST 100
                VOLATILE 
                ROWS 1000
            AS $BODY$ 
            DECLARE 
                vpenjamin_id int4;
                vkelaspelayanan_id int4;
                vkelas_id int4;
                vkelas_nama VARCHAR;
                vis_spesialis BOOLEAN;
            BEGIN
                SELECT default_kelas, is_spesialis INTO vkelaspelayanan_id, vis_spesialis
                FROM konfigtarif_k
                WHERE konfigtarif_id = 1;
                
                IF(vis_spesialis = FALSE)
                THEN 
                    xspesialis_id := 0;
                END IF;
                
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
                
                
               IF(xtipe = \'pelayanan\')
                THEN
                    IF (xspesialis_id = 0)
                    THEN
                        RETURN QUERY 
                        SELECT *FROM (
                            SELECT 
                                \'tindakan\'::text AS jenis,
                                COALESCE( tarif_normal.tariftindakan_id, tarif_penjamin.tariftindakan_id, tarif_kelas.tariftindakan_id, tarif_konfig.tariftindakan_id) AS tariftindakan_id ,
                                tindakanruangan_mp.ruangan_id,
                                r_tindakan.ruangan_nama,
                                r_tindakan.instalasi_id,
                                ins_tindakan.instalasi_nama,
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
                                COALESCE(tarif_normal.persendiskon_tindakan, tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan,tarif_konfig.persendiskon_tindakan) AS persendiskon_tindakan ,
                                tindakanruangan_mp.is_default,
                                daftartindakan_m.is_akomodasi,
                                COALESCE(tarif_normal.carabayar_id, tarif_penjamin.carabayar_id, tarif_kelas.carabayar_id,tarif_konfig.carabayar_id) AS carabayar_id,
                                daftartindakan_m.is_konsultasi,
                                NULL::VARCHAR AS kamarruangan_nokamar,
                                NULL::int4 AS kamarruangan_id,
                                NULL::int4 AS ambulan_id,
                                NULL::VARCHAR AS no_polisi,
                                NULL::integer AS kelompokpemeriksaanlab_id,
                                NULL::character varying AS nama_kelompok,
                                NULL::integer AS jenispemeriksaanlab_id,
                                NULL::character varying AS jenispemeriksaanlab_nama,
                                NULL::integer AS pemeriksaanlab_id,
                                NULL::character varying AS pemeriksaanlab_nama,
                                COALESCE(tarif_normal.persen_penyulit, tarif_penjamin.persen_penyulit, tarif_kelas.persen_penyulit,tarif_konfig.persen_penyulit) AS persen_penyulit,
                                daftartindakan_m.daftartindakan_kode AS kode,
                                COALESCE(tarif_normal.dokter_id, tarif_penjamin.dokter_id, tarif_kelas.dokter_id,tarif_konfig.dokter_id) AS dokter_id,
                                COALESCE(tarif_normal.ruangan_tarif_id, tarif_penjamin.ruangan_tarif_id, tarif_kelas.ruangan_tarif_id,tarif_konfig.ruangan_tarif_id) AS ruangan_tarif_id
                            FROM daftartindakan_m           
                ------------------------------tarif normal          
                            LEFT JOIN (
                                SELECT 
                                    \'normal\' AS tipe,   
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
                                    tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                                    \'penjamin\' AS tipe,
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
                                    tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                                    \'kelas\' AS tipe,
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
                                    tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                                    \'global\' AS tipe,
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
                                    tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                            JOIN tindakanruangan_mp ON daftartindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id
                            JOIN ruangan_m r_tindakan ON tindakanruangan_mp.ruangan_id = r_tindakan.ruangan_id
                            JOIN instalasi_m ins_tindakan ON r_tindakan.instalasi_id = ins_tindakan.instalasi_id
                            LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id   
                            WHERE tindakanruangan_mp.is_deleted = FALSE
                            AND tarif_normal.tariftindakan_id IS NOT NULL OR tarif_penjamin.tariftindakan_id IS NOT NULL OR tarif_kelas.tariftindakan_id IS NOT NULL 
                            UNION ALL
                            SELECT 
                                \'paket\'::text AS jenis,
                                COALESCE( tarif_normal.tariftindakan_id, tarif_penjamin.tariftindakan_id, tarif_kelas.tariftindakan_id, tarif_konfig.tariftindakan_id) AS tariftindakan_id ,
                                paketruangan_mp.ruangan_id,
                                ruangan_m.ruangan_nama,
                                ruangan_m.instalasi_id,
                                instalasi_m.instalasi_nama,
                                paketruangan_mp.ruangan_id AS ruanganpaket_id,
                                ruangan_m.ruangan_namalainnya AS ruanganpaket_nama,
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
                                paketruangan_mp.is_default,
                                NULL::boolean AS is_akomodasi,
                                COALESCE(tarif_normal.carabayar_id, tarif_penjamin.carabayar_id, tarif_kelas.carabayar_id,tarif_konfig.carabayar_id) AS carabayar_id,
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
                                COALESCE(tarif_normal.persen_penyulit, tarif_penjamin.persen_penyulit, tarif_kelas.persen_penyulit,tarif_konfig.persen_penyulit) AS persen_penyulit,
                                tipepaket_m.tipepaket_kode as kode,
                                COALESCE(tarif_normal.dokter_id, tarif_penjamin.dokter_id, tarif_kelas.dokter_id,tarif_konfig.dokter_id) AS dokter_id,
                                COALESCE(tarif_normal.ruangan_tarif_id, tarif_penjamin.ruangan_tarif_id, tarif_kelas.ruangan_tarif_id,tarif_konfig.ruangan_tarif_id) AS ruangan_tarif_id
                        FROM tipepaket_m            
                ------------------------------tarif normal          
                            LEFT JOIN (
                                SELECT 
                                    \'normal\' AS tipe,   
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
                                    tariftindakan_m.daftartindakan_id,
                                    tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                                  \'penjamin\' AS tipe,
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
                                    tariftindakan_m.daftartindakan_id,
                                    tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                                    \'kelas\' AS tipe,
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
                                    tariftindakan_m.daftartindakan_id,
                                    tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                            LEFT JOIN (
                                SELECT 
                                    \'global\' AS tipe,
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
                                    tariftindakan_m.daftartindakan_id,
                                    tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                            JOIN paketruangan_mp ON tipepaket_m.tipepaket_id = paketruangan_mp.tipepaket_id
                            JOIN ruangan_m ON paketruangan_mp.ruangan_id = ruangan_m.ruangan_id
                            JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                            WHERE paketruangan_mp.is_deleted = FALSE    
                                AND tarif_normal.tariftindakan_id IS NOT NULL OR tarif_penjamin.tariftindakan_id IS NOT NULL OR tarif_kelas.tariftindakan_id IS NOT NULL
                                        
                        ) AS x
                        WHERE x.ruangan_id = xruangan_id
                        GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit, x.kode, x.dokter_id, x.ruangan_tarif_id;
                    ELSE -- QUERY spealiasi
                        RETURN QUERY 
                        SELECT *FROM (
                            SELECT 
                                \'tindakan\'::text AS jenis,
                                COALESCE( tarif_normal.tariftindakan_id, tarif_penjamin.tariftindakan_id, tarif_kelas.tariftindakan_id, tarif_konfig.tariftindakan_id) AS tariftindakan_id ,
                                -- tindakanruangan_mp.ruangan_id,
                                -- ruangan_m.ruangan_nama,
                                -- ruangan_m.instalasi_id,
                                -- instalasi_m.instalasi_nama,
                                tindakanspesialis_mp.spesialis_id AS ruangan_id,
                                NULL::VARCHAR AS ruangan_nama,
                                NULL::int4 AS instalasi_id,
                                NULL::VARCHAR AS instalasi_nama,
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
                                daftartindakan_m.daftartindakan_id,
                                daftartindakan_m.daftartindakan_nama,
                                NULL::integer AS tipepaket_id,
                                NULL::character varying AS tipepaket_nama,
                                COALESCE(tarif_normal.komponentarif_id, tarif_penjamin.komponentarif_id, tarif_kelas.komponentarif_id,tarif_konfig.komponentarif_id) AS komponentarif_id,
                                COALESCE(tarif_normal.komponentarif_nama, tarif_penjamin.komponentarif_nama, tarif_kelas.komponentarif_nama,tarif_konfig.komponentarif_nama) AS komponentarif_nama,
                                COALESCE(tarif_normal.harga_tariftindakan, tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan,tarif_konfig.harga_tariftindakan) AS harga_tariftindakan ,
                                COALESCE(tarif_normal.persencyto_tindakan, tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan,tarif_konfig.persencyto_tindakan) AS persencyto_tindakan ,
                                COALESCE(tarif_normal.persendiskon_tindakan, tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan,tarif_konfig.persendiskon_tindakan) AS persendiskon_tindakan ,
                                FALSE AS is_default,
                                daftartindakan_m.is_akomodasi,
                                COALESCE(tarif_normal.carabayar_id, tarif_penjamin.carabayar_id, tarif_kelas.carabayar_id,tarif_konfig.carabayar_id) AS carabayar_id,
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
                                COALESCE(tarif_normal.persen_penyulit, tarif_penjamin.persen_penyulit, tarif_kelas.persen_penyulit,tarif_konfig.persen_penyulit) AS persen_penyulit,
                                daftartindakan_m.daftartindakan_kode as kode,
                                COALESCE(tarif_normal.dokter_id, tarif_penjamin.dokter_id, tarif_kelas.dokter_id,tarif_konfig.dokter_id) AS dokter_id,
                                COALESCE(tarif_normal.ruangan_tarif_id, tarif_penjamin.ruangan_tarif_id, tarif_kelas.ruangan_tarif_id,tarif_konfig.ruangan_tarif_id) AS ruangan_tarif_id
                            FROM daftartindakan_m           
                                ------------------------------tarif normal          
                            LEFT JOIN (
                                SELECT 
                                    \'normal\' AS tipe,   
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
                                    tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                                  \'penjamin\' AS tipe,
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
                                    tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                                    \'kelas\' AS tipe,
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
                                    tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                                    \'global\' AS tipe,
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
                                    tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                            JOIN tindakanspesialis_mp ON daftartindakan_m.daftartindakan_id = tindakanspesialis_mp.daftartindakan_id
                                                                                AND tindakanspesialis_mp.is_deleted = FALSE
                            JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
                            -- JOIN tindakanruangan_mp ON daftartindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id
                            -- JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
                            -- JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                            LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id   
                            WHERE tarif_normal.tariftindakan_id IS NOT NULL OR tarif_penjamin.tariftindakan_id IS NOT NULL OR tarif_kelas.tariftindakan_id IS NOT NULL             
                            -- AND tindakanruangan_mp.is_deleted = FALSE
                            -- AND tindakanspesialis_mp.spesialis_id = xspesialis_id
                                
                                        
                        ) AS x
                        WHERE x.ruangan_id = xspesialis_id
                        GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit, x.kode, x.dokter_id, x.ruangan_tarif_id;
                    END IF;
                ELSEIF(xtipe = \'kamar\')
                THEN
                    RETURN QUERY 
                    SELECT *FROM (
                        SELECT      
                            \'kamar\'::text AS jenis,
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
                            COALESCE(tarif_normal.persendiskon_tindakan, tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan,tarif_konfig.persendiskon_tindakan) AS persendiskon_tindakan ,
                            NULL::BOOLEAN AS is_default,
                            daftartindakan_m.is_akomodasi as is_akomodasi,
                            COALESCE(tarif_normal.carabayar_id, tarif_penjamin.carabayar_id, tarif_kelas.carabayar_id,tarif_konfig.carabayar_id) AS carabayar_id,
                            daftartindakan_m.is_konsultasi as is_konsultasi,
                            COALESCE(tarif_normal.kamarruangan_nokamar, tarif_penjamin.kamarruangan_nokamar, tarif_kelas.kamarruangan_nokamar) as  kamarruangan_nokamar,
                            COALESCE( tarif_normal.kamarruangan_id, 
                                                tarif_penjamin.kamarruangan_id, 
                                                tarif_kelas.kamarruangan_id, 
                                                tarif_konfig.kamarruangan_id) as kamarruangan_id,
                            NULL::int4 as ambulan_id,
                            NULL::VARCHAR as no_polisi,
                            NULL::integer as kelompokpemeriksaanlab_id,
                            NULL::character varying AS nama_kelompok,
                            NULL::integer AS jenispemeriksaanlab_id,
                            NULL::character varying AS jenispemeriksaanlab_nama,
                            NULL::integer AS pemeriksaanlab_id,
                            NULL::character varying  AS pemeriksaanlab_nama,
                            COALESCE(tarif_normal.persen_penyulit, tarif_penjamin.persen_penyulit, tarif_kelas.persen_penyulit,tarif_konfig.persen_penyulit) AS persen_penyulit,
                            daftartindakan_m.daftartindakan_kode as kode,
                            COALESCE(tarif_normal.dokter_id, tarif_penjamin.dokter_id, tarif_kelas.dokter_id,tarif_konfig.dokter_id) AS dokter_id,
                            COALESCE(tarif_normal.ruangan_tarif_id, tarif_penjamin.ruangan_tarif_id, tarif_kelas.ruangan_tarif_id,tarif_konfig.ruangan_tarif_id) AS ruangan_tarif_id
                        FROM daftartindakan_m           
                ------------------------------tarif normal          
                        LEFT JOIN (
                            SELECT 
                                \'normal\' AS tipe,   
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
                                instalasi_m.instalasi_nama,
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                              \'penjamin\' AS tipe,
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
                                instalasi_m.instalasi_nama,
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                                \'kelas\' AS tipe,
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
                                instalasi_m.instalasi_nama,
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                                    \'global\' AS tipe,
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
                                    instalasi_m.instalasi_nama,
                                    tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                    WHERE x.ruangan_id = xruangan_id
            --      AND x.kelaspelayanan_id = xkelaspelayanan_id
                    GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit, x.kode, x.dokter_id, x.ruangan_tarif_id;
                ELSEIF(xtipe = \'ambulan\')
                THEN
                    RETURN QUERY 
                    SELECT *FROM (
                        SELECT 
                            \'ambulan\'::text AS jenis,
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
                            COALESCE(tarif_normal.persendiskon_tindakan, tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan,tarif_konfig.persendiskon_tindakan) AS persendiskon_tindakan ,
                            ambulandetail_m.is_default AS is_default,
                            daftartindakan_m.is_akomodasi as is_akomodasi,
                            COALESCE(tarif_normal.carabayar_id, tarif_penjamin.carabayar_id, tarif_kelas.carabayar_id,tarif_konfig.carabayar_id) AS carabayar_id,
                            daftartindakan_m.is_konsultasi as is_konsultasi,
                            NULL::VARCHAR as  kamarruangan_nokamar,
                            COALESCE(tarif_normal.kamarruangan_id, tarif_penjamin.kamarruangan_id, tarif_kelas.kamarruangan_id) as kamarruangan_id,
                            ambulan_m.ambulan_id as ambulan_id,
                            ambulan_m.no_polisi as no_polisi,
                            NULL::integer as kelompokpemeriksaanlab_id,
                            NULL::character varying AS nama_kelompok,
                            NULL::integer AS jenispemeriksaanlab_id,
                            NULL::character varying AS jenispemeriksaanlab_nama,
                            NULL::integer AS pemeriksaanlab_id,
                            NULL::character varying AS pemeriksaanlab_nama,
                            COALESCE(tarif_normal.persen_penyulit, tarif_penjamin.persen_penyulit, tarif_kelas.persen_penyulit,tarif_konfig.persen_penyulit) AS persen_penyulit,
                            daftartindakan_m.daftartindakan_kode as kode,
                            COALESCE(tarif_normal.dokter_id, tarif_penjamin.dokter_id, tarif_kelas.dokter_id,tarif_konfig.dokter_id) AS dokter_id,
                            COALESCE(tarif_normal.ruangan_tarif_id, tarif_penjamin.ruangan_tarif_id, tarif_kelas.ruangan_tarif_id,tarif_konfig.ruangan_tarif_id) AS ruangan_tarif_id
                        FROM daftartindakan_m           
                ------------------------------tarif normal          
                        LEFT JOIN (
                            SELECT 
                                \'normal\' AS tipe,   
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
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                              \'penjamin\' AS tipe,
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
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                                \'kelas\' AS tipe,
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
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                                \'global\' AS tipe,
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
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                    WHERE x.kelaspelayanan_id = xkelaspelayanan_id
                    GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit, x.kode, x.dokter_id, x.ruangan_tarif_id;
                ELSEIF(xtipe = \'penunjang\')
                THEN
                    RETURN QUERY
                    SELECT *FROM (
                        SELECT
                            \'lab\'::text AS jenis,
                            COALESCE( tarif_normal.tariftindakan_id, tarif_penjamin.tariftindakan_id, tarif_kelas.tariftindakan_id, tarif_konfig.tariftindakan_id) AS tariftindakan_id ,
                            tindakanruangan_mp.ruangan_id as ruangan_id,
                            ruangan_m.ruangan_nama as ruangan_nama,
                            ruangan_m.instalasi_id as instalasi_id,
                            instalasi_m.instalasi_nama as instalasi_nama,
                            NULL::integer AS ruanganpaket_id,
                            NULL::character varying AS ruanganpaket_nama,
                            COALESCE(tarif_normal.perdatarif_id, tarif_penjamin.perdatarif_id, tarif_kelas.perdatarif_id,tarif_konfig.perdatarif_id) AS perdatarif_id ,
                            COALESCE(tarif_normal.perdanama_sk, tarif_penjamin.perdanama_sk, tarif_kelas.perdanama_sk,tarif_konfig.perdanama_sk) AS perdanama_sk,
                            COALESCE(tarif_normal.kelaspelayanan_id, tarif_penjamin.kelaspelayanan_id, tarif_kelas.kelaspelayanan_id,tarif_konfig.kelaspelayanan_id) AS kelaspelayanan_id,
                            COALESCE(tarif_normal.kelaspelayanan_nama, tarif_penjamin.kelaspelayanan_nama, tarif_kelas.kelaspelayanan_nama,tarif_konfig.kelaspelayanan_nama) AS kelaspelayanan_nama,
                            COALESCE(tarif_normal.penjamin_id, tarif_penjamin.penjamin_id, tarif_kelas.penjamin_id,tarif_konfig.penjamin_id) AS penjamin_id ,
                            COALESCE(tarif_normal.penjamin_nama, tarif_penjamin.penjamin_nama, tarif_kelas.penjamin_nama,tarif_konfig.penjamin_nama) AS penjamin_nama,
                            NULL::integer as kelompoktindakan_id,
                            NULL::character varying as kelompoktindakan_nama,
                            NULL::integer as kategoritindakan_id,
                            NULL::character varying kategoritindakan_nama,
                            daftartindakan_m.daftartindakan_id AS daftartindakan_id ,
                            daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                            NULL::integer AS tipepaket_id,
                            NULL::character varying AS tipepaket_nama,
                            COALESCE(tarif_normal.komponentarif_id, tarif_penjamin.komponentarif_id, tarif_kelas.komponentarif_id,tarif_konfig.komponentarif_id) AS komponentarif_id,
                            COALESCE(tarif_normal.komponentarif_nama, tarif_penjamin.komponentarif_nama, tarif_kelas.komponentarif_nama,tarif_konfig.komponentarif_nama) AS komponentarif_nama,
                            COALESCE(tarif_normal.harga_tariftindakan, tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan,tarif_konfig.harga_tariftindakan) AS harga_tariftindakan ,
                            COALESCE(tarif_normal.persencyto_tindakan, tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan,tarif_konfig.persencyto_tindakan) AS persencyto_tindakan ,
                            COALESCE(tarif_normal.persendiskon_tindakan, tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan,tarif_konfig.persendiskon_tindakan) AS persendiskon_tindakan ,
                            tindakanruangan_mp.is_default AS is_default,
                            daftartindakan_m.is_akomodasi as is_akomodasi,
                            COALESCE(tarif_normal.carabayar_id, tarif_penjamin.carabayar_id, tarif_kelas.carabayar_id,tarif_konfig.carabayar_id) AS carabayar_id,
                            daftartindakan_m.is_konsultasi as is_konsultasi,
                            NULL::character varying as  kamarruangan_nokamar,
                            NULL::integer as kamarruangan_id,
                            NULL::integer as ambulan_id,
                            NULL::character varying as no_polisi,
                            pemeriksaanlab_m.kelompokpemeriksaanlab_id,
                            kelompokpemeriksaanlab_m.nama_kelompok,
                            pemeriksaanlab_m.jenispemeriksaanlab_id,
                            jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
                            pemeriksaanlab_m.pemeriksaanlab_id,
                            pemeriksaanlab_m.pemeriksaanlab_nama,
                            COALESCE(tarif_normal.persen_penyulit, tarif_penjamin.persen_penyulit, tarif_kelas.persen_penyulit,tarif_konfig.persen_penyulit) AS persen_penyulit,
                            daftartindakan_m.daftartindakan_kode as kode,
                            COALESCE(tarif_normal.dokter_id, tarif_penjamin.dokter_id, tarif_kelas.dokter_id,tarif_konfig.dokter_id) AS dokter_id,
                            COALESCE(tarif_normal.ruangan_tarif_id, tarif_penjamin.ruangan_tarif_id, tarif_kelas.ruangan_tarif_id,tarif_konfig.ruangan_tarif_id) AS ruangan_tarif_id
                        FROM daftartindakan_m           
                ------------------------------tarif normal          
                        LEFT JOIN (
                            SELECT 
                                \'normal\' AS tipe,   
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
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                              \'penjamin\' AS tipe,
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
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                                \'kelas\' AS tipe,
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
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                                \'global\' AS tipe,
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
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                        JOIN pemeriksaanlab_m ON daftartindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id 
                                                                    AND pemeriksaanlab_m.is_deleted = FALSE
                        JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
                        JOIN kelompokpemeriksaanlab_m ON pemeriksaanlab_m.kelompokpemeriksaanlab_id = kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id
                        JOIN tindakanruangan_mp ON daftartindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id
                        JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
                        JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                        WHERE tindakanruangan_mp.is_deleted = FALSE
                        AND tarif_normal.tariftindakan_id IS NOT NULL OR tarif_penjamin.tariftindakan_id IS NOT NULL OR tarif_kelas.tariftindakan_id IS NOT NULL
                        UNION ALL    
                        SELECT  
                            \'rad\'::text AS jenis,
                            COALESCE( tarif_normal.tariftindakan_id, tarif_penjamin.tariftindakan_id, tarif_kelas.tariftindakan_id, tarif_konfig.tariftindakan_id) AS tariftindakan_id ,
                            tindakanruangan_mp.ruangan_id as ruangan_id,
                            ruangan_m.ruangan_nama as ruangan_nama,
                            ruangan_m.instalasi_id as instalasi_id,
                            instalasi_m.instalasi_nama as instalasi_nama,
                            NULL::integer AS ruanganpaket_id,
                            NULL::character varying AS ruanganpaket_nama,
                            COALESCE(tarif_normal.perdatarif_id, tarif_penjamin.perdatarif_id, tarif_kelas.perdatarif_id,tarif_konfig.perdatarif_id) AS perdatarif_id ,
                            COALESCE(tarif_normal.perdanama_sk, tarif_penjamin.perdanama_sk, tarif_kelas.perdanama_sk,tarif_konfig.perdanama_sk) AS perdanama_sk,
                            COALESCE(tarif_normal.kelaspelayanan_id, tarif_penjamin.kelaspelayanan_id, tarif_kelas.kelaspelayanan_id,tarif_konfig.kelaspelayanan_id) AS kelaspelayanan_id,
                            COALESCE(tarif_normal.kelaspelayanan_nama, tarif_penjamin.kelaspelayanan_nama, tarif_kelas.kelaspelayanan_nama,tarif_konfig.kelaspelayanan_nama) AS kelaspelayanan_nama,
                            COALESCE(tarif_normal.penjamin_id, tarif_penjamin.penjamin_id, tarif_kelas.penjamin_id,tarif_konfig.penjamin_id) AS penjamin_id ,
                            COALESCE(tarif_normal.penjamin_nama, tarif_penjamin.penjamin_nama, tarif_kelas.penjamin_nama,tarif_konfig.penjamin_nama) AS penjamin_nama,
                            NULL::integer as kelompoktindakan_id,
                            NULL::character varying as kelompoktindakan_nama,
                            NULL::integer as kategoritindakan_id,
                            NULL::character varying as kategoritindakan_nama,
                            daftartindakan_m.daftartindakan_id AS daftartindakan_id ,
                            daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                            NULL::integer AS tipepaket_id,
                            NULL::character varying AS tipepaket_nama,
                            COALESCE(tarif_normal.komponentarif_id, tarif_penjamin.komponentarif_id, tarif_kelas.komponentarif_id,tarif_konfig.komponentarif_id) AS komponentarif_id,
                            COALESCE(tarif_normal.komponentarif_nama, tarif_penjamin.komponentarif_nama, tarif_kelas.komponentarif_nama,tarif_konfig.komponentarif_nama) AS komponentarif_nama,
                            COALESCE(tarif_normal.harga_tariftindakan, tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan,tarif_konfig.harga_tariftindakan) AS harga_tariftindakan ,
                            COALESCE(tarif_normal.persencyto_tindakan, tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan,tarif_konfig.persencyto_tindakan) AS persencyto_tindakan ,
                            COALESCE(tarif_normal.persendiskon_tindakan, tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan,tarif_konfig.persendiskon_tindakan) AS persendiskon_tindakan ,
                            tindakanruangan_mp.is_default AS is_default,
                            daftartindakan_m.is_akomodasi as is_akomodasi,
                            COALESCE(tarif_normal.carabayar_id, tarif_penjamin.carabayar_id, tarif_kelas.carabayar_id,tarif_konfig.carabayar_id) AS carabayar_id,
                            daftartindakan_m.is_konsultasi as is_konsultasi,
                            NULL::character varying as  kamarruangan_nokamar,
                            NULL::integer as kamarruangan_id,
                            NULL::integer as ambulan_id,
                            NULL::character varying as no_polisi,
                            pemeriksaanrad_m.kelompokpemeriksaanrad_id,
                            kelompokpemeriksaanrad_m.nama_kelompok,
                            pemeriksaanrad_m.jenispemeriksaanrad_id,
                            jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
                            pemeriksaanrad_m.pemeriksaanradiologi_id,
                            pemeriksaanrad_m.pemeriksaanrad_nama,
                            COALESCE(tarif_normal.persen_penyulit, tarif_penjamin.persen_penyulit, tarif_kelas.persen_penyulit,tarif_konfig.persen_penyulit) AS persen_penyulit,
                            daftartindakan_m.daftartindakan_kode as kode,
                            COALESCE(tarif_normal.dokter_id, tarif_penjamin.dokter_id, tarif_kelas.dokter_id,tarif_konfig.dokter_id) AS dokter_id,
                            COALESCE(tarif_normal.ruangan_tarif_id, tarif_penjamin.ruangan_tarif_id, tarif_kelas.ruangan_tarif_id,tarif_konfig.ruangan_tarif_id) AS ruangan_tarif_id
                        FROM daftartindakan_m           
                ------------------------------tarif normal          
                        LEFT JOIN (
                            SELECT 
                                \'normal\' AS tipe,   
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
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                              \'penjamin\' AS tipe,
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
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                                \'kelas\' AS tipe,
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
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                                \'global\' AS tipe,
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
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                        JOIN pemeriksaanrad_m ON daftartindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id 
                                                                    AND pemeriksaanrad_m.is_deleted = FALSE
                        JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
                        JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id
                        JOIN tindakanruangan_mp ON daftartindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id
                        JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
                        JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                        WHERE tindakanruangan_mp.is_deleted = FALSE
                            AND tarif_normal.tariftindakan_id IS NOT NULL OR tarif_penjamin.tariftindakan_id IS NOT NULL OR tarif_kelas.tariftindakan_id IS NOT NULL      
                        UNION ALL
                        SELECT  
                            \'fisio\'::text AS jenis,
                            COALESCE( tarif_normal.tariftindakan_id, tarif_penjamin.tariftindakan_id, tarif_kelas.tariftindakan_id, tarif_konfig.tariftindakan_id) AS tariftindakan_id ,
                            tindakanruangan_mp.ruangan_id as ruangan_id,
                            ruangan_m.ruangan_nama as ruangan_nama,
                            ruangan_m.instalasi_id as instalasi_id,
                            instalasi_m.instalasi_nama as instalasi_nama,
                            NULL::integer AS ruanganpaket_id,
                            NULL::character varying AS ruanganpaket_nama,
                            COALESCE(tarif_normal.perdatarif_id, tarif_penjamin.perdatarif_id, tarif_kelas.perdatarif_id,tarif_konfig.perdatarif_id) AS perdatarif_id ,
                            COALESCE(tarif_normal.perdanama_sk, tarif_penjamin.perdanama_sk, tarif_kelas.perdanama_sk,tarif_konfig.perdanama_sk) AS perdanama_sk,
                            COALESCE(tarif_normal.kelaspelayanan_id, tarif_penjamin.kelaspelayanan_id, tarif_kelas.kelaspelayanan_id,tarif_konfig.kelaspelayanan_id) AS kelaspelayanan_id,
                            COALESCE(tarif_normal.kelaspelayanan_nama, tarif_penjamin.kelaspelayanan_nama, tarif_kelas.kelaspelayanan_nama,tarif_konfig.kelaspelayanan_nama) AS kelaspelayanan_nama,
                            COALESCE(tarif_normal.penjamin_id, tarif_penjamin.penjamin_id, tarif_kelas.penjamin_id,tarif_konfig.penjamin_id) AS penjamin_id ,
                            COALESCE(tarif_normal.penjamin_nama, tarif_penjamin.penjamin_nama, tarif_kelas.penjamin_nama,tarif_konfig.penjamin_nama) AS penjamin_nama,
                            NULL::integer as kelompoktindakan_id,
                            NULL::character varying as kelompoktindakan_nama,
                            NULL::integer as kategoritindakan_id,
                            NULL::character varying as kategoritindakan_nama,
                            daftartindakan_m.daftartindakan_id AS daftartindakan_id ,
                            daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                            NULL::integer AS tipepaket_id,
                            NULL::character varying AS tipepaket_nama,
                            COALESCE(tarif_normal.komponentarif_id, tarif_penjamin.komponentarif_id, tarif_kelas.komponentarif_id,tarif_konfig.komponentarif_id) AS komponentarif_id,
                            COALESCE(tarif_normal.komponentarif_nama, tarif_penjamin.komponentarif_nama, tarif_kelas.komponentarif_nama,tarif_konfig.komponentarif_nama) AS komponentarif_nama,
                            COALESCE(tarif_normal.harga_tariftindakan, tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan,tarif_konfig.harga_tariftindakan) AS harga_tariftindakan ,
                            COALESCE(tarif_normal.persencyto_tindakan, tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan,tarif_konfig.persencyto_tindakan) AS persencyto_tindakan ,
                            COALESCE(tarif_normal.persendiskon_tindakan, tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan,tarif_konfig.persendiskon_tindakan) AS persendiskon_tindakan ,
                            tindakanruangan_mp.is_default AS is_default,
                            daftartindakan_m.is_akomodasi as is_akomodasi,
                            COALESCE(tarif_normal.carabayar_id, tarif_penjamin.carabayar_id, tarif_kelas.carabayar_id,tarif_konfig.carabayar_id) AS carabayar_id,
                            daftartindakan_m.is_konsultasi as is_konsultasi,
                            NULL::character varying as  kamarruangan_nokamar,
                            NULL::integer as kamarruangan_id,
                            NULL::integer as ambulan_id,
                            NULL::character varying as no_polisi,
                            pemeriksaanfisio_m.kelompokpemeriksaanfisio_id,
                            kelompokpemeriksaanfisio_m.nama_kelompok,
                            pemeriksaanfisio_m.jenispemeriksaanfisio_id,
                            jenispemeriksaanfisio_m.jenispemeriksaanfisio_nama,
                            pemeriksaanfisio_m.pemeriksaanfisio_id,
                            pemeriksaanfisio_m.pemeriksaanfisio_nama,
                            COALESCE(tarif_normal.persen_penyulit, tarif_penjamin.persen_penyulit, tarif_kelas.persen_penyulit,tarif_konfig.persen_penyulit) AS persen_penyulit,
                            daftartindakan_m.daftartindakan_kode as kode,
                            COALESCE(tarif_normal.dokter_id, tarif_penjamin.dokter_id, tarif_kelas.dokter_id,tarif_konfig.dokter_id) AS dokter_id,
                            COALESCE(tarif_normal.ruangan_tarif_id, tarif_penjamin.ruangan_tarif_id, tarif_kelas.ruangan_tarif_id,tarif_konfig.ruangan_tarif_id) AS ruangan_tarif_id
                        FROM daftartindakan_m           
                ------------------------------tarif normal          
                        LEFT JOIN (
                            SELECT 
                                \'normal\' AS tipe,   
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
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                              \'penjamin\' AS tipe,
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
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                                \'kelas\' AS tipe,
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
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                                \'global\' AS tipe,
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
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                        JOIN pemeriksaanfisio_m ON daftartindakan_m.daftartindakan_id = pemeriksaanfisio_m.daftartindakan_id 
                                                                        AND pemeriksaanfisio_m.is_deleted = FALSE
                        JOIN jenispemeriksaanfisio_m ON pemeriksaanfisio_m.jenispemeriksaanfisio_id = jenispemeriksaanfisio_m.jenispemeriksaanfisio_id
                        JOIN kelompokpemeriksaanfisio_m ON pemeriksaanfisio_m.kelompokpemeriksaanfisio_id = kelompokpemeriksaanfisio_m.kelompokpemeriksaanfisio_id
                        JOIN tindakanruangan_mp ON daftartindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id
                        JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
                        JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                        WHERE tindakanruangan_mp.is_deleted = FALSE
                            AND tarif_normal.tariftindakan_id IS NOT NULL OR tarif_penjamin.tariftindakan_id IS NOT NULL OR tarif_kelas.tariftindakan_id IS NOT NULL
                        UNION ALL
                        SELECT  
                            \'operasi\'::text AS jenis,
                            COALESCE( tarif_normal.tariftindakan_id, tarif_penjamin.tariftindakan_id, tarif_kelas.tariftindakan_id, tarif_konfig.tariftindakan_id) AS tariftindakan_id ,
                            tindakanruangan_mp.ruangan_id as ruangan_id,
                            ruangan_m.ruangan_nama as ruangan_nama,
                            ruangan_m.instalasi_id as instalasi_id,
                            instalasi_m.instalasi_nama as instalasi_nama,
                            NULL::integer AS ruanganpaket_id,
                            NULL::character varying AS ruanganpaket_nama,
                            COALESCE(tarif_normal.perdatarif_id, tarif_penjamin.perdatarif_id, tarif_kelas.perdatarif_id,tarif_konfig.perdatarif_id) AS perdatarif_id ,
                            COALESCE(tarif_normal.perdanama_sk, tarif_penjamin.perdanama_sk, tarif_kelas.perdanama_sk,tarif_konfig.perdanama_sk) AS perdanama_sk,
                            COALESCE(tarif_normal.kelaspelayanan_id, tarif_penjamin.kelaspelayanan_id, tarif_kelas.kelaspelayanan_id,tarif_konfig.kelaspelayanan_id) AS kelaspelayanan_id,
                            COALESCE(tarif_normal.kelaspelayanan_nama, tarif_penjamin.kelaspelayanan_nama, tarif_kelas.kelaspelayanan_nama,tarif_konfig.kelaspelayanan_nama) AS kelaspelayanan_nama,
                            COALESCE(tarif_normal.penjamin_id, tarif_penjamin.penjamin_id, tarif_kelas.penjamin_id,tarif_konfig.penjamin_id) AS penjamin_id ,
                            COALESCE(tarif_normal.penjamin_nama, tarif_penjamin.penjamin_nama, tarif_kelas.penjamin_nama,tarif_konfig.penjamin_nama) AS penjamin_nama,
                            NULL::integer as kelompoktindakan_id,
                            NULL::character varying as kelompoktindakan_nama,
                            NULL::integer as kategoritindakan_id,
                            NULL::character varying as kategoritindakan_nama,
                            daftartindakan_m.daftartindakan_id AS daftartindakan_id ,
                            daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                            NULL::integer AS tipepaket_id,
                            NULL::character varying AS tipepaket_nama,
                            COALESCE(tarif_normal.komponentarif_id, tarif_penjamin.komponentarif_id, tarif_kelas.komponentarif_id,tarif_konfig.komponentarif_id) AS komponentarif_id,
                            COALESCE(tarif_normal.komponentarif_nama, tarif_penjamin.komponentarif_nama, tarif_kelas.komponentarif_nama,tarif_konfig.komponentarif_nama) AS komponentarif_nama,
                            COALESCE(tarif_normal.harga_tariftindakan, tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan,tarif_konfig.harga_tariftindakan) AS harga_tariftindakan ,
                            COALESCE(tarif_normal.persencyto_tindakan, tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan,tarif_konfig.persencyto_tindakan) AS persencyto_tindakan ,
                            COALESCE(tarif_normal.persendiskon_tindakan, tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan,tarif_konfig.persendiskon_tindakan) AS persendiskon_tindakan ,
                            tindakanruangan_mp.is_default AS is_default,
                            daftartindakan_m.is_akomodasi as is_akomodasi,
                            COALESCE(tarif_normal.carabayar_id, tarif_penjamin.carabayar_id, tarif_kelas.carabayar_id,tarif_konfig.carabayar_id) AS carabayar_id,
                            daftartindakan_m.is_konsultasi as is_konsultasi,
                            NULL::character varying as  kamarruangan_nokamar,
                            NULL::integer as kamarruangan_id,
                            NULL::integer as ambulan_id,
                            NULL::character varying as no_polisi,
                            operasi_m.golonganoperasi_id,
                            golonganoperasi_m.golonganoperasi_nama,
                            operasi_m.kegiatanoperasi_id,
                            kegiatanoperasi_m.kegiatanoperasi_nama,
                            operasi_m.operasi_id,
                            operasi_m.operasi_nama,
                            COALESCE(tarif_normal.persen_penyulit, tarif_penjamin.persen_penyulit, tarif_kelas.persen_penyulit,tarif_konfig.persen_penyulit) AS persen_penyulit,
                            daftartindakan_m.daftartindakan_kode as kode,
                            COALESCE(tarif_normal.dokter_id, tarif_penjamin.dokter_id, tarif_kelas.dokter_id,tarif_konfig.dokter_id) AS dokter_id,
                            COALESCE(tarif_normal.ruangan_tarif_id, tarif_penjamin.ruangan_tarif_id, tarif_kelas.ruangan_tarif_id,tarif_konfig.ruangan_tarif_id) AS ruangan_tarif_id
                        FROM daftartindakan_m           
                ------------------------------tarif normal          
                        LEFT JOIN (
                            SELECT 
                                \'normal\' AS tipe,   
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
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                              \'penjamin\' AS tipe,
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
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                                \'kelas\' AS tipe,
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
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                                \'global\' AS tipe,
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
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                        JOIN operasi_m ON daftartindakan_m.daftartindakan_id = operasi_m.daftartindakan_id 
                                                     AND operasi_m.is_deleted = FALSE
                        JOIN golonganoperasi_m ON operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id
                        JOIN kegiatanoperasi_m ON operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id
                        JOIN tindakanruangan_mp ON daftartindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id
                        JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
                        JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                        WHERE tindakanruangan_mp.is_deleted = FALSE
                            AND tarif_normal.tariftindakan_id IS NOT NULL OR tarif_penjamin.tariftindakan_id IS NOT NULL OR tarif_kelas.tariftindakan_id IS NOT NULL
                        UNION ALL
                        SELECT 
                            CASE
                                WHEN (ruangan_m.instalasi_id = 4) THEN \'paket_lab\'
                                WHEN (ruangan_m.instalasi_id = 5) THEN \'paket_rad\'
                                WHEN (ruangan_m.instalasi_id = 21) THEN \'paket_mcu\'
                                WHEN (ruangan_m.instalasi_id = 7) THEN \'paket_fisio\'
                                ELSE \'paket_operasi\'
                            END AS jenis,
                            COALESCE( tarif_normal.tariftindakan_id, tarif_penjamin.tariftindakan_id, tarif_kelas.tariftindakan_id, tarif_konfig.tariftindakan_id) AS tariftindakan_id ,
                            paketruangan_mp.ruangan_id as ruangan_id,
                            ruangan_m.ruangan_nama as ruangan_nama,
                            ruangan_m.instalasi_id as instalasi_id,
                            null::character varying AS instalasi_nama,
                            NULL::integer AS ruanganpaket_id,
                            NULL::character varying AS ruanganpaket_nama,
                            COALESCE(tarif_normal.perdatarif_id, tarif_penjamin.perdatarif_id, tarif_kelas.perdatarif_id,tarif_konfig.perdatarif_id) AS perdatarif_id ,
                            COALESCE(tarif_normal.perdanama_sk, tarif_penjamin.perdanama_sk, tarif_kelas.perdanama_sk,tarif_konfig.perdanama_sk) AS perdanama_sk,
                            COALESCE(tarif_normal.kelaspelayanan_id, tarif_penjamin.kelaspelayanan_id, tarif_kelas.kelaspelayanan_id,tarif_konfig.kelaspelayanan_id) AS kelaspelayanan_id,
                            COALESCE(tarif_normal.kelaspelayanan_nama, tarif_penjamin.kelaspelayanan_nama, tarif_kelas.kelaspelayanan_nama,tarif_konfig.kelaspelayanan_nama) AS kelaspelayanan_nama,
                            COALESCE(tarif_normal.penjamin_id, tarif_penjamin.penjamin_id, tarif_kelas.penjamin_id,tarif_konfig.penjamin_id) AS penjamin_id ,
                            COALESCE(tarif_normal.penjamin_nama, tarif_penjamin.penjamin_nama, tarif_kelas.penjamin_nama,tarif_konfig.penjamin_nama) AS penjamin_nama,
                            null::integer as kelompoktindakan_id,
                            null::character varying  as kelompoktindakan_nama,
                            null::integer as kategoritindakan_id,
                            null::character varying  as kategoritindakan_nama,
                            COALESCE(tarif_normal.daftartindakan_id, tarif_penjamin.daftartindakan_id, tarif_kelas.daftartindakan_id)  AS daftartindakan_id ,
                            NULL AS daftartindakan_nama,
                            tipepaket_m.tipepaket_id  AS tipepaket_id,
                            tipepaket_m.tipepaket_nama AS tipepaket_nama,
                            COALESCE(tarif_normal.komponentarif_id, tarif_penjamin.komponentarif_id, tarif_kelas.komponentarif_id,tarif_konfig.komponentarif_id) AS komponentarif_id,
                            COALESCE(tarif_normal.komponentarif_nama, tarif_penjamin.komponentarif_nama, tarif_kelas.komponentarif_nama,tarif_konfig.komponentarif_nama) AS komponentarif_nama,
                            COALESCE(tarif_normal.harga_tariftindakan, tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan,tarif_konfig.harga_tariftindakan) AS harga_tariftindakan ,
                            COALESCE(tarif_normal.persencyto_tindakan, tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan,tarif_konfig.persencyto_tindakan) AS persencyto_tindakan ,
                            COALESCE(tarif_normal.persendiskon_tindakan, tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan,tarif_konfig.persendiskon_tindakan) AS persendiskon_tindakan ,
                            paketruangan_mp.is_default AS is_default,
                            null::boolean as is_akomodasi,
                            COALESCE(tarif_normal.carabayar_id, tarif_penjamin.carabayar_id, tarif_kelas.carabayar_id,tarif_konfig.carabayar_id) AS carabayar_id,
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
                            COALESCE(tarif_normal.persen_penyulit, tarif_penjamin.persen_penyulit, tarif_kelas.persen_penyulit,tarif_konfig.persen_penyulit) AS persen_penyulit,
                            tipepaket_m.tipepaket_kode  as kode,
                            COALESCE(tarif_normal.dokter_id, tarif_penjamin.dokter_id, tarif_kelas.dokter_id,tarif_konfig.dokter_id) AS dokter_id,
                            COALESCE(tarif_normal.ruangan_tarif_id, tarif_penjamin.ruangan_tarif_id, tarif_kelas.ruangan_tarif_id,tarif_konfig.ruangan_tarif_id) AS ruangan_tarif_id
                        FROM tipepaket_m            
                ------------------------------tarif normal          
                        LEFT JOIN (
                            SELECT 
                                \'normal\' AS tipe,   
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
                                tariftindakan_m.daftartindakan_id,
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                              \'penjamin\' AS tipe,
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
                                tariftindakan_m.daftartindakan_id,
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                                \'kelas\' AS tipe,
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
                                tariftindakan_m.daftartindakan_id,
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                        LEFT JOIN (
                            SELECT 
                                \'global\' AS tipe,
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
                                tariftindakan_m.daftartindakan_id,
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                        JOIN paketruangan_mp ON tipepaket_m.tipepaket_id = paketruangan_mp.tipepaket_id
                        JOIN ruangan_m ON paketruangan_mp.ruangan_id = ruangan_m.ruangan_id
                        JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                        WHERE paketruangan_mp.is_deleted = FALSE
                        AND tarif_normal.tariftindakan_id IS NOT NULL OR tarif_penjamin.tariftindakan_id IS NOT NULL OR tarif_kelas.tariftindakan_id IS NOT NULL
                        
                    ) x
                    WHERE x.ruangan_id = xruangan_id
                    GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit, x.kode, x.dokter_id, x.ruangan_tarif_id;
                ELSEIF(xtipe = \'makanan\')
                THEN
                    RETURN QUERY 
                    SELECT *FROM (
                        SELECT
                            \'makanan\'::text AS jenis,
                            COALESCE( tarif_normal.tariftindakan_id, tarif_penjamin.tariftindakan_id, tarif_kelas.tariftindakan_id, tarif_konfig.tariftindakan_id) AS tariftindakan_id ,
                            tindakanruangan_mp.ruangan_id as ruangan_id,
                            ruangan_m.ruangan_nama as ruangan_nama,
                            ruangan_m.instalasi_id as instalasi_id,
                            null::character varying as instalasi_nama,
                            NULL::integer AS ruanganpaket_id,
                            NULL::character varying AS ruanganpaket_nama,
                            COALESCE(tarif_normal.perdatarif_id, tarif_penjamin.perdatarif_id, tarif_kelas.perdatarif_id,tarif_konfig.perdatarif_id) AS perdatarif_id ,
                            COALESCE(tarif_normal.perdanama_sk, tarif_penjamin.perdanama_sk, tarif_kelas.perdanama_sk,tarif_konfig.perdanama_sk) AS perdanama_sk,
                            COALESCE(tarif_normal.kelaspelayanan_id, tarif_penjamin.kelaspelayanan_id, tarif_kelas.kelaspelayanan_id,tarif_konfig.kelaspelayanan_id) AS kelaspelayanan_id,
                            COALESCE(tarif_normal.kelaspelayanan_nama, tarif_penjamin.kelaspelayanan_nama, tarif_kelas.kelaspelayanan_nama,tarif_konfig.kelaspelayanan_nama) AS kelaspelayanan_nama,
                            COALESCE(tarif_normal.penjamin_id, tarif_penjamin.penjamin_id, tarif_kelas.penjamin_id,tarif_konfig.penjamin_id) AS penjamin_id ,
                            COALESCE(tarif_normal.penjamin_nama, tarif_penjamin.penjamin_nama, tarif_kelas.penjamin_nama,tarif_konfig.penjamin_nama) AS penjamin_nama,
                            NULL::integer as kelompoktindakan_id,
                            NULL::character varying as kelompoktindakan_nama,
                            NULL::integer as kategoritindakan_id,
                            NULL::character varying kategoritindakan_nama,
                            daftartindakan_m.daftartindakan_id AS daftartindakan_id ,
                            daftartindakan_m.daftartindakan_nama as daftartindakan_nama,
                            NULL::integer AS tipepaket_id,
                            NULL::character varying AS tipepaket_nama,
                            COALESCE(tarif_normal.komponentarif_id, tarif_penjamin.komponentarif_id, tarif_kelas.komponentarif_id,tarif_konfig.komponentarif_id) AS komponentarif_id,
                            COALESCE(tarif_normal.komponentarif_nama, tarif_penjamin.komponentarif_nama, tarif_kelas.komponentarif_nama,tarif_konfig.komponentarif_nama) AS komponentarif_nama,
                            COALESCE(tarif_normal.harga_tariftindakan, tarif_penjamin.harga_tariftindakan, tarif_kelas.harga_tariftindakan,tarif_konfig.harga_tariftindakan) AS harga_tariftindakan ,
                            COALESCE(tarif_normal.persencyto_tindakan, tarif_penjamin.persencyto_tindakan, tarif_kelas.persencyto_tindakan,tarif_konfig.persencyto_tindakan) AS persencyto_tindakan ,
                            COALESCE(tarif_normal.persendiskon_tindakan, tarif_penjamin.persendiskon_tindakan, tarif_kelas.persendiskon_tindakan,tarif_konfig.persendiskon_tindakan) AS persendiskon_tindakan ,
                            tindakanruangan_mp.is_default AS is_default,
                            daftartindakan_m.is_akomodasi as is_akomodasi,
                            COALESCE(tarif_normal.carabayar_id, tarif_penjamin.carabayar_id, tarif_kelas.carabayar_id,tarif_konfig.carabayar_id) AS carabayar_id,
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
                            COALESCE(tarif_normal.persen_penyulit, tarif_penjamin.persen_penyulit, tarif_kelas.persen_penyulit,tarif_konfig.persen_penyulit) AS persen_penyulit,
                            daftartindakan_m.daftartindakan_kode as kode,
                            COALESCE(tarif_normal.dokter_id, tarif_penjamin.dokter_id, tarif_kelas.dokter_id,tarif_konfig.dokter_id) AS dokter_id,
                            COALESCE(tarif_normal.ruangan_tarif_id, tarif_penjamin.ruangan_tarif_id, tarif_kelas.ruangan_tarif_id,tarif_konfig.ruangan_tarif_id) AS ruangan_tarif_id
                        FROM daftartindakan_m           
                ------------------------------tarif normal          
                        LEFT JOIN (
                            SELECT 
                                \'normal\' AS tipe,   
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
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                              \'penjamin\' AS tipe,
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
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                                \'kelas\' AS tipe,
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
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                                \'global\' AS tipe,
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
                                tariftindakan_m.ruangan_id AS ruangan_tarif_id
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
                        JOIN makanandiet_m ON daftartindakan_m.daftartindakan_id = makanandiet_m.daftartindakan_id 
                                                             AND makanandiet_m.is_deleted = FALSE
                        JOIN jenisdiet_m ON makanandiet_m.jenisdiet_id = jenisdiet_m.jenisdiet_id
                        JOIN tindakanruangan_mp ON daftartindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id
                        JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
                        JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                        WHERE tindakanruangan_mp.is_deleted = FALSE
                            AND tarif_normal.tariftindakan_id IS NOT NULL OR tarif_penjamin.tariftindakan_id IS NOT NULL OR tarif_kelas.tariftindakan_id IS NOT NULL
                   
                            ) AS x
                    WHERE  x.ruangan_id = xruangan_id
                    GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit, x.kode, x.dokter_id, x.ruangan_tarif_id;
                END IF;
            END; 
            $BODY$;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220218_094550_migrate_ODH218_penyesuian_function_komponen_MCU cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220218_094550_migrate_ODH218_penyesuian_function_komponen_MCU cannot be reverted.\n";

        return false;
    }
    */
}
