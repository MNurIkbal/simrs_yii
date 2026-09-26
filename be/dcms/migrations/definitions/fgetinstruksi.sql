CREATE OR REPLACE FUNCTION "public"."fgetinstruksi"("xpasien_id" int4=0, "xstart_date" date=NULL::date, "xend_date" date=NULL::date, "xjenis_deskripsi" text=NULL::text, "xinstruksi" text=NULL::text, "xlimit" int4=10, "xoffset" int4=0)
  RETURNS TABLE("tipe_instruksi" text, "instruksi_id" int4, "cppt_id" int4, "catatan_instruksi" text, "instruksitindakan_id" int4, "tgl_instruksi" timestamp, "daftartindakan_id" int4, "instruksi" text, "paket" varchar, "tindakan" text, "qty" float8, "is_cyto" bool, "qty_sisa" float8, "dokter_id" int4, "dokter" varchar, "status_implementasi" int4, "status" varchar, "instruksi_deleted" bool, "tindakan_deleted" bool, "tindakaninstruksi_nama" varchar, "ket_cyto" text, "ket_racik_nama" varchar, "ket_racik" varchar, "cpptpegawai_id" int4, "is_verifikasi_dpjp" bool, "daftar_paket" json, "tanggal_terapi" timestamp, "grouping_tipe" text, "is_telah_implementasi" bool, "ruangan_pertindakan" varchar, "bmhp_tindakandetail" json, "tanggal_input" timestamp, "pendaftaran_id" int4, "pasienadmisi_id" int4, "kelompoktindakan_id" int4, "instalasi_id" int4, "instalasi_nama" varchar, "is_bayar" bool, "pasienkirimkeunitlain_id" int4, "alasan_batal" text, "pegawai_hapus_id" int4, "pegawai_hapus_nama" varchar, "tgl_batal" timestamp, "is_penatajasa" bool, "is_pulang" bool, "status_penunjang" text, "status_penunjang_nama" text, "noresep" text, "tgl_resep_dibuat" timestamp, "satuankecil_id" int4, "satuankecil_nama" text, "pasien_id" int4, "no_pendaftaran" varchar, "tgl_pendaftaran" timestamp, "jenis_deskripsi" text, "status_bmhp_id" int4, "status_bmhp_nama" text, "ruangan_pertindakan_id" int4) AS $BODY$ 
BEGIN

	IF(xstart_date IS NULL)
	THEN 
		xstart_date := CURRENT_DATE - '3 month'::interval;
-- 		xstart_date := '2022-01-01';
	END IF;

	IF(xend_date IS NULL)
	THEN 
		xend_date := CURRENT_DATE;
	END IF;

RETURN QUERY
SELECT *FROM (
	SELECT 'TINDAKAN'::text AS tipe_instruksi,
    instruksi_t.instruksi_id::int4,
    instruksi_t.cppt_id,
    instruksi_t.catatan_instruksi,
    instruksitindakan_t.instruksitindakan_id::int4,
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
    instruksitindakan_t.dokterdpjp_id::int4,
    dokter.nama_pegawai AS dokter,
    instruksitindakan_t.status_implementasi::int4,
    status_implementasi.lookup_name AS status,
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
    NULL::int4 AS pasienkirimkeunitlain_id,
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
    NULL::int4 AS satuankecil_id,
    NULL::text AS satuankecil_nama,
    pendaftaran_t.pasien_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    concat('TINDAKANBMHP TINDAKAN') AS jenis_deskripsi,
    NULL::int4 AS status_bmhp_id,
    NULL::text AS status_bmhp_nama,
		ruangan_m.ruangan_id
   FROM (	 
		SELECT
			a.instruksi_id::int4,
			a.cppt_id,
			a.catatan_instruksi,
			a.is_deleted,
			a.tgl_instruksi ,
			a.created_date
		FROM instruksi_t a
	 ) instruksi_t
     JOIN ( SELECT a.instruksi_id::int4,
            a.instruksitindakan_id,
            a.pendaftaran_id,
            a.tgl_tindakan,
            a.daftartindakan_id,
            a.is_cyto,
            a.is_concern,
            a.qty,
            a.qty_sisa,
            a.dokterdpjp_id,
            a.status_implementasi::int4,
            a.alasan_batal,
            a.deleted_date,
            a.ruangan_id,
            a.is_deleted,
            a.deleted_by
           FROM instruksitindakan_t a
					 ORDER BY created_date DESC
					 ) instruksitindakan_t ON instruksi_t.instruksi_id::int4 = instruksitindakan_t.instruksi_id::int4
     JOIN ( SELECT a.kelompoktindakan_id,
            a.daftartindakan_id,
            a.daftartindakan_nama
           FROM daftartindakan_m a) daftartindakan_m ON instruksitindakan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) dokter ON instruksitindakan_t.dokterdpjp_id = dokter.pegawai_id
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
            a.pendaftaran_id,
            a.pasien_id,
            a.no_pendaftaran,
            a.tgl_pendaftaran
           FROM pendaftaran_t a) pendaftaran_t ON cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT a.pendaftaran_id,
            a.instruksitindakan_id,
            a.daftartindakan_id,
            peg_deleted.pegawai_id AS pegawai_hapus_id,
            peg_deleted.nama_pegawai AS pegawai_hapus_nama,
            a.deleted_date AS tgl_batal,
            a.alasan_batal,
            a.is_penatajasa
           FROM tindakanpelayanan_t a
             LEFT JOIN ( SELECT loginpemakai_k.loginpemakai_id,
                    loginpemakai_k.pegawai_id
                   FROM loginpemakai_k) leg_deleted ON a.deleted_by = leg_deleted.loginpemakai_id
             LEFT JOIN ( SELECT pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai
                   FROM pegawai_m) peg_deleted ON leg_deleted.pegawai_id = peg_deleted.pegawai_id
          WHERE a.is_deleted IS TRUE) tindakan_deleted ON instruksitindakan_t.pendaftaran_id = tindakan_deleted.pendaftaran_id AND instruksitindakan_t.instruksitindakan_id = tindakan_deleted.instruksitindakan_id AND instruksitindakan_t.daftartindakan_id = tindakan_deleted.daftartindakan_id
     LEFT JOIN ( SELECT a.loginpemakai_id,
            pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai
           FROM loginpemakai_k a
             JOIN ( SELECT a1.pegawai_id,
                    a1.nama_pegawai
                   FROM pegawai_m a1) pegawai_m ON a.pegawai_id = pegawai_m.pegawai_id) pegawai_hapus ON instruksitindakan_t.deleted_by = pegawai_hapus.loginpemakai_id
     LEFT JOIN ( SELECT a.lookup_id,
            a.lookup_name
           FROM lookup_m a) status_implementasi ON instruksitindakan_t.status_implementasi::int4 = status_implementasi.lookup_id
		 WHERE pendaftaran_t.pasien_id = xpasien_id
		 AND instruksi_t.tgl_instruksi::DATE BETWEEN xstart_date AND xend_date
