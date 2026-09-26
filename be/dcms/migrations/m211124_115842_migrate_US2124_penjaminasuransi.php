<?php

use yii\db\Migration;

/**
 * Class m211124_115842_migrate_US2124_penjaminasuransi
 */
class m211124_115842_migrate_US2124_penjaminasuransi extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
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
            pembayaranpelayanan_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pembayaranpelayanan_t.penjamin_id,
            penjamin_m.penjamin_nama,
            rincian.kelaspelayanan_id,
            rincian.kelaspelayanan_nama,
            rincian.jeniskasuspenyakit_id,
            rincian.jeniskasuspenyakit_nama,
            rincian.pegawai_id,
            rincian.dokter,
            rincian.status_bayar,
            rincian_tagihan.total_tagihan,
            rincianpasien_detail.total_sdh_bayar,
            sum(cektagihanpejamin.total) AS total_sisa_tagihan,
            sum(cektagihanpejamin.total) AS total_asuransi,
            rincian.is_skd,
            CASE
            WHEN (rincian.is_skd IS FALSE) THEN 'Belum Dibuat'::text
            WHEN (rincian.is_skd IS TRUE) THEN 'Sudah Dibuat'::text
            ELSE NULL::text
            END AS status_skd,
            rincian.pasienadmisi_id,
            rincian.status_verifikasi,
            rincian.lookup_name AS status_verif,
            rincian.no_pembayaran AS no_invoice
            FROM ((((((( SELECT 'RD'::text AS tipe,
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
            valid_cara_bayar.no_pembayaran
            FROM ((((((((((pasien_m
            JOIN pendaftaran_t ON (((pasien_m.pasien_id = pendaftaran_t.pasien_id) AND (pendaftaran_t.instalasi_id <> 2))))
            JOIN carabayar_m carabayar_m_1 ON ((pendaftaran_t.carabayar_id = carabayar_m_1.carabayar_id)))
            JOIN penjamin_m penjamin_m_1 ON ((pendaftaran_t.penjamin_id = penjamin_m_1.penjamin_id)))
            JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            JOIN ( SELECT pembayaranpelayanan_t_1.pendaftaran_id,
            pembayaranpelayanan_t_1.no_pembayaran
            FROM pembayaranpelayanan_t pembayaranpelayanan_t_1
            WHERE ((pembayaranpelayanan_t_1.carabayar_id <> ALL (ARRAY[5, 6])) AND (pembayaranpelayanan_t_1.is_deleted = false))
            GROUP BY pembayaranpelayanan_t_1.pendaftaran_id, pembayaranpelayanan_t_1.no_pembayaran) valid_cara_bayar ON ((valid_cara_bayar.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
            WHERE ((pendaftaran_t.is_active = true) AND (pendaftaran_t.is_deleted = false) AND (carabayar_m_1.carabayar_id <> ALL (ARRAY[5, 6])))
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
            valid_cara_bayar.no_pembayaran
            FROM (((((((((((pasien_m
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
            JOIN ( SELECT pembayaranpelayanan_t_1.pendaftaran_id,
            pembayaranpelayanan_t_1.no_pembayaran
            FROM pembayaranpelayanan_t pembayaranpelayanan_t_1
            WHERE ((pembayaranpelayanan_t_1.carabayar_id <> ALL (ARRAY[5, 6])) AND (pembayaranpelayanan_t_1.is_deleted = false))
            GROUP BY pembayaranpelayanan_t_1.pendaftaran_id, pembayaranpelayanan_t_1.no_pembayaran) valid_cara_bayar ON ((valid_cara_bayar.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
            WHERE ((pasienadmisi_t.is_active = true) AND (pasienadmisi_t.is_deleted = false) AND (pendaftaran_t.is_active = true) AND (pendaftaran_t.is_deleted = false) AND (carabayar_m_1.carabayar_id <> ALL (ARRAY[5, 6])))
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
            valid_cara_bayar.no_pembayaran
            FROM ((pendaftaran_t pendaftaran_t_1
            LEFT JOIN pasienadmisi_t ON ((pasienadmisi_t.pasienadmisi_id = pendaftaran_t_1.pasienadmisi_id)))
            JOIN ( SELECT pembayaranpelayanan_t_1.pendaftaran_id,
            pembayaranpelayanan_t_1.no_pembayaran
            FROM pembayaranpelayanan_t pembayaranpelayanan_t_1
            WHERE ((pembayaranpelayanan_t_1.carabayar_id <> ALL (ARRAY[5, 6])) AND (pembayaranpelayanan_t_1.is_deleted = false))
            GROUP BY pembayaranpelayanan_t_1.pendaftaran_id, pembayaranpelayanan_t_1.no_pembayaran) valid_cara_bayar ON ((valid_cara_bayar.pendaftaran_id = pendaftaran_t_1.pendaftaran_id)))
            WHERE (pendaftaran_t_1.instalasi_id = 2)) pendaftaran_t ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN carabayar_m carabayar_m_1 ON ((pendaftaran_t.carabayar_id = carabayar_m_1.carabayar_id)))
            JOIN penjamin_m penjamin_m_1 ON ((pendaftaran_t.penjamin_id = penjamin_m_1.penjamin_id)))
            JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
            JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
            JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))) rincian
            LEFT JOIN ( SELECT tagihan.pendaftaran_id,
            tagihan.pasienadmisi_id,
            pasien_m.no_rekam_medik,
            sum(tagihan.tagihan_tindakan_obat) AS total_tagihan,
            sum(tagihan.tagihan_sudah_bayar) AS total_sdh_bayar,
            ((sum(tagihan.tagihan_tindakan_obat) - sum(tagihan.tagihan_sudah_bayar)) - sum(tagihan.tagihan_asuransi)) AS total_sisa_tagihan,
            sum(tagihan.tagihan_uang_muka) AS total_uang_muka,
            sum(tagihan.tagihan_biaya_admin) AS total_administrasi,
            sum(tagihan.tagihan_pembulatan) AS total_pembulatan,
            sum(tagihan.tagihan_asuransi) AS total_asuransi,
            sum(tagihan.pembayaran_pasien) AS total_pembayaran_pasien
            FROM (( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            0 AS tagihan_tindakan_obat,
            0 AS tagihan_sudah_bayar,
            sum(bayaruangmuka_t.jumlah_uangmuka) AS tagihan_uang_muka,
            0 AS tagihan_biaya_admin,
            0 AS tagihan_pembulatan,
            0 AS tagihan_asuransi,
            0 AS pembayaran_pasien,
            pendaftaran_t.pasien_id
            FROM (pendaftaran_t
            LEFT JOIN bayaruangmuka_t ON ((pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id)))
            GROUP BY pendaftaran_t.pendaftaran_id, bayaruangmuka_t.jumlah_uangmuka, pendaftaran_t.pasienadmisi_id
            UNION ALL
            SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            sum(pembayaranpelayanan_t_1.total_biayapelayanan) AS tagihan_tindakan_obat,
            sum(pembayaranpelayanan_t_1.total_terbayar) AS tagihan_sudah_bayar,
            0 AS tagihan_uang_muka,
            sum(pembayaranpelayanan_t_1.biaya_administrasi) AS tagihan_biaya_admin,
            sum(pembayaranpelayanan_t_1.pembulatan) AS tagihan_pembulatan,
            sum(pembayaranpelayanan_t_1.total_subsidiasuransi) AS tagihan_asuransi,
            sum(pembayaranpelayanan_t_1.total_bayartindakan) AS pembayaran_pasien,
            pendaftaran_t.pasien_id
            FROM (pendaftaran_t
            LEFT JOIN pembayaranpelayanan_t pembayaranpelayanan_t_1 ON ((pendaftaran_t.pendaftaran_id = pembayaranpelayanan_t_1.pendaftaran_id)))
            GROUP BY pendaftaran_t.pendaftaran_id, pembayaranpelayanan_t_1.biaya_administrasi, pembayaranpelayanan_t_1.pembulatan, pendaftaran_t.pasienadmisi_id, pembayaranpelayanan_t_1.total_subsidiasuransi) tagihan
            LEFT JOIN pasien_m ON ((tagihan.pasien_id = pasien_m.pasien_id)))
            GROUP BY tagihan.pendaftaran_id, tagihan.pasienadmisi_id, pasien_m.no_rekam_medik) rincianpasien_detail ON ((rincian.pendaftaran_id = rincianpasien_detail.pendaftaran_id)))
            LEFT JOIN pembayaranpelayanan_t ON ((rincian.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id)))
            LEFT JOIN carabayar_m ON ((pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id)))
            LEFT JOIN penjamin_m ON ((pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id)))
            LEFT JOIN ( SELECT hitung.pendaftaran_id,
            CASE
            WHEN (hitung.instalasi_id = 3) THEN pendaftaran_t.pasienadmisi_id
            ELSE NULL::integer
            END AS pasienadmisi_id,
            hitung.instalasi_id,
            hitung.instalasi_nama,
            sum(hitung.tarif) AS total,
            hitung.penjamin_id,
            penjamin_m_1.penjamin_nama
            FROM ((( SELECT tindakanpelayanan_t.pendaftaran_id,
            tindakanpelayanan_t.instalasi_id,
            instalasi_m.instalasi_nama,
            tindakanpelayanan_t.penjamin_id,
            sum(tindakanpelayanan_t.tarif_tindakan) AS tarif
            FROM (tindakanpelayanan_t
            LEFT JOIN instalasi_m ON ((tindakanpelayanan_t.instalasi_id = instalasi_m.instalasi_id)))
            WHERE (tindakanpelayanan_t.pasienmasukpenunjang_id IS NULL)
            GROUP BY tindakanpelayanan_t.pendaftaran_id, tindakanpelayanan_t.instalasi_id, instalasi_m.instalasi_nama, tindakanpelayanan_t.penjamin_id
            UNION ALL
            SELECT tindakanpelayanan_t.pendaftaran_id,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            tindakanpelayanan_t.penjamin_id,
            sum(tindakanpelayanan_t.tarif_tindakan) AS tarif
            FROM (((tindakanpelayanan_t
            LEFT JOIN pasienmasukpenunjang_t ON ((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id)))
            LEFT JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
            LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            WHERE (tindakanpelayanan_t.pasienmasukpenunjang_id IS NOT NULL)
            GROUP BY tindakanpelayanan_t.pendaftaran_id, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, tindakanpelayanan_t.penjamin_id
            UNION ALL
            SELECT obatalkespasien_t.pendaftaran_id,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            obatalkespasien_t.penjamin_id,
            sum(obatalkespasien_t.hargajual_oa) AS sum
            FROM ((obatalkespasien_t
            LEFT JOIN ruangan_m ON ((obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id)))
            LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            WHERE (obatalkespasien_t.resepturdetail_id IS NULL)
            GROUP BY obatalkespasien_t.pendaftaran_id, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, obatalkespasien_t.penjamin_id
            UNION ALL
            SELECT obatalkespasien_t.pendaftaran_id,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            obatalkespasien_t.penjamin_id,
            sum(obatalkespasien_t.hargajual_oa) AS sum
            FROM ((((obatalkespasien_t
            LEFT JOIN resepturdetail_t ON ((obatalkespasien_t.resepturdetail_id = resepturdetail_t.resepturdetail_id)))
            LEFT JOIN reseptur_t ON ((resepturdetail_t.reseptur_id = reseptur_t.reseptur_id)))
            LEFT JOIN ruangan_m ON ((reseptur_t.ruanganreseptur_id = ruangan_m.ruangan_id)))
            LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            WHERE (obatalkespasien_t.resepturdetail_id IS NOT NULL)
            GROUP BY obatalkespasien_t.pendaftaran_id, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, obatalkespasien_t.penjamin_id) hitung
            JOIN pendaftaran_t ON ((hitung.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
            JOIN penjamin_m penjamin_m_1 ON ((hitung.penjamin_id = penjamin_m_1.penjamin_id)))
            GROUP BY hitung.pendaftaran_id, hitung.instalasi_id, hitung.instalasi_nama, pendaftaran_t.pasienadmisi_id, pendaftaran_t.instalasi_id, hitung.penjamin_id, penjamin_m_1.penjamin_nama) cektagihanpejamin ON (((rincian.pendaftaran_id = cektagihanpejamin.pendaftaran_id) AND (pembayaranpelayanan_t.penjamin_id = cektagihanpejamin.penjamin_id))))
            LEFT JOIN ( SELECT x.pendaftaran_id,
            sum(x.tagihan_tindakan_obat) AS total_tagihan
            FROM ( SELECT pendaftaran_t_1.pendaftaran_id,
            sum(tindakanpelayanan_t.tarif_tindakan) AS tagihan_tindakan_obat
            FROM (pendaftaran_t pendaftaran_t_1
            JOIN tindakanpelayanan_t ON (((pendaftaran_t_1.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id) AND (tindakanpelayanan_t.is_deleted = false))))
            GROUP BY pendaftaran_t_1.pendaftaran_id
            UNION ALL
            SELECT pendaftaran_t_1.pendaftaran_id,
            sum(obatalkespasien_t.hargajual_oa) AS tagihan_tindakan_obat
            FROM (pendaftaran_t pendaftaran_t_1
            JOIN obatalkespasien_t ON (((pendaftaran_t_1.pendaftaran_id = obatalkespasien_t.pendaftaran_id) AND (obatalkespasien_t.is_deleted = false))))
            GROUP BY pendaftaran_t_1.pendaftaran_id) x
            GROUP BY x.pendaftaran_id) rincian_tagihan ON ((rincian.pendaftaran_id = rincian_tagihan.pendaftaran_id)))
            WHERE (pembayaranpelayanan_t.carabayar_id <> ALL (ARRAY[5, 6]))
            GROUP BY rincian.tipe, rincian.pasien_id, rincian.no_rekam_medik, rincian.nama_pasien, rincian.pendaftaran_id, rincian.tgl_pendaftaran, rincian.tglpasienpulang, rincian.no_pendaftaran, rincian.ruangan_id, rincian.ruangan_nama, rincian.instalasi_id, rincian.instalasi_nama, pembayaranpelayanan_t.carabayar_id, carabayar_m.carabayar_nama, pembayaranpelayanan_t.penjamin_id, penjamin_m.penjamin_nama, rincian.kelaspelayanan_id, rincian.kelaspelayanan_nama, rincian.jeniskasuspenyakit_id, rincian.jeniskasuspenyakit_nama, rincian.pegawai_id, rincian.dokter, rincian.status_bayar, rincian_tagihan.total_tagihan, rincianpasien_detail.total_sdh_bayar, rincian.is_skd,
            CASE
            WHEN (rincian.is_skd IS FALSE) THEN 'Belum Dibuat'::text
            WHEN (rincian.is_skd IS TRUE) THEN 'Sudah Dibuat'::text
            ELSE NULL::text
            END, rincian.pasienadmisi_id, rincian.status_verifikasi, rincian.lookup_name, rincian.no_pembayaran
            ;");
        $this->execute('
            ALTER TABLE public.infopasiennonbpjs_v OWNER TO postgres;
            ');

        $this->execute('DROP VIEW if exists public.pengajuanklaim_v;');
        $this->execute("
            CREATE VIEW \"public\".\"pengajuanklaim_v\" AS
            SELECT rincian.tipe,
            rincian.pasien_id,
            rincian.no_rekam_medik,
            rincian.nama_pasien,
            rincian.pendaftaran_id,
            rincian.pasienadmisi_id,
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
            CASE
            WHEN (rincian.tagihan_igd = (0)::double precision) THEN rincianpasien_detail.total_tagihan
            ELSE rincian.tagihan_igd
            END AS total_tagihan,
            rincianpasien_detail.total_sdh_bayar,
            sum(rincian.total_subsidiasuransi) AS total_sisa_tagihan,
            sum(rincian.total_subsidiasuransi) AS total_asuransi,
            rincian.is_skd,
            CASE
            WHEN (rincian.is_skd IS FALSE) THEN 'Belum Dibuat'::text
            WHEN (rincian.is_skd IS TRUE) THEN 'Sudah Dibuat'::text
            ELSE NULL::text
            END AS status_skd,
            rincian.bpjs_id,
            rincian.nosep,
            rincian.total AS jumlah_inacbg,
            rincian.status_verifikasi,
            pengajuan.pengajuanklaimdetail_id,
            pengajuan.pengajuanklaim_id,
            rincian.verif_klaim_id
            FROM (((( SELECT 'RJ'::text AS tipe,
            pasien_m.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.pendaftaran_id,
            NULL::integer AS pasienadmisi_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pembayaranpelayanan_t_1.carabayar_id,
            carabayar_m.carabayar_nama,
            pembayaranpelayanan_t_1.penjamin_id,
            penjamin_m.penjamin_nama,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.is_skd,
            bpjs_t.bpjs_id,
            bpjs_t.nosep,
            pendaftaran_t.status_verifikasi,
            pembayaranpelayanan_t_1.total_subsidiasuransi,
            klaimgroup_t.total,
            0 AS tagihan_igd,
            (('RJ'::text || '-'::text) || pendaftaran_t.pendaftaran_id) AS verif_klaim_id
            FROM (((((((((((pasien_m
            JOIN pendaftaran_t ON (((pasien_m.pasien_id = pendaftaran_t.pasien_id) AND (pendaftaran_t.instalasi_id <> 2))))
            JOIN pembayaranpelayanan_t pembayaranpelayanan_t_1 ON ((pendaftaran_t.pendaftaran_id = pembayaranpelayanan_t_1.pendaftaran_id)))
            JOIN carabayar_m ON ((pembayaranpelayanan_t_1.carabayar_id = carabayar_m.carabayar_id)))
            JOIN penjamin_m ON ((pembayaranpelayanan_t_1.penjamin_id = penjamin_m.penjamin_id)))
            JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
            JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            LEFT JOIN bpjs_t ON (((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id) AND (bpjs_t.is_deleted = false))))
            LEFT JOIN klaiminacbg_t ON (((pendaftaran_t.pendaftaran_id = klaiminacbg_t.pendaftaran_id) AND (klaiminacbg_t.is_deleted = false) AND (klaiminacbg_t.pasienadmisi_id IS NULL))))
            LEFT JOIN klaimgroup_t ON (((klaiminacbg_t.klaiminacbg_id = klaimgroup_t.klaiminacbg_id) AND (klaiminacbg_t.klaimgroup_id = klaimgroup_t.klaimgroup_id) AND (klaimgroup_t.is_deleted = false))))
            UNION ALL
            SELECT 'RI'::text AS tipe,
            pasien_m.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.pendaftaran_id,
            pasienadmisi_t.pasienadmisi_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pembayaranpelayanan_t_1.carabayar_id,
            carabayar_m.carabayar_nama,
            pembayaranpelayanan_t_1.penjamin_id,
            penjamin_m.penjamin_nama,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pasienadmisi_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pasienpulang_t.tglpasienpulang,
            pasienadmisi_t.is_skd,
            bpjs_t.bpjs_id,
            bpjs_t.nosep,
            pasienadmisi_t.status_verifikasi,
            pembayaranpelayanan_t_1.total_subsidiasuransi,
            klaimgroup_t.total,
            0 AS tagihan_igd,
            (('RI'::text || '-'::text) || pasienadmisi_t.pasienadmisi_id) AS verif_klaim_id
            FROM (((((((((((pasien_m
            JOIN pendaftaran_t ON (((pasien_m.pasien_id = pendaftaran_t.pasien_id) AND (pendaftaran_t.instalasi_id <> 2))))
            JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            JOIN pembayaranpelayanan_t pembayaranpelayanan_t_1 ON ((pendaftaran_t.pendaftaran_id = pembayaranpelayanan_t_1.pendaftaran_id)))
            JOIN carabayar_m ON ((pembayaranpelayanan_t_1.carabayar_id = carabayar_m.carabayar_id)))
            JOIN penjamin_m ON ((pembayaranpelayanan_t_1.penjamin_id = penjamin_m.penjamin_id)))
            JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            JOIN pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
            LEFT JOIN bpjs_t ON ((pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id)))
            LEFT JOIN klaiminacbg_t ON (((pasienadmisi_t.pasienadmisi_id = klaiminacbg_t.pasienadmisi_id) AND (klaiminacbg_t.is_deleted = false))))
            LEFT JOIN klaimgroup_t ON (((klaiminacbg_t.klaiminacbg_id = klaimgroup_t.klaiminacbg_id) AND (klaiminacbg_t.klaimgroup_id = klaimgroup_t.klaimgroup_id) AND (klaimgroup_t.is_deleted = false))))
            UNION ALL
            SELECT 'RD-RI'::text AS tipe,
            pasien_m.pasien_id,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.tgl_pendaftaran,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.carabayar_id,
            carabayar_m.carabayar_nama,
            pendaftaran_t.penjamin_id,
            penjamin_m.penjamin_nama,
            ruangan_m.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.is_skd,
            pendaftaran_t.bpjs_id,
            pendaftaran_t.nosep,
            pendaftaran_t.status_verifikasi,
            pendaftaran_t.total_subsidiasuransi,
            pendaftaran_t.total,
            pendaftaran_t.tagihan AS tagihan_igd,
            CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN (('RJ'::text || '-'::text) || pendaftaran_t.pendaftaran_id)
            ELSE (('RI'::text || '-'::text) || pendaftaran_t.pasienadmisi_id)
            END AS verif_klaim_id
            FROM ((((((pasien_m
            JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
            pendaftaran_t_1.pasienadmisi_id,
            pendaftaran_t_1.pasien_id,
            pendaftaran_t_1.bpjs_id,
            pendaftaran_t_1.nosep,
            pendaftaran_t_1.total,
            sum(pendaftaran_t_1.total_tahihan) AS tagihan,
            pendaftaran_t_1.carabayar_id,
            pendaftaran_t_1.penjamin_id,
            pendaftaran_t_1.pasienpulang_id,
            pendaftaran_t_1.tgl_pendaftaran,
            pendaftaran_t_1.no_pendaftaran,
            pendaftaran_t_1.ruangan_id,
            pendaftaran_t_1.status_verifikasi,
            pendaftaran_t_1.instalasi_id,
            pendaftaran_t_1.is_skd,
            pendaftaran_t_1.total_subsidiasuransi
            FROM ( SELECT pendaftaran_t_2.pendaftaran_id,
            CASE
            WHEN (pembayaranpelayanan_t.carabayar_id = 6) THEN NULL::integer
            ELSE pendaftaran_t_2.pasienadmisi_id
            END AS pasienadmisi_id,
            pendaftaran_t_2.pasien_id,
            CASE
            WHEN (pembayaranpelayanan_t.carabayar_id <> 6) THEN NULL::integer
            ELSE bpjs_t.bpjs_id
            END AS bpjs_id,
            CASE
            WHEN (pembayaranpelayanan_t.carabayar_id <> 6) THEN NULL::character varying
            ELSE bpjs_t.nosep
            END AS nosep,
            CASE
            WHEN (pembayaranpelayanan_t.carabayar_id <> 6) THEN NULL::double precision
            ELSE klaimgroup_t.total
            END AS total,
            COALESCE(cek_tagihan.total, (0)::double precision) AS total_tahihan,
            pembayaranpelayanan_t.carabayar_id,
            pembayaranpelayanan_t.penjamin_id,
            CASE
            WHEN (pembayaranpelayanan_t.carabayar_id = 6) THEN pendaftaran_t_2.pasienpulang_id
            ELSE
            CASE
            WHEN (pasienadmisi_t.pasienpulang_id IS NULL) THEN pendaftaran_t_2.pasienpulang_id
            ELSE pasienadmisi_t.pasienpulang_id
            END
            END AS pasienpulang_id,
            pendaftaran_t_2.tgl_pendaftaran,
            pendaftaran_t_2.no_pendaftaran,
            CASE
            WHEN (pembayaranpelayanan_t.carabayar_id = 6) THEN pendaftaran_t_2.ruangan_id
            ELSE
            CASE
            WHEN (pasienadmisi_t.ruangan_id IS NULL) THEN pendaftaran_t_2.ruangan_id
            ELSE pasienadmisi_t.ruangan_id
            END
            END AS ruangan_id,
            CASE
            WHEN (pembayaranpelayanan_t.carabayar_id = 6) THEN pendaftaran_t_2.status_verifikasi
            ELSE
            CASE
            WHEN (pasienadmisi_t.status_verifikasi IS NULL) THEN pendaftaran_t_2.status_verifikasi
            ELSE pasienadmisi_t.status_verifikasi
            END
            END AS status_verifikasi,
            CASE
            WHEN (pembayaranpelayanan_t.carabayar_id = 6) THEN pendaftaran_t_2.is_skd
            ELSE
            CASE
            WHEN (pasienadmisi_t.is_skd IS NULL) THEN pendaftaran_t_2.is_skd
            ELSE pasienadmisi_t.is_skd
            END
            END AS is_skd,
            pendaftaran_t_2.instalasi_id,
            pembayaranpelayanan_t.total_subsidiasuransi
            FROM ((((((pendaftaran_t pendaftaran_t_2
            LEFT JOIN pasienadmisi_t ON ((pendaftaran_t_2.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            LEFT JOIN bpjs_t ON ((pendaftaran_t_2.bpjs_id = bpjs_t.bpjs_id)))
            LEFT JOIN klaiminacbg_t ON (((pendaftaran_t_2.pendaftaran_id = klaiminacbg_t.pendaftaran_id) AND (klaiminacbg_t.is_deleted = false) AND (klaiminacbg_t.pasienadmisi_id IS NULL))))
            LEFT JOIN klaimgroup_t ON (((klaiminacbg_t.klaiminacbg_id = klaimgroup_t.klaiminacbg_id) AND (klaiminacbg_t.klaimgroup_id = klaimgroup_t.klaimgroup_id) AND (klaimgroup_t.is_deleted = false))))
            JOIN ( SELECT hitung.pendaftaran_id,
            CASE
            WHEN (hitung.instalasi_id = 3) THEN pendaftaran_t_3.pasienadmisi_id
            ELSE NULL::integer
            END AS pasienadmisi_id,
            sum(hitung.tarif) AS total
            FROM ((( SELECT tindakanpelayanan_t.pendaftaran_id,
            tindakanpelayanan_t.instalasi_id,
            instalasi_m_1.instalasi_nama,
            tindakanpelayanan_t.penjamin_id,
            sum(tindakanpelayanan_t.tarif_tindakan) AS tarif
            FROM (tindakanpelayanan_t
            LEFT JOIN instalasi_m instalasi_m_1 ON ((tindakanpelayanan_t.instalasi_id = instalasi_m_1.instalasi_id)))
            WHERE (tindakanpelayanan_t.pasienmasukpenunjang_id IS NULL)
            GROUP BY tindakanpelayanan_t.pendaftaran_id, tindakanpelayanan_t.instalasi_id, instalasi_m_1.instalasi_nama, tindakanpelayanan_t.penjamin_id
            UNION ALL
            SELECT tindakanpelayanan_t.pendaftaran_id,
            ruangan_m_1.instalasi_id,
            instalasi_m_1.instalasi_nama,
            tindakanpelayanan_t.penjamin_id,
            sum(tindakanpelayanan_t.tarif_tindakan) AS tarif
            FROM (((tindakanpelayanan_t
            LEFT JOIN pasienmasukpenunjang_t ON ((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id)))
            LEFT JOIN ruangan_m ruangan_m_1 ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m_1.ruangan_id)))
            LEFT JOIN instalasi_m instalasi_m_1 ON ((ruangan_m_1.instalasi_id = instalasi_m_1.instalasi_id)))
            WHERE (tindakanpelayanan_t.pasienmasukpenunjang_id IS NOT NULL)
            GROUP BY tindakanpelayanan_t.pendaftaran_id, ruangan_m_1.instalasi_id, instalasi_m_1.instalasi_nama, tindakanpelayanan_t.penjamin_id
            UNION ALL
            SELECT obatalkespasien_t.pendaftaran_id,
            ruangan_m_1.instalasi_id,
            instalasi_m_1.instalasi_nama,
            obatalkespasien_t.penjamin_id,
            sum(obatalkespasien_t.hargajual_oa) AS sum
            FROM ((obatalkespasien_t
            LEFT JOIN ruangan_m ruangan_m_1 ON ((obatalkespasien_t.ruangan_id = ruangan_m_1.ruangan_id)))
            LEFT JOIN instalasi_m instalasi_m_1 ON ((ruangan_m_1.instalasi_id = instalasi_m_1.instalasi_id)))
            WHERE (obatalkespasien_t.resepturdetail_id IS NULL)
            GROUP BY obatalkespasien_t.pendaftaran_id, ruangan_m_1.instalasi_id, instalasi_m_1.instalasi_nama, obatalkespasien_t.penjamin_id
            UNION ALL
            SELECT obatalkespasien_t.pendaftaran_id,
            ruangan_m_1.instalasi_id,
            instalasi_m_1.instalasi_nama,
            obatalkespasien_t.penjamin_id,
            sum(obatalkespasien_t.hargajual_oa) AS sum
            FROM ((((obatalkespasien_t
            LEFT JOIN resepturdetail_t ON ((obatalkespasien_t.resepturdetail_id = resepturdetail_t.resepturdetail_id)))
            LEFT JOIN reseptur_t ON ((resepturdetail_t.reseptur_id = reseptur_t.reseptur_id)))
            LEFT JOIN ruangan_m ruangan_m_1 ON ((reseptur_t.ruanganreseptur_id = ruangan_m_1.ruangan_id)))
            LEFT JOIN instalasi_m instalasi_m_1 ON ((ruangan_m_1.instalasi_id = instalasi_m_1.instalasi_id)))
            WHERE (obatalkespasien_t.resepturdetail_id IS NOT NULL)
            GROUP BY obatalkespasien_t.pendaftaran_id, ruangan_m_1.instalasi_id, instalasi_m_1.instalasi_nama, obatalkespasien_t.penjamin_id) hitung
            JOIN pendaftaran_t pendaftaran_t_3 ON ((hitung.pendaftaran_id = pendaftaran_t_3.pendaftaran_id)))
            JOIN penjamin_m penjamin_m_1 ON ((hitung.penjamin_id = penjamin_m_1.penjamin_id)))
            GROUP BY hitung.pendaftaran_id, hitung.instalasi_id, hitung.instalasi_nama, pendaftaran_t_3.pasienadmisi_id, pendaftaran_t_3.instalasi_id, hitung.penjamin_id, penjamin_m_1.penjamin_nama) cek_tagihan ON (((pendaftaran_t_2.pendaftaran_id = cek_tagihan.pendaftaran_id) AND (cek_tagihan.pasienadmisi_id IS NULL))))
            LEFT JOIN pembayaranpelayanan_t ON ((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t_2.pendaftaran_id)))
            UNION ALL
            SELECT pendaftaran_t_2.pendaftaran_id,
            pasienadmisi_t.pasienadmisi_id,
            pendaftaran_t_2.pasien_id,
            CASE
            WHEN (pembayaranpelayanan_t.carabayar_id <> 6) THEN NULL::integer
            ELSE bpjs_t.bpjs_id
            END AS bpjs_id,
            CASE
            WHEN (pembayaranpelayanan_t.carabayar_id <> 6) THEN NULL::character varying
            ELSE bpjs_t.nosep
            END AS nosep,
            CASE
            WHEN (pembayaranpelayanan_t.carabayar_id <> 6) THEN NULL::double precision
            ELSE klaimgroup_t.total
            END AS total,
            COALESCE(cek_tagihan.total, (0)::double precision) AS total_tahihan,
            pembayaranpelayanan_t.carabayar_id,
            pembayaranpelayanan_t.penjamin_id,
            pasienadmisi_t.pasienpulang_id,
            pendaftaran_t_2.tgl_pendaftaran,
            pendaftaran_t_2.no_pendaftaran,
            pasienadmisi_t.ruangan_id,
            pasienadmisi_t.status_verifikasi,
            pasienadmisi_t.is_skd,
            pendaftaran_t_2.instalasi_id,
            pembayaranpelayanan_t.total_subsidiasuransi
            FROM ((((((pendaftaran_t pendaftaran_t_2
            JOIN pasienadmisi_t ON ((pendaftaran_t_2.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
            LEFT JOIN bpjs_t ON ((pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id)))
            LEFT JOIN klaiminacbg_t ON (((pasienadmisi_t.pasienadmisi_id = klaiminacbg_t.pasienadmisi_id) AND (klaiminacbg_t.is_deleted = false))))
            LEFT JOIN klaimgroup_t ON (((klaiminacbg_t.klaiminacbg_id = klaimgroup_t.klaiminacbg_id) AND (klaiminacbg_t.klaimgroup_id = klaimgroup_t.klaimgroup_id) AND (klaimgroup_t.is_deleted = false))))
            JOIN ( SELECT hitung.pendaftaran_id,
            CASE
            WHEN (hitung.instalasi_id = 3) THEN pendaftaran_t_3.pasienadmisi_id
            ELSE NULL::integer
            END AS pasienadmisi_id,
            sum(hitung.tarif) AS total
            FROM ((( SELECT tindakanpelayanan_t.pendaftaran_id,
            tindakanpelayanan_t.instalasi_id,
            instalasi_m_1.instalasi_nama,
            tindakanpelayanan_t.penjamin_id,
            sum(tindakanpelayanan_t.tarif_tindakan) AS tarif
            FROM (tindakanpelayanan_t
            LEFT JOIN instalasi_m instalasi_m_1 ON ((tindakanpelayanan_t.instalasi_id = instalasi_m_1.instalasi_id)))
            WHERE (tindakanpelayanan_t.pasienmasukpenunjang_id IS NULL)
            GROUP BY tindakanpelayanan_t.pendaftaran_id, tindakanpelayanan_t.instalasi_id, instalasi_m_1.instalasi_nama, tindakanpelayanan_t.penjamin_id
            UNION ALL
            SELECT tindakanpelayanan_t.pendaftaran_id,
            ruangan_m_1.instalasi_id,
            instalasi_m_1.instalasi_nama,
            tindakanpelayanan_t.penjamin_id,
            sum(tindakanpelayanan_t.tarif_tindakan) AS tarif
            FROM (((tindakanpelayanan_t
            LEFT JOIN pasienmasukpenunjang_t ON ((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id)))
            LEFT JOIN ruangan_m ruangan_m_1 ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m_1.ruangan_id)))
            LEFT JOIN instalasi_m instalasi_m_1 ON ((ruangan_m_1.instalasi_id = instalasi_m_1.instalasi_id)))
            WHERE (tindakanpelayanan_t.pasienmasukpenunjang_id IS NOT NULL)
            GROUP BY tindakanpelayanan_t.pendaftaran_id, ruangan_m_1.instalasi_id, instalasi_m_1.instalasi_nama, tindakanpelayanan_t.penjamin_id
            UNION ALL
            SELECT obatalkespasien_t.pendaftaran_id,
            ruangan_m_1.instalasi_id,
            instalasi_m_1.instalasi_nama,
            obatalkespasien_t.penjamin_id,
            sum(obatalkespasien_t.hargajual_oa) AS sum
            FROM ((obatalkespasien_t
            LEFT JOIN ruangan_m ruangan_m_1 ON ((obatalkespasien_t.ruangan_id = ruangan_m_1.ruangan_id)))
            LEFT JOIN instalasi_m instalasi_m_1 ON ((ruangan_m_1.instalasi_id = instalasi_m_1.instalasi_id)))
            WHERE (obatalkespasien_t.resepturdetail_id IS NULL)
            GROUP BY obatalkespasien_t.pendaftaran_id, ruangan_m_1.instalasi_id, instalasi_m_1.instalasi_nama, obatalkespasien_t.penjamin_id
            UNION ALL
            SELECT obatalkespasien_t.pendaftaran_id,
            ruangan_m_1.instalasi_id,
            instalasi_m_1.instalasi_nama,
            obatalkespasien_t.penjamin_id,
            sum(obatalkespasien_t.hargajual_oa) AS sum
            FROM ((((obatalkespasien_t
            LEFT JOIN resepturdetail_t ON ((obatalkespasien_t.resepturdetail_id = resepturdetail_t.resepturdetail_id)))
            LEFT JOIN reseptur_t ON ((resepturdetail_t.reseptur_id = reseptur_t.reseptur_id)))
            LEFT JOIN ruangan_m ruangan_m_1 ON ((reseptur_t.ruanganreseptur_id = ruangan_m_1.ruangan_id)))
            LEFT JOIN instalasi_m instalasi_m_1 ON ((ruangan_m_1.instalasi_id = instalasi_m_1.instalasi_id)))
            WHERE (obatalkespasien_t.resepturdetail_id IS NOT NULL)
            GROUP BY obatalkespasien_t.pendaftaran_id, ruangan_m_1.instalasi_id, instalasi_m_1.instalasi_nama, obatalkespasien_t.penjamin_id) hitung
            JOIN pendaftaran_t pendaftaran_t_3 ON ((hitung.pendaftaran_id = pendaftaran_t_3.pendaftaran_id)))
            JOIN penjamin_m penjamin_m_1 ON ((hitung.penjamin_id = penjamin_m_1.penjamin_id)))
            GROUP BY hitung.pendaftaran_id, hitung.instalasi_id, hitung.instalasi_nama, pendaftaran_t_3.pasienadmisi_id, pendaftaran_t_3.instalasi_id, hitung.penjamin_id, penjamin_m_1.penjamin_nama) cek_tagihan ON ((pendaftaran_t_2.pasienadmisi_id = cek_tagihan.pasienadmisi_id)))
            LEFT JOIN pembayaranpelayanan_t ON ((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t_2.pendaftaran_id)))) pendaftaran_t_1
            WHERE (pendaftaran_t_1.instalasi_id = 2)
            GROUP BY pendaftaran_t_1.total_subsidiasuransi, pendaftaran_t_1.pendaftaran_id, pendaftaran_t_1.pasienadmisi_id, pendaftaran_t_1.pasien_id, pendaftaran_t_1.bpjs_id, pendaftaran_t_1.nosep, pendaftaran_t_1.total, pendaftaran_t_1.carabayar_id, pendaftaran_t_1.penjamin_id, pendaftaran_t_1.pasienpulang_id, pendaftaran_t_1.tgl_pendaftaran, pendaftaran_t_1.no_pendaftaran, pendaftaran_t_1.ruangan_id, pendaftaran_t_1.status_verifikasi, pendaftaran_t_1.instalasi_id, pendaftaran_t_1.is_skd) pendaftaran_t ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
            JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
            JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
            JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
            JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
            JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))) rincian
            LEFT JOIN ( SELECT tagihan.pendaftaran_id,
            tagihan.pasienadmisi_id,
            pasien_m.no_rekam_medik,
            sum(tagihan.tagihan_tindakan_obat) AS total_tagihan,
            sum(tagihan.tagihan_sudah_bayar) AS total_sdh_bayar,
            ((sum(tagihan.tagihan_tindakan_obat) - sum(tagihan.tagihan_sudah_bayar)) - sum(tagihan.tagihan_asuransi)) AS total_sisa_tagihan,
            sum(tagihan.tagihan_uang_muka) AS total_uang_muka,
            sum(tagihan.tagihan_biaya_admin) AS total_administrasi,
            sum(tagihan.tagihan_pembulatan) AS total_pembulatan,
            sum(tagihan.tagihan_asuransi) AS total_asuransi,
            sum(tagihan.pembayaran_pasien) AS total_pembayaran_pasien
            FROM (( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            0 AS tagihan_tindakan_obat,
            0 AS tagihan_sudah_bayar,
            sum(bayaruangmuka_t.jumlah_uangmuka) AS tagihan_uang_muka,
            0 AS tagihan_biaya_admin,
            0 AS tagihan_pembulatan,
            0 AS tagihan_asuransi,
            0 AS pembayaran_pasien,
            pendaftaran_t.pasien_id
            FROM (pendaftaran_t
            LEFT JOIN bayaruangmuka_t ON ((pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id)))
            GROUP BY pendaftaran_t.pendaftaran_id, bayaruangmuka_t.jumlah_uangmuka, pendaftaran_t.pasienadmisi_id
            UNION ALL
            SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pasienadmisi_id,
            sum(pembayaranpelayanan_t.total_biayapelayanan) AS tagihan_tindakan_obat,
            sum(pembayaranpelayanan_t.total_terbayar) AS tagihan_sudah_bayar,
            0 AS tagihan_uang_muka,
            sum(pembayaranpelayanan_t.biaya_administrasi) AS tagihan_biaya_admin,
            sum(pembayaranpelayanan_t.pembulatan) AS tagihan_pembulatan,
            sum(pembayaranpelayanan_t.total_subsidiasuransi) AS tagihan_asuransi,
            sum(pembayaranpelayanan_t.total_bayartindakan) AS pembayaran_pasien,
            pendaftaran_t.pasien_id
            FROM (pendaftaran_t
            LEFT JOIN pembayaranpelayanan_t ON ((pendaftaran_t.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id)))
            GROUP BY pendaftaran_t.pendaftaran_id, pembayaranpelayanan_t.biaya_administrasi, pembayaranpelayanan_t.pembulatan, pendaftaran_t.pasienadmisi_id, pembayaranpelayanan_t.total_subsidiasuransi) tagihan
            LEFT JOIN pasien_m ON ((tagihan.pasien_id = pasien_m.pasien_id)))
            GROUP BY tagihan.pendaftaran_id, tagihan.pasienadmisi_id, pasien_m.no_rekam_medik) rincianpasien_detail ON ((rincian.pendaftaran_id = rincianpasien_detail.pendaftaran_id)))
            LEFT JOIN ( SELECT pembayaranpelayanan_t.pendaftaran_id,
            pembayaranpelayanan_t.penjamin_id
            FROM ((pembayaranpelayanan_t
            JOIN ( SELECT pendaftaran_t.pendaftaran_id
            FROM pendaftaran_t
            WHERE (pendaftaran_t.status_bayar = 348)) kon_pendaftaran ON ((kon_pendaftaran.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id)))
            JOIN ( SELECT pembayaranpelayanan_t_1.pendaftaran_id,
            count(*) AS total_closing,
            pembayaranpelayanan_t_1.penjamin_id
            FROM (pembayaranpelayanan_t pembayaranpelayanan_t_1
            JOIN tandabuktibayar_t ON ((tandabuktibayar_t.tandabuktibayar_id = pembayaranpelayanan_t_1.tandabuktibayar_id)))
            WHERE (tandabuktibayar_t.closingkasir_id IS NOT NULL)
            GROUP BY pembayaranpelayanan_t_1.pendaftaran_id, pembayaranpelayanan_t_1.penjamin_id) valid_closing ON (((valid_closing.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id) AND (valid_closing.penjamin_id = pembayaranpelayanan_t.penjamin_id))))
            WHERE (pembayaranpelayanan_t.penjamin_id <> 1)
            GROUP BY pembayaranpelayanan_t.pendaftaran_id, pembayaranpelayanan_t.penjamin_id, valid_closing.total_closing
            HAVING (count(*) = valid_closing.total_closing)) cek_closing ON (((rincian.pendaftaran_id = cek_closing.pendaftaran_id) AND (rincian.penjamin_id = cek_closing.penjamin_id))))
            LEFT JOIN ( SELECT pengajuanklaimdetail_t.pengajuanklaimdetail_id,
            pengajuanklaimdetail_t.pendaftaran_id,
            pengajuanklaimdetail_t.pasienadmisi_id,
            pengajuanklaim_t.carabayar_id,
            pengajuanklaim_t.penjamin_id,
            pengajuanklaim_t.pengajuanklaim_id,
            CASE
            WHEN (pengajuanklaimdetail_t.pasienadmisi_id IS NULL) THEN (('RJ'::text || '-'::text) || pengajuanklaimdetail_t.pendaftaran_id)
            ELSE (('RI'::text || '-'::text) || pengajuanklaimdetail_t.pasienadmisi_id)
            END AS verif_klaim_id
            FROM (pengajuanklaimdetail_t
            JOIN pengajuanklaim_t ON ((pengajuanklaim_t.pengajuanklaim_id = pengajuanklaimdetail_t.pengajuanklaim_id)))
            WHERE ((pengajuanklaim_t.is_deleted = false) AND (pengajuanklaimdetail_t.is_deleted = false))) pengajuan ON (((rincian.verif_klaim_id = pengajuan.verif_klaim_id) AND (rincian.penjamin_id = pengajuan.penjamin_id))))
            WHERE (pengajuan.pengajuanklaim_id IS NULL)
            GROUP BY rincian.tipe, rincian.pasien_id, rincian.no_rekam_medik, rincian.nama_pasien, rincian.pendaftaran_id, rincian.pasienadmisi_id, rincian.tgl_pendaftaran, rincian.tglpasienpulang, rincian.no_pendaftaran, rincian.ruangan_id, rincian.ruangan_nama, rincian.instalasi_id, rincian.instalasi_nama, rincian.carabayar_id, rincian.carabayar_nama, rincian.penjamin_id, rincian.penjamin_nama, rincianpasien_detail.total_tagihan, rincianpasien_detail.total_sdh_bayar, rincian.is_skd, rincianpasien_detail.total_sisa_tagihan, rincianpasien_detail.total_asuransi,
            CASE
            WHEN (rincian.is_skd IS FALSE) THEN 'Belum Dibuat'::text
            WHEN (rincian.is_skd IS TRUE) THEN 'Sudah Dibuat'::text
            ELSE NULL::text
            END, rincian.bpjs_id, rincian.nosep, rincian.total, rincian.status_verifikasi, rincian.tagihan_igd, pengajuan.pengajuanklaimdetail_id, pengajuan.pengajuanklaim_id, rincian.verif_klaim_id
            ;");
            $this->execute('
                ALTER TABLE public.pengajuanklaim_v OWNER TO postgres;
            ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211124_115842_migrate_US2124_penjaminasuransi cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211124_115842_migrate_US2124_penjaminasuransi cannot be reverted.\n";

        return false;
    }
    */
}
