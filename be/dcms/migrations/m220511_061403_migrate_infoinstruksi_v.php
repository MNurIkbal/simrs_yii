<?php

use yii\db\Migration;

/**
 * Class m220511_061403_migrate_infoinstruksi_v
 */
class m220511_061403_migrate_infoinstruksi_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('DROP VIEW if exists "public"."infoinstruksi_v";');

         $this->execute("
            CREATE VIEW \"public\".\"infoinstruksi_v\" AS  SELECT 'TINDAKAN'::text AS tipe_instruksi,
    instruksi_t.instruksi_id,
    instruksi_t.cppt_id,
    instruksi_t.catatan_instruksi,
    instruksitindakan_t.instruksitindakan_id,
    instruksitindakan_t.tgl_tindakan AS tgl_instruksi,
    instruksitindakan_t.daftartindakan_id,
    concat(daftartindakan_m.daftartindakan_nama, ' - ',
        CASE
            WHEN instruksitindakan_t.is_cyto = true THEN 'Cyto - '::text
            ELSE NULL::text
        END,
        CASE
            WHEN instruksitindakan_t.is_concern = true THEN 'Informed Consent - '::text
            ELSE NULL::text
        END, instruksitindakan_t.qty) AS instruksi,
    NULL::character varying AS paket,
    NULL::text AS tindakan,
    instruksitindakan_t.qty,
    instruksitindakan_t.is_cyto,
    instruksitindakan_t.qty_sisa,
    instruksitindakan_t.dokterdpjp_id AS dokter_id,
    dokter.nama_pegawai AS dokter,
    instruksitindakan_t.status_implementasi,
    status.lookup_name AS status,
    instruksi_t.is_deleted AS instruksi_deleted,
    instruksitindakan_t.is_deleted AS tindakan_deleted,
    daftartindakan_m.daftartindakan_nama AS tindakaninstruksi_nama,
        CASE
            WHEN instruksitindakan_t.is_cyto = true THEN 'Cyto'::text
            ELSE 'Non Cyto'::text
        END AS ket_cyto,
    NULL::character varying AS ket_racik_nama,
    NULL::character varying AS ket_racik,
    cppt_t.pegawai_id AS cpptpegawai_id,
    cppt_t.is_verifikasi AS is_verifikasi_dpjp,
    array_to_json(NULL::character varying[]) AS daftar_paket,
    instruksi_t.tgl_instruksi AS tanggal_terapi,
    'TINDAKANBMHP'::text AS grouping_tipe,
        CASE
            WHEN instruksitindakan_t.status_implementasi::text = '455'::character varying::text THEN true
            ELSE false
        END AS is_telah_implementasi,
    ruangan_m.ruangan_nama AS ruangan_pertindakan,
    NULL::json AS bmhp_tindakandetail,
    instruksi_t.created_date AS tanggal_input,
    cppt_t.pendaftaran_id,
    cppt_t.pasienadmisi_id,
    daftartindakan_m.kelompoktindakan_id,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
        CASE pendaftaran_t.status_bayar
            WHEN 348 THEN true
            ELSE false
        END AS is_bayar,
    NULL::integer AS pasienkirimkeunitlain_id,
    COALESCE(instruksitindakan_t.alasan_batal, tindakan_deleted.alasan_batal) AS alasan_batal,
    COALESCE(pegawai_hapus.pegawai_id, tindakan_deleted.pegawai_hapus_id) AS pegawai_hapus_id,
    COALESCE(pegawai_hapus.nama_pegawai, tindakan_deleted.pegawai_hapus_nama) AS pegawai_hapus_nama,
    COALESCE(instruksitindakan_t.deleted_date, tindakan_deleted.tgl_batal) AS tgl_batal,
    COALESCE(tindakan_deleted.is_penatajasa, false) AS is_penatajasa,
        CASE
            WHEN cppt_t.pasienadmisi_id IS NOT NULL THEN
            CASE
                WHEN pendaftaran_t.is_stopakomodasi IS TRUE THEN true
                ELSE false
            END
            ELSE
            CASE
                WHEN pendaftaran_t.status_periksa::text = '4'::text THEN true
                WHEN pendaftaran_t.status_periksa::text = '433'::text THEN true
                ELSE false
            END
        END AS is_pulang,
    NULL::text AS status_penunjang,
    NULL::text AS status_penunjang_nama,
    NULL::text AS noresep,
    NULL::timestamp(6) without time zone AS tgl_resep_dibuat,
    NULL::integer AS satuankecil_id,
    NULL::text AS satuankecil_nama
   FROM instruksi_t
     JOIN ( SELECT a.instruksi_id,
            a.instruksitindakan_id,
            a.pendaftaran_id,
            a.tgl_tindakan,
            a.daftartindakan_id,
            a.is_cyto,
            a.is_concern,
            a.qty,
            a.qty_sisa,
            a.dokterdpjp_id,
            a.status_implementasi,
            a.alasan_batal,
            a.deleted_date,
            a.ruangan_id,
            a.is_deleted,
            a.deleted_by
           FROM instruksitindakan_t a) instruksitindakan_t ON instruksi_t.instruksi_id = instruksitindakan_t.instruksi_id
     JOIN ( SELECT daftartindakan_m_1.kelompoktindakan_id,
            daftartindakan_m_1.daftartindakan_id,
            daftartindakan_m_1.daftartindakan_nama
           FROM daftartindakan_m daftartindakan_m_1) daftartindakan_m ON instruksitindakan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN ( SELECT pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai
           FROM pegawai_m) dokter ON instruksitindakan_t.dokterdpjp_id = dokter.pegawai_id
     LEFT JOIN ( SELECT a.cppt_id,
            a.pegawai_id,
            a.is_verifikasi,
            a.pendaftaran_id,
            a.pasienadmisi_id
           FROM cppt_t a) cppt_t ON cppt_t.cppt_id = instruksi_t.cppt_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) ruangan_m ON instruksitindakan_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT a.status_bayar,
            a.is_stopakomodasi,
            a.status_periksa,
            a.pendaftaran_id
           FROM pendaftaran_t a) pendaftaran_t ON cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.pendaftaran_id,
            tindakanpelayanan_t.instruksitindakan_id,
            tindakanpelayanan_t.daftartindakan_id,
            peg_deleted.pegawai_id AS pegawai_hapus_id,
            peg_deleted.nama_pegawai AS pegawai_hapus_nama,
            tindakanpelayanan_t.deleted_date AS tgl_batal,
            tindakanpelayanan_t.alasan_batal,
            tindakanpelayanan_t.is_penatajasa
           FROM tindakanpelayanan_t
             LEFT JOIN ( SELECT loginpemakai_k.loginpemakai_id,
                    loginpemakai_k.pegawai_id
                   FROM loginpemakai_k) leg_deleted ON tindakanpelayanan_t.deleted_by = leg_deleted.loginpemakai_id
             LEFT JOIN ( SELECT pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai
                   FROM pegawai_m) peg_deleted ON leg_deleted.pegawai_id = peg_deleted.pegawai_id
          WHERE tindakanpelayanan_t.is_deleted IS TRUE) tindakan_deleted ON instruksitindakan_t.pendaftaran_id = tindakan_deleted.pendaftaran_id AND instruksitindakan_t.instruksitindakan_id = tindakan_deleted.instruksitindakan_id AND instruksitindakan_t.daftartindakan_id = tindakan_deleted.daftartindakan_id
     LEFT JOIN ( SELECT loginpemakai_k.loginpemakai_id,
            pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai
           FROM loginpemakai_k
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id) pegawai_hapus ON instruksitindakan_t.deleted_by = pegawai_hapus.loginpemakai_id
     LEFT JOIN ( SELECT lookup_m.lookup_id,
            lookup_m.lookup_name
           FROM lookup_m) status ON instruksitindakan_t.status_implementasi::integer = status.lookup_id
UNION ALL
 SELECT 'PAKET'::text AS tipe_instruksi,
    instruksi_t.instruksi_id,
    instruksi_t.cppt_id,
    instruksi_t.catatan_instruksi,
    instruksitindakan_t.instruksitindakan_id,
    instruksitindakan_t.tgl_tindakan AS tgl_instruksi,
    instruksitindakan_t.tipepaket_id AS daftartindakan_id,
    concat(tipepaket_m.tipepaket_nama, ' - ',
        CASE
            WHEN instruksitindakan_t.is_cyto = true THEN 'Cyto - '::text
            ELSE NULL::text
        END, ' - ',
        CASE
            WHEN instruksitindakan_t.is_concern = true THEN 'Informed Consent - '::text
            ELSE NULL::text
        END, ' - '::text || instruksitindakan_t.qty) AS instruksi,
    tipepaket_m.tipepaket_nama AS paket,
    NULL::text AS tindakan,
    instruksitindakan_t.qty,
    instruksitindakan_t.is_cyto,
    instruksitindakan_t.qty_sisa,
    instruksitindakan_t.dokterdpjp_id AS dokter_id,
    dokter.nama_pegawai AS dokter,
    instruksitindakan_t.status_implementasi,
    status.lookup_name AS status,
    instruksi_t.is_deleted AS instruksi_deleted,
    instruksitindakan_t.is_deleted AS tindakan_deleted,
    tipepaket_m.tipepaket_nama AS tindakaninstruksi_nama,
        CASE
            WHEN instruksitindakan_t.is_cyto = true THEN 'Cyto'::text
            ELSE 'Non Cyto'::text
        END AS ket_cyto,
    NULL::character varying AS ket_racik_nama,
    NULL::character varying AS ket_racik,
    cppt_t.pegawai_id AS cpptpegawai_id,
    cppt_t.is_verifikasi AS is_verifikasi_dpjp,
    array_to_json(ARRAY( SELECT daftartindakan_m.daftartindakan_nama
           FROM paketpelayanan_mp
             LEFT JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
          WHERE paketpelayanan_mp.tipepaket_id = instruksitindakan_t.tipepaket_id)) AS daftar_paket,
    instruksi_t.tgl_instruksi AS tanggal_terapi,
    'TINDAKANBMHP'::text AS grouping_tipe,
        CASE
            WHEN instruksitindakan_t.status_implementasi::text = '455'::character varying::text THEN true
            ELSE false
        END AS is_telah_implementasi,
    ruangan_m.ruangan_nama AS ruangan_pertindakan,
    NULL::json AS bmhp_tindakandetail,
    instruksi_t.created_date AS tanggal_input,
    cppt_t.pendaftaran_id,
    cppt_t.pasienadmisi_id,
    NULL::integer AS kelompoktindakan_id,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
        CASE pendaftaran_t.status_bayar
            WHEN 348 THEN true
            ELSE false
        END AS is_bayar,
    NULL::integer AS pasienkirimkeunitlain_id,
    COALESCE(instruksitindakan_t.alasan_batal, tindakan_deleted.alasan_batal) AS alasan_batal,
    COALESCE(pegawai_hapus.pegawai_id, tindakan_deleted.pegawai_hapus_id) AS pegawai_hapus_id,
    COALESCE(pegawai_hapus.nama_pegawai, tindakan_deleted.pegawai_hapus_nama) AS pegawai_hapus_nama,
    COALESCE(instruksitindakan_t.deleted_date, tindakan_deleted.tgl_batal) AS tgl_batal,
    COALESCE(tindakan_deleted.is_penatajasa, false) AS is_penatajasa,
        CASE
            WHEN cppt_t.pasienadmisi_id IS NOT NULL THEN
            CASE
                WHEN pendaftaran_t.is_stopakomodasi IS TRUE THEN true
                ELSE false
            END
            ELSE
            CASE
                WHEN pendaftaran_t.status_periksa::text = '4'::text THEN true
                WHEN pendaftaran_t.status_periksa::text = '433'::text THEN true
                ELSE false
            END
        END AS is_pulang,
    NULL::text AS status_penunjang,
    NULL::text AS status_penunjang_nama,
    NULL::text AS noresep,
    NULL::timestamp(6) without time zone AS tgl_resep_dibuat,
    NULL::integer AS satuankecil_id,
    NULL::text AS satuankecil_nama
   FROM instruksi_t
     JOIN ( SELECT a.instruksi_id,
            a.instruksitindakan_id,
            a.pendaftaran_id,
            a.tgl_tindakan,
            a.tipepaket_id,
            a.is_cyto,
            a.is_concern,
            a.qty,
            a.qty_sisa,
            a.dokterdpjp_id,
            a.status_implementasi,
            a.alasan_batal,
            a.deleted_date,
            a.ruangan_id,
            a.is_deleted,
            a.deleted_by
           FROM instruksitindakan_t a) instruksitindakan_t ON instruksi_t.instruksi_id = instruksitindakan_t.instruksi_id
     JOIN ( SELECT tipepaket_m_1.tipepaket_id,
            tipepaket_m_1.tipepaket_nama
           FROM tipepaket_m tipepaket_m_1) tipepaket_m ON instruksitindakan_t.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN ( SELECT pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai
           FROM pegawai_m) dokter ON instruksitindakan_t.dokterdpjp_id = dokter.pegawai_id
     LEFT JOIN ( SELECT a.cppt_id,
            a.pegawai_id,
            a.is_verifikasi,
            a.pendaftaran_id,
            a.pasienadmisi_id
           FROM cppt_t a) cppt_t ON cppt_t.cppt_id = instruksi_t.cppt_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) ruangan_m ON instruksitindakan_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT a.status_bayar,
            a.is_stopakomodasi,
            a.status_periksa,
            a.pendaftaran_id
           FROM pendaftaran_t a) pendaftaran_t ON cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.pendaftaran_id,
            tindakanpelayanan_t.instruksitindakan_id,
            tindakanpelayanan_t.tipepaket_id,
            peg_deleted.pegawai_id AS pegawai_hapus_id,
            peg_deleted.nama_pegawai AS pegawai_hapus_nama,
            tindakanpelayanan_t.deleted_date AS tgl_batal,
            tindakanpelayanan_t.alasan_batal,
            tindakanpelayanan_t.is_penatajasa
           FROM tindakanpelayanan_t
             LEFT JOIN loginpemakai_k leg_deleted ON tindakanpelayanan_t.deleted_by = leg_deleted.loginpemakai_id
             LEFT JOIN pegawai_m peg_deleted ON leg_deleted.pegawai_id = peg_deleted.pegawai_id
          WHERE tindakanpelayanan_t.is_deleted IS TRUE) tindakan_deleted ON instruksitindakan_t.pendaftaran_id = tindakan_deleted.pendaftaran_id AND instruksitindakan_t.instruksitindakan_id = tindakan_deleted.instruksitindakan_id AND instruksitindakan_t.tipepaket_id = tindakan_deleted.tipepaket_id
     LEFT JOIN ( SELECT loginpemakai_k.loginpemakai_id,
            pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai
           FROM loginpemakai_k
             JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id) pegawai_hapus ON instruksitindakan_t.deleted_by = pegawai_hapus.loginpemakai_id
     LEFT JOIN ( SELECT lookup_m.lookup_id,
            lookup_m.lookup_name
           FROM lookup_m) status ON instruksitindakan_t.status_implementasi::integer = status.lookup_id
