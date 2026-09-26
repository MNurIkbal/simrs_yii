<?php

use yii\db\Migration;

/**
 * Class m211221_081507_migrate_pegawai_m
 */
class m211221_081507_migrate_pegawai_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("select public.deps_save_and_drop_dependencies('public', 'pegawai_m');");

        $this->execute('ALTER TABLE "public"."pegawai_m" 
            ALTER COLUMN "nama_pegawai" TYPE varchar(255) COLLATE "pg_catalog"."default";');

        $this->execute("select public.deps_restore_dependencies('public', 'pegawai_m');");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211221_081507_migrate_pegawai_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211221_081507_migrate_pegawai_m cannot be reverted.\n";

        return false;
    }
    */
}
