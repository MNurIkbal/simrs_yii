<?php

use yii\db\Migration;

/**
 * Class m220420_173629_migrate_sobarang_table
 */
class m220420_173629_migrate_sobarang_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."stokopnamebarang_t" ADD IF NOT EXISTS "pegawaiverifikasi_id" int4;');
		
		$this->execute('ALTER TABLE "public"."stokopnamebarang_t" ADD IF NOT EXISTS "is_verifikasi" bool NOT NULL DEFAULT false;');
		
		$this->execute('ALTER TABLE "public"."stokopnamebarang_t" ADD IF NOT EXISTS "tglverifikasi" timestamp;');
		
		$this->execute('ALTER TABLE "public"."stokopnamebarang_t" ADD IF NOT EXISTS "tgl_implementasi" timestamp;');
		
		
		
		$this->execute('ALTER TABLE "public"."stokopnamebarangdetail_t" ADD IF NOT EXISTS "revisi_stok" float8;');
		
		$this->execute('ALTER TABLE "public"."stokopnamebarangdetail_t" ADD IF NOT EXISTS "stok_akhir" float8;');
		
		$this->execute('ALTER TABLE "public"."stokopnamebarangdetail_t" ADD IF NOT EXISTS "selisih_akhir" float8;');
		
		
		
		$this->execute('ALTER TABLE "public"."formsobarangdetail_t" ADD IF NOT EXISTS "satuankecil_id" int4;');
		
		
		$this->execute('ALTER TABLE "public"."konfiggudang_k" ADD IF NOT EXISTS "is_verifstokopnamebarang" bool DEFAULT false;');
		
		$this->execute('ALTER TABLE "public"."konfiggudang_k" ADD IF NOT EXISTS "is_tgl_implementasi_sesuai_verif" bool DEFAULT false;');
		
		$this->execute('ALTER TABLE "public"."konfiggudang_k" ADD IF NOT EXISTS "max_dataso" int4;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220420_173629_migrate_sobarang_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220420_173629_migrate_sobarang_table cannot be reverted.\n";

        return false;
    }
    */
}
