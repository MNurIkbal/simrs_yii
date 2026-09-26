<?php

use yii\db\Migration;

/**
 * Class m201020_041310_oddo_functionrekap_20201020
 */
class m201020_041310_oddo_functionrekap_20201020 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"adjusmenobatkeluar_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN
------------------------------->INSERT table history adjusmenobatkeluar_r<----------------------------------
        INSERT INTO adjusmenobatkeluar_r (      
                adjusmenobatkeluar_id,
                adjusmenobat_id,
                obatalkes_id,
                qty,
                satuankecil_id,
                alasan,
                satuanbesar_id,
                qty_konversi,
                satuankonversi_id,
                additional_data,
                created_date,
                created_by,
                modified_count,
                last_modified_date,
                last_modified_by,
                is_deleted,
                is_active,
                deleted_date,
                deleted_by,
                no_batch,
                keterangan,
                keterangan_rekap
        )VALUES(
                NEW.adjusmenobatkeluar_id,
                NEW.adjusmenobat_id,
                NEW.obatalkes_id,
                NEW.qty,
                NEW.satuankecil_id,
                NEW.alasan,
                NEW.satuanbesar_id,
                NEW.qty_konversi,
                NEW.satuankonversi_id,
                NEW.additional_data,
                NEW.created_date,
                NEW.created_by,
                NEW.modified_count,
                NEW.last_modified_date,
                NEW.last_modified_by,
                NEW.is_deleted,
                NEW.is_active,
                NEW.deleted_date,
                NEW.deleted_by,
                NEW.no_batch,
                NEW.keterangan,
                'ACCRUAL'
            );

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"adjusmenobatmasuk_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN
------------------------------->INSERT table history adjusmenobatmasuk_r<----------------------------------
        INSERT INTO adjusmenobatmasuk_r (       
                adjusmenobatmasuk_id,
                adjusmenobat_id,
                obatalkes_id,
                tgl_kadaluarsa,
                qty,
                satuankecil_id,
                harga_netto,
                satuanbesar_id,
                qty_konversi,
                satuankonversi_id,
                additional_data,
                created_date,
                created_by,
                modified_count,
                last_modified_date,
                last_modified_by,
                is_deleted,
                is_active,
                deleted_date,
                deleted_by,
                no_batch,
                keterangan,
                keterangan_rekap
        )VALUES(
                NEW.adjusmenobatmasuk_id,
                NEW.adjusmenobat_id,
                NEW.obatalkes_id,
                NEW.tgl_kadaluarsa,
                NEW.qty,
                NEW.satuankecil_id,
                NEW.harga_netto,
                NEW.satuanbesar_id,
                NEW.qty_konversi,
                NEW.satuankonversi_id,
                NEW.additional_data,
                NEW.created_date,
                NEW.created_by,
                NEW.modified_count,
                NEW.last_modified_date,
                NEW.last_modified_by,
                NEW.is_deleted,
                NEW.is_active,
                NEW.deleted_date,
                NEW.deleted_by,
                NEW.no_batch,
                NEW.keterangan,
                'ACCRUAL'
            );

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"bayaruangmuka_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    
        
BEGIN
        -- INSERT table history bayaruangmuka_r
        INSERT INTO bayaruangmuka_r (       
        bayaruangmuka_id,
        pembatalanuangmuka_id,
        pasienadmisi_id,
        pemakaianuangmuka_id,
        tandabuktibayar_id,
        ruangan_id,
        pasien_id,
        pendaftaran_id,
        tgl_uangmuka,
        jumlah_uangmuka,
        keterangan_uangmuka,
        tgl_perjanjian,
        keterangan_perjanjian,
        pembayarankapitasidetail_id,
        no_uangmuka,
        pengembalianuangmuka_id,
        metode_pembayaran,
        jenisnontunai_id,
        additional_data,
        created_date,
        created_by,
        modified_count,
        last_modified_date,
        last_modified_by,
        is_deleted,
        is_active,
        deleted_date,
        deleted_by,
        keterangan
        )VALUES(
        NEW.bayaruangmuka_id ,
        NEW.pembatalanuangmuka_id ,
        NEW.pasienadmisi_id ,
        NEW.pemakaianuangmuka_id ,
        NEW.tandabuktibayar_id ,
        NEW.ruangan_id ,
        NEW.pasien_id ,
        NEW.pendaftaran_id ,
        NEW.tgl_uangmuka ,
        NEW.jumlah_uangmuka ,
        NEW.keterangan_uangmuka ,
        NEW.tgl_perjanjian ,
        NEW.keterangan_perjanjian ,
        NEW.pembayarankapitasidetail_id ,
        NEW.no_uangmuka ,
        NEW.pengembalianuangmuka_id ,
        NEW.metode_pembayaran ,
        NEW.jenisnontunai_id ,
        NEW.additional_data ,
        NEW.created_date ,
        NEW.created_by ,
        NEW.modified_count ,
        NEW.last_modified_date ,
        NEW.last_modified_by ,
        NEW.is_deleted ,
        NEW.is_active ,
        NEW.deleted_date ,
        NEW.deleted_by ,
            'ACCRUAL'
        );

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"bayaruangmuka_r_update\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
    v_keterangan VARCHAR;
BEGIN
    IF(NEW.is_deleted IS TRUE)
    THEN
        v_keterangan := 'ACCRUAL REVERSAL';
        -- INSERT table history bayaruangmuka_r REVERSAL
            INSERT INTO bayaruangmuka_r (       
            bayaruangmuka_id ,
            pembatalanuangmuka_id ,
            pasienadmisi_id ,
            pemakaianuangmuka_id ,
            tandabuktibayar_id ,
            ruangan_id ,
            pasien_id ,
            pendaftaran_id ,
            tgl_uangmuka ,
            jumlah_uangmuka ,
            keterangan_uangmuka ,
            tgl_perjanjian ,
            keterangan_perjanjian ,
            pembayarankapitasidetail_id ,
            no_uangmuka ,
            pengembalianuangmuka_id ,
            metode_pembayaran ,
            jenisnontunai_id ,
            additional_data ,
            created_date ,
            created_by ,
            modified_count ,
            last_modified_date ,
            last_modified_by ,
            is_deleted ,
            is_active ,
            deleted_date ,
            deleted_by ,
            keterangan
        )VALUES(
            OLD.bayaruangmuka_id ,
            OLD.pembatalanuangmuka_id ,
            OLD.pasienadmisi_id ,
            OLD.pemakaianuangmuka_id ,
            OLD.tandabuktibayar_id ,
            OLD.ruangan_id ,
            OLD.pasien_id ,
            OLD.pendaftaran_id ,
            OLD.tgl_uangmuka ,
            -1 * OLD.jumlah_uangmuka ,
            OLD.keterangan_uangmuka ,
            OLD.tgl_perjanjian ,
            OLD.keterangan_perjanjian ,
            OLD.pembayarankapitasidetail_id ,
            OLD.no_uangmuka ,
            OLD.pengembalianuangmuka_id ,
            OLD.metode_pembayaran ,
            OLD.jenisnontunai_id ,
            OLD.additional_data ,
            OLD.created_date ,
            OLD.created_by ,
            OLD.modified_count ,
            OLD.last_modified_date ,
            OLD.last_modified_by ,
            OLD.is_deleted ,
            OLD.is_active ,
            OLD.deleted_date ,
            OLD.deleted_by ,
            v_keterangan
        );
            ELSE
        v_keterangan := 'ACCRUAL';
        
        -- INSERT table history bayaruangmuka_r REVERSAL
            INSERT INTO bayaruangmuka_r (       
            bayaruangmuka_id ,
            pembatalanuangmuka_id ,
            pasienadmisi_id ,
            pemakaianuangmuka_id ,
            tandabuktibayar_id ,
            ruangan_id ,
            pasien_id ,
            pendaftaran_id ,
            tgl_uangmuka ,
            jumlah_uangmuka ,
            keterangan_uangmuka ,
            tgl_perjanjian ,
            keterangan_perjanjian ,
            pembayarankapitasidetail_id ,
            no_uangmuka ,
            pengembalianuangmuka_id ,
            metode_pembayaran ,
            jenisnontunai_id ,
            additional_data ,
            created_date ,
            created_by ,
            modified_count ,
            last_modified_date ,
            last_modified_by ,
            is_deleted ,
            is_active ,
            deleted_date ,
            deleted_by ,
            keterangan
        )VALUES(
            OLD.bayaruangmuka_id ,
            OLD.pembatalanuangmuka_id ,
            OLD.pasienadmisi_id ,
            OLD.pemakaianuangmuka_id ,
            OLD.tandabuktibayar_id ,
            OLD.ruangan_id ,
            OLD.pasien_id ,
            OLD.pendaftaran_id ,
            OLD.tgl_uangmuka ,
            -1 * OLD.jumlah_uangmuka ,
            OLD.keterangan_uangmuka ,
            OLD.tgl_perjanjian ,
            OLD.keterangan_perjanjian ,
            OLD.pembayarankapitasidetail_id ,
            OLD.no_uangmuka ,
            OLD.pengembalianuangmuka_id ,
            OLD.metode_pembayaran ,
            OLD.jenisnontunai_id ,
            OLD.additional_data ,
            OLD.created_date ,
            OLD.created_by ,
            OLD.modified_count ,
            OLD.last_modified_date ,
            OLD.last_modified_by ,
            OLD.is_deleted ,
            OLD.is_active ,
            OLD.deleted_date ,
            OLD.deleted_by ,
            'ACCRUAL REVERSAL'
        );
        
    -- INSERT table history bayaruangmuka_r
        INSERT INTO bayaruangmuka_r (       
            bayaruangmuka_id ,
            pembatalanuangmuka_id ,
            pasienadmisi_id ,
            pemakaianuangmuka_id ,
            tandabuktibayar_id ,
            ruangan_id ,
            pasien_id ,
            pendaftaran_id ,
            tgl_uangmuka ,
            jumlah_uangmuka ,
            keterangan_uangmuka ,
            tgl_perjanjian ,
            keterangan_perjanjian ,
            pembayarankapitasidetail_id ,
            no_uangmuka ,
            pengembalianuangmuka_id ,
            metode_pembayaran ,
            jenisnontunai_id ,
            additional_data ,
            created_date ,
            created_by ,
            modified_count ,
            last_modified_date ,
            last_modified_by ,
            is_deleted ,
            is_active ,
            deleted_date ,
            deleted_by ,
            keterangan
        )VALUES(
            NEW.bayaruangmuka_id ,
            NEW.pembatalanuangmuka_id ,
            NEW.pasienadmisi_id ,
            NEW.pemakaianuangmuka_id ,
            NEW.tandabuktibayar_id ,
            NEW.ruangan_id ,
            NEW.pasien_id ,
            NEW.pendaftaran_id ,
            NEW.tgl_uangmuka ,
            NEW.jumlah_uangmuka ,
            NEW.keterangan_uangmuka ,
            NEW.tgl_perjanjian ,
            NEW.keterangan_perjanjian ,
            NEW.pembayarankapitasidetail_id ,
            NEW.no_uangmuka ,
            NEW.pengembalianuangmuka_id ,
            NEW.metode_pembayaran ,
            NEW.jenisnontunai_id ,
            NEW.additional_data ,
            NEW.created_date ,
            NEW.created_by ,
            NEW.modified_count ,
            NEW.last_modified_date ,
            NEW.last_modified_by ,
            NEW.is_deleted ,
            NEW.is_active ,
            NEW.deleted_date ,
            NEW.deleted_by ,
            v_keterangan
        );
    END IF;
    
    
    

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"generate_obatalkespasien\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$DECLARE
    vId integer := 70; -- 
    vPrefix varchar;
    vLast varchar;
    vYear varchar;
    vMonth varchar;
    vDate varchar;
    v_Nomor varchar;
    vIdPenjualan integer;
    
BEGIN
    
    SELECT 
        prefix,
        date_part('YEAR',now()) as year, 
        date_part('month',now()) as month,
        date_part('day',now()) as date,
        (
            SELECT CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0')) last_no
                FROM penomoran_k where penomoran_id = vId
        )
    INTO
        vPrefix,
        vYear,
        vMonth,
        vDate,
        vLast
        
    FROM penomoran_k WHERE penomoran_id = vId;
    v_Nomor = vPrefix || vYear || vMonth || vDate || vLast;

    UPDATE penomoran_k SET
        last_number = vLast,
        last_generate = V_Nomor
    WHERE penomoran_id = vId;
    
    NEW.no_obatalkespasien = v_Nomor;

    RETURN NEW;
END\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");
      
      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"generate_tindakanpelayanan\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$DECLARE
    vId integer := 69; -- 
    vPrefix varchar;
    vLast varchar;
    vYear varchar;
    vMonth varchar;
    vDate varchar;
    v_Nomor varchar;
    vIdPenjualan integer;
    
BEGIN
    
    SELECT 
        prefix,
        date_part('YEAR',now()) as year, 
        date_part('month',now()) as month,
        date_part('day',now()) as date,
        (
            SELECT CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0')) last_no
                FROM penomoran_k where penomoran_id = vId
        )
    INTO
        vPrefix,
        vYear,
        vMonth,
        vDate,
        vLast
        
    FROM penomoran_k WHERE penomoran_id = vId;
    v_Nomor = vPrefix || vYear || vMonth || vDate || vLast;

    UPDATE penomoran_k SET
        last_number = vLast,
        last_generate = V_Nomor
    WHERE penomoran_id = vId;
    
    NEW.no_tindakanpelayanan = v_Nomor;

    RETURN NEW;
