<?php

use yii\db\Migration;

/**
 * Class m210608_065750_migrate_20210608_tabel_bpjs_t
 */
class m210608_065750_migrate_20210608_tabel_bpjs_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."bpjs_t" 
          ADD COLUMN IF NOT EXISTS "nama_dpjp_melayani" varchar(255) COLLATE "pg_catalog"."default",
          ADD COLUMN IF NOT EXISTS "nama_ppk_perujuk" varchar(255) COLLATE "pg_catalog"."default",
          ADD COLUMN IF NOT EXISTS "kode_ppk_perujuk" varchar(255) COLLATE "pg_catalog"."default";');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210608_065750_migrate_20210608_tabel_bpjs_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210608_065750_migrate_20210608_tabel_bpjs_t cannot be reverted.\n";

        return false;
    }
    */
}