-- UNION ALL
--  SELECT 'PAKET'::text AS tipe_instruksi,
--     instruksi_t.instruksi_id::int4,
--     instruksi_t.cppt_id,
--     instruksi_t.catatan_instruksi,
--     instruksitindakan_t.instruksitindakan_id,
--     instruksitindakan_t.tgl_tindakan AS tgl_instruksi,
--     instruksitindakan_t.tipepaket_id AS daftartindakan_id,
--     concat(tipepaket_m.tipepaket_nama, ' - ',
--         CASE
--             WHEN instruksitindakan_t.is_cyto = true THEN 'Cyto - '::text
--             ELSE NULL::text
--         END, ' - ',
--         CASE
--             WHEN instruksitindakan_t.is_concern = true THEN 'Informed Consent - '::text
--             ELSE NULL::text
--         END, ' - '::text || instruksitindakan_t.qty) AS instruksi,
--     tipepaket_m.tipepaket_nama AS paket,
--     NULL::text AS tindakan,
--     instruksitindakan_t.qty,
--     instruksitindakan_t.is_cyto,
--     instruksitindakan_t.qty_sisa,
--     instruksitindakan_t.dokterdpjp_id AS dokter_id,
--     dokter.nama_pegawai AS dokter,
--     instruksitindakan_t.status_implementasi::int4,
--     status.lookup_name AS status,
--     instruksi_t.is_deleted AS instruksi_deleted,
--     instruksitindakan_t.is_deleted AS tindakan_deleted,
--     tipepaket_m.tipepaket_nama AS tindakaninstruksi_nama,
--         CASE
--             WHEN instruksitindakan_t.is_cyto = true THEN 'Cyto'::text
--             ELSE 'Non Cyto'::text
--         END AS ket_cyto,
--     NULL::character varying AS ket_racik_nama,
--     NULL::character varying AS ket_racik,
--     cppt_t.pegawai_id AS cpptpegawai_id,
--     cppt_t.is_verifikasi AS is_verifikasi_dpjp,
--     array_to_json(ARRAY( SELECT daftartindakan_m.daftartindakan_nama
--            FROM paketpelayanan_mp
--              LEFT JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
--           WHERE paketpelayanan_mp.tipepaket_id = instruksitindakan_t.tipepaket_id)) AS daftar_paket,
--     instruksi_t.tgl_instruksi AS tanggal_terapi,
--     'TINDAKANBMHP'::text AS grouping_tipe,
--         CASE
--             WHEN instruksitindakan_t.status_implementasi::int4::text = '455'::character varying::text THEN true
--             ELSE false
--         END AS is_telah_implementasi,
--     ruangan_m.ruangan_nama AS ruangan_pertindakan,
--     NULL::json AS bmhp_tindakandetail,
--     instruksi_t.created_date AS tanggal_input,
--     cppt_t.pendaftaran_id,
--     cppt_t.pasienadmisi_id,
--     NULL::int4 AS kelompoktindakan_id,
--     instalasi_m.instalasi_id,
--     instalasi_m.instalasi_nama,
--         CASE pendaftaran_t.status_bayar
--             WHEN 348 THEN true
--             ELSE false
--         END AS is_bayar,
--     NULL::int4 AS pasienkirimkeunitlain_id,
--     COALESCE(instruksitindakan_t.alasan_batal, tindakan_deleted.alasan_batal) AS alasan_batal,
--     COALESCE(pegawai_hapus.pegawai_id, tindakan_deleted.pegawai_hapus_id) AS pegawai_hapus_id,
--     COALESCE(pegawai_hapus.nama_pegawai, tindakan_deleted.pegawai_hapus_nama) AS pegawai_hapus_nama,
--     COALESCE(instruksitindakan_t.deleted_date, tindakan_deleted.tgl_batal) AS tgl_batal,
--     COALESCE(tindakan_deleted.is_penatajasa, false) AS is_penatajasa,
--         CASE
--             WHEN cppt_t.pasienadmisi_id IS NOT NULL THEN
--             CASE
--                 WHEN pendaftaran_t.is_stopakomodasi IS TRUE THEN true
--                 ELSE false
--             END
--             ELSE
--             CASE
--                 WHEN pendaftaran_t.status_periksa::text = '4'::text THEN true
--                 WHEN pendaftaran_t.status_periksa::text = '433'::text THEN true
--                 ELSE false
--             END
--         END AS is_pulang,
--     NULL::text AS status_penunjang,
--     NULL::text AS status_penunjang_nama,
--     NULL::text AS noresep,
--     NULL::timestamp(6) without time zone AS tgl_resep_dibuat,
--     NULL::int4 AS satuankecil_id,
--     NULL::text AS satuankecil_nama,
--     pendaftaran_t.pasien_id,
--     pendaftaran_t.no_pendaftaran,
--     pendaftaran_t.tgl_pendaftaran,
--     concat('TINDAKANBMHP PAKET') AS jenis_deskripsi,
--     NULL::int4 AS status_bmhp_id,
--     NULL::text AS status_bmhp_nama
--    FROM instruksi_t
--      JOIN ( SELECT b.instruksi_id::int4,
--             b.instruksitindakan_id,
--             b.pendaftaran_id,
--             b.tgl_tindakan,
--             b.tipepaket_id,
--             b.is_cyto,
--             b.is_concern,
--             b.qty,
--             b.qty_sisa,
--             b.dokterdpjp_id,
--             b.status_implementasi::int4,
--             b.alasan_batal,
--             b.deleted_date,
--             b.ruangan_id,
--             b.is_deleted,
--             b.deleted_by
--            FROM instruksitindakan_t b) instruksitindakan_t ON instruksi_t.instruksi_id::int4 = instruksitindakan_t.instruksi_id::int4
--      JOIN ( SELECT b.tipepaket_id,
--             b.tipepaket_nama
--            FROM tipepaket_m b) tipepaket_m ON instruksitindakan_t.tipepaket_id = tipepaket_m.tipepaket_id
--      JOIN ( SELECT b.pegawai_id,
--             b.nama_pegawai
--            FROM pegawai_m b) dokter ON instruksitindakan_t.dokterdpjp_id = dokter.pegawai_id
--      LEFT JOIN ( SELECT b.cppt_id,
--             b.pegawai_id,
--             b.is_verifikasi,
--             b.pendaftaran_id,
--             b.pasienadmisi_id
--            FROM cppt_t b) cppt_t ON cppt_t.cppt_id = instruksi_t.cppt_id
--      JOIN ( SELECT b.ruangan_id,
--             b.ruangan_nama,
--             b.instalasi_id
--            FROM ruangan_m b) ruangan_m ON instruksitindakan_t.ruangan_id = ruangan_m.ruangan_id
--      JOIN ( SELECT b.instalasi_id,
--             b.instalasi_nama
--            FROM instalasi_m b) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
--      LEFT JOIN ( SELECT b.status_bayar,
--             b.is_stopakomodasi,
--             b.status_periksa,
--             b.pendaftaran_id,
--             b.pasien_id,
--             b.no_pendaftaran,
--             b.tgl_pendaftaran
--            FROM pendaftaran_t b) pendaftaran_t ON cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
--      LEFT JOIN ( SELECT b.pendaftaran_id,
--             b.instruksitindakan_id,
--             b.tipepaket_id,
--             peg_deleted.pegawai_id AS pegawai_hapus_id,
--             peg_deleted.nama_pegawai AS pegawai_hapus_nama,
--             b.deleted_date AS tgl_batal,
--             b.alasan_batal,
--             b.is_penatajasa
--            FROM tindakanpelayanan_t b
--              LEFT JOIN ( SELECT loginpemakai_k.loginpemakai_id,
--                     loginpemakai_k.pegawai_id
--                    FROM loginpemakai_k) leg_deleted ON b.deleted_by = leg_deleted.loginpemakai_id
--              LEFT JOIN ( SELECT pegawai_m.pegawai_id,
--                     pegawai_m.nama_pegawai
--                    FROM pegawai_m) peg_deleted ON leg_deleted.pegawai_id = peg_deleted.pegawai_id
--           WHERE b.is_deleted IS TRUE) tindakan_deleted ON instruksitindakan_t.pendaftaran_id = tindakan_deleted.pendaftaran_id AND instruksitindakan_t.instruksitindakan_id = tindakan_deleted.instruksitindakan_id AND instruksitindakan_t.tipepaket_id = tindakan_deleted.tipepaket_id
--      LEFT JOIN ( SELECT b.loginpemakai_id,
--             b.pegawai_id,
--             pegawai_m.nama_pegawai
--            FROM loginpemakai_k b
--              JOIN ( SELECT b1.pegawai_id,
--                     b1.nama_pegawai
--                    FROM pegawai_m b1) pegawai_m ON b.pegawai_id = pegawai_m.pegawai_id) pegawai_hapus ON instruksitindakan_t.deleted_by = pegawai_hapus.loginpemakai_id
--      LEFT JOIN ( SELECT b.lookup_id,
--             b.lookup_name
--            FROM lookup_m b) status ON instruksitindakan_t.status_implementasi::int4::integer = status.lookup_id
UNION ALL
 SELECT 'BMHP'::text AS tipe_instruksi,
    instruksi_t.instruksi_id::int4,
    instruksi_t.cppt_id,
    instruksi_t.catatan_instruksi,
    instruksitindakanbmhp_t.instruksitindakanbmhp_id::int4,
    instruksitindakanbmhp_t.tgl_pelayanan AS tgl_instruksi,
    instruksitindakanbmhp_t.obatalkes_id AS daftartindakan_id,
    concat(obatalkes_m.obatalkes_nama, '-'::text || instruksitindakanbmhp_t.qty) AS instruksi,
    tipepaket_m.tipepaket_nama AS paket,
    daftartindakan_m.daftartindakan_nama AS tindakan,
    instruksitindakanbmhp_t.qty,
    NULL::boolean AS is_cyto,
    instruksitindakanbmhp_t.qty_sisa,
    instruksitindakanbmhp_t.dokter_id::int4,
    dokter.nama_pegawai AS dokter,
    instruksitindakanbmhp_t.status_implementasi::int4,
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
            WHEN instruksitindakanbmhp_t.status_implementasi::int4::text = '455'::character varying::text THEN true
            ELSE false
        END AS is_telah_implementasi,
    ruangan_m.ruangan_nama AS ruangan_pertindakan,
    ( SELECT row_to_json(json_bmhp.*) AS row_to_json
           FROM ( SELECT c.instruksitindakan_id,
                    c.dokterdpjp_id,
                    c.daftartindakan_id,
                    dokterbmhp.nama_pegawai,
                    daftartindakanbmhp.daftartindakan_nama
                   FROM instruksitindakan_t c
                     LEFT JOIN ( SELECT c1.pegawai_id,
                            c1.nama_pegawai
                           FROM pegawai_m c1) dokterbmhp ON dokterbmhp.pegawai_id = c.dokterdpjp_id
                     LEFT JOIN ( SELECT c2.daftartindakan_id,
                            c2.daftartindakan_nama
                           FROM daftartindakan_m c2) daftartindakanbmhp ON daftartindakanbmhp.daftartindakan_id = c.daftartindakan_id
                  WHERE c.instruksitindakan_id = instruksitindakanbmhp_t.instruksitindakan_id) json_bmhp) AS bmhp_tindakandetail,
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
    NULL::int4 AS pasienkirimkeunitlain_id,
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
    NULL::int4 AS satuankecil_id,
    NULL::text AS satuankecil_nama,
    pendaftaran_t.pasien_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    concat('TINDAKANBMHP BMHP') AS jenis_deskripsi,
    NULL::int4 AS status_bmhp_id,
    NULL::text AS status_bmhp_nama,
		ruangan_m.ruangan_id
   FROM (	 
		SELECT
			a.instruksi_id::int4,
			a.cppt_id,
			a.catatan_instruksi,
			a.is_deleted,
			a.tgl_instruksi ,
			a.created_date
		FROM instruksi_t a
		ORDER BY tgl_instruksi DESC
	 ) instruksi_t
     JOIN ( SELECT c.instruksi_id::int4,
            c.instruksitindakan_id,
            c.instruksitindakanbmhp_id,
            c.tgl_pelayanan,
            c.obatalkes_id,
            c.daftartindakan_id,
            c.tipepaket_id,
            c.ruangan_id,
            c.qty,
            c.qty_sisa,
            c.dokter_id,
            c.status_implementasi::int4,
            c.is_deleted,
            c.deleted_by,
            c.alasan_batal,
            c.deleted_date
           FROM instruksitindakanbmhp_t c
					 ) instruksitindakanbmhp_t ON instruksi_t.instruksi_id::int4 = instruksitindakanbmhp_t.instruksi_id::int4
     JOIN ( SELECT c.obatalkes_id,
            c.obatalkes_nama,
            c.jenisobatalkes_id
           FROM obatalkes_m c) obatalkes_m ON instruksitindakanbmhp_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT c.kelompoktindakan_id,
            c.daftartindakan_id,
            c.daftartindakan_nama
           FROM daftartindakan_m c) daftartindakan_m ON instruksitindakanbmhp_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     LEFT JOIN ( SELECT c.tipepaket_id,
            c.tipepaket_nama
           FROM tipepaket_m c) tipepaket_m ON instruksitindakanbmhp_t.tipepaket_id = tipepaket_m.tipepaket_id
     LEFT JOIN ( SELECT c.pegawai_id,
            c.nama_pegawai
           FROM pegawai_m c) dokter ON instruksitindakanbmhp_t.dokter_id = dokter.pegawai_id
     LEFT JOIN ( SELECT c.cppt_id,
            c.pegawai_id,
            c.is_verifikasi,
            c.pendaftaran_id,
            c.pasienadmisi_id
           FROM cppt_t c) cppt_t ON cppt_t.cppt_id = instruksi_t.cppt_id
     LEFT JOIN ( SELECT c.ruangan_id,
            c.ruangan_nama,
            c.instalasi_id
           FROM ruangan_m c) ruangan_m ON instruksitindakanbmhp_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT c.instalasi_id,
            c.instalasi_nama
           FROM instalasi_m c) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT c.status_bayar,
            c.is_stopakomodasi,
            c.status_periksa,
            c.pendaftaran_id,
            c.pasien_id,
            c.no_pendaftaran,
            c.tgl_pendaftaran
           FROM pendaftaran_t c) pendaftaran_t ON cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT c.instruksitindakanbmhp_id,
            peg_deleted.pegawai_id AS pegawai_hapus_id,
            peg_deleted.nama_pegawai AS pegawai_hapus_nama,
            c.deleted_date AS tgl_batal,
            c.alasan_batal,
            c.is_penatajasa
           FROM obatalkespasien_t c
             LEFT JOIN ( SELECT c1.loginpemakai_id,
                    c1.pegawai_id
                   FROM loginpemakai_k c1) leg_deleted ON c.deleted_by = leg_deleted.loginpemakai_id
             LEFT JOIN ( SELECT c2.pegawai_id,
                    c2.nama_pegawai
                   FROM pegawai_m c2) peg_deleted ON leg_deleted.pegawai_id = peg_deleted.pegawai_id
          WHERE c.is_deleted IS TRUE) obat_deleted ON instruksitindakanbmhp_t.instruksitindakanbmhp_id = obat_deleted.instruksitindakanbmhp_id
     LEFT JOIN ( SELECT c.loginpemakai_id,
            pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai
           FROM loginpemakai_k c
             JOIN ( SELECT c1.pegawai_id,
                    c1.nama_pegawai
                   FROM pegawai_m c1) pegawai_m ON c.pegawai_id = pegawai_m.pegawai_id) pegawai_hapus ON instruksitindakanbmhp_t.deleted_by = pegawai_hapus.loginpemakai_id
     LEFT JOIN ( SELECT c.lookup_id,
            c.lookup_name
           FROM lookup_m c) status ON instruksitindakanbmhp_t.status_implementasi::int4::integer = status.lookup_id
		 WHERE pendaftaran_t.pasien_id = xpasien_id
		 AND instruksi_t.tgl_instruksi::DATE BETWEEN xstart_date AND xend_date
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
    COALESCE(resepturdetail_t.tgl_resepturdetail, resepturdetail_t.created_date) AS tgl_instruksi,
    resepturdetail_t.obatalkes_id AS daftartindakan_id,
    concat(obatalkes_m.obatalkes_nama, ' - '::text || resepturdetail_t.qty_medis) AS instruksi,
    NULL::character varying AS paket,
    NULL::text AS tindakan,
    resepturdetail_t.qty_medis AS qty,
    NULL::boolean AS is_cyto,
    NULL::double precision AS qty_sisa,
    reseptur_t.pegawai_id AS dokter_id, 
    dokter.nama_pegawai AS dokter,
    reseptur_t.status_reseptur AS status_implementasi,
    status.lookup_name AS status,
    instruksi_t.is_deleted AS instruksi_deleted,
        CASE
            WHEN resepturdetail_t.is_deleted THEN true
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
    reseptur_t.ruangan_nama AS ruangan_pertindakan,
    NULL::json AS bmhp_tindakandetail,
    instruksi_t.created_date AS tanggal_input,
    cppt_t.pendaftaran_id,
    cppt_t.pasienadmisi_id,
    NULL::integer AS kelompoktindakan_id,
    reseptur_t.instalasi_id,
    reseptur_t.instalasi_nama,
        CASE pendaftaran_t.status_bayar
            WHEN 348 THEN true
            ELSE false
        END AS is_bayar,
    NULL::integer AS pasienkirimkeunitlain_id,
    COALESCE(reseptur_t.alasan_batal) AS alasan_batal,
    peg_deleted.pegawai_id AS pegawai_hapus_id,
    peg_deleted.nama_pegawai AS pegawai_hapus_nama,
    COALESCE(reseptur_t.deleted_date) AS tgl_batal,
    false AS is_penatajasa,
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
    satuanunit_m.satuanunit_nama AS satuankecil_nama,
    pendaftaran_t.pasien_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    concat('RESEPTUR',
        CASE
            WHEN racikan_m.racikan_singkatan::text = 'OR'::text THEN 'RACIKAN'::text
            ELSE 'NONRACIKAN'::text
        END) AS jenis_deskripsi,
    NULL::integer AS status_bmhp_id,
    NULL::text AS status_bmhp_nama,
		reseptur_t.ruangan_id
   FROM ( SELECT a.instruksi_id,
            a.cppt_id,
            a.catatan_instruksi,
            a.is_deleted,
            a.tgl_instruksi,
            a.created_date
           FROM instruksi_t a) instruksi_t
     JOIN ( SELECT d.reseptur_id,
            d.ruangan_id,
            d.instruksi_id,
            d.pegawai_id,
            d.status_reseptur,
            d.deleted_date,
            d.deleted_by,
            d.alasan_batal,
            d.noresep,
            d.tglreseptur,
            d.pendaftaran_id,
            ( SELECT ruangan_m.ruangan_nama
                   FROM ruangan_m
                  WHERE ruangan_m.ruangan_id = d.ruangan_id) AS ruangan_nama,
            ( SELECT ruangan_m.instalasi_id
                   FROM ruangan_m
                  WHERE ruangan_m.ruangan_id = d.ruangan_id) AS instalasi_id,
            ( SELECT instalasi_m.instalasi_nama
                   FROM instalasi_m
                  WHERE instalasi_m.instalasi_id = (( SELECT ruangan_m.instalasi_id
                           FROM ruangan_m
                          WHERE ruangan_m.ruangan_id = d.ruangan_id))) AS instalasi_nama
           FROM reseptur_t d
					 ) reseptur_t ON instruksi_t.instruksi_id = reseptur_t.instruksi_id
     JOIN ( SELECT d.reseptur_id,
            d.obatalkes_id,
            d.racikan_id,
            d.satuankecil_id,
            d.resepturdetail_id,
            d.tgl_resepturdetail,
            d.created_date,
            d.qty_reseptur,
            d.qty_medis,
            d.is_deleted
           FROM resepturdetail_t d
					 ) resepturdetail_t ON reseptur_t.reseptur_id = resepturdetail_t.reseptur_id
     JOIN ( SELECT d.obatalkes_id,
            d.obatalkes_nama
           FROM obatalkes_m d) obatalkes_m ON resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN ( SELECT d.pegawai_id,
            d.nama_pegawai
           FROM pegawai_m d) dokter ON reseptur_t.pegawai_id = dokter.pegawai_id
     LEFT JOIN ( SELECT d.racikan_id,
            d.racikan_nama,
            d.racikan_singkatan
           FROM racikan_m d) racikan_m ON resepturdetail_t.racikan_id = racikan_m.racikan_id
     LEFT JOIN ( SELECT d.cppt_id,
            d.pendaftaran_id,
            d.pegawai_id,
            d.is_verifikasi,
            d.pasienadmisi_id
           FROM cppt_t d) cppt_t ON cppt_t.cppt_id = instruksi_t.cppt_id
     JOIN ( SELECT d.pendaftaran_id,
            d.no_pendaftaran,
            d.status_bayar,
            d.is_stopakomodasi,
            d.status_periksa,
            d.pasien_id,
            d.tgl_pendaftaran
           FROM pendaftaran_t d) pendaftaran_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT d.satuanunit_id,
            d.satuanunit_nama
           FROM satuanunit_m d) satuanunit_m ON resepturdetail_t.satuankecil_id = satuanunit_m.satuanunit_id
     LEFT JOIN ( SELECT d.reseptur_id,
            d.is_deleted,
            d.deleted_by,
            d.deleted_date
           FROM penjualanresep_t d) penjualanresep_t ON reseptur_t.reseptur_id = penjualanresep_t.reseptur_id
     LEFT JOIN ( SELECT d.loginpemakai_id,
            d.pegawai_id
           FROM loginpemakai_k d) leg_deleted ON COALESCE(reseptur_t.deleted_by) = leg_deleted.loginpemakai_id
     LEFT JOIN ( SELECT d.pegawai_id,
            d.nama_pegawai
           FROM pegawai_m d) peg_deleted ON leg_deleted.pegawai_id = peg_deleted.pegawai_id
     LEFT JOIN ( SELECT d.lookup_id,
            d.lookup_name
           FROM lookup_m d) status ON reseptur_t.status_reseptur = status.lookup_id
		 WHERE pendaftaran_t.pasien_id = xpasien_id
		 AND instruksi_t.tgl_instruksi::DATE BETWEEN xstart_date AND xend_date