UNION ALL
 SELECT 'BMHP'::text AS tipe_instruksi,
    instruksi_t.instruksi_id,
    instruksi_t.cppt_id,
    instruksi_t.catatan_instruksi,
    instruksitindakanbmhp_t.instruksitindakanbmhp_id AS instruksitindakan_id,
    instruksitindakanbmhp_t.tgl_pelayanan AS tgl_instruksi,
    instruksitindakanbmhp_t.obatalkes_id AS daftartindakan_id,
    concat(obatalkes_m.obatalkes_nama, '-'::text || instruksitindakanbmhp_t.qty) AS instruksi,
    tipepaket_m.tipepaket_nama AS paket,
    daftartindakan_m.daftartindakan_nama AS tindakan,
    instruksitindakanbmhp_t.qty,
    NULL::boolean AS is_cyto,
    instruksitindakanbmhp_t.qty_sisa,
    instruksitindakanbmhp_t.dokter_id,
    dokter.nama_pegawai AS dokter,
    instruksitindakanbmhp_t.status_implementasi,
    status.lookup_name AS status,
    instruksi_t.is_deleted AS instruksi_deleted,
    instruksitindakanbmhp_t.is_deleted AS tindakan_deleted,
    obatalkes_m.obatalkes_nama AS tindakaninstruksi_nama,
    NULL::text AS ket_cyto,
    NULL::character varying AS ket_racik_nama,
    NULL::character varying AS ket_racik,
    cppt_t.pegawai_id AS cpptpegawai_id,
    cppt_t.is_verifikasi AS is_verifikasi_dpjp,
    array_to_json(NULL::character varying[]) AS daftar_paket,
    instruksi_t.tgl_instruksi AS tanggal_terapi,
    'TINDAKANBMHP'::text AS grouping_tipe,
        CASE
            WHEN instruksitindakanbmhp_t.status_implementasi::text = '455'::character varying::text THEN true
            ELSE false
        END AS is_telah_implementasi,
    ruangan_m.ruangan_nama AS ruangan_pertindakan,
    ( SELECT row_to_json(json_bmhp.*) AS row_to_json
           FROM ( SELECT inst.instruksitindakan_id,
                    inst.dokterdpjp_id,
                    inst.daftartindakan_id,
                    dokterbmhp.nama_pegawai,
                    daftartindakanbmhp.daftartindakan_nama
                   FROM instruksitindakan_t inst
                     LEFT JOIN pegawai_m dokterbmhp ON dokterbmhp.pegawai_id = inst.dokterdpjp_id
                     LEFT JOIN daftartindakan_m daftartindakanbmhp ON daftartindakanbmhp.daftartindakan_id = inst.daftartindakan_id
                  WHERE inst.instruksitindakan_id = instruksitindakanbmhp_t.instruksitindakan_id) json_bmhp) AS bmhp_tindakandetail,
    instruksi_t.created_date AS tanggal_input,
    cppt_t.pendaftaran_id,
    cppt_t.pasienadmisi_id,
    daftartindakan_m.kelompoktindakan_id,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
        CASE pendaftaran_t.status_bayar
            WHEN 348 THEN true
            ELSE false
        END AS is_bayar,
    NULL::integer AS pasienkirimkeunitlain_id,
    COALESCE(instruksitindakanbmhp_t.alasan_batal, obat_deleted.alasan_batal) AS alasan_batal,
    COALESCE(pegawai_hapus.pegawai_id, obat_deleted.pegawai_hapus_id) AS pegawai_hapus_id,
    COALESCE(pegawai_hapus.nama_pegawai, obat_deleted.pegawai_hapus_nama) AS pegawai_hapus_nama,
    COALESCE(instruksitindakanbmhp_t.deleted_date, obat_deleted.tgl_batal) AS tgl_batal,
    COALESCE(obat_deleted.is_penatajasa, false) AS is_penatajasa,
        CASE
            WHEN cppt_t.pasienadmisi_id IS NOT NULL THEN
            CASE
                WHEN pendaftaran_t.is_stopakomodasi IS TRUE THEN true
                ELSE false
            END
            ELSE
            CASE
                WHEN pendaftaran_t.status_periksa::text = '4'::text THEN true
                WHEN pendaftaran_t.status_periksa::text = '433'::text THEN true
                ELSE false
            END
        END AS is_pulang,
    NULL::text AS status_penunjang,
    NULL::text AS status_penunjang_nama,
    NULL::text AS noresep,
    NULL::timestamp(6) without time zone AS tgl_resep_dibuat,
    NULL::integer AS satuankecil_id,
    NULL::text AS satuankecil_nama
   FROM instruksi_t
     JOIN ( SELECT a.instruksi_id,
            a.instruksitindakan_id,
            a.instruksitindakanbmhp_id,
            a.tgl_pelayanan,
            a.obatalkes_id,
            a.daftartindakan_id,
            a.tipepaket_id,
            a.ruangan_id,
            a.qty,
            a.qty_sisa,
            a.dokter_id,
            a.status_implementasi,
            a.is_deleted,
            a.deleted_by,
            a.alasan_batal,
            a.deleted_date
           FROM instruksitindakanbmhp_t a) instruksitindakanbmhp_t ON instruksi_t.instruksi_id = instruksitindakanbmhp_t.instruksi_id
     JOIN ( SELECT a.obatalkes_id,
            a.obatalkes_nama,
            a.jenisobatalkes_id
           FROM obatalkes_m a) obatalkes_m ON instruksitindakanbmhp_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT a.kelompoktindakan_id,
            a.daftartindakan_id,
            a.daftartindakan_nama
           FROM daftartindakan_m a) daftartindakan_m ON instruksitindakanbmhp_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN ( SELECT tipepaket_m_1.tipepaket_id,
            tipepaket_m_1.tipepaket_nama
           FROM tipepaket_m tipepaket_m_1) tipepaket_m ON instruksitindakanbmhp_t.tipepaket_id = tipepaket_m.tipepaket_id
     LEFT JOIN ( SELECT pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai
           FROM pegawai_m) dokter ON instruksitindakanbmhp_t.dokter_id = dokter.pegawai_id
     LEFT JOIN ( SELECT a.cppt_id,
            a.pegawai_id,
            a.is_verifikasi,
            a.pendaftaran_id,
            a.pasienadmisi_id
           FROM cppt_t a) cppt_t ON cppt_t.cppt_id = instruksi_t.cppt_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) ruangan_m ON instruksitindakanbmhp_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT a.status_bayar,
            a.is_stopakomodasi,
            a.status_periksa,
            a.pendaftaran_id
           FROM pendaftaran_t a) pendaftaran_t ON cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT obatalkespasien_t.instruksitindakanbmhp_id,
            peg_deleted.pegawai_id AS pegawai_hapus_id,
            peg_deleted.nama_pegawai AS pegawai_hapus_nama,
            obatalkespasien_t.deleted_date AS tgl_batal,
            obatalkespasien_t.alasan_batal,
            obatalkespasien_t.is_penatajasa
           FROM obatalkespasien_t
             LEFT JOIN loginpemakai_k leg_deleted ON obatalkespasien_t.deleted_by = leg_deleted.loginpemakai_id
             LEFT JOIN pegawai_m peg_deleted ON leg_deleted.pegawai_id = peg_deleted.pegawai_id
          WHERE obatalkespasien_t.is_deleted IS TRUE) obat_deleted ON instruksitindakanbmhp_t.instruksitindakanbmhp_id = obat_deleted.instruksitindakanbmhp_id
     LEFT JOIN ( SELECT loginpemakai_k.loginpemakai_id,
            pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai
           FROM loginpemakai_k
             JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id) pegawai_hapus ON instruksitindakanbmhp_t.deleted_by = pegawai_hapus.loginpemakai_id
     LEFT JOIN ( SELECT lookup_m.lookup_id,
            lookup_m.lookup_name
           FROM lookup_m) status ON instruksitindakanbmhp_t.status_implementasi::integer = status.lookup_id
