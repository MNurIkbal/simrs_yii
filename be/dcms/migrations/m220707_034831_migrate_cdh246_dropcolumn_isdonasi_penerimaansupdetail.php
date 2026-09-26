<?php

use yii\db\Migration;

/**
 * Class m220707_034831_migrate_cdh246_dropcolumn_isdonasi_penerimaansupdetail
 */
class m220707_034831_migrate_cdh246_dropcolumn_isdonasi_penerimaansupdetail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
			ALTER TABLE "public"."penerimaansuppdetail_t" 
			  DROP COLUMN IF EXISTS "is_donasi";
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220707_034831_migrate_cdh246_dropcolumn_isdonasi_penerimaansupdetail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220707_034831_migrate_cdh246_dropcolumn_isdonasi_penerimaansupdetail cannot be reverted.\n";

        return false;
    }
    */
}
