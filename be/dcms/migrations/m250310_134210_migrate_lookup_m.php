<?php

use yii\db\Migration;

/**
 * Class m250310_134210_migrate_lookup_m
 */
class m250310_134210_migrate_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookup_m
            WHERE lookup_id IN (
            2213,
            2214,
            2215
            );
        ');

        $this->execute('
            INSERT INTO "public"."lookup_m" ("lookup_id", "lookup_type", "lookup_name", "lookup_value", "lookup_urutan", "lookup_kode", "additional_data") VALUES 
            (2213, \'form_asmed_ranap\', \'Asesmen Medis Pasien Ginekologi\', \'Asesmen Medis Pasien Ginekologi\', NULL, \'ginekologi\', NULL),
            (2214, \'form_asmed_ranap\', \'Asesmen Medis Spesialis Kebidanan dan Kandungan\', \'Asesmen Medis Spesialis Kebidanan dan Kandungan\', NULL, \'kebidanan\', NULL),
            (2215, \'form_asmed_ranap\', \'Asesmen Medis Mata\', \'Asesmen Medis Mata\', NULL, \'mata\', NULL)
            ;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250310_134210_migrate_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250310_134210_migrate_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
