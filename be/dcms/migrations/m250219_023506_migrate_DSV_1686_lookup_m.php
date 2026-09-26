<?php

use yii\db\Migration;

/**
 * Class m250219_023506_migrate_DSV_1686_lookup_m
 */
class m250219_023506_migrate_DSV_1686_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookup_m
            WHERE lookup_id IN (
            2211,
            2212
            );
        ');

        $this->execute('
            INSERT INTO "public"."lookup_m" ("lookup_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan", "lookup_kode", "additional_data") VALUES 
            (2211, \'form_asmed_ranap\', \'Asesmen Medis Pra Bedah\', \'Asesmen Medis Pra Bedah\', NULL, \'bedah\', NULL),
            (2212, \'form_asmed_ranap\', \'Asesmen Medis Khusus Luka Bakar\', \'Asesmen Medis Khusus Luka Bakar\', NULL, \'luka-bakar\', NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250219_023506_migrate_DSV_1686_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250219_023506_migrate_DSV_1686_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
