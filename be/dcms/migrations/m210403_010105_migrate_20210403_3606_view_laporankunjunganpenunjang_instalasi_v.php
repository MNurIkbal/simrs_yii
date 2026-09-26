<?php

use yii\db\Migration;

/**
 * Class m210403_010105_migrate_20210403_3606_view_laporankunjunganpenunjang_instalasi_v
 */
class m210403_010105_migrate_20210403_3606_view_laporankunjunganpenunjang_instalasi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        
        $this->execute('DROP VIEW if exists public.laporankunjunganpenunjang_instalasi_v;');
        $this->execute("
            CREATE VIEW \"public\".\"laporankunjunganpenunjang_instalasi_v\" AS
              SELECT instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    nama_pemeriksaan.id AS jenispemeriksaan_id,
    nama_pemeriksaan.jenis AS jenispemeriksaan_nama
   FROM (((pendaftaran_t
     JOIN ( SELECT 'LAB'::text AS tipe,
            tindakanpelayanan_t.pendaftaran_id,
            pasienmasukpenunjang_t_1.pasienmasukpenunjang_id,
            jenispemeriksaanlab_m.jenispemeriksaanlab_nama AS jenis,
            daftartindakan_m.daftartindakan_nama,
            COALESCE(pemeriksaanlab_m.pemeriksaanlab_nama, daftartindakan_m.daftartindakan_nama) AS nama_pemeriksaan,
            tindakanpelayanan_t.qty_tindakan,
            pasienmasukpenunjang_t_1.ruangan_id,
            pembayaranpelayanan_t_1.tgl_pembayaran,
            jenispemeriksaanlab_m.jenispemeriksaanlab_id AS id
           FROM ((((((tindakanpelayanan_t
             JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
             LEFT JOIN pemeriksaanlab_m ON ((tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id)))
             JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
             JOIN pasienmasukpenunjang_t pasienmasukpenunjang_t_1 ON ((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t_1.pasienmasukpenunjang_id)))
             JOIN pendaftaran_t pendaftaran_t_1 ON ((pasienmasukpenunjang_t_1.pendaftaran_id = pendaftaran_t_1.pendaftaran_id)))
             LEFT JOIN pembayaranpelayanan_t pembayaranpelayanan_t_1 ON ((pendaftaran_t_1.pendaftaran_id = pembayaranpelayanan_t_1.pendaftaran_id)))
          WHERE (((tindakanpelayanan_t.is_deleted = false) AND (pemeriksaanlab_m.is_deleted = false) AND (pasienmasukpenunjang_t_1.is_bayar = true) AND (pendaftaran_t_1.is_aps = true)) OR ((tindakanpelayanan_t.is_deleted = false) AND (pemeriksaanlab_m.is_deleted = false) AND (pendaftaran_t_1.is_aps = false)))
        UNION ALL
         SELECT 'LAB_PAKET'::text AS tipe,
            tindakanpelayanan_t.pendaftaran_id,
            pasienmasukpenunjang_t_1.pasienmasukpenunjang_id,
            jenispemeriksaanlab_m.jenispemeriksaanlab_nama AS jenis,
            daftartindakan_m.daftartindakan_nama,
            pemeriksaanlab_m.pemeriksaanlab_nama AS nama_pemeriksaan,
            tindakanpelayanan_t.qty_tindakan,
            pasienmasukpenunjang_t_1.ruangan_id,
            pembayaranpelayanan_t_1.tgl_pembayaran,
            jenispemeriksaanlab_m.jenispemeriksaanlab_id AS id
           FROM ((((((((tindakanpelayanan_t
             JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
             JOIN paketpelayanan_mp ON ((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
             JOIN daftartindakan_m ON ((daftartindakan_m.daftartindakan_id = paketpelayanan_mp.daftartindakan_id)))
             JOIN pemeriksaanlab_m ON ((paketpelayanan_mp.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id)))
             LEFT JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
             JOIN pasienmasukpenunjang_t pasienmasukpenunjang_t_1 ON ((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t_1.pasienmasukpenunjang_id)))
             JOIN pendaftaran_t pendaftaran_t_1 ON ((pasienmasukpenunjang_t_1.pendaftaran_id = pendaftaran_t_1.pendaftaran_id)))
             LEFT JOIN pembayaranpelayanan_t pembayaranpelayanan_t_1 ON ((pendaftaran_t_1.pendaftaran_id = pembayaranpelayanan_t_1.pendaftaran_id)))
          WHERE (((tindakanpelayanan_t.is_deleted = false) AND (pemeriksaanlab_m.is_deleted = false) AND (pasienmasukpenunjang_t_1.is_bayar = true) AND (pendaftaran_t_1.is_aps = true)) OR ((tindakanpelayanan_t.is_deleted = false) AND (pemeriksaanlab_m.is_deleted = false) AND (pendaftaran_t_1.is_aps = false)))
          GROUP BY tindakanpelayanan_t.pendaftaran_id, pasienmasukpenunjang_t_1.pasienmasukpenunjang_id, jenispemeriksaanlab_m.jenispemeriksaanlab_nama, daftartindakan_m.daftartindakan_nama, pemeriksaanlab_m.pemeriksaanlab_nama, tindakanpelayanan_t.qty_tindakan, pasienmasukpenunjang_t_1.ruangan_id, pembayaranpelayanan_t_1.tgl_pembayaran, jenispemeriksaanlab_m.jenispemeriksaanlab_id
        UNION ALL
         SELECT 'RAD'::text AS tipe,
            tindakanpelayanan_t.pendaftaran_id,
            pasienmasukpenunjang_t_1.pasienmasukpenunjang_id,
            jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS jenis,
            daftartindakan_m.daftartindakan_nama,
            COALESCE(pemeriksaanrad_m.pemeriksaanrad_nama, daftartindakan_m.daftartindakan_nama) AS nama_pemeriksaan,
            tindakanpelayanan_t.qty_tindakan,
            pasienmasukpenunjang_t_1.ruangan_id,
            pembayaranpelayanan_t_1.tgl_pembayaran,
            jenispemeriksaanrad_m.jenispemeriksaanrad_id AS id
           FROM ((((((tindakanpelayanan_t
             JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
             JOIN pemeriksaanrad_m ON ((tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id)))
             JOIN jenispemeriksaanrad_m ON ((pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id)))
             JOIN pasienmasukpenunjang_t pasienmasukpenunjang_t_1 ON ((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t_1.pasienmasukpenunjang_id)))
             JOIN pendaftaran_t pendaftaran_t_1 ON ((pasienmasukpenunjang_t_1.pendaftaran_id = pendaftaran_t_1.pendaftaran_id)))
             LEFT JOIN pembayaranpelayanan_t pembayaranpelayanan_t_1 ON ((pendaftaran_t_1.pendaftaran_id = pembayaranpelayanan_t_1.pendaftaran_id)))
          WHERE (((tindakanpelayanan_t.is_deleted = false) AND (pemeriksaanrad_m.is_deleted = false) AND (pasienmasukpenunjang_t_1.is_bayar = true) AND (pendaftaran_t_1.is_aps = true)) OR ((tindakanpelayanan_t.is_deleted = false) AND (pemeriksaanrad_m.is_deleted = false) AND (pendaftaran_t_1.is_aps = false)))
        UNION ALL
         SELECT 'RAD_PAKET'::text AS tipe,
            tindakanpelayanan_t.pendaftaran_id,
            pasienmasukpenunjang_t_1.pasienmasukpenunjang_id,
            jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS jenis,
            daftartindakan_m.daftartindakan_nama,
            pemeriksaanrad_m.pemeriksaanrad_nama AS nama_pemeriksaan,
            tindakanpelayanan_t.qty_tindakan,
            pasienmasukpenunjang_t_1.ruangan_id,
            pembayaranpelayanan_t_1.tgl_pembayaran,
            jenispemeriksaanrad_m.jenispemeriksaanrad_id AS id
           FROM ((((((((tindakanpelayanan_t
             JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
             JOIN paketpelayanan_mp ON ((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
             JOIN daftartindakan_m ON ((daftartindakan_m.daftartindakan_id = paketpelayanan_mp.daftartindakan_id)))
             JOIN pemeriksaanrad_m ON ((paketpelayanan_mp.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id)))
             JOIN jenispemeriksaanrad_m ON ((pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id)))
             JOIN pasienmasukpenunjang_t pasienmasukpenunjang_t_1 ON ((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t_1.pasienmasukpenunjang_id)))
             JOIN pendaftaran_t pendaftaran_t_1 ON ((pasienmasukpenunjang_t_1.pendaftaran_id = pendaftaran_t_1.pendaftaran_id)))
             LEFT JOIN pembayaranpelayanan_t pembayaranpelayanan_t_1 ON ((pendaftaran_t_1.pendaftaran_id = pembayaranpelayanan_t_1.pendaftaran_id)))
          WHERE (((tindakanpelayanan_t.is_deleted = false) AND (pemeriksaanrad_m.is_deleted = false) AND (pasienmasukpenunjang_t_1.is_bayar = true) AND (pendaftaran_t_1.is_aps = true)) OR ((tindakanpelayanan_t.is_deleted = false) AND (pemeriksaanrad_m.is_deleted = false) AND (pendaftaran_t_1.is_aps = false)))
          GROUP BY tindakanpelayanan_t.pendaftaran_id, pasienmasukpenunjang_t_1.pasienmasukpenunjang_id, jenispemeriksaanrad_m.jenispemeriksaanrad_nama, daftartindakan_m.daftartindakan_nama, pemeriksaanrad_m.pemeriksaanrad_nama, tindakanpelayanan_t.qty_tindakan, pasienmasukpenunjang_t_1.ruangan_id, pembayaranpelayanan_t_1.tgl_pembayaran, jenispemeriksaanrad_m.jenispemeriksaanrad_id
        UNION ALL
         SELECT 'OPERASI'::text AS tipe,
            tindakanpelayanan_t.pendaftaran_id,
            pasienmasukpenunjang_t_1.pasienmasukpenunjang_id,
            kegiatanoperasi_m.kegiatanoperasi_nama AS jenis,
            operasi_m.operasi_nama,
            COALESCE(kegiatanoperasi_m.kegiatanoperasi_nama, daftartindakan_m.daftartindakan_nama) AS nama_pemeriksaan,
            tindakanpelayanan_t.qty_tindakan,
            pasienmasukpenunjang_t_1.ruangan_id,
            pembayaranpelayanan_t_1.tgl_pembayaran,
            kegiatanoperasi_m.kegiatanoperasi_id AS id
           FROM ((((((tindakanpelayanan_t
             JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
             JOIN operasi_m ON ((tindakanpelayanan_t.daftartindakan_id = operasi_m.daftartindakan_id)))
             JOIN kegiatanoperasi_m ON ((operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id)))
             JOIN pasienmasukpenunjang_t pasienmasukpenunjang_t_1 ON ((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t_1.pasienmasukpenunjang_id)))
             JOIN pendaftaran_t pendaftaran_t_1 ON ((pasienmasukpenunjang_t_1.pendaftaran_id = pendaftaran_t_1.pendaftaran_id)))
             LEFT JOIN pembayaranpelayanan_t pembayaranpelayanan_t_1 ON ((pendaftaran_t_1.pendaftaran_id = pembayaranpelayanan_t_1.pendaftaran_id)))
          WHERE (((tindakanpelayanan_t.is_deleted = false) AND (pasienmasukpenunjang_t_1.is_bayar = true) AND (pendaftaran_t_1.is_aps = true)) OR ((tindakanpelayanan_t.is_deleted = false) AND (pendaftaran_t_1.is_aps = false)))
        UNION ALL
         SELECT 'OPERASI_PAKET'::text AS tipe,
            tindakanpelayanan_t.pendaftaran_id,
            pasienmasukpenunjang_t_1.pasienmasukpenunjang_id,
            kegiatanoperasi_m.kegiatanoperasi_nama AS jenis,
            daftartindakan_m.daftartindakan_nama,
            operasi_m.operasi_nama AS nama_pemeriksaan,
            tindakanpelayanan_t.qty_tindakan,
            pasienmasukpenunjang_t_1.ruangan_id,
            pembayaranpelayanan_t_1.tgl_pembayaran,
            kegiatanoperasi_m.kegiatanoperasi_id AS id
           FROM ((((((((tindakanpelayanan_t
             JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
             JOIN paketpelayanan_mp ON ((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
             JOIN daftartindakan_m ON ((daftartindakan_m.daftartindakan_id = paketpelayanan_mp.daftartindakan_id)))
             JOIN operasi_m ON ((paketpelayanan_mp.daftartindakan_id = operasi_m.daftartindakan_id)))
             JOIN kegiatanoperasi_m ON ((operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id)))
             JOIN pasienmasukpenunjang_t pasienmasukpenunjang_t_1 ON ((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t_1.pasienmasukpenunjang_id)))
             JOIN pendaftaran_t pendaftaran_t_1 ON ((pasienmasukpenunjang_t_1.pendaftaran_id = pendaftaran_t_1.pendaftaran_id)))
             LEFT JOIN pembayaranpelayanan_t pembayaranpelayanan_t_1 ON ((pendaftaran_t_1.pendaftaran_id = pembayaranpelayanan_t_1.pendaftaran_id)))
          WHERE (((tindakanpelayanan_t.is_deleted = false) AND (pasienmasukpenunjang_t_1.is_bayar = true) AND (pendaftaran_t_1.is_aps = true)) OR ((tindakanpelayanan_t.is_deleted = false) AND (pendaftaran_t_1.is_aps = false)))
          GROUP BY tindakanpelayanan_t.pendaftaran_id, pasienmasukpenunjang_t_1.pasienmasukpenunjang_id, kegiatanoperasi_m.kegiatanoperasi_nama, daftartindakan_m.daftartindakan_nama, operasi_m.operasi_nama, tindakanpelayanan_t.qty_tindakan, pasienmasukpenunjang_t_1.ruangan_id, pembayaranpelayanan_t_1.tgl_pembayaran, kegiatanoperasi_m.kegiatanoperasi_id) nama_pemeriksaan ON ((pendaftaran_t.pendaftaran_id = nama_pemeriksaan.pendaftaran_id)))
     JOIN ruangan_m ON ((nama_pemeriksaan.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
  WHERE (instalasi_m.instalasi_id = ANY (ARRAY[4, 5, 7, 21]))
  GROUP BY instalasi_m.instalasi_id, instalasi_m.instalasi_nama, nama_pemeriksaan.id, nama_pemeriksaan.jenis
  ORDER BY instalasi_m.instalasi_id
            ;");
            $this->execute('
                ALTER TABLE public.laporankunjunganpenunjang_instalasi_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210403_010105_migrate_20210403_3606_view_laporankunjunganpenunjang_instalasi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210403_010105_migrate_20210403_3606_view_laporankunjunganpenunjang_instalasi_v cannot be reverted.\n";

        return false;
    }
    */
}
