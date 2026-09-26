<?php

use yii\db\Migration;

/**
 * Class m211221_035728_migrate_US2465_US2460_infopasiennonbpjs_infopengajuanklaim_infopembayaranalokasi
 */
class m211221_035728_migrate_US2465_US2460_infopasiennonbpjs_infopengajuanklaim_infopembayaranalokasi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."pengajuanklaimdetail_t" 
            ADD COLUMN IF NOT exists "pembayaranpelayanan_id" int4;
          ');

        $this->execute('DROP VIEW if exists public.infopasiennonbpjs_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infopasiennonbpjs_v\" AS
            SELECT rincian.tipe,
            rincian.pasien_id,
            rincian.no_rekam_medik,
            rincian.nama_pasien,
            rincian.pendaftaran_id,
            rincian.tgl_pendaftaran,
            rincian.tglpasienpulang,
            rincian.no_pendaftaran,
            rincian.ruangan_id,
            rincian.ruangan_nama,
            rincian.instalasi_id,
            rincian.instalasi_nama,
            rincian.carabayar_id,
            rincian.carabayar_nama,
            rincian.penjamin_id,
            rincian.penjamin_nama,
            rincian.kelaspelayanan_id,
            rincian.kelaspelayanan_nama,
            rincian.jeniskasuspenyakit_id,
            rincian.jeniskasuspenyakit_nama,
            rincian.pegawai_id,
            rincian.dokter,
            rincian.status_bayar,
            pembayaran_t.total_tagihan,
            pembayaran_t.total_dibayar AS total_sdh_bayar,
            CASE
            WHEN (penajuanklaim.total_telahbayar IS NULL) THEN (pembayaran_t.total_dijamin)::integer
            ELSE ((pembayaran_t.total_dijamin)::integer - (penajuanklaim.total_telahbayar)::integer)
            END AS total_sisa_tagihan,
            (pembayaran_t.total_dijamin)::integer AS total_asuransi,
            rincian.is_skd,
            CASE
            WHEN (rincian.is_skd IS FALSE) THEN 'Belum Dibuat'::text
            WHEN (rincian.is_skd IS TRUE) THEN 'Sudah Dibuat'::text
            ELSE NULL::text
            END AS status_skd,
            rincian.pasienadmisi_id,
            rincian.status_verifikasi,
            rincian.lookup_name AS status_verif,
            pembayaran_t.no_pembayaran AS no_invoice,
            penajuanklaim.total_telahbayar AS jumlah_pembayaran,
            pembayaran_t.pembayaran_id
            FROM ((( SELECT 'RJ'::text AS tipe,
            pasien_m.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.carabayar_id,
            carabayar_m_1.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m_1.penjamin_nama,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter,
            pendaftaran_t.status_bayar,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.is_skd,
            NULL::integer AS pasienadmisi_id,
            pendaftaran_t.status_verifikasi,
            fgetnamalookup(pendaftaran_t.status_verifikasi) AS lookup_name,
            NULL::integer AS no_pembayaran
            FROM (((((((((pasien_m
            JOIN pendaftaran_t ON (((pasien_m.pasien_id = pendaftaran_t.pasien_id) AND (pendaftaran_t.instalasi_id <> 2))))
            JOIN carabayar_m carabayar_m_1 ON ((pendaftaran_t.carabayar_id = carabayar_m_1.carabayar_id)))
            JOIN penjamin_m penjamin_m_1 ON ((pendaftaran_t.penjamin_id = penjamin_m_1.penjamin_id)))
            JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            WHERE ((pendaftaran_t.is_active = true) AND (pendaftaran_t.is_deleted = false) AND (carabayar_m_1.carabayar_id <> 5))
            UNION ALL
            SELECT 'RI'::text AS tipe,
            pasien_m.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pasienadmisi_t.carabayar_id,
            carabayar_m_1.carabayar_nama,
            pasienadmisi_t.penjamin_id,
            penjamin_m_1.penjamin_nama,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pasienadmisi_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter,
            pendaftaran_t.status_bayar,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pasienadmisi_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pasienpulang_t.tglpasienpulang,
            pasienadmisi_t.is_skd,
            pasienadmisi_t.pasienadmisi_id,
            pasienadmisi_t.status_verifikasi,
            fgetnamalookup(pasienadmisi_t.status_verifikasi) AS lookup_name,
            NULL::integer AS no_pembayaran
            FROM ((((((((((pasien_m
            JOIN pendaftaran_t ON (((pasien_m.pasien_id = pendaftaran_t.pasien_id) AND (pendaftaran_t.instalasi_id <> 2))))
            JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            JOIN carabayar_m carabayar_m_1 ON ((pasienadmisi_t.carabayar_id = carabayar_m_1.carabayar_id)))
            JOIN penjamin_m penjamin_m_1 ON ((pasienadmisi_t.penjamin_id = penjamin_m_1.penjamin_id)))
            JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
            JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            JOIN pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            WHERE ((pasienadmisi_t.is_active = true) AND (pasienadmisi_t.is_deleted = false) AND (pendaftaran_t.is_active = true) AND (pendaftaran_t.is_deleted = false) AND (carabayar_m_1.carabayar_id <> 5))
            UNION ALL
            SELECT 'LAB'::text AS tipe,
            pasien_m.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.carabayar_id,
            carabayar_m_1.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m_1.penjamin_nama,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter,
            pendaftaran_t.status_bayar,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pasienmasukpenunjang_t.last_modified_date AS tglpasienpulang,
            pendaftaran_t.is_skd,
            NULL::integer AS pasienadmisi_id,
            pendaftaran_t.status_verifikasi,
            fgetnamalookup(pendaftaran_t.status_verifikasi) AS lookup_name,
            NULL::integer AS no_pembayaran
            FROM ((((((((((pasien_m
            JOIN pendaftaran_t ON (((pasien_m.pasien_id = pendaftaran_t.pasien_id) AND (pendaftaran_t.instalasi_id = 4))))
            JOIN carabayar_m carabayar_m_1 ON ((pendaftaran_t.carabayar_id = carabayar_m_1.carabayar_id)))
            JOIN penjamin_m penjamin_m_1 ON ((pendaftaran_t.penjamin_id = penjamin_m_1.penjamin_id)))
            JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN pasienmasukpenunjang_t ON ((pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id)))
            JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
            WHERE ((pendaftaran_t.is_active = true) AND (pendaftaran_t.is_deleted = false) AND (carabayar_m_1.carabayar_id <> 5))
            UNION ALL
            SELECT 'RAD'::text AS tipe,
            pasien_m.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.carabayar_id,
            carabayar_m_1.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m_1.penjamin_nama,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter,
            pendaftaran_t.status_bayar,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pasienmasukpenunjang_t.last_modified_date AS tglpasienpulang,
            pendaftaran_t.is_skd,
            NULL::integer AS pasienadmisi_id,
            pendaftaran_t.status_verifikasi,
            fgetnamalookup(pendaftaran_t.status_verifikasi) AS lookup_name,
            NULL::integer AS no_pembayaran
            FROM ((((((((((pasien_m
            JOIN pendaftaran_t ON (((pasien_m.pasien_id = pendaftaran_t.pasien_id) AND (pendaftaran_t.instalasi_id = 5))))
            JOIN carabayar_m carabayar_m_1 ON ((pendaftaran_t.carabayar_id = carabayar_m_1.carabayar_id)))
            JOIN penjamin_m penjamin_m_1 ON ((pendaftaran_t.penjamin_id = penjamin_m_1.penjamin_id)))
            JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN pasienmasukpenunjang_t ON ((pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id)))
            JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
            WHERE ((pendaftaran_t.is_active = true) AND (pendaftaran_t.is_deleted = false) AND (carabayar_m_1.carabayar_id <> 5))
            UNION ALL
            SELECT 'MCU'::text AS tipe,
            pasien_m.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.carabayar_id,
            carabayar_m_1.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m_1.penjamin_nama,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter,
            pendaftaran_t.status_bayar,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pendaftaran_t.tgl_pendaftaran AS tglpasienpulang,
            pendaftaran_t.is_skd,
            NULL::integer AS pasienadmisi_id,
            pendaftaran_t.status_verifikasi,
            fgetnamalookup(pendaftaran_t.status_verifikasi) AS lookup_name,
            NULL::integer AS no_pembayaran
            FROM ((((((((pasien_m
            JOIN pendaftaran_t ON (((pasien_m.pasien_id = pendaftaran_t.pasien_id) AND (pendaftaran_t.instalasi_id = 21))))
            JOIN carabayar_m carabayar_m_1 ON ((pendaftaran_t.carabayar_id = carabayar_m_1.carabayar_id)))
            JOIN penjamin_m penjamin_m_1 ON ((pendaftaran_t.penjamin_id = penjamin_m_1.penjamin_id)))
            JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            WHERE ((pendaftaran_t.is_active = true) AND (pendaftaran_t.is_deleted = false) AND (carabayar_m_1.carabayar_id <> 5))
            UNION ALL
            SELECT 'RD-RI'::text AS tipe,
            pasien_m.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.carabayar_id,
            carabayar_m_1.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m_1.penjamin_nama,
            pendaftaran_t.kelaspelayanan_id,
            kelaspelayanan_m.kelaspelayanan_nama,
            pendaftaran_t.jeniskasuspenyakit_id,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.pegawai_id,
            pegawai_m.nama_pegawai AS dokter,
            pendaftaran_t.status_bayar,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.is_skd,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.status_verifikasi,
            fgetnamalookup(pendaftaran_t.status_verifikasi) AS lookup_name,
            pendaftaran_t.no_pembayaran
            FROM (((((((((pasien_m
            JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
            pendaftaran_t_1.pasienadmisi_id,
            pendaftaran_t_1.tgl_pendaftaran,
            pendaftaran_t_1.no_pendaftaran,
            CASE
            WHEN (pasienadmisi_t.carabayar_id IS NULL) THEN pendaftaran_t_1.carabayar_id
            ELSE pasienadmisi_t.carabayar_id
            END AS carabayar_id,
            CASE
            WHEN (pasienadmisi_t.penjamin_id IS NULL) THEN pendaftaran_t_1.penjamin_id
            ELSE pasienadmisi_t.penjamin_id
            END AS penjamin_id,
            CASE
            WHEN (pasienadmisi_t.kelaspelayanan_id IS NULL) THEN pendaftaran_t_1.kelaspelayanan_id
            ELSE pasienadmisi_t.kelaspelayanan_id
            END AS kelaspelayanan_id,
            pendaftaran_t_1.jeniskasuspenyakit_id,
            CASE
            WHEN (pasienadmisi_t.pegawai_id IS NULL) THEN pendaftaran_t_1.pegawai_id
            ELSE pasienadmisi_t.pegawai_id
            END AS pegawai_id,
            CASE
            WHEN (pasienadmisi_t.ruangan_id IS NULL) THEN pendaftaran_t_1.ruangan_id
            ELSE pasienadmisi_t.ruangan_id
            END AS ruangan_id,
            CASE
            WHEN (pasienadmisi_t.ruangan_id IS NULL) THEN pendaftaran_t_1.instalasi_id
            ELSE 3
            END AS instalasi_id,
            CASE
            WHEN (pasienadmisi_t.pasienpulang_id IS NULL) THEN pendaftaran_t_1.pasienpulang_id
            ELSE pasienadmisi_t.pasienpulang_id
            END AS pasienpulang_id,
            CASE
            WHEN (pasienadmisi_t.is_skd IS NULL) THEN pendaftaran_t_1.is_skd
            ELSE pasienadmisi_t.is_skd
            END AS is_skd,
            CASE
            WHEN (pasienadmisi_t.status_verifikasi IS NULL) THEN pendaftaran_t_1.status_verifikasi
            ELSE pasienadmisi_t.status_verifikasi
            END AS status_verifikasi,
            pendaftaran_t_1.status_bayar,
            pendaftaran_t_1.pasien_id,
            NULL::integer AS no_pembayaran
            FROM (pendaftaran_t pendaftaran_t_1
            LEFT JOIN pasienadmisi_t ON ((pasienadmisi_t.pasienadmisi_id = pendaftaran_t_1.pasienadmisi_id)))
            WHERE (pendaftaran_t_1.instalasi_id = 2)) pendaftaran_t ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN carabayar_m carabayar_m_1 ON (((pendaftaran_t.carabayar_id = carabayar_m_1.carabayar_id) AND (carabayar_m_1.carabayar_id <> 5))))
            JOIN penjamin_m penjamin_m_1 ON ((pendaftaran_t.penjamin_id = penjamin_m_1.penjamin_id)))
            JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))) rincian
            LEFT JOIN ( SELECT ((a.total_tagihan + a.total_administrasi) + a.total_pembulatan) AS total_tagihan,
            a.total_dibayar,
            a.total_dijamin,
            pembayaranpelayanan_t.no_pembayaran,
            a.total_sisatagihan,
            a.pendaftaran_id,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            a.pembayaran_id
            FROM (pembayaran_t a
            JOIN ( SELECT a1.pembayaran_id,
            a1.no_pembayaran,
            a1.pembayaranpelayanan_id
            FROM pembayaranpelayanan_t a1
            WHERE (a1.is_deleted = false)) pembayaranpelayanan_t ON ((a.pembayaran_id = pembayaranpelayanan_t.pembayaran_id)))
            WHERE (a.is_deleted = false)) pembayaran_t ON ((rincian.pendaftaran_id = pembayaran_t.pendaftaran_id)))
            LEFT JOIN ( SELECT sum(pengajuanklaimdetail_t.jumlah_telahbayar) AS total_telahbayar,
            pengajuanklaimdetail_t.pembayaranpelayanan_id
            FROM pengajuanklaimdetail_t
            GROUP BY pengajuanklaimdetail_t.pembayaranpelayanan_id) penajuanklaim ON ((penajuanklaim.pembayaranpelayanan_id = pembayaran_t.pembayaranpelayanan_id)))
            GROUP BY rincian.tipe, rincian.pasien_id, rincian.no_rekam_medik, rincian.nama_pasien, rincian.pendaftaran_id, rincian.tgl_pendaftaran, rincian.tglpasienpulang, rincian.no_pendaftaran, rincian.ruangan_id, rincian.ruangan_nama, rincian.instalasi_id, rincian.instalasi_nama, rincian.kelaspelayanan_id, rincian.kelaspelayanan_nama, rincian.jeniskasuspenyakit_id, rincian.jeniskasuspenyakit_nama, rincian.pegawai_id, rincian.dokter, rincian.status_bayar, rincian.is_skd,
            CASE
            WHEN (rincian.is_skd IS FALSE) THEN 'Belum Dibuat'::text
            WHEN (rincian.is_skd IS TRUE) THEN 'Sudah Dibuat'::text
            ELSE NULL::text
            END, rincian.pasienadmisi_id, rincian.status_verifikasi, rincian.lookup_name, rincian.no_pembayaran, rincian.carabayar_id, rincian.carabayar_nama, rincian.penjamin_id, rincian.penjamin_nama, pembayaran_t.total_tagihan, pembayaran_t.total_sisatagihan, pembayaran_t.total_dijamin, pembayaran_t.no_pembayaran, pembayaran_t.total_dibayar, penajuanklaim.total_telahbayar, pembayaran_t.pembayaran_id
            ;");
        $this->execute('
            ALTER TABLE public.infopasiennonbpjs_v OWNER TO postgres;
            ');

        $this->execute('DROP VIEW if exists public.infopengajuanklaimdetail_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infopengajuanklaimdetail_v\" AS
            SELECT rincian.pengajuanklaim_id,
            rincian.pengajuanklaimdetail_id,
            rincian.no_pengajuanklaim,
            rincian.pendaftaran_id,
            rincian.pasienadmisi_id,
            rincian.pasien_id,
            rincian.nama_pasien,
            rincian.no_rekam_medik,
            rincian.jumlah_bayar,
            rincian.jumlah_piutang,
            rincian.total_dibayar AS jumlah_telahbayar,
            rincian.jumlah_sisapiutang,
            rincian.no_pendaftaran,
            rincian.tgl_pendaftaran,
            rincian.tglpasienpulang,
            rincian.nosep,
            rincian.instalasi_id,
            rincian.instalasi_nama,
            rincian.ruangan_id,
            rincian.ruangan_nama,
            rincian.total_tagihan,
            rincian.total_tarifrs,
            rincian.no_pembayaran,
            rincian.total_dijamin,
            rincian.total_dibayar,
            rincian.no_pengajuanklaim AS no_invoice,
            rincian.pembayaran_id
            FROM ( SELECT pengajuanklaim_t.pengajuanklaim_id,
            pengajuanklaimdetail_t.pengajuanklaimdetail_id,
            pengajuanklaim_t.no_pengajuanklaim,
            pengajuanklaimdetail_t.pendaftaran_id,
            pengajuanklaimdetail_t.pasienadmisi_id,
            pengajuanklaimdetail_t.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            pengajuanklaimdetail_t.jumlah_bayar,
            pengajuanklaimdetail_t.jumlah_piutang,
            pengajuanklaimdetail_t.jumlah_telahbayar,
            pengajuanklaimdetail_t.jumlah_sisapiutang,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            pasienpulang_t.tglpasienpulang,
            bpjs_t.nosep,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pembayaran_t.total_tagihan,
            pembayaran_t.total_tagihan AS total_tarifrs,
            pembayaran_t.no_pembayaran,
            pembayaran_t.total_dijamin,
            pembayaran_t.total_dibayar,
            pembayaran_t.pembayaran_id
            FROM ((((((((pengajuanklaim_t
            JOIN pengajuanklaimdetail_t ON (((pengajuanklaim_t.pengajuanklaim_id = pengajuanklaimdetail_t.pengajuanklaim_id) AND (pengajuanklaimdetail_t.is_deleted = false))))
            JOIN pasien_m ON ((pengajuanklaimdetail_t.pasien_id = pasien_m.pasien_id)))
            JOIN pendaftaran_t ON (((pengajuanklaimdetail_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (pengajuanklaimdetail_t.pasienadmisi_id IS NULL))))
            JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            LEFT JOIN bpjs_t ON ((bpjs_t.bpjs_id = pendaftaran_t.bpjs_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            ((a.total_tagihan + a.total_administrasi) + a.total_pembulatan) AS total_tagihan,
            a.total_dibayar,
            a.total_dijamin,
            pembayaranpelayanan_t.no_pembayaran,
            a.total_sisatagihan,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            a.pembayaran_id
            FROM (pembayaran_t a
            JOIN pembayaranpelayanan_t ON ((a.pembayaran_id = pembayaranpelayanan_t.pembayaran_id)))
            WHERE (a.is_deleted = false)) pembayaran_t ON ((pengajuanklaimdetail_t.pembayaranpelayanan_id = pembayaran_t.pembayaranpelayanan_id)))
            UNION ALL
            SELECT pengajuanklaim_t.pengajuanklaim_id,
            pengajuanklaimdetail_t.pengajuanklaimdetail_id,
            pengajuanklaim_t.no_pengajuanklaim,
            pengajuanklaimdetail_t.pendaftaran_id,
            pengajuanklaimdetail_t.pasienadmisi_id,
            pengajuanklaimdetail_t.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            pengajuanklaimdetail_t.jumlah_bayar,
            pengajuanklaimdetail_t.jumlah_piutang,
            pengajuanklaimdetail_t.jumlah_telahbayar,
            pengajuanklaimdetail_t.jumlah_sisapiutang,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            pasienpulang_t.tglpasienpulang,
            bpjs_t.nosep,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pasienadmisi_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pembayaran_t.total_tagihan,
            pembayaran_t.total_tagihan AS total_tarifrs,
            pembayaran_t.no_pembayaran,
            pembayaran_t.total_dijamin,
            pembayaran_t.total_dibayar,
            pembayaran_t.pembayaran_id
            FROM (((((((((pengajuanklaim_t
            JOIN pengajuanklaimdetail_t ON (((pengajuanklaim_t.pengajuanklaim_id = pengajuanklaimdetail_t.pengajuanklaim_id) AND (pengajuanklaimdetail_t.is_deleted = false))))
            JOIN pasien_m ON ((pengajuanklaimdetail_t.pasien_id = pasien_m.pasien_id)))
            JOIN pendaftaran_t ON (((pengajuanklaimdetail_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (pengajuanklaimdetail_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id))))
            JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            JOIN pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            LEFT JOIN bpjs_t ON ((bpjs_t.bpjs_id = pasienadmisi_t.bpjs_id)))
            JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN ( SELECT b.pendaftaran_id,
            ((b.total_tagihan + b.total_administrasi) + b.total_pembulatan) AS total_tagihan,
            b.total_dibayar,
            b.total_dijamin,
            pembayaranpelayanan_t.no_pembayaran,
            b.total_sisatagihan,
            pembayaranpelayanan_t.pembayaranpelayanan_id,
            b.pembayaran_id
            FROM (pembayaran_t b
            JOIN pembayaranpelayanan_t ON ((b.pembayaran_id = pembayaranpelayanan_t.pembayaran_id)))
            WHERE (b.is_deleted = false)) pembayaran_t ON ((pengajuanklaimdetail_t.pembayaranpelayanan_id = pembayaran_t.pembayaranpelayanan_id)))) rincian
            ;");
        $this->execute('
            ALTER TABLE public.infopengajuanklaimdetail_v OWNER TO postgres;
            ');

        $this->execute('DROP VIEW if exists public.infopembayaranalokasidetail_v;');
        $this->execute("
            CREATE VIEW \"public\".\"infopembayaranalokasidetail_v\" AS
            SELECT pengajuan.pengajuanklaim_id,
            pengajuan.pengajuanklaimdetail_id,
            pengajuan.no_pengajuanklaim,
            pengajuan.pendaftaran_id,
            pengajuan.pasienadmisi_id,
            pengajuan.pasien_id,
            pengajuan.nama_pasien,
            pengajuan.no_rekam_medik,
            pengajuan.jumlah_bayar,
            pengajuan.jumlah_piutang,
            pengajuan.jumlah_telahbayar,
            pengajuan.jumlah_sisapiutang,
            pengajuan.tgl_pendaftaran,
            pengajuan.tglpasienpulang,
            pengajuan.no_pendaftaran,
            pengajuan.nosep,
            pengajuan.instalasi_id,
            pengajuan.instalasi_nama,
            pengajuan.ruangan_id,
            pengajuan.ruangan_nama,
            pembayaranalokasidetail_t.jumlah_bayar AS bayar_alokasi,
            pembayaranalokasidetail_t.pembayaranalokasidetail_id,
            pembayaranalokasidetail_t.pembayaranalokasi_id,
            pengajuan.total_tagihan,
            pengajuan.no_pembayaran,
            pengajuan.total_dijamin,
            pengajuan.total_dibayar
            FROM (( SELECT pengajuanklaim_t.pengajuanklaim_id,
            pengajuanklaimdetail_t.pengajuanklaimdetail_id,
            pengajuanklaim_t.no_pengajuanklaim,
            pengajuanklaimdetail_t.pendaftaran_id,
            pengajuanklaimdetail_t.pasienadmisi_id,
            pengajuanklaimdetail_t.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            pengajuanklaimdetail_t.jumlah_bayar,
            pengajuanklaimdetail_t.jumlah_piutang,
            pengajuanklaimdetail_t.jumlah_telahbayar,
            pengajuanklaimdetail_t.jumlah_sisapiutang,
            pendaftaran_t.tgl_pendaftaran,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.no_pendaftaran,
            bpjs_t.nosep,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pembayaran.total_tagihan,
            pembayaran.total_tagihan,
            pembayaran.no_pembayaran,
            pembayaran.total_dijamin,
            pembayaran.total_dibayar
            FROM ((((((((pengajuanklaim_t
            JOIN pengajuanklaimdetail_t ON ((pengajuanklaim_t.pengajuanklaim_id = pengajuanklaimdetail_t.pengajuanklaim_id)))
            JOIN pasien_m ON ((pengajuanklaimdetail_t.pasien_id = pasien_m.pasien_id)))
            JOIN pendaftaran_t ON ((pengajuanklaimdetail_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
            JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            LEFT JOIN bpjs_t ON ((bpjs_t.bpjs_id = pendaftaran_t.bpjs_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN ( SELECT a.pendaftaran_id,
            ((a.total_tagihan + a.total_administrasi) + a.total_pembulatan) AS total_tagihan,
            a.total_dibayar,
            a.total_dijamin,
            a.no_pembayaran,
            a.total_sisatagihan,
            pembayaranpelayanan_t.pembayaranpelayanan_id
            FROM (pembayaran_t a
            JOIN pembayaranpelayanan_t ON ((a.pembayaran_id = pembayaranpelayanan_t.pembayaran_id)))
            WHERE (a.is_deleted = false)) pembayaran ON ((pengajuanklaimdetail_t.pembayaranpelayanan_id = pembayaran.pembayaranpelayanan_id)))
            WHERE ((pengajuanklaimdetail_t.pasienadmisi_id IS NULL) AND (pengajuanklaimdetail_t.is_deleted = false))
            UNION ALL
            SELECT pengajuanklaim_t.pengajuanklaim_id,
            pengajuanklaimdetail_t.pengajuanklaimdetail_id,
            pengajuanklaim_t.no_pengajuanklaim,
            pengajuanklaimdetail_t.pendaftaran_id,
            pengajuanklaimdetail_t.pasienadmisi_id,
            pengajuanklaimdetail_t.pasien_id,
            pasien_m.nama_pasien,
            pasien_m.no_rekam_medik,
            pengajuanklaimdetail_t.jumlah_bayar,
            pengajuanklaimdetail_t.jumlah_piutang,
            pengajuanklaimdetail_t.jumlah_telahbayar,
            pengajuanklaimdetail_t.jumlah_sisapiutang,
            pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.no_pendaftaran,
            bpjs_t.nosep,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pasienadmisi_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pembayaran.total_tagihan,
            pembayaran.total_tagihan,
            pembayaran.no_pembayaran,
            pembayaran.total_dijamin,
            pembayaran.total_dibayar
            FROM (((((((((pengajuanklaim_t
            JOIN pengajuanklaimdetail_t ON ((pengajuanklaim_t.pengajuanklaim_id = pengajuanklaimdetail_t.pengajuanklaim_id)))
            JOIN pasien_m ON ((pengajuanklaimdetail_t.pasien_id = pasien_m.pasien_id)))
            JOIN pasienadmisi_t ON ((pengajuanklaimdetail_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            JOIN pendaftaran_t ON ((pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id)))
            JOIN pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            LEFT JOIN bpjs_t ON ((bpjs_t.bpjs_id = pasienadmisi_t.bpjs_id)))
            JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            LEFT JOIN ( SELECT b.pendaftaran_id,
            ((b.total_tagihan + b.total_administrasi) + b.total_pembulatan) AS total_tagihan,
            b.total_dibayar,
            b.total_dijamin,
            b.no_pembayaran,
            b.total_sisatagihan,
            pembayaranpelayanan_t.pembayaranpelayanan_id
            FROM (pembayaran_t b
            JOIN pembayaranpelayanan_t ON ((b.pembayaran_id = pembayaranpelayanan_t.pembayaran_id)))
            WHERE (b.is_deleted = false)) pembayaran ON ((pengajuanklaimdetail_t.pembayaranpelayanan_id = pembayaran.pembayaranpelayanan_id)))
            WHERE (pengajuanklaimdetail_t.is_deleted = false)) pengajuan(pengajuanklaim_id, pengajuanklaimdetail_id, no_pengajuanklaim, pendaftaran_id, pasienadmisi_id, pasien_id, nama_pasien, no_rekam_medik, jumlah_bayar, jumlah_piutang, jumlah_telahbayar, jumlah_sisapiutang, tgl_pendaftaran, tglpasienpulang, no_pendaftaran, nosep, instalasi_id, instalasi_nama, ruangan_id, ruangan_nama, total_tagihan, total_tagihan_1, no_pembayaran, total_dijamin, total_dibayar)
            JOIN pembayaranalokasidetail_t ON ((pembayaranalokasidetail_t.pengajuanklaimdetail_id = pengajuan.pengajuanklaimdetail_id)))
            WHERE (pembayaranalokasidetail_t.is_deleted = false)
            ;");
        $this->execute('
            ALTER TABLE public.infopembayaranalokasidetail_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211221_035728_migrate_US2465_US2460_infopasiennonbpjs_infopengajuanklaim_infopembayaranalokasi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211221_035728_migrate_US2465_US2460_infopasiennonbpjs_infopengajuanklaim_infopembayaranalokasi cannot be reverted.\n";

        return false;
    }
    */
}
