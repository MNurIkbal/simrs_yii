<?php

use yii\db\Migration;

/**
 * Class m251113_144251_migrate_konfigsystem_k_konfig_sbar
 */
class m251113_144251_migrate_konfigsystem_k_konfig_sbar extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.konfigsystem_k
            ADD COLUMN IF NOT EXISTS konfig_sbar text COLLATE \"pg_catalog\".\"default\"
            DEFAULT '{\"RJ\":false,\"RI\":false,\"RD\":false}'
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251113_144251_migrate_konfigsystem_k_konfig_sbar cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251113_144251_migrate_konfigsystem_k_konfig_sbar cannot be reverted.\n";

        return false;
    }
    */
}
