<?php

use yii\db\Migration;

/**
 * Class m210107_073335_migrate_live_20210107_tabel_konfigsystem_k
 */
class m210107_073335_migrate_live_20210107_tabel_konfigsystem_k extends Migration
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
        echo "m210107_073335_migrate_live_20210107_tabel_konfigsystem_k cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210107_073335_migrate_live_20210107_tabel_konfigsystem_k cannot be reverted.\n";

        return false;
    }
    */
}
