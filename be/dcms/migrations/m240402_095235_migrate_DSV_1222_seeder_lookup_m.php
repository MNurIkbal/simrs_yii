<?php

use yii\db\Migration;

/**
 * Class m240402_095235_migrate_DSV_1222_seeder_lookup_m
 */
class m240402_095235_migrate_DSV_1222_seeder_lookup_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookup_m WHERE lookup_id IN (2161,2162,2164,2171,2170)");

        $this->execute("
            INSERT INTO public.lookup_m (lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES
            (2161, 'sumber_penerimaan', 'BLUD', 'BLUD', NULL, NULL, NULL, '2024-04-02 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (2162, 'sumber_penerimaan', 'APBD', 'APBD', NULL, NULL, NULL, '2024-04-02 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (2164, 'sumber_penerimaan', 'DINKES', 'DINKES', NULL, NULL, NULL, '2024-04-02 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (2171, 'sumber_penerimaan', 'LAIN-LAIN', 'LAIN-LAIN', NULL, NULL, NULL, '2024-04-02 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL),
            (2170, 'sumber_penerimaan', 'RS. LAIN', 'RS. LAIN', NULL, NULL, NULL, '2024-04-02 00:00:00.000', NULL, NULL, NULL, NULL, false, true, NULL, NULL);
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240402_095235_migrate_DSV_1222_seeder_lookup_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240402_095235_migrate_DSV_1222_seeder_lookup_m cannot be reverted.\n";

        return false;
    }
    */
}