END\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"int_obatalkespasien_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN
------------------------------->INSERT table history int_obatalkespasien_r<----------------------------------
        INSERT INTO int_obatalkespasien_r (     
        obatalkespasien_id ,
        sumberdana_id ,
        racikan_id ,
        returresepdetail_id ,
        tipepaket_id ,
        ruangan_id ,
        carabayar_id ,
        pegawai_id ,
        daftartindakan_id ,
        tindakanpelayanan_id ,
        satuankecil_id ,
        shift_id ,
        pendaftaran_id ,
        obatalkes_id ,
        pasien_id ,
        penjamin_id ,
        kelaspelayanan_id ,
        pasienanastesi_id ,
        pasienmasukpenunjang_id ,
        pasienadmisi_id ,
        obatsudahbayar_id ,
        penjualanresep_id ,
        tglpelayanan ,
        r ,
        rke ,
        permintaan_oa ,
        jmlkemasan_oa ,
        kekuatan_oa ,
        satuankekuatan_oa ,
        qty_oa ,
        hargasatuan_oa ,
        signa_oa ,
        harganetto_oa ,
        hargajual_oa , 
        etiket ,
        jmlexposerad ,
        kontrasrad ,
        biayaservice ,
        biayakonseling ,
        jasadokterresep ,
        biayakemasan ,
        biayaadministrasi ,
        tarifcyto ,
        discount , 
        subsidiasuransi ,
        subsidipemerintah ,
        subsidirs ,
        iurbiaya ,
        oa ,
        pembulatan ,
        verifikasitagihan_id ,
        jurnalrekening_id ,
        permohonanoadetail_id ,
        persenppnjual ,
        resepturdetail_id ,
        nilaippnjual ,
        perawat1_id ,
        perawat2_id ,
        instruksitindakanbmhp_id ,
        implementasi_id ,
        is_dilakukan ,
        pemakaianambulan_id ,
        is_jurnal ,
        konfigmargindetail_id ,
        qty_konversi ,
        is_penatajasa ,
        det ,
        status_bmhp ,
        det_konversi ,
        signa ,
        additional_data ,
        created_date ,
        created_by ,
        modified_count ,
        last_modified_date ,
        last_modified_by ,
        is_deleted ,
        is_active ,
        deleted_date , 
        deleted_by ,
        keterangan,
        no_obatalkespasien
        )VALUES(
        NEW.obatalkespasien_id ,
        NEW.sumberdana_id ,
        NEW.racikan_id ,
        NEW.returresepdetail_id ,
        NEW.tipepaket_id ,
        NEW.ruangan_id ,
        NEW.carabayar_id ,
        NEW.pegawai_id ,
        NEW.daftartindakan_id ,
        NEW.tindakanpelayanan_id ,
        NEW.satuankecil_id ,
        NEW.shift_id ,
        NEW.pendaftaran_id ,
        NEW.obatalkes_id ,
        NEW.pasien_id ,
        NEW.penjamin_id ,
        NEW.kelaspelayanan_id ,
        NEW.pasienanastesi_id ,
        NEW.pasienmasukpenunjang_id ,
        NEW.pasienadmisi_id ,
        NEW.obatsudahbayar_id ,
        NEW.penjualanresep_id ,
        NEW.tglpelayanan ,
        NEW.r ,
        NEW.rke ,
        NEW.permintaan_oa ,
        NEW.jmlkemasan_oa ,
        NEW.kekuatan_oa ,
        NEW.satuankekuatan_oa ,
        NEW.qty_oa ,
        NEW.hargasatuan_oa ,
        NEW.signa_oa ,
        NEW.harganetto_oa ,
        NEW.hargajual_oa , 
        NEW.etiket ,
        NEW.jmlexposerad ,
        NEW.kontrasrad ,
        NEW.biayaservice ,
        NEW.biayakonseling ,
        NEW.jasadokterresep ,
        NEW.biayakemasan ,
        NEW.biayaadministrasi ,
        NEW.tarifcyto ,
        NEW.discount , 
        NEW.subsidiasuransi ,
        NEW.subsidipemerintah ,
        NEW.subsidirs ,
        NEW.iurbiaya ,
        NEW.oa ,
        NEW.pembulatan ,
        NEW.verifikasitagihan_id ,
        NEW.jurnalrekening_id ,
        NEW.permohonanoadetail_id ,
        NEW.persenppnjual ,
        NEW.resepturdetail_id ,
        NEW.nilaippnjual ,
        NEW.perawat1_id ,
        NEW.perawat2_id ,
        NEW.instruksitindakanbmhp_id ,
        NEW.implementasi_id ,
        NEW.is_dilakukan ,
        NEW.pemakaianambulan_id ,
        NEW.is_jurnal ,
        NEW.konfigmargindetail_id ,
        NEW.qty_konversi ,
        NEW.is_penatajasa ,
        NEW.det ,
        NEW.status_bmhp ,
        NEW.det_konversi ,
        NEW.signa ,
        NEW.additional_data ,
        NEW.created_date ,
        NEW.created_by ,
        NEW.modified_count ,
        NEW.last_modified_date ,
        NEW.last_modified_by ,
        NEW.is_deleted ,
        NEW.is_active ,
        NEW.deleted_date , 
        NEW.deleted_by ,
            'ACCRUAL',
        NEW.no_obatalkespasien
        );

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"int_obatalkespasien_r_update\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$ 
    
    DECLARE
        v_keterangan VARCHAR;
    BEGIN
        IF(NEW.is_deleted IS TRUE)
        THEN
            v_keterangan := 'ACCRUAL REVERSAL';
            
            -- INSERT table history int_obatalkespasien_r REVERSAL
                INSERT INTO int_obatalkespasien_r (     
                obatalkespasien_id ,
                sumberdana_id ,
                racikan_id ,
                returresepdetail_id ,
                tipepaket_id ,
                ruangan_id ,
                carabayar_id ,
                pegawai_id ,
                daftartindakan_id ,
                tindakanpelayanan_id ,
                satuankecil_id ,
                shift_id ,
                pendaftaran_id ,
                obatalkes_id ,
                pasien_id ,
                penjamin_id ,
                kelaspelayanan_id ,
                pasienanastesi_id ,
                pasienmasukpenunjang_id ,
                pasienadmisi_id ,
                obatsudahbayar_id ,
                penjualanresep_id ,
                tglpelayanan ,
                r ,
                rke ,
                permintaan_oa ,
                jmlkemasan_oa ,
                kekuatan_oa ,
                satuankekuatan_oa ,
                qty_oa ,
                hargasatuan_oa ,
                signa_oa ,
                harganetto_oa ,
                hargajual_oa , 
                etiket ,
                jmlexposerad ,
                kontrasrad ,
                biayaservice ,
                biayakonseling ,
                jasadokterresep ,
                biayakemasan ,
                biayaadministrasi ,
                tarifcyto ,
                discount , 
                subsidiasuransi ,
                subsidipemerintah ,
                subsidirs ,
                iurbiaya ,
                oa ,
                pembulatan ,
                verifikasitagihan_id ,
                jurnalrekening_id ,
                permohonanoadetail_id ,
                persenppnjual ,
                resepturdetail_id ,
                nilaippnjual ,
                perawat1_id ,
                perawat2_id ,
                instruksitindakanbmhp_id ,
                implementasi_id ,
                is_dilakukan ,
                pemakaianambulan_id ,
                is_jurnal ,
                konfigmargindetail_id ,
                qty_konversi ,
                is_penatajasa ,
                det ,
                status_bmhp ,
                det_konversi ,
                signa ,
                additional_data ,
                created_date ,
                created_by ,
                modified_count ,
                last_modified_date ,
                last_modified_by ,
                is_deleted ,
                is_active ,
                deleted_date , 
                deleted_by ,
                keterangan,
                no_obatalkespasien
            )VALUES(
                OLD.obatalkespasien_id ,
                OLD.sumberdana_id ,
                OLD.racikan_id ,
                OLD.returresepdetail_id ,
                OLD.tipepaket_id ,
                OLD.ruangan_id ,
                OLD.carabayar_id ,
                OLD.pegawai_id ,
                OLD.daftartindakan_id ,
                OLD.tindakanpelayanan_id ,
                OLD.satuankecil_id ,
                OLD.shift_id ,
                OLD.pendaftaran_id ,
                OLD.obatalkes_id ,
                OLD.pasien_id ,
                OLD.penjamin_id ,
                OLD.kelaspelayanan_id ,
                OLD.pasienanastesi_id ,
                OLD.pasienmasukpenunjang_id ,
                OLD.pasienadmisi_id ,
                OLD.obatsudahbayar_id ,
                OLD.penjualanresep_id ,
                OLD.tglpelayanan ,
                OLD.r ,
                OLD.rke ,
                OLD.permintaan_oa ,
                OLD.jmlkemasan_oa ,
                OLD.kekuatan_oa ,
                OLD.satuankekuatan_oa ,
                -1 * OLD.qty_oa ,
                -1 * OLD.hargasatuan_oa ,
                OLD.signa_oa ,
                -1 * OLD.harganetto_oa ,
                -1 * OLD.hargajual_oa , 
                OLD.etiket ,
                -1 * OLD.jmlexposerad ,
                OLD.kontrasrad ,
                -1 * OLD.biayaservice ,
                -1 * OLD.biayakonseling ,
                -1 * OLD.jasadokterresep ,
                -1 * OLD.biayakemasan ,
                -1 * OLD.biayaadministrasi ,
                -1 * OLD.tarifcyto ,
                -1 * OLD.discount , 
                -1 * OLD.subsidiasuransi ,
                -1 * OLD.subsidipemerintah ,
                -1 * OLD.subsidirs ,
                -1 * OLD.iurbiaya ,
                OLD.oa ,
                -1 * OLD.pembulatan ,
                OLD.verifikasitagihan_id ,
                OLD.jurnalrekening_id ,
                OLD.permohonanoadetail_id ,
                OLD.persenppnjual ,
                OLD.resepturdetail_id ,
                -1 * OLD.nilaippnjual ,
                OLD.perawat1_id ,
                OLD.perawat2_id ,
                OLD.instruksitindakanbmhp_id ,
                OLD.implementasi_id ,
                OLD.is_dilakukan ,
                OLD.pemakaianambulan_id ,
                OLD.is_jurnal ,
                OLD.konfigmargindetail_id ,
                -1 * OLD.qty_konversi ,
                OLD.is_penatajasa ,
                OLD.det ,
                OLD.status_bmhp ,
                OLD.det_konversi ,
                OLD.signa ,
                OLD.additional_data ,
                OLD.created_date ,
                OLD.created_by ,
                OLD.modified_count ,
                OLD.last_modified_date ,
                OLD.last_modified_by ,
                OLD.is_deleted ,
                OLD.is_active ,
                OLD.deleted_date , 
                OLD.deleted_by ,
                v_keterangan,
                OLD.no_obatalkespasien
            );
        ELSE
            v_keterangan := 'ACCRUAL';
            
            -- INSERT table history int_obatalkespasien_r REVERSAL
                INSERT INTO int_obatalkespasien_r (     
                obatalkespasien_id ,
                sumberdana_id ,
                racikan_id ,
                returresepdetail_id ,
                tipepaket_id ,
                ruangan_id ,
                carabayar_id ,
                pegawai_id ,
                daftartindakan_id ,
                tindakanpelayanan_id ,
                satuankecil_id ,
                shift_id ,
                pendaftaran_id ,
                obatalkes_id ,
                pasien_id ,
                penjamin_id ,
                kelaspelayanan_id ,
                pasienanastesi_id ,
                pasienmasukpenunjang_id ,
                pasienadmisi_id ,
                obatsudahbayar_id ,
                penjualanresep_id ,
                tglpelayanan ,
                r ,
                rke ,
                permintaan_oa ,
                jmlkemasan_oa ,
                kekuatan_oa ,
                satuankekuatan_oa ,
                qty_oa ,
                hargasatuan_oa ,
                signa_oa ,
                harganetto_oa ,
                hargajual_oa , 
                etiket ,
                jmlexposerad ,
                kontrasrad ,
                biayaservice ,
                biayakonseling ,
                jasadokterresep ,
                biayakemasan ,
                biayaadministrasi ,
                tarifcyto ,
                discount , 
                subsidiasuransi ,
                subsidipemerintah ,
                subsidirs ,
                iurbiaya ,
                oa ,
                pembulatan ,
                verifikasitagihan_id ,
                jurnalrekening_id ,
                permohonanoadetail_id ,
                persenppnjual ,
                resepturdetail_id ,
                nilaippnjual ,
                perawat1_id ,
                perawat2_id ,
                instruksitindakanbmhp_id ,
                implementasi_id ,
                is_dilakukan ,
                pemakaianambulan_id ,
                is_jurnal ,
                konfigmargindetail_id ,
                qty_konversi ,
                is_penatajasa ,
                det ,
                status_bmhp ,
                det_konversi ,
                signa ,
                additional_data ,
                created_date ,
                created_by ,
                modified_count ,
                last_modified_date ,
                last_modified_by ,
                is_deleted ,
                is_active ,
                deleted_date , 
                deleted_by ,
                keterangan,
                no_obatalkespasien
            )VALUES(
                OLD.obatalkespasien_id ,
                OLD.sumberdana_id ,
                OLD.racikan_id ,
                OLD.returresepdetail_id ,
                OLD.tipepaket_id ,
                OLD.ruangan_id ,
                OLD.carabayar_id ,
                OLD.pegawai_id ,
                OLD.daftartindakan_id ,
                OLD.tindakanpelayanan_id ,
                OLD.satuankecil_id ,
                OLD.shift_id ,
                OLD.pendaftaran_id ,
                OLD.obatalkes_id ,
                OLD.pasien_id ,
                OLD.penjamin_id ,
                OLD.kelaspelayanan_id ,
                OLD.pasienanastesi_id ,
                OLD.pasienmasukpenunjang_id ,
                OLD.pasienadmisi_id ,
                OLD.obatsudahbayar_id ,
                OLD.penjualanresep_id ,
                OLD.tglpelayanan ,
                OLD.r ,
                OLD.rke ,
                OLD.permintaan_oa ,
                OLD.jmlkemasan_oa ,
                OLD.kekuatan_oa ,
                OLD.satuankekuatan_oa ,
                -1 * OLD.qty_oa ,
                -1 * OLD.hargasatuan_oa ,
                OLD.signa_oa ,
                -1 * OLD.harganetto_oa ,
                -1 * OLD.hargajual_oa , 
                OLD.etiket ,
                -1 * OLD.jmlexposerad ,
                OLD.kontrasrad ,
                -1 * OLD.biayaservice ,
                -1 * OLD.biayakonseling ,
                -1 * OLD.jasadokterresep ,
                -1 * OLD.biayakemasan ,
                -1 * OLD.biayaadministrasi ,
                -1 * OLD.tarifcyto ,
                -1 * OLD.discount , 
                -1 * OLD.subsidiasuransi ,
                -1 * OLD.subsidipemerintah ,
                -1 * OLD.subsidirs ,
                -1 * OLD.iurbiaya ,
                OLD.oa ,
                -1 * OLD.pembulatan ,
                OLD.verifikasitagihan_id ,
                OLD.jurnalrekening_id ,
                OLD.permohonanoadetail_id ,
                OLD.persenppnjual ,
                OLD.resepturdetail_id ,
                -1 * OLD.nilaippnjual ,
                OLD.perawat1_id ,
                OLD.perawat2_id ,
                OLD.instruksitindakanbmhp_id ,
                OLD.implementasi_id ,
                OLD.is_dilakukan ,
                OLD.pemakaianambulan_id ,
                OLD.is_jurnal ,
                OLD.konfigmargindetail_id ,
                -1 * OLD.qty_konversi ,
                OLD.is_penatajasa ,
                OLD.det ,
                OLD.status_bmhp ,
                OLD.det_konversi ,
                OLD.signa ,
                OLD.additional_data ,
                OLD.created_date ,
                OLD.created_by ,
                OLD.modified_count ,
                OLD.last_modified_date ,
                OLD.last_modified_by ,
                OLD.is_deleted ,
                OLD.is_active ,
                OLD.deleted_date , 
                OLD.deleted_by ,
                'ACCRUAL REVERSAL',
                OLD.no_obatalkespasien
            );
        
    -- INSERT table history int_obatalkespasien_r
    INSERT INTO int_obatalkespasien_r (     
        obatalkespasien_id ,
        sumberdana_id ,
        racikan_id ,
        returresepdetail_id ,
        tipepaket_id ,
        ruangan_id ,
        carabayar_id ,
        pegawai_id ,
        daftartindakan_id ,
        tindakanpelayanan_id ,
        satuankecil_id ,
        shift_id ,
        pendaftaran_id ,
        obatalkes_id ,
        pasien_id ,
        penjamin_id ,
        kelaspelayanan_id ,
        pasienanastesi_id ,
        pasienmasukpenunjang_id ,
        pasienadmisi_id ,
        obatsudahbayar_id ,
        penjualanresep_id ,
        tglpelayanan ,
        r ,
        rke ,
        permintaan_oa ,
        jmlkemasan_oa ,
        kekuatan_oa ,
        satuankekuatan_oa ,
        qty_oa ,
        hargasatuan_oa ,
        signa_oa ,
        harganetto_oa ,
        hargajual_oa , 
        etiket ,
        jmlexposerad ,
        kontrasrad ,
        biayaservice ,
        biayakonseling ,
        jasadokterresep ,
        biayakemasan ,
        biayaadministrasi ,
        tarifcyto ,
        discount , 
        subsidiasuransi ,
        subsidipemerintah ,
        subsidirs ,
        iurbiaya ,
        oa ,
        pembulatan ,
        verifikasitagihan_id ,
        jurnalrekening_id ,
        permohonanoadetail_id ,
        persenppnjual ,
        resepturdetail_id ,
        nilaippnjual ,
        perawat1_id ,
        perawat2_id ,
        instruksitindakanbmhp_id ,
        implementasi_id ,
        is_dilakukan ,
        pemakaianambulan_id ,
        is_jurnal ,
        konfigmargindetail_id ,
        qty_konversi ,
        is_penatajasa ,
        det ,
        status_bmhp ,
        det_konversi ,
        signa ,
        additional_data ,
        created_date ,
        created_by ,
        modified_count ,
        last_modified_date ,
        last_modified_by ,
        is_deleted ,
        is_active ,
        deleted_date , 
        deleted_by ,
        keterangan,
        no_obatalkespasien
    )VALUES(
        NEW.obatalkespasien_id ,
        NEW.sumberdana_id ,
        NEW.racikan_id ,
        NEW.returresepdetail_id ,
        NEW.tipepaket_id ,
        NEW.ruangan_id ,
        NEW.carabayar_id ,
        NEW.pegawai_id ,
        NEW.daftartindakan_id ,
        NEW.tindakanpelayanan_id ,
        NEW.satuankecil_id ,
        NEW.shift_id ,
        NEW.pendaftaran_id ,
        NEW.obatalkes_id ,
        NEW.pasien_id ,
        NEW.penjamin_id ,
        NEW.kelaspelayanan_id ,
        NEW.pasienanastesi_id ,
        NEW.pasienmasukpenunjang_id ,
        NEW.pasienadmisi_id ,
        NEW.obatsudahbayar_id ,
        NEW.penjualanresep_id ,
        NEW.tglpelayanan ,
        NEW.r ,
        NEW.rke ,
        NEW.permintaan_oa ,
        NEW.jmlkemasan_oa ,
        NEW.kekuatan_oa ,
        NEW.satuankekuatan_oa ,
        NEW.qty_oa ,
        NEW.hargasatuan_oa ,
        NEW.signa_oa ,
        NEW.harganetto_oa ,
        NEW.hargajual_oa , 
        NEW.etiket ,
        NEW.jmlexposerad ,
        NEW.kontrasrad ,
        NEW.biayaservice ,
        NEW.biayakonseling ,
        NEW.jasadokterresep ,
        NEW.biayakemasan ,
        NEW.biayaadministrasi ,
        NEW.tarifcyto ,
        NEW.discount , 
        NEW.subsidiasuransi ,
        NEW.subsidipemerintah ,
        NEW.subsidirs ,
        NEW.iurbiaya ,
        NEW.oa ,
        NEW.pembulatan ,
        NEW.verifikasitagihan_id ,
        NEW.jurnalrekening_id ,
        NEW.permohonanoadetail_id ,
        NEW.persenppnjual ,
        NEW.resepturdetail_id ,
        NEW.nilaippnjual ,
        NEW.perawat1_id ,
        NEW.perawat2_id ,
        NEW.instruksitindakanbmhp_id ,
        NEW.implementasi_id ,
        NEW.is_dilakukan ,
        NEW.pemakaianambulan_id ,
        NEW.is_jurnal ,
        NEW.konfigmargindetail_id ,
        NEW.qty_konversi ,
        NEW.is_penatajasa ,
        NEW.det ,
        NEW.status_bmhp ,
        NEW.det_konversi ,
        NEW.signa ,
        NEW.additional_data ,
        NEW.created_date ,
        NEW.created_by ,
        NEW.modified_count ,
        NEW.last_modified_date ,
        NEW.last_modified_by ,
        NEW.is_deleted ,
        NEW.is_active ,
        NEW.deleted_date , 
        NEW.deleted_by ,
        v_keterangan,
        NEW.no_obatalkespasien
    );  
    END IF;

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"int_pendaftaranbmhp_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    
        
