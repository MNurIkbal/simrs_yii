<?php

use yii\db\Migration;

/**
 * Class m241001_041604_migrate_dsv_1454_sp_ubahdokter_tindakan
 */
class m241001_041604_migrate_dsv_1454_sp_ubahdokter_tindakan extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP FUNCTION IF EXISTS "public"."sp_ubah_dokter_tindakan"("xpendaftaran_id" int4, "xpegawai_id" int4, "xuser_id" int4);');
        $sp_ubah_dokter_tindakan = file_get_contents(__DIR__ . '/definitions/sp_ubah_dokter_tindakan.sql');
        $this->execute($sp_ubah_dokter_tindakan);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241001_041604_migrate_dsv_1454_sp_ubahdokter_tindakan cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241001_041604_migrate_dsv_1454_sp_ubahdokter_tindakan cannot be reverted.\n";

        return false;
    }
    */
}
