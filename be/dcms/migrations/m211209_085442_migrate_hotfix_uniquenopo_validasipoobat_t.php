<?php

use yii\db\Migration;

/**
 * Class m211209_085442_migrate_hotfix_uniquenopo_validasipoobat_t
 */
class m211209_085442_migrate_hotfix_uniquenopo_validasipoobat_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
 	   $this->execute('ALTER TABLE "public"."validasipoobat_t" DROP CONSTRAINT IF EXISTS "unique_no_poobat";');
	   
	   $this->execute('ALTER TABLE "public"."validasipoobat_t" ADD CONSTRAINT unique_no_poobat UNIQUE ("no_poobat");');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211209_085442_migrate_hotfix_uniquenopo_validasipoobat_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211209_085442_migrate_hotfix_uniquenopo_validasipoobat_t cannot be reverted.\n";

        return false;
    }
    */
}
