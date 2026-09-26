<?php

use yii\db\Migration;

/**
 * Class m250617_092348_GLBJ553_create_seeder_lookup_m_statuscheckin
 */
class m250617_092348_GLBJ553_create_seeder_lookup_m_statuscheckin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DELETE FROM lookup_m
            WHERE lookup_type = \'status_checkin\';
        ');

        $this->execute('
            INSERT INTO lookup_m (lookup_type,lookup_name,lookup_value,lookup_urutan,lookup_kode,additional_data,created_date,created_by,modified_count,last_modified_date,last_modified_by,is_deleted,is_active,deleted_date,deleted_by) VALUES
            (\'status_checkin\',\'Sudah Check-in\',\'1\',NULL,NULL,NULL,\'2023-10-24 00:00:00.000\',NULL,NULL,NULL,NULL,false,true,NULL,NULL),
            (\'status_checkin\',\'Belum Check-in\',\'0\',NULL,NULL,NULL,\'2023-10-24 00:00:00.000\',NULL,NULL,NULL,NULL,false,true,NULL,NULL);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250617_092348_GLBJ553_create_seeder_lookup_m_statuscheckin cannot be reverted.\n";

        return false;
    }
    */
}
