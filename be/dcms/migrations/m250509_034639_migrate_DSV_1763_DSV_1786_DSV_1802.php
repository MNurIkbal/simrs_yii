<?php

use yii\db\Migration;

/**
 * Class m250509_034639_migrate_DSV_1763_DSV_1786_DSV_1802
 */
class m250509_034639_migrate_DSV_1763_DSV_1786_DSV_1802 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookup_m
            WHERE lookup_id IN (
            2218,
            2225,
            2226
            );
        ');

        $this->execute('
            INSERT INTO "public"."lookup_m" ("lookup_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan", "lookup_kode", "additional_data") VALUES
            (2218, \'form_asmed_ranap\', \'Asesmen Awal Rawat Inap Medis Bedah\', \'Asesmen Awal Rawat Inap Medis Bedah\', NULL, \'medis-bedah\', NULL),
            (2225, \'form_asmed_ranap\', \'Asesmen Ilmu Kesehatan Anak\', \'Asesmen Ilmu Kesehatan Anak\', NULL, \'kesehatan-anak\', NULL),
            (2226, \'form_asmed_ranap\', \'Asesmen Medis Penyakit Dalam\', \'Asesmen Medis Penyakit Dalam\', NULL, \'penyakit-dalam\', NULL)
            ;

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250509_034639_migrate_DSV_1763_DSV_1786_DSV_1802 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250509_034639_migrate_DSV_1763_DSV_1786_DSV_1802 cannot be reverted.\n";

        return false;
    }
    */
}
