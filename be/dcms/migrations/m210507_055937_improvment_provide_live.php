<?php

use yii\db\Migration;

/**
 * Class m210507_055937_improvment_provide_live
 */
class m210507_055937_improvment_provide_live extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    	$this->execute('
            ALTER TABLE cppt_t ADD IF NOT EXISTS final INT;
        ');

        $this->execute('
            ALTER TABLE soaprj_t ADD IF NOT EXISTS final INT;
        ');

        $this->execute('
            ALTER TABLE hasilpemeriksaanlabdetail_t ADD IF NOT EXISTS is_verifikasi BOOLEAN DEFAULT FALSE;
        ');

        $this->execute('
            ALTER TABLE hasilpemeriksaanlabdetail_t ADD IF NOT EXISTS tanggal_verifikasi TIMESTAMP(6);
        ');

        $this->execute('
            ALTER TABLE hasilpemeriksaanlabdetail_t ADD IF NOT EXISTS petugas_verifikasi INT;
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."infoinstruksi_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infoinstruksi_v" AS  SELECT \'TINDAKAN\'::text AS tipe_instruksi,
			    instruksi_t.instruksi_id,
			    instruksi_t.cppt_id, 
			    instruksi_t.catatan_instruksi,
			    instruksitindakan_t.instruksitindakan_id,
			    instruksitindakan_t.tgl_tindakan AS tgl_instruksi,
			    instruksitindakan_t.daftartindakan_id,
			    concat(daftartindakan_m.daftartindakan_nama, \' - \',
			        CASE
			            WHEN (instruksitindakan_t.is_cyto = true) THEN \'Cyto\'::text
			            ELSE \'Non Cyto\'::text
			        END, (\' - \'::text || instruksitindakan_t.qty)) AS instruksi,
			    NULL::character varying AS paket,
			    NULL::text AS tindakan,
			    instruksitindakan_t.qty,
			    instruksitindakan_t.is_cyto,
			    instruksitindakan_t.qty_sisa,
			    instruksitindakan_t.dokterdpjp_id AS dokter_id,
			    dokter.nama_pegawai AS dokter,
			    instruksitindakan_t.status_implementasi,
			    fgetnamalookup((instruksitindakan_t.status_implementasi)::integer) AS status,
			    instruksi_t.is_deleted AS instruksi_deleted,
			    instruksitindakan_t.is_deleted AS tindakan_deleted,
			    daftartindakan_m.daftartindakan_nama AS tindakaninstruksi_nama,
			        CASE
			            WHEN (instruksitindakan_t.is_cyto = true) THEN \'Cyto\'::text
			            ELSE \'Non Cyto\'::text
			        END AS ket_cyto,
			    NULL::character varying AS ket_racik_nama,
			    NULL::character varying AS ket_racik,
			    cppt_t.pegawai_id AS cpptpegawai_id,
			    cppt_t.is_verifikasi AS is_verifikasi_dpjp,
			    array_to_json(NULL::character varying[]) AS daftar_paket,
			    instruksi_t.tgl_instruksi AS tanggal_terapi,
			    \'TINDAKANBMHP\'::text AS grouping_tipe,
			        CASE
			            WHEN ((instruksitindakan_t.status_implementasi)::text = (\'455\'::character varying)::text) THEN true
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
			    NULL::integer AS pasienkirimkeunitlain_id
			   FROM (((((((instruksi_t
			     JOIN instruksitindakan_t ON ((instruksi_t.instruksi_id = instruksitindakan_t.instruksi_id)))
			     JOIN daftartindakan_m ON ((instruksitindakan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
			     JOIN pegawai_m dokter ON ((instruksitindakan_t.dokterdpjp_id = dokter.pegawai_id)))
			     LEFT JOIN cppt_t ON ((cppt_t.cppt_id = instruksi_t.cppt_id)))
			     LEFT JOIN ruangan_m ON ((instruksitindakan_t.ruangan_id = ruangan_m.ruangan_id)))
			     LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
			     LEFT JOIN pendaftaran_t ON ((cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
			UNION ALL
			 SELECT \'PAKET\'::text AS tipe_instruksi,
			    instruksi_t.instruksi_id,
			    instruksi_t.cppt_id,
			    instruksi_t.catatan_instruksi,
			    instruksitindakan_t.instruksitindakan_id,
			    instruksitindakan_t.tgl_tindakan AS tgl_instruksi,
			    instruksitindakan_t.tipepaket_id AS daftartindakan_id,
			    concat(tipepaket_m.tipepaket_nama, \' - \',
			        CASE
			            WHEN (instruksitindakan_t.is_cyto = true) THEN \'Cyto\'::text
			            ELSE \'Non Cyto\'::text
			        END, (\' - \'::text || instruksitindakan_t.qty)) AS instruksi,
			    tipepaket_m.tipepaket_nama AS paket,
			    NULL::text AS tindakan,
			    instruksitindakan_t.qty,
			    instruksitindakan_t.is_cyto,
			    instruksitindakan_t.qty_sisa,
			    instruksitindakan_t.dokterdpjp_id AS dokter_id,
			    dokter.nama_pegawai AS dokter,
			    instruksitindakan_t.status_implementasi,
			    fgetnamalookup((instruksitindakan_t.status_implementasi)::integer) AS status,
			    instruksi_t.is_deleted AS instruksi_deleted,
			    instruksitindakan_t.is_deleted AS tindakan_deleted,
			    tipepaket_m.tipepaket_nama AS tindakaninstruksi_nama,
			        CASE
			            WHEN (instruksitindakan_t.is_cyto = true) THEN \'Cyto\'::text
			            ELSE \'Non Cyto\'::text
			        END AS ket_cyto,
			    NULL::character varying AS ket_racik_nama,
			    NULL::character varying AS ket_racik,
			    cppt_t.pegawai_id AS cpptpegawai_id,
			    cppt_t.is_verifikasi AS is_verifikasi_dpjp,
			    array_to_json(ARRAY( SELECT daftartindakan_m.daftartindakan_nama
			           FROM (paketpelayanan_mp
			             LEFT JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
			          WHERE (paketpelayanan_mp.tipepaket_id = instruksitindakan_t.tipepaket_id))) AS daftar_paket,
			    instruksi_t.tgl_instruksi AS tanggal_terapi,
			    \'TINDAKANBMHP\'::text AS grouping_tipe,
			        CASE
			            WHEN ((instruksitindakan_t.status_implementasi)::text = (\'455\'::character varying)::text) THEN true
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
			    NULL::integer AS pasienkirimkeunitlain_id
			   FROM (((((((instruksi_t
			     JOIN instruksitindakan_t ON ((instruksi_t.instruksi_id = instruksitindakan_t.instruksi_id)))
			     JOIN tipepaket_m ON ((instruksitindakan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
			     JOIN pegawai_m dokter ON ((instruksitindakan_t.dokterdpjp_id = dokter.pegawai_id)))
			     LEFT JOIN cppt_t ON ((cppt_t.cppt_id = instruksi_t.cppt_id)))
			     LEFT JOIN ruangan_m ON ((instruksitindakan_t.ruangan_id = ruangan_m.ruangan_id)))
			     LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
			     LEFT JOIN pendaftaran_t ON ((cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
			UNION ALL
			 SELECT \'BMHP\'::text AS tipe_instruksi,
			    instruksi_t.instruksi_id,
			    instruksi_t.cppt_id,
			    instruksi_t.catatan_instruksi,
			    instruksitindakanbmhp_t.instruksitindakanbmhp_id AS instruksitindakan_id,
			    instruksitindakanbmhp_t.tgl_pelayanan AS tgl_instruksi,
			    instruksitindakanbmhp_t.obatalkes_id AS daftartindakan_id,
			    concat(obatalkes_m.obatalkes_nama, (\'-\'::text || instruksitindakanbmhp_t.qty)) AS instruksi,
			    tipepaket_m.tipepaket_nama AS paket,
			    daftartindakan_m.daftartindakan_nama AS tindakan,
			    instruksitindakanbmhp_t.qty,
			    NULL::boolean AS is_cyto,
			    instruksitindakanbmhp_t.qty_sisa,
			    instruksitindakanbmhp_t.dokter_id,
			    dokter.nama_pegawai AS dokter,
			    instruksitindakanbmhp_t.status_implementasi,
			    fgetnamalookup((instruksitindakanbmhp_t.status_implementasi)::integer) AS status,
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
			    \'TINDAKANBMHP\'::text AS grouping_tipe,
			        CASE
			            WHEN ((instruksitindakanbmhp_t.status_implementasi)::text = (\'455\'::character varying)::text) THEN true
			            ELSE false
			        END AS is_telah_implementasi,
			    ruangan_m.ruangan_nama AS ruangan_pertindakan,
			    ( SELECT row_to_json(json_bmhp.*) AS row_to_json
			           FROM ( SELECT inst.instruksitindakan_id,
			                    inst.dokterdpjp_id,
			                    inst.daftartindakan_id,
			                    dokterbmhp.nama_pegawai,
			                    daftartindakanbmhp.daftartindakan_nama
			                   FROM ((instruksitindakan_t inst
			                     LEFT JOIN pegawai_m dokterbmhp ON ((dokterbmhp.pegawai_id = inst.dokterdpjp_id)))
			                     LEFT JOIN daftartindakan_m daftartindakanbmhp ON ((daftartindakanbmhp.daftartindakan_id = inst.daftartindakan_id)))
			                  WHERE (inst.instruksitindakan_id = instruksitindakanbmhp_t.instruksitindakan_id)) json_bmhp) AS bmhp_tindakandetail,
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
			    NULL::integer AS pasienkirimkeunitlain_id
			   FROM ((((((((((instruksi_t
			     JOIN instruksitindakanbmhp_t ON ((instruksi_t.instruksi_id = instruksitindakanbmhp_t.instruksi_id)))
			     LEFT JOIN instruksitindakan_t ON ((instruksitindakanbmhp_t.instruksitindakan_id = instruksitindakan_t.instruksitindakan_id)))
			     JOIN obatalkes_m ON ((instruksitindakanbmhp_t.obatalkes_id = obatalkes_m.obatalkes_id)))
			     LEFT JOIN daftartindakan_m ON ((instruksitindakanbmhp_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
			     LEFT JOIN tipepaket_m ON ((instruksitindakanbmhp_t.tipepaket_id = tipepaket_m.tipepaket_id)))
			     LEFT JOIN pegawai_m dokter ON ((instruksitindakanbmhp_t.dokter_id = dokter.pegawai_id)))
			     LEFT JOIN cppt_t ON ((cppt_t.cppt_id = instruksi_t.cppt_id)))
			     LEFT JOIN ruangan_m ON ((instruksitindakanbmhp_t.ruangan_id = ruangan_m.ruangan_id)))
			     LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
			     LEFT JOIN pendaftaran_t ON ((cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
			UNION ALL
			 SELECT
			        CASE
			            WHEN ((racikan_m.racikan_singkatan)::text = \'OR\'::text) THEN \'RACIKAN\'::text
			            ELSE \'NONRACIKAN\'::text
			        END AS tipe_instruksi,
			    instruksi_t.instruksi_id,
			    instruksi_t.cppt_id,
			    instruksi_t.catatan_instruksi,
			    resepturdetail_t.resepturdetail_id AS instruksitindakan_id,
			        CASE
			            WHEN (resepturdetail_t.tgl_resepturdetail IS NULL) THEN resepturdetail_t.created_date
			            ELSE resepturdetail_t.tgl_resepturdetail
			        END AS tgl_instruksi,
			    resepturdetail_t.obatalkes_id AS daftartindakan_id,
			    concat(obatalkes_m.obatalkes_nama, (\' - \'::text || resepturdetail_t.qty_reseptur)) AS instruksi,
			    NULL::character varying AS paket,
			    NULL::text AS tindakan,
			    resepturdetail_t.qty_reseptur AS qty,
			    NULL::boolean AS is_cyto,
			    NULL::integer AS qty_sisa,
			    reseptur_t.pegawai_id AS dokter_id,
			    dokter.nama_pegawai AS dokter,
			    (reseptur_t.status_reseptur)::character varying AS status_implementasi,
			    fgetnamalookup(reseptur_t.status_reseptur) AS status,
			    instruksi_t.is_deleted AS instruksi_deleted,
			    resepturdetail_t.is_deleted AS tindakan_deleted,
			    obatalkes_m.obatalkes_nama AS tindakaninstruksi_nama,
			    NULL::text AS ket_cyto,
			    racikan_m.racikan_nama AS ket_racik_nama,
			    racikan_m.racikan_singkatan AS ket_racik,
			    cppt_t.pegawai_id AS cpptpegawai_id,
			    cppt_t.is_verifikasi AS is_verifikasi_dpjp,
			    array_to_json(NULL::character varying[]) AS daftar_paket,
			    instruksi_t.tgl_instruksi AS tanggal_terapi,
			    \'RESEPTUR\'::text AS grouping_tipe,
			        CASE
			            WHEN (((reseptur_t.status_reseptur)::character varying)::text = (\'347\'::character varying)::text) THEN true
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
			    NULL::integer AS pasienkirimkeunitlain_id
			   FROM (((((((((instruksi_t
			     JOIN reseptur_t ON ((instruksi_t.instruksi_id = reseptur_t.instruksi_id)))
			     JOIN resepturdetail_t ON ((reseptur_t.reseptur_id = resepturdetail_t.reseptur_id)))
			     JOIN obatalkes_m ON ((resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
			     JOIN pegawai_m dokter ON ((reseptur_t.pegawai_id = dokter.pegawai_id)))
			     LEFT JOIN racikan_m ON ((resepturdetail_t.racikan_id = racikan_m.racikan_id)))
			     LEFT JOIN cppt_t ON ((cppt_t.cppt_id = instruksi_t.cppt_id)))
			     LEFT JOIN ruangan_m ON ((reseptur_t.ruangan_id = ruangan_m.ruangan_id)))
			     LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
			     LEFT JOIN pendaftaran_t ON ((cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
			UNION ALL
			 SELECT \'RACIKAN\'::text AS tipe_instruksi,
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
			    (reseptur_t.status_reseptur)::character varying AS status_implementasi,
			    fgetnamalookup(reseptur_t.status_reseptur) AS status,
			    instruksi_t.is_deleted AS instruksi_deleted,
			    resepturracikan_t.is_deleted AS tindakan_deleted,
			    resepturracikan_t.racikan AS tindakaninstruksi_nama,
			    NULL::text AS ket_cyto,
			    \'Racikan\'::character varying AS ket_racik_nama,
			    \'OR\'::character varying AS ket_racik,
			    cppt_t.pegawai_id AS cpptpegawai_id,
			    cppt_t.is_verifikasi AS is_verifikasi_dpjp,
			    array_to_json(NULL::character varying[]) AS daftar_paket,
			    instruksi_t.tgl_instruksi AS tanggal_terapi,
			    \'RESEPTUR\'::text AS grouping_tipe,
			        CASE
			            WHEN (((reseptur_t.status_reseptur)::character varying)::text = (\'347\'::character varying)::text) THEN true
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
			    NULL::integer AS pasienkirimkeunitlain_id
			   FROM (((((((instruksi_t
			     JOIN reseptur_t ON ((instruksi_t.instruksi_id = reseptur_t.instruksi_id)))
			     JOIN resepturracikan_t ON ((reseptur_t.reseptur_id = resepturracikan_t.reseptur_id)))
			     JOIN pegawai_m dokter ON ((reseptur_t.pegawai_id = dokter.pegawai_id)))
			     LEFT JOIN cppt_t ON ((cppt_t.cppt_id = instruksi_t.cppt_id)))
			     LEFT JOIN ruangan_m ON ((reseptur_t.ruangan_id = ruangan_m.ruangan_id)))
			     LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
			     LEFT JOIN pendaftaran_t ON ((cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
			UNION ALL
			 SELECT \'LAB_TINDAKAN\'::text AS tipe_instruksi,
			    instruksi_t.instruksi_id,
			    instruksi_t.cppt_id,
			    instruksi_t.catatan_instruksi,
			    permintaankepenunjang_t.permintaankepenunjang_id AS instruksitindakan_id,
			    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_instruksi,
			    permintaankepenunjang_t.daftartindakan_id,
			    concat(daftartindakan_m.daftartindakan_nama, \'-\',
			        CASE
			            WHEN (permintaankepenunjang_t.is_cyto = true) THEN \'Cyto\'::text
			            ELSE \'Non Cyto\'::text
			        END, (\'-\'::text || permintaankepenunjang_t.qtypermintaan)) AS instruksi,
			    NULL::character varying AS paket,
			    NULL::text AS tindakan,
			    permintaankepenunjang_t.qtypermintaan AS qty,
			    permintaankepenunjang_t.is_cyto,
			    NULL::integer AS qty_sisa,
			    pasienkirimkeunitlain_t.pegawai_id AS dokter_id,
			    dokter.nama_pegawai AS dokter,
			        CASE
			            WHEN (pasienmasukpenunjang_t.status_periksa IS NULL) THEN pasienkirimkeunitlain_t.status_penunjang
			            ELSE pasienmasukpenunjang_t.status_periksa
			        END AS status_implementasi,
			        CASE
			            WHEN (pasienmasukpenunjang_t.status_periksa IS NULL) THEN fgetnamalookup((pasienkirimkeunitlain_t.status_penunjang)::integer)
			            ELSE fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer)
			        END AS status,
			    instruksi_t.is_deleted AS instruksi_deleted,
			    permintaankepenunjang_t.is_deleted AS tindakan_deleted,
			    daftartindakan_m.daftartindakan_nama AS tindakaninstruksi_nama,
			        CASE
			            WHEN (permintaankepenunjang_t.is_cyto = true) THEN \'Cyto\'::text
			            ELSE \'Non Cyto\'::text
			        END AS ket_cyto,
			    NULL::character varying AS ket_racik_nama,
			    NULL::character varying AS ket_racik,
			    cppt_t.pegawai_id AS cpptpegawai_id,
			    cppt_t.is_verifikasi AS is_verifikasi_dpjp,
			    array_to_json(NULL::character varying[]) AS daftar_paket,
			    instruksi_t.tgl_instruksi AS tanggal_terapi,
			    \'PENUNJANG\'::text AS grouping_tipe,
			        CASE
			            WHEN ((pasienkirimkeunitlain_t.status_penunjang)::text = (\'471\'::character varying)::text) THEN true
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
			    pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
			   FROM (((((((((instruksi_t
			     JOIN pasienkirimkeunitlain_t ON ((instruksi_t.instruksi_id = pasienkirimkeunitlain_t.instruksi_id)))
			     JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
			     LEFT JOIN pasienmasukpenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id)))
			     JOIN daftartindakan_m ON ((permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
			     JOIN pegawai_m dokter ON ((pasienkirimkeunitlain_t.pegawai_id = dokter.pegawai_id)))
			     LEFT JOIN ruangan_m ON ((pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id)))
			     LEFT JOIN cppt_t ON ((cppt_t.cppt_id = instruksi_t.cppt_id)))
			     LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
			     LEFT JOIN pendaftaran_t ON ((cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
			  WHERE (pasienkirimkeunitlain_t.instalasi_id = 4)
			UNION ALL
			 SELECT \'LAB_PAKET\'::text AS tipe_instruksi,
			    instruksi_t.instruksi_id,
			    instruksi_t.cppt_id,
			    instruksi_t.catatan_instruksi,
			    permintaankepenunjang_t.permintaankepenunjang_id AS instruksitindakan_id,
			    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_instruksi,
			    permintaankepenunjang_t.tipepaket_id AS daftartindakan_id,
			    concat(tipepaket_m.tipepaket_nama, \'-\',
			        CASE
			            WHEN (permintaankepenunjang_t.is_cyto = true) THEN \'Cyto\'::text
			            ELSE \'Non Cyto\'::text
			        END, (\'-\'::text || permintaankepenunjang_t.qtypermintaan)) AS instruksi,
			    tipepaket_m.tipepaket_nama AS paket,
			    NULL::text AS tindakan,
			    permintaankepenunjang_t.qtypermintaan AS qty,
			    permintaankepenunjang_t.is_cyto,
			    NULL::integer AS qty_sisa,
			    pasienkirimkeunitlain_t.pegawai_id AS dokter_id,
			    dokter.nama_pegawai AS dokter,
			        CASE
			            WHEN (pasienmasukpenunjang_t.status_periksa IS NULL) THEN pasienkirimkeunitlain_t.status_penunjang
			            ELSE pasienmasukpenunjang_t.status_periksa
			        END AS status_implementasi,
			        CASE
			            WHEN (pasienmasukpenunjang_t.status_periksa IS NULL) THEN fgetnamalookup((pasienkirimkeunitlain_t.status_penunjang)::integer)
			            ELSE fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer)
			        END AS status,
			    instruksi_t.is_deleted AS instruksi_deleted,
			    permintaankepenunjang_t.is_deleted AS tindakan_deleted,
			    tipepaket_m.tipepaket_nama AS tindakaninstruksi_nama,
			        CASE
			            WHEN (permintaankepenunjang_t.is_cyto = true) THEN \'Cyto\'::text
			            ELSE \'Non Cyto\'::text
			        END AS ket_cyto,
			    NULL::character varying AS ket_racik_nama,
			    NULL::character varying AS ket_racik,
			    cppt_t.pegawai_id AS cpptpegawai_id,
			    cppt_t.is_verifikasi AS is_verifikasi_dpjp,
			    array_to_json(ARRAY( SELECT daftartindakan_m.daftartindakan_nama
			           FROM (paketpelayanan_mp
			             LEFT JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
			          WHERE (paketpelayanan_mp.tipepaket_id = permintaankepenunjang_t.tipepaket_id))) AS daftar_paket,
			    instruksi_t.tgl_instruksi AS tanggal_terapi,
			    \'PENUNJANG\'::text AS grouping_tipe,
			        CASE
			            WHEN ((pasienkirimkeunitlain_t.status_penunjang)::text = (\'471\'::character varying)::text) THEN true
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
			    pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
			   FROM (((((((((instruksi_t
			     JOIN pasienkirimkeunitlain_t ON ((instruksi_t.instruksi_id = pasienkirimkeunitlain_t.instruksi_id)))
			     JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
			     LEFT JOIN pasienmasukpenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id)))
			     JOIN tipepaket_m ON ((permintaankepenunjang_t.tipepaket_id = tipepaket_m.tipepaket_id)))
			     JOIN pegawai_m dokter ON ((pasienkirimkeunitlain_t.pegawai_id = dokter.pegawai_id)))
			     LEFT JOIN ruangan_m ON ((pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id)))
			     LEFT JOIN cppt_t ON ((cppt_t.cppt_id = instruksi_t.cppt_id)))
			     LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
			     LEFT JOIN pendaftaran_t ON ((cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
			  WHERE (pasienkirimkeunitlain_t.instalasi_id = 4)
			UNION ALL
			 SELECT \'RAD_TINDAKAN\'::text AS tipe_instruksi,
			    instruksi_t.instruksi_id,
			    instruksi_t.cppt_id,
			    instruksi_t.catatan_instruksi,
			    permintaankepenunjang_t.permintaankepenunjang_id AS instruksitindakan_id,
			    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_instruksi,
			    permintaankepenunjang_t.daftartindakan_id,
			    concat(daftartindakan_m.daftartindakan_nama, \'-\',
			        CASE
			            WHEN (permintaankepenunjang_t.is_cyto = true) THEN \'Cyto\'::text
			            ELSE \'Non Cyto\'::text
			        END, (\'-\'::text || permintaankepenunjang_t.qtypermintaan)) AS instruksi,
			    NULL::character varying AS paket,
			    NULL::text AS tindakan,
			    permintaankepenunjang_t.qtypermintaan AS qty,
			    permintaankepenunjang_t.is_cyto,
			    NULL::integer AS qty_sisa,
			    pasienkirimkeunitlain_t.pegawai_id AS dokter_id,
			    dokter.nama_pegawai AS dokter,
			        CASE
			            WHEN (pasienmasukpenunjang_t.status_periksa IS NULL) THEN pasienkirimkeunitlain_t.status_penunjang
			            ELSE pasienmasukpenunjang_t.status_periksa
			        END AS status_implementasi,
			        CASE
			            WHEN (pasienmasukpenunjang_t.status_periksa IS NULL) THEN fgetnamalookup((pasienkirimkeunitlain_t.status_penunjang)::integer)
			            ELSE fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer)
			        END AS status,
			    instruksi_t.is_deleted AS instruksi_deleted,
			    permintaankepenunjang_t.is_deleted AS tindakan_deleted,
			    daftartindakan_m.daftartindakan_nama AS tindakaninstruksi_nama,
			        CASE
			            WHEN (permintaankepenunjang_t.is_cyto = true) THEN \'Cyto\'::text
			            ELSE \'Non Cyto\'::text
			        END AS ket_cyto,
			    NULL::character varying AS ket_racik_nama,
			    NULL::character varying AS ket_racik,
			    cppt_t.pegawai_id AS cpptpegawai_id,
			    cppt_t.is_verifikasi AS is_verifikasi_dpjp,
			    array_to_json(NULL::character varying[]) AS daftar_paket,
			    instruksi_t.tgl_instruksi AS tanggal_terapi,
			    \'PENUNJANG\'::text AS grouping_tipe,
			        CASE
			            WHEN ((pasienkirimkeunitlain_t.status_penunjang)::text = (\'471\'::character varying)::text) THEN true
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
			    pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
			   FROM (((((((((instruksi_t
			     JOIN pasienkirimkeunitlain_t ON ((instruksi_t.instruksi_id = pasienkirimkeunitlain_t.instruksi_id)))
			     JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
			     LEFT JOIN pasienmasukpenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id)))
			     JOIN daftartindakan_m ON ((permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
			     JOIN pegawai_m dokter ON ((pasienkirimkeunitlain_t.pegawai_id = dokter.pegawai_id)))
			     LEFT JOIN ruangan_m ON ((pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id)))
			     LEFT JOIN cppt_t ON ((cppt_t.cppt_id = instruksi_t.cppt_id)))
			     LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
			     LEFT JOIN pendaftaran_t ON ((cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
			  WHERE (pasienkirimkeunitlain_t.instalasi_id = 5)
			UNION ALL
			 SELECT \'RAD_PAKET\'::text AS tipe_instruksi,
			    instruksi_t.instruksi_id,
			    instruksi_t.cppt_id,
			    instruksi_t.catatan_instruksi,
			    permintaankepenunjang_t.permintaankepenunjang_id AS instruksitindakan_id,
			    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_instruksi,
			    permintaankepenunjang_t.tipepaket_id AS daftartindakan_id,
			    concat(tipepaket_m.tipepaket_nama, \'-\',
			        CASE
			            WHEN (permintaankepenunjang_t.is_cyto = true) THEN \'Cyto\'::text
			            ELSE \'Non Cyto\'::text
			        END, (\'-\'::text || permintaankepenunjang_t.qtypermintaan)) AS instruksi,
			    tipepaket_m.tipepaket_nama AS paket,
			    NULL::text AS tindakan,
			    permintaankepenunjang_t.qtypermintaan AS qty,
			    permintaankepenunjang_t.is_cyto,
			    NULL::integer AS qty_sisa,
			    pasienkirimkeunitlain_t.pegawai_id AS dokter_id,
			    dokter.nama_pegawai AS dokter,
			        CASE
			            WHEN (pasienmasukpenunjang_t.status_periksa IS NULL) THEN pasienkirimkeunitlain_t.status_penunjang
			            ELSE pasienmasukpenunjang_t.status_periksa
			        END AS status_implementasi,
			        CASE
			            WHEN (pasienmasukpenunjang_t.status_periksa IS NULL) THEN fgetnamalookup((pasienkirimkeunitlain_t.status_penunjang)::integer)
			            ELSE fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer)
			        END AS status,
			    instruksi_t.is_deleted AS instruksi_deleted,
			    permintaankepenunjang_t.is_deleted AS tindakan_deleted,
			    tipepaket_m.tipepaket_nama AS tindakaninstruksi_nama,
			        CASE
			            WHEN (permintaankepenunjang_t.is_cyto = true) THEN \'Cyto\'::text
			            ELSE \'Non Cyto\'::text
			        END AS ket_cyto,
			    NULL::character varying AS ket_racik_nama,
			    NULL::character varying AS ket_racik,
			    cppt_t.pegawai_id AS cpptpegawai_id,
			    cppt_t.is_verifikasi AS is_verifikasi_dpjp,
			    array_to_json(ARRAY( SELECT daftartindakan_m.daftartindakan_nama
			           FROM (paketpelayanan_mp
			             LEFT JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
			          WHERE (paketpelayanan_mp.tipepaket_id = permintaankepenunjang_t.tipepaket_id))) AS daftar_paket,
			    instruksi_t.tgl_instruksi AS tanggal_terapi,
			    \'PENUNJANG\'::text AS grouping_tipe,
			        CASE
			            WHEN ((pasienkirimkeunitlain_t.status_penunjang)::text = (\'471\'::character varying)::text) THEN true
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
			    pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
			   FROM (((((((((instruksi_t
			     JOIN pasienkirimkeunitlain_t ON ((instruksi_t.instruksi_id = pasienkirimkeunitlain_t.instruksi_id)))
			     JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
			     LEFT JOIN pasienmasukpenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id)))
			     JOIN tipepaket_m ON ((permintaankepenunjang_t.tipepaket_id = tipepaket_m.tipepaket_id)))
			     JOIN pegawai_m dokter ON ((pasienkirimkeunitlain_t.pegawai_id = dokter.pegawai_id)))
			     LEFT JOIN ruangan_m ON ((pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id)))
			     LEFT JOIN cppt_t ON ((cppt_t.cppt_id = instruksi_t.cppt_id)))
			     LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
			     LEFT JOIN pendaftaran_t ON ((cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
			  WHERE (pasienkirimkeunitlain_t.instalasi_id = 5)
			UNION ALL
			 SELECT \'BED_TINDAKAN\'::text AS tipe_instruksi,
			    instruksi_t.instruksi_id,
			    instruksi_t.cppt_id,
			    instruksi_t.catatan_instruksi,
			    permintaankepenunjang_t.permintaankepenunjang_id AS instruksitindakan_id,
			    COALESCE(permintaankepenunjang_t.tglpermintaankepenunjang, pasienkirimkeunitlain_t.tgl_kirimpasien) AS tgl_instruksi,
			    permintaankepenunjang_t.daftartindakan_id,
			    concat(daftartindakan_m.daftartindakan_nama, \'-\',
			        CASE
			            WHEN (permintaankepenunjang_t.is_cyto = true) THEN \'Cyto\'::text
			            ELSE \'Non Cyto\'::text
			        END, (\'-\'::text || permintaankepenunjang_t.qtypermintaan)) AS instruksi,
			    NULL::character varying AS paket,
			    NULL::text AS tindakan,
			    permintaankepenunjang_t.qtypermintaan AS qty,
			    permintaankepenunjang_t.is_cyto,
			    NULL::integer AS qty_sisa,
			    pasienkirimkeunitlain_t.pegawai_id AS dokter_id,
			    dokter.nama_pegawai AS dokter,
			        CASE
			            WHEN (pasienmasukpenunjang_t.status_periksa IS NULL) THEN pasienkirimkeunitlain_t.status_penunjang
			            ELSE pasienmasukpenunjang_t.status_periksa
			        END AS status_implementasi,
			        CASE
			            WHEN (pasienmasukpenunjang_t.status_periksa IS NULL) THEN fgetnamalookup((pasienkirimkeunitlain_t.status_penunjang)::integer)
			            ELSE fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer)
			        END AS status,
			    instruksi_t.is_deleted AS instruksi_deleted,
			    permintaankepenunjang_t.is_deleted AS tindakan_deleted,
			    daftartindakan_m.daftartindakan_nama AS tindakaninstruksi_nama,
			        CASE
			            WHEN (permintaankepenunjang_t.is_cyto = true) THEN \'Cyto\'::text
			            ELSE \'Non Cyto\'::text
			        END AS ket_cyto,
			    NULL::character varying AS ket_racik_nama,
			    NULL::character varying AS ket_racik,
			    cppt_t.pegawai_id AS cpptpegawai_id,
			    cppt_t.is_verifikasi AS is_verifikasi_dpjp,
			    array_to_json(NULL::character varying[]) AS daftar_paket,
			    instruksi_t.tgl_instruksi AS tanggal_terapi,
			    \'PENUNJANG\'::text AS grouping_tipe,
			        CASE
			            WHEN ((pasienkirimkeunitlain_t.status_penunjang)::text = (\'471\'::character varying)::text) THEN true
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
			    pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
			   FROM (((((((((instruksi_t
			     JOIN pasienkirimkeunitlain_t ON ((instruksi_t.instruksi_id = pasienkirimkeunitlain_t.instruksi_id)))
			     JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
			     LEFT JOIN pasienmasukpenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id)))
			     JOIN daftartindakan_m ON ((permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
			     JOIN pegawai_m dokter ON ((pasienkirimkeunitlain_t.pegawai_id = dokter.pegawai_id)))
			     LEFT JOIN ruangan_m ON ((pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id)))
			     LEFT JOIN cppt_t ON ((cppt_t.cppt_id = instruksi_t.cppt_id)))
			     LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
			     LEFT JOIN pendaftaran_t ON ((cppt_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
			  WHERE (pasienkirimkeunitlain_t.instalasi_id = 12);
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."infopasienrd_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infopasienrd_v" AS  SELECT pendaftaran_t.pendaftaran_id,
			    pendaftaran_t.tgl_pendaftaran,
			    pendaftaran_t.no_pendaftaran,
			    pendaftaran_t.pasien_id,
			    pasien_m.nama_pasien,
			    pasien_m.no_rekam_medik,
			    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
			    pendaftaran_t.penjamin_id,
			    penjamin_m.penjamin_nama,
			    pendaftaran_t.pegawai_id AS dokter_jaga_id,
			    dok_jaga.nama_pegawai AS dokter_jaga,
			    ( SELECT dokpj.dokterbaru_id
			           FROM gantidokterpj_t dokpj
			          WHERE ((dokpj.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (dokpj.jenis_dokter = 485) AND (dokpj.is_active = true) AND (dokpj.is_deleted = false))
			         LIMIT 1) AS dokter_id,
			    ( SELECT dokter.nama_pegawai
			           FROM (gantidokterpj_t dokpj
			             JOIN ( SELECT pegawai_m.pegawai_id,
			                    pegawai_m.nama_pegawai
			                   FROM (pegawai_m
			                     JOIN ruanganpegawai_mp ON ((pegawai_m.pegawai_id = ruanganpegawai_mp.pegawai_id)))
			                  WHERE ((pegawai_m.kelompokpegawai_id = 1) AND (pegawai_m.is_active = true) AND (pegawai_m.is_deleted = false))) dokter ON ((dokpj.dokterbaru_id = dokter.pegawai_id)))
			          WHERE ((dokpj.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (dokpj.jenis_dokter = 485) AND (dokpj.is_active = true) AND (dokpj.is_deleted = false))
			         LIMIT 1) AS dokter,
			    ( SELECT dokpj.jenis_dokter
			           FROM gantidokterpj_t dokpj
			          WHERE ((dokpj.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (dokpj.jenis_dokter = 485) AND (dokpj.is_active = true) AND (dokpj.is_deleted = false))
			         LIMIT 1) AS jenis_dokter_id,
			    ( SELECT fgetnamalookup(gantidokterpj_t.jenis_dokter) AS lookup_name
			           FROM gantidokterpj_t dokpj
			          WHERE ((dokpj.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (dokpj.jenis_dokter = 485) AND (dokpj.is_active = true) AND (dokpj.is_deleted = false))
			         LIMIT 1) AS jenis_dokter,
			    pendaftaran_t.status_periksa AS status_periksa_id,
			    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa,
			    pendaftaran_t.instalasi_id,
			    pendaftaran_t.ruangan_id,
			    ruangan_m.ruangan_nama,
			    pasien_m.jeniskelamin,
			    pendaftaran_t.*::pendaftaran_t AS pendaftaran_t,
			    pendaftaran_t.kelaspelayanan_id,
			    kelaspelayanan_m.kelaspelayanan_nama, 
			    pendaftaran_t.jeniskasuspenyakit_id,
			    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
			    pendaftaran_t.carabayar_id,
			    carabayar_m.carabayar_nama,
			    pendaftaran_t.umur,
			    pasien_m.tanggal_lahir,
			    pasien_m.photopasien,
			    bpjs_t.nosep,
			    carabayar_m.carabayar_warna,
			    carabayar_m.carabayar_kode_warna,
			    pendaftaran_t.pasienpulang_id,
			    pekerjaan_m.pekerjaan_nama,
			    pasien_m.catatanpenting_pasien,
			    asesmenperawatrd_t.is_alergi AS alergi,
			    pasien_m.alamat_pasien
			   FROM ((((((((((((pendaftaran_t
			     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
			     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
			     LEFT JOIN pegawai_m dok_jaga ON ((pendaftaran_t.pegawai_id = dok_jaga.pegawai_id)))
			     LEFT JOIN gantidokterpj_t ON ((pendaftaran_t.pendaftaran_id = gantidokterpj_t.pendaftaran_id)))
			     LEFT JOIN pegawai_m dok_dpjp ON ((gantidokterpj_t.dokterbaru_id = dok_dpjp.pegawai_id)))
			     JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
			     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
			     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
			     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
			     LEFT JOIN bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
			     LEFT JOIN asesmenperawatrd_t ON ((pendaftaran_t.pendaftaran_id = asesmenperawatrd_t.pendaftaran_id)))
			     LEFT JOIN pekerjaan_m ON ((COALESCE(asesmenperawatrd_t.pekerjaan_id, pasien_m.pekerjaan_id) = pekerjaan_m.pekerjaan_id)))
			  WHERE (pendaftaran_t.instalasi_id = 2);
        ');

		$this->execute('
            DROP VIEW IF EXISTS "public"."infopasienrs_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infopasienrs_v" AS  SELECT \'RJ\'::text AS jenis,
			    pendaftaran_t.pendaftaran_id,
			    pendaftaran_t.tgl_pendaftaran,
			    pendaftaran_t.no_pendaftaran,
			    pasien_m.no_rekam_medik, 
			    pasien_m.nama_pasien,
			    pasien_m.tanggal_lahir,
			        CASE pasien_m.jeniskelamin
			            WHEN \'15\'::text THEN \'L\'::text
			            ELSE \'P\'::text
			        END AS jk,
			    pegawai_m.nama_pegawai,
			    carabayar_m.carabayar_nama,
			    penjamin_m.penjamin_nama,
			    (\'Kelas \'::text || bpjs_t.klsrawat) AS hak_kelas,
			    kelaspelayanan_m.kelaspelayanan_nama,
			    \'-\'::text AS kelas_tagihan,
			        CASE
			            WHEN (ruangan_asal.pendaftaranbaru_id IS NULL) THEN false
			            ELSE true
			        END AS is_konsul,
			    false AS is_pasientitipan,
			    carabayar_m.carabayar_kode_warna,
			    carabayar_m.carabayar_warna,
			    pendaftaran_t.carabayar_id,
			    pendaftaran_t.penjamin_id,
			    pendaftaran_t.jeniskasuspenyakit_id,
			    pendaftaran_t.ruangan_id,
			    pendaftaran_t.kelaspelayanan_id,
			    pendaftaran_t.pegawai_id,
			    (pendaftaran_t.status_periksa)::integer AS status_periksa,
			    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa_nama,
			        CASE
			            WHEN (kelahiranbayi_t.is_bayi > 0) THEN true
			            ELSE false
			        END AS is_bayi,
			    false AS is_stoppasientitipan,
			        CASE COALESCE(pendaftaran_t.pasienpulang_id, 0)
			            WHEN 0 THEN false
			            ELSE true
			        END AS is_pulang,
			        CASE pendaftaran_t.status_bayar
			            WHEN 348 THEN true
			            ELSE false
			        END AS is_lunas,
			    pendaftaran_t.is_stopakomodasi,
			    NULL::integer AS pasienmasukpenunjang_id,
			    pendaftaran_t.keterangan_pendaftaran,
			    ruangan_m.instalasi_id,
			    ruangan_m.ruangan_nama,
			    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
			    NULL::text AS kamarruangan_nokamar,
			    NULL::text AS no_tempattidur,
			    NULL::text AS kettempattidur_nama,
			    NULL::text AS status_kamar,
			    NULL::timestamp without time zone AS tgl_pindahkamar,
			    NULL::timestamp without time zone AS rencana_pulang,
			    antrian_t.no_antrian,
			        CASE
			            WHEN (soap_rj.soap_id IS NULL) THEN false
			            ELSE true
			        END AS is_isisoap,
			    peg_create.pegawai_id AS peg_create_id,
			    peg_create.nama_pegawai AS peg_create_nama,
			    NULL::integer AS konsulpoli_id,
			    anamnesa_t.alergi,
			    pasien_m.no_telepon_pasien,
			    pasien_m.no_mobile_pasien,
			    pasien_m.no_identitas_pasien
			   FROM ((((((((((((((((((pendaftaran_t
			     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
			     LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
			     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
			     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
			     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
			     LEFT JOIN caramasuk_m ON ((pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id)))
			     JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
			     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
			     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
			     LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
			     LEFT JOIN ( SELECT konsulpoli_t.pendaftaranbaru_id,
			            konsulpoli_t.asalpoliklinikkonsul_id,
			            poli_asal.ruangan_nama
			           FROM (konsulpoli_t
			             LEFT JOIN ruangan_m poli_asal ON ((konsulpoli_t.asalpoliklinikkonsul_id = poli_asal.ruangan_id)))) ruangan_asal ON ((pendaftaran_t.pendaftaran_id = ruangan_asal.pendaftaranbaru_id)))
			     LEFT JOIN bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
			     LEFT JOIN ( SELECT count(*) AS is_bayi,
			            kelahiranbayi_t_1.pendaftaranbaru_id
			           FROM kelahiranbayi_t kelahiranbayi_t_1
			          GROUP BY kelahiranbayi_t_1.pendaftaranbaru_id) kelahiranbayi_t ON ((pendaftaran_t.pendaftaran_id = kelahiranbayi_t.pendaftaranbaru_id)))
			     LEFT JOIN antrian_t ON (((pendaftaran_t.antrian_id = antrian_t.antrian_id) AND (antrian_t.jenisantrian_id = 312))))
			     LEFT JOIN ( SELECT soaprj_t.pendaftaran_id AS soap_id
			           FROM soaprj_t
			          WHERE (soaprj_t.is_deleted = false)
			          GROUP BY soaprj_t.pendaftaran_id) soap_rj ON ((pendaftaran_t.pendaftaran_id = soap_rj.soap_id)))
			     LEFT JOIN loginpemakai_k ON ((pendaftaran_t.created_by = loginpemakai_k.loginpemakai_id)))
			     LEFT JOIN pegawai_m peg_create ON ((loginpemakai_k.pegawai_id = peg_create.pegawai_id)))
			     LEFT JOIN anamnesa_t ON ((pendaftaran_t.pendaftaran_id = anamnesa_t.pendaftaran_id)))
			  WHERE (pendaftaran_t.instalasi_id = 1)
			UNION ALL
			 SELECT \'RJ\'::text AS jenis,
			    pendaftaran_t.pendaftaran_id,
			    pendaftaran_t.tgl_pendaftaran,
			    pendaftaran_t.no_pendaftaran,
			    pasien_m.no_rekam_medik,
			    pasien_m.nama_pasien,
			    pasien_m.tanggal_lahir,
			        CASE pasien_m.jeniskelamin
			            WHEN \'15\'::text THEN \'L\'::text
			            ELSE \'P\'::text
			        END AS jk,
			    pegawai_m.nama_pegawai,
			    carabayar_m.carabayar_nama,
			    penjamin_m.penjamin_nama,
			    (\'Kelas \'::text || bpjs_t.klsrawat) AS hak_kelas,
			    kelaspelayanan_m.kelaspelayanan_nama,
			    \'-\'::text AS kelas_tagihan,
			    true AS is_konsul,
			    false AS is_pasientitipan,
			    carabayar_m.carabayar_kode_warna,
			    carabayar_m.carabayar_warna,
			    pendaftaran_t.carabayar_id,
			    pendaftaran_t.penjamin_id,
			    pendaftaran_t.jeniskasuspenyakit_id,
			    ruangan_m.ruangan_id,
			    pendaftaran_t.kelaspelayanan_id,
			    konsulpoli_t.pegawai_id,
			    (konsulpoli_t.status_periksa)::integer AS status_periksa,
			    fgetnamalookup((konsulpoli_t.status_periksa)::integer) AS status_periksa_nama,
			        CASE
			            WHEN (kelahiranbayi_t.is_bayi > 0) THEN true
			            ELSE false
			        END AS is_bayi,
			    false AS is_stoppasientitipan,
			        CASE COALESCE(konsulpoli_t.pasienpulang_id, 0)
			            WHEN 0 THEN false
			            ELSE true
			        END AS is_pulang,
			        CASE pendaftaran_t.status_bayar
			            WHEN 348 THEN true
			            ELSE false
			        END AS is_lunas,
			    pendaftaran_t.is_stopakomodasi,
			    NULL::integer AS pasienmasukpenunjang_id,
			    pendaftaran_t.keterangan_pendaftaran,
			    ruangan_m.instalasi_id,
			    ruangan_m.ruangan_nama,
			    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
			    NULL::text AS kamarruangan_nokamar,
			    NULL::text AS no_tempattidur,
			    NULL::text AS kettempattidur_nama,
			    NULL::text AS status_kamar,
			    NULL::timestamp without time zone AS tgl_pindahkamar,
			    NULL::timestamp without time zone AS rencana_pulang,
			    antrian_t.no_antrian,
			        CASE
			            WHEN (soap_rj.soap_id IS NULL) THEN false
			            ELSE true
			        END AS is_isisoap,
			    peg_create.pegawai_id AS peg_create_id,
			    peg_create.nama_pegawai AS peg_create_nama,
			    konsulpoli_t.konsulpoli_id,
			    anamnesa_t.alergi,
			    pasien_m.no_telepon_pasien,
			    pasien_m.no_mobile_pasien,
			    pasien_m.no_identitas_pasien
			   FROM (((((((((((((((((((((((((((((konsulpoli_t
			     JOIN pendaftaran_t ON ((konsulpoli_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
			     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
			     LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
			     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
			     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
			     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
			     LEFT JOIN caramasuk_m ON ((pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id)))
			     LEFT JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
			     LEFT JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
			     LEFT JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
			     LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
			     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
			     LEFT JOIN pegawai_m ON ((konsulpoli_t.pegawai_id = pegawai_m.pegawai_id)))
			     JOIN ruangan_m ON ((konsulpoli_t.ruangan_id = ruangan_m.ruangan_id)))
			     JOIN ruangan_m ruanganasal_m ON ((konsulpoli_t.asalpoliklinikkonsul_id = ruanganasal_m.ruangan_id)))
			     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
			     LEFT JOIN antrian_t ON (((antrian_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (antrian_t.jenisantrian_id = 312))))
			     LEFT JOIN loket_m ON ((antrian_t.loket_id = loket_m.loket_id)))
			     LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
			     LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
			     LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
			     LEFT JOIN bpjs_t ON (((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id) AND (bpjs_t.is_deleted = false))))
			     LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
			     LEFT JOIN ( SELECT count(*) AS soap,
			            soaprj_t.pendaftaran_id
			           FROM soaprj_t
			          WHERE (soaprj_t.is_deleted IS FALSE)
			          GROUP BY soaprj_t.pendaftaran_id) soaprj ON ((pendaftaran_t.pendaftaran_id = soaprj.pendaftaran_id)))
			     LEFT JOIN ( SELECT count(*) AS is_bayi,
			            kelahiranbayi_t_1.pendaftaranbaru_id
			           FROM kelahiranbayi_t kelahiranbayi_t_1
			          GROUP BY kelahiranbayi_t_1.pendaftaranbaru_id) kelahiranbayi_t ON ((pendaftaran_t.pendaftaran_id = kelahiranbayi_t.pendaftaranbaru_id)))
			     LEFT JOIN ( SELECT soaprj_t.pendaftaran_id AS soap_id
			           FROM soaprj_t
			          WHERE (soaprj_t.is_deleted = false)
			          GROUP BY soaprj_t.pendaftaran_id) soap_rj ON ((pendaftaran_t.pendaftaran_id = soap_rj.soap_id)))
			     LEFT JOIN loginpemakai_k ON ((pendaftaran_t.created_by = loginpemakai_k.loginpemakai_id)))
			     LEFT JOIN pegawai_m peg_create ON ((loginpemakai_k.pegawai_id = peg_create.pegawai_id)))
			     LEFT JOIN anamnesa_t ON ((pendaftaran_t.pendaftaran_id = anamnesa_t.pendaftaran_id)))
			  WHERE ((pendaftaran_t.instalasi_id = 1) AND (pendaftaran_t.is_deleted = false) AND (pendaftaran_t.is_active = true) AND (konsulpoli_t.status_approve = 565) AND (konsulpoli_t.pendaftaranbaru_id IS NULL))
			UNION ALL
			 SELECT \'RD\'::text AS jenis,
			    pendaftaran_t.pendaftaran_id,
			    pendaftaran_t.tgl_pendaftaran,
			    pendaftaran_t.no_pendaftaran,
			    pasien_m.no_rekam_medik,
			    pasien_m.nama_pasien,
			    pasien_m.tanggal_lahir,
			        CASE pasien_m.jeniskelamin
			            WHEN \'15\'::text THEN \'L\'::text
			            ELSE \'P\'::text
			        END AS jk,
			    pegawai_m.nama_pegawai,
			    carabayar_m.carabayar_nama,
			    penjamin_m.penjamin_nama,
			    (\'Kelas \'::text || bpjs_t.klsrawat) AS hak_kelas,
			    kelaspelayanan_m.kelaspelayanan_nama,
			    \'-\'::text AS kelas_tagihan,
			        CASE
			            WHEN (ruangan_asal.pendaftaranbaru_id IS NULL) THEN false
			            ELSE true
			        END AS is_konsul,
			    false AS is_pasientitipan,
			    carabayar_m.carabayar_kode_warna,
			    carabayar_m.carabayar_warna,
			    pendaftaran_t.carabayar_id,
			    pendaftaran_t.penjamin_id,
			    pendaftaran_t.jeniskasuspenyakit_id,
			    pendaftaran_t.ruangan_id,
			    pendaftaran_t.kelaspelayanan_id,
			    pendaftaran_t.pegawai_id,
			    (pendaftaran_t.status_periksa)::integer AS status_periksa,
			    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa_nama,
			        CASE
			            WHEN (kelahiranbayi_t.is_bayi > 0) THEN true
			            ELSE false
			        END AS is_bayi,
			    false AS is_stoppasientitipan,
			        CASE COALESCE(pendaftaran_t.pasienpulang_id, 0)
			            WHEN 0 THEN false
			            ELSE true
			        END AS is_pulang,
			        CASE pendaftaran_t.status_bayar
			            WHEN 348 THEN true
			            ELSE false
			        END AS is_lunas,
			    pendaftaran_t.is_stopakomodasi,
			    NULL::integer AS pasienmasukpenunjang_id,
			    pendaftaran_t.keterangan_pendaftaran,
			    ruangan_m.instalasi_id,
			    ruangan_m.ruangan_nama,
			    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
			    NULL::text AS kamarruangan_nokamar,
			    NULL::text AS no_tempattidur,
			    NULL::text AS kettempattidur_nama,
			    NULL::text AS status_kamar,
			    NULL::timestamp without time zone AS tgl_pindahkamar,
			    NULL::timestamp without time zone AS rencana_pulang,
			    antrian_t.no_antrian,
			        CASE
			            WHEN (soap_rd.soap_id IS NULL) THEN false
			            ELSE true
			        END AS is_isisoap,
			    peg_create.pegawai_id AS peg_create_id,
			    peg_create.nama_pegawai AS peg_create_nama,
			    NULL::integer AS konsulpoli_id,
			    asesmenperawatrd_t.is_alergi AS alergi,
			    pasien_m.no_telepon_pasien,
			    pasien_m.no_mobile_pasien,
			    pasien_m.no_identitas_pasien
			   FROM ((((((((((((((((((pendaftaran_t
			     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
			     LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
			     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
			     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
			     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
			     LEFT JOIN caramasuk_m ON ((pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id)))
			     JOIN ruangan_m ON ((pendaftaran_t.ruangan_id = ruangan_m.ruangan_id)))
			     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
			     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
			     LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
			     LEFT JOIN ( SELECT konsulpoli_t.pendaftaranbaru_id,
			            konsulpoli_t.asalpoliklinikkonsul_id,
			            poli_asal.ruangan_nama
			           FROM (konsulpoli_t
			             LEFT JOIN ruangan_m poli_asal ON ((konsulpoli_t.asalpoliklinikkonsul_id = poli_asal.ruangan_id)))) ruangan_asal ON ((pendaftaran_t.pendaftaran_id = ruangan_asal.pendaftaranbaru_id)))
			     LEFT JOIN bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
			     LEFT JOIN ( SELECT count(*) AS is_bayi,
			            kelahiranbayi_t_1.pendaftaranbaru_id
			           FROM kelahiranbayi_t kelahiranbayi_t_1
			          GROUP BY kelahiranbayi_t_1.pendaftaranbaru_id) kelahiranbayi_t ON ((pendaftaran_t.pendaftaran_id = kelahiranbayi_t.pendaftaranbaru_id)))
			     LEFT JOIN antrian_t ON (((pendaftaran_t.antrian_id = antrian_t.antrian_id) AND (antrian_t.jenisantrian_id = 312))))
			     LEFT JOIN ( SELECT cppt_t.pendaftaran_id AS soap_id
			           FROM cppt_t
			          WHERE (cppt_t.is_deleted = false)
			          GROUP BY cppt_t.pendaftaran_id) soap_rd ON ((pendaftaran_t.pendaftaran_id = soap_rd.soap_id)))
			     LEFT JOIN loginpemakai_k ON ((pendaftaran_t.created_by = loginpemakai_k.loginpemakai_id)))
			     LEFT JOIN pegawai_m peg_create ON ((loginpemakai_k.pegawai_id = peg_create.pegawai_id)))
			     LEFT JOIN asesmenperawatrd_t ON ((asesmenperawatrd_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
			  WHERE (pendaftaran_t.instalasi_id = 2)
			UNION ALL
			 SELECT \'RI\'::text AS jenis,
			    pendaftaran_t.pendaftaran_id,
			    pasienadmisi_t.tgl_pendaftaran,
			    pendaftaran_t.no_pendaftaran,
			    pasien_m.no_rekam_medik,
			    pasien_m.nama_pasien,
			    pasien_m.tanggal_lahir,
			        CASE pasien_m.jeniskelamin
			            WHEN \'15\'::text THEN \'L\'::text
			            ELSE \'P\'::text
			        END AS jk,
			    pegawai_m.nama_pegawai,
			    carabayar_m.carabayar_nama,
			    penjamin_m.penjamin_nama,
			    (\'Kelas \'::text || bpjs_t.klsrawat) AS hak_kelas,
			    kelaspelayanan_m.kelaspelayanan_nama,
			    \'-\'::text AS kelas_tagihan,
			    false AS is_konsul,
			    pasienadmisi_t.is_pasientitipan,
			    carabayar_m.carabayar_kode_warna,
			    carabayar_m.carabayar_warna,
			    pasienadmisi_t.carabayar_id,
			    pasienadmisi_t.penjamin_id,
			    pendaftaran_t.jeniskasuspenyakit_id,
			    pasienadmisi_t.ruangan_id,
			    pasienadmisi_t.kelaspelayanan_id,
			    COALESCE(pasienadmisi_t.pegawai_id, pendaftaran_t.pegawai_id) AS pegawai_id,
			    pasienadmisi_t.status_ranap AS status_periksa,
			    fgetnamalookup(pasienadmisi_t.status_ranap) AS status_periksa_nama,
			        CASE
			            WHEN (kelahiranbayi_t.is_bayi > 0) THEN true
			            ELSE false
			        END AS is_bayi,
			        CASE
			            WHEN ((stop_titipan.pindahkamar_id IS NULL) AND (pasienadmisi_t.is_stoptitipan IS FALSE)) THEN false
			            WHEN ((stop_titipan.pindahkamar_id IS NULL) AND (pasienadmisi_t.is_stoptitipan IS TRUE)) THEN true
			            WHEN ((stop_titipan.is_pasientitipan IS FALSE) AND (stop_titipan.is_stoptitipan IS FALSE)) THEN true
			            WHEN ((stop_titipan.is_pasientitipan IS TRUE) AND (stop_titipan.is_stoptitipan IS TRUE)) THEN true
			            ELSE false
			        END AS is_stoppasientitipan,
			        CASE COALESCE(pasienadmisi_t.pasienpulang_id, 0)
			            WHEN 0 THEN false
			            ELSE true
			        END AS is_pulang,
			        CASE pendaftaran_t.status_bayar
			            WHEN 348 THEN true
			            ELSE false
			        END AS is_lunas,
			    pendaftaran_t.is_stopakomodasi,
			    NULL::integer AS pasienmasukpenunjang_id,
			    pendaftaran_t.keterangan_pendaftaran,
			    ruangan_m.instalasi_id,
			    ruangan_m.ruangan_nama,
			    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
			    kamarruangan_m.kamarruangan_nokamar,
			    kamartempattidur_m.no_tempattidur,
			    kettempattidur_m.kettempattidur_nama,
			    fgetnamalookup((kamarruangan_m.keterangan_kamar)::integer) AS status_kamar,
			    pasienadmisi_t.tgl_pindahkamar,
			    rencanapulang_t.rencana_pulang,
			    antrian_t.no_antrian,
			        CASE
			            WHEN (soap_ri.soap_id IS NULL) THEN false
			            ELSE true
			        END AS is_isisoap,
			    peg_create.pegawai_id AS peg_create_id,
			    peg_create.nama_pegawai AS peg_create_nama,
			    NULL::integer AS konsulpoli_id,
			    asesmenawal_t.r_alergi AS alergi,
			    pasien_m.no_telepon_pasien,
			    pasien_m.no_mobile_pasien,
			    pasien_m.no_identitas_pasien
			   FROM (((((((((((((((((((((((((((((((((((((((pendaftaran_t
			     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
			     LEFT JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
			     LEFT JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
			     LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
			     JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
			     LEFT JOIN caramasuk_m ON ((pasienadmisi_t.caramasuk_id = caramasuk_m.caramasuk_id)))
			     JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
			     JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
			     JOIN jeniskasuspenyakit_m ON ((kamarruangan_m.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
			     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
			     JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
			     JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
			     JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
			     LEFT JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
			     LEFT JOIN loginpemakai_k petugas ON ((pendaftaran_t.last_modified_by = petugas.loginpemakai_id)))
			     LEFT JOIN pegawai_m petugas_pemakai ON ((petugas.pegawai_id = petugas_pemakai.pegawai_id)))
			     LEFT JOIN loginpemakai_k pembuat ON ((pendaftaran_t.created_by = pembuat.loginpemakai_id)))
			     LEFT JOIN pegawai_m petugas_pembuat ON ((pembuat.pegawai_id = petugas_pembuat.pegawai_id)))
			     LEFT JOIN suku_m ON ((pasien_m.suku_id = suku_m.suku_id)))
			     LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
			     LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
			     LEFT JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
			     JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
			     LEFT JOIN bpjs_t ON ((pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id)))
			     LEFT JOIN kelaspelayanan_m kelas_ditagihkan ON ((pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id)))
			     LEFT JOIN kamarruangan_m kamar_ditagihkan ON ((pasienadmisi_t.kamar_titipan_id = kamar_ditagihkan.kamarruangan_id)))
			     LEFT JOIN ruangan_m ruangan_ditagihkan ON ((pasienadmisi_t.ruangan_titipan_id = ruangan_ditagihkan.ruangan_id)))
			     LEFT JOIN ( SELECT pindahkamar_t.pindahkamar_id,
			            pindahkamar_t.pasienadmisi_id,
			            pindahkamar_t.kelas_ditagihkan_id,
			            kelas_ditagihkan_1.kelaspelayanan_nama AS kelas_ditagihkan,
			            pindahkamar_t.is_stoptitipan
			           FROM ((pindahkamar_t
			             JOIN ( SELECT max(pk.pindahkamar_id) AS pindahkamar_id,
			                    pk.pasienadmisi_id
			                   FROM pindahkamar_t pk
			                  GROUP BY pk.pasienadmisi_id) max_pk ON (((pindahkamar_t.pindahkamar_id = max_pk.pindahkamar_id) AND (pindahkamar_t.pasienadmisi_id = max_pk.pasienadmisi_id))))
			             LEFT JOIN kelaspelayanan_m kelas_ditagihkan_1 ON ((pindahkamar_t.kelas_ditagihkan_id = kelas_ditagihkan_1.kelaspelayanan_id)))
			          WHERE ((pindahkamar_t.is_deleted = false) AND (pindahkamar_t.is_pasientitipan = true))) pindah_kamar ON ((pasienadmisi_t.pasienadmisi_id = pindah_kamar.pasienadmisi_id)))
			     LEFT JOIN ( SELECT pindahkamar_t.pindahkamar_id,
			            pindahkamar_t.pasienadmisi_id,
			            pindahkamar_t.is_pasientitipan,
			            pindahkamar_t.is_stoptitipan
			           FROM (pindahkamar_t
			             JOIN ( SELECT max(pk.pindahkamar_id) AS pindahkamar_id,
			                    pk.pasienadmisi_id
			                   FROM pindahkamar_t pk
			                  GROUP BY pk.pasienadmisi_id) max_pk ON (((pindahkamar_t.pindahkamar_id = max_pk.pindahkamar_id) AND (pindahkamar_t.pasienadmisi_id = max_pk.pasienadmisi_id))))
			          WHERE (pindahkamar_t.is_deleted = false)) stop_titipan ON ((pasienadmisi_t.pasienadmisi_id = stop_titipan.pasienadmisi_id)))
			     LEFT JOIN pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienadmisi_id)))
			     LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
			     LEFT JOIN ( SELECT count(*) AS is_bayi,
			            kelahiranbayi_t_1.pendaftaranbaru_id
			           FROM kelahiranbayi_t kelahiranbayi_t_1
			          GROUP BY kelahiranbayi_t_1.pendaftaranbaru_id) kelahiranbayi_t ON ((pendaftaran_t.pendaftaran_id = kelahiranbayi_t.pendaftaranbaru_id)))
			     LEFT JOIN kettempattidur_m ON ((kamartempattidur_m.kettempattidur_id = kettempattidur_m.kettempattidur_id)))
			     LEFT JOIN rencanapulang_t ON (((pasienadmisi_t.pasienadmisi_id = rencanapulang_t.pasienadmisi_id) AND (rencanapulang_t.is_deleted = false))))
			     LEFT JOIN antrian_t ON (((pendaftaran_t.antrian_id = antrian_t.antrian_id) AND (antrian_t.jenisantrian_id = 312))))
			     LEFT JOIN ( SELECT cppt_t.pasienadmisi_id AS soap_id
			           FROM cppt_t
			          WHERE (cppt_t.is_deleted = false)
			          GROUP BY cppt_t.pasienadmisi_id) soap_ri ON ((pendaftaran_t.pasienadmisi_id = soap_ri.soap_id)))
			     LEFT JOIN loginpemakai_k ON ((pasienadmisi_t.created_by = loginpemakai_k.loginpemakai_id)))
			     LEFT JOIN pegawai_m peg_create ON ((loginpemakai_k.pegawai_id = peg_create.pegawai_id)))
			     LEFT JOIN asesmenawal_t ON ((asesmenawal_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
			  WHERE ((pendaftaran_t.is_active = true) AND (pendaftaran_t.is_deleted = false))
			UNION ALL
			 SELECT \'MCU\'::text AS jenis,
			    pendaftaran_t.pendaftaran_id,
			    pendaftaran_t.tgl_pendaftaran,
			    pendaftaran_t.no_pendaftaran,
			    pasien_m.no_rekam_medik,
			    pasien_m.nama_pasien,
			    pasien_m.tanggal_lahir,
			        CASE pasien_m.jeniskelamin
			            WHEN \'15\'::text THEN \'L\'::text
			            ELSE \'P\'::text
			        END AS jk,
			    pegawai_m.nama_pegawai,
			    carabayar_m.carabayar_nama,
			    penjamin_m.penjamin_nama,
			    (\'Kelas \'::text || bpjs_t.klsrawat) AS hak_kelas,
			    kelaspelayanan_m.kelaspelayanan_nama,
			    \'-\'::text AS kelas_tagihan,
			    true AS is_konsul,
			    false AS is_pasientitipan,
			    carabayar_m.carabayar_kode_warna,
			    carabayar_m.carabayar_warna,
			    pendaftaran_t.carabayar_id,
			    pendaftaran_t.penjamin_id,
			    pendaftaran_t.jeniskasuspenyakit_id,
			    ruangan_m.ruangan_id,
			    pendaftaran_t.kelaspelayanan_id,
			    pendaftaran_t.pegawai_id,
			    (pendaftaran_t.status_periksa)::integer AS status_periksa,
			    fgetnamalookup((pendaftaran_t.status_periksa)::integer) AS status_periksa_nama,
			        CASE
			            WHEN (kelahiranbayi_t.is_bayi > 0) THEN true
			            ELSE false
			        END AS is_bayi,
			    false AS is_stoppasientitipan,
			        CASE COALESCE(pendaftaran_t.pasienpulang_id, 0)
			            WHEN 0 THEN false
			            ELSE true
			        END AS is_pulang,
			        CASE pendaftaran_t.status_bayar
			            WHEN 348 THEN true
			            ELSE false
			        END AS is_lunas,
			    pendaftaran_t.is_stopakomodasi,
			    NULL::integer AS pasienmasukpenunjang_id,
			    pendaftaran_t.keterangan_pendaftaran,
			    ruangan_m.instalasi_id,
			    ruangan_m.ruangan_nama,
			    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
			    NULL::text AS kamarruangan_nokamar,
			    NULL::text AS no_tempattidur,
			    NULL::text AS kettempattidur_nama,
			    NULL::text AS status_kamar,
			    NULL::timestamp without time zone AS tgl_pindahkamar,
			    NULL::timestamp without time zone AS rencana_pulang,
			    antrian_t.no_antrian,
			    false AS is_isisoap,
			    peg_create.pegawai_id AS peg_create_id,
			    peg_create.nama_pegawai AS peg_create_nama,
			    konsulpoli_t.konsulpoli_id,
			    anamnesa_t.is_alergi AS alergi,
			    pasien_m.no_telepon_pasien,
			    pasien_m.no_mobile_pasien,
			    pasien_m.no_identitas_pasien
			   FROM (((((((((((((((((pendaftaran_t
			     JOIN konsulpoli_t ON ((pendaftaran_t.pendaftaran_id = konsulpoli_t.pendaftaran_id)))
			     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
			     LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
			     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
			     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
			     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
			     LEFT JOIN caramasuk_m ON ((pendaftaran_t.caramasuk_id = caramasuk_m.caramasuk_id)))
			     JOIN ruangan_m ON ((COALESCE(konsulpoli_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id)))
			     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
			     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
			     LEFT JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
			     LEFT JOIN bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
			     LEFT JOIN ( SELECT count(*) AS is_bayi,
			            kelahiranbayi_t_1.pendaftaranbaru_id
			           FROM kelahiranbayi_t kelahiranbayi_t_1
			          GROUP BY kelahiranbayi_t_1.pendaftaranbaru_id) kelahiranbayi_t ON ((pendaftaran_t.pendaftaran_id = kelahiranbayi_t.pendaftaranbaru_id)))
			     LEFT JOIN antrian_t ON (((pendaftaran_t.antrian_id = antrian_t.antrian_id) AND (antrian_t.jenisantrian_id = 312))))
			     LEFT JOIN loginpemakai_k ON ((pendaftaran_t.created_by = loginpemakai_k.loginpemakai_id)))
			     LEFT JOIN pegawai_m peg_create ON ((loginpemakai_k.pegawai_id = peg_create.pegawai_id)))
			     LEFT JOIN anamnesa_t ON ((pendaftaran_t.pendaftaran_id = anamnesa_t.pendaftaran_id)))
			  WHERE ((pendaftaran_t.instalasi_id = 21) AND (konsulpoli_t.status_approve = 565) AND (konsulpoli_t.pendaftaranbaru_id IS NOT NULL))
			UNION ALL
			 SELECT \'OT\'::text AS jenis,
			    pendaftaran_t.pendaftaran_id,
			    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_pendaftaran,
			    pendaftaran_t.no_pendaftaran,
			    pasien_m.no_rekam_medik,
			    pasien_m.nama_pasien,
			    pasien_m.tanggal_lahir,
			        CASE pasien_m.jeniskelamin
			            WHEN \'15\'::text THEN \'L\'::text
			            ELSE \'P\'::text
			        END AS jk,
			    pegawai_m.nama_pegawai,
			    carabayar_m.carabayar_nama,
			    penjamin_m.penjamin_nama,
			    (\'Kelas \'::text || bpjs_t.klsrawat) AS hak_kelas,
			    kelaspelayanan_m.kelaspelayanan_nama,
			    \'-\'::text AS kelas_tagihan,
			    false AS is_konsul,
			    false AS is_pasientitipan,
			    carabayar_m.carabayar_kode_warna,
			    carabayar_m.carabayar_warna,
			    pendaftaran_t.carabayar_id,
			    pendaftaran_t.penjamin_id,
			    pendaftaran_t.jeniskasuspenyakit_id,
			    ruangan_m.ruangan_id,
			    pendaftaran_t.kelaspelayanan_id,
			    pendaftaran_t.pegawai_id,
			    (pasienmasukpenunjang_t.status_periksa)::integer AS status_periksa,
			    fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status_periksa_nama,
			        CASE
			            WHEN (kelahiranbayi_t.is_bayi > 0) THEN true
			            ELSE false
			        END AS is_bayi,
			    false AS is_stoppasientitipan,
			        CASE COALESCE(pendaftaran_t.pasienpulang_id, 0)
			            WHEN 0 THEN false
			            ELSE true
			        END AS is_pulang,
			        CASE pendaftaran_t.status_bayar
			            WHEN 348 THEN true
			            ELSE false
			        END AS is_lunas,
			    pendaftaran_t.is_stopakomodasi,
			    pasienkirimkeunitlain_t.pasienmasukpenunjang_id,
			    pendaftaran_t.keterangan_pendaftaran,
			    ruangan_m.instalasi_id,
			    ruangan_m.ruangan_nama,
			    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
			    NULL::text AS kamarruangan_nokamar,
			    NULL::text AS no_tempattidur,
			    NULL::text AS kettempattidur_nama,
			    NULL::text AS status_kamar,
			    NULL::timestamp without time zone AS tgl_pindahkamar,
			    NULL::timestamp without time zone AS rencana_pulang,
			    antrian_t.no_antrian,
			    true AS is_isisoap,
			    peg_create.pegawai_id AS peg_create_id,
			    peg_create.nama_pegawai AS peg_create_nama,
			    NULL::integer AS konsulpoli_id,
			    NULL::boolean AS alergi,
			    pasien_m.no_telepon_pasien,
			    pasien_m.no_mobile_pasien,
			    pasien_m.no_identitas_pasien
			   FROM ((((((((((((((((pasienkirimkeunitlain_t
			     JOIN pendaftaran_t ON ((pasienkirimkeunitlain_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
			     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
			     JOIN instalasi_m ON ((pendaftaran_t.instalasi_id = instalasi_m.instalasi_id)))
			     JOIN ruangan_m ON ((pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id)))
			     JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
			     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
			     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
			     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
			     LEFT JOIN pasienmasukpenunjang_t ON ((pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id)))
			     LEFT JOIN bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
			     LEFT JOIN ( SELECT count(*) AS is_bayi,
			            kelahiranbayi_t_1.pendaftaranbaru_id
			           FROM kelahiranbayi_t kelahiranbayi_t_1
			          GROUP BY kelahiranbayi_t_1.pendaftaranbaru_id) kelahiranbayi_t ON ((pendaftaran_t.pendaftaran_id = kelahiranbayi_t.pendaftaranbaru_id)))
			     LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
			     LEFT JOIN kamarruangan_m ON ((pasienmasukpenunjang_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
			     LEFT JOIN antrian_t ON (((pendaftaran_t.antrian_id = antrian_t.antrian_id) AND (antrian_t.jenisantrian_id = 312))))
			     LEFT JOIN loginpemakai_k ON ((pendaftaran_t.created_by = loginpemakai_k.loginpemakai_id)))
			     LEFT JOIN pegawai_m peg_create ON ((loginpemakai_k.pegawai_id = peg_create.pegawai_id)))
			  WHERE (pasienkirimkeunitlain_t.instalasi_id = 12)
			UNION ALL
			 SELECT \'OT\'::text AS jenis,
			    pendaftaran_t.pendaftaran_id,
			    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_pendaftaran,
			    pendaftaran_t.no_pendaftaran,
			    pasien_m.no_rekam_medik,
			    pasien_m.nama_pasien,
			    pasien_m.tanggal_lahir,
			        CASE pasien_m.jeniskelamin
			            WHEN \'15\'::text THEN \'L\'::text
			            ELSE \'P\'::text
			        END AS jk,
			    pegawai_m.nama_pegawai,
			    carabayar_m.carabayar_nama,
			    penjamin_m.penjamin_nama,
			    (\'Kelas \'::text || bpjs_t.klsrawat) AS hak_kelas,
			    kelaspelayanan_m.kelaspelayanan_nama,
			    \'-\'::text AS kelas_tagihan,
			    false AS is_konsul,
			    false AS is_pasientitipan,
			    carabayar_m.carabayar_kode_warna,
			    carabayar_m.carabayar_warna,
			    pasienadmisi_t.carabayar_id,
			    pasienadmisi_t.penjamin_id,
			    pendaftaran_t.jeniskasuspenyakit_id,
			    ruangan_m.ruangan_id,
			    pasienadmisi_t.kelaspelayanan_id,
			    COALESCE(pasienadmisi_t.pegawai_id, pendaftaran_t.pegawai_id) AS pegawai_id,
			    (pasienmasukpenunjang_t.status_periksa)::integer AS status_periksa,
			    fgetnamalookup((pasienmasukpenunjang_t.status_periksa)::integer) AS status_periksa_nama,
			        CASE
			            WHEN (kelahiranbayi_t.is_bayi > 0) THEN true
			            ELSE false
			        END AS is_bayi,
			    false AS is_stoppasientitipan,
			        CASE COALESCE(pasienadmisi_t.pasienpulang_id, 0)
			            WHEN 0 THEN false
			            ELSE true
			        END AS is_pulang,
			        CASE pendaftaran_t.status_bayar
			            WHEN 348 THEN true
			            ELSE false
			        END AS is_lunas,
			    pendaftaran_t.is_stopakomodasi,
			    pasienkirimkeunitlain_t.pasienmasukpenunjang_id,
			    pendaftaran_t.keterangan_pendaftaran,
			    ruangan_m.instalasi_id,
			    ruangan_m.ruangan_nama,
			    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
			    kamarruangan_m.kamarruangan_nokamar,
			    kamartempattidur_m.no_tempattidur,
			    NULL::text AS kettempattidur_nama,
			    NULL::text AS status_kamar,
			    NULL::timestamp without time zone AS tgl_pindahkamar,
			    NULL::timestamp without time zone AS rencana_pulang,
			    antrian_t.no_antrian,
			    true AS is_isisoap,
			    peg_create.pegawai_id AS peg_create_id,
			    peg_create.nama_pegawai AS peg_create_nama,
			    NULL::integer AS konsulpoli_id,
			    NULL::boolean AS alergi,
			    pasien_m.no_telepon_pasien,
			    pasien_m.no_mobile_pasien,
			    pasien_m.no_identitas_pasien
			   FROM ((((((((((((((((((pasienkirimkeunitlain_t
			     JOIN pasienadmisi_t ON ((pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
			     JOIN pendaftaran_t ON ((pasienadmisi_t.pasienadmisi_id = pendaftaran_t.pasienadmisi_id)))
			     JOIN pasien_m ON ((pasienadmisi_t.pasien_id = pasien_m.pasien_id)))
			     JOIN ruangan_m ON ((pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id)))
			     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
			     JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
			     JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
			     JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
			     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
			     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
			     JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
			     LEFT JOIN pasienmasukpenunjang_t ON ((pasienkirimkeunitlain_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id)))
			     LEFT JOIN bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
			     LEFT JOIN ( SELECT count(*) AS is_bayi,
			            kelahiranbayi_t_1.pendaftaranbaru_id
			           FROM kelahiranbayi_t kelahiranbayi_t_1
			          GROUP BY kelahiranbayi_t_1.pendaftaranbaru_id) kelahiranbayi_t ON ((pendaftaran_t.pendaftaran_id = kelahiranbayi_t.pendaftaranbaru_id)))
			     LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
			     LEFT JOIN antrian_t ON (((pendaftaran_t.antrian_id = antrian_t.antrian_id) AND (antrian_t.jenisantrian_id = 312))))
			     LEFT JOIN loginpemakai_k ON ((pendaftaran_t.created_by = loginpemakai_k.loginpemakai_id)))
			     LEFT JOIN pegawai_m peg_create ON ((loginpemakai_k.pegawai_id = peg_create.pegawai_id)))
			  WHERE (pasienkirimkeunitlain_t.instalasi_id = 12)
			UNION ALL
			 SELECT \'OL\'::text AS jenis,
			    pendaftaranol_t.pendaftaran_id,
			    pendaftaranol_t.tgl_pendaftaranol AS tgl_pendaftaran,
			    pendaftaranol_t.no_pendaftaranol AS no_pendaftaran,
			    COALESCE(pasien_m.no_rekam_medik, pendaftaranol_t.no_identitas_pasien) AS no_rekam_medik,
			    COALESCE(pasien_m.nama_pasien, pendaftaranol_t.nama_pasien) AS nama_pasien,
			    COALESCE(pasien_m.tanggal_lahir, pendaftaranol_t.tanggal_lahir) AS tanggal_lahir,
			        CASE COALESCE(pasien_m.jeniskelamin, pendaftaranol_t.jeniskelamin)
			            WHEN \'15\'::text THEN \'L\'::text
			            ELSE \'P\'::text
			        END AS jk,
			    pegawai_m.nama_pegawai,
			    carabayar_m.carabayar_nama,
			    penjamin_m.penjamin_nama,
			    NULL::text AS hak_kelas,
			    NULL::character varying AS kelaspelayanan_nama,
			    \'-\'::text AS kelas_tagihan,
			    false AS is_konsul,
			    false AS is_pasientitipan,
			    carabayar_m.carabayar_kode_warna,
			    carabayar_m.carabayar_warna,
			    pendaftaranol_t.carabayar_id,
			    pendaftaranol_t.penjamin_id,
			    NULL::integer AS jeniskasuspenyakit_id,
			    pendaftaranol_t.ruangan_id,
			    NULL::integer AS kelaspelayanan_id,
			    pendaftaranol_t.pegawai_id,
			    pendaftaranol_t.status_daftar_ol AS status_periksa,
			    fgetnamalookup(pendaftaranol_t.status_daftar_ol) AS status_periksa_nama,
			    false AS is_bayi,
			    false AS is_stoppasientitipan,
			        CASE COALESCE(pendaftaran_t.pasienpulang_id, 0)
			            WHEN 0 THEN false
			            ELSE true
			        END AS is_pulang,
			        CASE pendaftaran_t.status_bayar
			            WHEN 348 THEN true
			            ELSE false
			        END AS is_lunas,
			    pendaftaran_t.is_stopakomodasi,
			    NULL::integer AS pasienmasukpenunjang_id,
			    pendaftaran_t.keterangan_pendaftaran,
			    pendaftaran_t.instalasi_id,
			    ruangan_m.ruangan_nama,
			    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
			    NULL::text AS kamarruangan_nokamar,
			    NULL::text AS no_tempattidur,
			    NULL::text AS kettempattidur_nama,
			    NULL::text AS status_kamar,
			    NULL::timestamp without time zone AS tgl_pindahkamar,
			    NULL::timestamp without time zone AS rencana_pulang,
			    antrian_t.no_antrian,
			    false AS is_isisoap,
			    peg_create.pegawai_id AS peg_create_id,
			    peg_create.nama_pegawai AS peg_create_nama,
			    NULL::integer AS konsulpoli_id,
			    NULL::boolean AS alergi,
			    pasien_m.no_telepon_pasien,
			    pasien_m.no_mobile_pasien,
			    pasien_m.no_identitas_pasien
			   FROM (((((((((((((pendaftaranol_t
			     LEFT JOIN pasien_m ON ((pendaftaranol_t.pasien_id = pasien_m.pasien_id)))
			     JOIN ruangan_m ON ((pendaftaranol_t.ruangan_id = ruangan_m.ruangan_id)))
			     LEFT JOIN pegawai_m ON ((pendaftaranol_t.pegawai_id = pegawai_m.pegawai_id)))
			     LEFT JOIN carabayar_m ON ((pendaftaranol_t.carabayar_id = carabayar_m.carabayar_id)))
			     LEFT JOIN penjamin_m ON ((pendaftaranol_t.penjamin_id = penjamin_m.penjamin_id)))
			     LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
			     LEFT JOIN pekerjaan_m ON ((pasien_m.pekerjaan_id = pekerjaan_m.pekerjaan_id)))
			     LEFT JOIN jenispasien_m ON ((pendaftaranol_t.klasifikasipasien_id = jenispasien_m.jenispasien_id)))
			     LEFT JOIN antrian_t ON ((pendaftaranol_t.antrian_id = antrian_t.antrian_id)))
			     LEFT JOIN pendaftaran_t ON ((pendaftaranol_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
			     LEFT JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
			     LEFT JOIN loginpemakai_k ON ((pendaftaran_t.created_by = loginpemakai_k.loginpemakai_id)))
			     LEFT JOIN pegawai_m peg_create ON ((loginpemakai_k.pegawai_id = peg_create.pegawai_id)))
			  WHERE (pendaftaranol_t.status_daftar_ol = ANY (ARRAY[564, 566]));
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."infokunjunganri_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infokunjunganri_v" AS  SELECT pasien_m.pasien_id,
			    pasien_m.jenisidentitas,
			    pasien_m.no_identitas_pasien,
			    pasien_m.namadepan, 
			    fgetnamalookup((pasien_m.namadepan)::integer) AS nama_depan,
			    pasien_m.nama_pasien,
			    pasien_m.nama_bin,
			    pasien_m.jeniskelamin,
			    pasien_m.tempat_lahir,
			    pasien_m.tanggal_lahir,
			    pasien_m.alamat_pasien,
			    pasien_m.rt,
			    pasien_m.rw,
			    pasien_m.agama,
			    pasien_m.golongandarah,
			    pasien_m.photopasien,
			    pasien_m.alamatemail,
			    pasien_m.statusrekammedis,
			    pasien_m.statusperkawinan,
			    pasien_m.no_rekam_medik,
			    pasien_m.tgl_rekam_medik,
			    pendaftaran_t.pendaftaran_id,
			    pendaftaran_t.no_pendaftaran,
			    pasienadmisi_t.tgl_admisi AS tgl_pendaftaran,
			    pendaftaran_t.no_urutantri,
			    pendaftaran_t.transportasi,
			    pendaftaran_t.keadaan_masuk,
			    pendaftaran_t.status_pasien,
			    pendaftaran_t.alih_status,
			    pendaftaran_t.by_phone,
			    pendaftaran_t.kunjungan_rumah,
			    pendaftaran_t.status_masuk,
			    pendaftaran_t.umur,
			    pendaftaran_t.golonganumur_id,
			    asuransipasien_m.nokartuasuransi AS no_asuransi,
			    asuransipasien_m.namapemilikasuransi AS namapemilik_asuransi,
			    asuransipasien_m.nomorpokokperusahaan AS nopokokperusahaan,
			    carabayar_m.carabayar_id,
			    carabayar_m.carabayar_nama,
			    penjamin_m.penjamin_id,
			    penjamin_m.penjamin_nama,
			    caramasuk_m.caramasuk_id,
			    caramasuk_m.caramasuk_nama,
			    pendaftaran_t.shift_id,
			    rujukan_t.no_rujukan,
			    rujukan_t.nama_perujuk,
			    rujukan_t.tanggal_rujukan,
			    rujukan_t.kodediagnosa_rujukan,
			    asalrujukan_m.asalrujukan_id,
			    asalrujukan_m.asalrujukan_nama,
			    penanggungjawab_m.penanggungjawab_id,
			    penanggungjawab_m.pengantar,
			    penanggungjawab_m.hubungankeluarga,
			    penanggungjawab_m.penanggungjawab_nama,
			    ruangan_m.ruangan_id,
			    ruangan_m.ruangan_nama,
			    instalasi_m.instalasi_id,
			    instalasi_m.instalasi_nama,
			    jeniskasuspenyakit_m.jeniskasuspenyakit_id,
			    jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
			    kelaspelayanan_m.kelaspelayanan_id,
			    kelaspelayanan_m.kelaspelayanan_nama,
			    pasienadmisi_t.pasienadmisi_id,
			    pasienadmisi_t.tgl_admisi,
			    pasienadmisi_t.tgl_pulang,
			    pasienadmisi_t.kunjungan,
			    pasienadmisi_t.status_keluar,
			    pasienadmisi_t.rawat_gabung,
			    kamarruangan_m.kamarruangan_id,
			    pegawai_m.gelardepan,
			    pegawai_m.nama_pegawai,
			    pegawai_m.gelarbelakang,
			    asuransipasien_m.status_konfirmasi,
			    asuransipasien_m.tgl_konfirmasi,
			    pasienadmisi_t.pegawai_id,
			    pasien_m.rhesus,
			    pasien_m.anakke,
			    pasien_m.jumlah_bersaudara,
			    pasien_m.no_telepon_pasien,
			    pasien_m.no_mobile_pasien,
			    pasien_m.warga_negara,
			    suku_m.suku_id,
			    suku_m.suku_nama,
			    pendidikan_m.pendidikan_id,
			    pendidikan_m.pendidikan_nama,
			    pasien_m.nama_ibu,
			    pasien_m.nama_ayah,
			    asuransipasien_m.nopeserta,
			    asuransipasien_m.tglcetakkartuasuransi,
			    asuransipasien_m.kodefeskestk1,
			    asuransipasien_m.nama_feskestk1,
			    asuransipasien_m.masaberlakukartu,
			    asuransipasien_m.nokartukeluarga,
			    asuransipasien_m.nopassport,
			    asuransipasien_m.is_active,
			    pendaftaran_t.keterangan_pendaftaran,
			    pegawai_m.kelompokpegawai_id,
			    pasien_m.is_deleted,
			    fgetnamalookup(pasienadmisi_t.status_ranap) AS status_periksa,
			    kamarruangan_m.kamarruangan_nokamar,
			    kamartempattidur_m.no_tempattidur,
			    golonganumur_m.golonganumur_nama,
			    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
			    pasienadmisi_t.created_by,
			    kamartempattidur_m.kamartempattidur_id,
			    pasienadmisi_t.bpjs_id,
			    pasienadmisi_t.status_ranap AS status_periksa_id,
			    bpjs_t.nosep,
			    kelaspelayanan_m.urutankelas,
			    kelaspelayanan_m.bpjs_kelas,
			    bpjs_t.klsrawat,
			    pasienadmisi_t.is_aps,
			    pasienadmisi_t.is_pasientitipan,
			        CASE
			            WHEN (pindah_kamar.pindahkamar_id IS NULL) THEN pasienadmisi_t.kelas_ditagihkan_id
			            ELSE pindah_kamar.kelas_ditagihkan_id
			        END AS kelas_ditagihkan_id,
			        CASE
			            WHEN (pindah_kamar.pindahkamar_id IS NULL) THEN kelas_ditagihkan.kelaspelayanan_nama
			            ELSE pindah_kamar.kelas_ditagihkan
			        END AS kelas_ditagihkan_nama,
			    pasienadmisi_t.kamar_titipan_id,
			    kamar_ditagihkan.kamarruangan_nokamar AS kamar_titipan_nama,
			    pasienadmisi_t.ruangan_titipan_id,
			    ruangan_ditagihkan.ruangan_nama AS ruangan_titipan_nama,
			    pasienadmisi_t.is_stoptitipan,
			    pindah_kamar.pindahkamar_id,
			        CASE
			            WHEN ((stop_titipan.pindahkamar_id IS NULL) AND (pasienadmisi_t.is_stoptitipan IS FALSE)) THEN false
			            WHEN ((stop_titipan.pindahkamar_id IS NULL) AND (pasienadmisi_t.is_stoptitipan IS TRUE)) THEN true
			            WHEN ((stop_titipan.is_pasientitipan IS FALSE) AND (stop_titipan.is_stoptitipan IS FALSE)) THEN true
			            WHEN ((stop_titipan.is_pasientitipan IS TRUE) AND (stop_titipan.is_stoptitipan IS TRUE)) THEN true
			            ELSE false
			        END AS is_stoppasientitipan,
			    stop_titipan.is_pasientitipan AS is_pasientitipan_pk,
			    carakeluar_m.carakeluar_nama,
			    fgetnamalookup((pasien_m.agama)::integer) AS agama_nama,
			    pendaftaran_t.last_modified_date AS tgl_update_terakhir,
			    petugas_pemakai.nama_pegawai AS petugas_nama,
			    pendaftaran_t.created_date AS tgl_pembuatan,
			    petugas_pembuat.nama_pegawai AS pembuat_nama,
			    pasien_m.additional_pasien,
			    pendaftaran_t.is_stopakomodasi,
			    carabayar_m.carabayar_kode_warna,
			        CASE
			            WHEN (antrian_poli.jenisantrian_id = 312) THEN (antrian_poli.no_antrian)::text
			            ELSE \'-\'::text
			        END AS no_antrian_poli,
			    pasien_m.catatanpenting_pasien,
			    penanggungjawab_m.penanggungjawab_alamat,
			    penanggungjawab_m.penanggungjawab_notelp,
			    penanggungjawab_m.pj_pekerjaan_id,
			    pj_kerja.pekerjaan_nama AS pj_pekerjaan_nama,
			    penanggungjawab_m.pj_propinsi_id,
			    pj_prop.propinsi_nama AS pj_propinsi_nama,
			    penanggungjawab_m.pj_kabupaten_id,
			    pj_kab.kabupaten_nama AS pj_kabupaten_nama,
			    penanggungjawab_m.pj_kecamatan_id,
			    pj_kec.kecamatan_nama AS pj_kecamatan_nama,
			    penanggungjawab_m.pj_kelurahan_id,
			    pj_kel.kelurahan_nama AS pj_kelurahan_nama,
			    pasien_m.bahasa_sehari,
			    fgetnamalookup((pasien_m.bahasa_sehari)::integer) AS bahasa_sehari_nama,
			    penanggungjawab_m.pj_namadepan,
			    fgetnamalookup((penanggungjawab_m.pj_namadepan)::integer) AS pj_namadepan_nama,
			    pasienadmisi_t.limit_tagihan,
			    perujuk_m.namaperujuk AS rujukan_dari,
			    resumemedisri_t.resumemedisri_id,
			    pendaftaran_t.tgl_stopakomodasi
			   FROM ((((((((((((((((((((((((((((((((((((((((pendaftaran_t
			     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
			     LEFT JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
			     LEFT JOIN perujuk_m ON ((rujukan_t.rujukandari_id = perujuk_m.perujuk_id)))
			     LEFT JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
			     LEFT JOIN penanggungjawab_m ON ((pendaftaran_t.penanggungjawab_id = penanggungjawab_m.penanggungjawab_id)))
			     JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
			     JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
			     LEFT JOIN caramasuk_m ON ((pasienadmisi_t.caramasuk_id = caramasuk_m.caramasuk_id)))
			     JOIN kamarruangan_m ON ((pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id)))
			     JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
			     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
			     JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
			     JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
			     JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
			     LEFT JOIN antrian_t ON ((antrian_t.antrian_id = pendaftaran_t.antrian_id)))
			     LEFT JOIN antrian_t antrian_poli ON (((pendaftaran_t.pendaftaran_id = antrian_poli.pendaftaran_id) AND (antrian_poli.jenisantrian_id = 312))))
			     LEFT JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
			     LEFT JOIN loginpemakai_k petugas ON ((pendaftaran_t.last_modified_by = petugas.loginpemakai_id)))
			     LEFT JOIN pegawai_m petugas_pemakai ON ((petugas.pegawai_id = petugas_pemakai.pegawai_id)))
			     LEFT JOIN loginpemakai_k pembuat ON ((pendaftaran_t.created_by = pembuat.loginpemakai_id)))
			     LEFT JOIN pegawai_m petugas_pembuat ON ((pembuat.pegawai_id = petugas_pembuat.pegawai_id)))
			     LEFT JOIN pekerjaan_m pj_kerja ON ((penanggungjawab_m.pj_pekerjaan_id = pj_kerja.pekerjaan_id)))
			     LEFT JOIN propinsi_m pj_prop ON ((penanggungjawab_m.pj_propinsi_id = pj_prop.propinsi_id)))
			     LEFT JOIN kabupaten_m pj_kab ON ((penanggungjawab_m.pj_kabupaten_id = pj_kab.kabupaten_id)))
			     LEFT JOIN kecamatan_m pj_kec ON ((penanggungjawab_m.pj_kecamatan_id = pj_kec.kecamatan_id)))
			     LEFT JOIN kelurahan_m pj_kel ON ((penanggungjawab_m.pj_kelurahan_id = pj_kel.kelurahan_id)))
			     LEFT JOIN suku_m ON ((pasien_m.suku_id = suku_m.suku_id)))
			     LEFT JOIN pendidikan_m ON ((pasien_m.pendidikan_id = pendidikan_m.pendidikan_id)))
			     LEFT JOIN asuransipasien_m ON ((pendaftaran_t.asuransipasien_id = asuransipasien_m.asuransipasien_id)))
			     LEFT JOIN golonganumur_m ON ((pendaftaran_t.golonganumur_id = golonganumur_m.golonganumur_id)))
			     JOIN kamartempattidur_m ON ((pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id)))
			     LEFT JOIN bpjs_t ON ((pasienadmisi_t.bpjs_id = bpjs_t.bpjs_id)))
			     LEFT JOIN kelaspelayanan_m kelas_ditagihkan ON ((pasienadmisi_t.kelas_ditagihkan_id = kelas_ditagihkan.kelaspelayanan_id)))
			     LEFT JOIN kamarruangan_m kamar_ditagihkan ON ((pasienadmisi_t.kamar_titipan_id = kamar_ditagihkan.kamarruangan_id)))
			     LEFT JOIN ruangan_m ruangan_ditagihkan ON ((pasienadmisi_t.ruangan_titipan_id = ruangan_ditagihkan.ruangan_id)))
			     LEFT JOIN ( SELECT pindahkamar_t.pindahkamar_id,
			            pindahkamar_t.pasienadmisi_id,
			            pindahkamar_t.kelas_ditagihkan_id,
			            kelas_ditagihkan_1.kelaspelayanan_nama AS kelas_ditagihkan,
			            pindahkamar_t.is_stoptitipan
			           FROM ((pindahkamar_t
			             JOIN ( SELECT max(pk.pindahkamar_id) AS pindahkamar_id,
			                    pk.pasienadmisi_id
			                   FROM pindahkamar_t pk
			                  GROUP BY pk.pasienadmisi_id) max_pk ON (((pindahkamar_t.pindahkamar_id = max_pk.pindahkamar_id) AND (pindahkamar_t.pasienadmisi_id = max_pk.pasienadmisi_id))))
			             LEFT JOIN kelaspelayanan_m kelas_ditagihkan_1 ON ((pindahkamar_t.kelas_ditagihkan_id = kelas_ditagihkan_1.kelaspelayanan_id)))
			          WHERE ((pindahkamar_t.is_deleted = false) AND (pindahkamar_t.is_pasientitipan = true))) pindah_kamar ON ((pasienadmisi_t.pasienadmisi_id = pindah_kamar.pasienadmisi_id)))
			     LEFT JOIN ( SELECT pindahkamar_t.pindahkamar_id,
			            pindahkamar_t.pasienadmisi_id,
			            pindahkamar_t.is_pasientitipan,
			            pindahkamar_t.is_stoptitipan
			           FROM (pindahkamar_t
			             JOIN ( SELECT max(pk.pindahkamar_id) AS pindahkamar_id,
			                    pk.pasienadmisi_id
			                   FROM pindahkamar_t pk
			                  GROUP BY pk.pasienadmisi_id) max_pk ON (((pindahkamar_t.pindahkamar_id = max_pk.pindahkamar_id) AND (pindahkamar_t.pasienadmisi_id = max_pk.pasienadmisi_id))))
			          WHERE (pindahkamar_t.is_deleted = false)) stop_titipan ON ((pasienadmisi_t.pasienadmisi_id = stop_titipan.pasienadmisi_id)))
			     LEFT JOIN pasienpulang_t ON ((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienadmisi_id)))
			     LEFT JOIN carakeluar_m ON ((pasienpulang_t.carakeluar_id = carakeluar_m.carakeluar_id)))
			     LEFT JOIN resumemedisri_t ON (((pendaftaran_t.pendaftaran_id = resumemedisri_t.pendaftaran_id) AND (pasienadmisi_t.pasienadmisi_id = resumemedisri_t.pasienadmisi_id))))
			  WHERE ((pendaftaran_t.is_active = true) AND (pendaftaran_t.is_deleted = false));
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."laporankunjunganmcu_v";
        ');

        $this->execute('
            CREATE VIEW "public"."laporankunjunganmcu_v" AS  SELECT pendaftaran_t.pendaftaran_id,
			    pendaftaran_t.no_pendaftaran,
			    pendaftaran_t.tgl_pendaftaran, 
			    pasien_m.pasien_id,
			    pasien_m.no_rekam_medik,
			    pasien_m.nama_pasien,
			    tipepaket_m.tipepaket_kode,
			    tipepaket_m.tipepaket_nama,
			    tindakanpelayanan_t.tarif_tindakan,
			    penjamin_m.penjamin_id,
			    penjamin_m.penjamin_nama
			   FROM ((((pendaftaran_t
			     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
			     JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
			     JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
			     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
			  WHERE (pendaftaran_t.instalasi_id = 21)
			  ORDER BY pendaftaran_t.tgl_pendaftaran;
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."infopasienlab_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infopasienlab_v" AS  SELECT \'ORDER\'::text AS tipe_pasien,
			    pasienmasukpenunjang_t.pendaftaran_id,
			    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
			    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
			    pasienmasukpenunjang_t.tglmasukpenunjang,
			    pendaftaran_t.no_pendaftaran,
			    pasienmasukpenunjang_t.no_masukpenunjang,
			    pasien_m.no_rekam_medik,
			    pasien_m.nama_pasien,
			    pasienmasukpenunjang_t.pegawai_id,
			    pegawai_m.nama_pegawai AS dokter_penunjang,
			    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
			    pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
			    instalasi_m.instalasi_nama AS asalrujukan_nama,
			    pasienmasukpenunjang_t.ruanganasal_id,
			    ruangan_m.ruangan_nama,
			    pasienmasukpenunjang_t.status_periksa,
			    pasienmasukpenunjang_t.no_antrian,
			    pendaftaran_t.carabayar_id,
			    carabayar_m.carabayar_nama,
			    pendaftaran_t.penjamin_id,
			    penjamin_m.penjamin_nama,
			    pendaftaran_t.kelaspelayanan_id,
			    kelaspelayanan_m.kelaspelayanan_nama,
			    pendaftaran_t.umur,
			    pasien_m.jeniskelamin,
			    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
			    pasien_m.tanggal_lahir,
			    ((pendaftaran_t.label_gelang)::json ->> \'resiko_jatuh\'::text) AS kuning,
			    ((pendaftaran_t.label_gelang)::json ->> \'alergi\'::text) AS merah,
			    ((pendaftaran_t.label_gelang)::json ->> \'dnr\'::text) AS ungu,
			    ((pendaftaran_t.label_gelang)::json ->> \'duplikat\'::text) AS coklat,
			    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
			    pasienmasukpenunjang_t.pasien_id,
			    pasienadmisi_t.pasienadmisi_id,
			    pasienmasukpenunjang_t.ruangan_id,
			    pasienmasukpenunjang_t.is_bayar,
			    pasienkirimkeunitlain_t.status_penunjang,
			    pasienmasukpenunjang_t.tanggal_verifikasi,
			    pendaftaran_t.instalasi_id,
			    NULL::text AS received_flag,
			    pasienmasukpenunjang_t.is_hasil,
			    pasien_m.alamat_pasien,
			    dokter_perujuk.pegawai_id AS dokter_perujuk_id,
			    dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
			    concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), \'\'::character varying), \' \', dokter_perujuk.nama_pegawai, \' \', COALESCE(gelarbelakang_m.gelarbelakang_nama, \'\'::character varying)) AS dokter_perujuk_nama_w_gelar,
			        CASE
			            WHEN (hasil_manual.hasil > 0) THEN true
			            ELSE false
			        END AS is_hasil_manual,
			    pasienmasukpenunjang_t.additional_data,
			        CASE
			            WHEN (COALESCE(hasil.jml_hasil, (0)::bigint) > 0) THEN true
			            ELSE false
			        END AS is_hasil_bridging,
			        CASE COALESCE(tindakanpelayanan.jumlah_tagihan, (0)::double precision)
			            WHEN 0 THEN \'Sudah Bayar\'::text
			            ELSE \'Belum Bayar\'::text
			        END AS status_bayar
			   FROM (((((((((((((((pasienmasukpenunjang_t
			     JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
			     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
			     JOIN pasienadmisi_t ON ((pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
			     JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
			     LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
			     JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
			     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
			     JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
			     JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
			     JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
			     LEFT JOIN pegawai_m dokter_perujuk ON ((pasienadmisi_t.pegawai_id = dokter_perujuk.pegawai_id)))
			     LEFT JOIN gelarbelakang_m ON (((dokter_perujuk.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
			     LEFT JOIN ( SELECT hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
			            count(*) AS hasil
			           FROM (hasilpemeriksaanlabdetail_t
			             JOIN hasilpemeriksaanlab_t ON ((hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id = hasilpemeriksaanlab_t.hasilpemeriksaanlab_id)))
			          GROUP BY hasilpemeriksaanlab_t.pasienmasukpenunjang_id) hasil_manual ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasil_manual.pasienmasukpenunjang_id)))
			     LEFT JOIN ( SELECT count(*) AS jml_hasil,
			            hasilpemeriksaanlab_roche_t.order_no
			           FROM hasilpemeriksaanlab_roche_t
			          GROUP BY hasilpemeriksaanlab_roche_t.order_no) hasil ON (((pasienmasukpenunjang_t.no_masukpenunjang)::text = (hasil.order_no)::text)))
			     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
			            sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, (0)::double precision)) AS jumlah_tagihan
			           FROM tindakanpelayanan_t
			          WHERE ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false))
			          GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakanpelayanan ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id)))
			  WHERE ((pasienkirimkeunitlain_t.instalasi_id = 4) AND (pasienmasukpenunjang_t.status_periksa IS NOT NULL))
			UNION ALL
			 SELECT \'ORDER\'::text AS tipe_pasien,
			    pasienmasukpenunjang_t.pendaftaran_id,
			    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
			    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
			    pasienmasukpenunjang_t.tglmasukpenunjang,
			    pendaftaran_t.no_pendaftaran,
			    pasienmasukpenunjang_t.no_masukpenunjang,
			    pasien_m.no_rekam_medik,
			    pasien_m.nama_pasien,
			    pasienmasukpenunjang_t.pegawai_id,
			    pegawai_m.nama_pegawai AS dokter_penunjang,
			    pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
			    pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
			    instalasi_m.instalasi_nama AS asalrujukan_nama,
			    pasienmasukpenunjang_t.ruanganasal_id,
			    ruangan_m.ruangan_nama,
			    pasienmasukpenunjang_t.status_periksa,
			    pasienmasukpenunjang_t.no_antrian,
			    pendaftaran_t.carabayar_id,
			    carabayar_m.carabayar_nama,
			    pendaftaran_t.penjamin_id,
			    penjamin_m.penjamin_nama,
			    pendaftaran_t.kelaspelayanan_id,
			    kelaspelayanan_m.kelaspelayanan_nama,
			    pendaftaran_t.umur,
			    pasien_m.jeniskelamin,
			    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
			    pasien_m.tanggal_lahir,
			    ((pendaftaran_t.label_gelang)::json ->> \'resiko_jatuh\'::text) AS kuning,
			    ((pendaftaran_t.label_gelang)::json ->> \'alergi\'::text) AS merah,
			    ((pendaftaran_t.label_gelang)::json ->> \'dnr\'::text) AS ungu,
			    ((pendaftaran_t.label_gelang)::json ->> \'duplikat\'::text) AS coklat,
			    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
			    pasienmasukpenunjang_t.pasien_id,
			    pasienadmisi_t.pasienadmisi_id,
			    pasienmasukpenunjang_t.ruangan_id,
			    pasienmasukpenunjang_t.is_bayar,
			    pasienkirimkeunitlain_t.status_penunjang,
			    pasienmasukpenunjang_t.tanggal_verifikasi,
			    pendaftaran_t.instalasi_id,
			    NULL::text AS received_flag,
			    pasienmasukpenunjang_t.is_hasil,
			    pasien_m.alamat_pasien,
			    dokter_perujuk.pegawai_id AS dokter_perujuk_id,
			    dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
			    concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), \'\'::character varying), \' \', dokter_perujuk.nama_pegawai, \' \', COALESCE(gelarbelakang_m.gelarbelakang_nama, \'\'::character varying)) AS dokter_perujuk_nama_w_gelar,
			        CASE
			            WHEN (hasil_manual.hasil > 0) THEN true
			            ELSE false
			        END AS is_hasil_manual,
			    pasienmasukpenunjang_t.additional_data,
			        CASE
			            WHEN (COALESCE(hasil.jml_hasil, (0)::bigint) > 0) THEN true
			            ELSE false
			        END AS is_hasil_bridging,
			        CASE COALESCE(tindakanpelayanan.jumlah_tagihan, (0)::double precision)
			            WHEN 0 THEN \'Sudah Bayar\'::text
			            ELSE \'Belum Bayar\'::text
			        END AS status_bayar
			   FROM (((((((((((((((pasienmasukpenunjang_t
			     JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
			     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
			     LEFT JOIN pasienadmisi_t ON ((pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
			     JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
			     LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
			     JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
			     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
			     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
			     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
			     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
			     LEFT JOIN pegawai_m dokter_perujuk ON ((pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id)))
			     LEFT JOIN gelarbelakang_m ON (((dokter_perujuk.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
			     LEFT JOIN ( SELECT hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
			            count(*) AS hasil
			           FROM (hasilpemeriksaanlabdetail_t
			             JOIN hasilpemeriksaanlab_t ON ((hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id = hasilpemeriksaanlab_t.hasilpemeriksaanlab_id)))
			          GROUP BY hasilpemeriksaanlab_t.pasienmasukpenunjang_id) hasil_manual ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasil_manual.pasienmasukpenunjang_id)))
			     LEFT JOIN ( SELECT count(*) AS jml_hasil,
			            hasilpemeriksaanlab_roche_t.order_no
			           FROM hasilpemeriksaanlab_roche_t
			          GROUP BY hasilpemeriksaanlab_roche_t.order_no) hasil ON (((pasienmasukpenunjang_t.no_masukpenunjang)::text = (hasil.order_no)::text)))
			     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
			            sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, (0)::double precision)) AS jumlah_tagihan
			           FROM tindakanpelayanan_t
			          WHERE ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false))
			          GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakanpelayanan ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id)))
			  WHERE ((pasienkirimkeunitlain_t.instalasi_id = 4) AND (pasienmasukpenunjang_t.status_periksa IS NOT NULL) AND (pasienkirimkeunitlain_t.pasienadmisi_id IS NULL))
			UNION ALL
			 SELECT \'RUJUKAN RS\'::text AS tipe_pasien,
			    pasienmasukpenunjang_t.pendaftaran_id,
			    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
			    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
			    pendaftaran_t.tgl_pendaftaran AS tglmasukpenunjang,
			    pendaftaran_t.no_pendaftaran,
			    pasienmasukpenunjang_t.no_masukpenunjang,
			    pasien_m.no_rekam_medik,
			    pasien_m.nama_pasien,
			    pasienmasukpenunjang_t.pegawai_id,
			    pegawai_m.nama_pegawai AS dokter_penunjang,
			    rujukan_t.no_rujukan,
			    rujukan_t.asalrujukan_id,
			    asalrujukan_m.asalrujukan_nama,
			    rujukan_t.rujukandari_id AS ruanganasal_id,
			    perujuk_m.namaperujuk AS ruangan_nama,
			    pasienmasukpenunjang_t.status_periksa,
			    pasienmasukpenunjang_t.no_antrian,
			    pendaftaran_t.carabayar_id,
			    carabayar_m.carabayar_nama,
			    pendaftaran_t.penjamin_id,
			    penjamin_m.penjamin_nama,
			    pendaftaran_t.kelaspelayanan_id,
			    kelaspelayanan_m.kelaspelayanan_nama,
			    pendaftaran_t.umur,
			    pasien_m.jeniskelamin,
			    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
			    pasien_m.tanggal_lahir,
			    ((pendaftaran_t.label_gelang)::json ->> \'resiko_jatuh\'::text) AS kuning,
			    ((pendaftaran_t.label_gelang)::json ->> \'alergi\'::text) AS merah,
			    ((pendaftaran_t.label_gelang)::json ->> \'dnr\'::text) AS ungu,
			    ((pendaftaran_t.label_gelang)::json ->> \'duplikat\'::text) AS coklat,
			    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
			    pasienmasukpenunjang_t.pasien_id,
			    NULL::integer AS pasienadmisi_id,
			    pasienmasukpenunjang_t.ruangan_id,
			    pasienmasukpenunjang_t.is_bayar,
			    pasienkirimkeunitlain_t.status_penunjang,
			    pasienmasukpenunjang_t.tanggal_verifikasi,
			    pendaftaran_t.instalasi_id,
			    NULL::text AS received_flag,
			    pasienmasukpenunjang_t.is_hasil,
			    pasien_m.alamat_pasien,
			    dokter_perujuk.pegawai_id AS dokter_perujuk_id,
			    dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
			    concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), \'\'::character varying), \' \', dokter_perujuk.nama_pegawai, \' \', COALESCE(gelarbelakang_m.gelarbelakang_nama, \'\'::character varying)) AS dokter_perujuk_nama_w_gelar,
			        CASE
			            WHEN (hasil_manual.hasil > 0) THEN true
			            ELSE false
			        END AS is_hasil_manual,
			    pasienmasukpenunjang_t.additional_data,
			        CASE
			            WHEN (COALESCE(hasil.jml_hasil, (0)::bigint) > 0) THEN true
			            ELSE false
			        END AS is_hasil_bridging,
			        CASE COALESCE(tindakanpelayanan.jumlah_tagihan, (0)::double precision)
			            WHEN 0 THEN \'Sudah Bayar\'::text
			            ELSE \'Belum Bayar\'::text
			        END AS status_bayar
			   FROM (((((((((((((((pasienmasukpenunjang_t
			     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
			     JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
			     JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
			     LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
			     JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
			     LEFT JOIN perujuk_m ON ((rujukan_t.rujukandari_id = perujuk_m.perujuk_id)))
			     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
			     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
			     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
			     LEFT JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
			     LEFT JOIN pegawai_m dokter_perujuk ON ((pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id)))
			     LEFT JOIN gelarbelakang_m ON (((dokter_perujuk.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
			     LEFT JOIN ( SELECT hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
			            count(*) AS hasil
			           FROM (hasilpemeriksaanlabdetail_t
			             JOIN hasilpemeriksaanlab_t ON ((hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id = hasilpemeriksaanlab_t.hasilpemeriksaanlab_id)))
			          GROUP BY hasilpemeriksaanlab_t.pasienmasukpenunjang_id) hasil_manual ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasil_manual.pasienmasukpenunjang_id)))
			     LEFT JOIN ( SELECT count(*) AS jml_hasil,
			            hasilpemeriksaanlab_roche_t.order_no
			           FROM hasilpemeriksaanlab_roche_t
			          GROUP BY hasilpemeriksaanlab_roche_t.order_no) hasil ON (((pasienmasukpenunjang_t.no_masukpenunjang)::text = (hasil.order_no)::text)))
			     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
			            sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, (0)::double precision)) AS jumlah_tagihan
			           FROM tindakanpelayanan_t
			          WHERE ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false))
			          GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakanpelayanan ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id)))
			  WHERE ((pendaftaran_t.instalasi_id = 4) AND (pasienmasukpenunjang_t.status_periksa IS NOT NULL))
			UNION ALL
			 SELECT \'APS\'::text AS tipe_pasien,
			    pasienmasukpenunjang_t.pendaftaran_id,
			    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
			    pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
			    pendaftaran_t.tgl_pendaftaran AS tglmasukpenunjang,
			    pendaftaran_t.no_pendaftaran,
			    pasienmasukpenunjang_t.no_masukpenunjang,
			    pasien_m.no_rekam_medik,
			    pasien_m.nama_pasien,
			    pasienmasukpenunjang_t.pegawai_id,
			    pegawai_m.nama_pegawai AS dokter_penunjang,
			    NULL::character varying AS no_rujukan,
			    pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
			    \'APS\'::character varying AS asalrujukan_nama,
			    pasienmasukpenunjang_t.ruanganasal_id,
			    ruangan_m.ruangan_nama,
			    pasienmasukpenunjang_t.status_periksa,
			    pasienmasukpenunjang_t.no_antrian,
			    pendaftaran_t.carabayar_id,
			    carabayar_m.carabayar_nama,
			    pendaftaran_t.penjamin_id,
			    penjamin_m.penjamin_nama,
			    pendaftaran_t.kelaspelayanan_id,
			    kelaspelayanan_m.kelaspelayanan_nama,
			    pendaftaran_t.umur,
			    pasien_m.jeniskelamin,
			    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
			    pasien_m.tanggal_lahir,
			    ((pendaftaran_t.label_gelang)::json ->> \'resiko_jatuh\'::text) AS kuning,
			    ((pendaftaran_t.label_gelang)::json ->> \'alergi\'::text) AS merah,
			    ((pendaftaran_t.label_gelang)::json ->> \'dnr\'::text) AS ungu,
			    ((pendaftaran_t.label_gelang)::json ->> \'duplikat\'::text) AS coklat,
			    pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
			    pasienmasukpenunjang_t.pasien_id,
			    NULL::integer AS pasienadmisi_id,
			    pasienmasukpenunjang_t.ruangan_id,
			    pasienmasukpenunjang_t.is_bayar,
			    pasienkirimkeunitlain_t.status_penunjang,
			    pasienmasukpenunjang_t.tanggal_verifikasi,
			    pendaftaran_t.instalasi_id,
			    NULL::text AS received_flag,
			    pasienmasukpenunjang_t.is_hasil,
			    pasien_m.alamat_pasien,
			    dokter_perujuk.pegawai_id AS dokter_perujuk_id,
			    dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
			    concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), \'\'::character varying), \' \', dokter_perujuk.nama_pegawai, \' \', COALESCE(gelarbelakang_m.gelarbelakang_nama, \'\'::character varying)) AS dokter_perujuk_nama_w_gelar,
			        CASE
			            WHEN (hasil_manual.hasil > 0) THEN true
			            ELSE false
			        END AS is_hasil_manual,
			    pasienmasukpenunjang_t.additional_data,
			        CASE
			            WHEN (COALESCE(hasil.jml_hasil, (0)::bigint) > 0) THEN true
			            ELSE false
			        END AS is_hasil_bridging,
			        CASE COALESCE(tindakanpelayanan.jumlah_tagihan, (0)::double precision)
			            WHEN 0 THEN \'Sudah Bayar\'::text
			            ELSE \'Belum Bayar\'::text
			        END AS status_bayar
			   FROM (((((((((((((((pasienmasukpenunjang_t 
			     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
			     JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
			     LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
			     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
			     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
			     JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
			     LEFT JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
			     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
			     JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
			     JOIN ruangan_m ruang_penunjang ON ((pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id)))
			     LEFT JOIN pegawai_m dokter_perujuk ON ((pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id)))
			     LEFT JOIN gelarbelakang_m ON (((dokter_perujuk.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
			     LEFT JOIN ( SELECT hasilpemeriksaanlab_t.pasienmasukpenunjang_id,
			            count(*) AS hasil
			           FROM (hasilpemeriksaanlabdetail_t
			             JOIN hasilpemeriksaanlab_t ON ((hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id = hasilpemeriksaanlab_t.hasilpemeriksaanlab_id)))
			          GROUP BY hasilpemeriksaanlab_t.pasienmasukpenunjang_id) hasil_manual ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasil_manual.pasienmasukpenunjang_id)))
			     LEFT JOIN ( SELECT count(*) AS jml_hasil,
			            hasilpemeriksaanlab_roche_t.order_no
			           FROM hasilpemeriksaanlab_roche_t
			          GROUP BY hasilpemeriksaanlab_roche_t.order_no) hasil ON (((pasienmasukpenunjang_t.no_masukpenunjang)::text = (hasil.order_no)::text)))
			     LEFT JOIN ( SELECT tindakanpelayanan_t.pasienmasukpenunjang_id,
			            sum(COALESCE(tindakanpelayanan_t.tarif_tindakan, (0)::double precision)) AS jumlah_tagihan
			           FROM tindakanpelayanan_t
			          WHERE ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false))
			          GROUP BY tindakanpelayanan_t.pasienmasukpenunjang_id) tindakanpelayanan ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan.pasienmasukpenunjang_id)))
			  WHERE ((ruang_penunjang.instalasi_id = 4) AND (pendaftaran_t.is_aps = true) AND (pasienmasukpenunjang_t.status_periksa IS NOT NULL) AND
			        CASE
			            WHEN (pendaftaran_t.carabayar_id <> 2) THEN (pasienmasukpenunjang_t.is_bayar = true)
			            ELSE (pasienmasukpenunjang_t.is_deleted IS FALSE)
			        END);
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."nilaipemeriksaanlabdetail_v";
        ');

        $this->execute('
            CREATE VIEW "public"."nilaipemeriksaanlabdetail_v" AS  SELECT pemeriksaanlab_m.pemeriksaanlab_id,
			    pemeriksaanlab_m.daftartindakan_id,
			    daftartindakan_m.daftartindakan_nama,
			    NULL::integer AS tipepaket_id,
			    NULL::character varying AS tipepaket_nama,
			    nilairujukan_m.nilairujukan_id,
			    nilairujukan_m.nama_rujukan,
			    nilairujukan_m.jenis_kelamin,
			    fgetnamalookup(nilairujukan_m.jenis_kelamin) AS jenis_kelamin_nama,
			    nilairujukan_m.golonganumur_id,
			    golonganumurlab_m.gol_umurlab_nama, 
			    COALESCE(nilairujukan_m.umur_awal, 0) AS gol_umurlab_minimal,
			    COALESCE(nilairujukan_m.umur_akhir, 54750) AS gol_umurlab_maksimal,
			    nilairujukan_m.nilai_rujukan,
			    nilairujukan_m.nilai_min,
			    nilairujukan_m.nilai_max,
			    nilairujukan_m.satuan_hasillab AS satuanlab_nama,
			    nilairujukan_m.keterangan,
			    hasilpemeriksaanlabdetail_t.hasil,
			    hasilpemeriksaanlabdetail_t.petugaslab_id,
			    hasilpemeriksaanlabdetail_t.hasilpemeriksaanlabdetail_id,
			    petugaslab.nama_pegawai AS petugaslab_nama,
			    ambilsample_t.samplelab_id,
			    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
			    hasilpemeriksaanlab_t.hasilpemeriksaanlab_id,
			    nilairujukan_m.is_deleted,
			    hasilpemeriksaanlabdetail_t.metode,
			    jenispemeriksaanlab_m.jenispemeriksaanlab_id,
			    jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
			    (COALESCE(hasilpemeriksaanlabdetail_t.no_urut, nilairujukan_m.no_urut))::integer AS no_urut,
			    hasilpemeriksaanlabdetail_t.is_verifikasi,
			    hasilpemeriksaanlabdetail_t.tanggal_verifikasi,
			    hasilpemeriksaanlabdetail_t.petugas_verifikasi
			   FROM ((((((((((ambilsample_t
			     JOIN pemeriksaanlab_m ON ((ambilsample_t.tindakanpaket_id = pemeriksaanlab_m.daftartindakan_id)))
			     JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
			     JOIN pasienmasukpenunjang_t ON ((ambilsample_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id)))
			     JOIN daftartindakan_m ON ((pemeriksaanlab_m.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
			     LEFT JOIN nilairujukan_m ON ((pemeriksaanlab_m.pemeriksaanlab_id = nilairujukan_m.pemeriksaanlab_id)))
			     LEFT JOIN golonganumurlab_m ON ((nilairujukan_m.golonganumur_id = golonganumurlab_m.golonganumurlab_id)))
			     LEFT JOIN hasilpemeriksaanlab_t ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanlab_t.pasienmasukpenunjang_id) AND (ambilsample_t.samplelab_id = hasilpemeriksaanlab_t.samplelab_id))))
			     LEFT JOIN hasilpemeriksaanlabdetail_t ON (((pemeriksaanlab_m.pemeriksaanlab_id = hasilpemeriksaanlabdetail_t.pemeriksaanlab_id) AND (nilairujukan_m.nilairujukan_id = hasilpemeriksaanlabdetail_t.nilairujukan_id) AND (hasilpemeriksaanlab_t.hasilpemeriksaanlab_id = hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id))))
			     LEFT JOIN pegawai_m petugaslab ON ((hasilpemeriksaanlabdetail_t.petugaslab_id = petugaslab.pegawai_id)))
			     LEFT JOIN samplelab_m ON ((hasilpemeriksaanlabdetail_t.samplelab_id = samplelab_m.samplelab_id)))
			  WHERE ((ambilsample_t.is_deleted = false) AND (ambilsample_t.is_active = true) AND (pemeriksaanlab_m.is_deleted = false) AND (pemeriksaanlab_m.is_active = true) AND (daftartindakan_m.is_deleted = false) AND (daftartindakan_m.is_active = true))
			UNION ALL
			 SELECT pemeriksaanlab_m.pemeriksaanlab_id,
			    paketpelayanan_mp.daftartindakan_id,
			    daftartindakan_m.daftartindakan_nama,
			    pemeriksaanlab_m.tipepaket_id,
			    tipepaket_m.tipepaket_nama,
			    nilairujukan_m.nilairujukan_id,
			    nilairujukan_m.nama_rujukan,
			    nilairujukan_m.jenis_kelamin,
			    fgetnamalookup(nilairujukan_m.jenis_kelamin) AS jenis_kelamin_nama,
			    nilairujukan_m.golonganumur_id,
			    golonganumurlab_m.gol_umurlab_nama,
			    COALESCE(nilairujukan_m.umur_awal, 0) AS gol_umurlab_minimal,
			    COALESCE(nilairujukan_m.umur_akhir, 54750) AS gol_umurlab_maksimal,
			    nilairujukan_m.nilai_rujukan,
			    nilairujukan_m.nilai_min,
			    nilairujukan_m.nilai_max,
			    nilairujukan_m.satuan_hasillab AS satuanlab_nama,
			    nilairujukan_m.keterangan,
			    hasilpemeriksaanlabdetail_t.hasil,
			    hasilpemeriksaanlabdetail_t.petugaslab_id,
			    hasilpemeriksaanlabdetail_t.hasilpemeriksaanlabdetail_id,
			    petugaslab.nama_pegawai AS petugaslab_nama,
			    ambilsample_t.samplelab_id,
			    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
			    hasilpemeriksaanlab_t.hasilpemeriksaanlab_id,
			    nilairujukan_m.is_deleted,
			    hasilpemeriksaanlabdetail_t.metode,
			    jenispemeriksaanlab_m.jenispemeriksaanlab_id,
			    jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
			    (COALESCE(hasilpemeriksaanlabdetail_t.no_urut, nilairujukan_m.no_urut))::integer AS no_urut,
			    hasilpemeriksaanlabdetail_t.is_verifikasi,
			    hasilpemeriksaanlabdetail_t.tanggal_verifikasi,
			    hasilpemeriksaanlabdetail_t.petugas_verifikasi
			   FROM ((((((((((((ambilsample_t
			     JOIN pemeriksaanlab_m ON ((ambilsample_t.tindakanpaket_id = pemeriksaanlab_m.tipepaket_id)))
			     JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
			     JOIN pasienmasukpenunjang_t ON ((ambilsample_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id)))
			     JOIN tipepaket_m ON ((pemeriksaanlab_m.tipepaket_id = tipepaket_m.tipepaket_id)))
			     JOIN paketpelayanan_mp ON ((pemeriksaanlab_m.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
			     JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
			     LEFT JOIN nilairujukan_m ON ((pemeriksaanlab_m.pemeriksaanlab_id = nilairujukan_m.pemeriksaanlab_id)))
			     LEFT JOIN golonganumurlab_m ON ((nilairujukan_m.golonganumur_id = golonganumurlab_m.golonganumurlab_id)))
			     LEFT JOIN hasilpemeriksaanlab_t ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanlab_t.pasienmasukpenunjang_id) AND (ambilsample_t.samplelab_id = hasilpemeriksaanlab_t.samplelab_id))))
			     LEFT JOIN hasilpemeriksaanlabdetail_t ON (((pemeriksaanlab_m.pemeriksaanlab_id = hasilpemeriksaanlabdetail_t.pemeriksaanlab_id) AND (nilairujukan_m.nilairujukan_id = hasilpemeriksaanlabdetail_t.nilairujukan_id) AND (hasilpemeriksaanlab_t.hasilpemeriksaanlab_id = hasilpemeriksaanlabdetail_t.hasilpemeriksaanlab_id))))
			     LEFT JOIN pegawai_m petugaslab ON ((hasilpemeriksaanlabdetail_t.petugaslab_id = petugaslab.pegawai_id)))
			     LEFT JOIN samplelab_m ON ((hasilpemeriksaanlabdetail_t.samplelab_id = samplelab_m.samplelab_id)))
			  WHERE ((ambilsample_t.is_deleted = false) AND (ambilsample_t.is_active = true) AND (pemeriksaanlab_m.is_deleted = false) AND (pemeriksaanlab_m.is_active = true) AND (daftartindakan_m.is_deleted = false) AND (daftartindakan_m.is_active = true));
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."infopasienradiologi_v";
        ');

        $this->execute('
        	CREATE VIEW "public"."infopasienradiologi_v" AS  SELECT header.tipe_pasien, 
			    header.pendaftaran_id,
			    header.pasienmasukpenunjang_id,
			    header.pasienkirimkeunitlain_id,
			    header.tglmasukpenunjang,
			    header.no_pendaftaran,
			    header.no_masukpenunjang,
			    header.no_rekam_medik,
			    header.nama_pasien,
			        CASE header.is_mcu
			            WHEN true THEN header.pegawai_id
			            ELSE detail.dokter_id_detail
			        END AS pegawai_id,
			        CASE header.is_mcu
			            WHEN true THEN header.dokter_penunjang
			            ELSE detail.dokter_nama_detail
			        END AS dokter_penunjang,
			    header.no_rujukan,
			    header.asalrujukan_id,
			    header.asalrujukan_nama,
			    header.ruanganasal_id,
			    header.ruangan_nama,
			    header.status_periksa,
			    header.no_antrian,
			    header.carabayar_id,
			    header.carabayar_nama,
			    header.penjamin_id,
			    header.penjamin_nama,
			    header.kelaspelayanan_id,
			    header.kelaspelayanan_nama,
			    header.umur,
			    header.jeniskelamin,
			    header.j_kelamin,
			    header.tanggal_lahir,
			    header.kuning,
			    header.merah,
			    header.ungu,
			    header.coklat,
			    header.tgl_rujukan,
			    header.pasien_id,
			    header.pasienadmisi_id,
			    header.ruangan_id,
			    header.is_bayar,
			    header.status_penunjang,
			    header.catatan_dokterpengirim,
			    header.no_telepon_pasien,
			    header.dokter_perujuk_id,
			    header.dokter_perujuk_nama,
			        CASE header.is_mcu
			            WHEN true THEN (header.nama_dokter_penunjang)::character varying
			            ELSE detail.dokter_nama_detail
			        END AS nama_dokter_penunjang,
			    header.unit_asal,
			    header.nama_diagnosa,
			    header.is_mcu,
			    detail.jenispemeriksaanrad_nama,
			    detail.tipepaket_nama,
			    detail.detail_2,
			    detail.daftartindakan_id,
			    detail.daftartindakan_nama,
			        CASE
			            WHEN (hasil.is_hasil >= 1) THEN true
			            ELSE false
			        END AS is_hasil,
			    detail.tindakanpelayanan_id,
			    hasil.tgl_verifikasi,
			    header.created_by,
			    header.penjamin_kode,
			    detail.cyto_tindakan,
			    detail.qty_tindakan,
			    header.no_identitas_pasien,
			    detail.hasilpemeriksaanrad_id,
			    detail.status_bayar,
			    detail.status_periksa_penunjang,
			    detail.status_batal,
			    header.groupcarabayar_id,
			    header.sepesial_pemeriksaan,
			    header.perujuk_id,
			    header.perujuk_nama,
			    header.catatan
			   FROM ((( SELECT \'ORDER\'::text AS tipe_pasien,
			            pasienmasukpenunjang_t.pendaftaran_id,
			            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
			            pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
			            pasienmasukpenunjang_t.tglmasukpenunjang,
			            pendaftaran_t.no_pendaftaran,
			            pasienmasukpenunjang_t.no_masukpenunjang,
			            pasien_m.no_rekam_medik,
			            pasien_m.nama_pasien,
			            pasienmasukpenunjang_t.pegawai_id,
			            pegawai_m.nama_pegawai AS dokter_penunjang,
			            pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
			            pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
			            instalasi_m.instalasi_nama AS asalrujukan_nama,
			            pasienmasukpenunjang_t.ruanganasal_id,
			            ruangan_m.ruangan_nama,
			            COALESCE(pasienmasukpenunjang_t.status_periksa, \'477\'::character varying) AS status_periksa,
			            pasienmasukpenunjang_t.no_antrian,
			            pendaftaran_t.carabayar_id,
			            carabayar_m.carabayar_nama,
			            pendaftaran_t.penjamin_id,
			            penjamin_m.penjamin_nama,
			            pendaftaran_t.kelaspelayanan_id,
			            kelaspelayanan_m.kelaspelayanan_nama,
			            pendaftaran_t.umur,
			            pasien_m.jeniskelamin,
			            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
			            pasien_m.tanggal_lahir,
			            ((pendaftaran_t.label_gelang)::json ->> \'resiko_jatuh\'::text) AS kuning,
			            ((pendaftaran_t.label_gelang)::json ->> \'alergi\'::text) AS merah,
			            ((pendaftaran_t.label_gelang)::json ->> \'dnr\'::text) AS ungu,
			            ((pendaftaran_t.label_gelang)::json ->> \'duplikat\'::text) AS coklat,
			            pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
			            pasienmasukpenunjang_t.pasien_id,
			            pasienadmisi_t.pasienadmisi_id,
			            pasienmasukpenunjang_t.ruangan_id,
			            pasienmasukpenunjang_t.is_bayar,
			            pasienkirimkeunitlain_t.status_penunjang,
			            COALESCE(pasienmasukpenunjang_t.catatan, pasienkirimkeunitlain_t.catatan_dokterpengirim) AS catatan_dokterpengirim,
			            pasien_m.no_telepon_pasien,
			            dokter_perujuk.pegawai_id AS dokter_perujuk_id,
			            dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
			            concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), \'\'::character varying), \' \', dokter_perujuk.nama_pegawai, \' \', COALESCE(gelarbelakang_m.gelarbelakang_nama, \'\'::character varying)) AS nama_pegawai,
			            concat(COALESCE(fgetnamalookup((pegawai_m.gelardepan)::integer), \'\'::character varying), \' \', pegawai_m.nama_pegawai, \' \', COALESCE(gelar_penunjang.gelarbelakang_nama, \'\'::character varying)) AS nama_dokter_penunjang,
			                CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
			                    WHEN 0 THEN \'Pendaftaran\'::text
			                    ELSE \'Unit\'::text
			                END AS unit_asal,
			            diagnosa.diagnosa_utama AS nama_diagnosa,
			            false AS is_mcu,
			            pasienmasukpenunjang_t.created_by,
			            penjamin_m.penjamin_kode,
			            COALESCE(pasien_m.no_identitas_pasien, (pasien_m.additional_pasien)::character varying) AS no_identitas_pasien,
			            carabayar_m.groupcarabayar_id,
			            true AS sepesial_pemeriksaan,
			            NULL::integer AS perujuk_id,
			            NULL::character varying AS perujuk_nama,
			            COALESCE(pasienmasukpenunjang_t.catatan, pasienkirimkeunitlain_t.catatan_dokterpengirim) AS catatan
			           FROM ((((((((((((((pasienmasukpenunjang_t
			             JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
			             JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
			             JOIN pasienadmisi_t ON ((pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
			             JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
			             LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
			             JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
			             JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
			             JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
			             JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
			             JOIN kelaspelayanan_m ON ((pasienadmisi_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
			             LEFT JOIN pegawai_m dokter_perujuk ON ((COALESCE(pasienadmisi_t.pegawai_id, pendaftaran_t.pegawai_id) = dokter_perujuk.pegawai_id)))
			             LEFT JOIN gelarbelakang_m ON (((dokter_perujuk.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
			             LEFT JOIN gelarbelakang_m gelar_penunjang ON (((pegawai_m.gelarbelakang)::integer = gelar_penunjang.gelarbelakang_id)))
			             LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
			                        CASE
			                            WHEN (pasienmorbiditas_t.kelompokdiagnosa_id = 2) THEN pasienmorbiditas_t.diagnosa_pasien
			                            ELSE NULL::json
			                        END AS diagnosa_utama
			                   FROM ((pendaftaran_t pendaftaran_t_1
			                     JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
			                     JOIN pasienmorbiditas_t ON (((pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id) AND (pasienmorbiditas_t.is_deleted = false))))
			                  WHERE ((pasienmorbiditas_t.kelompokdiagnosa_id = 2) AND (pasienmorbiditas_t.diagnosa_pasien IS NOT NULL))
			                UNION ALL
			                 SELECT pendaftaran_t_1.pendaftaran_id,
			                    cppt_t.a_diag_utama AS diagnosa_utama
			                   FROM ((pendaftaran_t pendaftaran_t_1
			                     JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
			                     JOIN ( SELECT cppt_t_1.cppt_id,
			                            cppt_t_1.pendaftaran_id,
			                            cppt_t_1.a_diag_utama,
			                            cppt_t_1.a_diag_penyerta
			                           FROM (cppt_t cppt_t_1
			                             JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
			                                    cppt_last.pendaftaran_id
			                                   FROM cppt_t cppt_last
			                                  WHERE (cppt_last.is_deleted = false)
			                                  GROUP BY cppt_last.pendaftaran_id) cppt_max ON (((cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id) AND (cppt_t_1.cppt_id = cppt_max.cppt_id))))) cppt_t ON ((pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id)))
			                  WHERE (cppt_t.a_diag_utama IS NOT NULL)
			                UNION ALL
			                 SELECT pendaftaran_t_1.pendaftaran_id,
			                    resumemedisri_t.diag_utama AS diagnosa_utama
			                   FROM (((pendaftaran_t pendaftaran_t_1
			                     JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
			                     JOIN pasienadmisi_t pasienadmisi_t_1 ON ((pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id)))
			                     JOIN resumemedisri_t ON (((pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id) AND (pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id))))
			                  WHERE (resumemedisri_t.diag_utama IS NOT NULL)) diagnosa ON ((pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id)))
			          WHERE (pasienkirimkeunitlain_t.instalasi_id = 5)
			        UNION ALL
			         SELECT \'ORDER\'::text AS tipe_pasien,
			            pasienmasukpenunjang_t.pendaftaran_id,
			            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
			            pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
			            pasienmasukpenunjang_t.tglmasukpenunjang,
			            pendaftaran_t.no_pendaftaran,
			            pasienmasukpenunjang_t.no_masukpenunjang,
			            pasien_m.no_rekam_medik,
			            pasien_m.nama_pasien,
			            pasienmasukpenunjang_t.pegawai_id,
			            pegawai_m.nama_pegawai AS dokter_penunjang,
			            pasienkirimkeunitlain_t.no_orderkeunitlain AS no_rujukan,
			            pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
			            instalasi_m.instalasi_nama AS asalrujukan_nama,
			            pasienmasukpenunjang_t.ruanganasal_id,
			            ruangan_m.ruangan_nama,
			            COALESCE(pasienmasukpenunjang_t.status_periksa, \'477\'::character varying) AS status_periksa,
			            pasienmasukpenunjang_t.no_antrian,
			            pendaftaran_t.carabayar_id,
			            carabayar_m.carabayar_nama,
			            pendaftaran_t.penjamin_id,
			            penjamin_m.penjamin_nama,
			            pendaftaran_t.kelaspelayanan_id,
			            kelaspelayanan_m.kelaspelayanan_nama,
			            pendaftaran_t.umur,
			            pasien_m.jeniskelamin,
			            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
			            pasien_m.tanggal_lahir,
			            ((pendaftaran_t.label_gelang)::json ->> \'resiko_jatuh\'::text) AS kuning,
			            ((pendaftaran_t.label_gelang)::json ->> \'alergi\'::text) AS merah,
			            ((pendaftaran_t.label_gelang)::json ->> \'dnr\'::text) AS ungu,
			            ((pendaftaran_t.label_gelang)::json ->> \'duplikat\'::text) AS coklat,
			            pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_rujukan,
			            pasienmasukpenunjang_t.pasien_id,
			            pasienadmisi_t.pasienadmisi_id,
			            pasienmasukpenunjang_t.ruangan_id,
			            pasienmasukpenunjang_t.is_bayar,
			            pasienkirimkeunitlain_t.status_penunjang,
			            COALESCE(pasienmasukpenunjang_t.catatan, pasienkirimkeunitlain_t.catatan_dokterpengirim) AS catatan_dokterpengirim,
			            pasien_m.no_telepon_pasien,
			            dokter_perujuk.pegawai_id AS dokter_perujuk_id,
			            dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
			            concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), \'\'::character varying), \' \', dokter_perujuk.nama_pegawai, \' \', COALESCE(gelarbelakang_m.gelarbelakang_nama, \'\'::character varying)) AS nama_pegawai,
			            concat(COALESCE(fgetnamalookup((pegawai_m.gelardepan)::integer), \'\'::character varying), \' \', pegawai_m.nama_pegawai, \' \', COALESCE(gelar_penunjang.gelarbelakang_nama, \'\'::character varying)) AS nama_dokter_penunjang,
			                CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
			                    WHEN 0 THEN \'Pendaftaran\'::text
			                    ELSE \'Unit\'::text
			                END AS unit_asal,
			            diagnosa.diagnosa_utama AS nama_diagnosa,
			            false AS is_mcu,
			            pasienmasukpenunjang_t.created_by,
			            penjamin_m.penjamin_kode,
			            COALESCE(pasien_m.no_identitas_pasien, (pasien_m.additional_pasien)::character varying) AS no_identitas_pasien,
			            carabayar_m.groupcarabayar_id,
			                CASE
			                    WHEN (pendaftaran_t.instalasi_id = 2) THEN true
			                    ELSE false
			                END AS sepesial_pemeriksaan,
			            NULL::integer AS perujuk_id,
			            NULL::character varying AS perujuk_nama,
			            COALESCE(pasienmasukpenunjang_t.catatan, pasienkirimkeunitlain_t.catatan_dokterpengirim) AS catatan
			           FROM ((((((((((((((pasienmasukpenunjang_t
			             JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
			             JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
			             LEFT JOIN pasienadmisi_t ON ((pasienmasukpenunjang_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
			             JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
			             LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
			             JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
			             JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
			             JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
			             JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
			             JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
			             LEFT JOIN pegawai_m dokter_perujuk ON ((pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id)))
			             LEFT JOIN gelarbelakang_m ON (((dokter_perujuk.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
			             LEFT JOIN gelarbelakang_m gelar_penunjang ON (((pegawai_m.gelarbelakang)::integer = gelar_penunjang.gelarbelakang_id)))
			             LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
			                        CASE
			                            WHEN (pasienmorbiditas_t.kelompokdiagnosa_id = 2) THEN pasienmorbiditas_t.diagnosa_pasien
			                            ELSE NULL::json
			                        END AS diagnosa_utama
			                   FROM ((pendaftaran_t pendaftaran_t_1
			                     JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
			                     JOIN pasienmorbiditas_t ON (((pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id) AND (pasienmorbiditas_t.is_deleted = false))))
			                  WHERE ((pasienmorbiditas_t.kelompokdiagnosa_id = 2) AND (pasienmorbiditas_t.diagnosa_pasien IS NOT NULL))
			                UNION ALL
			                 SELECT pendaftaran_t_1.pendaftaran_id,
			                    cppt_t.a_diag_utama AS diagnosa_utama
			                   FROM ((pendaftaran_t pendaftaran_t_1
			                     JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
			                     JOIN ( SELECT cppt_t_1.cppt_id,
			                            cppt_t_1.pendaftaran_id,
			                            cppt_t_1.a_diag_utama,
			                            cppt_t_1.a_diag_penyerta
			                           FROM (cppt_t cppt_t_1
			                             JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
			                                    cppt_last.pendaftaran_id
			                                   FROM cppt_t cppt_last
			                                  WHERE (cppt_last.is_deleted = false)
			                                  GROUP BY cppt_last.pendaftaran_id) cppt_max ON (((cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id) AND (cppt_t_1.cppt_id = cppt_max.cppt_id))))) cppt_t ON ((pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id)))
			                  WHERE (cppt_t.a_diag_utama IS NOT NULL)
			                UNION ALL
			                 SELECT pendaftaran_t_1.pendaftaran_id,
			                    resumemedisri_t.diag_utama AS diagnosa_utama
			                   FROM (((pendaftaran_t pendaftaran_t_1
			                     JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
			                     JOIN pasienadmisi_t pasienadmisi_t_1 ON ((pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id)))
			                     JOIN resumemedisri_t ON (((pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id) AND (pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id))))
			                  WHERE (resumemedisri_t.diag_utama IS NOT NULL)) diagnosa ON ((pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id)))
			          WHERE ((pasienkirimkeunitlain_t.instalasi_id = 5) AND (pasienkirimkeunitlain_t.pasienadmisi_id IS NULL))
			        UNION ALL
			         SELECT \'RUJUKAN RS\'::text AS tipe_pasien,
			            pasienmasukpenunjang_t.pendaftaran_id,
			            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
			            pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
			            pendaftaran_t.tgl_pendaftaran AS tglmasukpenunjang,
			            pendaftaran_t.no_pendaftaran,
			            pasienmasukpenunjang_t.no_masukpenunjang,
			            pasien_m.no_rekam_medik,
			            pasien_m.nama_pasien,
			            pasienmasukpenunjang_t.pegawai_id,
			            pegawai_m.nama_pegawai AS dokter_penunjang,
			            rujukan_t.no_rujukan,
			            rujukan_t.asalrujukan_id,
			            asalrujukan_m.asalrujukan_nama,
			            NULL::integer AS ruanganasal_id,
			            NULL::character varying AS ruangan_nama,
			            COALESCE(pasienmasukpenunjang_t.status_periksa, \'477\'::character varying) AS status_periksa,
			            pasienmasukpenunjang_t.no_antrian,
			            pendaftaran_t.carabayar_id,
			            carabayar_m.carabayar_nama,
			            pendaftaran_t.penjamin_id,
			            penjamin_m.penjamin_nama,
			            pendaftaran_t.kelaspelayanan_id,
			            kelaspelayanan_m.kelaspelayanan_nama,
			            pendaftaran_t.umur,
			            pasien_m.jeniskelamin,
			            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
			            pasien_m.tanggal_lahir,
			            ((pendaftaran_t.label_gelang)::json ->> \'resiko_jatuh\'::text) AS kuning,
			            ((pendaftaran_t.label_gelang)::json ->> \'alergi\'::text) AS merah,
			            ((pendaftaran_t.label_gelang)::json ->> \'dnr\'::text) AS ungu,
			            ((pendaftaran_t.label_gelang)::json ->> \'duplikat\'::text) AS coklat,
			            pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
			            pasienmasukpenunjang_t.pasien_id,
			            NULL::integer AS pasienadmisi_id,
			            pasienmasukpenunjang_t.ruangan_id,
			            pasienmasukpenunjang_t.is_bayar,
			            pasienkirimkeunitlain_t.status_penunjang,
			            COALESCE(pasienmasukpenunjang_t.catatan, pasienkirimkeunitlain_t.catatan_dokterpengirim) AS catatan_dokterpengirim,
			            pasien_m.no_telepon_pasien,
			            dokter_perujuk.pegawai_id AS dokter_perujuk_id,
			            dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
			            concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), \'\'::character varying), \' \', dokter_perujuk.nama_pegawai, \' \', COALESCE(gelarbelakang_m.gelarbelakang_nama, \'\'::character varying)) AS nama_pegawai,
			            concat(COALESCE(fgetnamalookup((pegawai_m.gelardepan)::integer), \'\'::character varying), \' \', pegawai_m.nama_pegawai, \' \', COALESCE(gelar_penunjang.gelarbelakang_nama, \'\'::character varying)) AS nama_dokter_penunjang,
			                CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
			                    WHEN 0 THEN \'Pendaftaran\'::text
			                    ELSE \'Unit\'::text
			                END AS unit_asal,
			            diagnosa.diagnosa_utama AS nama_diagnosa,
			            false AS is_mcu,
			            pasienmasukpenunjang_t.created_by,
			            penjamin_m.penjamin_kode,
			            COALESCE(pasien_m.no_identitas_pasien, (pasien_m.additional_pasien)::character varying) AS no_identitas_pasien,
			            carabayar_m.groupcarabayar_id,
			            false AS sepesial_pemeriksaan,
			            rujukan_t.rujukandari_id AS perujuk_id,
			            rujukan_t.nama_perujuk AS perujuk_nama,
			            COALESCE(pasienmasukpenunjang_t.catatan, pasienkirimkeunitlain_t.catatan_dokterpengirim) AS catatan
			           FROM ((((((((((((((pasienmasukpenunjang_t
			             JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
			             JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
			             JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
			             LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
			             JOIN asalrujukan_m ON ((rujukan_t.asalrujukan_id = asalrujukan_m.asalrujukan_id)))
			             LEFT JOIN perujuk_m ON ((rujukan_t.rujukandari_id = perujuk_m.perujuk_id)))
			             JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
			             JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
			             JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
			             LEFT JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
			             LEFT JOIN pegawai_m dokter_perujuk ON ((pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id)))
			             LEFT JOIN gelarbelakang_m ON (((dokter_perujuk.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
			             LEFT JOIN gelarbelakang_m gelar_penunjang ON (((pegawai_m.gelarbelakang)::integer = gelar_penunjang.gelarbelakang_id)))
			             LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
			                        CASE
			                            WHEN (pasienmorbiditas_t.kelompokdiagnosa_id = 2) THEN pasienmorbiditas_t.diagnosa_pasien
			                            ELSE NULL::json
			                        END AS diagnosa_utama
			                   FROM ((pendaftaran_t pendaftaran_t_1
			                     JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
			                     JOIN pasienmorbiditas_t ON (((pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id) AND (pasienmorbiditas_t.is_deleted = false))))
			                  WHERE ((pasienmorbiditas_t.kelompokdiagnosa_id = 2) AND (pasienmorbiditas_t.diagnosa_pasien IS NOT NULL))
			                UNION ALL
			                 SELECT pendaftaran_t_1.pendaftaran_id,
			                    cppt_t.a_diag_utama AS diagnosa_utama
			                   FROM ((pendaftaran_t pendaftaran_t_1
			                     JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
			                     JOIN ( SELECT cppt_t_1.cppt_id,
			                            cppt_t_1.pendaftaran_id,
			                            cppt_t_1.a_diag_utama,
			                            cppt_t_1.a_diag_penyerta
			                           FROM (cppt_t cppt_t_1
			                             JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
			                                    cppt_last.pendaftaran_id
			                                   FROM cppt_t cppt_last
			                                  WHERE (cppt_last.is_deleted = false)
			                                  GROUP BY cppt_last.pendaftaran_id) cppt_max ON (((cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id) AND (cppt_t_1.cppt_id = cppt_max.cppt_id))))) cppt_t ON ((pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id)))
			                  WHERE (cppt_t.a_diag_utama IS NOT NULL)
			                UNION ALL
			                 SELECT pendaftaran_t_1.pendaftaran_id,
			                    resumemedisri_t.diag_utama AS diagnosa_utama
			                   FROM (((pendaftaran_t pendaftaran_t_1
			                     JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
			                     JOIN pasienadmisi_t pasienadmisi_t_1 ON ((pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id)))
			                     JOIN resumemedisri_t ON (((pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id) AND (pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id))))
			                  WHERE (resumemedisri_t.diag_utama IS NOT NULL)) diagnosa ON ((pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id)))
			          WHERE ((pendaftaran_t.instalasi_id = 5) AND (pendaftaran_t.is_aps IS FALSE))
			        UNION ALL
			         SELECT \'APS\'::text AS tipe_pasien,
			            pasienmasukpenunjang_t.pendaftaran_id,
			            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
			            pasienmasukpenunjang_t.pasienkirimkeunitlain_id,
			            pendaftaran_t.tgl_pendaftaran AS tglmasukpenunjang,
			            pendaftaran_t.no_pendaftaran,
			            pasienmasukpenunjang_t.no_masukpenunjang,
			            pasien_m.no_rekam_medik,
			            pasien_m.nama_pasien,
			            pegawai_m.pegawai_id,
			            pegawai_m.nama_pegawai AS dokter_penunjang,
			            NULL::character varying AS no_rujukan,
			            pasienmasukpenunjang_t.instalasiasal_id AS asalrujukan_id,
			            \'APS\'::character varying AS asalrujukan_nama,
			            pasienmasukpenunjang_t.ruanganasal_id,
			            ruangan_m.ruangan_nama,
			            COALESCE(pasienmasukpenunjang_t.status_periksa, \'477\'::character varying) AS status_periksa,
			            pasienmasukpenunjang_t.no_antrian,
			            pendaftaran_t.carabayar_id,
			            carabayar_m.carabayar_nama,
			            pendaftaran_t.penjamin_id,
			            penjamin_m.penjamin_nama,
			            pendaftaran_t.kelaspelayanan_id,
			            kelaspelayanan_m.kelaspelayanan_nama,
			            pendaftaran_t.umur,
			            pasien_m.jeniskelamin,
			            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS j_kelamin,
			            pasien_m.tanggal_lahir,
			            ((pendaftaran_t.label_gelang)::json ->> \'resiko_jatuh\'::text) AS kuning,
			            ((pendaftaran_t.label_gelang)::json ->> \'alergi\'::text) AS merah,
			            ((pendaftaran_t.label_gelang)::json ->> \'dnr\'::text) AS ungu,
			            ((pendaftaran_t.label_gelang)::json ->> \'duplikat\'::text) AS coklat,
			            pasienmasukpenunjang_t.tglmasukpenunjang AS tgl_rujukan,
			            pasienmasukpenunjang_t.pasien_id,
			            NULL::integer AS pasienadmisi_id,
			            pasienmasukpenunjang_t.ruangan_id,
			            pasienmasukpenunjang_t.is_bayar,
			            pasienkirimkeunitlain_t.status_penunjang,
			            COALESCE(pasienmasukpenunjang_t.catatan, pasienkirimkeunitlain_t.catatan_dokterpengirim) AS catatan_dokterpengirim,
			            pasien_m.no_telepon_pasien,
			            dokter_perujuk.pegawai_id AS dokter_perujuk_id,
			            dokter_perujuk.nama_pegawai AS dokter_perujuk_nama,
			            concat(COALESCE(fgetnamalookup((dokter_perujuk.gelardepan)::integer), \'\'::character varying), \' \', dokter_perujuk.nama_pegawai, \' \', COALESCE(gelarbelakang_m.gelarbelakang_nama, \'\'::character varying)) AS nama_pegawai,
			            concat(COALESCE(fgetnamalookup((pegawai_m.gelardepan)::integer), \'\'::character varying), \' \', pegawai_m.nama_pegawai, \' \', COALESCE(gelar_penunjang.gelarbelakang_nama, \'\'::character varying)) AS nama_dokter_penunjang,
			                CASE COALESCE(pasienmasukpenunjang_t.pasienkirimkeunitlain_id, 0)
			                    WHEN 0 THEN \'Pendaftaran\'::text
			                    ELSE \'Unit\'::text
			                END AS unit_asal,
			            diagnosa.diagnosa_utama AS nama_diagnosa,
			                CASE ruangan_pendaftaran.instalasi_id
			                    WHEN 21 THEN true
			                    ELSE false
			                END AS is_mcu,
			            pasienmasukpenunjang_t.created_by,
			            penjamin_m.penjamin_kode,
			            COALESCE(pasien_m.no_identitas_pasien, (pasien_m.additional_pasien)::character varying) AS no_identitas_pasien,
			            carabayar_m.groupcarabayar_id,
			            false AS sepesial_pemeriksaan,
			            NULL::integer AS perujuk_id,
			            rujukan_t.nama_perujuk AS perujuk_nama,
			            COALESCE(pasienmasukpenunjang_t.catatan, pasienkirimkeunitlain_t.catatan_dokterpengirim) AS catatan
			           FROM ((((((((((((((((pasienmasukpenunjang_t
			             JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
			             JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
			             LEFT JOIN pegawai_m ON ((pasienmasukpenunjang_t.pegawai_id = pegawai_m.pegawai_id)))
			             JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
			             JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
			             JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
			             LEFT JOIN pasienkirimkeunitlain_t ON ((pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id)))
			             JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id)))
			             JOIN instalasi_m ON ((pasienmasukpenunjang_t.instalasiasal_id = instalasi_m.instalasi_id)))
			             JOIN ruangan_m ruang_penunjang ON ((pasienmasukpenunjang_t.ruangan_id = ruang_penunjang.ruangan_id)))
			             LEFT JOIN pegawai_m dokter_perujuk ON ((pendaftaran_t.pegawai_id = dokter_perujuk.pegawai_id)))
			             LEFT JOIN gelarbelakang_m ON (((dokter_perujuk.gelarbelakang)::integer = gelarbelakang_m.gelarbelakang_id)))
			             LEFT JOIN gelarbelakang_m gelar_penunjang ON (((pegawai_m.gelarbelakang)::integer = gelar_penunjang.gelarbelakang_id)))
			             LEFT JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
			                        CASE
			                            WHEN (pasienmorbiditas_t.kelompokdiagnosa_id = 2) THEN pasienmorbiditas_t.diagnosa_pasien
			                            ELSE NULL::json
			                        END AS diagnosa_utama
			                   FROM ((pendaftaran_t pendaftaran_t_1
			                     JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
			                     JOIN pasienmorbiditas_t ON (((pendaftaran_t_1.pendaftaran_id = pasienmorbiditas_t.pendaftaran_id) AND (pasienmorbiditas_t.is_deleted = false))))
			                  WHERE ((pasienmorbiditas_t.kelompokdiagnosa_id = 2) AND (pasienmorbiditas_t.diagnosa_pasien IS NOT NULL))
			                UNION ALL
			                 SELECT pendaftaran_t_1.pendaftaran_id,
			                    cppt_t.a_diag_utama AS diagnosa_utama
			                   FROM ((pendaftaran_t pendaftaran_t_1
			                     JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
			                     JOIN ( SELECT cppt_t_1.cppt_id,
			                            cppt_t_1.pendaftaran_id,
			                            cppt_t_1.a_diag_utama,
			                            cppt_t_1.a_diag_penyerta
			                           FROM (cppt_t cppt_t_1
			                             JOIN ( SELECT max(cppt_last.cppt_id) AS cppt_id,
			                                    cppt_last.pendaftaran_id
			                                   FROM cppt_t cppt_last
			                                  WHERE (cppt_last.is_deleted = false)
			                                  GROUP BY cppt_last.pendaftaran_id) cppt_max ON (((cppt_t_1.pendaftaran_id = cppt_max.pendaftaran_id) AND (cppt_t_1.cppt_id = cppt_max.cppt_id))))) cppt_t ON ((pendaftaran_t_1.pendaftaran_id = cppt_t.pendaftaran_id)))
			                  WHERE (cppt_t.a_diag_utama IS NOT NULL)
			                UNION ALL
			                 SELECT pendaftaran_t_1.pendaftaran_id,
			                    resumemedisri_t.diag_utama AS diagnosa_utama
			                   FROM (((pendaftaran_t pendaftaran_t_1
			                     JOIN pasien_m pasien_m_1 ON ((pendaftaran_t_1.pasien_id = pasien_m_1.pasien_id)))
			                     JOIN pasienadmisi_t pasienadmisi_t_1 ON ((pendaftaran_t_1.pasienadmisi_id = pasienadmisi_t_1.pasienadmisi_id)))
			                     JOIN resumemedisri_t ON (((pendaftaran_t_1.pendaftaran_id = resumemedisri_t.pendaftaran_id) AND (pasienadmisi_t_1.pasienadmisi_id = resumemedisri_t.pasienadmisi_id))))
			                  WHERE (resumemedisri_t.diag_utama IS NOT NULL)) diagnosa ON ((pendaftaran_t.pendaftaran_id = diagnosa.pendaftaran_id)))
			             LEFT JOIN ruangan_m ruangan_pendaftaran ON ((pendaftaran_t.ruangan_id = ruangan_pendaftaran.ruangan_id)))
			             LEFT JOIN rujukan_t ON ((pendaftaran_t.rujukan_id = rujukan_t.rujukan_id)))
			          WHERE ((ruang_penunjang.instalasi_id = 5) AND (pendaftaran_t.is_aps = true) AND (pasienmasukpenunjang_t.is_bayar = true))) header
			     LEFT JOIN ( SELECT \'NON_PAKET\'::text AS jenis,
			            tindakanpelayanan_t.tindakanpelayanan_id,
			            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
			            tindakanpelayanan_t.tgl_tindakan,
			            jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
			            tindakanpelayanan_t.tipepaket_id,
			            \'\'::character varying AS tipepaket_nama,
			            NULL::text AS detail_2,
			            tindakanpelayanan_t.daftartindakan_id,
			            daftartindakan_m.daftartindakan_nama,
			            tindakanpelayanan_t.tarif_satuan,
			            tindakanpelayanan_t.cyto_tindakan,
			            tindakanpelayanan_t.tarifcyto_tindakan,
			            tindakanpelayanan_t.tarif_tindakan,
			            tindakanpelayanan_t.qty_tindakan,
			            COALESCE(pasienmasukpenunjang_t.status_periksa, \'477\'::character varying) AS status_periksa,
			            pasienmasukpenunjang_t.pendaftaran_id,
			            pasienmasukpenunjang_t.pasien_id,
			            pegawai_m.pegawai_id AS dokter_id_detail,
			            pegawai_m.nama_pegawai AS dokter_nama_detail,
			            pasienmasukpenunjang_t.created_by,
			            hasilpemeriksaanrad.hasilpemeriksaanrad_id,
			                CASE
			                    WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN false
			                    WHEN (tindakanpelayanan_t.is_deleted = true) THEN false
			                    ELSE true
			                END AS status_bayar,
			                CASE
			                    WHEN (hasilpemeriksaanrad.tindakanpelayanan_id IS NULL) THEN \'BELUM PERIKSA\'::text
			                    WHEN (hasilpemeriksaanrad.tindakanpelayanan_id IS NOT NULL) THEN \'SELESAI\'::text
			                    WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN \'BATAL\'::text
			                    ELSE NULL::text
			                END AS status_periksa_penunjang,
			                CASE
			                    WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN false
			                    WHEN (tindakanpelayanan_t.is_deleted = true) THEN true
			                    ELSE false
			                END AS status_batal
			           FROM (((((((pasienmasukpenunjang_t
			             JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
			             JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
			             LEFT JOIN permintaankepenunjang_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id)))
			             LEFT JOIN pemeriksaanrad_m ON ((permintaankepenunjang_t.pemeriksaanrad_id = pemeriksaanrad_m.pemeriksaanradiologi_id)))
			             LEFT JOIN jenispemeriksaanrad_m ON ((pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id)))
			             LEFT JOIN pegawai_m ON ((COALESCE((permintaankepenunjang_t.dokter_id)::bigint, tindakanpelayanan_t.dokterpenanggungjawab_id) = pegawai_m.pegawai_id)))
			             LEFT JOIN ( SELECT hasilpemeriksaanrad_t.hasilpemeriksaanrad_id,
			                    hasilpemeriksaanrad_t.pasienmasukpenunjang_id,
			                    hasilpemeriksaanrad_t.tindakanpelayanan_id
			                   FROM hasilpemeriksaanrad_t
			                  WHERE (hasilpemeriksaanrad_t.is_deleted IS FALSE)) hasilpemeriksaanrad ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanrad.pasienmasukpenunjang_id) AND (tindakanpelayanan_t.tindakanpelayanan_id = hasilpemeriksaanrad.tindakanpelayanan_id))))
			          WHERE (daftartindakan_m.kelompoktindakan_id = 10)
			        UNION ALL
			         SELECT \'PAKET\'::text AS jenis,
			            tindakanpelayanan_t.tindakanpelayanan_id,
			            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
			            tindakanpelayanan_t.tgl_tindakan,
			            jenispemeriksaanrad_m.jenispemeriksaanrad_nama,
			            tindakanpelayanan_t.tipepaket_id,
			            tipepaket_m.tipepaket_nama,
			            NULL::text AS detail_2,
			            paketpelayanan_mp.daftartindakan_id,
			            daftartindakan_m.daftartindakan_nama,
			            tindakanpelayanan_t.tarif_satuan,
			            tindakanpelayanan_t.cyto_tindakan,
			            tindakanpelayanan_t.tarifcyto_tindakan,
			            tindakanpelayanan_t.tarif_tindakan,
			            tindakanpelayanan_t.qty_tindakan,
			            COALESCE(pasienmasukpenunjang_t.status_periksa, \'477\'::character varying) AS status_periksa,
			            pasienmasukpenunjang_t.pendaftaran_id,
			            pasienmasukpenunjang_t.pasien_id,
			            pegawai_m.pegawai_id AS dokter_id_detail,
			            pegawai_m.nama_pegawai AS dokter_nama_detail,
			            pasienmasukpenunjang_t.created_by,
			            hasilpemeriksaanrad.hasilpemeriksaanrad_id,
			                CASE
			                    WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN false
			                    WHEN (tindakanpelayanan_t.is_deleted = true) THEN false
			                    ELSE true
			                END AS status_bayar,
			                CASE
			                    WHEN (hasilpemeriksaanrad.tindakanpelayanan_id IS NULL) THEN \'BELUM PERIKSA\'::text
			                    WHEN (hasilpemeriksaanrad.tindakanpelayanan_id IS NOT NULL) THEN \'SELESAI\'::text
			                    WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN \'BATAL\'::text
			                    ELSE NULL::text
			                END AS status_periksa_penunjang,
			                CASE
			                    WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN false
			                    WHEN (tindakanpelayanan_t.is_deleted = true) THEN true
			                    ELSE false
			                END AS status_batal
			           FROM (((((((((pasienmasukpenunjang_t
			             JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id)))
			             JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
			             JOIN paketpelayanan_mp ON ((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
			             JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
			             LEFT JOIN permintaankepenunjang_t ON ((tindakanpelayanan_t.tindakanpelayanan_id = permintaankepenunjang_t.tindakanpelayanan_id)))
			             LEFT JOIN pemeriksaanrad_m ON ((permintaankepenunjang_t.pemeriksaanrad_id = pemeriksaanrad_m.pemeriksaanradiologi_id)))
			             LEFT JOIN jenispemeriksaanrad_m ON ((pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id)))
			             LEFT JOIN pegawai_m ON ((COALESCE((permintaankepenunjang_t.dokter_id)::bigint, tindakanpelayanan_t.dokterpenanggungjawab_id) = pegawai_m.pegawai_id)))
			             LEFT JOIN ( SELECT hasilpemeriksaanrad_t.hasilpemeriksaanrad_id,
			                    hasilpemeriksaanrad_t.pasienmasukpenunjang_id,
			                    hasilpemeriksaanrad_t.tindakanpelayanan_id
			                   FROM hasilpemeriksaanrad_t
			                  WHERE (hasilpemeriksaanrad_t.is_deleted IS FALSE)) hasilpemeriksaanrad ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanrad.pasienmasukpenunjang_id) AND (tindakanpelayanan_t.tindakanpelayanan_id = hasilpemeriksaanrad.tindakanpelayanan_id))))
			          WHERE (daftartindakan_m.kelompoktindakan_id = 10)
			        UNION ALL
			         SELECT \'PAKET_MCU\'::text AS jenis,
			            tindakanpelayanan_t.tindakanpelayanan_id,
			            pasienmasukpenunjang_t.pasienmasukpenunjang_id,
			            tindakanpelayanan_t.tgl_tindakan,
			            detail_1.j_rad AS jenispemeriksaanrad_nama,
			            tindakanpelayanan_t.tipepaket_id,
			            tipepaket_m.tipepaket_nama,
			            detail_1.detail_2,
			            detail_1.detail_3id AS daftartindakan_id,
			            detail_1.detail_3 AS daftartindakan_nama,
			            tindakanpelayanan_t.tarif_satuan,
			            tindakanpelayanan_t.cyto_tindakan,
			            tindakanpelayanan_t.tarifcyto_tindakan,
			            tindakanpelayanan_t.tarif_tindakan,
			            tindakanpelayanan_t.qty_tindakan,
			            COALESCE(pasienmasukpenunjang_t.status_periksa, \'477\'::character varying) AS status_periksa,
			            pasienmasukpenunjang_t.pendaftaran_id,
			            pasienmasukpenunjang_t.pasien_id,
			            pegawai_m.pegawai_id AS dokter_id_detail,
			            pegawai_m.nama_pegawai AS dokter_nama_detail,
			            pasienmasukpenunjang_t.created_by,
			            hasilpemeriksaanrad.hasilpemeriksaanrad_id,
			                CASE
			                    WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN false
			                    WHEN (tindakanpelayanan_t.is_deleted = true) THEN false
			                    ELSE true
			                END AS status_bayar,
			                CASE
			                    WHEN (hasilpemeriksaanrad.tindakanpelayanan_id IS NULL) THEN \'BELUM PERIKSA\'::text
			                    WHEN (hasilpemeriksaanrad.tindakanpelayanan_id IS NOT NULL) THEN \'SELESAI\'::text
			                    WHEN ((tindakanpelayanan_t.is_deleted = true) AND (tindakanpelayanan_t.tindakansudahbayar_id IS NULL)) THEN \'BATAL\'::text
			                    ELSE NULL::text
			                END AS status_periksa_penunjang,
			                CASE
			                    WHEN ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (tindakanpelayanan_t.is_deleted = false)) THEN false
			                    WHEN (tindakanpelayanan_t.is_deleted = true) THEN true
			                    ELSE false
			                END AS status_batal
			           FROM ((((((pasienmasukpenunjang_t
			             JOIN tindakanpelayanan_t ON ((pasienmasukpenunjang_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
			             JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
			             JOIN ( SELECT paketpelayanan_mp.tipepaket_id AS detail_1id,
			                    paketpelayanan_mp.paketdetail_id AS detail_2id,
			                    paket_detail.tipepaket_nama AS detail_2,
			                    paket_detail.daftartindakan_id AS detail_3id,
			                    paket_detail.daftartindakan_nama AS detail_3,
			                    paket_detail.p_rad,
			                    paket_detail.j_rad
			                   FROM ((tipepaket_m tipepaket_m_1
			                     JOIN paketpelayanan_mp ON (((tipepaket_m_1.tipepaket_id = paketpelayanan_mp.tipepaket_id) AND (paketpelayanan_mp.is_deleted = false))))
			                     JOIN ( SELECT a.tipepaket_id,
			                            a.tipepaket_nama,
			                            daftartindakan_m.daftartindakan_id,
			                            daftartindakan_m.daftartindakan_nama,
			                            pemeriksaanrad_m.pemeriksaanrad_nama AS p_rad,
			                            jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS j_rad
			                           FROM (((((tipepaket_m a
			                             JOIN paketpelayanan_mp paketpelayanan_mp_1 ON ((a.tipepaket_id = paketpelayanan_mp_1.tipepaket_id)))
			                             JOIN ruangan_m ruangan_m_1 ON (((paketpelayanan_mp_1.ruangan_id = ruangan_m_1.ruangan_id) AND (ruangan_m_1.instalasi_id = 5))))
			                             JOIN daftartindakan_m ON ((paketpelayanan_mp_1.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
			                             LEFT JOIN pemeriksaanrad_m ON ((daftartindakan_m.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id)))
			                             LEFT JOIN jenispemeriksaanrad_m ON ((pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id)))) paket_detail ON ((paketpelayanan_mp.paketdetail_id = paket_detail.tipepaket_id)))
			                  WHERE (tipepaket_m_1.is_deleted = false)
			                UNION ALL
			                 SELECT paketpelayanan_mp.tipepaket_id AS detail_1id,
			                    NULL::integer AS detail_2id,
			                    NULL::character varying AS detail_2,
			                    paketpelayanan_mp.daftartindakan_id AS detail_3id,
			                    tindakan_detail.daftartindakan_nama AS detail_3,
			                    pemeriksaanrad_m.pemeriksaanrad_nama AS p_rad,
			                    jenispemeriksaanrad_m.jenispemeriksaanrad_nama AS j_rad
			                   FROM (((((tipepaket_m tipepaket_m_1
			                     JOIN paketpelayanan_mp ON (((tipepaket_m_1.tipepaket_id = paketpelayanan_mp.tipepaket_id) AND (paketpelayanan_mp.is_deleted = false))))
			                     JOIN ruangan_m ruangan_m_1 ON (((paketpelayanan_mp.ruangan_id = ruangan_m_1.ruangan_id) AND (ruangan_m_1.instalasi_id = 5))))
			                     JOIN daftartindakan_m tindakan_detail ON ((paketpelayanan_mp.daftartindakan_id = tindakan_detail.daftartindakan_id)))
			                     LEFT JOIN pemeriksaanrad_m ON ((tindakan_detail.daftartindakan_id = pemeriksaanrad_m.daftartindakan_id)))
			                     LEFT JOIN jenispemeriksaanrad_m ON ((pemeriksaanrad_m.jenispemeriksaanrad_id = jenispemeriksaanrad_m.jenispemeriksaanrad_id)))
			                  WHERE (tipepaket_m_1.is_deleted = false)) detail_1 ON ((tindakanpelayanan_t.tipepaket_id = detail_1.detail_1id)))
			             JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id)))
			             LEFT JOIN pegawai_m ON ((tindakanpelayanan_t.dokterpenanggungjawab_id = pegawai_m.pegawai_id)))
			             LEFT JOIN ( SELECT hasilpemeriksaanrad_t.hasilpemeriksaanrad_id,
			                    hasilpemeriksaanrad_t.pasienmasukpenunjang_id,
			                    hasilpemeriksaanrad_t.tindakanpelayanan_id
			                   FROM hasilpemeriksaanrad_t
			                  WHERE (hasilpemeriksaanrad_t.is_deleted IS FALSE)) hasilpemeriksaanrad ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = hasilpemeriksaanrad.pasienmasukpenunjang_id) AND (tindakanpelayanan_t.tindakanpelayanan_id = hasilpemeriksaanrad.tindakanpelayanan_id))))) detail ON ((header.pasienmasukpenunjang_id = detail.pasienmasukpenunjang_id)))
			     LEFT JOIN ( SELECT count(*) AS is_hasil,
			            hasilpemeriksaanrad_t.pasienmasukpenunjang_id,
			            hasilpemeriksaanrad_t.tindakanpelayanan_id,
			            pemeriksaanrad_m.daftartindakan_id,
			            hasilpemeriksaanrad_t.tgl_verifikasi
			           FROM (hasilpemeriksaanrad_t
			             JOIN pemeriksaanrad_m ON ((pemeriksaanrad_m.pemeriksaanradiologi_id = hasilpemeriksaanrad_t.pemeriksaanrad_id)))
			          WHERE (hasilpemeriksaanrad_t.is_deleted = false)
			          GROUP BY hasilpemeriksaanrad_t.pasienmasukpenunjang_id, hasilpemeriksaanrad_t.tindakanpelayanan_id, pemeriksaanrad_m.daftartindakan_id, hasilpemeriksaanrad_t.tgl_verifikasi) hasil ON (((header.pasienmasukpenunjang_id = hasil.pasienmasukpenunjang_id) AND (detail.tindakanpelayanan_id = hasil.tindakanpelayanan_id) AND (detail.daftartindakan_id = hasil.daftartindakan_id))));    
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."infotagihanpasienpulang_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infotagihanpasienpulang_v" AS  SELECT gabung.pendaftaran_id,
			    gabung.pasienpulang_id,
			    gabung.pasienpulangri_id,
			        CASE 
			            WHEN ((gabung.pasienpulangri_id IS NULL) AND (gabung.pasienpulang_id IS NULL)) THEN COALESCE(gabung.tgl_stopakomodasi, gabung.tgl_pendaftaran)
			            ELSE COALESCE(gabung.tglpasienpulang, gabung.tgl_pendaftaran)
			        END AS tglpasienpulang,
			    gabung.no_pendaftaran,
			    gabung.instalasi_id,
			    gabung.instalasi_nama,
			    gabung.ruanganakhir_id AS ruangan_id,
			    gabung.ruangan_nama,
			    gabung.no_rekam_medik,
			    gabung.nama_pasien,
			    gabung.carabayar_id,
			    gabung.carabayar_nama,
			    gabung.penjamin_id,
			    gabung.penjamin_nama,
			    gabung.jeniskasuspenyakit_nama,
			    gabung.status_bayar,
			    gabung.kelaspelayanan_nama,
			    gabung.nama_pegawai AS dokter,
			    COALESCE((sum((gabung.total_tindakan)::integer))::double precision, (0)::double precision) AS total_tindakan,
			    COALESCE((sum((gabung.total_obat)::integer))::double precision, (0)::double precision) AS total_obat,
			    ((COALESCE((sum((gabung.total_tindakan)::integer))::double precision, (0)::double precision) + COALESCE((sum((gabung.total_obat)::integer))::double precision, (0)::double precision)))::integer AS total_tagihan,
			    gabung.pegawai_id,
			    gabung.photopasien,
			    gabung.tanggal_lahir,
			    gabung.umur,
			    gabung.jeniskelamin,
			    gabung.jenis_kelamin,
			    gabung.tgl_pendaftaran,
			    gabung.is_stopakomodasi,
			    gabung.tgl_stopakomodasi
			   FROM ( SELECT pendaftaran_t.pendaftaran_id,
			            pendaftaran_t.pasienpulang_id,
			            NULL::integer AS pasienpulangri_id,
			            pasienpulang_t.tglpasienpulang,
			            pendaftaran_t.no_pendaftaran,
			            ruangan_m.instalasi_id,
			            instalasi_m.instalasi_nama,
			            pasienpulang_t.ruanganakhir_id,
			            ruangan_m.ruangan_nama,
			            pasien_m.no_rekam_medik,
			            pasien_m.nama_pasien,
			            pendaftaran_t.carabayar_id,
			            carabayar_m.carabayar_nama,
			            pendaftaran_t.penjamin_id,
			            penjamin_m.penjamin_nama,
			            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
			            fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
			            kelaspelayanan_m.kelaspelayanan_nama,
			            pegawai_m.nama_pegawai,
			            sum(tindakanpelayanan_t.tarif_tindakan) AS total_tindakan,
			            NULL::double precision AS total_obat,
			            pendaftaran_t.pegawai_id,
			            pasien_m.photopasien,
			            pasien_m.tanggal_lahir,
			            pendaftaran_t.umur,
			            pasien_m.jeniskelamin,
			            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
			            pendaftaran_t.tgl_pendaftaran,
			            tindakanpelayanan_t.tindakansudahbayar_id AS sudah_bayar,
			            pendaftaran_t.is_stopakomodasi,
			            pendaftaran_t.tgl_stopakomodasi
			           FROM ((((((((((pendaftaran_t
			             LEFT JOIN tindakanpelayanan_t ON (((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id) AND (tindakanpelayanan_t.is_deleted = false))))
			             LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
			             LEFT JOIN ruangan_m ON ((pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id)))
			             LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
			             JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
			             JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
			             JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
			             JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
			             JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
			             JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
			          WHERE (((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (pendaftaran_t.instalasi_id = 1)) OR (pendaftaran_t.instalasi_id = 2))
			          GROUP BY kelaspelayanan_m.kelaspelayanan_nama, pegawai_m.nama_pegawai, pendaftaran_t.status_bayar, pendaftaran_t.pendaftaran_id, pasienpulang_t.tglpasienpulang, pendaftaran_t.no_pendaftaran, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, pasienpulang_t.ruanganakhir_id, ruangan_m.ruangan_nama, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pendaftaran_t.carabayar_id, carabayar_m.carabayar_nama, pendaftaran_t.penjamin_id, penjamin_m.penjamin_nama, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pendaftaran_t.pasienpulang_id, pendaftaran_t.pegawai_id, pasien_m.photopasien, pasien_m.tanggal_lahir, pendaftaran_t.umur, pasien_m.jeniskelamin, (fgetnamalookup((pasien_m.jeniskelamin)::integer)), pendaftaran_t.tgl_pendaftaran, tindakanpelayanan_t.tindakansudahbayar_id, pendaftaran_t.is_stopakomodasi, pendaftaran_t.tgl_stopakomodasi
			        UNION ALL
			         SELECT pendaftaran_t.pendaftaran_id,
			            NULL::integer AS pasienpulang_id,
			            pasienadmisi_t.pasienpulang_id AS pasienpulangri_id,
			            pasienpulang_t.tglpasienpulang,
			            pendaftaran_t.no_pendaftaran,
			            ruangan_m.instalasi_id,
			            instalasi_m.instalasi_nama,
			            pasienadmisi_t.ruangan_id AS ruanganakhir_id,
			            ruangan_m.ruangan_nama,
			            pasien_m.no_rekam_medik,
			            pasien_m.nama_pasien,
			            pendaftaran_t.carabayar_id,
			            carabayar_m.carabayar_nama,
			            pendaftaran_t.penjamin_id,
			            penjamin_m.penjamin_nama,
			            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
			            fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
			            kelaspelayanan_m.kelaspelayanan_nama,
			            pegawai_m.nama_pegawai,
			            sum(tindakanpelayanan_t.tarif_tindakan) AS total_tindakan,
			            NULL::double precision AS total_obat,
			            pendaftaran_t.pegawai_id,
			            pasien_m.photopasien,
			            pasien_m.tanggal_lahir,
			            pendaftaran_t.umur,
			            pasien_m.jeniskelamin,
			            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
			            pendaftaran_t.tgl_pendaftaran,
			            tindakanpelayanan_t.tindakansudahbayar_id,
			            pendaftaran_t.is_stopakomodasi,
			            pendaftaran_t.tgl_stopakomodasi
			           FROM (((((((((((pendaftaran_t
			             LEFT JOIN tindakanpelayanan_t ON (((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id) AND (tindakanpelayanan_t.is_deleted = false))))
			             LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
			             LEFT JOIN pasienpulang_t ON (((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id) AND (pasienpulang_t.carakeluar_id <> 5))))
			             LEFT JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
			             LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
			             JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
			             JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
			             JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
			             JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
			             JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
			             JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
			          WHERE ((tindakanpelayanan_t.tindakansudahbayar_id IS NULL) AND (pendaftaran_t.is_stopakomodasi = true))
			          GROUP BY kelaspelayanan_m.kelaspelayanan_nama, pegawai_m.nama_pegawai, pendaftaran_t.status_bayar, pendaftaran_t.pendaftaran_id, pasienpulang_t.tglpasienpulang, pendaftaran_t.no_pendaftaran, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, ruangan_m.ruangan_nama, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pendaftaran_t.carabayar_id, carabayar_m.carabayar_nama, pendaftaran_t.penjamin_id, penjamin_m.penjamin_nama, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pendaftaran_t.pasienpulang_id, pendaftaran_t.pegawai_id, pasienadmisi_t.pasienpulang_id, pasien_m.photopasien, pasien_m.tanggal_lahir, pendaftaran_t.umur, pasien_m.jeniskelamin, (fgetnamalookup((pasien_m.jeniskelamin)::integer)), pendaftaran_t.tgl_pendaftaran, tindakanpelayanan_t.tindakansudahbayar_id, pendaftaran_t.is_stopakomodasi, pendaftaran_t.tgl_stopakomodasi, pasienadmisi_t.ruangan_id
			        UNION ALL
			         SELECT pendaftaran_t.pendaftaran_id,
			            pendaftaran_t.pasienpulang_id,
			            NULL::integer AS pasienpulangri_id,
			            pasienpulang_t.tglpasienpulang,
			            pendaftaran_t.no_pendaftaran,
			            ruangan_m.instalasi_id,
			            instalasi_m.instalasi_nama,
			            pasienpulang_t.ruanganakhir_id,
			            ruangan_m.ruangan_nama,
			            pasien_m.no_rekam_medik,
			            pasien_m.nama_pasien,
			            pendaftaran_t.carabayar_id,
			            carabayar_m.carabayar_nama,
			            pendaftaran_t.penjamin_id,
			            penjamin_m.penjamin_nama,
			            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
			            fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
			            kelaspelayanan_m.kelaspelayanan_nama,
			            pegawai_m.nama_pegawai,
			            NULL::double precision AS total_tindakan,
			            sum(obatalkespasien_t.hargajual_oa) AS total_obat,
			            pendaftaran_t.pegawai_id,
			            pasien_m.photopasien,
			            pasien_m.tanggal_lahir,
			            pendaftaran_t.umur,
			            pasien_m.jeniskelamin,
			            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
			            pendaftaran_t.tgl_pendaftaran,
			            obatalkespasien_t.obatsudahbayar_id,
			            pendaftaran_t.is_stopakomodasi,
			            pendaftaran_t.tgl_stopakomodasi
			           FROM ((((((((((pendaftaran_t
			             LEFT JOIN obatalkespasien_t ON (((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id) AND (obatalkespasien_t.is_deleted = false))))
			             LEFT JOIN pasienpulang_t ON ((pendaftaran_t.pasienpulang_id = pasienpulang_t.pasienpulang_id)))
			             LEFT JOIN ruangan_m ON ((pasienpulang_t.ruanganakhir_id = ruangan_m.ruangan_id)))
			             LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
			             JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
			             JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
			             JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
			             JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
			             JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
			             JOIN pegawai_m ON ((pendaftaran_t.pegawai_id = pegawai_m.pegawai_id)))
			          WHERE (((obatalkespasien_t.obatsudahbayar_id IS NULL) AND (pendaftaran_t.instalasi_id = 1)) OR ((pendaftaran_t.instalasi_id = 2) AND (pasienpulang_t.carakeluar_id <> 5)))
			          GROUP BY kelaspelayanan_m.kelaspelayanan_nama, pegawai_m.nama_pegawai, pendaftaran_t.status_bayar, pendaftaran_t.pendaftaran_id, pasienpulang_t.tglpasienpulang, pendaftaran_t.no_pendaftaran, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, pasienpulang_t.ruanganakhir_id, ruangan_m.ruangan_nama, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pendaftaran_t.carabayar_id, carabayar_m.carabayar_nama, pendaftaran_t.penjamin_id, penjamin_m.penjamin_nama, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pendaftaran_t.pasienpulang_id, pendaftaran_t.pegawai_id, pasien_m.photopasien, pasien_m.tanggal_lahir, pendaftaran_t.umur, pasien_m.jeniskelamin, (fgetnamalookup((pasien_m.jeniskelamin)::integer)), pendaftaran_t.tgl_pendaftaran, obatalkespasien_t.obatsudahbayar_id, pendaftaran_t.is_stopakomodasi, pendaftaran_t.tgl_stopakomodasi
			        UNION ALL
			         SELECT pendaftaran_t.pendaftaran_id,
			            NULL::integer AS pasienpulang_id,
			            pasienadmisi_t.pasienpulang_id AS pasienpulangri_id,
			            pasienpulang_t.tglpasienpulang,
			            pendaftaran_t.no_pendaftaran,
			            ruangan_m.instalasi_id,
			            instalasi_m.instalasi_nama,
			            pasienadmisi_t.ruangan_id AS ruanganakhir_id,
			            ruangan_m.ruangan_nama,
			            pasien_m.no_rekam_medik,
			            pasien_m.nama_pasien,
			            pendaftaran_t.carabayar_id,
			            carabayar_m.carabayar_nama,
			            pendaftaran_t.penjamin_id,
			            penjamin_m.penjamin_nama,
			            jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
			            fgetnamalookup(pendaftaran_t.status_bayar) AS status_bayar,
			            kelaspelayanan_m.kelaspelayanan_nama,
			            pegawai_m.nama_pegawai,
			            NULL::double precision AS total_tindakan,
			            sum(obatalkespasien_t.hargajual_oa) AS total_obat,
			            pendaftaran_t.pegawai_id,
			            pasien_m.photopasien,
			            pasien_m.tanggal_lahir,
			            pendaftaran_t.umur,
			            pasien_m.jeniskelamin,
			            fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
			            pendaftaran_t.tgl_pendaftaran,
			            obatalkespasien_t.obatsudahbayar_id,
			            pendaftaran_t.is_stopakomodasi,
			            pendaftaran_t.tgl_stopakomodasi
			           FROM (((((((((((pendaftaran_t
			             LEFT JOIN obatalkespasien_t ON (((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id) AND (obatalkespasien_t.is_deleted = false))))
			             LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
			             LEFT JOIN pasienpulang_t ON (((pasienadmisi_t.pasienpulang_id = pasienpulang_t.pasienpulang_id) AND (pasienpulang_t.carakeluar_id <> 5))))
			             LEFT JOIN ruangan_m ON ((pasienadmisi_t.ruangan_id = ruangan_m.ruangan_id)))
			             LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
			             JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
			             JOIN carabayar_m ON ((pasienadmisi_t.carabayar_id = carabayar_m.carabayar_id)))
			             JOIN penjamin_m ON ((pasienadmisi_t.penjamin_id = penjamin_m.penjamin_id)))
			             JOIN jeniskasuspenyakit_m ON ((pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id)))
			             JOIN kelaspelayanan_m ON ((pendaftaran_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id)))
			             JOIN pegawai_m ON ((pasienadmisi_t.pegawai_id = pegawai_m.pegawai_id)))
			          WHERE ((obatalkespasien_t.obatsudahbayar_id IS NULL) AND (pendaftaran_t.is_stopakomodasi = true))
			          GROUP BY kelaspelayanan_m.kelaspelayanan_nama, pegawai_m.nama_pegawai, pendaftaran_t.status_bayar, pendaftaran_t.pendaftaran_id, pasienpulang_t.tglpasienpulang, pendaftaran_t.no_pendaftaran, ruangan_m.instalasi_id, instalasi_m.instalasi_nama, ruangan_m.ruangan_nama, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pendaftaran_t.carabayar_id, carabayar_m.carabayar_nama, pendaftaran_t.penjamin_id, penjamin_m.penjamin_nama, jeniskasuspenyakit_m.jeniskasuspenyakit_nama, pendaftaran_t.pasienpulang_id, pendaftaran_t.pegawai_id, pasienadmisi_t.pasienpulang_id, pasien_m.photopasien, pasien_m.tanggal_lahir, pendaftaran_t.umur, pasien_m.jeniskelamin, (fgetnamalookup((pasien_m.jeniskelamin)::integer)), pendaftaran_t.tgl_pendaftaran, obatalkespasien_t.obatsudahbayar_id, pendaftaran_t.is_stopakomodasi, pendaftaran_t.tgl_stopakomodasi, pasienadmisi_t.ruangan_id) gabung
			  WHERE (gabung.sudah_bayar IS NULL)
			  GROUP BY gabung.kelaspelayanan_nama, gabung.nama_pegawai, gabung.status_bayar, gabung.pendaftaran_id, gabung.pasienpulang_id, gabung.tglpasienpulang, gabung.no_pendaftaran, gabung.instalasi_id, gabung.instalasi_nama, gabung.ruanganakhir_id, gabung.ruangan_nama, gabung.no_rekam_medik, gabung.nama_pasien, gabung.carabayar_id, gabung.carabayar_nama, gabung.penjamin_id, gabung.penjamin_nama, gabung.jeniskasuspenyakit_nama, gabung.pegawai_id, gabung.pasienpulangri_id, gabung.photopasien, gabung.tanggal_lahir, gabung.umur, gabung.jeniskelamin, gabung.jenis_kelamin, gabung.tgl_pendaftaran, gabung.is_stopakomodasi, gabung.tgl_stopakomodasi;
        ');

        $this->execute('
            DROP VIEW IF EXISTS "public"."infopasienbelumbayar_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infopasienbelumbayar_v" AS  SELECT pendaftaran_t.pendaftaran_id,
			    pendaftaran_t.pasienadmisi_id,
			    pendaftaran_t.no_pendaftaran,
			    pendaftaran_t.tgl_pendaftaran,
			    pasien_m.nama_pasien,
			    pasien_m.no_rekam_medik,
			    pasien_m.tanggal_lahir,
			    fgetnamalookup((pasien_m.jeniskelamin)::integer) AS jenis_kelamin,
			        CASE
			            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN cb_1.carabayar_nama
			            ELSE cb_2.carabayar_nama
			        END AS carabayar_nama,
			        CASE 
			            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pj_1.penjamin_nama
			            ELSE pj_2.penjamin_nama
			        END AS penjamin_nama,
			        CASE
			            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN dr_1.nama_pegawai
			            ELSE dr_2.nama_pegawai
			        END AS nama_dokter,
			        CASE
			            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ins_1.instalasi_nama
			            ELSE ins_1.instalasi_nama
			        END AS instalasi_nama,
			        CASE
			            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruang_1.ruangan_nama
			            ELSE ruang_2.ruangan_nama
			        END AS ruangan_nama,
			        CASE
			            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN fgetnamalookup((pendaftaran_t.status_periksa)::integer)
			            ELSE fgetnamalookup(pasienadmisi_t.status_ranap)
			        END AS status_periksa,
			    (COALESCE((tindakan.total_tindakan)::double precision, (0)::double precision) + COALESCE((obat.total_obat)::double precision, (0)::double precision)) AS total_tagihan,
			    COALESCE(uang_masuk.total_uangmasuk, (0)::double precision) AS uang_masuk,
			    ((COALESCE((tindakan.total_tindakan)::double precision, (0)::double precision) + COALESCE((obat.total_obat)::double precision, (0)::double precision)) - COALESCE(uang_masuk.total_uangmasuk, (0)::double precision)) AS sisa_tagihan,
			    konfigsystem_k.kelola_tagihan,
			        CASE
			            WHEN ((COALESCE((tindakan.total_tindakan)::double precision, (0)::double precision) + COALESCE((obat.total_obat)::double precision, (0)::double precision)) >= (konfigsystem_k.kelola_tagihan)::double precision) THEN true
			            ELSE false
			        END AS is_kelola_tagihan,
			        CASE
			            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruang_1.instalasi_id
			            ELSE ruang_2.instalasi_id
			        END AS instalasi_id,
			        CASE
			            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN ruang_1.ruangan_id
			            ELSE ruang_2.ruangan_id
			        END AS ruangan_id,
			    COALESCE(pendaftaran_t.limit_tagihan, (0)::double precision) AS limit_tagihan,
			        CASE
			            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN bpjs_t.nosep
			            ELSE bpjs_admisi.nosep
			        END AS no_sep,
			        CASE
			            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN pendaftaran_t.pasienpulang_id
			            ELSE pasienadmisi_t.pasienpulang_id
			        END AS pasienpulang_id,
			    pendaftaran_t.is_stopakomodasi,
			    uang_muka.uang_muka
			   FROM ((((((((((((((((((((pendaftaran_t
			     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
			     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
			     LEFT JOIN carabayar_m cb_1 ON ((pendaftaran_t.carabayar_id = cb_1.carabayar_id)))
			     LEFT JOIN carabayar_m cb_2 ON ((pasienadmisi_t.carabayar_id = cb_2.carabayar_id)))
			     LEFT JOIN penjamin_m pj_1 ON ((pendaftaran_t.penjamin_id = pj_1.penjamin_id)))
			     LEFT JOIN penjamin_m pj_2 ON ((pasienadmisi_t.penjamin_id = pj_2.penjamin_id)))
			     LEFT JOIN pegawai_m dr_1 ON ((pendaftaran_t.pegawai_id = dr_1.pegawai_id)))
			     LEFT JOIN pegawai_m dr_2 ON ((pasienadmisi_t.pegawai_id = dr_2.pegawai_id)))
			     LEFT JOIN ruangan_m ruang_1 ON ((pendaftaran_t.ruangan_id = ruang_1.ruangan_id)))
			     LEFT JOIN ruangan_m ruang_2 ON ((pasienadmisi_t.ruangan_id = ruang_2.ruangan_id)))
			     LEFT JOIN instalasi_m ins_1 ON ((ruang_1.instalasi_id = ins_1.instalasi_id)))
			     LEFT JOIN instalasi_m ins_2 ON ((ruang_2.instalasi_id = dr_2.pegawai_id)))
			     LEFT JOIN ( SELECT tindakanpelayanan_t.pendaftaran_id,
			            sum((tindakanpelayanan_t.tarif_tindakan)::integer) AS total_tindakan
			           FROM tindakanpelayanan_t
			          WHERE (tindakanpelayanan_t.is_deleted IS FALSE)
			          GROUP BY tindakanpelayanan_t.pendaftaran_id) tindakan ON ((pendaftaran_t.pendaftaran_id = tindakan.pendaftaran_id)))
			     LEFT JOIN ( SELECT obatalkespasien_t.pendaftaran_id,
			            sum((obatalkespasien_t.hargajual_oa)::integer) AS total_obat
			           FROM obatalkespasien_t
			          WHERE (obatalkespasien_t.is_deleted = false)
			          GROUP BY obatalkespasien_t.pendaftaran_id) obat ON ((pendaftaran_t.pendaftaran_id = obat.pendaftaran_id)))
			     LEFT JOIN ( SELECT pembayaran_t.pendaftaran_id,
			            sum(((((pembayaran_t.total_dibayar - pembayaran_t.total_kembalian) + pembayaran_t.total_dijamin) - pembayaran_t.total_administrasi) - pembayaran_t.total_pembulatan)) AS total_uangmasuk
			           FROM (pembayaranpelayanan_t pembayaranpelayanan_t_1
			             JOIN pembayaran_t ON ((pembayaranpelayanan_t_1.pembayaran_id = pembayaran_t.pembayaran_id)))
			          WHERE ((pembayaranpelayanan_t_1.is_deleted = false) AND (pembayaran_t.is_deleted = false))
			          GROUP BY pembayaran_t.pendaftaran_id) uang_masuk ON ((pendaftaran_t.pendaftaran_id = uang_masuk.pendaftaran_id)))
			     LEFT JOIN konfigsystem_k ON ((konfigsystem_k.is_deleted = false)))
			     LEFT JOIN pembayaranpelayanan_t ON (((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id) AND (pembayaranpelayanan_t.is_deleted = false))))
			     LEFT JOIN bpjs_t ON ((pendaftaran_t.bpjs_id = bpjs_t.bpjs_id)))
			     LEFT JOIN bpjs_t bpjs_admisi ON ((pasienadmisi_t.bpjs_id = bpjs_admisi.bpjs_id)))
			     LEFT JOIN ( SELECT bayaruangmuka_t.pendaftaran_id,
			            sum(bayaruangmuka_t.jumlah_uangmuka) AS uang_muka
			           FROM bayaruangmuka_t
			          WHERE (bayaruangmuka_t.is_deleted = false)
			          GROUP BY bayaruangmuka_t.pendaftaran_id) uang_muka ON ((pendaftaran_t.pendaftaran_id = uang_muka.pendaftaran_id)))
			  WHERE ((pendaftaran_t.status_bayar = 349) AND ((pendaftaran_t.status_periksa)::integer <> 628));
        ');

		$this->execute('
            DROP VIEW IF EXISTS "public"."soaprj_v";
        ');

        $this->execute('
            CREATE VIEW "public"."soaprj_v" AS  SELECT t1.jenis,
			    t1.grouping_tipe,
			    t1.jenis_urutan,
			    t1.pendaftaran_id,
			    t1.ruangan_id,
			    t1.ruangan_nama,
			    t1.pegawai_id,
			    t1.nama_pegawai,
			    t1.kelompokpegawai_nama,
			    t1.nama_profesi,
			    t1.subject,
			    t1.object,
			    t1.a_diag_utama,
			    t1.a_diag_penyerta,
			    t1.tgl_tindakan,
			    t1.instruksi,
			    t1.soaprj_id,
			    t1.tgl_soaprj,
			    t1.planning,
			    t1.catatan_dokter,
			    t1.daftar_paket,
			    t1.catatan_dokterpengirim,
			    t1.is_hapus,
			    t1.status,
			    t1.cyto_tindakan,
			    t1.qty,
			    t1.no_penunjang,
			    t1.verbal_instruksi,
			    t1.pemberi_instruksi_id,
			    t1.is_verifikasi_verbal,
			    t1.tgl_verif_verbal,
			    t1.pegawai_verbal_id,
			    t1.pasien_id,
			    t1.ruangan_penunjang_id,
			    t1.ruangan_penunjang_nama,
			    t1.instalasi_penunjang_id,
			    t1.instalasi_penunjang_nama,
			    t1.kelompoktindakan_id,
			    t1.pegawai_soap,
			    t1.kelompokpegawai_soap,
			    t1.is_bayar,
			    t1.permintaankepenunjang_id,
			    t1.obatalkespasien_id, 
			    t1.tindakanpelayanan_id,
			    t1.pasienkirimkeunitlain_id
			   FROM ( SELECT \'TINDAKAN\'::text AS jenis,
			            \'Tindakan & BMHP\'::text AS grouping_tipe,
			            2 AS jenis_urutan,
			            pendaftaran_t.pendaftaran_id,
			            ruangan_m.ruangan_id,
			            ruangan_m.ruangan_nama,
			            soaprj_t.pegawai_id,
			            pegawai_m.nama_pegawai,
			            kelompokpegawai_m.kelompokpegawai_nama,
			            pendidikankualifikasi_m.pendkualifikasi_nama AS nama_profesi,
			            soaprj_t.subject,
			            soaprj_t.object,
			            soaprj_t.a_diag_utama,
			            soaprj_t.a_diag_penyerta,
			            tindakanpelayanan_t.tgl_tindakan,
			            daftartindakan_m.daftartindakan_nama AS instruksi,
			            soaprj_t.soaprj_id,
			            soaprj_t.tgl_soaprj,
			            soaprj_t.planning,
			            soaprj_t.catatan_dokter,
			            array_to_json(NULL::character varying[]) AS daftar_paket,
			            \'-\'::text AS catatan_dokterpengirim,
			            tindakanpelayanan_t.is_deleted AS is_hapus,
			            NULL::character varying AS status,
			            tindakanpelayanan_t.cyto_tindakan,
			            tindakanpelayanan_t.qty_tindakan AS qty,
			            NULL::character varying AS no_penunjang,
			            soaprj_t.instruksi AS verbal_instruksi,
			            soaprj_t.pemberi_instruksi_id,
			            soaprj_t.is_verifikasi_verbal,
			            soaprj_t.tgl_verif_verbal,
			            soaprj_t.pegawai_verbal_id,
			            pendaftaran_t.pasien_id,
			            ruangan_m.ruangan_id AS ruangan_penunjang_id,
			            ruangan_m.ruangan_nama AS ruangan_penunjang_nama,
			            instalasi_m.instalasi_id AS instalasi_penunjang_id,
			            instalasi_m.instalasi_nama AS instalasi_penunjang_nama,
			            daftartindakan_m.kelompoktindakan_id,
			            COALESCE(peg_soap.nama_pegawai, pegawai_m.nama_pegawai) AS pegawai_soap,
			            COALESCE(kelpeg_soap.kelompokpegawai_nama, kelompokpegawai_m.kelompokpegawai_nama) AS kelompokpegawai_soap,
			                CASE COALESCE(tindakanpelayanan_t.tindakansudahbayar_id, 0)
			                    WHEN 0 THEN false
			                    ELSE true
			                END AS is_bayar,
			            NULL::integer AS permintaankepenunjang_id,
			            NULL::integer AS obatalkespasien_id,
			            tindakanpelayanan_t.tindakanpelayanan_id,
			            NULL::integer AS pasienkirimkeunitlain_id
			           FROM ((((((((((pendaftaran_t
			             LEFT JOIN soaprj_t ON ((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id)))
			             LEFT JOIN ruangan_m ON ((COALESCE(soaprj_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id)))
			             LEFT JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
			             LEFT JOIN pegawai_m ON ((COALESCE(tindakanpelayanan_t.dokterpenanggungjawab_id, (pendaftaran_t.pegawai_id)::bigint) = pegawai_m.pegawai_id)))
			             LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
			             LEFT JOIN pendidikankualifikasi_m ON ((pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
			             LEFT JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
			             LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
			             LEFT JOIN pegawai_m peg_soap ON ((soaprj_t.pegawai_id = peg_soap.pegawai_id)))
			             LEFT JOIN kelompokpegawai_m kelpeg_soap ON ((peg_soap.kelompokpegawai_id = kelpeg_soap.kelompokpegawai_id)))
			          WHERE (tindakanpelayanan_t.pasienmasukpenunjang_id IS NULL)
			        UNION ALL
			         SELECT \'PAKET\'::text AS jenis,
			            \'Tindakan & BMHP\'::text AS grouping_tipe,
			            2 AS jenis_urutan,
			            pendaftaran_t.pendaftaran_id,
			            ruangan_m.ruangan_id,
			            ruangan_m.ruangan_nama,
			            pegawai_m.pegawai_id,
			            pegawai_m.nama_pegawai,
			            kelompokpegawai_m.kelompokpegawai_nama,
			            pendidikankualifikasi_m.pendkualifikasi_nama AS nama_profesi,
			            soaprj_t.subject,
			            soaprj_t.object,
			            soaprj_t.a_diag_utama,
			            soaprj_t.a_diag_penyerta,
			            tindakanpelayanan_t.tgl_tindakan,
			            tipepaket_m.tipepaket_nama AS instruksi,
			            soaprj_t.soaprj_id,
			            soaprj_t.tgl_soaprj,
			            soaprj_t.planning,
			            soaprj_t.catatan_dokter,
			            array_to_json(ARRAY( SELECT daftartindakan_m.daftartindakan_nama
			                   FROM (paketpelayanan_mp
			                     LEFT JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
			                  WHERE (paketpelayanan_mp.tipepaket_id = tindakanpelayanan_t.tipepaket_id))) AS daftar_paket,
			            \'-\'::text AS catatan_dokterpengirim,
			            tindakanpelayanan_t.is_deleted AS is_hapus,
			            NULL::character varying AS status,
			            tindakanpelayanan_t.cyto_tindakan,
			            tindakanpelayanan_t.qty_tindakan AS qty,
			            NULL::character varying AS no_penunjang,
			            soaprj_t.instruksi AS verbal_instruksi,
			            soaprj_t.pemberi_instruksi_id,
			            soaprj_t.is_verifikasi_verbal,
			            soaprj_t.tgl_verif_verbal,
			            soaprj_t.pegawai_verbal_id,
			            pendaftaran_t.pasien_id,
			            ruangan_m.ruangan_id AS ruangan_penunjang_id,
			            ruangan_m.ruangan_nama AS ruangan_penunjang_nama,
			            instalasi_m.instalasi_id AS instalasi_penunjang_id,
			            instalasi_m.instalasi_nama AS instalasi_penunjang_nama,
			            NULL::integer AS kelompoktindakan_id,
			            COALESCE(peg_soap.nama_pegawai, pegawai_m.nama_pegawai) AS pegawai_soap,
			            COALESCE(kelpeg_soap.kelompokpegawai_nama, kelompokpegawai_m.kelompokpegawai_nama) AS kelompokpegawai_soap,
			                CASE COALESCE(tindakanpelayanan_t.tindakansudahbayar_id, 0)
			                    WHEN 0 THEN false
			                    ELSE true
			                END AS is_bayar,
			            NULL::integer AS permintaankepenunjang_id,
			            NULL::integer AS obatalkespasien_id,
			            tindakanpelayanan_t.tindakanpelayanan_id,
			            NULL::integer AS pasienkirimkeunitlain_id
			           FROM ((((((((((pendaftaran_t
			             JOIN soaprj_t ON ((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id)))
			             LEFT JOIN ruangan_m ON ((COALESCE(soaprj_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id)))
			             LEFT JOIN tindakanpelayanan_t ON ((pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id)))
			             LEFT JOIN pegawai_m ON ((COALESCE(tindakanpelayanan_t.dokterpenanggungjawab_id, (pendaftaran_t.pegawai_id)::bigint) = pegawai_m.pegawai_id)))
			             LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
			             LEFT JOIN pendidikankualifikasi_m ON ((pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
			             JOIN tipepaket_m ON ((tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id)))
			             LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
			             LEFT JOIN pegawai_m peg_soap ON ((soaprj_t.pegawai_id = peg_soap.pegawai_id)))
			             LEFT JOIN kelompokpegawai_m kelpeg_soap ON ((peg_soap.kelompokpegawai_id = kelpeg_soap.kelompokpegawai_id)))
			          WHERE (tindakanpelayanan_t.pasienmasukpenunjang_id IS NULL)
			        UNION ALL
			         SELECT \'BMHP\'::text AS jenis,
			            \'Tindakan & BMHP\'::text AS grouping_tipe,
			            2 AS jenis_urutan,
			            pendaftaran_t.pendaftaran_id,
			            ruangan_m.ruangan_id,
			            ruangan_m.ruangan_nama,
			            pegawai_m.pegawai_id,
			            pegawai_m.nama_pegawai,
			            kelompokpegawai_m.kelompokpegawai_nama,
			            pendidikankualifikasi_m.pendkualifikasi_nama AS nama_profesi,
			            soaprj_t.subject,
			            soaprj_t.object,
			            soaprj_t.a_diag_utama,
			            soaprj_t.a_diag_penyerta,
			            obatalkespasien_t.tglpelayanan AS tgl_tindakan,
			            obatalkes_m.obatalkes_nama AS instruksi,
			            soaprj_t.soaprj_id,
			            soaprj_t.tgl_soaprj,
			            soaprj_t.planning,
			            soaprj_t.catatan_dokter,
			            array_to_json(NULL::character varying[]) AS daftar_paket,
			            \'-\'::text AS catatan_dokterpengirim,
			            obatalkespasien_t.is_deleted AS is_hapus,
			            NULL::character varying AS status,
			            NULL::boolean AS cyto_tindakan,
			            obatalkespasien_t.qty_oa AS qty,
			            NULL::character varying AS no_penunjang,
			            soaprj_t.instruksi AS verbal_instruksi,
			            soaprj_t.pemberi_instruksi_id,
			            soaprj_t.is_verifikasi_verbal,
			            soaprj_t.tgl_verif_verbal,
			            soaprj_t.pegawai_verbal_id,
			            pendaftaran_t.pasien_id,
			            ruangan_m.ruangan_id AS ruangan_penunjang_id,
			            ruangan_m.ruangan_nama AS ruangan_penunjang_nama,
			            instalasi_m.instalasi_id AS instalasi_penunjang_id,
			            instalasi_m.instalasi_nama AS instalasi_penunjang_nama,
			            NULL::integer AS kelompoktindakan_id,
			            COALESCE(peg_soap.nama_pegawai, pegawai_m.nama_pegawai) AS pegawai_soap,
			            COALESCE(kelpeg_soap.kelompokpegawai_nama, kelompokpegawai_m.kelompokpegawai_nama) AS kelompokpegawai_soap,
			                CASE COALESCE(obatalkespasien_t.obatsudahbayar_id, 0)
			                    WHEN 0 THEN false
			                    ELSE true
			                END AS is_bayar,
			            NULL::integer AS permintaankepenunjang_id,
			            obatalkespasien_t.obatalkespasien_id,
			            NULL::integer AS tindakanpelayanan_id,
			            NULL::integer AS pasienkirimkeunitlain_id
			           FROM ((((((((((pendaftaran_t
			             LEFT JOIN soaprj_t ON ((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id)))
			             LEFT JOIN ruangan_m ON ((COALESCE(soaprj_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id)))
			             LEFT JOIN obatalkespasien_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
			             LEFT JOIN pegawai_m ON ((COALESCE(obatalkespasien_t.pegawai_id, pendaftaran_t.pegawai_id) = pegawai_m.pegawai_id)))
			             LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
			             LEFT JOIN pendidikankualifikasi_m ON ((pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
			             LEFT JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
			             LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
			             LEFT JOIN pegawai_m peg_soap ON ((soaprj_t.pegawai_id = peg_soap.pegawai_id)))
			             LEFT JOIN kelompokpegawai_m kelpeg_soap ON ((peg_soap.kelompokpegawai_id = kelpeg_soap.kelompokpegawai_id)))
			          WHERE (obatalkespasien_t.status_bmhp IS NOT NULL)
			        UNION ALL
			         SELECT
			                CASE
			                    WHEN ((racikan_m.racikan_singkatan)::text = \'OR\'::text) THEN \'Racikan\'::text
			                    ELSE \'Non Racikan\'::text
			                END AS jenis,
			            \'Reseptur\'::text AS grouping_tipe,
			            1 AS jenis_urutan,
			            pendaftaran_t.pendaftaran_id,
			            ruangan_m.ruangan_id,
			            ruangan_m.ruangan_nama,
			            pegawai_m.pegawai_id,
			            pegawai_m.nama_pegawai,
			            kelompokpegawai_m.kelompokpegawai_nama,
			            pendidikankualifikasi_m.pendkualifikasi_nama AS nama_profesi,
			            soaprj_t.subject,
			            soaprj_t.object,
			            soaprj_t.a_diag_utama,
			            soaprj_t.a_diag_penyerta,
			            resepturdetail_t.created_date AS tgl_tindakan,
			            obatalkes_m.obatalkes_nama AS instruksi,
			            soaprj_t.soaprj_id,
			            soaprj_t.tgl_soaprj,
			            soaprj_t.planning,
			            soaprj_t.catatan_dokter,
			            array_to_json(NULL::character varying[]) AS daftar_paket,
			            \'-\'::text AS catatan_dokterpengirim,
			            resepturdetail_t.is_deleted AS is_hapus,
			            (reseptur_t.status_reseptur)::character varying AS status,
			            NULL::boolean AS cyto_tindakan,
			            resepturdetail_t.qty_reseptur AS qty,
			            NULL::character varying AS no_penunjang,
			            soaprj_t.instruksi AS verbal_instruksi,
			            soaprj_t.pemberi_instruksi_id,
			            soaprj_t.is_verifikasi_verbal,
			            soaprj_t.tgl_verif_verbal,
			            soaprj_t.pegawai_verbal_id,
			            pendaftaran_t.pasien_id,
			            ruangan_m.ruangan_id AS ruangan_penunjang_id,
			            ruangan_m.ruangan_nama AS ruangan_penunjang_nama,
			            instalasi_m.instalasi_id AS instalasi_penunjang_id,
			            instalasi_m.instalasi_nama AS instalasi_penunjang_nama,
			            NULL::integer AS kelompoktindakan_id,
			            COALESCE(peg_soap.nama_pegawai, pegawai_m.nama_pegawai) AS pegawai_soap,
			            COALESCE(kelpeg_soap.kelompokpegawai_nama, kelompokpegawai_m.kelompokpegawai_nama) AS kelompokpegawai_soap,
			                CASE COALESCE(obatalkespasien_t.obatsudahbayar_id, 0)
			                    WHEN 0 THEN false
			                    ELSE true
			                END AS is_bayar,
			            NULL::integer AS permintaankepenunjang_id,
			            obatalkespasien_t.obatalkespasien_id,
			            NULL::integer AS tindakanpelayanan_id,
			            NULL::integer AS pasienkirimkeunitlain_id
			           FROM (((((((((((((reseptur_t
			             JOIN resepturdetail_t ON ((reseptur_t.reseptur_id = resepturdetail_t.reseptur_id)))
			             LEFT JOIN obatalkes_m ON ((resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
			             LEFT JOIN racikan_m ON ((resepturdetail_t.racikan_id = racikan_m.racikan_id)))
			             JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = reseptur_t.pendaftaran_id)))
			             LEFT JOIN soaprj_t ON ((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id)))
			             LEFT JOIN ruangan_m ON ((COALESCE(soaprj_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id)))
			             LEFT JOIN pegawai_m ON ((COALESCE(reseptur_t.pegawai_id, pendaftaran_t.pegawai_id) = pegawai_m.pegawai_id)))
			             LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
			             LEFT JOIN pendidikankualifikasi_m ON ((pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
			             LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
			             LEFT JOIN pegawai_m peg_soap ON ((soaprj_t.pegawai_id = peg_soap.pegawai_id)))
			             LEFT JOIN kelompokpegawai_m kelpeg_soap ON ((peg_soap.kelompokpegawai_id = kelpeg_soap.kelompokpegawai_id)))
			             LEFT JOIN obatalkespasien_t ON ((resepturdetail_t.resepturdetail_id = obatalkespasien_t.resepturdetail_id)))
			          WHERE ((racikan_m.racikan_singkatan)::text <> \'OR\'::text)
			        UNION ALL
			         SELECT \'Racikan\'::text AS jenis,
			            \'Reseptur\'::text AS grouping_tipe,
			            1 AS jenis_urutan,
			            pendaftaran_t.pendaftaran_id,
			            ruangan_m.ruangan_id,
			            ruangan_m.ruangan_nama,
			            pegawai_m.pegawai_id,
			            pegawai_m.nama_pegawai,
			            kelompokpegawai_m.kelompokpegawai_nama,
			            pendidikankualifikasi_m.pendkualifikasi_nama AS nama_profesi,
			            soaprj_t.subject,
			            soaprj_t.object,
			            soaprj_t.a_diag_utama,
			            soaprj_t.a_diag_penyerta,
			            resepturracikan_t.created_date AS tgl_tindakan,
			            resepturracikan_t.racikan AS instruksi,
			            soaprj_t.soaprj_id,
			            soaprj_t.tgl_soaprj,
			            soaprj_t.planning,
			            soaprj_t.catatan_dokter,
			            array_to_json(NULL::character varying[]) AS daftar_paket,
			            \'-\'::text AS catatan_dokterpengirim,
			            resepturracikan_t.is_deleted AS is_hapus,
			            (reseptur_t.status_reseptur)::character varying AS status,
			            NULL::boolean AS cyto_tindakan,
			            0 AS qty,
			            NULL::character varying AS no_penunjang,
			            soaprj_t.instruksi AS verbal_instruksi,
			            soaprj_t.pemberi_instruksi_id,
			            soaprj_t.is_verifikasi_verbal,
			            soaprj_t.tgl_verif_verbal,
			            soaprj_t.pegawai_verbal_id,
			            pendaftaran_t.pasien_id,
			            ruangan_m.ruangan_id AS ruangan_penunjang_id,
			            ruangan_m.ruangan_nama AS ruangan_penunjang_nama,
			            instalasi_m.instalasi_id AS instalasi_penunjang_id,
			            instalasi_m.instalasi_nama AS instalasi_penunjang_nama,
			            NULL::integer AS kelompoktindakan_id,
			            COALESCE(peg_soap.nama_pegawai, pegawai_m.nama_pegawai) AS pegawai_soap,
			            COALESCE(kelpeg_soap.kelompokpegawai_nama, kelompokpegawai_m.kelompokpegawai_nama) AS kelompokpegawai_soap,
			                CASE COALESCE(obatalkespasien_t.obatsudahbayar_id, 0)
			                    WHEN 0 THEN false
			                    ELSE true
			                END AS is_bayar,
			            NULL::integer AS permintaankepenunjang_id,
			            obatalkespasien_t.obatalkespasien_id,
			            NULL::integer AS tindakanpelayanan_id,
			            NULL::integer AS pasienkirimkeunitlain_id
			           FROM ((((((((((((reseptur_t
			             JOIN resepturracikan_t ON ((reseptur_t.reseptur_id = resepturracikan_t.reseptur_id)))
			             JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = reseptur_t.pendaftaran_id)))
			             LEFT JOIN soaprj_t ON ((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id)))
			             LEFT JOIN ruangan_m ON ((COALESCE(soaprj_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id)))
			             LEFT JOIN pegawai_m ON ((COALESCE(reseptur_t.pegawai_id, pendaftaran_t.pegawai_id) = pegawai_m.pegawai_id)))
			             LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
			             LEFT JOIN pendidikankualifikasi_m ON ((pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
			             LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
			             LEFT JOIN pegawai_m peg_soap ON ((soaprj_t.pegawai_id = peg_soap.pegawai_id)))
			             LEFT JOIN kelompokpegawai_m kelpeg_soap ON ((peg_soap.kelompokpegawai_id = kelpeg_soap.kelompokpegawai_id)))
			             LEFT JOIN penjualanresep_t ON ((reseptur_t.reseptur_id = penjualanresep_t.reseptur_id)))
			             LEFT JOIN obatalkespasien_t ON ((penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id)))
			        UNION ALL
			         SELECT \'TINDAKAN\'::text AS jenis,
			            \'Penunjang\'::text AS grouping_tipe,
			            3 AS jenis_urutan,
			            pendaftaran_t.pendaftaran_id,
			            ruangan_m.ruangan_id,
			            ruangan_m.ruangan_nama,
			            pegawai_m.pegawai_id,
			            pegawai_m.nama_pegawai,
			            kelompokpegawai_m.kelompokpegawai_nama,
			            pendidikankualifikasi_m.pendkualifikasi_nama AS nama_profesi,
			            soaprj_t.subject,
			            soaprj_t.object,
			            soaprj_t.a_diag_utama,
			            soaprj_t.a_diag_penyerta,
			            pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_tindakan,
			            daftartindakan_m.daftartindakan_nama AS instruksi,
			            soaprj_t.soaprj_id,
			            soaprj_t.tgl_soaprj,
			            soaprj_t.planning,
			            soaprj_t.catatan_dokter,
			            array_to_json(NULL::character varying[]) AS daftar_paket,
			            pasienkirimkeunitlain_t.catatan_dokterpengirim,
			            permintaankepenunjang_t.is_deleted AS is_hapus,
			                CASE
			                    WHEN (pasienmasukpenunjang_t.status_periksa IS NULL) THEN pasienkirimkeunitlain_t.status_penunjang
			                    ELSE pasienmasukpenunjang_t.status_periksa
			                END AS status,
			            NULL::boolean AS cyto_tindakan,
			            permintaankepenunjang_t.qtypermintaan AS qty,
			            pasienkirimkeunitlain_t.no_orderkeunitlain AS no_penunjang,
			            soaprj_t.instruksi AS verbal_instruksi,
			            soaprj_t.pemberi_instruksi_id,
			            soaprj_t.is_verifikasi_verbal,
			            soaprj_t.tgl_verif_verbal,
			            soaprj_t.pegawai_verbal_id,
			            pendaftaran_t.pasien_id,
			            r_penunjang.ruangan_id AS ruangan_penunjang_id,
			            r_penunjang.ruangan_nama AS ruangan_penunjang_nama,
			            i_penunjang.instalasi_id AS instalasi_penunjang_id,
			            i_penunjang.instalasi_nama AS instalasi_penunjang_nama,
			            daftartindakan_m.kelompoktindakan_id,
			            COALESCE(peg_soap.nama_pegawai, pegawai_m.nama_pegawai) AS pegawai_soap,
			            COALESCE(kelpeg_soap.kelompokpegawai_nama, kelompokpegawai_m.kelompokpegawai_nama) AS kelompokpegawai_soap,
			                CASE COALESCE(tindakanpelayanan_t.tindakansudahbayar_id, 0)
			                    WHEN 0 THEN false
			                    ELSE true
			                END AS is_bayar,
			            permintaankepenunjang_t.permintaankepenunjang_id,
			            NULL::integer AS obatalkespasien_id,
			            tindakanpelayanan_t.tindakanpelayanan_id,
			            pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
			           FROM ((((((((((((((pasienkirimkeunitlain_t
			             JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
			             LEFT JOIN pasienmasukpenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id)))
			             JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = pasienkirimkeunitlain_t.pendaftaran_id)))
			             LEFT JOIN soaprj_t ON ((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id)))
			             LEFT JOIN ruangan_m ON ((COALESCE(soaprj_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id)))
			             LEFT JOIN pegawai_m ON ((COALESCE(pasienmasukpenunjang_t.pegawai_id, pasienkirimkeunitlain_t.pegawai_id) = pegawai_m.pegawai_id)))
			             LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
			             LEFT JOIN pendidikankualifikasi_m ON ((pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
			             LEFT JOIN daftartindakan_m ON ((permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
			             LEFT JOIN ruangan_m r_penunjang ON ((pasienkirimkeunitlain_t.ruangan_id = r_penunjang.ruangan_id)))
			             LEFT JOIN instalasi_m i_penunjang ON ((pasienkirimkeunitlain_t.instalasi_id = i_penunjang.instalasi_id)))
			             LEFT JOIN pegawai_m peg_soap ON ((soaprj_t.pegawai_id = peg_soap.pegawai_id)))
			             LEFT JOIN kelompokpegawai_m kelpeg_soap ON ((peg_soap.kelompokpegawai_id = kelpeg_soap.kelompokpegawai_id)))
			             LEFT JOIN tindakanpelayanan_t ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id) AND (permintaankepenunjang_t.daftartindakan_id = tindakanpelayanan_t.daftartindakan_id))))
			        UNION ALL
			         SELECT \'PAKET\'::text AS jenis,
			            \'Penunjang\'::text AS grouping_tipe,
			            3 AS jenis_urutan,
			            pendaftaran_t.pendaftaran_id,
			            ruangan_m.ruangan_id,
			            ruangan_m.ruangan_nama,
			            pegawai_m.pegawai_id,
			            pegawai_m.nama_pegawai,
			            kelompokpegawai_m.kelompokpegawai_nama,
			            pendidikankualifikasi_m.pendkualifikasi_nama AS nama_profesi,
			            soaprj_t.subject,
			            soaprj_t.object,
			            soaprj_t.a_diag_utama,
			            soaprj_t.a_diag_penyerta,
			            pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_tindakan,
			            tipepaket_m.tipepaket_nama AS instruksi,
			            soaprj_t.soaprj_id,
			            soaprj_t.tgl_soaprj,
			            soaprj_t.planning,
			            soaprj_t.catatan_dokter,
			            array_to_json(ARRAY( SELECT daftartindakan_m.daftartindakan_nama
			                   FROM (paketpelayanan_mp
			                     LEFT JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
			                  WHERE (paketpelayanan_mp.tipepaket_id = permintaankepenunjang_t.tipepaket_id))) AS daftar_paket,
			            pasienkirimkeunitlain_t.catatan_dokterpengirim,
			            permintaankepenunjang_t.is_deleted AS is_hapus,
			                CASE
			                    WHEN (pasienmasukpenunjang_t.status_periksa IS NULL) THEN pasienkirimkeunitlain_t.status_penunjang
			                    ELSE pasienmasukpenunjang_t.status_periksa
			                END AS status,
			            NULL::boolean AS cyto_tindakan,
			            permintaankepenunjang_t.qtypermintaan AS qty,
			            pasienkirimkeunitlain_t.no_orderkeunitlain AS no_penunjang,
			            soaprj_t.instruksi AS verbal_instruksi,
			            soaprj_t.pemberi_instruksi_id,
			            soaprj_t.is_verifikasi_verbal,
			            soaprj_t.tgl_verif_verbal,
			            soaprj_t.pegawai_verbal_id,
			            pendaftaran_t.pasien_id,
			            r_penunjang.ruangan_id AS ruangan_penunjang_id,
			            r_penunjang.ruangan_nama AS ruangan_penunjang_nama,
			            i_penunjang.instalasi_id AS instalasi_penunjang_id,
			            i_penunjang.instalasi_nama AS instalasi_penunjang_nama,
			            NULL::integer AS kelompoktindakan_id,
			            COALESCE(peg_soap.nama_pegawai, pegawai_m.nama_pegawai) AS pegawai_soap,
			            COALESCE(kelpeg_soap.kelompokpegawai_nama, kelompokpegawai_m.kelompokpegawai_nama) AS kelompokpegawai_soap,
			                CASE COALESCE(tindakanpelayanan_t.tindakansudahbayar_id, 0)
			                    WHEN 0 THEN false
			                    ELSE true
			                END AS is_bayar,
			            permintaankepenunjang_t.permintaankepenunjang_id,
			            NULL::integer AS obatalkespasien_id,
			            tindakanpelayanan_t.tindakanpelayanan_id,
			            pasienkirimkeunitlain_t.pasienkirimkeunitlain_id
			           FROM ((((((((((((((pasienkirimkeunitlain_t
			             JOIN permintaankepenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id)))
			             LEFT JOIN pasienmasukpenunjang_t ON ((pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = pasienmasukpenunjang_t.pasienkirimkeunitlain_id)))
			             JOIN pendaftaran_t ON ((pendaftaran_t.pendaftaran_id = pasienkirimkeunitlain_t.pendaftaran_id)))
			             LEFT JOIN soaprj_t ON ((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id)))
			             LEFT JOIN ruangan_m ON ((COALESCE(soaprj_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id)))
			             LEFT JOIN pegawai_m ON ((COALESCE(pasienmasukpenunjang_t.pegawai_id, pasienkirimkeunitlain_t.pegawai_id) = pegawai_m.pegawai_id)))
			             LEFT JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
			             LEFT JOIN pendidikankualifikasi_m ON ((pegawai_m.pendkualifikasi_id = pendidikankualifikasi_m.pendkualifikasi_id)))
			             JOIN tipepaket_m ON ((permintaankepenunjang_t.tipepaket_id = tipepaket_m.tipepaket_id)))
			             LEFT JOIN ruangan_m r_penunjang ON ((pasienkirimkeunitlain_t.ruangan_id = r_penunjang.ruangan_id)))
			             LEFT JOIN instalasi_m i_penunjang ON ((pasienkirimkeunitlain_t.instalasi_id = i_penunjang.instalasi_id)))
			             LEFT JOIN pegawai_m peg_soap ON ((soaprj_t.pegawai_id = peg_soap.pegawai_id)))
			             LEFT JOIN kelompokpegawai_m kelpeg_soap ON ((peg_soap.kelompokpegawai_id = kelpeg_soap.kelompokpegawai_id)))
			             LEFT JOIN tindakanpelayanan_t ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = tindakanpelayanan_t.pasienmasukpenunjang_id) AND (tindakanpelayanan_t.tipepaket_id = permintaankepenunjang_t.tipepaket_id))))
			        UNION ALL
			         SELECT \'Verbal Order\'::text AS jenis,
			            \'Verbal Order\'::text AS grouping_tipe,
			            5 AS jenis_urutan,
			            pendaftaran_t.pendaftaran_id,
			            ruangan_m.ruangan_id,
			            ruangan_m.ruangan_nama,
			            pegawai_m.pegawai_id,
			            pegawai_m.nama_pegawai,
			            kelompokpegawai_m.kelompokpegawai_nama,
			            \'-\'::text AS nama_profesi,
			            soaprj_t.subject,
			            soaprj_t.object,
			            array_to_json(NULL::character varying[]) AS a_diag_utama,
			            array_to_json(NULL::character varying[]) AS a_diag_penyerta,
			            soaprj_t.tgl_soaprj AS tgl_tindakan,
			            \'-\'::text AS instruksi,
			            soaprj_t.soaprj_id,
			            soaprj_t.tgl_soaprj,
			            soaprj_t.planning,
			            soaprj_t.catatan_dokter,
			            array_to_json(NULL::character varying[]) AS daftar_paket,
			            \'-\'::text AS catatan_dokterpengirim,
			            soaprj_t.is_deleted AS is_hapus,
			            \'-\'::text AS status,
			            NULL::boolean AS cyto_tindakan,
			            NULL::double precision AS qty,
			            \'-\'::text AS no_penunjang,
			            soaprj_t.instruksi AS verbal_instruksi,
			            soaprj_t.pemberi_instruksi_id,
			            soaprj_t.is_verifikasi_verbal,
			            soaprj_t.tgl_verif_verbal,
			            soaprj_t.pegawai_verbal_id,
			            pendaftaran_t.pasien_id,
			            ruangan_m.ruangan_id AS ruangan_penunjang_id,
			            ruangan_m.ruangan_nama AS ruangan_penunjang_nama,
			            instalasi_m.instalasi_id AS instalasi_penunjang_id,
			            instalasi_m.instalasi_nama AS instalasi_penunjang_nama,
			            NULL::integer AS kelompoktindakan_id,
			            COALESCE(peg_soap.nama_pegawai, pegawai_m.nama_pegawai) AS pegawai_soap,
			            COALESCE(kelpeg_soap.kelompokpegawai_nama, kelompokpegawai_m.kelompokpegawai_nama) AS kelompokpegawai_soap,
			            false AS is_bayar,
			            NULL::integer AS permintaankepenunjang_id,
			            NULL::integer AS obatalkespasien_id,
			            NULL::integer AS tindakanpelayanan_id,
			            NULL::integer AS pasienkirimkeunitlain_id
			           FROM (((((((pendaftaran_t
			             JOIN soaprj_t ON ((pendaftaran_t.pendaftaran_id = soaprj_t.pendaftaran_id)))
			             LEFT JOIN ruangan_m ON ((COALESCE(soaprj_t.ruangan_id, pendaftaran_t.ruangan_id) = ruangan_m.ruangan_id)))
			             LEFT JOIN pegawai_m ON ((COALESCE(soaprj_t.pegawai_id, pendaftaran_t.pegawai_id) = pegawai_m.pegawai_id)))
			             JOIN kelompokpegawai_m ON ((pegawai_m.kelompokpegawai_id = kelompokpegawai_m.kelompokpegawai_id)))
			             LEFT JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
			             LEFT JOIN pegawai_m peg_soap ON ((soaprj_t.pegawai_id = peg_soap.pegawai_id)))
			             LEFT JOIN kelompokpegawai_m kelpeg_soap ON ((peg_soap.kelompokpegawai_id = kelpeg_soap.kelompokpegawai_id)))
			          WHERE (soaprj_t.instruksi IS NOT NULL)) t1
			  ORDER BY t1.pendaftaran_id DESC;
        ');
		
		$this->execute('
            DROP VIEW IF EXISTS "public"."nilaipemeriksaanlab_v";
        ');

        $this->execute('
            CREATE VIEW "public"."nilaipemeriksaanlab_v" AS  SELECT tindakanpelayanan_t.tindakanpelayanan_id,
			    tindakanpelayanan_t.pasienmasukpenunjang_id,
			    pasienmasukpenunjang_t.pendaftaran_id,
			    pasienmasukpenunjang_t.pasienadmisi_id,
			    pasienmasukpenunjang_t.pasien_id, 
			    pemeriksaanlab_m.pemeriksaanlab_id,
			    jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
			    tindakanpelayanan_t.daftartindakan_id,
			    daftartindakan_m.daftartindakan_nama,
			    samplelab_m.nama_sample,
			    pasien_m.nama_pasien,
			    pasien_m.jeniskelamin,
			    pendaftaran_t.umur,
			    ambilsample_t.ambilsample_id,
			    ambilsample_t.samplelab_id,
			    pasien_m.tanggal_lahir,
			    pasienmasukpenunjang_t.tglmasukpenunjang
			   FROM ((((((((tindakanpelayanan_t
			     JOIN pasienmasukpenunjang_t ON ((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id)))
			     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
			     JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
			     JOIN daftartindakan_m ON ((tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
			     JOIN ambilsample_t ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = ambilsample_t.pasienmasukpenunjang_id) AND (tindakanpelayanan_t.tindakanpelayanan_id = ambilsample_t.tindakanpelayanan_id))))
			     JOIN samplelab_m ON ((ambilsample_t.samplelab_id = samplelab_m.samplelab_id)))
			     JOIN pemeriksaanlab_m ON ((tindakanpelayanan_t.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id)))
			     JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
			  WHERE (tindakanpelayanan_t.instalasi_id = 4)
			UNION ALL
			 SELECT tindakanpelayanan_t.tindakanpelayanan_id,
			    tindakanpelayanan_t.pasienmasukpenunjang_id,
			    pasienmasukpenunjang_t.pendaftaran_id,
			    pasienmasukpenunjang_t.pasienadmisi_id,
			    pasienmasukpenunjang_t.pasien_id,
			    pemeriksaanlab_m.pemeriksaanlab_id,
			    jenispemeriksaanlab_m.jenispemeriksaanlab_nama,
			    paketpelayanan_mp.daftartindakan_id,
			    daftartindakan_m.daftartindakan_nama,
			    samplelab_m.nama_sample,
			    pasien_m.nama_pasien,
			    pasien_m.jeniskelamin,
			    pendaftaran_t.umur,
			    ambilsample_t.ambilsample_id,
			    ambilsample_t.samplelab_id,
			    pasien_m.tanggal_lahir,
			    pasienmasukpenunjang_t.tglmasukpenunjang
			   FROM (((((((((tindakanpelayanan_t
			     JOIN pasienmasukpenunjang_t ON ((tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id)))
			     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
			     JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
			     JOIN paketpelayanan_mp ON ((tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id)))
			     JOIN daftartindakan_m ON ((paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
			     JOIN ambilsample_t ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = ambilsample_t.pasienmasukpenunjang_id) AND (tindakanpelayanan_t.tindakanpelayanan_id = ambilsample_t.tindakanpelayanan_id) AND (paketpelayanan_mp.daftartindakan_id = ambilsample_t.tindakanpaket_id))))
			     JOIN samplelab_m ON ((ambilsample_t.samplelab_id = samplelab_m.samplelab_id)))
			     JOIN pemeriksaanlab_m ON ((paketpelayanan_mp.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id)))
			     JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
			  WHERE (tindakanpelayanan_t.instalasi_id = 4)
			UNION ALL
			 SELECT tindakanpelayanan_t.tindakanpelayanan_id,
			    pasienmasukpenunjang_t.pasienmasukpenunjang_id,
			    pasienmasukpenunjang_t.pendaftaran_id,
			    pasienmasukpenunjang_t.pasienadmisi_id,
			    pasienmasukpenunjang_t.pasien_id,
			    detail.p_lab_id AS pemeriksaanlab_id,
			    detail.j_lab AS jenispemeriksaanlab_nama,
			    detail.detail_3id AS daftartindakan_id,
			    detail.detail_3 AS daftartindakan_nama,
			    samplelab_m.nama_sample,
			    pasien_m.nama_pasien,
			    pasien_m.jeniskelamin,
			    pendaftaran_t.umur,
			    ambilsample_t.ambilsample_id,
			    ambilsample_t.samplelab_id,
			    pasien_m.tanggal_lahir,
			    pasienmasukpenunjang_t.tglmasukpenunjang
			   FROM (((((((tindakanpelayanan_t
			     JOIN pasienmasukpenunjang_t ON ((tindakanpelayanan_t.pendaftaran_id = pasienmasukpenunjang_t.pendaftaran_id)))
			     JOIN pendaftaran_t ON ((pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
			     JOIN pasien_m ON ((pasienmasukpenunjang_t.pasien_id = pasien_m.pasien_id)))
			     JOIN ( SELECT \'3tingkatan\'::text AS jenis,
			            paketpelayanan_mp.tipepaket_id AS detail_1id,
			            paketpelayanan_mp.paketdetail_id AS detail_2id,
			            paket_detail.tipepaket_nama AS detail_2,
			            paket_detail.daftartindakan_id AS detail_3id,
			            paket_detail.daftartindakan_nama AS detail_3,
			            paket_detail.p_lab_id,
			            paket_detail.p_lab,
			            paket_detail.j_lab
			           FROM ((tipepaket_m
			             JOIN paketpelayanan_mp ON (((tipepaket_m.tipepaket_id = paketpelayanan_mp.tipepaket_id) AND (paketpelayanan_mp.is_deleted = false))))
			             JOIN ( SELECT a.tipepaket_id,
			                    a.tipepaket_nama,
			                    daftartindakan_m.daftartindakan_id,
			                    daftartindakan_m.daftartindakan_nama,
			                    pemeriksaanlab_m.pemeriksaanlab_id AS p_lab_id,
			                    pemeriksaanlab_m.pemeriksaanlab_nama AS p_lab,
			                    jenispemeriksaanlab_m.jenispemeriksaanlab_nama AS j_lab
			                   FROM ((((tipepaket_m a
			                     JOIN paketpelayanan_mp paketpelayanan_mp_1 ON ((a.tipepaket_id = paketpelayanan_mp_1.tipepaket_id)))
			                     JOIN daftartindakan_m ON ((paketpelayanan_mp_1.daftartindakan_id = daftartindakan_m.daftartindakan_id)))
			                     LEFT JOIN pemeriksaanlab_m ON ((daftartindakan_m.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id)))
			                     LEFT JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))) paket_detail ON ((paketpelayanan_mp.paketdetail_id = paket_detail.tipepaket_id)))
			          WHERE (tipepaket_m.is_deleted = false)
			        UNION ALL
			         SELECT \'2tingkatan\'::text AS jenis,
			            paketpelayanan_mp.tipepaket_id AS detail_1id,
			            NULL::integer AS detail_2id,
			            NULL::character varying AS detail_2,
			            paketpelayanan_mp.daftartindakan_id AS detail_3id,
			            tindakan_detail.daftartindakan_nama AS detail_3,
			            pemeriksaanlab_m.pemeriksaanlab_id AS p_lab_id,
			            pemeriksaanlab_m.pemeriksaanlab_nama AS p_lab,
			            jenispemeriksaanlab_m.jenispemeriksaanlab_nama AS j_lab
			           FROM ((((tipepaket_m
			             JOIN paketpelayanan_mp ON (((tipepaket_m.tipepaket_id = paketpelayanan_mp.tipepaket_id) AND (paketpelayanan_mp.is_deleted = false))))
			             JOIN daftartindakan_m tindakan_detail ON ((paketpelayanan_mp.daftartindakan_id = tindakan_detail.daftartindakan_id)))
			             LEFT JOIN pemeriksaanlab_m ON ((tindakan_detail.daftartindakan_id = pemeriksaanlab_m.daftartindakan_id)))
			             LEFT JOIN jenispemeriksaanlab_m ON ((pemeriksaanlab_m.jenispemeriksaanlab_id = jenispemeriksaanlab_m.jenispemeriksaanlab_id)))
			          WHERE (tipepaket_m.is_deleted = false)) detail ON ((tindakanpelayanan_t.tipepaket_id = detail.detail_1id)))
			     JOIN ambilsample_t ON (((pasienmasukpenunjang_t.pasienmasukpenunjang_id = ambilsample_t.pasienmasukpenunjang_id) AND (tindakanpelayanan_t.tindakanpelayanan_id = ambilsample_t.tindakanpelayanan_id))))
			     JOIN samplelab_m ON ((ambilsample_t.samplelab_id = samplelab_m.samplelab_id)))
			     JOIN ruangan_m ON ((pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id)))
			  WHERE (ruangan_m.instalasi_id = 4);

        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210507_055937_improvment_provide_live cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210507_055937_improvment_provide_live cannot be reverted.\n";

        return false;
    }
    */
}
