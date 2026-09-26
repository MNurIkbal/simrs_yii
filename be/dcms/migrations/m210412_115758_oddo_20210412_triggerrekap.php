<?php

use yii\db\Migration;

/**
 * Class m210412_115758_oddo_20210412_triggerrekap
 */
class m210412_115758_oddo_20210412_triggerrekap extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"adjusmenbarangkeluar_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN
------------------------------->INSERT table history adjusmenbarangkeluar_r<----------------------------------
        INSERT INTO adjusmenbarangkeluar_r (        
                adjusmenbarangkeluar_id,
                adjusmenbarang_id,
                barang_id,
                qty,
                satuankecil_id,
                alasan,
                satuanbesar_id,
                qty_konversi,
                satuankonversibrg_id,               
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
                keterangan_rekap
        )VALUES(
                NEW.adjusmenbarangkeluar_id,
                NEW.adjusmenbarang_id,
                NEW.barang_id,
                NEW.qty,
                NEW.satuankecil_id,
                NEW.alasan,
                NEW.satuanbesar_id,
                NEW.qty_konversi,
                NEW.satuankonversibrg_id,               
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
                'ACCRUAL'
            );

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

    $this->execute('ALTER FUNCTION "public"."adjusmenbarangkeluar_r_insert"() OWNER TO "postgres";');

    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"adjusmenbarangmasuk_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN
------------------------------->INSERT table history adjusmenbarangmasuk_r<----------------------------------
        INSERT INTO adjusmenbarangmasuk_r (     
                adjusmenbarangmasuk_id,
                adjusmenbarang_id,
                barang_id,
                tgl_kadaluarsa,
                qty,
                satuankecil_id,
                harga_netto,
                satuanbesar_id,
                qty_konversi,
                satuankonversibrg_id,
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
                keterangan_rekap
        )VALUES(
                NEW.adjusmenbarangmasuk_id,
                NEW.adjusmenbarang_id,
                NEW.barang_id,
                NEW.tgl_kadaluarsa,
                NEW.qty,
                NEW.satuankecil_id,
                NEW.harga_netto,
                NEW.satuanbesar_id,
                NEW.qty_konversi,
                NEW.satuankonversibrg_id,
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
                'ACCRUAL'
            );

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

    $this->execute('ALTER FUNCTION "public"."adjusmenbarangmasuk_r_insert"() OWNER TO "postgres";');

    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"pemakaianbarangdetail_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN
------------------------------->INSERT table history pemakaianbarangdetail_r<----------------------------------
        INSERT INTO pemakaianbarangdetail_r (       
            pemakaianbarangdetail_id,
            pemakaianbarang_id,
            barang_id,
            jumlah_pakai,
            harga_netto,
            ppn,
            disc,
            hpp,
            harga_jual,
            catatan_barang,
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
            satuankecil_id,
            keterangan_rekap
        )VALUES(
            NEW.pemakaianbarangdetail_id,
            NEW.pemakaianbarang_id,
            NEW.barang_id,
            NEW.jumlah_pakai,
            NEW.harga_netto,
            NEW.ppn,
            NEW.disc,
            NEW.hpp,
            NEW.harga_jual,
            NEW.catatan_barang,
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
            NEW.satuankecil_id,
            'ACCRUAL'
            );

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

    $this->execute('ALTER FUNCTION "public"."pemakaianbarangdetail_r_insert"() OWNER TO "postgres";');

    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"pemusnahanbarangdetail_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN
------------------------------->INSERT table history pemusnahanbarangdetail_r<----------------------------------
        INSERT INTO pemusnahanbarangdetail_r (      
            pemusnahanbarangdetail_id,
            pemusnahanbarang_id,
            barang_id,
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
            keterangan_rekap
        )VALUES(
            NEW.pemusnahanbarangdetail_id,
            NEW.pemusnahanbarang_id,
            NEW.barang_id,
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
            'ACCRUAL'
            );

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

    $this->execute('ALTER FUNCTION "public"."pemusnahanbarangdetail_r_insert"() OWNER TO "postgres";');

    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"penerimaanbarang_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    
        
BEGIN
    IF (NEW.is_verifikasi = 1)
    THEN
        -- INSERT table history penerimaanbarang_r
        INSERT INTO penerimaanbarang_r (        
            penerimaanbarang_id,
            validasipobarang_id,
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
            no_faktur_sementara,
            status_rekap
        )VALUES(
            NEW.penerimaanbarang_id,
            NEW.validasipobarang_id,
            NEW.no_penerimaan,
            NEW.tgl_penerimaan,
            NEW.supplier_id,
            NEW.no_suratjalan,
            NEW.tgl_suratjalan,
            NEW.no_faktur,
            NEW.diterima_oleh,
            NEW.ruanganpenerima_id,
            NEW.peg_mengetahui,
            NEW.peg_menyetujui,
            NEW.upload_berkas,
            NEW.catatan_berkas,
            NEW.catatan,
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
            NEW.is_verifikasi,
            NEW.no_faktur_sementara,
            'ACCRUAL'
        );
        
END IF;
    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

    $this->execute('ALTER FUNCTION "public"."penerimaanbarang_r_insert"() OWNER TO "postgres";');

    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"penerimaanbarangdetail_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    
        
