<?php

use yii\db\Migration;

/**
 * Class m220624_164113_migrate_cssd_cssdpengirimandet_t_add_is_alkes
 */
class m220624_164113_migrate_cssd_cssdpengirimandet_t_add_is_alkes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."cssdpengirimandet_t" 
            ADD COLUMN IF NOT EXISTS "is_alkes" bool DEFAULT true;
          ');

        $this->execute('COMMENT ON COLUMN "public"."cssdpengirimandet_t"."is_alkes" IS \'flaging pembeda barang atau alkes\';
          ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220624_164113_migrate_cssd_cssdpengirimandet_t_add_is_alkes cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220624_164113_migrate_cssd_cssdpengirimandet_t_add_is_alkes cannot be reverted.\n";

        return false;
    }
    */
}
