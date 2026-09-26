<?php

use yii\db\Migration;

/**
 * Class m190401_021826_seed_asalrujukan_m
 */
class m190401_021826_seed_asalrujukan_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("
            TRUNCATE TABLE asalrujukan_m RESTART IDENTITY;
        ");

        $this->execute('
            INSERT INTO "public"."asalrujukan_m"("asalrujukan_id", "asalrujukan_nama", "asalrujukan_institusi", "asalrujukan_namalainnya", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by", "is_rujukan", "asalrujukan_kode") VALUES 
            (1, \'Datang Sendiri\', NULL, \'Datang Sendiri\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, \'f\', \'8\'),
            (2, \'Rujukan dari Puskesmas\', NULL, \'Rujukan dari Puskesmas\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, \'t\', \'2\'),
            (3, \'Rujukan dari RSU/RSK/RB\', NULL, \'Rujukan dari RSU/RSK/RB\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, \'t\', \'1\'),
            (4, \'Rujukan dari Dr/Drg\', NULL, \'Rujukan dari Dr/Drg\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, \'t\', \'3\'),
            (5, \'Rujukan dari Dr. Spesialis\', NULL, \'Rujukan dari Dr. Spesialis\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, \'t\', \'4\'),
            (6, \'Rujukan dari Tenaga Paramedik\', NULL, \'Rujukan dari Tenaga Paramedik\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, \'t\', \'5\'),
            (7, \'Rujukan Kasus Polisi\', NULL, \'Rujukan Kasus Polisi\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, \'t\', \'6\'),
            (27, \'BPJS\', NULL, \'BPJS\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL, \'f\', \'9\');
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_021826_seed_asalrujukan_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_021826_seed_asalrujukan_m cannot be reverted.\n";

        return false;
    }
    */
}
