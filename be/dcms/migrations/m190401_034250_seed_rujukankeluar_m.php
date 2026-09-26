<?php

use yii\db\Migration;

/**
 * Class m190401_034250_seed_rujukankeluar_m
 */
class m190401_034250_seed_rujukankeluar_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            TRUNCATE TABLE rujukankeluar_m RESTART IDENTITY;
        ');

        $this->execute('
            INSERT INTO "public"."rujukankeluar_m"("rujukankeluar_id", "asalrujukan_id", "rumahsakit_rujukan", "alamat_rsrujukan", "telp_fax", "additional_data", "created_date", "created_by", "modified_count", "last_modified_date", "last_modified_by", "is_deleted", "is_active", "deleted_date", "deleted_by") VALUES 
            (1, 1, \'Rumah Sakit Tri Sakti Jakarta\', \'Jakarta Timur\', \'021-23929\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (4, 3, \'RSMM\', \'Bogor\', \'0123810\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (6, 2, \'RS\', \'abcd\', \'089123123\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (9, 25, \'RS Hasan Sadikin\', \'Jl Pasirkaliki\', \'0225335222\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (10, 1, \'RS Advent\', \'Jl Cihampelas\', \'0226293733\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL),
            (11, 1, \'RS Immanuel\', \'Jl Astana anyar\', \'02262533\', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, \'f\', \'t\', NULL, NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190401_034250_seed_rujukankeluar_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190401_034250_seed_rujukankeluar_m cannot be reverted.\n";

        return false;
    }
    */
}
