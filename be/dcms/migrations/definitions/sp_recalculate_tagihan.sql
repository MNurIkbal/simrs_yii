CREATE OR REPLACE FUNCTION "public"."sp_recalculate_tagihan"("xpendaftaran_id" int4, "xpenjamin_id" int4, "xuser_id" int4)
  RETURNS TABLE("status" int4, "message" text) AS $BODY$
DECLARE 
    vstatus int4;
    vmessage TEXT;
    vno_pendaftaran VARCHAR;
    vpasienadmisi_id int;
    vstatus_bayar int;
    vcarabayar_id int;
    vpenjamin_nama VARCHAR;
    vpelayanan_id int;
    vtindakan_obat_id INT;
    vruangan_id INT;
    vkamarruangan_id INT;
    vpersentase_akomodasi VARCHAR;
    vkelaspelayanan_id INT;
    vkomponentarif_id INT;
    vqty FLOAT;
    vis_obat BOOLEAN;
    vis_cyto BOOLEAN;
    vharga_satuan FLOAT;
    vharga_total FLOAT;
    vtarifcyto_tindakan FLOAT;
    vpersen_cyto FLOAT;
    vpersentase VARCHAR;
    vadditional_data TEXT;
    
    rec_grouping RECORD;
    cur_grouping CURSOR FOR 
    SELECT 
            ruangan_id, 
            kelaspelayanan_id
        FROM tindakanpelayanan_t
        WHERE pendaftaran_id = xpendaftaran_id
        AND is_deleted IS FALSE 
        AND COALESCE(is_overwrite, FALSE) IS FALSE 
        AND tindakansudahbayar_id IS NULL
        AND parent_id IS NULL
        AND tipepaket_id IS NULL 
        AND instalasi_id <> 12
        GROUP BY ruangan_id, 
            kelaspelayanan_id;
    
    rec_grouping_akomodasi RECORD;
    cur_grouping_akomodasi CURSOR FOR 
    SELECT 
            ruangan_id, 
            kelaspelayanan_id,
            kamarruangan_id
        FROM tindakanpelayanan_t
        WHERE pendaftaran_id = xpendaftaran_id
        AND is_deleted IS FALSE 
        AND COALESCE(is_overwrite, FALSE) IS FALSE 
        AND tindakansudahbayar_id IS NULL
        AND parent_id IS NULL
        AND tipepaket_id IS NULL 
        AND instalasi_id <> 12
        AND daftartindakan_id IN (
            SELECT daftartindakan_id
            FROM daftartindakan_m 
            WHERE is_akomodasi IS TRUE
        )
        GROUP BY ruangan_id, 
            kelaspelayanan_id,
            kamarruangan_id;
    
    rec_grouping_obat RECORD;
    cur_grouping_obat CURSOR FOR 
        SELECT 
            kelaspelayanan_id
        FROM obatalkespasien_t
        WHERE pendaftaran_id = xpendaftaran_id 
        AND is_deleted IS FALSE 
        AND obatsudahbayar_id IS NULL
        AND ruangan_id NOT IN (
            SELECT ruangan_id 
            FROM ruangan_m
            WHERE instalasi_id = 12
        )
        GROUP BY kelaspelayanan_id;
    
    rec_tagihan RECORD;
    cur_tagihan CURSOR FOR 
        SELECT 
            tindakanpelayanan_id AS pelayanan_id,
            daftartindakan_id AS tindakan_obat_id,
            ruangan_id, 
            kelaspelayanan_id,
            qty_tindakan::float AS qty,
            FALSE AS is_obat,
            cyto_tindakan AS is_cyto
        FROM tindakanpelayanan_t
        WHERE pendaftaran_id = xpendaftaran_id 
        AND is_deleted IS FALSE 
        AND COALESCE(is_overwrite, FALSE) IS FALSE 
        AND tindakansudahbayar_id IS NULL
        AND parent_id IS NULL
        AND tipepaket_id IS NULL 
        AND instalasi_id <> 12
        UNION ALL 
        SELECT 
            obatalkespasien_id AS pelayanan_id,
            obatalkes_id AS tindakan_obat_id, 
            ruangan_id, 
            kelaspelayanan_id,
            CASE
                WHEN det IS NULL THEN qty_oa
                ELSE det
            END AS qty,
            TRUE AS is_obat,
            FALSE AS is_cyto
        FROM obatalkespasien_t
        WHERE pendaftaran_id = xpendaftaran_id 
        AND is_deleted IS FALSE 
        AND obatsudahbayar_id IS NULL
        AND ruangan_id NOT IN (
            SELECT ruangan_id 
            FROM ruangan_m
            WHERE instalasi_id = 12
        );
    