BEGIN
        -- INSERT ke table int_pendaftaranbmhp_r
        INSERT INTO int_pendaftaranbmhp_r (     
        pendaftaran_id,
        no_pendaftaran,
        tgl_pendaftaran,
        pasienpulang_id,
        pasienbatalperiksa_id,
        penanggungjawab_id,
        penjamin_id,
        shift_id,
        pasien_id,
        persalinan_id,
        pegawai_id,
        instalasi_id,
        caramasuk_id,
        jeniskasuspenyakit_id,
        pembayaranpelayanan_id,
        kelaspelayanan_id,
        carabayar_id,
        pasienadmisi_id,
        golonganumur_id,
        rujukan_id,
        antrian_id,
        karcis_id,
        ruangan_id,
        no_urutantri,
        transportasi,
        keadaan_masuk,
        status_periksa,
        status_pasien,
        kunjungan,
        alih_status,
        by_phone,
        kunjungan_rumah,
        status_masuk,
        umur,
        tgl_selesaiperiksa,
        keterangan_pendaftaran,
        nopendaftaran_aktif,
        status_konfirmasi,
        tgl_konfirmasi,
        tgl_renkontrol,
        status_farmasi,
        panggil_antrian,
        asuransipasien_id,
        tgl_akandilayani,
        statusdok_rekammedik,
        bpjs_id,
        status_bayar,
        is_aps,
        label_gelang,
        is_karcis,
        tgl_masukperiksa,
        status_verifikasi,
        is_ranap,
        is_skd,
        pendaftaranibu_id,
        is_skl,
        catatan_penatajasa,
        is_stopakomodasi,
        tgl_stopakomodasi,
        additional_data,
        created_date,
        created_by,
        modified_count,
        last_modified_date,
        last_modified_by,
        is_deleted,
        is_active,
        deleted_date,
        deleted_by,
        keterangan
        )VALUES(
        NEW.pendaftaran_id ,
        NEW.no_pendaftaran ,
        NEW.tgl_pendaftaran ,
        NEW.pasienpulang_id ,
        NEW.pasienbatalperiksa_id ,
        NEW.penanggungjawab_id ,
        NEW.penjamin_id ,
        NEW.shift_id ,
        NEW.pasien_id ,
        NEW.persalinan_id ,
        NEW.pegawai_id ,
        NEW.instalasi_id ,
        NEW.caramasuk_id ,
        NEW.jeniskasuspenyakit_id ,
        NEW.pembayaranpelayanan_id ,
        NEW.kelaspelayanan_id ,
        NEW.carabayar_id ,
        NEW.pasienadmisi_id ,
        NEW.golonganumur_id ,
        NEW.rujukan_id ,
        NEW.antrian_id ,
        NEW.karcis_id ,
        NEW.ruangan_id ,
        NEW.no_urutantri ,
        NEW.transportasi ,
        NEW.keadaan_masuk ,
        NEW.status_periksa ,
        NEW.status_pasien ,
        NEW.kunjungan ,
        NEW.alih_status ,
        NEW.by_phone ,
        NEW.kunjungan_rumah ,
        NEW.status_masuk ,
        NEW.umur ,
        NEW.tgl_selesaiperiksa ,
        NEW.keterangan_pendaftaran ,
        NEW.nopendaftaran_aktif ,
        NEW.status_konfirmasi ,
        NEW.tgl_konfirmasi ,
        NEW.tgl_renkontrol ,
        NEW.status_farmasi ,
        NEW.panggil_antrian ,
        NEW.asuransipasien_id ,
        NEW.tgl_akandilayani ,
        NEW.statusdok_rekammedik ,
        NEW.bpjs_id ,
        NEW.status_bayar ,
        NEW.is_aps ,
        NEW.label_gelang ,
        NEW.is_karcis ,
        NEW.tgl_masukperiksa ,
        NEW.status_verifikasi ,
        NEW.is_ranap ,
        NEW.is_skd ,
        NEW.pendaftaranibu_id ,
        NEW.is_skl ,
        NEW.catatan_penatajasa ,
        NEW.is_stopakomodasi ,
        NEW.tgl_stopakomodasi ,
        NEW.additional_data ,
        NEW.created_date ,
        NEW.created_by ,
        NEW.modified_count ,
        NEW.last_modified_date ,
        NEW.last_modified_by ,
        NEW.is_deleted ,
        NEW.is_active ,
        NEW.deleted_date ,
        NEW.deleted_by ,
            'ACCRUAL'
        );

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"int_pendaftaranbmhp_r_update\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$ 
    
    DECLARE
        v_keterangan VARCHAR;
    BEGIN
        IF(NEW.is_deleted IS TRUE)
        THEN
            v_keterangan := 'DELETE';
        ELSE
            v_keterangan := 'UPDATE';
  END IF;
        
    -- INSERT table history int_pendaftaranbmhp_r
    INSERT INTO int_pendaftaranbmhp_r (     
        pendaftaran_id,
        no_pendaftaran,
        tgl_pendaftaran,
        pasienpulang_id,
        pasienbatalperiksa_id,
        penanggungjawab_id,
        penjamin_id,
        shift_id,
        pasien_id,
        persalinan_id,
        pegawai_id,
        instalasi_id,
        caramasuk_id,
        jeniskasuspenyakit_id,
        pembayaranpelayanan_id,
        kelaspelayanan_id,
        carabayar_id,
        pasienadmisi_id,
        golonganumur_id,
        rujukan_id,
        antrian_id,
        karcis_id,
        ruangan_id,
        no_urutantri,
        transportasi,
        keadaan_masuk,
        status_periksa,
        status_pasien,
        kunjungan,
        alih_status,
        by_phone,
        kunjungan_rumah,
        status_masuk,
        umur,
        tgl_selesaiperiksa,
        keterangan_pendaftaran,
        nopendaftaran_aktif,
        status_konfirmasi,
        tgl_konfirmasi,
        tgl_renkontrol,
        status_farmasi,
        panggil_antrian,
        asuransipasien_id,
        tgl_akandilayani,
        statusdok_rekammedik,
        bpjs_id,
        status_bayar,
        is_aps,
        label_gelang,
        is_karcis,
        tgl_masukperiksa,
        status_verifikasi,
        is_ranap,
        is_skd,
        pendaftaranibu_id,
        is_skl,
        catatan_penatajasa,
        is_stopakomodasi,
        tgl_stopakomodasi,
        additional_data,
        created_date,
        created_by,
        modified_count,
        last_modified_date,
        last_modified_by,
        is_deleted,
        is_active,
        deleted_date,
        deleted_by,
        keterangan
    )VALUES(
        NEW.pendaftaran_id ,
        NEW.no_pendaftaran ,
        NEW.tgl_pendaftaran ,
        NEW.pasienpulang_id ,
        NEW.pasienbatalperiksa_id ,
        NEW.penanggungjawab_id ,
        NEW.penjamin_id ,
        NEW.shift_id ,
        NEW.pasien_id ,
        NEW.persalinan_id ,
        NEW.pegawai_id ,
        NEW.instalasi_id ,
        NEW.caramasuk_id ,
        NEW.jeniskasuspenyakit_id ,
        NEW.pembayaranpelayanan_id ,
        NEW.kelaspelayanan_id ,
        NEW.carabayar_id ,
        NEW.pasienadmisi_id ,
        NEW.golonganumur_id ,
        NEW.rujukan_id ,
        NEW.antrian_id ,
        NEW.karcis_id ,
        NEW.ruangan_id ,
        NEW.no_urutantri ,
        NEW.transportasi ,
        NEW.keadaan_masuk ,
        NEW.status_periksa ,
        NEW.status_pasien ,
        NEW.kunjungan ,
        NEW.alih_status ,
        NEW.by_phone ,
        NEW.kunjungan_rumah ,
        NEW.status_masuk ,
        NEW.umur ,
        NEW.tgl_selesaiperiksa ,
        NEW.keterangan_pendaftaran ,
        NEW.nopendaftaran_aktif ,
        NEW.status_konfirmasi ,
        NEW.tgl_konfirmasi ,
        NEW.tgl_renkontrol ,
        NEW.status_farmasi ,
        NEW.panggil_antrian ,
        NEW.asuransipasien_id ,
        NEW.tgl_akandilayani ,
        NEW.statusdok_rekammedik ,
        NEW.bpjs_id ,
        NEW.status_bayar ,
        NEW.is_aps ,
        NEW.label_gelang ,
        NEW.is_karcis ,
        NEW.tgl_masukperiksa ,
        NEW.status_verifikasi ,
        NEW.is_ranap ,
        NEW.is_skd ,
        NEW.pendaftaranibu_id ,
        NEW.is_skl ,
        NEW.catatan_penatajasa ,
        NEW.is_stopakomodasi ,
        NEW.tgl_stopakomodasi ,
        NEW.additional_data ,
        NEW.created_date ,
        NEW.created_by ,
        NEW.modified_count ,
        NEW.last_modified_date ,
        NEW.last_modified_by ,
        NEW.is_deleted ,
        NEW.is_active ,
        NEW.deleted_date ,
        NEW.deleted_by ,
        v_keterangan
    );

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"int_penjualanresep_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN

------------------------------->INSERT table rekap int_penjualanresep_r<----------------------------------
        INSERT INTO int_penjualanresep_r (      
            penjualanresep_id,
            pasienadmisi_id,
            pegawai_id,
            pendaftaran_id,
            returresep_id,
            kelaspelayanan_id,
            penjamin_id,
            pasien_id,
            carabayar_id,
            ruangan_id,
            reseptur_id,
            shift_id,
            tglpenjualan,
            jenispenjualan,
            tglresep,
            noresep,
            totharganetto,
            totalhargajual,
            totaltarifservice,
            biayaadministrasi,
            biayakonseling,
            pembulatanharga,
            jasadokterresep,
            discount,
            subsidiasuransi,
            subsidipemerintah,
            subsidirs,
            iurbiaya,
            lamapelayanan,
            penjpasienpegawai_id,
            penjpasienruangan_id,
            antrianfarmasi_id,
            permohonanoa_id,
            takaranresep,
            isresepperawatan,
            additional_data,
            created_date,
            created_by,
            modified_count,
            last_modified_date,
            last_modified_by,
            is_deleted,
            is_active,
            deleted_date,
            deleted_by,
            iter,
            nama_pembeli,
            pegawairesep_id,
            karyawan_id,
            catatan,
            status_bayar,
            status_reseptur,
            antrian_id,
            pembatalanresep_id,
            status_worklist,
            tgl_lahir,
            log_user,
            keterangan
        )VALUES(
            NEW.penjualanresep_id,
            NEW.pasienadmisi_id,
            NEW.pegawai_id,
            NEW.pendaftaran_id,
            NEW.returresep_id,
            NEW.kelaspelayanan_id,
            NEW.penjamin_id,
            NEW.pasien_id,
            NEW.carabayar_id,
            NEW.ruangan_id,
            NEW.reseptur_id,
            NEW.shift_id,
            NEW.tglpenjualan,
            NEW.jenispenjualan,
            NEW.tglresep,
            NEW.noresep,
            NEW.totharganetto,
            NEW.totalhargajual,
            NEW.totaltarifservice,
            NEW.biayaadministrasi,
            NEW.biayakonseling,
            NEW.pembulatanharga,
            NEW.jasadokterresep,
            NEW.discount,
            NEW.subsidiasuransi,
            NEW.subsidipemerintah,
            NEW.subsidirs,
            NEW.iurbiaya,
            NEW.lamapelayanan,
            NEW.penjpasienpegawai_id,
            NEW.penjpasienruangan_id,
            NEW.antrianfarmasi_id,
            NEW.permohonanoa_id,
            NEW.takaranresep,
            NEW.isresepperawatan,
            NEW.additional_data,
            NEW.created_date,
            NEW.created_by,
            NEW.modified_count,
            NEW.last_modified_date,
            NEW.last_modified_by,
            NEW.is_deleted,
            NEW.is_active,
            NEW.deleted_date,
            NEW.deleted_by,
            NEW.iter,
            NEW.nama_pembeli,
            NEW.pegawairesep_id,
            NEW.karyawan_id,
            NEW.catatan,
            NEW.status_bayar,
            NEW.status_reseptur,
            NEW.antrian_id,
            NEW.pembatalanresep_id,
            NEW.status_worklist,
            NEW.tgl_lahir,
            NEW.log_user,
            'ACCRUAL'
        );

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"obatalkes_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN
------------------------------->INSERT table history obatalkes_r<----------------------------------
        INSERT INTO obatalkes_r (       
            obatalkes_id,
            jenisobatalkes_id,
            sumberdana_id,
            lokasigudang_id,
            satuankecil_id,
            satuanbesar_id,
            ven,
            obatalkes_barcode,
            obatalkes_kode,
            obatalkes_namalain,
            obatalkes_nobatch,
            obatalkes_kategori,
            obatalkes_kadarobat,
            kemasan_besar,
            kekuatan_obat,
            satuankekuatan,
            ppn_persen,
            harganetto,
            hargajual,
            hargamaksimum,
            hargaminimum,
            hargaratarata,
            discount,
            tglkadaluarsa,
            minimalstok,
            is_deleted,
            is_active,
            is_generik,
            satuansedang_id,
            obatalkes_nama,
            is_formularium,
            kemasan_sedang,
            supplier_id,
            indikasi,
            kontradiksi,
            interaksi,
            efek_samping,
            maksimalstok,
            harga_beli,
            lead_time,
            avg_usage,
            min_order,
            max_order,
            nilai_ro,
            groupinacbg_id,
            on_ro,
            on_po,
            hargaterakhir,
            additional_data,
            created_date,
            created_by,
            modified_count,
            last_modified_date,
            last_modified_by,
            deleted_date,
            deleted_by,
            ket_ubah_harga,
            kode_ecatalog,
            is_oral,
            is_antibiotic,
            is_psycothropica,
            is_prescibe_item,
            is_allow_franction,
            is_lasa,
            is_expiry,
            is_highalert,
            is_consigment,
            is_embalase,
            strength,
            strength_uom,
            catatan,
            keterangan_rekap
        )VALUES(
            NEW.obatalkes_id,
            NEW.jenisobatalkes_id,
            NEW.sumberdana_id,
            NEW.lokasigudang_id,
            NEW.satuankecil_id,
            NEW.satuanbesar_id,
            NEW.ven,
            NEW.obatalkes_barcode,
            NEW.obatalkes_kode,
            NEW.obatalkes_namalain,
            NEW.obatalkes_nobatch,
            NEW.obatalkes_kategori,
            NEW.obatalkes_kadarobat,
            NEW.kemasan_besar,
            NEW.kekuatan_obat,
            NEW.satuankekuatan,
            NEW.ppn_persen,
            NEW.harganetto,
            NEW.hargajual,
            NEW.hargamaksimum,
            NEW.hargaminimum,
            NEW.hargaratarata,
            NEW.discount,
            NEW.tglkadaluarsa,
            NEW.minimalstok,
            NEW.is_deleted,
            NEW.is_active,
            NEW.is_generik,
            NEW.satuansedang_id,
            NEW.obatalkes_nama,
            NEW.is_formularium,
            NEW.kemasan_sedang,
            NEW.supplier_id,
            NEW.indikasi,
            NEW.kontradiksi,
            NEW.interaksi,
            NEW.efek_samping,
            NEW.maksimalstok,
            NEW.harga_beli,
            NEW.lead_time,
            NEW.avg_usage,
            NEW.min_order,
            NEW.max_order,
            NEW.nilai_ro,
            NEW.groupinacbg_id,
            NEW.on_ro,
            NEW.on_po,
            NEW.hargaterakhir,
            NEW.additional_data,
            NEW.created_date,
            NEW.created_by,
            NEW.modified_count,
            NEW.last_modified_date,
            NEW.last_modified_by,
            NEW.deleted_date,
            NEW.deleted_by,
            NEW.ket_ubah_harga,
            NEW.kode_ecatalog,
            NEW.is_oral,
            NEW.is_antibiotic,
            NEW.is_psycothropica,
            NEW.is_prescibe_item,
            NEW.is_allow_franction,
            NEW.is_lasa,
            NEW.is_expiry,
            NEW.is_highalert,
            NEW.is_consigment,
            NEW.is_embalase,
            NEW.strength,
            NEW.strength_uom,
            NEW.catatan,
            'INSERT'
            );

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"obatalkes_r_update\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN
------------------------------->INSERT table history obatalkes_r<----------------------------------
        INSERT INTO obatalkes_r (       
            obatalkes_id,
            jenisobatalkes_id,
            sumberdana_id,
            lokasigudang_id,
            satuankecil_id,
            satuanbesar_id,
            ven,
            obatalkes_barcode,
            obatalkes_kode,
            obatalkes_namalain,
            obatalkes_nobatch,
            obatalkes_kategori,
            obatalkes_kadarobat,
            kemasan_besar,
            kekuatan_obat,
            satuankekuatan,
            ppn_persen,
            harganetto,
            hargajual,
            hargamaksimum,
            hargaminimum,
            hargaratarata,
            discount,
            tglkadaluarsa,
            minimalstok,
            is_deleted,
            is_active,
            is_generik,
            satuansedang_id,
            obatalkes_nama,
            is_formularium,
            kemasan_sedang,
            supplier_id,
            indikasi,
            kontradiksi,
            interaksi,
            efek_samping,
            maksimalstok,
            harga_beli,
            lead_time,
            avg_usage,
            min_order,
            max_order,
            nilai_ro,
            groupinacbg_id,
            on_ro,
            on_po,
            hargaterakhir,
            additional_data,
            created_date,
            created_by,
            modified_count,
            last_modified_date,
            last_modified_by,
            deleted_date,
            deleted_by,
            ket_ubah_harga,
            kode_ecatalog,
            is_oral,
            is_antibiotic,
            is_psycothropica,
            is_prescibe_item,
            is_allow_franction,
            is_lasa,
            is_expiry,
            is_highalert,
            is_consigment,
            is_embalase,
            strength,
            strength_uom,
            catatan,
            keterangan_rekap
        )VALUES(
            NEW.obatalkes_id,
            NEW.jenisobatalkes_id,
            NEW.sumberdana_id,
            NEW.lokasigudang_id,
            NEW.satuankecil_id,
            NEW.satuanbesar_id,
            NEW.ven,
            NEW.obatalkes_barcode,
            NEW.obatalkes_kode,
            NEW.obatalkes_namalain,
            NEW.obatalkes_nobatch,
            NEW.obatalkes_kategori,
            NEW.obatalkes_kadarobat,
            NEW.kemasan_besar,
            NEW.kekuatan_obat,
            NEW.satuankekuatan,
            NEW.ppn_persen,
            NEW.harganetto,
            NEW.hargajual,
            NEW.hargamaksimum,
            NEW.hargaminimum,
            NEW.hargaratarata,
            NEW.discount,
            NEW.tglkadaluarsa,
            NEW.minimalstok,
            NEW.is_deleted,
            NEW.is_active,
            NEW.is_generik,
            NEW.satuansedang_id,
            NEW.obatalkes_nama,
            NEW.is_formularium,
            NEW.kemasan_sedang,
            NEW.supplier_id,
            NEW.indikasi,
            NEW.kontradiksi,
            NEW.interaksi,
            NEW.efek_samping,
            NEW.maksimalstok,
            NEW.harga_beli,
            NEW.lead_time,
            NEW.avg_usage,
            NEW.min_order,
            NEW.max_order,
            NEW.nilai_ro,
            NEW.groupinacbg_id,
            NEW.on_ro,
            NEW.on_po,
            NEW.hargaterakhir,
            NEW.additional_data,
            NEW.created_date,
            NEW.created_by,
            NEW.modified_count,
            NEW.last_modified_date,
            NEW.last_modified_by,
            NEW.deleted_date,
            NEW.deleted_by,
            NEW.ket_ubah_harga,
            NEW.kode_ecatalog,
            NEW.is_oral,
            NEW.is_antibiotic,
            NEW.is_psycothropica,
            NEW.is_prescibe_item,
            NEW.is_allow_franction,
            NEW.is_lasa,
            NEW.is_expiry,
            NEW.is_highalert,
            NEW.is_consigment,
            NEW.is_embalase,
            NEW.strength,
            NEW.strength_uom,
            NEW.catatan,
            'UPDATE'
            );

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"obatalkespasien_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN
    NEW.hargajual_oa = NEW.hargasatuan_oa * NEW.qty_oa;
        
        -- INSERT table history obatalkespasien_r
        INSERT INTO obatalkespasien_r (     
        obatalkespasien_id ,
        sumberdana_id ,
        racikan_id ,
        returresepdetail_id ,
        tipepaket_id ,
        ruangan_id ,
        carabayar_id ,
        pegawai_id ,
        daftartindakan_id ,
        tindakanpelayanan_id ,
        satuankecil_id ,
        shift_id ,
        pendaftaran_id ,
        obatalkes_id ,
        pasien_id ,
        penjamin_id ,
        kelaspelayanan_id ,
        pasienanastesi_id ,
        pasienmasukpenunjang_id ,
        pasienadmisi_id ,
        obatsudahbayar_id ,
        penjualanresep_id ,
        tglpelayanan ,
        r ,
        rke ,
        permintaan_oa ,
        jmlkemasan_oa ,
        kekuatan_oa ,
        satuankekuatan_oa ,
        qty_oa ,
        hargasatuan_oa ,
        signa_oa ,
        harganetto_oa ,
        hargajual_oa , 
        etiket ,
        jmlexposerad ,
        kontrasrad ,
        biayaservice ,
        biayakonseling ,
        jasadokterresep ,
        biayakemasan ,
        biayaadministrasi ,
        tarifcyto ,
        discount , 
        subsidiasuransi ,
        subsidipemerintah ,
        subsidirs ,
        iurbiaya ,
        oa ,
        pembulatan ,
        verifikasitagihan_id ,
        jurnalrekening_id ,
        permohonanoadetail_id ,
        persenppnjual ,
        resepturdetail_id ,
        nilaippnjual ,
        perawat1_id ,
        perawat2_id ,
        instruksitindakanbmhp_id ,
        implementasi_id ,
        is_dilakukan ,
        pemakaianambulan_id ,
        is_jurnal ,
        konfigmargindetail_id ,
        qty_konversi ,
        is_penatajasa ,
        det ,
        status_bmhp ,
        det_konversi ,
        signa ,
        additional_data ,
        created_date ,
        created_by ,
        modified_count ,
        last_modified_date ,
        last_modified_by ,
        is_deleted ,
        is_active ,
        deleted_date , 
        deleted_by ,
        keterangan,
        no_obatalkespasien
        )VALUES(
        NEW.obatalkespasien_id ,
        NEW.sumberdana_id ,
        NEW.racikan_id ,
        NEW.returresepdetail_id ,
        NEW.tipepaket_id ,
        NEW.ruangan_id ,
        NEW.carabayar_id ,
        NEW.pegawai_id ,
        NEW.daftartindakan_id ,
        NEW.tindakanpelayanan_id ,
        NEW.satuankecil_id ,
        NEW.shift_id ,
        NEW.pendaftaran_id ,
        NEW.obatalkes_id ,
        NEW.pasien_id ,
        NEW.penjamin_id ,
        NEW.kelaspelayanan_id ,
        NEW.pasienanastesi_id ,
        NEW.pasienmasukpenunjang_id ,
        NEW.pasienadmisi_id ,
        NEW.obatsudahbayar_id ,
        NEW.penjualanresep_id ,
        NEW.tglpelayanan ,
        NEW.r ,
        NEW.rke ,
        NEW.permintaan_oa ,
        NEW.jmlkemasan_oa ,
        NEW.kekuatan_oa ,
        NEW.satuankekuatan_oa ,
        NEW.qty_oa ,
        NEW.hargasatuan_oa ,
        NEW.signa_oa ,
        NEW.harganetto_oa ,
        NEW.hargajual_oa , 
        NEW.etiket ,
        NEW.jmlexposerad ,
        NEW.kontrasrad ,
        NEW.biayaservice ,
        NEW.biayakonseling ,
        NEW.jasadokterresep ,
        NEW.biayakemasan ,
        NEW.biayaadministrasi ,
        NEW.tarifcyto ,
        NEW.discount , 
        NEW.subsidiasuransi ,
        NEW.subsidipemerintah ,
        NEW.subsidirs ,
        NEW.iurbiaya ,
        NEW.oa ,
        NEW.pembulatan ,
        NEW.verifikasitagihan_id ,
        NEW.jurnalrekening_id ,
        NEW.permohonanoadetail_id ,
        NEW.persenppnjual ,
        NEW.resepturdetail_id ,
        NEW.nilaippnjual ,
        NEW.perawat1_id ,
        NEW.perawat2_id ,
        NEW.instruksitindakanbmhp_id ,
        NEW.implementasi_id ,
        NEW.is_dilakukan ,
        NEW.pemakaianambulan_id ,
        NEW.is_jurnal ,
        NEW.konfigmargindetail_id ,
        NEW.qty_konversi ,
        NEW.is_penatajasa ,
        NEW.det ,
        NEW.status_bmhp ,
        NEW.det_konversi ,
        NEW.signa ,
        NEW.additional_data ,
        NEW.created_date ,
        NEW.created_by ,
        NEW.modified_count ,
        NEW.last_modified_date ,
        NEW.last_modified_by ,
        NEW.is_deleted ,
        NEW.is_active ,
        NEW.deleted_date , 
        NEW.deleted_by ,
        'ACCRUAL',
        NEW.no_obatalkespasien
        );

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"obatalkespasien_r_update\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$ 
    
