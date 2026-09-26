<?php

use yii\db\Migration;

/**
 * Class m250424_044445_gls_988_fgetinstruksi_laporan_terapi
 */
class m250424_044445_gls_988_fgetinstruksi_laporan_terapi extends Migration
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
        echo "m250424_044445_gls_988_fgetinstruksi_laporan_terapi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m250424_044445_gls_988_fgetinstruksi_laporan_terapi cannot be reverted.\n";

        return false;
    }
    */
}
