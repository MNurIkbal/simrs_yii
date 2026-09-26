<?php

use yii\db\Migration;

/**
 * Class m220511_055645_migrate_tindakanpelayanan_t
 */
class m220511_055645_migrate_tindakanpelayanan_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."tindakanpelayanan_t" 
                              ADD COLUMN if not exists "programterapidetail_id" int4,
                              ADD COLUMN if not exists "programterapi_id" int4;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220511_055645_migrate_tindakanpelayanan_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220511_055645_migrate_tindakanpelayanan_t cannot be reverted.\n";

        return false;
    }
    */
}