DECLARE
        v_keterangan VARCHAR;
BEGIN
        IF((NEW.is_deleted IS TRUE OR NEW.obatsudahbayar_id IS NOT NULL) AND OLD.is_deleted IS FALSE)
        THEN
                v_keterangan := 'ACCRUAL REVERSAL';
                --> INSERT table history obatalkespasien_r menjadi ACCRUAL REVERSAL(-)
                INSERT INTO obatalkespasien_r (     
                obatalkespasien_id ,
                sumberdana_id ,
                racikan_id ,
                returresepdetail_id ,
                tipepaket_id ,
                ruangan_id ,
                carabayar_id ,
                pegawai_id ,
                daftartindakan_id ,
                tindakanpelayanan_id ,
                satuankecil_id ,
                shift_id ,
                pendaftaran_id ,
                obatalkes_id ,
                pasien_id ,
                penjamin_id ,
                kelaspelayanan_id ,
                pasienanastesi_id ,
                pasienmasukpenunjang_id ,
                pasienadmisi_id ,
                obatsudahbayar_id ,
                penjualanresep_id ,
                tglpelayanan ,
                r ,
                rke ,
                permintaan_oa ,
                jmlkemasan_oa ,
                kekuatan_oa ,
                satuankekuatan_oa ,
                qty_oa ,
                hargasatuan_oa ,
                signa_oa ,
                harganetto_oa ,
                hargajual_oa , 
                etiket ,
                jmlexposerad ,
                kontrasrad ,
                biayaservice ,
                biayakonseling ,
                jasadokterresep ,
                biayakemasan ,
                biayaadministrasi ,
                tarifcyto ,
                discount , 
                subsidiasuransi ,
                subsidipemerintah ,
                subsidirs ,
                iurbiaya ,
                oa ,
                pembulatan ,
                verifikasitagihan_id ,
                jurnalrekening_id ,
                permohonanoadetail_id ,
                persenppnjual ,
                resepturdetail_id ,
                nilaippnjual ,
                perawat1_id ,
                perawat2_id ,
                instruksitindakanbmhp_id ,
                implementasi_id ,
                is_dilakukan ,
                pemakaianambulan_id ,
                is_jurnal ,
                konfigmargindetail_id ,
                qty_konversi ,
                is_penatajasa ,
                det ,
                status_bmhp ,
                det_konversi ,
                signa ,
                additional_data ,
                created_date ,
                created_by ,
                modified_count ,
                last_modified_date ,
                last_modified_by ,
                is_deleted ,
                is_active ,
                deleted_date , 
                deleted_by ,
                keterangan,
                no_obatalkespasien
            )VALUES(
                OLD.obatalkespasien_id ,
                OLD.sumberdana_id ,
                OLD.racikan_id ,
                OLD.returresepdetail_id ,
                OLD.tipepaket_id ,
                OLD.ruangan_id ,
                OLD.carabayar_id ,
                OLD.pegawai_id ,
                OLD.daftartindakan_id ,
                OLD.tindakanpelayanan_id ,
                OLD.satuankecil_id ,
                OLD.shift_id ,
                OLD.pendaftaran_id ,
                OLD.obatalkes_id ,
                OLD.pasien_id ,
                OLD.penjamin_id ,
                OLD.kelaspelayanan_id ,
                OLD.pasienanastesi_id ,
                OLD.pasienmasukpenunjang_id ,
                OLD.pasienadmisi_id ,
                OLD.obatsudahbayar_id ,
                OLD.penjualanresep_id ,
                OLD.tglpelayanan ,
                OLD.r ,
                OLD.rke ,
                OLD.permintaan_oa ,
                OLD.jmlkemasan_oa ,
                OLD.kekuatan_oa ,
                OLD.satuankekuatan_oa ,
                OLD.qty_oa ,
                -1 * OLD.hargasatuan_oa ,
                OLD.signa_oa ,
                -1 * OLD.harganetto_oa ,
                -1 * OLD.hargajual_oa , 
                OLD.etiket ,
                -1 * OLD.jmlexposerad ,
                OLD.kontrasrad ,
                -1 * OLD.biayaservice ,
                -1 * OLD.biayakonseling ,
                -1 * OLD.jasadokterresep ,
                -1 * OLD.biayakemasan ,
                -1 * OLD.biayaadministrasi ,
                -1 * OLD.tarifcyto ,
                -1 * OLD.discount , 
                -1 * OLD.subsidiasuransi ,
                -1 * OLD.subsidipemerintah ,
                -1 * OLD.subsidirs ,
                -1 * OLD.iurbiaya ,
                OLD.oa ,
                -1 * OLD.pembulatan ,
                OLD.verifikasitagihan_id ,
                OLD.jurnalrekening_id ,
                OLD.permohonanoadetail_id ,
                OLD.persenppnjual ,
                OLD.resepturdetail_id ,
                -1 * OLD.nilaippnjual ,
                OLD.perawat1_id ,
                OLD.perawat2_id ,
                OLD.instruksitindakanbmhp_id ,
                OLD.implementasi_id ,
                OLD.is_dilakukan ,
                OLD.pemakaianambulan_id ,
                OLD.is_jurnal ,
                OLD.konfigmargindetail_id ,
                -1 * OLD.qty_konversi ,
                OLD.is_penatajasa ,
                OLD.det ,
                OLD.status_bmhp ,
                OLD.det_konversi ,
                OLD.signa ,
                OLD.additional_data ,
                OLD.created_date ,
                OLD.created_by ,
                OLD.modified_count ,
                OLD.last_modified_date ,
                OLD.last_modified_by ,
                OLD.is_deleted ,
                OLD.is_active ,
                OLD.deleted_date , 
                OLD.deleted_by ,
                v_keterangan,
                OLD.no_obatalkespasien
            );
        RETURN NEW;
        END IF;
            
            IF (OLD.obatsudahbayar_id is null and OLD.is_deleted is FALSE)
                THEN    
                -- INSERT table history obatalkespasien_r menjadi ACCRUAL REVERSAL(-) setelah BILLING CANCEL
                INSERT INTO obatalkespasien_r (     
                obatalkespasien_id ,
                sumberdana_id ,
                racikan_id ,
                returresepdetail_id ,
                tipepaket_id ,
                ruangan_id ,
                carabayar_id ,
                pegawai_id ,
                daftartindakan_id ,
                tindakanpelayanan_id ,
                satuankecil_id ,
                shift_id ,
                pendaftaran_id ,
                obatalkes_id ,
                pasien_id ,
                penjamin_id ,
                kelaspelayanan_id ,
                pasienanastesi_id ,
                pasienmasukpenunjang_id ,
                pasienadmisi_id ,
                obatsudahbayar_id ,
                penjualanresep_id ,
                tglpelayanan ,
                r ,
                rke ,
                permintaan_oa ,
                jmlkemasan_oa ,
                kekuatan_oa ,
                satuankekuatan_oa ,
                qty_oa ,
                hargasatuan_oa ,
                signa_oa ,
                harganetto_oa ,
                hargajual_oa , 
                etiket ,
                jmlexposerad ,
                kontrasrad ,
                biayaservice ,
                biayakonseling ,
                jasadokterresep ,
                biayakemasan ,
                biayaadministrasi ,
                tarifcyto ,
                discount , 
                subsidiasuransi ,
                subsidipemerintah ,
                subsidirs ,
                iurbiaya ,
                oa ,
                pembulatan ,
                verifikasitagihan_id ,
                jurnalrekening_id ,
                permohonanoadetail_id ,
                persenppnjual ,
                resepturdetail_id ,
                nilaippnjual ,
                perawat1_id ,
                perawat2_id ,
                instruksitindakanbmhp_id ,
                implementasi_id ,
                is_dilakukan ,
                pemakaianambulan_id ,
                is_jurnal ,
                konfigmargindetail_id ,
                qty_konversi ,
                is_penatajasa ,
                det ,
                status_bmhp ,
                det_konversi ,
                signa ,
                additional_data ,
                created_date ,
                created_by ,
                modified_count ,
                last_modified_date ,
                last_modified_by ,
                is_deleted ,
                is_active ,
                deleted_date , 
                deleted_by ,
                keterangan,
                no_obatalkespasien
            )VALUES(
                OLD.obatalkespasien_id ,
                OLD.sumberdana_id ,
                OLD.racikan_id ,
                OLD.returresepdetail_id ,
                OLD.tipepaket_id ,
                OLD.ruangan_id ,
                OLD.carabayar_id ,
                OLD.pegawai_id ,
                OLD.daftartindakan_id ,
                OLD.tindakanpelayanan_id ,
                OLD.satuankecil_id ,
                OLD.shift_id ,
                OLD.pendaftaran_id ,
                OLD.obatalkes_id ,
                OLD.pasien_id ,
                OLD.penjamin_id ,
                OLD.kelaspelayanan_id ,
                OLD.pasienanastesi_id ,
                OLD.pasienmasukpenunjang_id ,
                OLD.pasienadmisi_id ,
                OLD.obatsudahbayar_id ,
                OLD.penjualanresep_id ,
                OLD.tglpelayanan ,
                OLD.r ,
                OLD.rke ,
                OLD.permintaan_oa ,
                OLD.jmlkemasan_oa ,
                OLD.kekuatan_oa ,
                OLD.satuankekuatan_oa ,
                OLD.qty_oa ,
                -1 * OLD.hargasatuan_oa ,
                OLD.signa_oa ,
                -1 * OLD.harganetto_oa ,
                -1 * OLD.hargajual_oa , 
                OLD.etiket ,
                -1 * OLD.jmlexposerad ,
                OLD.kontrasrad ,
                -1 * OLD.biayaservice ,
                -1 * OLD.biayakonseling ,
                -1 * OLD.jasadokterresep ,
                -1 * OLD.biayakemasan ,
                -1 * OLD.biayaadministrasi ,
                -1 * OLD.tarifcyto ,
                -1 * OLD.discount , 
                -1 * OLD.subsidiasuransi ,
                -1 * OLD.subsidipemerintah ,
                -1 * OLD.subsidirs ,
                -1 * OLD.iurbiaya ,
                OLD.oa ,
                -1 * OLD.pembulatan ,
                OLD.verifikasitagihan_id ,
                OLD.jurnalrekening_id ,
                OLD.permohonanoadetail_id ,
                OLD.persenppnjual ,
                OLD.resepturdetail_id ,
                -1 * OLD.nilaippnjual ,
                OLD.perawat1_id ,
                OLD.perawat2_id ,
                OLD.instruksitindakanbmhp_id ,
                OLD.implementasi_id ,
                OLD.is_dilakukan ,
                OLD.pemakaianambulan_id ,
                OLD.is_jurnal ,
                OLD.konfigmargindetail_id ,
                -1 * OLD.qty_konversi ,
                OLD.is_penatajasa ,
                OLD.det ,
                OLD.status_bmhp ,
                OLD.det_konversi ,
                OLD.signa ,
                OLD.additional_data ,
                OLD.created_date ,
                OLD.created_by ,
                OLD.modified_count ,
                OLD.last_modified_date ,
                OLD.last_modified_by ,
                OLD.is_deleted ,
                OLD.is_active ,
                OLD.deleted_date , 
                OLD.deleted_by ,
                'ACCRUAL REVERSAL',
                OLD.no_obatalkespasien
            );
        
            INSERT INTO obatalkespasien_r (     
                obatalkespasien_id ,
                sumberdana_id ,
                racikan_id ,
                returresepdetail_id ,
                tipepaket_id ,
                ruangan_id ,
                carabayar_id ,
                pegawai_id ,
                daftartindakan_id ,
                tindakanpelayanan_id ,
                satuankecil_id ,
                shift_id ,
                pendaftaran_id ,
                obatalkes_id ,
                pasien_id ,
                penjamin_id ,
                kelaspelayanan_id ,
                pasienanastesi_id ,
                pasienmasukpenunjang_id ,
                pasienadmisi_id ,
                obatsudahbayar_id ,
                penjualanresep_id ,
                tglpelayanan ,
                r ,
                rke ,
                permintaan_oa ,
                jmlkemasan_oa ,
                kekuatan_oa ,
                satuankekuatan_oa ,
                qty_oa ,
                hargasatuan_oa ,
                signa_oa ,
                harganetto_oa ,
                hargajual_oa , 
                etiket ,
                jmlexposerad ,
                kontrasrad ,
                biayaservice ,
                biayakonseling ,
                jasadokterresep ,
                biayakemasan ,
                biayaadministrasi ,
                tarifcyto ,
                discount , 
                subsidiasuransi ,
                subsidipemerintah ,
                subsidirs ,
                iurbiaya ,
                oa ,
                pembulatan ,
                verifikasitagihan_id ,
                jurnalrekening_id ,
                permohonanoadetail_id ,
                persenppnjual ,
                resepturdetail_id ,
                nilaippnjual ,
                perawat1_id ,
                perawat2_id ,
                instruksitindakanbmhp_id ,
                implementasi_id ,
                is_dilakukan ,
                pemakaianambulan_id ,
                is_jurnal ,
                konfigmargindetail_id ,
                qty_konversi ,
                is_penatajasa ,
                det ,
                status_bmhp ,
                det_konversi ,
                signa ,
                additional_data ,
                created_date ,
                created_by ,
                modified_count ,
                last_modified_date ,
                last_modified_by ,
                is_deleted ,
                is_active ,
                deleted_date , 
                deleted_by ,
                keterangan,
                no_obatalkespasien
            )VALUES(
                OLD.obatalkespasien_id ,
                OLD.sumberdana_id ,
                OLD.racikan_id ,
                OLD.returresepdetail_id ,
                OLD.tipepaket_id ,
                OLD.ruangan_id ,
                OLD.carabayar_id ,
                OLD.pegawai_id ,
                OLD.daftartindakan_id ,
                OLD.tindakanpelayanan_id ,
                OLD.satuankecil_id ,
                OLD.shift_id ,
                OLD.pendaftaran_id ,
                OLD.obatalkes_id ,
                OLD.pasien_id ,
                OLD.penjamin_id ,
                OLD.kelaspelayanan_id ,
                OLD.pasienanastesi_id ,
                OLD.pasienmasukpenunjang_id ,
                OLD.pasienadmisi_id ,
                OLD.obatsudahbayar_id ,
                OLD.penjualanresep_id ,
                OLD.tglpelayanan ,
                OLD.r ,
                OLD.rke ,
                OLD.permintaan_oa ,
                OLD.jmlkemasan_oa ,
                OLD.kekuatan_oa ,
                OLD.satuankekuatan_oa ,
                OLD.qty_oa ,
                OLD.hargasatuan_oa ,
                OLD.signa_oa ,
                OLD.harganetto_oa ,
                OLD.hargajual_oa , 
                OLD.etiket ,
                OLD.jmlexposerad ,
                OLD.kontrasrad ,
                OLD.biayaservice ,
                OLD.biayakonseling ,
                OLD.jasadokterresep ,
                OLD.biayakemasan ,
                OLD.biayaadministrasi ,
                OLD.tarifcyto ,
                OLD.discount , 
                OLD.subsidiasuransi ,
                OLD.subsidipemerintah ,
                OLD.subsidirs ,
                OLD.iurbiaya ,
                OLD.oa ,
                OLD.pembulatan ,
                OLD.verifikasitagihan_id ,
                OLD.jurnalrekening_id ,
                OLD.permohonanoadetail_id ,
                OLD.persenppnjual ,
                OLD.resepturdetail_id ,
                OLD.nilaippnjual ,
                OLD.perawat1_id ,
                OLD.perawat2_id ,
                OLD.instruksitindakanbmhp_id ,
                OLD.implementasi_id ,
                OLD.is_dilakukan ,
                OLD.pemakaianambulan_id ,
                OLD.is_jurnal ,
                OLD.konfigmargindetail_id ,
                OLD.qty_konversi ,
                OLD.is_penatajasa ,
                OLD.det ,
                OLD.status_bmhp ,
                OLD.det_konversi ,
                OLD.signa ,
                OLD.additional_data ,
                OLD.created_date ,
                OLD.created_by ,
                OLD.modified_count ,
                OLD.last_modified_date ,
                OLD.last_modified_by ,
                OLD.is_deleted ,
                OLD.is_active ,
                OLD.deleted_date , 
                OLD.deleted_by ,
                'ACCRUAL',
                OLD.no_obatalkespasien
            );  

