<?php

use yii\db\Migration;

/**
 * Class m220509_072509_migrate_trg_histori_rencana_operasi
 */
class m220509_072509_migrate_trg_histori_rencana_operasi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP TRIGGER IF EXISTS "insert_history" ON "public"."rencanaoperasi_t";');

        $this->execute('DROP FUNCTION IF EXISTS "public"."histori_rencana_operasi";');


        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"histori_rencana_operasi\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$
BEGIN
    INSERT INTO \"historirencanaoperasi_r\" (
        rencanaoperasi_id,
        ruangan_id,
        tgl_perubahan ,
        tgl_permintaan ,
        jam_rencana_mulai ,
        jam_rencana_selesai ,
        status_operasi ,
        keterangan ,
        created_date ,
        created_by 
    )VALUES(
        NEW.rencanaoperasi_id,
        NEW.ruangan_id,
        (CURRENT_TIMESTAMP)::TIMESTAMP(0),
        NEW.tgl_permintaan,
        NEW.jam_rencana_mulai ,
        NEW.jam_rencana_selesai ,
        470,
        NULL ,
        (CURRENT_TIMESTAMP)::TIMESTAMP(0),
        NEW.created_by 
    );
    
    RETURN NEW;

END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100");

        $this->execute('ALTER FUNCTION "public"."histori_rencana_operasi"() OWNER TO "postgres";');

        $this->execute('CREATE TRIGGER "insert_history" BEFORE INSERT ON "public"."rencanaoperasi_t"
                        FOR EACH ROW
                        EXECUTE PROCEDURE "public"."histori_rencana_operasi"();');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220509_072509_migrate_trg_histori_rencana_operasi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220509_072509_migrate_trg_histori_rencana_operasi cannot be reverted.\n";

        return false;
    }
    */
}
