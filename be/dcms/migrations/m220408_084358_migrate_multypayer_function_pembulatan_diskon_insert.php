<?php

use yii\db\Migration;

/**
 * Class m220408_084358_migrate_multypayer_function_pembulatan_diskon_insert
 */
class m220408_084358_migrate_multypayer_function_pembulatan_diskon_insert extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."pembulatan_diskon_insert"()
              RETURNS "pg_catalog"."trigger" AS $BODY$ DECLARE v_daftartindakan_id int;
            v_ruangan_id int;
            v_penjamin_id int;
            v_instalasi_id int;
            v_pegawai_id int;
            v_kelaspelayanan_id int;
            BEGIN 

            ---------------------------------- SETUP META DATA TRANSAKSI --------------------------------------------------------
            IF (NEW.total_pembulatan <> 0 OR 
                COALESCE(NEW.total_discount, 0) + COALESCE(NEW.total_discountpembayaran, 0) <> 0 OR
                NEW.total_administrasi <> 0) THEN
                
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
            END IF;

             --------------------------> insert pembulatan ke tindakanpelayanan_r <--------------------------------
            IF(NEW.total_pembulatan <> 0) THEN 
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
              tarif_dibayarkan
            ) 
            VALUES 
              (
                NEW.pendaftaran_id, 
                v_daftartindakan_id, 
                NEW.total_pembulatan, 
                NEW.total_pembulatan, 
                \'1\', 
                \'BILLING\', 
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
                new.pembayaran_id, 
                0, 
                    0, --tarif_dijamin
                NEW.total_pembulatan -- tarif_dibayarkan             
                );
            END IF;
            -------------------------> insert diskon ke tindakanpelayanan_r <----------------------------------
            IF(
              COALESCE(NEW.total_discount, 0) + COALESCE(NEW.total_discountpembayaran, 0) <> 0
            ) THEN 
            v_daftartindakan_id := 99991;
            -- daftartindakan_id untuk diskon
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
              tarif_dibayarkan
            ) 
            VALUES 
              (
                NEW.pendaftaran_id, 
                v_daftartindakan_id, 
                COALESCE(-1 * NEW.total_discount, 0) + COALESCE(
                  -1 * NEW.total_discountpembayaran, 
                  0
                ), 
                COALESCE(-1 * NEW.total_discount, 0) + COALESCE(
                  -1 * NEW.total_discountpembayaran, 
                  0
                ), 
                \'1\', 
                \'DISCOUNT\', 
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
                new.pembayaran_id, 
                0, 
                CASE WHEN NEW.total_dijamin <> 0 THEN COALESCE(-1 * NEW.total_discount, 0) + COALESCE(
                  -1 * NEW.total_discountpembayaran, 
                  0
                ) ELSE 0 END, 
                --tarif_dijamin
                CASE WHEN NEW.total_dijamin = 0 THEN COALESCE(-1 * NEW.total_discount, 0) + COALESCE(
                  -1 * NEW.total_discountpembayaran, 
                  0
                ) ELSE 0 END -- tarif_dibayarkan
                );
            END IF;
            ---------------------------------> insert administrasi ke tindakanpelayanan_r <-------------------------------------
            IF(NEW.total_administrasi <> 0) THEN 
            v_daftartindakan_id := 99992;
            -- daftartindakan_id untuk administrasi
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
                NEW.total_administrasi, 
                NEW.total_administrasi, 
                \'1\', 
                \'BILLING\', 
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
                new.pembayaran_id, 
                (
                  (
                    NEW.additional_data :: json ->> \'adm_asuransi\'
                  ):: json ->> \'nominal_diskon\'
                ):: float, -- diskon
                (
                  (
                    NEW.additional_data :: json ->> \'adm_asuransi\'
                  ):: json ->> \'dijamin\'
                ):: float, --tarif_dijamin
                (
                  (
                    NEW.additional_data :: json ->> \'adm_asuransi\'
                  ):: json ->> \'harusbayar\'
                ):: float, --tarif_dibayarkan
                    (
                  (
                    NEW.additional_data :: json ->> \'adm_asuransi\'
                  ):: json ->> \'is_penjaminutama\'
                ):: BOOLEAN -- is_penjaminutama
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
        echo "m220408_084358_migrate_multypayer_function_pembulatan_diskon_insert cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220408_084358_migrate_multypayer_function_pembulatan_diskon_insert cannot be reverted.\n";

        return false;
    }
    */
}