END IF;

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;
        ");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"obatsudahbayar_t_cancel\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$ 
    
DECLARE
        v_keterangan VARCHAR;
    BEGIN
        IF(NEW.is_deleted IS TRUE)
        THEN
            -- INSERT table history obatalkespasien_r menjadi ACCRUAL(+), jika is_deleted=TRUE
            INSERT INTO obatalkespasien_r (     
                obatalkespasien_id ,
                sumberdana_id ,
                racikan_id ,
                returresepdetail_id ,
                tipepaket_id ,
                ruangan_id ,
                carabayar_id ,
                pegawai_id ,
                daftartindakan_id ,
                tindakanpelayanan_id ,
                satuankecil_id ,
                shift_id ,
                pendaftaran_id ,
                obatalkes_id ,
                pasien_id ,
                penjamin_id ,
                kelaspelayanan_id ,
                pasienanastesi_id ,
                pasienmasukpenunjang_id ,
                pasienadmisi_id ,
                obatsudahbayar_id ,
                penjualanresep_id ,
                tglpelayanan ,
                r ,
                rke ,
                permintaan_oa ,
                jmlkemasan_oa ,
                kekuatan_oa ,
                satuankekuatan_oa ,
                qty_oa ,
                hargasatuan_oa ,
                signa_oa ,
                harganetto_oa ,
                hargajual_oa , 
                etiket ,
                jmlexposerad ,
                kontrasrad ,
                biayaservice ,
                biayakonseling ,
                jasadokterresep ,
                biayakemasan ,
                biayaadministrasi ,
                tarifcyto ,
                discount , 
                subsidiasuransi ,
                subsidipemerintah ,
                subsidirs ,
                iurbiaya ,
                oa ,
                pembulatan ,
                verifikasitagihan_id ,
                jurnalrekening_id ,
                permohonanoadetail_id ,
                persenppnjual ,
                resepturdetail_id ,
                nilaippnjual ,
                perawat1_id ,
                perawat2_id ,
                instruksitindakanbmhp_id ,
                implementasi_id ,
                is_dilakukan ,
                pemakaianambulan_id ,
                is_jurnal ,
                konfigmargindetail_id ,
                qty_konversi ,
                is_penatajasa ,
                det ,
                status_bmhp ,
                det_konversi ,
                signa ,
                additional_data ,
                created_date ,
                created_by ,
                modified_count ,
                last_modified_date ,
                last_modified_by ,
                is_deleted ,
                is_active ,
                deleted_date , 
                deleted_by ,
                keterangan
                )
                SELECT
                obatalkespasien_id ,
                sumberdana_id ,
                racikan_id ,
                returresepdetail_id ,
                tipepaket_id ,
                ruangan_id ,
                carabayar_id ,
                pegawai_id ,
                daftartindakan_id ,
                tindakanpelayanan_id ,
                satuankecil_id ,
                shift_id ,
                pendaftaran_id ,
                obatalkes_id ,
                pasien_id ,
                penjamin_id ,
                kelaspelayanan_id ,
                pasienanastesi_id ,
                pasienmasukpenunjang_id ,
                pasienadmisi_id ,
                NEW.obatsudahbayar_id ,
                penjualanresep_id ,
                tglpelayanan ,
                r ,
                rke ,
                permintaan_oa ,
                jmlkemasan_oa ,
                kekuatan_oa ,
                satuankekuatan_oa ,
                qty_oa ,
                hargasatuan_oa ,
                signa_oa ,
                harganetto_oa ,
                hargajual_oa , 
                etiket ,
                jmlexposerad ,
                kontrasrad ,
                biayaservice ,
                biayakonseling ,
                jasadokterresep ,
                biayakemasan ,
                biayaadministrasi ,
                tarifcyto ,
                discount , 
                subsidiasuransi ,
                subsidipemerintah ,
                subsidirs ,
                iurbiaya ,
                oa ,
                pembulatan ,
                verifikasitagihan_id ,
                jurnalrekening_id ,
                permohonanoadetail_id ,
                persenppnjual ,
                resepturdetail_id ,
                nilaippnjual ,
                perawat1_id ,
                perawat2_id ,
                instruksitindakanbmhp_id ,
                implementasi_id ,
                is_dilakukan ,
                pemakaianambulan_id ,
                is_jurnal ,
                konfigmargindetail_id ,
                qty_konversi ,
                is_penatajasa ,
                det ,
                status_bmhp ,
                det_konversi ,
                signa ,
                additional_data ,
                created_date ,
                created_by ,
                modified_count ,
                last_modified_date ,
                last_modified_by ,
                is_deleted ,
                is_active ,
                deleted_date , 
                deleted_by ,
                'ACCRUAL'
                FROM obatalkespasien_t
            WHERE obatalkespasien_id = NEW.obatalkespasien_id;
        
            -- INSERT table history obatalkespasien_r menjadi BILLING CANCEL(-), jika is_deleted=TRUE
                INSERT INTO obatalkespasien_r (     
                    obatalkespasien_id ,
                    sumberdana_id ,
                    racikan_id ,
                    returresepdetail_id ,
                    tipepaket_id ,
                    ruangan_id ,
                    carabayar_id ,
                    pegawai_id ,
                    daftartindakan_id ,
                    tindakanpelayanan_id ,
                    satuankecil_id ,
                    shift_id ,
                    pendaftaran_id ,
                    obatalkes_id ,
                    pasien_id ,
                    penjamin_id ,
                    kelaspelayanan_id ,
                    pasienanastesi_id ,
                    pasienmasukpenunjang_id ,
                    pasienadmisi_id ,
                    obatsudahbayar_id ,
                    penjualanresep_id ,
                    tglpelayanan ,
                    r ,
                    rke ,
                    permintaan_oa ,
                    jmlkemasan_oa ,
                    kekuatan_oa ,
                    satuankekuatan_oa ,
                    qty_oa ,
                    hargasatuan_oa ,
                    signa_oa ,
                    harganetto_oa ,
                    hargajual_oa , 
                    etiket ,
                    jmlexposerad ,
                    kontrasrad ,
                    biayaservice ,
                    biayakonseling ,
                    jasadokterresep ,
                    biayakemasan ,
                    biayaadministrasi ,
                    tarifcyto ,
                    discount , 
                    subsidiasuransi ,
                    subsidipemerintah ,
                    subsidirs ,
                    iurbiaya ,
                    oa ,
                    pembulatan ,
                    verifikasitagihan_id ,
                    jurnalrekening_id ,
                    permohonanoadetail_id ,
                    persenppnjual ,
                    resepturdetail_id ,
                    nilaippnjual ,
                    perawat1_id ,
                    perawat2_id ,
                    instruksitindakanbmhp_id ,
                    implementasi_id ,
                    is_dilakukan ,
                    pemakaianambulan_id ,
                    is_jurnal ,
                    konfigmargindetail_id ,
                    qty_konversi ,
                    is_penatajasa ,
                    det ,
                    status_bmhp ,
                    det_konversi ,
                    signa ,
                    additional_data ,
                    created_date ,
                    created_by ,
                    modified_count ,
                    last_modified_date ,
                    last_modified_by ,
                    is_deleted ,
                    is_active ,
                    deleted_date , 
                    deleted_by ,
                    keterangan
                    )
                    SELECT
                    obatalkespasien_id ,
                    sumberdana_id ,
                    racikan_id ,
                    returresepdetail_id ,
                    tipepaket_id ,
                    ruangan_id ,
                    carabayar_id ,
                    pegawai_id ,
                    daftartindakan_id ,
                    tindakanpelayanan_id ,
                    satuankecil_id ,
                    shift_id ,
                    pendaftaran_id ,
                    obatalkes_id ,
                    pasien_id ,
                    penjamin_id ,
                    kelaspelayanan_id ,
                    pasienanastesi_id ,
                    pasienmasukpenunjang_id ,
                    pasienadmisi_id ,
                    NEW.obatsudahbayar_id ,
                    penjualanresep_id ,
                    tglpelayanan ,
                    r ,
                    rke ,
                    permintaan_oa ,
                    jmlkemasan_oa ,
                    kekuatan_oa ,
                    satuankekuatan_oa ,
                    qty_oa ,
                    -1 * hargasatuan_oa ,
                    signa_oa ,
                    -1 * harganetto_oa ,
                    -1 * hargajual_oa , 
                    etiket ,
                    -1 * jmlexposerad ,
                    kontrasrad ,
                    -1 * biayaservice ,
                    -1 * biayakonseling ,
                    -1 * jasadokterresep ,
                    -1 * biayakemasan ,
                    -1 * biayaadministrasi ,
                    -1 * tarifcyto ,
                    -1 * discount , 
                    -1 * subsidiasuransi ,
                    -1 * subsidipemerintah ,
                    -1 * subsidirs ,
                    -1 * iurbiaya ,
                    oa ,
                    -1 * pembulatan ,
                    verifikasitagihan_id ,
                    jurnalrekening_id ,
                    permohonanoadetail_id ,
                    persenppnjual ,
                    resepturdetail_id ,
                    -1 * nilaippnjual ,
                    perawat1_id ,
                    perawat2_id ,
                    instruksitindakanbmhp_id ,
                    implementasi_id ,
                    is_dilakukan ,
                    pemakaianambulan_id ,
                    is_jurnal ,
                    konfigmargindetail_id ,
                    -1 * qty_konversi ,
                    is_penatajasa ,
                    det ,
                    status_bmhp ,
                    det_konversi ,
                    signa ,
                    additional_data ,
                    created_date ,
                    created_by ,
                    modified_count ,
                    last_modified_date ,
                    last_modified_by ,
                    is_deleted ,
                    is_active ,
                    deleted_date , 
                    deleted_by ,
                    'BILLING CANCEL'
                    FROM obatalkespasien_t
                WHERE obatalkespasien_id = NEW.obatalkespasien_id;
                
    END IF;
    
    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"obatsudahbayar_t_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

BEGIN
        -- INSERT table history obatalkespasien_r BILLING(+)
        INSERT INTO obatalkespasien_r (     
                obatalkespasien_id ,
                sumberdana_id ,
                racikan_id ,
                returresepdetail_id ,
                tipepaket_id ,
                ruangan_id ,
                carabayar_id ,
                pegawai_id ,
                daftartindakan_id ,
                tindakanpelayanan_id ,
                satuankecil_id ,
                shift_id ,
                pendaftaran_id ,
                obatalkes_id ,
                pasien_id ,
                penjamin_id ,
                kelaspelayanan_id ,
                pasienanastesi_id ,
                pasienmasukpenunjang_id ,
                pasienadmisi_id ,
                obatsudahbayar_id ,
                penjualanresep_id ,
                tglpelayanan ,
                r ,
                rke ,
                permintaan_oa ,
                jmlkemasan_oa ,
                kekuatan_oa ,
                satuankekuatan_oa ,
                qty_oa ,
                hargasatuan_oa ,
                signa_oa ,
                harganetto_oa ,
                hargajual_oa , 
                etiket ,
                jmlexposerad ,
                kontrasrad ,
                biayaservice ,
                biayakonseling ,
                jasadokterresep ,
                biayakemasan ,
                biayaadministrasi ,
                tarifcyto ,
                discount , 
                subsidiasuransi ,
                subsidipemerintah ,
                subsidirs ,
                iurbiaya ,
                oa ,
                pembulatan ,
                verifikasitagihan_id ,
                jurnalrekening_id ,
                permohonanoadetail_id ,
                persenppnjual ,
                resepturdetail_id ,
                nilaippnjual ,
                perawat1_id ,
                perawat2_id ,
                instruksitindakanbmhp_id ,
                implementasi_id ,
                is_dilakukan ,
                pemakaianambulan_id ,
                is_jurnal ,
                konfigmargindetail_id ,
                qty_konversi ,
                is_penatajasa ,
                det ,
                status_bmhp ,
                det_konversi ,
                signa ,
                additional_data ,
                created_date ,
                created_by ,
                modified_count ,
                last_modified_date ,
                last_modified_by ,
                is_deleted ,
                is_active ,
                deleted_date , 
                deleted_by ,
                keterangan,
                no_obatalkespasien
        )
        SELECT  
                obatalkespasien_id ,
                sumberdana_id ,
                racikan_id ,
                returresepdetail_id ,
                tipepaket_id ,
                ruangan_id ,
                carabayar_id ,
                pegawai_id ,
                daftartindakan_id ,
                tindakanpelayanan_id ,
                satuankecil_id ,
                shift_id ,
                pendaftaran_id ,
                obatalkes_id ,
                pasien_id ,
                penjamin_id ,
                kelaspelayanan_id ,
                pasienanastesi_id ,
                pasienmasukpenunjang_id ,
                pasienadmisi_id ,
                NEW.obatsudahbayar_id ,
                penjualanresep_id ,
                tglpelayanan ,
                r ,
                rke ,
                permintaan_oa ,
                jmlkemasan_oa ,
                kekuatan_oa ,
                satuankekuatan_oa ,
                qty_oa ,
                hargasatuan_oa ,
                signa_oa ,
                harganetto_oa ,
                hargajual_oa , 
                etiket ,
                jmlexposerad ,
                kontrasrad ,
                biayaservice ,
                biayakonseling ,
                jasadokterresep ,
                biayakemasan ,
                biayaadministrasi ,
                tarifcyto ,
                discount , 
                subsidiasuransi ,
                subsidipemerintah ,
                subsidirs ,
                iurbiaya ,
                oa ,
                pembulatan ,
                verifikasitagihan_id ,
                jurnalrekening_id ,
                permohonanoadetail_id ,
                persenppnjual ,
                resepturdetail_id ,
                nilaippnjual ,
                perawat1_id ,
                perawat2_id ,
                instruksitindakanbmhp_id ,
                implementasi_id ,
                is_dilakukan ,
                pemakaianambulan_id ,
                is_jurnal ,
                konfigmargindetail_id ,
                qty_konversi ,
                is_penatajasa ,
                det ,
                status_bmhp ,
                det_konversi ,
                signa ,
                additional_data ,
                created_date ,
                created_by ,
                modified_count ,
                last_modified_date ,
                last_modified_by ,
                is_deleted ,
                is_active ,
                deleted_date , 
                deleted_by ,
                'BILLING',
                no_obatalkespasien
    FROM obatalkespasien_t
    WHERE obatalkespasien_id = NEW.obatalkespasien_id;
    
    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"pasien_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
