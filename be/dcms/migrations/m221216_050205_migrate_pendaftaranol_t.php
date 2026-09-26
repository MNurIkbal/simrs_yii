<?php

use yii\db\Migration;

/**
 * Class m221216_050205_migrate_pendaftaranol_t
 */
class m221216_050205_migrate_pendaftaranol_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.pendaftaranol_t ADD IF NOT EXISTS is_postranap bool NULL DEFAULT false;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221216_050205_migrate_pendaftaranol_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221216_050205_migrate_pendaftaranol_t cannot be reverted.\n";

        return false;
    }
    */
}
