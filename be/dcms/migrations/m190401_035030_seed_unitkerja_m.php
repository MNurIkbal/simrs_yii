<?php

use yii\db\Migration;

/**
 * Class m190401_035030_seed_unitkerja_m
 */
class m190401_035030_seed_unitkerja_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            TRUNCATE TABLE unitkerja_m RESTART IDENTITY;
        ');

        $this->execute('
            INSERT INTO "public"."unitkerja_m"("unitkerja_id", "kodeunitkerja", "namaunitkerja", "namalain", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES (1, \'poliklinik\', \'poliklinik\', \'poliklinik\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (2, \'igd\', \'igd\', \'igd\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (3, \'rm\', \'rm\', \'rm\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (4, \'apotek\', \'apotek\', \'apotek\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (5, \'pendaftaran\', \'pendaftaran\', \'pendaftaran\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (6, \'lab\', \'lab\', \'lab\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (7, \'rad\', \'rad\', \'rad\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (8, \'kasir\', \'kasir\', \'kasir\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_035030_seed_unitkerja_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_035030_seed_unitkerja_m cannot be reverted.\n";

        return false;
    }
    */
}
