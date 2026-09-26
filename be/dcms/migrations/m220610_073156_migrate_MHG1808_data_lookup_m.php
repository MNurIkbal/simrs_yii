<?php

use yii\db\Migration;

/**
 * Class m220610_073156_migrate_MHG1808_data_lookup_m
 */
class m220610_073156_migrate_MHG1808_data_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookup_m
            WHERE lookup_id IN (
            1211,
            1212
            );
        ');

        $this->execute('
            INSERT INTO "public"."lookup_m" ("lookup_id", "lookup_type", "lookup_name", "lookup_value") VALUES (1211, \'jenis_skrining_nrs\', \'Skrining Lanjut 1\', \'Skrining Lanjut 1\'),
            (1212, \'jenis_skrining_nrs\', \'Skrining Lanjut 2\', \'Skrining Lanjut 2\');
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220610_073156_migrate_MHG1808_data_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220610_073156_migrate_MHG1808_data_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