UNION ALL
 SELECT
        CASE
            WHEN COALESCE(resepturracikan_t.type, 'OR'::character varying)::text = 'OR'::text THEN 'RACIKAN'::text
            ELSE 'NONRACIKAN'::text
        END AS tipe_instruksi,
    instruksi_t.instruksi_id::int4,
    instruksi_t.cppt_id,
    instruksi_t.catatan_instruksi,
    resepturracikan_t.resepturracikan_id::int4,
    resepturracikan_t.created_date AS tgl_instruksi,
    NULL::int4 AS daftartindakan_id,
    resepturracikan_t.racikan AS instruksi,
    NULL::character varying AS paket,
    NULL::text AS tindakan,
    NULL::double precision AS qty,
    NULL::boolean AS is_cyto,
    NULL::int4 AS qty_sisa,
    reseptur_t.pegawai_id::int4,
    dokter.nama_pegawai AS dokter,
    reseptur_t.status_reseptur::int4,
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
    reseptur_t.ruangan_nama AS ruangan_pertindakan,
    NULL::json AS bmhp_tindakandetail,
    instruksi_t.created_date AS tanggal_input,
    cppt_t.pendaftaran_id,
    cppt_t.pasienadmisi_id,
    NULL::int4 AS kelompoktindakan_id,
    reseptur_t.instalasi_id,
    reseptur_t.instalasi_nama,
        CASE pendaftaran_t.status_bayar
            WHEN 348 THEN true
            ELSE false
        END AS is_bayar,
    NULL::int4 AS pasienkirimkeunitlain_id,
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
    NULL::int4 AS satuankecil_id,
    NULL::text AS satuankecil_nama,
    pendaftaran_t.pasien_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    concat('RESEPTUR',
        CASE
            WHEN COALESCE(resepturracikan_t.type, 'OR'::character varying)::text = 'OR'::text THEN 'RACIKAN'::text
            ELSE 'NONRACIKAN'::text
        END) AS jenis_deskripsi,
    NULL::int4 AS status_bmhp_id,
    NULL::text AS status_bmhp_nama,
		reseptur_t.ruangan_id
   FROM instruksi_t
     JOIN (SELECT d.reseptur_id,
            d.ruangan_id,
            d.instruksi_id,
            d.pegawai_id,
            d.status_reseptur,
            d.deleted_date,
            d.deleted_by,
            d.alasan_batal,
            d.noresep,
            d.tglreseptur,
            d.pendaftaran_id,
            ( SELECT ruangan_m.ruangan_nama
                   FROM ruangan_m
                  WHERE ruangan_m.ruangan_id = d.ruangan_id) AS ruangan_nama,
            ( SELECT ruangan_m.instalasi_id
                   FROM ruangan_m
                  WHERE ruangan_m.ruangan_id = d.ruangan_id) AS instalasi_id,
            ( SELECT instalasi_m.instalasi_nama
                   FROM instalasi_m
                  WHERE instalasi_m.instalasi_id = (( SELECT ruangan_m.instalasi_id
                           FROM ruangan_m
                          WHERE ruangan_m.ruangan_id = d.ruangan_id))) AS instalasi_nama
           FROM reseptur_t d) reseptur_t ON instruksi_t.instruksi_id::int4 = reseptur_t.instruksi_id::int4
     JOIN ( SELECT e.reseptur_id,
            e.created_date,
            e.is_deleted,
            e.type,
            e.resepturracikan_id,
            e.racikan
           FROM resepturracikan_t e
					 ) resepturracikan_t ON reseptur_t.reseptur_id = resepturracikan_t.reseptur_id
     JOIN ( SELECT e.pegawai_id,
            e.nama_pegawai
           FROM pegawai_m e) dokter ON reseptur_t.pegawai_id = dokter.pegawai_id
     LEFT JOIN ( SELECT e.cppt_id,
            e.pendaftaran_id,
            e.pegawai_id,
            e.is_verifikasi,
            e.pasienadmisi_id
           FROM cppt_t e) cppt_t ON cppt_t.cppt_id = instruksi_t.cppt_id
     LEFT JOIN ( SELECT e.pendaftaran_id,
            e.no_pendaftaran,
            e.status_periksa,
            e.status_bayar,
            e.pasienadmisi_id,
            e.is_stopakomodasi,
            e.pasien_id,
            e.tgl_pendaftaran
           FROM pendaftaran_t e) pendaftaran_t ON cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT e.reseptur_id,
            e.is_deleted,
            e.deleted_by,
            e.deleted_date
           FROM penjualanresep_t e) penjualanresep_t ON reseptur_t.reseptur_id = penjualanresep_t.reseptur_id
     LEFT JOIN ( SELECT e.loginpemakai_id,
            e.pegawai_id
           FROM loginpemakai_k e) leg_deleted ON reseptur_t.deleted_by = leg_deleted.loginpemakai_id
     LEFT JOIN ( SELECT e.pegawai_id,
            e.nama_pegawai
           FROM pegawai_m e) peg_deleted ON leg_deleted.pegawai_id = peg_deleted.pegawai_id
     LEFT JOIN ( SELECT e.lookup_id,
            e.lookup_name
           FROM lookup_m e) status ON reseptur_t.status_reseptur = status.lookup_id
		 WHERE pendaftaran_t.pasien_id = xpasien_id
		 AND instruksi_t.tgl_instruksi::DATE BETWEEN xstart_date AND xend_date
