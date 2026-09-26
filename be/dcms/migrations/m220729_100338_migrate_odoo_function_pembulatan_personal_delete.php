<?php

use yii\db\Migration;

/**
 * Class m220729_100338_migrate_odoo_function_pembulatan_personal_delete
 */
class m220729_100338_migrate_odoo_function_pembulatan_personal_delete extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."pembulatan_personal_delete"()
              RETURNS "pg_catalog"."trigger" AS $BODY$ 
                DECLARE 
                v_daftartindakan_id int;
                v_ruangan_id int;
                v_penjamin_id int;
                v_instalasi_id int;
                v_pegawai_id int;
                v_pasienadmisi_id int;
                v_kelaspelayanan_id int;
                v_total_diskon float;
                v_total_discountpembayaran float;
                v_totaltunai float;
                v_totaldijamin float;
                v_adm_dijamin float;
                v_adm_dibayar float;
                v_total_dibayar float;
                v_total_sisatagihan float;
                v_total_kembalian float;
                v_total_administrasi float; 
                v_total_pembulatan float; 
                v_pembulatan float; 
                v_total_pembebasan float; 
                v_penggunaan_uangmuka float; 
                v_pemberianpiutang_id float; 
                v_total_ditagihkan float; 
                v_total_tunai float; 
                v_total_discount float; 
                v_total_tagihan float; 
                v_catatan text;
                v_disc_adm float;
            BEGIN 



                        -------------------------------- SETUP METADATA PENDAFTARAN -----------------------------------------------
                          IF (v_pasienadmisi_id IS NOT NULL) THEN
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
                              pasienadmisi_t.pasienadmisi_id = v_pasienadmisi_id;
                          ELSE 
                            SELECT 
                              pendaftaran_t.ruangan_id, 
                              pendaftaran_t.penjamin_id, 
                              pendaftaran_t.instalasi_id, 
                              pendaftaran_t.pegawai_id,
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
                        -------------------------------- END SETUP ----------------------------------------------------------------


                        ----------------------------> insert pembulatan ke tindakanpelayanan_r <---------------------------------   

                        IF(
                         OLD.pembulatan <> 0
                        ) THEN 


                        v_daftartindakan_id := 99990;
                        -- daftartindakan_id untuk pembulatan
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
                          pembayaran_id, 
                          tarif_diskon, 
                          tarif_dijamin, 
                          tarif_dibayarkan,
                          kelaspelayanan_id,
                                        is_penjaminutama
                        ) 
                        VALUES 
                          (
                            OLD.pendaftaran_id, 
                            v_daftartindakan_id,
                                            OLD.pembulatan,
                                            OLD.pembulatan,  
                            \'-1\', 
                            \'BILLING CANCEL\', 
                            v_ruangan_id, 
                            v_instalasi_id, 
                            v_penjamin_id, 
                            v_pegawai_id, 
                            NEW.deleted_date, 
                            OLD.additional_data, 
                            OLD.created_date, 
                            OLD.created_by, 
                            NEW.modified_count, 
                            NEW.last_modified_date, 
                            NEW.last_modified_by, 
                            NEW.is_deleted, 
                            NEW.is_active, 
                            NEW.deleted_date, 
                            NEW.deleted_by, 
                            NEW.deleted_date, 
                            OLD.pembayaran_id, 
                            0, 
                            0, --tarif_dijamin
                            -1 * OLD.pembulatan, --tarif_dibayarkan
                            v_kelaspelayanan_id,
                                            FALSE
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
        echo "m220729_100338_migrate_odoo_function_pembulatan_personal_delete cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220729_100338_migrate_odoo_function_pembulatan_personal_delete cannot be reverted.\n";

        return false;
    }
    */
}
