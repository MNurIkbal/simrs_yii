<?php

use yii\db\Migration;

/**
 * Class m220905_090036_migrate_ORDH159_infopasiennonbpjs_v
 */
class m220905_090036_migrate_ORDH159_infopasiennonbpjs_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."infopasiennonbpjs_v";');
        $this->execute("CREATE OR REPLACE VIEW public.infopasiennonbpjs_v
        AS SELECT rincian.tipe,
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
            pembayaran_t.total_ditagihkan,
                CASE
                    WHEN pembayaran_t.total_pembulatan = 0::double precision THEN ceil(pembayaran_t.total_dijamin) + ceil(pembayaran_t.total_dibayar)
                    WHEN pembayaran_t.total_discountpembayaran IS NOT NULL THEN ceil(pembayaran_t.total_dijamin) + ceil(pembayaran_t.total_dibayar)
                    ELSE ceil(pembayaran_t.total_tagihan)
                END AS total_tagihan,
            pembayaran_t.total_dibayar AS total_sdh_bayar,
                CASE
                    WHEN pengajuan_klaim.total_telahbayar IS NULL THEN ceil(pembayaran_t.total_dijamin)
                    ELSE ceil(pembayaran_t.total_dijamin) - ceil(pengajuan_klaim.total_telahbayar)
                END AS total_sisa_tagihan,
            ceil(pembayaran_t.total_dijamin) AS total_asuransi,
            rincian.is_skd,
                CASE
                    WHEN rincian.is_skd IS FALSE THEN 'Belum Dibuat'::text
                    WHEN rincian.is_skd IS TRUE THEN 'Sudah Dibuat'::text
                    ELSE NULL::text
                END AS status_skd,
            rincian.pasienadmisi_id,
            rincian.status_verifikasi,
            rincian.lookup_name AS status_verif,
            pembayaran_t.no_pembayaran AS no_invoice,
            pengajuan_klaim.total_telahbayar AS jumlah_pembayaran,
            pembayaran_t.pembayaran_id,
                CASE
                    WHEN pengajuan_klaim.statuspengajuan_id IS NULL THEN 1120
                    ELSE pengajuan_klaim.statuspengajuan_id::integer
                END AS statuspengajuan_id,
                CASE
                    WHEN pengajuan_klaim.statuspengajuan_nama IS NULL THEN 'Belum Melakukan Pengajuan'::character varying
                    ELSE pengajuan_klaim.statuspengajuan_nama
                END AS statuspengajuan_nama,
            ceil(pembayaran_t.total_discountpembayaran) AS total_discountpembayaran,
            pembayaran_t.total_dijamin AS total_dijamin_cek,
            pembayaran_t.total_dibayar AS total_dibayar_cek
           FROM ( SELECT 'RJ'::text AS tipe,
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
                    look_status_verifikasi.lookup_name,
                    NULL::integer AS no_pembayaran
                   FROM pasien_m
                     JOIN ( SELECT a.pasien_id,
                            a.pendaftaran_id,
                            a.tgl_pendaftaran,
                            a.no_pendaftaran,
                            a.carabayar_id,
                            a.penjamin_id,
                            a.kelaspelayanan_id,
                            a.jeniskasuspenyakit_id,
                            a.pegawai_id,
                            a.status_bayar,
                            a.instalasi_id,
                            a.ruangan_id,
                            a.is_skd,
                            a.status_verifikasi,
                            a.pasienpulang_id,
                            a.is_active,
                            a.is_deleted
                           FROM pendaftaran_t a) pendaftaran_t ON pasien_m.pasien_id = pendaftaran_t.pasien_id AND pendaftaran_t.instalasi_id <> 2
                     JOIN ( SELECT a.carabayar_id,
                            a.carabayar_nama
                           FROM carabayar_m a) carabayar_m_1 ON pendaftaran_t.carabayar_id = carabayar_m_1.carabayar_id
                     JOIN ( SELECT a.penjamin_id,
                            a.penjamin_nama
                           FROM penjamin_m a) penjamin_m_1 ON pendaftaran_t.penjamin_id = penjamin_m_1.penjamin_id
                     JOIN ( SELECT a.kelaspelayanan_id,
                            a.kelaspelayanan_nama
                           FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                     JOIN ( SELECT a.jeniskasuspenyakit_id,
                            a.jeniskasuspenyakit_nama
                           FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
                     JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai
                           FROM pegawai_m a) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
                     JOIN ( SELECT a.instalasi_id,
                            a.instalasi_nama
                           FROM instalasi_m a) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
                     JOIN ( SELECT a.ruangan_id,
                            a.ruangan_nama
                           FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
                     JOIN ( SELECT a.pasienpulang_id,
                            a.tglpasienpulang
                           FROM pasienpulang_t a) pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
                     LEFT JOIN ( SELECT a.lookup_id,
                            a.lookup_name
                           FROM lookup_m a) look_status_verifikasi ON pendaftaran_t.status_verifikasi = look_status_verifikasi.lookup_id
                  WHERE pendaftaran_t.is_active = true AND pendaftaran_t.is_deleted = false AND carabayar_m_1.carabayar_id <> 5
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
                    look_status_verifikasi.lookup_name,
                    NULL::integer AS no_pembayaran
                   FROM pasien_m
                     JOIN ( SELECT a.pendaftaran_id,
                            a.pasien_id,
                            a.instalasi_id,
                            a.tgl_pendaftaran,
                            a.no_pendaftaran,
                            a.kelaspelayanan_id,
                            a.jeniskasuspenyakit_id,
                            a.status_bayar,
                            a.pasienadmisi_id,
                            a.is_active,
                            a.is_deleted
                           FROM pendaftaran_t a) pendaftaran_t ON pasien_m.pasien_id = pendaftaran_t.pasien_id AND pendaftaran_t.instalasi_id <> 2
                     JOIN ( SELECT a.pasienadmisi_id,
                            a.carabayar_id,
                            a.penjamin_id,
                            a.pegawai_id,
                            a.ruangan_id,
                            a.is_skd,
                            a.status_verifikasi,
                            a.pasienpulang_id,
                            a.is_active,
                            a.is_deleted
                           FROM pasienadmisi_t a) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
                     JOIN ( SELECT a.carabayar_id,
                            a.carabayar_nama
                           FROM carabayar_m a) carabayar_m_1 ON pasienadmisi_t.carabayar_id = carabayar_m_1.carabayar_id
                     JOIN ( SELECT a.penjamin_id,
                            a.penjamin_nama
                           FROM penjamin_m a) penjamin_m_1 ON pasienadmisi_t.penjamin_id = penjamin_m_1.penjamin_id
                     JOIN ( SELECT a.kelaspelayanan_id,
                            a.kelaspelayanan_nama
                           FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                     JOIN ( SELECT a.jeniskasuspenyakit_id,
                            a.jeniskasuspenyakit_nama
                           FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
                     JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai
                           FROM pegawai_m a) pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
                     JOIN ( SELECT a.ruangan_id,
                            a.ruangan_nama,
                            a.instalasi_id
                           FROM ruangan_m a) ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
                     JOIN ( SELECT a.instalasi_id,
                            a.instalasi_nama
                           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                     JOIN ( SELECT a.pasienpulang_id,
                            a.tglpasienpulang
                           FROM pasienpulang_t a) pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
                     LEFT JOIN ( SELECT a.lookup_id,
                            a.lookup_name
                           FROM lookup_m a) look_status_verifikasi ON pasienadmisi_t.status_verifikasi = look_status_verifikasi.lookup_id
                  WHERE pasienadmisi_t.is_active = true AND pasienadmisi_t.is_deleted = false AND pendaftaran_t.is_active = true AND pendaftaran_t.is_deleted = false AND carabayar_m_1.carabayar_id <> 5
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
                    look_status_verifikasi.lookup_name,
                    NULL::integer AS no_pembayaran
                   FROM pasien_m
                     JOIN ( SELECT a.pendaftaran_id,
                            a.pasien_id,
                            a.instalasi_id,
                            a.tgl_pendaftaran,
                            a.no_pendaftaran,
                            a.carabayar_id,
                            a.penjamin_id,
                            a.kelaspelayanan_id,
                            a.jeniskasuspenyakit_id,
                            a.pegawai_id,
                            a.status_bayar,
                            a.ruangan_id,
                            a.is_skd,
                            a.status_verifikasi,
                            a.is_active,
                            a.is_deleted
                           FROM pendaftaran_t a) pendaftaran_t ON pasien_m.pasien_id = pendaftaran_t.pasien_id AND pendaftaran_t.instalasi_id = 4
                     JOIN ( SELECT a.carabayar_id,
                            a.carabayar_nama
                           FROM carabayar_m a) carabayar_m_1 ON pendaftaran_t.carabayar_id = carabayar_m_1.carabayar_id
                     JOIN ( SELECT a.penjamin_id,
                            a.penjamin_nama
                           FROM penjamin_m a) penjamin_m_1 ON pendaftaran_t.penjamin_id = penjamin_m_1.penjamin_id
                     JOIN ( SELECT a.kelaspelayanan_id,
                            a.kelaspelayanan_nama
                           FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                     JOIN ( SELECT a.jeniskasuspenyakit_id,
                            a.jeniskasuspenyakit_nama
                           FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
                     JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai
                           FROM pegawai_m a) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
                     JOIN ( SELECT a.instalasi_id,
                            a.instalasi_nama
                           FROM instalasi_m a) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
                     JOIN ( SELECT a.ruangan_id,
                            a.ruangan_nama
                           FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
                     JOIN ( SELECT a.pendaftaran_id,
                            a.last_modified_date,
                            a.pasienkirimkeunitlain_id
                           FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
                     LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id
                           FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
                     LEFT JOIN ( SELECT a.lookup_id,
                            a.lookup_name
                           FROM lookup_m a) look_status_verifikasi ON pendaftaran_t.status_verifikasi = look_status_verifikasi.lookup_id
                  WHERE pendaftaran_t.is_active = true AND pendaftaran_t.is_deleted = false AND carabayar_m_1.carabayar_id <> 5
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
                    look_status_verifikasi.lookup_name,
                    NULL::integer AS no_pembayaran
                   FROM pasien_m
                     JOIN ( SELECT a.pendaftaran_id,
                            a.pasien_id,
                            a.instalasi_id,
                            a.tgl_pendaftaran,
                            a.no_pendaftaran,
                            a.carabayar_id,
                            a.penjamin_id,
                            a.kelaspelayanan_id,
                            a.jeniskasuspenyakit_id,
                            a.pegawai_id,
                            a.status_bayar,
                            a.ruangan_id,
                            a.is_skd,
                            a.status_verifikasi,
                            a.is_active,
                            a.is_deleted
                           FROM pendaftaran_t a) pendaftaran_t ON pasien_m.pasien_id = pendaftaran_t.pasien_id AND pendaftaran_t.instalasi_id = 5
                     JOIN ( SELECT a.carabayar_id,
                            a.carabayar_nama
                           FROM carabayar_m a) carabayar_m_1 ON pendaftaran_t.carabayar_id = carabayar_m_1.carabayar_id
                     JOIN ( SELECT a.penjamin_id,
                            a.penjamin_nama
                           FROM penjamin_m a) penjamin_m_1 ON pendaftaran_t.penjamin_id = penjamin_m_1.penjamin_id
                     JOIN ( SELECT a.kelaspelayanan_id,
                            a.kelaspelayanan_nama
                           FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                     JOIN ( SELECT a.jeniskasuspenyakit_id,
                            a.jeniskasuspenyakit_nama
                           FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
                     JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai
                           FROM pegawai_m a) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
                     JOIN ( SELECT a.instalasi_id,
                            a.instalasi_nama
                           FROM instalasi_m a) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
                     JOIN ( SELECT a.ruangan_id,
                            a.ruangan_nama
                           FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
                     JOIN ( SELECT a.pendaftaran_id,
                            a.pasienkirimkeunitlain_id,
                            a.last_modified_date
                           FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON pendaftaran_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id
                     LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id
                           FROM pasienkirimkeunitlain_t a) pasienkirimkeunitlain_t ON pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
                     LEFT JOIN ( SELECT a.lookup_id,
                            a.lookup_name
                           FROM lookup_m a) look_status_verifikasi ON pendaftaran_t.status_verifikasi = look_status_verifikasi.lookup_id
                  WHERE pendaftaran_t.is_active = true AND pendaftaran_t.is_deleted = false AND carabayar_m_1.carabayar_id <> 5
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
                    look_status_verifikasi.lookup_name,
                    NULL::integer AS no_pembayaran
                   FROM pasien_m
                     JOIN ( SELECT a.pendaftaran_id,
                            a.pasien_id,
                            a.instalasi_id,
                            a.tgl_pendaftaran,
                            a.no_pendaftaran,
                            a.carabayar_id,
                            a.penjamin_id,
                            a.kelaspelayanan_id,
                            a.jeniskasuspenyakit_id,
                            a.pegawai_id,
                            a.status_bayar,
                            a.ruangan_id,
                            a.is_skd,
                            a.status_verifikasi,
                            a.is_active,
                            a.is_deleted
                           FROM pendaftaran_t a) pendaftaran_t ON pasien_m.pasien_id = pendaftaran_t.pasien_id AND pendaftaran_t.instalasi_id = 21
                     JOIN ( SELECT a.carabayar_id,
                            a.carabayar_nama
                           FROM carabayar_m a) carabayar_m_1 ON pendaftaran_t.carabayar_id = carabayar_m_1.carabayar_id
                     JOIN ( SELECT a.penjamin_id,
                            a.penjamin_nama
                           FROM penjamin_m a) penjamin_m_1 ON pendaftaran_t.penjamin_id = penjamin_m_1.penjamin_id
                     JOIN ( SELECT a.kelaspelayanan_id,
                            a.kelaspelayanan_nama
                           FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                     JOIN ( SELECT a.jeniskasuspenyakit_id,
                            a.jeniskasuspenyakit_nama
                           FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
                     JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai
                           FROM pegawai_m a) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
                     JOIN ( SELECT a.instalasi_id,
                            a.instalasi_nama
                           FROM instalasi_m a) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
                     JOIN ( SELECT a.ruangan_id,
                            a.ruangan_nama
                           FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
                     LEFT JOIN ( SELECT a.lookup_id,
                            a.lookup_name
                           FROM lookup_m a) look_status_verifikasi ON pendaftaran_t.status_verifikasi = look_status_verifikasi.lookup_id
                  WHERE pendaftaran_t.is_active = true AND pendaftaran_t.is_deleted = false AND carabayar_m_1.carabayar_id <> 5
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
                    look_status_verifikasi.lookup_name,
                    pendaftaran_t.no_pembayaran
                   FROM pasien_m
                     JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                            pendaftaran_t_1.pasienadmisi_id,
                            pendaftaran_t_1.tgl_pendaftaran,
                            pendaftaran_t_1.no_pendaftaran,
                                CASE
                                    WHEN pasienadmisi_t.carabayar_id IS NULL THEN pendaftaran_t_1.carabayar_id
                                    ELSE pasienadmisi_t.carabayar_id
                                END AS carabayar_id,
                                CASE
                                    WHEN pasienadmisi_t.penjamin_id IS NULL THEN pendaftaran_t_1.penjamin_id
                                    ELSE pasienadmisi_t.penjamin_id
                                END AS penjamin_id,
                                CASE
                                    WHEN pasienadmisi_t.kelaspelayanan_id IS NULL THEN pendaftaran_t_1.kelaspelayanan_id
                                    ELSE pasienadmisi_t.kelaspelayanan_id
                                END AS kelaspelayanan_id,
                            pendaftaran_t_1.jeniskasuspenyakit_id,
                                CASE
                                    WHEN pasienadmisi_t.pegawai_id IS NULL THEN pendaftaran_t_1.pegawai_id
                                    ELSE pasienadmisi_t.pegawai_id
                                END AS pegawai_id,
                                CASE
                                    WHEN pasienadmisi_t.ruangan_id IS NULL THEN pendaftaran_t_1.ruangan_id
                                    ELSE pasienadmisi_t.ruangan_id
                                END AS ruangan_id,
                                CASE
                                    WHEN pasienadmisi_t.ruangan_id IS NULL THEN pendaftaran_t_1.instalasi_id
                                    ELSE 3
                                END AS instalasi_id,
                                CASE
                                    WHEN pasienadmisi_t.pasienpulang_id IS NULL THEN pendaftaran_t_1.pasienpulang_id
                                    ELSE pasienadmisi_t.pasienpulang_id
                                END AS pasienpulang_id,
                                CASE
                                    WHEN pasienadmisi_t.is_skd IS NULL THEN pendaftaran_t_1.is_skd
                                    ELSE pasienadmisi_t.is_skd
                                END AS is_skd,
                                CASE
                                    WHEN pasienadmisi_t.status_verifikasi IS NULL THEN pendaftaran_t_1.status_verifikasi
                                    ELSE pasienadmisi_t.status_verifikasi
                                END AS status_verifikasi,
                            pendaftaran_t_1.status_bayar,
                            pendaftaran_t_1.pasien_id,
                            NULL::integer AS no_pembayaran
                           FROM pendaftaran_t pendaftaran_t_1
                             LEFT JOIN ( SELECT a.pasienadmisi_id,
                                    a.carabayar_id,
                                    a.penjamin_id,
                                    a.kelaspelayanan_id,
                                    a.pegawai_id,
                                    a.ruangan_id,
                                    a.pasienpulang_id,
                                    a.is_skd,
                                    a.status_verifikasi
                                   FROM pasienadmisi_t a) pasienadmisi_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t_1.pasienadmisi_id
                          WHERE pendaftaran_t_1.instalasi_id = 2) pendaftaran_t ON pendaftaran_t.pasien_id = pasien_m.pasien_id
                     JOIN ( SELECT a.carabayar_id,
                            a.carabayar_nama
                           FROM carabayar_m a) carabayar_m_1 ON pendaftaran_t.carabayar_id = carabayar_m_1.carabayar_id AND carabayar_m_1.carabayar_id <> 5
                     JOIN ( SELECT a.penjamin_id,
                            a.penjamin_nama
                           FROM penjamin_m a) penjamin_m_1 ON pendaftaran_t.penjamin_id = penjamin_m_1.penjamin_id
                     JOIN ( SELECT a.kelaspelayanan_id,
                            a.kelaspelayanan_nama
                           FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                     JOIN ( SELECT a.jeniskasuspenyakit_id,
                            a.jeniskasuspenyakit_nama
                           FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
                     JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai
                           FROM pegawai_m a) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
                     JOIN ( SELECT a.ruangan_id,
                            a.ruangan_nama,
                            a.instalasi_id
                           FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
                     JOIN ( SELECT a.instalasi_id,
                            a.instalasi_nama
                           FROM instalasi_m a) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
                     JOIN ( SELECT a.pasienpulang_id,
                            a.tglpasienpulang
                           FROM pasienpulang_t a) pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
                     LEFT JOIN ( SELECT a.lookup_id,
                            a.lookup_name
                           FROM lookup_m a) look_status_verifikasi ON pendaftaran_t.status_verifikasi = look_status_verifikasi.lookup_id) rincian
             LEFT JOIN ( SELECT ceil(a.total_tagihan) + ceil(a.total_administrasi) + ceil(a.total_pembulatan) AS total_tagihan,
                    a.total_tagihan + a.total_administrasi AS total_ditagihkan,
                    a.total_dibayar,
                    a.total_dijamin,
                    pembayaranpelayanan_t.no_pembayaran,
                    a.total_sisatagihan,
                    a.pendaftaran_id,
                    pembayaranpelayanan_t.pembayaranpelayanan_id,
                    a.pembayaran_id,
                    a.total_pembulatan,
                    a.total_discountpembayaran
                   FROM pembayaran_t a
                     JOIN ( SELECT a1.pembayaran_id,
                            a1.no_pembayaran,
                            a1.pembayaranpelayanan_id
                           FROM pembayaranpelayanan_t a1
                          WHERE a1.is_deleted = false) pembayaranpelayanan_t ON a.pembayaran_id = pembayaranpelayanan_t.pembayaran_id
                  WHERE a.is_deleted = false) pembayaran_t ON rincian.pendaftaran_id = pembayaran_t.pendaftaran_id
             LEFT JOIN ( SELECT sum(pengajuanklaimdetail_t.jumlah_telahbayar) AS total_telahbayar,
                    pengajuanklaimdetail_t.pembayaranpelayanan_id,
                    pengajuanklaim_t.status_pengajuanklaim AS statuspengajuan_id,
                    fgetnamalookup(pengajuanklaim_t.status_pengajuanklaim::integer) AS statuspengajuan_nama
                   FROM pengajuanklaimdetail_t
                     JOIN ( SELECT a.pengajuanklaim_id,
                            a.status_pengajuanklaim
                           FROM pengajuanklaim_t a) pengajuanklaim_t ON pengajuanklaimdetail_t.pengajuanklaim_id = pengajuanklaim_t.pengajuanklaim_id
                  GROUP BY pengajuanklaimdetail_t.pembayaranpelayanan_id, pengajuanklaim_t.status_pengajuanklaim) pengajuan_klaim ON pengajuan_klaim.pembayaranpelayanan_id = pembayaran_t.pembayaranpelayanan_id
          GROUP BY rincian.tipe, rincian.pasien_id, rincian.no_rekam_medik, rincian.nama_pasien, rincian.pendaftaran_id, rincian.tgl_pendaftaran, rincian.tglpasienpulang, rincian.no_pendaftaran, rincian.ruangan_id, rincian.ruangan_nama, rincian.instalasi_id, rincian.instalasi_nama, rincian.kelaspelayanan_id, rincian.kelaspelayanan_nama, rincian.jeniskasuspenyakit_id, rincian.jeniskasuspenyakit_nama, rincian.pegawai_id, rincian.dokter, rincian.status_bayar, rincian.is_skd, (
                CASE
                    WHEN rincian.is_skd IS FALSE THEN 'Belum Dibuat'::text
                    WHEN rincian.is_skd IS TRUE THEN 'Sudah Dibuat'::text
                    ELSE NULL::text
                END), rincian.pasienadmisi_id, rincian.status_verifikasi, rincian.lookup_name, rincian.no_pembayaran, rincian.carabayar_id, rincian.carabayar_nama, rincian.penjamin_id, rincian.penjamin_nama, pembayaran_t.total_tagihan, pembayaran_t.total_sisatagihan, pembayaran_t.total_dijamin, pembayaran_t.no_pembayaran, pembayaran_t.total_dibayar, pengajuan_klaim.total_telahbayar, pembayaran_t.pembayaran_id, pengajuan_klaim.statuspengajuan_id, pengajuan_klaim.statuspengajuan_nama, pembayaran_t.total_pembulatan, pembayaran_t.total_discountpembayaran, pembayaran_t.total_ditagihkan;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220905_090036_migrate_ORDH159_infopasiennonbpjs_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220905_090036_migrate_ORDH159_infopasiennonbpjs_v cannot be reverted.\n";

        return false;
    }
    */
}
