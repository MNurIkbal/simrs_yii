<?php

use yii\db\Migration;

/**
 * Class m251115_064003_migrate_DSV_2158_alter_table_rujukanbantaran_t
 */
class m251115_064003_migrate_DSV_2158_alter_table_rujukanbantaran_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.rujukanbantaran_t ADD COLUMN IF NOT EXISTS pendaftaranol_id int4 NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251115_064003_migrate_DSV_2158_alter_table_rujukanbantaran_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251115_064003_migrate_DSV_2158_alter_table_rujukanbantaran_t cannot be reverted.\n";

        return false;
    }
    */
}
