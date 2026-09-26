<?php

use yii\db\Migration;

/**
 * Class m220510_042311_migrate_konfigfarmasi_k
 */
class m220510_042311_migrate_konfigfarmasi_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('ALTER TABLE "public"."konfigfarmasi_k" 
                            ADD COLUMN if not exists "basecalc_config" json;');

         $this->execute('UPDATE "public"."konfigfarmasi_k" SET "basecalc_config" = \'{"pemakaian_ruangan":"true","bmhp":"true","mutasi":"true"}\' WHERE "konfigfarmasi_id" = 1;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220510_042311_migrate_konfigfarmasi_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220510_042311_migrate_konfigfarmasi_k cannot be reverted.\n";

        return false;
    }
    */
}
