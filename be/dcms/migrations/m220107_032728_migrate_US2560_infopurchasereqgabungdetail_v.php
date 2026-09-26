<?php

use yii\db\Migration;

/**
 * Class m220107_032728_migrate_US2560_infopurchasereqgabungdetail_v
 */
class m220107_032728_migrate_US2560_infopurchasereqgabungdetail_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP VIEW if exists "public"."infopurchasereqgabungdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopurchasereqgabungdetail_v\" AS  
	    SELECT 'OBAT'::text AS tipe,
	       purchasereq_t.purchasereq_id,
	       purchasereqdetail_t.purchasereqdetail_id,
	       purchasereq_t.no_pr,
	       purchasereqdetail_t.obatalkes_id AS item_id,
	       obatalkes_m.obatalkes_nama AS item_nama,
	       purchasereqdetail_t.qty_input,
	       purchasereqdetail_t.qty_konversi,
	       purchasereqdetail_t.satuan_id,
	       satuan_1.satuanunit_nama AS satuan,
	       purchasereqdetail_t.satuankonversi_id,
	       satuan_2.satuanunit_nama AS satuan_konversi,
	       purchasereqdetail_t.catatan,
	       (purchasereqdetail_t.qty_saatini)::double precision AS stok,
	       satuan_stok.satuanunit_nama AS satuan_stok,
	       fgetnamalookup((purchasereqdetail_t.status)::integer) AS status,
	       purchasereqdetail_t.status AS status_id,
	       uom.nilai_konversi,
	       concat('1 ', uom.uom_besar, ' = ', uom.nilai_konversi, ' ', uom.uom_kecil) AS uom,
	       purchasereqdetail_t.alasan,
	       kontrak_supplier.kontraksupplierdetail_id,
	           CASE
	               WHEN (kontrak_supplier.kontraksupplierdetail_id IS NOT NULL) THEN kontrak_supplier.satuankonv1_id
	               WHEN (kontrak_supplier.kontraksupplierdetail_id IS NULL) THEN uom.satuanbesar_id
	               ELSE NULL::integer
	           END AS satuanbesar_id,
	           CASE
	               WHEN (kontrak_supplier.kontraksupplierdetail_id IS NOT NULL) THEN kontrak_supplier.satuan_besar
	               WHEN (kontrak_supplier.kontraksupplierdetail_id IS NULL) THEN uom.uom_besar
	               ELSE NULL::character varying
	           END AS satuan_besar,
	       obatalkes_m.obatalkes_kode AS item_kode,
	       po.no_poobat AS nomor_po,
	       obatalkes_m.harganetto AS baseprice,
	       obatalkes_m.supplier_id AS defaultsupplier_id,
	       defaultsupplier.pajak_id AS defaultsupplierpajak_id,
	       defaultsupplierpajak.pajak_persen AS defaultsupplierpajakpersen,
	       obat_sisa.min_stok,
	       obat_sisa.max_stok,
	       fgetnamalookup(po.status_penerimaan) AS status_penerimaan,
	       purchasereqdetail_t.doi,
	       purchasereqdetail_t.ssmin,
	       purchasereqdetail_t.qty_sugesstion,
	       purchasereqdetail_t.qty_pr,
	       purchasereqdetail_t.stok_gudang,
	       purchasereqdetail_t.stok_farmasi,
	       purchasereqdetail_t.stok_ruanganlain,
	       purchasereqdetail_t.last_7,
	       purchasereqdetail_t.last_14,
	       purchasereqdetail_t.last_30,
	       purchasereqdetail_t.qty_outstanding
	      FROM (((((((((((purchasereq_t
	        JOIN purchasereqdetail_t ON ((purchasereq_t.purchasereq_id = purchasereqdetail_t.purchasereq_id)))
	        LEFT JOIN obatalkes_m ON ((purchasereqdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
	        LEFT JOIN supplier_m defaultsupplier ON ((defaultsupplier.supplier_id = obatalkes_m.supplier_id)))
	        LEFT JOIN pajak_m defaultsupplierpajak ON ((defaultsupplierpajak.pajak_id = defaultsupplier.pajak_id)))
	        LEFT JOIN satuanunit_m satuan_1 ON ((purchasereqdetail_t.satuan_id = satuan_1.satuanunit_id)))
	        LEFT JOIN satuanunit_m satuan_2 ON ((purchasereqdetail_t.satuankonversi_id = satuan_2.satuanunit_id)))
	        LEFT JOIN satuanunit_m satuan_stok ON ((obatalkes_m.satuankecil_id = satuan_stok.satuanunit_id)))
	        LEFT JOIN ( SELECT satuankonversi_m.obatalkes_id,
	               satuankonversi_m.satuankecil_id,
	               satuankonversi_m.satuanbesar_id,
	               uom_besar.satuanunit_nama AS uom_besar,
	               uom_kecil.satuanunit_nama AS uom_kecil,
	               satuankonversi_m.nilai_konversi
	              FROM ((satuankonversi_m
	                LEFT JOIN satuanunit_m uom_besar ON ((satuankonversi_m.satuanbesar_id = uom_besar.satuanunit_id)))
	                LEFT JOIN satuanunit_m uom_kecil ON ((satuankonversi_m.satuankecil_id = uom_kecil.satuanunit_id)))
	             WHERE ((satuankonversi_m.is_deleted = false) AND (satuankonversi_m.is_active = true))
	             GROUP BY satuankonversi_m.obatalkes_id, satuankonversi_m.satuankecil_id, satuankonversi_m.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, satuankonversi_m.nilai_konversi) uom ON (((purchasereqdetail_t.obatalkes_id = uom.obatalkes_id) AND (purchasereqdetail_t.satuan_id = uom.satuanbesar_id) AND (purchasereqdetail_t.satuankonversi_id = uom.satuankecil_id))))
	        LEFT JOIN ( SELECT stokobatalkes_r.obatalkes_id,
	               stokobatalkes_r.ruangan_id,
	               konfigrak_m.min_stok,
	               konfigrak_m.max_stok,
	               sum(stokobatalkes_r.qty_sisa) AS qty_sisa
	              FROM (stokobatalkes_r
	                JOIN konfigrak_m ON ((stokobatalkes_r.stokobatr_id = konfigrak_m.stokobatr_id)))
	             GROUP BY stokobatalkes_r.obatalkes_id, stokobatalkes_r.ruangan_id, konfigrak_m.min_stok, konfigrak_m.max_stok) obat_sisa ON (((purchasereqdetail_t.obatalkes_id = obat_sisa.obatalkes_id) AND (purchasereq_t.ruangan_id = obat_sisa.ruangan_id))))
	        LEFT JOIN ( SELECT kontraksupplierdetail_m.obatalkes_id,
	               kontraksupplierdetail_m.satuankecil_id,
	               kontraksupplierdetail_m.satuankonv1_id,
	               array_agg(kontraksupplierdetail_m.kontraksupplierdetail_id) AS kontraksupplierdetail_id,
	               satuan_besar.satuanunit_nama AS satuan_besar
	              FROM (kontraksupplierdetail_m
	                LEFT JOIN satuanunit_m satuan_besar ON ((kontraksupplierdetail_m.satuankonv1_id = satuan_besar.satuanunit_id)))
	             WHERE ((kontraksupplierdetail_m.is_deleted = false) AND (kontraksupplierdetail_m.is_active = true))
	             GROUP BY kontraksupplierdetail_m.obatalkes_id, kontraksupplierdetail_m.satuankecil_id, kontraksupplierdetail_m.satuankonv1_id, satuan_besar.satuanunit_nama) kontrak_supplier ON (((purchasereqdetail_t.obatalkes_id = kontrak_supplier.obatalkes_id) AND (purchasereqdetail_t.satuan_id = kontrak_supplier.satuankonv1_id) AND (purchasereqdetail_t.satuankonversi_id = kontrak_supplier.satuankecil_id))))
	        LEFT JOIN ( SELECT validasipoobat_t.no_poobat,
	               validasipoobatdetail_t.purchasereqdetail_id,
	               validasipoobat_t.status_penerimaan
	              FROM (validasipoobat_t
	                JOIN validasipoobatdetail_t ON ((validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id)))) po ON ((purchasereqdetail_t.purchasereqdetail_id = po.purchasereqdetail_id)))
	     WHERE ((purchasereq_t.is_deleted = false) AND (purchasereqdetail_t.is_deleted = false))
	   UNION ALL
	    SELECT 'BARANG'::text AS tipe,
	       purchasereqbrg_t.purchasereqbrg_id AS purchasereq_id,
	       purchasereqbrgdetail_t.purchasereqbrgdetail_id AS purchasereqdetail_id,
	       purchasereqbrg_t.no_pr,
	       purchasereqbrgdetail_t.barang_id AS item_id,
	       barang_m.barang_nama AS item_nama,
	       purchasereqbrgdetail_t.qty_input,
	       purchasereqbrgdetail_t.qty_konversi,
	       purchasereqbrgdetail_t.satuan_id,
	       uom.uom_besar AS satuan,
	       purchasereqbrgdetail_t.satuankonversi_id,
	       uom.uom_kecil AS satuan_konversi,
	       purchasereqbrgdetail_t.catatan,
	       (purchasereqbrgdetail_t.qty_saatini)::double precision AS stok,
	       satuan_stok.satuanunit_nama AS satuan_stok,
	       fgetnamalookup((purchasereqbrgdetail_t.status)::integer) AS status,
	       purchasereqbrgdetail_t.status AS status_id,
	       uom.nilai_konversi,
	       concat('1 ', uom.uom_besar, ' = ', uom.nilai_konversi, ' ', uom.uom_kecil) AS uom,
	       purchasereqbrgdetail_t.alasan,
	       kontrak_supplier.kontraksupplierbrgdetail_id AS kontraksupplierdetail_id,
	           CASE
	               WHEN (kontrak_supplier.kontraksupplierbrgdetail_id IS NOT NULL) THEN kontrak_supplier.satuankonv1_id
	               WHEN (kontrak_supplier.kontraksupplierbrgdetail_id IS NULL) THEN uom.satuanbesar_id
	               ELSE NULL::integer
	           END AS satuanbesar_id,
	           CASE
	               WHEN (kontrak_supplier.kontraksupplierbrgdetail_id IS NOT NULL) THEN kontrak_supplier.satuan_besar
	               WHEN (kontrak_supplier.kontraksupplierbrgdetail_id IS NULL) THEN uom.uom_besar
	               ELSE NULL::character varying
	           END AS satuan_besar,
	       barang_m.barang_kode AS item_kode,
	       po.no_pobarang AS nomor_po,
	       barang_m.barang_harganetto AS baseprice,
	       barang_m.supplier_id AS defaultsupplier_id,
	       defaultsupplier.pajak_id AS defaultsupplierpajak_id,
	       defaultsupplierpajak.pajak_persen AS defaultsupplierpajakpersen,
	       0 AS min_stok,
	       0 AS max_stok,
	       fgetnamalookup(po.status_penerimaan) AS status_penerimaan,
	       NULL::integer AS doi,
	       NULL::numeric AS ssmin,
	       NULL::numeric AS qty_sugesstion,
	       purchasereqbrgdetail_t.qty_pr,
	           CASE
	               WHEN (kartustok.ruangan_id = purchasereqbrg_t.ruangan_id) THEN (kartustok.total)::numeric
	               ELSE (0)::numeric
	           END AS stok_gudang,
	       NULL::numeric AS stok_farmasi,
	       ((ruangan_lain.total_ruangan_lain - kartustok.total))::numeric AS stok_ruanganlain,
	       (brgseven.last_7)::numeric AS last_7,
	       (brgseven.last_14)::numeric AS last_14,
	       (brgseven.last_30)::numeric AS last_30,
	       NULL::numeric AS qty_outstanding
	      FROM ((((((((((((purchasereqbrg_t
	        JOIN purchasereqbrgdetail_t ON ((purchasereqbrg_t.purchasereqbrg_id = purchasereqbrgdetail_t.purchasereqbrg_id)))
	        LEFT JOIN barang_m ON ((purchasereqbrgdetail_t.barang_id = barang_m.barang_id)))
	        LEFT JOIN supplier_m defaultsupplier ON ((defaultsupplier.supplier_id = barang_m.supplier_id)))
	        LEFT JOIN pajak_m defaultsupplierpajak ON ((defaultsupplierpajak.pajak_id = defaultsupplier.pajak_id)))
	        LEFT JOIN satuanunit_m satuan_stok ON ((barang_m.satuankecil_id = satuan_stok.satuanunit_id)))
	        LEFT JOIN ( SELECT satuankonversibrg_m.barang_id,
	               satuankonversibrg_m.satuankecil_id,
	               satuankonversibrg_m.satuanbesar_id,
	               uom_besar.satuanunit_nama AS uom_besar,
	               uom_kecil.satuanunit_nama AS uom_kecil,
	               satuankonversibrg_m.nilai_konversi
	              FROM ((satuankonversibrg_m
	                LEFT JOIN satuanunit_m uom_besar ON ((satuankonversibrg_m.satuanbesar_id = uom_besar.satuanunit_id)))
	                LEFT JOIN satuanunit_m uom_kecil ON ((satuankonversibrg_m.satuankecil_id = uom_kecil.satuanunit_id)))
	             WHERE ((satuankonversibrg_m.is_deleted = false) AND (satuankonversibrg_m.is_active = true))
	             GROUP BY satuankonversibrg_m.barang_id, satuankonversibrg_m.satuankecil_id, satuankonversibrg_m.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, satuankonversibrg_m.nilai_konversi) uom ON (((purchasereqbrgdetail_t.barang_id = uom.barang_id) AND (purchasereqbrgdetail_t.satuan_id = uom.satuanbesar_id) AND (purchasereqbrgdetail_t.satuankonversi_id = uom.satuankecil_id))))
	        LEFT JOIN ( SELECT stokbarang_r.barang_id,
	               stokbarang_r.ruangan_id,
	               sum(stokbarang_r.qty_sisa) AS qty_sisa
	              FROM stokbarang_r
	             GROUP BY stokbarang_r.barang_id, stokbarang_r.ruangan_id) obat_sisa ON (((purchasereqbrgdetail_t.barang_id = obat_sisa.barang_id) AND (purchasereqbrg_t.ruangan_id = obat_sisa.ruangan_id))))
	        LEFT JOIN ( SELECT kontraksupplierbrgdetail_m.barang_id,
	               kontraksupplierbrgdetail_m.satuankecil_id,
	               kontraksupplierbrgdetail_m.satuankonv1_id,
	               array_agg(kontraksupplierbrgdetail_m.kontraksupplierbrgdetail_id) AS kontraksupplierbrgdetail_id,
	               satuan_besar.satuanunit_nama AS satuan_besar
	              FROM (kontraksupplierbrgdetail_m
	                LEFT JOIN satuanunit_m satuan_besar ON ((kontraksupplierbrgdetail_m.satuankonv1_id = satuan_besar.satuanunit_id)))
	             WHERE ((kontraksupplierbrgdetail_m.is_deleted = false) AND (kontraksupplierbrgdetail_m.is_active = true))
	             GROUP BY kontraksupplierbrgdetail_m.barang_id, kontraksupplierbrgdetail_m.satuankecil_id, kontraksupplierbrgdetail_m.satuankonv1_id, satuan_besar.satuanunit_nama) kontrak_supplier ON (((purchasereqbrgdetail_t.barang_id = kontrak_supplier.barang_id) AND (purchasereqbrgdetail_t.satuan_id = kontrak_supplier.satuankonv1_id) AND (purchasereqbrgdetail_t.satuankonversi_id = kontrak_supplier.satuankecil_id))))
	        LEFT JOIN ( SELECT validasipobarang_t.no_pobarang,
	               validasipobarangdetail_t.purchasereqbrgdetail_id,
	               validasipobarang_t.status_penerimaan
	              FROM (validasipobarang_t
	                JOIN validasipobarangdetail_t ON ((validasipobarang_t.validasipobarang_id = validasipobarangdetail_t.validasipobarang_id)))) po ON ((purchasereqbrgdetail_t.purchasereqbrgdetail_id = po.purchasereqbrgdetail_id)))
	        LEFT JOIN ( SELECT st.ruangan_id,
	               st.barang_id,
	               sum((st.qtystok_in - st.qtystok_out)) AS total
	              FROM stokbarang_t st
	             GROUP BY st.ruangan_id, st.barang_id) kartustok ON (((kartustok.ruangan_id = purchasereqbrg_t.ruangan_id) AND (kartustok.barang_id = purchasereqbrgdetail_t.barang_id))))
	        LEFT JOIN ( SELECT st.barang_id,
	               sum((st.qtystok_in - st.qtystok_out)) AS total_ruangan_lain
	              FROM stokbarang_t st
	             GROUP BY st.barang_id) ruangan_lain ON ((ruangan_lain.barang_id = purchasereqbrgdetail_t.barang_id)))
	        LEFT JOIN ( SELECT count_stok.barang_id,
	               count_stok.ruangan_id,
	               sum(count_stok.last_7) AS last_7,
	               sum(count_stok.last_14) AS last_14,
	               sum(count_stok.last_30) AS last_30
	              FROM ( SELECT detail.barang_id,
	                       detail.ruangan_id,
	                       sum(
	                           CASE
	                               WHEN (detail.tanggal >= (CURRENT_DATE - '7 days'::interval)) THEN detail.qtystok_out
	                               ELSE (0)::double precision
	                           END) AS last_7,
	                       sum(
	                           CASE
	                               WHEN (detail.tanggal >= (CURRENT_DATE - '14 days'::interval)) THEN detail.qtystok_out
	                               ELSE (0)::double precision
	                           END) AS last_14,
	                       sum(
	                           CASE
	                               WHEN (detail.tanggal >= (CURRENT_DATE - '30 days'::interval)) THEN detail.qtystok_out
	                               ELSE (0)::double precision
	                           END) AS last_30
	                      FROM ( SELECT a.barang_id,
	                               a.ruangan_id,
	                               (to_char(a.tglstok_out, 'YYYY-MM-DD'::text))::date AS tanggal,
	                               a.qtystok_out
	                              FROM stokbarang_t a
	                             WHERE (((a.tglstok_out >= (CURRENT_DATE)::timestamp without time zone) AND (a.tglstok_out <= (CURRENT_DATE - '30 days'::interval))) OR ((a.tglstok_out >= (CURRENT_DATE - '30 days'::interval)) AND (a.tglstok_out <= (CURRENT_DATE)::timestamp without time zone)))) detail
	                     WHERE (detail.qtystok_out > (0)::double precision)
	                     GROUP BY detail.barang_id, detail.ruangan_id, detail.tanggal) count_stok
	             GROUP BY count_stok.barang_id, count_stok.ruangan_id) brgseven ON (((brgseven.ruangan_id = purchasereqbrg_t.ruangan_id) AND (brgseven.barang_id = purchasereqbrgdetail_t.barang_id))))
	     WHERE ((purchasereqbrg_t.is_deleted = false) AND (purchasereqbrgdetail_t.is_deleted = false))	;");

        $this->execute('ALTER TABLE "public"."infopurchasereqgabungdetail_v" OWNER TO "postgres";');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220107_032728_migrate_US2560_infopurchasereqgabungdetail_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220107_032728_migrate_US2560_infopurchasereqgabungdetail_v cannot be reverted.\n";

        return false;
    }
    */
}
