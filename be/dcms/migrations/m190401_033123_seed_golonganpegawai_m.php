<?php

use yii\db\Migration;

/**
 * Class m190401_033123_seed_golonganpegawai_m
 */
class m190401_033123_seed_golonganpegawai_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            TRUNCATE TABLE golonganpegawai_m RESTART IDENTITY;
        ');

        $this->execute('
                INSERT INTO "public"."golonganpegawai_m"("golonganpegawai_id", "golonganpegawai_nama", "golonganpegawai_namalainnya", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES 
                (1, \'Ia\', \'Ia\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (2, \'IIa\', \'IIa\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (3, \'IIIa\', \'IIIa\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (4, \'IVa\', \'IVa\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
                (5, \'IVb\', \'IVb\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_033123_seed_golonganpegawai_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_033123_seed_golonganpegawai_m cannot be reverted.\n";

        return false;
    }
    */
}
