<?php

use yii\db\Migration;

/**
 * Class m221208_042926_hotfix_temuan_live_view_infoorderanrad_v
 */
class m221208_042926_hotfix_temuan_live_view_infoorderanrad_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infoorderanrad_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infoorderanrad_v" AS  SELECT pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    pasienkirimkeunitlain_t.pendaftaran_id,
    pasienkirimkeunitlain_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran, 
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.umur,
    look_jeniskelamin.jeniskelamin AS jenis_kelamin,
    kelaspelayanan_m.kelaspelayanan_nama,
    pendaftaran_t.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    NULL::character varying AS kamarruangan_nokamar,
    NULL::character varying AS no_tempattidur,
    pendaftaran_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_perujuk,
    pendaftaran_t.carabayar_id,
    carabayar_m.carabayar_nama,
    pendaftaran_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienkirimkeunitlain_t.status_penunjang,
    look_status_penunjang.status_penunjang AS stat_penunjang,
    pendaftaran_t.kelaspelayanan_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pendaftaran_t.ruangan_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.kunjungan,
    pasienkirimkeunitlain_t.ruangan_id AS ruanganpenunjang_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.status_pasien,
    carabayar_m.groupcarabayar_id,
    pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienmasukpenunjang_t.status_periksa,
    pasien_m.alamat_pasien,
    NULL::character varying AS kode_pos,
    pasien_m.no_telepon_pasien,
    ruangan_m.ruangan_singkatan AS kode_ruangan,
        CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
            WHEN 0 THEN \'Pendaftaran\'::text
            ELSE \'Unit\'::text
        END AS unit_asal,
        CASE
            WHEN ruangan_m.instalasi_id = 3 THEN diagnosa_ri.diagnosa_utama
            WHEN ruangan_m.instalasi_id = 2 THEN diagnosa_rd.diagnosa_utama
            ELSE diagnosa.diagnosa_utama
        END AS nama_diagnosa,
    pemeriksaan.nama_pemeriksaan,
    penjamin_m.penjamin_kode AS carabayar_kode,
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
    pembayaran.no_pembayaran,
    pegawai_m.nomorindukpegawai,
    pasien_m.jeniskelamin AS jenis_kelamin_id,
        CASE
            WHEN pasienmasukpenunjang_t.pasienmasukpenunjang_id IS NULL THEN \'Belum Bayar\'::text
            ELSE COALESCE(tindakanpelayanan.status_bayar, \'Batal Bayar\'::text)
        END AS status_bayar,
    pasienkirimkeunitlain_t.is_rujukan,
    COALESCE(total_pemeriksaan.jml_pemeriksaan, 0::bigint) AS jml_pemeriksaan,
    COALESCE(cyto_tindakan.is_cyto, false) AS cyto_tindakan,
    look_jeniskelamin.jeniskelamin_kode AS jenis_kelamin_kode,
    rujukan_t.asalrujukan_id,
    rujukan_t.rujukandari_id,
        CASE
            WHEN asalrujukan_m.asalrujukan_nama IS NULL THEN ruangan_m.ruangan_nama
            ELSE asalrujukan_m.asalrujukan_nama
        END AS asalrujukan_nama,
    perujuk_m.namaperujuk AS rujukandari_nama,
    carabayar_m.carabayar_kode_warna,
        CASE
            WHEN permintaankepenunjang_t.qty_dirujuk > 0 THEN true
            ELSE false
        END AS is_referred
   FROM pasienkirimkeunitlain_t
     JOIN ( SELECT a.pendaftaran_id,
            a.pasien_id,
            a.instalasi_id,
            a.ruangan_id,
            a.pegawai_id,
            a.carabayar_id,
            a.penjamin_id,
            a.kelaspelayanan_id,
            a.rujukan_id,
            a.no_pendaftaran,
            a.umur,
            a.jeniskasuspenyakit_id,
            a.tgl_pendaftaran,
            a.kunjungan,
            a.status_pasien
           FROM pendaftaran_t a) pendaftaran_t ON pasienkirimkeunitlain_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_rekam_medik,
            a.jeniskelamin,
            a.tanggal_lahir,
            a.alamat_pasien,
            a.no_telepon_pasien
           FROM pasien_m a) pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON pendaftaran_t.instalasi_id = instalasi_m.instalasi_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.ruangan_singkatan,
            a.instalasi_id
           FROM ruangan_m a) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai,
            a.nomorindukpegawai
           FROM pegawai_m a) pegawai_m ON pendaftaran_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama,
            a.carabayar_kode_warna,
            a.groupcarabayar_id
           FROM carabayar_m a) carabayar_m ON pendaftaran_t.carabayar_id = carabayar_m.carabayar_id
     JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama,
            a.penjamin_kode
           FROM penjamin_m a) penjamin_m ON pendaftaran_t.penjamin_id = penjamin_m.penjamin_id
     JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_m ON pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT a.pasienmasukpenunjang_id,
            a.is_bayar,
            a.status_periksa,
            a.pasienkirimkeunitlain_id
           FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT a.rujukan_id,
            a.asalrujukan_id,
            a.rujukandari_id
           FROM rujukan_t a) rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     LEFT JOIN ( SELECT a.asalrujukan_id,
            a.asalrujukan_nama
           FROM asalrujukan_m a) asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     LEFT JOIN ( SELECT a.perujuk_id,
            a.namaperujuk
           FROM perujuk_m a) perujuk_m ON rujukan_t.rujukandari_id = perujuk_m.perujuk_id
     LEFT JOIN ( SELECT x.pasienmasukpenunjang_id,
            x.status_bayar
           FROM ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                        CASE
                            WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL THEN \'Sudah Bayar\'::text
                            WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false THEN \'Belum Bayar\'::text
                            ELSE \'Batal Bayar\'::text
                        END AS status_bayar
                   FROM tindakanpelayanan_t
                  WHERE tindakanpelayanan_t.is_deleted = false
                  GROUP BY tindakanpelayanan_t.tindakansudahbayar_id, tindakanpelayanan_t.is_deleted, tindakanpelayanan_t.pasienmasukpenunjang_id) x
          GROUP BY x.pasienmasukpenunjang_id, x.status_bayar) tindakanpelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                CASE
                    WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
                    ELSE NULL::json
                END AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN ( SELECT a.pasien_id
                   FROM pasien_m a) pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN ( SELECT a.pasienmorbiditas_id,
                    a.pendaftaran_id,
                    a.is_deleted,
                    a.kelompokdiagnosa_id,
                    a.diagnosa_pasien
                   FROM pasienmorbiditas_t a) pasienmorbiditas_t ON pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false
          WHERE pasienmorbiditas_t.kelompokdiagnosa_id = 2 AND pasienmorbiditas_t.diagnosa_pasien IS NOT NULL) diagnosa ON pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id
     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
            cppt_t.a_diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN ( SELECT a.pasien_id
                   FROM pasien_m a) pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN ( SELECT cppt_t_1.cppt_id,
                    cppt_t_1.pendaftaran_id,
                    cppt_t_1.a_diag_utama,
                    cppt_t_1.a_diag_penyerta
                   FROM cppt_t cppt_t_1
                     JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                            cppt_last.pendaftaran_id
                           FROM cppt_t cppt_last
                          WHERE cppt_last.is_deleted = false
                          GROUP BY cppt_last.pendaftaran_id) cppt_max ON cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id AND cppt_t_1.cppt_id = cppt_max.cppt_id) cppt_t ON pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id
          WHERE cppt_t.a_diag_utama IS NOT NULL) diagnosa_rd ON pendaftaran_t.pendaftaran_id = diagnosa_rd.pendaftaran_id
     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
            resumemedisri_t.diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN ( SELECT a.pasien_id
                   FROM pasien_m a) pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN ( SELECT a.pasienadmisi_id
                   FROM pasienadmisi_t a) pasienadmisi_t_1 ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
             JOIN ( SELECT a.resumemedisri_id,
                    a.pendaftaran_id,
                    a.pasienadmisi_id,
                    a.diag_utama
                   FROM resumemedisri_t a) resumemedisri_t ON pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
          WHERE resumemedisri_t.diag_utama IS NOT NULL) diagnosa_ri ON pendaftaran_t.pendaftaran_id = diagnosa_ri.pendaftaran_id
     LEFT JOIN ( SELECT permintaankepenunjang_t_1.pasienkirimkeunitlain_id,
            string_agg(DISTINCT daftartindakan_m.daftartindakan_nama::text ||
                CASE
                    WHEN permintaankepenunjang_t_1.is_referred IS TRUE THEN \' (Dirujuk)\'::text
                    ELSE \'\'::text
                END, \', \'::text) AS nama_pemeriksaan
           FROM permintaankepenunjang_t permintaankepenunjang_t_1
             JOIN ( SELECT a.daftartindakan_id,
                    a.daftartindakan_nama
                   FROM daftartindakan_m a) daftartindakan_m ON permintaankepenunjang_t_1.daftartindakan_id = daftartindakan_m.daftartindakan_id
          WHERE permintaankepenunjang_t_1.is_deleted IS FALSE
          GROUP BY permintaankepenunjang_t_1.pasienkirimkeunitlain_id) pemeriksaan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pemeriksaan.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
            string_agg(pembayaran_t.no_pembayaran::text, \',\'::text) AS no_pembayaran
           FROM pembayaran_t
          WHERE pembayaran_t.is_deleted IS FALSE
          GROUP BY pembayaran_t.pendaftaran_id) pembayaran ON pendaftaran_t.pendaftaran_id = pembayaran.pendaftaran_id
     LEFT JOIN ( SELECT permintaankepenunjang_t_1.pasienkirimkeunitlain_id,
            count(*) AS jml_pemeriksaan
           FROM permintaankepenunjang_t permintaankepenunjang_t_1
          WHERE permintaankepenunjang_t_1.is_deleted IS FALSE
          GROUP BY permintaankepenunjang_t_1.pasienkirimkeunitlain_id) total_pemeriksaan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = total_pemeriksaan.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT DISTINCT ON (permintaankepenunjang_t_1.pasienkirimkeunitlain_id, permintaankepenunjang_t_1.is_cyto) permintaankepenunjang_t_1.pasienkirimkeunitlain_id,
            permintaankepenunjang_t_1.is_cyto
           FROM permintaankepenunjang_t permintaankepenunjang_t_1
          WHERE permintaankepenunjang_t_1.is_cyto = true) cyto_tindakan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = cyto_tindakan.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
            count(*) AS qty_dirujuk
           FROM permintaankepenunjang_t a
          WHERE a.is_referred IS TRUE
          GROUP BY a.pasienkirimkeunitlain_id) permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name AS jeniskelamin,
            a.lookup_kode AS jeniskelamin_kode
           FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::text = look_jeniskelamin.jeniskelamin::text
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name AS status_penunjang
           FROM lookup_m a) look_status_penunjang ON pasienkirimkeunitlain_t.status_penunjang::text = look_status_penunjang.status_penunjang::text
  WHERE pasienkirimkeunitlain_t.instalasi_id = 5
