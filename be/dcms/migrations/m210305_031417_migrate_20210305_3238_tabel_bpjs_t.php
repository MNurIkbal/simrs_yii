<?php

use yii\db\Migration;

/**
 * Class m210305_031417_migrate_20210305_3238_tabel_bpjs_t
 */
class m210305_031417_migrate_20210305_3238_tabel_bpjs_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."bpjs_t"
            ADD COLUMN IF NOT EXISTS"kode_dpjp_melayani" varchar(50) COLLATE "pg_catalog"."default";
            ');
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210305_031417_migrate_20210305_3238_tabel_bpjs_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210305_031417_migrate_20210305_3238_tabel_bpjs_t cannot be reverted.\n";

        return false;
    }
    */
}