BEGIN
    SELECT no_pendaftaran, status_bayar::int, pasienadmisi_id INTO vno_pendaftaran, vstatus_bayar, vpasienadmisi_id
    FROM pendaftaran_t
    WHERE pendaftaran_id = xpendaftaran_id;
    
    SELECT carabayar_id, penjamin_nama INTO vcarabayar_id, vpenjamin_nama
    FROM penjamin_m
    WHERE penjamin_id = xpenjamin_id ;
    
    IF NOT EXISTS (
        SELECT 
                1
        FROM pendaftaran_t
        WHERE pendaftaran_id = xpendaftaran_id
        LIMIT 1
    ) 
    THEN
            vstatus := 1;
            vmessage := CONCAT('No Pendaftaran ' , vno_pendaftaran , ' Tidak Ditemukan') ;
    ELSE 
        IF(vstatus_bayar = 348)
        THEN 
            vstatus := 2;
            vmessage := CONCAT('No Pendaftaran ' , vno_pendaftaran , ' Sudah Dibayarkan') ;
        ELSE 
            IF NOT EXISTS ( 
                SELECT 1
                FROM penjamin_m
                WHERE penjamin_id = xpenjamin_id 
                LIMIT 1
            )
            THEN 
                vstatus := 3;
                vmessage := CONCAT('Penjamin ' , xpenjamin_id , ' Tidak Ditemukan') ;
                ELSE 
                    DROP TABLE IF EXISTS table_penampung_obat;
                    DROP TABLE IF EXISTS table_penampung_tarif;
                    CREATE TEMP TABLE IF NOT EXISTS table_penampung_tarif(
                        pendaftaran_id int4,
                        komponentarif_id int4,
                        ruangan_id int4,
                        daftartindakan_id int,
                        kelaspelayanan_id int,
                        harga_tarif FLOAT,
                        persen_cyto FLOAT,
                        kamarruangan_id int
                    );
                    
                    CREATE TEMP TABLE IF NOT EXISTS table_penampung_obat(
                        pendaftaran_id int4,
                        obatalkes_id int4,
                        kelaspelayanan_id int,
                        harga_obat FLOAT
                    );
                    
                    -- Open the cursor Untuk tindakan pelayanan
                    OPEN cur_grouping;

                    LOOP
                    -- fetch row into the film
                    FETCH cur_grouping INTO rec_grouping;
                    -- exit when no more row to fetch
                    EXIT WHEN NOT FOUND;
                    
                    vruangan_id := rec_grouping.ruangan_id;
                    vkelaspelayanan_id := rec_grouping.kelaspelayanan_id;
                    
                    INSERT INTO table_penampung_tarif(pendaftaran_id,komponentarif_id,ruangan_id,daftartindakan_id,kelaspelayanan_id,harga_tarif, persen_cyto)
                    SELECT xpendaftaran_id, komponentarif_id, vruangan_id, daftartindakan_id, vkelaspelayanan_id, harga_tariftindakan , persencyto_tindakan 
                    FROM tariftotalrs_fn(vruangan_id,xpenjamin_id,vkelaspelayanan_id,'pelayanan')
                    WHERE daftartindakan_id IN (
                        SELECT DISTINCT daftartindakan_id
                        FROM tindakanpelayanan_t 
                        WHERE pendaftaran_id = xpendaftaran_id
                        AND is_deleted IS FALSE 
                        AND COALESCE(is_overwrite,FALSE) IS FALSE 
                        AND tindakansudahbayar_id IS NULL
                        AND parent_id IS NULL
                        AND tipepaket_id IS NULL 
                        AND instalasi_id <> 12
                    );
                    
                    INSERT INTO table_penampung_tarif(pendaftaran_id,komponentarif_id,ruangan_id,daftartindakan_id,kelaspelayanan_id,harga_tarif, persen_cyto)
                    SELECT xpendaftaran_id, komponentarif_id, vruangan_id, daftartindakan_id, vkelaspelayanan_id, harga_tariftindakan , persencyto_tindakan 
                    FROM tarifkomponenrs_fn(vruangan_id,xpenjamin_id,vkelaspelayanan_id,'pelayanan')
                    WHERE daftartindakan_id IN (
                        SELECT DISTINCT daftartindakan_id
                        FROM tindakanpelayanan_t 
                        WHERE pendaftaran_id = xpendaftaran_id
                        AND is_deleted IS FALSE 
                        AND COALESCE(is_overwrite, FALSE) IS FALSE 
                        AND tindakansudahbayar_id IS NULL
                        AND parent_id IS NULL
                        AND tipepaket_id IS NULL 
                        AND instalasi_id <> 12
                    );
                    
                    
                    END LOOP;

                    -- Close the cursor
                    CLOSE cur_grouping;
                    
                    -----------------------------------------------------------
                    -- Open the cursor Untuk tindakan akomodasi
                    OPEN cur_grouping_akomodasi;

                    LOOP
                    -- fetch row into the film
                    FETCH cur_grouping_akomodasi INTO rec_grouping_akomodasi;
                    -- exit when no more row to fetch
                    EXIT WHEN NOT FOUND;
                    
                    vruangan_id := rec_grouping_akomodasi.ruangan_id;
                    vkelaspelayanan_id := rec_grouping_akomodasi.kelaspelayanan_id;
                    vkamarruangan_id := rec_grouping_akomodasi.kamarruangan_id;
                    
                    INSERT INTO table_penampung_tarif(pendaftaran_id,komponentarif_id,ruangan_id,daftartindakan_id,kelaspelayanan_id,harga_tarif, persen_cyto, kamarruangan_id)
                    SELECT DISTINCT xpendaftaran_id, komponentarif_id, vruangan_id, daftartindakan_id, vkelaspelayanan_id, harga_tariftindakan , persencyto_tindakan, kamarruangan_id 
                    FROM tariftotalkamarrs_fn(vruangan_id,xpenjamin_id,vkelaspelayanan_id,'kamar')
                    WHERE daftartindakan_id IN (
                        SELECT DISTINCT daftartindakan_id
                        FROM tindakanpelayanan_t 
                        WHERE pendaftaran_id = xpendaftaran_id
                        AND is_deleted IS FALSE 
                        AND COALESCE(is_overwrite, FALSE) IS FALSE 
                        AND tindakansudahbayar_id IS NULL
                        AND parent_id IS NULL
                        AND tipepaket_id IS NULL 
                        AND instalasi_id <> 12
                        AND daftartindakan_id IN (
                            SELECT daftartindakan_id
                            FROM daftartindakan_m 
                            WHERE is_akomodasi IS TRUE
                        )
                    );
                    
                    INSERT INTO table_penampung_tarif(pendaftaran_id,komponentarif_id,ruangan_id,daftartindakan_id,kelaspelayanan_id,harga_tarif, persen_cyto, kamarruangan_id)
                    SELECT DISTINCT xpendaftaran_id, komponentarif_id, vruangan_id, daftartindakan_id, vkelaspelayanan_id, harga_tariftindakan , persencyto_tindakan, kamarruangan_id 
                    FROM tarifkomponenkamarrs_fn(vruangan_id,xpenjamin_id,vkelaspelayanan_id,'kamar')
                    WHERE daftartindakan_id IN (
                        SELECT DISTINCT daftartindakan_id
                        FROM tindakanpelayanan_t 
                        WHERE pendaftaran_id = xpendaftaran_id
                        AND is_deleted IS FALSE 
                        AND COALESCE(is_overwrite, FALSE) IS FALSE 
                        AND tindakansudahbayar_id IS NULL
                        AND parent_id IS NULL
                        AND tipepaket_id IS NULL 
                        AND instalasi_id <> 12
                        AND daftartindakan_id IN (
                            SELECT daftartindakan_id
                            FROM daftartindakan_m 
                            WHERE is_akomodasi IS TRUE
                        )
                    );
                    
                    
                    END LOOP;

                    -- Close the cursor
                    CLOSE cur_grouping_akomodasi;
                    
                    -----------------------------------------------------------
                    -- Open the cursor Untuk Obat
                    OPEN cur_grouping_obat;

                    LOOP
                    -- fetch row into the film
                    FETCH cur_grouping_obat INTO rec_grouping_obat;
                    -- exit when no more row to fetch
                    EXIT WHEN NOT FOUND;
                    
                    vkelaspelayanan_id := rec_grouping_obat.kelaspelayanan_id;
                    
                    CREATE TEMP TABLE IF NOT EXISTS table_penampung_obat(
                        pendaftaran_id int4,
                        obatalkes_id int4,
                        kelaspelayanan_id int,
                        harga_obat FLOAT
                    );
                    
                    INSERT INTO table_penampung_obat(pendaftaran_id, obatalkes_id, kelaspelayanan_id, harga_obat)
                    SELECT 
                            xpendaftaran_id,
                            obatalkes_id,
                            vkelaspelayanan_id,
                            CEIL(hargajual)
                        FROM infostokobatalkes_fnr_new(xpenjamin_id, vkelaspelayanan_id, 25)
                        WHERE obatalkes_id IN (
                            SELECT obatalkes_id
                            FROM obatalkespasien_t
                            WHERE pendaftaran_id = xpendaftaran_id 
                            AND is_deleted IS FALSE 
                            AND obatsudahbayar_id IS NULL
                            AND ruangan_id NOT IN (
                                SELECT ruangan_id 
                                FROM ruangan_m
                                WHERE instalasi_id = 12
                            )
                            GROUP BY obatalkes_id
                        );
                    
                    END LOOP;

                    -- Close the cursor
                    CLOSE cur_grouping_obat;
                    
                    -------------------------------------------------- 
                    -- Open the cursor Proses recalculate
                    OPEN cur_tagihan;

                    LOOP
                    -- fetch row into the film
                    FETCH cur_tagihan INTO rec_tagihan;
                    -- exit when no more row to fetch
                    EXIT WHEN NOT FOUND;
                        
                    vpelayanan_id := rec_tagihan.pelayanan_id;
                    vtindakan_obat_id := rec_tagihan.tindakan_obat_id;
                    vruangan_id := rec_tagihan.ruangan_id;
                    vqty := rec_tagihan.qty;
                    vkelaspelayanan_id := rec_tagihan.kelaspelayanan_id;
                    vis_obat := rec_tagihan.is_obat;
                    vis_cyto := rec_tagihan.is_cyto;
                    
                    IF(vis_obat IS FALSE) -- proses tindakan
                    THEN 
                        
                        IF EXISTS(
                            SELECT 1
                            FROM daftartindakan_m
                            WHERE daftartindakan_id = vtindakan_obat_id
                            AND is_akomodasi IS TRUE
                            LIMIT 1
                        )
                        THEN 
                            SELECT harga_tarif INTO vharga_satuan
                            FROM table_penampung_tarif
                            WHERE pendaftaran_id = xpendaftaran_id
                            AND kelaspelayanan_id = vkelaspelayanan_id
                            AND ruangan_id = vruangan_id
                            AND daftartindakan_id = vtindakan_obat_id
                            AND komponentarif_id = 6
                            AND kamarruangan_id = vkamarruangan_id
                            LIMIT 1;
                            
                            vtarifcyto_tindakan := 0;
                            vharga_total := (vharga_satuan + vtarifcyto_tindakan) * vqty;
                            
                            IF(vharga_total IS NOT NULL)
                            THEN 
                                UPDATE tindakanpelayanan_t
                                SET penjamin_id = xpenjamin_id,
                                        carabayar_id = vcarabayar_id,
                                        tarif_satuan = vharga_satuan,
                                        tarif_tindakan = vharga_total,
                                        tarifcyto_tindakan = vtarifcyto_tindakan,
                                        last_modified_by = xuser_id,
                                        last_modified_date = CURRENT_TIMESTAMP::TIMESTAMP(0)
                                WHERE tindakanpelayanan_id = vpelayanan_id;
                                
                                UPDATE tindakankomponen_t
                                SET is_deleted = TRUE,
                                        deleted_by = xuser_id,
                                        deleted_date = CURRENT_TIMESTAMP::TIMESTAMP(0)
                                WHERE tindakanpelayanan_id = vpelayanan_id;
                                
                                INSERT INTO tindakankomponen_t(
                                    komponentarif_id, 
                                    tindakanpelayanan_id, 
                                    tarif_kompsatuan, 
                                    tarif_tindakankomp, 
                                    tarifcyto_tindakankomp, 
                                    subsidiasuransikomp, 
                                    subsidipemerintahkomp, 
                                    iurbiayakomp
                                )
                                SELECT 
                                    komponentarif_id,
                                    vpelayanan_id AS tindakanpelayanan_id ,
                                    harga_tarif AS tarif_kompsatuan, 
                                    CASE 
                                        WHEN vis_cyto IS TRUE 
                                        THEN harga_tarif + (harga_tarif * persen_cyto/100) 
                                        ELSE harga_tarif 
                                    END AS tarif_tindakankomp, 
                                    CASE 
                                        WHEN vis_cyto IS TRUE 
                                        THEN harga_tarif * persen_cyto/100 ELSE 0 
                                    END AS tarifcyto_tindakankomp, 
                                    0 AS subsidiasuransikomp, 
                                    0 AS subsidipemerintahkomp, 
                                    0 AS iurbiayakomp
                                FROM table_penampung_tarif
                                WHERE pendaftaran_id = xpendaftaran_id
                                AND kelaspelayanan_id = vkelaspelayanan_id
                                AND ruangan_id = vruangan_id
                                AND daftartindakan_id = vtindakan_obat_id
                                AND komponentarif_id <> 6
                                AND kamarruangan_id = vkamarruangan_id;
                            END IF;
                        ELSE 
                            
                            SELECT harga_tarif , persen_cyto INTO vharga_satuan, vpersen_cyto
                            FROM table_penampung_tarif
                            WHERE pendaftaran_id = xpendaftaran_id
                            AND kelaspelayanan_id = vkelaspelayanan_id
                            AND ruangan_id = vruangan_id
                            AND daftartindakan_id = vtindakan_obat_id
                            AND komponentarif_id = 6
                            LIMIT 1;
                            
                            IF(vis_cyto IS TRUE)
                            THEN
                                vtarifcyto_tindakan := (vharga_satuan * vpersen_cyto) / 100;
                                vharga_total := vharga_satuan + vtarifcyto_tindakan * vqty;
                            ELSE 
                                vtarifcyto_tindakan := 0;
                                vharga_total := (vharga_satuan + vtarifcyto_tindakan) * vqty;
                            END IF;
                            
                            IF(vpasienadmisi_id IS NULL)
                            THEN 
                                UPDATE tindakanpelayanan_t
                                SET penjamin_id = xpenjamin_id,
                                        carabayar_id = vcarabayar_id,
                                        tarif_satuan = vharga_satuan,
                                        tarif_tindakan = vharga_total,
                                        tarifcyto_tindakan = vtarifcyto_tindakan,
                                        last_modified_by = xuser_id,
                                        last_modified_date = CURRENT_TIMESTAMP::TIMESTAMP(0)
                                WHERE tindakanpelayanan_id = vpelayanan_id
                                AND pasienadmisi_id IS NULL;
                                
                                UPDATE tindakankomponen_t
                                SET is_deleted = TRUE,
                                        deleted_by = xuser_id,
                                        deleted_date = CURRENT_TIMESTAMP::TIMESTAMP(0)
                                WHERE tindakanpelayanan_id IN (
                                    SELECT 
                                        tindakanpelayanan_id
                                    FROM tindakanpelayanan_t
                                    WHERE tindakanpelayanan_id = vpelayanan_id
                                    AND pasienadmisi_id IS NULL
                                );
                                
                                IF EXISTS (
                                    SELECT
                                    FROM tindakanpelayanan_t
                                    WHERE tindakanpelayanan_id = vpelayanan_id
                                    AND pasienadmisi_id IS NULL
                                    LIMIT 1
                                )
                                THEN
                                    INSERT INTO tindakankomponen_t(
                                        komponentarif_id, 
                                        tindakanpelayanan_id, 
                                        tarif_kompsatuan, 
                                        tarif_tindakankomp, 
                                        tarifcyto_tindakankomp, 
                                        subsidiasuransikomp, 
                                        subsidipemerintahkomp, 
                                        iurbiayakomp
                                    )
                                    SELECT 
                                        komponentarif_id,
                                        vpelayanan_id AS tindakanpelayanan_id ,
                                        harga_tarif AS tarif_kompsatuan, 
                                        CASE 
                                            WHEN vis_cyto IS TRUE 
                                            THEN harga_tarif + (harga_tarif * persen_cyto/100) 
                                            ELSE harga_tarif 
                                        END AS tarif_tindakankomp, 
                                        CASE 
                                            WHEN vis_cyto IS TRUE 
                                            THEN harga_tarif * persen_cyto/100 ELSE 0 
                                        END AS tarifcyto_tindakankomp, 
                                        0 AS subsidiasuransikomp, 
                                        0 AS subsidipemerintahkomp, 
                                        0 AS iurbiayakomp
                                    FROM table_penampung_tarif
                                    WHERE pendaftaran_id = xpendaftaran_id
                                    AND kelaspelayanan_id = vkelaspelayanan_id
                                    AND ruangan_id = vruangan_id
                                    AND daftartindakan_id = vtindakan_obat_id
                                    AND komponentarif_id <> 6;
                                END IF;
                            ELSE 
                                UPDATE tindakanpelayanan_t
                                SET penjamin_id = xpenjamin_id,
                                        carabayar_id = vcarabayar_id,
                                        tarif_satuan = vharga_satuan,
                                        tarif_tindakan = vharga_total,
                                        tarifcyto_tindakan = vtarifcyto_tindakan,
                                        last_modified_by = xuser_id,
                                        last_modified_date = CURRENT_TIMESTAMP::TIMESTAMP(0)
                                WHERE tindakanpelayanan_id = vpelayanan_id
                                AND pasienadmisi_id IS NOT NULL;
                                
                                UPDATE tindakankomponen_t
                                SET is_deleted = TRUE,
                                        deleted_by = xuser_id,
                                        deleted_date = CURRENT_TIMESTAMP::TIMESTAMP(0)
                                WHERE tindakanpelayanan_id IN (
                                    SELECT 
                                        tindakanpelayanan_id
                                    FROM tindakanpelayanan_t
                                    WHERE tindakanpelayanan_id = vpelayanan_id
                                    AND pasienadmisi_id IS NOT NULL
                                );
                                
                                IF EXISTS (
                                    SELECT
                                    FROM tindakanpelayanan_t
                                    WHERE tindakanpelayanan_id = vpelayanan_id
                                    AND pasienadmisi_id IS NOT NULL
                                    LIMIT 1
                                )
                                THEN
                                    INSERT INTO tindakankomponen_t(
                                        komponentarif_id, 
                                        tindakanpelayanan_id, 
                                        tarif_kompsatuan, 
                                        tarif_tindakankomp, 
                                        tarifcyto_tindakankomp, 
                                        subsidiasuransikomp, 
                                        subsidipemerintahkomp, 
                                        iurbiayakomp
                                    )
                                    SELECT 
                                        komponentarif_id,
                                        vpelayanan_id AS tindakanpelayanan_id ,
                                        harga_tarif AS tarif_kompsatuan, 
                                        CASE 
                                            WHEN vis_cyto IS TRUE 
                                            THEN harga_tarif + (harga_tarif * persen_cyto/100) 
                                            ELSE harga_tarif 
                                        END AS tarif_tindakankomp, 
                                        CASE 
                                            WHEN vis_cyto IS TRUE 
                                            THEN harga_tarif * persen_cyto/100 ELSE 0 
                                        END AS tarifcyto_tindakankomp, 
                                        0 AS subsidiasuransikomp, 
                                        0 AS subsidipemerintahkomp, 
                                        0 AS iurbiayakomp
                                    FROM table_penampung_tarif
                                    WHERE pendaftaran_id = xpendaftaran_id
                                    AND kelaspelayanan_id = vkelaspelayanan_id
                                    AND ruangan_id = vruangan_id
                                    AND daftartindakan_id = vtindakan_obat_id
                                    AND komponentarif_id <> 6;
                                END IF;
                            END IF;
                        END IF;
                    ELSE -- proses obat
                        SELECT harga_obat INTO vharga_satuan
                        FROM table_penampung_obat
                        WHERE obatalkes_id = vtindakan_obat_id
                        AND kelaspelayanan_id = vkelaspelayanan_id
                        LIMIT 1;
                        
                        IF(vpasienadmisi_id IS NULL) 
                        THEN                        
                            UPDATE obatalkespasien_t
                            SET hargasatuan_oa = vharga_satuan,
                                    hargajual_oa = vharga_satuan * vqty ,
                                    penjamin_id = xpenjamin_id,
                                    carabayar_id = vcarabayar_id,
                                    last_modified_by = xuser_id,
                                    last_modified_date = CURRENT_TIMESTAMP::TIMESTAMP(0)
                            WHERE obatalkespasien_id = vpelayanan_id
                            AND pasienadmisi_id IS NULL;
                        ELSE 
                            UPDATE obatalkespasien_t
                            SET hargasatuan_oa = vharga_satuan,
                                    hargajual_oa = vharga_satuan * vqty ,
                                    penjamin_id = xpenjamin_id,
                                    carabayar_id = vcarabayar_id,
                                    last_modified_by = xuser_id,
                                    last_modified_date = CURRENT_TIMESTAMP::TIMESTAMP(0)
                            WHERE obatalkespasien_id = vpelayanan_id
                            AND pasienadmisi_id IS NOT NULL;
                        END IF;
                    END IF;
                    vmessage := CONCAT('CASE 4 ') ;
                        
                    END LOOP;

                    -- Close the cursor
                    CLOSE cur_tagihan;
                    
                    vmessage := CONCAT('CASE 5 ') ;
                
                    -- Reset Edit Tagihan
                    DELETE FROM infotagihanpasien_r WHERE pendaftaran_id = xpendaftaran_id;
                
                    IF(vpasienadmisi_id IS NOT NULL)
                    THEN 
                        UPDATE pasienadmisi_t
                        SET penjamin_id = xpenjamin_id,
                                carabayar_id = vcarabayar_id
                        WHERE pasienadmisi_id = vpasienadmisi_id;
                        
                        UPDATE tindakanpelayanan_t
                        SET penjamin_id = xpenjamin_id,
                                carabayar_id = vcarabayar_id 
                        WHERE pendaftaran_id = xpendaftaran_id
                        AND is_deleted IS FALSE 
                        AND penjamin_id <> xpenjamin_id
                        AND pasienadmisi_id IS NOT NULL;
                        
                        UPDATE obatalkespasien_t
                        SET penjamin_id = xpenjamin_id,
                                carabayar_id = vcarabayar_id 
                        WHERE pendaftaran_id = xpendaftaran_id
                        AND is_deleted IS FALSE 
                        AND penjamin_id <> xpenjamin_id
                        AND pasienadmisi_id IS NOT NULL;
                    ELSE 
                        UPDATE pendaftaran_t
                        SET penjamin_id = xpenjamin_id,
                                carabayar_id = vcarabayar_id
                        WHERE pendaftaran_id = xpendaftaran_id;
                        
                        UPDATE tindakanpelayanan_t
                        SET penjamin_id = xpenjamin_id,
                                carabayar_id = vcarabayar_id 
                        WHERE pendaftaran_id = xpendaftaran_id
                        AND is_deleted IS FALSE 
                        AND penjamin_id <> xpenjamin_id
                        AND pasienadmisi_id IS NULL;
                        
                        UPDATE obatalkespasien_t
                        SET penjamin_id = xpenjamin_id,
                                carabayar_id = vcarabayar_id 
                        WHERE pendaftaran_id = xpendaftaran_id
                        AND is_deleted IS FALSE 
                        AND penjamin_id <> xpenjamin_id
                        AND pasienadmisi_id IS NULL;
                    END IF;
                    
                    DROP TABLE IF EXISTS table_penampung_tarif;
                    DROP TABLE IF EXISTS table_penampung_obat;
                    
                    vstatus := 0;
                    vmessage := 'Success';
                
            END IF;
        END IF;
    END IF;
    
    RETURN QUERY 
    SELECT vstatus, vmessage;
        
END; $BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000