-- @created by ridona 31 Agustus 2020
    
        
BEGIN
        -- INSERT table history pasien_r
        INSERT INTO pasien_r (      
        pasien_id,
        no_rekam_medik,
        tgl_rekam_medik,
        jenisidentitas,
        no_identitas_pasien,
        namadepan,
        nama_pasien,
        nama_bin,
        jeniskelamin,
        tempat_lahir,
        tanggal_lahir,
        golonganumur_id,
        alamat_pasien,
        rt,
        rw,
        propinsi_id,
        kabupaten_id,
        kecamatan_id,
        kelurahan_id,
        pendidikan_id,
        pekerjaan_id,
        suku_id,
        statusperkawinan,
        agama,
        golongandarah,
        rhesus,
        anakke,
        jumlah_bersaudara,
        no_telepon_pasien,
        no_mobile_pasien,
        warga_negara,
        photopasien,
        alamatemail,
        nama_ibu,
        nama_ayah,
        dokrekammedis_id,
        tgl_meninggal,
        pegawai_id,
        loginpemakai_id,
        garis_latitude,
        garis_longitude,
        statusrekammedis,
        profilrs_id,
        alamat_sekarang,
        nopeserta_bpjs,
        is_aps,
        additional_data,
        created_date,
        created_by,
        modified_count,
        last_modified_date,
        last_modified_by,
        is_deleted,
        is_active,
        deleted_date,
        deleted_by,
        keterangan
        )VALUES(
        NEW.pasien_id ,
        NEW.no_rekam_medik ,
        NEW.tgl_rekam_medik ,
        NEW.jenisidentitas ,
        NEW.no_identitas_pasien ,
        NEW.namadepan ,
        NEW.nama_pasien ,
        NEW.nama_bin ,
        NEW.jeniskelamin ,
        NEW.tempat_lahir ,
        NEW.tanggal_lahir ,
        NEW.golonganumur_id ,
        NEW.alamat_pasien ,
        NEW.rt ,
        NEW.rw ,
        NEW.propinsi_id ,
        NEW.kabupaten_id ,
        NEW.kecamatan_id ,
        NEW.kelurahan_id ,
        NEW.pendidikan_id ,
        NEW.pekerjaan_id ,
        NEW.suku_id ,
        NEW.statusperkawinan ,
        NEW.agama ,
        NEW.golongandarah ,
        NEW.rhesus ,
        NEW.anakke ,
        NEW.jumlah_bersaudara ,
        NEW.no_telepon_pasien ,
        NEW.no_mobile_pasien ,
        NEW.warga_negara ,
        NEW.photopasien ,
        NEW.alamatemail ,
        NEW.nama_ibu ,
        NEW.nama_ayah ,
        NEW.dokrekammedis_id ,
        NEW.tgl_meninggal ,
        NEW.pegawai_id ,
        NEW.loginpemakai_id ,
        NEW.garis_latitude ,
        NEW.garis_longitude ,
        NEW.statusrekammedis ,
        NEW.profilrs_id ,
        NEW.alamat_sekarang ,
        NEW.nopeserta_bpjs ,
        NEW.is_aps ,
        NEW.additional_data ,
        NEW.created_date ,
        NEW.created_by ,
        NEW.modified_count ,
        NEW.last_modified_date ,
        NEW.last_modified_by ,
        NEW.is_deleted ,
        NEW.is_active ,
        NEW.deleted_date ,
        NEW.deleted_by ,
        'INSERT'
        );

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"pasien_r_update\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$ 
    -- @created by ridona 31 Agustus 2020
    
    DECLARE
        v_keterangan VARCHAR;
    BEGIN
        IF(NEW.is_deleted IS TRUE)
        THEN
            v_keterangan := 'DELETE';
        ELSE
            v_keterangan := 'UPDATE';
  END IF;
        
    -- INSERT table history pasien_r
    INSERT INTO pasien_r (      
        pasien_id,
        no_rekam_medik,
        tgl_rekam_medik,
        jenisidentitas,
        no_identitas_pasien,
        namadepan,
        nama_pasien,
        nama_bin,
        jeniskelamin,
        tempat_lahir,
        tanggal_lahir,
        golonganumur_id,
        alamat_pasien,
        rt,
        rw,
        propinsi_id,
        kabupaten_id,
        kecamatan_id,
        kelurahan_id,
        pendidikan_id,
        pekerjaan_id,
        suku_id,
        statusperkawinan,
        agama,
        golongandarah,
        rhesus,
        anakke,
        jumlah_bersaudara,
        no_telepon_pasien,
        no_mobile_pasien,
        warga_negara,
        photopasien,
        alamatemail,
        nama_ibu,
        nama_ayah,
        dokrekammedis_id,
        tgl_meninggal,
        pegawai_id,
        loginpemakai_id,
        garis_latitude,
        garis_longitude,
        statusrekammedis,
        profilrs_id,
        alamat_sekarang,
        nopeserta_bpjs,
        is_aps,
        additional_data,
        created_date,
        created_by,
        modified_count,
        last_modified_date,
        last_modified_by,
        is_deleted,
        is_active,
        deleted_date,
        deleted_by,
        keterangan
    )VALUES(
        NEW.pasien_id ,
        NEW.no_rekam_medik ,
        NEW.tgl_rekam_medik ,
        NEW.jenisidentitas ,
        NEW.no_identitas_pasien , 
        NEW.namadepan ,
        NEW.nama_pasien ,
        NEW.nama_bin ,
        NEW.jeniskelamin ,
        NEW.tempat_lahir ,
        NEW.tanggal_lahir ,
        NEW.golonganumur_id ,
        NEW.alamat_pasien ,
        NEW.rt ,
        NEW.rw ,
        NEW.propinsi_id ,
        NEW.kabupaten_id ,
        NEW.kecamatan_id ,
        NEW.kelurahan_id ,
        NEW.pendidikan_id ,
        NEW.pekerjaan_id ,
        NEW.suku_id ,
        NEW.statusperkawinan ,
        NEW.agama ,
        NEW.golongandarah ,
        NEW.rhesus ,
        NEW.anakke ,
        NEW.jumlah_bersaudara ,
        NEW.no_telepon_pasien ,
        NEW.no_mobile_pasien ,
        NEW.warga_negara ,
        NEW.photopasien ,
        NEW.alamatemail ,
        NEW.nama_ibu ,
        NEW.nama_ayah ,
        NEW.dokrekammedis_id ,
        NEW.tgl_meninggal ,
        NEW.pegawai_id ,
        NEW.loginpemakai_id ,
        NEW.garis_latitude ,
        NEW.garis_longitude ,
        NEW.statusrekammedis ,
        NEW.profilrs_id ,
        NEW.alamat_sekarang ,
        NEW.nopeserta_bpjs ,
        NEW.is_aps ,
        NEW.additional_data ,
        NEW.created_date ,
        NEW.created_by ,
        NEW.modified_count ,
        NEW.last_modified_date ,
        NEW.last_modified_by ,
        NEW.is_deleted ,
        NEW.is_active ,
        NEW.deleted_date ,
        NEW.deleted_by ,
        v_keterangan
    );

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"pasienadmisi_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
-- @created by ridona 28 Agustus 2020
    
        
BEGIN
        -- INSERT table history pasienadmisi_r
        INSERT INTO pasienadmisi_r (        
        pasienadmisi_id,
        shift_id,
        carabayar_id,
        penjamin_id,
        pasien_id,
        caramasuk_id,
        ruangan_id,
        pasienpulang_id,
        bookingkamar_id,
        pembayaranpelayanan_id,
        pendaftaran_id,
        kamarruangan_id,
        kelaspelayanan_id,
        pegawai_id,
        tgl_admisi,
        tgl_pendaftaran,
        tgl_pulang,
        kunjungan,
        status_keluar,
        rawat_gabung,
        rencana_pulang,
        kamartempattidur_id,
        bpjs_id,
        status_ranap,
        pasienbatalperiksa_id,
        tgl_pindahkamar,
        status_verifikasi,
        is_skd,
        is_pasientitipan,
        is_aps,
        asuransipasien_id,
        kelas_ditagihkan_id,
        kamar_titipan_id,
        tempattidur_titipan_id,
        ruangan_titipan_id,
        is_stoptitipan,
        additional_data,
        created_date,
        created_by,
        modified_count,
        last_modified_date,
        last_modified_by,
        is_deleted,
        is_active,
        deleted_date,
        deleted_by,
        keterangan
        )VALUES(
        NEW.pasienadmisi_id ,
        NEW.shift_id ,
        NEW.carabayar_id ,
        NEW.penjamin_id ,
        NEW.pasien_id ,
        NEW.caramasuk_id , 
        NEW.ruangan_id ,
        NEW.pasienpulang_id ,
        NEW.bookingkamar_id ,
        NEW.pembayaranpelayanan_id ,
        NEW.pendaftaran_id ,
        NEW.kamarruangan_id ,
        NEW.kelaspelayanan_id ,
        NEW.pegawai_id ,
        NEW.tgl_admisi ,
        NEW.tgl_pendaftaran ,
        NEW.tgl_pulang ,
        NEW.kunjungan ,
        NEW.status_keluar ,
        NEW.rawat_gabung ,
        NEW.rencana_pulang ,
        NEW.kamartempattidur_id ,
        NEW.bpjs_id ,
        NEW.status_ranap ,
        NEW.pasienbatalperiksa_id ,
        NEW.tgl_pindahkamar ,
        NEW.status_verifikasi ,
        NEW.is_skd ,
        NEW.is_pasientitipan ,
        NEW.is_aps ,
        NEW.asuransipasien_id ,
        NEW.kelas_ditagihkan_id ,
        NEW.kamar_titipan_id ,
        NEW.tempattidur_titipan_id ,
        NEW.ruangan_titipan_id ,
        NEW.is_stoptitipan ,
        NEW.additional_data ,
        NEW.created_date ,
        NEW.created_by ,
        NEW.modified_count ,
        NEW.last_modified_date ,
        NEW.last_modified_by ,
        NEW.is_deleted ,
        NEW.is_active ,
        NEW.deleted_date ,
        NEW.deleted_by ,
            'INSERT'
        );

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"pasienadmisi_r_update\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    -- @created by ridona 28 Agustus 2020
    
    DECLARE
        v_keterangan VARCHAR;
    BEGIN
        IF(NEW.is_deleted IS TRUE)
        THEN
            v_keterangan := 'DELETE';
        ELSE
            v_keterangan := 'UPDATE';
  END IF;
        
    -- INSERT table history pasienadmisi_r
    INSERT INTO pasienadmisi_r (        
        pasienadmisi_id,
        shift_id,
        carabayar_id,
        penjamin_id,
        pasien_id,
        caramasuk_id,
        ruangan_id,
        pasienpulang_id,
        bookingkamar_id,
        pembayaranpelayanan_id,
        pendaftaran_id,
        kamarruangan_id,
        kelaspelayanan_id,
        pegawai_id,
        tgl_admisi,
        tgl_pendaftaran,
        tgl_pulang,
        kunjungan,
        status_keluar,
        rawat_gabung,
        rencana_pulang,
        kamartempattidur_id,
        bpjs_id,
        status_ranap,
        pasienbatalperiksa_id,
        tgl_pindahkamar,
        status_verifikasi,
        is_skd,
        is_pasientitipan,
        is_aps,
        asuransipasien_id,
        kelas_ditagihkan_id,
        kamar_titipan_id,
        tempattidur_titipan_id,
        ruangan_titipan_id,
        is_stoptitipan,
        additional_data,
        created_date,
        created_by,
        modified_count,
        last_modified_date,
        last_modified_by,
        is_deleted,
        is_active,
        deleted_date,
        deleted_by,
        keterangan
    )VALUES(
        NEW.pasienadmisi_id ,
        NEW.shift_id ,
        NEW.carabayar_id ,
        NEW.penjamin_id ,
        NEW.pasien_id ,
        NEW.caramasuk_id , 
        NEW.ruangan_id ,
        NEW.pasienpulang_id ,
        NEW.bookingkamar_id ,
        NEW.pembayaranpelayanan_id ,
        NEW.pendaftaran_id ,
        NEW.kamarruangan_id ,
        NEW.kelaspelayanan_id ,
        NEW.pegawai_id ,
        NEW.tgl_admisi ,
        NEW.tgl_pendaftaran ,
        NEW.tgl_pulang ,
        NEW.kunjungan ,
        NEW.status_keluar ,
        NEW.rawat_gabung ,
        NEW.rencana_pulang ,
        NEW.kamartempattidur_id ,
        NEW.bpjs_id ,
        NEW.status_ranap ,
        NEW.pasienbatalperiksa_id ,
        NEW.tgl_pindahkamar ,
        NEW.status_verifikasi ,
        NEW.is_skd ,
        NEW.is_pasientitipan ,
        NEW.is_aps ,
        NEW.asuransipasien_id ,
        NEW.kelas_ditagihkan_id ,
        NEW.kamar_titipan_id ,
        NEW.tempattidur_titipan_id ,
        NEW.ruangan_titipan_id ,
        NEW.is_stoptitipan ,
        NEW.additional_data ,
        NEW.created_date ,
        NEW.created_by ,
        NEW.modified_count ,
        NEW.last_modified_date ,
        NEW.last_modified_by ,
        NEW.is_deleted ,
        NEW.is_active ,
        NEW.deleted_date ,
        NEW.deleted_by ,
        v_keterangan
    );

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"pemakaianobatdetail_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN
------------------------------->INSERT table history pemakaianobatdetail_r<----------------------------------
        INSERT INTO pemakaianobatdetail_r (     
            pemakaianobatdetail_id,
            satuankecil_id,
            pemakaianobat_id,
            obatalkes_id,
            qty_satuanpakai,
            harga_satuanpakai,
            harganetto_satuanpakai,
            ket_obatpakai,
            additional_data,
            created_date,
            created_by,
            modified_count,
            last_modified_date,
            last_modified_by,
            is_deleted,
            is_active,
            deleted_date,
            deleted_by,
            satuanbesar_id,
            jumlah_input,
            keterangan_rekap
        )VALUES(
            NEW.pemakaianobatdetail_id,
            NEW.satuankecil_id,
            NEW.pemakaianobat_id,
            NEW.obatalkes_id,
            NEW.qty_satuanpakai,
            NEW.harga_satuanpakai,
            NEW.harganetto_satuanpakai,
            NEW.ket_obatpakai,
            NEW.additional_data,
            NEW.created_date,
            NEW.created_by,
            NEW.modified_count,
            NEW.last_modified_date,
            NEW.last_modified_by,
            NEW.is_deleted,
            NEW.is_active,
            NEW.deleted_date,
            NEW.deleted_by,
            NEW.satuanbesar_id,
            NEW.jumlah_input,
            'ACCRUAL'
            );

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"pemakaianuangmuka_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    
        
BEGIN
------------------------------> INSERT table rekap pemakaianuangmuka_r <---------------------------------------------------
        INSERT INTO pemakaianuangmuka_r (       
            pemakaianuangmuka_id,
            pembayaranpelayanan_id,
            tandabuktikeluar_id,
            pendaftaran_id,
            tgl_pemakaian,
            total_uangmuka,
            pemakaian_uangmuka,
            sisa_uangmuka,
            additional_data,
            created_date,
            created_by,
            modified_count,
            last_modified_date,
            last_modified_by,
            is_deleted,
            is_active,
            deleted_date,
            deleted_by,
            bayaruangmuka_id,
            keterangan
        )VALUES(
            NEW.pemakaianuangmuka_id,
            NEW.pembayaranpelayanan_id,
            NEW.tandabuktikeluar_id,
            NEW.pendaftaran_id,
            NEW.tgl_pemakaian,
            NEW.total_uangmuka,
            -1 * NEW.pemakaian_uangmuka,
            NEW.sisa_uangmuka,
            NEW.additional_data,
            NEW.created_date,
            NEW.created_by,
            NEW.modified_count,
            NEW.last_modified_date,
            NEW.last_modified_by,
            NEW.is_deleted,
            NEW.is_active,
            NEW.deleted_date,
            NEW.deleted_by,
            NEW.bayaruangmuka_id,
            'Deposit Availed'
        );

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"pembatalanresep_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN
------------------------------->INSERT table history pembatalanresep_r<----------------------------------
        INSERT INTO pembatalanresep_r (     
                pembatalanresep_id,
                penjualanresep_id,
                tgl_pembatalan,
                no_pembatalan,
                petugas_batal_id,
                alasan_batal,
                additional_data,
                created_date,
                created_by,
                modified_count,
                last_modified_date,
                last_modified_by,
                is_deleted,
                is_active,
                deleted_date,
                deleted_by,
                keterangan_rekap
        )VALUES(
                NEW.pembatalanresep_id,
                NEW.penjualanresep_id,
                NEW.tgl_pembatalan,
                NEW.no_pembatalan,
                NEW.petugas_batal_id,
                NEW.alasan_batal,
                NEW.additional_data,
                NEW.created_date,
                NEW.created_by,
                NEW.modified_count,
                NEW.last_modified_date,
                NEW.last_modified_by,
                NEW.is_deleted,
                NEW.is_active,
                NEW.deleted_date,
                NEW.deleted_by,
                'ACCRUAL'
            );

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"pembatalanresepdetail_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN
------------------------------->INSERT table history pembatalanresepdetail_r<----------------------------------
        INSERT INTO pembatalanresepdetail_r (       
                obatalkespasien_id,
                sumberdana_id,
                racikan_id,
                returresepdetail_id,
                tipepaket_id,
                ruangan_id,
                carabayar_id,
                pegawai_id,
                daftartindakan_id,
                tindakanpelayanan_id,
                satuankecil_id,
                shift_id,
                pendaftaran_id,
                obatalkes_id,
                pasien_id,
                penjamin_id,
                kelaspelayanan_id,
                pasienanastesi_id,
                pasienmasukpenunjang_id,
                pasienadmisi_id,
                obatsudahbayar_id,
                penjualanresep_id,
                tglpelayanan,
                r,
                rke,
                permintaan_oa,
                jmlkemasan_oa,
                kekuatan_oa,
                satuankekuatan_oa,
                qty_oa,
                hargasatuan_oa,
                signa_oa,
                harganetto_oa,
                hargajual_oa,
                etiket,
                jmlexposerad,
                kontrasrad,
                biayaservice,
                biayakonseling,
                jasadokterresep,
                biayakemasan,
                biayaadministrasi,
                tarifcyto,
                discount,
                subsidiasuransi,
                subsidipemerintah,
                subsidirs,
                iurbiaya,
                oa,
                pembulatan,
                verifikasitagihan_id,
                jurnalrekening_id,
                permohonanoadetail_id,
                persenppnjual,
                resepturdetail_id,
                nilaippnjual,
                additional_data,
                created_date,
                created_by,
                modified_count,
                last_modified_date,
                last_modified_by,
                is_deleted,
                is_active,
                deleted_date,
                deleted_by,
                perawat1_id,
                perawat2_id,
                instruksitindakanbmhp_id,
                implementasi_id,
                is_dilakukan,
                pemakaianambulan_id,
                is_jurnal,
                konfigmargindetail_id,
                qty_konversi,
                is_penatajasa,
                det,
                status_bmhp,
                det_konversi,
                signa,
                no_obatalkespasien,
                keterangan_rekap
        )VALUES(
                NEW.obatalkespasien_id,
                NEW.sumberdana_id,
                NEW.racikan_id,
                NEW.returresepdetail_id,
                NEW.tipepaket_id,
                NEW.ruangan_id,
                NEW.carabayar_id,
                NEW.pegawai_id,
                NEW.daftartindakan_id,
                NEW.tindakanpelayanan_id,
                NEW.satuankecil_id,
                NEW.shift_id,
                NEW.pendaftaran_id,
                NEW.obatalkes_id,
                NEW.pasien_id,
                NEW.penjamin_id,
                NEW.kelaspelayanan_id,
                NEW.pasienanastesi_id,
                NEW.pasienmasukpenunjang_id,
                NEW.pasienadmisi_id,
                NEW.obatsudahbayar_id,
                NEW.penjualanresep_id,
                NEW.tglpelayanan,
                NEW.r,
                NEW.rke,
                NEW.permintaan_oa,
                NEW.jmlkemasan_oa,
                NEW.kekuatan_oa,
                NEW.satuankekuatan_oa,
                NEW.qty_oa,
                NEW.hargasatuan_oa,
                NEW.signa_oa,
                NEW.harganetto_oa,
                NEW.hargajual_oa,
                NEW.etiket,
                NEW.jmlexposerad,
                NEW.kontrasrad,
                NEW.biayaservice,
                NEW.biayakonseling,
                NEW.jasadokterresep,
                NEW.biayakemasan,
                NEW.biayaadministrasi,
                NEW.tarifcyto,
                NEW.discount,
                NEW.subsidiasuransi,
                NEW.subsidipemerintah,
                NEW.subsidirs,
                NEW.iurbiaya,
                NEW.oa,
                NEW.pembulatan,
                NEW.verifikasitagihan_id,
                NEW.jurnalrekening_id,
                NEW.permohonanoadetail_id,
                NEW.persenppnjual,
                NEW.resepturdetail_id,
                NEW.nilaippnjual,
                NEW.additional_data,
                NEW.created_date,
                NEW.created_by,
                NEW.modified_count,
                NEW.last_modified_date,
                NEW.last_modified_by,
                NEW.is_deleted,
                NEW.is_active,
                NEW.deleted_date,
                NEW.deleted_by,
                NEW.perawat1_id,
                NEW.perawat2_id,
                NEW.instruksitindakanbmhp_id,
                NEW.implementasi_id,
                NEW.is_dilakukan,
                NEW.pemakaianambulan_id,
                NEW.is_jurnal,
                NEW.konfigmargindetail_id,
                NEW.qty_konversi,
                NEW.is_penatajasa,
                NEW.det,
                NEW.status_bmhp,
                NEW.det_konversi,
                NEW.signa,
                NEW.no_obatalkespasien,
                'ACCRUAL'
        );

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"pembayaran_r_delete\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$ 
    
