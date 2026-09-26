<?php

use yii\db\Migration;

/**
 * Class m210125_100522_migrate_2998_20210125_tabel_golonganumur_m
 */
class m210125_100522_migrate_2998_20210125_tabel_golonganumur_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("TRUNCATE TABLE golonganumur_m RESTART IDENTITY;");
        $this->execute("
            INSERT INTO public.golonganumur_m(golonganumur_id, golonganumur_nama, golonganumur_namalainnya, golonganumur_minimal, golonganumur_maksimal, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
            (1, '0 - ≤6 hari', '0 - ≤6 hari', '0', '6', NULL, '2019-07-12 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (2, '>6 - ≤28 hari', '>6 - ≤28 hari', '7', '28', NULL, '2019-07-12 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (3, '>28 - ≤1 tahun', '>28 - ≤1 tahun', '29', '366', NULL, '2019-07-12 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (4, '>1- ≤4 tahun', '>1- ≤4 tahun', '367', '1462', NULL, '2019-07-12 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (6, '>4 - ≤ 14 tahun', '>4 - ≤ 14 tahun', '1463', '5115', NULL, '2019-07-12 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (7, '>14 - ≤24 tahun', '>14 - ≤24 tahun', '5116', '8767', NULL, '2019-07-12 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (8, '>24 - ≤ 44 tahun', '>24 - ≤ 44 tahun', '8768', '16072', NULL, '2019-07-12 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (9, '>44 - ≤ 64 tahun', '>44 - ≤ 64 tahun', '16073', '23377', NULL, '2019-07-12 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
            (10, '> 64 tahun', '> 64 tahun', '23378', '54789', NULL, '2019-07-12 00:00:00', NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);

        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210125_100522_migrate_2998_20210125_tabel_golonganumur_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210125_100522_migrate_2998_20210125_tabel_golonganumur_m cannot be reverted.\n";

        return false;
    }
    */
}