UNION ALL
 SELECT
        CASE
            WHEN racikan_m.racikan_singkatan::text = 'OR'::text THEN 'RACIKAN'::text
            ELSE 'NONRACIKAN'::text
        END AS tipe_instruksi,
    instruksi_t.instruksi_id,
    instruksi_t.cppt_id,
    instruksi_t.catatan_instruksi,
    resepturdetail_t.resepturdetail_id AS instruksitindakan_id,
        CASE
            WHEN resepturdetail_t.tgl_resepturdetail IS NULL THEN resepturdetail_t.created_date
            ELSE resepturdetail_t.tgl_resepturdetail
        END AS tgl_instruksi,
    resepturdetail_t.obatalkes_id AS daftartindakan_id,
    concat(obatalkes_m.obatalkes_nama, ' - '::text || resepturdetail_t.qty_reseptur) AS instruksi,
    NULL::character varying AS paket,
    NULL::text AS tindakan,
    resepturdetail_t.qty_reseptur AS qty,
    NULL::boolean AS is_cyto,
    NULL::integer AS qty_sisa,
    reseptur_t.pegawai_id AS dokter_id,
    dokter.nama_pegawai AS dokter,
    reseptur_t.status_reseptur::character varying AS status_implementasi,
    status.lookup_name AS status,
    instruksi_t.is_deleted AS instruksi_deleted,
        CASE
            WHEN resepturdetail_t.is_deleted OR obat_deleted.is_deleted = true THEN true
            ELSE false
        END AS tindakan_deleted,
    obatalkes_m.obatalkes_nama AS tindakaninstruksi_nama,
    NULL::text AS ket_cyto,
    racikan_m.racikan_nama AS ket_racik_nama,
    racikan_m.racikan_singkatan AS ket_racik,
    cppt_t.pegawai_id AS cpptpegawai_id,
    cppt_t.is_verifikasi AS is_verifikasi_dpjp,
    array_to_json(NULL::character varying[]) AS daftar_paket,
    instruksi_t.tgl_instruksi AS tanggal_terapi,
    'RESEPTUR'::text AS grouping_tipe,
        CASE
            WHEN reseptur_t.status_reseptur::character varying::text = '347'::character varying::text THEN true
            ELSE false
        END AS is_telah_implementasi,
    ruangan_m.ruangan_nama AS ruangan_pertindakan,
    NULL::json AS bmhp_tindakandetail,
    instruksi_t.created_date AS tanggal_input,
    cppt_t.pendaftaran_id,
    cppt_t.pasienadmisi_id,
    NULL::integer AS kelompoktindakan_id,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
        CASE pendaftaran_t.status_bayar
            WHEN 348 THEN true
            ELSE false
        END AS is_bayar,
    NULL::integer AS pasienkirimkeunitlain_id,
    COALESCE(reseptur_t.alasan_batal, obat_deleted.alasan_batal) AS alasan_batal,
    peg_deleted.pegawai_id AS pegawai_hapus_id,
    peg_deleted.nama_pegawai AS pegawai_hapus_nama,
    COALESCE(reseptur_t.deleted_date, obat_deleted.tgl_batal) AS tgl_batal,
    COALESCE(obat_deleted.is_penatajasa, false) AS is_penatajasa,
        CASE
            WHEN cppt_t.pasienadmisi_id IS NOT NULL THEN
            CASE
                WHEN pendaftaran_t.is_stopakomodasi IS TRUE THEN true
                ELSE false
            END
            ELSE
            CASE
                WHEN pendaftaran_t.status_periksa::text = '4'::text THEN true
                WHEN pendaftaran_t.status_periksa::text = '433'::text THEN true
                ELSE false
            END
        END AS is_pulang,
    NULL::text AS status_penunjang,
    NULL::text AS status_penunjang_nama,
    reseptur_t.noresep,
    reseptur_t.tglreseptur AS tgl_resep_dibuat,
    resepturdetail_t.satuankecil_id,
    satuanunit_m.satuanunit_nama AS satuankecil_nama
   FROM instruksi_t
     JOIN reseptur_t ON instruksi_t.instruksi_id = reseptur_t.instruksi_id
     JOIN resepturdetail_t ON reseptur_t.reseptur_id = resepturdetail_t.reseptur_id
     JOIN obatalkes_m ON resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN pegawai_m dokter ON reseptur_t.pegawai_id = dokter.pegawai_id
     LEFT JOIN racikan_m ON resepturdetail_t.racikan_id = racikan_m.racikan_id
     LEFT JOIN cppt_t ON cppt_t.cppt_id = instruksi_t.cppt_id
     LEFT JOIN ruangan_m ON reseptur_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN pendaftaran_t ON cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT a.satuanunit_id,
            a.satuanunit_nama
           FROM satuanunit_m a) satuanunit_m ON resepturdetail_t.satuankecil_id = satuanunit_m.satuanunit_id
     LEFT JOIN ( SELECT obatalkespasien_t.resepturdetail_id,
            obatalkespasien_t.deleted_date AS tgl_batal,
            obatalkespasien_t.alasan_batal,
            obatalkespasien_t.is_penatajasa,
            obatalkespasien_t.is_deleted,
            obatalkespasien_t.deleted_by
           FROM obatalkespasien_t
          WHERE obatalkespasien_t.is_deleted IS TRUE) obat_deleted ON resepturdetail_t.resepturdetail_id = obat_deleted.resepturdetail_id
     LEFT JOIN ( SELECT a.reseptur_id,
            a.is_deleted,
            a.deleted_by,
            a.deleted_date
           FROM penjualanresep_t a) penjualanresep_t ON reseptur_t.reseptur_id = penjualanresep_t.reseptur_id
     LEFT JOIN loginpemakai_k leg_deleted ON COALESCE(reseptur_t.deleted_by, obat_deleted.deleted_by) = leg_deleted.loginpemakai_id
     LEFT JOIN pegawai_m peg_deleted ON leg_deleted.pegawai_id = peg_deleted.pegawai_id
     LEFT JOIN ( SELECT lookup_m.lookup_id,
            lookup_m.lookup_name
           FROM lookup_m) status ON reseptur_t.status_reseptur = status.lookup_id
UNION ALL
 SELECT
        CASE
            WHEN COALESCE(resepturracikan_t.type, 'OR'::character varying)::text = 'OR'::text THEN 'RACIKAN'::text
            ELSE 'NONRACIKAN'::text
        END AS tipe_instruksi,
    instruksi_t.instruksi_id,
    instruksi_t.cppt_id,
    instruksi_t.catatan_instruksi,
    resepturracikan_t.resepturracikan_id AS instruksitindakan_id,
    resepturracikan_t.created_date AS tgl_instruksi,
    NULL::integer AS daftartindakan_id,
    resepturracikan_t.racikan AS instruksi,
    NULL::character varying AS paket,
    NULL::text AS tindakan,
    NULL::double precision AS qty,
    NULL::boolean AS is_cyto,
    NULL::integer AS qty_sisa,
    reseptur_t.pegawai_id AS dokter_id,
    dokter.nama_pegawai AS dokter,
    reseptur_t.status_reseptur::character varying AS status_implementasi,
    status.lookup_name AS status,
    instruksi_t.is_deleted AS instruksi_deleted,
    resepturracikan_t.is_deleted AS tindakan_deleted,
    resepturracikan_t.racikan AS tindakaninstruksi_nama,
    NULL::text AS ket_cyto,
        CASE
            WHEN COALESCE(resepturracikan_t.type, 'OR'::character varying)::text = 'OR'::text THEN 'Racikan'::text
            WHEN COALESCE(resepturracikan_t.type, 'OR'::character varying)::text = 'OT'::text THEN 'Other'::text
            ELSE 'Non Racikan'::text
        END AS ket_racik_nama,
    COALESCE(resepturracikan_t.type, 'OR'::character varying) AS ket_racik,
    cppt_t.pegawai_id AS cpptpegawai_id,
    cppt_t.is_verifikasi AS is_verifikasi_dpjp,
    array_to_json(NULL::character varying[]) AS daftar_paket,
    instruksi_t.tgl_instruksi AS tanggal_terapi,
    'RESEPTUR'::text AS grouping_tipe,
        CASE
            WHEN reseptur_t.status_reseptur::character varying::text = '347'::character varying::text THEN true
            ELSE false
        END AS is_telah_implementasi,
    ruangan_m.ruangan_nama AS ruangan_pertindakan,
    NULL::json AS bmhp_tindakandetail,
    instruksi_t.created_date AS tanggal_input,
    cppt_t.pendaftaran_id,
    cppt_t.pasienadmisi_id,
    NULL::integer AS kelompoktindakan_id,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
        CASE pendaftaran_t.status_bayar
            WHEN 348 THEN true
            ELSE false
        END AS is_bayar,
    NULL::integer AS pasienkirimkeunitlain_id,
    reseptur_t.alasan_batal,
    peg_deleted.pegawai_id AS pegawai_hapus_id,
    peg_deleted.nama_pegawai AS pegawai_hapus_nama,
    reseptur_t.deleted_date AS tgl_batal,
    false AS is_penatajasa,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NOT NULL THEN
            CASE
                WHEN pendaftaran_t.is_stopakomodasi IS TRUE THEN true
                ELSE false
            END
            ELSE
            CASE
                WHEN pendaftaran_t.status_periksa::text = '4'::text THEN true
                WHEN pendaftaran_t.status_periksa::text = '433'::text THEN true
                ELSE false
            END
        END AS is_pulang,
    NULL::text AS status_penunjang,
    NULL::text AS status_penunjang_nama,
    reseptur_t.noresep,
    reseptur_t.tglreseptur AS tgl_resep_dibuat,
    NULL::integer AS satuankecil_id,
    NULL::text AS satuankecil_nama
   FROM instruksi_t
     JOIN reseptur_t ON instruksi_t.instruksi_id = reseptur_t.instruksi_id
     JOIN resepturracikan_t ON reseptur_t.reseptur_id = resepturracikan_t.reseptur_id
     JOIN pegawai_m dokter ON reseptur_t.pegawai_id = dokter.pegawai_id
     LEFT JOIN cppt_t ON cppt_t.cppt_id = instruksi_t.cppt_id
     LEFT JOIN ruangan_m ON reseptur_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN pendaftaran_t ON cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT a.reseptur_id,
            a.is_deleted,
            a.deleted_by,
            a.deleted_date
           FROM penjualanresep_t a) penjualanresep_t ON reseptur_t.reseptur_id = penjualanresep_t.reseptur_id
     LEFT JOIN loginpemakai_k leg_deleted ON reseptur_t.deleted_by = leg_deleted.loginpemakai_id
     LEFT JOIN pegawai_m peg_deleted ON leg_deleted.pegawai_id = peg_deleted.pegawai_id
     LEFT JOIN ( SELECT lookup_m.lookup_id,
            lookup_m.lookup_name
           FROM lookup_m) status ON reseptur_t.status_reseptur = status.lookup_id
