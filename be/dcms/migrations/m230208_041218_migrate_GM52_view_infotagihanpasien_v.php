<?php

use yii\db\Migration;

/**
 * Class m230208_041218_migrate_GM52_view_infotagihanpasien_v
 */
class m230208_041218_migrate_GM52_view_infotagihanpasien_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('
            DROP VIEW IF EXISTS "public"."infotagihanpasien_v";
        ');

        $this->execute('
            CREATE VIEW "public"."infotagihanpasien_v" AS  SELECT tagihan.ref_pendaftaran_id,
    tagihan.pendaftaran_id,
    tagihan.no_pendaftaran,
    tagihan.tgl_pendaftaran,
    tagihan.tgl_pelayanan,
    tagihan.kelompoktindakan_id,
    tagihan.kelompoktindakan_nama, 
    tagihan.pelayanan_id,
    tagihan.tindakan_obat_id,
    tagihan.tindakan_obat_nama,
    tagihan.is_obat,
    tagihan.tarif_satuan,
    tagihan.qty,
    tagihan.tarif_cyto,
    tagihan.sub_total,
    tagihan.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_pelayanan,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_pelayanan,
    tagihan.kelaspelayanan_id,
    kelaspelayanan_m.kelaspelayanan_nama,
    tagihan.carabayar_pelayanan_id,
    carabayar_m.carabayar_nama AS carabayar_pelayanan,
    tagihan.penjamin_pelayanan_id,
    penjamin_m.penjamin_nama AS penjamin_pelayanan,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    tagihan.dokterpenanggungjawab_id,
    dokter_dpjp.nama_pegawai AS dokterpenanggungjawab_nama,
    pasien_m.pasien_id,
    pasien_m.no_mobile_pasien,
    pasien_m.alamatemail,
    tagihan.penjamin_pendaftaran_id,
    tagihan.pasienmasukpenunjang_id,
    tagihan.is_deleted,
    carabayar_m.groupcarabayar_id,
    tagihan.penjualanresep_id,
    tagihan.is_valid,
    tagihan.is_cyto,
    tagihan.pasienadmisi_id,
    tagihan.implementasi_id,
    tagihan.jeniskasuspenyakit_id,
    tagihan.discount,
    tagihan.tipepaket_id,
    tagihan.kamarruangan_id,
    tagihan.is_akomodasi,
    tagihan.kamartempattidur_id,
    tagihan.additional_data,
    tagihan.is_konsultasi,
    tagihan.tarifpenyulit_tindakan,
    tagihan.is_overwrite,
    tagihan.harga_origin,
    tagihan.cyto_origin,
    tagihan.penyulit_origin,
    lob.lob_id,
    asuransipasien_m.penjamingrade_id,
    NULL::text AS is_ditagihkan,
        CASE
            WHEN tagihan.pasienadmisi_id IS NULL AND tagihan.instalasi_id <> 2 THEN \'RAJAL\'::text
            WHEN tagihan.pasienadmisi_id IS NULL AND tagihan.instalasi_id = 2 THEN \'IGD\'::text
            ELSE \'RANAP\'::text
        END AS pelayanan,
    tagihan.daftartindakan_kode,
    tagihan.kamarruangan_nokamar,
    tagihan.jenisobatalkes_id,
    tagihan.remarks
   FROM ( SELECT 1 AS tipe,
            pendaftaran_t.pendaftaran_id AS ref_pendaftaran_id,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            tindakanpelayanan_t.tindakansudahbayar_id,
            tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
            daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            tindakanpelayanan_t.tarif_tindakan AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            daftartindakan_m.kelompoktindakan_id,
            kelompoktindakan_m.kelompoktindakan_nama,
            pendaftaran_t.pasien_id,
            tindakanpelayanan_t.dokterpenanggungjawab_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.is_deleted,
            0 AS penjualanresep_id,
            tindakanpelayanan_t.is_valid,
            tindakanpelayanan_t.cyto_tindakan AS is_cyto,
            tindakanpelayanan_t.pasienadmisi_id,
            tindakanpelayanan_t.implementasi_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            tindakanpelayanan_t.tarif_diskon AS discount,
            tindakanpelayanan_t.tipepaket_id,
            tindakanpelayanan_t.kamarruangan_id,
            daftartindakan_m.is_akomodasi,
            tindakanpelayanan_t.kamartempattidur_id,
            tindakanpelayanan_t.additional_data,
            daftartindakan_m.is_konsultasi,
            tindakanpelayanan_t.tarifpenyulit_tindakan,
            tindakanpelayanan_t.is_overwrite,
            tindakanpelayanan_t.harga_origin,
            tindakanpelayanan_t.cyto_origin,
            tindakanpelayanan_t.penyulit_origin,
            pendaftaran_t.asuransipasien_id,
            pendaftaran_t.instalasi_id,
            COALESCE(kamarruangan_m.kamarruangan_nokamar, pasienadmisi.kamarruangan_nokamar) AS kamarruangan_nokamar,
            daftartindakan_m.daftartindakan_kode,
            NULL::smallint AS jenisobatalkes_id,
            (tindakanpelayanan_t.additional_data::json -> \'remarks\'::text)::text AS remarks
           FROM tindakanpelayanan_t
             JOIN ( SELECT b.pendaftaran_id,
                    b.no_pendaftaran,
                    b.tgl_pendaftaran,
                    b.pasien_id,
                    b.penjamin_id,
                    b.jeniskasuspenyakit_id,
                    b.asuransipasien_id,
                    b.instalasi_id,
                    b.pasienadmisi_id
                   FROM pendaftaran_t b) pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT b.daftartindakan_id,
                    b.kelompoktindakan_id,
                    b.daftartindakan_nama,
                    b.is_akomodasi,
                    b.is_konsultasi,
                    b.daftartindakan_kode
                   FROM daftartindakan_m b) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN ( SELECT b.kelompoktindakan_id,
                    b.kelompoktindakan_nama
                   FROM kelompoktindakan_m b) kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
             LEFT JOIN kamarruangan_m ON tindakanpelayanan_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             LEFT JOIN ( SELECT pasienadmisi_t.pasienadmisi_id,
                    kamarruangan_m_1.kamarruangan_nokamar
                   FROM pasienadmisi_t
                     LEFT JOIN kamarruangan_m kamarruangan_m_1 ON pasienadmisi_t.kamarruangan_id = kamarruangan_m_1.kamarruangan_id) pasienadmisi ON pendaftaran_t.pasienadmisi_id = pasienadmisi.pasienadmisi_id
          WHERE tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.parent_id IS NULL
        UNION ALL
         SELECT 2 AS tipe,
            gabungtagihan.ref_pendaftaran_id,
            tindakanpelayanan_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            tindakanpelayanan_t.tindakansudahbayar_id,
            tindakanpelayanan_t.daftartindakan_id AS tindakan_obat_id,
            daftartindakan_m.daftartindakan_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            tindakanpelayanan_t.tarif_tindakan AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
            daftartindakan_m.kelompoktindakan_id,
            kelompoktindakan_m.kelompoktindakan_nama,
            pendaftaran_t.pasien_id,
            tindakanpelayanan_t.dokterpenanggungjawab_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.is_deleted,
            0 AS penjualanresep_id,
            tindakanpelayanan_t.is_valid,
            tindakanpelayanan_t.cyto_tindakan AS is_cyto,
            tindakanpelayanan_t.pasienadmisi_id,
            tindakanpelayanan_t.implementasi_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            tindakanpelayanan_t.tarif_diskon AS discount,
            tindakanpelayanan_t.tipepaket_id,
            tindakanpelayanan_t.kamarruangan_id,
            daftartindakan_m.is_akomodasi,
            tindakanpelayanan_t.kamartempattidur_id,
            tindakanpelayanan_t.additional_data,
            daftartindakan_m.is_konsultasi,
            tindakanpelayanan_t.tarifpenyulit_tindakan,
            tindakanpelayanan_t.is_overwrite,
            tindakanpelayanan_t.harga_origin,
            tindakanpelayanan_t.cyto_origin,
            tindakanpelayanan_t.penyulit_origin,
            pendaftaran_t.asuransipasien_id,
            pendaftaran_t.instalasi_id,
            COALESCE(kamarruangan_m.kamarruangan_nokamar, pasienadmisi.kamarruangan_nokamar) AS kamarruangan_nokamar,
            daftartindakan_m.daftartindakan_kode,
            NULL::smallint AS jenisobatalkes_id,
            (tindakanpelayanan_t.additional_data::json -> \'remarks\'::text)::text AS remarks
           FROM tindakanpelayanan_t
             JOIN ( SELECT b.pendaftaran_id,
                    b.no_pendaftaran,
                    b.tgl_pendaftaran,
                    b.pasien_id,
                    b.penjamin_id,
                    b.jeniskasuspenyakit_id,
                    b.asuransipasien_id,
                    b.instalasi_id,
                    b.pasienadmisi_id
                   FROM pendaftaran_t b) pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT b.daftartindakan_id,
                    b.kelompoktindakan_id,
                    b.daftartindakan_nama,
                    b.is_akomodasi,
                    b.is_konsultasi,
                    b.daftartindakan_kode
                   FROM daftartindakan_m b) daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
             JOIN ( SELECT b.kelompoktindakan_id,
                    b.kelompoktindakan_nama
                   FROM kelompoktindakan_m b) kelompoktindakan_m ON daftartindakan_m.kelompoktindakan_id = kelompoktindakan_m.kelompoktindakan_id
             JOIN ( SELECT gabungpelayanandetail_t.pendaftaran_id,
                    gabungpelayanandetail_t.ref_pendaftaran_id
                   FROM gabungpelayanandetail_t
                  WHERE gabungpelayanandetail_t.is_deleted = false) gabungtagihan ON tindakanpelayanan_t.pendaftaran_id = gabungtagihan.pendaftaran_id
             LEFT JOIN kamarruangan_m ON tindakanpelayanan_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             LEFT JOIN ( SELECT pasienadmisi_t.pasienadmisi_id,
                    kamarruangan_m_1.kamarruangan_nokamar
                   FROM pasienadmisi_t
                     LEFT JOIN kamarruangan_m kamarruangan_m_1 ON pasienadmisi_t.kamarruangan_id = kamarruangan_m_1.kamarruangan_id) pasienadmisi ON pendaftaran_t.pasienadmisi_id = pasienadmisi.pasienadmisi_id
          WHERE tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.parent_id IS NULL
        UNION ALL
         SELECT 3 AS tipe,
            pendaftaran_t.pendaftaran_id AS ref_pendaftaran_id,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            tindakanpelayanan_t.tindakansudahbayar_id,
            tindakanpelayanan_t.tipepaket_id AS tindakan_obat_id,
            tipepaket_m.tipepaket_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            tindakanpelayanan_t.tarif_tindakan AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                CASE
                    WHEN pendaftaran_t.instalasi_id = 21 THEN 17
                    ELSE NULL::integer
                END AS kelompoktindakan_id,
            \'Others\'::character varying AS kelompoktindakan_nama,
            pendaftaran_t.pasien_id,
            tindakanpelayanan_t.dokterpenanggungjawab_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.is_deleted,
            0 AS penjualanresep_id,
            tindakanpelayanan_t.is_valid,
            tindakanpelayanan_t.cyto_tindakan AS is_cyto,
            tindakanpelayanan_t.pasienadmisi_id,
            tindakanpelayanan_t.implementasi_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            tindakanpelayanan_t.tarif_diskon AS discount,
            tindakanpelayanan_t.tipepaket_id,
            tindakanpelayanan_t.kamarruangan_id,
            NULL::boolean AS is_akomodasi,
            tindakanpelayanan_t.kamartempattidur_id,
            tindakanpelayanan_t.additional_data,
            NULL::boolean AS is_konsultasi,
            tindakanpelayanan_t.tarifpenyulit_tindakan,
            tindakanpelayanan_t.is_overwrite,
            tindakanpelayanan_t.harga_origin,
            tindakanpelayanan_t.cyto_origin,
            tindakanpelayanan_t.penyulit_origin,
            pendaftaran_t.asuransipasien_id,
            pendaftaran_t.instalasi_id,
            COALESCE(kamarruangan_m.kamarruangan_nokamar, pasienadmisi.kamarruangan_nokamar) AS kamarruangan_nokamar,
            tipepaket_m.tipepaket_kode AS daftartindakan_kode,
            NULL::smallint AS jenisobatalkes_id,
            (tindakanpelayanan_t.additional_data::json -> \'remarks\'::text)::text AS remarks
           FROM tindakanpelayanan_t
             JOIN ( SELECT c.pendaftaran_id,
                    c.no_pendaftaran,
                    c.tgl_pendaftaran,
                    c.pasien_id,
                    c.penjamin_id,
                    c.jeniskasuspenyakit_id,
                    c.asuransipasien_id,
                    c.instalasi_id,
                    c.pasienadmisi_id
                   FROM pendaftaran_t c) pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT c.tipepaket_id,
                    c.tipepaket_nama,
                    c.tipepaket_kode
                   FROM tipepaket_m c) tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
             LEFT JOIN kamarruangan_m ON tindakanpelayanan_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             LEFT JOIN ( SELECT pasienadmisi_t.pasienadmisi_id,
                    kamarruangan_m_1.kamarruangan_nokamar
                   FROM pasienadmisi_t
                     LEFT JOIN kamarruangan_m kamarruangan_m_1 ON pasienadmisi_t.kamarruangan_id = kamarruangan_m_1.kamarruangan_id) pasienadmisi ON pendaftaran_t.pasienadmisi_id = pasienadmisi.pasienadmisi_id
          WHERE tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.parent_id IS NULL
        UNION ALL
         SELECT 4 AS tipe,
            gabungtagihan.ref_pendaftaran_id,
            tindakanpelayanan_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            tindakanpelayanan_t.tgl_tindakan AS tgl_pelayanan,
            tindakanpelayanan_t.tindakanpelayanan_id AS pelayanan_id,
            tindakanpelayanan_t.tindakansudahbayar_id,
            tindakanpelayanan_t.tipepaket_id AS tindakan_obat_id,
            tipepaket_m.tipepaket_nama AS tindakan_obat_nama,
            false AS is_obat,
            tindakanpelayanan_t.tarif_satuan,
            tindakanpelayanan_t.qty_tindakan AS qty,
            tindakanpelayanan_t.tarifcyto_tindakan AS tarif_cyto,
            tindakanpelayanan_t.tarif_tindakan AS sub_total,
            tindakanpelayanan_t.ruangan_id,
            tindakanpelayanan_t.kelaspelayanan_id,
            tindakanpelayanan_t.carabayar_id AS carabayar_pelayanan_id,
            tindakanpelayanan_t.penjamin_id AS penjamin_pelayanan_id,
                CASE
                    WHEN pendaftaran_t.instalasi_id = 21 THEN 17
                    ELSE NULL::integer
                END AS kelompoktindakan_id,
            \'Others\'::character varying AS kelompoktindakan_nama,
            pendaftaran_t.pasien_id,
            tindakanpelayanan_t.dokterpenanggungjawab_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            tindakanpelayanan_t.pasienmasukpenunjang_id,
            tindakanpelayanan_t.is_deleted,
            0 AS penjualanresep_id,
            tindakanpelayanan_t.is_valid,
            tindakanpelayanan_t.cyto_tindakan AS is_cyto,
            tindakanpelayanan_t.pasienadmisi_id,
            tindakanpelayanan_t.implementasi_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            tindakanpelayanan_t.tarif_diskon AS discount,
            tindakanpelayanan_t.tipepaket_id,
            tindakanpelayanan_t.kamarruangan_id,
            NULL::boolean AS is_akomodasi,
            tindakanpelayanan_t.kamartempattidur_id,
            tindakanpelayanan_t.additional_data,
            NULL::boolean AS is_konsultasi,
            tindakanpelayanan_t.tarifpenyulit_tindakan,
            tindakanpelayanan_t.is_overwrite,
            tindakanpelayanan_t.harga_origin,
            tindakanpelayanan_t.cyto_origin,
            tindakanpelayanan_t.penyulit_origin,
            pendaftaran_t.asuransipasien_id,
            pendaftaran_t.instalasi_id,
            COALESCE(kamarruangan_m.kamarruangan_nokamar, pasienadmisi.kamarruangan_nokamar) AS kamarruangan_nokamar,
            tipepaket_m.tipepaket_kode AS daftartindakan_kode,
            NULL::smallint AS jenisobatalkes_id,
            (tindakanpelayanan_t.additional_data::json -> \'remarks\'::text)::text AS remarks
           FROM tindakanpelayanan_t
             JOIN ( SELECT c.pendaftaran_id,
                    c.no_pendaftaran,
                    c.tgl_pendaftaran,
                    c.pasien_id,
                    c.penjamin_id,
                    c.jeniskasuspenyakit_id,
                    c.asuransipasien_id,
                    c.instalasi_id,
                    c.pasienadmisi_id
                   FROM pendaftaran_t c) pendaftaran_t ON tindakanpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT c.tipepaket_id,
                    c.tipepaket_nama,
                    c.tipepaket_kode
                   FROM tipepaket_m c) tipepaket_m ON tindakanpelayanan_t.tipepaket_id = tipepaket_m.tipepaket_id
             JOIN ( SELECT gabungpelayanandetail_t.pendaftaran_id,
                    gabungpelayanandetail_t.ref_pendaftaran_id
                   FROM gabungpelayanandetail_t
                  WHERE gabungpelayanandetail_t.is_deleted = false) gabungtagihan ON tindakanpelayanan_t.pendaftaran_id = gabungtagihan.pendaftaran_id
             LEFT JOIN kamarruangan_m ON tindakanpelayanan_t.kamarruangan_id = kamarruangan_m.kamarruangan_id
             LEFT JOIN ( SELECT pasienadmisi_t.pasienadmisi_id,
                    kamarruangan_m_1.kamarruangan_nokamar
                   FROM pasienadmisi_t
                     LEFT JOIN kamarruangan_m kamarruangan_m_1 ON pasienadmisi_t.kamarruangan_id = kamarruangan_m_1.kamarruangan_id) pasienadmisi ON pendaftaran_t.pasienadmisi_id = pasienadmisi.pasienadmisi_id
          WHERE tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.parent_id IS NULL
        UNION ALL
         SELECT 5 AS tipe,
            pendaftaran_t.pendaftaran_id AS ref_pendaftaran_id,
            pendaftaran_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
            obatalkespasien_t.obatsudahbayar_id,
            obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
            obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
            true AS is_obat,
            obatalkespasien_t.hargasatuan_oa,
                CASE
                    WHEN obatalkespasien_t.det IS NULL THEN obatalkespasien_t.qty_oa
                    ELSE obatalkespasien_t.det
                END AS qty,
            obatalkespasien_t.tarifcyto AS tarif_cyto,
            obatalkespasien_t.hargajual_oa AS sub_total,
            obatalkespasien_t.ruangan_id,
            obatalkespasien_t.kelaspelayanan_id,
            obatalkespasien_t.carabayar_id AS carabayar_pelayanan_id,
            obatalkespasien_t.penjamin_id AS penjamin_pelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            jenisobatalkes_m.jenisobatalkes_nama AS kelompoktindakan_nama,
            pendaftaran_t.pasien_id,
            obatalkespasien_t.pegawai_id AS dokterpenanggungjawab_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            obatalkespasien_t.pasienmasukpenunjang_id,
            obatalkespasien_t.is_deleted,
            obatalkespasien_t.penjualanresep_id,
            NULL::boolean AS is_valid,
            NULL::boolean AS is_cyto,
            obatalkespasien_t.pasienadmisi_id,
            NULL::integer AS implementasi_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            obatalkespasien_t.tarif_diskon AS discount,
            NULL::integer AS tipepaket_id,
            NULL::integer AS kamarruangan_id,
            NULL::boolean AS is_akomodasi,
            NULL::integer AS kamartempattidur_id,
            NULL::text AS additional_data,
            NULL::boolean AS is_konsultasi,
            0 AS tarifpenyulit_tindakan,
            obatalkespasien_t.is_overwrite,
            obatalkespasien_t.harga_origin,
            0 AS cyto_origin,
            0 AS penyulit_origin,
            pendaftaran_t.asuransipasien_id,
            pendaftaran_t.instalasi_id,
            COALESCE(pasienadmisi.kamarruangan_nokamar) AS kamarruangan_nokamar,
            obatalkes_m.obatalkes_kode AS daftartindakan_kode,
            obatalkes_m.jenisobatalkes_id,
            NULL::text AS remarks
           FROM obatalkespasien_t
             JOIN ( SELECT d.pendaftaran_id,
                    d.no_pendaftaran,
                    d.tgl_pendaftaran,
                    d.pasien_id,
                    d.penjamin_id,
                    d.jeniskasuspenyakit_id,
                    d.asuransipasien_id,
                    d.instalasi_id,
                    d.pasienadmisi_id
                   FROM pendaftaran_t d) pendaftaran_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT d.obatalkes_id,
                    d.obatalkes_nama,
                    d.obatalkes_kode,
                    d.jenisobatalkes_id
                   FROM obatalkes_m d) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
             LEFT JOIN ( SELECT pasienadmisi_t.pasienadmisi_id,
                    kamarruangan_m.kamarruangan_nokamar
                   FROM pasienadmisi_t
                     LEFT JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id) pasienadmisi ON pendaftaran_t.pasienadmisi_id = pasienadmisi.pasienadmisi_id
             JOIN ( SELECT d.jenisobatalkes_id,
                    d.jenisobatalkes_nama
                   FROM jenisobatalkes_m d) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
          WHERE obatalkespasien_t.is_deleted = false
        UNION ALL
         SELECT 6 AS tipe,
            gabungtagihan.ref_pendaftaran_id,
            obatalkespasien_t.pendaftaran_id,
            pendaftaran_t.no_pendaftaran,
            pendaftaran_t.tgl_pendaftaran,
            obatalkespasien_t.tglpelayanan AS tgl_pelayanan,
            obatalkespasien_t.obatalkespasien_id AS pelayanan_id,
            obatalkespasien_t.obatsudahbayar_id,
            obatalkespasien_t.obatalkes_id AS tindakan_obat_id,
            obatalkes_m.obatalkes_nama AS tindakan_obat_nama,
            true AS is_obat,
            obatalkespasien_t.hargasatuan_oa,
                CASE
                    WHEN obatalkespasien_t.det IS NULL THEN obatalkespasien_t.qty_oa
                    ELSE obatalkespasien_t.det
                END AS qty,
            obatalkespasien_t.tarifcyto AS tarif_cyto,
            obatalkespasien_t.hargajual_oa AS sub_total,
            obatalkespasien_t.ruangan_id,
            obatalkespasien_t.kelaspelayanan_id,
            obatalkespasien_t.carabayar_id AS carabayar_pelayanan_id,
            obatalkespasien_t.penjamin_id AS penjamin_pelayanan_id,
            NULL::integer AS kelompoktindakan_id,
            jenisobatalkes_m.jenisobatalkes_nama AS kelompoktindakan_nama,
            pendaftaran_t.pasien_id,
            obatalkespasien_t.pegawai_id AS dokterpenanggungjawab_id,
            pendaftaran_t.penjamin_id AS penjamin_pendaftaran_id,
            obatalkespasien_t.pasienmasukpenunjang_id,
            obatalkespasien_t.is_deleted,
            obatalkespasien_t.penjualanresep_id,
            NULL::boolean AS is_valid,
            NULL::boolean AS is_cyto,
            obatalkespasien_t.pasienadmisi_id,
            NULL::integer AS implementasi_id,
            pendaftaran_t.jeniskasuspenyakit_id,
            obatalkespasien_t.tarif_diskon AS discount,
            NULL::integer AS tipepaket_id,
            NULL::integer AS kamarruangan_id,
            NULL::boolean AS is_akomodasi,
            NULL::integer AS kamartempattidur_id,
            NULL::text AS additional_data,
            NULL::boolean AS is_konsultasi,
            0 AS tarifpenyulit_tindakan,
            obatalkespasien_t.is_overwrite,
            obatalkespasien_t.harga_origin,
            0 AS cyto_origin,
            0 AS penyulit_origin,
            pendaftaran_t.asuransipasien_id,
            pendaftaran_t.instalasi_id,
            COALESCE(pasienadmisi.kamarruangan_nokamar) AS kamarruangan_nokamar,
            obatalkes_m.obatalkes_kode AS daftartindakan_kode,
            obatalkes_m.jenisobatalkes_id,
            NULL::text AS remarks
           FROM obatalkespasien_t
             JOIN ( SELECT d.pendaftaran_id,
                    d.no_pendaftaran,
                    d.tgl_pendaftaran,
                    d.pasien_id,
                    d.penjamin_id,
                    d.jeniskasuspenyakit_id,
                    d.asuransipasien_id,
                    d.instalasi_id,
                    d.pasienadmisi_id
                   FROM pendaftaran_t d) pendaftaran_t ON obatalkespasien_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
             JOIN ( SELECT d.obatalkes_id,
                    d.obatalkes_nama,
                    d.obatalkes_kode,
                    d.jenisobatalkes_id
                   FROM obatalkes_m d) obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN ( SELECT gabungpelayanandetail_t.pendaftaran_id,
                    gabungpelayanandetail_t.ref_pendaftaran_id
                   FROM gabungpelayanandetail_t
                  WHERE gabungpelayanandetail_t.is_deleted = false) gabungtagihan ON obatalkespasien_t.pendaftaran_id = gabungtagihan.pendaftaran_id
             LEFT JOIN ( SELECT pasienadmisi_t.pasienadmisi_id,
                    kamarruangan_m.kamarruangan_nokamar
                   FROM pasienadmisi_t
                     LEFT JOIN kamarruangan_m ON pasienadmisi_t.kamarruangan_id = kamarruangan_m.kamarruangan_id) pasienadmisi ON pendaftaran_t.pasienadmisi_id = pasienadmisi.pasienadmisi_id
             JOIN ( SELECT d.jenisobatalkes_id,
                    d.jenisobatalkes_nama
                   FROM jenisobatalkes_m d) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
          WHERE obatalkespasien_t.is_deleted = false) tagihan
     LEFT JOIN ( SELECT a.ruangan_id,
            a.ruangan_nama,
            a.instalasi_id
           FROM ruangan_m a) ruangan_m ON tagihan.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN ( SELECT a.instalasi_id,
            a.instalasi_nama,
            a.lob_id
           FROM instalasi_m a) instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     LEFT JOIN ( SELECT a.instalasi_id,
            a.lob_id
           FROM instalasi_m a) lob ON tagihan.instalasi_id = lob.instalasi_id
     LEFT JOIN ( SELECT a.kelaspelayanan_id,
            a.kelaspelayanan_nama
           FROM kelaspelayanan_m a) kelaspelayanan_m ON tagihan.kelaspelayanan_id = kelaspelayanan_m.kelaspelayanan_id
     LEFT JOIN ( SELECT a.penjamin_id,
            a.penjamin_nama,
            a.carabayar_id
           FROM penjamin_m a) penjamin_m ON tagihan.penjamin_pelayanan_id = penjamin_m.penjamin_id
     LEFT JOIN ( SELECT a.carabayar_id,
            a.carabayar_nama,
            a.groupcarabayar_id
           FROM carabayar_m a) carabayar_m ON penjamin_m.carabayar_id = carabayar_m.carabayar_id
     LEFT JOIN ( SELECT a.pasien_id,
            a.nama_pasien,
            a.no_rekam_medik,
            a.no_mobile_pasien,
            a.alamatemail
           FROM pasien_m a) pasien_m ON tagihan.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT a.pegawai_id,
            a.nama_pegawai
           FROM pegawai_m a) dokter_dpjp ON tagihan.dokterpenanggungjawab_id = dokter_dpjp.pegawai_id
     LEFT JOIN ( SELECT a.asuransipasien_id,
            a.penjamingrade_id
           FROM asuransipasien_m a) asuransipasien_m ON tagihan.asuransipasien_id = asuransipasien_m.asuransipasien_id
  WHERE tagihan.tindakansudahbayar_id IS NULL
  ORDER BY tagihan.tgl_pelayanan DESC;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230208_041218_migrate_GM52_view_infotagihanpasien_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m230208_041218_migrate_GM52_view_infotagihanpasien_v cannot be reverted.\n";

        return false;
    }
    */
}