UNION ALL
 SELECT pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    pendaftaran_t.pendaftaran_id,
    pasienkirimkeunitlain_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
    pendaftaran_t.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    pendaftaran_t.umur,
    look_jeniskelamin.jeniskelamin AS jenis_kelamin,
    kelaspelayanan_m.kelaspelayanan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    ruangan_m.ruangan_nama,
    kamarruangan_m.kamarruangan_nokamar,
    kamartempattidur_m.no_tempattidur,
    pasienadmisi_t.pegawai_id,
    pegawai_m.nama_pegawai AS dokter_perujuk,
    penjamin_m.carabayar_id,
    carabayar_m.carabayar_nama,
    pasienadmisi_t.penjamin_id,
    penjamin_m.penjamin_nama,
    pasienkirimkeunitlain_t.status_penunjang,
    look_status_penunjang.status_penunjang AS stat_penunjang,
    pasienadmisi_t.kelaspelayanan_id,
    pendaftaran_t.jeniskasuspenyakit_id,
    pasienadmisi_t.ruangan_id,
    pendaftaran_t.tgl_pendaftaran,
    pendaftaran_t.kunjungan,
    pasienkirimkeunitlain_t.ruangan_id AS ruanganpenunjang_id,
    pasien_m.tanggal_lahir,
    pendaftaran_t.status_pasien,
    carabayar_m.groupcarabayar_id,
    pasienkirimkeunitlain_t.instalasi_id AS instalasipen_id,
    pasienmasukpenunjang_t.is_bayar,
    pasienmasukpenunjang_t.status_periksa,
    pasien_m.alamat_pasien,
    NULL::character varying AS kode_pos,
    pasien_m.no_telepon_pasien,
    ruangan_m.ruangan_singkatan AS kode_ruangan,
        CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
            WHEN 0 THEN \'Pendaftaran\'::text
            ELSE \'Unit\'::text
        END AS unit_asal,
    diagnosa.diagnosa_utama AS nama_diagnosa,
    pemeriksaan.nama_pemeriksaan,
    penjamin_m.penjamin_kode AS carabayar_kode,
    pasienkirimkeunitlain_t.catatan_dokterpengirim,
    pembayaran.no_pembayaran,
    pegawai_m.nomorindukpegawai,
    pasien_m.jeniskelamin AS jenis_kelamin_id,
        CASE
            WHEN pasienmasukpenunjang_t.pasienmasukpenunjang_id IS NULL THEN \'Belum Bayar\'::text
            ELSE COALESCE(tindakanpelayanan.status_bayar, \'Batal Bayar\'::text)
        END AS status_bayar,
    pasienkirimkeunitlain_t.is_rujukan,
    COALESCE(total_pemeriksaan.jml_pemeriksaan, 0::bigint) AS jml_pemeriksaan,
    COALESCE(cyto_tindakan.is_cyto, false) AS cyto_tindakan,
    look_jeniskelamin.jeniskelamin_kode AS jenis_kelamin_kode,
    rujukan_t.asalrujukan_id,
    rujukan_t.rujukandari_id,
        CASE
            WHEN asalrujukan_m.asalrujukan_nama IS NULL THEN ruangan_m.ruangan_nama
            ELSE asalrujukan_m.asalrujukan_nama
        END AS asalrujukan_nama,
    perujuk_m.namaperujuk AS rujukandari_nama,
    carabayar_m.carabayar_kode_warna,
        CASE
            WHEN permintaankepenunjang_t.qty_dirujuk > 0 THEN true
            ELSE false
        END AS is_referred
   FROM pasienkirimkeunitlain_t
     JOIN ( SELECT a.pasienadmisi_id,
            a.pasien_id,
            a.ruangan_id,
            a.kamarruangan_id,
            a.kamartempattidur_id,
            a.pegawai_id,
            a.penjamin_id,
            a.kelaspelayanan_id
           FROM pasienadmisi_t a) pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN ( SELECT a.pendaftaran_id,
            a.pasienadmisi_id,
            a.rujukan_id,
            a.no_pendaftaran,
            a.pasien_id,
            a.umur,
            a.jeniskasuspenyakit_id,
            a.tgl_pendaftaran,
            a.kunjungan,
            a.status_pasien
           FROM pendaftaran_t a) pendaftaran_t ON pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id
     JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_rekam_medik,
            a.jeniskelamin,
            a.tanggal_lahir,
            a.alamat_pasien,
            a.no_telepon_pasien
           FROM pasien_m a) pasien_m ON pasienadmisi_t.pasien_id = pasien_m.pasien_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id,
            a.ruangan_singkatan
           FROM ruangan_m a) ruangan_m ON pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN ( SELECT a.kamarruangan_id,
            a.kamarruangan_nokamar
           FROM kamarruangan_m a) kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
     JOIN ( SELECT a.kamartempattidur_id,
            a.no_tempattidur
           FROM kamartempattidur_m a) kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
     JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai,
            a.nomorindukpegawai
           FROM pegawai_m a) pegawai_m ON pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id
     JOIN ( SELECT a.penjamin_id,
            a.carabayar_id,
            a.penjamin_nama,
            a.penjamin_kode
           FROM penjamin_m a) penjamin_m ON pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id
     JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama,
            a.carabayar_kode_warna,
            a.groupcarabayar_id
           FROM carabayar_m a) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_m ON pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT a.pasienmasukpenunjang_id,
            a.is_bayar,
            a.status_periksa,
            a.pasienkirimkeunitlain_id
           FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT a.rujukan_id,
            a.asalrujukan_id,
            a.rujukandari_id
           FROM rujukan_t a) rujukan_t ON pendaftaran_t.rujukan_id = rujukan_t.rujukan_id
     LEFT JOIN ( SELECT a.asalrujukan_id,
            a.asalrujukan_nama
           FROM asalrujukan_m a) asalrujukan_m ON rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id
     LEFT JOIN ( SELECT a.perujuk_id,
            a.namaperujuk
           FROM perujuk_m a) perujuk_m ON rujukan_t.rujukandari_id = perujuk_m.perujuk_id
     LEFT JOIN ( SELECT x.pasienmasukpenunjang_id,
            x.status_bayar
           FROM ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
                        CASE
                            WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NOT NULL THEN \'Sudah Bayar\'::text
                            WHEN tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.is_deleted = false THEN \'Belum Bayar\'::text
                            ELSE \'Batal Bayar\'::text
                        END AS status_bayar
                   FROM tindakanpelayanan_t
                  WHERE tindakanpelayanan_t.is_deleted = false
                  GROUP BY tindakanpelayanan_t.tindakansudahbayar_id, tindakanpelayanan_t.is_deleted, tindakanpelayanan_t.pasienmasukpenunjang_id) x
          GROUP BY x.pasienmasukpenunjang_id, x.status_bayar) tindakanpelayanan ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id
     LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
                CASE
                    WHEN pasienmorbiditas_t.kelompokdiagnosa_id = 2 THEN pasienmorbiditas_t.diagnosa_pasien
                    ELSE NULL::json
                END AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN ( SELECT a.pasien_id
                   FROM pasien_m a) pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN ( SELECT a.pasienmorbiditas_id,
                    a.pendaftaran_id,
                    a.is_deleted,
                    a.kelompokdiagnosa_id,
                    a.diagnosa_pasien
                   FROM pasienmorbiditas_t a) pasienmorbiditas_t ON pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id AND pasienmorbiditas_t.is_deleted = false
          WHERE pasienmorbiditas_t.kelompokdiagnosa_id = 2 AND pasienmorbiditas_t.diagnosa_pasien IS NOT NULL
        UNION ALL
         SELECT pendaftaran_t_1.pendaftaran_id,
            cppt_t.a_diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN ( SELECT a.pasien_id
                   FROM pasien_m a) pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN ( SELECT cppt_t_1.cppt_id,
                    cppt_t_1.pendaftaran_id,
                    cppt_t_1.a_diag_utama,
                    cppt_t_1.a_diag_penyerta
                   FROM cppt_t cppt_t_1
                     JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
                            cppt_last.pendaftaran_id
                           FROM cppt_t cppt_last
                          WHERE cppt_last.is_deleted = false
                          GROUP BY cppt_last.pendaftaran_id) cppt_max ON cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id AND cppt_t_1.cppt_id = cppt_max.cppt_id) cppt_t ON pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id
          WHERE cppt_t.a_diag_utama IS NOT NULL
        UNION ALL
         SELECT pendaftaran_t_1.pendaftaran_id,
            resumemedisri_t.diag_utama AS diagnosa_utama
           FROM pendaftaran_t pendaftaran_t_1
             JOIN ( SELECT a.pasien_id
                   FROM pasien_m a) pasien_m_1 ON pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id
             JOIN ( SELECT a.pasienadmisi_id
                   FROM pasienadmisi_t a) pasienadmisi_t_1 ON pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id
             JOIN resumemedisri_t ON pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id AND pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id
          WHERE resumemedisri_t.diag_utama IS NOT NULL) diagnosa ON pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id
     LEFT JOIN ( SELECT permintaankepenunjang_t_1.pasienkirimkeunitlain_id,
            string_agg(DISTINCT daftartindakan_m.daftartindakan_nama::text ||
                CASE
                    WHEN permintaankepenunjang_t_1.is_referred IS TRUE THEN \' (Dirujuk)\'::text
                    ELSE \'\'::text
                END, \', \'::text) AS nama_pemeriksaan
           FROM permintaankepenunjang_t permintaankepenunjang_t_1
             JOIN ( SELECT a.daftartindakan_id,
                    a.daftartindakan_nama
                   FROM daftartindakan_m a) daftartindakan_m ON permintaankepenunjang_t_1.daftartindakan_id = daftartindakan_m.daftartindakan_id
          GROUP BY permintaankepenunjang_t_1.pasienkirimkeunitlain_id) pemeriksaan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pemeriksaan.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
            pembayaran_t.no_pembayaran
           FROM pembayaran_t
          WHERE pembayaran_t.is_deleted IS FALSE) pembayaran ON pendaftaran_t.pendaftaran_id = pembayaran.pendaftaran_id
     LEFT JOIN ( SELECT permintaankepenunjang_t_1.pasienkirimkeunitlain_id,
            count(*) AS jml_pemeriksaan
           FROM permintaankepenunjang_t permintaankepenunjang_t_1
          WHERE permintaankepenunjang_t_1.is_deleted IS FALSE
          GROUP BY permintaankepenunjang_t_1.pasienkirimkeunitlain_id) total_pemeriksaan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = total_pemeriksaan.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT DISTINCT ON (permintaankepenunjang_t_1.pasienkirimkeunitlain_id, permintaankepenunjang_t_1.is_cyto) permintaankepenunjang_t_1.pasienkirimkeunitlain_id,
            permintaankepenunjang_t_1.is_cyto
           FROM permintaankepenunjang_t permintaankepenunjang_t_1
          WHERE permintaankepenunjang_t_1.is_cyto = true) cyto_tindakan ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = cyto_tindakan.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT a.pasienkirimkeunitlain_id,
            count(*) AS qty_dirujuk
           FROM permintaankepenunjang_t a
          WHERE a.is_referred IS TRUE
          GROUP BY a.pasienkirimkeunitlain_id) permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name AS jeniskelamin,
            a.lookup_kode AS jeniskelamin_kode
           FROM lookup_m a) look_jeniskelamin ON pasien_m.jeniskelamin::text = look_jeniskelamin.jeniskelamin::text
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name AS status_penunjang
           FROM lookup_m a) look_status_penunjang ON pasienkirimkeunitlain_t.status_penunjang::text = look_status_penunjang.status_penunjang::text
  WHERE pasienkirimkeunitlain_t.instalasi_id = 5;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221208_042926_hotfix_temuan_live_view_infoorderanrad_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221208_042926_hotfix_temuan_live_view_infoorderanrad_v cannot be reverted.\n";

        return false;
    }
    */
}
