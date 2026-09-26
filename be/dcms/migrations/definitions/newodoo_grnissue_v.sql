-- public.newodoo_grnissue_v source

CREATE OR REPLACE VIEW public.newodoo_grnissue_v
AS SELECT 'obat'::text AS jenis,
    concat('RPOS', returpenerimaanobat_r.returpenerimaanobat_id) AS sync_id_api,
    concat(retur_detail.no_penerimaan, '-', returpenerimaanobat_r.no_returpenerimaanobat) AS origin,
    retur_detail.no_penerimaan AS receipt_no,
    returpenerimaanobat_r.no_returpenerimaanobat AS title,
    returpenerimaanobat_r.no_returpenerimaanobat AS name,
    returpenerimaanobat_r.no_returpenerimaanobat AS rfq,
    returpenerimaanobat_r.no_returpenerimaanobat AS purchase_name,
    concat(retur_detail.supplier_nama, '-', retur_detail.no_penerimaan) AS vendor_ref,
    13 AS currency_id,
    returpenerimaanobat_r.tgl_retur AS date_order,
    concat('SUP', retur_detail.supplier_id) AS partner_id,
    'incoming'::text AS picking_type_id,
    'no'::text AS invoice_status,
    retur_detail.amount_untaxed,
    retur_detail.amount_tax,
    retur_detail.total_discount,
    retur_detail.total_tampilan,
    retur_detail.amount_total,
    0 AS subtotal,
    0 AS total_ppn,
    'draft'::text AS state,
    returpenerimaanobat_r.tgl_retur AS date_planned,
    true AS is_return,
    retur_detail.ppn,
    0 AS pph,
    returpenerimaanobat_r.tgl_retur AS wipro_date,
    true AS no_approval,
    retur_detail.is_consigment AS is_consignment,
    'RPOS'::text AS tipe_rekap,
    returpenerimaanobat_r.id,
    NULL::text AS batch_no,
    NULL::text AS batch_id,
    NULL::text AS expiry_date,
    retur_detail.ruanganpenerima_id AS location_id
   FROM returpenerimaanobat_r
     JOIN ( SELECT returpenerimaanobatdetail_t.returpenerimaanobat_id,
            penerimaansupp_t.no_penerimaan,
            penerimaansupp_t.supplier_id,
            penerimaansupp_t.ruanganpenerima_id,
                CASE
                    WHEN penerimaansupp_t.is_consigment = true THEN true
                    ELSE false
                END AS is_consigment,
            supplier_m.supplier_nama,
            sum(obatalkes_m.harganetto / returpenerimaanobatdetail_t.qty_retur::double precision * (penerimaansuppdetail_t.diskon / 100::numeric)::double precision) AS amount_untaxed,
            sum((penerimaansuppdetail_t.harga_netto / returpenerimaanobatdetail_t.qty_retur::double precision - penerimaansuppdetail_t.harga_netto / returpenerimaanobatdetail_t.qty_retur::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision) * pajak_m.pajak_persen::double precision / 100::double precision) AS amount_tax,
            sum(penerimaansuppdetail_t.harga_netto / returpenerimaanobatdetail_t.qty_retur::double precision * (penerimaansuppdetail_t.diskon / 100::numeric)::double precision * returpenerimaanobatdetail_t.qty_retur::double precision) AS total_discount,
            sum(penerimaansuppdetail_t.harga_netto) AS total_tampilan,
            sum(penerimaansuppdetail_t.harga_netto / returpenerimaanobatdetail_t.qty_retur::double precision * returpenerimaanobatdetail_t.qty_retur::double precision - penerimaansuppdetail_t.harga_netto / returpenerimaanobatdetail_t.qty_retur::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision * returpenerimaanobatdetail_t.qty_retur::double precision + (penerimaansuppdetail_t.harga_netto / returpenerimaanobatdetail_t.qty_retur::double precision - penerimaansuppdetail_t.harga_netto / returpenerimaanobatdetail_t.qty_retur::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision) * pajak_m.pajak_persen::double precision / 100::double precision * returpenerimaanobatdetail_t.qty_retur::double precision) AS amount_total,
            pajak_m.pajak_persen AS ppn,
            penerimaansuppdetail_t.tgl_kadaluarsa
           FROM returpenerimaanobatdetail_t
             JOIN penerimaansuppdetail_t ON returpenerimaanobatdetail_t.penerimaansuppdetail_id = penerimaansuppdetail_t.penerimaansuppdetail_id
             JOIN penerimaansupp_t ON penerimaansuppdetail_t.penerimaansupp_id = penerimaansupp_t.penerimaansupp_id
             JOIN obatalkes_m ON returpenerimaanobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
             JOIN pajak_m ON penerimaansupp_t.pajak_id = pajak_m.pajak_id
             LEFT JOIN supplier_m ON penerimaansupp_t.supplier_id = supplier_m.supplier_id
          GROUP BY returpenerimaanobatdetail_t.returpenerimaanobat_id, penerimaansupp_t.no_penerimaan, penerimaansupp_t.supplier_id, penerimaansupp_t.ruanganpenerima_id, penerimaansupp_t.is_consigment, supplier_m.supplier_nama, pajak_m.pajak_persen, penerimaansuppdetail_t.tgl_kadaluarsa) retur_detail ON returpenerimaanobat_r.returpenerimaanobat_id = retur_detail.returpenerimaanobat_id
