<?php

use yii\db\Migration;

/**
 * Class m220312_072945_migrate_DHC126_table_buatjanjipoli_t
 */
class m220312_072945_migrate_DHC126_table_buatjanjipoli_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            ALTER TABLE "public"."buatjanjipoli_t" ALTER COLUMN "antrian_id" DROP NOT NULL;
        ');

        $this->execute('
            ALTER TABLE buatjanjipoli_t ADD IF NOT EXISTS ruanganasal_id int4;
        ');

        $this->execute('
            ALTER TABLE buatjanjipoli_t ADD IF NOT EXISTS pegawaiasal_id int4;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220312_072945_migrate_DHC126_table_buatjanjipoli_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220312_072945_migrate_DHC126_table_buatjanjipoli_t cannot be reverted.\n";

        return false;
    }
    */
}
