<?php

use yii\db\Migration;

/**
 * Class m211115_234913_migrate_resepturdetail_t
 */
class m211115_234913_migrate_resepturdetail_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
       $this->execute('ALTER TABLE "public"."resepturdetail_t" ADD COLUMN if not exists "det_medis" float8;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211115_234913_migrate_resepturdetail_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211115_234913_migrate_resepturdetail_t cannot be reverted.\n";

        return false;
    }
    */
}
