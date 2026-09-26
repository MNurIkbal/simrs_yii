<?php

use yii\db\Migration;

/**
 * Class m251030_101932_AUTODAFTAR_create_seeder_lookupm_defaultpenjamin
 */
class m251030_101932_AUTODAFTAR_create_seeder_lookupm_defaultpenjamin extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("DELETE FROM lookup_m WHERE lookup_type = 'default_penjamin_id_by_carabayar';");

        $this->execute("
            INSERT INTO lookup_m (lookup_type,lookup_name,lookup_value,lookup_urutan,lookup_kode,additional_data,created_date,created_by,modified_count,last_modified_date,last_modified_by,is_deleted,is_active,deleted_date,deleted_by) VALUES
	        ('default_penjamin_id_by_carabayar','BPJS ADIRA DINAMIKA MULTI FINANCE, PT','21',1,NULL,'{\"carabayar_id\": 6}','2025-10-30 00:00:00.000',NULL,NULL,NULL,NULL,false,true,NULL,NULL)
        ");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251030_101932_AUTODAFTAR_create_seeder_lookupm_defaultpenjamin cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251030_101932_AUTODAFTAR_create_seeder_lookupm_defaultpenjamin cannot be reverted.\n";

        return false;
    }
    */
}
