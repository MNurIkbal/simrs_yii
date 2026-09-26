-- public.newodoo_grnissuedetail_v source

CREATE OR REPLACE VIEW public.newodoo_grnissuedetail_v
AS SELECT 'obat'::text AS jenis,
    concat('RPOS', returpenerimaanobatdetail_r.returpenerimaanobatdetail_id) AS sync_id_api,
    concat('RPOS', returpenerimaanobat_r.returpenerimaanobat_id) AS picking_id,
    concat('RPOS', returpenerimaanobat_r.returpenerimaanobat_id) AS order_id,
    NULL::text AS sequence,
    concat('SUP', penerimaansupp_t.supplier_id) AS partner_id,
    concat('OBT', returpenerimaanobatdetail_r.obatalkes_id) AS product_id,
    concat('OBT', obatalkes_m.jenisobatalkes_id) AS product_categ_id,
    obatalkes_m.obatalkes_nama AS name,
    penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_kecil::double precision AS normal_price,
    penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_kecil::double precision AS price_unit,
    penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_kecil::double precision * (penerimaansuppdetail_t.diskon / 100::numeric)::double precision AS discount_value,
    penerimaansuppdetail_t.diskon AS discount_persen,
    penerimaansuppdetail_t.satuankecil_id AS product_uom,
    false AS is_conversion,
    obatalkes_m.is_consigment AS is_consignment,
    false AS converted,
    konversi.nilai_konversi::integer AS convertion_rate,
    returpenerimaanobatdetail_r.qty_retur::double precision * COALESCE(konversi.nilai_konversi, 1::double precision) AS product_qty,
    returpenerimaanobatdetail_r.qty_retur::double precision * COALESCE(konversi.nilai_konversi, 1::double precision) AS qty_received,
        CASE COALESCE(pajak_m.pajak_persen::integer, 0)
            WHEN 0 THEN NULL::text
            ELSE 'ppn'::text
        END AS taxes_id,
    returpenerimaanobatdetail_r.qty_retur::double precision * (penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_kecil::double precision - penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_kecil::double precision * (penerimaansuppdetail_t.diskon / 100::numeric)::double precision) * (pajak_m.pajak_persen::double precision / 100::double precision) AS price_tax,
    returpenerimaanobatdetail_r.qty_retur::double precision * (penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_kecil::double precision - penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_kecil::double precision * (penerimaansuppdetail_t.diskon / 100::numeric)::double precision) * (pajak_m.pajak_persen::double precision / 100::double precision) AS has_tax,
    returpenerimaanobatdetail_r.qty_retur::double precision * (penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_kecil::double precision - penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_kecil::double precision * (penerimaansuppdetail_t.diskon / 100::numeric)::double precision) + returpenerimaanobatdetail_r.qty_retur::double precision * (penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_kecil::double precision - penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_kecil::double precision * (penerimaansuppdetail_t.diskon / 100::numeric)::double precision) * (pajak_m.pajak_persen::double precision / 100::double precision) AS price_total,
    returpenerimaanobatdetail_r.qty_retur::double precision * (penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_kecil::double precision) - returpenerimaanobatdetail_r.qty_retur::double precision * (penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_kecil::double precision * (penerimaansuppdetail_t.diskon / 100::numeric)::double precision) AS price_subtotal,
    returpenerimaanobat_r.tgl_retur AS date_planned,
    13 AS currency_id,
    'draft'::text AS state,
    returpenerimaanobatdetail_r.id,
    'RPOS'::text AS tipe_rekap,
    NULL::text AS batch_no,
    NULL::text AS batch_id,
    penerimaansuppdetail_t.tgl_kadaluarsa AS expiry_date,
    pajak_m.pajak_persen AS ppn,
    0 AS pph,
    NULL::integer AS qty_grn,
    NULL::text AS uom_grn,
    NULL::integer AS uom_grn_id
   FROM returpenerimaanobatdetail_r
     JOIN returpenerimaanobat_r ON returpenerimaanobatdetail_r.returpenerimaanobat_id = returpenerimaanobat_r.returpenerimaanobat_id
     JOIN penerimaansupp_t ON returpenerimaanobat_r.panerimaanobatsupp_id = penerimaansupp_t.penerimaansupp_id
     JOIN penerimaansuppdetail_t ON returpenerimaanobatdetail_r.penerimaansuppdetail_id = penerimaansuppdetail_t.penerimaansuppdetail_id
     JOIN obatalkes_m ON returpenerimaanobatdetail_r.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN pajak_m ON penerimaansupp_t.pajak_id = pajak_m.pajak_id
     LEFT JOIN satuankonversi_m konversi ON konversi.obatalkes_id = obatalkes_m.obatalkes_id AND konversi.satuankecil_id = obatalkes_m.satuankecil_id AND konversi.satuanbesar_id = returpenerimaanobatdetail_r.satuanbesar_id AND konversi.is_deleted = false AND konversi.is_active = true
