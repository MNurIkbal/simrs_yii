<?php

use yii\db\Migration;

/**
 * Class m210113_095851_migrate_20210113_sy_lookup_m_bahasa_sehari
 */
class m210113_095851_migrate_20210113_sy_lookup_m_bahasa_sehari extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE from lookup_m where lookup_type='bahasa_sehari';
            ");
        $this->execute("INSERT INTO public.lookup_m(lookup_id, lookup_type, lookup_name, lookup_value, lookup_urutan, lookup_kode, additional_data, created_date, created_by, modified_count, last_modified_date, last_modified_by, is_deleted, is_active, deleted_date, deleted_by) VALUES
(983, 'bahasa_sehari', 'Sunda', 'Sunda', 1, '', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(984, 'bahasa_sehari', 'Jawa', 'Jawa', 2, '', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(985, 'bahasa_sehari', 'Batak', 'Batak', 3, '', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(986, 'bahasa_sehari', 'Melayu', 'Melayu', 4, '', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(987, 'bahasa_sehari', 'Madura', 'Madura', 5, '', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(988, 'bahasa_sehari', 'Bugis', 'Bugis', 6, '', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL),
(989, 'bahasa_sehari', 'Betawi', 'Betawi', 7, '', NULL, CURRENT_DATE, NULL, NULL, NULL, NULL, 'f', 't', NULL, NULL);

            ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210113_095851_migrate_20210113_sy_lookup_m_bahasa_sehari cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210113_095851_migrate_20210113_sy_lookup_m_bahasa_sehari cannot be reverted.\n";

        return false;
    }
    */
}