UNION ALL
 SELECT 'LAB_TINDAKAN'::text AS tipe_instruksi,
    instruksi_t.instruksi_id::int4,
    instruksi_t.cppt_id,
    instruksi_t.catatan_instruksi,
    permintaankepenunjang_t.permintaankepenunjang_id::int4,
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
    NULL::int4 AS qty_sisa,
    pasienkirimkeunitlain_t.pegawai_id::int4,
    dokter.nama_pegawai AS dokter,
        CASE
            WHEN hasil_wynacom.no_masukpenunjang IS NOT NULL THEN 475::int4
            WHEN pasienmasukpenunjang_t.status_periksa IS NULL THEN pasienkirimkeunitlain_t.status_penunjang::int4
            ELSE pasienmasukpenunjang_t.status_periksa::int4
        END,
        CASE
            WHEN hasil_wynacom.no_masukpenunjang IS NOT NULL THEN 'SELESAI'::character varying
            WHEN pasienmasukpenunjang_t.status_periksa IS NULL THEN status_penunjang.lookup_name
            ELSE status_periksa.lookup_name
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
    permintaankepenunjang_t.is_approve AS is_telah_implementasi,
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
    status_penunjang.lookup_name AS status_penunjang_nama,
    NULL::text AS noresep,
    NULL::timestamp(6) without time zone AS tgl_resep_dibuat,
    NULL::int4 AS satuankecil_id,
    NULL::text AS satuankecil_nama,
    pendaftaran_t.pasien_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    concat('PENUNJANG', ' ', instalasi_m.instalasi_nama, ' ', ruangan_m.ruangan_nama) AS jenis_deskripsi,
    NULL::int4 AS status_bmhp_id,
    NULL::text AS status_bmhp_nama,
		ruangan_m.ruangan_id
   FROM (	 
		SELECT
			a.instruksi_id::int4,
			a.cppt_id,
			a.catatan_instruksi,
			a.is_deleted,
			a.tgl_instruksi ,
			a.created_date
		FROM instruksi_t a
		ORDER BY tgl_instruksi DESC
	 ) instruksi_t
     JOIN ( SELECT f.tgl_kirimpasien,
            f.pegawai_id,
            f.status_penunjang,
            f.pasienkirimkeunitlain_id,
            f.instruksi_id::int4,
            f.ruangan_id,
            f.instalasi_id
           FROM pasienkirimkeunitlain_t f) pasienkirimkeunitlain_t ON instruksi_t.instruksi_id::int4 = pasienkirimkeunitlain_t.instruksi_id::int4
     JOIN ( SELECT f.pasienkirimkeunitlain_id,
            f.daftartindakan_id,
            f.deleted_by,
            f.permintaankepenunjang_id,
            f.is_cyto,
            f.qtypermintaan,
            f.is_deleted,
            f.alasan_batal,
            f.deleted_date,
            f.is_approve
           FROM permintaankepenunjang_t f) permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT f.pasienkirimkeunitlain_id,
            f.pasienmasukpenunjang_id,
            f.no_masukpenunjang,
            f.status_periksa
           FROM pasienmasukpenunjang_t f) pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id
     JOIN ( SELECT f.daftartindakan_id,
            f.daftartindakan_nama,
            f.kelompoktindakan_id
           FROM daftartindakan_m f) daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN ( SELECT f.pegawai_id,
            f.nama_pegawai
           FROM pegawai_m f) dokter ON pasienkirimkeunitlain_t.pegawai_id = dokter.pegawai_id
     LEFT JOIN ( SELECT f.ruangan_id,
            f.ruangan_nama,
            f.instalasi_id
           FROM ruangan_m f) ruangan_m ON pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT f.cppt_id,
            f.pendaftaran_id,
            f.pegawai_id,
            f.is_verifikasi,
            f.pasienadmisi_id
           FROM cppt_t f) cppt_t ON cppt_t.cppt_id = instruksi_t.cppt_id
     LEFT JOIN ( SELECT f.instalasi_id,
            f.instalasi_nama
           FROM instalasi_m f) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT f.pendaftaran_id,
            f.no_pendaftaran,
            f.status_periksa,
            f.status_bayar,
            f.pasienadmisi_id,
            f.is_stopakomodasi,
            f.pasien_id,
            f.tgl_pendaftaran
           FROM pendaftaran_t f) pendaftaran_t ON cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT f.pasienmasukpenunjang_id,
            f.daftartindakan_id,
            f.deleted_date AS tgl_batal,
            f.alasan_batal,
            f.deleted_by,
            f.is_penatajasa
           FROM tindakanpelayanan_t f
          WHERE f.is_deleted IS TRUE) tindakan_deleted ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_deleted.pasienmasukpenunjang_id AND permintaankepenunjang_t.daftartindakan_id = tindakan_deleted.daftartindakan_id
     LEFT JOIN ( SELECT f.pasienkirimkeunitlain_id,
            f.tgl_batalorder AS tgl_batal,
            f.alasan AS alasan_batal,
            f.created_by,
            f.is_active,
            f.peg_menyetujui_id
           FROM batalorderpenunjang_t f) batal_order ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = batal_order.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT f.loginpemakai_id,
            f.pegawai_id
           FROM loginpemakai_k f) leg_deleted ON COALESCE(tindakan_deleted.deleted_by, permintaankepenunjang_t.deleted_by) = leg_deleted.loginpemakai_id
     LEFT JOIN ( SELECT f.pegawai_id,
            f.nama_pegawai
           FROM pegawai_m f) peg_deleted ON COALESCE(batal_order.peg_menyetujui_id, leg_deleted.pegawai_id) = peg_deleted.pegawai_id
     LEFT JOIN ( SELECT f.his_reg_no AS no_masukpenunjang
           FROM hasilpemeriksaanlab_wynacom_t f
          WHERE f.is_deleted = false
          GROUP BY f.his_reg_no) hasil_wynacom ON pasienmasukpenunjang_t.no_masukpenunjang::text = hasil_wynacom.no_masukpenunjang::text
     LEFT JOIN ( SELECT f.lookup_id,
            f.lookup_name
           FROM lookup_m f) status_penunjang ON pasienkirimkeunitlain_t.status_penunjang::integer = status_penunjang.lookup_id
     LEFT JOIN ( SELECT f.lookup_id,
            f.lookup_name
           FROM lookup_m f) status_periksa ON pasienmasukpenunjang_t.status_periksa::integer = status_periksa.lookup_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 4
	AND pendaftaran_t.pasien_id = xpasien_id
	AND instruksi_t.tgl_instruksi::DATE BETWEEN xstart_date AND xend_date
