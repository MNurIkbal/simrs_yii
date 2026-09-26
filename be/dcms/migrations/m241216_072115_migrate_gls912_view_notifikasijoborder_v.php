<?php

use yii\db\Migration;

/**
 * Class m241216_072115_migrate_gls912_view_notifikasijoborder_v
 */
class m241216_072115_migrate_gls912_view_notifikasijoborder_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS notifikasijoborder_v;    
        ');

        $this->execute('
            CREATE VIEW "public"."notifikasijoborder_v" AS  SELECT \'Tindakan\'::text AS tipe,
    false AS is_obat,
    instruksitindakan_t.instruksitindakan_id AS joborder_id,
    instruksi_t.tgl_instruksi AS tgl_pelayanan,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    instruksitindakan_t.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    instruksitindakan_t.penjamin_id,
    instruksitindakan_t.kelaspelayanan_id,
    daftartindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    instruksitindakan_t.qty,
    0 AS harga,
    instalasi_m.instalasi_nama AS instalasi_tujuan,
    petugas.pegawai_nama
   FROM instruksi_t
     JOIN ( SELECT a.instruksi_id,
            a.pendaftaran_id,
            a.instruksitindakan_id,
            a.instalasi_id,
            a.ruangan_id,
            a.penjamin_id,
            a.kelaspelayanan_id,
            a.status_implementasi,
            a.qty,
            a.is_deleted,
            a.daftartindakan_id,
            a.created_by
           FROM instruksitindakan_t a) instruksitindakan_t ON instruksi_t.instruksi_id = instruksitindakan_t.instruksi_id
     JOIN ( SELECT a.daftartindakan_id,
            a.daftartindakan_nama
           FROM daftartindakan_m a) daftartindakan_m ON instruksitindakan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN ( SELECT a.pendaftaran_id,
            a.pasienadmisi_id
           FROM pendaftaran_t a) pendaftaran_t ON instruksitindakan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON instruksitindakan_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON instruksitindakan_t.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT tindakanpelayanan_t_1.instruksitindakan_id
           FROM tindakanpelayanan_t tindakanpelayanan_t_1
          WHERE tindakanpelayanan_t_1.is_deleted IS FALSE) tindakanpelayanan_t ON instruksitindakan_t.instruksitindakan_id = tindakanpelayanan_t.instruksitindakan_id
     LEFT JOIN ( SELECT a.loginpemakai_id,
            b.nama_pegawai AS pegawai_nama
           FROM loginpemakai_k a
             JOIN ( SELECT pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai
                   FROM pegawai_m) b ON b.pegawai_id = a.pegawai_id) petugas ON instruksitindakan_t.created_by = petugas.loginpemakai_id
  WHERE instruksitindakan_t.status_implementasi::text = \'454\'::text AND instruksitindakan_t.is_deleted IS FALSE AND tindakanpelayanan_t.instruksitindakan_id IS NULL
