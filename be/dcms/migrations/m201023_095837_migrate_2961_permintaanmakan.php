<?php

use yii\db\Migration;

/**
 * Class m201023_095837_migrate_2961_permintaanmakan
 */
class m201023_095837_migrate_2961_permintaanmakan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
$this->execute('ALTER TABLE "public"."makanandiet_m" ADD COLUMN IF NOT EXISTS "jenisdiet_id" int4;');  
        $this->execute('ALTER TABLE "public"."makanandiet_m" ADD COLUMN IF NOT EXISTS "daftartindakan_id" int4;'); 
        
        $this->execute('TRUNCATE TABLE jenisdiet_m;');  
        $this->execute('ALTER SEQUENCE jenisdiet_m_jenisdiet_id_seq RESTART WITH 1;');  
        
        $this->execute('
            INSERT INTO jenisdiet_m(jenisdiet_id, jenisdiet_kode, jenisdiet_nama, jenisdiet_keterangan)
            VALUES (1, \'LOS\', \'Food & Beverages\', \'Food & Beverages\') ;'
        );  

        $this->execute('TRUNCATE TABLE makanandiet_m;');  
        $this->execute('ALTER SEQUENCE makanandiet_m_makanandiet_id_seq RESTART WITH 1;');  

        $this->execute('
            INSERT INTO makanandiet_m (jenisdiet_id, daftartindakan_id, makanandiet_kode, makanandiet_nama)
            SELECT 1, daftartindakan_id, daftartindakan_kode, daftartindakan_nama
            FROM daftartindakan_m
            WHERE kelompoktindakan_id = 33;
        ');  

        $this->execute('
            DELETE FROM tindakanruangan_mp
            WHERE ruangan_id IN (
                SELECT default_ruangan 
                FROM konfigtarif_k 
                WHERE konfigtarif_k.konfigtarif_id = 1
            )
            AND daftartindakan_id IN (
                SELECT daftartindakan_id 
                FROM daftartindakan_m 
                WHERE kelompoktindakan_id = 33
            );
        ');  
        
        $this->execute('
            INSERT INTO tindakanruangan_mp(
                ruangan_id, daftartindakan_id
            )
            SELECT default_ruangan, daftartindakan_id
            FROM daftartindakan_m
            JOIN konfigtarif_k ON konfigtarif_k.konfigtarif_id = 1
            WHERE kelompoktindakan_id = 33;
        ');  
        
        $this->execute('DROP VIEW "public"."infopermintaanmakan_v";');

        $this->execute('
            CREATE VIEW "public"."infopermintaanmakan_v" AS  
            SELECT 
                permintaanmakan_t.permintaaanmakan_id,
                permintaanmakan_t.pendaftaran_id,
                pendaftaran_t.no_pendaftaran,
                pendaftaran_t.tgl_pendaftaran,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                lookup_m.lookup_name AS jenis_kelamin,
                lookup_m.lookup_value AS jenis_kelamin_value,
                pasien_m.tanggal_lahir,
                pendaftaran_t.umur,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                permintaanmakan_t.no_permintaanmakan,
                pegawai_m.nama_pegawai,
                kelaspelayanan_m.kelaspelayanan_nama,
                permintaanmakan_t.tgl_permintaanmakan,
                permintaanmakan_t.status AS status_permintaanmakan,
                CASE
                    WHEN (permintaanmakan_t.status = 1) THEN \'PROSES\'::text
                    ELSE \'BATAL\'::text
                END AS status_permintaan,
                permintaanmakan_t.no_pembatalan,
                permintaanmakan_t.waktu_pembatalan,
                permintaanmakan_t.alasan_pembatalan,
                ruangan_m.ruangan_nama,
                kamarruangan_m.kamarruangan_nokamar,
                ruangan_m.ruangan_id,
                kamarruangan_m.kamarruangan_id,
                carabayar_m.carabayar_id,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_nama,
                dok_dpjp.nama_pegawai AS dok_dpjp,
                kamartempattidur_m.no_tempattidur,
                pemesan.nama_pegawai AS nama_pemesan
                FROM ((((((((((((((permintaanmakan_t
                JOIN pendaftaran_t ON ((permintaanmakan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
                JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
                JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
                JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
                JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
                JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
                JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
                JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
                JOIN pegawai_m ON ((permintaanmakan_t.peg_pemesan_id = pegawai_m.pegawai_id)))
                JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
                JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
                JOIN pegawai_m dok_dpjp ON ((pasienadmisi_t.pegawai_id = dok_dpjp.pegawai_id)))
                JOIN pegawai_m pemesan ON ((permintaanmakan_t.peg_pemesan_id = pemesan.pegawai_id)))
                LEFT JOIN lookup_m ON (((pasien_m.jeniskelamin)::integer = lookup_m.lookup_id)))
            UNION ALL
            SELECT 
                permintaanmakan_t.permintaaanmakan_id,
                permintaanmakan_t.pendaftaran_id,
                pendaftaran_t.no_pendaftaran,
                pendaftaran_t.tgl_pendaftaran,
                pasien_m.no_rekam_medik,
                pasien_m.nama_pasien,
                lookup_m.lookup_name AS jenis_kelamin,
                lookup_m.lookup_value AS jenis_kelamin_value,
                pasien_m.tanggal_lahir,
                pendaftaran_t.umur,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                permintaanmakan_t.no_permintaanmakan,
                pegawai_m.nama_pegawai,
                kelaspelayanan_m.kelaspelayanan_nama,
                permintaanmakan_t.tgl_permintaanmakan,
                permintaanmakan_t.status AS status_permintaanmakan,
                CASE
                WHEN (permintaanmakan_t.status = 1) THEN \'PROSES\'::text
                ELSE \'BATAL\'::text
                END AS status_permintaan,
                permintaanmakan_t.no_pembatalan,
                permintaanmakan_t.waktu_pembatalan,
                permintaanmakan_t.alasan_pembatalan, 
                ruangan_m.ruangan_nama,
                ruangan_m.ruangan_nama AS kamarruangan_nokamar,
                ruangan_m.ruangan_id,
                ruangan_m.ruangan_id AS kamarruangan_id,
                carabayar_m.carabayar_id,
                carabayar_m.carabayar_nama,
                penjamin_m.penjamin_nama,
                dok_dpjp.nama_pegawai AS dok_dpjp,
                \'\' AS no_tempattidur,
                pemesan.nama_pegawai AS nama_pemesan
            FROM (((((((((((permintaanmakan_t
            JOIN pendaftaran_t ON ((permintaanmakan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
            JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN pegawai_m ON ((permintaanmakan_t.peg_pemesan_id = pegawai_m.pegawai_id)))
            JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN pegawai_m dok_dpjp ON ((pendaftaran_t.pegawai_id = dok_dpjp.pegawai_id)))
            JOIN pegawai_m pemesan ON ((permintaanmakan_t.peg_pemesan_id = pemesan.pegawai_id)))
            LEFT JOIN lookup_m ON (((pasien_m.jeniskelamin)::integer = lookup_m.lookup_id)));
        ');

        $this->execute('ALTER TABLE "public"."infopermintaanmakan_v" OWNER TO "postgres";');
        
        $this->execute('DROP VIEW IF EXISTS menudiet_v;');

        $this->execute('
            CREATE VIEW "menudiet_v" AS  
            SELECT 
                jenisdiet_m.jenisdiet_id, 
                jenisdiet_m.jenisdiet_nama,
                makanandiet_m.makanandiet_id,
                makanandiet_m.daftartindakan_id,
                makanandiet_m.makanandiet_kode,
                makanandiet_m.makanandiet_nama,
                makanandiet_m.makanandiet_keterangan,
                row_number() OVER (ORDER BY jenisdiet_m.jenisdiet_id) AS row_number,
                makanandiet_m.is_active
            FROM makanandiet_m 
            JOIN jenisdiet_m ON makanandiet_m.jenisdiet_id = jenisdiet_m.jenisdiet_id
            WHERE (makanandiet_m.is_deleted = false);
        ');

        $this->execute('ALTER TABLE "public"."menudiet_v" OWNER TO "postgres";');
        
        $this->execute('
            CREATE OR REPLACE FUNCTION public.tariftotalrs_fn(
                xruangan_id integer DEFAULT 0,
                xpenjamin_id integer DEFAULT 0,
                xkelaspelayanan_id integer DEFAULT 0,
                xtipe character varying DEFAULT \'pelayanan\'::character varying)
                RETURNS TABLE(jenis text, tariftindakan_id integer, ruangan_id integer, ruangan_nama character varying, instalasi_id integer, instalasi_nama character varying, ruanganpaket_id integer, ruanganpaket_nama character varying, perdatarif_id integer, perdanama_sk character varying, kelaspelayanan_id integer, kelaspelayanan_nama character varying, penjamin_id integer, penjamin_nama character varying, kelompoktindakan_id integer, kelompoktindakan_nama character varying, kategoritindakan_id integer, kategoritindakan_nama character varying, daftartindakan_id integer, daftartindakan_nama character varying, tipepaket_id integer, tipepaket_nama character varying, komponentarif_id integer, komponentarif_nama character varying, harga_tariftindakan numeric, persencyto_tindakan numeric, persendiskon_tindakan numeric, is_default boolean, is_akomodasi boolean, carabayar_id integer, is_konsultasi boolean, kamarruangan_nokamar character varying, kamarruangan_id integer, ambulan_id integer, no_polisi character varying, kelompokpemeriksaanlab_id integer, nama_kelompok character varying, jenispemeriksaanlab_id integer, jenispemeriksaanlab_nama character varying, pemeriksaanlab_id integer, pemeriksaanlab_nama character varying, persen_penyulit numeric) 
                LANGUAGE \'plpgsql\'

                COST 100
                VOLATILE 
                ROWS 1000
            AS $BODY$ 
            DECLARE vpenjamin_id int4;
            BEGIN
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
                            tariftindakan_m.kelaspelayanan_id,
                            kelaspelayanan_m.kelaspelayanan_nama,
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
                            COALESCE( tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                            COALESCE( tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                            COALESCE( tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
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
                             ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
                            WHERE tindakanruangan_mp.is_deleted = false 
                            AND komponentarif_m.is_deleted IS FALSE
                            AND perdatarif_m.is_active = true 
                            AND tariftindakan_m.is_deleted = false 
                            AND tariftindakan_m.is_active = true 
                            AND tariftindakan_m.tarifparent_id IS NULL
                            AND tariftindakan_m.komponentarif_id = 6
                            AND tariftindakan_m.penjamin_id = vpenjamin_id
                    UNION ALL
                     SELECT \'paket\'::text AS jenis,
                            COALESCE( tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
                            paketruangan_mp.ruangan_id,
                            r_paket.ruangan_nama,
                            r_paket.instalasi_id,
                            ins_paket.instalasi_nama,
                            paketruangan_mp.ruangan_id AS ruanganpaket_id,
                            r_paket.ruangan_namalainnya AS ruanganpaket_nama,
                            COALESCE( tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id ,
                            perdatarif_m.perdanama_sk,
                            tariftindakan_m.kelaspelayanan_id,
                            kelaspelayanan_m.kelaspelayanan_nama,
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
                            COALESCE( tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                            COALESCE( tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                            COALESCE( tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
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
                                        tariftindakan_m.tipepaket_id
                                    FROM tariftindakan_m
                                                            JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
                                                                                            AND tipepaket_m.is_deleted= FALSE
                                                                                            AND tipepaket_m.is_active = TRUE
                                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                                    JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                                                                                AND perdatarif_m.is_active = TRUE 
                                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                                    WHERE tariftindakan_m.is_deleted = FALSE
                                    AND tariftindakan_m.is_active = TRUE 
                                    AND tariftindakan_m.tarifparent_id IS NULL
                                    AND tariftindakan_m.komponentarif_id = 6
                                    AND tariftindakan_m.penjamin_id = xpenjamin_id
                             ) tarif_penjamin ON tariftindakan_m.tipepaket_id = tarif_penjamin.tipepaket_id 
                                                                                AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                        WHERE tariftindakan_m.is_deleted = false 
                        AND tariftindakan_m.is_active = true 
                        AND tariftindakan_m.tarifparent_id IS NULL
                        AND tariftindakan_m.komponentarif_id = 6
                        AND tariftindakan_m.penjamin_id = vpenjamin_id
                    UNION ALL
                     SELECT \'paket\'::text AS jenis,
                            COALESCE( tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
                            paketruangan_mp.ruangan_id,
                            r_paket.ruangan_nama,
                            r_paket.instalasi_id,
                            ins_paket.instalasi_nama,
                            paketruangan_mp.ruangan_id AS ruanganpaket_id,
                            r_paket.ruangan_namalainnya AS ruanganpaket_nama,
                            COALESCE( tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id ,
                            perdatarif_m.perdanama_sk,
                            tariftindakan_m.kelaspelayanan_id,
                            kelaspelayanan_m.kelaspelayanan_nama,
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
                            COALESCE( tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                            COALESCE( tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                            COALESCE( tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
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
                                                                                AND tipepaket_m.is_deleted= FALSE
                                                                                AND tipepaket_m.is_active = TRUE
                             JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                             JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                             JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                             JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                             JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id
                             JOIN ruangan_m r_paket ON paketruangan_mp.ruangan_id = r_paket.ruangan_id
                                                                                            AND paketruangan_mp.is_deleted = FALSE 
                             JOIN instalasi_m ins_paket ON r_paket.instalasi_id = ins_paket.instalasi_id
                             LEFT JOIN (
                                    SELECT tariftindakan_m.daftartindakan_id,
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
                                                            JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
                                                                                            AND tipepaket_m.is_deleted= FALSE
                                                                                            AND tipepaket_m.is_active = TRUE
                                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id 
                                    JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                                                                                AND perdatarif_m.is_deleted= FALSE
                                                                                                AND perdatarif_m.is_active = TRUE
                                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                                    WHERE tariftindakan_m.is_deleted = false 
                                    AND tariftindakan_m.is_active = true 
                                    AND tariftindakan_m.tarifparent_id IS NULL
                                    AND tariftindakan_m.komponentarif_id = 6
                                    AND tariftindakan_m.penjamin_id = xpenjamin_id
                             ) tarif_penjamin ON tariftindakan_m.tipepaket_id = tarif_penjamin.tipepaket_id AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
                        WHERE tariftindakan_m.is_deleted = false 
                        AND tariftindakan_m.is_active = true 
                        AND tariftindakan_m.tarifparent_id IS NOT NULL
                        AND tariftindakan_m.komponentarif_id = 6
                        AND tariftindakan_m.penjamin_id = vpenjamin_id
                    ) AS x
                    WHERE x.ruangan_id = xruangan_id
                    AND x.kelaspelayanan_id = xkelaspelayanan_id
                    GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;
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
                                AND tariftindakan_m.penjamin_id = vpenjamin_id
                    ) AS x
                    WHERE x.ruangan_id = xruangan_id
                    AND x.kelaspelayanan_id = xkelaspelayanan_id
                    GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;
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
                            COALESCE(tariftindakan_m.kelaspelayanan_id, 3)::integer as kelaspelayanan_id,
                             COALESCE(kelaspelayanan_m.kelaspelayanan_nama, \'Kelas 3\')::character varying AS kelaspelayanan_nama,
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
                            COALESCE(tarif_penjamin.harga_tariftindakan, COALESCE((tariftindakan_m.harga_tariftindakan), 0)) AS harga_tariftindakan,
                            COALESCE(tarif_penjamin.persencyto_tindakan, COALESCE((tariftindakan_m.persencyto_tindakan), 0)) AS persencyto_tindakan,
                            COALESCE(tarif_penjamin.persendiskon_tindakan, COALESCE((tariftindakan_m.persendiskon_tindakan), 0)) AS persencyto_tindakan,
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
                             ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
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
                    ) AS x
                    WHERE x.kelaspelayanan_id = xkelaspelayanan_id
                    GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;
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
                            tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                            kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
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
                            COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
                            COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
                            COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
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
                        JOIN pemeriksaanlab_m ON tariftindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id 
                                                                                AND pemeriksaanlab_m.is_deleted = FALSE
                        JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
                        JOIN kelompokpemeriksaanlab_m ON pemeriksaanlab_m.kelompokpemeriksaanlab_id = kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id
                        JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                        JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                                                        AND perdatarif_m.is_deleted = FALSE 
                                                                        AND perdatarif_m.is_active = TRUE
                        JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                        JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                        JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id 
                                                                                    AND tindakanruangan_mp.is_deleted = FALSE
                        JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
                        LEFT JOIN (SELECT tariftindakan_m.daftartindakan_id,
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
                                   ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                                                            AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                     WHERE tariftindakan_m.is_deleted = FALSE 
                                 AND tariftindakan_m.is_active = TRUE 
                                 AND tariftindakan_m.tarifparent_id IS NULL 
                                 AND tariftindakan_m.komponentarif_id = 6
                       AND tariftindakan_m.penjamin_id = vpenjamin_id
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
                                tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                                kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
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
                                COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
                                COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
                                COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
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
                                JOIN pemeriksaanrad_m ON tariftindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id 
                                                                            AND pemeriksaanrad_m.is_deleted = FALSE
                                JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
                                JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id
                                JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                                                    AND perdatarif_m.is_deleted = FALSE 
                                                                    AND perdatarif_m.is_active = TRUE
                                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                                JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id 
                                                                                AND tindakanruangan_mp.is_deleted = FALSE
                                JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
                                LEFT JOIN (SELECT tariftindakan_m.daftartindakan_id,
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
                                                        ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                                                        AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                        WHERE tariftindakan_m.is_deleted = FALSE 
                            AND tariftindakan_m.is_active = TRUE 
                            AND tariftindakan_m.tarifparent_id IS NULL 
                            AND tariftindakan_m.komponentarif_id = 6
                            AND tariftindakan_m.penjamin_id = vpenjamin_id
            UNION ALL
               SELECT \'operasi\'::text AS jenis,
                            COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                            tindakanruangan_mp.ruangan_id as ruangan_id,
                            ruangan_m.ruangan_nama as ruangan_nama,
                            ruangan_m.instalasi_id as instalasi_id,
                            NULL::character varying instalasi_nama,
                            NULL::integer AS ruanganpaket_id,
                            NULL::character varying AS ruanganpaket_nama,
                            tariftindakan_m.perdatarif_id AS perdatarif_id ,
                            perdatarif_m.perdanama_sk as perdanama_sk,
                            tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                            kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
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
                            COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
                            COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
                            COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
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
                                         ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
                            WHERE  tariftindakan_m.is_deleted = false AND
                                     tariftindakan_m.is_active = true AND
                                     tariftindakan_m.tarifparent_id IS NULL AND
                                     perdatarif_m.is_active = true AND
                                     tariftindakan_m.komponentarif_id = 6
                                     AND tariftindakan_m.penjamin_id = vpenjamin_id
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
                    tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                    kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
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
                    COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
                    COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
                    COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
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
                    COALESCE(tariftindakan_m.persen_penyulit, 0::numeric) AS persen_penyulit  
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
                LEFT JOIN (SELECT tariftindakan_m.tipepaket_id,
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
                                        ) tarif_penjamin ON tariftindakan_m.tipepaket_id = tarif_penjamin.tipepaket_id 
                                                                         AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                    WHERE tariftindakan_m.is_deleted = FALSE 
                        AND tariftindakan_m.is_active = TRUE 
                        AND perdatarif_m.is_active = TRUE 
                        AND tariftindakan_m.komponentarif_id = 6
                        AND tariftindakan_m.penjamin_id = vpenjamin_id          
            ) x
              WHERE x.ruangan_id = xruangan_id
                AND x.kelaspelayanan_id = xkelaspelayanan_id
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
                            tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                            kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
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
                            COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
                            COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
                            COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
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
                        JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                                                        AND perdatarif_m.is_deleted = FALSE 
                                                                        AND perdatarif_m.is_active = TRUE
                        JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                        JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                        JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id 
                                                                                    AND tindakanruangan_mp.is_deleted = FALSE
                        JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
                        LEFT JOIN (SELECT tariftindakan_m.daftartindakan_id,
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
                                   ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                                                            AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                     WHERE tariftindakan_m.is_deleted = FALSE 
                    AND tariftindakan_m.is_active = TRUE 
                    AND tariftindakan_m.tarifparent_id IS NULL 
                    AND tariftindakan_m.komponentarif_id = 6
                    AND tariftindakan_m.penjamin_id = vpenjamin_id
                    ) AS x
                    WHERE x.kelaspelayanan_id = xkelaspelayanan_id
                            AND x.ruangan_id = xruangan_id
                    GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;
                END IF;
            END; 
            $BODY$;
        ');  

    
        $this->execute('
            ALTER FUNCTION public.tariftotalrs_fn(integer, integer, integer, character varying) OWNER TO postgres;
        ');  

        $this->execute('
            CREATE OR REPLACE FUNCTION public.tarifkomponenrs_fn(
                xruangan_id integer DEFAULT 0,
                xpenjamin_id integer DEFAULT 0,
                xkelaspelayanan_id integer DEFAULT 0,
                xtipe character varying DEFAULT \'pelayanan\'::character varying)
                RETURNS TABLE(jenis text, tariftindakan_id integer, ruangan_id integer, ruangan_nama character varying, instalasi_id integer, instalasi_nama character varying, ruanganpaket_id integer, ruanganpaket_nama character varying, perdatarif_id integer, perdanama_sk character varying, kelaspelayanan_id integer, kelaspelayanan_nama character varying, penjamin_id integer, penjamin_nama character varying, kelompoktindakan_id integer, kelompoktindakan_nama character varying, kategoritindakan_id integer, kategoritindakan_nama character varying, daftartindakan_id integer, daftartindakan_nama character varying, tipepaket_id integer, tipepaket_nama character varying, komponentarif_id integer, komponentarif_nama character varying, harga_tariftindakan numeric, persencyto_tindakan numeric, persendiskon_tindakan numeric, is_default boolean, is_akomodasi boolean, carabayar_id integer, is_konsultasi boolean, kamarruangan_nokamar character varying, kamarruangan_id integer, ambulan_id integer, no_polisi character varying, kelompokpemeriksaanlab_id integer, nama_kelompok character varying, jenispemeriksaanlab_id integer, jenispemeriksaanlab_nama character varying, pemeriksaanlab_id integer, pemeriksaanlab_nama character varying, persen_penyulit numeric) 
                LANGUAGE \'plpgsql\'

                COST 100
                VOLATILE 
                ROWS 1000
            AS $BODY$ 

            DECLARE 

                vpenjamin_id int4;

            BEGIN

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
                    THEN xpenjamin_id := 0;
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
                            tariftindakan_m.kelaspelayanan_id,
                            kelaspelayanan_m.kelaspelayanan_nama,
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
                            COALESCE( tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                            COALESCE( tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                            COALESCE( tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
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
                            LEFT JOIN   (SELECT tariftindakan_m.daftartindakan_id,
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
                                        ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                                                AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                        WHERE tindakanruangan_mp.is_deleted = false 
                            AND komponentarif_m.is_deleted IS FALSE
                            AND perdatarif_m.is_active = true 
                            AND tariftindakan_m.is_deleted = false 
                            AND tariftindakan_m.is_active = true 
                            AND tariftindakan_m.tarifparent_id IS NULL
                            AND tariftindakan_m.komponentarif_id <> 6
                            AND tariftindakan_m.penjamin_id = vpenjamin_id

                   
            UNION ALL
                    SELECT \'paket\'::text AS jenis,
                            COALESCE( tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id ,
                            paketruangan_mp.ruangan_id,
                            r_paket.ruangan_nama,
                            r_paket.instalasi_id,
                            ins_paket.instalasi_nama,
                            paketruangan_mp.ruangan_id AS ruanganpaket_id,
                            r_paket.ruangan_namalainnya AS ruanganpaket_nama,
                            COALESCE( tarif_penjamin.perdatarif_id, tariftindakan_m.perdatarif_id) AS perdatarif_id ,
                            perdatarif_m.perdanama_sk,
                            tariftindakan_m.kelaspelayanan_id,
                            kelaspelayanan_m.kelaspelayanan_nama,
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
                            COALESCE( tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan) AS harga_tariftindakan ,
                            COALESCE( tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan) AS persencyto_tindakan ,
                            COALESCE( tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan) AS persendiskon_tindakan ,
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
                       JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
                                                                    AND perdatarif_m.is_active = TRUE
                                                                    AND perdatarif_m.is_deleted = FALSE
                      JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id
                                                                            AND paketruangan_mp.is_deleted = FALSE 
                      JOIN ruangan_m r_paket ON paketruangan_mp.ruangan_id = r_paket.ruangan_id
                      JOIN instalasi_m ins_paket ON r_paket.instalasi_id = ins_paket.instalasi_id
                      LEFT JOIN   (SELECT tariftindakan_m.tipepaket_id,
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
                                  ) tarif_penjamin ON tariftindakan_m.tipepaket_id = tarif_penjamin.tipepaket_id
                                                                                        AND tariftindakan_m.komponentarif_id = tarif_penjamin.komponentarif_id
                                                                                        AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                    WHERE tariftindakan_m.is_deleted = false 
                      AND tariftindakan_m.is_active = true 
                      AND tariftindakan_m.komponentarif_id <> 6
                      AND tariftindakan_m.penjamin_id = vpenjamin_id
                    ) AS x
                    WHERE x.ruangan_id = xruangan_id
                      AND x.kelaspelayanan_id = xkelaspelayanan_id
                    GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;

            ELSEIF(xtipe = \'kamar\')
                THEN
                    RETURN QUERY 
                    SELECT * FROM 
                      (SELECT \'kamar\'::text AS jenis,
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
                        LEFT JOIN (SELECT tariftindakan_m.daftartindakan_id,
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
                             ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                                                AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                                                                                AND tariftindakan_m.kamarruangan_id = tarif_penjamin.kamarruangan_id
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
                  SELECT * FROM 
                    (SELECT \'ambulan\'::text AS jenis,
                            COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id) AS tariftindakan_id,
                            NULL::integer as ruangan_id,
                            NULL::character varying as ruangan_nama,
                            NULL::integer as instalasi_id,
                            NULL::character varying as instalasi_nama,
                            NULL::integer AS ruanganpaket_id,
                            NULL::character varying AS ruanganpaket_nama,
                            tariftindakan_m.perdatarif_id AS perdatarif_id ,
                            perdatarif_m.perdanama_sk as perdanama_sk,
                            COALESCE(tariftindakan_m.kelaspelayanan_id, 3)::integer as kelaspelayanan_id,
                            COALESCE(kelaspelayanan_m.kelaspelayanan_nama, \'Kelas 3\')::character varying AS kelaspelayanan_nama,
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
                            COALESCE(tarif_penjamin.harga_tariftindakan, COALESCE((tariftindakan_m.harga_tariftindakan), 0)) AS harga_tariftindakan,
                            COALESCE(tarif_penjamin.persencyto_tindakan, COALESCE((tariftindakan_m.persencyto_tindakan), 0)) AS persencyto_tindakan,
                            COALESCE(tarif_penjamin.persendiskon_tindakan, COALESCE((tariftindakan_m.persendiskon_tindakan), 0)) AS persencyto_tindakan,
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
                      LEFT JOIN (SELECT tariftindakan_m.daftartindakan_id,
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
                                ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                                                   AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
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
                    ) AS x
                  WHERE x.kelaspelayanan_id = xkelaspelayanan_id
                    GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;

            ELSEIF(xtipe = \'penunjang\')
              THEN
                RETURN QUERY
                  SELECT * FROM 
                    (SELECT \'lab\'::text AS jenis,
                            COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                            tindakanruangan_mp.ruangan_id as ruangan_id,
                            ruangan_m.ruangan_nama as ruangan_nama,
                            ruangan_m.instalasi_id as instalasi_id,
                            null::character varying as instalasi_nama,
                            NULL::integer AS ruanganpaket_id,
                            NULL::character varying AS ruanganpaket_nama,
                            tariftindakan_m.perdatarif_id AS perdatarif_id ,
                            perdatarif_m.perdanama_sk as perdanama_sk,
                            tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                            kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
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
                            COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
                            COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
                            COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
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
                      JOIN pemeriksaanlab_m ON tariftindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id 
                                            AND pemeriksaanlab_m.is_deleted = FALSE
                      JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
                      JOIN kelompokpemeriksaanlab_m ON pemeriksaanlab_m.kelompokpemeriksaanlab_id = kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id
                      JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                      JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                        AND perdatarif_m.is_deleted = FALSE 
                                        AND perdatarif_m.is_active = TRUE
                      JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                      JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                      JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id 
                                              AND tindakanruangan_mp.is_deleted = FALSE
                      JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
                      LEFT JOIN (SELECT tariftindakan_m.daftartindakan_id,
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
                    ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                            AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                    WHERE  tariftindakan_m.is_deleted = FALSE 
                      AND tariftindakan_m.is_active = TRUE 
                      AND tariftindakan_m.tarifparent_id IS NULL 
                      AND perdatarif_m.is_active = TRUE 
                      AND tariftindakan_m.komponentarif_id <> 6
                      AND tariftindakan_m.penjamin_id = vpenjamin_id
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
                tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
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
                COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
                COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
                COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
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
              JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id 
                                   AND paketruangan_mp.is_deleted = FALSE
              JOIN ruangan_m ON paketruangan_mp.ruangan_id = ruangan_m.ruangan_id
              JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
              JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                                 AND perdatarif_m.is_deleted = FALSE 
                               AND perdatarif_m.is_active = TRUE
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                JOIN pemeriksaanlab_m ON tariftindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id 
                                    and pemeriksaanlab_m.is_deleted = FALSE
                LEFT JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
                LEFT JOIN kelompokpemeriksaanlab_m ON pemeriksaanlab_m.kelompokpemeriksaanlab_id = kelompokpemeriksaanlab_m.kelompokpemeriksaanlab_id
                LEFT JOIN (SELECT tariftindakan_m.tipepaket_id,
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
                                    WHERE   tariftindakan_m.is_deleted = FALSE 
                          AND tariftindakan_m.is_active = TRUE 
                          AND   perdatarif_m.is_active = TRUE 
                          AND tariftindakan_m.komponentarif_id <> 6
                                        AND tariftindakan_m.penjamin_id = xpenjamin_id
                                  ) tarif_penjamin ON tariftindakan_m.tipepaket_id = tarif_penjamin.tipepaket_id 
                                                                AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                                                                    AND pemeriksaanlab_m.pemeriksaanlab_id = tarif_penjamin.pemeriksaanlab_id
                WHERE tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND perdatarif_m.is_active = TRUE 
                AND tariftindakan_m.komponentarif_id <> 6 
                AND tariftindakan_m.penjamin_id = vpenjamin_id 
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
                      tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                      kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
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
                      COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
                      COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
                      COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
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
                JOIN pemeriksaanrad_m ON tariftindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id 
                                      AND pemeriksaanrad_m.is_deleted = FALSE
                JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
                JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id
                JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                  AND perdatarif_m.is_deleted = FALSE 
                                  AND perdatarif_m.is_active = TRUE
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id 
                                        AND tindakanruangan_mp.is_deleted = FALSE
                JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
                LEFT JOIN (SELECT tariftindakan_m.daftartindakan_id,
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
                          ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                                AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
              WHERE tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND perdatarif_m.is_active = TRUE 
                AND tariftindakan_m.komponentarif_id <> 6
                AND tariftindakan_m.penjamin_id = vpenjamin_id
            UNION ALL
              SELECT  \'paket_rad\'::text AS jenis,
                        COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                        paketruangan_mp.ruangan_id as ruangan_id,
                        ruangan_m.ruangan_nama as ruangan_nama,
                        ruangan_m.instalasi_id as instalasi_id,
                        null::VARCHAR as instalasi_nama,
                        NULL::INTEGER AS ruanganpaket_id,
                        NULL::character varying AS ruanganpaket_nama,
                        tariftindakan_m.perdatarif_id AS perdatarif_id ,
                        perdatarif_m.perdanama_sk as perdanama_sk,
                        tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                        kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
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
                        COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
                        COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
                        COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
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
                    JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id 
                                                             AND paketruangan_mp.is_deleted = FALSE
                    JOIN ruangan_m ON paketruangan_mp.ruangan_id = ruangan_m.ruangan_id
                    JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                    JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                                      AND perdatarif_m.is_deleted = FALSE 
                                                      AND perdatarif_m.is_active = TRUE
                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                    JOIN pemeriksaanrad_m ON tariftindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id 
                                                                AND pemeriksaanrad_m.is_deleted = FALSE
                    LEFT JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
                    LEFT JOIN kelompokpemeriksaanrad_m ON pemeriksaanrad_m.kelompokpemeriksaanrad_id = kelompokpemeriksaanrad_m.kelompokpemeriksaanrad_id   
                    LEFT JOIN (SELECT tariftindakan_m.tipepaket_id,
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
                                        ) tarif_penjamin ON tariftindakan_m.tipepaket_id = tarif_penjamin.tipepaket_id 
                                                                         AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                                                                         AND pemeriksaanrad_m.pemeriksaanradiologi_id = tarif_penjamin.pemeriksaanradiologi_id
                WHERE tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND perdatarif_m.is_active = TRUE 
                AND tariftindakan_m.komponentarif_id <> 6 
                AND tariftindakan_m.penjamin_id = vpenjamin_id                  
            UNION ALL
              SELECT \'operasi\'::text AS jenis,
                     COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                     tindakanruangan_mp.ruangan_id as ruangan_id,
                     ruangan_m.ruangan_nama as ruangan_nama,
                     ruangan_m.instalasi_id as instalasi_id,
                     NULL::character varying instalasi_nama,
                     NULL::integer AS ruanganpaket_id,
                     NULL::character varying AS ruanganpaket_nama,
                     tariftindakan_m.perdatarif_id AS perdatarif_id ,
                     perdatarif_m.perdanama_sk as perdanama_sk,
                     tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                     kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
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
                     COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
                     COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
                     COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
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
                JOIN operasi_m ON tariftindakan_m.daftartindakan_id = operasi_m.daftartindakan_id 
                               AND operasi_m.is_deleted = FALSE
                JOIN golonganoperasi_m ON operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id
                JOIN kegiatanoperasi_m ON operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id
                JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                  AND perdatarif_m.is_deleted = FALSE 
                                  AND perdatarif_m.is_active = TRUE
                JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id 
                                        AND tindakanruangan_mp.is_deleted = FALSE
                JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
                LEFT JOIN (SELECT tariftindakan_m.daftartindakan_id,
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
                          ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                     AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                          WHERE tariftindakan_m.is_deleted = FALSE 
                            AND tariftindakan_m.is_active = TRUE 
                            AND perdatarif_m.is_active = TRUE 
                            AND tariftindakan_m.komponentarif_id <> 6
                            AND tariftindakan_m.penjamin_id = xpenjamin_id
            UNION ALL
              SELECT  \'paket_operasi\'::text AS jenis,
                      COALESCE(tarif_penjamin.tariftindakan_id, tariftindakan_m.tariftindakan_id ) AS tariftindakan_id ,
                      paketruangan_mp.ruangan_id as ruangan_id,
                      ruangan_m.ruangan_nama as ruangan_nama,
                      ruangan_m.instalasi_id as instalasi_id,
                      null::VARCHAR as instalasi_nama,
                      NULL::integer AS ruanganpaket_id,
                      NULL::character varying AS ruanganpaket_nama,
                      tariftindakan_m.perdatarif_id AS perdatarif_id ,
                      perdatarif_m.perdanama_sk as perdanama_sk,
                      tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                      kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
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
                      COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
                      COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
                      COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
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
                    JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id 
                                     AND paketruangan_mp.is_deleted = FALSE
                    JOIN ruangan_m ON paketruangan_mp.ruangan_id = ruangan_m.ruangan_id
                    JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                    JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                           AND perdatarif_m.is_deleted = FALSE 
                                 AND perdatarif_m.is_active = TRUE
                    JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                    JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                    JOIN operasi_m ON tariftindakan_m.daftartindakan_id = operasi_m.daftartindakan_id 
                               AND operasi_m.is_deleted = FALSE
                    LEFT JOIN kegiatanoperasi_m ON operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id
                    LEFT JOIN golonganoperasi_m ON operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id          
                    LEFT JOIN (SELECT tariftindakan_m.tipepaket_id,
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
                                  ) tarif_penjamin ON tariftindakan_m.tipepaket_id = tarif_penjamin.tipepaket_id 
                                                   AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                                                   AND operasi_m.operasi_id = tarif_penjamin.operasi_id
                WHERE  tariftindakan_m.is_deleted = FALSE 
                AND tariftindakan_m.is_active = TRUE 
                AND perdatarif_m.is_active = TRUE 
                AND tariftindakan_m.komponentarif_id <> 6 
                AND tariftindakan_m.penjamin_id = vpenjamin_id 
            ) x
            WHERE x.ruangan_id = xruangan_id
              AND x.kelaspelayanan_id = xkelaspelayanan_id
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
                            tariftindakan_m.kelaspelayanan_id as kelaspelayanan_id,
                            kelaspelayanan_m.kelaspelayanan_nama as kelaspelayanan_nama,
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
                            COALESCE(tarif_penjamin.harga_tariftindakan, tariftindakan_m.harga_tariftindakan ) AS harga_tariftindakan ,
                            COALESCE(tarif_penjamin.persencyto_tindakan, tariftindakan_m.persencyto_tindakan ) AS persencyto_tindakan ,
                            COALESCE(tarif_penjamin.persendiskon_tindakan, tariftindakan_m.persendiskon_tindakan ) AS persendiskon_tindakan ,
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
                        JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id 
                                                                        AND perdatarif_m.is_deleted = FALSE 
                                                                        AND perdatarif_m.is_active = TRUE
                        JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
                        JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id
                        JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id 
                                                                                    AND tindakanruangan_mp.is_deleted = FALSE
                        JOIN ruangan_m ON tindakanruangan_mp.ruangan_id = ruangan_m.ruangan_id
                        LEFT JOIN (SELECT tariftindakan_m.daftartindakan_id,
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
                                   ) tarif_penjamin ON tariftindakan_m.daftartindakan_id = tarif_penjamin.daftartindakan_id 
                                                                                            AND tariftindakan_m.kelaspelayanan_id = tarif_penjamin.kelaspelayanan_id 
                     WHERE tariftindakan_m.is_deleted = FALSE 
                    AND tariftindakan_m.is_active = TRUE 
                    AND tariftindakan_m.tarifparent_id IS NULL 
                    AND tariftindakan_m.komponentarif_id <> 6
                    AND tariftindakan_m.penjamin_id = vpenjamin_id
                    ) AS x
                    WHERE x.kelaspelayanan_id = xkelaspelayanan_id
                    AND x.ruangan_id = xruangan_id
                    GROUP BY x.jenis, x.tariftindakan_id , x.ruangan_id , x.ruangan_nama , x.instalasi_id , x.instalasi_nama , x.ruanganpaket_id , x.ruanganpaket_nama , x.perdatarif_id , x.perdanama_sk , x.kelaspelayanan_id , x.kelaspelayanan_nama , x.penjamin_id , x.penjamin_nama , x.kelompoktindakan_id , x.kelompoktindakan_nama , x.kategoritindakan_id , x.kategoritindakan_nama , x.daftartindakan_id , x.daftartindakan_nama , x.tipepaket_id , x.tipepaket_nama , x.komponentarif_id , x.komponentarif_nama , x.harga_tariftindakan , x.persencyto_tindakan , x.persendiskon_tindakan , x.is_default , x.is_akomodasi , x.carabayar_id , x.is_konsultasi , x.kamarruangan_nokamar , x.kamarruangan_id , x.ambulan_id , x.no_polisi , x.kelompokpemeriksaanlab_id , x.nama_kelompok , x.jenispemeriksaanlab_id , x.jenispemeriksaanlab_nama , x.pemeriksaanlab_id , x.pemeriksaanlab_nama , x.persen_penyulit;
                
            END IF;
            END; 
            $BODY$;
        '); 
        
        $this->execute('
            ALTER FUNCTION public.tarifkomponenrs_fn(integer, integer, integer, character varying) OWNER TO postgres;
        ');  
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201023_095837_migrate_2961_permintaanmakan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201023_095837_migrate_2961_permintaanmakan cannot be reverted.\n";

        return false;
    }
    */
}
