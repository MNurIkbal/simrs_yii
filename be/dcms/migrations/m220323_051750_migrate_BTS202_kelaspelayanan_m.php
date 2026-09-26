<?php

use yii\db\Migration;

/**
 * Class m220323_051750_migrate_BTS202_kelaspelayanan_m
 */
class m220323_051750_migrate_BTS202_kelaspelayanan_m extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

        $this->execute('
            ALTER TABLE "public"."kelaspelayanan_m"
            ADD COLUMN IF NOT EXISTS "kelas_rawat_naik_bpjs" int4;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220323_051750_migrate_BTS202_kelaspelayanan_m cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220323_051750_migrate_BTS202_kelaspelayanan_m cannot be reverted.\n";

        return false;
    }
    */
}
