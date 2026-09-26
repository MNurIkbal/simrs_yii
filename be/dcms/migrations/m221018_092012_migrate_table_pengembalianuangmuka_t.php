<?php

use yii\db\Migration;

/**
 * Class m221018_092012_migrate_table_pengembalianuangmuka_t
 */
class m221018_092012_migrate_table_pengembalianuangmuka_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE pengembalianuangmuka_t ADD IF NOT EXISTS keterangan TEXT;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221018_092012_migrate_table_pengembalianuangmuka_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221018_092012_migrate_table_pengembalianuangmuka_t cannot be reverted.\n";

        return false;
    }
    */
}
