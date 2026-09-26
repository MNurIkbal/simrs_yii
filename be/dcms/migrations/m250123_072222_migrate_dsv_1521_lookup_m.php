<?php

use yii\db\Migration;

/**
 * Class m250123_072222_migrate_dsv_1521_lookup_m
 */
class m250123_072222_migrate_dsv_1521_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookup_m
            WHERE lookup_id IN (
            2209
            );
        ');

        $this->execute('
            INSERT INTO "public"."lookup_m" ("lookup_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan", "lookup_kode", "additional_data") VALUES 
            (2209, \'form_asmed_ranap\', \'Asesmen Pasien Dengan Gangguan Emosional/Pasien Psikiatris\', \'Asesmen Pasien Dengan Gangguan Emosional/Pasien Psikiatris\', NULL, \'psikiatris\', NULL);

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250123_072222_migrate_dsv_1521_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250123_072222_migrate_dsv_1521_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
