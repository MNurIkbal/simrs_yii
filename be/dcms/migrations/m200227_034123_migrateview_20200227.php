<?php

use yii\db\Migration;

/**
 * Class m200227_034123_migrateview_20200227
 */
class m200227_034123_migrateview_20200227 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('DROP VIEW if exists public.rincianpasien_v;');

         $this->execute("
            CREATE OR REPLACE VIEW public.rincianpasien_v AS
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_asal,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    carabayar_admisi.carabayar_nama AS carabayar_admisi,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    penjamin_admisi.penjamin_nama AS penjamin_admisi,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    kelas_admisi.kelaspelayanan_nama AS kelas_admisi,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.pegawai_id AS dok_pendaftaran_id,
    dok_pendaftaran.nama_pegawai AS dok_pendaftaran,
    pasienadmisi_t.pegawai_id AS dok_admisi_id,
    dok_admisi.nama_pegawai AS dok_admisi,
    pendaftaran_t.ruangan_id AS ruangan_pendaftaran_id,
    r_pendaftaran.ruangan_nama AS ruangan_pendaftaran,
    pasienadmisi_t.ruangan_id AS ruangan_admisi_id,
    r_admisi.ruangan_nama AS ruangan_admisi,
    pasienadmisi_t.kamarruangan_id AS kamar_id,
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    pasienadmisi_t.kamartempattidur_id AS tempattidur_id,
    kamartempattidur_m.no_tempattidur AS tempat_tidur,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_lunas,
    pendaftaran_t.is_stopakomodasi,
        CASE
            WHEN pendaftaran_t.is_stopakomodasi <> true THEN false
            WHEN pendaftaran_t.is_stopakomodasi = true THEN true
            ELSE NULL::boolean
        END AS is_pulang
   FROM pendaftaran_t
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN carabayar_m carabayar_admisi ON pasienadmisi_t.carabayar_id = carabayar_admisi.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN penjamin_m penjamin_admisi ON pasienadmisi_t.penjamin_id = penjamin_admisi.penjamin_id
     LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m dok_pendaftaran ON pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id
     LEFT JOIN pegawai_m dok_admisi ON pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN kelaspelayanan_m kelas_admisi ON pasienadmisi_t.kelaspelayanan_id = kelas_admisi.kelaspelayanan_id
     JOIN ruangan_m r_pendaftaran ON pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id
     LEFT JOIN ruangan_m r_admisi ON pasienadmisi_t.ruangan_id = r_admisi.ruangan_id
     LEFT JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     LEFT JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
     LEFT JOIN bayaruangmuka_t ON pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id
     LEFT JOIN tindakansudahbayar_t ON tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
     LEFT JOIN obatsudahbayar_t ON obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
     LEFT JOIN pembayaranpelayanan_t ON pendaftaran_t.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id
  GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.pasienadmisi_id, pendaftaran_t.no_pendaftaran, pendaftaran_t.tgl_pendaftaran, pasienadmisi_t.tgl_admisi, pendaftaran_t.instalasi_id, instalasi_m.instalasi_nama, pendaftaran_t.pasien_id, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pendaftaran_t.carabayar_id, carabayar_m.carabayar_nama, pendaftaran_t.penjamin_id, penjamin_m.penjamin_nama, pendaftaran_t.kelaspelayanan_id, kelaspelayanan_m.kelaspelayanan_nama, pendaftaran_t.jeniskasuspenyakit_id, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pendaftaran_t.pegawai_id, pasienadmisi_t.pegawai_id, dok_pendaftaran.nama_pegawai, dok_admisi.nama_pegawai, pendaftaran_t.ruangan_id, r_pendaftaran.ruangan_nama, pasienadmisi_t.ruangan_id, r_admisi.ruangan_nama, pasienadmisi_t.kamarruangan_id, kamarruangan_m.kamarruangan_nokamar, pasienadmisi_t.kamartempattidur_id, kamartempattidur_m.no_tempattidur, pendaftaran_t.status_bayar, (fgetnamalookup(pendaftaran_t.status_bayar)), penjamin_admisi.penjamin_nama, carabayar_admisi.carabayar_nama, kelas_admisi.kelaspelayanan_nama;
");

         $this->execute('ALTER TABLE public.rincianpasien_v
    OWNER TO postgres;');

         $this->execute('DROP VIEW if exists public.rincianpasiendetail_v;');

         $this->execute("
            CREATE OR REPLACE VIEW public.rincianpasiendetail_v AS
 SELECT 'tindakan'::text AS tipe,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_asal,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.pegawai_id AS dok_pendaftaran_id,
    dok_pendaftaran.nama_pegawai AS dok_pendaftaran,
    pasienadmisi_t.pegawai_id AS dok_admisi_id,
    dok_admisi.nama_pegawai AS dok_admisi,
    pendaftaran_t.ruangan_id AS ruangan_pendaftaran_id,
    r_pendaftaran.ruangan_nama AS ruangan_pendaftaran,
    pasienadmisi_t.ruangan_id AS ruangan_admisi_id,
    r_admisi.ruangan_nama AS ruangan_admisi,
    pasienadmisi_t.kamarruangan_id AS kamar_id,
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    pasienadmisi_t.kamartempattidur_id AS tempattidur_id,
    kamartempattidur_m.no_tempattidur AS tempat_tidur,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar_nama,
    tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
    tindakanpelayanan_t.instalasi_id AS instalasi_pelayanan_id,
    instalasi_pelayanan.instalasi_nama AS instalasi_pelayanan,
    tindakanpelayanan_t.ruangan_id AS ruangan_pelayanan_id,
    ruangan_pelayanan.ruangan_nama AS ruangan_pelayanan,
    tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
    tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
    daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
    tindakanpelayanan_t.tipepaket_id,
    tipepaket_m.tipepaket_nama,
    jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
    pemeriksaanlab_m.pemeriksaanlab_nama,
    jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
    pemeriksaanrad_m.pemeriksaanrad_nama,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.tarif_satuan,
    tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
    tindakanpelayanan_t.tarif_tindakan AS jumlah_tarif,
    false AS is_obat,
    tindakanpelayanan_t.tindakansudahbayar_id,
    daftartindakan_m.is_akomodasi,
    daftartindakan_m.kelompoktindakan_id,
    kelompoktindakan_m.kelompoktindakan_nama
   FROM pendaftaran_t
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m dok_pendaftaran ON pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id
     LEFT JOIN pegawai_m dok_admisi ON pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ruangan_m r_pendaftaran ON pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id
     LEFT JOIN ruangan_m r_admisi ON pasienadmisi_t.ruangan_id = r_admisi.ruangan_id
     LEFT JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     LEFT JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
     LEFT JOIN instalasi_m instalasi_pelayanan ON tindakanpelayanan_t.instalasi_id = instalasi_pelayanan.instalasi_id
     LEFT JOIN ruangan_m ruangan_pelayanan ON tindakanpelayanan_t.ruangan_id = ruangan_pelayanan.ruangan_id
     LEFT JOIN pemeriksaanlab_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id
     LEFT JOIN jenispemeriksaanlab_m ON pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id
     LEFT JOIN pemeriksaanrad_m ON tindakanpelayanan_t.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id
     LEFT JOIN jenispemeriksaanrad_m ON pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id
     LEFT JOIN kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
UNION ALL
 SELECT 'obat'::text AS tipe,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    pasienadmisi_t.tgl_admisi,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_asal,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pendaftaran_t.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.jeniskasuspenyakit_id,
    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
    pendaftaran_t.pegawai_id AS dok_pendaftaran_id,
    dok_pendaftaran.nama_pegawai AS dok_pendaftaran,
    pasienadmisi_t.pegawai_id AS dok_admisi_id,
    dok_admisi.nama_pegawai AS dok_admisi,
    pendaftaran_t.ruangan_id AS ruangan_pendaftaran_id,
    r_pendaftaran.ruangan_nama AS ruangan_pendaftaran,
    pasienadmisi_t.ruangan_id AS ruangan_admisi_id,
    r_admisi.ruangan_nama AS ruangan_admisi,
    pasienadmisi_t.kamarruangan_id AS kamar_id,
    kamarruangan_m.kamarruangan_nokamar AS kamar,
    pasienadmisi_t.kamartempattidur_id AS tempattidur_id,
    kamartempattidur_m.no_tempattidur AS tempat_tidur,
    pendaftaran_t.status_bayar,
    fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar_nama,
    obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
    NULL::integer AS instalasi_pelayanan_id,
    NULL::character varying AS instalasi_pelayanan,
    NULL::integer AS ruangan_pelayanan_id,
    NULL::character varying AS ruangan_pelayanan,
    obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
    obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
    obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
    NULL::integer AS tipepaket_id,
    NULL::character varying AS tipepaket_nama,
    NULL::character varying AS jenispemeriksaanlab_nama,
    NULL::character varying AS pemeriksaanlab_nama,
    NULL::character varying AS jenispemeriksaanrad_nama,
    NULL::character varying AS pemeriksaanrad_nama,
    obatalkespasien_t.qty_oa AS qty,
    obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
    0 AS tarif_cyto,
    obatalkespasien_t.hargajual_oa AS jumlah_tarif,
    true AS is_obat,
    obatalkespasien_t.obatsudahbayar_id AS tindakansudahbayar_id,
    false AS is_akomodasi,
    NULL::integer AS kelompoktindakan_id,
    NULL::character varying AS kelompoktindakan_nama
   FROM pendaftaran_t
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
     LEFT JOIN pegawai_m dok_pendaftaran ON pendaftaran_t.pegawai_id = dok_pendaftaran.pegawai_id
     LEFT JOIN pegawai_m dok_admisi ON pasienadmisi_t.pegawai_id = dok_admisi.pegawai_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     JOIN ruangan_m r_pendaftaran ON pendaftaran_t.ruangan_id = r_pendaftaran.ruangan_id
     LEFT JOIN ruangan_m r_admisi ON pasienadmisi_t.ruangan_id = r_admisi.ruangan_id
     LEFT JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     LEFT JOIN kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     LEFT JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
     LEFT JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id;");

         $this->execute('ALTER TABLE public.rincianpasiendetail_v
    OWNER TO postgres;');

         $this->execute('DROP VIEW if exists public.rincianpasiendetail2_v;');

         $this->execute("
            CREATE OR REPLACE VIEW public.rincianpasiendetail2_v AS
 SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pasien_m.no_rekam_medik,
    COALESCE(total_tagihan.total, 0::double precision) AS total_tagihan,
        CASE
            WHEN pendaftaran_t.is_stopakomodasi <> true THEN COALESCE(sum(pendaftaranpenjamin_t.nominal_dijamin), 0::double precision)
            WHEN pendaftaran_t.is_stopakomodasi = true THEN COALESCE(pembayaran.total_dijamin, 0::double precision)
            ELSE 0::double precision
        END AS subsidi_asuransi,
    COALESCE(pembayaran.uang_masuk, 0::double precision) AS uang_masuk,
    COALESCE(pembayaran.total_sisatagihan, 0::double precision) AS total_sisa_tagihan,
    COALESCE(pembayaran.total_dibayar, 0::double precision) AS total_sdh_bayar,
    COALESCE(sum(pembayaranpelayanan_t.total_subsidiasuransi), 0::double precision) AS total_asuransi,
    COALESCE(sum(bayaruangmuka_t.jumlah_uangmuka), 0::double precision) AS total_uang_muka,
    COALESCE(pembayaran.total_administrasi, 0::double precision) AS total_administrasi,
    0 AS total_pembulatan,
    0 AS total_pembayaran_pasien
   FROM pendaftaran_t
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     LEFT JOIN ( SELECT pem.pendaftaran_id,
            sum(pem.total_dibayar) AS total_dibayar,
            sum(pem.total_tagihan) AS total_tagihan,
            sum(pem.total_administrasi) AS total_administrasi,
            sum(pem.total_tunai) AS total_tunai,
            sum(pem.total_nontunai) AS total_nontunai,
            sum(pem.total_kembalian) AS total_kembalian,
            sum(pem.total_sisatagihan) AS total_sisatagihan,
            sum(pem.total_tunai) + sum(pem.total_nontunai) - sum(pem.total_kembalian) AS uang_masuk,
            sum(pem.total_dijamin) AS total_dijamin
           FROM ( SELECT pembayaran_t.pendaftaran_id,
                    sum(pembayaran_t.total_dibayar) AS total_dibayar,
                    sum(pembayaran_t.total_tagihan) AS total_tagihan,
                    sum(pembayaran_t.total_administrasi) AS total_administrasi,
                    sum(pembayaran_t.total_tunai) AS total_tunai,
                    sum(pembayaran_t.total_nontunai) AS total_nontunai,
                    sum(pembayaran_t.total_kembalian) AS total_kembalian,
                    sum(pembayaran_t.total_sisatagihan) AS total_sisatagihan,
                    sum(pembayaran_t.total_tunai) + sum(pembayaran_t.total_nontunai) - sum(pembayaran_t.total_kembalian) AS uang_masuk,
                    sum(pembayaranpenjamin_t.total_dijamin) AS total_dijamin
                   FROM pembayaran_t
                     LEFT JOIN pembayaranpenjamin_t ON pembayaran_t.pembayaran_id = pembayaranpenjamin_t.pembayaran_id AND pembayaranpenjamin_t.is_deleted = false
                  WHERE pembayaran_t.is_deleted = false
                  GROUP BY pembayaran_t.pendaftaran_id, pembayaran_t.pembayaran_id) pem
          GROUP BY pem.pendaftaran_id) pembayaran ON pendaftaran_t.pendaftaran_id = pembayaran.pendaftaran_id
     LEFT JOIN ( SELECT tagihan.pendaftaran_id,
            sum(tagihan.total) AS total
           FROM ( SELECT tindakanpelayanan_t.pendaftaran_id,
                    sum(tindakanpelayanan_t.tarif_tindakan) AS total
                   FROM tindakanpelayanan_t
                  WHERE tindakanpelayanan_t.is_deleted = false
                  GROUP BY tindakanpelayanan_t.pendaftaran_id
                UNION ALL
                 SELECT obatalkespasien_t.pendaftaran_id,
                    sum(obatalkespasien_t.hargajual_oa) AS total
                   FROM obatalkespasien_t
                  WHERE obatalkespasien_t.is_deleted = false
                  GROUP BY obatalkespasien_t.pendaftaran_id) tagihan
          GROUP BY tagihan.pendaftaran_id) total_tagihan ON pendaftaran_t.pendaftaran_id = total_tagihan.pendaftaran_id
     LEFT JOIN pendaftaranpenjamin_t ON pendaftaran_t.pendaftaran_id = pendaftaranpenjamin_t.pendaftaran_id AND pendaftaranpenjamin_t.is_deleted = false
     LEFT JOIN pembayaranpelayanan_t ON pendaftaran_t.pendaftaran_id = pembayaranpelayanan_t.pendaftaran_id AND pembayaranpelayanan_t.is_deleted = false
     LEFT JOIN bayaruangmuka_t ON pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id AND bayaruangmuka_t.is_deleted = false
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
  WHERE pendaftaran_t.is_deleted = false
  GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.pasienadmisi_id, pasien_m.no_rekam_medik, total_tagihan.total, pembayaran.total_dibayar, pembayaran.total_tagihan, pembayaran.total_administrasi, pembayaran.total_tunai, pembayaran.total_nontunai, pembayaran.total_kembalian, pembayaran.total_sisatagihan, pembayaran.uang_masuk, pembayaran.total_dijamin;
");

         $this->execute('ALTER TABLE public.rincianpasiendetail2_v
    OWNER TO postgres;');

         $this->execute('DROP VIEW if exists public.tandabuktibayar_v;');

         $this->execute("
            CREATE OR REPLACE VIEW public.tandabuktibayar_v AS
 SELECT tandabuktibayar_t.tandabuktibayar_id,
    tandabuktibayar_t.nobuktibayar,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    kelaspelayanan_m.kelaspelayanan_nama,
    tandabuktibayar_t.tglbuktibayar,
    pembayaran_t.total_ditagihkan AS jmlpembayaran,
    pembayaranpelayanan_t.no_pembayaran,
    carabayar_m.carabayar_id,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pulang_rj.instalasi_nama
            ELSE pulang_ri.instalasi_nama
        END AS instalasi_akhir,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pulang_rj.ruangan_nama
            ELSE pulang_ri.ruangan_nama
        END AS ruangan_akhir,
    pendaftaran_t.tgl_pendaftaran,
    pasien_m.tanggal_lahir AS tgl_lahir,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN pulang_rj.tglpasienpulang
            WHEN pendaftaran_t.pasienadmisi_id IS NOT NULL THEN pulang_ri.tglpasienpulang
            ELSE pendaftaran_t.tgl_stopakomodasi
        END AS tgl_keluar,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN bpjs_rj.klsrawat
            ELSE bpjs_ri.klsrawat
        END AS hak_kelas,
        CASE
            WHEN retur.tandabuktibayar_id IS NULL THEN false
            ELSE true
        END AS is_returbayarpelayanan
   FROM tandabuktibayar_t
     JOIN pembayaranpelayanan_t ON tandabuktibayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
     JOIN pembayaran_t ON pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id AND pembayaran_t.is_deleted = false
     JOIN pendaftaran_t ON pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN carabayar_m ON pembayaranpelayanan_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pembayaranpelayanan_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN bpjs_t bpjs_rj ON pendaftaran_t.bpjs_id = bpjs_rj.bpjs_id
     LEFT JOIN bpjs_t bpjs_ri ON pasienadmisi_t.bpjs_id = bpjs_ri.bpjs_id
     LEFT JOIN ( SELECT pasienpulang_t.pasienpulang_id,
            pasienpulang_t.tglpasienpulang,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_nama
           FROM pasienpulang_t
             JOIN ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id) pulang_rj ON pendaftaran_t.pasienpulang_id = pulang_rj.pasienpulang_id
     LEFT JOIN ( SELECT pasienpulang_t.pasienpulang_id,
            pasienpulang_t.tglpasienpulang,
            ruangan_m.ruangan_nama,
            instalasi_m.instalasi_nama
           FROM pasienpulang_t
             JOIN ruangan_m ON pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id) pulang_ri ON pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id
     LEFT JOIN ( SELECT returbayarpelayanan_t.tandabuktibayar_id,
            sum(returbayarpelayanan_t.total_biayaretur) AS total_retur,
            tandabuktibayar_t_1.uangditerima - sum(returbayarpelayanan_t.total_biayaretur) AS sisa_pembayaran
           FROM returbayarpelayanan_t
             JOIN tandabuktibayar_t tandabuktibayar_t_1 ON returbayarpelayanan_t.tandabuktibayar_id = tandabuktibayar_t_1.tandabuktibayar_id
          WHERE returbayarpelayanan_t.is_deleted = false
          GROUP BY returbayarpelayanan_t.tandabuktibayar_id, tandabuktibayar_t_1.uangditerima) retur ON retur.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id
UNION ALL
 SELECT tandabuktibayar_t.tandabuktibayar_id,
    tandabuktibayar_t.nobuktibayar,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    kelaspelayanan_m.kelaspelayanan_nama,
    tandabuktibayar_t.tglbuktibayar,
    tandabuktibayar_t.jmlpembayaran,
    NULL::character varying AS no_pembayaran,
    carabayar_m.carabayar_id,
    NULL::character varying AS instalasi_akhir,
    NULL::character varying AS ruangan_akhir,
    NULL::timestamp without time zone AS tgl_pendaftaran,
    NULL::date AS tgl_lahir,
    NULL::timestamp without time zone AS tgl_keluar,
    NULL::integer AS hak_kelas,
    NULL::boolean AS is_returbayarpelayanan
   FROM tandabuktibayar_t
     JOIN bayaruangmuka_t ON tandabuktibayar_t.bayaruangmuka_id = bayaruangmuka_t.bayaruangmuka_id
     JOIN pendaftaran_t ON bayaruangmuka_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id;
");

         $this->execute('ALTER TABLE public.tandabuktibayar_v
    OWNER TO postgres;');

         $this->execute('DROP VIEW if exists public.infodatapendaftaran_v;');

         $this->execute("
            CREATE OR REPLACE VIEW public.infodatapendaftaran_v AS
 SELECT data_info.pendaftaran_id,
    data_info.instalasi_id AS ins_id,
    data_info.ruangan_id AS rua_id,
    data_info.pasien_id,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL THEN data_info.penjamin_id
            ELSE data_info.penjaminri_id
        END AS pen_id,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL THEN data_info.carabayar_id
            ELSE data_info.carabayarri_id
        END AS car_id,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL THEN data_info.kelaspelayanan_id
            ELSE data_info.kelaspelayananri_id
        END AS kelaspelayanan_id,
    data_info.pasienpulang_id,
    data_info.no_pendaftaran,
    data_info.tgl_pendaftaran,
    data_info.no_rekam_medik,
    data_info.nama_pasien,
    data_info.no_mobile_pasien,
    data_info.instalasi_nama AS ins_nama,
    data_info.ruangan_nama AS rua_nama,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL THEN data_info.carabayar_nama
            ELSE data_info.carabayar_nama_ri
        END AS car,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL THEN data_info.penjamin_nama
            ELSE data_info.penjamin_nama_ri
        END AS pen,
    data_info.kelaspelayanan_nama,
    data_info.jumlah_uangmuka,
    data_info.pasienpulangri_id,
    data_info.pasienadmisi_id,
    data_info.status_pasien,
    data_info.pasienmasukpenunjang_id,
        CASE
            WHEN data_info.tglpasienpulang IS NULL THEN data_info.tglpasienpulang_ri
            ELSE data_info.tglpasienpulang
        END AS tglpasienpulang,
    data_info.dokterrj_id,
    data_info.nama_dok_rj_rd,
    data_info.dokterri_id,
    data_info.nama_dok_ri,
    data_info.jeniskasuspenyakit_nama,
    data_info.umur,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL THEN data_info.carabayar_id
            ELSE data_info.carabayarri_id
        END AS carabayar_id,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL THEN data_info.penjamin_id
            ELSE data_info.penjaminri_id
        END AS penjamin_id,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL THEN data_info.carabayar_nama
            ELSE data_info.carabayar_nama_ri
        END AS carabayar_nama,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL THEN data_info.penjamin_nama
            ELSE data_info.penjamin_nama_ri
        END AS penjamin_nama,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL THEN data_info.instalasi_id
            ELSE data_info.instalasiri_id
        END AS instalasi_id,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL THEN data_info.ruangan_id
            ELSE data_info.ruanganri_id
        END AS ruangan_id,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL THEN data_info.instalasi_nama
            ELSE data_info.instalasi_nama_ri
        END AS instalasi_nama,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL THEN data_info.ruangan_nama
            ELSE data_info.ruangan_nama_ri
        END AS ruangan_nama,
    data_info.status_bayar,
    data_info.jeniskasuspenyakit_id,
    data_info.tanggal_lahir,
    data_info.penjualanresep_id,
    data_info.jasa,
    data_info.administrasi,
    data_info.obat,
    data_info.totalharga_jual,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL THEN data_info.kelas_bpjspendaftaran
            ELSE data_info.kelas_bpjsadmisi
        END AS hak_kelas,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL AND data_info.bpjs_idpendaftaran IS NOT NULL THEN data_info.no_bpjspendaftaran
            WHEN data_info.pasienadmisi_id IS NOT NULL AND data_info.bpjs_idadmisi IS NOT NULL THEN data_info.no_bpjsadmisi
            WHEN data_info.pasienadmisi_id IS NULL AND data_info.bpjs_idadmisi IS NULL THEN data_info.no_asuransipendaftaran
            WHEN data_info.pasienadmisi_id IS NOT NULL AND data_info.bpjs_idadmisi IS NULL THEN data_info.no_asuransiadmisi
            ELSE NULL::character varying
        END AS no_kartu,
        CASE
            WHEN data_info.pasienadmisi_id IS NULL THEN data_info.groupcarabayar_pendaftaran
            ELSE data_info.groupcarabayar_admisi
        END AS group_carabayar,
    data_info.total_piutang,
    data_info.keadaanmasuk_id,
    data_info.keadaan_masuk,
    data_info.transportasi_id,
    data_info.transportasi,
    data_info.keterangan_pendaftaran
   FROM ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.instalasi_id,
            pendaftaran_t.ruangan_id,
            pendaftaran_t.pasien_id,
            pendaftaran_t.penjamin_id,
            pendaftaran_t.carabayar_id,
            pendaftaran_t.kelaspelayanan_id,
            pasienadmisi_t.kelaspelayanan_id AS kelaspelayananri_id,
            pendaftaran_t.pasienpulang_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            pasien_m.no_rekam_medik,
            pasien_m.nama_pasien,
            pasien_m.no_mobile_pasien,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
                CASE
                    WHEN pendaftaran_t.pasienadmisi_id IS NULL THEN kelaspelayanan_m.kelaspelayanan_nama
                    ELSE kelaspelayanan_ri.kelaspelayanan_nama
                END AS kelaspelayanan_nama,
            pasienpulang_t.tglpasienpulang,
            pulang_ri.tglpasienpulang AS tglpasienpulang_ri,
            COALESCE(bayaruangmuka_t.jumlah_uangmuka, 0::double precision) - COALESCE(pemakaianuangmuka_t.pemakaian_uangmuka, 0::double precision) - COALESCE(pengembalianuangmuka_t.total_pengembalian, 0::double precision) AS jumlah_uangmuka,
            pasienadmisi_t.pasienpulang_id AS pasienpulangri_id,
            pasienadmisi_t.pasienadmisi_id,
            pendaftaran_t.status_pasien,
            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
            dok_rj_rd.nama_pegawai AS nama_dok_rj_rd,
            dok_ri.nama_pegawai AS nama_dok_ri,
            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
            pendaftaran_t.umur,
            pasienadmisi_t.carabayar_id AS carabayarri_id,
            carabayar_ri.carabayar_nama AS carabayar_nama_ri,
            pasienadmisi_t.penjamin_id AS penjaminri_id,
            penjamin_ri.penjamin_nama AS penjamin_nama_ri,
            pasienadmisi_t.ruangan_id AS ruanganri_id,
            ruang_ri.instalasi_id AS instalasiri_id,
            ruang_ri.ruangan_nama AS ruangan_nama_ri,
            ins_ri.instalasi_nama AS instalasi_nama_ri,
            pendaftaran_t.status_bayar,
            jeniskasuspenyakit_m.jeniskasuspenyakit_id,
            pasien_m.tanggal_lahir,
            NULL::integer AS penjualanresep_id,
            0 AS jasa,
                CASE
                    WHEN penjualan_resep.biaya_adm IS NULL THEN 0::double precision
                    ELSE penjualan_resep.biaya_adm
                END AS administrasi,
            0 AS obat,
            0 AS totalharga_jual,
            bpjs_pendaftaran.klsrawat AS kelas_bpjspendaftaran,
            bpjs_admisi.klsrawat AS kelas_bpjsadmisi,
            bpjs_pendaftaran.bpjs_id AS bpjs_idpendaftaran,
            bpjs_admisi.bpjs_id AS bpjs_idadmisi,
            bpjs_pendaftaran.nokartuasuransi AS no_bpjspendaftaran,
            bpjs_admisi.nokartuasuransi AS no_bpjsadmisi,
            asuransi_pendaftaran.nokartuasuransi AS no_asuransipendaftaran,
            asuransi_admisi.nokartuasuransi AS no_asuransiadmisi,
            carabayar_m.groupcarabayar_id AS groupcarabayar_pendaftaran,
            carabayar_ri.groupcarabayar_id AS groupcarabayar_admisi,
            COALESCE(pemberianpiutang_t.total_piutang, 0::double precision) AS total_piutang,
            pendaftaran_t.pegawai_id AS dokterrj_id,
            pasienadmisi_t.pegawai_id AS dokterri_id,
            pendaftaran_t.keadaan_masuk AS keadaanmasuk_id,
            fgetnamalookup(pendaftaran_t.keadaan_masuk::integer) AS keadaan_masuk,
            pendaftaran_t.transportasi AS transportasi_id,
            fgetnamalookup(pendaftaran_t.transportasi::integer) AS transportasi,
            pendaftaran_t.keterangan_pendaftaran
           FROM pendaftaran_t
             LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
             JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
             JOIN instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
             JOIN ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN ruangan_m ruang_ri ON pasienadmisi_t.ruangan_id = ruang_ri.ruangan_id
             LEFT JOIN instalasi_m ins_ri ON ruang_ri.instalasi_id = ins_ri.instalasi_id
             JOIN carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
             JOIN penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN carabayar_m carabayar_ri ON pasienadmisi_t.carabayar_id = carabayar_ri.carabayar_id
             LEFT JOIN penjamin_m penjamin_ri ON pasienadmisi_t.penjamin_id = penjamin_ri.penjamin_id
             JOIN kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             LEFT JOIN kelaspelayanan_m kelaspelayanan_ri ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_ri.kelaspelayanan_id
             JOIN jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
             LEFT JOIN pasienpulang_t ON pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id
             LEFT JOIN pasienpulang_t pulang_ri ON pasienadmisi_t.pasienpulang_id = pulang_ri.pasienpulang_id
             LEFT JOIN ( SELECT bayaruangmuka_t_1.pendaftaran_id,
                    sum(bayaruangmuka_t_1.jumlah_uangmuka) AS jumlah_uangmuka
                   FROM bayaruangmuka_t bayaruangmuka_t_1
                  WHERE bayaruangmuka_t_1.is_deleted = false
                  GROUP BY bayaruangmuka_t_1.pendaftaran_id) bayaruangmuka_t ON pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id
             LEFT JOIN pasienmasukpenunjang_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT pengembalianuangmuka_t_1.pendaftaran_id,
                    sum(pengembalianuangmuka_t_1.total_pengembalian) AS total_pengembalian
                   FROM pengembalianuangmuka_t pengembalianuangmuka_t_1
                  WHERE pengembalianuangmuka_t_1.is_deleted = false
                  GROUP BY pengembalianuangmuka_t_1.pendaftaran_id) pengembalianuangmuka_t ON pendaftaran_t.pendaftaran_id = pengembalianuangmuka_t.pendaftaran_id
             LEFT JOIN ( SELECT pemakaianuangmuka_t_1.pendaftaran_id,
                    sum(pemakaianuangmuka_t_1.pemakaian_uangmuka) AS pemakaian_uangmuka
                   FROM pemakaianuangmuka_t pemakaianuangmuka_t_1
                  WHERE pemakaianuangmuka_t_1.is_deleted = false
                  GROUP BY pemakaianuangmuka_t_1.pendaftaran_id) pemakaianuangmuka_t ON pendaftaran_t.pendaftaran_id = pemakaianuangmuka_t.pendaftaran_id
             LEFT JOIN pegawai_m dok_rj_rd ON pendaftaran_t.pegawai_id = dok_rj_rd.pegawai_id
             LEFT JOIN pegawai_m dok_ri ON pasienadmisi_t.pegawai_id = dok_ri.pegawai_id
             LEFT JOIN bpjs_t bpjs_pendaftaran ON pendaftaran_t.bpjs_id = bpjs_pendaftaran.bpjs_id
             LEFT JOIN bpjs_t bpjs_admisi ON pasienadmisi_t.bpjs_id = bpjs_admisi.bpjs_id
             LEFT JOIN asuransipasien_m asuransi_pendaftaran ON pendaftaran_t.asuransipasien_id = asuransi_pendaftaran.asuransipasien_id
             LEFT JOIN asuransipasien_m asuransi_admisi ON pasienadmisi_t.asuransipasien_id = asuransi_admisi.asuransipasien_id
             LEFT JOIN pemberianpiutang_t ON pemberianpiutang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             LEFT JOIN ( SELECT sum(pt.biayaadministrasi) AS biaya_adm,
                    pt.pendaftaran_id
                   FROM penjualanresep_t pt
                  WHERE pt.status_bayar = 349 AND pt.is_deleted = false
                  GROUP BY pt.pendaftaran_id) penjualan_resep ON penjualan_resep.pendaftaran_id = pendaftaran_t.pendaftaran_id
          GROUP BY pendaftaran_t.pendaftaran_id, pendaftaran_t.instalasi_id, pendaftaran_t.ruangan_id, pendaftaran_t.pasien_id, pendaftaran_t.penjamin_id, pendaftaran_t.carabayar_id, pendaftaran_t.kelaspelayanan_id, pasienadmisi_t.kelaspelayanan_id, pendaftaran_t.pasienpulang_id, pendaftaran_t.no_pendaftaran, pendaftaran_t.tgl_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pasien_m.no_mobile_pasien, instalasi_m.instalasi_nama, ruangan_m.ruangan_nama, carabayar_m.carabayar_nama, penjamin_m.penjamin_nama, kelaspelayanan_m.kelaspelayanan_nama, pasienpulang_t.tglpasienpulang, pasienadmisi_t.pasienpulang_id, pasienadmisi_t.pasienadmisi_id, pasienmasukpenunjang_t.pasienmasukpenunjang_id, pendaftaran_t.status_pasien, pulang_ri.tglpasienpulang, dok_rj_rd.nama_pegawai, dok_ri.nama_pegawai, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pasienadmisi_t.carabayar_id, carabayar_ri.carabayar_nama, pasienadmisi_t.penjamin_id, penjamin_ri.penjamin_nama, pasienadmisi_t.ruangan_id, ruang_ri.instalasi_id, ruang_ri.ruangan_nama, ins_ri.instalasi_nama, bayaruangmuka_t.jumlah_uangmuka, pemakaianuangmuka_t.pemakaian_uangmuka, pengembalianuangmuka_t.total_pengembalian, pendaftaran_t.status_bayar, jeniskasuspenyakit_m.jeniskasuspenyakit_id, pasien_m.tanggal_lahir, bpjs_pendaftaran.klsrawat, bpjs_admisi.klsrawat, bpjs_pendaftaran.bpjs_id, bpjs_admisi.bpjs_id, bpjs_pendaftaran.nokartuasuransi, bpjs_admisi.nokartuasuransi, asuransi_pendaftaran.nokartuasuransi, asuransi_admisi.nokartuasuransi, kelaspelayanan_ri.kelaspelayanan_nama, carabayar_m.groupcarabayar_id, carabayar_ri.groupcarabayar_id, pemberianpiutang_t.total_piutang, pendaftaran_t.keadaan_masuk, pendaftaran_t.transportasi, pendaftaran_t.keterangan_pendaftaran, penjualan_resep.biaya_adm
        UNION ALL
         SELECT NULL::integer AS pendaftaran_id,
            ruangan_m.instalasi_id,
            penjualanresep_t.ruangan_id,
            penjualanresep_t.pasien_id,
            penjualanresep_t.penjamin_id,
            penjualanresep_t.carabayar_id,
            penjualanresep_t.kelaspelayanan_id,
            penjualanresep_t.kelaspelayanan_id AS kelaspelayananri_id,
            0 AS pasienpulang_id,
            penjualanresep_t.noresep AS no_pendaftaran,
            penjualanresep_t.tglpenjualan AS tgl_pendaftaran,
            pasien_m.no_rekam_medik,
            penjualanresep_t.nama_pembeli AS nama_pasien,
            pasien_m.no_mobile_pasien,
            instalasi_m.instalasi_nama,
            ruangan_m.ruangan_nama,
            carabayar_m.carabayar_nama,
            penjamin_m.penjamin_nama,
            NULL::character varying AS kelaspelayanan_nama,
            penjualanresep_t.tglresep AS tglpasienpulang,
            penjualanresep_t.tglresep AS tglpasienpulang_ri,
            0 AS jumlah_uangmuka,
            0 AS pasienpulangri_id,
            0 AS pasienadmisi_id,
            NULL::character varying AS status_pasien,
            0 AS pasienmasukpenunjang_id,
            pegawai_m.nama_pegawai AS nama_dok_rj_rd,
            pegawai_m.nama_pegawai AS nama_dok_ri,
            NULL::character varying AS jeniskasuspenyakit_nama,
            NULL::character varying AS umur,
            penjualanresep_t.carabayar_id AS carabayarri_id,
            carabayar_m.carabayar_nama AS carabayar_nama_ri,
            penjualanresep_t.penjamin_id AS penjaminri_id,
            penjamin_m.penjamin_nama AS penjamin_nama_ri,
            penjualanresep_t.ruangan_id AS ruanganri_id,
            ruangan_m.instalasi_id AS instalasiri_id,
            ruangan_m.ruangan_nama AS ruangan_nama_ri,
            instalasi_m.instalasi_nama AS instalasi_nama_ri,
            penjualanresep_t.status_bayar,
            0 AS jeniskasuspenyakit_id,
            pasien_m.tanggal_lahir,
            penjualanresep_t.penjualanresep_id,
            COALESCE(penjualanresep_t.totaltarifservice, 0::double precision) AS jasa,
            COALESCE(penjualanresep_t.biayaadministrasi, 0::double precision) AS administrasi,
            COALESCE(penjualanresep_t.totalhargajual, 0::double precision) AS obat,
            COALESCE(penjualanresep_t.totalhargajual, 0::double precision) + COALESCE(penjualanresep_t.totaltarifservice, 0::double precision) + COALESCE(penjualanresep_t.biayaadministrasi, 0::double precision) AS totalharga_jual,
            NULL::integer AS kelas_bpjspendaftaran,
            NULL::integer AS kelas_bpjsadmisi,
            NULL::integer AS bpjs_idpendaftaran,
            NULL::integer AS bpjs_idadmisi,
            NULL::character varying AS no_bpjspendaftaran,
            NULL::character varying AS no_bpjsadmisi,
            NULL::character varying AS no_asuransipendaftaran,
            NULL::character varying AS no_asuransiadmisi,
            carabayar_m.groupcarabayar_id AS groupcarabayar_pendaftaran,
            carabayar_m.groupcarabayar_id AS groupcarabayar_admisi,
            0 AS total_piutang,
            NULL::integer AS dokterrj_id,
            NULL::integer AS dokterri_id,
            NULL::character varying AS keadaanmasuk_id,
            NULL::character varying AS keadaan_masuk,
            NULL::character varying AS transportasi_id,
            NULL::character varying AS transportasi,
            NULL::text AS keterangan_pendaftaran
           FROM penjualanresep_t
             LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
             LEFT JOIN ruangan_m ON penjualanresep_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             LEFT JOIN carabayar_m ON penjualanresep_t.carabayar_id = carabayar_m.carabayar_id
             LEFT JOIN penjamin_m ON penjualanresep_t.penjamin_id = penjamin_m.penjamin_id
             LEFT JOIN pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
          WHERE penjualanresep_t.jenispenjualan::text = '343'::text AND penjualanresep_t.is_deleted = false) data_info;
");

         $this->execute('ALTER TABLE public.infodatapendaftaran_v
    OWNER TO postgres;');

         $this->execute('GRANT ALL ON TABLE public.infodatapendaftaran_v TO postgres;
');
         
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200227_034123_migrateview_20200227 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200227_034123_migrateview_20200227 cannot be reverted.\n";

        return false;
    }
    */
}
