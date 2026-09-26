<?php

use yii\db\Migration;

/**
 * Class m210830_062512_migrate_penerimaanbarang_trg
 */
class m210830_062512_migrate_penerimaanbarang_trg extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"penerimaanbarang_validasi\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$

DECLARE

        
BEGIN
        IF (NEW.is_verifikasi = 1) 
            THEN
                    -- Update Validasi untuk bisa melakukan penerimaan lagi
                        UPDATE validasipobarang_t SET 
                            is_verifikasi = FALSE
                        WHERE validasipobarang_id = NEW.validasipobarang_id;

                    -- Ini Update Trigger untuk menyatakan bahwa parentnya sudah di verifikasi
                        UPDATE penerimaanbarangdetail_t SET
                                additional_data  = 'is_verifikasi'
                        WHERE penerimaanbarang_id = NEW.penerimaanbarang_id AND is_deleted = false;                                             
        RETURN NEW;
    END IF;
    
     RETURN NEW;    
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

        $this->execute('ALTER FUNCTION "public"."penerimaanbarang_validasi"() OWNER TO "postgres";');

        $this->execute('CREATE TRIGGER "penerimaanbarang_validasi" AFTER UPDATE ON "public"."penerimaanbarang_t"
FOR EACH ROW
EXECUTE PROCEDURE "public"."penerimaanbarang_validasi"();');

        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210830_062512_migrate_penerimaanbarang_trg cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210830_062512_migrate_penerimaanbarang_trg cannot be reverted.\n";

        return false;
    }
    */
}
