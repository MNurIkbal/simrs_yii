<?php

use yii\db\Migration;

/**
 * Class m220331_022034_migrate_cdh246_dropcolum_isdonasi_penerimaansupdetail
 */
class m220331_022034_migrate_cdh246_dropcolum_isdonasi_penerimaansupdetail extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		
		
        $this->execute('
			ALTER TABLE "public"."penerimaansuppdetail_t" 
			  DROP COLUMN "is_donasi";
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220331_022034_migrate_cdh246_dropcolum_isdonasi_penerimaansupdetail cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220331_022034_migrate_cdh246_dropcolum_isdonasi_penerimaansupdetail cannot be reverted.\n";

        return false;
    }
    */
}
