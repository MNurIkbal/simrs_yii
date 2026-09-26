<?php

use yii\db\Migration;

/**
 * Class m210502_123041_migrate_20210502_migratelivebg
 */
class m210502_123041_migrate_20210502_migratelivebg extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.infopasienlabdetail_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infopasienlabdetail_v\" AS
            SELECT 'NON_PAKET'::text AS jenis,
            tindakanpelayanan_t.tindakanpelayanan_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.tgl_tindakan,
            jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
            tindakanpelayanan_t.tipepaket_id,
            ''::character varying AS tipepaket_nama,
            NULL::text AS detail_2,
            tindakanpelayanan_t.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.cyto_tindakan,
            tindakanpelayanan_t.tarifcyto_tindakan,
            tindakanpelayanan_t.tarif_tindakan,
            ambilsample_t.ambilsample_id,
            tindakanpelayanan_t.qty_tindakan,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasien_id,
            tindakanpelayanan_t.is_deleted,
            CASE
            WHEN ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN false
            WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN false
            ELSE true
            END AS is_bayar,
            CASE
            WHEN ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN 'Belum Bayar'::text
            WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN 'Batal Bayar'::text
            ELSE 'Sudah Bayar'::text
            END AS status_bayar,
            CASE
            WHEN (tindakanpelayanan_t.is_deleted = true) THEN 'BATAL'::text
            WHEN ((ambilsample_t.ambilsample_id IS NULL) AND (hasil.tindakanpelayanan_id IS NULL)) THEN 'BELUM PERIKSA'::text
            WHEN ((ambilsample_t.ambilsample_id IS NOT NULL) AND (hasil.tindakanpelayanan_id IS NULL)) THEN 'PERIKSA'::text
            WHEN ((ambilsample_t.ambilsample_id IS NOT NULL) AND (hasil.tindakanpelayanan_id IS NOT NULL)) THEN 'SELESAI'::text
            WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN 'BATAL'::text
            ELSE NULL::text
            END AS status_periksa,
            CASE
            WHEN (tindakanpelayanan_t.is_deleted = true) THEN 476
            WHEN ((ambilsample_t.ambilsample_id IS NULL) AND (hasil.tindakanpelayanan_id IS NULL)) THEN 477
            WHEN ((ambilsample_t.ambilsample_id IS NOT NULL) AND (hasil.tindakanpelayanan_id IS NULL)) THEN 473
            WHEN ((ambilsample_t.ambilsample_id IS NOT NULL) AND (hasil.tindakanpelayanan_id IS NOT NULL)) THEN 475
            WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN 476
            ELSE NULL::integer
            END AS status_periksa_id
            FROM ((((((((pendaftaran_t
            JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
            LEFT JOIN pasienmasukpenunjang_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
            JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
            LEFT JOIN permintaankepenunjang_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id)))
            LEFT JOIN pemeriksaanlab_m ON ((permintaankepenunjang_t.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id)))
            LEFT JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
            LEFT JOIN ambilsample_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = ambilsample_t.tindakanpelayanan_id)))
            LEFT JOIN ( SELECT hasilpemeriksaanlabdetail_t.tindakanpelayanan_id
            FROM hasilpemeriksaanlabdetail_t
            WHERE (hasilpemeriksaanlabdetail_t.is_deleted = false)
            GROUP BY hasilpemeriksaanlabdetail_t.tindakanpelayanan_id) hasil ON ((tindakanpelayanan_t.tindakanpelayanan_id = hasil.tindakanpelayanan_id)))
            WHERE (daftartindakan_m.kelompoktindakan_id = 26)
            UNION ALL
            SELECT 'PAKET'::text AS jenis,
            tindakanpelayanan_t.tindakanpelayanan_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.tgl_tindakan,
            jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
            tindakanpelayanan_t.tipepaket_id,
            tipepaket_m.tipepaket_nama,
            NULL::text AS detail_2,
            paketpelayanan_mp.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.cyto_tindakan,
            tindakanpelayanan_t.tarifcyto_tindakan,
            tindakanpelayanan_t.tarif_tindakan,
            ambilsample_t.ambilsample_id,
            tindakanpelayanan_t.qty_tindakan,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasien_id,
            tindakanpelayanan_t.is_deleted,
            CASE
            WHEN ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN false
            WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN false
            ELSE true
            END AS is_bayar,
            CASE
            WHEN ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN 'Belum Bayar'::text
            WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN 'Batal Bayar'::text
            ELSE 'Sudah Bayar'::text
            END AS status_bayar,
            CASE
            WHEN (tindakanpelayanan_t.is_deleted = true) THEN 'BATAL'::text
            WHEN ((ambilsample_t.ambilsample_id IS NULL) AND (hasil.tindakanpelayanan_id IS NULL)) THEN 'BELUM PERIKSA'::text
            WHEN ((ambilsample_t.ambilsample_id IS NOT NULL) AND (hasil.tindakanpelayanan_id IS NULL)) THEN 'PERIKSA'::text
            WHEN ((ambilsample_t.ambilsample_id IS NOT NULL) AND (hasil.tindakanpelayanan_id IS NOT NULL)) THEN 'SELESAI'::text
            WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN 'BATAL'::text
            ELSE NULL::text
            END AS status_periksa,
            CASE
            WHEN (tindakanpelayanan_t.is_deleted = true) THEN 476
            WHEN ((ambilsample_t.ambilsample_id IS NULL) AND (hasil.tindakanpelayanan_id IS NULL)) THEN 477
            WHEN ((ambilsample_t.ambilsample_id IS NOT NULL) AND (hasil.tindakanpelayanan_id IS NULL)) THEN 473
            WHEN ((ambilsample_t.ambilsample_id IS NOT NULL) AND (hasil.tindakanpelayanan_id IS NOT NULL)) THEN 475
            WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN 476
            ELSE NULL::integer
            END AS status_periksa_id
            FROM ((((((((((pendaftaran_t
            JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
            LEFT JOIN pasienmasukpenunjang_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
            JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
            JOIN paketpelayanan_mp ON ((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
            JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
            LEFT JOIN permintaankepenunjang_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id)))
            LEFT JOIN pemeriksaanlab_m ON ((permintaankepenunjang_t.pemeriksaanlab_id = pemeriksaanlab_m.pemeriksaanlab_id)))
            LEFT JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
            LEFT JOIN ambilsample_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = ambilsample_t.tindakanpelayanan_id)))
            LEFT JOIN ( SELECT hasilpemeriksaanlabdetail_t.tindakanpelayanan_id
            FROM hasilpemeriksaanlabdetail_t
            WHERE (hasilpemeriksaanlabdetail_t.is_deleted = false)
            GROUP BY hasilpemeriksaanlabdetail_t.tindakanpelayanan_id) hasil ON ((tindakanpelayanan_t.tindakanpelayanan_id = hasil.tindakanpelayanan_id)))
            WHERE (daftartindakan_m.kelompoktindakan_id = 26)
            UNION ALL
            SELECT 'PAKET_MCU'::text AS jenis,
            tindakanpelayanan_t.tindakanpelayanan_id,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.tgl_tindakan,
            detail.j_lab AS jenispemeriksaanlab_nama,
            tindakanpelayanan_t.tipepaket_id,
            tipepaket_m.tipepaket_nama,
            detail.detail_2,
            detail.detail_3id AS daftartindakan_id,
            detail.detail_3 AS daftartindakan_nama,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.cyto_tindakan,
            tindakanpelayanan_t.tarifcyto_tindakan,
            tindakanpelayanan_t.tarif_tindakan,
            ambilsample_t.ambilsample_id,
            tindakanpelayanan_t.qty_tindakan,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasien_id,
            tindakanpelayanan_t.is_deleted,
            CASE
            WHEN ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN false
            WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN false
            ELSE true
            END AS is_bayar,
            CASE
            WHEN ((tindakanpelayanan_t.is_deleted = false) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN 'Belum Bayar'::text
            WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN 'Batal Bayar'::text
            ELSE 'Sudah Bayar'::text
            END AS status_bayar,
            CASE
            WHEN (tindakanpelayanan_t.is_deleted = true) THEN 'BATAL'::text
            WHEN ((ambilsample_t.ambilsample_id IS NULL) AND (hasil.tindakanpelayanan_id IS NULL)) THEN 'BELUM PERIKSA'::text
            WHEN ((ambilsample_t.ambilsample_id IS NOT NULL) AND (hasil.tindakanpelayanan_id IS NULL)) THEN 'PERIKSA'::text
            WHEN ((ambilsample_t.ambilsample_id IS NOT NULL) AND (hasil.tindakanpelayanan_id IS NOT NULL)) THEN 'SELESAI'::text
            WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN 'BATAL'::text
            ELSE NULL::text
            END AS status_periksa,
            CASE
            WHEN (tindakanpelayanan_t.is_deleted = true) THEN 476
            WHEN ((ambilsample_t.ambilsample_id IS NULL) AND (hasil.tindakanpelayanan_id IS NULL)) THEN 477
            WHEN ((ambilsample_t.ambilsample_id IS NOT NULL) AND (hasil.tindakanpelayanan_id IS NULL)) THEN 473
            WHEN ((ambilsample_t.ambilsample_id IS NOT NULL) AND (hasil.tindakanpelayanan_id IS NOT NULL)) THEN 475
            WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN 476
            ELSE NULL::integer
            END AS status_periksa_id
            FROM (((((((pendaftaran_t
            JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
            LEFT JOIN pasienmasukpenunjang_t ON ((pasienmasukpenunjang_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
            JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
            LEFT JOIN ( SELECT hasilpemeriksaanlabdetail_t.tindakanpelayanan_id
            FROM hasilpemeriksaanlabdetail_t
            WHERE (hasilpemeriksaanlabdetail_t.is_deleted = false)) hasil ON ((tindakanpelayanan_t.tindakanpelayanan_id = hasil.tindakanpelayanan_id)))
            JOIN ( SELECT paketpelayanan_mp.tipepaket_id AS detail_1id,
            paketpelayanan_mp.paketdetail_id AS detail_2id,
            paket_detail.tipepaket_nama AS detail_2,
            paket_detail.daftartindakan_id AS detail_3id,
            paket_detail.daftartindakan_nama AS detail_3,
            paket_detail.p_lab,
            paket_detail.j_lab
            FROM (((tipepaket_m tipepaket_m_1
            JOIN paketpelayanan_mp ON (((tipepaket_m_1.tipepaket_id = paketpelayanan_mp.tipepaket_id) AND (paketpelayanan_mp.is_deleted = false))))
            JOIN ruangan_m ruangan_m_1 ON (((ruangan_m_1.ruangan_id = paketpelayanan_mp.ruangan_id) AND (ruangan_m_1.instalasi_id = 4))))
            JOIN ( SELECT a.tipepaket_id,
            a.tipepaket_nama,
            daftartindakan_m.daftartindakan_id,
            daftartindakan_m.daftartindakan_nama,
            pemeriksaanlab_m.pemeriksaanlab_nama AS p_lab,
            jenispemeriksaanlab_m.jenispemeriksaanlab_nama AS j_lab
            FROM ((((tipepaket_m a
            JOIN paketpelayanan_mp paketpelayanan_mp_1 ON ((a.tipepaket_id = paketpelayanan_mp_1.tipepaket_id)))
            JOIN daftartindakan_m ON ((paketpelayanan_mp_1.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
            LEFT JOIN pemeriksaanlab_m ON ((daftartindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id)))
            LEFT JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))) paket_detail ON ((paketpelayanan_mp.paketdetail_id = paket_detail.tipepaket_id)))
            WHERE (tipepaket_m_1.is_deleted = false)
            UNION ALL
            SELECT paketpelayanan_mp.tipepaket_id AS detail_1id,
            NULL::integer AS detail_2id,
            NULL::character varying AS detail_2,
            paketpelayanan_mp.daftartindakan_id AS detail_3id,
            tindakan_detail.daftartindakan_nama AS detail_3,
            pemeriksaanlab_m.pemeriksaanlab_nama AS p_lab,
            jenispemeriksaanlab_m.jenispemeriksaanlab_nama AS j_lab
            FROM (((((tipepaket_m tipepaket_m_1
            JOIN paketpelayanan_mp ON (((tipepaket_m_1.tipepaket_id = paketpelayanan_mp.tipepaket_id) AND (paketpelayanan_mp.is_deleted = false))))
            JOIN ruangan_m ruangan_m_1 ON (((ruangan_m_1.ruangan_id = paketpelayanan_mp.ruangan_id) AND (ruangan_m_1.instalasi_id = 4))))
            JOIN daftartindakan_m tindakan_detail ON ((paketpelayanan_mp.daftartindakan_id = tindakan_detail.daftartindakan_id)))
            LEFT JOIN pemeriksaanlab_m ON ((tindakan_detail.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id)))
            LEFT JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
            WHERE (tipepaket_m_1.is_deleted = false)) detail ON ((tindakanpelayanan_t.tipepaket_id = detail.detail_1id)))
            LEFT JOIN ambilsample_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = ambilsample_t.tindakanpelayanan_id)))
            JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id)))
            WHERE (ruangan_m.instalasi_id = 4)
            ;");
$this->execute('
    ALTER TABLE public.infopasienlabdetail_v OWNER TO postgres;
    ');

$this->execute('DROP VIEW if exists public.rincianpasienlab_v;');
$this->execute("
    CREATE VIEW \"public\".\"rincianpasienlab_v\" AS
    SELECT 'ORDER'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pegawai_m.nama_pegawai AS dokter,
    ruangan_m.ruangan_nama,
    kelaspelayanan_m.kelaspelayanan_nama,
    penjamin_m.penjamin_nama,
    carabayar_m.carabayar_nama,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
    sum(tindakanpelayanan_t.tarif_tindakan) AS total_tagihan,
    sum(tindakansudahbayar_t.jmliur_biaya) AS total_sdh_bayar,
    (tindakanpelayanan_t.tarif_tindakan - tindakansudahbayar_t.jmliur_biaya) AS total_sisa_tagihan,
    pemakaianuangmuka_t.total_uangmuka,
    pasien_m.tanggal_lahir
    FROM ((((((((((((((pasienmasukpenunjang_t
    JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
    JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
    LEFT JOIN pasienadmisi_t ON ((pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
    JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
    JOIN pegawai_m ON ((COALESCE(pasienadmisi_t.pegawai_id, pendaftaran_t.pegawai_id) = pegawai_m.pegawai_id)))
    JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
    JOIN ruangan_m ON ((COALESCE(pasienadmisi_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id)))
    JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
    JOIN kelaspelayanan_m ON ((COALESCE(pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.kelaspelayanan_id) = kelaspelayanan_m.kelaspelayanan_id)))
    JOIN penjamin_m ON ((COALESCE(pasienadmisi_t.penjamin_id, pendaftaran_t.penjamin_id) = penjamin_m.penjamin_id)))
    JOIN carabayar_m ON ((COALESCE(pasienadmisi_t.carabayar_id, pendaftaran_t.carabayar_id) = carabayar_m.carabayar_id)))
    JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
    LEFT JOIN tindakansudahbayar_t ON ((tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id)))
    LEFT JOIN pemakaianuangmuka_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pemakaianuangmuka_t.pendaftaran_id)))
    WHERE (pasienkirimkeunitlain_t.instalasi_id = 4)
    GROUP BY pasienmasukpenunjang_t.pasienmasukpenunjang_id, pasienmasukpenunjang_t.pendaftaran_id, pendaftaran_t.tgl_pendaftaran, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pegawai_m.nama_pegawai, ruangan_m.ruangan_nama, kelaspelayanan_m.kelaspelayanan_nama, penjamin_m.penjamin_nama, carabayar_m.carabayar_nama, pendaftaran_t.status_bayar, (tindakanpelayanan_t.tarif_tindakan - tindakansudahbayar_t.jmliur_biaya), pemakaianuangmuka_t.total_uangmuka, pasien_m.tanggal_lahir
    UNION ALL
    SELECT 'RUJUKAN RS'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pegawai_m.nama_pegawai AS dokter,
    ruangan_m.ruangan_nama,
    kelaspelayanan_m.kelaspelayanan_nama,
    penjamin_m.penjamin_nama,
    carabayar_m.carabayar_nama,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
    sum(tindakanpelayanan_t.tarif_tindakan) AS total_tagihan,
    sum(tindakansudahbayar_t.jmliur_biaya) AS total_sdh_bayar,
    (tindakanpelayanan_t.tarif_tindakan - tindakansudahbayar_t.jmliur_biaya) AS total_sisa_tagihan,
    pemakaianuangmuka_t.total_uangmuka,
    pasien_m.tanggal_lahir
    FROM ((((((((((((((pasienmasukpenunjang_t
    JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
    JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
    LEFT JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
    JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
    LEFT JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
    LEFT JOIN rujukandari_m ON ((rujukan_t.rujukandari_id = rujukandari_m.rujukandari_id)))
    JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
    JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
    JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
    JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
    JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
    JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
    LEFT JOIN tindakansudahbayar_t ON ((tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id)))
    LEFT JOIN pemakaianuangmuka_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pemakaianuangmuka_t.pendaftaran_id)))
    WHERE (pendaftaran_t.instalasi_id = ANY (ARRAY[4, 21]))
    GROUP BY pasienmasukpenunjang_t.pasienmasukpenunjang_id, pasienmasukpenunjang_t.pendaftaran_id, pendaftaran_t.tgl_pendaftaran, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pegawai_m.nama_pegawai, ruangan_m.ruangan_nama, kelaspelayanan_m.kelaspelayanan_nama, penjamin_m.penjamin_nama, carabayar_m.carabayar_nama, pendaftaran_t.status_bayar, (tindakanpelayanan_t.tarif_tindakan - tindakansudahbayar_t.jmliur_biaya), pemakaianuangmuka_t.total_uangmuka, pasien_m.tanggal_lahir
    UNION ALL
    SELECT 'APS'::text AS tipe_pasien,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    pasienmasukpenunjang_t.pendaftaran_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pegawai_m.nama_pegawai AS dokter,
    ruangan_m.ruangan_nama,
    kelaspelayanan_m.kelaspelayanan_nama,
    penjamin_m.penjamin_nama,
    carabayar_m.carabayar_nama,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
    sum(tindakanpelayanan_t.tarif_tindakan) AS total_tagihan,
    sum(tindakansudahbayar_t.jmliur_biaya) AS total_sdh_bayar,
    (tindakanpelayanan_t.tarif_tindakan - tindakansudahbayar_t.jmliur_biaya) AS total_sisa_tagihan,
    pemakaianuangmuka_t.total_uangmuka,
    pasien_m.tanggal_lahir
    FROM (((((((((((pasienmasukpenunjang_t
    JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
    JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
    JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
    JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
    JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
    JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
    JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
    JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
    JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
    LEFT JOIN tindakansudahbayar_t ON ((tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id)))
    LEFT JOIN pemakaianuangmuka_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pemakaianuangmuka_t.pendaftaran_id)))
    WHERE ((pendaftaran_t.instalasi_id = 4) AND (pendaftaran_t.is_aps = true))
    GROUP BY pasienmasukpenunjang_t.pasienmasukpenunjang_id, pasienmasukpenunjang_t.pendaftaran_id, pendaftaran_t.tgl_pendaftaran, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pegawai_m.nama_pegawai, ruangan_m.ruangan_nama, kelaspelayanan_m.kelaspelayanan_nama, penjamin_m.penjamin_nama, carabayar_m.carabayar_nama, pendaftaran_t.status_bayar, (tindakanpelayanan_t.tarif_tindakan - tindakansudahbayar_t.jmliur_biaya), pemakaianuangmuka_t.total_uangmuka, pasien_m.tanggal_lahir
    ;");
$this->execute('
    ALTER TABLE public.rincianpasienlab_v OWNER TO postgres;
    ');

$this->execute('DROP VIEW if exists public.rincian_header_penunjang_view;');
$this->execute("
    CREATE VIEW \"public\".\"rincian_header_penunjang_view\" AS
    SELECT tagihan.pendaftaran_id,
    tagihan.pasienmasukpenunjang_id,
    sum(tagihan.tagihan_tindakan_obat) AS total_tagihan,
    ((sum(pembayaranpelayanan_t.biaya_administrasi) + sum(pembayaranpelayanan_t.pembulatan)) + sum(pembayaranpelayanan_t.total_terbayar)) AS total_sdh_bayar,
    ((sum(pembayaranpelayanan_t.total_biayapelayanan) - sum(pembayaranpelayanan_t.total_terbayar)) - sum(pembayaranpelayanan_t.total_subsidiasuransi)) AS total_sisa_tagihan,
    sum(COALESCE(bayaruangmuka_t.jumlah_uangmuka, (0)::double precision)) AS total_uang_muka,
    sum(pembayaranpelayanan_t.biaya_administrasi) AS total_administrasi,
    sum(pembayaranpelayanan_t.pembulatan) AS total_pembulatan,
    sum(pembayaranpelayanan_t.total_subsidiasuransi) AS total_asuransi,
    sum(pembayaranpelayanan_t.total_bayartindakan) AS total_pembayaran_pasien
    FROM ((( SELECT dadang.pendaftaran_id,
    sum(dadang.tagihan_tindakan_obat) AS tagihan_tindakan_obat,
    dadang.pembayaranpelayanan_id,
    dadang.pasienmasukpenunjang_id
    FROM ( SELECT pendaftaran_t.pendaftaran_id,
    sum(tindakanpelayanan_t.tarif_tindakan) AS tagihan_tindakan_obat,
    pembayaranpelayanan_t_1.pembayaranpelayanan_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id
    FROM ((((pendaftaran_t
    JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
    LEFT JOIN tindakansudahbayar_t ON ((tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id)))
    LEFT JOIN pembayaranpelayanan_t pembayaranpelayanan_t_1 ON ((pembayaranpelayanan_t_1.pembayaranpelayanan_id = tindakansudahbayar_t.pembayaranpelayanan_id)))
    LEFT JOIN pasienmasukpenunjang_t ON ((pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id)))
    GROUP BY pendaftaran_t.pendaftaran_id, tindakanpelayanan_t.tarif_tindakan, pembayaranpelayanan_t_1.pembayaranpelayanan_id, pasienmasukpenunjang_t.pasienmasukpenunjang_id
    UNION ALL
    SELECT pendaftaran_t.pendaftaran_id,
    sum(obatalkespasien_t.hargajual_oa) AS tagihan_tindakan_obat,
    pembayaranpelayanan_t_1.pembayaranpelayanan_id,
    obatalkespasien_t.pasienmasukpenunjang_id
    FROM ((((pendaftaran_t
    JOIN obatalkespasien_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
    JOIN ruangan_m ON ((obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id)))
    LEFT JOIN obatsudahbayar_t ON ((obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id)))
    LEFT JOIN pembayaranpelayanan_t pembayaranpelayanan_t_1 ON ((obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t_1.pembayaranpelayanan_id)))
    GROUP BY pendaftaran_t.pendaftaran_id, obatalkespasien_t.hargajual_oa, pembayaranpelayanan_t_1.pembayaranpelayanan_id, obatalkespasien_t.pasienmasukpenunjang_id) dadang
    GROUP BY dadang.pendaftaran_id, dadang.pembayaranpelayanan_id, dadang.pasienmasukpenunjang_id) tagihan
    LEFT JOIN bayaruangmuka_t ON ((tagihan.pendaftaran_id = bayaruangmuka_t.pendaftaran_id)))
    LEFT JOIN pembayaranpelayanan_t ON ((tagihan.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
    GROUP BY tagihan.pendaftaran_id, tagihan.pasienmasukpenunjang_id
    ;");
$this->execute('
    ALTER TABLE public.rincian_header_penunjang_view OWNER TO postgres;
    ');

$this->execute('DROP VIEW if exists public.infotagihandetail_v;');
$this->execute("
    CREATE VIEW \"public\".\"infotagihandetail_v\" AS
    SELECT tagihan.pendaftaran_id,
    tagihan.no_pendaftaran,
    tagihan.tgl_pendaftaran,
    tagihan.tgl_pelayanan,
    tagihan.kelompoktindakan_id,
    tagihan.kelompoktindakan_nama,
    tagihan.pelayanan_id,
    tagihan.tindakan_obat_id,
    tagihan.tindakan_obat_nama,
    tagihan.is_obat,
    tagihan.tarif_satuan,
    tagihan.qty,
    tagihan.tarif_cyto,
    tagihan.sub_total,
    tagihan.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_pelayanan,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_pelayanan,
    tagihan.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tagihan.carabayar_pelayanan_id,
    carabayar_m.carabayar_nama AS carabayar_pelayanan,
    tagihan.penjamin_pelayanan_id,
    penjamin_m.penjamin_nama AS penjamin_pelayanan,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    tagihan.dokterpenanggungjawab_id,
    dokter_dpjp.nama_pegawai AS dokterpenanggungjawab_nama,
    pasien_m.pasien_id,
    tagihan.penjamin_pendaftaran_id,
    tagihan.pasienmasukpenunjang_id,
    tagihan.is_cyto
    FROM (((((((( SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
    tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
    tindakanpelayanan_t.tindakansudahbayar_id,
    tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
    daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
    false AS is_obat,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
    tindakanpelayanan_t.tarif_tindakan AS sub_total,
    tindakanpelayanan_t.ruangan_id,
    tindakanpelayanan_t.kelaspelayanan_id,
    tindakanpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
    tindakanpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
    daftartindakan_m.kelompoktindakan_id,
    kelompoktindakan_m.kelompoktindakan_nama,
    pendaftaran_t.pasien_id,
    tindakanpelayanan_t.dokterpenanggungjawab_id,
    pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
    tindakanpelayanan_t.pasienmasukpenunjang_id,
    CASE COALESCE(tindakanpelayanan_t.tarifcyto_tindakan, (0)::double precision)
    WHEN 0 THEN false
    ELSE true
    END AS is_cyto
    FROM (((pendaftaran_t
    JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
    JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
    JOIN kelompoktindakan_m ON ((daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id)))
    WHERE (tindakanpelayanan_t.is_deleted IS FALSE)
    UNION ALL
    SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
    tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
    tindakanpelayanan_t.tindakansudahbayar_id,
    tindakanpelayanan_t.tipepaket_id AS tindakan_obat_id,
    tipepaket_m.tipepaket_nama AS tindakan_obat_nama,
    false AS is_obat,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
    tindakanpelayanan_t.tarif_tindakan AS sub_total,
    tindakanpelayanan_t.ruangan_id,
    tindakanpelayanan_t.kelaspelayanan_id,
    tindakanpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
    tindakanpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
    NULL::integer AS kelompoktindakan_id,
    'kelompok_paket'::character varying AS kelompoktindakan_nama,
    pendaftaran_t.pasien_id,
    tindakanpelayanan_t.dokterpenanggungjawab_id,
    pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
    CASE COALESCE(tindakanpelayanan_t.tarifcyto_tindakan, (0)::double precision)
    WHEN 0 THEN false
    ELSE true
    END AS is_cyto
    FROM (((pendaftaran_t
    JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
    JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
    LEFT JOIN pasienmasukpenunjang_t ON ((pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id)))
    WHERE (tindakanpelayanan_t.is_deleted IS FALSE)
    UNION ALL
    SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
    obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
    obatalkespasien_t.obatsudahbayar_id,
    obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
    obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
    true AS is_obat,
    obatalkespasien_t.hargasatuan_oa,
    obatalkespasien_t.qty_oa AS qty,
    obatalkespasien_t.tarifcyto AS tarif_cyto,
    obatalkespasien_t.hargajual_oa AS sub_total,
    obatalkespasien_t.ruangan_id,
    obatalkespasien_t.kelaspelayanan_id,
    obatalkespasien_t.carabayar_id AS carabayar_pelayanan_id,
    obatalkespasien_t.penjamin_id AS penjamin_pelayanan_id,
    NULL::integer AS kelompoktindakan_id,
    'kelompok_obat'::character varying AS kelompoktindakan_nama,
    pendaftaran_t.pasien_id,
    obatalkespasien_t.pegawai_id AS dokterpenanggungjawab_id,
    pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
    obatalkespasien_t.pasienmasukpenunjang_id,
    CASE COALESCE(obatalkespasien_t.tarifcyto, (0)::double precision)
    WHEN 0 THEN false
    ELSE true
    END AS is_cyto
    FROM ((pendaftaran_t
    JOIN obatalkespasien_t ON (((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id) AND (obatalkespasien_t.is_deleted = false))))
    JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
    WHERE (obatalkespasien_t.is_deleted IS FALSE)) tagihan
    LEFT JOIN ruangan_m ON ((tagihan.ruangan_id = ruangan_m.ruangan_id)))
    LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
    LEFT JOIN kelaspelayanan_m ON ((tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
    LEFT JOIN carabayar_m ON ((tagihan.carabayar_pelayanan_id = carabayar_m.carabayar_id)))
    LEFT JOIN penjamin_m ON ((tagihan.penjamin_pelayanan_id = penjamin_m.penjamin_id)))
    LEFT JOIN pasien_m ON ((tagihan.pasien_id = pasien_m.pasien_id)))
    LEFT JOIN pegawai_m dokter_dpjp ON ((tagihan.dokterpenanggungjawab_id = dokter_dpjp.pegawai_id)))
    ;");
$this->execute('
    ALTER TABLE public.infotagihandetail_v OWNER TO postgres;
    ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210502_123041_migrate_20210502_migratelivebg cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210502_123041_migrate_20210502_migratelivebg cannot be reverted.\n";

        return false;
    }
    */
}
