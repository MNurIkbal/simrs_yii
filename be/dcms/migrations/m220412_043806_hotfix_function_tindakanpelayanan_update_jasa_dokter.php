<?php

use yii\db\Migration;

/**
 * Class m220412_043806_hotfix_function_tindakanpelayanan_update_jasa_dokter
 */
class m220412_043806_hotfix_function_tindakanpelayanan_update_jasa_dokter extends Migration
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
                
                IF(COALESCE(vtarif_tindakan, 0)=0)
                THEN
                    vtarif_tindakan := 1;
                END IF;
                
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
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220412_043806_hotfix_function_tindakanpelayanan_update_jasa_dokter cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220412_043806_hotfix_function_tindakanpelayanan_update_jasa_dokter cannot be reverted.\n";

        return false;
    }
    */
}