UNION ALL
--  SELECT 'LAB_PAKET'::text AS tipe_instruksi,
--     instruksi_t.instruksi_id::int4,
--     instruksi_t.cppt_id,
--     instruksi_t.catatan_instruksi,
--     permintaankepenunjang_t.permintaankepenunjang_id AS instruksitindakan_id,
--     pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_instruksi,
--     permintaankepenunjang_t.tipepaket_id AS daftartindakan_id,
--     concat(tipepaket_m.tipepaket_nama, '-',
--         CASE
--             WHEN permintaankepenunjang_t.is_cyto = true THEN 'Cyto - '::text
--             ELSE NULL::text
--         END, permintaankepenunjang_t.qtypermintaan) AS instruksi,
--     tipepaket_m.tipepaket_nama AS paket,
--     NULL::text AS tindakan,
--     permintaankepenunjang_t.qtypermintaan AS qty,
--     permintaankepenunjang_t.is_cyto,
--     NULL::int4 AS qty_sisa,
--     pasienkirimkeunitlain_t.pegawai_id AS dokter_id,
--     dokter.nama_pegawai AS dokter,
--         CASE
--             WHEN pasienmasukpenunjang_t.status_periksa IS NULL THEN pasienkirimkeunitlain_t.status_penunjang
--             ELSE pasienmasukpenunjang_t.status_periksa
--         END AS status_implementasi::int4,
--         CASE
--             WHEN pasienmasukpenunjang_t.status_periksa IS NULL THEN status_penunjang.lookup_name
--             ELSE status_periksa.lookup_name
--         END AS status,
--     instruksi_t.is_deleted AS instruksi_deleted,
--     permintaankepenunjang_t.is_deleted AS tindakan_deleted,
--     tipepaket_m.tipepaket_nama AS tindakaninstruksi_nama,
--         CASE
--             WHEN permintaankepenunjang_t.is_cyto = true THEN 'Cyto'::text
--             ELSE 'Non Cyto'::text
--         END AS ket_cyto,
--     NULL::character varying AS ket_racik_nama,
--     NULL::character varying AS ket_racik,
--     cppt_t.pegawai_id AS cpptpegawai_id,
--     cppt_t.is_verifikasi AS is_verifikasi_dpjp,
--     array_to_json(ARRAY( SELECT daftartindakan_m.daftartindakan_nama
--            FROM paketpelayanan_mp
--              LEFT JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
--           WHERE paketpelayanan_mp.tipepaket_id = permintaankepenunjang_t.tipepaket_id)) AS daftar_paket,
--     instruksi_t.tgl_instruksi AS tanggal_terapi,
--     'PENUNJANG'::text AS grouping_tipe,
--         CASE
--             WHEN pasienkirimkeunitlain_t.pasienmasukpenunjang_id IS NOT NULL THEN true
--             ELSE false
--         END AS is_telah_implementasi,
--     ruangan_m.ruangan_nama AS ruangan_pertindakan,
--     NULL::json AS bmhp_tindakandetail,
--     instruksi_t.created_date AS tanggal_input,
--     cppt_t.pendaftaran_id,
--     cppt_t.pasienadmisi_id,
--     NULL::int4 AS kelompoktindakan_id,
--     instalasi_m.instalasi_id,
--     instalasi_m.instalasi_nama,
--         CASE pendaftaran_t.status_bayar
--             WHEN 348 THEN true
--             ELSE false
--         END AS is_bayar,
--     pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
--     COALESCE(batal_order.alasan_batal, tindakan_deleted.alasan_batal, permintaankepenunjang_t.alasan_batal) AS alasan_batal,
--     peg_deleted.pegawai_id AS pegawai_hapus_id,
--     peg_deleted.nama_pegawai AS pegawai_hapus_nama,
--     COALESCE(batal_order.tgl_batal, tindakan_deleted.tgl_batal, permintaankepenunjang_t.deleted_date) AS tgl_batal,
--     COALESCE(tindakan_deleted.is_penatajasa, false) AS is_penatajasa,
--         CASE
--             WHEN cppt_t.pasienadmisi_id IS NOT NULL THEN
--             CASE
--                 WHEN pendaftaran_t.is_stopakomodasi IS TRUE THEN true
--                 ELSE false
--             END
--             ELSE
--             CASE
--                 WHEN pendaftaran_t.status_periksa::text = '4'::text THEN true
--                 WHEN pendaftaran_t.status_periksa::text = '433'::text THEN true
--                 ELSE false
--             END
--         END AS is_pulang,
--     pasienkirimkeunitlain_t.status_penunjang,
--     status_penunjang.lookup_name AS status_penunjang_nama,
--     NULL::text AS noresep,
--     NULL::timestamp(6) without time zone AS tgl_resep_dibuat,
--     NULL::int4 AS satuankecil_id,
--     NULL::text AS satuankecil_nama,
--     pendaftaran_t.pasien_id,
--     pendaftaran_t.no_pendaftaran,
--     pendaftaran_t.tgl_pendaftaran,
--     concat('PENUNJANG', ' ', instalasi_m.instalasi_nama, ' ', ruangan_m.ruangan_nama) AS jenis_deskripsi,
--     NULL::int4 AS status_bmhp_id,
--     NULL::text AS status_bmhp_nama
--    FROM instruksi_t
--      JOIN ( SELECT g.tgl_kirimpasien,
--             g.pegawai_id,
--             g.status_penunjang,
--             g.pasienkirimkeunitlain_id,
--             g.instruksi_id::int4,
--             g.ruangan_id,
--             g.instalasi_id,
--             g.pasienmasukpenunjang_id
--            FROM pasienkirimkeunitlain_t g) pasienkirimkeunitlain_t ON instruksi_t.instruksi_id::int4 = pasienkirimkeunitlain_t.instruksi_id::int4
--      JOIN ( SELECT g.pasienkirimkeunitlain_id,
--             g.tipepaket_id,
--             g.deleted_by,
--             g.permintaankepenunjang_id,
--             g.is_cyto,
--             g.qtypermintaan,
--             g.is_deleted,
--             g.alasan_batal,
--             g.deleted_date
--            FROM permintaankepenunjang_t g) permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
--      LEFT JOIN ( SELECT g.pasienkirimkeunitlain_id,
--             g.pasienmasukpenunjang_id,
--             g.no_masukpenunjang,
--             g.status_periksa
--            FROM pasienmasukpenunjang_t g) pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id
--      JOIN ( SELECT g.tipepaket_id,
--             g.tipepaket_nama
--            FROM tipepaket_m g) tipepaket_m ON permintaankepenunjang_t.tipepaket_id = tipepaket_m.tipepaket_id
--      JOIN ( SELECT g.pegawai_id,
--             g.nama_pegawai
--            FROM pegawai_m g) dokter ON pasienkirimkeunitlain_t.pegawai_id = dokter.pegawai_id
--      LEFT JOIN ( SELECT g.ruangan_id,
--             g.ruangan_nama,
--             g.instalasi_id
--            FROM ruangan_m g) ruangan_m ON pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id
--      LEFT JOIN ( SELECT g.cppt_id,
--             g.pendaftaran_id,
--             g.pegawai_id,
--             g.is_verifikasi,
--             g.pasienadmisi_id
--            FROM cppt_t g) cppt_t ON cppt_t.cppt_id = instruksi_t.cppt_id
--      LEFT JOIN ( SELECT g.instalasi_id,
--             g.instalasi_nama
--            FROM instalasi_m g) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
--      LEFT JOIN ( SELECT g.pendaftaran_id,
--             g.no_pendaftaran,
--             g.status_periksa,
--             g.status_bayar,
--             g.pasienadmisi_id,
--             g.is_stopakomodasi,
--             g.pasien_id,
--             g.tgl_pendaftaran
--            FROM pendaftaran_t g) pendaftaran_t ON cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
--      LEFT JOIN ( SELECT g.pasienmasukpenunjang_id,
--             g.tipepaket_id,
--             g.deleted_date AS tgl_batal,
--             g.alasan_batal,
--             g.deleted_by,
--             g.is_penatajasa
--            FROM tindakanpelayanan_t g
--           WHERE g.is_deleted IS TRUE) tindakan_deleted ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_deleted.pasienmasukpenunjang_id AND permintaankepenunjang_t.tipepaket_id = tindakan_deleted.tipepaket_id
--      LEFT JOIN ( SELECT g.pasienkirimkeunitlain_id,
--             g.tgl_batalorder AS tgl_batal,
--             g.alasan AS alasan_batal,
--             g.created_by,
--             g.is_active,
--             g.peg_menyetujui_id
--            FROM batalorderpenunjang_t g) batal_order ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = batal_order.pasienkirimkeunitlain_id
--      LEFT JOIN ( SELECT g.loginpemakai_id,
--             g.pegawai_id
--            FROM loginpemakai_k g) leg_deleted ON COALESCE(tindakan_deleted.deleted_by, permintaankepenunjang_t.deleted_by) = leg_deleted.loginpemakai_id
--      LEFT JOIN ( SELECT g.pegawai_id,
--             g.nama_pegawai
--            FROM pegawai_m g) peg_deleted ON COALESCE(batal_order.peg_menyetujui_id, leg_deleted.pegawai_id) = peg_deleted.pegawai_id
--      LEFT JOIN ( SELECT g.lookup_id,
--             g.lookup_name
--            FROM lookup_m g) status_penunjang ON pasienkirimkeunitlain_t.status_penunjang::integer = status_penunjang.lookup_id
--      LEFT JOIN ( SELECT g.lookup_id,
--             g.lookup_name
--            FROM lookup_m g) status_periksa ON pasienmasukpenunjang_t.status_periksa::integer = status_periksa.lookup_id
--   WHERE pasienkirimkeunitlain_t.instalasi_id = 4
-- UNION ALL
 SELECT 'RAD_TINDAKAN'::text AS tipe_instruksi,
    instruksi_t.instruksi_id::int4,
    instruksi_t.cppt_id,
    instruksi_t.catatan_instruksi,
    permintaankepenunjang_t.permintaankepenunjang_id::int4,
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
    NULL::int4 AS qty_sisa,
    pasienkirimkeunitlain_t.pegawai_id::int4,
    dokter.nama_pegawai AS dokter,
        (CASE
            WHEN pasienmasukpenunjang_t.status_periksa IS NULL THEN pasienkirimkeunitlain_t.status_penunjang
            ELSE pasienmasukpenunjang_t.status_periksa
        END)::int4,
        CASE
            WHEN pasienmasukpenunjang_t.status_periksa IS NULL THEN status_penunjang.lookup_name
            ELSE status_periksa.lookup_name
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
            WHEN permintaankepenunjang_t.tindakanpelayanan_id IS NOT NULL THEN true
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
    status_penunjang.lookup_name AS status_penunjang_nama,
    NULL::text AS noresep,
    NULL::timestamp(6) without time zone AS tgl_resep_dibuat,
    NULL::int4 AS satuankecil_id,
    NULL::text AS satuankecil_nama,
    pendaftaran_t.pasien_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    concat('PENUNJANG', ' ', instalasi_m.instalasi_nama, ' ', ruangan_m.ruangan_nama) AS jenis_deskripsi,
    NULL::int4 AS status_bmhp_id,
    NULL::text AS status_bmhp_nama,
		ruangan_m.ruangan_id
   FROM (	 
		SELECT
			a.instruksi_id::int4,
			a.cppt_id,
			a.catatan_instruksi,
			a.is_deleted,
			a.tgl_instruksi ,
			a.created_date
		FROM instruksi_t a
		ORDER BY tgl_instruksi DESC
	 ) instruksi_t
     JOIN ( SELECT h.tgl_kirimpasien,
            h.pegawai_id,
            h.status_penunjang,
            h.pasienkirimkeunitlain_id,
            h.instruksi_id::int4,
            h.ruangan_id,
            h.instalasi_id
           FROM pasienkirimkeunitlain_t h
					 ) pasienkirimkeunitlain_t ON instruksi_t.instruksi_id::int4 = pasienkirimkeunitlain_t.instruksi_id::int4
     JOIN ( SELECT h.pasienkirimkeunitlain_id,
            h.daftartindakan_id,
            h.deleted_by,
            h.permintaankepenunjang_id,
            h.is_cyto,
            h.qtypermintaan,
            h.is_deleted,
            h.alasan_batal,
            h.deleted_date,
            h.tindakanpelayanan_id
           FROM permintaankepenunjang_t h) permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT h.pasienkirimkeunitlain_id,
            h.pasienmasukpenunjang_id,
            h.no_masukpenunjang,
            h.status_periksa
           FROM pasienmasukpenunjang_t h) pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id
     JOIN ( SELECT h.daftartindakan_id,
            h.daftartindakan_nama,
            h.kelompoktindakan_id
           FROM daftartindakan_m h) daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN ( SELECT h.pegawai_id,
            h.nama_pegawai
           FROM pegawai_m h) dokter ON pasienkirimkeunitlain_t.pegawai_id = dokter.pegawai_id
     LEFT JOIN ( SELECT h.ruangan_id,
            h.ruangan_nama,
            h.instalasi_id
           FROM ruangan_m h) ruangan_m ON pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT h.cppt_id,
            h.pendaftaran_id,
            h.pegawai_id,
            h.is_verifikasi,
            h.pasienadmisi_id
           FROM cppt_t h) cppt_t ON cppt_t.cppt_id = instruksi_t.cppt_id
     LEFT JOIN ( SELECT h.instalasi_id,
            h.instalasi_nama
           FROM instalasi_m h) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT h.pendaftaran_id,
            h.no_pendaftaran,
            h.status_periksa,
            h.status_bayar,
            h.pasienadmisi_id,
            h.is_stopakomodasi,
            h.pasien_id,
            h.tgl_pendaftaran
           FROM pendaftaran_t h) pendaftaran_t ON cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT h.pasienmasukpenunjang_id,
            h.daftartindakan_id,
            h.deleted_date AS tgl_batal,
            h.alasan_batal,
            h.deleted_by,
            h.is_penatajasa
           FROM tindakanpelayanan_t h
          WHERE h.is_deleted IS TRUE) tindakan_deleted ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_deleted.pasienmasukpenunjang_id AND permintaankepenunjang_t.daftartindakan_id = tindakan_deleted.daftartindakan_id
     LEFT JOIN ( SELECT h.pasienkirimkeunitlain_id,
            h.tgl_batalorder AS tgl_batal,
            h.alasan AS alasan_batal,
            h.created_by,
            h.is_active,
            h.peg_menyetujui_id
           FROM batalorderpenunjang_t h) batal_order ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = batal_order.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT h.loginpemakai_id,
            h.pegawai_id
           FROM loginpemakai_k h) leg_deleted ON COALESCE(tindakan_deleted.deleted_by, permintaankepenunjang_t.deleted_by) = leg_deleted.loginpemakai_id
     LEFT JOIN ( SELECT h.pegawai_id,
            h.nama_pegawai
           FROM pegawai_m h) peg_deleted ON COALESCE(batal_order.peg_menyetujui_id, leg_deleted.pegawai_id) = peg_deleted.pegawai_id
     LEFT JOIN ( SELECT h.lookup_id,
            h.lookup_name
           FROM lookup_m h) status_penunjang ON pasienkirimkeunitlain_t.status_penunjang::integer = status_penunjang.lookup_id
     LEFT JOIN ( SELECT h.lookup_id,
            h.lookup_name
           FROM lookup_m h) status_periksa ON pasienmasukpenunjang_t.status_periksa::integer = status_periksa.lookup_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 5
	AND pendaftaran_t.pasien_id = xpasien_id
	AND instruksi_t.tgl_instruksi::DATE BETWEEN xstart_date AND xend_date
