<?php

use yii\db\Migration;

/**
 * Class m220520_113520_hotfix_closing_trigger_pembatalanuangmuka_t_insert
 */
class m220520_113520_hotfix_closing_trigger_pembatalanuangmuka_t_insert extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."pembatalanuangmuka_t_insert"()
              RETURNS "pg_catalog"."trigger" AS $BODY$
                
                    
            BEGIN
            -- INSERT table pembatalanuangmuka_t untuk batal uang muka --
                IF(NEW.is_deleted = TRUE)
                    THEN
                            IF NOT EXISTS (
                                SELECT 1
                                FROM pembatalanuangmuka_t 
                                WHERE bayaruangmuka_id = NEW.bayaruangmuka_id 
                            )
                            THEN
                                INSERT INTO pembatalanuangmuka_t (      
                                        bayaruangmuka_id,
                                        tandabuktibayar_id,
                                        ruangan_id,
                                        tglpembatalan,
                                        keterangan_batal,
                                        jmlkaskeluarbatal,
                                        created_by
                                )VALUES(
                                        OLD.bayaruangmuka_id,
                                        OLD.tandabuktibayar_id,
                                        OLD.ruangan_id,
                                        NEW.deleted_date,
                                        NEW.alasan_batal,
                                        OLD.jumlah_uangmuka,
                                        NEW.deleted_by                      
                                );
                            END IF;
            END IF;


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
        echo "m220520_113520_hotfix_closing_trigger_pembatalanuangmuka_t_insert cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220520_113520_hotfix_closing_trigger_pembatalanuangmuka_t_insert cannot be reverted.\n";

        return false;
    }
    */
}
