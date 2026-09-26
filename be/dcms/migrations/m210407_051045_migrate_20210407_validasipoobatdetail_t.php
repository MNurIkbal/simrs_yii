<?php

use yii\db\Migration;

/**
 * Class m210407_051045_migrate_20210407_validasipoobatdetail_t
 */
class m210407_051045_migrate_20210407_validasipoobatdetail_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
$this->execute('ALTER TABLE "public"."validasipoobatdetail_t" ADD COLUMN if not exists "status" int4 DEFAULT 572;');

$this->execute('COMMENT ON COLUMN "public"."validasipoobatdetail_t"."status" IS \'lookup_type:  status_penerimaan_po\';');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210407_051045_migrate_20210407_validasipoobatdetail_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210407_051045_migrate_20210407_validasipoobatdetail_t cannot be reverted.\n";

        return false;
    }
    */
}
