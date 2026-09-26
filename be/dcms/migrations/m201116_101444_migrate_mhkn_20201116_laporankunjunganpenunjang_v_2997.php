<?php

use yii\db\Migration;

/**
 * Class m201116_101444_migrate_mhkn_20201116_laporankunjunganpenunjang_v_2997
 */
class m201116_101444_migrate_mhkn_20201116_laporankunjunganpenunjang_v_2997 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.laporankunjunganpenunjang_v;');
        $this->execute("CREATE VIEW \"public\".\"laporankunjunganpenunjang_v\" AS
                 SELECT pendaftaran_t.no_pendaftaran,
        CASE
            WHEN (pasienmasukpenunjang_t.tglmasukpenunjang IS NULL) THEN (to_char(pasienkirimkeunitlain_t.tgl_kirimpasien, 'YYYY-MM-DD'::text))::date
            ELSE (to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text))::date
        END AS tglmasukpenunjang,
    pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    ruangan_m.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
        CASE
            WHEN (pendaftaran_t.is_aps = true) THEN ('APS'::text)::character varying
            ELSE ruangan_m.ruangan_nama
        END AS unit,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    nama_pemeriksaan.jenis AS jeniskegiatantindakan_nama,
    nama_pemeriksaan.daftartindakan_nama,
    count(
        CASE
            WHEN (nama_pemeriksaan.qty_tindakan IS NOT NULL) THEN ''::text
            ELSE NULL::text
        END) AS jumlah_tindakan
   FROM (((((((pendaftaran_t
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN ( SELECT 'LAB'::text AS tipe,
            tindakanpelayanan_t.pendaftaran_id,
            pasienmasukpenunjang_t_1.pasienmasukpenunjang_id,
            jenispemeriksaanlab_m.jenispemeriksaanlab_nama AS jenis,
            daftartindakan_m.daftartindakan_nama,
            COALESCE(pemeriksaanlab_m.pemeriksaanlab_nama, daftartindakan_m.daftartindakan_nama) AS nama_pemeriksaan,
            tindakanpelayanan_t.qty_tindakan,
            pasienmasukpenunjang_t_1.ruangan_id
           FROM ((((tindakanpelayanan_t
             JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
             LEFT JOIN pemeriksaanlab_m ON ((tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id)))
             JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
             JOIN pasienmasukpenunjang_t pasienmasukpenunjang_t_1 ON ((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t_1.pasienmasukpenunjang_id)))
          WHERE ((tindakanpelayanan_t.is_deleted = false) AND (pemeriksaanlab_m.is_deleted = false))
        UNION ALL
         SELECT 'LAB_PAKET'::text AS tipe,
            tindakanpelayanan_t.pendaftaran_id,
            pasienmasukpenunjang_t_1.pasienmasukpenunjang_id,
            jenispemeriksaanlab_m.jenispemeriksaanlab_nama AS jenis,
            daftartindakan_m.daftartindakan_nama,
            pemeriksaanlab_m.pemeriksaanlab_nama AS nama_pemeriksaan,
            tindakanpelayanan_t.qty_tindakan,
            pasienmasukpenunjang_t_1.ruangan_id
           FROM ((((((tindakanpelayanan_t
             JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
             JOIN paketpelayanan_mp ON ((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
             JOIN daftartindakan_m ON ((daftartindakan_m.daftartindakan_id = paketpelayanan_mp.daftartindakan_id)))
             JOIN pemeriksaanlab_m ON ((paketpelayanan_mp.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id)))
             LEFT JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
             JOIN pasienmasukpenunjang_t pasienmasukpenunjang_t_1 ON ((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t_1.pasienmasukpenunjang_id)))
          WHERE ((tindakanpelayanan_t.is_deleted = false) AND (pemeriksaanlab_m.is_deleted = false))
        UNION ALL
         SELECT 'RAD'::text AS tipe,
            tindakanpelayanan_t.pendaftaran_id,
            pasienmasukpenunjang_t_1.pasienmasukpenunjang_id,
            jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS jenis,
            daftartindakan_m.daftartindakan_nama,
            COALESCE(pemeriksaanrad_m.pemeriksaanrad_nama, daftartindakan_m.daftartindakan_nama) AS nama_pemeriksaan,
            tindakanpelayanan_t.qty_tindakan,
            pasienmasukpenunjang_t_1.ruangan_id
           FROM ((((tindakanpelayanan_t
             JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
             JOIN pemeriksaanrad_m ON ((tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id)))
             JOIN jenispemeriksaanrad_m ON ((pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id)))
             JOIN pasienmasukpenunjang_t pasienmasukpenunjang_t_1 ON ((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t_1.pasienmasukpenunjang_id)))
          WHERE ((tindakanpelayanan_t.is_deleted = false) AND (pemeriksaanrad_m.is_deleted = false))
        UNION ALL
         SELECT 'RAD_PAKET'::text AS tipe,
            tindakanpelayanan_t.pendaftaran_id,
            pasienmasukpenunjang_t_1.pasienmasukpenunjang_id,
            jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS jenis,
            daftartindakan_m.daftartindakan_nama,
            pemeriksaanrad_m.pemeriksaanrad_nama AS nama_pemeriksaan,
            tindakanpelayanan_t.qty_tindakan,
            pasienmasukpenunjang_t_1.ruangan_id
           FROM ((((((tindakanpelayanan_t
             JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
             JOIN paketpelayanan_mp ON ((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
             JOIN daftartindakan_m ON ((daftartindakan_m.daftartindakan_id = paketpelayanan_mp.daftartindakan_id)))
             JOIN pemeriksaanrad_m ON ((paketpelayanan_mp.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id)))
             JOIN jenispemeriksaanrad_m ON ((pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id)))
             JOIN pasienmasukpenunjang_t pasienmasukpenunjang_t_1 ON ((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t_1.pasienmasukpenunjang_id)))
          WHERE ((tindakanpelayanan_t.is_deleted = false) AND (pemeriksaanrad_m.is_deleted = false))
        UNION ALL
         SELECT 'OPERASI'::text AS tipe,
            tindakanpelayanan_t.pendaftaran_id,
            pasienmasukpenunjang_t_1.pasienmasukpenunjang_id,
            kegiatanoperasi_m.kegiatanoperasi_nama AS jenis,
            operasi_m.operasi_nama,
            COALESCE(kegiatanoperasi_m.kegiatanoperasi_nama, daftartindakan_m.daftartindakan_nama) AS nama_pemeriksaan,
            tindakanpelayanan_t.qty_tindakan,
            pasienmasukpenunjang_t_1.ruangan_id
           FROM ((((tindakanpelayanan_t
             JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
             JOIN operasi_m ON ((tindakanpelayanan_t.daftartindakan_id = operasi_m.daftartindakan_id)))
             JOIN kegiatanoperasi_m ON ((operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id)))
             JOIN pasienmasukpenunjang_t pasienmasukpenunjang_t_1 ON ((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t_1.pasienmasukpenunjang_id)))
          WHERE (tindakanpelayanan_t.is_deleted = false)
        UNION ALL
         SELECT 'OPERASI_PAKET'::text AS tipe,
            tindakanpelayanan_t.pendaftaran_id,
            pasienmasukpenunjang_t_1.pasienmasukpenunjang_id,
            kegiatanoperasi_m.kegiatanoperasi_nama AS jenis,
            daftartindakan_m.daftartindakan_nama,
            operasi_m.operasi_nama AS nama_pemeriksaan,
            tindakanpelayanan_t.qty_tindakan,
            pasienmasukpenunjang_t_1.ruangan_id
           FROM ((((((tindakanpelayanan_t
             JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
             JOIN paketpelayanan_mp ON ((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
             JOIN daftartindakan_m ON ((daftartindakan_m.daftartindakan_id = paketpelayanan_mp.daftartindakan_id)))
             JOIN operasi_m ON ((paketpelayanan_mp.daftartindakan_id = operasi_m.daftartindakan_id)))
             JOIN kegiatanoperasi_m ON ((operasi_m.kegiatanoperasi_id = kegiatanoperasi_m.kegiatanoperasi_id)))
             JOIN pasienmasukpenunjang_t pasienmasukpenunjang_t_1 ON ((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t_1.pasienmasukpenunjang_id)))
          WHERE (tindakanpelayanan_t.is_deleted = false)) nama_pemeriksaan ON ((pendaftaran_t.pendaftaran_id = nama_pemeriksaan.pendaftaran_id)))
     JOIN pasienmasukpenunjang_t ON ((nama_pemeriksaan.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id)))
     LEFT JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
     JOIN ruangan_m ON ((nama_pemeriksaan.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
  GROUP BY pendaftaran_t.no_pendaftaran, pasien_m.pasien_id, pasien_m.nama_pasien, ruangan_m.ruangan_id, instalasi_m.instalasi_id, instalasi_m.instalasi_nama, pendaftaran_t.penjamin_id, pendaftaran_t.is_aps, penjamin_m.penjamin_nama, nama_pemeriksaan.daftartindakan_nama, nama_pemeriksaan.jenis, (to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text))::date, (to_char(pasienkirimkeunitlain_t.tgl_kirimpasien, 'YYYY-MM-DD'::text))::date, pasienmasukpenunjang_t.tglmasukpenunjang
  ORDER BY (to_char(pasienmasukpenunjang_t.tglmasukpenunjang, 'YYYY-MM-DD'::text))::date DESC
            ;");

        $this->execute('ALTER TABLE public.laporankunjunganpenunjang_v
    OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m201116_101444_migrate_mhkn_20201116_laporankunjunganpenunjang_v_2997 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m201116_101444_migrate_mhkn_20201116_laporankunjunganpenunjang_v_2997 cannot be reverted.\n";

        return false;
    }
    */
}
