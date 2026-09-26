<?php

use yii\db\Migration;

/**
 * Class m201020_044743_oddo_functionrekap_20201020_2
 */
class m201020_044743_oddo_functionrekap_20201020_2 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"penerimaanobat_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    
        
BEGIN
    IF (NEW.is_verifikasi = 1)
    THEN
        -- INSERT table history penerimaanobat_r
        INSERT INTO penerimaanobat_r (      
        penerimaanobat_id,
        validasipoobat_id,
        no_penerimaan,
        tgl_penerimaan,
        supplier_id,
        no_suratjalan,
        tgl_suratjalan,
        no_faktur,
        diterima_oleh,
        ruanganpenerima_id,
        peg_mengetahui,
        peg_menyetujui,
        upload_berkas,
        catatan_berkas,
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
        is_verifikasi,
        status_rekap
        )VALUES(
        NEW.penerimaanobat_id ,
        NEW.validasipoobat_id ,
        NEW.no_penerimaan ,
        NEW.tgl_penerimaan ,
        NEW.supplier_id ,
        NEW.no_suratjalan ,
        NEW.tgl_suratjalan ,
        NEW.no_faktur ,
        NEW.diterima_oleh ,
        NEW.ruanganpenerima_id ,
        NEW.peg_mengetahui ,
        NEW.peg_menyetujui ,
        NEW.upload_berkas ,
        NEW.catatan_berkas ,
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
        NEW.is_verifikasi ,
            'ACCRUAL'
        );
END IF;
    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"penerimaanobat_r_update\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
    v_keterangan VARCHAR;
BEGIN
    IF(NEW.is_deleted IS TRUE)
    THEN
        v_keterangan := 'ACCRUAL REVERSAL';
        -- INSERT table history penerimaanobat_r REVERSAL
            INSERT INTO penerimaanobat_r (      
            penerimaanobat_id,
            validasipoobat_id,
            no_penerimaan,
            tgl_penerimaan,
            supplier_id,
            no_suratjalan,
            tgl_suratjalan,
            no_faktur,
            diterima_oleh,
            ruanganpenerima_id,
            peg_mengetahui,
            peg_menyetujui,
            upload_berkas,
            catatan_berkas,
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
            is_verifikasi,
            status_rekap
        )VALUES(
            OLD.penerimaanobat_id ,
            OLD.validasipoobat_id ,
            OLD.no_penerimaan ,
            OLD.tgl_penerimaan ,
            OLD.supplier_id ,
            OLD.no_suratjalan ,
            OLD.tgl_suratjalan ,
            OLD.no_faktur ,
            OLD.diterima_oleh ,
            OLD.ruanganpenerima_id ,
            OLD.peg_mengetahui ,
            OLD.peg_menyetujui ,
            OLD.upload_berkas ,
            OLD.catatan_berkas ,
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
            OLD.is_verifikasi ,
            v_keterangan
        );
            ELSE
        v_keterangan := 'ACCRUAL';
        
        -- INSERT table history penerimaanobatdetail_r REVERSAL
            INSERT INTO penerimaanobat_r (      
            penerimaanobat_id,
            validasipoobat_id,
            no_penerimaan,
            tgl_penerimaan,
            supplier_id,
            no_suratjalan,
            tgl_suratjalan,
            no_faktur,
            diterima_oleh,
            ruanganpenerima_id,
            peg_mengetahui,
            peg_menyetujui,
            upload_berkas,
            catatan_berkas,
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
            is_verifikasi,
            status_rekap
        )VALUES(
            OLD.penerimaanobat_id ,
            OLD.validasipoobat_id ,
            OLD.no_penerimaan ,
            OLD.tgl_penerimaan ,
            OLD.supplier_id ,
            OLD.no_suratjalan ,
            OLD.tgl_suratjalan ,
            OLD.no_faktur ,
            OLD.diterima_oleh ,
            OLD.ruanganpenerima_id ,
            OLD.peg_mengetahui ,
            OLD.peg_menyetujui ,
            OLD.upload_berkas ,
            OLD.catatan_berkas ,
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
            OLD.is_verifikasi ,
            'ACCRUAL REVERSAL'
        );
        
    -- INSERT table history penerimaanobat_r
        INSERT INTO penerimaanobat_r (      
            penerimaanobat_id,
            validasipoobat_id,
            no_penerimaan,
            tgl_penerimaan,
            supplier_id,
            no_suratjalan,
            tgl_suratjalan,
            no_faktur,
            diterima_oleh,
            ruanganpenerima_id,
            peg_mengetahui,
            peg_menyetujui,
            upload_berkas,
            catatan_berkas,
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
            is_verifikasi,
            status_rekap
        )VALUES(
            NEW.penerimaanobat_id ,
            NEW.validasipoobat_id ,
            NEW.no_penerimaan ,
            NEW.tgl_penerimaan ,
            NEW.supplier_id ,
            NEW.no_suratjalan ,
            NEW.tgl_suratjalan ,
            NEW.no_faktur ,
            NEW.diterima_oleh ,
            NEW.ruanganpenerima_id ,
            NEW.peg_mengetahui ,
            NEW.peg_menyetujui ,
            NEW.upload_berkas ,
            NEW.catatan_berkas ,
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
            NEW.is_verifikasi ,
            v_keterangan
        );
    END IF;
    
    
    

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"penerimaanobatdetail_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    
        
