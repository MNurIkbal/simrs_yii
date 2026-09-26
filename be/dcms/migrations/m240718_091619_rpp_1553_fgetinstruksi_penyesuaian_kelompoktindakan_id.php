<?php

use yii\db\Migration;

/**
 * Class m240718_091619_rpp_1553_fgetinstruksi_penyesuaian_kelompoktindakan_id
 */
class m240718_091619_rpp_1553_fgetinstruksi_penyesuaian_kelompoktindakan_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP FUNCTION IF EXISTS "public"."fgetinstruksi"("xpasien_id" int4, "xstart_date" date, "xend_date" date, "xjenis_deskripsi" text, "xinstruksi" text, "xpendaftaran_id" int4, "xkelompoktindakan_id" text, "xlimit" int4, "xoffset" int4);');
        $fgetinstruksi = file_get_contents(__DIR__ . '/definitions/fgetinstruksi_penyesuaian_kelompotindakan.sql');
        $this->execute($fgetinstruksi);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240718_091619_rpp_1553_fgetinstruksi_penyesuaian_kelompoktindakan_id cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m240718_091619_rpp_1553_fgetinstruksi_penyesuaian_kelompoktindakan_id cannot be reverted.\n";

        return false;
    }
    */
}
