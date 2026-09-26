<?php

use yii\db\Migration;

/**
 * Class m240726_093652_migrate_dsv_1343_stokobatalkes_t
 */
class m240726_093652_migrate_dsv_1343_stokobatalkes_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
           ALTER TABLE "public"."stokobatalkes_t" 
            ADD COLUMN "produksiobatalkesdetail_id" int4,
          ADD COLUMN "produksiobatalkesbahanbaku_id" int4;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240726_093652_migrate_dsv_1343_stokobatalkes_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240726_093652_migrate_dsv_1343_stokobatalkes_t cannot be reverted.\n";

        return false;
    }
    */
}