UNION ALL
 SELECT 'LAB_TINDAKAN'::text AS tipe_instruksi,
    instruksi_t.instruksi_id,
    instruksi_t.cppt_id,
    instruksi_t.catatan_instruksi,
    permintaankepenunjang_t.permintaankepenunjang_id AS instruksitindakan_id,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_instruksi,
    permintaankepenunjang_t.daftartindakan_id,
    concat(daftartindakan_m.daftartindakan_nama, '-',
        CASE
            WHEN permintaankepenunjang_t.is_cyto = true THEN 'Cyto - '::text
            ELSE NULL::text
        END, permintaankepenunjang_t.qtypermintaan) AS instruksi,
    NULL::character varying AS paket,
    NULL::text AS tindakan,
    permintaankepenunjang_t.qtypermintaan AS qty,
    permintaankepenunjang_t.is_cyto,
    NULL::integer AS qty_sisa,
    pasienkirimkeunitlain_t.pegawai_id AS dokter_id,
    dokter.nama_pegawai AS dokter,
        CASE
            WHEN hasil_wynacom.no_masukpenunjang IS NOT NULL THEN '475'::character varying
            WHEN pasienmasukpenunjang_t.status_periksa IS NULL THEN pasienkirimkeunitlain_t.status_penunjang
            ELSE pasienmasukpenunjang_t.status_periksa
        END AS status_implementasi,
        CASE
            WHEN hasil_wynacom.no_masukpenunjang IS NOT NULL THEN 'SELESAI'::character varying
            WHEN pasienmasukpenunjang_t.status_periksa IS NULL THEN fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer)
            ELSE fgetnamalookup(pasienmasukpenunjang_t.status_periksa::integer)
        END AS status,
    instruksi_t.is_deleted AS instruksi_deleted,
    permintaankepenunjang_t.is_deleted AS tindakan_deleted,
    daftartindakan_m.daftartindakan_nama AS tindakaninstruksi_nama,
        CASE
            WHEN permintaankepenunjang_t.is_cyto = true THEN 'Cyto'::text
            ELSE 'Non Cyto'::text
        END AS ket_cyto,
    NULL::character varying AS ket_racik_nama,
    NULL::character varying AS ket_racik,
    cppt_t.pegawai_id AS cpptpegawai_id,
    cppt_t.is_verifikasi AS is_verifikasi_dpjp,
    array_to_json(NULL::character varying[]) AS daftar_paket,
    instruksi_t.tgl_instruksi AS tanggal_terapi,
    'PENUNJANG'::text AS grouping_tipe,
        CASE
            WHEN pasienkirimkeunitlain_t.status_penunjang::text = '471'::character varying::text THEN true
            ELSE false
        END AS is_telah_implementasi,
    ruangan_m.ruangan_nama AS ruangan_pertindakan,
    NULL::json AS bmhp_tindakandetail,
    instruksi_t.created_date AS tanggal_input,
    cppt_t.pendaftaran_id,
    cppt_t.pasienadmisi_id,
    daftartindakan_m.kelompoktindakan_id,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
        CASE pendaftaran_t.status_bayar
            WHEN 348 THEN true
            ELSE false
        END AS is_bayar,
    pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    COALESCE(batal_order.alasan_batal, tindakan_deleted.alasan_batal, permintaankepenunjang_t.alasan_batal) AS alasan_batal,
    peg_deleted.pegawai_id AS pegawai_hapus_id,
    peg_deleted.nama_pegawai AS pegawai_hapus_nama,
    COALESCE(batal_order.tgl_batal, tindakan_deleted.tgl_batal, permintaankepenunjang_t.deleted_date) AS tgl_batal,
    COALESCE(tindakan_deleted.is_penatajasa, false) AS is_penatajasa,
        CASE
            WHEN pendaftaran_t.pasienadmisi_id IS NOT NULL THEN
            CASE
                WHEN pendaftaran_t.is_stopakomodasi IS TRUE THEN true
                ELSE false
            END
            ELSE
            CASE
                WHEN pendaftaran_t.status_periksa::text = '4'::text THEN true
                WHEN pendaftaran_t.status_periksa::text = '433'::text THEN true
                ELSE false
            END
        END AS is_pulang,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienkirimkeunitlain_t.status_penunjang_nama,
    NULL::text AS noresep,
    NULL::timestamp(6) without time zone AS tgl_resep_dibuat,
    NULL::integer AS satuankecil_id,
    NULL::text AS satuankecil_nama
   FROM instruksi_t
     JOIN ( SELECT a.tgl_kirimpasien,
            a.pegawai_id,
            a.status_penunjang,
            a.pasienkirimkeunitlain_id,
            a.instruksi_id,
            a.ruangan_id,
            a.instalasi_id,
            b.lookup_name AS status_penunjang_nama
           FROM pasienkirimkeunitlain_t a
             LEFT JOIN lookup_m b ON a.status_penunjang::integer = b.lookup_id) pasienkirimkeunitlain_t ON instruksi_t.instruksi_id = pasienkirimkeunitlain_t.instruksi_id
     JOIN permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
     LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id
     JOIN daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN pegawai_m dokter ON pasienkirimkeunitlain_t.pegawai_id = dokter.pegawai_id
     LEFT JOIN ruangan_m ON pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN cppt_t ON cppt_t.cppt_id = instruksi_t.cppt_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN pendaftaran_t ON cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.daftartindakan_id,
            tindakanpelayanan_t.deleted_date AS tgl_batal,
            tindakanpelayanan_t.alasan_batal,
            tindakanpelayanan_t.deleted_by,
            tindakanpelayanan_t.is_penatajasa
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.is_deleted IS TRUE) tindakan_deleted ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_deleted.pasienmasukpenunjang_id AND permintaankepenunjang_t.daftartindakan_id = tindakan_deleted.daftartindakan_id
     LEFT JOIN ( SELECT batalorderpenunjang_t.pasienkirimkeunitlain_id,
            batalorderpenunjang_t.tgl_batalorder AS tgl_batal,
            batalorderpenunjang_t.alasan AS alasan_batal,
            batalorderpenunjang_t.created_by,
            batalorderpenunjang_t.is_active,
            batalorderpenunjang_t.peg_menyetujui_id
           FROM batalorderpenunjang_t) batal_order ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = batal_order.pasienkirimkeunitlain_id
     LEFT JOIN loginpemakai_k leg_deleted ON COALESCE(tindakan_deleted.deleted_by, permintaankepenunjang_t.deleted_by) = leg_deleted.loginpemakai_id
     LEFT JOIN pegawai_m peg_deleted ON COALESCE(batal_order.peg_menyetujui_id, leg_deleted.pegawai_id) = peg_deleted.pegawai_id
     LEFT JOIN ( SELECT hasilpemeriksaanlab_wynacom_t.his_reg_no AS no_masukpenunjang
           FROM hasilpemeriksaanlab_wynacom_t
          WHERE hasilpemeriksaanlab_wynacom_t.is_deleted = false
          GROUP BY hasilpemeriksaanlab_wynacom_t.his_reg_no) hasil_wynacom ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasil_wynacom.no_masukpenunjang::text
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4
UNION ALL
 SELECT 'LAB_PAKET'::text AS tipe_instruksi,
    instruksi_t.instruksi_id,
    instruksi_t.cppt_id,
    instruksi_t.catatan_instruksi,
    permintaankepenunjang_t.permintaankepenunjang_id AS instruksitindakan_id,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_instruksi,
    permintaankepenunjang_t.tipepaket_id AS daftartindakan_id,
    concat(tipepaket_m.tipepaket_nama, '-',
        CASE
            WHEN permintaankepenunjang_t.is_cyto = true THEN 'Cyto - '::text
            ELSE NULL::text
        END, permintaankepenunjang_t.qtypermintaan) AS instruksi,
    tipepaket_m.tipepaket_nama AS paket,
    NULL::text AS tindakan,
    permintaankepenunjang_t.qtypermintaan AS qty,
    permintaankepenunjang_t.is_cyto,
    NULL::integer AS qty_sisa,
    pasienkirimkeunitlain_t.pegawai_id AS dokter_id,
    dokter.nama_pegawai AS dokter,
        CASE
            WHEN pasienmasukpenunjang_t.status_periksa IS NULL THEN pasienkirimkeunitlain_t.status_penunjang
            ELSE pasienmasukpenunjang_t.status_periksa
        END AS status_implementasi,
        CASE
            WHEN pasienmasukpenunjang_t.status_periksa IS NULL THEN fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer)
            ELSE fgetnamalookup(pasienmasukpenunjang_t.status_periksa::integer)
        END AS status,
    instruksi_t.is_deleted AS instruksi_deleted,
    permintaankepenunjang_t.is_deleted AS tindakan_deleted,
    tipepaket_m.tipepaket_nama AS tindakaninstruksi_nama,
        CASE
            WHEN permintaankepenunjang_t.is_cyto = true THEN 'Cyto'::text
            ELSE 'Non Cyto'::text
        END AS ket_cyto,
    NULL::character varying AS ket_racik_nama,
    NULL::character varying AS ket_racik,
    cppt_t.pegawai_id AS cpptpegawai_id,
    cppt_t.is_verifikasi AS is_verifikasi_dpjp,
    array_to_json(ARRAY( SELECT daftartindakan_m.daftartindakan_nama
           FROM paketpelayanan_mp
             LEFT JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
          WHERE paketpelayanan_mp.tipepaket_id = permintaankepenunjang_t.tipepaket_id)) AS daftar_paket,
    instruksi_t.tgl_instruksi AS tanggal_terapi,
    'PENUNJANG'::text AS grouping_tipe,
        CASE
            WHEN pasienkirimkeunitlain_t.status_penunjang::text = '471'::character varying::text THEN true
            ELSE false
        END AS is_telah_implementasi,
    ruangan_m.ruangan_nama AS ruangan_pertindakan,
    NULL::json AS bmhp_tindakandetail,
    instruksi_t.created_date AS tanggal_input,
    cppt_t.pendaftaran_id,
    cppt_t.pasienadmisi_id,
    NULL::integer AS kelompoktindakan_id,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
        CASE pendaftaran_t.status_bayar
            WHEN 348 THEN true
            ELSE false
        END AS is_bayar,
    pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    COALESCE(batal_order.alasan_batal, tindakan_deleted.alasan_batal, permintaankepenunjang_t.alasan_batal) AS alasan_batal,
    peg_deleted.pegawai_id AS pegawai_hapus_id,
    peg_deleted.nama_pegawai AS pegawai_hapus_nama,
    COALESCE(batal_order.tgl_batal, tindakan_deleted.tgl_batal, permintaankepenunjang_t.deleted_date) AS tgl_batal,
    COALESCE(tindakan_deleted.is_penatajasa, false) AS is_penatajasa,
        CASE
            WHEN cppt_t.pasienadmisi_id IS NOT NULL THEN
            CASE
                WHEN pendaftaran_t.is_stopakomodasi IS TRUE THEN true
                ELSE false
            END
            ELSE
            CASE
                WHEN pendaftaran_t.status_periksa::text = '4'::text THEN true
                WHEN pendaftaran_t.status_periksa::text = '433'::text THEN true
                ELSE false
            END
        END AS is_pulang,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienkirimkeunitlain_t.status_penunjang_nama,
    NULL::text AS noresep,
    NULL::timestamp(6) without time zone AS tgl_resep_dibuat,
    NULL::integer AS satuankecil_id,
    NULL::text AS satuankecil_nama
   FROM instruksi_t
     JOIN ( SELECT a.tgl_kirimpasien,
            a.pegawai_id,
            a.status_penunjang,
            a.pasienkirimkeunitlain_id,
            a.instruksi_id,
            a.ruangan_id,
            a.instalasi_id,
            b.lookup_name AS status_penunjang_nama
           FROM pasienkirimkeunitlain_t a
             LEFT JOIN lookup_m b ON a.status_penunjang::integer = b.lookup_id) pasienkirimkeunitlain_t ON instruksi_t.instruksi_id = pasienkirimkeunitlain_t.instruksi_id
     JOIN permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
     LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id
     JOIN tipepaket_m ON permintaankepenunjang_t.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN pegawai_m dokter ON pasienkirimkeunitlain_t.pegawai_id = dokter.pegawai_id
     LEFT JOIN ruangan_m ON pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN cppt_t ON cppt_t.cppt_id = instruksi_t.cppt_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN pendaftaran_t ON cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.tipepaket_id,
            tindakanpelayanan_t.deleted_date AS tgl_batal,
            tindakanpelayanan_t.alasan_batal,
            tindakanpelayanan_t.deleted_by,
            tindakanpelayanan_t.is_penatajasa
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.is_deleted IS TRUE) tindakan_deleted ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_deleted.pasienmasukpenunjang_id AND permintaankepenunjang_t.tipepaket_id = tindakan_deleted.tipepaket_id
     LEFT JOIN ( SELECT batalorderpenunjang_t.pasienkirimkeunitlain_id,
            batalorderpenunjang_t.tgl_batalorder AS tgl_batal,
            batalorderpenunjang_t.alasan AS alasan_batal,
            batalorderpenunjang_t.created_by,
            batalorderpenunjang_t.is_active,
            batalorderpenunjang_t.peg_menyetujui_id
           FROM batalorderpenunjang_t) batal_order ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = batal_order.pasienkirimkeunitlain_id
     LEFT JOIN loginpemakai_k leg_deleted ON COALESCE(tindakan_deleted.deleted_by, permintaankepenunjang_t.deleted_by) = leg_deleted.loginpemakai_id
     LEFT JOIN pegawai_m peg_deleted ON COALESCE(batal_order.peg_menyetujui_id, leg_deleted.pegawai_id) = peg_deleted.pegawai_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4