BEGIN
        -- INSERT table history penerimaanobatdetail_r
        INSERT INTO penerimaanobatdetail_r (        
        penerimaanobatdetail_id,
        penerimaanobat_id,
        validasipoobatdetail_id,
        obatalkes_id,
        qty_po,
        po_balance,
        qty_diterima,
        s_konversiobt_id,
        tgl_kadaluarsa,
        no_batch,
        harga,
        discount,
        discount_rp,
        jumlah,
        keterangan,
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
        status_rekap
        )VALUES(
        NEW.penerimaanobatdetail_id ,
        NEW.penerimaanobat_id ,
        NEW.validasipoobatdetail_id ,
        NEW.obatalkes_id ,
        NEW.qty_po ,
        NEW.po_balance ,
        NEW.qty_diterima ,
        NEW.s_konversiobt_id ,
        NEW.tgl_kadaluarsa ,
        NEW.no_batch ,
        NEW.harga ,
        NEW.discount ,
        NEW.discount_rp ,
        NEW.jumlah ,
        NEW.keterangan ,
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
        CREATE OR REPLACE FUNCTION \"public\".\"penerimaanobatdetail_r_update\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
    v_keterangan VARCHAR;
BEGIN
    IF(NEW.is_deleted IS TRUE)
    THEN
        v_keterangan := 'ACCRUAL REVERSAL';
        -- INSERT table history penerimaanobatdetail_r REVERSAL
            INSERT INTO penerimaanobatdetail_r (        
            penerimaanobatdetail_id,
            penerimaanobat_id,
            validasipoobatdetail_id,
            obatalkes_id,
            qty_po,
            po_balance,
            qty_diterima,
            s_konversiobt_id,
            tgl_kadaluarsa,
            no_batch,
            harga,
            discount,
            discount_rp,
            jumlah,
            keterangan,
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
            status_rekap
        )VALUES(
            OLD.penerimaanobatdetail_id ,
            OLD.penerimaanobat_id ,
            OLD.validasipoobatdetail_id ,
            OLD.obatalkes_id ,
            -1 * OLD.qty_po ,
            -1 * OLD.po_balance ,
            -1 * OLD.qty_diterima ,
            OLD.s_konversiobt_id ,
            OLD.tgl_kadaluarsa ,
            OLD.no_batch ,
            -1 * OLD.harga ,
            -1 * OLD.discount ,
            -1 * OLD.discount_rp ,
            -1 * OLD.jumlah , 
            OLD.keterangan ,
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
        
        -- INSERT table history penerimaanobatdetail_r REVERSAL
            INSERT INTO penerimaanobatdetail_r (        
            penerimaanobatdetail_id,
            penerimaanobat_id,
            validasipoobatdetail_id,
            obatalkes_id,
            qty_po,
            po_balance,
            qty_diterima,
            s_konversiobt_id,
            tgl_kadaluarsa,
            no_batch,
            harga,
            discount,
            discount_rp,
            jumlah,
            keterangan,
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
            status_rekap
        )VALUES(
            OLD.penerimaanobatdetail_id ,
            OLD.penerimaanobat_id ,
            OLD.validasipoobatdetail_id ,
            OLD.obatalkes_id ,
            -1 * OLD.qty_po ,
            -1 * OLD.po_balance ,
            -1 * OLD.qty_diterima ,
            OLD.s_konversiobt_id ,
            OLD.tgl_kadaluarsa ,
            OLD.no_batch ,
            -1 * OLD.harga ,
            -1 * OLD.discount ,
            -1 * OLD.discount_rp ,
            -1 * OLD.jumlah , 
            OLD.keterangan ,
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
        
    -- INSERT table history penerimaanobatdetail_r
        INSERT INTO penerimaanobatdetail_r (        
            penerimaanobatdetail_id,
            penerimaanobat_id,
            validasipoobatdetail_id,
            obatalkes_id,
            qty_po,
            po_balance,
            qty_diterima,
            s_konversiobt_id,
            tgl_kadaluarsa,
            no_batch,
            harga,
            discount,
            discount_rp,
            jumlah,
            keterangan,
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
            status_rekap
        )VALUES(
            NEW.penerimaanobatdetail_id ,
            NEW.penerimaanobat_id ,
            NEW.validasipoobatdetail_id ,
            NEW.obatalkes_id ,
            NEW.qty_po ,
            NEW.po_balance ,
            NEW.qty_diterima ,
            NEW.s_konversiobt_id ,
            NEW.tgl_kadaluarsa ,
            NEW.no_batch ,
            NEW.harga ,
            NEW.discount ,
            NEW.discount_rp ,
            NEW.jumlah ,
            NEW.keterangan ,
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
        CREATE OR REPLACE FUNCTION \"public\".\"penerimaansupp_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    
        
BEGIN
IF (NEW.is_verifikasi IS TRUE)
THEN
        -- INSERT table history penerimaansupp_r
        INSERT INTO penerimaansupp_r (      
        penerimaansupp_id,
        no_penerimaan,
        tgl_penerimaan,
        supplier_id,
        no_faktur,
        peg_mengetahui,
        peg_menyetujui,
        ruanganpenerima_id,
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
        pajak_id,
        payterm_id,
        is_tipe,
        no_suratjalan,
        is_verifikasi,
        tgl_verifikasi,
        status_rekap
        )VALUES(
        NEW.penerimaansupp_id ,
        NEW.no_penerimaan ,
        NEW.tgl_penerimaan ,
        NEW.supplier_id ,
        NEW.no_faktur ,
        NEW.peg_mengetahui ,
        NEW.peg_menyetujui ,
        NEW.ruanganpenerima_id ,
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
        NEW.pajak_id ,
        NEW.payterm_id ,
        NEW.is_tipe ,
        NEW.no_suratjalan ,
        NEW.is_verifikasi ,
        NEW.tgl_verifikasi ,
            'ACCRUAL'
        );
END IF;
    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"penerimaansuppdetail_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    
        
BEGIN
        -- INSERT table history penerimaansuppdetail_r
        INSERT INTO penerimaansuppdetail_r (        
        penerimaansuppdetail_id,
        penerimaansupp_id,
        obatalkes_id,
        tgl_kadaluarsa,
        satuanbesar_id,
        qty_besar,
        satuankecil_id,
        qty_kecil,
        harga_netto,
        ppn,
        diskon,
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
        satuankonversi_id,
        no_batch,
        keterangan,
        barang_id,
        status_rekap
        )VALUES(
        NEW.penerimaansuppdetail_id ,
        NEW.penerimaansupp_id ,
        NEW.obatalkes_id ,
        NEW.tgl_kadaluarsa ,
        NEW.satuanbesar_id ,
        NEW.qty_besar ,
        NEW.satuankecil_id ,
        NEW.qty_kecil ,
        NEW.harga_netto ,
        NEW.ppn ,
        NEW.diskon ,
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
        NEW.satuankonversi_id ,
        NEW.no_batch ,
        NEW.keterangan ,
        NEW.barang_id ,
            'ACCRUAL'
        );

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"penerimaansuppdetail_r_update\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
    v_keterangan VARCHAR;
BEGIN
    IF(NEW.is_deleted IS TRUE)
    THEN
        v_keterangan := 'ACCRUAL REVERSAL';
        -- INSERT table history penerimaansuppdetail_r REVERSAL
            INSERT INTO penerimaansuppdetail_r (        
            penerimaansuppdetail_id,
            penerimaansupp_id,
            obatalkes_id,
            tgl_kadaluarsa,
            satuanbesar_id,
            qty_besar,
            satuankecil_id,
            qty_kecil,
            harga_netto,
            ppn,
            diskon,
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
            satuankonversi_id,
            no_batch,
            keterangan,
            barang_id,
            status_rekap
        )VALUES(
            OLD.penerimaansuppdetail_id ,
            OLD.penerimaansupp_id ,
            OLD.obatalkes_id ,
            OLD.tgl_kadaluarsa ,
            OLD.satuanbesar_id ,
            -1 * OLD.qty_besar ,
            OLD.satuankecil_id ,
            -1 * OLD.qty_kecil ,
            -1 * OLD.harga_netto ,
            -1 * OLD.ppn ,
            -1 * OLD.diskon ,
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
            OLD.satuankonversi_id ,
            OLD.no_batch ,
            OLD.keterangan ,
            OLD.barang_id ,
            v_keterangan
        );
            ELSE
        v_keterangan := 'ACCRUAL';
        
        -- INSERT table history penerimaansuppdetail_r REVERSAL
            INSERT INTO penerimaansuppdetail_r (        
            penerimaansuppdetail_id,
            penerimaansupp_id,
            obatalkes_id,
            tgl_kadaluarsa,
            satuanbesar_id,
            qty_besar,
            satuankecil_id,
            qty_kecil,
            harga_netto,
            ppn,
            diskon,
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
            satuankonversi_id,
            no_batch,
            keterangan,
            barang_id,
            status_rekap
        )VALUES(
            OLD.penerimaansuppdetail_id ,
            OLD.penerimaansupp_id ,
            OLD.obatalkes_id ,
            OLD.tgl_kadaluarsa ,
            OLD.satuanbesar_id ,
            -1 * OLD.qty_besar ,
            OLD.satuankecil_id ,
            -1 * OLD.qty_kecil ,
            -1 * OLD.harga_netto ,
            -1 * OLD.ppn ,
            -1 * OLD.diskon ,
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
            OLD.satuankonversi_id ,
            OLD.no_batch ,
            OLD.keterangan ,
            OLD.barang_id ,
            'ACCRUAL REVERSAL' 
        );
        
    -- INSERT table history penerimaansuppdetail_r
        INSERT INTO penerimaansuppdetail_r (        
            penerimaansuppdetail_id,
            penerimaansupp_id,
            obatalkes_id,
            tgl_kadaluarsa,
            satuanbesar_id,
            qty_besar,
            satuankecil_id,
            qty_kecil,
            harga_netto,
            ppn,
            diskon,
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
            satuankonversi_id,
            no_batch,
            keterangan,
            barang_id,
            status_rekap
        )VALUES(
            NEW.penerimaansuppdetail_id ,
            NEW.penerimaansupp_id ,
            NEW.obatalkes_id ,
            NEW.tgl_kadaluarsa ,
            NEW.satuanbesar_id ,
            NEW.qty_besar ,
            NEW.satuankecil_id ,
            NEW.qty_kecil ,
            NEW.harga_netto ,
            NEW.ppn ,
            NEW.diskon ,
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
            NEW.satuankonversi_id ,
            NEW.no_batch ,
            NEW.keterangan ,
            NEW.barang_id ,
            v_keterangan 
        );
    END IF;

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"pengembalianuangmuka_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    
        
BEGIN
------------------------------> INSERT table rekap pengembalianuangmuka_r <---------------------------------------------
        INSERT INTO pengembalianuangmuka_r (        
            pengembalianuangmuka_id,
            pendaftaran_id,
            tandabuktikeluar_id,
            ruangan_id,
            tgl_pengembalian,
            total_pengembalian,
            biaya_administrasi,
            pembulatan,
            additional_data,
            created_date,
            created_by,
            modified_count,
            last_modified_by,
            is_deleted,
            is_active,
            deleted_date,
            deleted_by,
            keterangan
        )VALUES(
            NEW.pengembalianuangmuka_id,
            NEW.pendaftaran_id,
            NEW.tandabuktikeluar_id,
            NEW.ruangan_id,
            NEW.tgl_pengembalian,
            -1 * NEW.total_pengembalian,
            NEW.biaya_administrasi,
            NEW.pembulatan,
            NEW.additional_data,
            NEW.created_date,
            NEW.created_by,
            NEW.modified_count,
            NEW.last_modified_by,
            NEW.is_deleted,
            NEW.is_active,
            NEW.deleted_date,
            NEW.deleted_by,
            'Deposit Refund'
        );

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"returpenerimaanobat_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN
------------------------------->INSERT table history returpenerimaanobat_r<----------------------------------
        INSERT INTO returpenerimaanobat_r (     
                returpenerimaanobat_id,
                panerimaanobatsupp_id,
                no_returpenerimaanobat,
                tgl_retur,
                pegawairetur_id,
                alasan_retur,
                ruanganretur_id,
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
                NEW.returpenerimaanobat_id,
                NEW.panerimaanobatsupp_id,
                NEW.no_returpenerimaanobat,
                NEW.tgl_retur,
                NEW.pegawairetur_id,
                NEW.alasan_retur,
                NEW.ruanganretur_id,
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
        CREATE OR REPLACE FUNCTION \"public\".\"returpenerimaanobatdetail_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN
------------------------------->INSERT table history returpenerimaanobat_r<----------------------------------
        INSERT INTO returpenerimaanobatdetail_r (       
                returpenerimaanobatdetail_id,
                returpenerimaanobat_id,
                obatalkes_id,
                satuanbesar_id,
                tgl_kadaluarsa,
                qty_retur,
                created_date,
                created_by,
                modified_count,
                last_modified_date,
                last_modified_by,
                is_deleted,
                is_active,
                deleted_date,
                deleted_by,
                penerimaansuppdetail_id,
                penerimaanobat_id,
                qty_input,
                penerimaanobatdetail_id,
                keterangan_rekap
        )VALUES(
                NEW.returpenerimaanobatdetail_id,
                NEW.returpenerimaanobat_id,
                NEW.obatalkes_id,
                NEW.satuanbesar_id,
                NEW.tgl_kadaluarsa,
                NEW.qty_retur,
                NEW.created_date,
                NEW.created_by,
                NEW.modified_count,
                NEW.last_modified_date,
                NEW.last_modified_by,
                NEW.is_deleted,
                NEW.is_active,
                NEW.deleted_date,
                NEW.deleted_by,
                NEW.penerimaansuppdetail_id,
                NEW.penerimaanobat_id,
                NEW.qty_input,
                NEW.penerimaanobatdetail_id,
                'ACCRUAL'
            );

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"returresep_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN
------------------------------->INSERT table history returresep_r<----------------------------------
        INSERT INTO returresep_r (      
            returresep_id,
            ruangan_id,
            penjualanresep_id,
            pendaftaran_id,
            pasien_id,
            pasienadmisi_id,
            tgl_retur,
            no_returresep,
            alasan_retur,
            keterangan_retur,
            pegawaimengetahui_id,
            pegawairetur_id,
            total_retur,
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
            NEW.returresep_id,
            NEW.ruangan_id,
            NEW.penjualanresep_id,
            NEW.pendaftaran_id,
            NEW.pasien_id,
            NEW.pasienadmisi_id,
            NEW.tgl_retur,
            NEW.no_returresep,
            NEW.alasan_retur,
            NEW.keterangan_retur,
            NEW.pegawaimengetahui_id,
            NEW.pegawairetur_id,
            NEW.total_retur,
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
        CREATE OR REPLACE FUNCTION \"public\".\"returresepdetail_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN
------------------------------->INSERT table history returresepdetail_r<----------------------------------
        INSERT INTO returresepdetail_r (        
            returresepdetail_id,
            obatalkespasien_id,
            returresep_id,
            qty_retur,
            hargasatuan,
            kondisibrg,
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
            NEW.returresepdetail_id,
            NEW.obatalkespasien_id,
            NEW.returresep_id,
            NEW.qty_retur,
            NEW.hargasatuan,
            NEW.kondisibrg,
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
        CREATE OR REPLACE FUNCTION \"public\".\"satuanunit_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN
------------------------------->INSERT table history satuanunit_r<----------------------------------
        INSERT INTO satuanunit_r (      
            satuanunit_id,
            satuanunit_nama,
            satuanunit_singkatan,
            satuan_jenis,
            satuanunit_namalain,
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
            NEW.satuanunit_id,
            NEW.satuanunit_nama,
            NEW.satuanunit_singkatan,
            NEW.satuan_jenis,
            NEW.satuanunit_namalain,
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
            'INSERT'
            );

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"satuanunit_r_update\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN
------------------------------->INSERT table history satuanunit_r<----------------------------------
        INSERT INTO satuanunit_r (      
            satuanunit_id,
            satuanunit_nama,
            satuanunit_singkatan,
            satuan_jenis,
            satuanunit_namalain,
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
            NEW.satuanunit_id,
            NEW.satuanunit_nama,
            NEW.satuanunit_singkatan,
            NEW.satuan_jenis,
            NEW.satuanunit_namalain,
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
            'UPDATE'
            );

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"supplier_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN
------------------------------->INSERT table history supplier_r<----------------------------------
        INSERT INTO supplier_r (        
            supplier_id,
            pbf_id,
            supplier_kode,
            supplier_nama,
            supplier_namalain,
            supplier_alamat,
            propinsi_id,
            kabupaten_id,
            no_tlp,
            email,
            no_fax,
            no_npwp,
            no_rekening,
            nama_pemilikrek,
            bank_id,
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
            is_pmi,
            negara_id,
            website,
            nama_pic,
            credit_limit,
            remarks,
            parent_suppliercode,
            keterangan_rekap
        )VALUES(
            NEW.supplier_id,
            NEW.pbf_id,
            NEW.supplier_kode,
            NEW.supplier_nama,
            NEW.supplier_namalain,
            NEW.supplier_alamat,
            NEW.propinsi_id,
            NEW.kabupaten_id,
            NEW.no_tlp,
            NEW.email,
            NEW.no_fax,
            NEW.no_npwp,
            NEW.no_rekening,
            NEW.nama_pemilikrek,
            NEW.bank_id,
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
            NEW.is_pmi,
            NEW.negara_id,
            NEW.website,
            NEW.nama_pic,
            NEW.credit_limit,
            NEW.remarks,
            NEW.parent_suppliercode,
            'INSERT'
            );

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"supplier_r_update\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN
------------------------------->INSERT table history supplier_r<----------------------------------
        INSERT INTO supplier_r (        
            supplier_id,
            pbf_id,
            supplier_kode,
            supplier_nama,
            supplier_namalain,
            supplier_alamat,
            propinsi_id,
            kabupaten_id,
            no_tlp,
            email,
            no_fax,
            no_npwp,
            no_rekening,
            nama_pemilikrek,
            bank_id,
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
            is_pmi,
            negara_id,
            website,
            nama_pic,
            credit_limit,
            remarks,
            parent_suppliercode,
            keterangan_rekap
        )VALUES(
            NEW.supplier_id,
            NEW.pbf_id,
            NEW.supplier_kode,
            NEW.supplier_nama,
            NEW.supplier_namalain,
            NEW.supplier_alamat,
            NEW.propinsi_id,
            NEW.kabupaten_id,
            NEW.no_tlp,
            NEW.email,
            NEW.no_fax,
            NEW.no_npwp,
            NEW.no_rekening,
            NEW.nama_pemilikrek,
            NEW.bank_id,
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
            NEW.is_pmi,
            NEW.negara_id,
            NEW.website,
            NEW.nama_pic,
            NEW.credit_limit,
            NEW.remarks,
            NEW.parent_suppliercode,
            'UPDATE'
            );

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"tindakankomponen_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    
        
BEGIN
        -- INSERT table history tindakankomponen_r
        INSERT INTO tindakankomponen_r (        
        tindakankomponen_id,
        komponentarif_id,
        tindakanpelayanan_id,
        tarif_kompsatuan,
        tarif_tindakankomp,
        tarifcyto_tindakankomp,
        subsidiasuransikomp,
        subsidipemerintahkomp,
        subsidirumahsakitkomp,
        iurbiayakomp,
        pembayaranjasa_id,
        is_jurnal,
        discount_komponen,
        tarifpenyulit_komponen,
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
        NEW.tindakankomponen_id ,
        NEW.komponentarif_id ,
        NEW.tindakanpelayanan_id ,
        NEW.tarif_kompsatuan ,
        NEW.tarif_tindakankomp ,
        NEW.tarifcyto_tindakankomp ,
        NEW.subsidiasuransikomp ,
        NEW.subsidipemerintahkomp ,
        NEW.subsidirumahsakitkomp ,
        NEW.iurbiayakomp ,
        NEW.pembayaranjasa_id ,
        NEW.is_jurnal ,
        NEW.discount_komponen ,
        NEW.tarifpenyulit_komponen ,
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
        CREATE OR REPLACE FUNCTION \"public\".\"tindakankomponen_r_update\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$ 
    
    DECLARE
        v_keterangan VARCHAR;
    BEGIN
        IF(NEW.is_deleted IS TRUE)
        THEN
            v_keterangan := 'DELETE';
            
            -- INSERT table history tindakankomponen_r REVERSAL
                INSERT INTO tindakankomponen_r (        
                tindakankomponen_id,
                komponentarif_id,
                tindakanpelayanan_id,
                tarif_kompsatuan,
                tarif_tindakankomp,
                tarifcyto_tindakankomp,
                subsidiasuransikomp,
                subsidipemerintahkomp,
                subsidirumahsakitkomp,
                iurbiayakomp,
                pembayaranjasa_id,
                is_jurnal,
                discount_komponen,
                tarifpenyulit_komponen,
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
                OLD.tindakankomponen_id ,
                OLD.komponentarif_id ,
                OLD.tindakanpelayanan_id ,
                -1 * OLD.tarif_kompsatuan ,
                -1 * OLD.tarif_tindakankomp ,
                -1 * OLD.tarifcyto_tindakankomp ,
                -1 * OLD.subsidiasuransikomp ,
                -1 * OLD.subsidipemerintahkomp ,
                -1 * OLD.subsidirumahsakitkomp ,
                -1 * OLD.iurbiayakomp ,
                OLD.pembayaranjasa_id ,
                OLD.is_jurnal ,
                -1 * OLD.discount_komponen ,
                -1 * OLD.tarifpenyulit_komponen ,
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
            v_keterangan := 'UPDATE';
            
            -- INSERT table history tindakankomponen_r REVERSAL
                INSERT INTO tindakankomponen_r (        
                tindakankomponen_id,
                komponentarif_id,
                tindakanpelayanan_id,
                tarif_kompsatuan,
                tarif_tindakankomp,
                tarifcyto_tindakankomp,
                subsidiasuransikomp,
                subsidipemerintahkomp,
                subsidirumahsakitkomp,
                iurbiayakomp,
                pembayaranjasa_id,
                is_jurnal,
                discount_komponen,
                tarifpenyulit_komponen,
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
                OLD.tindakankomponen_id ,
                OLD.komponentarif_id ,
                OLD.tindakanpelayanan_id ,
                -1 * OLD.tarif_kompsatuan ,
                -1 * OLD.tarif_tindakankomp ,
                -1 * OLD.tarifcyto_tindakankomp ,
                -1 * OLD.subsidiasuransikomp ,
                -1 * OLD.subsidipemerintahkomp ,
                -1 * OLD.subsidirumahsakitkomp ,
                -1 * OLD.iurbiayakomp ,
                OLD.pembayaranjasa_id ,
                OLD.is_jurnal ,
                -1 * OLD.discount_komponen ,
                -1 * OLD.tarifpenyulit_komponen ,
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
        
    -- INSERT table history tindakankomponen_r
    INSERT INTO tindakankomponen_r (        
        tindakankomponen_id,
        komponentarif_id,
        tindakanpelayanan_id,
        tarif_kompsatuan,
        tarif_tindakankomp,
        tarifcyto_tindakankomp,
        subsidiasuransikomp,
        subsidipemerintahkomp,
        subsidirumahsakitkomp,
        iurbiayakomp,
        pembayaranjasa_id,
        is_jurnal,
        discount_komponen,
        tarifpenyulit_komponen,
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
        NEW.tindakankomponen_id ,
        NEW.komponentarif_id ,
        NEW.tindakanpelayanan_id ,
        NEW.tarif_kompsatuan ,
        NEW.tarif_tindakankomp ,
        NEW.tarifcyto_tindakankomp ,
        NEW.subsidiasuransikomp ,
        NEW.subsidipemerintahkomp ,
        NEW.subsidirumahsakitkomp ,
        NEW.iurbiayakomp ,
        NEW.pembayaranjasa_id ,
        NEW.is_jurnal ,
        NEW.discount_komponen ,
        NEW.tarifpenyulit_komponen ,
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
        CREATE OR REPLACE FUNCTION \"public\".\"tindakanpelayanan_r_update\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE
    v_keterangan VARCHAR;
BEGIN
    IF((NEW.is_deleted IS TRUE OR NEW.tindakansudahbayar_id IS NOT NULL) AND OLD.is_deleted IS FALSE)
        THEN
            v_keterangan := 'ACCRUAL REVERSAL';
        --> INSERT table history tindakanpelayanan_r menjadi ACCRUAL REVERSAL(-)
            INSERT INTO tindakanpelayanan_r (       
            tindakanpelayanan_id ,
            shift_id ,
            kelaspelayanan_id ,
            kelastanggungan_id ,
            pasien_id ,
            rencanaoperasi_id ,
            instalasi_id ,
            daftartindakan_id ,
            alatmedis_id ,
            tipepaket_id ,
            tindakansudahbayar_id ,
            carabayar_id ,
            pendaftaran_id ,
            hasilpemeriksaanrad_id ,
            jeniskasuspenyakit_id ,
            hasilpemeriksaanrm_id ,
            ruangan_id ,
            konsulpoli_id ,
            pasienmasukpenunjang_id ,
            hasilpemeriksaanlabdetail_id ,
            penjamin_id ,
            pasienadmisi_id ,
            verifikasitagihan_id ,
            jurnalrekening_id ,
            instruksitindakan_id ,
            tgl_tindakan ,
            tarif_rsakomodasi ,
            tarif_medis ,
            tarif_paramedis ,
            tarif_bhp ,
            tarif_satuan ,
            tarif_tindakan ,
            tarifcyto_tindakan ,
            satuan_tindakan ,
            qty_tindakan ,
            cyto_tindakan ,
            dokterpenanggungjawab_id ,
            dokterpelaksana_id ,
            dokteranastesi_id ,
            dokterdelegasi_id ,
            bidan1_id ,
            bidan2_id ,
            perawat1_id ,
            perawat2_id ,
            discount_tindakan ,
            pembebasan_tindakan ,
            subsidiasuransi_tindakan ,
            subsidipemerintah_tindakan ,
            subsisidirumahsakit_tindakan ,
            uangditerima_tindakan ,
            keterangantindakan ,
            pembulatan ,
            implementasi_id ,
            is_dilakukan ,
            pemakaianambulan_id ,
            is_penatajasa ,
            additional_riwayat ,
            is_valid ,
            kamarruangan_id ,
            kamartempattidur_id ,
            penyulit_tindakan ,
            tarifpenyulit_tindakan ,
            additional_data ,
            created_date ,
            created_by ,
            modified_count,
            last_modified_date,
            last_modified_by,
            is_deleted,
            is_active,
            deleted_date,
            deleted_by,
            keterangan,
            no_tindakanpelayanan
        )VALUES(
            OLD.tindakanpelayanan_id ,
            OLD.shift_id ,
            OLD.kelaspelayanan_id ,
            OLD.kelastanggungan_id ,
            OLD.pasien_id ,
            OLD.rencanaoperasi_id ,
            OLD.instalasi_id ,
            OLD.daftartindakan_id ,
            OLD.alatmedis_id ,
            OLD.tipepaket_id ,
            OLD.tindakansudahbayar_id ,
            OLD.carabayar_id ,
            OLD.pendaftaran_id ,
            OLD.hasilpemeriksaanrad_id ,
            OLD.jeniskasuspenyakit_id ,
            OLD.hasilpemeriksaanrm_id ,
            OLD.ruangan_id ,
            OLD.konsulpoli_id ,
            OLD.pasienmasukpenunjang_id ,
            OLD.hasilpemeriksaanlabdetail_id ,
            OLD.penjamin_id ,
            OLD.pasienadmisi_id ,
            OLD.verifikasitagihan_id ,
            OLD.jurnalrekening_id ,
            OLD.instruksitindakan_id ,
            OLD.tgl_tindakan ,
            -1 * OLD.tarif_rsakomodasi ,
            -1 * OLD.tarif_medis ,
            -1 * OLD.tarif_paramedis ,
            -1 * OLD.tarif_bhp ,
            -1 * OLD.tarif_satuan ,
            -1 * OLD.tarif_tindakan ,
            -1 * OLD.tarifcyto_tindakan ,
            OLD.satuan_tindakan ,
            OLD.qty_tindakan ,
            OLD.cyto_tindakan ,
            OLD.dokterpenanggungjawab_id ,
            OLD.dokterpelaksana_id ,
            OLD.dokteranastesi_id ,
            OLD.dokterdelegasi_id ,
            OLD.bidan1_id ,
            OLD.bidan2_id ,
            OLD.perawat1_id ,
            OLD.perawat2_id ,
            -1 * OLD.discount_tindakan ,
            -1 * OLD.pembebasan_tindakan ,
            -1 * OLD.subsidiasuransi_tindakan ,
            -1 * OLD.subsidipemerintah_tindakan ,
            -1 * OLD.subsisidirumahsakit_tindakan ,
            -1 * OLD.uangditerima_tindakan ,
            OLD.keterangantindakan ,
            -1 * OLD.pembulatan ,
            OLD.implementasi_id ,
            OLD.is_dilakukan ,
            OLD.pemakaianambulan_id ,
            OLD.is_penatajasa ,
            OLD.additional_riwayat ,
            OLD.is_valid ,
            OLD.kamarruangan_id ,
            OLD.kamartempattidur_id ,
            OLD.penyulit_tindakan ,
            -1 * OLD.tarifpenyulit_tindakan ,
            OLD.additional_data ,
            OLD.created_date ,
            OLD.created_by ,
            OLD.modified_count,
            OLD.last_modified_date,
            OLD.last_modified_by,
            OLD.is_deleted,
            OLD.is_active,
            OLD.deleted_date,
            OLD.deleted_by,
            v_keterangan,
            OLD.no_tindakanpelayanan
        );
            RETURN NEW;
            END IF;
            
            IF (old.tindakansudahbayar_id is null AND OLD.is_deleted= FALSE)
                THEN    
            -- INSERT table history tindakanpelayanan_r menjadi ACCRUAL REVERSAL(-) setelah BILLING CANCEL
                INSERT INTO tindakanpelayanan_r (       
                    tindakanpelayanan_id ,
                    shift_id ,
                    kelaspelayanan_id ,
                    kelastanggungan_id ,
                    pasien_id ,
                    rencanaoperasi_id ,
                    instalasi_id ,
                    daftartindakan_id ,
                    alatmedis_id ,
                    tipepaket_id ,
                    tindakansudahbayar_id ,
                    carabayar_id ,
                    pendaftaran_id ,
                    hasilpemeriksaanrad_id ,
                    jeniskasuspenyakit_id ,
                    hasilpemeriksaanrm_id ,
                    ruangan_id ,
                    konsulpoli_id ,
                    pasienmasukpenunjang_id ,
                    hasilpemeriksaanlabdetail_id ,
                    penjamin_id ,
                    pasienadmisi_id ,
                    verifikasitagihan_id ,
                    jurnalrekening_id ,
                    instruksitindakan_id ,
                    tgl_tindakan ,
                    tarif_rsakomodasi ,
                    tarif_medis ,
                    tarif_paramedis ,
                    tarif_bhp ,
                    tarif_satuan ,
                    tarif_tindakan ,
                    tarifcyto_tindakan ,
                    satuan_tindakan ,
                    qty_tindakan ,
                    cyto_tindakan ,
                    dokterpenanggungjawab_id ,
                    dokterpelaksana_id ,
                    dokteranastesi_id ,
                    dokterdelegasi_id ,
                    bidan1_id ,
                    bidan2_id ,
                    perawat1_id ,
                    perawat2_id ,
                    discount_tindakan ,
                    pembebasan_tindakan ,
                    subsidiasuransi_tindakan ,
                    subsidipemerintah_tindakan ,
                    subsisidirumahsakit_tindakan ,
                    uangditerima_tindakan ,
                    keterangantindakan ,
                    pembulatan ,
                    implementasi_id ,
                    is_dilakukan ,
                    pemakaianambulan_id ,
                    is_penatajasa ,
                    additional_riwayat ,
                    is_valid ,
                    kamarruangan_id ,
                    kamartempattidur_id ,
                    penyulit_tindakan ,
                    tarifpenyulit_tindakan ,
                    additional_data ,
                    created_date ,
                    created_by ,
                    modified_count,
                    last_modified_date,
                    last_modified_by,
                    is_deleted,
                    is_active,
                    deleted_date,
                    deleted_by,
                    keterangan,
                    no_tindakanpelayanan
                )VALUES(
                    OLD.tindakanpelayanan_id ,
                    OLD.shift_id ,
                    OLD.kelaspelayanan_id ,
                    OLD.kelastanggungan_id ,
                    OLD.pasien_id ,
                    OLD.rencanaoperasi_id ,
                    OLD.instalasi_id ,
                    OLD.daftartindakan_id ,
                    OLD.alatmedis_id ,
                    OLD.tipepaket_id ,
                    OLD.tindakansudahbayar_id ,
                    OLD.carabayar_id ,
                    OLD.pendaftaran_id ,
                    OLD.hasilpemeriksaanrad_id ,
                    OLD.jeniskasuspenyakit_id ,
                    OLD.hasilpemeriksaanrm_id ,
                    OLD.ruangan_id ,
                    OLD.konsulpoli_id ,
                    OLD.pasienmasukpenunjang_id ,
                    OLD.hasilpemeriksaanlabdetail_id ,
                    OLD.penjamin_id ,
                    OLD.pasienadmisi_id ,
                    OLD.verifikasitagihan_id ,
                    OLD.jurnalrekening_id ,
                    OLD.instruksitindakan_id ,
                    OLD.tgl_tindakan ,
                    -1 * OLD.tarif_rsakomodasi ,
                    -1 * OLD.tarif_medis ,
                    -1 * OLD.tarif_paramedis ,
                    -1 * OLD.tarif_bhp ,
                    -1 * OLD.tarif_satuan ,
                    -1 * OLD.tarif_tindakan ,
                    -1 * OLD.tarifcyto_tindakan ,
                    OLD.satuan_tindakan ,
                    OLD.qty_tindakan ,
                    OLD.cyto_tindakan ,
                    OLD.dokterpenanggungjawab_id ,
                    OLD.dokterpelaksana_id ,
                    OLD.dokteranastesi_id ,
                    OLD.dokterdelegasi_id ,
                    OLD.bidan1_id ,
                    OLD.bidan2_id ,
                    OLD.perawat1_id ,
                    OLD.perawat2_id ,
                    -1 * OLD.discount_tindakan ,
                    -1 * OLD.pembebasan_tindakan ,
                    -1 * OLD.subsidiasuransi_tindakan ,
                    -1 * OLD.subsidipemerintah_tindakan ,
                    -1 * OLD.subsisidirumahsakit_tindakan ,
                    -1 * OLD.uangditerima_tindakan ,
                    OLD.keterangantindakan ,
                    -1 * OLD.pembulatan ,
                    OLD.implementasi_id ,
                    OLD.is_dilakukan ,
                    OLD.pemakaianambulan_id ,
                    OLD.is_penatajasa ,
                    OLD.additional_riwayat ,
                    OLD.is_valid ,
                    OLD.kamarruangan_id ,
                    OLD.kamartempattidur_id ,
                    OLD.penyulit_tindakan ,
                    -1 * OLD.tarifpenyulit_tindakan ,
                    OLD.additional_data ,
                    OLD.created_date ,
                    OLD.created_by ,
                    OLD.modified_count,
                    OLD.last_modified_date,
                    OLD.last_modified_by,
                    OLD.is_deleted,
                    OLD.is_active,
                    OLD.deleted_date,
                    OLD.deleted_by,
                    'ACCRUAL REVERSAL',
                    OLD.no_tindakanpelayanan
                );
        
            INSERT INTO tindakanpelayanan_r (       
                        tindakanpelayanan_id ,
                        shift_id ,
                        kelaspelayanan_id ,
                        kelastanggungan_id ,
                        pasien_id ,
                        rencanaoperasi_id ,
                        instalasi_id ,
                        daftartindakan_id ,
                        alatmedis_id ,
                        tipepaket_id ,
                        tindakansudahbayar_id ,
                        carabayar_id ,
                        pendaftaran_id ,
                        hasilpemeriksaanrad_id ,
                        jeniskasuspenyakit_id ,
                        hasilpemeriksaanrm_id ,
                        ruangan_id ,
                        konsulpoli_id ,
                        pasienmasukpenunjang_id ,
                        hasilpemeriksaanlabdetail_id ,
                        penjamin_id ,
                        pasienadmisi_id ,
                        verifikasitagihan_id ,
                        jurnalrekening_id ,
                        instruksitindakan_id ,
                        tgl_tindakan ,
                        tarif_rsakomodasi ,
                        tarif_medis ,
                        tarif_paramedis ,
                        tarif_bhp ,
                        tarif_satuan ,
                        tarif_tindakan ,
                        tarifcyto_tindakan ,
                        satuan_tindakan ,
                        qty_tindakan ,
                        cyto_tindakan ,
                        dokterpenanggungjawab_id ,
                        dokterpelaksana_id ,
                        dokteranastesi_id ,
                        dokterdelegasi_id ,
                        bidan1_id ,
                        bidan2_id ,
                        perawat1_id ,
                        perawat2_id ,
                        discount_tindakan ,
                        pembebasan_tindakan ,
                        subsidiasuransi_tindakan ,
                        subsidipemerintah_tindakan ,
                        subsisidirumahsakit_tindakan ,
                        uangditerima_tindakan ,
                        keterangantindakan ,
                        pembulatan ,
                        implementasi_id ,
                        is_dilakukan ,
                        pemakaianambulan_id ,
                        is_penatajasa ,
                        additional_riwayat ,
                        is_valid ,
                        kamarruangan_id ,
                        kamartempattidur_id ,
                        penyulit_tindakan ,
                        tarifpenyulit_tindakan ,
                        additional_data ,
                        created_date ,
                        created_by ,
                        modified_count,
                        last_modified_date,
                        last_modified_by,
                        is_deleted,
                        is_active,
                        deleted_date,
                        deleted_by,
                        keterangan,
                        no_tindakanpelayanan
                    )VALUES(
                        OLD.tindakanpelayanan_id ,
                        OLD.shift_id ,
                        OLD.kelaspelayanan_id ,
                        OLD.kelastanggungan_id ,
                        OLD.pasien_id ,
                        OLD.rencanaoperasi_id ,
                        OLD.instalasi_id ,
                        OLD.daftartindakan_id ,
                        OLD.alatmedis_id ,
                        OLD.tipepaket_id ,
                        OLD.tindakansudahbayar_id ,
                        OLD.carabayar_id ,
                        OLD.pendaftaran_id ,
                        OLD.hasilpemeriksaanrad_id ,
                        OLD.jeniskasuspenyakit_id ,
                        OLD.hasilpemeriksaanrm_id ,
                        OLD.ruangan_id ,
                        OLD.konsulpoli_id ,
                        OLD.pasienmasukpenunjang_id ,
                        OLD.hasilpemeriksaanlabdetail_id ,
                        OLD.penjamin_id ,
                        OLD.pasienadmisi_id ,
                        OLD.verifikasitagihan_id ,
                        OLD.jurnalrekening_id ,
                        OLD.instruksitindakan_id ,
                        OLD.tgl_tindakan ,
                        OLD.tarif_rsakomodasi ,
                        OLD.tarif_medis ,
                        OLD.tarif_paramedis ,
                        OLD.tarif_bhp ,
                        OLD.tarif_satuan ,
                        OLD.tarif_tindakan ,
                        OLD.tarifcyto_tindakan ,
                        OLD.satuan_tindakan ,
                        OLD.qty_tindakan ,
                        OLD.cyto_tindakan ,
                        OLD.dokterpenanggungjawab_id ,
                        OLD.dokterpelaksana_id ,
                        OLD.dokteranastesi_id ,
                        OLD.dokterdelegasi_id ,
                        OLD.bidan1_id ,
                        OLD.bidan2_id ,
                        OLD.perawat1_id ,
                        OLD.perawat2_id ,
                        OLD.discount_tindakan ,
                        OLD.pembebasan_tindakan ,
                        OLD.subsidiasuransi_tindakan ,
                        OLD.subsidipemerintah_tindakan ,
                        OLD.subsisidirumahsakit_tindakan ,
                        OLD.uangditerima_tindakan ,
                        OLD.keterangantindakan ,
                        OLD.pembulatan ,
                        OLD.implementasi_id ,
                        OLD.is_dilakukan ,
                        OLD.pemakaianambulan_id ,
                        OLD.is_penatajasa ,
                        OLD.additional_riwayat ,
                        OLD.is_valid ,
                        OLD.kamarruangan_id ,
                        OLD.kamartempattidur_id ,
                        OLD.penyulit_tindakan ,
                        OLD.tarifpenyulit_tindakan ,
                        OLD.additional_data ,
                        OLD.created_date ,
                        OLD.created_by ,
                        OLD.modified_count,
                        OLD.last_modified_date,
                        OLD.last_modified_by,
                        OLD.is_deleted,
                        OLD.is_active,
                        OLD.deleted_date,
                        OLD.deleted_by,
                        'ACCRUAL',
                        OLD.no_tindakanpelayanan
                    );

            
    END IF;
    
    
    

    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"tindakansudahbayar_t_cancel\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$ 
    
DECLARE
        v_keterangan VARCHAR;
    BEGIN
        IF(NEW.is_deleted IS TRUE)
        THEN
            -- INSERT table history tindakanpelayanan_r menjadi ACCRUAL(+), jika is_deleted=TRUE
            INSERT INTO tindakanpelayanan_r (       
                tindakanpelayanan_id ,
                shift_id ,
                kelaspelayanan_id ,
                kelastanggungan_id ,
                pasien_id ,
                rencanaoperasi_id ,
                instalasi_id ,
                daftartindakan_id ,
                alatmedis_id ,
                tipepaket_id ,
                tindakansudahbayar_id ,
                carabayar_id ,
                pendaftaran_id ,
                hasilpemeriksaanrad_id ,
                jeniskasuspenyakit_id ,
                hasilpemeriksaanrm_id ,
                ruangan_id ,
                konsulpoli_id ,
                pasienmasukpenunjang_id ,
                hasilpemeriksaanlabdetail_id ,
                penjamin_id ,
                pasienadmisi_id ,
                verifikasitagihan_id ,
                jurnalrekening_id ,
                instruksitindakan_id ,
                tgl_tindakan ,
                tarif_rsakomodasi ,
                tarif_medis ,
                tarif_paramedis ,
                tarif_bhp ,
                tarif_satuan ,
                tarif_tindakan ,
                tarifcyto_tindakan ,
                satuan_tindakan ,
                qty_tindakan ,
                cyto_tindakan ,
                dokterpenanggungjawab_id ,
                dokterpelaksana_id ,
                dokteranastesi_id ,
                dokterdelegasi_id ,
                bidan1_id ,
                bidan2_id ,
                perawat1_id ,
                perawat2_id ,
                discount_tindakan ,
                pembebasan_tindakan ,
                subsidiasuransi_tindakan ,
                subsidipemerintah_tindakan ,
                subsisidirumahsakit_tindakan ,
                uangditerima_tindakan ,
                keterangantindakan ,
                pembulatan ,
                implementasi_id ,
                is_dilakukan ,
                pemakaianambulan_id ,
                is_penatajasa ,
                additional_riwayat ,
                is_valid ,
                kamarruangan_id ,
                kamartempattidur_id ,
                penyulit_tindakan ,
                tarifpenyulit_tindakan ,
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
                no_tindakanpelayanan
                )
                SELECT
                tindakanpelayanan_id ,
                shift_id ,
                kelaspelayanan_id ,
                kelastanggungan_id ,
                pasien_id ,
                rencanaoperasi_id ,
                instalasi_id ,
                daftartindakan_id ,
                alatmedis_id ,
                tipepaket_id ,
                NEW.tindakansudahbayar_id ,
                carabayar_id ,
                pendaftaran_id ,
                hasilpemeriksaanrad_id ,
                jeniskasuspenyakit_id ,
                hasilpemeriksaanrm_id ,
                ruangan_id ,
                konsulpoli_id ,
                pasienmasukpenunjang_id ,
                hasilpemeriksaanlabdetail_id ,
                penjamin_id ,
                pasienadmisi_id ,
                verifikasitagihan_id ,
                jurnalrekening_id ,
                instruksitindakan_id ,
                tgl_tindakan ,
                tarif_rsakomodasi ,
                tarif_medis ,
                tarif_paramedis ,
                tarif_bhp ,
                tarif_satuan ,
                tarif_tindakan ,
                tarifcyto_tindakan ,
                satuan_tindakan ,
                qty_tindakan ,
                cyto_tindakan ,
                dokterpenanggungjawab_id ,
                dokterpelaksana_id ,
                dokteranastesi_id ,
                dokterdelegasi_id ,
                bidan1_id ,
                bidan2_id ,
                perawat1_id ,
                perawat2_id ,
                discount_tindakan ,
                pembebasan_tindakan ,
                subsidiasuransi_tindakan ,
                subsidipemerintah_tindakan ,
                subsisidirumahsakit_tindakan ,
                uangditerima_tindakan ,
                keterangantindakan ,
                pembulatan ,
                implementasi_id ,
                is_dilakukan ,
                pemakaianambulan_id ,
                is_penatajasa ,
                additional_riwayat ,
                is_valid ,
                kamarruangan_id ,
                kamartempattidur_id ,
                penyulit_tindakan ,
                tarifpenyulit_tindakan ,
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
                'ACCRUAL',
                no_tindakanpelayanan
                FROM tindakanpelayanan_t
            WHERE tindakanpelayanan_id = NEW.tindakanpelayanan_id;
        
        
            -- INSERT table history tindakanpelayanan_r menjadi BILLING CANCEL(-), jika is_deleted=TRUE
                INSERT INTO tindakanpelayanan_r (       
                    tindakanpelayanan_id ,
                    shift_id ,
                    kelaspelayanan_id ,
                    kelastanggungan_id ,
                    pasien_id ,
                    rencanaoperasi_id ,
                    instalasi_id ,
                    daftartindakan_id ,
                    alatmedis_id ,
                    tipepaket_id ,
                    tindakansudahbayar_id ,
                    carabayar_id ,
                    pendaftaran_id ,
                    hasilpemeriksaanrad_id ,
                    jeniskasuspenyakit_id ,
                    hasilpemeriksaanrm_id ,
                    ruangan_id ,
                    konsulpoli_id ,
                    pasienmasukpenunjang_id ,
                    hasilpemeriksaanlabdetail_id ,
                    penjamin_id ,
                    pasienadmisi_id ,
                    verifikasitagihan_id ,
                    jurnalrekening_id ,
                    instruksitindakan_id ,
                    tgl_tindakan ,
                    tarif_rsakomodasi ,
                    tarif_medis ,
                    tarif_paramedis ,
                    tarif_bhp ,
                    tarif_satuan ,
                    tarif_tindakan ,
                    tarifcyto_tindakan ,
                    satuan_tindakan ,
                    qty_tindakan ,
                    cyto_tindakan ,
                    dokterpenanggungjawab_id ,
                    dokterpelaksana_id ,
                    dokteranastesi_id ,
                    dokterdelegasi_id ,
                    bidan1_id ,
                    bidan2_id ,
                    perawat1_id ,
                    perawat2_id ,
                    discount_tindakan ,
                    pembebasan_tindakan ,
                    subsidiasuransi_tindakan ,
                    subsidipemerintah_tindakan ,
                    subsisidirumahsakit_tindakan ,
                    uangditerima_tindakan ,
                    keterangantindakan ,
                    pembulatan ,
                    implementasi_id ,
                    is_dilakukan ,
                    pemakaianambulan_id ,
                    is_penatajasa ,
                    additional_riwayat ,
                    is_valid ,
                    kamarruangan_id ,
                    kamartempattidur_id ,
                    penyulit_tindakan ,
                    tarifpenyulit_tindakan ,
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
                    no_tindakanpelayanan
                    )
                    SELECT
                    tindakanpelayanan_id ,
                    shift_id ,
                    kelaspelayanan_id ,
                    kelastanggungan_id ,
                    pasien_id ,
                    rencanaoperasi_id ,
                    instalasi_id ,
                    daftartindakan_id ,
                    alatmedis_id ,
                    tipepaket_id ,
                    NEW.tindakansudahbayar_id ,
                    carabayar_id ,
                    pendaftaran_id ,
                    hasilpemeriksaanrad_id ,
                    jeniskasuspenyakit_id ,
                    hasilpemeriksaanrm_id ,
                    ruangan_id ,
                    konsulpoli_id ,
                    pasienmasukpenunjang_id ,
                    hasilpemeriksaanlabdetail_id ,
                    penjamin_id ,
                    pasienadmisi_id ,
                    verifikasitagihan_id ,
                    jurnalrekening_id ,
                    instruksitindakan_id ,
                    tgl_tindakan ,
                    -1 * tarif_rsakomodasi ,
                    -1 * tarif_medis ,
                    -1 * tarif_paramedis ,
                    -1 * tarif_bhp ,
                    -1 * tarif_satuan ,
                    -1 * tarif_tindakan ,
                    -1 * tarifcyto_tindakan ,
                    satuan_tindakan ,
                    qty_tindakan ,
                    cyto_tindakan ,
                    dokterpenanggungjawab_id ,
                    dokterpelaksana_id ,
                    dokteranastesi_id ,
                    dokterdelegasi_id ,
                    bidan1_id ,
                    bidan2_id ,
                    perawat1_id ,
                    perawat2_id ,
                    discount_tindakan ,
                    pembebasan_tindakan ,
                    subsidiasuransi_tindakan ,
                    subsidipemerintah_tindakan ,
                    subsisidirumahsakit_tindakan ,
                    uangditerima_tindakan ,
                    keterangantindakan ,
                    pembulatan ,
                    implementasi_id ,
                    is_dilakukan ,
                    pemakaianambulan_id ,
                    is_penatajasa ,
                    additional_riwayat ,
                    is_valid ,
                    kamarruangan_id ,
                    kamartempattidur_id ,
                    penyulit_tindakan ,
                    tarifpenyulit_tindakan ,
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
                    'BILLING CANCEL',
                    no_tindakanpelayanan
                    FROM tindakanpelayanan_t
                WHERE tindakanpelayanan_id = NEW.tindakanpelayanan_id;
                
    END IF;
    
    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"tindakansudahbayar_t_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

BEGIN
        -- INSERT table history tindakanpelayanan_r BILLING(+)
        INSERT INTO tindakanpelayanan_r (       
            tindakanpelayanan_id ,
            shift_id ,
            kelaspelayanan_id ,
            kelastanggungan_id ,
            pasien_id ,
            rencanaoperasi_id ,
            instalasi_id ,
            daftartindakan_id ,
            alatmedis_id ,
            tipepaket_id ,
            tindakansudahbayar_id ,
            carabayar_id ,
            pendaftaran_id ,
            hasilpemeriksaanrad_id ,
            jeniskasuspenyakit_id ,
            hasilpemeriksaanrm_id ,
            ruangan_id ,
            konsulpoli_id ,
            pasienmasukpenunjang_id ,
            hasilpemeriksaanlabdetail_id ,
            penjamin_id ,
            pasienadmisi_id ,
            verifikasitagihan_id ,
            jurnalrekening_id ,
            instruksitindakan_id ,
            tgl_tindakan ,
            tarif_rsakomodasi ,
            tarif_medis ,
            tarif_paramedis ,
            tarif_bhp ,
            tarif_satuan ,
            tarif_tindakan ,
            tarifcyto_tindakan ,
            satuan_tindakan ,
            qty_tindakan ,
            cyto_tindakan ,
            dokterpenanggungjawab_id ,
            dokterpelaksana_id ,
            dokteranastesi_id ,
            dokterdelegasi_id ,
            bidan1_id ,
            bidan2_id ,
            perawat1_id ,
            perawat2_id ,
            discount_tindakan ,
            pembebasan_tindakan ,
            subsidiasuransi_tindakan ,
            subsidipemerintah_tindakan ,
            subsisidirumahsakit_tindakan ,
            uangditerima_tindakan ,
            keterangantindakan ,
            pembulatan ,
            implementasi_id ,
            is_dilakukan ,
            pemakaianambulan_id ,
            is_penatajasa ,
            additional_riwayat ,
            is_valid ,
            kamarruangan_id ,
            kamartempattidur_id ,
            penyulit_tindakan ,
            tarifpenyulit_tindakan ,
            additional_data ,
            created_date ,
            created_by ,
            keterangan,
            no_tindakanpelayanan,
            tarif_dijamin,
            tarif_dibayarkan
        )
        SELECT  
            tindakanpelayanan_id ,
            shift_id ,
            kelaspelayanan_id ,
            kelastanggungan_id ,
            pasien_id ,
            rencanaoperasi_id ,
            instalasi_id ,
            daftartindakan_id ,
            alatmedis_id ,
            tipepaket_id ,
            NEW.tindakansudahbayar_id,
            carabayar_id ,
            pendaftaran_id ,
            hasilpemeriksaanrad_id ,
            jeniskasuspenyakit_id ,
            hasilpemeriksaanrm_id ,
            ruangan_id ,
            konsulpoli_id ,
            pasienmasukpenunjang_id ,
            hasilpemeriksaanlabdetail_id ,
            penjamin_id ,
            pasienadmisi_id ,
            verifikasitagihan_id ,
            jurnalrekening_id ,
            instruksitindakan_id ,
            tgl_tindakan ,
            tarif_rsakomodasi ,
            tarif_medis ,
            tarif_paramedis ,
            tarif_bhp ,
            tarif_satuan ,
            tarif_tindakan ,
            tarifcyto_tindakan ,
            satuan_tindakan ,
            qty_tindakan ,
            cyto_tindakan ,
            dokterpenanggungjawab_id ,
            dokterpelaksana_id ,
            dokteranastesi_id ,
            dokterdelegasi_id ,
            bidan1_id ,
            bidan2_id ,
            perawat1_id ,
            perawat2_id ,
            discount_tindakan ,
            pembebasan_tindakan ,
            subsidiasuransi_tindakan ,
            subsidipemerintah_tindakan ,
            subsisidirumahsakit_tindakan ,
            uangditerima_tindakan ,
            keterangantindakan ,
            pembulatan ,
            implementasi_id ,
            is_dilakukan ,
            pemakaianambulan_id ,
            is_penatajasa ,
            additional_riwayat ,
            is_valid ,
            kamarruangan_id ,
            kamartempattidur_id ,
            penyulit_tindakan ,
            tarifpenyulit_tindakan ,
            additional_data ,
            created_date ,
            created_by ,
            'BILLING',
            no_tindakanpelayanan,
            tarif_dijamin,
            tarif_dibayarkan
    FROM tindakanpelayanan_t
    WHERE tindakanpelayanan_id = NEW.tindakanpelayanan_id;
    
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
        echo "m201020_044743_oddo_functionrekap_20201020_2 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201020_044743_oddo_functionrekap_20201020_2 cannot be reverted.\n";

        return false;
    }
    */
}