UNION ALL
 SELECT 'obat'::text AS jenis,
    concat('RPOM', returpenerimaanobat_r.returpenerimaanobat_id) AS sync_id_api,
    concat(retur_detail.no_penerimaan, '-', returpenerimaanobat_r.no_returpenerimaanobat) AS origin,
    retur_detail.no_penerimaan AS receipt_no,
    returpenerimaanobat_r.no_returpenerimaanobat AS title,
    returpenerimaanobat_r.no_returpenerimaanobat AS name,
    returpenerimaanobat_r.no_returpenerimaanobat AS rfq,
    returpenerimaanobat_r.no_returpenerimaanobat AS purchase_name,
    concat(retur_detail.supplier_nama, '-', retur_detail.no_poobat) AS vendor_ref,
    13 AS currency_id,
    returpenerimaanobat_r.tgl_retur AS date_order,
    concat('SUP', retur_detail.supplier_id) AS partner_id,
    'incoming'::text AS picking_type_id,
    'no'::text AS invoice_status,
    retur_detail.amount_untaxed,
    retur_detail.amount_tax,
    retur_detail.total_discount,
    retur_detail.total_tampilan,
    retur_detail.amount_total,
    retur_detail.subtotal,
    retur_detail.total_ppn,
    'draft'::text AS state,
    returpenerimaanobat_r.tgl_retur AS date_planned,
    true AS is_return,
    retur_detail.ppn,
    0 AS pph,
    returpenerimaanobat_r.tgl_retur AS wipro_date,
    true AS no_approval,
    retur_detail.is_consigment AS is_consignment,
    'RPOM'::text AS tipe_rekap,
    returpenerimaanobat_r.id,
    NULL::text AS batch_no,
    NULL::text AS batch_id,
    NULL::text AS expiry_date,
    retur_detail.ruanganpenerima_id AS location_id
   FROM returpenerimaanobat_r
     JOIN ( SELECT returpenerimaanobatdetail_t.returpenerimaanobat_id,
            penerimaanobat_t.no_penerimaan,
            po.no_poobat,
            po.is_consigment,
            penerimaanobat_t.supplier_id,
            penerimaanobat_t.ruanganpenerima_id,
            supplier_m.supplier_nama,
            sum(qty_konversi.qty_konversi::double precision * qty_konversi.harga_konversi) AS subtotal,
            sum((qty_konversi.harga_konversi - qty_konversi.harga_konversi * penerimaanobatdetail_t.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * qty_konversi.qty_konversi::double precision) AS total_ppn,
            sum(qty_konversi.harga_konversi * qty_konversi.qty_konversi::double precision - qty_konversi.harga_konversi * penerimaanobatdetail_t.discount / 100::double precision * qty_konversi.qty_konversi::double precision) AS amount_untaxed,
            sum((qty_konversi.harga_konversi - qty_konversi.harga_konversi * penerimaanobatdetail_t.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision) AS amount_tax,
            sum(qty_konversi.harga_konversi * penerimaanobatdetail_t.discount / 100::double precision * qty_konversi.qty_konversi::double precision) AS total_discount,
            sum(qty_konversi.harga_konversi) AS total_tampilan,
            sum(qty_konversi.harga_konversi * qty_konversi.qty_konversi::double precision - qty_konversi.harga_konversi * penerimaanobatdetail_t.discount / 100::double precision * qty_konversi.qty_konversi::double precision + (qty_konversi.harga_konversi - qty_konversi.harga_konversi * penerimaanobatdetail_t.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * qty_konversi.qty_konversi::double precision) AS amount_total,
            po.pajak_persen AS ppn
           FROM returpenerimaanobatdetail_t
             JOIN penerimaanobatdetail_t ON returpenerimaanobatdetail_t.penerimaanobatdetail_id = penerimaanobatdetail_t.penerimaanobatdetail_id
             JOIN penerimaanobat_t ON penerimaanobatdetail_t.penerimaanobat_id = penerimaanobat_t.penerimaanobat_id
             JOIN obatalkes_m ON returpenerimaanobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
             LEFT JOIN supplier_m ON penerimaanobat_t.supplier_id = supplier_m.supplier_id
             LEFT JOIN ( SELECT validasipoobat_t.validasipoobat_id,
                    pajak_m.pajak_persen,
                    validasipoobat_t.no_poobat,
                    validasipoobat_t.is_consigment
                   FROM validasipoobat_t
                     JOIN pajak_m ON validasipoobat_t.pajak_id = pajak_m.pajak_id
                  WHERE validasipoobat_t.is_deleted = false) po ON penerimaanobat_t.validasipoobat_id = po.validasipoobat_id
             LEFT JOIN ( SELECT penerimaanobatdetail_t_1.penerimaanobatdetail_id,
                    returpenerimaanobatdetail_t_1.qty_retur AS qty_konversi,
                    penerimaanobatdetail_t_1.harga / satuankonversi_m.nilai_konversi AS harga_konversi
                   FROM penerimaanobatdetail_t penerimaanobatdetail_t_1
                     JOIN returpenerimaanobatdetail_t returpenerimaanobatdetail_t_1 ON penerimaanobatdetail_t_1.penerimaanobatdetail_id = returpenerimaanobatdetail_t_1.penerimaanobatdetail_id
                     JOIN satuankonversi_m ON penerimaanobatdetail_t_1.s_konversiobt_id = satuankonversi_m.satuankonversi_id
                  WHERE satuankonversi_m.is_deleted = false) qty_konversi ON penerimaanobatdetail_t.penerimaanobatdetail_id = qty_konversi.penerimaanobatdetail_id
          GROUP BY returpenerimaanobatdetail_t.returpenerimaanobat_id, penerimaanobat_t.no_penerimaan, po.no_poobat, penerimaanobat_t.supplier_id, penerimaanobat_t.ruanganpenerima_id, supplier_m.supplier_nama, po.pajak_persen, po.is_consigment) retur_detail ON returpenerimaanobat_r.returpenerimaanobat_id = retur_detail.returpenerimaanobat_id
UNION ALL
 SELECT 'barang'::text AS jenis,
    concat('RPBM', returpenerimaanbarang_r.returpenerimaanbarang_id) AS sync_id_api,
    concat(retur_detail.no_penerimaan, '-', returpenerimaanbarang_r.no_returpenerimaanbarang) AS origin,
    retur_detail.no_penerimaan AS receipt_no,
    returpenerimaanbarang_r.no_returpenerimaanbarang AS title,
    returpenerimaanbarang_r.no_returpenerimaanbarang AS name,
    returpenerimaanbarang_r.no_returpenerimaanbarang AS rfq,
    returpenerimaanbarang_r.no_returpenerimaanbarang AS purchase_name,
    concat(retur_detail.supplier_nama, '-', retur_detail.no_pobarang) AS vendor_ref,
    13 AS currency_id,
    returpenerimaanbarang_r.tgl_retur AS date_order,
    concat('SUP', retur_detail.supplier_id) AS partner_id,
    'incoming'::text AS picking_type_id,
    'no'::text AS invoice_status,
    retur_detail.amount_untaxed,
    retur_detail.amount_tax,
    retur_detail.total_discount,
    retur_detail.total_tampilan,
    retur_detail.amount_total,
    0 AS subtotal,
    0 AS total_ppn,
    'draft'::text AS state,
    returpenerimaanbarang_r.tgl_retur AS date_planned,
    true AS is_return,
    retur_detail.ppn,
    0 AS pph,
    returpenerimaanbarang_r.tgl_retur AS wipro_date,
    true AS no_approval,
    false AS is_consignment,
    'RPBM'::text AS tipe_rekap,
    returpenerimaanbarang_r.id,
    NULL::text AS batch_no,
    NULL::text AS batch_id,
    NULL::text AS expiry_date,
    retur_detail.ruanganpenerima_id AS location_id
   FROM returpenerimaanbarang_r
     JOIN ( SELECT returpenerimaanbarangdetail_t.returpenerimaanbarang_id,
            penerimaanbarang_t.no_penerimaan,
            po.no_pobarang,
            penerimaanbarang_t.supplier_id,
            penerimaanbarang_t.ruanganpenerima_id,
            supplier_m.supplier_nama,
            sum(qty_konversi.harga_konversi * penerimaanbarangdetail_t.discount / 100::double precision) AS amount_untaxed,
            sum((qty_konversi.harga_konversi - qty_konversi.harga_konversi * penerimaanbarangdetail_t.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision) AS amount_tax,
            sum(qty_konversi.harga_konversi * penerimaanbarangdetail_t.discount / 100::double precision * qty_konversi.qty_konversi::double precision) AS total_discount,
            sum(qty_konversi.harga_konversi) AS total_tampilan,
            sum(qty_konversi.harga_konversi * qty_konversi.qty_konversi::double precision - qty_konversi.harga_konversi * penerimaanbarangdetail_t.discount / 100::double precision * qty_konversi.qty_konversi::double precision + (qty_konversi.harga_konversi - qty_konversi.harga_konversi * penerimaanbarangdetail_t.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * qty_konversi.qty_konversi::double precision) AS amount_total,
            po.pajak_persen AS ppn,
            penerimaanbarangdetail_t.tgl_kadaluarsa
           FROM returpenerimaanbarangdetail_t
             JOIN penerimaanbarangdetail_t ON returpenerimaanbarangdetail_t.penerimaanbarangdetail_id = penerimaanbarangdetail_t.penerimaanbarangdetail_id
             JOIN penerimaanbarang_t ON penerimaanbarangdetail_t.penerimaanbarang_id = penerimaanbarang_t.penerimaanbarang_id
             JOIN barang_m ON returpenerimaanbarangdetail_t.barang_id = barang_m.barang_id
             LEFT JOIN supplier_m ON penerimaanbarang_t.supplier_id = supplier_m.supplier_id
             LEFT JOIN ( SELECT validasipobarang_t.validasipobarang_id,
                    pajak_m.pajak_persen,
                    validasipobarang_t.no_pobarang
                   FROM validasipobarang_t
                     JOIN pajak_m ON validasipobarang_t.pajak_id = pajak_m.pajak_id
                  WHERE validasipobarang_t.is_deleted = false) po ON penerimaanbarang_t.validasipobarang_id = po.validasipobarang_id
             LEFT JOIN ( SELECT penerimaanbarangdetail_t_1.penerimaanbarangdetail_id,
                    returpenerimaanbarangdetail_t_1.qty_retur AS qty_konversi,
                    penerimaanbarangdetail_t_1.harga / satuankonversibrg_m.nilai_konversi AS harga_konversi
                   FROM penerimaanbarangdetail_t penerimaanbarangdetail_t_1
                     JOIN returpenerimaanbarangdetail_t returpenerimaanbarangdetail_t_1 ON penerimaanbarangdetail_t_1.penerimaanbarangdetail_id = returpenerimaanbarangdetail_t_1.penerimaanbarangdetail_id
                     JOIN satuankonversibrg_m ON penerimaanbarangdetail_t_1.s_konversibrg_id = satuankonversibrg_m.satuankonversibrg_id
                  WHERE satuankonversibrg_m.is_deleted = false) qty_konversi ON penerimaanbarangdetail_t.penerimaanbarangdetail_id = qty_konversi.penerimaanbarangdetail_id
          GROUP BY returpenerimaanbarangdetail_t.returpenerimaanbarang_id, penerimaanbarang_t.no_penerimaan, po.no_pobarang, penerimaanbarang_t.supplier_id, penerimaanbarang_t.ruanganpenerima_id, supplier_m.supplier_nama, po.pajak_persen, penerimaanbarangdetail_t.tgl_kadaluarsa) retur_detail ON returpenerimaanbarang_r.returpenerimaanbarang_id = retur_detail.returpenerimaanbarang_id
UNION ALL
 SELECT 'barang'::text AS jenis,
    concat('RPBS', returpenerimaanbarang_r.returpenerimaanbarang_id) AS sync_id_api,
    concat(retur_detail.no_penerimaan, '-', returpenerimaanbarang_r.no_returpenerimaanbarang) AS origin,
    retur_detail.no_penerimaan AS receipt_no,
    returpenerimaanbarang_r.no_returpenerimaanbarang AS title,
    returpenerimaanbarang_r.no_returpenerimaanbarang AS name,
    returpenerimaanbarang_r.no_returpenerimaanbarang AS rfq,
    returpenerimaanbarang_r.no_returpenerimaanbarang AS purchase_name,
    concat(retur_detail.supplier_nama, '-', retur_detail.no_penerimaan) AS vendor_ref,
    13 AS currency_id,
    returpenerimaanbarang_r.tgl_retur AS date_order,
    concat('SUP', retur_detail.supplier_id) AS partner_id,
    'incoming'::text AS picking_type_id,
    'no'::text AS invoice_status,
    retur_detail.amount_untaxed,
    retur_detail.amount_tax,
    retur_detail.total_discount,
    retur_detail.total_tampilan,
    retur_detail.amount_total,
    0 AS subtotal,
    0 AS total_ppn,
    'draft'::text AS state,
    returpenerimaanbarang_r.tgl_retur AS date_planned,
    true AS is_return,
    retur_detail.ppn,
    0 AS pph,
    returpenerimaanbarang_r.tgl_retur AS wipro_date,
    true AS no_approval,
    false AS is_consignment,
    'RPBS'::text AS tipe_rekap,
    returpenerimaanbarang_r.id,
    NULL::text AS batch_no,
    NULL::text AS batch_id,
    NULL::text AS expiry_date,
    retur_detail.ruanganpenerima_id AS location_id
   FROM returpenerimaanbarang_r
     JOIN ( SELECT returpenerimaanbarangdetail_t.returpenerimaanbarang_id,
            penerimaansupp_t.no_penerimaan,
            penerimaansupp_t.supplier_id,
            penerimaansupp_t.ruanganpenerima_id,
            supplier_m.supplier_nama,
            sum(barang_m.barang_harganetto / returpenerimaanbarangdetail_t.qty_retur::double precision * (penerimaansuppdetail_t.diskon / 100::numeric)::double precision) AS amount_untaxed,
            sum((penerimaansuppdetail_t.harga_netto / returpenerimaanbarangdetail_t.qty_retur::double precision - penerimaansuppdetail_t.harga_netto / returpenerimaanbarangdetail_t.qty_retur::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision) * pajak_m.pajak_persen::double precision / 100::double precision) AS amount_tax,
            sum(penerimaansuppdetail_t.harga_netto / returpenerimaanbarangdetail_t.qty_retur::double precision * (penerimaansuppdetail_t.diskon / 100::numeric)::double precision * returpenerimaanbarangdetail_t.qty_retur::double precision) AS total_discount,
            sum(penerimaansuppdetail_t.harga_netto) AS total_tampilan,
            sum(penerimaansuppdetail_t.harga_netto / returpenerimaanbarangdetail_t.qty_retur::double precision * returpenerimaanbarangdetail_t.qty_retur::double precision - penerimaansuppdetail_t.harga_netto / returpenerimaanbarangdetail_t.qty_retur::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision * returpenerimaanbarangdetail_t.qty_retur::double precision + (penerimaansuppdetail_t.harga_netto / returpenerimaanbarangdetail_t.qty_retur::double precision - penerimaansuppdetail_t.harga_netto / returpenerimaanbarangdetail_t.qty_retur::double precision * penerimaansuppdetail_t.diskon::double precision / 100::double precision) * pajak_m.pajak_persen::double precision / 100::double precision * returpenerimaanbarangdetail_t.qty_retur::double precision) AS amount_total,
            pajak_m.pajak_persen AS ppn,
            penerimaansuppdetail_t.tgl_kadaluarsa
           FROM returpenerimaanbarangdetail_t
             JOIN penerimaansuppdetail_t ON returpenerimaanbarangdetail_t.penerimaansuppbrgdetail_id = penerimaansuppdetail_t.penerimaansuppdetail_id
             JOIN penerimaansupp_t ON penerimaansuppdetail_t.penerimaansupp_id = penerimaansupp_t.penerimaansupp_id
             JOIN barang_m ON returpenerimaanbarangdetail_t.barang_id = barang_m.barang_id
             JOIN pajak_m ON penerimaansupp_t.pajak_id = pajak_m.pajak_id
             LEFT JOIN supplier_m ON penerimaansupp_t.supplier_id = supplier_m.supplier_id
          GROUP BY returpenerimaanbarangdetail_t.returpenerimaanbarang_id, penerimaansupp_t.no_penerimaan, penerimaansupp_t.supplier_id, penerimaansupp_t.ruanganpenerima_id, supplier_m.supplier_nama, pajak_m.pajak_persen, penerimaansuppdetail_t.tgl_kadaluarsa) retur_detail ON returpenerimaanbarang_r.returpenerimaanbarang_id = retur_detail.returpenerimaanbarang_id;