<?php

use yii\db\Migration;

/**
 * Class m250318_045439_migrate_DSV_1748_alter_table_snomed_m
 */
class m250318_045439_migrate_DSV_1748_alter_table_snomed_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.snomad_m ADD IF NOT EXISTS snomed_category varchar(255) NULL;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250318_045439_migrate_DSV_1748_alter_table_snomed_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250318_045439_migrate_DSV_1748_alter_table_snomed_m cannot be reverted.\n";

        return false;
    }
    */
}
