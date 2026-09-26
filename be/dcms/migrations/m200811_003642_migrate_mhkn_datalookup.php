<?php

use yii\db\Migration;

/**
 * Class m200811_003642_migrate_mhkn_datalookup
 */
class m200811_003642_migrate_mhkn_datalookup extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookup_m WHERE lookup_type='nilai_pembulatan';");

        $this->execute("
        INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES 
(714, 'nilai_pembulatan', '10', '10', 1, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(715, 'nilai_pembulatan', '50', '50', 2, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(716, 'nilai_pembulatan', '100', '100', 3, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(717, 'nilai_pembulatan', '500', '500', 4, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(718, 'nilai_pembulatan', '1000', '1000', 5, NULL, NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);");
      
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200811_003642_migrate_mhkn_datalookup cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200811_003642_migrate_mhkn_datalookup cannot be reverted.\n";

        return false;
    }
    */
}
