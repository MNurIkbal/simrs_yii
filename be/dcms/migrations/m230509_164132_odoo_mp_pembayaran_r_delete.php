<?php

use yii\db\Migration;

/**
 * Class m230509_164132_odoo_mp_pembayaran_r_delete
 */
class m230509_164132_odoo_mp_pembayaran_r_delete extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE OR REPLACE FUNCTION public.pembayaran_r_delete()
            RETURNS trigger
            LANGUAGE plpgsql
            AS \$function\$ 
            DECLARE v_daftartindakan_id int;
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
                v_ispenjaminutama BOOLEAN;
                v_pembulatan_payer FLOAT8;
                v_pembulatan_subpayer FLOAT8;
            BEGIN 
            
            
            ----------------------------- SETUP META DATA PEMBAYARAN -----------------------------
                SELECT 
                pembayaran_t.total_dijamin,
                pembayaran_t.total_discount,
                pembayaran_t.total_discountpembayaran,
                (
                    (
                    pembayaran_t.additional_data :: json ->> 'adm_asuransi'
                    ):: json ->> 'dijamin'
                ):: float,
                (
                    (
                    pembayaran_t.additional_data :: json ->> 'adm_asuransi'
                    ):: json ->> 'harusbayar'
                ):: float,
                pembayaran_t.pasienadmisi_id,
                pembayaran_t.total_tagihan,
                pembayaran_t.total_dibayar,
                pembayaran_t.total_sisatagihan,
                pembayaran_t.total_kembalian,
                pembayaran_t.total_administrasi, 
                pembayaran_t.total_pembulatan, 
                pembayaran_t.pembulatan, 
                pembayaran_t.total_pembebasan, 
                pembayaran_t.penggunaan_uangmuka, 
                pembayaran_t.pemberianpiutang_id, 
                pembayaran_t.total_ditagihkan, 
                pembayaran_t.total_tunai, 
                pembayaran_t.total_discount, 
                pembayaran_t.catatan,
                (
                    (
                    pembayaran_t.additional_data :: json ->> 'adm_asuransi'
                    ):: json ->> 'nominal_diskon'
                ):: float,
                    (
                    (
                    pembayaran_t.additional_data :: json ->> 'adm_asuransi'
                    ):: json ->> 'is_penjaminutama'
                ):: BOOLEAN
                INTO 
                v_totaldijamin,
                v_total_diskon,
                v_total_discountpembayaran,
                v_adm_dijamin,
                v_adm_dibayar,
                v_pasienadmisi_id,
                v_total_tagihan,
                v_total_dibayar,
                v_total_sisatagihan,
                v_total_kembalian,
                v_total_administrasi, 
                v_total_pembulatan, 
                v_pembulatan, 
                v_total_pembebasan, 
                v_penggunaan_uangmuka, 
                v_pemberianpiutang_id, 
                v_total_ditagihkan, 
                v_total_tunai, 
                v_total_discount, 
                v_catatan,
                v_disc_adm,
                    v_ispenjaminutama
                FROM 
                pembayaran_t 
                WHERE 
                pembayaran_t.pembayaran_id = NEW.pembayaran_id;
            
            ------------------------- END SETUP ----------------------------------------------------------
            
            
            ---------------------------------- INSERT PEMBAYARAN TUNAI --------------------------------------
            IF(
                NEW.is_deleted IS TRUE 
                and v_total_tunai <> 0
            ) THEN -- INSERT table history pembayaran_r, menjadi Deposit Refund
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
            ) VALUES  (
                NEW.pembayaran_id,
                NEW.pendaftaran_id,
                v_pasienadmisi_id,
                -1 * v_total_tagihan, 
                -1 * v_total_dibayar, 
                0, 
                -1 * v_total_sisatagihan, 
                -1 * v_total_kembalian, 
                -1 * v_total_administrasi, 
                -1 * v_total_pembulatan, 
                -1 * v_total_pembebasan, 
                v_penggunaan_uangmuka, 
                v_pemberianpiutang_id, 
                -1 * v_total_ditagihkan, 
                -1 * v_total_tunai, 
                0, 
                -1 * v_total_discount, 
                -1 * v_total_discountpembayaran, 
                v_catatan, 
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
            );
            
            ELSEIF(
                NEW.is_deleted IS TRUE 
                and v_totaldijamin <> 0
            ) THEN -- INSERT table history pembayaran_r, menjadi Deposit Refund
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
            ) VALUES  (
                NEW.pembayaran_id,
                NEW.pendaftaran_id,
                v_pasienadmisi_id,
                -1 * v_total_tagihan, 
                -1 * v_total_dibayar, 
                0, 
                -1 * v_total_sisatagihan, 
                -1 * v_total_kembalian, 
                -1 * v_total_administrasi, 
                -1 * v_total_pembulatan, 
                -1 * v_total_pembebasan, 
                v_penggunaan_uangmuka, 
                v_pemberianpiutang_id, 
                -1 * v_total_ditagihkan, 
                -1 * v_total_tunai, 
                0, 
                -1 * v_total_discount, 
                -1 * v_total_discountpembayaran, 
                v_catatan, 
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
            );
            
            END IF;
            ------------------------- END INSERT -----------------------------------------------------------------------
            
            
            -------------------------------- SETUP METADATA PENDAFTARAN -----------------------------------------------
            IF (NEW.is_deleted IS TRUE AND (
                v_total_pembulatan <> 0 OR v_pembulatan <> 0 OR
                (COALESCE(v_total_diskon, 0) + COALESCE(v_total_discountpembayaran, 0) <> 0) OR 
                v_total_administrasi <> 0 )) THEN
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
            END IF;
            
            -------------------------------- END SETUP ----------------------------------------------------------------
            ---------------------------------- SETUP META DATA PEMBAYARAN --------------------------------------------------------
                
                SELECT
                pembayaranpelayanan_t.pembulatan
                INTO
                v_pembulatan_payer
                FROM pembayaranpelayanan_t
                WHERE pembayaranpelayanan_t.pembayaran_id = OLD.pembayaran_id and pembayaranpelayanan_t.is_penjaminutama = TRUE;
                
                SELECT
                pembayaranpelayanan_t.pembulatan
                INTO
                v_pembulatan_subpayer
                FROM pembayaranpelayanan_t
                WHERE pembayaranpelayanan_t.pembayaran_id = OLD.pembayaran_id and pembayaranpelayanan_t.is_penjaminutama = FALSE;
            
            
            ----------------------------> insert pembulatan ke tindakanpelayanan_r <---------------------------------   
            
            IF(
                NEW.is_deleted IS TRUE 
                and (v_total_pembulatan <> 0 OR v_pembulatan <> 0)
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
                tarif_subpayer, 
                tarif_dibayarkan,
                kelaspelayanan_id
            ) 
            VALUES 
                (
                NEW.pendaftaran_id, 
                v_daftartindakan_id,
                CASE 
                    WHEN v_total_pembulatan = 0 THEN v_pembulatan
                    WHEN v_pembulatan = 0 THEN v_total_pembulatan
                    ELSE v_total_pembulatan + v_pembulatan
                END,
                CASE 
                    WHEN v_total_pembulatan = 0 THEN -1 * v_pembulatan
                    WHEN v_pembulatan = 0 THEN -1 * v_total_pembulatan
                    ELSE -1 * (v_total_pembulatan + v_pembulatan)
                END,   
                '-1', 
                'BILLING CANCEL', 
                v_ruangan_id, 
                v_instalasi_id, 
                v_penjamin_id, 
                v_pegawai_id, 
                NEW.deleted_date, 
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
                NEW.deleted_date, 
                NEW.pembayaran_id, 
                0, 
                -1 * v_pembulatan_payer, --tarif_dijamin
                -1 * v_pembulatan_subpayer, --tarif_subpayer
                -1 * v_pembulatan, --tarif_dibayarkan
                v_kelaspelayanan_id
                );
            END IF;
            
            
            ---------------------------> insert diskon ke tindakanpelayanan_r <-------------------------
            IF(
                NEW.is_deleted IS TRUE 
                and (
                COALESCE(v_total_diskon, 0) <> 0
                )
            ) THEN 
            
            v_daftartindakan_id := 99991;
            -- daftartindakan_id untuk diskon
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
                kelaspelayanan_id
            ) 
            VALUES 
                (
                NEW.pendaftaran_id, 
                v_daftartindakan_id, 
                COALESCE(-1 * v_total_diskon, 0), 
                COALESCE(v_total_diskon, 0), 
                '-1', 
                'DISCOUNT CANCEL', 
                v_ruangan_id, 
                v_instalasi_id, 
                v_penjamin_id, 
                v_pegawai_id, 
                NEW.deleted_date, 
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
                NEW.deleted_date, 
                NEW.pembayaran_id, 
                0, 
                CASE 
                    WHEN v_totaldijamin <> 0 THEN COALESCE(v_total_diskon, 0)
                    ELSE 0 
                END, 
                CASE 
                    WHEN v_totaldijamin = 0 THEN COALESCE(v_total_diskon, 0)
                    ELSE 0 
                END,
                v_kelaspelayanan_id
                );
            END IF;
            
            
            -------------------------------------> insert administrasi ke tindakanpelayanan_r <--------------------------------------------
            IF(
                NEW.is_deleted IS TRUE 
                and v_total_administrasi <> 0
            ) THEN 
            
            v_daftartindakan_id := 99992;
            -- daftartindakan_id untuk administrasi
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
                NEW.pendaftaran_id, 
                v_daftartindakan_id, 
                v_total_administrasi, 
                -1 * v_total_administrasi, 
                '-1', 
                'BILLING CANCEL', 
                v_ruangan_id, 
                v_instalasi_id, 
                v_penjamin_id, 
                v_pegawai_id, 
                NEW.deleted_date, 
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
                NEW.deleted_date, 
                NEW.pembayaran_id, 
                -1 * v_disc_adm, -- diskon, 
                -1 * v_adm_dijamin, 
                -1 * v_adm_dibayar,
                v_kelaspelayanan_id,
                    v_ispenjaminutama
                );
            END IF;
            
            ---------------------------------> insert cancel discount administrasi ke tindakanpelayanan_r <-------------------------------------
            IF(
                    NEW.is_deleted IS TRUE and
                NEW.total_administrasi <> 0 and 
                ((NEW.additional_data :: json ->> 'adm_asuransi'):: json ->> 'nominal_diskon'):: float > 0
            ) 
            THEN 
            INSERT INTO tindakanpelayanan_r (       
                pendaftaran_id,
                daftartindakan_id,
                tarif_satuan,
                tarif_tindakan,
                qty_tindakan,
                keterangan,
                ruangan_id,
                instalasi_id, 
                    penjamin_id, kelaspelayanan_id, 
                    dokterpenanggungjawab_id, tgl_tindakan, 
                    additional_data, created_date, created_by, 
                    modified_count, last_modified_date, 
                    last_modified_by, is_deleted, is_active, 
                    deleted_date, deleted_by, tgl_proses, 
                    pembayaran_id, tarif_diskon, tarif_dijamin, 
                    tarif_dibayarkan,
                    is_penjaminutama,pasienadmisi_id 
                    ,dijamin_payer
                    ,dijamin_subpayer
            ) values (
                NEW.pendaftaran_id, 
                v_daftartindakan_id, 
                (
                    (
                    NEW.additional_data :: json ->> 'adm_asuransi'
                    ):: json ->> 'nominal_diskon'
                ):: float, 
                (
                    (
                    NEW.additional_data :: json ->> 'adm_asuransi'
                    ):: json ->> 'nominal_diskon'
                ):: float, 
                '-1', 
                'DISCOUNT CANCEL', 
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
                    NEW.additional_data :: json ->> 'adm_asuransi'
                    ):: json ->> 'nominal_diskon'
                ):: float, -- diskon
                (
                    (
                    NEW.additional_data :: json ->> 'adm_asuransi'
                    ):: json ->> 'nominal_diskon'
                ):: float, --tarif_dijamin
                (
                    (
                    NEW.additional_data :: json ->> 'adm_asuransi'
                    ):: json ->> 'harusbayar'
                ):: float, --tarif_dibayarkan
                    (
                    (
                    NEW.additional_data :: json ->> 'adm_asuransi'
                    ):: json ->> 'is_penjaminutama'
                ):: BOOLEAN, -- is_penjaminutama
                v_pasienadmisi_id    
                ,case when (new.total_dijamin > 0 and ((NEW.additional_data :: json ->> 'adm_asuransi'):: json ->> 'is_penjaminutama'):: BOOLEAN is TRUE)
                    then ((NEW.additional_data :: json ->> 'adm_asuransi'):: json ->> 'nominal_diskon'):: float 
                    else 0::float 
                end -- dijamin_payer
                ,case when (new.total_dijamin > 0 and ((NEW.additional_data :: json ->> 'adm_asuransi'):: json ->> 'is_penjaminutama'):: BOOLEAN is FALSE)
                    then ((NEW.additional_data :: json ->> 'adm_asuransi'):: json ->> 'nominal_diskon'):: float --diskon_payer
                    else 0::float 
                end -- dijamin_subpayer
                    );
            end if;
                                    
            RETURN NEW;
            END \$function\$
            ;       
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230509_164132_odoo_mp_pembayaran_r_delete cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230509_164132_odoo_mp_pembayaran_r_delete cannot be reverted.\n";

        return false;
    }
    */
}
