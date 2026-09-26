<?php

use yii\db\Migration;

/**
 * Class m190327_041235_fgetpersenmargin_id
 */
class m190327_041235_fgetpersenmargin_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE OR REPLACE FUNCTION "public"."fgetpersenmargin_id"("vharga" float8)
              RETURNS "pg_catalog"."int4" AS $BODY$
            DECLARE 
                vmargin float8;
                vkonfigmargin_id int4;
            BEGIN
                SELECT COALESCE(konfigmargin_id,0)
                INTO vkonfigmargin_id
                FROM konfigmargin_k
                WHERE tgl_berlaku <= CURRENT_DATE
                AND is_deleted IS FALSE
                ORDER BY tgl_berlaku DESC
                LIMIT 1;
                
                IF COALESCE(vkonfigmargin_id,0) = 0
                THEN
                    vmargin := 0;
                ELSE
                    SELECT konfigmargindetail_k.konfigmargindetail_id
                    INTO vmargin
                    FROM konfigmargindetail_k
                    WHERE vharga >= harga_min 
                    AND vharga <= harga_max
                    AND konfigmargin_id = vkonfigmargin_id
                    AND is_deleted IS FALSE;
                END IF;
                
                RETURN COALESCE(vmargin,0);
                    
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
        echo "m190327_041235_fgetpersenmargin_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190327_041235_fgetpersenmargin_id cannot be reverted.\n";

        return false;
    }
    */
}
