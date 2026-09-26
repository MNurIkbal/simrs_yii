<?php

use yii\db\Migration;

/**
 * Class m190401_033908_seed_perujuk_m
 */
class m190401_033908_seed_perujuk_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            TRUNCATE TABLE perujuk_m RESTART IDENTITY;
        ');

        $this->execute('
            INSERT INTO "public"."perujuk_m"("perujuk_id", "asalrujukan_id", "namaperujuk", "spesialis", "alamatlengkap", "notelp", "kodeppk", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES 
            (12, 4, \'Dr. Gusti Ngurah Rai\', \'Penyakit Dalam\', \'Bangli - Bali\', \'08712737\', NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (29, 5, \'RSUD Mandiri\', \'Mata\', \'Jalan Pak Gatot V No 12\', \'06198043685995\', NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (31, 3, \'RSUD Mandiri\', \'Hidung\', \'Jalan Pak Gatot V No 12\', \'06198043685995\', NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (32, 5, \'RSUD Mandiri\', \'Mata\', \'Jalan Pak Gatot V No 12\', \'61980436859995\', NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (36, 3, \'RSUD Bandung\', \'Hidung\', \'Jalan Pak Gatot VIII No 87\', \'08124516828\', NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (37, 27, \'Faskes tingkat 1\', NULL, NULL, NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (38, 27, \'Faskes tingkat 2 (RS)\', NULL, NULL, NULL, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_033908_seed_perujuk_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_033908_seed_perujuk_m cannot be reverted.\n";

        return false;
    }
    */
}
