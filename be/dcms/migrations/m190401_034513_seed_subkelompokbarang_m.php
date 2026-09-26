<?php

use yii\db\Migration;

/**
 * Class m190401_034513_seed_subkelompokbarang_m
 */
class m190401_034513_seed_subkelompokbarang_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            TRUNCATE TABLE subkelompokbarang_m RESTART IDENTITY;
        ');

        $this->execute('
            INSERT INTO "public"."subkelompokbarang_m"("subkelompokbarang_id", "kelompokbarang_id", "subkelompok_kode", "subkelompok_nama", "subkelompok_namalain", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES 
            (1, 1, \'MBL\', \'Mobil\', NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (15, 5, \'PC\', \'Komputer\', \'Komputer\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (13, 15, \'ATK\', \'Alat Tulis Kantor\', \'Alat Tulis Kantor\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (17, 18, \'syr\', \'Sayuran\', \'Sayuran\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (12, 20, \'LGK\', \'Logistik\', NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_034513_seed_subkelompokbarang_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_034513_seed_subkelompokbarang_m cannot be reverted.\n";

        return false;
    }
    */
}
