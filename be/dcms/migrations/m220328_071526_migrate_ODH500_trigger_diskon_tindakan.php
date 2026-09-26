<?php

use yii\db\Migration;

/**
 * Class m220328_071526_migrate_ODH500_trigger_diskon_tindakan
 */
class m220328_071526_migrate_ODH500_trigger_diskon_tindakan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."tindakanpelayanan_update_jasa_dokter"()
              RETURNS "pg_catalog"."trigger" AS $BODY$

            DECLARE
                vtindakanpelayanan_id int8;
                vtarif_tindakan float8;
                vtarif_diskon float8;
                vpersen_diskon float8;
            BEGIN
                vtindakanpelayanan_id := NEW.tindakanpelayanan_id ;
                vtarif_tindakan := NEW.tarif_tindakan;
                vtarif_diskon := NEW.tarif_diskon;
                IF(COALESCE(vtarif_diskon,0) > 0)
                THEN
                    vpersen_diskon := (vtarif_diskon/vtarif_tindakan)*100;

                    UPDATE tindakankomponen_t
                    SET discount_komponen = (tarif_tindakankomp*vpersen_diskon)/100
                    WHERE tindakanpelayanan_id = vtindakanpelayanan_id;
                ELSE
                    UPDATE tindakankomponen_t
                    SET discount_komponen = 0
                    WHERE tindakanpelayanan_id = vtindakanpelayanan_id;
                END IF;
                RETURN NEW;
            END
            $BODY$
              LANGUAGE plpgsql VOLATILE
              COST 100;
        ');

        $this->execute('
            DROP TRIGGER IF EXISTS "update_jasa_dokter" ON "public"."tindakanpelayanan_t";
        ');

        $this->execute('
            CREATE TRIGGER "update_jasa_dokter" 
            AFTER UPDATE OF "tarif_diskon" ON "public"."tindakanpelayanan_t"
            FOR EACH ROW
            EXECUTE PROCEDURE "public"."tindakanpelayanan_update_jasa_dokter"();
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220328_071526_migrate_ODH500_trigger_diskon_tindakan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220328_071526_migrate_ODH500_trigger_diskon_tindakan cannot be reverted.\n";

        return false;
    }
    */
}
