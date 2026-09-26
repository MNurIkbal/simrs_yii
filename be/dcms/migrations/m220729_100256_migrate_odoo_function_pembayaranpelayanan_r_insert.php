<?php

use yii\db\Migration;

/**
 * Class m220729_100256_migrate_odoo_function_pembayaranpelayanan_r_insert
 */
class m220729_100256_migrate_odoo_function_pembayaranpelayanan_r_insert extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."pembayaranpelayanan_r_insert"()
              RETURNS "pg_catalog"."trigger" AS $BODY$   
                    
            BEGIN

                 INSERT INTO pembayaranpelayanan_r (
                            pembayaranpelayanan_id,
                            carabayar_id,
                            ruangan_id,
                            penjamin_id,
                            pembebasantarif_id,
                            pendaftaran_id,
                            pasienadmisi_id,
                            suratketjaminan_id,
                            tandabuktibayar_id,
                            pasien_id,
                            pembklaimdetail_id,
                            ruangan_pelakhir_id,
                            no_pembayaran,
                            tgl_pembayaran,
                            no_resep,
                            no_sjp,
                            total_biayaoa,
                            total_biayatindakan,
                            total_biayapelayanan,
                            total_subsidiasuransi,
                            total_subsidipemerintah,
                            total_subsidirs,
                            total_iurbiaya,
                            total_bayartindakan,
                            total_discount,
                            total_pembebasan,
                            total_sisatagihan,
                            statusbayar,
                            biaya_administrasi,
                            e_collection,
                            no_rekening,
                            nama_pemrekening,
                            penggunaan_uangmuka,
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
                            total_terbayar,
                            pembulatan,
                            is_lunas,
                            penjualanresep_id,
                            pembayaran_id,
                            is_penjaminutama,
                            keterangan  
                 )VALUES(
                            NEW.pembayaranpelayanan_id,
                            NEW.carabayar_id,
                            NEW.ruangan_id,
                            NEW.penjamin_id,
                            NEW.pembebasantarif_id,
                            NEW.pendaftaran_id,
                            NEW.pasienadmisi_id,
                            NEW.suratketjaminan_id,
                            NEW.tandabuktibayar_id,
                            NEW.pasien_id,
                            NEW.pembklaimdetail_id,
                            NEW.ruangan_pelakhir_id,
                            NEW.no_pembayaran,
                            NEW.tgl_pembayaran,
                            NEW.no_resep,
                            NEW.no_sjp,
                            NEW.total_biayaoa,
                            NEW.total_biayatindakan,
                            NEW.total_biayapelayanan,
                            NEW.total_subsidiasuransi,
                            NEW.total_subsidipemerintah,
                            NEW.total_subsidirs,
                            NEW.total_iurbiaya,
                            NEW.total_bayartindakan,
                            NEW.total_discount,
                            NEW.total_pembebasan,
                            NEW.total_sisatagihan,
                            NEW.statusbayar,
                            NEW.biaya_administrasi,
                            NEW.e_collection,
                            NEW.no_rekening,
                            NEW.nama_pemrekening,
                            NEW.penggunaan_uangmuka,
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
                            NEW.total_terbayar,
                            NEW.pembulatan,
                            NEW.is_lunas,
                            NEW.penjualanresep_id,
                            NEW.pembayaran_id,
                            NEW.is_penjaminutama,
                            \'ACCRUAL\'
                    );


                    RETURN NEW;

            END
            $BODY$
              LANGUAGE plpgsql VOLATILE
              COST 100;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220729_100256_migrate_odoo_function_pembayaranpelayanan_r_insert cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220729_100256_migrate_odoo_function_pembayaranpelayanan_r_insert cannot be reverted.\n";

        return false;
    }
    */
}
