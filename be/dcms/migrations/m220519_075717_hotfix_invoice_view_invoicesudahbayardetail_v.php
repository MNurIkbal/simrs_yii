<?php

use yii\db\Migration;

/**
 * Class m220519_075717_hotfix_invoice_view_invoicesudahbayardetail_v
 */
class m220519_075717_hotfix_invoice_view_invoicesudahbayardetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."invoicesudahbayardetail_v";
        ');

        $this->execute('
            CREATE VIEW "public"."invoicesudahbayardetail_v" AS  SELECT tagihan.pendaftaran_id,
                tagihan.pelayanan_id,
                tagihan.pasien_id,
                pasien_m.no_rekam_medik,
                    CASE
                        WHEN pasien_m.nama_pasien IS NULL THEN tagihan.nama_pembeli::character varying
                        ELSE pasien_m.nama_pasien
                    END AS nama_pasien,
                pasien_m.tanggal_lahir,
                tagihan.umur,
                jk.lookup_name AS jeniskelamin,
                tagihan.tgl_pendaftaran,
                tagihan.no_pendaftaran,
                tagihan.tindakan_obat_id,
                tagihan.tindakan_obat_nama,
                tagihan.is_obat,
                tagihan.tarif_satuan,
                tagihan.qty,
                tagihan.sub_total,
                tagihan.ruangan_id,
                ruangan_m.ruangan_nama AS ruangan_pelayanan,
                ruangan_m.instalasi_id,
                instalasi_m.instalasi_nama AS instalasi_pelayanan,
                tagihan.tgl_pelayanan,
                tagihan.kelaspelayanan_id,
                kelaspelayanan_m.kelaspelayanan_nama,
                tagihan.carabayar_tinpelayanan_id,
                carabayar_m.carabayar_nama AS carabayar_tinpelayanan,
                tagihan.penjamin_tinpelayanan_id,
                penjamin_m.penjamin_nama AS penjamin_tinpelayanan,
                tagihan.kelompoktindakan_id,
                tagihan.kelompoktindakan_nama,
                tagihan.jeniskasuspenyakit_id,
                tagihan.pembayaranpelayanan_id,
                tagihan.biaya_administrasi,
                tagihan.e_collection,
                tagihan.nama_pemrekening,
                tagihan.no_rekening,
                tagihan.carabayar_pelayanan_id,
                tagihan.carabayar_pelayanan,
                tagihan.penjamin_pelayanan_id,
                tagihan.penjamin_pelayanan,
                tagihan.tarif_cyto,
                tagihan.tandabuktibayar_id,
                tagihan.jeniskasuspenyakit_nama,
                tagihan.penjualanresep_id,
                tagihan.is_konsultasi,
                dok_tindakan.nama_pegawai AS dokter_tindakan,
                tagihan.pembayaran_id,
                tagihan.satuan_kecil AS uom,
                tagihan.tarif_dijamin,
                tagihan.tarif_dibayarkan,
                tagihan.groupinacbg_nama,
                tagihan.tarif_diskon,
                tagihan.is_visite,
                tagihan.tarifpenyulit_tindakan,
                tagihan.jenis_racikan,
                tagihan.tindakan_obat_kode,
                tagihan.is_akomodasi,
                tagihan.is_diskon
               FROM ( SELECT pendaftaran_t.pendaftaran_id,
                        tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
                        pendaftaran_t.pasien_id,
                        pendaftaran_t.tgl_pendaftaran,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.umur,
                        tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
                        daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
                        false AS is_obat, 
                        tindakanpelayanan_t.tarif_satuan,
                        tindakanpelayanan_t.qty_tindakan AS qty,
                        tindakanpelayanan_t.tarifcyto_tindakan,
                        tindakanpelayanan_t.tarif_tindakan AS sub_total,
                        tindakanpelayanan_t.ruangan_id,
                        tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
                        tindakanpelayanan_t.kelaspelayanan_id,
                        tindakanpelayanan_t.carabayar_id AS carabayar_tinpelayanan_id,
                        tindakanpelayanan_t.penjamin_id AS penjamin_tinpelayanan_id,
                        daftartindakan_m.kelompoktindakan_id,
                            CASE
                                WHEN daftartindakan_m.is_konsultasi = true THEN kelompoktindakan_m.kelompoktindakan_nama
                                ELSE kelompoktindakan_m.kelompoktindakan_nama
                            END AS kelompoktindakan_nama,
                        pendaftaran_t.jeniskasuspenyakit_id,
                        pembayaranpelayanan_t.pembayaranpelayanan_id,
                        pembayaranpelayanan_t.biaya_administrasi,
                        pembayaranpelayanan_t.e_collection,
                        pembayaranpelayanan_t.nama_pemrekening,
                        pembayaranpelayanan_t.no_rekening,
                        pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
                        carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
                        pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                        penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
                        tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
                        pembayaranpelayanan_t.tandabuktibayar_id,
                        jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                        0 AS penjualanresep_id,
                        NULL::text AS nama_pembeli,
                        daftartindakan_m.is_konsultasi,
                        tindakanpelayanan_t.dokterpenanggungjawab_id AS doktertindakan_id,
                        pembayaranpelayanan_t.pembayaran_id,
                        NULL::text AS satuan_kecil,
                        tindakanpelayanan_t.tarif_dijamin,
                        tindakanpelayanan_t.tarif_dibayarkan,
                        groupinacbg_m.groupinacbg_nama,
                        tindakanpelayanan_t.tarif_diskon,
                            CASE
                                WHEN daftartindakan_m.daftartindakan_id = 99993 THEN true
                                ELSE false
                            END AS is_visite,
                        tindakanpelayanan_t.tarifpenyulit_tindakan,
                        NULL::text AS jenis_racikan,
                        daftartindakan_m.daftartindakan_kode AS tindakan_obat_kode,
                        daftartindakan_m.is_akomodasi,
                        false AS is_diskon
                       FROM pendaftaran_t
                         JOIN ( SELECT a.jeniskasuspenyakit_id,
                                a.jeniskasuspenyakit_nama
                               FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
                         JOIN ( SELECT a.tindakanpelayanan_id,
                                a.pendaftaran_id,
                                a.daftartindakan_id,
                                a.tarif_satuan,
                                a.qty_tindakan,
                                a.tarifcyto_tindakan,
                                a.tarif_tindakan,
                                a.ruangan_id,
                                a.tgl_tindakan,
                                a.kelaspelayanan_id,
                                a.carabayar_id,
                                a.penjamin_id,
                                a.dokterpenanggungjawab_id,
                                a.tarif_dijamin,
                                a.tarif_dibayarkan,
                                a.tarif_diskon,
                                a.tarifpenyulit_tindakan,
                                a.tindakansudahbayar_id,
                                a.is_deleted,
                                a.parent_id
                               FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.parent_id IS NULL
                         JOIN ( SELECT a.daftartindakan_id,
                                a.daftartindakan_nama,
                                a.kelompoktindakan_id,
                                a.is_konsultasi,
                                a.daftartindakan_kode,
                                a.is_akomodasi,
                                a.groupinacbg_id
                               FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                         JOIN ( SELECT a.tindakansudahbayar_id,
                                a.pembayaranpelayanan_id
                               FROM tindakansudahbayar_t a) tindakansudahbayar_t ON tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
                         JOIN ( SELECT a.kelompoktindakan_id,
                                a.kelompoktindakan_nama
                               FROM kelompoktindakan_m a) kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
                         JOIN ( SELECT a.pembayaranpelayanan_id,
                                a.biaya_administrasi,
                                a.e_collection,
                                a.nama_pemrekening,
                                a.no_rekening,
                                a.carabayar_id,
                                a.penjamin_id,
                                a.tandabuktibayar_id,
                                a.pembayaran_id
                               FROM pembayaranpelayanan_t a) pembayaranpelayanan_t ON tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
                         JOIN ( SELECT a.carabayar_id,
                                a.carabayar_nama
                               FROM carabayar_m a) carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
                         JOIN ( SELECT a.penjamin_id,
                                a.penjamin_nama
                               FROM penjamin_m a) penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
                         LEFT JOIN ( SELECT a.groupinacbg_id,
                                a.groupinacbg_nama
                               FROM groupinacbg_m a) groupinacbg_m ON daftartindakan_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
                      WHERE tindakanpelayanan_t.is_deleted IS FALSE
                    UNION ALL
                     SELECT pendaftaran_t.pendaftaran_id,
                        tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
                        pendaftaran_t.pasien_id,
                        pendaftaran_t.tgl_pendaftaran,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.umur,
                        tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
                        daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
                        false AS is_obat,
                        - tindakanpelayanan_t.tarif_diskon AS tarif_satuan,
                        tindakanpelayanan_t.qty_tindakan AS qty,
                        tindakanpelayanan_t.tarifcyto_tindakan,
                        - tindakanpelayanan_t.tarif_diskon AS sub_total,
                        tindakanpelayanan_t.ruangan_id,
                        tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
                        tindakanpelayanan_t.kelaspelayanan_id,
                        tindakanpelayanan_t.carabayar_id AS carabayar_tinpelayanan_id,
                        tindakanpelayanan_t.penjamin_id AS penjamin_tinpelayanan_id,
                        daftartindakan_m.kelompoktindakan_id,
                            CASE
                                WHEN daftartindakan_m.is_konsultasi = true THEN kelompoktindakan_m.kelompoktindakan_nama
                                ELSE kelompoktindakan_m.kelompoktindakan_nama
                            END AS kelompoktindakan_nama,
                        pendaftaran_t.jeniskasuspenyakit_id,
                        pembayaranpelayanan_t.pembayaranpelayanan_id,
                        pembayaranpelayanan_t.biaya_administrasi,
                        pembayaranpelayanan_t.e_collection,
                        pembayaranpelayanan_t.nama_pemrekening,
                        pembayaranpelayanan_t.no_rekening,
                        pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
                        carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
                        pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                        penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
                        tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
                        pembayaranpelayanan_t.tandabuktibayar_id,
                        jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                        0 AS penjualanresep_id,
                        NULL::text AS nama_pembeli,
                        daftartindakan_m.is_konsultasi,
                        tindakanpelayanan_t.dokterpenanggungjawab_id AS doktertindakan_id,
                        pembayaranpelayanan_t.pembayaran_id,
                        NULL::text AS satuan_kecil,
                        tindakanpelayanan_t.tarif_dijamin,
                        tindakanpelayanan_t.tarif_dibayarkan,
                        groupinacbg_m.groupinacbg_nama,
                        tindakanpelayanan_t.tarif_diskon,
                            CASE
                                WHEN daftartindakan_m.daftartindakan_id = 99993 THEN true
                                ELSE false
                            END AS is_visite,
                        tindakanpelayanan_t.tarifpenyulit_tindakan,
                        NULL::text AS jenis_racikan,
                        daftartindakan_m.daftartindakan_kode AS tindakan_obat_kode,
                        daftartindakan_m.is_akomodasi,
                        true AS is_diskon
                       FROM pendaftaran_t
                         JOIN ( SELECT a.jeniskasuspenyakit_id,
                                a.jeniskasuspenyakit_nama
                               FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
                         JOIN ( SELECT a.tindakanpelayanan_id,
                                a.pendaftaran_id,
                                a.daftartindakan_id,
                                a.tarif_satuan,
                                a.qty_tindakan,
                                a.tarifcyto_tindakan,
                                a.tarif_tindakan,
                                a.ruangan_id,
                                a.tgl_tindakan,
                                a.kelaspelayanan_id,
                                a.carabayar_id,
                                a.penjamin_id,
                                a.dokterpenanggungjawab_id,
                                a.tarif_dijamin,
                                a.tarif_dibayarkan,
                                a.tarif_diskon,
                                a.tarifpenyulit_tindakan,
                                a.tindakansudahbayar_id,
                                a.is_deleted,
                                a.parent_id
                               FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.parent_id IS NULL
                         JOIN ( SELECT a.daftartindakan_id,
                                a.daftartindakan_nama,
                                a.kelompoktindakan_id,
                                a.is_konsultasi,
                                a.daftartindakan_kode,
                                a.is_akomodasi,
                                a.groupinacbg_id
                               FROM daftartindakan_m a) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
                         JOIN ( SELECT a.tindakansudahbayar_id,
                                a.pembayaranpelayanan_id
                               FROM tindakansudahbayar_t a) tindakansudahbayar_t ON tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
                         JOIN ( SELECT a.kelompoktindakan_id,
                                a.kelompoktindakan_nama
                               FROM kelompoktindakan_m a) kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
                         JOIN ( SELECT a.pembayaranpelayanan_id,
                                a.biaya_administrasi,
                                a.e_collection,
                                a.nama_pemrekening,
                                a.no_rekening,
                                a.carabayar_id,
                                a.penjamin_id,
                                a.tandabuktibayar_id,
                                a.pembayaran_id
                               FROM pembayaranpelayanan_t a) pembayaranpelayanan_t ON tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
                         JOIN ( SELECT a.carabayar_id,
                                a.carabayar_nama
                               FROM carabayar_m a) carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
                         JOIN ( SELECT a.penjamin_id,
                                a.penjamin_nama
                               FROM penjamin_m a) penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
                         LEFT JOIN ( SELECT a.groupinacbg_id,
                                a.groupinacbg_nama
                               FROM groupinacbg_m a) groupinacbg_m ON daftartindakan_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
                         JOIN ( SELECT a.is_invoice_diskon
                               FROM konfigtarif_k a) konfigtarif_k ON konfigtarif_k.is_invoice_diskon IS TRUE
                      WHERE tindakanpelayanan_t.tarif_diskon <> 0::double precision AND tindakanpelayanan_t.is_deleted IS FALSE
                    UNION ALL
                     SELECT pendaftaran_t.pendaftaran_id,
                        tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
                        pendaftaran_t.pasien_id,
                        pendaftaran_t.tgl_pendaftaran,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.umur,
                        tindakanpelayanan_t.tipepaket_id AS tindakan_obat_id,
                        tipepaket_m.tipepaket_nama AS tindakan_obat_nama,
                        false AS is_obat,
                        tindakanpelayanan_t.tarif_satuan,
                        tindakanpelayanan_t.qty_tindakan AS qty,
                        tindakanpelayanan_t.tarifcyto_tindakan,
                        tindakanpelayanan_t.tarif_tindakan AS sub_total,
                        tindakanpelayanan_t.ruangan_id,
                        tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
                        tindakanpelayanan_t.kelaspelayanan_id,
                        tindakanpelayanan_t.carabayar_id AS carabayar_tinpelayanan_id,
                        tindakanpelayanan_t.penjamin_id AS penjamin_tinpelayanan_id,
                        NULL::integer AS kelompoktindakan_id,
                            CASE
                                WHEN tipepaket_m.is_mcu IS TRUE THEN \'kelompok_paket_mcu\'::text
                                ELSE \'kelompok_paket\'::text
                            END AS kelompoktindakan_nama,
                        pendaftaran_t.jeniskasuspenyakit_id,
                        pembayaranpelayanan_t.pembayaranpelayanan_id,
                        pembayaranpelayanan_t.biaya_administrasi,
                        pembayaranpelayanan_t.e_collection,
                        pembayaranpelayanan_t.nama_pemrekening,
                        pembayaranpelayanan_t.no_rekening,
                        pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
                        carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
                        pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                        penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
                        tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
                        pembayaranpelayanan_t.tandabuktibayar_id,
                        jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                        0 AS penjualanresep_id,
                        NULL::text AS nama_pembeli,
                        NULL::boolean AS is_konsultasi,
                        tindakanpelayanan_t.dokterpenanggungjawab_id AS doktertindakan_id,
                        pembayaranpelayanan_t.pembayaran_id,
                        NULL::text AS satuan_kecil,
                        tindakanpelayanan_t.tarif_dijamin,
                        tindakanpelayanan_t.tarif_dibayarkan,
                        NULL::character varying AS groupinacbg_nama,
                        tindakanpelayanan_t.tarif_diskon,
                        false AS is_visite,
                        tindakanpelayanan_t.tarifpenyulit_tindakan,
                        NULL::text AS jenis_racikan,
                        tipepaket_m.tipepaket_kode AS tindakan_obat_kode,
                        false AS is_akomodasi,
                        false AS is_diskon
                       FROM pendaftaran_t
                         JOIN ( SELECT a.jeniskasuspenyakit_id,
                                a.jeniskasuspenyakit_nama
                               FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
                         JOIN ( SELECT a.tindakanpelayanan_id,
                                a.pendaftaran_id,
                                a.daftartindakan_id,
                                a.tarif_satuan,
                                a.qty_tindakan,
                                a.tarifcyto_tindakan,
                                a.tarif_tindakan,
                                a.ruangan_id,
                                a.tgl_tindakan,
                                a.kelaspelayanan_id,
                                a.carabayar_id,
                                a.penjamin_id,
                                a.dokterpenanggungjawab_id,
                                a.tarif_dijamin,
                                a.tarif_dibayarkan,
                                a.tarif_diskon,
                                a.tarifpenyulit_tindakan,
                                a.tindakansudahbayar_id,
                                a.is_deleted,
                                a.parent_id,
                                a.tipepaket_id
                               FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.parent_id IS NULL
                         JOIN ( SELECT a.tipepaket_id,
                                a.tipepaket_nama,
                                a.is_mcu,
                                a.tipepaket_kode
                               FROM tipepaket_m a) tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
                         JOIN ( SELECT a.tindakansudahbayar_id,
                                a.pembayaranpelayanan_id
                               FROM tindakansudahbayar_t a) tindakansudahbayar_t ON tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
                         JOIN ( SELECT a.pembayaranpelayanan_id,
                                a.biaya_administrasi,
                                a.e_collection,
                                a.nama_pemrekening,
                                a.no_rekening,
                                a.carabayar_id,
                                a.penjamin_id,
                                a.tandabuktibayar_id,
                                a.pembayaran_id
                               FROM pembayaranpelayanan_t a) pembayaranpelayanan_t ON tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
                         JOIN ( SELECT a.carabayar_id,
                                a.carabayar_nama
                               FROM carabayar_m a) carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
                         JOIN ( SELECT a.penjamin_id,
                                a.penjamin_nama
                               FROM penjamin_m a) penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
                      WHERE tindakanpelayanan_t.is_deleted = false
                    UNION ALL
                     SELECT pendaftaran_t.pendaftaran_id,
                        tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
                        pendaftaran_t.pasien_id,
                        pendaftaran_t.tgl_pendaftaran,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.umur,
                        tindakanpelayanan_t.tipepaket_id AS tindakan_obat_id,
                        tipepaket_m.tipepaket_nama AS tindakan_obat_nama,
                        false AS is_obat,
                        - tindakanpelayanan_t.tarif_diskon AS tarif_satuan,
                        tindakanpelayanan_t.qty_tindakan AS qty,
                        tindakanpelayanan_t.tarifcyto_tindakan,
                        - tindakanpelayanan_t.tarif_diskon AS sub_total,
                        tindakanpelayanan_t.ruangan_id,
                        tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
                        tindakanpelayanan_t.kelaspelayanan_id,
                        tindakanpelayanan_t.carabayar_id AS carabayar_tinpelayanan_id,
                        tindakanpelayanan_t.penjamin_id AS penjamin_tinpelayanan_id,
                        NULL::integer AS kelompoktindakan_id,
                            CASE
                                WHEN tipepaket_m.is_mcu IS TRUE THEN \'kelompok_paket_mcu\'::text
                                ELSE \'kelompok_paket\'::text
                            END AS kelompoktindakan_nama,
                        pendaftaran_t.jeniskasuspenyakit_id,
                        pembayaranpelayanan_t.pembayaranpelayanan_id,
                        pembayaranpelayanan_t.biaya_administrasi,
                        pembayaranpelayanan_t.e_collection,
                        pembayaranpelayanan_t.nama_pemrekening,
                        pembayaranpelayanan_t.no_rekening,
                        pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
                        carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
                        pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                        penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
                        tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
                        pembayaranpelayanan_t.tandabuktibayar_id,
                        jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                        0 AS penjualanresep_id,
                        NULL::text AS nama_pembeli,
                        NULL::boolean AS is_konsultasi,
                        tindakanpelayanan_t.dokterpenanggungjawab_id AS doktertindakan_id,
                        pembayaranpelayanan_t.pembayaran_id,
                        NULL::text AS satuan_kecil,
                        tindakanpelayanan_t.tarif_dijamin,
                        tindakanpelayanan_t.tarif_dibayarkan,
                        NULL::character varying AS groupinacbg_nama,
                        tindakanpelayanan_t.tarif_diskon,
                        false AS is_visite,
                        tindakanpelayanan_t.tarifpenyulit_tindakan,
                        NULL::text AS jenis_racikan,
                        tipepaket_m.tipepaket_kode AS tindakan_obat_kode,
                        false AS is_akomodasi,
                        true AS is_diskon
                       FROM pendaftaran_t
                         JOIN ( SELECT a.jeniskasuspenyakit_id,
                                a.jeniskasuspenyakit_nama
                               FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
                         JOIN ( SELECT a.tindakanpelayanan_id,
                                a.pendaftaran_id,
                                a.daftartindakan_id,
                                a.tarif_satuan,
                                a.qty_tindakan,
                                a.tarifcyto_tindakan,
                                a.tarif_tindakan,
                                a.ruangan_id,
                                a.tgl_tindakan,
                                a.kelaspelayanan_id,
                                a.carabayar_id,
                                a.penjamin_id,
                                a.dokterpenanggungjawab_id,
                                a.tarif_dijamin,
                                a.tarif_dibayarkan,
                                a.tarif_diskon,
                                a.tarifpenyulit_tindakan,
                                a.tindakansudahbayar_id,
                                a.is_deleted,
                                a.parent_id,
                                a.tipepaket_id
                               FROM tindakanpelayanan_t a) tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id AND tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.parent_id IS NULL
                         JOIN ( SELECT a.tipepaket_id,
                                a.tipepaket_nama,
                                a.is_mcu,
                                a.tipepaket_kode
                               FROM tipepaket_m a) tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
                         JOIN ( SELECT a.tindakansudahbayar_id,
                                a.pembayaranpelayanan_id
                               FROM tindakansudahbayar_t a) tindakansudahbayar_t ON tindakanpelayanan_t.tindakansudahbayar_id = tindakansudahbayar_t.tindakansudahbayar_id
                         JOIN ( SELECT a.pembayaranpelayanan_id,
                                a.biaya_administrasi,
                                a.e_collection,
                                a.nama_pemrekening,
                                a.no_rekening,
                                a.carabayar_id,
                                a.penjamin_id,
                                a.tandabuktibayar_id,
                                a.pembayaran_id
                               FROM pembayaranpelayanan_t a) pembayaranpelayanan_t ON tindakansudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
                         JOIN ( SELECT a.carabayar_id,
                                a.carabayar_nama
                               FROM carabayar_m a) carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
                         JOIN ( SELECT a.penjamin_id,
                                a.penjamin_nama
                               FROM penjamin_m a) penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
                         JOIN ( SELECT a.is_invoice_diskon
                               FROM konfigtarif_k a) konfigtarif_k ON konfigtarif_k.is_invoice_diskon IS TRUE
                      WHERE tindakanpelayanan_t.is_deleted = false AND COALESCE(tindakanpelayanan_t.tarif_diskon, 0::double precision) <> 0::double precision
                    UNION ALL
                     SELECT pendaftaran_t.pendaftaran_id,
                        obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
                        pendaftaran_t.pasien_id,
                        pendaftaran_t.tgl_pendaftaran,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.umur,
                        obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
                        obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
                        true AS is_obat,
                        obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
                            CASE
                                WHEN obatalkespasien_t.det = 0::double precision THEN obatalkespasien_t.qty_oa
                                WHEN obatalkespasien_t.det IS NULL THEN obatalkespasien_t.qty_oa
                                ELSE obatalkespasien_t.det
                            END AS qty,
                        obatalkespasien_t.tarifcyto AS tarifcyto_tindakan,
                        obatalkespasien_t.hargajual_oa AS sub_total,
                        obatalkespasien_t.ruangan_id,
                        obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
                        obatalkespasien_t.kelaspelayanan_id,
                        obatalkespasien_t.carabayar_id AS carabayar_tinpelayanan_id,
                        obatalkespasien_t.penjamin_id AS penjamin_tinpelayanan_id,
                        NULL::integer AS kelompoktindakan_id,
                        \'Drugs & Consumables\'::character varying AS kelompoktindakan_nama,
                        pendaftaran_t.jeniskasuspenyakit_id,
                        pembayaranpelayanan_t.pembayaranpelayanan_id,
                        pembayaranpelayanan_t.biaya_administrasi,
                        pembayaranpelayanan_t.e_collection,
                        pembayaranpelayanan_t.nama_pemrekening,
                        pembayaranpelayanan_t.no_rekening,
                        pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
                        carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
                        pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                        penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
                        obatalkespasien_t.tarifcyto AS tarif_cyto,
                        pembayaranpelayanan_t.tandabuktibayar_id,
                        jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                        0 AS penjualanresep_id,
                        NULL::text AS nama_pembeli,
                        NULL::boolean AS is_konsultasi,
                        NULL::bigint AS doktertindakan_id,
                        pembayaranpelayanan_t.pembayaran_id,
                        satuan_kecil.satuanunit_nama AS satuan_kecil,
                        obatalkespasien_t.tarif_dijamin,
                        obatalkespasien_t.tarif_dibayarkan,
                        groupinacbg_m.groupinacbg_nama,
                        obatalkespasien_t.tarif_diskon,
                        false AS is_visite,
                        0 AS tarifpenyulit_tindakan,
                            CASE COALESCE(obatalkespasien_t.racikan_id, 0)
                                WHEN 0 THEN \'Non Racikan\'::text
                                ELSE \'Racikan\'::text
                            END AS jenis_racikan,
                        obatalkes_m.obatalkes_kode AS tindakan_obat_kode,
                        false AS is_akomodasi,
                        false AS is_diskon
                       FROM pendaftaran_t
                         JOIN ( SELECT a.jeniskasuspenyakit_id,
                                a.jeniskasuspenyakit_nama
                               FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
                         JOIN ( SELECT a.obatalkespasien_id,
                                a.pendaftaran_id,
                                a.satuankecil_id,
                                a.obatalkes_id,
                                a.hargasatuan_oa,
                                a.det,
                                a.qty_oa,
                                a.hargajual_oa,
                                a.ruangan_id,
                                a.tglpelayanan,
                                a.kelaspelayanan_id,
                                a.carabayar_id,
                                a.penjamin_id,
                                a.tarifcyto,
                                a.tarif_dijamin,
                                a.tarif_dibayarkan,
                                a.tarif_diskon,
                                a.racikan_id,
                                a.obatsudahbayar_id,
                                a.is_deleted
                               FROM obatalkespasien_t a) obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
                         JOIN ( SELECT a.obatalkes_id,
                                a.obatalkes_kode,
                                a.obatalkes_nama,
                                a.groupinacbg_id
                               FROM obatalkes_m a) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
                         JOIN ( SELECT a.obatsudahbayar_id,
                                a.pembayaranpelayanan_id
                               FROM obatsudahbayar_t a) obatsudahbayar_t ON obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
                         JOIN ( SELECT a.pembayaranpelayanan_id,
                                a.biaya_administrasi,
                                a.e_collection,
                                a.nama_pemrekening,
                                a.no_rekening,
                                a.carabayar_id,
                                a.penjamin_id,
                                a.tandabuktibayar_id,
                                a.pembayaran_id
                               FROM pembayaranpelayanan_t a) pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
                         JOIN ( SELECT a.carabayar_id,
                                a.carabayar_nama
                               FROM carabayar_m a) carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
                         JOIN ( SELECT a.penjamin_id,
                                a.penjamin_nama
                               FROM penjamin_m a) penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
                         LEFT JOIN ( SELECT satuanunit_m.satuanunit_id,
                                satuanunit_m.satuanunit_nama
                               FROM satuanunit_m) satuan_kecil ON obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id
                         LEFT JOIN ( SELECT a.groupinacbg_id,
                                a.groupinacbg_nama
                               FROM groupinacbg_m a) groupinacbg_m ON obatalkes_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
                      WHERE obatalkespasien_t.is_deleted = false
                    UNION ALL
                     SELECT pendaftaran_t.pendaftaran_id,
                        obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
                        pendaftaran_t.pasien_id,
                        pendaftaran_t.tgl_pendaftaran,
                        pendaftaran_t.no_pendaftaran,
                        pendaftaran_t.umur,
                        obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
                        obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
                        true AS is_obat,
                        - obatalkespasien_t.tarif_diskon AS tarif_satuan,
                            CASE
                                WHEN obatalkespasien_t.det = 0::double precision THEN obatalkespasien_t.qty_oa
                                WHEN obatalkespasien_t.det IS NULL THEN obatalkespasien_t.qty_oa
                                ELSE obatalkespasien_t.det
                            END AS qty,
                        obatalkespasien_t.tarifcyto AS tarifcyto_tindakan,
                        - obatalkespasien_t.tarif_diskon AS sub_total,
                        obatalkespasien_t.ruangan_id,
                        obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
                        obatalkespasien_t.kelaspelayanan_id,
                        obatalkespasien_t.carabayar_id AS carabayar_tinpelayanan_id,
                        obatalkespasien_t.penjamin_id AS penjamin_tinpelayanan_id,
                        NULL::integer AS kelompoktindakan_id,
                        \'Drugs & Consumables\'::character varying AS kelompoktindakan_nama,
                        pendaftaran_t.jeniskasuspenyakit_id,
                        pembayaranpelayanan_t.pembayaranpelayanan_id,
                        pembayaranpelayanan_t.biaya_administrasi,
                        pembayaranpelayanan_t.e_collection,
                        pembayaranpelayanan_t.nama_pemrekening,
                        pembayaranpelayanan_t.no_rekening,
                        pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
                        carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
                        pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                        penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
                        obatalkespasien_t.tarifcyto AS tarif_cyto,
                        pembayaranpelayanan_t.tandabuktibayar_id,
                        jeniskasuspenyakit_m.jeniskasuspenyakit_nama,
                        0 AS penjualanresep_id,
                        NULL::text AS nama_pembeli,
                        NULL::boolean AS is_konsultasi,
                        NULL::bigint AS doktertindakan_id,
                        pembayaranpelayanan_t.pembayaran_id,
                        satuan_kecil.satuanunit_nama AS satuan_kecil,
                        obatalkespasien_t.tarif_dijamin,
                        obatalkespasien_t.tarif_dibayarkan,
                        groupinacbg_m.groupinacbg_nama,
                        obatalkespasien_t.tarif_diskon,
                        false AS is_visite,
                        0 AS tarifpenyulit_tindakan,
                            CASE COALESCE(obatalkespasien_t.racikan_id, 0)
                                WHEN 0 THEN \'Non Racikan\'::text
                                ELSE \'Racikan\'::text
                            END AS jenis_racikan,
                        obatalkes_m.obatalkes_kode AS tindakan_obat_kode,
                        false AS is_akomodasi,
                        true AS is_diskon
                       FROM pendaftaran_t
                         JOIN ( SELECT a.jeniskasuspenyakit_id,
                                a.jeniskasuspenyakit_nama
                               FROM jeniskasuspenyakit_m a) jeniskasuspenyakit_m ON pendaftaran_t.jeniskasuspenyakit_id = jeniskasuspenyakit_m.jeniskasuspenyakit_id
                         JOIN ( SELECT a.obatalkespasien_id,
                                a.pendaftaran_id,
                                a.satuankecil_id,
                                a.obatalkes_id,
                                a.hargasatuan_oa,
                                a.det,
                                a.qty_oa,
                                a.hargajual_oa,
                                a.ruangan_id,
                                a.tglpelayanan,
                                a.kelaspelayanan_id,
                                a.carabayar_id,
                                a.penjamin_id,
                                a.tarifcyto,
                                a.tarif_dijamin,
                                a.tarif_dibayarkan,
                                a.tarif_diskon,
                                a.racikan_id,
                                a.obatsudahbayar_id,
                                a.is_deleted
                               FROM obatalkespasien_t a) obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
                         JOIN ( SELECT a.obatalkes_id,
                                a.obatalkes_kode,
                                a.obatalkes_nama,
                                a.groupinacbg_id
                               FROM obatalkes_m a) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
                         JOIN ( SELECT a.obatsudahbayar_id,
                                a.pembayaranpelayanan_id
                               FROM obatsudahbayar_t a) obatsudahbayar_t ON obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
                         JOIN ( SELECT a.pembayaranpelayanan_id,
                                a.biaya_administrasi,
                                a.e_collection,
                                a.nama_pemrekening,
                                a.no_rekening,
                                a.carabayar_id,
                                a.penjamin_id,
                                a.tandabuktibayar_id,
                                a.pembayaran_id
                               FROM pembayaranpelayanan_t a) pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
                         JOIN ( SELECT a.carabayar_id,
                                a.carabayar_nama
                               FROM carabayar_m a) carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
                         JOIN ( SELECT a.penjamin_id,
                                a.penjamin_nama
                               FROM penjamin_m a) penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
                         LEFT JOIN ( SELECT satuanunit_m.satuanunit_id,
                                satuanunit_m.satuanunit_nama
                               FROM satuanunit_m) satuan_kecil ON obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id
                         LEFT JOIN ( SELECT a.groupinacbg_id,
                                a.groupinacbg_nama
                               FROM groupinacbg_m a) groupinacbg_m ON obatalkes_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
                         JOIN ( SELECT a.is_invoice_diskon
                               FROM konfigtarif_k a) konfigtarif_k ON konfigtarif_k.is_invoice_diskon IS TRUE
                      WHERE obatalkespasien_t.is_deleted = false AND COALESCE(obatalkespasien_t.tarif_diskon, 0::double precision) <> 0::double precision
                    UNION ALL
                     SELECT penjualanresep_t.pendaftaran_id,
                        obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
                        penjualanresep_t.pasien_id,
                        penjualanresep_t.tglresep AS tgl_pendaftaran,
                        penjualanresep_t.noresep AS no_pendaftaran,
                        NULL::character varying AS umur,
                        obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
                        obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
                        true AS is_obat,
                        obatalkespasien_t.hargasatuan_oa AS tarif_satuan,
                        obatalkespasien_t.qty_oa AS qty,
                        obatalkespasien_t.tarifcyto AS tarifcyto_tindakan,
                        obatalkespasien_t.hargajual_oa AS sub_total,
                        obatalkespasien_t.ruangan_id,
                        obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
                        obatalkespasien_t.kelaspelayanan_id,
                        obatalkespasien_t.carabayar_id AS carabayar_tinpelayanan_id,
                        obatalkespasien_t.penjamin_id AS penjamin_tinpelayanan_id,
                        NULL::integer AS kelompoktindakan_id,
                        \'kelompok_obat\'::character varying AS kelompoktindakan_nama,
                        NULL::integer AS jeniskasuspenyakit_id,
                        pembayaranpelayanan_t.pembayaranpelayanan_id,
                        pembayaranpelayanan_t.biaya_administrasi,
                        pembayaranpelayanan_t.e_collection,
                        pembayaranpelayanan_t.nama_pemrekening,
                        pembayaranpelayanan_t.no_rekening,
                        pembayaranpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
                        carabayar_m_1.carabayar_nama AS carabayar_pelayanan,
                        pembayaranpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                        penjamin_m_1.penjamin_nama AS penjamin_pelayanan,
                        obatalkespasien_t.tarifcyto AS tarif_cyto,
                        pembayaranpelayanan_t.tandabuktibayar_id,
                        NULL::character varying AS jeniskasuspenyakit_nama,
                        obatalkespasien_t.penjualanresep_id,
                        penjualanresep_t.nama_pembeli,
                        NULL::boolean AS is_konsultasi,
                        NULL::bigint AS doktertindakan_id,
                        pembayaranpelayanan_t.pembayaran_id,
                        satuan_kecil.satuanunit_nama AS satuan_kecil,
                        obatalkespasien_t.tarif_dijamin,
                        obatalkespasien_t.tarif_dibayarkan,
                        groupinacbg_m.groupinacbg_nama,
                        obatalkespasien_t.tarif_diskon,
                        false AS is_visite,
                        0 AS tarifpenyulit_tindakan,
                            CASE COALESCE(obatalkespasien_t.racikan_id, 0)
                                WHEN 0 THEN \'Non Racikan\'::text
                                ELSE \'Racikan\'::text
                            END AS jenis_racikan,
                        obatalkes_m.obatalkes_kode AS tindakan_obat_kode,
                        false AS is_akomodasi,
                        false AS is_diskon
                       FROM obatalkespasien_t
                         JOIN ( SELECT a.penjualanresep_id,
                                a.pendaftaran_id,
                                a.pasien_id,
                                a.tglresep,
                                a.noresep,
                                a.nama_pembeli,
                                a.jenispenjualan
                               FROM penjualanresep_t a) penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id
                         JOIN ( SELECT a.obatalkes_id,
                                a.obatalkes_kode,
                                a.obatalkes_nama,
                                a.groupinacbg_id
                               FROM obatalkes_m a) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
                         JOIN ( SELECT a.obatsudahbayar_id,
                                a.pembayaranpelayanan_id
                               FROM obatsudahbayar_t a) obatsudahbayar_t ON obatalkespasien_t.obatsudahbayar_id = obatsudahbayar_t.obatsudahbayar_id
                         JOIN ( SELECT a.pembayaranpelayanan_id,
                                a.biaya_administrasi,
                                a.e_collection,
                                a.nama_pemrekening,
                                a.no_rekening,
                                a.carabayar_id,
                                a.penjamin_id,
                                a.tandabuktibayar_id,
                                a.pembayaran_id
                               FROM pembayaranpelayanan_t a) pembayaranpelayanan_t ON obatsudahbayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id
                         JOIN ( SELECT a.carabayar_id,
                                a.carabayar_nama
                               FROM carabayar_m a) carabayar_m_1 ON pembayaranpelayanan_t.carabayar_id = carabayar_m_1.carabayar_id
                         JOIN ( SELECT a.penjamin_id,
                                a.penjamin_nama
                               FROM penjamin_m a) penjamin_m_1 ON pembayaranpelayanan_t.penjamin_id = penjamin_m_1.penjamin_id
                         LEFT JOIN ( SELECT satuanunit_m.satuanunit_id,
                                satuanunit_m.satuanunit_nama
                               FROM satuanunit_m) satuan_kecil ON obatalkespasien_t.satuankecil_id = satuan_kecil.satuanunit_id
                         LEFT JOIN ( SELECT a.groupinacbg_id,
                                a.groupinacbg_nama
                               FROM groupinacbg_m a) groupinacbg_m ON obatalkes_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
                      WHERE penjualanresep_t.jenispenjualan::integer <> 344) tagihan
                 LEFT JOIN ( SELECT a.ruangan_id,
                        a.ruangan_nama,
                        a.instalasi_id
                       FROM ruangan_m a) ruangan_m ON tagihan.ruangan_id = ruangan_m.ruangan_id
                 LEFT JOIN ( SELECT a.instalasi_nama,
                        a.instalasi_id
                       FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
                 LEFT JOIN ( SELECT a.kelaspelayanan_id,
                        a.kelaspelayanan_nama
                       FROM kelaspelayanan_m a) kelaspelayanan_m ON tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
                 LEFT JOIN ( SELECT a.carabayar_id,
                        a.carabayar_nama
                       FROM carabayar_m a) carabayar_m ON tagihan.carabayar_tinpelayanan_id = carabayar_m.carabayar_id
                 LEFT JOIN ( SELECT a.penjamin_id,
                        a.penjamin_nama
                       FROM penjamin_m a) penjamin_m ON tagihan.penjamin_tinpelayanan_id = penjamin_m.penjamin_id
                 LEFT JOIN ( SELECT a.pasien_id,
                        a.no_rekam_medik,
                        a.nama_pasien,
                        a.tanggal_lahir,
                        a.jeniskelamin
                       FROM pasien_m a) pasien_m ON tagihan.pasien_id = pasien_m.pasien_id
                 LEFT JOIN ( SELECT pegawai_m.pegawai_id,
                        pegawai_m.nama_pegawai
                       FROM pegawai_m) dok_tindakan ON tagihan.doktertindakan_id = dok_tindakan.pegawai_id
                 LEFT JOIN ( SELECT a.lookup_id,
                        a.lookup_name
                       FROM lookup_m a) jk ON pasien_m.jeniskelamin::integer = jk.lookup_id;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220519_075717_hotfix_invoice_view_invoicesudahbayardetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220519_075717_hotfix_invoice_view_invoicesudahbayardetail_v cannot be reverted.\n";

        return false;
    }
    */
}
