<?php

use yii\db\Migration;

/**
 * Class m201211_074544_migrate_mhbg_20201211_tabel_konfigsystem_k
 */
class m201211_074544_migrate_mhbg_20201211_tabel_konfigsystem_k extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k" 
  ADD COLUMN IF NOT EXISTS "is_limit_tagihan" bool DEFAULT false;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201211_074544_migrate_mhbg_20201211_tabel_konfigsystem_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201211_074544_migrate_mhbg_20201211_tabel_konfigsystem_k cannot be reverted.\n";

        return false;
    }
    */
}
