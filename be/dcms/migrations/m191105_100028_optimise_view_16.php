<?php

use yii\db\Migration;

/**
 * Class m191105_100028_optimise_view_16
 */
class m191105_100028_optimise_view_16 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists public.pengajuanklaimalokasidetail_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.pengajuanklaimalokasidetail_v AS 
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
    rincian_tagihan.total_tagihan,
    klaiminacbg_t.total_tarifrs
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
            pendaftaran_t.tgl_pendaftaran,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.no_pendaftaran,
            bpjs_t.nosep,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama
           FROM pengajuanklaim_t
             JOIN pengajuanklaimdetail_t ON pengajuanklaim_t.pengajuanklaim_id = pengajuanklaimdetail_t.pengajuanklaim_id AND pengajuanklaimdetail_t.is_deleted = false
             JOIN pasien_m ON pengajuanklaimdetail_t.pasien_id = pasien_m.pasien_id
             JOIN carabayar_m ON pengajuanklaim_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pengajuanklaim_t.penjamin_id = penjamin_m.penjamin_id
             JOIN pendaftaran_t ON pengajuanklaimdetail_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             LEFT JOIN bpjs_t ON bpjs_t.bpjs_id = pendaftaran_t.bpjs_id
             JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
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
            ruangan_m.ruangan_nama
           FROM pengajuanklaim_t
             JOIN pengajuanklaimdetail_t ON pengajuanklaim_t.pengajuanklaim_id = pengajuanklaimdetail_t.pengajuanklaim_id AND pengajuanklaimdetail_t.is_deleted = false
             JOIN pasien_m ON pengajuanklaimdetail_t.pasien_id = pasien_m.pasien_id
             JOIN carabayar_m ON pengajuanklaim_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pengajuanklaim_t.penjamin_id = penjamin_m.penjamin_id
             JOIN pasienadmisi_t ON pengajuanklaimdetail_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN pendaftaran_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
             JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             LEFT JOIN bpjs_t ON bpjs_t.bpjs_id = pasienadmisi_t.bpjs_id
             JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id) pengajuan
     LEFT JOIN ( SELECT x.pendaftaran_id,
            sum(x.tagihan_tindakan_obat) AS total_tagihan
           FROM ( SELECT pendaftaran_t_1.pendaftaran_id,
                    sum(tindakanpelayanan_t.tarif_tindakan) AS tagihan_tindakan_obat
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN tindakanpelayanan_t ON pendaftaran_t_1.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false
                  GROUP BY pendaftaran_t_1.pendaftaran_id
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    sum(obatalkespasien_t.hargajual_oa) AS tagihan_tindakan_obat
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN obatalkespasien_t ON pendaftaran_t_1.pendaftaran_id = obatalkespasien_t.pendaftaran_id AND obatalkespasien_t.is_deleted = false
                  GROUP BY pendaftaran_t_1.pendaftaran_id) x
          GROUP BY x.pendaftaran_id) rincian_tagihan ON pengajuan.pendaftaran_id = rincian_tagihan.pendaftaran_id
     LEFT JOIN klaiminacbg_t ON pengajuan.pendaftaran_id = klaiminacbg_t.pendaftaran_id;
