<?php

use yii\db\Migration;

/**
 * Class m210309_095859_migrate_20210309_func_adjusmenbarangmasuk_t_insert
 */
class m210309_095859_migrate_20210309_func_adjusmenbarangmasuk_t_insert extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            CREATE OR REPLACE FUNCTION \"public\".\"adjusmenbarangmasuk_t_insert\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$-- author ikbal 2019-03-11

DECLARE
    vobalalkes_id  int4;
    vqty int4;
    vhargasatuan float8;
    vharganetto float8;
BEGIN
    vobalalkes_id := NEW.obatalkes_id;
    vqty := NEW.qty_konversi;
    vharganetto := NEW.harga_netto;
    
    vhargasatuan := 0;
    IF (vqty > 0) THEN
        vhargasatuan := vharganetto / vqty;
    END IF;
        
    UPDATE obatalkes_m 
    SET 
--      harganetto = (
--          CASE 
--              WHEN harganetto = 0 THEN 0 
--              ELSE vhargasatuan 
--          END
--      ), 
        hargamaksimum = (
            CASE 
            WHEN hargamaksimum = 0 THEN vhargaSatuan
            ELSE
                CASE WHEN vhargaSatuan > hargamaksimum  
                THEN
                    vhargaSatuan
                ELSE
                    hargamaksimum
                END
            END
        ),
        hargaminimum = (
            CASE WHEN hargaminimum = 0 THEN vhargaSatuan
            ELSE
                CASE 
                    WHEN hargaminimum < vhargaSatuan  
                    THEN
                        hargaminimum
                    ELSE
                        vhargaSatuan
                END
            END
        ),
        hargaratarata = (
            CASE 
                WHEN hargaratarata = 0 THEN vhargaSatuan
                ELSE  
                    (hargaterakhir + vhargaSatuan) / 2
            END
        ),
        hargaterakhir = vhargasatuan 
    WHERE obatalkes_id = vobalalkes_id;
    

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210309_095859_migrate_20210309_func_adjusmenbarangmasuk_t_insert cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210309_095859_migrate_20210309_func_adjusmenbarangmasuk_t_insert cannot be reverted.\n";

        return false;
    }
    */
}
