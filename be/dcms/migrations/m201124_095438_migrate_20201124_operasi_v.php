<?php

use yii\db\Migration;

/**
 * Class m201124_095438_migrate_20201124_operasi_v
 */
class m201124_095438_migrate_20201124_operasi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."operasi_v";');

        $this->execute("
            CREATE VIEW \"public\".\"operasi_v\" AS  SELECT operasi_m.operasi_id,
    operasi_m.kegiatanoperasi_id,
    kegiatanoperasi_m.kegiatanoperasi_nama,
    operasi_m.operasi_nama,
    operasi_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_kode,
    daftartindakan_m.daftartindakan_nama,
    operasi_m.golonganoperasi_id,
    golonganoperasi_m.golonganoperasi_nama,
    operasi_m.operasi_kode
   FROM operasi_m
     JOIN kegiatanoperasi_m ON operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id
     JOIN daftartindakan_m ON operasi_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN golonganoperasi_m ON operasi_m.golonganoperasi_id = golonganoperasi_m.golonganoperasi_id
  WHERE operasi_m.is_active = true AND operasi_m.is_deleted = false;");
        
        $this->execute('ALTER TABLE "public"."operasi_v" OWNER TO "postgres";');

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201124_095438_migrate_20201124_operasi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201124_095438_migrate_20201124_operasi_v cannot be reverted.\n";

        return false;
    }
    */
}
