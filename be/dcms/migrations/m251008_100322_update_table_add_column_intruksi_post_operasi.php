<?php

use yii\db\Migration;

/**
 * Class m251008_100322_update_table_add_column_intruksi_post_operasi
 */
class m251008_100322_update_table_add_column_intruksi_post_operasi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."laporanoperasi_r" 
                      ADD COLUMN IF NOT EXISTS "intruksi_post_operasi" TEXT COLLATE "pg_catalog"."default";');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m251008_100322_update_table_add_column_intruksi_post_operasi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m251008_100322_update_table_add_column_intruksi_post_operasi cannot be reverted.\n";

        return false;
    }
    */
}