UNION ALL
 SELECT \'Paket\'::text AS tipe,
    false AS is_obat,
    instruksitindakan_t.instruksitindakan_id AS joborder_id,
    instruksi_t.tgl_instruksi AS tgl_pelayanan,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    instruksitindakan_t.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    instruksitindakan_t.penjamin_id,
    instruksitindakan_t.kelaspelayanan_id,
    tipepaket_m.tipepaket_id AS daftartindakan_id,
    tipepaket_m.tipepaket_nama AS daftartindakan_nama,
    instruksitindakan_t.qty,
    0 AS harga,
    instalasi_m.instalasi_nama AS instalasi_tujuan,
    petugas.pegawai_nama
   FROM instruksi_t
     JOIN ( SELECT a.instruksi_id,
            a.pendaftaran_id,
            a.instruksitindakan_id,
            a.instalasi_id,
            a.ruangan_id,
            a.penjamin_id,
            a.kelaspelayanan_id,
            a.status_implementasi,
            a.qty,
            a.is_deleted,
            a.tipepaket_id,
            a.created_by
           FROM instruksitindakan_t a) instruksitindakan_t ON instruksi_t.instruksi_id = instruksitindakan_t.instruksi_id
     JOIN ( SELECT a.tipepaket_id,
            a.tipepaket_nama
           FROM tipepaket_m a) tipepaket_m ON instruksitindakan_t.tipepaket_id = tipepaket_m.tipepaket_id
     JOIN ( SELECT a.pendaftaran_id,
            a.pasienadmisi_id
           FROM pendaftaran_t a) pendaftaran_t ON instruksitindakan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON instruksitindakan_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON instruksitindakan_t.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT a.loginpemakai_id,
            b.nama_pegawai AS pegawai_nama
           FROM loginpemakai_k a
             JOIN ( SELECT pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai
                   FROM pegawai_m) b ON b.pegawai_id = a.pegawai_id) petugas ON instruksitindakan_t.created_by = petugas.loginpemakai_id
  WHERE instruksitindakan_t.status_implementasi::text = \'454\'::text AND instruksitindakan_t.is_deleted IS FALSE
UNION ALL
 SELECT \'BMHP\'::text AS tipe,
    true AS is_obat,
    instruksitindakanbmhp_t.instruksitindakan_id AS joborder_id,
    instruksi_t.tgl_instruksi AS tgl_pelayanan,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    instruksitindakanbmhp_t.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    instruksitindakanbmhp_t.penjamin_id,
    instruksitindakanbmhp_t.kelaspelayanan_id,
    obatalkes_m.obatalkes_id AS daftartindakan_id,
    obatalkes_m.obatalkes_nama AS daftartindakan_nama,
    instruksitindakanbmhp_t.qty,
    obatalkes_m.harganetto AS harga,
    instalasi_m.instalasi_nama AS instalasi_tujuan,
    petugas.pegawai_nama
   FROM instruksi_t
     JOIN ( SELECT a.pendaftaran_id,
            a.instruksi_id,
            a.obatalkes_id,
            a.instruksitindakan_id,
            a.ruangan_id,
            a.penjamin_id,
            a.kelaspelayanan_id,
            a.qty,
            a.instalasi_id,
            a.status_implementasi,
            a.is_deleted,
            a.created_by
           FROM instruksitindakanbmhp_t a) instruksitindakanbmhp_t ON instruksi_t.instruksi_id = instruksitindakanbmhp_t.instruksi_id
     JOIN ( SELECT a.obatalkes_id,
            a.obatalkes_nama,
            a.harganetto
           FROM obatalkes_m a) obatalkes_m ON instruksitindakanbmhp_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN ( SELECT a.pendaftaran_id,
            a.pasienadmisi_id
           FROM pendaftaran_t a) pendaftaran_t ON instruksitindakanbmhp_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON instruksitindakanbmhp_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON instruksitindakanbmhp_t.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT a.loginpemakai_id,
            b.nama_pegawai AS pegawai_nama
           FROM loginpemakai_k a
             JOIN ( SELECT pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai
                   FROM pegawai_m) b ON b.pegawai_id = a.pegawai_id) petugas ON instruksitindakanbmhp_t.created_by = petugas.loginpemakai_id
  WHERE instruksitindakanbmhp_t.status_implementasi::text = \'454\'::text AND instruksitindakanbmhp_t.is_deleted IS FALSE