UNION ALL
 SELECT 'RAD_TINDAKAN'::text AS tipe_instruksi,
    instruksi_t.instruksi_id,
    instruksi_t.cppt_id,
    instruksi_t.catatan_instruksi,
    permintaankepenunjang_t.permintaankepenunjang_id AS instruksitindakan_id,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_instruksi,
    permintaankepenunjang_t.daftartindakan_id,
    concat(daftartindakan_m.daftartindakan_nama, '-',
        CASE
            WHEN permintaankepenunjang_t.is_cyto = true THEN 'Cyto - '::text
            ELSE NULL::text
        END, permintaankepenunjang_t.qtypermintaan) AS instruksi,
    NULL::character varying AS paket,
    NULL::text AS tindakan,
    permintaankepenunjang_t.qtypermintaan AS qty,
    permintaankepenunjang_t.is_cyto,
    NULL::integer AS qty_sisa,
    pasienkirimkeunitlain_t.pegawai_id AS dokter_id,
    dokter.nama_pegawai AS dokter,
        CASE
            WHEN pasienmasukpenunjang_t.status_periksa IS NULL THEN pasienkirimkeunitlain_t.status_penunjang
            ELSE pasienmasukpenunjang_t.status_periksa
        END AS status_implementasi,
        CASE
            WHEN pasienmasukpenunjang_t.status_periksa IS NULL THEN fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer)
            ELSE fgetnamalookup(pasienmasukpenunjang_t.status_periksa::integer)
        END AS status,
    instruksi_t.is_deleted AS instruksi_deleted,
    COALESCE(batal_order.is_active, permintaankepenunjang_t.is_deleted) AS tindakan_deleted,
    daftartindakan_m.daftartindakan_nama AS tindakaninstruksi_nama,
        CASE
            WHEN permintaankepenunjang_t.is_cyto = true THEN 'Cyto'::text
            ELSE 'Non Cyto'::text
        END AS ket_cyto,
    NULL::character varying AS ket_racik_nama,
    NULL::character varying AS ket_racik,
    cppt_t.pegawai_id AS cpptpegawai_id,
    cppt_t.is_verifikasi AS is_verifikasi_dpjp,
    array_to_json(NULL::character varying[]) AS daftar_paket,
    instruksi_t.tgl_instruksi AS tanggal_terapi,
    'PENUNJANG'::text AS grouping_tipe,
        CASE
            WHEN pasienkirimkeunitlain_t.status_penunjang::text = '471'::character varying::text THEN true
            ELSE false
        END AS is_telah_implementasi,
    ruangan_m.ruangan_nama AS ruangan_pertindakan,
    NULL::json AS bmhp_tindakandetail,
    instruksi_t.created_date AS tanggal_input,
    cppt_t.pendaftaran_id,
    cppt_t.pasienadmisi_id,
    daftartindakan_m.kelompoktindakan_id,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
        CASE pendaftaran_t.status_bayar
            WHEN 348 THEN true
            ELSE false
        END AS is_bayar,
    pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    COALESCE(batal_order.alasan_batal, tindakan_deleted.alasan_batal, permintaankepenunjang_t.alasan_batal) AS alasan_batal,
    peg_deleted.pegawai_id AS pegawai_hapus_id,
    peg_deleted.nama_pegawai AS pegawai_hapus_nama,
    COALESCE(batal_order.tgl_batal, tindakan_deleted.tgl_batal, permintaankepenunjang_t.deleted_date) AS tgl_batal,
    COALESCE(tindakan_deleted.is_penatajasa, false) AS is_penatajasa,
        CASE
            WHEN cppt_t.pasienadmisi_id IS NOT NULL THEN
            CASE
                WHEN pendaftaran_t.is_stopakomodasi IS TRUE THEN true
                ELSE false
            END
            ELSE
            CASE
                WHEN pendaftaran_t.status_periksa::text = '4'::text THEN true
                WHEN pendaftaran_t.status_periksa::text = '433'::text THEN true
                ELSE false
            END
        END AS is_pulang,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienkirimkeunitlain_t.status_penunjang_nama,
    NULL::text AS noresep,
    NULL::timestamp(6) without time zone AS tgl_resep_dibuat,
    NULL::integer AS satuankecil_id,
    NULL::text AS satuankecil_nama
   FROM instruksi_t
     JOIN ( SELECT a.tgl_kirimpasien,
            a.pegawai_id,
            a.status_penunjang,
            a.pasienkirimkeunitlain_id,
            a.instruksi_id,
            a.ruangan_id,
            a.instalasi_id,
            b.lookup_name AS status_penunjang_nama
           FROM pasienkirimkeunitlain_t a
             LEFT JOIN lookup_m b ON a.status_penunjang::integer = b.lookup_id) pasienkirimkeunitlain_t ON instruksi_t.instruksi_id = pasienkirimkeunitlain_t.instruksi_id
     JOIN permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
     LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id
     JOIN daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN pegawai_m dokter ON pasienkirimkeunitlain_t.pegawai_id = dokter.pegawai_id
     LEFT JOIN ruangan_m ON pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN cppt_t ON cppt_t.cppt_id = instruksi_t.cppt_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN pendaftaran_t ON cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.daftartindakan_id,
            tindakanpelayanan_t.deleted_date AS tgl_batal,
            tindakanpelayanan_t.alasan_batal,
            tindakanpelayanan_t.deleted_by,
            tindakanpelayanan_t.is_penatajasa
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.is_deleted IS TRUE) tindakan_deleted ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_deleted.pasienmasukpenunjang_id AND permintaankepenunjang_t.daftartindakan_id = tindakan_deleted.daftartindakan_id
     LEFT JOIN ( SELECT batalorderpenunjang_t.pasienkirimkeunitlain_id,
            batalorderpenunjang_t.tgl_batalorder AS tgl_batal,
            batalorderpenunjang_t.alasan AS alasan_batal,
            batalorderpenunjang_t.created_by,
            batalorderpenunjang_t.is_active,
            batalorderpenunjang_t.peg_menyetujui_id
           FROM batalorderpenunjang_t) batal_order ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = batal_order.pasienkirimkeunitlain_id
     LEFT JOIN loginpemakai_k leg_deleted ON COALESCE(tindakan_deleted.deleted_by, permintaankepenunjang_t.deleted_by) = leg_deleted.loginpemakai_id
     LEFT JOIN pegawai_m peg_deleted ON COALESCE(batal_order.peg_menyetujui_id, leg_deleted.pegawai_id) = peg_deleted.pegawai_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 5
UNION ALL
 SELECT 'RAD_PAKET'::text AS tipe_instruksi,
    instruksi_t.instruksi_id,
    instruksi_t.cppt_id,
    instruksi_t.catatan_instruksi,
    permintaankepenunjang_t.permintaankepenunjang_id AS instruksitindakan_id,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_instruksi,
    permintaankepenunjang_t.tipepaket_id AS daftartindakan_id,
    concat(tipepaket_m.tipepaket_nama, '-',
        CASE
            WHEN permintaankepenunjang_t.is_cyto = true THEN 'Cyto - '::text
            ELSE NULL::text
        END, permintaankepenunjang_t.qtypermintaan) AS instruksi,
    tipepaket_m.tipepaket_nama AS paket,
    NULL::text AS tindakan,
    permintaankepenunjang_t.qtypermintaan AS qty,
    permintaankepenunjang_t.is_cyto,
    NULL::integer AS qty_sisa,
    pasienkirimkeunitlain_t.pegawai_id AS dokter_id,
    dokter.nama_pegawai AS dokter,
        CASE
            WHEN pasienmasukpenunjang_t.status_periksa IS NULL THEN pasienkirimkeunitlain_t.status_penunjang
            ELSE pasienmasukpenunjang_t.status_periksa
        END AS status_implementasi,
        CASE
            WHEN pasienmasukpenunjang_t.status_periksa IS NULL THEN fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer)
            ELSE fgetnamalookup(pasienmasukpenunjang_t.status_periksa::integer)
        END AS status,
    instruksi_t.is_deleted AS instruksi_deleted,
    permintaankepenunjang_t.is_deleted AS tindakan_deleted,
    tipepaket_m.tipepaket_nama AS tindakaninstruksi_nama,
        CASE
            WHEN permintaankepenunjang_t.is_cyto = true THEN 'Cyto'::text
            ELSE 'Non Cyto'::text
        END AS ket_cyto,
    NULL::character varying AS ket_racik_nama,
    NULL::character varying AS ket_racik,
    cppt_t.pegawai_id AS cpptpegawai_id,
    cppt_t.is_verifikasi AS is_verifikasi_dpjp,
    array_to_json(ARRAY( SELECT daftartindakan_m.daftartindakan_nama
           FROM paketpelayanan_mp
             LEFT JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
          WHERE paketpelayanan_mp.tipepaket_id = permintaankepenunjang_t.tipepaket_id)) AS daftar_paket,
    instruksi_t.tgl_instruksi AS tanggal_terapi,
    'PENUNJANG'::text AS grouping_tipe,
        CASE
            WHEN pasienkirimkeunitlain_t.status_penunjang::text = '471'::character varying::text THEN true
            ELSE false
        END AS is_telah_implementasi,
    ruangan_m.ruangan_nama AS ruangan_pertindakan,
    NULL::json AS bmhp_tindakandetail,
    instruksi_t.created_date AS tanggal_input,
    cppt_t.pendaftaran_id,
    cppt_t.pasienadmisi_id,
    NULL::integer AS kelompoktindakan_id,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
        CASE pendaftaran_t.status_bayar
            WHEN 348 THEN true
            ELSE false
        END AS is_bayar,
    pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    COALESCE(batal_order.alasan_batal, tindakan_deleted.alasan_batal, permintaankepenunjang_t.alasan_batal) AS alasan_batal,
    peg_deleted.pegawai_id AS pegawai_hapus_id,
    peg_deleted.nama_pegawai AS pegawai_hapus_nama,
    COALESCE(batal_order.tgl_batal, tindakan_deleted.tgl_batal, permintaankepenunjang_t.deleted_date) AS tgl_batal,
    COALESCE(tindakan_deleted.is_penatajasa, false) AS is_penatajasa,
        CASE
            WHEN cppt_t.pasienadmisi_id IS NOT NULL THEN
            CASE
                WHEN pendaftaran_t.is_stopakomodasi IS TRUE THEN true
                ELSE false
            END
            ELSE
            CASE
                WHEN pendaftaran_t.status_periksa::text = '4'::text THEN true
                WHEN pendaftaran_t.status_periksa::text = '433'::text THEN true
                ELSE false
            END
        END AS is_pulang,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienkirimkeunitlain_t.status_penunjang_nama,
    NULL::text AS noresep,
    NULL::timestamp(6) without time zone AS tgl_resep_dibuat,
    NULL::integer AS satuankecil_id,
    NULL::text AS satuankecil_nama
   FROM instruksi_t
     JOIN ( SELECT a.tgl_kirimpasien,
            a.pegawai_id,
            a.status_penunjang,
            a.pasienkirimkeunitlain_id,
            a.instruksi_id,
            a.ruangan_id,
            a.instalasi_id,
            b.lookup_name AS status_penunjang_nama
           FROM pasienkirimkeunitlain_t a
             LEFT JOIN lookup_m b ON a.status_penunjang::integer = b.lookup_id) pasienkirimkeunitlain_t ON instruksi_t.instruksi_id = pasienkirimkeunitlain_t.instruksi_id
     JOIN permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
     LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id
     JOIN tipepaket_m ON permintaankepenunjang_t.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN pegawai_m dokter ON pasienkirimkeunitlain_t.pegawai_id = dokter.pegawai_id
     LEFT JOIN ruangan_m ON pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN cppt_t ON cppt_t.cppt_id = instruksi_t.cppt_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN pendaftaran_t ON cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.tipepaket_id,
            tindakanpelayanan_t.deleted_date AS tgl_batal,
            tindakanpelayanan_t.alasan_batal,
            tindakanpelayanan_t.deleted_by,
            tindakanpelayanan_t.is_penatajasa
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.is_deleted IS TRUE) tindakan_deleted ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_deleted.pasienmasukpenunjang_id AND permintaankepenunjang_t.tipepaket_id = tindakan_deleted.tipepaket_id
     LEFT JOIN ( SELECT batalorderpenunjang_t.pasienkirimkeunitlain_id,
            batalorderpenunjang_t.tgl_batalorder AS tgl_batal,
            batalorderpenunjang_t.alasan AS alasan_batal,
            batalorderpenunjang_t.created_by,
            batalorderpenunjang_t.is_active,
            batalorderpenunjang_t.peg_menyetujui_id
           FROM batalorderpenunjang_t) batal_order ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = batal_order.pasienkirimkeunitlain_id
     LEFT JOIN loginpemakai_k leg_deleted ON COALESCE(tindakan_deleted.deleted_by, permintaankepenunjang_t.deleted_by) = leg_deleted.loginpemakai_id
     LEFT JOIN pegawai_m peg_deleted ON COALESCE(batal_order.peg_menyetujui_id, leg_deleted.pegawai_id) = peg_deleted.pegawai_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 5
