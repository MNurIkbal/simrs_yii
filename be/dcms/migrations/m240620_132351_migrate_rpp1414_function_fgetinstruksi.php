<?php

use yii\db\Migration;

/**
 * Class m240620_132351_migrate_rpp1414_function_fgetinstruksi
 */
class m240620_132351_migrate_rpp1414_function_fgetinstruksi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP FUNCTION IF EXISTS "public"."fgetinstruksi"("xpasien_id" int4, "xstart_date" date, "xend_date" date, "xjenis_deskripsi" text, "xinstruksi" text, "xlimit" int4, "xoffset" int4);');
        $fgetinstruksi = file_get_contents(__DIR__ . '/definitions/fgetinstruksi.sql');
        $this->execute($fgetinstruksi);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240620_132351_migrate_rpp1414_function_fgetinstruksi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240620_132351_migrate_rpp1414_function_fgetinstruksi cannot be reverted.\n";

        return false;
    }
    */
}
