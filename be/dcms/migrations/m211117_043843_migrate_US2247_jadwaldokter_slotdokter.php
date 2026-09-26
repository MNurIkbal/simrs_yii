<?php

use yii\db\Migration;

/**
 * Class m211117_043843_migrate_US2247_jadwaldokter_slotdokter
 */
class m211117_043843_migrate_US2247_jadwaldokter_slotdokter extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k" 
            ADD COLUMN IF NOT EXISTS "is_slot_dokter" bool DEFAULT false;
          ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211117_043843_migrate_US2247_jadwaldokter_slotdokter cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211117_043843_migrate_US2247_jadwaldokter_slotdokter cannot be reverted.\n";

        return false;
    }
    */
}
