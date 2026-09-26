<?php

use yii\db\Migration;

/**
 * Class m220221_095915_migrate_DHC204_disable_tregger_diskon_mcu
 */
class m220221_095915_migrate_DHC204_disable_tregger_diskon_mcu extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DO
            $do$
            BEGIN
               IF EXISTS (
                   SELECT 1
                   FROM   pg_trigger
                   WHERE  NOT tgisinternal AND tgname = \'tindakansudahbayar_t_discount_cancel\'
                   ) 
                 THEN
                  ALTER TABLE "public"."tindakansudahbayar_t" DISABLE TRIGGER "tindakansudahbayar_t_discount_cancel";
               END IF;
            END
            $do$;
        ');

        $this->execute('
            DO
            $do$
            BEGIN
               IF EXISTS (
                   SELECT 1
                   FROM   pg_trigger
                   WHERE  NOT tgisinternal AND tgname = \'tindakansudahbayar_t_discount_insert\'
                   ) 
                 THEN
                  ALTER TABLE "public"."tindakansudahbayar_t" DISABLE TRIGGER "tindakansudahbayar_t_discount_insert";
               END IF;
            END
            $do$;
            
        ');

        $this->execute('
            DO
            $do$
            BEGIN
               IF EXISTS (
                   SELECT 1
                   FROM   pg_trigger
                   WHERE  NOT tgisinternal AND tgname = \'pembulatan_payer\'
                   ) 
                 THEN
                  ALTER TABLE "public"."pembayaranpelayanan_t" DISABLE TRIGGER "pembulatan_payer";
               END IF;
            END
            $do$;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220221_095915_migrate_DHC204_disable_tregger_diskon_mcu cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220221_095915_migrate_DHC204_disable_tregger_diskon_mcu cannot be reverted.\n";

        return false;
    }
    */
}
