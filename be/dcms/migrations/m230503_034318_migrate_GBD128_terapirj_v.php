<?php

use yii\db\Migration;

/**
 * Class m230503_034318_migrate_GBD128_terapirj_v
 */
class m230503_034318_migrate_GBD128_terapirj_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."terapirj_v";');
        $this->execute("CREATE OR REPLACE VIEW public.terapirj_v
        AS SELECT terapi.jenis,
            terapi.grouping_tipe,
            terapi.jenis_urutan,
            terapi.pendaftaran_id,
            terapi.pegawai_id,
            terapi.nama_pegawai,
            terapi.tgl_tindakan,
            terapi.instruksi,
            terapi.daftar_paket,
            terapi.is_hapus,
            terapi.status,
            terapi.cyto_tindakan,
            terapi.qty,
            terapi.no_penunjang,
            terapi.pemberi_instruksi_id,
            terapi.pasien_id,
            terapi.ruangan_penunjang_id,
            terapi.ruangan_penunjang_nama,
            terapi.instalasi_penunjang_id,
            terapi.instalasi_penunjang_nama,
            terapi.kelompoktindakan_id,
            terapi.is_bayar,
            terapi.permintaankepenunjang_id,
            terapi.obatalkespasien_id,
            terapi.tindakanpelayanan_id,
            terapi.pasienkirimkeunitlain_id,
            terapi.alasan_batal,
            terapi.pegawai_hapus_id,
            terapi.pegawai_hapus_nama,
            terapi.tgl_batal,
            terapi.is_cyto,
            terapi.is_concern,
            terapi.instruksitindakan_id,
            terapi.is_deleted,
            terapi.grouping_tipe_key,
            terapi.status_periksa,
            terapi.status_penunjang,
            terapi.status_penunjang_nama,
            terapi.resepturdetail_id,
            terapi.noresep,
            terapi.tgl_resep_dibuat,
            terapi.satuankecil_id,
            terapi.satuankecil_nama,
            terapi.tindakan_nama,
            terapi.no_pendaftaran,
            terapi.tgl_pendaftaran,
            terapi.is_penunjang,
            terapi.status_bmhp_id,
            terapi.status_bmhp_nama,
            terapi.perawat_id,
            terapi.perawat_nama,
            terapi.is_telah_implementasi,
                CASE
                    WHEN terapi.status_periksa::text = '4'::text THEN true
                    WHEN terapi.status_periksa::text = '433'::text THEN true
                    ELSE false
                END AS is_pulang,
            terapi.status_bayar
           FROM ( SELECT 'TINDAKAN'::text AS jenis,
                    'Tindakan & BMHP'::text AS grouping_tipe,
                    2 AS jenis_urutan,
                    pendaftaran_t.pendaftaran_id,
                    tindakanpelayanan_t.dokterpenanggungjawab_id AS pegawai_id,
                    dokter.nama_pegawai,
                    COALESCE(instruksitindakan_t.tgl_tindakan, tindakanpelayanan_t.tgl_tindakan) AS tgl_tindakan,
                    concat(daftartindakan_m.daftartindakan_nama, ' - ',
                        CASE
                            WHEN COALESCE(instruksitindakan_t.is_cyto, tindakanpelayanan_t.cyto_tindakan) = true THEN 'Cyto - '::text
                            ELSE ''::text
                        END,
                        CASE
                            WHEN COALESCE(instruksitindakan_t.is_concern, false) = true THEN 'Informed Consent - '::text
                            ELSE ''::text
                        END, COALESCE(instruksitindakan_t.qty, tindakanpelayanan_t.qty_tindakan)) AS instruksi,
                    array_to_json(NULL::character varying[]) AS daftar_paket,
                    COALESCE(instruksitindakan_t.is_deleted, tindakanpelayanan_t.is_deleted) AS is_hapus,
                    NULL::character varying AS status,
                    COALESCE(instruksitindakan_t.is_cyto, tindakanpelayanan_t.cyto_tindakan) AS cyto_tindakan,
                    COALESCE(instruksitindakan_t.qty, tindakanpelayanan_t.qty_tindakan) AS qty,
                    NULL::character varying AS no_penunjang,
                    NULL::integer AS pemberi_instruksi_id,
                    pendaftaran_t.pasien_id,
                    ruangan_m.ruangan_id AS ruangan_penunjang_id,
                    ruangan_m.ruangan_nama AS ruangan_penunjang_nama,
                    instalasi_m.instalasi_id AS instalasi_penunjang_id,
                    instalasi_m.instalasi_nama AS instalasi_penunjang_nama,
                    daftartindakan_m.kelompoktindakan_id,
                        CASE COALESCE(tindakanpelayanan_t.tindakansudahbayar_id, 0)
                            WHEN 0 THEN false
                            ELSE true
                        END AS is_bayar,
                    NULL::integer AS permintaankepenunjang_id,
                    NULL::integer AS obatalkespasien_id,
                    tindakanpelayanan_t.tindakanpelayanan_id,
                    NULL::integer AS pasienkirimkeunitlain_id,
                    COALESCE(instruksitindakan_t.alasan_batal, tindakanpelayanan_t.alasan_batal) AS alasan_batal,
                    peg_deleted.pegawai_id AS pegawai_hapus_id,
                    peg_deleted.nama_pegawai AS pegawai_hapus_nama,
                    COALESCE(instruksitindakan_t.deleted_date, tindakanpelayanan_t.deleted_date) AS tgl_batal,
                    instruksitindakan_t.is_cyto,
                    instruksitindakan_t.is_concern,
                    instruksitindakan_t.instruksitindakan_id,
                    COALESCE(instruksitindakan_t.is_deleted, tindakanpelayanan_t.is_deleted) AS is_deleted,
                    'tindakanbmhp'::text AS grouping_tipe_key,
                    pendaftaran_t.status_periksa,
                    NULL::text AS status_penunjang,
                    NULL::text AS status_penunjang_nama,
                    NULL::integer AS resepturdetail_id,
                    NULL::text AS noresep,
                    NULL::timestamp(6) without time zone AS tgl_resep_dibuat,
                    NULL::integer AS satuankecil_id,
                    NULL::text AS satuankecil_nama,
                    daftartindakan_m.daftartindakan_nama AS tindakan_nama,
                    pendaftaran_t.no_pendaftaran,
                    pendaftaran_t.tgl_pendaftaran,
                    false AS is_penunjang,
                    NULL::integer AS status_bmhp_id,
                    NULL::text AS status_bmhp_nama,
                    NULL::integer AS perawat_id,
                    NULL::text AS perawat_nama,
                        CASE
                            WHEN tindakanpelayanan_t.tindakanpelayanan_id IS NOT NULL THEN true
                            ELSE false
                        END AS is_telah_implementasi,
                    pendaftaran_t.status_bayar
                   FROM pendaftaran_t
                     LEFT JOIN ( SELECT a.ruangan_id,
                            a.ruangan_nama,
                            a.instalasi_id
                           FROM ruangan_m a) ruangan_m ON COALESCE(pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id
                     LEFT JOIN ( SELECT a.pendaftaran_id,
                            a.tindakanpelayanan_id,
                            a.tgl_tindakan,
                            a.cyto_tindakan,
                            a.is_deleted,
                            a.qty_tindakan,
                            a.tindakansudahbayar_id,
                            a.alasan_batal,
                            a.deleted_date,
                            a.dokterpenanggungjawab_id,
                            a.deleted_by,
                            a.pasienmasukpenunjang_id,
                            a.instruksitindakan_id,
                            a.daftartindakan_id
                           FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.pasienmasukpenunjang_id IS NULL
                     LEFT JOIN ( SELECT a.tgl_tindakan,
                            a.is_cyto,
                            a.is_concern,
                            a.qty,
                            a.alasan_batal,
                            a.deleted_date,
                            a.instruksitindakan_id,
                            a.is_deleted,
                            a.daftartindakan_id
                           FROM instruksitindakan_t a) instruksitindakan_t ON tindakanpelayanan_t.instruksitindakan_id = instruksitindakan_t.instruksitindakan_id
                     LEFT JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai,
                            a.kelompokpegawai_id,
                            a.pendkualifikasi_id,
                            a.spesialis_id
                           FROM pegawai_m a) pegawai_m ON COALESCE(tindakanpelayanan_t.dokterpenanggungjawab_id, pendaftaran_t.pegawai_id::bigint) = pegawai_m.pegawai_id
                     LEFT JOIN ( SELECT a.kelompokpegawai_id,
                            a.kelompokpegawai_nama
                           FROM kelompokpegawai_m a) kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
                     LEFT JOIN ( SELECT a.pendkualifikasi_id,
                            a.pendkualifikasi_nama
                           FROM pendidikankualifikasi_m a) pendidikankualifikasi_m ON pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id
                     LEFT JOIN ( SELECT a.daftartindakan_id,
                            a.daftartindakan_kode,
                            a.daftartindakan_nama,
                            a.kelompoktindakan_id
                           FROM daftartindakan_m a) daftartindakan_m ON COALESCE(instruksitindakan_t.daftartindakan_id, tindakanpelayanan_t.daftartindakan_id) = daftartindakan_m.daftartindakan_id
                     LEFT JOIN ( SELECT a.instalasi_id,
                            a.instalasi_nama
                           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                     LEFT JOIN ( SELECT a.loginpemakai_id,
                            a.pegawai_id
                           FROM loginpemakai_k a) leg_deleted ON tindakanpelayanan_t.deleted_by = leg_deleted.loginpemakai_id
                     LEFT JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai
                           FROM pegawai_m a) peg_deleted ON leg_deleted.pegawai_id = peg_deleted.pegawai_id
                     LEFT JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai
                           FROM pegawai_m a) dokter ON tindakanpelayanan_t.dokterpenanggungjawab_id = dokter.pegawai_id
                UNION ALL
                 SELECT 'PAKET'::text AS jenis,
                    'Tindakan & BMHP'::text AS grouping_tipe,
                    2 AS jenis_urutan,
                    pendaftaran_t.pendaftaran_id,
                    dokter.pegawai_id,
                    dokter.nama_pegawai,
                    COALESCE(instruksitindakan_t.tgl_tindakan, tindakanpelayanan_t.tgl_tindakan) AS tgl_tindakan,
                    concat(tipepaket_m.tipepaket_nama, ' - ',
                        CASE
                            WHEN COALESCE(instruksitindakan_t.is_cyto, tindakanpelayanan_t.cyto_tindakan) = true THEN 'Cyto - '::text
                            ELSE ''::text
                        END,
                        CASE
                            WHEN COALESCE(instruksitindakan_t.is_concern, false) = true THEN 'Informed Consent - '::text
                            ELSE ''::text
                        END, COALESCE(instruksitindakan_t.qty, tindakanpelayanan_t.qty_tindakan)) AS instruksi,
                    array_to_json(ARRAY( SELECT daftartindakan_m.daftartindakan_nama
                           FROM paketpelayanan_mp
                             LEFT JOIN ( SELECT daftartindakan_m_1.daftartindakan_id,
                                    daftartindakan_m_1.daftartindakan_nama
                                   FROM daftartindakan_m daftartindakan_m_1) daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
                          WHERE paketpelayanan_mp.tipepaket_id = tindakanpelayanan_t.tipepaket_id)) AS daftar_paket,
                    COALESCE(instruksitindakan_t.is_deleted, tindakanpelayanan_t.is_deleted) AS is_hapus,
                    NULL::character varying AS status,
                    COALESCE(instruksitindakan_t.is_cyto, tindakanpelayanan_t.cyto_tindakan) AS cyto_tindakan,
                    COALESCE(instruksitindakan_t.qty, tindakanpelayanan_t.qty_tindakan) AS qty,
                    NULL::character varying AS no_penunjang,
                    NULL::integer AS pemberi_instruksi_id,
                    pendaftaran_t.pasien_id,
                    ruangan_m.ruangan_id AS ruangan_penunjang_id,
                    ruangan_m.ruangan_nama AS ruangan_penunjang_nama,
                    instalasi_m.instalasi_id AS instalasi_penunjang_id,
                    instalasi_m.instalasi_nama AS instalasi_penunjang_nama,
                    NULL::integer AS kelompoktindakan_id,
                        CASE COALESCE(tindakanpelayanan_t.tindakansudahbayar_id, 0)
                            WHEN 0 THEN false
                            ELSE true
                        END AS is_bayar,
                    NULL::integer AS permintaankepenunjang_id,
                    NULL::integer AS obatalkespasien_id,
                    tindakanpelayanan_t.tindakanpelayanan_id,
                    NULL::integer AS pasienkirimkeunitlain_id,
                    COALESCE(instruksitindakan_t.alasan_batal, tindakanpelayanan_t.alasan_batal) AS alasan_batal,
                    peg_deleted.pegawai_id AS pegawai_hapus_id,
                    peg_deleted.nama_pegawai AS pegawai_hapus_nama,
                    COALESCE(instruksitindakan_t.deleted_date, tindakanpelayanan_t.deleted_date) AS tgl_batal,
                    instruksitindakan_t.is_cyto,
                    instruksitindakan_t.is_concern,
                    instruksitindakan_t.instruksitindakan_id,
                    instruksitindakan_t.is_deleted,
                    'tindakanbmhp'::text AS grouping_tipe_key,
                    pendaftaran_t.status_periksa,
                    NULL::text AS status_penunjang,
                    NULL::text AS status_penunjang_nama,
                    NULL::integer AS resepturdetail_id,
                    NULL::text AS noresep,
                    NULL::timestamp(6) without time zone AS tgl_resep_dibuat,
                    NULL::integer AS satuankecil_id,
                    NULL::text AS satuankecil_nama,
                    tipepaket_m.tipepaket_nama AS tindakan_nama,
                    pendaftaran_t.no_pendaftaran,
                    pendaftaran_t.tgl_pendaftaran,
                    false AS is_penunjang,
                    NULL::integer AS status_bmhp_id,
                    NULL::text AS status_bmhp_nama,
                    NULL::integer AS perawat_id,
                    NULL::text AS perawat_nama,
                        CASE
                            WHEN tindakanpelayanan_t.tindakanpelayanan_id IS NOT NULL THEN true
                            ELSE false
                        END AS is_telah_implementasi,
                    pendaftaran_t.status_bayar
                   FROM pendaftaran_t
                     LEFT JOIN ( SELECT a.ruangan_id,
                            a.ruangan_nama,
                            a.instalasi_id
                           FROM ruangan_m a) ruangan_m ON COALESCE(pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id
                     LEFT JOIN ( SELECT a.pendaftaran_id,
                            a.tindakanpelayanan_id,
                            a.tgl_tindakan,
                            a.cyto_tindakan,
                            a.is_deleted,
                            a.qty_tindakan,
                            a.tindakansudahbayar_id,
                            a.alasan_batal,
                            a.deleted_date,
                            a.dokterpenanggungjawab_id,
                            a.deleted_by,
                            a.pasienmasukpenunjang_id,
                            a.instruksitindakan_id,
                            a.tipepaket_id
                           FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
                     LEFT JOIN ( SELECT a.tgl_tindakan,
                            a.is_cyto,
                            a.is_concern,
                            a.qty,
                            a.alasan_batal,
                            a.deleted_date,
                            a.instruksitindakan_id,
                            a.is_deleted,
                            a.daftartindakan_id,
                            a.tipepaket_id
                           FROM instruksitindakan_t a) instruksitindakan_t ON tindakanpelayanan_t.instruksitindakan_id = instruksitindakan_t.instruksitindakan_id
                     LEFT JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai,
                            a.kelompokpegawai_id,
                            a.pendkualifikasi_id,
                            a.spesialis_id
                           FROM pegawai_m a) pegawai_m ON COALESCE(tindakanpelayanan_t.dokterpenanggungjawab_id, pendaftaran_t.pegawai_id::bigint) = pegawai_m.pegawai_id
                     LEFT JOIN ( SELECT a.kelompokpegawai_id,
                            a.kelompokpegawai_nama
                           FROM kelompokpegawai_m a) kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
                     LEFT JOIN ( SELECT a.pendkualifikasi_id,
                            a.pendkualifikasi_nama
                           FROM pendidikankualifikasi_m a) pendidikankualifikasi_m ON pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id
                     JOIN ( SELECT a.tipepaket_id,
                            a.tipepaket_nama,
                            a.tipepaket_kode
                           FROM tipepaket_m a) tipepaket_m ON COALESCE(instruksitindakan_t.tipepaket_id, tindakanpelayanan_t.tipepaket_id) = tipepaket_m.tipepaket_id
                     LEFT JOIN ( SELECT a.instalasi_id,
                            a.instalasi_nama
                           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                     LEFT JOIN ( SELECT a.loginpemakai_id,
                            a.pegawai_id
                           FROM loginpemakai_k a) leg_deleted ON tindakanpelayanan_t.deleted_by = leg_deleted.loginpemakai_id
                     LEFT JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai
                           FROM pegawai_m a) peg_deleted ON leg_deleted.pegawai_id = peg_deleted.pegawai_id
                     LEFT JOIN ( SELECT a.spesialis_id,
                            a.spesialis_nama
                           FROM spesialis_m a) spesialis_m ON pegawai_m.spesialis_id = spesialis_m.spesialis_id
                     LEFT JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai
                           FROM pegawai_m a) dokter ON tindakanpelayanan_t.dokterpenanggungjawab_id = dokter.pegawai_id
                  WHERE tindakanpelayanan_t.pasienmasukpenunjang_id IS NULL
                UNION ALL
                 SELECT 'BMHP'::text AS jenis,
                    'Tindakan & BMHP'::text AS grouping_tipe,
                    2 AS jenis_urutan,
                    pendaftaran_t.pendaftaran_id,
                    dokter.pegawai_id,
                    dokter.nama_pegawai,
                    obatalkespasien_t.tglpelayanan AS tgl_tindakan,
                    concat(obatalkes_m.obatalkes_nama, ' - ', COALESCE(obatalkespasien_t.qty_oa, 0::double precision)) AS instruksi,
                    array_to_json(NULL::character varying[]) AS daftar_paket,
                    obatalkespasien_t.is_deleted AS is_hapus,
                    NULL::character varying AS status,
                    NULL::boolean AS cyto_tindakan,
                    obatalkespasien_t.qty_oa AS qty,
                    NULL::character varying AS no_penunjang,
                    NULL::integer AS pemberi_instruksi_id,
                    pendaftaran_t.pasien_id,
                    ruangan_m.ruangan_id AS ruangan_penunjang_id,
                    ruangan_m.ruangan_nama AS ruangan_penunjang_nama,
                    instalasi_m.instalasi_id AS instalasi_penunjang_id,
                    instalasi_m.instalasi_nama AS instalasi_penunjang_nama,
                    NULL::integer AS kelompoktindakan_id,
                        CASE COALESCE(obatalkespasien_t.obatsudahbayar_id, 0)
                            WHEN 0 THEN false
                            ELSE true
                        END AS is_bayar,
                    NULL::integer AS permintaankepenunjang_id,
                    obatalkespasien_t.obatalkespasien_id,
                    NULL::integer AS tindakanpelayanan_id,
                    NULL::integer AS pasienkirimkeunitlain_id,
                    obatalkespasien_t.alasan_batal,
                    peg_deleted.pegawai_id AS pegawai_hapus_id,
                    peg_deleted.nama_pegawai AS pegawai_hapus_nama,
                    obatalkespasien_t.deleted_date AS tgl_batal,
                    false AS is_cyto,
                    false AS is_concern,
                    NULL::integer AS instruksitindakan_id,
                    obatalkespasien_t.is_deleted,
                    'tindakanbmhp'::text AS grouping_tipe_key,
                    pendaftaran_t.status_periksa,
                    NULL::text AS status_penunjang,
                    NULL::text AS status_penunjang_nama,
                    NULL::integer AS resepturdetail_id,
                    NULL::text AS noresep,
                    NULL::timestamp(6) without time zone AS tgl_resep_dibuat,
                    NULL::integer AS satuankecil_id,
                    NULL::text AS satuankecil_nama,
                    obatalkes_m.obatalkes_nama AS tindakan_nama,
                    pendaftaran_t.no_pendaftaran,
                    pendaftaran_t.tgl_pendaftaran,
                    false AS is_penunjang,
                    obatalkespasien_t.status_bmhp AS status_bmhp_id,
                    lkp_status_bmhp.lookup_name AS status_bmhp_nama,
                    perawat.pegawai_id AS perawat_id,
                    perawat.nama_pegawai AS perawat_nama,
                        CASE
                            WHEN obatalkespasien_t.obatalkespasien_id IS NOT NULL THEN true
                            ELSE false
                        END AS is_telah_implementasi,
                    pendaftaran_t.status_bayar
                   FROM pendaftaran_t
                     LEFT JOIN ( SELECT a.ruangan_id,
                            a.ruangan_nama,
                            a.instalasi_id
                           FROM ruangan_m a) ruangan_m ON COALESCE(pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id
                     LEFT JOIN ( SELECT a.pendaftaran_id,
                            a.tglpelayanan,
                            a.qty_oa,
                            a.is_deleted,
                            a.obatsudahbayar_id,
                            a.obatalkespasien_id,
                            a.alasan_batal,
                            a.deleted_date,
                            a.pegawai_id,
                            a.obatalkes_id,
                            a.deleted_by,
                            a.status_bmhp,
                            a.perawat1_id
                           FROM obatalkespasien_t a) obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
                     LEFT JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai,
                            a.kelompokpegawai_id,
                            a.pendkualifikasi_id,
                            a.spesialis_id
                           FROM pegawai_m a) pegawai_m ON COALESCE(obatalkespasien_t.pegawai_id, pendaftaran_t.pegawai_id) = pegawai_m.pegawai_id
                     LEFT JOIN ( SELECT a.kelompokpegawai_id,
                            a.kelompokpegawai_nama
                           FROM kelompokpegawai_m a) kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
                     LEFT JOIN ( SELECT a.pendkualifikasi_id,
                            a.pendkualifikasi_nama
                           FROM pendidikankualifikasi_m a) pendidikankualifikasi_m ON pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id
                     LEFT JOIN ( SELECT obatalkes_m_1.obatalkes_id,
                            obatalkes_m_1.obatalkes_nama
                           FROM obatalkes_m obatalkes_m_1) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
                     LEFT JOIN ( SELECT a.instalasi_id,
                            a.instalasi_nama
                           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                     LEFT JOIN ( SELECT a.loginpemakai_id,
                            a.pegawai_id
                           FROM loginpemakai_k a) leg_deleted ON obatalkespasien_t.deleted_by = leg_deleted.loginpemakai_id
                     LEFT JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai
                           FROM pegawai_m a) peg_deleted ON leg_deleted.pegawai_id = peg_deleted.pegawai_id
                     LEFT JOIN ( SELECT a.spesialis_id,
                            a.spesialis_nama
                           FROM spesialis_m a) spesialis_m ON pegawai_m.spesialis_id = spesialis_m.spesialis_id
                     LEFT JOIN ( SELECT lookup_m.lookup_id,
                            lookup_m.lookup_name
                           FROM lookup_m) lkp_status_bmhp ON obatalkespasien_t.status_bmhp::integer = lkp_status_bmhp.lookup_id
                     LEFT JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai
                           FROM pegawai_m a) perawat ON obatalkespasien_t.perawat1_id = perawat.pegawai_id
                     LEFT JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai
                           FROM pegawai_m a) dokter ON obatalkespasien_t.pegawai_id = dokter.pegawai_id
                  WHERE obatalkespasien_t.status_bmhp IS NOT NULL
                UNION ALL
                 SELECT
                        CASE
                            WHEN racikan_m.racikan_singkatan::text = 'OR'::text THEN 'Racikan'::text
                            ELSE 'Non Racikan'::text
                        END AS jenis,
                    'Reseptur'::text AS grouping_tipe,
                    1 AS jenis_urutan,
                    pendaftaran_t.pendaftaran_id,
                    dokter.pegawai_id,
                    dokter.nama_pegawai,
                    resepturdetail_t.created_date AS tgl_tindakan,
                    concat(obatalkes_m.obatalkes_nama, ' - ', resepturdetail_t.qty_reseptur) AS instruksi,
                    array_to_json(NULL::character varying[]) AS daftar_paket,
                    resepturdetail_t.is_deleted AS is_hapus,
                    reseptur_t.status_reseptur::character varying AS status,
                    NULL::boolean AS cyto_tindakan,
                    resepturdetail_t.qty_reseptur AS qty,
                    NULL::character varying AS no_penunjang,
                    NULL::integer AS pemberi_instruksi_id,
                    pendaftaran_t.pasien_id,
                    ruangan_m.ruangan_id AS ruangan_penunjang_id,
                    ruangan_m.ruangan_nama AS ruangan_penunjang_nama,
                    instalasi_m.instalasi_id AS instalasi_penunjang_id,
                    instalasi_m.instalasi_nama AS instalasi_penunjang_nama,
                    NULL::integer AS kelompoktindakan_id,
                        CASE COALESCE(obatalkespasien_t.obatsudahbayar_id, 0)
                            WHEN 0 THEN false
                            ELSE true
                        END AS is_bayar,
                    NULL::integer AS permintaankepenunjang_id,
                    obatalkespasien_t.obatalkespasien_id,
                    NULL::integer AS tindakanpelayanan_id,
                    NULL::integer AS pasienkirimkeunitlain_id,
                    COALESCE(reseptur_t.alasan_batal, obatalkespasien_t.alasan_batal) AS alasan_batal,
                    peg_deleted.pegawai_id AS pegawai_hapus_id,
                    peg_deleted.nama_pegawai AS pegawai_hapus_nama,
                    COALESCE(reseptur_t.deleted_date, obatalkespasien_t.deleted_date) AS tgl_batal,
                    false AS is_cyto,
                    false AS is_concern,
                    NULL::integer AS instruksitindakan_id,
                        CASE
                            WHEN resepturdetail_t.is_deleted OR obatalkespasien_t.is_deleted = true THEN true
                            ELSE false
                        END AS is_deleted,
                    'reseptur'::text AS grouping_tipe_key,
                    pendaftaran_t.status_periksa,
                    NULL::text AS status_penunjang,
                    NULL::text AS status_penunjang_nama,
                    resepturdetail_t.resepturdetail_id,
                    reseptur_t.noresep,
                    reseptur_t.tglreseptur AS tgl_resep_dibuat,
                    resepturdetail_t.satuankecil_id,
                    satuanunit_m.satuanunit_nama AS satuankecil_nama,
                    obatalkes_m.obatalkes_nama AS tindakan_nama,
                    pendaftaran_t.no_pendaftaran,
                    pendaftaran_t.tgl_pendaftaran,
                    false AS is_penunjang,
                    NULL::integer AS status_bmhp_id,
                    NULL::text AS status_bmhp_nama,
                    NULL::integer AS perawat_id,
                    NULL::text AS perawat_nama,
                        CASE
                            WHEN obatalkespasien_t.obatalkespasien_id IS NOT NULL THEN true
                            ELSE false
                        END AS is_telah_implementasi,
                    COALESCE(penjualanresep_t.status_bayar::integer, pendaftaran_t.status_bayar) AS status_bayar
                   FROM reseptur_t
                     JOIN ( SELECT a.reseptur_id,
                            a.obatalkes_id,
                            a.created_date,
                            a.qty_reseptur,
                            a.is_deleted,
                            a.resepturdetail_id,
                            a.satuankecil_id,
                            a.racikan_id
                           FROM resepturdetail_t a) resepturdetail_t ON reseptur_t.reseptur_id = resepturdetail_t.reseptur_id
                     LEFT JOIN ( SELECT obatalkes_m_1.obatalkes_id,
                            obatalkes_m_1.obatalkes_nama
                           FROM obatalkes_m obatalkes_m_1) obatalkes_m ON resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
                     LEFT JOIN ( SELECT a.racikan_id,
                            a.racikan_nama,
                            a.racikan_singkatan
                           FROM racikan_m a) racikan_m ON resepturdetail_t.racikan_id = racikan_m.racikan_id
                     JOIN ( SELECT a.pendaftaran_id,
                            a.ruangan_id,
                            a.pegawai_id,
                            a.pasien_id,
                            a.status_periksa,
                            a.no_pendaftaran,
                            a.tgl_pendaftaran,
                            a.status_bayar
                           FROM pendaftaran_t a) pendaftaran_t ON pendaftaran_t.pendaftaran_id = reseptur_t.pendaftaran_id
                     LEFT JOIN ( SELECT a.ruangan_id,
                            a.ruangan_nama,
                            a.instalasi_id
                           FROM ruangan_m a) ruangan_m ON COALESCE(pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id
                     LEFT JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai,
                            a.kelompokpegawai_id,
                            a.pendkualifikasi_id,
                            a.spesialis_id
                           FROM pegawai_m a) pegawai_m ON COALESCE(reseptur_t.pegawai_id, pendaftaran_t.pegawai_id) = pegawai_m.pegawai_id
                     LEFT JOIN ( SELECT a.kelompokpegawai_id,
                            a.kelompokpegawai_nama
                           FROM kelompokpegawai_m a) kelompokpegawai_m ON pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
                     LEFT JOIN ( SELECT a.pendkualifikasi_id,
                            a.pendkualifikasi_nama
                           FROM pendidikankualifikasi_m a) pendidikankualifikasi_m ON pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id
                     LEFT JOIN ( SELECT a.instalasi_id,
                            a.instalasi_nama
                           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                     LEFT JOIN ( SELECT a.pendaftaran_id,
                            a.tglpelayanan,
                            a.qty_oa,
                            a.is_deleted,
                            a.obatsudahbayar_id,
                            a.obatalkespasien_id,
                            a.alasan_batal,
                            a.deleted_date,
                            a.pegawai_id,
                            a.obatalkes_id,
                            a.deleted_by,
                            a.status_bmhp,
                            a.resepturdetail_id
                           FROM obatalkespasien_t a) obatalkespasien_t ON resepturdetail_t.resepturdetail_id = obatalkespasien_t.resepturdetail_id
                     LEFT JOIN ( SELECT a.reseptur_id,
                            a.is_deleted,
                            a.deleted_by,
                            a.deleted_date,
                            a.status_bayar
                           FROM penjualanresep_t a) penjualanresep_t ON reseptur_t.reseptur_id = penjualanresep_t.reseptur_id
                     LEFT JOIN ( SELECT a.loginpemakai_id,
                            a.pegawai_id
                           FROM loginpemakai_k a) leg_deleted ON COALESCE(reseptur_t.deleted_by, obatalkespasien_t.deleted_by) = leg_deleted.loginpemakai_id
                     LEFT JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai
                           FROM pegawai_m a) peg_deleted ON leg_deleted.pegawai_id = peg_deleted.pegawai_id
                     LEFT JOIN ( SELECT a.spesialis_id,
                            a.spesialis_nama
                           FROM spesialis_m a) spesialis_m ON pegawai_m.spesialis_id = spesialis_m.spesialis_id
                     LEFT JOIN ( SELECT a.satuanunit_id,
                            a.satuanunit_nama
                           FROM satuanunit_m a) satuanunit_m ON resepturdetail_t.satuankecil_id = satuanunit_m.satuanunit_id
                     LEFT JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai
                           FROM pegawai_m a) dokter ON reseptur_t.pegawai_id = dokter.pegawai_id
                UNION ALL
                 SELECT
                        CASE
                            WHEN COALESCE(resepturracikan_t.type, 'OR'::character varying)::text = 'OR'::text THEN 'Racikan'::text
                            ELSE 'Non Racikan'::text
                        END AS jenis,
                    'Reseptur'::text AS grouping_tipe,
                    1 AS jenis_urutan,
                    pendaftaran_t.pendaftaran_id,
                    dokter.pegawai_id,
                    dokter.nama_pegawai,
                    resepturracikan_t.created_date AS tgl_tindakan,
                        CASE
                            WHEN resepturracikan_t.type::text = 'NR'::text THEN 'Obat OTHERS'::text
                            ELSE resepturracikan_t.racikan
                        END AS instruksi,
                    array_to_json(NULL::character varying[]) AS daftar_paket,
                    resepturracikan_t.is_deleted AS is_hapus,
                    reseptur_t.status_reseptur::character varying AS status,
                    NULL::boolean AS cyto_tindakan,
                    0 AS qty,
                    NULL::character varying AS no_penunjang,
                    NULL::integer AS pemberi_instruksi_id,
                    pendaftaran_t.pasien_id,
                    ruangan_m.ruangan_id AS ruangan_penunjang_id,
                    ruangan_m.ruangan_nama AS ruangan_penunjang_nama,
                    instalasi_m.instalasi_id AS instalasi_penunjang_id,
                    instalasi_m.instalasi_nama AS instalasi_penunjang_nama,
                    NULL::integer AS kelompoktindakan_id,
                        CASE COALESCE(obatalkespasien_t.obatsudahbayar_id, 0)
                            WHEN 0 THEN false
                            ELSE true
                        END AS is_bayar,
                    NULL::integer AS permintaankepenunjang_id,
                    obatalkespasien_t.obatalkespasien_id,
                    NULL::integer AS tindakanpelayanan_id,
                    NULL::integer AS pasienkirimkeunitlain_id,
                    COALESCE(reseptur_t.alasan_batal, obatalkespasien_t.alasan_batal) AS alasan_batal,
                    peg_deleted.pegawai_id AS pegawai_hapus_id,
                    peg_deleted.nama_pegawai AS pegawai_hapus_nama,
                    COALESCE(reseptur_t.deleted_date, obatalkespasien_t.deleted_date) AS tgl_batal,
                    false AS is_cyto,
                    false AS is_concern,
                    0 AS instruksitindakan_id,
                    resepturracikan_t.is_deleted,
                    'reseptur'::text AS grouping_tipe_key,
                    pendaftaran_t.status_periksa,
                    NULL::text AS status_penunjang,
                    NULL::text AS status_penunjang_nama,
                    NULL::integer AS resepturdetail_id,
                    reseptur_t.noresep,
                    NULL::timestamp(6) without time zone AS tgl_resep_dibuat,
                    NULL::integer AS satuankecil_id,
                    NULL::text AS satuankecil_nama,
                    resepturracikan_t.racikan AS tindakan_nama,
                    pendaftaran_t.no_pendaftaran,
                    pendaftaran_t.tgl_pendaftaran,
                    false AS is_penunjang,
                    NULL::integer AS status_bmhp_id,
                    NULL::text AS status_bmhp_nama,
                    NULL::integer AS perawat_id,
                    NULL::text AS perawat_nama,
                        CASE
                            WHEN obatalkespasien_t.obatalkespasien_id IS NOT NULL THEN true
                            ELSE false
                        END AS is_telah_implementasi,
                    COALESCE(penjualanresep_t.status_bayar::integer, pendaftaran_t.status_bayar) AS status_bayar
                   FROM reseptur_t
                     JOIN ( SELECT a.reseptur_id,
                            a.created_date,
                            a.is_deleted,
                            a.type,
                            a.racikan
                           FROM resepturracikan_t a) resepturracikan_t ON reseptur_t.reseptur_id = resepturracikan_t.reseptur_id
                     JOIN ( SELECT a.pendaftaran_id,
                            a.ruangan_id,
                            a.pegawai_id,
                            a.pasien_id,
                            a.status_periksa,
                            a.no_pendaftaran,
                            a.tgl_pendaftaran,
                            a.status_bayar
                           FROM pendaftaran_t a) pendaftaran_t ON pendaftaran_t.pendaftaran_id = reseptur_t.pendaftaran_id
                     LEFT JOIN ( SELECT a.ruangan_id,
                            a.ruangan_nama,
                            a.instalasi_id
                           FROM ruangan_m a) ruangan_m ON COALESCE(pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id
                     LEFT JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai,
                            a.kelompokpegawai_id,
                            a.pendkualifikasi_id,
                            a.spesialis_id
                           FROM pegawai_m a) pegawai_m ON COALESCE(reseptur_t.pegawai_id, pendaftaran_t.pegawai_id) = pegawai_m.pegawai_id
                     LEFT JOIN ( SELECT a.instalasi_id,
                            a.instalasi_nama
                           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                     LEFT JOIN ( SELECT a.reseptur_id,
                            a.is_deleted,
                            a.deleted_by,
                            a.deleted_date,
                            a.penjualanresep_id,
                            a.status_bayar
                           FROM penjualanresep_t a) penjualanresep_t ON reseptur_t.reseptur_id = penjualanresep_t.reseptur_id
                     LEFT JOIN ( SELECT a.pendaftaran_id,
                            a.tglpelayanan,
                            a.qty_oa,
                            a.is_deleted,
                            a.obatsudahbayar_id,
                            a.obatalkespasien_id,
                            a.alasan_batal,
                            a.deleted_date,
                            a.pegawai_id,
                            a.obatalkes_id,
                            a.deleted_by,
                            a.status_bmhp,
                            a.penjualanresep_id
                           FROM obatalkespasien_t a) obatalkespasien_t ON penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id
                     LEFT JOIN ( SELECT obatalkes_m_1.obatalkes_id,
                            obatalkes_m_1.obatalkes_nama
                           FROM obatalkes_m obatalkes_m_1) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
                     LEFT JOIN ( SELECT a.loginpemakai_id,
                            a.pegawai_id
                           FROM loginpemakai_k a) leg_deleted ON COALESCE(reseptur_t.deleted_by, obatalkespasien_t.deleted_by) = leg_deleted.loginpemakai_id
                     LEFT JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai
                           FROM pegawai_m a) peg_deleted ON leg_deleted.pegawai_id = peg_deleted.pegawai_id
                     LEFT JOIN ( SELECT a.spesialis_id,
                            a.spesialis_nama
                           FROM spesialis_m a) spesialis_m ON pegawai_m.spesialis_id = spesialis_m.spesialis_id
                     LEFT JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai
                           FROM pegawai_m a) dokter ON reseptur_t.pegawai_id = dokter.pegawai_id
                UNION ALL
                 SELECT 'TINDAKAN'::text AS jenis,
                    'Penunjang'::text AS grouping_tipe,
                    3 AS jenis_urutan,
                    pendaftaran_t.pendaftaran_id,
                    dokter.pegawai_id,
                    dokter.nama_pegawai,
                    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_tindakan,
                    concat(daftartindakan_m.daftartindakan_nama, ' - ', permintaankepenunjang_t.qtypermintaan) AS instruksi,
                    array_to_json(NULL::character varying[]) AS daftar_paket,
                        CASE
                            WHEN batal_order.peg_menyetujui_id IS NULL THEN permintaankepenunjang_t.is_deleted
                            ELSE true
                        END AS is_hapus,
                        CASE
                            WHEN pasienmasukpenunjang_t.status_periksa IS NULL THEN pasienkirimkeunitlain_t.status_penunjang
                            ELSE pasienmasukpenunjang_t.status_periksa
                        END AS status,
                    NULL::boolean AS cyto_tindakan,
                    permintaankepenunjang_t.qtypermintaan AS qty,
                    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_penunjang,
                    NULL::integer AS pemberi_intruksi_id,
                    pendaftaran_t.pasien_id,
                    r_penunjang.ruangan_id AS ruangan_penunjang_id,
                    r_penunjang.ruangan_nama AS ruangan_penunjang_nama,
                    i_penunjang.instalasi_id AS instalasi_penunjang_id,
                    i_penunjang.instalasi_nama AS instalasi_penunjang_nama,
                    daftartindakan_m.kelompoktindakan_id,
                        CASE COALESCE(tindakanpelayanan_t.tindakansudahbayar_id, 0)
                            WHEN 0 THEN false
                            ELSE true
                        END AS is_bayar,
                    permintaankepenunjang_t.permintaankepenunjang_id,
                    NULL::integer AS obatalkespasien_id,
                    tindakanpelayanan_t.tindakanpelayanan_id,
                    pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
                    COALESCE(batal_order.alasan_batal, tindakanpelayanan_t.alasan_batal, permintaankepenunjang_t.alasan_batal) AS alasan_batal,
                    peg_deleted.pegawai_id AS pegawai_hapus_id,
                    peg_deleted.nama_pegawai AS pegawai_hapus_nama,
                    COALESCE(batal_order.tgl_batal, tindakanpelayanan_t.deleted_date, permintaankepenunjang_t.deleted_date) AS tgl_batal,
                    false AS is_cyto,
                    false AS is_concern,
                    0 AS instruksitindakan_id,
                        CASE
                            WHEN batal_order.peg_menyetujui_id IS NULL THEN permintaankepenunjang_t.is_deleted
                            ELSE true
                        END AS is_deleted,
                    'penunjang'::text AS grouping_tipe_key,
                    pendaftaran_t.status_periksa,
                    pasienkirimkeunitlain_t.status_penunjang,
                    lookup_m.lookup_name AS status_penunjang_nama,
                    NULL::integer AS resepturdetail_id,
                    NULL::text AS noresep,
                    NULL::timestamp(6) without time zone AS tgl_resep_dibuat,
                    NULL::integer AS satuankecil_id,
                    NULL::text AS satuankecil_nama,
                    daftartindakan_m.daftartindakan_nama AS tindakan_nama,
                    pendaftaran_t.no_pendaftaran,
                    pendaftaran_t.tgl_pendaftaran,
                    true AS is_penunjang,
                    NULL::integer AS status_bmhp_id,
                    NULL::text AS status_bmhp_nama,
                    NULL::integer AS perawat_id,
                    NULL::text AS perawat_nama,
                        CASE
                            WHEN tindakanpelayanan_t.tindakanpelayanan_id IS NOT NULL THEN true
                            ELSE false
                        END AS is_telah_implementasi,
                    pendaftaran_t.status_bayar
                   FROM pasienkirimkeunitlain_t
                     JOIN ( SELECT a.qtypermintaan,
                            a.is_deleted,
                            a.permintaankepenunjang_id,
                            a.alasan_batal,
                            a.deleted_date,
                            a.pasienkirimkeunitlain_id,
                            a.daftartindakan_id,
                            a.deleted_by
                           FROM permintaankepenunjang_t a) permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
                     LEFT JOIN ( SELECT pasienmasukpenunjang_t_1.pasienkirimkeunitlain_id,
                            pasienmasukpenunjang_t_1.status_periksa,
                            pasienmasukpenunjang_t_1.pegawai_id,
                            pasienmasukpenunjang_t_1.pasienmasukpenunjang_id
                           FROM pasienmasukpenunjang_t pasienmasukpenunjang_t_1) pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id
                     JOIN ( SELECT a.pendaftaran_id,
                            a.ruangan_id,
                            a.pegawai_id,
                            a.pasien_id,
                            a.status_periksa,
                            a.no_pendaftaran,
                            a.tgl_pendaftaran,
                            a.status_bayar
                           FROM pendaftaran_t a) pendaftaran_t ON pendaftaran_t.pendaftaran_id = pasienkirimkeunitlain_t.pendaftaran_id
                     LEFT JOIN ( SELECT a.ruangan_id,
                            a.ruangan_nama,
                            a.instalasi_id
                           FROM ruangan_m a) ruangan_m ON COALESCE(pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id
                     LEFT JOIN ( SELECT a.daftartindakan_id,
                            a.daftartindakan_kode,
                            a.daftartindakan_nama,
                            a.kelompoktindakan_id
                           FROM daftartindakan_m a) daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                     LEFT JOIN ( SELECT a.ruangan_id,
                            a.ruangan_nama,
                            a.instalasi_id
                           FROM ruangan_m a) r_penunjang ON pasienkirimkeunitlain_t.ruangan_id = r_penunjang.ruangan_id
                     LEFT JOIN ( SELECT a.instalasi_id,
                            a.instalasi_nama
                           FROM instalasi_m a) i_penunjang ON pasienkirimkeunitlain_t.instalasi_id = i_penunjang.instalasi_id
                     LEFT JOIN ( SELECT a.pendaftaran_id,
                            a.tindakanpelayanan_id,
                            a.tgl_tindakan,
                            a.cyto_tindakan,
                            a.is_deleted,
                            a.qty_tindakan,
                            a.tindakansudahbayar_id,
                            a.alasan_batal,
                            a.deleted_date,
                            a.dokterpenanggungjawab_id,
                            a.deleted_by,
                            a.pasienmasukpenunjang_id,
                            a.instruksitindakan_id,
                            a.daftartindakan_id
                           FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id AND permintaankepenunjang_t.daftartindakan_id = tindakanpelayanan_t.daftartindakan_id
                     LEFT JOIN ( SELECT batalorderpenunjang_t.pasienkirimkeunitlain_id,
                            batalorderpenunjang_t.tgl_batalorder AS tgl_batal,
                            batalorderpenunjang_t.alasan AS alasan_batal,
                            batalorderpenunjang_t.created_by,
                            batalorderpenunjang_t.is_active,
                            batalorderpenunjang_t.peg_menyetujui_id
                           FROM batalorderpenunjang_t) batal_order ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = batal_order.pasienkirimkeunitlain_id
                     LEFT JOIN ( SELECT a.loginpemakai_id,
                            a.pegawai_id
                           FROM loginpemakai_k a) leg_deleted ON COALESCE(permintaankepenunjang_t.deleted_by) = leg_deleted.loginpemakai_id
                     LEFT JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai,
                            a.kelompokpegawai_id,
                            a.pendkualifikasi_id,
                            a.spesialis_id
                           FROM pegawai_m a) peg_deleted ON COALESCE(leg_deleted.pegawai_id, batal_order.peg_menyetujui_id) = peg_deleted.pegawai_id
                     LEFT JOIN ( SELECT a.lookup_id,
                            a.lookup_name
                           FROM lookup_m a) lookup_m ON pasienkirimkeunitlain_t.status_penunjang::integer = lookup_m.lookup_id
                     LEFT JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai
                           FROM pegawai_m a) dokter ON pasienkirimkeunitlain_t.pegawai_id = dokter.pegawai_id
                UNION ALL
                 SELECT 'PAKET'::text AS jenis,
                    'Penunjang'::text AS grouping_tipe,
                    3 AS jenis_urutan,
                    pendaftaran_t.pendaftaran_id,
                    dokter.pegawai_id,
                    dokter.nama_pegawai,
                    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_tindakan,
                    concat(tipepaket_m.tipepaket_nama, ' - ', permintaankepenunjang_t.qtypermintaan) AS instruksi,
                    array_to_json(ARRAY( SELECT daftartindakan_m.daftartindakan_nama
                           FROM paketpelayanan_mp
                             LEFT JOIN ( SELECT a.daftartindakan_id,
                                    a.daftartindakan_nama
                                   FROM daftartindakan_m a) daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
                          WHERE paketpelayanan_mp.tipepaket_id = permintaankepenunjang_t.tipepaket_id)) AS daftar_paket,
                    permintaankepenunjang_t.is_deleted AS is_hapus,
                        CASE
                            WHEN pasienmasukpenunjang_t.status_periksa IS NULL THEN pasienkirimkeunitlain_t.status_penunjang
                            ELSE pasienmasukpenunjang_t.status_periksa
                        END AS status,
                    NULL::boolean AS cyto_tindakan,
                    permintaankepenunjang_t.qtypermintaan AS qty,
                    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_penunjang,
                    NULL::integer AS pemberi_instruksi_id,
                    pendaftaran_t.pasien_id,
                    r_penunjang.ruangan_id AS ruangan_penunjang_id,
                    r_penunjang.ruangan_nama AS ruangan_penunjang_nama,
                    i_penunjang.instalasi_id AS instalasi_penunjang_id,
                    i_penunjang.instalasi_nama AS instalasi_penunjang_nama,
                    NULL::integer AS kelompoktindakan_id,
                        CASE COALESCE(tindakanpelayanan_t.tindakansudahbayar_id, 0)
                            WHEN 0 THEN false
                            ELSE true
                        END AS is_bayar,
                    permintaankepenunjang_t.permintaankepenunjang_id,
                    NULL::integer AS obatalkespasien_id,
                    tindakanpelayanan_t.tindakanpelayanan_id,
                    pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
                    COALESCE(batal_order.alasan_batal, tindakanpelayanan_t.alasan_batal, permintaankepenunjang_t.alasan_batal) AS alasan_batal,
                    peg_deleted.pegawai_id AS pegawai_hapus_id,
                    peg_deleted.nama_pegawai AS pegawai_hapus_nama,
                    COALESCE(batal_order.tgl_batal, tindakanpelayanan_t.deleted_date, permintaankepenunjang_t.deleted_date) AS tgl_batal,
                    false AS is_cyto,
                    false AS is_concern,
                    0 AS instruksitindakan_id,
                    permintaankepenunjang_t.is_deleted,
                    'penunjang'::text AS grouping_tipe_key,
                    pendaftaran_t.status_periksa,
                    pasienkirimkeunitlain_t.status_penunjang,
                    lookup_m.lookup_name AS status_penunjang_nama,
                    NULL::integer AS resepturdetail_id,
                    NULL::text AS noresep,
                    NULL::timestamp(6) without time zone AS tgl_resep_dibuat,
                    NULL::integer AS satuankecil_id,
                    NULL::text AS satuankecil_nama,
                    tipepaket_m.tipepaket_nama AS tindakan_nama,
                    pendaftaran_t.no_pendaftaran,
                    pendaftaran_t.tgl_pendaftaran,
                    true AS is_penunjang,
                    NULL::integer AS status_bmhp_id,
                    NULL::text AS status_bmhp_nama,
                    NULL::integer AS perawat_id,
                    NULL::text AS perawat_nama,
                        CASE
                            WHEN tindakanpelayanan_t.tindakanpelayanan_id IS NOT NULL THEN true
                            ELSE false
                        END AS is_telah_implementasi,
                    pendaftaran_t.status_bayar
                   FROM pasienkirimkeunitlain_t
                     JOIN ( SELECT a.qtypermintaan,
                            a.is_deleted,
                            a.permintaankepenunjang_id,
                            a.alasan_batal,
                            a.deleted_date,
                            a.pasienkirimkeunitlain_id,
                            a.tipepaket_id,
                            a.deleted_by
                           FROM permintaankepenunjang_t a) permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
                     LEFT JOIN ( SELECT pasienmasukpenunjang_t_1.pasienkirimkeunitlain_id,
                            pasienmasukpenunjang_t_1.status_periksa,
                            pasienmasukpenunjang_t_1.pegawai_id,
                            pasienmasukpenunjang_t_1.pasienmasukpenunjang_id
                           FROM pasienmasukpenunjang_t pasienmasukpenunjang_t_1) pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id
                     JOIN ( SELECT a.pendaftaran_id,
                            a.ruangan_id,
                            a.pegawai_id,
                            a.pasien_id,
                            a.status_periksa,
                            a.no_pendaftaran,
                            a.tgl_pendaftaran,
                            a.status_bayar
                           FROM pendaftaran_t a) pendaftaran_t ON pendaftaran_t.pendaftaran_id = pasienkirimkeunitlain_t.pendaftaran_id
                     LEFT JOIN ( SELECT a.ruangan_id,
                            a.ruangan_nama,
                            a.instalasi_id
                           FROM ruangan_m a) ruangan_m ON COALESCE(pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id
                     JOIN ( SELECT a.tipepaket_id,
                            a.tipepaket_nama,
                            a.tipepaket_kode
                           FROM tipepaket_m a) tipepaket_m ON permintaankepenunjang_t.tipepaket_id = tipepaket_m.tipepaket_id
                     LEFT JOIN ( SELECT a.ruangan_id,
                            a.ruangan_nama,
                            a.instalasi_id
                           FROM ruangan_m a) r_penunjang ON pasienkirimkeunitlain_t.ruangan_id = r_penunjang.ruangan_id
                     LEFT JOIN ( SELECT a.instalasi_id,
                            a.instalasi_nama
                           FROM instalasi_m a) i_penunjang ON pasienkirimkeunitlain_t.instalasi_id = i_penunjang.instalasi_id
                     LEFT JOIN ( SELECT a.pendaftaran_id,
                            a.tindakanpelayanan_id,
                            a.tgl_tindakan,
                            a.cyto_tindakan,
                            a.is_deleted,
                            a.qty_tindakan,
                            a.tindakansudahbayar_id,
                            a.alasan_batal,
                            a.deleted_date,
                            a.dokterpenanggungjawab_id,
                            a.deleted_by,
                            a.pasienmasukpenunjang_id,
                            a.instruksitindakan_id,
                            a.tipepaket_id
                           FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id AND tindakanpelayanan_t.tipepaket_id = permintaankepenunjang_t.tipepaket_id
                     LEFT JOIN ( SELECT batalorderpenunjang_t.pasienkirimkeunitlain_id,
                            batalorderpenunjang_t.tgl_batalorder AS tgl_batal,
                            batalorderpenunjang_t.alasan AS alasan_batal,
                            batalorderpenunjang_t.created_by,
                            batalorderpenunjang_t.is_active,
                            batalorderpenunjang_t.peg_menyetujui_id
                           FROM batalorderpenunjang_t) batal_order ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = batal_order.pasienkirimkeunitlain_id
                     LEFT JOIN ( SELECT a.loginpemakai_id,
                            a.pegawai_id
                           FROM loginpemakai_k a) leg_deleted ON COALESCE(tindakanpelayanan_t.deleted_by, permintaankepenunjang_t.deleted_by) = leg_deleted.loginpemakai_id
                     LEFT JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai,
                            a.kelompokpegawai_id,
                            a.pendkualifikasi_id,
                            a.spesialis_id
                           FROM pegawai_m a) peg_deleted ON COALESCE(batal_order.peg_menyetujui_id, leg_deleted.pegawai_id) = peg_deleted.pegawai_id
                     LEFT JOIN ( SELECT a.lookup_id,
                            a.lookup_name
                           FROM lookup_m a) lookup_m ON pasienkirimkeunitlain_t.status_penunjang::integer = lookup_m.lookup_id
                     LEFT JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai
                           FROM pegawai_m a) dokter ON pasienkirimkeunitlain_t.pegawai_id = dokter.pegawai_id
                UNION ALL
                 SELECT 'Penunjang Rehab Medik'::text AS jenis,
                    'REHAB MEDIK'::text AS grouping_tipe,
                    12 AS jenis_urutan,
                    programterapi_t.pendaftaran_id,
                    programterapi_t.dokterperujuk_id AS pegawai_id,
                    pegawai_m.nama_pegawai,
                    programterapi_t.tgl_permintaan AS tgl_tindakan,
                    daftartindakan_m.daftartindakan_nama AS instruksi,
                    NULL::json AS daftar_paket,
                    programterapi_t.is_deleted AS is_hapus,
                    NULL::character varying AS status,
                    NULL::boolean AS cyto_tindakan,
                        CASE
                            WHEN programterapidetail_t.qty_pemeriksaan IS NULL THEN 1
                            ELSE programterapidetail_t.qty_pemeriksaan
                        END AS qty,
                    NULL::character varying AS no_penunjang,
                    NULL::integer AS pemberi_instruksi_id,
                    programterapi_t.pasien_id,
                    NULL::integer AS ruangan_penunjang_id,
                    NULL::character varying AS ruangan_penunjang_nama,
                    NULL::integer AS instalasi_penunjang_id,
                    NULL::character varying AS instalasi_penunjang_nama,
                    daftartindakan_m.kelompoktindakan_id,
                    false AS is_bayar,
                    NULL::integer AS permintaankepenunjang_id,
                    NULL::integer AS obatalkespasien_id,
                    NULL::integer AS tindakanpelayanan_id,
                    programterapi_t.pasienkirimkeunitlain_id,
                    NULL::text AS alasan_batal,
                    NULL::integer AS pegawai_hapus_id,
                    NULL::character varying AS pegawai_hapus_nama,
                    NULL::timestamp without time zone AS tgl_batal,
                    NULL::boolean AS is_cyto,
                    NULL::boolean AS is_concern,
                    NULL::integer AS instruksitindakan_id,
                    programterapi_t.is_deleted,
                    'penunjang_rehab_medik'::text AS grouping_tipe_key,
                    pendaftaran_t.status_periksa,
                    NULL::text AS status_penunjang,
                    NULL::text AS status_penunjang_nama,
                    NULL::integer AS resepturdetail_id,
                    NULL::text AS noresep,
                    NULL::timestamp without time zone AS tgl_resep_dibuat,
                    NULL::integer AS satuankecil_id,
                    NULL::text AS satuankecil_nama,
                    daftartindakan_m.daftartindakan_nama AS tindakan_nama,
                    pendaftaran_t.no_pendaftaran,
                    pendaftaran_t.tgl_pendaftaran,
                    true AS is_penunjang,
                    NULL::integer AS status_bmhp_id,
                    NULL::text AS status_bmhp_nama,
                    NULL::integer AS perawat_id,
                    NULL::text AS perawat_nama,
                    false AS is_telah_implementasi,
                    pendaftaran_t.status_bayar
                   FROM programterapi_t
                     LEFT JOIN ( SELECT a.pendaftaran_id,
                            a.status_periksa,
                            a.no_pendaftaran,
                            a.tgl_pendaftaran,
                            a.status_bayar
                           FROM pendaftaran_t a) pendaftaran_t ON programterapi_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     LEFT JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai,
                            a.spesialis_id,
                            a.kelompokpegawai_id
                           FROM pegawai_m a) pegawai_m ON programterapi_t.dokterperujuk_id = pegawai_m.pegawai_id
                     LEFT JOIN ( SELECT a.programterapi_id,
                            a.is_deleted,
                            a.daftartindakan_id,
                            a.qty_pemeriksaan
                           FROM programterapidetail_t a) programterapidetail_t ON programterapi_t.programterapi_id = programterapidetail_t.programterapi_id AND programterapidetail_t.is_deleted = false
                     LEFT JOIN ( SELECT a.daftartindakan_id,
                            a.is_deleted,
                            a.daftartindakan_nama,
                            a.kelompoktindakan_id
                           FROM daftartindakan_m a) daftartindakan_m ON programterapidetail_t.daftartindakan_id = daftartindakan_m.daftartindakan_id AND daftartindakan_m.is_deleted = false
                  WHERE programterapi_t.is_deleted = false
                UNION ALL
                 SELECT 'DIET'::text AS jenis,
                    'diet'::text AS grouping_tipe,
                    10 AS jenis_urutan,
                    permintaanmakan_t.pendaftaran_id,
                    dokter.pegawai_id,
                    dokter.nama_pegawai,
                    permintaanmakan_t.tgl_permintaanmakan AS tgl_tindakan,
                    concat(daftartindakan_m.daftartindakan_nama, ' - ', permintaanmakandetail_t.jumlah) AS instruksi,
                    NULL::json AS daftar_paket,
                    permintaanmakan_t.is_deleted AS is_hapus,
                        CASE
                            WHEN permintaanmakandetail_t.permintaanmakandetail_id IS NOT NULL THEN '1'::text
                            ELSE '0'::text
                        END AS status,
                    NULL::boolean AS cyto_tindakan,
                    permintaanmakandetail_t.jumlah AS qty,
                    NULL::character varying AS no_penunjang,
                    NULL::integer AS pemberi_instruksi_id,
                    pendaftaran_t.pasien_id,
                    NULL::integer AS ruangan_penunjang_id,
                    NULL::character varying AS ruangan_penunjang_nama,
                    NULL::integer AS instalasi_penunjang_id,
                    NULL::character varying AS instalasi_penunjang_nama,
                    daftartindakan_m.kelompoktindakan_id,
                        CASE pendaftaran_t.status_bayar
                            WHEN 348 THEN true
                            ELSE false
                        END AS is_bayar,
                    NULL::integer AS permintaankepenunjang_id,
                    NULL::integer AS obatalkespasien_id,
                    NULL::integer AS tindakanpelayanan_id,
                    NULL::integer AS pasienkirimkeunitlain_id,
                    NULL::text AS alasan_batal,
                    NULL::integer AS pegawai_hapus_id,
                    NULL::character varying AS pegawai_hapus_nama,
                    NULL::timestamp(6) without time zone AS tgl_batal,
                    false AS is_cyto,
                    false AS is_concern,
                    permintaanmakandetail_t.permintaanmakandetail_id AS instruksitindakan_id,
                    permintaanmakandetail_t.is_deleted,
                    'diet'::text AS grouping_tipe_key,
                    pendaftaran_t.status_periksa,
                    NULL::text AS status_penunjang,
                    NULL::text AS status_penunjang_nama,
                    NULL::integer AS resepturdetail_id,
                    NULL::text AS noresep,
                    NULL::timestamp(6) without time zone AS tgl_resep_dibuat,
                    NULL::integer AS satuankecil_id,
                    NULL::text AS satuankecil_nama,
                    daftartindakan_m.daftartindakan_nama AS tindakan_nama,
                    pendaftaran_t.no_pendaftaran,
                    pendaftaran_t.tgl_pendaftaran,
                    false AS is_penunjang,
                    NULL::integer AS status_bmhp_id,
                    NULL::text AS status_bmhp_nama,
                    NULL::integer AS perawat_id,
                    NULL::text AS perawat_nama,
                        CASE
                            WHEN permintaanmakandetail_t.permintaanmakandetail_id IS NOT NULL THEN true
                            ELSE false
                        END AS is_telah_implementasi,
                    pendaftaran_t.status_bayar
                   FROM permintaanmakan_t
                     LEFT JOIN ( SELECT m.permintaanmakan_id,
                            m.makanandiet_id,
                            m.permintaanmakandetail_id,
                            m.jumlah,
                            m.is_deleted
                           FROM permintaanmakandetail_t m) permintaanmakandetail_t ON permintaanmakan_t.permintaaanmakan_id = permintaanmakandetail_t.permintaanmakan_id
                     LEFT JOIN ( SELECT m.makanandiet_id,
                            m.daftartindakan_id
                           FROM makanandiet_m m) makanandiet_m ON permintaanmakandetail_t.makanandiet_id = makanandiet_m.makanandiet_id
                     LEFT JOIN ( SELECT m.daftartindakan_id,
                            m.daftartindakan_nama,
                            m.kelompoktindakan_id
                           FROM daftartindakan_m m) daftartindakan_m ON makanandiet_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
                     JOIN ( SELECT m.pendaftaran_id,
                            m.pegawai_id,
                            m.pasienadmisi_id,
                            m.is_stopakomodasi,
                            m.tgl_stopakomodasi,
                            m.status_periksa,
                            m.ruangan_id,
                            m.status_bayar,
                            m.pasien_id,
                            m.no_pendaftaran,
                            m.tgl_pendaftaran,
                            m.instalasi_id
                           FROM pendaftaran_t m) pendaftaran_t ON permintaanmakan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
                     JOIN ( SELECT m.pegawai_id,
                            m.nama_pegawai,
                            m.kelompokpegawai_id,
                            m.spesialis_id
                           FROM pegawai_m m) dokter ON permintaanmakan_t.peg_pemesan_id = dokter.pegawai_id
                     LEFT JOIN ( SELECT a.kelompokpegawai_id,
                            a.kelompokpegawai_nama
                           FROM kelompokpegawai_m a) kelompokpegawai_m ON dokter.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id
                     LEFT JOIN ( SELECT a.spesialis_id,
                            a.spesialis_nama
                           FROM spesialis_m a) spesialis_m ON dokter.spesialis_id = spesialis_m.spesialis_id
                     LEFT JOIN ( SELECT m.ruangan_id,
                            m.ruangan_nama,
                            m.instalasi_id
                           FROM ruangan_m m) ruangan_m ON pendaftaran_t.ruangan_id = ruangan_m.ruangan_id
                     LEFT JOIN ( SELECT m.instalasi_id,
                            m.instalasi_nama
                           FROM instalasi_m m) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                  WHERE (pendaftaran_t.instalasi_id = ANY (ARRAY[1, 2])) AND pendaftaran_t.pasienadmisi_id IS NULL) terapi
          ORDER BY terapi.pendaftaran_id, terapi.tgl_tindakan, terapi.jenis DESC;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230503_034318_migrate_GBD128_terapirj_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230503_034318_migrate_GBD128_terapirj_v cannot be reverted.\n";

        return false;
    }
    */
}
