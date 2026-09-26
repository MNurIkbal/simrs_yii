<?php

use yii\db\Migration;

/**
 * Class m241001_023958_migrate_dsv_1454_sp_recalculate_tagihan
 */
class m241001_023958_migrate_dsv_1454_sp_recalculate_tagihan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP FUNCTION IF EXISTS "public"."sp_recalculate_tagihan"("xpendaftaran_id" int4, "xpenjamin_id" int4, "xuser_id" int4);');
        $sp_recalculate_tagihan = file_get_contents(__DIR__ . '/definitions/sp_recalculate_tagihan.sql');
        $this->execute($sp_recalculate_tagihan);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241001_023958_migrate_dsv_1454_sp_recalculate_tagihan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241001_023958_migrate_dsv_1454_sp_recalculate_tagihan cannot be reverted.\n";

        return false;
    }
    */
}