");

        $this->execute('ALTER TABLE public.pengajuanklaimalokasidetail_v
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.infopasiennonbpjs_v;');

        $this->execute("
                CREATE OR REPLACE VIEW public.infopasiennonbpjs_v AS 
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
            WHEN rincian.is_skd IS FALSE THEN 'Belum Dibuat'::text
            WHEN rincian.is_skd IS TRUE THEN 'Sudah Dibuat'::text
            ELSE NULL::text
        END AS status_skd,
    rincian.pasienadmisi_id,
    rincian.status_verifikasi,
    rincian.lookup_name AS status_verif
   FROM ( SELECT 'RD'::text AS tipe,
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
            fgetnamalookup(pendaftaran_t.status_verifikasi) AS lookup_name
           FROM pasien_m
             JOIN pendaftaran_t ON pasien_m.pasien_id = pendaftaran_t.pasien_id AND pendaftaran_t.instalasi_id <> 2
             JOIN carabayar_m carabayar_m_1 ON pendaftaran_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN penjamin_m penjamin_m_1 ON pendaftaran_t.penjamin_id = penjamin_m_1.penjamin_id
             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
             JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             JOIN ( SELECT pembayaranpelayanan_t_1.pendaftaran_id
                   FROM pembayaranpelayanan_t pembayaranpelayanan_t_1
                  WHERE (pembayaranpelayanan_t_1.carabayar_id <> ALL (ARRAY[5, 6])) AND pembayaranpelayanan_t_1.is_deleted = false
                  GROUP BY pembayaranpelayanan_t_1.pendaftaran_id) valid_cara_bayar ON valid_cara_bayar.pendaftaran_id = pendaftaran_t.pendaftaran_id
          WHERE pendaftaran_t.is_active = true AND pendaftaran_t.is_deleted = false AND (carabayar_m_1.carabayar_id <> ALL (ARRAY[5, 6]))
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
            fgetnamalookup(pasienadmisi_t.status_verifikasi) AS lookup_name
           FROM pasien_m
             JOIN pendaftaran_t ON pasien_m.pasien_id = pendaftaran_t.pasien_id AND pendaftaran_t.instalasi_id <> 2
             JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN carabayar_m carabayar_m_1 ON pasienadmisi_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN penjamin_m penjamin_m_1 ON pasienadmisi_t.penjamin_id = penjamin_m_1.penjamin_id
             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             JOIN ( SELECT pembayaranpelayanan_t_1.pendaftaran_id
                   FROM pembayaranpelayanan_t pembayaranpelayanan_t_1
                  WHERE (pembayaranpelayanan_t_1.carabayar_id <> ALL (ARRAY[5, 6])) AND pembayaranpelayanan_t_1.is_deleted = false
                  GROUP BY pembayaranpelayanan_t_1.pendaftaran_id) valid_cara_bayar ON valid_cara_bayar.pendaftaran_id = pendaftaran_t.pendaftaran_id
          WHERE pasienadmisi_t.is_active = true AND pasienadmisi_t.is_deleted = false AND pendaftaran_t.is_active = true AND pendaftaran_t.is_deleted = false AND (carabayar_m_1.carabayar_id <> ALL (ARRAY[5, 6]))
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
            fgetnamalookup(pendaftaran_t.status_verifikasi) AS lookup_name
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
                    pendaftaran_t_1.pasien_id
                   FROM pendaftaran_t pendaftaran_t_1
                     LEFT JOIN pasienadmisi_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t_1.pasienadmisi_id
                     JOIN ( SELECT pembayaranpelayanan_t_1.pendaftaran_id
                           FROM pembayaranpelayanan_t pembayaranpelayanan_t_1
                          WHERE (pembayaranpelayanan_t_1.carabayar_id <> ALL (ARRAY[5, 6])) AND pembayaranpelayanan_t_1.is_deleted = false
                          GROUP BY pembayaranpelayanan_t_1.pendaftaran_id) valid_cara_bayar ON valid_cara_bayar.pendaftaran_id = pendaftaran_t_1.pendaftaran_id
                  WHERE pendaftaran_t_1.instalasi_id = 2) pendaftaran_t ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN carabayar_m carabayar_m_1 ON pendaftaran_t.carabayar_id = carabayar_m_1.carabayar_id
             JOIN penjamin_m penjamin_m_1 ON pendaftaran_t.penjamin_id = penjamin_m_1.penjamin_id
             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             JOIN pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
             JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id) rincian
     LEFT JOIN ( SELECT tagihan.pendaftaran_id,
            tagihan.pasienadmisi_id,
            pasien_m.no_rekam_medik,
            sum(tagihan.tagihan_tindakan_obat) AS total_tagihan,
            sum(tagihan.tagihan_sudah_bayar) AS total_sdh_bayar,
            sum(tagihan.tagihan_tindakan_obat) - sum(tagihan.tagihan_sudah_bayar) - sum(tagihan.tagihan_asuransi) AS total_sisa_tagihan,
            sum(tagihan.tagihan_uang_muka) AS total_uang_muka,
            sum(tagihan.tagihan_biaya_admin) AS total_administrasi,
            sum(tagihan.tagihan_pembulatan) AS total_pembulatan,
            sum(tagihan.tagihan_asuransi) AS total_asuransi,
            sum(tagihan.pembayaran_pasien) AS total_pembayaran_pasien
           FROM ( SELECT pendaftaran_t.pendaftaran_id,
                    pendaftaran_t.pasienadmisi_id,
                    0 AS tagihan_tindakan_obat,
                    0 AS tagihan_sudah_bayar,
                    sum(bayaruangmuka_t.jumlah_uangmuka) AS tagihan_uang_muka,
                    0 AS tagihan_biaya_admin,
                    0 AS tagihan_pembulatan,
                    0 AS tagihan_asuransi,
                    0 AS pembayaran_pasien,
                    pendaftaran_t.pasien_id
                   FROM pendaftaran_t
                     LEFT JOIN bayaruangmuka_t ON pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id
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
                   FROM pendaftaran_t
                     LEFT JOIN pembayaranpelayanan_t pembayaranpelayanan_t_1 ON pendaftaran_t.pendaftaran_id = pembayaranpelayanan_t_1.pendaftaran_id
                  GROUP BY pendaftaran_t.pendaftaran_id, pembayaranpelayanan_t_1.biaya_administrasi, pembayaranpelayanan_t_1.pembulatan, pendaftaran_t.pasienadmisi_id, pembayaranpelayanan_t_1.total_subsidiasuransi) tagihan
             LEFT JOIN pasien_m ON tagihan.pasien_id = pasien_m.pasien_id
          GROUP BY tagihan.pendaftaran_id, tagihan.pasienadmisi_id, pasien_m.no_rekam_medik) rincianpasien_detail ON rincian.pendaftaran_id = rincianpasien_detail.pendaftaran_id
     LEFT JOIN pembayaranpelayanan_t ON rincian.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id
     LEFT JOIN carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT hitung.pendaftaran_id,
                CASE
                    WHEN hitung.instalasi_id = 3 THEN pendaftaran_t.pasienadmisi_id
                    ELSE NULL::integer
                END AS pasienadmisi_id,
            hitung.instalasi_id,
            hitung.instalasi_nama,
            sum(hitung.tarif) AS total,
            hitung.penjamin_id,
            penjamin_m_1.penjamin_nama
           FROM ( SELECT tindakanpelayanan_t.pendaftaran_id,
                    tindakanpelayanan_t.instalasi_id,
                    instalasi_m.instalasi_nama,
                    tindakanpelayanan_t.penjamin_id,
                    sum(tindakanpelayanan_t.tarif_tindakan) AS tarif
                   FROM tindakanpelayanan_t
                     LEFT JOIN instalasi_m ON tindakanpelayanan_t.instalasi_id = instalasi_m.instalasi_id
                  WHERE tindakanpelayanan_t.pasienmasukpenunjang_id IS NULL
                  GROUP BY tindakanpelayanan_t.pendaftaran_id, tindakanpelayanan_t.instalasi_id, instalasi_m.instalasi_nama, tindakanpelayanan_t.penjamin_id
                UNION ALL
                 SELECT tindakanpelayanan_t.pendaftaran_id,
                    ruangan_m.instalasi_id,
                    instalasi_m.instalasi_nama,
                    tindakanpelayanan_t.penjamin_id,
                    sum(tindakanpelayanan_t.tarif_tindakan) AS tarif
                   FROM tindakanpelayanan_t
                     LEFT JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
                     LEFT JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
                     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                  WHERE tindakanpelayanan_t.pasienmasukpenunjang_id IS NOT NULL
                  GROUP BY tindakanpelayanan_t.pendaftaran_id, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, tindakanpelayanan_t.penjamin_id
                UNION ALL
                 SELECT obatalkespasien_t.pendaftaran_id,
                    ruangan_m.instalasi_id,
                    instalasi_m.instalasi_nama,
                    obatalkespasien_t.penjamin_id,
                    sum(obatalkespasien_t.hargajual_oa) AS sum
                   FROM obatalkespasien_t
                     LEFT JOIN ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id
                     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                  WHERE obatalkespasien_t.resepturdetail_id IS NULL
                  GROUP BY obatalkespasien_t.pendaftaran_id, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, obatalkespasien_t.penjamin_id
                UNION ALL
                 SELECT obatalkespasien_t.pendaftaran_id,
                    ruangan_m.instalasi_id,
                    instalasi_m.instalasi_nama,
                    obatalkespasien_t.penjamin_id,
                    sum(obatalkespasien_t.hargajual_oa) AS sum
                   FROM obatalkespasien_t
                     LEFT JOIN resepturdetail_t ON obatalkespasien_t.resepturdetail_id = resepturdetail_t.resepturdetail_id
                     LEFT JOIN reseptur_t ON resepturdetail_t.reseptur_id = reseptur_t.reseptur_id
                     LEFT JOIN ruangan_m ON reseptur_t.ruanganreseptur_id = ruangan_m.ruangan_id
                     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                  WHERE obatalkespasien_t.resepturdetail_id IS NOT NULL
                  GROUP BY obatalkespasien_t.pendaftaran_id, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, obatalkespasien_t.penjamin_id) hitung
             JOIN pendaftaran_t ON hitung.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN penjamin_m penjamin_m_1 ON hitung.penjamin_id = penjamin_m_1.penjamin_id
          GROUP BY hitung.pendaftaran_id, hitung.instalasi_id, hitung.instalasi_nama, pendaftaran_t.pasienadmisi_id, pendaftaran_t.instalasi_id, hitung.penjamin_id, penjamin_m_1.penjamin_nama) cektagihanpejamin ON rincian.pendaftaran_id = cektagihanpejamin.pendaftaran_id AND pembayaranpelayanan_t.penjamin_id = cektagihanpejamin.penjamin_id
     LEFT JOIN ( SELECT x.pendaftaran_id,
            sum(x.tagihan_tindakan_obat) AS total_tagihan
           FROM ( SELECT pendaftaran_t_1.pendaftaran_id,
                    sum(tindakanpelayanan_t.tarif_tindakan) AS tagihan_tindakan_obat
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN tindakanpelayanan_t ON pendaftaran_t_1.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false
                  GROUP BY pendaftaran_t_1.pendaftaran_id
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    sum(obatalkespasien_t.hargajual_oa) AS tagihan_tindakan_obat
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN obatalkespasien_t ON pendaftaran_t_1.pendaftaran_id = obatalkespasien_t.pendaftaran_id AND obatalkespasien_t.is_deleted = false
                  GROUP BY pendaftaran_t_1.pendaftaran_id) x
          GROUP BY x.pendaftaran_id) rincian_tagihan ON rincian.pendaftaran_id = rincian_tagihan.pendaftaran_id
  WHERE pembayaranpelayanan_t.carabayar_id <> ALL (ARRAY[5, 6])
  GROUP BY rincian.tipe, rincian.pasien_id, rincian.no_rekam_medik, rincian.nama_pasien, rincian.pendaftaran_id, rincian.tgl_pendaftaran, rincian.tglpasienpulang, rincian.no_pendaftaran, rincian.ruangan_id, rincian.ruangan_nama, rincian.instalasi_id, rincian.instalasi_nama, pembayaranpelayanan_t.carabayar_id, carabayar_m.carabayar_nama, pembayaranpelayanan_t.penjamin_id, penjamin_m.penjamin_nama, rincian.kelaspelayanan_id, rincian.kelaspelayanan_nama, rincian.jeniskasuspenyakit_id, rincian.jeniskasuspenyakit_nama, rincian.pegawai_id, rincian.dokter, rincian.status_bayar, rincian_tagihan.total_tagihan, rincianpasien_detail.total_sdh_bayar, rincian.is_skd, (
        CASE
            WHEN rincian.is_skd IS FALSE THEN 'Belum Dibuat'::text
            WHEN rincian.is_skd IS TRUE THEN 'Sudah Dibuat'::text
            ELSE NULL::text
        END), rincian.pasienadmisi_id, rincian.status_verifikasi, rincian.lookup_name;");

        $this->execute('ALTER TABLE public.infopasiennonbpjs_v
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.infopembayaranalokasidetail_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infopembayaranalokasidetail_v AS 
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
    rincian_tagihan.total_tagihan,
    pembayaranalokasidetail_t.jumlah_bayar AS bayar_alokasi,
    pembayaranalokasidetail_t.pembayaranalokasidetail_id,
    pembayaranalokasidetail_t.pembayaranalokasi_id
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
            pendaftaran_t.tgl_pendaftaran,
            pasienpulang_t.tglpasienpulang,
            pendaftaran_t.no_pendaftaran,
            bpjs_t.nosep,
            pendaftaran_t.instalasi_id,
            instalasi_m.instalasi_nama,
            pendaftaran_t.ruangan_id,
            ruangan_m.ruangan_nama
           FROM pengajuanklaim_t
             JOIN pengajuanklaimdetail_t ON pengajuanklaim_t.pengajuanklaim_id = pengajuanklaimdetail_t.pengajuanklaim_id
             JOIN pasien_m ON pengajuanklaimdetail_t.pasien_id = pasien_m.pasien_id
             JOIN pendaftaran_t ON pengajuanklaimdetail_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             LEFT JOIN bpjs_t ON bpjs_t.bpjs_id = pendaftaran_t.bpjs_id
             JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
          WHERE pengajuanklaimdetail_t.pasienadmisi_id IS NULL AND pengajuanklaimdetail_t.is_deleted = false
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
            ruangan_m.ruangan_nama
           FROM pengajuanklaim_t
             JOIN pengajuanklaimdetail_t ON pengajuanklaim_t.pengajuanklaim_id = pengajuanklaimdetail_t.pengajuanklaim_id
             JOIN pasien_m ON pengajuanklaimdetail_t.pasien_id = pasien_m.pasien_id
             JOIN pasienadmisi_t ON pengajuanklaimdetail_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN pendaftaran_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
             JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             LEFT JOIN bpjs_t ON bpjs_t.bpjs_id = pasienadmisi_t.bpjs_id
             JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
          WHERE pengajuanklaimdetail_t.is_deleted = false) pengajuan
     LEFT JOIN ( SELECT x.pendaftaran_id,
            sum(x.tagihan_tindakan_obat) AS total_tagihan
           FROM ( SELECT pendaftaran_t_1.pendaftaran_id,
                    sum(tindakanpelayanan_t.tarif_tindakan) AS tagihan_tindakan_obat
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN tindakanpelayanan_t ON pendaftaran_t_1.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false
                  GROUP BY pendaftaran_t_1.pendaftaran_id
                UNION ALL
                 SELECT pendaftaran_t_1.pendaftaran_id,
                    sum(obatalkespasien_t.hargajual_oa) AS tagihan_tindakan_obat
                   FROM pendaftaran_t pendaftaran_t_1
                     JOIN obatalkespasien_t ON pendaftaran_t_1.pendaftaran_id = obatalkespasien_t.pendaftaran_id AND obatalkespasien_t.is_deleted = false
                  GROUP BY pendaftaran_t_1.pendaftaran_id) x
          GROUP BY x.pendaftaran_id) rincian_tagihan ON pengajuan.pendaftaran_id = rincian_tagihan.pendaftaran_id
     JOIN pembayaranalokasidetail_t ON pembayaranalokasidetail_t.pengajuanklaimdetail_id = pengajuan.pengajuanklaimdetail_id
  WHERE pembayaranalokasidetail_t.is_deleted = false;");

        $this->execute('ALTER TABLE public.infopembayaranalokasidetail_v
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.infopengajuanklaimdetail_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infopengajuanklaimdetail_v AS 
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
    rincianpasiendetail.total_sdh_bayar AS jumlah_telahbayar,
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
    rincian.total_tarifrs
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
            rincian_tagihan.total_tagihan,
            klaiminacbg_t.total_tarifrs
           FROM pengajuanklaim_t
             JOIN pengajuanklaimdetail_t ON pengajuanklaim_t.pengajuanklaim_id = pengajuanklaimdetail_t.pengajuanklaim_id AND pengajuanklaimdetail_t.is_deleted = false
             JOIN pasien_m ON pengajuanklaimdetail_t.pasien_id = pasien_m.pasien_id
             JOIN pendaftaran_t ON pengajuanklaimdetail_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND pengajuanklaimdetail_t.pasienadmisi_id IS NULL
             JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             LEFT JOIN bpjs_t ON bpjs_t.bpjs_id = pendaftaran_t.bpjs_id
             JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN ( SELECT x.pendaftaran_id,
                    sum(x.tagihan_tindakan_obat) AS total_tagihan
                   FROM ( SELECT pendaftaran_t_1.pendaftaran_id,
                            sum(tindakanpelayanan_t.tarif_tindakan) AS tagihan_tindakan_obat
                           FROM pendaftaran_t pendaftaran_t_1
                             JOIN tindakanpelayanan_t ON pendaftaran_t_1.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false
                          GROUP BY pendaftaran_t_1.pendaftaran_id
                        UNION ALL
                         SELECT pendaftaran_t_1.pendaftaran_id,
                            sum(obatalkespasien_t.hargajual_oa) AS tagihan_tindakan_obat
                           FROM pendaftaran_t pendaftaran_t_1
                             JOIN obatalkespasien_t ON pendaftaran_t_1.pendaftaran_id = obatalkespasien_t.pendaftaran_id AND obatalkespasien_t.is_deleted = false
                          GROUP BY pendaftaran_t_1.pendaftaran_id) x
                  GROUP BY x.pendaftaran_id) rincian_tagihan ON pendaftaran_t.pendaftaran_id = rincian_tagihan.pendaftaran_id
             LEFT JOIN klaiminacbg_t ON pendaftaran_t.pendaftaran_id = klaiminacbg_t.pendaftaran_id AND klaiminacbg_t.pasienadmisi_id IS NULL AND klaiminacbg_t.is_deleted = false
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
            rincian_tagihan.total_tagihan,
            klaiminacbg_t.total_tarifrs
           FROM pengajuanklaim_t
             JOIN pengajuanklaimdetail_t ON pengajuanklaim_t.pengajuanklaim_id = pengajuanklaimdetail_t.pengajuanklaim_id AND pengajuanklaimdetail_t.is_deleted = false
             JOIN pasien_m ON pengajuanklaimdetail_t.pasien_id = pasien_m.pasien_id
             JOIN pendaftaran_t ON pengajuanklaimdetail_t.pendaftaran_id = pendaftaran_t.pendaftaran_id AND pengajuanklaimdetail_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
             JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN pasienpulang_t ON pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             LEFT JOIN bpjs_t ON bpjs_t.bpjs_id = pasienadmisi_t.bpjs_id
             JOIN ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN ( SELECT x.pendaftaran_id,
                    sum(x.tagihan_tindakan_obat) AS total_tagihan
                   FROM ( SELECT pendaftaran_t_1.pendaftaran_id,
                            sum(tindakanpelayanan_t.tarif_tindakan) AS tagihan_tindakan_obat
                           FROM pendaftaran_t pendaftaran_t_1
                             JOIN tindakanpelayanan_t ON pendaftaran_t_1.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false
                          GROUP BY pendaftaran_t_1.pendaftaran_id
                        UNION ALL
                         SELECT pendaftaran_t_1.pendaftaran_id,
                            sum(obatalkespasien_t.hargajual_oa) AS tagihan_tindakan_obat
                           FROM pendaftaran_t pendaftaran_t_1
                             JOIN obatalkespasien_t ON pendaftaran_t_1.pendaftaran_id = obatalkespasien_t.pendaftaran_id AND obatalkespasien_t.is_deleted = false
                          GROUP BY pendaftaran_t_1.pendaftaran_id) x
                  GROUP BY x.pendaftaran_id) rincian_tagihan ON pendaftaran_t.pendaftaran_id = rincian_tagihan.pendaftaran_id
             LEFT JOIN klaiminacbg_t ON pendaftaran_t.pendaftaran_id = klaiminacbg_t.pendaftaran_id AND pendaftaran_t.pasienadmisi_id = klaiminacbg_t.pasienadmisi_id AND klaiminacbg_t.is_deleted = false) rincian
     JOIN ( SELECT pendaftaran_t.pendaftaran_id,
            sum(pembayaranpelayanan_t.total_terbayar) AS total_sdh_bayar
           FROM pendaftaran_t
             LEFT JOIN pembayaranpelayanan_t ON pendaftaran_t.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id
          GROUP BY pendaftaran_t.pendaftaran_id) rincianpasiendetail ON rincian.pendaftaran_id = rincianpasiendetail.pendaftaran_id;
");

        $this->execute('ALTER TABLE public.infopengajuanklaimdetail_v
  OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191105_100028_optimise_view_16 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191105_100028_optimise_view_16 cannot be reverted.\n";

        return false;
    }
    */
}
