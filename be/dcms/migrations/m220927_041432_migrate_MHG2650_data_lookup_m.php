<?php

use yii\db\Migration;

/**
 * Class m220927_041432_migrate_MHG2650_data_lookup_m
 */
class m220927_041432_migrate_MHG2650_data_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookup_m
            WHERE lookup_id IN (
            1290,
            1297);
        ');

        $this->execute('
            INSERT INTO "public"."lookup_m" ("lookup_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan") VALUES 
            (1290, \'jenis_layanan\', \'Jenis Obat\', \'Jenis Obat\', 6),
            (1297, \'jenis_layanan\', \'Biaya Admin Rawat Inap\', \'Biaya Admin Rawat Inap\', 7);            
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220927_041432_migrate_MHG2650_data_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220927_041432_migrate_MHG2650_data_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
