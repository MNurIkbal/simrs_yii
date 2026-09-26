<?php

use yii\db\Migration;

/**
 * Class m190401_022346_seed_bagiantubuh_m
 */
class m190401_022346_seed_bagiantubuh_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            TRUNCATE TABLE bagiantubuh_m RESTART IDENTITY;
        ');
        $this->execute('
            INSERT INTO "public"."bagiantubuh_m"("bagiantubuh_id", "namabagtubuh", "bagtubuh_namalain", "kordinat_x", "kordinat_y", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES 
            (1, \'Dada\', \'Dada\', 0, 0, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (2, \'Hidung\', \'Hidung\', 0, 0, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (3, \'Kaki\', \'Kaki\', 0, 0, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (4, \'Kelamin\', \'Kelamin\', 0, 0, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (5, \'Kepala\', \'Kepala\', 0, 0, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (6, \'Leher\', \'Leher\', 0, 0, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (7, \'Mata\', \'Mata\', 0, 0, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (8, \'Mulut\', \'Mulut\', 0, 0, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (9, \'Perut\', \'Perut\', 0, 0, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (10, \'Tangan\', \'Tangan\', 0, 0, NULL, \'2019-03-19\', NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_022346_seed_bagiantubuh_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_022346_seed_bagiantubuh_m cannot be reverted.\n";

        return false;
    }
    */
}
