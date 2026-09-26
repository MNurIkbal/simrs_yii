<?php

use yii\db\Migration;

/**
 * Class m240621_101305_rpp_1414_function_update_fgetinstruksi
 */
class m240621_101305_rpp_1414_function_update_fgetinstruksi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP FUNCTION IF EXISTS "public"."fgetinstruksi"("xpasien_id" int4, "xstart_date" date, "xend_date" date, "xjenis_deskripsi" text, "xinstruksi" text, "xpendaftaran_id" int4, "xkelompoktindakan_id" text, "xlimit" int4, "xoffset" int4);');
        $fgetinstruksi = file_get_contents(__DIR__ . '/definitions/fgetinstruksi_new.sql');
        $this->execute($fgetinstruksi);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240621_101305_rpp_1414_function_update_fgetinstruksi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240621_101305_rpp_1414_function_update_fgetinstruksi cannot be reverted.\n";

        return false;
    }
    */
}