UNION ALL
 SELECT 'BED_TINDAKAN'::text AS tipe_instruksi,
    instruksi_t.instruksi_id,
    instruksi_t.cppt_id,
    instruksi_t.catatan_instruksi,
    permintaankepenunjang_t.permintaankepenunjang_id AS instruksitindakan_id,
    COALESCE(permintaankepenunjang_t.tglpermintaankepenunjang, pasienkirimkeunitlain_t.tgl_kirimpasien) AS tgl_instruksi,
    permintaankepenunjang_t.daftartindakan_id,
    concat(daftartindakan_m.daftartindakan_nama, '-',
        CASE
            WHEN permintaankepenunjang_t.is_cyto = true THEN 'Cyto - '::text
            ELSE NULL::text
        END, permintaankepenunjang_t.qtypermintaan) AS instruksi,
    NULL::character varying AS paket,
    NULL::text AS tindakan,
    permintaankepenunjang_t.qtypermintaan AS qty,
    permintaankepenunjang_t.is_cyto,
    NULL::integer AS qty_sisa,
    pasienkirimkeunitlain_t.pegawai_id AS dokter_id,
    dokter.nama_pegawai AS dokter,
        CASE
            WHEN pasienmasukpenunjang_t.status_periksa IS NULL THEN pasienkirimkeunitlain_t.status_penunjang
            ELSE pasienmasukpenunjang_t.status_periksa
        END AS status_implementasi,
        CASE
            WHEN pasienmasukpenunjang_t.status_periksa IS NULL THEN fgetnamalookup(pasienkirimkeunitlain_t.status_penunjang::integer)
            ELSE fgetnamalookup(pasienmasukpenunjang_t.status_periksa::integer)
        END AS status,
    instruksi_t.is_deleted AS instruksi_deleted,
    permintaankepenunjang_t.is_deleted AS tindakan_deleted,
    daftartindakan_m.daftartindakan_nama AS tindakaninstruksi_nama,
        CASE
            WHEN permintaankepenunjang_t.is_cyto = true THEN 'Cyto'::text
            ELSE 'Non Cyto'::text
        END AS ket_cyto,
    NULL::character varying AS ket_racik_nama,
    NULL::character varying AS ket_racik,
    cppt_t.pegawai_id AS cpptpegawai_id,
    cppt_t.is_verifikasi AS is_verifikasi_dpjp,
    array_to_json(NULL::character varying[]) AS daftar_paket,
    instruksi_t.tgl_instruksi AS tanggal_terapi,
    'PENUNJANG'::text AS grouping_tipe,
        CASE
            WHEN pasienkirimkeunitlain_t.status_penunjang::text = '471'::character varying::text THEN true
            ELSE false
        END AS is_telah_implementasi,
    ruangan_m.ruangan_nama AS ruangan_pertindakan,
    NULL::json AS bmhp_tindakandetail,
    instruksi_t.created_date AS tanggal_input,
    cppt_t.pendaftaran_id,
    cppt_t.pasienadmisi_id,
    daftartindakan_m.kelompoktindakan_id,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
        CASE pendaftaran_t.status_bayar
            WHEN 348 THEN true
            ELSE false
        END AS is_bayar,
    pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
    COALESCE(batal_order.alasan_batal, tindakan_deleted.alasan_batal, permintaankepenunjang_t.alasan_batal) AS alasan_batal,
    peg_deleted.pegawai_id AS pegawai_hapus_id,
    peg_deleted.nama_pegawai AS pegawai_hapus_nama,
    COALESCE(batal_order.tgl_batal, tindakan_deleted.tgl_batal, permintaankepenunjang_t.deleted_date) AS tgl_batal,
    COALESCE(tindakan_deleted.is_penatajasa, false) AS is_penatajasa,
        CASE
            WHEN cppt_t.pasienadmisi_id IS NOT NULL THEN
            CASE
                WHEN pendaftaran_t.is_stopakomodasi IS TRUE THEN true
                ELSE false
            END
            ELSE
            CASE
                WHEN pendaftaran_t.status_periksa::text = '4'::text THEN true
                WHEN pendaftaran_t.status_periksa::text = '433'::text THEN true
                ELSE false
            END
        END AS is_pulang,
    pasienkirimkeunitlain_t.status_penunjang,
    pasienkirimkeunitlain_t.status_penunjang_nama,
    NULL::text AS noresep,
    NULL::timestamp(6) without time zone AS tgl_resep_dibuat,
    NULL::integer AS satuankecil_id,
    NULL::text AS satuankecil_nama
   FROM instruksi_t
     JOIN ( SELECT a.tgl_kirimpasien,
            a.pegawai_id,
            a.status_penunjang,
            a.pasienkirimkeunitlain_id,
            a.instruksi_id,
            a.ruangan_id,
            a.instalasi_id,
            b.lookup_name AS status_penunjang_nama
           FROM pasienkirimkeunitlain_t a
             LEFT JOIN lookup_m b ON a.status_penunjang::integer = b.lookup_id) pasienkirimkeunitlain_t ON instruksi_t.instruksi_id = pasienkirimkeunitlain_t.instruksi_id
     JOIN permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
     LEFT JOIN pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id
     JOIN daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN pegawai_m dokter ON pasienkirimkeunitlain_t.pegawai_id = dokter.pegawai_id
     LEFT JOIN ruangan_m ON pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN cppt_t ON cppt_t.cppt_id = instruksi_t.cppt_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN pendaftaran_t ON cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.daftartindakan_id,
            tindakanpelayanan_t.deleted_date AS tgl_batal,
            tindakanpelayanan_t.alasan_batal,
            tindakanpelayanan_t.deleted_by,
            tindakanpelayanan_t.is_penatajasa
           FROM tindakanpelayanan_t
          WHERE tindakanpelayanan_t.is_deleted IS TRUE) tindakan_deleted ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_deleted.pasienmasukpenunjang_id AND permintaankepenunjang_t.daftartindakan_id = tindakan_deleted.daftartindakan_id
     LEFT JOIN ( SELECT batalorderpenunjang_t.pasienkirimkeunitlain_id,
            batalorderpenunjang_t.tgl_batalorder AS tgl_batal,
            batalorderpenunjang_t.alasan AS alasan_batal,
            batalorderpenunjang_t.created_by,
            batalorderpenunjang_t.is_active,
            batalorderpenunjang_t.peg_menyetujui_id
           FROM batalorderpenunjang_t) batal_order ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = batal_order.pasienkirimkeunitlain_id
     LEFT JOIN loginpemakai_k leg_deleted ON COALESCE(tindakan_deleted.deleted_by, permintaankepenunjang_t.deleted_by) = leg_deleted.loginpemakai_id
     LEFT JOIN pegawai_m peg_deleted ON COALESCE(batal_order.peg_menyetujui_id, leg_deleted.pegawai_id) = peg_deleted.pegawai_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 12
