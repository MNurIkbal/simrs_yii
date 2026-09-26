<?php

use yii\db\Migration;

/**
 * Class m221222_085351_migrate_pendaftaranol_t
 */
class m221222_085351_migrate_pendaftaranol_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.pendaftaranol_t ADD IF NOT EXISTS is_cetaktracer bool NULL DEFAULT false;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221222_085351_migrate_pendaftaranol_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221222_085351_migrate_pendaftaranol_t cannot be reverted.\n";

        return false;
    }
    */
}
