<?php

use yii\db\Migration;

/**
 * Class m220919_095640_migrate_MHG3385_table_tariftindakan_m
 */
class m220919_095640_migrate_MHG3385_table_tariftindakan_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE tariftindakan_m ADD IF NOT EXISTS ruangan_id int4;
        ');

        $this->execute('
            ALTER TABLE tariftindakan_m ADD IF NOT EXISTS persentase_komponen DECIMAL(15,2);
        ');

        $this->execute('
            ALTER TABLE tariftindakan_m ADD IF NOT EXISTS is_persentase BOOLEAN DEFAULT FALSE;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220919_095640_migrate_MHG3385_table_tariftindakan_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220919_095640_migrate_MHG3385_table_tariftindakan_m cannot be reverted.\n";

        return false;
    }
    */
}
