<?php

use yii\db\Migration;

/**
 * Class m220425_020810_migrate_odoo_bayaruangmuka_r_batal
 */
class m220425_020810_migrate_odoo_bayaruangmuka_r_batal extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('DROP TRIGGER if exists "bayaruangmuka_r_batal" ON "public"."bayaruangmuka_t";');

         $this->execute('DROP FUNCTION if exists "public"."bayaruangmuka_r_batal"()');

         $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"bayaruangmuka_r_batal\"()
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
        OLD.bayaruangmuka_id ,
        OLD.pembatalanuangmuka_id ,
        OLD.pasienadmisi_id ,
        OLD.pemakaianuangmuka_id ,
        OLD.tandabuktibayar_id ,
        OLD.ruangan_id ,
        OLD.pasien_id ,
        OLD.pendaftaran_id ,
        OLD.tgl_uangmuka ,
        -1 * OLD.jumlah_uangmuka, 
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
        NEW.modified_count ,
        NEW.last_modified_date ,
        NEW.last_modified_by ,
        NEW.is_deleted ,
        OLD.is_active ,
        NEW.deleted_date ,
        NEW.deleted_by ,
            'Deposit Refund'
        );

    RETURN OLD;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

         $this->execute('ALTER FUNCTION "public"."bayaruangmuka_r_batal"() OWNER TO "postgres";');

         $this->execute('CREATE TRIGGER "bayaruangmuka_r_batal" AFTER UPDATE OF "is_deleted" ON "public"."bayaruangmuka_t"
                            FOR EACH ROW
                            EXECUTE PROCEDURE "public"."bayaruangmuka_r_batal"();');

         

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220425_020810_migrate_odoo_bayaruangmuka_r_batal cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220425_020810_migrate_odoo_bayaruangmuka_r_batal cannot be reverted.\n";

        return false;
    }
    */
}
