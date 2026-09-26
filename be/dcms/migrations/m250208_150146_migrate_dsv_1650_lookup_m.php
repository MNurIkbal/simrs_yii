<?php

use yii\db\Migration;

/**
 * Class m250208_150146_migrate_dsv_1650_lookup_m
 */
class m250208_150146_migrate_dsv_1650_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookup_m
            WHERE lookup_id IN (
            2210
            );
        ');

        $this->execute('
            INSERT INTO "public"."lookup_m" ("lookup_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan", "lookup_kode", "additional_data") VALUES 
            (2210, \'form_asmed_ranap\', \'Asesmen Medis Spesialis Anak\', \'Asesmen Medis Spesialis Anak\', NULL, \'anak\', NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250208_150146_migrate_dsv_1650_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250208_150146_migrate_dsv_1650_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