UNION ALL
 SELECT 'obat'::text AS jenis,
    concat('RPOM', returpenerimaanobatdetail_r.returpenerimaanobatdetail_id) AS sync_id_api,
    concat('RPOM', returpenerimaanobat_r.returpenerimaanobat_id) AS picking_id,
    concat('RPOM', returpenerimaanobat_r.returpenerimaanobat_id) AS order_id,
    NULL::text AS sequence,
    concat('SUP', penerimaanobat_t.supplier_id) AS partner_id,
    concat('OBT', returpenerimaanobatdetail_r.obatalkes_id) AS product_id,
    concat('OBT', obatalkes_m.jenisobatalkes_id) AS product_categ_id,
    obatalkes_m.obatalkes_nama AS name,
    penerimaanobatdetail.harga_konversi AS normal_price,
    penerimaanobatdetail.harga_konversi AS price_unit,
    penerimaanobatdetail.harga_konversi * penerimaanobatdetail.discount / 100::double precision AS discount_value,
    penerimaanobatdetail.discount AS discount_persen,
    satuan.satuankecil_id AS product_uom,
    false AS is_conversion,
    obatalkes_m.is_consigment AS is_consignment,
    false AS converted,
    1 AS convertion_rate,
    returpenerimaanobatdetail_r.qty_retur AS product_qty,
    returpenerimaanobatdetail_r.qty_retur AS qty_received,
        CASE COALESCE(po.pajak_persen::integer, 0)
            WHEN 0 THEN NULL::text
            ELSE 'ppn'::text
        END AS taxes_id,
    (penerimaanobatdetail.harga_konversi - penerimaanobatdetail.harga_konversi * penerimaanobatdetail.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * returpenerimaanobatdetail_r.qty_retur::double precision AS price_tax,
    (penerimaanobatdetail.harga_konversi - penerimaanobatdetail.harga_konversi * penerimaanobatdetail.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * returpenerimaanobatdetail_r.qty_retur::double precision AS has_tax,
    penerimaanobatdetail.harga_konversi * returpenerimaanobatdetail_r.qty_retur::double precision - penerimaanobatdetail.harga_konversi * penerimaanobatdetail.discount / 100::double precision * returpenerimaanobatdetail_r.qty_retur::double precision + (penerimaanobatdetail.harga_konversi - penerimaanobatdetail.harga_konversi * penerimaanobatdetail.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * returpenerimaanobatdetail_r.qty_retur::double precision AS price_total,
    penerimaanobatdetail.harga_konversi * returpenerimaanobatdetail_r.qty_retur::double precision - penerimaanobatdetail.harga_konversi * penerimaanobatdetail.discount / 100::double precision * returpenerimaanobatdetail_r.qty_retur::double precision + (penerimaanobatdetail.harga_konversi - penerimaanobatdetail.harga_konversi * penerimaanobatdetail.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * returpenerimaanobatdetail_r.qty_retur::double precision AS price_subtotal,
    returpenerimaanobat_r.tgl_retur AS date_planned,
    13 AS currency_id,
    'draft'::text AS state,
    returpenerimaanobatdetail_r.id,
    'RPOM'::text AS tipe_rekap,
    NULL::text AS batch_no,
    NULL::text AS batch_id,
    penerimaanobatdetail.tgl_kadaluarsa AS expiry_date,
    po.pajak_persen AS ppn,
    0 AS pph,
    penerimaanobatdetail.qty_grn,
    penerimaanobatdetail.uom_grn::text AS uom_grn,
    penerimaanobatdetail.uom_grn_id
   FROM returpenerimaanobatdetail_r
     JOIN returpenerimaanobat_r ON returpenerimaanobatdetail_r.returpenerimaanobat_id = returpenerimaanobat_r.returpenerimaanobat_id
     LEFT JOIN ( SELECT penerimaanobatdetail_t.penerimaanobatdetail_id,
            penerimaanobatdetail_t.qty_diterima::double precision * satuankonversi_m.nilai_konversi AS qty_konversi,
            penerimaanobatdetail_t.harga / satuankonversi_m.nilai_konversi AS harga_konversi,
            penerimaanobatdetail_t.s_konversiobt_id,
            penerimaanobatdetail_t.no_batch,
            penerimaanobatdetail_t.discount,
            penerimaanobatdetail_t.tgl_kadaluarsa,
            penerimaanobatdetail_t.qty_diterima AS qty_grn,
            sm.satuanunit_nama AS uom_grn,
            satuankonversi_m.satuanbesar_id AS uom_grn_id
           FROM penerimaanobatdetail_t
             JOIN satuankonversi_m ON penerimaanobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id
             LEFT JOIN satuanunit_m sm ON sm.satuanunit_id = satuankonversi_m.satuanbesar_id
          WHERE satuankonversi_m.is_deleted = false) penerimaanobatdetail ON returpenerimaanobatdetail_r.penerimaanobatdetail_id = penerimaanobatdetail.penerimaanobatdetail_id
     JOIN penerimaanobat_t ON returpenerimaanobatdetail_r.penerimaanobat_id = penerimaanobat_t.penerimaanobat_id
     JOIN obatalkes_m ON returpenerimaanobatdetail_r.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN ( SELECT validasipoobat_t.validasipoobat_id,
            validasipoobat_t.pajak_id,
            pajak_m.pajak_persen
           FROM validasipoobat_t
             JOIN pajak_m ON validasipoobat_t.pajak_id = pajak_m.pajak_id
          WHERE validasipoobat_t.is_deleted = false) po ON penerimaanobat_t.validasipoobat_id = po.validasipoobat_id
     LEFT JOIN ( SELECT satuankonversi_m.satuankonversi_id,
            satuankonversi_m.satuankecil_id,
            satuan_kecil.satuanunit_nama AS satuan_kecil
           FROM satuankonversi_m
             JOIN satuanunit_m satuan_kecil ON satuankonversi_m.satuankecil_id = satuan_kecil.satuanunit_id
          WHERE satuankonversi_m.is_deleted = false) satuan ON penerimaanobatdetail.s_konversiobt_id = satuan.satuankonversi_id
UNION ALL
 SELECT 'barang'::text AS jenis,
    concat('RPBM', returpenerimaanbarangdetail_r.returpenerimaanbarangdetail_id) AS sync_id_api,
    concat('RPBM', returpenerimaanbarang_r.returpenerimaanbarang_id) AS picking_id,
    concat('RPBM', returpenerimaanbarang_r.returpenerimaanbarang_id) AS order_id,
    NULL::text AS sequence,
    concat('SUP', penerimaanbarang_t.supplier_id) AS partner_id,
    concat('BRG', returpenerimaanbarangdetail_r.barang_id) AS product_id,
    concat('BRG', barang_m.kelompokbarang_id) AS product_categ_id,
    barang_m.barang_nama AS name,
    penerimaanbarangdetail.harga_konversi AS normal_price,
    penerimaanbarangdetail.harga_konversi AS price_unit,
    penerimaanbarangdetail.harga_konversi * penerimaanbarangdetail.discount / 100::double precision AS discount_value,
    penerimaanbarangdetail.discount AS discount_persen,
    satuan.satuankecil_id AS product_uom,
    false AS is_conversion,
        CASE
            WHEN "left"(barang_m.barang_kode::text, 3) = 'CGN'::text THEN true
            ELSE false
        END AS is_consignment,
    false AS converted,
    1 AS convertion_rate,
    returpenerimaanbarangdetail_r.qty_retur AS product_qty,
    returpenerimaanbarangdetail_r.qty_retur AS qty_received,
        CASE COALESCE(po.pajak_persen::integer, 0)
            WHEN 0 THEN NULL::text
            ELSE 'ppn'::text
        END AS taxes_id,
    (penerimaanbarangdetail.harga_konversi - penerimaanbarangdetail.harga_konversi * penerimaanbarangdetail.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * returpenerimaanbarangdetail_r.qty_retur::double precision AS price_tax,
    (penerimaanbarangdetail.harga_konversi - penerimaanbarangdetail.harga_konversi * penerimaanbarangdetail.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * returpenerimaanbarangdetail_r.qty_retur::double precision AS has_tax,
    penerimaanbarangdetail.harga_konversi * returpenerimaanbarangdetail_r.qty_retur::double precision - penerimaanbarangdetail.harga_konversi * penerimaanbarangdetail.discount / 100::double precision * returpenerimaanbarangdetail_r.qty_retur::double precision + (penerimaanbarangdetail.harga_konversi - penerimaanbarangdetail.harga_konversi * penerimaanbarangdetail.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * returpenerimaanbarangdetail_r.qty_retur::double precision AS price_total,
    penerimaanbarangdetail.harga_konversi * returpenerimaanbarangdetail_r.qty_retur::double precision - penerimaanbarangdetail.harga_konversi * penerimaanbarangdetail.discount / 100::double precision * returpenerimaanbarangdetail_r.qty_retur::double precision + (penerimaanbarangdetail.harga_konversi - penerimaanbarangdetail.harga_konversi * penerimaanbarangdetail.discount / 100::double precision) * po.pajak_persen::double precision / 100::double precision * returpenerimaanbarangdetail_r.qty_retur::double precision AS price_subtotal,
    returpenerimaanbarang_r.tgl_retur AS date_planned,
    13 AS currency_id,
    'draft'::text AS state,
    returpenerimaanbarangdetail_r.id,
    'RPBM'::text AS tipe_rekap,
    NULL::text AS batch_no,
    NULL::text AS batch_id,
    penerimaanbarangdetail.tgl_kadaluarsa AS expiry_date,
    po.pajak_persen AS ppn,
    0 AS pph,
    NULL::integer AS qty_grn,
    NULL::text AS uom_grn,
    NULL::integer AS uom_grn_id
   FROM returpenerimaanbarangdetail_r
     JOIN returpenerimaanbarang_r ON returpenerimaanbarangdetail_r.returpenerimaanbarang_id = returpenerimaanbarang_r.returpenerimaanbarang_id
     LEFT JOIN ( SELECT penerimaanbarangdetail_t.penerimaanbarangdetail_id,
            penerimaanbarangdetail_t.qty_diterima::double precision * satuankonversibrg_m.nilai_konversi AS qty_konversi,
            penerimaanbarangdetail_t.harga / satuankonversibrg_m.nilai_konversi AS harga_konversi,
            penerimaanbarangdetail_t.s_konversibrg_id,
            penerimaanbarangdetail_t.no_batch,
            penerimaanbarangdetail_t.discount,
            penerimaanbarangdetail_t.tgl_kadaluarsa
           FROM penerimaanbarangdetail_t
             JOIN satuankonversibrg_m ON penerimaanbarangdetail_t.s_konversibrg_id = satuankonversibrg_m.satuankonversibrg_id
          WHERE satuankonversibrg_m.is_deleted = false) penerimaanbarangdetail ON returpenerimaanbarangdetail_r.penerimaanbarangdetail_id = penerimaanbarangdetail.penerimaanbarangdetail_id
     JOIN penerimaanbarang_t ON returpenerimaanbarangdetail_r.penerimaanbarang_id = penerimaanbarang_t.penerimaanbarang_id
     JOIN barang_m ON returpenerimaanbarangdetail_r.barang_id = barang_m.barang_id
     LEFT JOIN ( SELECT validasipobarang_t.validasipobarang_id,
            validasipobarang_t.pajak_id,
            pajak_m.pajak_persen
           FROM validasipobarang_t
             JOIN pajak_m ON validasipobarang_t.pajak_id = pajak_m.pajak_id
          WHERE validasipobarang_t.is_deleted = false) po ON penerimaanbarang_t.validasipobarang_id = po.validasipobarang_id
     LEFT JOIN ( SELECT satuankonversibrg_m.satuankonversibrg_id,
            satuankonversibrg_m.satuankecil_id,
            satuan_kecil.satuanunit_nama AS satuan_kecil
           FROM satuankonversibrg_m
             JOIN satuanunit_m satuan_kecil ON satuankonversibrg_m.satuankecil_id = satuan_kecil.satuanunit_id
          WHERE satuankonversibrg_m.is_deleted = false) satuan ON penerimaanbarangdetail.s_konversibrg_id = satuan.satuankonversibrg_id
UNION ALL
 SELECT 'barang'::text AS jenis,
    concat('RPBS', returpenerimaanbarangdetail_r.returpenerimaanbarangdetail_id) AS sync_id_api,
    concat('RPBS', returpenerimaanbarang_r.returpenerimaanbarang_id) AS picking_id,
    concat('RPBS', returpenerimaanbarang_r.returpenerimaanbarang_id) AS order_id,
    NULL::text AS sequence,
    concat('SUP', penerimaansupp_t.supplier_id) AS partner_id,
    concat('BRG', returpenerimaanbarangdetail_r.barang_id) AS product_id,
    concat('BRG', barang_m.kelompokbarang_id) AS product_categ_id,
    barang_m.barang_nama AS name,
    penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_kecil::double precision AS normal_price,
    penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_kecil::double precision AS price_unit,
    penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_kecil::double precision * (penerimaansuppdetail_t.diskon / 100::numeric)::double precision AS discount_value,
    penerimaansuppdetail_t.diskon AS discount_persen,
    penerimaansuppdetail_t.satuankecil_id AS product_uom,
    false AS is_conversion,
        CASE
            WHEN "left"(barang_m.barang_kode::text, 3) = 'CGN'::text THEN true
            ELSE false
        END AS is_consignment,
    false AS converted,
    konversi.nilai_konversi::integer AS convertion_rate,
    returpenerimaanbarangdetail_r.qty_retur::double precision * konversi.nilai_konversi AS product_qty,
    returpenerimaanbarangdetail_r.qty_retur::double precision * konversi.nilai_konversi AS qty_received,
        CASE COALESCE(pajak_m.pajak_persen::integer, 0)
            WHEN 0 THEN NULL::text
            ELSE 'ppn'::text
        END AS taxes_id,
    returpenerimaanbarangdetail_r.qty_retur::double precision * konversi.nilai_konversi * (penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_kecil::double precision - penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_kecil::double precision * (penerimaansuppdetail_t.diskon / 100::numeric)::double precision) * (pajak_m.pajak_persen::double precision / 100::double precision) AS price_tax,
    returpenerimaanbarangdetail_r.qty_retur::double precision * konversi.nilai_konversi * (penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_kecil::double precision - penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_kecil::double precision * (penerimaansuppdetail_t.diskon / 100::numeric)::double precision) * (pajak_m.pajak_persen::double precision / 100::double precision) AS has_tax,
    returpenerimaanbarangdetail_r.qty_retur::double precision * konversi.nilai_konversi * (penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_kecil::double precision - penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_kecil::double precision * (penerimaansuppdetail_t.diskon / 100::numeric)::double precision) + returpenerimaanbarangdetail_r.qty_retur::double precision * (penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_kecil::double precision - penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_kecil::double precision * (penerimaansuppdetail_t.diskon / 100::numeric)::double precision) * (pajak_m.pajak_persen::double precision / 100::double precision) AS price_total,
    returpenerimaanbarangdetail_r.qty_retur::double precision * konversi.nilai_konversi * (penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_kecil::double precision) - returpenerimaanbarangdetail_r.qty_retur::double precision * (penerimaansuppdetail_t.harga_netto / penerimaansuppdetail_t.qty_kecil::double precision * (penerimaansuppdetail_t.diskon / 100::numeric)::double precision) AS price_subtotal,
    returpenerimaanbarang_r.tgl_retur AS date_planned,
    13 AS currency_id,
    'draft'::text AS state,
    returpenerimaanbarangdetail_r.id,
    'RPBS'::text AS tipe_rekap,
    NULL::text AS batch_no,
    NULL::text AS batch_id,
    penerimaansuppdetail_t.tgl_kadaluarsa AS expiry_date,
    pajak_m.pajak_persen AS ppn,
    0 AS pph,
    NULL::integer AS qty_grn,
    NULL::text AS uom_grn,
    NULL::integer AS uom_grn_id
   FROM returpenerimaanbarangdetail_r
     JOIN returpenerimaanbarang_r ON returpenerimaanbarangdetail_r.returpenerimaanbarang_id = returpenerimaanbarang_r.returpenerimaanbarang_id
     LEFT JOIN returpenerimaanbarang_t ON returpenerimaanbarang_t.returpenerimaanbarang_id = returpenerimaanbarang_r.returpenerimaanbarang_id
     JOIN penerimaansupp_t ON returpenerimaanbarang_t.penerimaansupp_id = penerimaansupp_t.penerimaansupp_id
     JOIN penerimaansuppdetail_t ON returpenerimaanbarangdetail_r.penerimaansuppbrgdetail_id = penerimaansuppdetail_t.penerimaansuppdetail_id
     JOIN barang_m ON returpenerimaanbarangdetail_r.barang_id = barang_m.barang_id
     JOIN pajak_m ON penerimaansupp_t.pajak_id = pajak_m.pajak_id
     LEFT JOIN satuankonversibrg_m konversi ON konversi.barang_id = barang_m.barang_id AND konversi.satuankecil_id = barang_m.satuankecil_id AND konversi.satuanbesar_id = returpenerimaanbarangdetail_r.satuanbesar_id AND konversi.is_deleted = false AND konversi.is_active = true;