DECLARE  
        v_daftartindakan_id int;
        v_ruangan_id int;
        v_penjamin_id int;
        v_instalasi_id int;
        v_pegawai_id int;
    v_totalpembulatan float;
    v_total_diskon float;
    v_total_discountpembayaran float;
    v_totaladministrasi float;
        v_totaltunai float;

BEGIN
        
        SELECT
            pembayaran_t.total_tunai
            INTO
            v_totaltunai
        FROM pembayaran_t
        WHERE pembayaran_t.pembayaran_id = NEW.pembayaran_id;
        
        IF(NEW.is_deleted IS TRUE and v_totaltunai <> 0)
        THEN
            
           -- INSERT table history pembayaran_r, menjadi Deposit Refund
           INSERT INTO pembayaran_r (       
                pembayaran_id,
                pendaftaran_id,
                pasienadmisi_id,
                total_tagihan,
                total_dibayar,
                total_dijamin,
                total_sisatagihan,
                total_kembalian,
                total_administrasi,
                total_pembulatan,
                total_pembebasan,
                penggunaan_uangmuka,
                pemberianpiutang_id,
                total_ditagihkan,
                total_tunai,
                total_nontunai,
                total_discount,
                total_discountpembayaran,
                catatan,
                tipe_pembayaran,
                additional_data,
                created_date,
                created_by,
                modified_count,
                last_modified_date,
                last_modified_by,
                is_deleted,
                is_active,
                deleted_date,
                deleted_by,
                keterangan
            )
            SELECT
                pembayaran_id,
                pendaftaran_id,
                pasienadmisi_id,
                -1 * total_tagihan ,
                -1 * total_dibayar ,
                -1 * total_dijamin ,
                -1 * total_sisatagihan ,
                -1 * total_kembalian ,
                -1 * total_administrasi ,
                -1 * total_pembulatan ,
                -1 * total_pembebasan ,
                penggunaan_uangmuka ,
                pemberianpiutang_id ,
                -1 * total_ditagihkan ,
                -1 * total_tunai ,
                0,
                -1 * total_discount ,
                -1 * total_discountpembayaran ,
                catatan,
                682,
                NEW.additional_data,
                NEW.created_date,
                NEW.created_by,
                NEW.modified_count,
                NEW.last_modified_date,
                NEW.last_modified_by,
                NEW.is_deleted,
                NEW.is_active,
                NEW.deleted_date,
                NEW.deleted_by,
                'BILL CANCEL'
            FROM pembayaran_t
            WHERE pembayaran_id = NEW.pembayaran_id;
    
    END IF;
----------------------------> insert pembulatan ke tindakanpelayanan_r <---------------------------------   
    
            SELECT
                pembayaran_t.total_pembulatan
            INTO
                v_totalpembulatan
            FROM pembayaran_t
            WHERE pembayaran_t.pembayaran_id = NEW.pembayaran_id;
    
    IF(NEW.is_deleted IS TRUE and v_totalpembulatan <> 0)
        THEN

            SELECT 
                pendaftaran_t.ruangan_id,
                pendaftaran_t.penjamin_id,
                pendaftaran_t.instalasi_id,
                pendaftaran_t.pegawai_id
            INTO
                v_ruangan_id,
                v_penjamin_id,
                v_instalasi_id,
                v_pegawai_id
            FROM pendaftaran_t
            WHERE pendaftaran_t.pendaftaran_id = NEW.pendaftaran_id;
            
            v_daftartindakan_id:=99990; -- daftartindakan_id untuk pembulatan
                    
        INSERT INTO tindakanpelayanan_r (       
                pendaftaran_id,
                daftartindakan_id,
                tarif_satuan,
                tarif_tindakan,
                qty_tindakan,
                keterangan,
                ruangan_id,
                instalasi_id,
                penjamin_id,
                dokterpenanggungjawab_id,
                tgl_tindakan,
                additional_data,
                created_date,
                created_by,
                modified_count,
                last_modified_date,
                last_modified_by,
                is_deleted,
                is_active,
                deleted_date,
                deleted_by,
              tgl_proses,
                pembayaran_id
        )VALUES(
                NEW.pendaftaran_id,
                v_daftartindakan_id,
                v_totalpembulatan,
                v_totalpembulatan,
                '-1',
                'BILL CANCEL',
                v_ruangan_id,
                v_instalasi_id,
                v_penjamin_id,
                v_pegawai_id,
                NEW.created_date,
                NEW.additional_data,
                NEW.created_date,
                NEW.created_by,
                NEW.modified_count,
                NEW.last_modified_date,
                NEW.last_modified_by,
                NEW.is_deleted,
                NEW.is_active,
                NEW.deleted_date,
                NEW.deleted_by,
                NEW.created_date,
                NEW.pembayaran_id
        );
        
    END IF;
    
    ---------------------------> insert diskon ke tindakanpelayanan_r <-------------------------
    
            SELECT
                pembayaran_t.total_discount,
                pembayaran_t.total_discountpembayaran
            INTO
                v_total_diskon,
                v_total_discountpembayaran
            FROM pembayaran_t
            WHERE pembayaran_t.pembayaran_id = NEW.pembayaran_id;
    
    IF(NEW.is_deleted IS TRUE and (COALESCE(v_total_diskon,0) + COALESCE(v_total_discountpembayaran,0) <> 0))
        THEN

            SELECT 
                pendaftaran_t.ruangan_id,
                pendaftaran_t.penjamin_id,
                pendaftaran_t.instalasi_id,
                pendaftaran_t.pegawai_id
            INTO
                v_ruangan_id,
                v_penjamin_id,
                v_instalasi_id,
                v_pegawai_id
            FROM pendaftaran_t
            WHERE pendaftaran_t.pendaftaran_id = NEW.pendaftaran_id;
            
            v_daftartindakan_id:=99991; -- daftartindakan_id untuk diskon
            
        INSERT INTO tindakanpelayanan_r (       
                pendaftaran_id,
                daftartindakan_id,
                tarif_satuan,
                tarif_tindakan,
                qty_tindakan,
                keterangan,
                ruangan_id,
                instalasi_id,
                penjamin_id,
                dokterpenanggungjawab_id,
                tgl_tindakan,
                additional_data,
                created_date,
                created_by,
                modified_count,
                last_modified_date,
                last_modified_by,
                is_deleted,
                is_active,
                deleted_date,
                deleted_by,
              tgl_proses,
                pembayaran_id
        )VALUES(
                NEW.pendaftaran_id,
                v_daftartindakan_id,
                COALESCE(-1 * v_total_diskon,0)+COALESCE(-1 * v_total_discountpembayaran,0),
                COALESCE(-1 * v_total_diskon,0)+COALESCE(-1 * v_total_discountpembayaran,0),
                '-1',
                'DISCOUNT CANCEL',
                v_ruangan_id,
                v_instalasi_id,
                v_penjamin_id,
                v_pegawai_id,
                NEW.created_date,
                NEW.additional_data,
                NEW.created_date,
                NEW.created_by,
                NEW.modified_count,
                NEW.last_modified_date,
                NEW.last_modified_by,
                NEW.is_deleted,
                NEW.is_active,
                NEW.deleted_date,
                NEW.deleted_by,
                NEW.created_date,
                NEW.pembayaran_id
        );
        
    END IF;
    
    -------------------------------------> insert administrasi ke tindakanpelayanan_r <--------------------------------------------
    
            SELECT
                pembayaran_t.total_administrasi
            INTO
                v_totaladministrasi
            FROM pembayaran_t
            WHERE pembayaran_t.pembayaran_id = NEW.pembayaran_id;
    
    IF(NEW.is_deleted IS TRUE and v_totaladministrasi <> 0)
        THEN

            SELECT 
                pendaftaran_t.ruangan_id,
                pendaftaran_t.penjamin_id,
                pendaftaran_t.instalasi_id,
                pendaftaran_t.pegawai_id
            INTO
                v_ruangan_id,
                v_penjamin_id,
                v_instalasi_id,
                v_pegawai_id
            FROM pendaftaran_t
            WHERE pendaftaran_t.pendaftaran_id = NEW.pendaftaran_id;
        
            v_daftartindakan_id:=99992; -- daftartindakan_id untuk administrasi
            
        INSERT INTO tindakanpelayanan_r (       
                pendaftaran_id,
                daftartindakan_id,
                tarif_satuan,
                tarif_tindakan,
                qty_tindakan,
                keterangan,
                ruangan_id,
                instalasi_id,
                penjamin_id,
                dokterpenanggungjawab_id,
                tgl_tindakan,
                additional_data,
                created_date,
                created_by,
                modified_count,
                last_modified_date,
                last_modified_by,
                is_deleted,
                is_active,
                deleted_date,
                deleted_by,
              tgl_proses,
                pembayaran_id
        )VALUES(
                NEW.pendaftaran_id,
                v_daftartindakan_id,
                -1 * v_totaladministrasi,
                -1 * v_totaladministrasi,
                '1',
                'BILL CANCEL',
                v_ruangan_id,
                v_instalasi_id,
                v_penjamin_id,
                v_pegawai_id,
                NEW.created_date,
                NEW.additional_data,
                NEW.created_date,
                NEW.created_by,
                NEW.modified_count,
                NEW.last_modified_date,
                NEW.last_modified_by,
                NEW.is_deleted,
                NEW.is_active,
                NEW.deleted_date,
                NEW.deleted_by,
                NEW.created_date,
                NEW.pembayaran_id
        );
        
    END IF;
    
    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"pembayaran_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    
        
BEGIN
    IF(NEW.total_tunai <> 0)
        THEN
        -- INSERT table history pembayaran_r untuk case tunai
        INSERT INTO pembayaran_r (      
        pembayaran_id,
        pendaftaran_id,
        pasienadmisi_id,
        total_tagihan,
        total_dibayar,
        total_dijamin,
        total_sisatagihan,
        total_kembalian,
        total_administrasi,
        total_pembulatan,
        total_pembebasan,
        penggunaan_uangmuka,
        pemberianpiutang_id,
        total_ditagihkan,
        total_tunai,
        total_nontunai,
        total_discount,
        total_discountpembayaran,
        catatan,
        tipe_pembayaran,
        additional_data,
        created_date,
        created_by,
        modified_count,
        last_modified_date,
        last_modified_by,
        is_deleted,
        is_active,
        deleted_date,
        deleted_by,
        keterangan  
        )VALUES(
        NEW.pembayaran_id ,
        NEW.pendaftaran_id ,
        NEW.pasienadmisi_id ,
        NEW.total_tagihan ,
        NEW.total_dibayar ,
        NEW.total_dijamin ,
        NEW.total_sisatagihan ,
        NEW.total_kembalian ,
        NEW.total_administrasi ,
        NEW.total_pembulatan ,
        NEW.total_pembebasan ,
        NEW.penggunaan_uangmuka ,
        NEW.pemberianpiutang_id ,
        NEW.total_ditagihkan ,
        NEW.total_tunai ,
        0 , --total_nontunai
        NEW.total_discount ,
        NEW.total_discountpembayaran ,
        NEW.catatan ,
        682,
        NEW.additional_data ,
        NEW.created_date ,
        NEW.created_by ,
        NEW.modified_count ,
        NEW.last_modified_date ,
        NEW.last_modified_by ,
        NEW.is_deleted ,
        NEW.is_active ,
        NEW.deleted_date ,
        NEW.deleted_by ,
        'RECEIPT'
        );
END IF;
    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"pembayaran_r_nontunai\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE  
        v_tipe_pembayaran INT;
        
BEGIN
    
            SELECT 
                jenisnontunai_m.tipe_pembayaran
            INTO
                v_tipe_pembayaran
            FROM jenisnontunai_m
            WHERE jenisnontunai_m.jenisnontunai_id = NEW.jenisnontunai_id;

        -- INSERT table history pembayaran_r untuk case non tunai
        INSERT INTO pembayaran_r (      
        pembayaran_id,
        pendaftaran_id,
        total_tunai,
        total_nontunai,
        nama_edc,
        tipe_pembayaran,
        additional_data,
        created_date,
        created_by,
        modified_count,
        last_modified_date,
        last_modified_by,
        is_deleted,
        is_active,
        deleted_date,
        deleted_by,
        keterangan
        )VALUES(
        NEW.pembayaran_id ,
        NEW.pendaftaran_id ,
        0, --total_tunai
        NEW.total_dibayar ,
        NEW.nama_edc,
        v_tipe_pembayaran,
        NEW.additional_data ,
        NEW.created_date ,
        NEW.created_by ,
        NEW.modified_count ,
        NEW.last_modified_date ,
        NEW.last_modified_by ,
        NEW.is_deleted ,
        NEW.is_active ,
        NEW.deleted_date ,
        NEW.deleted_by ,
        'RECEIPT'
        );

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"pembayaran_r_nontunai_batal\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE  
        v_tipe_pembayaran INT;
        
BEGIN
            SELECT 
                jenisnontunai_m.tipe_pembayaran
            INTO
                v_tipe_pembayaran
            FROM jenisnontunai_m
            WHERE jenisnontunai_m.jenisnontunai_id = NEW.jenisnontunai_id;
            
        IF(NEW.is_deleted IS TRUE)
            THEN
        -- INSERT table history pembayaran_r untuk case pembatalan non tunai
        INSERT INTO pembayaran_r (      
        pembayaran_id,
        pendaftaran_id,
        total_tunai,
        total_nontunai,
        nama_edc,
        tipe_pembayaran,
        additional_data,
        created_date,
        created_by,
        modified_count,
        last_modified_date,
        last_modified_by,
        is_deleted,
        is_active,
        deleted_date,
        deleted_by,
        keterangan
        )VALUES(
        NEW.pembayaran_id ,
        NEW.pendaftaran_id ,
        0, --total_tunai
        -1 * OLD.total_dibayar ,
        NEW.nama_edc,
        v_tipe_pembayaran,
        NEW.additional_data ,
        NEW.created_date ,
        NEW.created_by ,
        NEW.modified_count ,
        NEW.last_modified_date ,
        NEW.last_modified_by ,
        NEW.is_deleted ,
        NEW.is_active ,
        NEW.deleted_date ,
        NEW.deleted_by ,
        'BILL CANCEL'
        );
END IF;

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"pembayaran_r_update\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$ 
    -- @created by ridona 31 Agustus 2020
    
    DECLARE
        v_keterangan VARCHAR;
    BEGIN
        IF(NEW.is_deleted IS TRUE)
        THEN
            v_keterangan := 'ACCRUAL REVERSAL';
            
           -- INSERT table history pembayaran_r REVERSAL
           INSERT INTO pembayaran_r (       
                pembayaran_id,
                pendaftaran_id,
                pasienadmisi_id,
                total_tagihan,
                total_dibayar,
                total_dijamin,
                total_sisatagihan,
                total_kembalian,
                total_administrasi,
                total_pembulatan,
                total_pembebasan,
                penggunaan_uangmuka,
                pemberianpiutang_id,
                total_ditagihkan,
                total_tunai,
                total_nontunai,
                total_discount,
                total_discountpembayaran,
                catatan,
                additional_data,
                created_date,
                created_by,
                modified_count,
                last_modified_date,
                last_modified_by,
                is_deleted,
                is_active,
                deleted_date,
                deleted_by,
                keterangan
            )VALUES(
                OLD.pembayaran_id ,
                OLD.pendaftaran_id ,
                OLD.pasienadmisi_id ,
                -1 * OLD.total_tagihan ,
                -1 * OLD.total_dibayar ,
                -1 * OLD.total_dijamin ,
                -1 * OLD.total_sisatagihan ,
                -1 * OLD.total_kembalian ,
                -1 * OLD.total_administrasi ,
                -1 * OLD.total_pembulatan ,
                -1 * OLD.total_pembebasan ,
                OLD.penggunaan_uangmuka ,
                OLD.pemberianpiutang_id ,
                -1 * OLD.total_ditagihkan ,
                -1 * OLD.total_tunai ,
                -1 * OLD.total_nontunai ,
                -1 * OLD.total_discount ,
                -1 * OLD.total_discountpembayaran ,
                OLD.catatan ,
                OLD.additional_data ,
                OLD.created_date ,
                OLD.created_by ,
                OLD.modified_count ,
                OLD.last_modified_date ,
                OLD.last_modified_by ,
                OLD.is_deleted ,
                OLD.is_active ,
                OLD.deleted_date ,
                OLD.deleted_by ,
                v_keterangan
            );
        ELSE
                v_keterangan := 'RECEIPT';
            
                -- INSERT table history pembayaran_r REVERSAL
                INSERT INTO pembayaran_r (      
                pembayaran_id,
                pendaftaran_id,
                pasienadmisi_id,
                total_tagihan,
                total_dibayar,
                total_dijamin,
                total_sisatagihan,
                total_kembalian,
                total_administrasi,
                total_pembulatan,
                total_pembebasan,
                penggunaan_uangmuka,
                pemberianpiutang_id,
                total_ditagihkan,
                total_tunai,
                total_nontunai,
                total_discount,
                total_discountpembayaran,
                catatan,
                additional_data,
                created_date,
                created_by,
                modified_count,
                last_modified_date,
                last_modified_by,
                is_deleted,
                is_active,
                deleted_date,
                deleted_by,
                keterangan
                )VALUES(
                OLD.pembayaran_id ,
                OLD.pendaftaran_id ,
                OLD.pasienadmisi_id ,
                -1 * OLD.total_tagihan ,
                -1 * OLD.total_dibayar ,
                -1 * OLD.total_dijamin ,
                -1 * OLD.total_sisatagihan ,
                -1 * OLD.total_kembalian ,
                -1 * OLD.total_administrasi ,
                -1 * OLD.total_pembulatan ,
                -1 * OLD.total_pembebasan ,
                OLD.penggunaan_uangmuka ,
                OLD.pemberianpiutang_id ,
                -1 * OLD.total_ditagihkan ,
                -1 * OLD.total_tunai ,
                -1 * OLD.total_nontunai ,
                -1 * OLD.total_discount ,
                -1 * OLD.total_discountpembayaran ,
                OLD.catatan ,
                OLD.additional_data ,
                OLD.created_date ,
                OLD.created_by ,
                OLD.modified_count ,
                OLD.last_modified_date ,
                OLD.last_modified_by ,
                OLD.is_deleted ,
                OLD.is_active ,
                OLD.deleted_date ,
                OLD.deleted_by ,
                'ACCRUAL REVERSAL'
                );
        
    -- INSERT table history pembayaran_r
    INSERT INTO pembayaran_r (      
        pembayaran_id,
        pendaftaran_id,
        pasienadmisi_id,
        total_tagihan,
        total_dibayar,
        total_dijamin,
        total_sisatagihan,
        total_kembalian,
        total_administrasi,
        total_pembulatan,
        total_pembebasan,
        penggunaan_uangmuka,
        pemberianpiutang_id,
        total_ditagihkan,
        total_tunai,
        total_nontunai,
        total_discount,
        total_discountpembayaran,
        catatan,
        additional_data,
        created_date,
        created_by,
        modified_count,
        last_modified_date,
        last_modified_by,
        is_deleted,
        is_active,
        deleted_date,
        deleted_by,
        keterangan
    )VALUES(
        NEW.pembayaran_id ,
        NEW.pendaftaran_id ,
        NEW.pasienadmisi_id ,
        NEW.total_tagihan ,
        NEW.total_dibayar ,
        NEW.total_dijamin ,
        NEW.total_sisatagihan ,
        NEW.total_kembalian ,
        NEW.total_administrasi ,
        NEW.total_pembulatan ,
        NEW.total_pembebasan ,
        NEW.penggunaan_uangmuka ,
        NEW.pemberianpiutang_id ,
        NEW.total_ditagihkan ,
        NEW.total_tunai ,
        NEW.total_nontunai ,
        NEW.total_discount ,
        NEW.total_discountpembayaran ,
        NEW.catatan ,
        NEW.additional_data ,
        NEW.created_date ,
        NEW.created_by ,
        NEW.modified_count ,
        NEW.last_modified_date ,
        NEW.last_modified_by ,
        NEW.is_deleted ,
        NEW.is_active ,
        NEW.deleted_date ,
        NEW.deleted_by ,
        v_keterangan
    );
    END IF;
    
    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"pembulatan_diskon_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$ 
    
