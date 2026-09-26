<?php

use yii\db\Migration;

/**
 * Class m220112_000750_migrate_rinciankelompoktindakan_v
 */
class m220112_000750_migrate_rinciankelompoktindakan_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.rinciankelompoktindakan_v;');
        
        $this->execute("
            CREATE VIEW \"public\".\"rinciankelompoktindakan_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    kelompoktindakan_m.kelompoktindakan_nama,
    sum(tindakanpelayanan_t.tarif_tindakan) AS total,
    pegawai_m.nama_pegawai AS nama_dokter,
    count(tindakanpelayanan_t.tindakanpelayanan_id) AS jumlah_item
   FROM pendaftaran_t
     JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
     LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
  WHERE tindakanpelayanan_t.is_deleted = false
  GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, kelompoktindakan_m.kelompoktindakan_nama, pegawai_m.nama_pegawai
UNION ALL
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    tipepaket_m.tipepaket_nama AS kelompoktindakan_nama,
    sum(tindakanpelayanan_t.tarif_tindakan) AS total,
    pegawai_m.nama_pegawai AS nama_dokter,
    count(tindakanpelayanan_t.tindakanpelayanan_id) AS jumlah_item
   FROM pendaftaran_t
     JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
     LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
  WHERE tindakanpelayanan_t.is_deleted = false
  GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, tipepaket_m.tipepaket_nama, pegawai_m.nama_pegawai
UNION ALL
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    'obat'::text AS kelompoktindakan_nama,
    sum(obatalkespasien_t.hargajual_oa) AS total,
    pegawai_m.nama_pegawai AS nama_dokter,
    count(obatalkespasien_t.obatalkespasien_id) AS jumlah_item
   FROM pendaftaran_t
     JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
     JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
  WHERE obatalkespasien_t.is_deleted = false
  GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.no_pendaftaran, 'obat'::text, pegawai_m.nama_pegawai;
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220112_000750_migrate_rinciankelompoktindakan_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220112_000750_migrate_rinciankelompoktindakan_v cannot be reverted.\n";

        return false;
    }
    */
}
