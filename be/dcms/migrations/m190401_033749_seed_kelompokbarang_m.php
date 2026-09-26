<?php

use yii\db\Migration;

/**
 * Class m190401_033749_seed_kelompokbarang_m
 */
class m190401_033749_seed_kelompokbarang_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            TRUNCATE TABLE kelompokbarang_m RESTART IDENTITY;
        ');

        $this->execute('
            INSERT INTO "public"."kelompokbarang_m"("kelompokbarang_id", "kelompokbarang_kode", "kelompokbarang_nama", "kelompokbarang_namalain", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES 
            (1, \'KEN\', \'Kendaraan\', \'KENDARAAN\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (5, \'ELEK\', \'Elektronik\', \'Elektronik\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (15, \'PK\', \'Peralatan Kantor\', \'Peralatan Kantor\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (18, \'BM\', \'Bahan Makanan\', \'Makanan\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (20, \'PD\', \'Peralatan Dapur\', \'Peralatan Dapur\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (23, \'MKN\', \'Makanan\', \'Makanan\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_033749_seed_kelompokbarang_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_033749_seed_kelompokbarang_m cannot be reverted.\n";

        return false;
    }
    */
}