UNION ALL
 SELECT \'Penunjang RI\'::text AS tipe,
    false AS is_obat,
    pasienkirimkeunitlain_t.pasienkirimkeunitlain_id AS joborder_id,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_pelayanan,
    pasienadmisi_t.pendaftaran_id,
    pasienadmisi_t.pasienadmisi_id,
    pasienkirimkeunitlain_t.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    pasienadmisi_t.penjamin_id,
    pasienadmisi_t.kelaspelayanan_id,
    daftartindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    permintaankepenunjang_t.qtypermintaan AS qty,
    0 AS harga,
    instalasi_m.instalasi_nama AS instalasi_tujuan,
    petugas.pegawai_nama
   FROM pasienkirimkeunitlain_t
     JOIN ( SELECT a.daftartindakan_id,
            a.pasienkirimkeunitlain_id,
            a.qtypermintaan
           FROM permintaankepenunjang_t a
          WHERE a.is_approve IS FALSE AND a.is_deleted IS FALSE) permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
     JOIN ( SELECT a.daftartindakan_id,
            a.daftartindakan_kode,
            a.daftartindakan_nama
           FROM daftartindakan_m a) daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN ( SELECT a.pasienadmisi_id,
            b.no_pendaftaran,
            b.pendaftaran_id,
            a.kelaspelayanan_id,
            a.penjamin_id
           FROM pasienadmisi_t a
             JOIN pendaftaran_t b ON a.pasienadmisi_id = b.pasienadmisi_id) pasienadmisi_t ON pasienkirimkeunitlain_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON pasienkirimkeunitlain_t.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT a.loginpemakai_id,
            b.nama_pegawai AS pegawai_nama
           FROM loginpemakai_k a
             JOIN ( SELECT pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai
                   FROM pegawai_m) b ON b.pegawai_id = a.pegawai_id) petugas ON pasienkirimkeunitlain_t.created_by = petugas.loginpemakai_id
  WHERE pasienkirimkeunitlain_t.pasienmasukpenunjang_id IS NULL AND (pasienkirimkeunitlain_t.status_penunjang::text <> ALL (ARRAY[\'472\'::character varying::text, \'541\'::character varying::text]))
UNION ALL
 SELECT \'Penunjang RD/RJ\'::text AS tipe,
    false AS is_obat,
    pasienkirimkeunitlain_t.pasienkirimkeunitlain_id AS joborder_id,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_pelayanan,
    pendaftaran_t.pendaftaran_id,
    pasienkirimkeunitlain_t.pasienadmisi_id,
    pasienkirimkeunitlain_t.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    pendaftaran_t.penjamin_id,
    pendaftaran_t.kelaspelayanan_id,
    daftartindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    permintaankepenunjang_t.qtypermintaan AS qty,
    0 AS harga,
    instalasi_m.instalasi_nama AS instalasi_tujuan,
    petugas.pegawai_nama
   FROM pasienkirimkeunitlain_t
     JOIN ( SELECT a.daftartindakan_id,
            a.pasienkirimkeunitlain_id,
            a.qtypermintaan
           FROM permintaankepenunjang_t a
          WHERE a.is_approve IS FALSE AND a.is_deleted IS FALSE) permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
     JOIN ( SELECT a.daftartindakan_id,
            a.daftartindakan_kode,
            a.daftartindakan_nama
           FROM daftartindakan_m a) daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN ( SELECT a.no_pendaftaran,
            a.pendaftaran_id,
            a.kelaspelayanan_id,
            a.penjamin_id
           FROM pendaftaran_t a) pendaftaran_t ON pasienkirimkeunitlain_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON pasienkirimkeunitlain_t.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT a.loginpemakai_id,
            b.nama_pegawai AS pegawai_nama
           FROM loginpemakai_k a
             JOIN ( SELECT pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai
                   FROM pegawai_m) b ON b.pegawai_id = a.pegawai_id) petugas ON pasienkirimkeunitlain_t.created_by = petugas.loginpemakai_id
  WHERE pasienkirimkeunitlain_t.pasienmasukpenunjang_id IS NULL AND pasienkirimkeunitlain_t.pasienadmisi_id IS NULL AND (pasienkirimkeunitlain_t.status_penunjang::text <> ALL (ARRAY[\'472\'::character varying::text, \'541\'::character varying::text]))
UNION ALL
 SELECT \'Penunjang Bedah Approve\'::text AS tipe,
    false AS is_obat,
    pasienkirimkeunitlain_t.pasienkirimkeunitlain_id AS joborder_id,
    pasienkirimkeunitlain_t.tgl_kirimpasien AS tgl_pelayanan,
    pendaftaran_t.pendaftaran_id,
    pasienkirimkeunitlain_t.pasienadmisi_id,
    pasienkirimkeunitlain_t.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    pendaftaran_t.penjamin_id,
    pendaftaran_t.kelaspelayanan_id,
    daftartindakan_m.daftartindakan_id,
    daftartindakan_m.daftartindakan_nama,
    permintaankepenunjang_t.qtypermintaan AS qty,
    0 AS harga,
    instalasi_m.instalasi_nama AS instalasi_tujuan,
    petugas.pegawai_nama
   FROM pasienkirimkeunitlain_t
     JOIN ( SELECT a.daftartindakan_id,
            a.pasienkirimkeunitlain_id,
            a.qtypermintaan
           FROM permintaankepenunjang_t a
          WHERE a.is_approve IS FALSE AND a.is_deleted IS FALSE) permintaankepenunjang_t ON pasienkirimkeunitlain_t.pasienkirimkeunitlain_id = permintaankepenunjang_t.pasienkirimkeunitlain_id
     JOIN ( SELECT a.daftartindakan_id,
            a.daftartindakan_kode,
            a.daftartindakan_nama
           FROM daftartindakan_m a) daftartindakan_m ON permintaankepenunjang_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN ( SELECT a.no_pendaftaran,
            a.pendaftaran_id, 
            a.kelaspelayanan_id,
            a.penjamin_id
           FROM pendaftaran_t a) pendaftaran_t ON pasienkirimkeunitlain_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama
           FROM ruangan_m a) ruangan_m ON pasienkirimkeunitlain_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON pasienkirimkeunitlain_t.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT a.loginpemakai_id,
            b.nama_pegawai AS pegawai_nama
           FROM loginpemakai_k a
             JOIN ( SELECT pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai
                   FROM pegawai_m) b ON b.pegawai_id = a.pegawai_id) petugas ON pasienkirimkeunitlain_t.created_by = petugas.loginpemakai_id
  WHERE pasienkirimkeunitlain_t.pasienadmisi_id IS NULL AND pasienkirimkeunitlain_t.status_penunjang::text <> \'541\'::text AND pasienkirimkeunitlain_t.instalasi_id = 12 AND NOT (pasienkirimkeunitlain_t.pasienmasukpenunjang_id IN ( SELECT verifikasibedah_r.pasienmasukpenunjang_id
           FROM verifikasibedah_r)) AND NOT (EXISTS ( SELECT 1
           FROM pasienmasukpenunjang_t
          WHERE pasienmasukpenunjang_t.pasienkirimkeunitlain_id = pasienkirimkeunitlain_t.pasienkirimkeunitlain_id AND pasienmasukpenunjang_t.status_periksa::text = \'476\'::text
         LIMIT 1))
UNION ALL
 SELECT \'Penunjang Bedah\'::text AS tipe,
    false AS is_obat,
    verifikasibedah_r.id AS joborder_id,
    verifikasibedah_r.created_date AS tgl_pelayanan,
    pasienmasukpenunjang_t.pendaftaran_id,
    pasienadmisi_t.pasienadmisi_id,
    pasienmasukpenunjang_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    pasienadmisi_t.penjamin_id,
    pasienadmisi_t.kelaspelayanan_id,
    verifikasibedah_r.daftartindakan_id,
    verifikasibedah_r.daftartindakan_nama,
    verifikasibedah_r.qty,
        CASE
            WHEN verifikasibedah_r.kode_posisi::text = \'508\'::text THEN
            CASE
                WHEN verifikasibedah_r.persentase > 0::double precision THEN
                CASE
                    WHEN verifikasibedah_r.is_cyto IS TRUE AND verifikasibedah_r.is_penyulit IS FALSE THEN (verifikasibedah_r.harga + verifikasibedah_r.harga * verifikasibedah_r.persencyto_tindakan / 100::double precision) * verifikasibedah_r.persentase / 100::double precision
                    WHEN verifikasibedah_r.is_cyto IS FALSE AND verifikasibedah_r.is_penyulit IS TRUE THEN (verifikasibedah_r.harga + verifikasibedah_r.harga * verifikasibedah_r.persen_penyulit / 100::double precision) * verifikasibedah_r.persentase / 100::double precision
                    WHEN verifikasibedah_r.is_cyto IS TRUE AND verifikasibedah_r.is_penyulit IS TRUE THEN (verifikasibedah_r.harga + verifikasibedah_r.harga * (verifikasibedah_r.persencyto_tindakan + verifikasibedah_r.persen_penyulit) / 100::double precision) * verifikasibedah_r.persentase / 100::double precision
                    ELSE verifikasibedah_r.harga * verifikasibedah_r.persentase / 100::double precision
                END
                ELSE verifikasibedah_r.harga
            END
            ELSE verifikasibedah_r.harga
        END AS harga,
    instalasi_m.instalasi_nama AS instalasi_tujuan,
    petugas.pegawai_nama
   FROM verifikasibedah_r
     JOIN ( SELECT a.pendaftaran_id,
            a.ruangan_id,
            a.pasienmasukpenunjang_id,
            a.status_periksa
           FROM pasienmasukpenunjang_t a) pasienmasukpenunjang_t ON pasienmasukpenunjang_t.pasienmasukpenunjang_id = verifikasibedah_r.pasienmasukpenunjang_id
     JOIN ( SELECT a.pendaftaran_id,
            a.pasienadmisi_id
           FROM pendaftaran_t a) pendaftaran_t ON pasienmasukpenunjang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT pasienadmisi_t_1.pasienadmisi_id,
            pasienadmisi_t_1.penjamin_id,
            pasienadmisi_t_1.kelaspelayanan_id
           FROM pasienadmisi_t pasienadmisi_t_1) pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) ruangan_m ON pasienmasukpenunjang_t.ruangan_id = ruangan_m.ruangan_id
     JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT a.loginpemakai_id,
            b.nama_pegawai AS pegawai_nama
           FROM loginpemakai_k a
             JOIN ( SELECT pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai
                   FROM pegawai_m) b ON b.pegawai_id = a.pegawai_id) petugas ON verifikasibedah_r.created_by = petugas.loginpemakai_id
  WHERE (pasienmasukpenunjang_t.status_periksa::text <> ALL (ARRAY[\'483\'::character varying::text, \'476\'::character varying::text])) AND verifikasibedah_r.is_deleted IS FALSE
UNION ALL
 SELECT \'Reseptur\'::text AS tipe,
    true AS is_obat,
    instruksi_t.instruksi_id AS joborder_id,
    instruksi_t.tgl_instruksi AS tgl_pelayanan,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    reseptur_t.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    pendaftaran_t.penjamin_id,
    pendaftaran_t.kelaspelayanan_id,
    obatalkes_m.obatalkes_id AS daftartindakan_id,
    obatalkes_m.obatalkes_nama AS daftartindakan_nama,
    resepturdetail_t.qty_reseptur AS qty,
    resepturdetail_t.hargasatuan_reseptur AS harga,
    instalasi_m.instalasi_nama AS instalasi_tujuan,
    petugas.pegawai_nama
   FROM instruksi_t
     JOIN ( SELECT a.instruksi_id,
            a.reseptur_id,
            a.ruangan_id,
            a.pendaftaran_id,
            a.status_reseptur,
            a.penjualanresep_id
           FROM reseptur_t a) reseptur_t ON instruksi_t.instruksi_id = reseptur_t.instruksi_id AND reseptur_t.penjualanresep_id IS NULL
     JOIN ( SELECT a.reseptur_id,
            a.obatalkes_id,
            a.qty_reseptur,
            a.hargasatuan_reseptur
           FROM resepturdetail_t a
          WHERE a.is_deleted IS FALSE) resepturdetail_t ON reseptur_t.reseptur_id = resepturdetail_t.reseptur_id
     JOIN ( SELECT a.obatalkes_id,
            a.obatalkes_nama,
            a.harganetto
           FROM obatalkes_m a) obatalkes_m ON resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN ( SELECT pendaftaran_t_1.pendaftaran_id,
            pendaftaran_t_1.pasienadmisi_id,
            pendaftaran_t_1.penjamin_id,
            pendaftaran_t_1.kelaspelayanan_id
           FROM pendaftaran_t pendaftaran_t_1) pendaftaran_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) ruangan_m ON reseptur_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT a.loginpemakai_id,
            b.nama_pegawai AS pegawai_nama
           FROM loginpemakai_k a
             JOIN ( SELECT pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai
                   FROM pegawai_m) b ON b.pegawai_id = a.pegawai_id) petugas ON instruksi_t.created_by = petugas.loginpemakai_id
  WHERE reseptur_t.status_reseptur <> ALL (ARRAY[660, 432])
UNION ALL
 SELECT
        CASE
            WHEN COALESCE(resepturracikan_t.type, \'OR\'::character varying)::text = \'OR\'::text THEN \'Reseptur Racikan\'::text
            ELSE \'Reseptur Non Racikan\'::text
        END AS tipe,
    true AS is_obat,
    instruksi_t.instruksi_id AS joborder_id,
    instruksi_t.tgl_instruksi AS tgl_pelayanan,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    reseptur_t.ruangan_id,
    ruangan_m.ruangan_nama,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama,
    pendaftaran_t.penjamin_id,
    pendaftaran_t.kelaspelayanan_id,
    NULL::integer AS daftartindakan_id,
    resepturracikan_t.racikan AS daftartindakan_nama,
    NULL::double precision AS qty,
    1 AS harga,
    instalasi_m.instalasi_nama AS instalasi_tujuan,
    petugas.pegawai_nama
   FROM instruksi_t
     JOIN ( SELECT a.instruksi_id,
            a.reseptur_id,
            a.ruangan_id,
            a.pendaftaran_id,
            a.status_reseptur,
            a.penjualanresep_id
           FROM reseptur_t a) reseptur_t ON instruksi_t.instruksi_id = reseptur_t.instruksi_id AND reseptur_t.penjualanresep_id IS NULL
     JOIN ( SELECT resepturracikan_t_1.reseptur_id,
            resepturracikan_t_1.racikan,
            resepturracikan_t_1.type
           FROM resepturracikan_t resepturracikan_t_1) resepturracikan_t ON reseptur_t.reseptur_id = resepturracikan_t.reseptur_id
     JOIN ( SELECT a.pasienadmisi_id,
            a.no_pendaftaran,
            a.pendaftaran_id,
            COALESCE(b.kelaspelayanan_id, a.kelaspelayanan_id) AS kelaspelayanan_id,
            COALESCE(b.penjamin_id, a.penjamin_id) AS penjamin_id
           FROM pendaftaran_t a
             LEFT JOIN pasienadmisi_t b ON a.pasienadmisi_id = b.pasienadmisi_id) pendaftaran_t ON reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) ruangan_m ON reseptur_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama
           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT a.loginpemakai_id,
            b.nama_pegawai AS pegawai_nama
           FROM loginpemakai_k a
             JOIN ( SELECT pegawai_m.pegawai_id,
                    pegawai_m.nama_pegawai
                   FROM pegawai_m) b ON b.pegawai_id = a.pegawai_id) petugas ON instruksi_t.created_by = petugas.loginpemakai_id
  WHERE reseptur_t.status_reseptur <> ALL (ARRAY[660, 432]);
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m241216_072115_migrate_gls912_view_notifikasijoborder_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m241216_072115_migrate_gls912_view_notifikasijoborder_v cannot be reverted.\n";

        return false;
    }
    */
}
