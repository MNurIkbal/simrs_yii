<?php

use yii\db\Migration;

/**
 * Class m221102_063852_migrate_plafon_view_invoiceridetail_v
 */
class m221102_063852_migrate_plafon_view_invoiceridetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."invoiceridetail_v";
        ');

        $this->execute('
            CREATE VIEW "public"."invoiceridetail_v" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    layanan.layanan_jenis,
    layanan.tgl_pelayanan,
    layanan.groupinacbg_id,
    layanan.groupinacbg_nama,
    layanan.tindakan_obat_kode,
    layanan.tindakan_obat_id,
    layanan.tindakan_obat,
    layanan.kelompok,
    layanan.qty,
    layanan.harga_satuan, 
    layanan.tarif,
    layanan.uom,
    layanan.ruangan,
    layanan.dokter,
    layanan.is_akomodasi,
    layanan.is_konsultasi,
    layanan.additional_data,
    COALESCE(layanan.kamarruangan_nokamar, pasienadmisi.kamarruangan_nokamar) AS kamar,
    COALESCE(layanan.no_tempattidur, pasienadmisi.no_tempattidur) AS no_bed,
    COALESCE(layanan.kelaspelayanan_nama, pasienadmisi.kelaspelayanan_nama) AS kelas,
    layanan.pembayaran_id,
    layanan.tarif_dijamin,
    layanan.tarif_dibayarkan,
    layanan.tarif_diskon,
    layanan.tarifcyto_tindakan,
    layanan.cyto_tindakan,
    layanan.is_visite,
    layanan.kelompoktindakan_id,
    layanan.tarifpenyulit_tindakan,
    layanan.pasienadmisi_id,
    layanan.penjamin_tinpelayanan_id,
    layanan.penjamin_pelayanan_id,
    layanan.is_diskon,
    layanan.instalasi_id,
    layanan.dijamin_payer,
    layanan.dijamin_subpayer,
    pasienadmisi.penjamin_id AS penjamin_utama_id
   FROM pendaftaran_t
     JOIN ( SELECT pasienadmisi_t.pasienadmisi_id,
            kamarruangan_m.kamarruangan_nokamar,
            kamartempattidur_m.no_tempattidur,
            kelaspelayanan_m.kelaspelayanan_nama,
            pasienadmisi_t.penjamin_id
           FROM pasienadmisi_t
             LEFT JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar
                   FROM kamarruangan_m a) kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             LEFT JOIN ( SELECT a.kamarruangan_id,
                    a.kamartempattidur_id,
                    a.no_tempattidur
                   FROM kamartempattidur_m a) kamartempattidur_m ON pasienadmisi_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON COALESCE(pasienadmisi_t.kelas_ditagihkan_id, pasienadmisi_t.kelaspelayanan_id) = kelaspelayanan_m.kelaspelayanan_id) pasienadmisi ON pendaftaran_t.pasienadmisi_id = pasienadmisi.pasienadmisi_id
     JOIN ( SELECT \'tindakan\'::text AS layanan_jenis,
            tindakanpelayanan_t.pendaftaran_id,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            daftartindakan_m.groupinacbg_id,
            groupinacbg_m.groupinacbg_nama,
            daftartindakan_m.daftartindakan_kode AS tindakan_obat_kode,
            daftartindakan_m.daftartindakan_id AS tindakan_obat_id,
            daftartindakan_m.daftartindakan_nama AS tindakan_obat,
                CASE
                    WHEN daftartindakan_m.is_konsultasi = true THEN \'Consultation\'::character varying
                    ELSE kelompoktindakan_m.kelompoktindakan_nama
                END AS kelompok,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarif_satuan AS harga_satuan,
            tindakanpelayanan_t.tarif_tindakan AS tarif,
            NULL::character varying AS uom,
            ruangan_m.ruangan_nama AS ruangan,
            dok_dpjp.nama_pegawai AS dokter,
            daftartindakan_m.is_akomodasi,
            daftartindakan_m.is_konsultasi,
            tindakanpelayanan_t.additional_data,
            kamarruangan_m.kamarruangan_nokamar,
            kamartempattidur_m.no_tempattidur,
            kelaspelayanan_m.kelaspelayanan_nama,
            pembayaranpelayanan_t.pembayaran_id,
            tindakanpelayanan_t.tarif_dijamin,
            tindakanpelayanan_t.tarif_dibayarkan,
            tindakanpelayanan_t.tarifcyto_tindakan,
            tindakanpelayanan_t.tarif_diskon,
            tindakanpelayanan_t.cyto_tindakan,
                CASE
                    WHEN daftartindakan_m.daftartindakan_id = 99993 THEN true
                    ELSE false
                END AS is_visite,
            daftartindakan_m.kelompoktindakan_id,
            tindakanpelayanan_t.tarifpenyulit_tindakan,
            tindakanpelayanan_t.pasienadmisi_id,
            tindakanpelayanan_t.penjamin_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_tinpelayanan_id,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            false AS is_diskon,
            tindakanpelayanan_t.instalasi_id,
            tindakanpelayanan_t.dijamin_payer,
            tindakanpelayanan_t.dijamin_subpayer
           FROM tindakanpelayanan_t
             JOIN ( SELECT a.daftartindakan_id,
                    a.daftartindakan_nama,
                    a.kelompoktindakan_id,
                    a.is_konsultasi,
                    a.daftartindakan_kode,
                    a.is_akomodasi,
                    a.groupinacbg_id
                   FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN ( SELECT a.kelompoktindakan_id,
                    a.kelompoktindakan_nama
                   FROM kelompoktindakan_m a) kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
             LEFT JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) dok_dpjp ON tindakanpelayanan_t.dokterpenanggungjawab_id = dok_dpjp.pegawai_id
             LEFT JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar
                   FROM kamarruangan_m a) kamarruangan_m ON tindakanpelayanan_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             LEFT JOIN ( SELECT a.kamartempattidur_id,
                    a.no_tempattidur
                   FROM kamartempattidur_m a) kamartempattidur_m ON tindakanpelayanan_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN ( SELECT a.tindakansudahbayar_id,
                    a.pembayaranpelayanan_id
                   FROM tindakansudahbayar_t a) tindakansudahbayar_t ON tindakansudahbayar_t.tindakansudahbayar_id = tindakanpelayanan_t.tindakansudahbayar_id
             JOIN ( SELECT a.pembayaranpelayanan_id,
                    a.pembayaran_id,
                    a.penjamin_id
                   FROM pembayaranpelayanan_t a) pembayaranpelayanan_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tindakansudahbayar_t.pembayaranpelayanan_id
             LEFT JOIN ( SELECT a.groupinacbg_id,
                    a.groupinacbg_nama
                   FROM groupinacbg_m a) groupinacbg_m ON daftartindakan_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
          WHERE tindakanpelayanan_t.is_deleted = false
        UNION ALL
         SELECT diskon_tindakan.layanan_jenis,
            diskon_tindakan.pendaftaran_id,
            diskon_tindakan.tgl_pelayanan,
            diskon_tindakan.groupinacbg_id,
            diskon_tindakan.groupinacbg_nama,
            diskon_tindakan.tindakan_obat_kode,
            diskon_tindakan.tindakan_obat_id,
            diskon_tindakan.tindakan_obat,
            diskon_tindakan.kelompok,
            diskon_tindakan.qty,
            diskon_tindakan.harga_satuan,
            diskon_tindakan.tarif,
            diskon_tindakan.uom,
            diskon_tindakan.ruangan,
            diskon_tindakan.dokter,
            diskon_tindakan.is_akomodasi,
            diskon_tindakan.is_konsultasi,
            diskon_tindakan.additional_data,
            diskon_tindakan.kamarruangan_nokamar,
            diskon_tindakan.no_tempattidur,
            diskon_tindakan.kelaspelayanan_nama,
            diskon_tindakan.pembayaran_id,
            diskon_tindakan.tarif_dijamin,
            diskon_tindakan.tarif_dibayarkan,
            diskon_tindakan.tarifcyto_tindakan,
            diskon_tindakan.tarif_diskon,
            diskon_tindakan.cyto_tindakan,
            diskon_tindakan.is_visite,
            diskon_tindakan.kelompoktindakan_id,
            diskon_tindakan.tarifpenyulit_tindakan,
            diskon_tindakan.pasienadmisi_id,
            diskon_tindakan.penjamin_id,
            diskon_tindakan.penjamin_tinpelayanan_id,
            diskon_tindakan.penjamin_pelayanan_id,
            diskon_tindakan.is_diskon,
            diskon_tindakan.instalasi_id,
            diskon_tindakan.dijamin_payer,
            diskon_tindakan.dijamin_subpayer
           FROM ( SELECT \'tindakan\'::text AS layanan_jenis,
                    tindakanpelayanan_t.pendaftaran_id,
                    tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
                    daftartindakan_m.groupinacbg_id,
                    groupinacbg_m.groupinacbg_nama,
                    daftartindakan_m.daftartindakan_kode AS tindakan_obat_kode,
                    daftartindakan_m.daftartindakan_id AS tindakan_obat_id,
                    daftartindakan_m.daftartindakan_nama AS tindakan_obat,
                        CASE
                            WHEN daftartindakan_m.is_konsultasi = true THEN \'Consultation\'::character varying
                            ELSE kelompoktindakan_m.kelompoktindakan_nama
                        END AS kelompok,
                    tindakanpelayanan_t.qty_tindakan AS qty,
                    - tindakanpelayanan_t.tarif_diskon AS harga_satuan,
                    - tindakanpelayanan_t.tarif_diskon AS tarif,
                    NULL::character varying AS uom,
                    ruangan_m.ruangan_nama AS ruangan,
                    dok_dpjp.nama_pegawai AS dokter,
                    daftartindakan_m.is_akomodasi,
                    daftartindakan_m.is_konsultasi,
                    tindakanpelayanan_t.additional_data,
                    kamarruangan_m.kamarruangan_nokamar,
                    kamartempattidur_m.no_tempattidur,
                    kelaspelayanan_m.kelaspelayanan_nama,
                    pembayaranpelayanan_t.pembayaran_id,
                    tindakanpelayanan_t.tarif_dijamin,
                    tindakanpelayanan_t.tarif_dibayarkan,
                    tindakanpelayanan_t.tarifcyto_tindakan,
                    tindakanpelayanan_t.tarif_diskon,
                    tindakanpelayanan_t.cyto_tindakan,
                        CASE
                            WHEN daftartindakan_m.daftartindakan_id = 99993 THEN true
                            ELSE false
                        END AS is_visite,
                    daftartindakan_m.kelompoktindakan_id,
                    tindakanpelayanan_t.tarifpenyulit_tindakan,
                    tindakanpelayanan_t.pasienadmisi_id,
                    tindakanpelayanan_t.penjamin_id,
                    tindakanpelayanan_t.penjamin_id AS penjamin_tinpelayanan_id,
                    pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                        CASE
                            WHEN tindakanpelayanan_t.tarif_diskon > 0::double precision THEN true
                            ELSE false
                        END AS is_diskon,
                    tindakanpelayanan_t.instalasi_id,
                    tindakanpelayanan_t.dijamin_payer,
                    tindakanpelayanan_t.dijamin_subpayer
                   FROM tindakanpelayanan_t
                     JOIN ( SELECT a.daftartindakan_id,
                            a.daftartindakan_nama,
                            a.kelompoktindakan_id,
                            a.is_konsultasi,
                            a.daftartindakan_kode,
                            a.is_akomodasi,
                            a.groupinacbg_id
                           FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                     JOIN ( SELECT a.kelompoktindakan_id,
                            a.kelompoktindakan_nama
                           FROM kelompoktindakan_m a) kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
                     LEFT JOIN ( SELECT a.ruangan_id,
                            a.ruangan_nama
                           FROM ruangan_m a) ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
                     LEFT JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai
                           FROM pegawai_m a) dok_dpjp ON tindakanpelayanan_t.dokterpenanggungjawab_id = dok_dpjp.pegawai_id
                     LEFT JOIN ( SELECT a.kamarruangan_id,
                            a.kamarruangan_nokamar
                           FROM kamarruangan_m a) kamarruangan_m ON tindakanpelayanan_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
                     LEFT JOIN ( SELECT a.kamartempattidur_id,
                            a.no_tempattidur
                           FROM kamartempattidur_m a) kamartempattidur_m ON tindakanpelayanan_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
                     LEFT JOIN ( SELECT a.kelaspelayanan_id,
                            a.kelaspelayanan_nama
                           FROM kelaspelayanan_m a) kelaspelayanan_m ON tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                     JOIN ( SELECT a.tindakansudahbayar_id,
                            a.pembayaranpelayanan_id
                           FROM tindakansudahbayar_t a) tindakansudahbayar_t ON tindakansudahbayar_t.tindakansudahbayar_id = tindakanpelayanan_t.tindakansudahbayar_id
                     JOIN ( SELECT a.pembayaranpelayanan_id,
                            a.pembayaran_id,
                            a.penjamin_id
                           FROM pembayaranpelayanan_t a) pembayaranpelayanan_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tindakansudahbayar_t.pembayaranpelayanan_id
                     LEFT JOIN ( SELECT a.groupinacbg_id,
                            a.groupinacbg_nama
                           FROM groupinacbg_m a) groupinacbg_m ON daftartindakan_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
                     JOIN ( SELECT a.is_invoice_diskon
                           FROM konfigtarif_k a) konfigtarif_k ON konfigtarif_k.is_invoice_diskon IS TRUE
                  WHERE tindakanpelayanan_t.is_deleted = false) diskon_tindakan
          WHERE diskon_tindakan.is_diskon IS TRUE
        UNION ALL
         SELECT \'tindakan\'::text AS layanan_jenis,
            tindakanpelayanan_t.pendaftaran_id,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            NULL::integer AS groupinacbg_id,
            NULL::character varying AS groupinacbg_nama,
            tipepaket_m.tipepaket_kode AS tindakan_obat_kode,
            tipepaket_m.tipepaket_id AS tindakan_obat_id,
            tipepaket_m.tipepaket_nama AS tindakan_obat,
                CASE
                    WHEN tipepaket_m.is_mcu IS TRUE THEN \'kelompok_paket_mcu\'::text
                    ELSE \'kelompok_paket\'::text
                END AS kelompok,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarif_satuan AS harga_satuan,
            tindakanpelayanan_t.tarif_tindakan AS tarif,
            NULL::character varying AS uom,
            ruangan_m.ruangan_nama AS ruangan,
            dok_dpjp.nama_pegawai AS dokter,
            false AS is_akomodasi,
            NULL::boolean AS is_konsultasi,
            tindakanpelayanan_t.additional_data,
            kamarruangan_m.kamarruangan_nokamar,
            kamartempattidur_m.no_tempattidur,
            kelaspelayanan_m.kelaspelayanan_nama,
            pembayaranpelayanan_t.pembayaran_id,
            tindakanpelayanan_t.tarif_dijamin,
            tindakanpelayanan_t.tarif_dibayarkan,
            tindakanpelayanan_t.tarifcyto_tindakan,
            tindakanpelayanan_t.tarif_diskon,
            tindakanpelayanan_t.cyto_tindakan,
            false AS is_visite,
            NULL::integer AS kelompoktindakan_id,
            tindakanpelayanan_t.tarifpenyulit_tindakan,
            tindakanpelayanan_t.pasienadmisi_id,
            tindakanpelayanan_t.penjamin_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_tinpelayanan_id,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            false AS is_diskon,
            tindakanpelayanan_t.instalasi_id,
            tindakanpelayanan_t.dijamin_payer,
            tindakanpelayanan_t.dijamin_subpayer
           FROM tindakanpelayanan_t
             JOIN ( SELECT tipepaket_m_1.tipepaket_id,
                    tipepaket_m_1.tipepaket_nama,
                    tipepaket_m_1.tipepaket_kode,
                    tipepaket_m_1.is_mcu
                   FROM tipepaket_m tipepaket_m_1) tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
             LEFT JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) dok_dpjp ON tindakanpelayanan_t.dokterpenanggungjawab_id = dok_dpjp.pegawai_id
             LEFT JOIN ( SELECT a.kamarruangan_id,
                    a.kamarruangan_nokamar
                   FROM kamarruangan_m a) kamarruangan_m ON tindakanpelayanan_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             LEFT JOIN ( SELECT a.kamartempattidur_id,
                    a.no_tempattidur
                   FROM kamartempattidur_m a) kamartempattidur_m ON tindakanpelayanan_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
             LEFT JOIN ( SELECT a.kelaspelayanan_id,
                    a.kelaspelayanan_nama
                   FROM kelaspelayanan_m a) kelaspelayanan_m ON tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
             JOIN ( SELECT a.tindakansudahbayar_id,
                    a.pembayaranpelayanan_id
                   FROM tindakansudahbayar_t a) tindakansudahbayar_t ON tindakansudahbayar_t.tindakansudahbayar_id = tindakanpelayanan_t.tindakansudahbayar_id
             JOIN ( SELECT a.pembayaranpelayanan_id,
                    a.pembayaran_id,
                    a.penjamin_id
                   FROM pembayaranpelayanan_t a) pembayaranpelayanan_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tindakansudahbayar_t.pembayaranpelayanan_id
          WHERE tindakanpelayanan_t.is_deleted = false
        UNION ALL
         SELECT diskon_paket.layanan_jenis,
            diskon_paket.pendaftaran_id,
            diskon_paket.tgl_pelayanan,
            diskon_paket.groupinacbg_id,
            diskon_paket.groupinacbg_nama,
            diskon_paket.tindakan_obat_kode,
            diskon_paket.tindakan_obat_id,
            diskon_paket.tindakan_obat,
            diskon_paket.kelompok,
            diskon_paket.qty,
            diskon_paket.harga_satuan,
            diskon_paket.tarif,
            diskon_paket.uom,
            diskon_paket.ruangan,
            diskon_paket.dokter,
            diskon_paket.is_akomodasi,
            diskon_paket.is_konsultasi,
            diskon_paket.additional_data,
            diskon_paket.kamarruangan_nokamar,
            diskon_paket.no_tempattidur,
            diskon_paket.kelaspelayanan_nama,
            diskon_paket.pembayaran_id,
            diskon_paket.tarif_dijamin,
            diskon_paket.tarif_dibayarkan,
            diskon_paket.tarifcyto_tindakan,
            diskon_paket.tarif_diskon,
            diskon_paket.cyto_tindakan,
            diskon_paket.is_visite,
            diskon_paket.kelompoktindakan_id,
            diskon_paket.tarifpenyulit_tindakan,
            diskon_paket.pasienadmisi_id,
            diskon_paket.penjamin_id,
            diskon_paket.penjamin_tinpelayanan_id,
            diskon_paket.penjamin_pelayanan_id,
            diskon_paket.is_diskon,
            diskon_paket.instalasi_id,
            diskon_paket.dijamin_payer,
            diskon_paket.dijamin_subpayer
           FROM ( SELECT \'tindakan\'::text AS layanan_jenis,
                    tindakanpelayanan_t.pendaftaran_id,
                    tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
                    NULL::integer AS groupinacbg_id,
                    NULL::character varying AS groupinacbg_nama,
                    tipepaket_m.tipepaket_kode AS tindakan_obat_kode,
                    tipepaket_m.tipepaket_id AS tindakan_obat_id,
                    tipepaket_m.tipepaket_nama AS tindakan_obat,
                        CASE
                            WHEN tipepaket_m.is_mcu IS TRUE THEN \'kelompok_paket_mcu\'::text
                            ELSE \'kelompok_paket\'::text
                        END AS kelompok,
                    tindakanpelayanan_t.qty_tindakan AS qty,
                    - tindakanpelayanan_t.tarif_diskon AS harga_satuan,
                    - tindakanpelayanan_t.tarif_diskon AS tarif,
                    NULL::character varying AS uom,
                    ruangan_m.ruangan_nama AS ruangan,
                    dok_dpjp.nama_pegawai AS dokter,
                    false AS is_akomodasi,
                    NULL::boolean AS is_konsultasi,
                    tindakanpelayanan_t.additional_data,
                    kamarruangan_m.kamarruangan_nokamar,
                    kamartempattidur_m.no_tempattidur,
                    kelaspelayanan_m.kelaspelayanan_nama,
                    pembayaranpelayanan_t.pembayaran_id,
                    tindakanpelayanan_t.tarif_dijamin,
                    tindakanpelayanan_t.tarif_dibayarkan,
                    tindakanpelayanan_t.tarifcyto_tindakan,
                    tindakanpelayanan_t.tarif_diskon,
                    tindakanpelayanan_t.cyto_tindakan,
                    false AS is_visite,
                    NULL::integer AS kelompoktindakan_id,
                    tindakanpelayanan_t.tarifpenyulit_tindakan,
                    tindakanpelayanan_t.pasienadmisi_id,
                    tindakanpelayanan_t.penjamin_id,
                    tindakanpelayanan_t.penjamin_id AS penjamin_tinpelayanan_id,
                    pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                        CASE
                            WHEN COALESCE(tindakanpelayanan_t.tarif_diskon, 0::double precision) > 0::double precision THEN true
                            ELSE false
                        END AS is_diskon,
                    tindakanpelayanan_t.instalasi_id,
                    tindakanpelayanan_t.dijamin_payer,
                    tindakanpelayanan_t.dijamin_subpayer
                   FROM tindakanpelayanan_t
                     JOIN ( SELECT tipepaket_m_1.tipepaket_id,
                            tipepaket_m_1.tipepaket_nama,
                            tipepaket_m_1.tipepaket_kode,
                            tipepaket_m_1.is_mcu
                           FROM tipepaket_m tipepaket_m_1) tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
                     LEFT JOIN ( SELECT a.ruangan_id,
                            a.ruangan_nama
                           FROM ruangan_m a) ruangan_m ON tindakanpelayanan_t.ruangan_id = ruangan_m.ruangan_id
                     LEFT JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai
                           FROM pegawai_m a) dok_dpjp ON tindakanpelayanan_t.dokterpenanggungjawab_id = dok_dpjp.pegawai_id
                     LEFT JOIN ( SELECT a.kamarruangan_id,
                            a.kamarruangan_nokamar
                           FROM kamarruangan_m a) kamarruangan_m ON tindakanpelayanan_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
                     LEFT JOIN ( SELECT a.kamartempattidur_id,
                            a.no_tempattidur
                           FROM kamartempattidur_m a) kamartempattidur_m ON tindakanpelayanan_t.kamartempattidur_id = kamartempattidur_m.kamartempattidur_id
                     LEFT JOIN ( SELECT a.kelaspelayanan_id,
                            a.kelaspelayanan_nama
                           FROM kelaspelayanan_m a) kelaspelayanan_m ON tindakanpelayanan_t.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                     JOIN ( SELECT a.tindakansudahbayar_id,
                            a.pembayaranpelayanan_id
                           FROM tindakansudahbayar_t a) tindakansudahbayar_t ON tindakansudahbayar_t.tindakansudahbayar_id = tindakanpelayanan_t.tindakansudahbayar_id
                     JOIN ( SELECT a.pembayaranpelayanan_id,
                            a.pembayaran_id,
                            a.penjamin_id
                           FROM pembayaranpelayanan_t a) pembayaranpelayanan_t ON pembayaranpelayanan_t.pembayaranpelayanan_id = tindakansudahbayar_t.pembayaranpelayanan_id
                     JOIN ( SELECT a.is_invoice_diskon
                           FROM konfigtarif_k a) konfigtarif_k ON konfigtarif_k.is_invoice_diskon IS TRUE
                  WHERE tindakanpelayanan_t.is_deleted = false) diskon_paket
          WHERE diskon_paket.is_diskon IS TRUE
        UNION ALL
         SELECT \'obat\'::text AS layanan_jenis,
            obatalkespasien_t.pendaftaran_id,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            NULL::integer AS groupinacbg_id,
            NULL::character varying AS groupinacbg_nama,
            obatalkes_m.obatalkes_kode AS tindakan_obat_kode,
            obatalkes_m.obatalkes_id AS tindakan_obat_id,
            obatalkes_m.obatalkes_nama AS tindakan_obat,
            \'Drugs & Consumables\'::character varying AS kelompok,
                CASE
                    WHEN obatalkespasien_t.det = 0::double precision THEN obatalkespasien_t.qty_oa
                    WHEN obatalkespasien_t.det IS NULL THEN obatalkespasien_t.qty_oa
                    ELSE obatalkespasien_t.det
                END AS qty,
            obatalkespasien_t.hargasatuan_oa AS harga_satuan,
            obatalkespasien_t.hargajual_oa AS tarif,
            satuanunit_m.satuanunit_nama AS uom,
            ruangan_m.ruangan_nama AS ruangan,
            dok_dpjp.nama_pegawai AS dokter,
            false AS is_akomodasi,
            false AS is_konsultasi,
            obatalkespasien_t.additional_data,
            NULL::character varying AS kamarruangan_nokamar,
            NULL::character varying AS no_tempattidur,
            NULL::character varying AS kelaspelayanan_nama,
            pembayaranpelayanan_t.pembayaran_id,
            obatalkespasien_t.tarif_dijamin,
            obatalkespasien_t.tarif_dibayarkan,
            0 AS tarifcyto_tindakan,
            obatalkespasien_t.tarif_diskon,
            false AS cyto_tindakan,
            false AS is_visite,
            NULL::integer AS kelompoktindakan_id,
            0 AS tarifpenyulit_tindakan,
            obatalkespasien_t.pasienadmisi_id,
            obatalkespasien_t.penjamin_id,
            obatalkespasien_t.penjamin_id AS penjamin_tinpelayanan_id,
            pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            false AS is_diskon,
            NULL::integer AS instalasi_id,
            obatalkespasien_t.dijamin_payer,
            obatalkespasien_t.dijamin_subpayer
           FROM obatalkespasien_t
             JOIN ( SELECT a.obatalkes_id,
                    a.obatalkes_kode,
                    a.obatalkes_nama,
                    a.groupinacbg_id,
                    a.jenisobatalkes_id
                   FROM obatalkes_m a) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN ( SELECT a.jenisobatalkes_id,
                    a.jenisobatalkes_nama
                   FROM jenisobatalkes_m a) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
             LEFT JOIN ( SELECT satuanunit_m_1.satuanunit_id,
                    satuanunit_m_1.satuanunit_nama
                   FROM satuanunit_m satuanunit_m_1) satuanunit_m ON obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id
             LEFT JOIN ( SELECT a.ruangan_id,
                    a.ruangan_nama
                   FROM ruangan_m a) ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id
             LEFT JOIN ( SELECT a.pegawai_id,
                    a.nama_pegawai
                   FROM pegawai_m a) dok_dpjp ON obatalkespasien_t.pegawai_id = dok_dpjp.pegawai_id
             JOIN ( SELECT a.obatsudahbayar_id,
                    a.pembayaranpelayanan_id
                   FROM obatsudahbayar_t a) obatsudahbayar_t ON obatsudahbayar_t.obatsudahbayar_id = obatalkespasien_t.obatsudahbayar_id
             JOIN ( SELECT a.pembayaranpelayanan_id,
                    a.pembayaran_id,
                    a.penjamin_id
                   FROM pembayaranpelayanan_t a) pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
          WHERE obatalkespasien_t.is_deleted = false
        UNION ALL
         SELECT diskon_obat.layanan_jenis,
            diskon_obat.pendaftaran_id,
            diskon_obat.tgl_pelayanan,
            diskon_obat.groupinacbg_id,
            diskon_obat.groupinacbg_nama,
            diskon_obat.tindakan_obat_kode,
            diskon_obat.tindakan_obat_id,
            diskon_obat.tindakan_obat,
            diskon_obat.kelompok,
            diskon_obat.qty,
            diskon_obat.harga_satuan,
            diskon_obat.tarif,
            diskon_obat.uom,
            diskon_obat.ruangan,
            diskon_obat.dokter,
            diskon_obat.is_akomodasi,
            diskon_obat.is_konsultasi,
            diskon_obat.additional_data,
            diskon_obat.kamarruangan_nokamar,
            diskon_obat.no_tempattidur,
            diskon_obat.kelaspelayanan_nama,
            diskon_obat.pembayaran_id,
            diskon_obat.tarif_dijamin,
            diskon_obat.tarif_dibayarkan,
            diskon_obat.tarifcyto_tindakan,
            diskon_obat.tarif_diskon,
            diskon_obat.cyto_tindakan,
            diskon_obat.is_visite,
            diskon_obat.kelompoktindakan_id,
            diskon_obat.tarifpenyulit_tindakan,
            diskon_obat.pasienadmisi_id,
            diskon_obat.penjamin_id,
            diskon_obat.penjamin_tinpelayanan_id,
            diskon_obat.penjamin_pelayanan_id,
            diskon_obat.is_diskon,
            diskon_obat.instalasi_id,
            diskon_obat.dijamin_payer,
            diskon_obat.dijamin_subpayer
           FROM ( SELECT \'obat\'::text AS layanan_jenis,
                    obatalkespasien_t.pendaftaran_id,
                    obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
                    NULL::integer AS groupinacbg_id,
                    NULL::character varying AS groupinacbg_nama,
                    obatalkes_m.obatalkes_kode AS tindakan_obat_kode,
                    obatalkes_m.obatalkes_id AS tindakan_obat_id,
                    obatalkes_m.obatalkes_nama AS tindakan_obat,
                    \'Drugs & Consumables\'::character varying AS kelompok,
                        CASE
                            WHEN obatalkespasien_t.det = 0::double precision THEN obatalkespasien_t.qty_oa
                            WHEN obatalkespasien_t.det IS NULL THEN obatalkespasien_t.qty_oa
                            ELSE obatalkespasien_t.det
                        END AS qty,
                    - obatalkespasien_t.tarif_diskon AS harga_satuan,
                    - obatalkespasien_t.tarif_diskon AS tarif,
                    satuanunit_m.satuanunit_nama AS uom,
                    ruangan_m.ruangan_nama AS ruangan,
                    dok_dpjp.nama_pegawai AS dokter,
                    false AS is_akomodasi,
                    false AS is_konsultasi,
                    obatalkespasien_t.additional_data,
                    NULL::character varying AS kamarruangan_nokamar,
                    NULL::character varying AS no_tempattidur,
                    NULL::character varying AS kelaspelayanan_nama,
                    pembayaranpelayanan_t.pembayaran_id,
                    obatalkespasien_t.tarif_dijamin,
                    obatalkespasien_t.tarif_dibayarkan,
                    0 AS tarifcyto_tindakan,
                    obatalkespasien_t.tarif_diskon,
                    false AS cyto_tindakan,
                    false AS is_visite,
                    NULL::integer AS kelompoktindakan_id,
                    0 AS tarifpenyulit_tindakan,
                    obatalkespasien_t.pasienadmisi_id,
                    obatalkespasien_t.penjamin_id,
                    obatalkespasien_t.penjamin_id AS penjamin_tinpelayanan_id,
                    pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                        CASE
                            WHEN COALESCE(obatalkespasien_t.tarif_diskon, 0::double precision) > 0::double precision THEN true
                            ELSE false
                        END AS is_diskon,
                    NULL::integer AS instalasi_id,
                    obatalkespasien_t.dijamin_payer,
                    obatalkespasien_t.dijamin_subpayer
                   FROM obatalkespasien_t
                     JOIN ( SELECT a.obatalkes_id,
                            a.obatalkes_kode,
                            a.obatalkes_nama,
                            a.groupinacbg_id,
                            a.jenisobatalkes_id
                           FROM obatalkes_m a) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
                     JOIN ( SELECT a.jenisobatalkes_id,
                            a.jenisobatalkes_nama
                           FROM jenisobatalkes_m a) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                     LEFT JOIN ( SELECT satuanunit_m_1.satuanunit_id,
                            satuanunit_m_1.satuanunit_nama
                           FROM satuanunit_m satuanunit_m_1) satuanunit_m ON obatalkespasien_t.satuankecil_id = satuanunit_m.satuanunit_id
                     LEFT JOIN ( SELECT a.ruangan_id,
                            a.ruangan_nama
                           FROM ruangan_m a) ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id
                     LEFT JOIN ( SELECT a.pegawai_id,
                            a.nama_pegawai
                           FROM pegawai_m a) dok_dpjp ON obatalkespasien_t.pegawai_id = dok_dpjp.pegawai_id
                     JOIN ( SELECT a.obatsudahbayar_id,
                            a.pembayaranpelayanan_id
                           FROM obatsudahbayar_t a) obatsudahbayar_t ON obatsudahbayar_t.obatsudahbayar_id = obatalkespasien_t.obatsudahbayar_id
                     JOIN ( SELECT a.pembayaranpelayanan_id,
                            a.pembayaran_id,
                            a.penjamin_id
                           FROM pembayaranpelayanan_t a) pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
                     JOIN ( SELECT a.is_invoice_diskon
                           FROM konfigtarif_k a) konfigtarif_k ON konfigtarif_k.is_invoice_diskon IS TRUE
                  WHERE obatalkespasien_t.is_deleted = false) diskon_obat
          WHERE diskon_obat.is_diskon IS TRUE) layanan ON pendaftaran_t.pendaftaran_id = layanan.pendaftaran_id;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m221102_063852_migrate_plafon_view_invoiceridetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m221102_063852_migrate_plafon_view_invoiceridetail_v cannot be reverted.\n";

        return false;
    }
    */
}
