<?php

use yii\db\Migration;

/**
 * Class m250429_031506_migrate_table_tindakanpelayanan_t
 */
class m250429_031506_migrate_table_tindakanpelayanan_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            CREATE INDEX IF NOT EXISTS "tindakanpelaynan_t_created_date_idx" ON "public"."tindakanpelayanan_t" USING btree (
              (created_date::date) "pg_catalog"."date_ops" ASC NULLS LAST
            );
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m250429_031506_migrate_table_tindakanpelayanan_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250429_031506_migrate_table_tindakanpelayanan_t cannot be reverted.\n";

        return false;
    }
    */
}
