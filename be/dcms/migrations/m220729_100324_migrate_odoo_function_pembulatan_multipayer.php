<?php

use yii\db\Migration;

/**
 * Class m220729_100324_migrate_odoo_function_pembulatan_multipayer
 */
class m220729_100324_migrate_odoo_function_pembulatan_multipayer extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."pembulatan_multipayer"()
              RETURNS "pg_catalog"."trigger" AS $BODY$ 
                
                DECLARE 
                    v_daftartindakan_id int;
                    v_ruangan_id int;
                    v_penjamin_id int;
                    v_instalasi_id int;
                    v_pegawai_id int;
                    v_kelaspelayanan_id int;
                    v_pembulatan float8;
                
                BEGIN 

                        ---------------------------------- SETUP META DATA TRANSAKSI --------------------------------------------------------               
                            IF (NEW.pasienadmisi_id IS NOT NULL) THEN
                                SELECT 
                                    ruangan_id, 
                                    penjamin_id, 
                                    3 as instalasi_id, 
                                    pegawai_id, 
                                    kelaspelayanan_id 
                                INTO 
                                    v_ruangan_id, 
                                    v_penjamin_id, 
                                    v_instalasi_id, 
                                    v_pegawai_id, 
                                    v_kelaspelayanan_id 
                                FROM 
                                  pasienadmisi_t 
                                WHERE 
                                  pasienadmisi_t.pasienadmisi_id = NEW.pasienadmisi_id;
                            ELSE 
                                SELECT 
                                    ruangan_id, 
                                    penjamin_id, 
                                    instalasi_id, 
                                    pegawai_id, 
                                   kelaspelayanan_id 
                                INTO 
                                  v_ruangan_id, 
                                  v_penjamin_id, 
                                  v_instalasi_id, 
                                  v_pegawai_id, 
                                  v_kelaspelayanan_id 
                                FROM 
                                  pendaftaran_t 
                                WHERE 
                                  pendaftaran_t.pendaftaran_id = NEW.pendaftaran_id;
                            END IF;
                                            
                             --------------------------> insert pembulatan penjamin ke tindakanpelayanan_r <--------------------------------
                        IF(NEW.pembulatan <> 0) THEN 
                        v_daftartindakan_id := 99990;
                        -- daftartindakan_id untuk pembulatan
                        INSERT INTO tindakanpelayanan_r (
                          pendaftaran_id, daftartindakan_id, 
                          tarif_satuan, tarif_tindakan, qty_tindakan, 
                          keterangan, ruangan_id, instalasi_id, 
                          penjamin_id, kelaspelayanan_id, 
                          dokterpenanggungjawab_id, tgl_tindakan, 
                          additional_data, created_date, created_by, 
                          modified_count, last_modified_date, 
                          last_modified_by, is_deleted, is_active, 
                          deleted_date, deleted_by, tgl_proses, 
                          pembayaran_id, tarif_diskon, tarif_dijamin, 
                          tarif_dibayarkan, is_penjaminutama
                        ) 
                        VALUES 
                          (
                            NEW.pendaftaran_id, 
                            v_daftartindakan_id, 
                            NEW.pembulatan, 
                                            NEW.pembulatan, 
                            \'1\', 
                            \'BILLING\', 
                            v_ruangan_id, 
                            v_instalasi_id, 
                            NEW.penjamin_id, 
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
                            new.pembayaran_id, 
                            0, 
                            NEW.pembulatan, --tarif_dijamin
                            0, -- tarif_dibayarkan,
                                            NEW.is_penjaminutama                         
                            );
                        END IF;                     

                        RETURN NEW;
                        END $BODY$
              LANGUAGE plpgsql VOLATILE
              COST 100;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220729_100324_migrate_odoo_function_pembulatan_multipayer cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220729_100324_migrate_odoo_function_pembulatan_multipayer cannot be reverted.\n";

        return false;
    }
    */
}