UNION ALL
 SELECT 'TINDAKAN'::text AS tipe_instruksi,
    NULL::integer AS instruksi_id,
    NULL::integer AS cppt_id,
    tindakanpelayanan_t.keterangantindakan AS catatan_instruksi,
    tindakanpelayanan_t.tindakanpelayanan_id AS instruksitindakan_id,
    tindakanpelayanan_t.tgl_tindakan AS tgl_instruksi,
    tindakanpelayanan_t.daftartindakan_id,
    concat(daftartindakan_m.daftartindakan_nama, ' - ',
        CASE
            WHEN tindakanpelayanan_t.cyto_tindakan = true THEN 'Cyto - '::text
            ELSE NULL::text
        END, tindakanpelayanan_t.qty_tindakan) AS instruksi,
    NULL::character varying AS paket,
    NULL::text AS tindakan,
    tindakanpelayanan_t.qty_tindakan AS qty,
    tindakanpelayanan_t.cyto_tindakan AS is_cyto,
    NULL::double precision AS qty_sisa,
    tindakanpelayanan_t.dokterpenanggungjawab_id AS dokter_id,
    dokter.nama_pegawai AS dokter,
    NULL::character varying AS status_implementasi,
    NULL::character varying AS status,
    tindakanpelayanan_t.is_deleted AS instruksi_deleted,
    tindakanpelayanan_t.is_deleted AS tindakan_deleted,
    daftartindakan_m.daftartindakan_nama AS tindakaninstruksi_nama,
        CASE
            WHEN tindakanpelayanan_t.cyto_tindakan = true THEN 'Cyto'::text
            ELSE 'Non Cyto'::text
        END AS ket_cyto,
    NULL::character varying AS ket_racik_nama,
    NULL::character varying AS ket_racik,
    NULL::integer AS cpptpegawai_id,
    NULL::boolean AS is_verifikasi_dpjp,
    array_to_json(NULL::character varying[]) AS daftar_paket,
    tindakanpelayanan_t.tgl_tindakan AS tanggal_terapi,
    'TINDAKANBMHP'::text AS grouping_tipe,
    true AS is_telah_implementasi,
    ruangan_m.ruangan_nama AS ruangan_pertindakan,
    NULL::json AS bmhp_tindakandetail,
    tindakanpelayanan_t.created_date AS tanggal_input,
    tindakanpelayanan_t.pendaftaran_id,
    tindakanpelayanan_t.pasienadmisi_id,
    daftartindakan_m.kelompoktindakan_id,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
        CASE pendaftaran.status_bayar
            WHEN 348 THEN true
            ELSE false
        END AS is_bayar,
    NULL::integer AS pasienkirimkeunitlain_id,
    COALESCE(tindakanpelayanan_t.alasan_batal) AS alasan_batal,
    COALESCE(pegawai_hapus.pegawai_id) AS pegawai_hapus_id,
    COALESCE(pegawai_hapus.nama_pegawai) AS pegawai_hapus_nama,
    COALESCE(tindakanpelayanan_t.deleted_date) AS tgl_batal,
    tindakanpelayanan_t.is_penatajasa,
        CASE
            WHEN pendaftaran.pasienadmisi_id IS NOT NULL THEN
            CASE
                WHEN pendaftaran.is_stopakomodasi IS TRUE THEN true
                ELSE false
            END
            ELSE
            CASE
                WHEN pendaftaran.status_periksa::text = '4'::text THEN true
                WHEN pendaftaran.status_periksa::text = '433'::text THEN true
                ELSE false
            END
        END AS is_pulang,
    NULL::text AS status_penunjang,
    NULL::text AS status_penunjang_nama,
    NULL::text AS noresep,
    NULL::timestamp(6) without time zone AS tgl_resep_dibuat,
    NULL::integer AS satuankecil_id,
    NULL::text AS satuankecil_nama
   FROM tindakanpelayanan_t
     JOIN ( SELECT pendaftaran_t.status_bayar,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.status_periksa,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.is_stopakomodasi
           FROM pendaftaran_t) pendaftaran ON tindakanpelayanan_t.pendaftaran_id = pendaftaran.pendaftaran_id
     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN pegawai_m dokter ON tindakanpelayanan_t.dokterpenanggungjawab_id = dokter.pegawai_id
     JOIN ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON tindakanpelayanan_t.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT loginpemakai_k.loginpemakai_id,
            pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai
           FROM loginpemakai_k
             JOIN pegawai_m ON loginpemakai_k.pegawai_id = pegawai_m.pegawai_id) pegawai_hapus ON tindakanpelayanan_t.deleted_by = pegawai_hapus.loginpemakai_id
  WHERE tindakanpelayanan_t.is_penatajasa IS TRUE
UNION ALL
 SELECT 'BMHP'::text AS tipe_instruksi,
    NULL::integer AS instruksi_id,
    NULL::integer AS cppt_id,
    obatalkespasien_t.keterangan AS catatan_instruksi,
    obatalkespasien_t.obatalkespasien_id AS instruksitindakan_id,
    obatalkespasien_t.tglpelayanan AS tgl_instruksi,
    obatalkespasien_t.obatalkes_id AS daftartindakan_id,
    concat(obatalkes_m.obatalkes_nama, '-'::text || obatalkespasien_t.qty_oa) AS instruksi,
    NULL::character varying AS paket,
    obatalkes_m.obatalkes_nama AS tindakan,
    obatalkespasien_t.qty_oa AS qty,
    NULL::boolean AS is_cyto,
    NULL::double precision AS qty_sisa,
    obatalkespasien_t.pegawai_id AS dokter_id,
    pegawai_m.nama_pegawai AS dokter,
    NULL::character varying AS status_implementasi,
    NULL::character varying AS status,
    obatalkespasien_t.is_deleted AS instruksi_deleted,
    obatalkespasien_t.is_deleted AS tindakan_deleted,
    obatalkes_m.obatalkes_nama AS tindakaninstruksi_nama,
    NULL::text AS ket_cyto,
    NULL::character varying AS ket_racik_nama,
    NULL::character varying AS ket_racik,
    obatalkespasien_t.pegawai_id AS cpptpegawai_id,
    NULL::boolean AS is_verifikasi_dpjp,
    array_to_json(NULL::character varying[]) AS daftar_paket,
    obatalkespasien_t.tglpelayanan AS tanggal_terapi,
    'TINDAKANBMHP'::text AS grouping_tipe,
    true AS is_telah_implementasi,
    ruangan_m.ruangan_nama AS ruangan_pertindakan,
    NULL::json AS bmhp_tindakandetail,
    obatalkespasien_t.created_date AS tanggal_input,
    obatalkespasien_t.pendaftaran_id,
    obatalkespasien_t.pasienadmisi_id,
    NULL::integer AS kelompoktindakan_id,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
        CASE pendaftaran.status_bayar
            WHEN 348 THEN true
            ELSE false
        END AS is_bayar,
    NULL::integer AS pasienkirimkeunitlain_id,
    obatalkespasien_t.alasan_batal,
    peg_deleted.pegawai_id AS pegawai_hapus_id,
    peg_deleted.nama_pegawai AS pegawai_hapus_nama,
    obatalkespasien_t.deleted_date AS tgl_batal,
    obatalkespasien_t.is_penatajasa,
        CASE
            WHEN pendaftaran.pasienadmisi_id IS NOT NULL THEN
            CASE
                WHEN pendaftaran.is_stopakomodasi IS TRUE THEN true
                ELSE false
            END
            ELSE
            CASE
                WHEN pendaftaran.status_periksa::text = '4'::text THEN true
                WHEN pendaftaran.status_periksa::text = '433'::text THEN true
                ELSE false
            END
        END AS is_pulang,
    NULL::text AS status_penunjang,
    NULL::text AS status_penunjang_nama,
    NULL::text AS noresep,
    NULL::timestamp(6) without time zone AS tgl_resep_dibuat,
    NULL::integer AS satuankecil_id,
    NULL::text AS satuankecil_nama
   FROM obatalkespasien_t
     JOIN ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.status_bayar,
            pendaftaran_t.status_periksa,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.is_stopakomodasi
           FROM pendaftaran_t) pendaftaran ON obatalkespasien_t.pendaftaran_id = pendaftaran.pendaftaran_id
     JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN pegawai_m ON obatalkespasien_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN loginpemakai_k leg_deleted ON obatalkespasien_t.deleted_by = leg_deleted.loginpemakai_id
     LEFT JOIN pegawai_m peg_deleted ON leg_deleted.pegawai_id = peg_deleted.pegawai_id
  WHERE obatalkespasien_t.is_penatajasa IS TRUE
UNION ALL
 SELECT 'DIET'::text AS tipe_instruksi,
    permintaanmakan_t.permintaaanmakan_id AS instruksi_id,
    NULL::integer AS cppt_id,
    permintaanmakan_t.catatan_diet AS catatan_instruksi,
    permintaanmakandetail_t.permintaanmakandetail_id AS instruksitindakan_id,
    permintaanmakan_t.tgl_permintaanmakan AS tgl_instruksi,
    daftartindakan_m.daftartindakan_id,
    concat(daftartindakan_m.daftartindakan_nama, ' - ', permintaanmakandetail_t.jumlah) AS instruksi,
    NULL::character varying AS paket,
    NULL::text AS tindakan,
    permintaanmakandetail_t.jumlah AS qty,
    false AS is_cyto,
    NULL::double precision AS qty_sisa,
    dokter.pegawai_id AS dokter_id,
    dokter.nama_pegawai AS dokter,
        CASE
            WHEN permintaanmakandetail_t.permintaanmakandetail_id IS NOT NULL THEN '1'::text
            ELSE '0'::text
        END AS status_implementasi,
        CASE
            WHEN permintaanmakandetail_t.permintaanmakandetail_id IS NOT NULL THEN 'Implementasi'::text
            ELSE 'Belum Implementasi'::text
        END AS status,
    permintaanmakan_t.is_deleted AS instruksi_deleted,
    permintaanmakandetail_t.is_deleted AS tindakan_deleted,
    daftartindakan_m.daftartindakan_nama AS tindakaninstruksi_nama,
    'Non Cyto'::text AS ket_cyto,
    NULL::character varying AS ket_racik_nama,
    NULL::character varying AS ket_racik,
    dokter.pegawai_id AS cpptpegawai_id,
    NULL::boolean AS is_verifikasi_dpjp,
    array_to_json(NULL::character varying[]) AS daftar_paket,
    permintaanmakan_t.tgl_permintaanmakan AS tanggal_terapi,
    'TINDAKANDIET'::text AS grouping_tipe,
        CASE
            WHEN permintaanmakandetail_t.permintaanmakandetail_id IS NOT NULL THEN true
            ELSE false
        END AS is_telah_implementasi,
    ruangan_m.ruangan_nama AS ruangan_pertindakan,
    NULL::json AS bmhp_tindakandetail,
    permintaanmakan_t.created_date AS tanggal_input,
    permintaanmakan_t.pendaftaran_id,
    permintaanmakan_t.pasienadmisi_id,
    daftartindakan_m.kelompoktindakan_id,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
        CASE pendaftaran.status_bayar
            WHEN 348 THEN true
            ELSE false
        END AS is_bayar,
    NULL::integer AS pasienkirimkeunitlain_id,
    permintaanmakan_t.alasan_pembatalan AS alasan_batal,
    NULL::integer AS pegawai_hapus_id,
    NULL::character varying AS pegawai_hapus_nama,
    NULL::timestamp without time zone AS tgl_batal,
    false AS is_penatajasa,
        CASE
            WHEN pendaftaran.pasienadmisi_id IS NOT NULL THEN
            CASE
                WHEN pendaftaran.is_stopakomodasi IS TRUE THEN true
                ELSE false
            END
            ELSE
            CASE
                WHEN pendaftaran.status_periksa::text = '4'::text THEN true
                WHEN pendaftaran.status_periksa::text = '433'::text THEN true
                ELSE false
            END
        END AS is_pulang,
    NULL::text AS status_penunjang,
    NULL::text AS status_penunjang_nama,
    NULL::text AS noresep,
    NULL::timestamp(6) without time zone AS tgl_resep_dibuat,
    NULL::integer AS satuankecil_id,
    NULL::text AS satuankecil_nama
   FROM permintaanmakan_t
     LEFT JOIN permintaanmakandetail_t ON permintaanmakan_t.permintaaanmakan_id = permintaanmakandetail_t.permintaanmakan_id
     LEFT JOIN makanandiet_m ON permintaanmakandetail_t.makanandiet_id = makanandiet_m.makanandiet_id
     LEFT JOIN daftartindakan_m ON makanandiet_m.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pegawai_id,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.is_stopakomodasi,
            pendaftaran_t.tgl_stopakomodasi,
            pendaftaran_t.status_periksa,
            pendaftaran_t.ruangan_id,
            pendaftaran_t.status_bayar
           FROM pendaftaran_t) pendaftaran ON permintaanmakan_t.pendaftaran_id = pendaftaran.pendaftaran_id
     LEFT JOIN ( SELECT pasienadmisi_t.pasienadmisi_id,
            pasienadmisi_t.pegawai_id,
            pasienadmisi_t.ruangan_id
           FROM pasienadmisi_t) pasienadmisi ON permintaanmakan_t.pasienadmisi_id = pasienadmisi.pasienadmisi_id
     JOIN pegawai_m dokter ON COALESCE(pasienadmisi.pegawai_id, pendaftaran.pegawai_id) = dokter.pegawai_id
     LEFT JOIN ruangan_m ON COALESCE(pasienadmisi.ruangan_id, pendaftaran.ruangan_id) = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
