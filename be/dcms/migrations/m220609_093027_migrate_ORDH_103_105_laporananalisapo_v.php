<?php

use yii\db\Migration;

/**
 * Class m220609_093027_migrate_ORDH_103_104_laporananalisapo_v
 */
class m220609_093027_migrate_ORDH_103_105_laporananalisapo_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW IF EXISTS "public"."laporananalisapo_v";');
        $this->execute("CREATE VIEW \"public\".\"laporananalisapo_v\" AS  SELECT a.tipe,
        a.no,
        a.kode_obat,
        a.nama_obat,
        a.manufaktur,
        a.jenis_obat,
        a.no_pr,
        a.tgl_pr,
        a.qty_pr,
        a.uom_pr,
        a.catatan,
        a.no_po,
        a.tgl_po,
        a.tgl_po_validasi,
        a.qty_po,
        a.uom_po,
        a.tgl_batal_po,
        a.harga,
        a.disc_persen,
        a.ppn_persen,
        a.sub_total,
        a.total,
        a.status_po,
        a.tgl_po_batal,
        a.catatan_batal,
        a.kode_supplier,
        a.nama_supplier,
        a.tgl_penerimaan,
        a.qty_penerimaan,
        a.uom_penerimaan,
        a.sisa_penerimaan,
        a.is_validasi,
        a.pr_to_po,
        a.pr_to_povalidasi,
        a.po_to_povalidasi,
        a.pr_to_penerimaan,
        a.povalidasi_to_penerimaan,
        a.satuan_pr,
        a.satuan_po,
        a.penerimaan,
        a.status_po_kondisi,
        a.status_pr,
        a.created_date_pr
       FROM ( SELECT 'VALIDASI PO'::text AS tipe,
                validasipoobat_t.validasipoobat_id AS no,
                obatalkes_m.obatalkes_kode AS kode_obat,
                obatalkes_m.obatalkes_nama AS nama_obat,
                manufaktur_m.nama AS manufaktur,
                jenisobatalkes_m.jenisobatalkes_nama AS jenis_obat,
                purchasereq_t.no_pr,
                purchasereq_t.tgl_pr,
                purchasereqdetail_t.qty_input AS qty_pr,
                    CASE
                        WHEN uom_pr.obatalkes_id IS NULL THEN NULL::text
                        ELSE concat('1 ', uom_pr.uom_besar, ' = ', uom_pr.nilai_konversi, ' ', uom_pr.uom_kecil)
                    END AS uom_pr,
                purchasereqdetail_t.catatan,
                validasipoobat_t.no_poobat AS no_po,
                validasipoobat_t.created_date AS tgl_po,
                validasipoobat_t.tgl_validasi AS tgl_po_validasi,
                validasipoobatdetail_t.qty_input AS qty_po,
                    CASE
                        WHEN uom_po.obatalkes_id IS NULL THEN NULL::text
                        ELSE concat('1 ', uom_po.uom_besar, ' = ', uom_po.nilai_konversi, ' ', uom_po.uom_kecil)
                    END AS uom_po,
                    CASE
                        WHEN validasipoobat_t.status_penerimaan = 575 THEN validasipoobat_t.last_modified_date
                        ELSE NULL::timestamp without time zone
                    END AS tgl_batal_po,
                    CASE
                        WHEN validasipoobat_t.status_penerimaan = 575 THEN 0::double precision
                        WHEN obatalkes_m.is_deleted = true OR validasipoobatdetail_t.is_deleted = true THEN 0::double precision
                        ELSE validasipoobatdetail_t.harga
                    END AS harga,
                    CASE
                        WHEN validasipoobat_t.status_penerimaan = 575 THEN 0::double precision
                        WHEN obatalkes_m.is_deleted = true OR validasipoobatdetail_t.is_deleted = true THEN 0::double precision
                        ELSE validasipoobatdetail_t.discount::double precision
                    END AS disc_persen,
                    CASE
                        WHEN validasipoobat_t.status_penerimaan = 575 THEN 0::double precision
                        WHEN obatalkes_m.is_deleted = true OR validasipoobatdetail_t.is_deleted = true THEN 0::double precision
                        ELSE pajak_m.pajak_persen::double precision
                    END AS ppn_persen,
                    CASE
                        WHEN validasipoobat_t.status_penerimaan = 575 THEN 0::double precision
                        WHEN obatalkes_m.is_deleted = true OR validasipoobatdetail_t.is_deleted = true THEN 0::double precision
                        ELSE validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision
                    END AS sub_total,
                    CASE
                        WHEN validasipoobat_t.status_penerimaan = 575 THEN 0::double precision
                        WHEN obatalkes_m.is_deleted = true OR validasipoobatdetail_t.is_deleted = true THEN 0::double precision
                        ELSE validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp + (validasipoobatdetail_t.harga * validasipoobatdetail_t.qty_input::double precision - validasipoobatdetail_t.discount_rp) * pajak_m.pajak_persen::double precision / 100::double precision
                    END AS total,
                    CASE
                        WHEN validasipoobatdetail_t.qty_penerimaan = validasipoobatdetail_t.qty_input THEN 'Sudah Semua Diterima'::character varying
                        WHEN validasipoobatdetail_t.qty_penerimaan > 0 AND validasipoobatdetail_t.qty_penerimaan < validasipoobatdetail_t.qty_input THEN 'Belum Semua Diterima'::character varying
                        WHEN validasipoobatdetail_t.qty_penerimaan = 0 THEN 'Belum Diterima'::character varying
                        WHEN obatalkes_m.is_deleted = true OR validasipoobatdetail_t.is_deleted = true THEN 'Dibatalkan'::character varying
                        ELSE look_statuspenerimaan.lookup_name
                    END AS status_po,
                    CASE
                        WHEN validasipoobat_t.status_penerimaan = 575 THEN validasipoobat_t.last_modified_date
                        ELSE NULL::timestamp without time zone
                    END AS tgl_po_batal,
                validasipoobat_t.catatan AS catatan_batal,
                supplier_m.supplier_kode AS kode_supplier,
                supplier_m.supplier_nama AS nama_supplier,
                penerimaan.tgl AS tgl_penerimaan,
                validasipoobatdetail_t.qty_penerimaan,
                    CASE
                        WHEN uom_terima.satuankonversi_id IS NULL THEN NULL::text
                        ELSE concat('1 ', uom_terima.uom_besar, ' = ', uom_terima.nilai_konversi, ' ', uom_terima.uom_kecil)
                    END AS uom_penerimaan,
                validasipoobatdetail_t.qty_sisa AS sisa_penerimaan,
                validasipoobat_t.is_validasi,
                COALESCE(validasipoobat_t.created_date::date - purchasereq_t.tgl_pr, NULL::integer) AS pr_to_po,
                    CASE
                        WHEN validasipoobat_t.is_validasi = true THEN COALESCE(validasipoobat_t.tgl_validasi::date - purchasereq_t.tgl_pr, NULL::integer)
                        ELSE NULL::integer
                    END AS pr_to_povalidasi,
                    CASE
                        WHEN validasipoobat_t.is_validasi = true THEN COALESCE((validasipoobat_t.tgl_validasi::date - validasipoobat_t.created_date::date)::double precision, NULL::double precision)
                        ELSE NULL::double precision
                    END AS po_to_povalidasi,
                COALESCE(penerimaan.tgl::date - purchasereq_t.tgl_pr, NULL::integer) AS pr_to_penerimaan,
                    CASE
                        WHEN validasipoobat_t.is_validasi = true THEN COALESCE(penerimaan.tgl::date - validasipoobat_t.tgl_validasi::date, NULL::integer)
                        ELSE NULL::integer
                    END AS povalidasi_to_penerimaan,
                uom_pr.uom_besar AS satuan_pr,
                uom_po.uom_besar AS satuan_po,
                uom_terima.uom_besar AS penerimaan,
                    CASE
                        WHEN validasipoobat_t.status_penerimaan = 575 THEN 'Dibatalkan'::text
                        WHEN validasipoobat_t.status_penerimaan = 572 THEN 'Belum Diterima'::text
                        WHEN validasipoobat_t.status_penerimaan = 573 THEN 'Belum Semua Diterima'::text
                        WHEN validasipoobat_t.status_penerimaan = 686 THEN 'Expired'::text
                        WHEN obatalkes_m.is_deleted = true OR validasipoobatdetail_t.is_deleted = true THEN 'Dibatalkan'::text
                        WHEN validasipoobat_t.status_penerimaan = 574 THEN 'Sudah Semua Diterima'::text
                        WHEN validasipoobat_t.status_penerimaan = 580 THEN 'Closing Supplier'::text
                        ELSE NULL::text
                    END AS status_po_kondisi,
                    CASE
                        WHEN purchasereq_t.status = 1056 THEN 'Approved'::text
                        WHEN purchasereq_t.status = 1057 THEN 'Belum Approved'::text
                        WHEN purchasereq_t.status = 712 THEN 'Belum PO'::text
                        ELSE NULL::text
                    END AS status_pr,
                purchasereq_t.created_date AS created_date_pr
               FROM validasipoobat_t
                 JOIN ( SELECT a_1.validasipoobat_id,
                        a_1.purchasereqdetail_id,
                        a_1.obatalkes_id,
                        a_1.s_konversiobt_id,
                        a_1.validasipoobatdetail_id,
                        a_1.qty_input,
                        a_1.harga,
                        a_1.discount,
                        a_1.jumlah,
                        a_1.discount_rp,
                        a_1.qty_penerimaan,
                        a_1.qty_sisa,
                        a_1.is_deleted
                       FROM validasipoobatdetail_t a_1) validasipoobatdetail_t ON validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id
                 LEFT JOIN ( SELECT a_1.purchasereqdetail_id,
                        a_1.purchasereq_id,
                        a_1.obatalkes_id,
                        a_1.satuan_id,
                        a_1.satuankonversi_id,
                        a_1.qty_konversi,
                        a_1.qty_input,
                        a_1.catatan
                       FROM purchasereqdetail_t a_1) purchasereqdetail_t ON validasipoobatdetail_t.purchasereqdetail_id = purchasereqdetail_t.purchasereqdetail_id
                 LEFT JOIN ( SELECT a_1.purchasereq_id,
                        a_1.no_pr,
                        a_1.tgl_pr,
                        a_1.status,
                        a_1.created_date
                       FROM purchasereq_t a_1) purchasereq_t ON purchasereqdetail_t.purchasereq_id = purchasereq_t.purchasereq_id
                 LEFT JOIN ( SELECT a_1.obatalkes_id,
                        a_1.satuankecil_id,
                        a_1.satuanbesar_id,
                        uom_besar.satuanunit_nama AS uom_besar,
                        uom_kecil.satuanunit_nama AS uom_kecil,
                        a_1.nilai_konversi
                       FROM satuankonversi_m a_1
                         JOIN ( SELECT a1.satuanunit_id,
                                a1.satuanunit_nama
                               FROM satuanunit_m a1) uom_besar ON a_1.satuanbesar_id = uom_besar.satuanunit_id
                         JOIN ( SELECT a1.satuanunit_id,
                                a1.satuanunit_nama
                               FROM satuanunit_m a1) uom_kecil ON a_1.satuankecil_id = uom_kecil.satuanunit_id
                      GROUP BY a_1.obatalkes_id, a_1.satuankecil_id, a_1.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, a_1.nilai_konversi) uom_pr ON purchasereqdetail_t.obatalkes_id = uom_pr.obatalkes_id AND purchasereqdetail_t.satuan_id = uom_pr.satuanbesar_id AND purchasereqdetail_t.satuankonversi_id = uom_pr.satuankecil_id
                 LEFT JOIN ( SELECT a_1.supplier_id,
                        a_1.supplier_kode,
                        a_1.supplier_nama
                       FROM supplier_m a_1) supplier_m ON validasipoobat_t.supplier_id = supplier_m.supplier_id
                 JOIN ( SELECT a_1.obatalkes_id,
                        a_1.obatalkes_kode,
                        a_1.jenisobatalkes_id,
                        a_1.manufaktur_id,
                        a_1.obatalkes_nama,
                        a_1.is_deleted
                       FROM obatalkes_m a_1) obatalkes_m ON validasipoobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
                 JOIN ( SELECT a_1.jenisobatalkes_id,
                        a_1.jenisobatalkes_nama
                       FROM jenisobatalkes_m a_1) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                 LEFT JOIN ( SELECT a_1.satuankonversi_id,
                        a_1.satuankecil_id,
                        a_1.satuanbesar_id
                       FROM satuankonversi_m a_1) satuankonversi_m ON validasipoobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id
                 LEFT JOIN ( SELECT a_1.satuanunit_id,
                        a_1.satuanunit_nama
                       FROM satuanunit_m a_1) kecil ON satuankonversi_m.satuankecil_id = kecil.satuanunit_id
                 LEFT JOIN ( SELECT a_1.satuanunit_id,
                        a_1.satuanunit_nama
                       FROM satuanunit_m a_1) besar ON satuankonversi_m.satuanbesar_id = besar.satuanunit_id
                 LEFT JOIN ( SELECT a_1.manufaktur_id,
                        a_1.nama
                       FROM manufaktur_m a_1) manufaktur_m ON obatalkes_m.manufaktur_id = manufaktur_m.manufaktur_id
                 LEFT JOIN ( SELECT a_1.pajak_id,
                        a_1.pajak_persen
                       FROM pajak_m a_1) pajak_m ON validasipoobat_t.pajak_id = pajak_m.pajak_id
                 LEFT JOIN ( SELECT a_1.satuankonversi_id,
                        a_1.obatalkes_id,
                        a_1.satuankecil_id,
                        a_1.satuanbesar_id,
                        uom_besar.satuanunit_nama AS uom_besar,
                        uom_kecil.satuanunit_nama AS uom_kecil,
                        a_1.nilai_konversi
                       FROM satuankonversi_m a_1
                         JOIN ( SELECT a1.satuanunit_id,
                                a1.satuanunit_nama
                               FROM satuanunit_m a1) uom_besar ON a_1.satuanbesar_id = uom_besar.satuanunit_id
                         JOIN ( SELECT a1.satuanunit_id,
                                a1.satuanunit_nama
                               FROM satuanunit_m a1) uom_kecil ON a_1.satuankecil_id = uom_kecil.satuanunit_id) uom_po ON validasipoobatdetail_t.obatalkes_id = uom_po.obatalkes_id AND validasipoobatdetail_t.s_konversiobt_id = uom_po.satuankonversi_id
                 LEFT JOIN ( SELECT a_1.validasipoobatdetail_id,
                        max(penerimaanobat_t.tgl_penerimaan) AS tgl,
                        a_1.obatalkes_id,
                        a_1.s_konversiobt_id
                       FROM penerimaanobatdetail_t a_1
                         JOIN ( SELECT a1.penerimaanobat_id,
                                a1.tgl_penerimaan
                               FROM penerimaanobat_t a1) penerimaanobat_t ON a_1.penerimaanobat_id = penerimaanobat_t.penerimaanobat_id
                      GROUP BY a_1.validasipoobatdetail_id, a_1.obatalkes_id, a_1.s_konversiobt_id) penerimaan ON validasipoobatdetail_t.validasipoobatdetail_id = penerimaan.validasipoobatdetail_id
                 LEFT JOIN ( SELECT a_1.satuankonversi_id,
                        a_1.obatalkes_id,
                        a_1.satuankecil_id,
                        a_1.satuanbesar_id,
                        uom_besar.satuanunit_nama AS uom_besar,
                        uom_kecil.satuanunit_nama AS uom_kecil,
                        a_1.nilai_konversi
                       FROM satuankonversi_m a_1
                         JOIN ( SELECT a1.satuanunit_id,
                                a1.satuanunit_nama
                               FROM satuanunit_m a1) uom_besar ON a_1.satuanbesar_id = uom_besar.satuanunit_id
                         JOIN ( SELECT a1.satuanunit_id,
                                a1.satuanunit_nama
                               FROM satuanunit_m a1) uom_kecil ON a_1.satuankecil_id = uom_kecil.satuanunit_id) uom_terima ON penerimaan.obatalkes_id = uom_terima.obatalkes_id AND penerimaan.s_konversiobt_id = uom_terima.satuankonversi_id
                 JOIN ( SELECT a_1.lookup_id,
                        a_1.lookup_name
                       FROM lookup_m a_1) look_statuspenerimaan ON validasipoobat_t.status_penerimaan = look_statuspenerimaan.lookup_id
            UNION ALL
             SELECT 'PURCHASEREQ'::text AS tipe,
                NULL::integer AS no,
                obatalkes_m.obatalkes_kode AS kode_obat,
                obatalkes_m.obatalkes_nama AS nama_obat,
                manufaktur_m.nama AS manufaktur,
                jenisobatalkes_m.jenisobatalkes_nama AS jenis_obat,
                purchasereq_t.no_pr,
                purchasereq_t.tgl_pr,
                purchasereqdetail_t.qty_input AS qty_pr,
                    CASE
                        WHEN uom_pr.obatalkes_id IS NULL THEN NULL::text
                        ELSE concat('1 ', uom_pr.uom_besar, ' = ', uom_pr.nilai_konversi, ' ', uom_pr.uom_kecil)
                    END AS uom_pr,
                purchasereqdetail_t.catatan,
                NULL::character varying AS no_po,
                NULL::timestamp without time zone AS tgl_po,
                NULL::timestamp without time zone AS tgl_po_validasi,
                NULL::integer AS qty_po,
                NULL::text AS uom_po,
                NULL::timestamp without time zone AS tgl_batal_po,
                NULL::double precision AS harga,
                NULL::double precision AS disc_persen,
                NULL::double precision AS ppn_persen,
                NULL::double precision AS sub_total,
                NULL::double precision AS total,
                NULL::character varying AS status_po,
                NULL::timestamp without time zone AS tgl_po_batal,
                NULL::text AS catatan_batal,
                NULL::character varying AS kode_supplier,
                NULL::character varying AS nama_supplier,
                NULL::timestamp without time zone AS tgl_penerimaan,
                NULL::integer AS qty_penerimaan,
                NULL::text AS uom_penerimaan,
                NULL::integer AS sisa_penerimaan,
                NULL::boolean AS is_validasi,
                NULL::integer AS pr_to_po,
                NULL::integer AS pr_to_povalidasi,
                NULL::double precision AS po_to_povalidasi,
                NULL::integer AS pr_to_penerimaan,
                NULL::integer AS povalidasi_to_penerimaan,
                uom_pr.uom_besar AS satuan_pr,
                NULL::character varying AS satuan_po,
                NULL::character varying AS penerimaan,
                NULL::text AS status_po_kondisi,
                    CASE
                        WHEN purchasereq_t.status = 1056 THEN 'Approved'::text
                        WHEN purchasereq_t.status = 1057 THEN 'Belum Approved'::text
                        WHEN purchasereq_t.status = 712 THEN 'Belum PO'::text
                        ELSE NULL::text
                    END AS status_pr,
                purchasereq_t.created_date AS created_date_pr
               FROM purchasereq_t
                 LEFT JOIN ( SELECT purchasereqdetail_t_1.purchasereqdetail_id,
                        purchasereqdetail_t_1.purchasereq_id,
                        purchasereqdetail_t_1.obatalkes_id,
                        purchasereqdetail_t_1.qty_input,
                        purchasereqdetail_t_1.qty_konversi,
                        purchasereqdetail_t_1.satuan_id,
                        purchasereqdetail_t_1.satuankonversi_id,
                        purchasereqdetail_t_1.catatan,
                        purchasereqdetail_t_1.additional_data,
                        purchasereqdetail_t_1.created_date,
                        purchasereqdetail_t_1.created_by,
                        purchasereqdetail_t_1.modified_count,
                        purchasereqdetail_t_1.last_modified_date,
                        purchasereqdetail_t_1.last_modified_by,
                        purchasereqdetail_t_1.is_deleted,
                        purchasereqdetail_t_1.is_active,
                        purchasereqdetail_t_1.deleted_date,
                        purchasereqdetail_t_1.deleted_by,
                        purchasereqdetail_t_1.status,
                        purchasereqdetail_t_1.alasan,
                        purchasereqdetail_t_1.doi,
                        purchasereqdetail_t_1.ssmin,
                        purchasereqdetail_t_1.qty_sugesstion,
                        purchasereqdetail_t_1.qty_final,
                        purchasereqdetail_t_1.satuan_final_id,
                        purchasereqdetail_t_1.qty_pr,
                        purchasereqdetail_t_1.qty_saatini,
                        purchasereqdetail_t_1.stok_gudang,
                        purchasereqdetail_t_1.stok_farmasi,
                        purchasereqdetail_t_1.stok_ruanganlain,
                        purchasereqdetail_t_1.last_7,
                        purchasereqdetail_t_1.last_14,
                        purchasereqdetail_t_1.last_30,
                        purchasereqdetail_t_1.qty_outstanding,
                        purchasereqdetail_t_1.move_category_id
                       FROM purchasereqdetail_t purchasereqdetail_t_1) purchasereqdetail_t ON purchasereq_t.purchasereq_id = purchasereqdetail_t.purchasereq_id
                 LEFT JOIN ( SELECT a_1.obatalkes_id,
                        a_1.satuankecil_id,
                        a_1.satuanbesar_id,
                        uom_besar.satuanunit_nama AS uom_besar,
                        uom_kecil.satuanunit_nama AS uom_kecil,
                        a_1.nilai_konversi
                       FROM satuankonversi_m a_1
                         JOIN ( SELECT a1.satuanunit_id,
                                a1.satuanunit_nama
                               FROM satuanunit_m a1) uom_besar ON a_1.satuanbesar_id = uom_besar.satuanunit_id
                         JOIN ( SELECT a1.satuanunit_id,
                                a1.satuanunit_nama
                               FROM satuanunit_m a1) uom_kecil ON a_1.satuankecil_id = uom_kecil.satuanunit_id
                      GROUP BY a_1.obatalkes_id, a_1.satuankecil_id, a_1.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, a_1.nilai_konversi) uom_pr ON purchasereqdetail_t.obatalkes_id = uom_pr.obatalkes_id AND purchasereqdetail_t.satuan_id = uom_pr.satuanbesar_id AND purchasereqdetail_t.satuankonversi_id = uom_pr.satuankecil_id
                 JOIN ( SELECT a_1.obatalkes_id,
                        a_1.obatalkes_kode,
                        a_1.jenisobatalkes_id,
                        a_1.manufaktur_id,
                        a_1.obatalkes_nama,
                        a_1.is_deleted
                       FROM obatalkes_m a_1) obatalkes_m ON purchasereqdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
                 JOIN ( SELECT a_1.jenisobatalkes_id,
                        a_1.jenisobatalkes_nama
                       FROM jenisobatalkes_m a_1) jenisobatalkes_m ON obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id
                 LEFT JOIN ( SELECT a_1.satuanunit_id,
                        a_1.satuanunit_nama
                       FROM satuanunit_m a_1) kecil ON purchasereqdetail_t.satuankonversi_id = kecil.satuanunit_id
                 LEFT JOIN ( SELECT a_1.satuanunit_id,
                        a_1.satuanunit_nama
                       FROM satuanunit_m a_1) besar ON purchasereqdetail_t.satuan_id = besar.satuanunit_id
                 LEFT JOIN ( SELECT a_1.manufaktur_id,
                        a_1.nama
                       FROM manufaktur_m a_1) manufaktur_m ON obatalkes_m.manufaktur_id = manufaktur_m.manufaktur_id
                 LEFT JOIN ( SELECT a_1.purchasereqdetail_id,
                        a_1.validasipoobat_id
                       FROM validasipoobatdetail_t a_1) validasipoobatdetail_t ON purchasereqdetail_t.purchasereqdetail_id = validasipoobatdetail_t.purchasereqdetail_id
                 LEFT JOIN ( SELECT a_1.validasipoobat_id
                       FROM validasipoobat_t a_1) validasipoobat_t ON validasipoobatdetail_t.validasipoobat_id = validasipoobat_t.validasipoobat_id
              WHERE purchasereqdetail_t.status <> 720 AND validasipoobat_t.validasipoobat_id IS NULL) a;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220609_093027_migrate_ORDH_103_104_laporananalisapo_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220609_093027_migrate_ORDH_103_104_laporananalisapo_v cannot be reverted.\n";

        return false;
    }
    */
}