-- UNION ALL
--  SELECT 'RAD_PAKET'::text AS tipe_instruksi,
--     instruksi_t.instruksi_id::int4,
--     instruksi_t.cppt_id,
--     instruksi_t.catatan_instruksi,
--     permintaankepenunjang_t.permintaankepenunjang_id AS instruksitindakan_id,
--     pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_instruksi,
--     permintaankepenunjang_t.tipepaket_id AS daftartindakan_id,
--     concat(tipepaket_m.tipepaket_nama, '-',
--         CASE
--             WHEN permintaankepenunjang_t.is_cyto = true THEN 'Cyto - '::text
--             ELSE NULL::text
--         END, permintaankepenunjang_t.qtypermintaan) AS instruksi,
--     tipepaket_m.tipepaket_nama AS paket,
--     NULL::text AS tindakan,
--     permintaankepenunjang_t.qtypermintaan AS qty,
--     permintaankepenunjang_t.is_cyto,
--     NULL::int4 AS qty_sisa,
--     pasienkirimkeunitlain_t.pegawai_id AS dokter_id,
--     dokter.nama_pegawai AS dokter,
--         CASE
--             WHEN pasienmasukpenunjang_t.status_periksa IS NULL THEN pasienkirimkeunitlain_t.status_penunjang
--             ELSE pasienmasukpenunjang_t.status_periksa
--         END AS status_implementasi::int4,
--         CASE
--             WHEN pasienmasukpenunjang_t.status_periksa IS NULL THEN status_penunjang.lookup_name
--             ELSE status_periksa.lookup_name
--         END AS status,
--     instruksi_t.is_deleted AS instruksi_deleted,
--     permintaankepenunjang_t.is_deleted AS tindakan_deleted,
--     tipepaket_m.tipepaket_nama AS tindakaninstruksi_nama,
--         CASE
--             WHEN permintaankepenunjang_t.is_cyto = true THEN 'Cyto'::text
--             ELSE 'Non Cyto'::text
--         END AS ket_cyto,
--     NULL::character varying AS ket_racik_nama,
--     NULL::character varying AS ket_racik,
--     cppt_t.pegawai_id AS cpptpegawai_id,
--     cppt_t.is_verifikasi AS is_verifikasi_dpjp,
--     array_to_json(ARRAY( SELECT daftartindakan_m.daftartindakan_nama
--            FROM paketpelayanan_mp
--              LEFT JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
--           WHERE paketpelayanan_mp.tipepaket_id = permintaankepenunjang_t.tipepaket_id)) AS daftar_paket,
--     instruksi_t.tgl_instruksi AS tanggal_terapi,
--     'PENUNJANG'::text AS grouping_tipe,
--         CASE
--             WHEN pasienkirimkeunitlain_t.status_penunjang::text = '471'::character varying::text THEN true
--             ELSE false
--         END AS is_telah_implementasi,
--     ruangan_m.ruangan_nama AS ruangan_pertindakan,
--     NULL::json AS bmhp_tindakandetail,
--     instruksi_t.created_date AS tanggal_input,
--     cppt_t.pendaftaran_id,
--     cppt_t.pasienadmisi_id,
--     NULL::int4 AS kelompoktindakan_id,
--     instalasi_m.instalasi_id,
--     instalasi_m.instalasi_nama,
--         CASE pendaftaran_t.status_bayar
--             WHEN 348 THEN true
--             ELSE false
--         END AS is_bayar,
--     pasienkirimkeunitlain_t.pasienkirimkeunitlain_id,
--     COALESCE(batal_order.alasan_batal, tindakan_deleted.alasan_batal, permintaankepenunjang_t.alasan_batal) AS alasan_batal,
--     peg_deleted.pegawai_id AS pegawai_hapus_id,
--     peg_deleted.nama_pegawai AS pegawai_hapus_nama,
--     COALESCE(batal_order.tgl_batal, tindakan_deleted.tgl_batal, permintaankepenunjang_t.deleted_date) AS tgl_batal,
--     COALESCE(tindakan_deleted.is_penatajasa, false) AS is_penatajasa,
--         CASE
--             WHEN cppt_t.pasienadmisi_id IS NOT NULL THEN
--             CASE
--                 WHEN pendaftaran_t.is_stopakomodasi IS TRUE THEN true
--                 ELSE false
--             END
--             ELSE
--             CASE
--                 WHEN pendaftaran_t.status_periksa::text = '4'::text THEN true
--                 WHEN pendaftaran_t.status_periksa::text = '433'::text THEN true
--                 ELSE false
--             END
--         END AS is_pulang,
--     pasienkirimkeunitlain_t.status_penunjang,
--     status_penunjang.lookup_name AS status_penunjang_nama,
--     NULL::text AS noresep,
--     NULL::timestamp(6) without time zone AS tgl_resep_dibuat,
--     NULL::int4 AS satuankecil_id,
--     NULL::text AS satuankecil_nama,
--     pendaftaran_t.pasien_id,
--     pendaftaran_t.no_pendaftaran,
--     pendaftaran_t.tgl_pendaftaran,
--     concat('PENUNJANG', ' ', instalasi_m.instalasi_nama, ' ', ruangan_m.ruangan_nama) AS jenis_deskripsi,
--     NULL::int4 AS status_bmhp_id,
--     NULL::text AS status_bmhp_nama
--    FROM instruksi_t
--      JOIN ( SELECT i.tgl_kirimpasien,
--             i.pegawai_id,
--             i.status_penunjang,
--             i.pasienkirimkeunitlain_id,
--             i.instruksi_id::int4,
--             i.ruangan_id,
--             i.instalasi_id
--            FROM pasienkirimkeunitlain_t i) pasienkirimkeunitlain_t ON instruksi_t.instruksi_id::int4 = pasienkirimkeunitlain_t.instruksi_id::int4
--      JOIN ( SELECT i.pasienkirimkeunitlain_id,
--             i.tipepaket_id,
--             i.deleted_by,
--             i.permintaankepenunjang_id,
--             i.is_cyto,
--             i.qtypermintaan,
--             i.is_deleted,
--             i.alasan_batal,
--             i.deleted_date
--            FROM permintaankepenunjang_t i) permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
--      LEFT JOIN ( SELECT i.pasienkirimkeunitlain_id,
--             i.pasienmasukpenunjang_id,
--             i.no_masukpenunjang,
--             i.status_periksa
--            FROM pasienmasukpenunjang_t i) pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id
--      JOIN ( SELECT i.tipepaket_id,
--             i.tipepaket_nama
--            FROM tipepaket_m i) tipepaket_m ON permintaankepenunjang_t.tipepaket_id = tipepaket_m.tipepaket_id
--      JOIN ( SELECT i.pegawai_id,
--             i.nama_pegawai
--            FROM pegawai_m i) dokter ON pasienkirimkeunitlain_t.pegawai_id = dokter.pegawai_id
--      LEFT JOIN ( SELECT i.ruangan_id,
--             i.ruangan_nama,
--             i.instalasi_id
--            FROM ruangan_m i) ruangan_m ON pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id
--      LEFT JOIN ( SELECT i.cppt_id,
--             i.pendaftaran_id,
--             i.pegawai_id,
--             i.is_verifikasi,
--             i.pasienadmisi_id
--            FROM cppt_t i) cppt_t ON cppt_t.cppt_id = instruksi_t.cppt_id
--      LEFT JOIN ( SELECT i.instalasi_id,
--             i.instalasi_nama
--            FROM instalasi_m i) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
--      LEFT JOIN ( SELECT i.pendaftaran_id,
--             i.no_pendaftaran,
--             i.status_periksa,
--             i.status_bayar,
--             i.pasienadmisi_id,
--             i.is_stopakomodasi,
--             i.pasien_id,
--             i.tgl_pendaftaran
--            FROM pendaftaran_t i) pendaftaran_t ON cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
--      LEFT JOIN ( SELECT i.pasienmasukpenunjang_id,
--             i.tipepaket_id,
--             i.deleted_date AS tgl_batal,
--             i.alasan_batal,
--             i.deleted_by,
--             i.is_penatajasa
--            FROM tindakanpelayanan_t i
--           WHERE i.is_deleted IS TRUE) tindakan_deleted ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_deleted.pasienmasukpenunjang_id AND permintaankepenunjang_t.tipepaket_id = tindakan_deleted.tipepaket_id
--      LEFT JOIN ( SELECT i.pasienkirimkeunitlain_id,
--             i.tgl_batalorder AS tgl_batal,
--             i.alasan AS alasan_batal,
--             i.created_by,
--             i.is_active,
--             i.peg_menyetujui_id
--            FROM batalorderpenunjang_t i) batal_order ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = batal_order.pasienkirimkeunitlain_id
--      LEFT JOIN ( SELECT i.loginpemakai_id,
--             i.pegawai_id
--            FROM loginpemakai_k i) leg_deleted ON COALESCE(tindakan_deleted.deleted_by, permintaankepenunjang_t.deleted_by) = leg_deleted.loginpemakai_id
--      LEFT JOIN ( SELECT i.pegawai_id,
--             i.nama_pegawai
--            FROM pegawai_m i) peg_deleted ON COALESCE(batal_order.peg_menyetujui_id, leg_deleted.pegawai_id) = peg_deleted.pegawai_id
--      LEFT JOIN ( SELECT i.lookup_id,
--             i.lookup_name
--            FROM lookup_m i) status_penunjang ON pasienkirimkeunitlain_t.status_penunjang::integer = status_penunjang.lookup_id
--      LEFT JOIN ( SELECT i.lookup_id,
--             i.lookup_name
--            FROM lookup_m i) status_periksa ON pasienmasukpenunjang_t.status_periksa::integer = status_periksa.lookup_id
--   WHERE pasienkirimkeunitlain_t.instalasi_id = 5
UNION ALL
 SELECT 'BED_TINDAKAN'::text AS tipe_instruksi,
    instruksi_t.instruksi_id::int4,
    instruksi_t.cppt_id,
    instruksi_t.catatan_instruksi,
    permintaankepenunjang_t.permintaankepenunjang_id::int4,
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
    NULL::int4 AS qty_sisa,
    pasienkirimkeunitlain_t.pegawai_id::int4,
    dokter.nama_pegawai AS dokter,
        CASE
            WHEN pasienmasukpenunjang_t.status_periksa IS NULL THEN pasienkirimkeunitlain_t.status_penunjang::int4
            ELSE pasienmasukpenunjang_t.status_periksa::int4
        END,
        CASE
            WHEN pasienmasukpenunjang_t.status_periksa IS NULL THEN status_penunjang.lookup_name
            ELSE status_periksa.lookup_name
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
    status_penunjang.lookup_name AS status_penunjang_nama,
    NULL::text AS noresep,
    NULL::timestamp(6) without time zone AS tgl_resep_dibuat,
    NULL::int4 AS satuankecil_id,
    NULL::text AS satuankecil_nama,
    pendaftaran_t.pasien_id,
    pendaftaran_t.no_pendaftaran,
    pendaftaran_t.tgl_pendaftaran,
    concat('PENUNJANG', ' ', instalasi_m.instalasi_nama, ' ', ruangan_m.ruangan_nama) AS jenis_deskripsi,
    NULL::int4 AS status_bmhp_id,
    NULL::text AS status_bmhp_nama,
		ruangan_m.ruangan_id
   FROM (	 
		SELECT
			a.instruksi_id::int4,
			a.cppt_id,
			a.catatan_instruksi,
			a.is_deleted,
			a.tgl_instruksi ,
			a.created_date
		FROM instruksi_t a
		ORDER BY tgl_instruksi DESC
	 ) instruksi_t
     JOIN ( SELECT j.tgl_kirimpasien,
            j.pegawai_id,
            j.status_penunjang,
            j.pasienkirimkeunitlain_id,
            j.instruksi_id::int4,
            j.ruangan_id,
            j.instalasi_id
           FROM pasienkirimkeunitlain_t j) pasienkirimkeunitlain_t ON instruksi_t.instruksi_id::int4 = pasienkirimkeunitlain_t.instruksi_id::int4
     JOIN ( SELECT j.pasienkirimkeunitlain_id,
            j.daftartindakan_id,
            j.deleted_by,
            j.permintaankepenunjang_id,
            j.is_cyto,
            j.qtypermintaan,
            j.is_deleted,
            j.alasan_batal,
            j.deleted_date,
            j.tglpermintaankepenunjang
           FROM permintaankepenunjang_t j) permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT j.pasienkirimkeunitlain_id,
            j.pasienmasukpenunjang_id,
            j.no_masukpenunjang,
            j.status_periksa
           FROM pasienmasukpenunjang_t j) pasienmasukpenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id
     JOIN ( SELECT j.daftartindakan_id,
            j.daftartindakan_nama,
            j.kelompoktindakan_id
           FROM daftartindakan_m j) daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN ( SELECT j.pegawai_id,
            j.nama_pegawai
           FROM pegawai_m j) dokter ON pasienkirimkeunitlain_t.pegawai_id = dokter.pegawai_id
     LEFT JOIN ( SELECT j.ruangan_id,
            j.ruangan_nama,
            j.instalasi_id
           FROM ruangan_m j) ruangan_m ON pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT j.cppt_id,
            j.pendaftaran_id,
            j.pegawai_id,
            j.is_verifikasi,
            j.pasienadmisi_id
           FROM cppt_t j) cppt_t ON cppt_t.cppt_id = instruksi_t.cppt_id
     LEFT JOIN ( SELECT j.instalasi_id,
            j.instalasi_nama
           FROM instalasi_m j) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT j.pendaftaran_id,
            j.no_pendaftaran,
            j.status_periksa,
            j.status_bayar,
            j.pasienadmisi_id,
            j.is_stopakomodasi,
            j.pasien_id,
            j.tgl_pendaftaran
           FROM pendaftaran_t j) pendaftaran_t ON cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT j.pasienmasukpenunjang_id,
            j.daftartindakan_id,
            j.deleted_date AS tgl_batal,
            j.alasan_batal,
            j.deleted_by,
            j.is_penatajasa
           FROM tindakanpelayanan_t j
          WHERE j.is_deleted IS TRUE) tindakan_deleted ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakan_deleted.pasienmasukpenunjang_id AND permintaankepenunjang_t.daftartindakan_id = tindakan_deleted.daftartindakan_id
     LEFT JOIN ( SELECT j.pasienkirimkeunitlain_id,
            j.tgl_batalorder AS tgl_batal,
            j.alasan AS alasan_batal,
            j.created_by,
            j.is_active,
            j.peg_menyetujui_id
           FROM batalorderpenunjang_t j) batal_order ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = batal_order.pasienkirimkeunitlain_id
     LEFT JOIN ( SELECT j.loginpemakai_id,
            j.pegawai_id
           FROM loginpemakai_k j) leg_deleted ON COALESCE(tindakan_deleted.deleted_by, permintaankepenunjang_t.deleted_by) = leg_deleted.loginpemakai_id
     LEFT JOIN ( SELECT j.pegawai_id,
            j.nama_pegawai
           FROM pegawai_m j) peg_deleted ON COALESCE(batal_order.peg_menyetujui_id, leg_deleted.pegawai_id) = peg_deleted.pegawai_id
     LEFT JOIN ( SELECT j.lookup_id,
            j.lookup_name
           FROM lookup_m j) status_penunjang ON pasienkirimkeunitlain_t.status_penunjang::integer = status_penunjang.lookup_id
     LEFT JOIN ( SELECT j.lookup_id,
            j.lookup_name
           FROM lookup_m j) status_periksa ON pasienmasukpenunjang_t.status_periksa::integer = status_periksa.lookup_id
  WHERE pasienkirimkeunitlain_t.instalasi_id = 12
	AND pendaftaran_t.pasien_id = xpasien_id
	AND instruksi_t.tgl_instruksi::DATE BETWEEN xstart_date AND xend_date
