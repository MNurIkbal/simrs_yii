<?php

use yii\db\Migration;

/**
 * Class m211209_090909_migrate_hotfix_uniquenopobarang_validasipobarang_t
 */
class m211209_090909_migrate_hotfix_uniquenopobarang_validasipobarang_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
  	   $this->execute('ALTER TABLE "public"."validasipobarang_t" DROP CONSTRAINT IF EXISTS "unique_no_pobarang";');
	   
 	   $this->execute('ALTER TABLE "public"."validasipobarang_t" ADD CONSTRAINT unique_no_pobarang UNIQUE ("no_pobarang");');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211209_090909_migrate_hotfix_uniquenopobarang_validasipobarang_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211209_090909_migrate_hotfix_uniquenopobarang_validasipobarang_t cannot be reverted.\n";

        return false;
    }
    */
}
