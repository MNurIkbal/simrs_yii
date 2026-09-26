<?php

use yii\db\Migration;

/**
 * Class m191007_031547_sp_tindakanpelayan
 */
class m191007_031547_sp_tindakanpelayan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP FUNCTION if exists public.sp_tindakanpelayan(integer, integer, integer, integer, integer, integer, integer, integer, integer, integer, integer, integer, integer, integer, integer, timestamp without time zone, integer, boolean, boolean, integer, integer, boolean, integer);');

        $this->execute("

CREATE OR REPLACE FUNCTION public.sp_tindakanpelayan(IN xkelaspelayanan_id integer, IN xpasien_id integer, IN xrencanaoperasi_id integer, IN xinstalasi_id integer, IN xdaftartindakan_id integer, IN xtipepaket_id integer, IN xtindakansudahbayar_id integer, IN xcarabayar_id integer, IN xpendaftaran_id integer, IN xjeniskasuspenyakit_id integer, IN xruangan_id integer, IN xpasienmasukpenunjang_id integer, IN xpenjamin_id integer, IN xpasienadmisi_id integer, IN xinstruksitindakan_id integer, IN xtgl_tindakan timestamp without time zone, IN xqty_tindakan integer, IN xis_cyto boolean, IN xis_diskon boolean, IN xperawat1 integer, IN xperawat2 integer, IN xis_penatajasa boolean, IN xuser_id integer)
  RETURNS TABLE(status boolean, message character varying) AS
\$BODY\$
DECLARE 
    vstatus bool;
    vmessage VARCHAR;
    vtarif_satuan FLOAT;
    vtarif_tindakan FLOAT;
    vtarif_cyto FLOAT;
    vdokter int4;
    vcyto FLOAT;
    vdiscount FLOAT; 
    vdiscount_tindakan FLOAT; 
    vtindakanpelayanan_id int4;
    vadditional_riwayat text;
BEGIN
    IF(xtgl_tindakan IS NULL)
    THEN
        xtgl_tindakan := CURRENT_TIMESTAMP;
    END IF;
    
    IF(COALESCE(xtipepaket_id,0) = 0)
    THEN
--      IF NOT EXISTS (
--          SELECT 
--              1
--          FROM tariftindakan_m
--          JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
--          JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
--          LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
--          JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
--          JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id AND komponentarif_m.is_deleted IS FALSE
--          JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
--          JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
--          JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id
--          WHERE tindakanruangan_mp.is_deleted = false AND perdatarif_m.is_active = true AND tariftindakan_m.is_deleted = false AND tariftindakan_m.is_active = true
--          AND tariftindakan_m.komponentarif_id = 6
--          AND tindakanruangan_mp.ruangan_id = xruangan_id 
--          AND tariftindakan_m.penjamin_id = xpenjamin_id 
--          AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
--          AND daftartindakan_m.daftartindakan_id = xdaftartindakan_id
--          LIMIT 1
--      )
--      THEN 
--          vstatus := FALSE;
--          
--          RAISE EXCEPTION 'Data Tindakan tidak ditemukan '
--       USING HINT = 'Please check your user ID';
--      END IF; 
        
        SELECT 
            tariftindakan_m.persencyto_tindakan,
            tariftindakan_m.persendiskon_tindakan
        INTO vcyto, vdiscount
        FROM tariftindakan_m
        JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
        JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
        LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
        JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
        JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id AND komponentarif_m.is_deleted IS FALSE
        JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
        JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
        JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id
        WHERE tindakanruangan_mp.is_deleted = false AND perdatarif_m.is_active = true AND tariftindakan_m.is_deleted = false AND tariftindakan_m.is_active = true
        AND tariftindakan_m.komponentarif_id = 6
        AND tindakanruangan_mp.ruangan_id = xruangan_id 
        AND tariftindakan_m.penjamin_id = xpenjamin_id 
        AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
        AND daftartindakan_m.daftartindakan_id = xdaftartindakan_id;
        
        SELECT 
            SUM(COALESCE(tariftindakan_m.harga_tariftindakan,0)) INTO vtarif_satuan
        FROM tariftindakan_m
        JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
        JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
        LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
        JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
        JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id AND komponentarif_m.is_deleted IS FALSE
        JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
        JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
        JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id
        WHERE tindakanruangan_mp.is_deleted = false AND perdatarif_m.is_active = true AND tariftindakan_m.is_deleted = false AND tariftindakan_m.is_active = true
        AND tariftindakan_m.komponentarif_id <> 6
        AND tindakanruangan_mp.ruangan_id = xruangan_id 
        AND tariftindakan_m.penjamin_id = xpenjamin_id 
        AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
        AND daftartindakan_m.daftartindakan_id = xdaftartindakan_id;
    ELSE
        SELECT 
            tariftindakan_m.persencyto_tindakan,
            tariftindakan_m.persendiskon_tindakan
        INTO vcyto, vdiscount
        FROM tariftindakan_m
        JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
        JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
        JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id AND komponentarif_m.is_deleted IS FALSE
        JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
        JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
        JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id
        WHERE paketruangan_mp.is_deleted = false AND perdatarif_m.is_active = true AND tariftindakan_m.is_deleted = false AND tariftindakan_m.is_active = true
        AND tariftindakan_m.komponentarif_id = 6
        AND paketruangan_mp.ruangan_id = xruangan_id 
        AND tariftindakan_m.penjamin_id = xpenjamin_id 
        AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
        AND tipepaket_m.tipepaket_id = xtipepaket_id;
        
        SELECT 
            SUM(COALESCE(tariftindakan_m.harga_tariftindakan,0)) INTO vtarif_satuan
        FROM tariftindakan_m
        JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
        JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
        JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id AND komponentarif_m.is_deleted IS FALSE
        JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
        JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
        JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id
        WHERE paketruangan_mp.is_deleted = false AND perdatarif_m.is_active = true AND tariftindakan_m.is_deleted = false AND tariftindakan_m.is_active = true
        AND tariftindakan_m.komponentarif_id <> 6
        AND paketruangan_mp.ruangan_id = xruangan_id 
        AND tariftindakan_m.penjamin_id = xpenjamin_id 
        AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
        AND tipepaket_m.tipepaket_id = xtipepaket_id;
        
    END IF;
    
    vtarif_tindakan = vtarif_satuan * xqty_tindakan;
        
    IF(xis_cyto = TRUE)
    THEN
        vtarif_cyto := ((vtarif_tindakan * vcyto)/100);
        vtarif_tindakan := vtarif_tindakan + COALESCE(vtarif_cyto,0);
    ELSE
        vtarif_cyto := 0;
    END IF;
    
    IF(COALESCE(xpasienadmisi_id,0) <> 0)
    THEN
        SELECT pegawai_id INTO vdokter
        FROM pasienadmisi_t
        WHERE pasienadmisi_id = xpasienadmisi_id;
    ELSE
        SELECT pegawai_id INTO vdokter
        FROM pendaftaran_t
        WHERE pendaftaran_id = xpendaftaran_id;
    END IF;
    
    
    INSERT INTO tindakanpelayanan_t(
        kelaspelayanan_id, pasien_id, rencanaoperasi_id, instalasi_id, daftartindakan_id, tipepaket_id, 
        tindakansudahbayar_id, carabayar_id, pendaftaran_id, jeniskasuspenyakit_id, ruangan_id , 
        pasienmasukpenunjang_id, penjamin_id, pasienadmisi_id, tgl_tindakan, tarif_satuan, tarif_tindakan,
        tarifcyto_tindakan, qty_tindakan, cyto_tindakan, dokterpenanggungjawab_id, perawat1_id, perawat2_id,
        discount_tindakan, created_by, created_date, instruksitindakan_id, is_penatajasa
    )VALUES(
        xkelaspelayanan_id, xpasien_id, xrencanaoperasi_id, xinstalasi_id, xdaftartindakan_id, xtipepaket_id,
        xtindakansudahbayar_id, xcarabayar_id, xpendaftaran_id, xjeniskasuspenyakit_id, xruangan_id, 
        xpasienmasukpenunjang_id, xpenjamin_id, xpasienadmisi_id, xtgl_tindakan, vtarif_satuan, vtarif_tindakan,
        vtarif_cyto, xqty_tindakan, xis_cyto, vdokter, xperawat1, xperawat2,
        vdiscount_tindakan, xuser_id, CURRENT_TIMESTAMP, xinstruksitindakan_id, xis_penatajasa
    );
        
    SELECT 
        last_value INTO vtindakanpelayanan_id
    FROM tindakanpelayanan_t_tindakanpelayanan_id_seq;
    
    IF(COALESCE(xtipepaket_id,0) = 0)
    THEN
        INSERT INTO tindakankomponen_t(
            komponentarif_id, 
            tindakanpelayanan_id, 
            tarif_kompsatuan, 
            tarif_tindakankomp, 
            tarifcyto_tindakankomp,  
            created_by, 
            created_date
        )
        SELECT 
            tariftindakan_m.komponentarif_id, 
            vtindakanpelayanan_id, 
            COALESCE(tariftindakan_m.harga_tariftindakan,0),  
            CASE xis_cyto
                WHEN TRUE THEN (COALESCE(tariftindakan_m.harga_tariftindakan,0) * xqty_tindakan) + ( ( COALESCE(tariftindakan_m.harga_tariftindakan,0) * vcyto) /100 )
                ELSE (COALESCE(tariftindakan_m.harga_tariftindakan,0) * xqty_tindakan) 
                END,
                CASE xis_cyto
                    WHEN TRUE THEN 
                        ( ( COALESCE(tariftindakan_m.harga_tariftindakan,0) * vcyto) /100 )
                    ELSE 0
                END,
            xuser_id,
            CURRENT_TIMESTAMP
        FROM tariftindakan_m
        JOIN daftartindakan_m ON tariftindakan_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
        JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
        LEFT JOIN kategoritindakan_m ON daftartindakan_m.kategoritindakan_id = kategoritindakan_m.kategoritindakan_id
        JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
        JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id AND komponentarif_m.is_deleted IS FALSE
        JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
        JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
        JOIN tindakanruangan_mp ON tariftindakan_m.daftartindakan_id = tindakanruangan_mp.daftartindakan_id
        WHERE tindakanruangan_mp.is_deleted = false AND perdatarif_m.is_active = true AND tariftindakan_m.is_deleted = false AND tariftindakan_m.is_active = true
        AND tariftindakan_m.komponentarif_id <> 6
        AND tindakanruangan_mp.ruangan_id = xruangan_id 
        AND tariftindakan_m.penjamin_id = xpenjamin_id 
        AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
        AND daftartindakan_m.daftartindakan_id = xdaftartindakan_id;
    ELSE
        INSERT INTO tindakankomponen_t(
            komponentarif_id, 
            tindakanpelayanan_id, 
            tarif_kompsatuan, 
            tarif_tindakankomp, 
            tarifcyto_tindakankomp,  
            created_by, 
            created_date
        )
        SELECT 
            tariftindakan_m.komponentarif_id, 
            vtindakanpelayanan_id, 
            COALESCE(tariftindakan_m.harga_tariftindakan,0),  
            CASE xis_cyto
                WHEN TRUE THEN (COALESCE(tariftindakan_m.harga_tariftindakan,0) * xqty_tindakan) + ( ( COALESCE(tariftindakan_m.harga_tariftindakan,0) * vcyto) /100 )
                ELSE (COALESCE(tariftindakan_m.harga_tariftindakan,0) * xqty_tindakan) 
                END,
                CASE xis_cyto
                    WHEN TRUE THEN 
                        ( ( COALESCE(tariftindakan_m.harga_tariftindakan,0) * vcyto) /100 )
                    ELSE 0
                END,
            xuser_id,
            CURRENT_TIMESTAMP
        FROM tariftindakan_m
        JOIN tipepaket_m ON tariftindakan_m.tipepaket_id = tipepaket_m.tipepaket_id
        JOIN penjamin_m ON tariftindakan_m.penjamin_id = penjamin_m.penjamin_id
        JOIN komponentarif_m ON tariftindakan_m.komponentarif_id = komponentarif_m.komponentarif_id AND komponentarif_m.is_deleted IS FALSE
        JOIN kelaspelayanan_m ON tariftindakan_m.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
        JOIN perdatarif_m ON tariftindakan_m.perdatarif_id = perdatarif_m.perdatarif_id
        JOIN paketruangan_mp ON tariftindakan_m.tipepaket_id = paketruangan_mp.tipepaket_id
        WHERE paketruangan_mp.is_deleted = false AND perdatarif_m.is_active = true AND tariftindakan_m.is_deleted = false AND tariftindakan_m.is_active = true
        AND tariftindakan_m.komponentarif_id <> 6
        AND paketruangan_mp.ruangan_id = xruangan_id 
        AND tariftindakan_m.penjamin_id = xpenjamin_id 
        AND tariftindakan_m.kelaspelayanan_id = xkelaspelayanan_id
        AND tipepaket_m.tipepaket_id = xtipepaket_id;
        
    END IF;
    
    SELECT row_to_json(d1.*) INTO vadditional_riwayat
        FROM ( 
            SELECT  
                kelaspelayanan_m.kelaspelayanan_nama,
                pasien.nama_pasien,
                instalasi_m.instalasi_nama,
                daftartindakan_m.daftartindakan_nama,
                tipepaket_m.tipepaket_nama,
                carabayar_m.carabayar_nama,
                jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                ruangan_m.ruangan_nama,
                penjamin_m.penjamin_nama,
                pegawai_m.nama_pegawai AS dokter_dpjp,
                bidan1.nama_pegawai AS bidan1,
                bidan1.nama_pegawai AS bidan2
            FROM tindakanpelayanan_t
            LEFT JOIN kelaspelayanan_m ON tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
            JOIN (
                SELECT pasien_id, no_rekam_medik, nama_pasien 
                FROM pasien_m 
            ) pasien ON tindakanpelayanan_t.pasien_id = pasien.pasien_id
            JOIN instalasi_m ON tindakanpelayanan_t.instalasi_id = instalasi_m.instalasi_id
            LEFT JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
            LEFT JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
            LEFT JOIN carabayar_m ON tindakanpelayanan_t.carabayar_id = carabayar_m.carabayar_id
            LEFT JOIN jeniskasuspenyakit_m ON tindakanpelayanan_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
            JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
            LEFT JOIN penjamin_m ON tindakanpelayanan_t.penjamin_id = penjamin_m.penjamin_id
            LEFT JOIN pegawai_m ON tindakanpelayanan_t.dokterpelaksana_id = pegawai_m.pegawai_id
            LEFT JOIN pegawai_m bidan1 ON tindakanpelayanan_t.bidan1_id = bidan1.pegawai_id
            LEFT JOIN pegawai_m bidan2 ON tindakanpelayanan_t.bidan2_id = bidan2.pegawai_id
            WHERE tindakanpelayanan_t.tindakanpelayanan_id = vtindakanpelayanan_id
        ) d1;
        
        UPDATE tindakanpelayanan_t
        SET additional_riwayat = vadditional_riwayat
        WHERE tindakanpelayanan_id = vtindakanpelayanan_id;
    
    vstatus := TRUE;
    vmessage := 'Success';
    
    RETURN QUERY 
    SELECT vstatus, vmessage;
        
END; 
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;");

        $this->execute('ALTER FUNCTION public.sp_tindakanpelayan(integer, integer, integer, integer, integer, integer, integer, integer, integer, integer, integer, integer, integer, integer, integer, timestamp without time zone, integer, boolean, boolean, integer, integer, boolean, integer)
  OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191007_031547_sp_tindakanpelayan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191007_031547_sp_tindakanpelayan cannot be reverted.\n";

        return false;
    }
    */
}
