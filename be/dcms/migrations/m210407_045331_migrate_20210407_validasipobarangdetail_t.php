<?php

use yii\db\Migration;

/**
 * Class m210407_045331_migrate_20210407_validasipobarangdetail_t
 */
class m210407_045331_migrate_20210407_validasipobarangdetail_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
$this->execute('ALTER TABLE "public"."validasipobarangdetail_t" ADD COLUMN IF NOT exists "status" int4 DEFAULT 572;');

$this->execute('COMMENT ON COLUMN "public"."validasipobarangdetail_t"."status" IS \'lookup_type: status_penerimaan_po\';');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210407_045331_migrate_20210407_validasipobarangdetail_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210407_045331_migrate_20210407_validasipobarangdetail_t cannot be reverted.\n";

        return false;
    }
    */
}
