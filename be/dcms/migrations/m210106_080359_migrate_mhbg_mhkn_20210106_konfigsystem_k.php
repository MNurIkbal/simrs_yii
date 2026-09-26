<?php

use yii\db\Migration;

/**
 * Class m210106_080359_migrate_mhbg_mhkn_20210106_konfigsystem_k
 */
class m210106_080359_migrate_mhbg_mhkn_20210106_konfigsystem_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k" 
  ADD COLUMN IF NOT EXISTS "is_hide_alias" bool DEFAULT false;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210106_080359_migrate_mhbg_mhkn_20210106_konfigsystem_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210106_080359_migrate_mhbg_mhkn_20210106_konfigsystem_k cannot be reverted.\n";

        return false;
    }
    */
}
