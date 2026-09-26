<?php

use yii\db\Migration;

/**
 * Class m200928_034848_migrate_20200928_validasipoobat
 */
class m200928_034848_migrate_20200928_validasipoobat extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."validasipoobat_t" ADD COLUMN if not exists "catatan" text COLLATE "pg_catalog"."default";');
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200928_034848_migrate_20200928_validasipoobat cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200928_034848_migrate_20200928_validasipoobat cannot be reverted.\n";

        return false;
    }
    */
}
