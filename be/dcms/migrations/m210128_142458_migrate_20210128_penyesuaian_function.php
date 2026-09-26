<?php

use yii\db\Migration;

/**
 * Class m210128_142458_migrate_20210128_penyesuaian_function
 */
class m210128_142458_migrate_20210128_penyesuaian_function extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"pembayaran_r_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
    
        
BEGIN
-- INSERT table history pembayaran_r untuk case tunai --
    IF(NEW.total_tunai <> 0)
        THEN
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
        0, -- total_dijamin
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

-- INSERT table history pembayaran_r untuk case dijamin --
IF(NEW.total_dijamin <> 0)
        THEN
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
        0 , --total_tunai
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
        v_totaldijamin float;

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
                0, -- total_dijamin
                -1 * total_sisatagihan ,
                -1 * total_kembalian ,
                -1 * total_administrasi ,
                -1 * total_pembulatan ,
                -1 * total_pembebasan ,
                penggunaan_uangmuka ,
                pemberianpiutang_id ,
                -1 * total_ditagihkan ,
                -1 * total_tunai ,
                0, -- total_nontunai
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
                'REFUND'
            FROM pembayaran_t
            WHERE pembayaran_id = NEW.pembayaran_id;
    
    END IF;

----------------------------> insert pembatalan total_dijamin<---------------------------------   
        
                SELECT
            pembayaran_t.total_dijamin
            INTO
            v_totaldijamin
        FROM pembayaran_t
                    WHERE pembayaran_t.pembayaran_id = NEW.pembayaran_id;
        
        IF(NEW.is_deleted IS TRUE and v_totaldijamin <> 0)
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
                0, --total_tunai
                0, --total_nontunai
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
                'REFUND'
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
                'BILLING CANCEL',
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
                'BILLING CANCEL',
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
        total_dijamin,
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
        0, --total dijamin
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
        total_dijamin,
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
        0, --total_dijamin
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
        'REFUND'
        );
END IF;

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
        echo "m210128_142458_migrate_20210128_penyesuaian_function cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210128_142458_migrate_20210128_penyesuaian_function cannot be reverted.\n";

        return false;
    }
    */
}