DECLARE  
        v_daftartindakan_id int;
        v_ruangan_id int;
        v_penjamin_id int;
        v_instalasi_id int;
        v_pegawai_id int;
        v_kelaspelayanan_id int;

BEGIN   
--------------------------> insert pembulatan ke tindakanpelayanan_r <--------------------------------

    IF(NEW.total_pembulatan <> 0)
        THEN
            
            SELECT 
                pendaftaran_t.ruangan_id,
                pendaftaran_t.penjamin_id,
                pendaftaran_t.instalasi_id,
                pendaftaran_t.pegawai_id,
                pendaftaran_t.kelaspelayanan_id
            INTO
                v_ruangan_id,
                v_penjamin_id,
                v_instalasi_id,
                v_pegawai_id,
                v_kelaspelayanan_id
            FROM pendaftaran_t
            WHERE pendaftaran_t.pendaftaran_id = NEW.pendaftaran_id;

            v_daftartindakan_id:=99990; -- daftartindakan_id untuk pembulatan
            
        
        INSERT INTO tindakanpelayanan_r (       
                pendaftaran_id,
                daftartindakan_id,
                tarif_satuan,
                tarif_tindakan,
                qty_tindakan,
                keterangan,
                ruangan_id,
                instalasi_id,
                penjamin_id,
                kelaspelayanan_id,
                dokterpenanggungjawab_id,
                tgl_tindakan,
                additional_data,
                created_date,
                created_by,
                modified_count,
                last_modified_date,
                last_modified_by,
                is_deleted,
                is_active,
                deleted_date,
                deleted_by,
              tgl_proses,
                pembayaran_id
        )VALUES(
                NEW.pendaftaran_id,
                v_daftartindakan_id,
                NEW.total_pembulatan,
                NEW.total_pembulatan,
                '1',
                'BILLING',
                v_ruangan_id,
                v_instalasi_id,
                v_penjamin_id,
                v_kelaspelayanan_id,
                v_pegawai_id,
                new.created_date,
                new.additional_data,
                new.created_date,
                new.created_by,
                new.modified_count,
                new.last_modified_date,
                new.last_modified_by,
                new.is_deleted,
                new.is_active,
                new.deleted_date,
                new.deleted_by,
                new.created_date,
                new.pembayaran_id
        );
        
END IF;     


-------------------------> insert diskon ke tindakanpelayanan_r <----------------------------------

IF(COALESCE(NEW.total_discount,0) + COALESCE(NEW.total_discountpembayaran,0) <> 0)
                THEN
                    
                    SELECT 
                        pendaftaran_t.ruangan_id,
                        pendaftaran_t.penjamin_id,
                        pendaftaran_t.instalasi_id,
                        pendaftaran_t.pegawai_id,
                        pendaftaran_t.kelaspelayanan_id
                    INTO
                        v_ruangan_id,
                        v_penjamin_id,
                        v_instalasi_id,
                        v_pegawai_id,
                        v_kelaspelayanan_id
                    FROM pendaftaran_t
                    WHERE pendaftaran_t.pendaftaran_id = NEW.pendaftaran_id;

                    v_daftartindakan_id:=99991; -- daftartindakan_id untuk diskon
                    
                
                INSERT INTO tindakanpelayanan_r (       
                        pendaftaran_id,
                        daftartindakan_id,
                        tarif_satuan,
                        tarif_tindakan,
                        qty_tindakan,
                        keterangan,
                        ruangan_id,
                        instalasi_id,
                        penjamin_id,
                        kelaspelayanan_id,
                        dokterpenanggungjawab_id,
                        tgl_tindakan,
                        additional_data,
                        created_date,
                        created_by,
                        modified_count,
                        last_modified_date,
                        last_modified_by,
                        is_deleted,
                        is_active,
                        deleted_date,
                        deleted_by,
                        tgl_proses,
                        pembayaran_id
                )VALUES(
                        NEW.pendaftaran_id,
                        v_daftartindakan_id,
                        COALESCE(-1*NEW.total_discount,0)+COALESCE(-1*NEW.total_discountpembayaran,0),
                     COALESCE(-1*NEW.total_discount,0)+COALESCE(-1*NEW.total_discountpembayaran,0),
                        '1',
                        'DISCOUNT',
                        v_ruangan_id,
                        v_instalasi_id,
                        v_penjamin_id,
                        v_kelaspelayanan_id,
                        v_pegawai_id,
                        new.created_date,
                        new.additional_data,
                        new.created_date,
                        new.created_by,
                        new.modified_count,
                        new.last_modified_date,
                        new.last_modified_by,
                        new.is_deleted,
                        new.is_active,
                        new.deleted_date,
                        new.deleted_by,
                        new.created_date,
                        new.pembayaran_id
                );

END IF;

---------------------------------> insert administrasi ke tindakanpelayanan_r <-------------------------------------

IF(NEW.total_administrasi <> 0)
                THEN
                    
                    SELECT 
                        pendaftaran_t.ruangan_id,
                        pendaftaran_t.penjamin_id,
                        pendaftaran_t.instalasi_id,
                        pendaftaran_t.pegawai_id,
                        pendaftaran_t.kelaspelayanan_id
                    INTO
                        v_ruangan_id,
                        v_penjamin_id,
                        v_instalasi_id,
                        v_pegawai_id,
                        v_kelaspelayanan_id
                    FROM pendaftaran_t
                    WHERE pendaftaran_t.pendaftaran_id = NEW.pendaftaran_id;

                    v_daftartindakan_id:=99992; -- daftartindakan_id untuk administrasi
                    
                
                INSERT INTO tindakanpelayanan_r (       
                        pendaftaran_id,
                        daftartindakan_id,
                        tarif_satuan,
                        tarif_tindakan,
                        qty_tindakan,
                        keterangan,
                        ruangan_id,
                        instalasi_id,
                        penjamin_id,
                        kelaspelayanan_id,
                        dokterpenanggungjawab_id,
                        tgl_tindakan,
                        additional_data,
                        created_date,
                        created_by,
                        modified_count,
                        last_modified_date,
                        last_modified_by,
                        is_deleted,
                        is_active,
                        deleted_date,
                        deleted_by,
                        tgl_proses,
                        pembayaran_id
                )VALUES(
                        NEW.pendaftaran_id,
                        v_daftartindakan_id,
                        NEW.total_administrasi,
                        NEW.total_administrasi,
                        '1',
                        'BILLING',
                        v_ruangan_id,
                        v_instalasi_id,
                        v_penjamin_id,
                        v_kelaspelayanan_id,
                        v_pegawai_id,
                        new.created_date,
                        new.additional_data,
                        new.created_date,
                        new.created_by,
                        new.modified_count,
                        new.last_modified_date,
                        new.last_modified_by,
                        new.is_deleted,
                        new.is_active,
                        new.deleted_date,
                        new.deleted_by,
                        new.created_date,
                        new.pembayaran_id
                );

END IF;

RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"pemusnahanobatdetail_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN
------------------------------->INSERT table history pemusnahanobatdetail_r<----------------------------------
        INSERT INTO pemusnahanobatdetail_r (        
            pemusnahanobatdetail_id,
            pemusnahanobat_id,
            obatalkes_id,
            jumlah,
            tglkadaluarsa,
            nobatch,
            kondisibarang,
            harganetto,
            additional_data,
            created_date,
            created_by,
            modified_count,
            last_modified_date,
            last_modified_by,
            is_deleted,
            is_active,
            deleted_date,
            deleted_by,
            satuan_id,
            keterangan_rekap
        )VALUES(
            NEW.pemusnahanobatdetail_id,
            NEW.pemusnahanobat_id,
            NEW.obatalkes_id,
            NEW.jumlah,
            NEW.tglkadaluarsa,
            NEW.nobatch,
            NEW.kondisibarang,
            NEW.harganetto,
            NEW.additional_data,
            NEW.created_date,
            NEW.created_by,
            NEW.modified_count,
            NEW.last_modified_date,
            NEW.last_modified_by,
            NEW.is_deleted,
            NEW.is_active,
            NEW.deleted_date,
            NEW.deleted_by,
            NEW.satuan_id,
            'ACCRUAL'
            );

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"pendaftaran_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
-- @created by ridona 28 Agustus 2020
    
        
BEGIN
        -- INSERT table history pendaftaran_r
        INSERT INTO pendaftaran_r (     
        pendaftaran_id,
        no_pendaftaran,
        tgl_pendaftaran,
        pasienpulang_id,
        pasienbatalperiksa_id,
        penanggungjawab_id,
        penjamin_id,
        shift_id,
        pasien_id,
        persalinan_id,
        pegawai_id,
        instalasi_id,
        caramasuk_id,
        jeniskasuspenyakit_id,
        pembayaranpelayanan_id,
        kelaspelayanan_id,
        carabayar_id,
        pasienadmisi_id,
        golonganumur_id,
        rujukan_id,
        antrian_id,
        karcis_id,
        ruangan_id,
        no_urutantri,
        transportasi,
        keadaan_masuk,
        status_periksa,
        status_pasien,
        kunjungan,
        alih_status,
        by_phone,
        kunjungan_rumah,
        status_masuk,
        umur,
        tgl_selesaiperiksa,
        keterangan_pendaftaran,
        nopendaftaran_aktif,
        status_konfirmasi,
        tgl_konfirmasi,
        tgl_renkontrol,
        status_farmasi,
        panggil_antrian,
        asuransipasien_id,
        tgl_akandilayani,
        statusdok_rekammedik,
        bpjs_id,
        status_bayar,
        is_aps,
        label_gelang,
        is_karcis,
        tgl_masukperiksa,
        status_verifikasi,
        is_ranap,
        is_skd,
        pendaftaranibu_id,
        is_skl,
        catatan_penatajasa,
        is_stopakomodasi,
        tgl_stopakomodasi,
        additional_data,
        created_date,
        created_by,
        modified_count,
        last_modified_date,
        last_modified_by,
        is_deleted,
        is_active,
        deleted_date,
        deleted_by,
        keterangan
        )VALUES(
        NEW.pendaftaran_id ,
        NEW.no_pendaftaran ,
        NEW.tgl_pendaftaran ,
        NEW.pasienpulang_id ,
        NEW.pasienbatalperiksa_id ,
        NEW.penanggungjawab_id ,
        NEW.penjamin_id ,
        NEW.shift_id ,
        NEW.pasien_id ,
        NEW.persalinan_id ,
        NEW.pegawai_id ,
        NEW.instalasi_id ,
        NEW.caramasuk_id ,
        NEW.jeniskasuspenyakit_id ,
        NEW.pembayaranpelayanan_id ,
        NEW.kelaspelayanan_id ,
        NEW.carabayar_id ,
        NEW.pasienadmisi_id ,
        NEW.golonganumur_id ,
        NEW.rujukan_id ,
        NEW.antrian_id ,
        NEW.karcis_id ,
        NEW.ruangan_id ,
        NEW.no_urutantri ,
        NEW.transportasi ,
        NEW.keadaan_masuk ,
        NEW.status_periksa ,
        NEW.status_pasien ,
        NEW.kunjungan ,
        NEW.alih_status ,
        NEW.by_phone ,
        NEW.kunjungan_rumah ,
        NEW.status_masuk ,
        NEW.umur ,
        NEW.tgl_selesaiperiksa ,
        NEW.keterangan_pendaftaran ,
        NEW.nopendaftaran_aktif ,
        NEW.status_konfirmasi ,
        NEW.tgl_konfirmasi ,
        NEW.tgl_renkontrol ,
        NEW.status_farmasi ,
        NEW.panggil_antrian ,
        NEW.asuransipasien_id ,
        NEW.tgl_akandilayani ,
        NEW.statusdok_rekammedik ,
        NEW.bpjs_id ,
        NEW.status_bayar ,
        NEW.is_aps ,
        NEW.label_gelang ,
        NEW.is_karcis ,
        NEW.tgl_masukperiksa ,
        NEW.status_verifikasi ,
        NEW.is_ranap ,
        NEW.is_skd ,
        NEW.pendaftaranibu_id ,
        NEW.is_skl ,
        NEW.catatan_penatajasa ,
        NEW.is_stopakomodasi ,
        NEW.tgl_stopakomodasi ,
        NEW.additional_data ,
        NEW.created_date ,
        NEW.created_by ,
        NEW.modified_count ,
        NEW.last_modified_date ,
        NEW.last_modified_by ,
        NEW.is_deleted ,
        NEW.is_active ,
        NEW.deleted_date ,
        NEW.deleted_by ,
            'INSERT'
        );

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

      $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"pendaftaran_r_update\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$ 
    -- @created by ridona 28 Agustus 2020
    
    DECLARE
        v_keterangan VARCHAR;
        vpenjamin_id INTEGER; 
    BEGIN
        IF(NEW.is_deleted IS TRUE)
        THEN
            v_keterangan := 'DELETE';
        ELSE
            v_keterangan := 'UPDATE';
            
        SELECT penjamin_id INTO vpenjamin_id
    FROM pasienadmisi_t
    WHERE pendaftaran_id = NEW.pendaftaran_id
    LIMIT 1;
        IF(NEW.pasienadmisi_id IS NOT NULL)
        THEN
      NEW.penjamin_id := vpenjamin_id ;
    END IF;
        
  END IF;
        
    -- INSERT table history pendaftaran_r
    INSERT INTO pendaftaran_r (     
        pendaftaran_id,
        no_pendaftaran,
        tgl_pendaftaran,
        pasienpulang_id,
        pasienbatalperiksa_id,
        penanggungjawab_id,
        penjamin_id,
        shift_id,
        pasien_id,
        persalinan_id,
        pegawai_id,
        instalasi_id,
        caramasuk_id,
        jeniskasuspenyakit_id,
        pembayaranpelayanan_id,
        kelaspelayanan_id,
        carabayar_id,
        pasienadmisi_id,
        golonganumur_id,
        rujukan_id,
        antrian_id,
        karcis_id,
        ruangan_id,
        no_urutantri,
        transportasi,
        keadaan_masuk,
        status_periksa,
        status_pasien,
        kunjungan,
        alih_status,
        by_phone,
        kunjungan_rumah,
        status_masuk,
        umur,
        tgl_selesaiperiksa,
        keterangan_pendaftaran,
        nopendaftaran_aktif,
        status_konfirmasi,
        tgl_konfirmasi,
        tgl_renkontrol,
        status_farmasi,
        panggil_antrian,
        asuransipasien_id,
        tgl_akandilayani,
        statusdok_rekammedik,
        bpjs_id,
        status_bayar,
        is_aps,
        label_gelang,
        is_karcis,
        tgl_masukperiksa,
        status_verifikasi,
        is_ranap,
        is_skd,
        pendaftaranibu_id,
        is_skl,
        catatan_penatajasa,
        is_stopakomodasi,
        tgl_stopakomodasi,
        additional_data,
        created_date,
        created_by,
        modified_count,
        last_modified_date,
        last_modified_by,
        is_deleted,
        is_active,
        deleted_date,
        deleted_by,
        keterangan
    )VALUES(
        NEW.pendaftaran_id ,
        NEW.no_pendaftaran ,
        NEW.tgl_pendaftaran ,
        NEW.pasienpulang_id ,
        NEW.pasienbatalperiksa_id ,
        NEW.penanggungjawab_id ,
        NEW.penjamin_id,
        NEW.shift_id ,
        NEW.pasien_id ,
        NEW.persalinan_id ,
        NEW.pegawai_id ,
        NEW.instalasi_id ,
        NEW.caramasuk_id ,
        NEW.jeniskasuspenyakit_id ,
        NEW.pembayaranpelayanan_id ,
        NEW.kelaspelayanan_id ,
        NEW.carabayar_id ,
        NEW.pasienadmisi_id ,
        NEW.golonganumur_id ,
        NEW.rujukan_id ,
        NEW.antrian_id ,
        NEW.karcis_id ,
        NEW.ruangan_id ,
        NEW.no_urutantri ,
        NEW.transportasi ,
        NEW.keadaan_masuk ,
        NEW.status_periksa ,
        NEW.status_pasien ,
        NEW.kunjungan ,
        NEW.alih_status ,
        NEW.by_phone ,
        NEW.kunjungan_rumah ,
        NEW.status_masuk ,
        NEW.umur ,
        NEW.tgl_selesaiperiksa ,
        NEW.keterangan_pendaftaran ,
        NEW.nopendaftaran_aktif ,
        NEW.status_konfirmasi ,
        NEW.tgl_konfirmasi ,
        NEW.tgl_renkontrol ,
        NEW.status_farmasi ,
        NEW.panggil_antrian ,
        NEW.asuransipasien_id ,
        NEW.tgl_akandilayani ,
        NEW.statusdok_rekammedik ,
        NEW.bpjs_id ,
        NEW.status_bayar ,
        NEW.is_aps ,
        NEW.label_gelang ,
        NEW.is_karcis ,
        NEW.tgl_masukperiksa ,
        NEW.status_verifikasi ,
        NEW.is_ranap ,
        NEW.is_skd ,
        NEW.pendaftaranibu_id ,
        NEW.is_skl ,
        NEW.catatan_penatajasa ,
        NEW.is_stopakomodasi ,
        NEW.tgl_stopakomodasi ,
        NEW.additional_data ,
        NEW.created_date ,
        NEW.created_by ,
        NEW.modified_count ,
        NEW.last_modified_date ,
        NEW.last_modified_by ,
        NEW.is_deleted ,
        NEW.is_active ,
        NEW.deleted_date ,
        NEW.deleted_by ,
        v_keterangan
    );

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");



    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201020_041310_oddo_functionrekap_20201020 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201020_041310_oddo_functionrekap_20201020 cannot be reverted.\n";

        return false;
    }
    */
}
