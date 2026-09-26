<?php

use yii\db\Migration;

/**
 * Class m241212_073032_migrate_SKM_808_alter_table_nilairujukan_m
 */
class m241212_073032_migrate_SKM_808_alter_table_nilairujukan_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE public.nilairujukan_m ADD IF NOT EXISTS loinc_id int4;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241212_073032_migrate_SKM_808_alter_table_nilairujukan_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241212_073032_migrate_SKM_808_alter_table_nilairujukan_m cannot be reverted.\n";

        return false;
    }
    */
}
