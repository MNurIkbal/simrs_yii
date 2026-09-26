<?php

use yii\db\Migration;

/**
 * Class m220202_042034_migrate_jkn_lookup_versionjkn
 */
class m220202_042034_migrate_jkn_lookup_versionjkn extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            DELETE from lookup_m where lookup_id = 1080;
        ");

        $this->execute("
            DELETE from lookup_m where lookup_id = 1140;
        ");

        $this->execute("
            INSERT INTO \"public\".\"lookup_m\"(\"lookup_id\", \"lookup_type\", \"lookup_name\", \"lookup_value\", \"lookup_urutan\", \"lookup_kode\", \"additional_data\", \"created_date\", \"created_by\", \"modified_count\", \"last_modified_date\", \"last_modified_by\", \"is_deleted\", \"is_active\", \"deleted_date\", \"deleted_by\") VALUES (1080, 'bpjs', 'version', '1', NULL, NULL, NULL, '2021-09-27 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
        ");

        $this->execute("
            INSERT INTO \"public\".\"lookup_m\"(\"lookup_id\", \"lookup_type\", \"lookup_name\", \"lookup_value\", \"lookup_urutan\", \"lookup_kode\", \"additional_data\", \"created_date\", \"created_by\", \"modified_count\", \"last_modified_date\", \"last_modified_by\", \"is_deleted\", \"is_active\", \"deleted_date\", \"deleted_by\") VALUES (1140, 'bpjs', 'version_jkn', '2.0', NULL, NULL, NULL, '2022-02-02 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);
        ");
    }   

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220202_042034_migrate_jkn_lookup_versionjkn cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220202_042034_migrate_jkn_lookup_versionjkn cannot be reverted.\n";

        return false;
    }
    */
}
