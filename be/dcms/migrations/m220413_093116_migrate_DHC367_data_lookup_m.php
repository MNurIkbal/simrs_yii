<?php

use yii\db\Migration;

/**
 * Class m220413_093116_migrate_DHC367_data_lookup_m
 */
class m220413_093116_migrate_DHC367_data_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookup_m
            WHERE lookup_id IN (
                1186,
                1187,
                1188,
                1189
            );
        ');

        $this->execute('
            INSERT INTO "public"."lookup_m"("lookup_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan", "lookup_kode", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES (1186, \'jenis_kamar\', \'Isolasi\', \'Isolasi\', 1, NULL, NULL, \'2022-04-01 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
             (1187, \'jenis_kamar\', \'ICU\', \'ICU\', 2, NULL, NULL, \'2022-04-01 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
             (1188, \'jenis_kamar\', \'ICCU\', \'ICCU\', 3, NULL, NULL, \'2022-04-01 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
             (1189, \'jenis_kamar\', \'NICU/PICU\', \'NICU/PICU\', 4, NULL, NULL, \'2022-04-01 00:00:00\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220413_093116_migrate_DHC367_data_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220413_093116_migrate_DHC367_data_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