UNION ALL
 SELECT 'TINDAKAN'::text AS tipe_instruksi,
    NULL::int4 AS instruksi_id,
    NULL::int4 AS cppt_id,
    tindakanpelayanan_t.keterangantindakan AS catatan_instruksi,
    tindakanpelayanan_t.tindakanpelayanan_id::int4,
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
    tindakanpelayanan_t.dokterpenanggungjawab_id::int4,
    dokter.nama_pegawai AS dokter,
    NULL::int4,
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
    NULL::int4 AS cpptpegawai_id,
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
    NULL::int4 AS pasienkirimkeunitlain_id,
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
    NULL::int4 AS satuankecil_id,
    NULL::text AS satuankecil_nama,
    pendaftaran.pasien_id,
    pendaftaran.no_pendaftaran,
    pendaftaran.tgl_pendaftaran,
    concat('TINDAKANBMHP TINDAKAN') AS jenis_deskripsi,
    NULL::int4 AS status_bmhp_id,
    NULL::text AS status_bmhp_nama,
		ruangan_m.ruangan_id
   FROM tindakanpelayanan_t
     JOIN ( SELECT k.status_bayar,
            k.pendaftaran_id,
            k.status_periksa,
            k.pasienadmisi_id,
            k.is_stopakomodasi,
            k.pasien_id,
            k.no_pendaftaran,
            k.tgl_pendaftaran
           FROM pendaftaran_t k) pendaftaran ON tindakanpelayanan_t.pendaftaran_id = pendaftaran.pendaftaran_id
     JOIN ( SELECT k.daftartindakan_id,
            k.daftartindakan_nama,
            k.kelompoktindakan_id
           FROM daftartindakan_m k) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN ( SELECT k.pegawai_id,
            k.nama_pegawai
           FROM pegawai_m k) dokter ON tindakanpelayanan_t.dokterpenanggungjawab_id = dokter.pegawai_id
     JOIN ( SELECT k.ruangan_id,
            k.ruangan_nama,
            k.instalasi_id
           FROM ruangan_m k) ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT k.instalasi_id,
            k.instalasi_nama
           FROM instalasi_m k) instalasi_m ON tindakanpelayanan_t.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT k.loginpemakai_id,
            pegawai_m.pegawai_id,
            pegawai_m.nama_pegawai
           FROM loginpemakai_k k
             JOIN ( SELECT k1.pegawai_id,
                    k1.nama_pegawai
                   FROM pegawai_m k1) pegawai_m ON k.pegawai_id = pegawai_m.pegawai_id) pegawai_hapus ON tindakanpelayanan_t.deleted_by = pegawai_hapus.loginpemakai_id
  WHERE tindakanpelayanan_t.is_penatajasa IS TRUE
	AND pendaftaran.pasien_id = xpasien_id
	AND tindakanpelayanan_t.tgl_tindakan::DATE BETWEEN xstart_date AND xend_date
UNION ALL
 SELECT 'BMHP'::text AS tipe_instruksi,
    NULL::int4 AS instruksi_id,
    NULL::int4 AS cppt_id,
    obatalkespasien_t.keterangan AS catatan_instruksi,
    obatalkespasien_t.obatalkespasien_id::int4,
    obatalkespasien_t.tglpelayanan AS tgl_instruksi,
    obatalkespasien_t.obatalkes_id AS daftartindakan_id,
    concat(obatalkes_m.obatalkes_nama, '-'::text || obatalkespasien_t.qty_oa) AS instruksi,
    NULL::character varying AS paket,
    obatalkes_m.obatalkes_nama AS tindakan,
    obatalkespasien_t.qty_oa AS qty,
    NULL::boolean AS is_cyto,
    NULL::double precision AS qty_sisa,
    obatalkespasien_t.pegawai_id::int4,
    pegawai_m.nama_pegawai AS dokter,
    NULL::int4,
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
    NULL::int4 AS kelompoktindakan_id,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
        CASE pendaftaran.status_bayar
            WHEN 348 THEN true
            ELSE false
        END AS is_bayar,
    NULL::int4 AS pasienkirimkeunitlain_id,
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
    NULL::int4 AS satuankecil_id,
    NULL::text AS satuankecil_nama,
    pendaftaran.pasien_id,
    pendaftaran.no_pendaftaran,
    pendaftaran.tgl_pendaftaran,
    concat('TINDAKANBMHP BMHP') AS jenis_deskripsi,
    obatalkespasien_t.status_bmhp AS status_bmhp_id,
    lkp_status_bmhp.lookup_name AS status_bmhp_nama,
		ruangan_m.ruangan_id
   FROM obatalkespasien_t
     JOIN ( SELECT l.pendaftaran_id,
            l.status_bayar,
            l.status_periksa,
            l.pasienadmisi_id,
            l.is_stopakomodasi,
            l.pasien_id,
            l.no_pendaftaran,
            l.tgl_pendaftaran
           FROM pendaftaran_t l) pendaftaran ON obatalkespasien_t.pendaftaran_id = pendaftaran.pendaftaran_id
     JOIN ( SELECT l.obatalkes_id,
            l.obatalkes_nama
           FROM obatalkes_m l) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN ( SELECT l.ruangan_id,
            l.ruangan_nama,
            l.instalasi_id
           FROM ruangan_m l) ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT l.instalasi_id,
            l.instalasi_nama
           FROM instalasi_m l) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT l.pegawai_id,
            l.nama_pegawai
           FROM pegawai_m l) pegawai_m ON obatalkespasien_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN ( SELECT l.loginpemakai_id,
            l.pegawai_id
           FROM loginpemakai_k l) leg_deleted ON obatalkespasien_t.deleted_by = leg_deleted.loginpemakai_id
     LEFT JOIN ( SELECT l.pegawai_id,
            l.nama_pegawai
           FROM pegawai_m l) peg_deleted ON leg_deleted.pegawai_id = peg_deleted.pegawai_id
     LEFT JOIN ( SELECT lookup_m.lookup_id,
            lookup_m.lookup_name
           FROM lookup_m) lkp_status_bmhp ON obatalkespasien_t.status_bmhp::integer = lkp_status_bmhp.lookup_id
  WHERE obatalkespasien_t.is_penatajasa IS TRUE
	AND pendaftaran.pasien_id = xpasien_id
	AND obatalkespasien_t.tglpelayanan::DATE BETWEEN xstart_date AND xend_date