UNION ALL
 SELECT 'FISIOTERAPI'::text AS tipe_instruksi,
    programterapi_t.programterapi_id AS instruksi_id,
    soapfisioterapi_t.soapfisioterapi_id AS cppt_id,
    programterapi_t.catatan AS catatan_instruksi,
    programterapidetail_t.programterapidetail_id AS instruksitindakan_id,
    programterapi_t.tgl_permintaan AS tgl_instruksi,
    daftartindakan_m.daftartindakan_id,
    concat(daftartindakan_m.daftartindakan_nama, ' - ', programterapidetail_t.frekuensi) AS instruksi,
    NULL::character varying AS paket,
    NULL::text AS tindakan,
    programterapidetail_t.frekuensi AS qty,
    false AS is_cyto,
    NULL::double precision AS qty_sisa,
    dokter.pegawai_id AS dokter_id,
    dokter.nama_pegawai AS dokter,
        CASE
            WHEN tindakanpelayanan.tindakanpelayanan_id IS NOT NULL THEN '1'::text
            ELSE '0'::text
        END AS status_implementasi,
        CASE
            WHEN tindakanpelayanan.tindakanpelayanan_id IS NOT NULL THEN 'Sudah Implementasi'::text
            ELSE 'Belum Implementasi'::text
        END AS status,
    tindakanpelayanan.is_deleted AS instruksi_deleted,
    tindakanpelayanan.is_deleted AS tindakan_deleted,
    daftartindakan_m.daftartindakan_nama AS tindakaninstruksi_nama,
    'Non Cyto'::text AS ket_cyto,
    NULL::character varying AS ket_racik_nama,
    NULL::character varying AS ket_racik,
    dokter.pegawai_id AS cpptpegawai_id,
    NULL::boolean AS is_verifikasi_dpjp,
    array_to_json(NULL::character varying[]) AS daftar_paket,
    programterapi_t.tgl_permintaan AS tanggal_terapi,
    'PENUNJANGFISIO'::text AS grouping_tipe,
        CASE
            WHEN tindakanpelayanan.tindakanpelayanan_id IS NOT NULL THEN true
            ELSE false
        END AS is_telah_implementasi,
    ruangan_m.ruangan_nama AS ruangan_pertindakan,
    NULL::json AS bmhp_tindakandetail,
    programterapi_t.created_date AS tanggal_input,
    programterapi_t.pendaftaran_id,
    pendaftaran.pasienadmisi_id,
    daftartindakan_m.kelompoktindakan_id,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
        CASE pendaftaran.status_bayar
            WHEN 348 THEN true
            ELSE false
        END AS is_bayar,
    NULL::integer AS pasienkirimkeunitlain_id,
    tindakanpelayanan.alasan_batal,
    tindakanpelayanan.pegawai_id AS pegawai_hapus_id,
    tindakanpelayanan.nama_pegawai AS pegawai_hapus_nama,
    tindakanpelayanan.deleted_date AS tgl_batal,
    tindakanpelayanan.is_penatajasa,
        CASE
            WHEN pendaftaran.pasienadmisi_id IS NOT NULL THEN
            CASE
                WHEN pendaftaran.is_stopakomodasi IS TRUE THEN true
                ELSE false
            END
            ELSE
            CASE
                WHEN pendaftaran.status_periksa::text = '4'::text THEN true
                WHEN pendaftaran.status_periksa::text = '433'::text THEN true
                ELSE false
            END
        END AS is_pulang,
    NULL::text AS status_penunjang,
    NULL::text AS status_penunjang_nama,
    NULL::text AS noresep,
    NULL::timestamp(6) without time zone AS tgl_resep_dibuat,
    NULL::integer AS satuankecil_id,
    NULL::text AS satuankecil_nama
   FROM programterapi_t
     JOIN ( SELECT detail.programterapi_id,
            detail.daftartindakan_id,
            detail.frekuensi,
            detail.programterapidetail_id
           FROM programterapidetail_t detail) programterapidetail_t ON programterapi_t.programterapi_id = programterapidetail_t.programterapi_id
     JOIN ( SELECT tindakan.daftartindakan_id,
            tindakan.daftartindakan_nama,
            tindakan.kelompoktindakan_id
           FROM daftartindakan_m tindakan) daftartindakan_m ON programterapidetail_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN ( SELECT soapterapi.soapfisioterapi_id,
            soapterapi.programterapi_id,
            soapterapi.subject,
            soapterapi.object,
            soapterapi.assesment,
            soapterapi.planning
           FROM soapfisioterapi_t soapterapi) soapfisioterapi_t ON programterapi_t.programterapi_id = soapfisioterapi_t.programterapi_id
     JOIN ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pegawai_id,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.is_stopakomodasi,
            pendaftaran_t.tgl_stopakomodasi,
            pendaftaran_t.status_periksa,
            pendaftaran_t.ruangan_id,
            pendaftaran_t.status_bayar
           FROM pendaftaran_t) pendaftaran ON programterapi_t.pendaftaran_id = pendaftaran.pendaftaran_id
     LEFT JOIN ( SELECT pasienadmisi_t.pasienadmisi_id,
            pasienadmisi_t.pegawai_id,
            pasienadmisi_t.ruangan_id
           FROM pasienadmisi_t) pasienadmisi ON pendaftaran.pasienadmisi_id = pasienadmisi.pasienadmisi_id
     JOIN ( SELECT pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai
           FROM pegawai_m) dokter ON COALESCE(pasienadmisi.pegawai_id, pendaftaran.pegawai_id) = dokter.pegawai_id
     LEFT JOIN ruangan_m ON COALESCE(pasienadmisi.ruangan_id, pendaftaran.ruangan_id) = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT tindakanpelayanan_t.tindakanpelayanan_id,
            tindakanpelayanan_t.programterapi_id,
            tindakanpelayanan_t.daftartindakan_id,
            tindakanpelayanan_t.is_deleted,
            tindakanpelayanan_t.alasan_batal,
            tindakanpelayanan_t.deleted_date,
            pegawai.pegawai_id,
            pegawai.nama_pegawai,
            tindakanpelayanan_t.is_penatajasa
           FROM tindakanpelayanan_t
             LEFT JOIN loginpemakai_k ON tindakanpelayanan_t.deleted_by = loginpemakai_k.loginpemakai_id
             LEFT JOIN ( SELECT pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai
                   FROM pegawai_m) pegawai ON loginpemakai_k.pegawai_id = pegawai.pegawai_id) tindakanpelayanan ON programterapi_t.programterapi_id = tindakanpelayanan.programterapi_id AND programterapidetail_t.daftartindakan_id = tindakanpelayanan.daftartindakan_id
UNION ALL
 SELECT 'UDD'::text AS tipe_instruksi,
    udd_t.udd_id AS instruksi_id,
    NULL::integer AS cppt_id,
    udd_detail_t.catatan_dokter AS catatan_instruksi,
    udd_detail_t.udd_detail_id AS instruksitindakan_id,
    udd_t.tgl_order AS tgl_instruksi,
    udd_detail_t.obatalkes_id AS daftartindakan_id,
    concat(obatalkes_m.obatalkes_nama, ' - ', udd_detail_t.qty) AS instruksi,
    NULL::character varying AS paket,
    obatalkes_m.obatalkes_nama AS tindakan,
    udd_detail_t.qty,
    false AS is_cyto,
    NULL::double precision AS qty_sisa,
    dokter.pegawai_id AS dokter_id,
    dokter.nama_pegawai AS dokter,
        CASE
            WHEN udd_t.status_udd = 1032 THEN '1'::text
            ELSE '0'::text
        END AS status_implementasi,
        CASE
            WHEN udd_t.status_udd = 1032 THEN 'Implementasi'::text
            ELSE 'Belum Implementasi'::text
        END AS status,
    udd_t.is_deleted AS instruksi_deleted,
    udd_detail_t.is_deleted AS tindakan_deleted,
    obatalkes_m.obatalkes_nama AS tindakaninstruksi_nama,
    'Non Cyto'::text AS ket_cyto,
    NULL::character varying AS ket_racik_nama,
    NULL::character varying AS ket_racik,
    dokter.pegawai_id AS cpptpegawai_id,
    NULL::boolean AS is_verifikasi_dpjp,
    array_to_json(NULL::character varying[]) AS daftar_paket,
    udd_t.tgl_order AS tanggal_terapi,
    'OBATUDD'::text AS grouping_tipe,
        CASE
            WHEN udd_t.status_udd = 1032 THEN true
            ELSE false
        END AS is_telah_implementasi,
    ruangan_m.ruangan_nama AS ruangan_pertindakan,
    NULL::json AS bmhp_tindakandetail,
    udd_t.created_date AS tanggal_input,
    udd_t.pendaftaran_id,
    udd_t.pasienadmisi_id,
    obatalkes_m.jenisobatalkes_id AS kelompoktindakan_id,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
        CASE pendaftaran.status_bayar
            WHEN 348 THEN true
            ELSE false
        END AS is_bayar,
    NULL::integer AS pasienkirimkeunitlain_id,
    NULL::text AS alasan_batal,
    NULL::integer AS pegawai_hapus_id,
    NULL::character varying AS pegawai_hapus_nama,
    NULL::timestamp without time zone AS tgl_batal,
    false AS is_penatajasa,
        CASE
            WHEN pendaftaran.pasienadmisi_id IS NOT NULL THEN
            CASE
                WHEN pendaftaran.is_stopakomodasi IS TRUE THEN true
                ELSE false
            END
            ELSE
            CASE
                WHEN pendaftaran.status_periksa::text = '4'::text THEN true
                WHEN pendaftaran.status_periksa::text = '433'::text THEN true
                ELSE false
            END
        END AS is_pulang,
    NULL::text AS status_penunjang,
    NULL::text AS status_penunjang_nama,
    udd_t.no_udd AS noresep,
    udd_t.tgl_order AS tgl_resep_dibuat,
    NULL::integer AS satuankecil_id,
    NULL::text AS satuankecil_nama
   FROM udd_t
     JOIN ( SELECT a.udd_id,
            a.udd_detail_id,
            a.catatan_dokter,
            a.obatalkes_id,
            a.qty,
            a.is_deleted
           FROM udd_detail_t a) udd_detail_t ON udd_t.udd_id = udd_detail_t.udd_id
     JOIN ( SELECT a.obatalkes_id,
            a.obatalkes_kode,
            a.obatalkes_nama,
            a.jenisobatalkes_id
           FROM obatalkes_m a) obatalkes_m ON udd_detail_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN ( SELECT pendaftaran_t.pendaftaran_id,
            pendaftaran_t.pegawai_id,
            pendaftaran_t.pasienadmisi_id,
            pendaftaran_t.is_stopakomodasi,
            pendaftaran_t.tgl_stopakomodasi,
            pendaftaran_t.status_periksa,
            pendaftaran_t.ruangan_id,
            pendaftaran_t.status_bayar
           FROM pendaftaran_t) pendaftaran ON udd_t.pendaftaran_id = pendaftaran.pendaftaran_id
     JOIN ( SELECT pasienadmisi_t.pasienadmisi_id,
            pasienadmisi_t.pegawai_id,
            pasienadmisi_t.ruangan_id
           FROM pasienadmisi_t) pasienadmisi ON udd_t.pasienadmisi_id = pasienadmisi.pasienadmisi_id
     JOIN pegawai_m dokter ON COALESCE(pasienadmisi.pegawai_id, pendaftaran.pegawai_id) = dokter.pegawai_id
     LEFT JOIN ruangan_m ON COALESCE(pasienadmisi.ruangan_id, pendaftaran.ruangan_id) = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id;
");

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220511_061403_migrate_infoinstruksi_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220511_061403_migrate_infoinstruksi_v cannot be reverted.\n";

        return false;
    }
    */
}
