<?php

use yii\db\Migration;

/**
 * Class m210330_015528_migrate_20210330_stokopnamedetail_t
 */
class m210330_015528_migrate_20210330_stokopnamedetail_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."stokopnamedetail_t" ALTER COLUMN "tglkadaluarsa" DROP NOT NULL;');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210330_015528_migrate_20210330_stokopnamedetail_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210330_015528_migrate_20210330_stokopnamedetail_t cannot be reverted.\n";

        return false;
    }
    */
}