UNION ALL
 SELECT 'DIET'::text AS tipe_instruksi,
    permintaanmakan_t.permintaaanmakan_id::Int4,
    NULL::int4 AS cppt_id,
    permintaanmakan_t.catatan_diet AS catatan_instruksi,
    permintaanmakandetail_t.permintaanmakandetail_id::int4,
    permintaanmakan_t.tgl_permintaanmakan AS tgl_instruksi,
    daftartindakan_m.daftartindakan_id,
    concat(daftartindakan_m.daftartindakan_nama, ' - ', permintaanmakandetail_t.jumlah) AS instruksi,
    NULL::character varying AS paket,
    NULL::text AS tindakan,
    permintaanmakandetail_t.jumlah AS qty,
    false AS is_cyto,
    NULL::double precision AS qty_sisa,
    dokter.pegawai_id::int4,
    dokter.nama_pegawai AS dokter,
        CASE
            WHEN permintaanmakandetail_t.permintaanmakandetail_id IS NOT NULL THEN 1
            ELSE 0
        END,
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
    NULL::int4 AS pasienkirimkeunitlain_id,
    permintaanmakan_t.alasan_pembatalan AS alasan_batal,
    NULL::int4 AS pegawai_hapus_id,
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
    NULL::int4 AS satuankecil_id,
    NULL::text AS satuankecil_nama,
    pendaftaran.pasien_id,
    pendaftaran.no_pendaftaran,
    pendaftaran.tgl_pendaftaran,
    concat('TINDAKANDIET DIET') AS jenis_deskripsi,
    NULL::int4 AS status_bmhp_id,
    NULL::text AS status_bmhp_nama,
		ruangan_m.ruangan_id
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
            m.tgl_pendaftaran
           FROM pendaftaran_t m) pendaftaran ON permintaanmakan_t.pendaftaran_id = pendaftaran.pendaftaran_id
     LEFT JOIN ( SELECT m.pasienadmisi_id,
            m.pegawai_id,
            m.ruangan_id
           FROM pasienadmisi_t m) pasienadmisi ON permintaanmakan_t.pasienadmisi_id = pasienadmisi.pasienadmisi_id
     JOIN ( SELECT m.pegawai_id,
            m.nama_pegawai
           FROM pegawai_m m) dokter ON COALESCE(pasienadmisi.pegawai_id, pendaftaran.pegawai_id) = dokter.pegawai_id
     LEFT JOIN ( SELECT m.ruangan_id,
            m.ruangan_nama,
            m.instalasi_id
           FROM ruangan_m m) ruangan_m ON COALESCE(pasienadmisi.ruangan_id, pendaftaran.ruangan_id) = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT m.instalasi_id,
            m.instalasi_nama
           FROM instalasi_m m) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
		 WHERE pendaftaran.pasien_id = xpasien_id
		 AND permintaanmakan_t.tgl_permintaanmakan::DATE BETWEEN xstart_date AND xend_date
UNION ALL
 SELECT 'Penunjang Rehab Medik'::text AS tipe_instruksi,
    programterapi_t.programterapi_id::Int4,
    NULL::int4 AS cppt_id,
    programterapi_t.catatan AS catatan_instruksi,
    programterapidetail_t.programterapidetail_id::int4,
    programterapi_t.tgl_permintaan AS tgl_instruksi,
    daftartindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama AS instruksi,
    NULL::character varying AS paket,
    NULL::text AS tindakan,
        CASE
            WHEN programterapidetail_t.qty_pemeriksaan IS NULL THEN 1
            ELSE programterapidetail_t.qty_pemeriksaan
        END AS qty,
    false AS is_cyto,
    NULL::double precision AS qty_sisa,
    dokter.pegawai_id::int4,
    dokter.nama_pegawai AS dokter,
        CASE
            WHEN tindakanpelayanan.tindakanpelayanan_id IS NOT NULL THEN 1
            ELSE 0
        END,
        CASE
            WHEN tindakanpelayanan.tindakanpelayanan_id IS NOT NULL THEN 'Sudah Implementasi'::text
            ELSE 'Belum Implementasi'::text
        END AS status,
    programterapi_t.is_deleted AS instruksi_deleted,
    tindakanpelayanan.is_deleted AS tindakan_deleted,
    daftartindakan_m.daftartindakan_nama AS tindakaninstruksi_nama,
    'Non Cyto'::text AS ket_cyto,
    NULL::character varying AS ket_racik_nama,
    NULL::character varying AS ket_racik,
    dokter.pegawai_id AS cpptpegawai_id,
    NULL::boolean AS is_verifikasi_dpjp,
    array_to_json(NULL::character varying[]) AS daftar_paket,
    programterapi_t.tgl_permintaan AS tanggal_terapi,
    'REHAB MEDIK'::text AS grouping_tipe,
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
    NULL::int4 AS pasienkirimkeunitlain_id,
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
    NULL::int4 AS satuankecil_id,
    NULL::text AS satuankecil_nama,
    pendaftaran.pasien_id,
    pendaftaran.no_pendaftaran,
    pendaftaran.tgl_pendaftaran,
    'Penunjang Rehab Medik'::text AS jenis_deskripsi,
    NULL::int4 AS status_bmhp_id,
    NULL::text AS status_bmhp_nama,
		ruangan_m.ruangan_id
   FROM programterapi_t
     JOIN ( SELECT n.programterapi_id,
            n.daftartindakan_id,
            n.qty_pemeriksaan,
            n.programterapidetail_id
           FROM programterapidetail_t n) programterapidetail_t ON programterapi_t.programterapi_id = programterapidetail_t.programterapi_id
     JOIN ( SELECT n.daftartindakan_id,
            n.daftartindakan_nama,
            n.kelompoktindakan_id
           FROM daftartindakan_m n) daftartindakan_m ON programterapidetail_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN ( SELECT n.pendaftaran_id,
            n.pegawai_id,
            n.pasienadmisi_id,
            n.is_stopakomodasi,
            n.tgl_stopakomodasi,
            n.status_periksa,
            n.ruangan_id,
            n.status_bayar,
            n.pasien_id,
            n.no_pendaftaran,
            n.tgl_pendaftaran
           FROM pendaftaran_t n) pendaftaran ON programterapi_t.pendaftaran_id = pendaftaran.pendaftaran_id
     LEFT JOIN ( SELECT n.pasienadmisi_id,
            n.pegawai_id,
            n.ruangan_id
           FROM pasienadmisi_t n) pasienadmisi ON pendaftaran.pasienadmisi_id = pasienadmisi.pasienadmisi_id
     JOIN ( SELECT n.pegawai_id,
            n.nama_pegawai
           FROM pegawai_m n) dokter ON programterapi_t.dokterperujuk_id = dokter.pegawai_id
     LEFT JOIN ( SELECT n.ruangan_id,
            n.ruangan_nama,
            n.instalasi_id
           FROM ruangan_m n) ruangan_m ON COALESCE(pasienadmisi.ruangan_id, pendaftaran.ruangan_id) = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT n.instalasi_id,
            n.instalasi_nama
           FROM instalasi_m n) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT n.tindakanpelayanan_id,
            n.programterapi_id,
            n.daftartindakan_id,
            n.is_deleted,
            n.alasan_batal,
            n.deleted_date,
            pegawai.pegawai_id,
            pegawai.nama_pegawai,
            n.is_penatajasa
           FROM tindakanpelayanan_t n
             LEFT JOIN ( SELECT n1.loginpemakai_id,
                    n1.pegawai_id
                   FROM loginpemakai_k n1) loginpemakai_k ON n.deleted_by = loginpemakai_k.loginpemakai_id
             LEFT JOIN ( SELECT n2.pegawai_id,
                    n2.nama_pegawai
                   FROM pegawai_m n2) pegawai ON loginpemakai_k.pegawai_id = pegawai.pegawai_id) tindakanpelayanan ON programterapi_t.programterapi_id = tindakanpelayanan.programterapi_id AND programterapidetail_t.daftartindakan_id = tindakanpelayanan.daftartindakan_id
			 WHERE pendaftaran.pasien_id = xpasien_id
			 AND programterapi_t.tgl_permintaan::DATE BETWEEN xstart_date AND xend_date
UNION ALL
 SELECT 'UDD'::text AS tipe_instruksi,
    udd_t.udd_id::Int4,
    NULL::int4 AS cppt_id,
    udd_detail_t.catatan_dokter AS catatan_instruksi,
    udd_detail_t.udd_detail_id::int4,
    udd_t.tgl_order AS tgl_instruksi,
    udd_detail_t.obatalkes_id AS daftartindakan_id,
    concat(obatalkes_m.obatalkes_nama, ' - ', udd_detail_t.qty) AS instruksi,
    NULL::character varying AS paket,
    obatalkes_m.obatalkes_nama AS tindakan,
    udd_detail_t.qty,
    false AS is_cyto,
    NULL::double precision AS qty_sisa,
    dokter.pegawai_id::int4,
    dokter.nama_pegawai AS dokter,
        CASE
            WHEN udd_t.status_udd = 1032 THEN 1
            ELSE 0
        END,
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
    NULL::int4 AS pasienkirimkeunitlain_id,
    NULL::text AS alasan_batal,
    NULL::int4 AS pegawai_hapus_id,
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
    NULL::int4 AS satuankecil_id,
    NULL::text AS satuankecil_nama,
    pendaftaran.pasien_id,
    pendaftaran.no_pendaftaran,
    pendaftaran.tgl_pendaftaran,
    concat('OBATUDD UDD') AS jenis_deskripsi,
    NULL::int4 AS status_bmhp_id,
    NULL::text AS status_bmhp_nama,
		ruangan_m.ruangan_id
   FROM udd_t
     JOIN ( SELECT o.udd_id,
            o.udd_detail_id,
            o.catatan_dokter,
            o.obatalkes_id,
            o.qty,
            o.is_deleted
           FROM udd_detail_t o) udd_detail_t ON udd_t.udd_id = udd_detail_t.udd_id
     JOIN ( SELECT o.obatalkes_id,
            o.obatalkes_kode,
            o.obatalkes_nama,
            o.jenisobatalkes_id
           FROM obatalkes_m o) obatalkes_m ON udd_detail_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN ( SELECT o.pendaftaran_id,
            o.pegawai_id,
            o.pasienadmisi_id,
            o.is_stopakomodasi,
            o.tgl_stopakomodasi,
            o.status_periksa,
            o.ruangan_id,
            o.status_bayar,
            o.pasien_id,
            o.no_pendaftaran,
            o.tgl_pendaftaran
           FROM pendaftaran_t o) pendaftaran ON udd_t.pendaftaran_id = pendaftaran.pendaftaran_id
     JOIN ( SELECT o.pasienadmisi_id,
            o.pegawai_id,
            o.ruangan_id
           FROM pasienadmisi_t o) pasienadmisi ON udd_t.pasienadmisi_id = pasienadmisi.pasienadmisi_id
     JOIN ( SELECT o.pegawai_id,
            o.nama_pegawai
           FROM pegawai_m o) dokter ON COALESCE(pasienadmisi.pegawai_id, pendaftaran.pegawai_id) = dokter.pegawai_id
     LEFT JOIN ( SELECT o.ruangan_id,
            o.ruangan_nama,
            o.instalasi_id
           FROM ruangan_m o) ruangan_m ON COALESCE(pasienadmisi.ruangan_id, pendaftaran.ruangan_id) = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT o.instalasi_id,
            o.instalasi_nama
           FROM instalasi_m o) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
		 WHERE pendaftaran.pasien_id = xpasien_id
		 AND udd_t.tgl_order::DATE BETWEEN xstart_date AND xend_date
					 
	 ) AS instruksi 
	 WHERE instruksi.jenis_deskripsi ilike CONCAT('%', COALESCE(xjenis_deskripsi,'') ,'%')
	 AND instruksi.instruksi ilike CONCAT('%', COALESCE(xinstruksi,'') ,'%')
	 ORDER BY instruksi.tgl_instruksi DESC
	 LIMIT xlimit OFFSET xoffset
					 
					 ;
					 
      END; 
      $BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100
  ROWS 1000;