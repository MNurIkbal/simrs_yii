<?php

use yii\db\Migration;

/**
 * Class m211228_033606_migrate_master_diagnosa
 */
class m211228_033606_migrate_master_diagnosa extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
      $this->execute("select public.deps_save_and_drop_dependencies('public', 'tabularlist_m');");

      $this->execute('ALTER TABLE "public"."tabularlist_m" 
                      ALTER COLUMN "tabularlist_chapter" TYPE varchar(255) COLLATE "pg_catalog"."default",
                      ALTER COLUMN "tabularlist_block" TYPE varchar(255) COLLATE "pg_catalog"."default",
                      ALTER COLUMN "tabularlist_revisi" TYPE varchar(255) COLLATE "pg_catalog"."default",
                      ALTER COLUMN "tabularlist_versi" TYPE varchar(255) COLLATE "pg_catalog"."default";');
      
      $this->execute("select public.deps_restore_dependencies('public', 'tabularlist_m');");



      $this->execute("select public.deps_save_and_drop_dependencies('public', 'dtd_m');");

      $this->execute('ALTER TABLE "public"."dtd_m" 
                    ALTER COLUMN "dtd_namalainnya" TYPE varchar(255) COLLATE "pg_catalog"."default";');

      $this->execute("select public.deps_restore_dependencies('public', 'dtd_m');");



      $this->execute("select public.deps_save_and_drop_dependencies('public', 'klasifikasidiagnosa_m');");

      $this->execute('ALTER TABLE "public"."klasifikasidiagnosa_m" 
                    ALTER COLUMN "klasifikasidiagnosa_kode" TYPE varchar(255) COLLATE "pg_catalog"."default";');

      $this->execute("select public.deps_restore_dependencies('public', 'klasifikasidiagnosa_m');");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211228_033606_migrate_master_diagnosa cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211228_033606_migrate_master_diagnosa cannot be reverted.\n";

        return false;
    }
    */
}
