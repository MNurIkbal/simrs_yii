<?php

use yii\db\Migration;

/**
 * Class m220725_010846_migrate_MHG1792_table_pasienmasukpenunjang_t
 */
class m220725_010846_migrate_MHG1792_table_pasienmasukpenunjang_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE pasienmasukpenunjang_t ADD IF NOT EXISTS image_link TEXT;
        ');

        $this->execute('
            ALTER TABLE pasienmasukpenunjang_t ADD IF NOT EXISTS image_link_date TIMESTAMP(6);
        ');

        $this->execute('
            ALTER TABLE pasienmasukpenunjang_t ADD IF NOT EXISTS list_result TEXT;
        ');

        $this->execute('
            ALTER TABLE pasienmasukpenunjang_t ADD IF NOT EXISTS is_complete BOOLEAN DEFAULT FALSE;
        ');

        $this->execute('
            ALTER TABLE pasienmasukpenunjang_t ADD IF NOT EXISTS image_link_history TEXT;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220725_010846_migrate_MHG1792_table_pasienmasukpenunjang_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220725_010846_migrate_MHG1792_table_pasienmasukpenunjang_t cannot be reverted.\n";

        return false;
    }
    */
}
