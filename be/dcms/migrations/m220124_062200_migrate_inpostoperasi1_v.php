<?php

use yii\db\Migration;

/**
 * Class m220124_062200_migrate_inpostoperasi1_v
 */
class m220124_062200_migrate_inpostoperasi1_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.inpostoperasi1_v;');

        $this->execute("
            CREATE VIEW \"public\".\"inpostoperasi1_v\" AS  SELECT timoperasi_t.inpostoperasi_id,
    timoperasi_t.pasienmasukpenunjang_id,
    timoperasi_t.timoperasi_id,
    timoperasi_t.posisi_tim,
    posisi_tim.lookup_name AS posisi,
    timoperasi_t.pegawai_id,
    pegawai_m.nama_pegawai,
    timoperasi_t.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    timoperasi_t.persentase,
    timoperasi_t.harga,
    inpostoperasidetail_t.operasi_id,
    inpostoperasidetail_t.is_cyto,
    inpostoperasidetail_t.is_penyulit,
    operasi_m.operasi_nama,
    operasi_m.kegiatanoperasi_id,
    kegiatanoperasi_m.kegiatanoperasi_nama,
    operasi_m.golonganoperasi_id,
    golonganoperasi_m.golonganoperasi_nama,
    timoperasi_t.created_by,
    peg_login.nama_pegawai AS pegawai_input,
    timoperasi_t.additional_data
   FROM timoperasi_t
     LEFT JOIN ( SELECT a.inpostoperasi_id,
            a.daftartindakan_id,
            a.is_cyto,
            a.is_penyulit,
            a.operasi_id
           FROM inpostoperasidetail_t a
          WHERE a.is_deleted = false) inpostoperasidetail_t ON inpostoperasidetail_t.inpostoperasi_id = timoperasi_t.inpostoperasi_id AND inpostoperasidetail_t.daftartindakan_id = timoperasi_t.daftartindakan_id
     LEFT JOIN ( SELECT a.operasi_id,
            a.kegiatanoperasi_id,
            a.golonganoperasi_id,
            a.operasi_nama
           FROM operasi_m a) operasi_m ON operasi_m.operasi_id = inpostoperasidetail_t.operasi_id
     LEFT JOIN ( SELECT a.daftartindakan_id,
            a.daftartindakan_nama
           FROM daftartindakan_m a) daftartindakan_m ON timoperasi_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN ( SELECT a.kegiatanoperasi_id,
            a.kegiatanoperasi_nama
           FROM kegiatanoperasi_m a) kegiatanoperasi_m ON kegiatanoperasi_m.kegiatanoperasi_id = operasi_m.kegiatanoperasi_id
     LEFT JOIN ( SELECT a.golonganoperasi_id,
            a.golonganoperasi_nama
           FROM golonganoperasi_m a) golonganoperasi_m ON golonganoperasi_m.golonganoperasi_id = operasi_m.golonganoperasi_id
     JOIN lookup_m posisi_tim ON posisi_tim.lookup_id = timoperasi_t.posisi_tim
     JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) pegawai_m ON timoperasi_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT a.loginpemakai_id,
            a.pegawai_id
           FROM loginpemakai_k a) loginpemakai_k ON loginpemakai_k.loginpemakai_id = timoperasi_t.created_by
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) peg_login ON peg_login.pegawai_id = loginpemakai_k.pegawai_id
  WHERE timoperasi_t.is_deleted = false;");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220124_062200_migrate_inpostoperasi1_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220124_062200_migrate_inpostoperasi1_v cannot be reverted.\n";

        return false;
    }
    */
}
