<?php

use yii\db\Migration;

/**
 * Class m221227_183205_migrate_GB497_laporankunjunganpenunjang_v
 */
class m221227_183205_migrate_GB497_laporankunjunganpenunjang_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."laporankunjunganpenunjang_v";');
        $this->execute("CREATE OR REPLACE VIEW public.laporankunjunganpenunjang_v
        AS SELECT pendaftaran_t.no_pendaftaran,
                CASE
                    WHEN pendaftaran_t.is_aps = false THEN pasienmasukpenunjang_t.tglmasukpenunjang
                    ELSE nama_pemeriksaan.tgl_pembayaran
                END AS tglmasukpenunjang,
            pasien_m.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            ruangan_m.ruangan_id,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama,
                CASE
                    WHEN pendaftaran_t.is_aps = true THEN 'APS'::text::character varying
                    ELSE instalasiasal.instalasi_nama
                END AS unit,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            nama_pemeriksaan.id AS jeniskegiatantindakan_id,
            nama_pemeriksaan.jenis AS jeniskegiatantindakan_nama,
            nama_pemeriksaan.daftartindakan_nama,
            count(
                CASE
                    WHEN nama_pemeriksaan.qty_tindakan IS NOT NULL THEN ''::text
                    ELSE NULL::text
                END) AS jumlah_tindakan,
            carabayar_m.carabayar_id,
            carabayar_m.carabayar_nama
           FROM pendaftaran_t
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN ( SELECT 'LAB'::text AS tipe,
                    tindakanpelayanan_t.pendaftaran_id,
                    pasienmasukpenunjang_t_1.pasienmasukpenunjang_id,
                    jenispemeriksaanlab_m.jenispemeriksaanlab_nama AS jenis,
                    daftartindakan_m.daftartindakan_nama,
                    COALESCE(pemeriksaanlab_m.pemeriksaanlab_nama, daftartindakan_m.daftartindakan_nama) AS nama_pemeriksaan,
                    tindakanpelayanan_t.qty_tindakan,
                    pasienmasukpenunjang_t_1.ruangan_id,
                    pasienmasukpenunjang_t_1.tglmasukpenunjang AS tgl_pembayaran,
                    jenispemeriksaanlab_m.jenispemeriksaanlab_id AS id
                   FROM tindakanpelayanan_t
                     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                     LEFT JOIN pemeriksaanlab_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
                     JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
                     JOIN pasienmasukpenunjang_t pasienmasukpenunjang_t_1 ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t_1.pasienmasukpenunjang_id
                     JOIN pendaftaran_t pendaftaran_t_1 ON pasienmasukpenunjang_t_1.pendaftaran_id = pendaftaran_t_1.pendaftaran_id
                     LEFT JOIN pembayaranpelayanan_t pembayaranpelayanan_t_1 ON pendaftaran_t_1.pendaftaran_id = pembayaranpelayanan_t_1.pendaftaran_id
                  WHERE pasienmasukpenunjang_t_1.status_periksa::integer <> 476 AND tindakanpelayanan_t.is_deleted = false AND pemeriksaanlab_m.is_deleted = false AND pendaftaran_t_1.is_aps = true OR tindakanpelayanan_t.is_deleted = false AND pemeriksaanlab_m.is_deleted = false AND pendaftaran_t_1.is_aps = false
                UNION ALL
                 SELECT 'LAB_PAKET'::text AS tipe,
                    tindakanpelayanan_t.pendaftaran_id,
                    pasienmasukpenunjang_t_1.pasienmasukpenunjang_id,
                    jenispemeriksaanlab_m.jenispemeriksaanlab_nama AS jenis,
                    daftartindakan_m.daftartindakan_nama,
                    pemeriksaanlab_m.pemeriksaanlab_nama AS nama_pemeriksaan,
                    tindakanpelayanan_t.qty_tindakan,
                    pasienmasukpenunjang_t_1.ruangan_id,
                    pasienmasukpenunjang_t_1.tglmasukpenunjang AS tgl_pembayaran,
                    jenispemeriksaanlab_m.jenispemeriksaanlab_id AS id
                   FROM tindakanpelayanan_t
                     JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
                     JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
                     JOIN daftartindakan_m ON daftartindakan_m.daftartindakan_id = paketpelayanan_mp.daftartindakan_id
                     JOIN pemeriksaanlab_m ON paketpelayanan_mp.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
                     LEFT JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
                     JOIN pasienmasukpenunjang_t pasienmasukpenunjang_t_1 ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t_1.pasienmasukpenunjang_id
                     JOIN pendaftaran_t pendaftaran_t_1 ON pasienmasukpenunjang_t_1.pendaftaran_id = pendaftaran_t_1.pendaftaran_id
                     LEFT JOIN pembayaranpelayanan_t pembayaranpelayanan_t_1 ON pendaftaran_t_1.pendaftaran_id = pembayaranpelayanan_t_1.pendaftaran_id
                  WHERE pasienmasukpenunjang_t_1.status_periksa::integer <> 476 AND tindakanpelayanan_t.is_deleted = false AND pemeriksaanlab_m.is_deleted = false AND pasienmasukpenunjang_t_1.is_bayar = true AND pendaftaran_t_1.is_aps = true OR tindakanpelayanan_t.is_deleted = false AND pemeriksaanlab_m.is_deleted = false AND pendaftaran_t_1.is_aps = false
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
                    pasienmasukpenunjang_t_1.tglmasukpenunjang AS tgl_pembayaran,
                    jenispemeriksaanrad_m.jenispemeriksaanrad_id AS id
                   FROM tindakanpelayanan_t
                     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                     JOIN pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                     JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
                     JOIN pasienmasukpenunjang_t pasienmasukpenunjang_t_1 ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t_1.pasienmasukpenunjang_id
                     JOIN pendaftaran_t pendaftaran_t_1 ON pasienmasukpenunjang_t_1.pendaftaran_id = pendaftaran_t_1.pendaftaran_id
                  WHERE pasienmasukpenunjang_t_1.status_periksa::integer <> 476 AND tindakanpelayanan_t.is_deleted = false AND pemeriksaanrad_m.is_deleted = false AND pasienmasukpenunjang_t_1.is_bayar = true AND pendaftaran_t_1.is_aps = true OR tindakanpelayanan_t.is_deleted = false AND pemeriksaanrad_m.is_deleted = false AND pendaftaran_t_1.is_aps = false
                UNION ALL
                 SELECT 'RAD_PAKET'::text AS tipe,
                    tindakanpelayanan_t.pendaftaran_id,
                    pasienmasukpenunjang_t_1.pasienmasukpenunjang_id,
                    jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS jenis,
                    daftartindakan_m.daftartindakan_nama,
                    pemeriksaanrad_m.pemeriksaanrad_nama AS nama_pemeriksaan,
                    tindakanpelayanan_t.qty_tindakan,
                    pasienmasukpenunjang_t_1.ruangan_id,
                    pasienmasukpenunjang_t_1.tglmasukpenunjang AS tgl_pembayaran,
                    jenispemeriksaanrad_m.jenispemeriksaanrad_id AS id
                   FROM tindakanpelayanan_t
                     JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
                     JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
                     JOIN daftartindakan_m ON daftartindakan_m.daftartindakan_id = paketpelayanan_mp.daftartindakan_id
                     JOIN pemeriksaanrad_m ON paketpelayanan_mp.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
                     JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
                     JOIN pasienmasukpenunjang_t pasienmasukpenunjang_t_1 ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t_1.pasienmasukpenunjang_id
                     JOIN pendaftaran_t pendaftaran_t_1 ON pasienmasukpenunjang_t_1.pendaftaran_id = pendaftaran_t_1.pendaftaran_id
                     LEFT JOIN pembayaranpelayanan_t pembayaranpelayanan_t_1 ON pendaftaran_t_1.pendaftaran_id = pembayaranpelayanan_t_1.pendaftaran_id
                  WHERE pasienmasukpenunjang_t_1.status_periksa::integer <> 476 AND tindakanpelayanan_t.is_deleted = false AND pemeriksaanrad_m.is_deleted = false AND pasienmasukpenunjang_t_1.is_bayar = true AND pendaftaran_t_1.is_aps = true OR tindakanpelayanan_t.is_deleted = false AND pemeriksaanrad_m.is_deleted = false AND pendaftaran_t_1.is_aps = false
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
                    pasienmasukpenunjang_t_1.tglmasukpenunjang AS tgl_pembayaran,
                    kegiatanoperasi_m.kegiatanoperasi_id AS id
                   FROM tindakanpelayanan_t
                     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                     JOIN operasi_m ON tindakanpelayanan_t.daftartindakan_id = operasi_m.daftartindakan_id
                     JOIN kegiatanoperasi_m ON operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id
                     JOIN pasienmasukpenunjang_t pasienmasukpenunjang_t_1 ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t_1.pasienmasukpenunjang_id
                     JOIN pendaftaran_t pendaftaran_t_1 ON pasienmasukpenunjang_t_1.pendaftaran_id = pendaftaran_t_1.pendaftaran_id
                     LEFT JOIN pembayaranpelayanan_t pembayaranpelayanan_t_1 ON pendaftaran_t_1.pendaftaran_id = pembayaranpelayanan_t_1.pendaftaran_id
                  WHERE pasienmasukpenunjang_t_1.status_periksa::integer <> 476 AND tindakanpelayanan_t.is_deleted = false AND pasienmasukpenunjang_t_1.is_bayar = true AND pendaftaran_t_1.is_aps = true OR tindakanpelayanan_t.is_deleted = false AND pendaftaran_t_1.is_aps = false
                UNION ALL
                 SELECT 'OPERASI_PAKET'::text AS tipe,
                    tindakanpelayanan_t.pendaftaran_id,
                    pasienmasukpenunjang_t_1.pasienmasukpenunjang_id,
                    kegiatanoperasi_m.kegiatanoperasi_nama AS jenis,
                    daftartindakan_m.daftartindakan_nama,
                    operasi_m.operasi_nama AS nama_pemeriksaan,
                    tindakanpelayanan_t.qty_tindakan,
                    pasienmasukpenunjang_t_1.ruangan_id,
                    pasienmasukpenunjang_t_1.tglmasukpenunjang AS tgl_pembayaran,
                    kegiatanoperasi_m.kegiatanoperasi_id AS id
                   FROM tindakanpelayanan_t
                     JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
                     JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
                     JOIN daftartindakan_m ON daftartindakan_m.daftartindakan_id = paketpelayanan_mp.daftartindakan_id
                     JOIN operasi_m ON paketpelayanan_mp.daftartindakan_id = operasi_m.daftartindakan_id
                     JOIN kegiatanoperasi_m ON operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id
                     JOIN pasienmasukpenunjang_t pasienmasukpenunjang_t_1 ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t_1.pasienmasukpenunjang_id
                     JOIN pendaftaran_t pendaftaran_t_1 ON pasienmasukpenunjang_t_1.pendaftaran_id = pendaftaran_t_1.pendaftaran_id
                     LEFT JOIN pembayaranpelayanan_t pembayaranpelayanan_t_1 ON pendaftaran_t_1.pendaftaran_id = pembayaranpelayanan_t_1.pendaftaran_id
                  WHERE pasienmasukpenunjang_t_1.status_periksa::integer <> 476 AND tindakanpelayanan_t.is_deleted = false AND pasienmasukpenunjang_t_1.is_bayar = true AND pendaftaran_t_1.is_aps = true OR tindakanpelayanan_t.is_deleted = false AND pendaftaran_t_1.is_aps = false
                  GROUP BY tindakanpelayanan_t.pendaftaran_id, pasienmasukpenunjang_t_1.pasienmasukpenunjang_id, kegiatanoperasi_m.kegiatanoperasi_nama, daftartindakan_m.daftartindakan_nama, operasi_m.operasi_nama, tindakanpelayanan_t.qty_tindakan, pasienmasukpenunjang_t_1.ruangan_id, pembayaranpelayanan_t_1.tgl_pembayaran, kegiatanoperasi_m.kegiatanoperasi_id) nama_pemeriksaan ON pendaftaran_t.pendaftaran_id = nama_pemeriksaan.pendaftaran_id
             JOIN pasienmasukpenunjang_t ON nama_pemeriksaan.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
             LEFT JOIN pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
             JOIN ruangan_m ON nama_pemeriksaan.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN instalasi_m instalasiasal ON pendaftaran_t.instalasi_id = instalasiasal.instalasi_id
             JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
          WHERE instalasi_m.instalasi_id = ANY (ARRAY[4, 5, 7])
          GROUP BY pendaftaran_t.no_pendaftaran, pasien_m.pasien_id, pasien_m.nama_pasien, ruangan_m.ruangan_id, instalasi_m.instalasi_id, instalasi_m.instalasi_nama, pendaftaran_t.penjamin_id, pendaftaran_t.is_aps, penjamin_m.penjamin_nama, nama_pemeriksaan.daftartindakan_nama, nama_pemeriksaan.jenis, nama_pemeriksaan.id, nama_pemeriksaan.tgl_pembayaran, (to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date), (to_char(pasienkirimkeunitlain_t.tgl_kirimpasien, 'YYYY-MM-DD'::text)::date), pasienmasukpenunjang_t.tglmasukpenunjang, carabayar_m.carabayar_id, carabayar_m.carabayar_nama, instalasiasal.instalasi_nama
          ORDER BY (to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text)::date) DESC;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221227_183205_migrate_GB497_laporankunjunganpenunjang_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221227_183205_migrate_GB497_laporankunjunganpenunjang_v cannot be reverted.\n";

        return false;
    }
    */
}
