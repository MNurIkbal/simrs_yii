<?php

use yii\db\Migration;

/**
 * Class m231218_070311_migrate_skema_intgrasiesiantri_table_klasifikasipasien_m_addcolumn_is_potong_stok_kuota
 */
class m231218_070311_migrate_skema_intgrasiesiantri_table_klasifikasipasien_m_addcolumn_is_potong_stok_kuota extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE klasifikasipasien_m ADD IF NOT EXISTS is_potong_stok_kuota bool NOT NULL DEFAULT true;
        ');
		
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231218_070311_migrate_skema_intgrasiesiantri_table_klasifikasipasien_m_addcolumn_is_potong_stok_kuota cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m231218_070311_migrate_skema_intgrasiesiantri_table_klasifikasipasien_m_addcolumn_is_potong_stok_kuota cannot be reverted.\n";

        return false;
    }
    */
}
