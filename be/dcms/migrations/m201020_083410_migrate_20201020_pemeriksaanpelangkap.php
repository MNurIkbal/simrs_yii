<?php

use yii\db\Migration;

/**
 * Class m201020_083410_migrate_20201020_pemeriksaanpelangkap
 */
class m201020_083410_migrate_20201020_pemeriksaanpelangkap extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $this->execute('ALTER TABLE "public"."pemeriksaanpelengkap_t" ADD IF NOT EXISTS "ruangan_id" int4;');

    $this->execute('select public.deps_save_and_drop_dependencies(\'public\', \'pemeriksaanpelengkap_t\');');
    
    $this->execute('ALTER TABLE "public"."pemeriksaanpelengkap_t" 
  ALTER COLUMN "qty" TYPE float8 USING "qty"::float8;');

    $this->execute('select public.deps_restore_dependencies(\'public\', \'pemeriksaanpelengkap_t\');');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201020_083410_migrate_20201020_pemeriksaanpelangkap cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201020_083410_migrate_20201020_pemeriksaanpelangkap cannot be reverted.\n";

        return false;
    }
    */
}