BEGIN
        -- INSERT table history penerimaanbarangdetail_r
        INSERT INTO penerimaanbarangdetail_r (      
            penerimaanbarangdetail_id,
            penerimaanbarang_id,
            validasipobarangdetail_id,
            barang_id,
            qty_po,
            po_balance,
            qty_diterima,
            s_konversibrg_id,
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
            NEW.penerimaanbarangdetail_id,
            NEW.penerimaanbarang_id,
            NEW.validasipobarangdetail_id,
            NEW.barang_id,
            NEW.qty_po,
            NEW.po_balance,
            NEW.qty_diterima,
            NEW.s_konversibrg_id,
            NEW.tgl_kadaluarsa,
            NEW.no_batch,
            NEW.harga,
            NEW.discount,
            NEW.discount_rp,
            NEW.jumlah,
            NEW.keterangan,
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

    $this->execute('ALTER FUNCTION "public"."penerimaanbarangdetail_r_insert"() OWNER TO "postgres";');

    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"returpenerimaanbarang_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN
------------------------------->INSERT table history returpenerimaanbarang_r<----------------------------------
        INSERT INTO returpenerimaanbarang_r (       
            returpenerimaanbarang_id,
            pegawairetur_id,
            ruanganretur_id,
            no_returpenerimaanbarang,
            tgl_retur,
            alasan_retur,
            keterangan_retur,
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
            NEW.returpenerimaanbarang_id,
            NEW.pegawairetur_id,
            NEW.ruanganretur_id,
            NEW.no_returpenerimaanbarang,
            NEW.tgl_retur,
            NEW.alasan_retur,
            NEW.keterangan_retur,
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

    $this->execute('ALTER FUNCTION "public"."returpenerimaanbarang_r_insert"() OWNER TO "postgres";');

    $this->execute("
        CREATE OR REPLACE FUNCTION \"public\".\"returpenerimaanbarangdetail_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
DECLARE

    
BEGIN
------------------------------->INSERT table history returpenerimaanbarangdetail_r<----------------------------------
        INSERT INTO returpenerimaanbarangdetail_r (     
            returpenerimaanbarangdetail_id,
            returpenerimaanbarang_id,
            penerimaanbarang_id,
            penerimaanbarangdetail_id,
            penerimaansuppbrgdetail_id,
            barang_id,
            satuanbesar_id,
            tgl_kadaluarsa,
            qty_retur,
            qty_input,
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
            stokbarang_id,
            keterangan_rekap
        )VALUES(
            NEW.returpenerimaanbarangdetail_id,
            NEW.returpenerimaanbarang_id,
            NEW.penerimaanbarang_id,
            NEW.penerimaanbarangdetail_id,
            NEW.penerimaansuppbrgdetail_id,
            NEW.barang_id,
            NEW.satuanbesar_id,
            NEW.tgl_kadaluarsa,
            NEW.qty_retur,
            NEW.qty_input,
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
            NEW.stokbarang_id,
            'ACCRUAL'
            );

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

    $this->execute('CREATE TRIGGER "adjusmenbarangkeluar_r_insert" AFTER INSERT ON "public"."adjusmenbarangkeluar_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."adjusmenbarangkeluar_r_insert"();');

    $this->execute('COMMENT ON TRIGGER "adjusmenbarangkeluar_r_insert" ON "public"."adjusmenbarangkeluar_t" IS \'rekap stockscrap (INSERT)\';');

    $this->execute('CREATE TRIGGER "adjusmenbarangmasuk_r_insert" AFTER INSERT ON "public"."adjusmenbarangmasuk_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."adjusmenbarangmasuk_r_insert"();');

    $this->execute('COMMENT ON TRIGGER "adjusmenbarangmasuk_r_insert" ON "public"."adjusmenbarangmasuk_t" IS \'rekap stockscrap (INSERT)\';');

    $this->execute('CREATE TRIGGER "pemakaianbarangdetail_r_insert" AFTER INSERT ON "public"."pemakaianbarangdetail_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."pemakaianbarangdetail_r_insert"();
');
    $this->execute('COMMENT ON TRIGGER "pemakaianbarangdetail_r_insert" ON "public"."pemakaianbarangdetail_t" IS \'rekap stockscrap (INSERT)\';');

    $this->execute('CREATE TRIGGER "pemusnahanbarangdetail_r_insert" AFTER INSERT ON "public"."pemusnahanbarangdetail_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."pemusnahanbarangdetail_r_insert"();');

    $this->execute('COMMENT ON TRIGGER "pemusnahanbarangdetail_r_insert" ON "public"."pemusnahanbarangdetail_t" IS \'rekap stockscrap (INSERT)\';');

    $this->execute('CREATE TRIGGER "penerimaanbarang_r_insert" AFTER INSERT ON "public"."penerimaanbarang_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."penerimaanbarang_r_insert"();');

    $this->execute('CREATE TRIGGER "penerimaanbarang_r_update" AFTER UPDATE ON "public"."penerimaanbarang_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."penerimaanbarang_r_insert"();');

    $this->execute('CREATE TRIGGER "penerimaanbarangdetail_r_insert" AFTER INSERT ON "public"."penerimaanbarangdetail_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."penerimaanbarangdetail_r_insert"();');

    $this->execute('CREATE TRIGGER "returpenerimaanbarang_r_insert" AFTER INSERT ON "public"."returpenerimaanbarang_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."returpenerimaanbarang_r_insert"();');

    $this->execute('COMMENT ON TRIGGER "returpenerimaanbarang_r_insert" ON "public"."returpenerimaanbarang_t" IS \'insert ke table rekap returpenerimaanbarang_r (INSERT)\';');

    $this->execute('CREATE TRIGGER "returpenerimaanbarangdetail_r_insert" AFTER INSERT ON "public"."returpenerimaanbarangdetail_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."returpenerimaanbarangdetail_r_insert"();');

    $this->execute('COMMENT ON TRIGGER "returpenerimaanbarangdetail_r_insert" ON "public"."returpenerimaanbarangdetail_t" IS \'rekap returpenerimaanbarangdetail_r (INSERT)\';');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210412_115758_oddo_20210412_triggerrekap cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210412_115758_oddo_20210412_triggerrekap cannot be reverted.\n";

        return false;
    }
    */
}
