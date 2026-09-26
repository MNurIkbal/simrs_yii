<?php

use yii\db\Migration;

/**
 * Class m211004_031821_migrate_timoperasi_t
 */
class m211004_031821_migrate_timoperasi_t extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."timoperasi_t" 
  ADD COLUMN if not exists "daftartindakan_id" int4;');

        $this->execute('DROP VIEW if exists "public"."inpostoperasi1_v";');

        $this->execute("
            CREATE VIEW \"public\".\"inpostoperasi1_v\" AS  SELECT inpostoperasi_t.inpostoperasi_id,
    inpostoperasi_t.pasienmasukpenunjang_id,
    timoperasi_t.timoperasi_id,
    timoperasi_t.posisi_tim,
    fgetnamalookup(timoperasi_t.posisi_tim) AS posisi,
    timoperasi_t.pegawai_id,
    pegawai_m.nama_pegawai,
    timoperasi_t.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    timoperasi_t.persentase,
    timoperasi_t.harga,
    operasi_m.operasi_id,
    operasi_m.operasi_nama,
    operasi_m.kegiatanoperasi_id,
    kegiatanoperasi_m.kegiatanoperasi_nama,
    operasi_m.golonganoperasi_id,
    golonganoperasi_m.golonganoperasi_nama,
    timoperasi_t.created_by,
    login.nama_pegawai AS pegawai_input,
    timoperasi_t.additional_data
   FROM inpostoperasi_t
     JOIN timoperasi_t ON inpostoperasi_t.inpostoperasi_id = timoperasi_t.inpostoperasi_id
     JOIN pegawai_m ON timoperasi_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN daftartindakan_m ON timoperasi_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN operasi_m ON operasi_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN kegiatanoperasi_m ON kegiatanoperasi_m.kegiatanoperasi_id = operasi_m.kegiatanoperasi_id
     LEFT JOIN golonganoperasi_m ON golonganoperasi_m.golonganoperasi_id = operasi_m.golonganoperasi_id
     LEFT JOIN loginpemakai_k ON loginpemakai_k.loginpemakai_id = timoperasi_t.created_by
     LEFT JOIN pegawai_m login ON login.pegawai_id = loginpemakai_k.pegawai_id;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211004_031821_migrate_timoperasi_t cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211004_031821_migrate_timoperasi_t cannot be reverted.\n";

        return false;
    }
    */
}
