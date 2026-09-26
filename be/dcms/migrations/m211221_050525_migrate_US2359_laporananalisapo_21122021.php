<?php

use yii\db\Migration;

/**
 * Class m211221_050525_migrate_US2359_laporananalisapo_21122021
 */
class m211221_050525_migrate_US2359_laporananalisapo_21122021 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
  	   	$this->execute('DROP VIEW if exists public.laporananalisapo_v;');
		
         $this->execute("
             CREATE VIEW \"public\".\"laporananalisapo_v\" AS
		 SELECT validasipoobat_t.validasipoobat_id AS no,
		    obatalkes_m.obatalkes_kode AS kode_obat,
		    obatalkes_m.obatalkes_nama AS nama_obat,
		    manufaktur_m.nama AS manufaktur,
		    jenisobatalkes_m.jenisobatalkes_nama AS jenis_obat,
		    purchasereq_t.no_pr,
		    purchasereq_t.tgl_pr,
		    purchasereqdetail_t.qty_input AS qty_pr,
		        CASE
		            WHEN (uom_pr.obatalkes_id IS NULL) THEN NULL::text
		            ELSE concat('1 ', uom_pr.uom_besar, ' = ', uom_pr.nilai_konversi, ' ', uom_pr.uom_kecil)
		        END AS uom_pr,
		    purchasereqdetail_t.catatan,
		    validasipoobat_t.no_poobat AS no_po,
		    validasipoobat_t.created_date AS tgl_po,
		    validasipoobat_t.tgl_validasi AS tgl_po_validasi,
		    validasipoobatdetail_t.qty_input AS qty_po,
		        CASE
		            WHEN (uom_po.obatalkes_id IS NULL) THEN NULL::text
		            ELSE concat('1 ', uom_po.uom_besar, ' = ', uom_po.nilai_konversi, ' ', uom_po.uom_kecil)
		        END AS uom_po,
		        CASE
		            WHEN (validasipoobat_t.status_penerimaan = 575) THEN validasipoobat_t.last_modified_date
		            ELSE NULL::timestamp without time zone
		        END AS tgl_batal_po,
		        CASE
		            WHEN (validasipoobat_t.status_penerimaan = 575) THEN (0)::double precision
		            ELSE validasipoobatdetail_t.harga
		        END AS harga,
		    validasipoobatdetail_t.discount AS disc_persen,
		    pajak_m.pajak_persen AS ppn_persen,
		        CASE
		            WHEN (validasipoobat_t.status_penerimaan = 575) THEN (0)::double precision
		            ELSE (validasipoobatdetail_t.harga * (validasipoobatdetail_t.qty_input)::double precision)
		        END AS sub_total,
		        CASE
		            WHEN (validasipoobat_t.status_penerimaan = 575) THEN (0)::double precision
		            ELSE (((validasipoobatdetail_t.harga * (validasipoobatdetail_t.qty_input)::double precision) - validasipoobatdetail_t.discount_rp) + ((((validasipoobatdetail_t.harga * (validasipoobatdetail_t.qty_input)::double precision) - validasipoobatdetail_t.discount_rp) * (pajak_m.pajak_persen)::double precision) / (100)::double precision))
		        END AS total,
		        CASE
		            WHEN (validasipoobatdetail_t.qty_penerimaan = validasipoobatdetail_t.qty_input) THEN 'Sudah Semua Diterima'::character varying
		            WHEN ((validasipoobatdetail_t.qty_penerimaan > 0) AND (validasipoobatdetail_t.qty_penerimaan < validasipoobatdetail_t.qty_input)) THEN 'Belum Semua Diterima'::character varying
		            WHEN (validasipoobatdetail_t.qty_penerimaan = 0) THEN 'Belum Diterima'::character varying
		            ELSE fgetnamalookup(validasipoobat_t.status_penerimaan)
		        END AS status_po,
		        CASE
		            WHEN (validasipoobat_t.status_penerimaan = 575) THEN validasipoobat_t.last_modified_date
		            ELSE NULL::timestamp without time zone
		        END AS tgl_po_batal,
		    validasipoobat_t.catatan AS catatan_batal,
		    supplier_m.supplier_kode AS kode_supplier,
		    supplier_m.supplier_nama AS nama_supplier,
		    penerimaan.tgl AS tgl_penerimaan,
		    validasipoobatdetail_t.qty_penerimaan,
		        CASE
		            WHEN (uom_terima.satuankonversi_id IS NULL) THEN NULL::text
		            ELSE concat('1 ', uom_terima.uom_besar, ' = ', uom_terima.nilai_konversi, ' ', uom_terima.uom_kecil)
		        END AS uom_penerimaan,
		    validasipoobatdetail_t.qty_sisa AS sisa_penerimaan,
		    validasipoobat_t.is_validasi,
		    COALESCE((date_part('day'::text, (validasipoobat_t.created_date)::date) - date_part('day'::text, purchasereq_t.tgl_pr)), (0)::double precision) AS pr_to_po,
		        CASE
		            WHEN (validasipoobat_t.is_validasi = true) THEN COALESCE((((validasipoobat_t.tgl_validasi)::date - purchasereq_t.tgl_pr))::double precision, (0)::double precision)
		            ELSE COALESCE((((validasipoobat_t.created_date)::date - purchasereq_t.tgl_pr))::double precision, (0)::double precision)
		        END AS pr_to_povalidasi,
		        CASE
		            WHEN (validasipoobat_t.is_validasi = true) THEN COALESCE((((validasipoobat_t.tgl_validasi)::date - (validasipoobat_t.created_date)::date))::double precision, (0)::double precision)
		            ELSE COALESCE((((validasipoobat_t.created_date)::date - (validasipoobat_t.created_date)::date))::double precision, (0)::double precision)
		        END AS po_to_povalidasi,
		    COALESCE((((penerimaan.tgl)::date - purchasereq_t.tgl_pr))::double precision, (0)::double precision) AS pr_to_penerimaan,
		        CASE
		            WHEN (validasipoobat_t.is_validasi = true) THEN COALESCE((((penerimaan.tgl)::date - (validasipoobat_t.tgl_validasi)::date))::double precision, (0)::double precision)
		            ELSE COALESCE((((penerimaan.tgl)::date - (validasipoobat_t.created_date)::date))::double precision, (0)::double precision)
		        END AS povalidasi_to_penerimaan,
		    uom_pr.uom_besar AS satuan_pr,
		    uom_po.uom_besar AS satuan_po,
		    uom_terima.uom_besar AS penerimaan,
		        CASE
		            WHEN (validasipoobat_t.status_penerimaan = 575) THEN 'Dibatalkan'::text
		            WHEN (validasipoobat_t.status_penerimaan = 572) THEN 'Belum Diterima'::text
		            WHEN (validasipoobat_t.status_penerimaan = 573) THEN 'Belum Semua Diterima'::text
		            WHEN (validasipoobat_t.status_penerimaan = 686) THEN 'Expired'::text
		            WHEN (validasipoobat_t.status_penerimaan = 574) THEN 'Sudah Semua Diterima'::text
		            WHEN (validasipoobat_t.status_penerimaan = 574) THEN 'Sudah Semua Diterima'::text
		            WHEN (validasipoobat_t.status_penerimaan = 580) THEN 'Closing Supplier'::text
		            ELSE NULL::text
		        END AS status_po_kondisi
		   FROM (((((((((((((((validasipoobat_t
		     JOIN ( SELECT a.validasipoobat_id,
		            a.purchasereqdetail_id,
		            a.obatalkes_id,
		            a.s_konversiobt_id,
		            a.validasipoobatdetail_id,
		            a.qty_input,
		            a.harga,
		            a.discount,
		            a.jumlah,
		            a.discount_rp,
		            a.qty_penerimaan,
		            a.qty_sisa
		           FROM validasipoobatdetail_t a) validasipoobatdetail_t ON ((validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id)))
		     LEFT JOIN ( SELECT a.purchasereqdetail_id,
		            a.purchasereq_id,
		            a.obatalkes_id,
		            a.satuan_id,
		            a.satuankonversi_id,
		            a.qty_konversi,
		            a.qty_input,
		            a.catatan
		           FROM purchasereqdetail_t a) purchasereqdetail_t ON ((validasipoobatdetail_t.purchasereqdetail_id = purchasereqdetail_t.purchasereqdetail_id)))
		     LEFT JOIN ( SELECT a.purchasereq_id,
		            a.no_pr,
		            a.tgl_pr
		           FROM purchasereq_t a) purchasereq_t ON ((purchasereqdetail_t.purchasereq_id = purchasereq_t.purchasereq_id)))
		     LEFT JOIN ( SELECT a.obatalkes_id,
		            a.satuankecil_id,
		            a.satuanbesar_id,
		            uom_besar.satuanunit_nama AS uom_besar,
		            uom_kecil.satuanunit_nama AS uom_kecil,
		            a.nilai_konversi
		           FROM ((satuankonversi_m a
		             JOIN ( SELECT a1.satuanunit_id,
		                    a1.satuanunit_nama
		                   FROM satuanunit_m a1) uom_besar ON ((a.satuanbesar_id = uom_besar.satuanunit_id)))
		             JOIN ( SELECT a1.satuanunit_id,
		                    a1.satuanunit_nama
		                   FROM satuanunit_m a1) uom_kecil ON ((a.satuankecil_id = uom_kecil.satuanunit_id)))
		          GROUP BY a.obatalkes_id, a.satuankecil_id, a.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, a.nilai_konversi) uom_pr ON (((purchasereqdetail_t.obatalkes_id = uom_pr.obatalkes_id) AND (purchasereqdetail_t.satuan_id = uom_pr.satuanbesar_id) AND (purchasereqdetail_t.satuankonversi_id = uom_pr.satuankecil_id))))
		     LEFT JOIN ( SELECT a.supplier_id,
		            a.supplier_kode,
		            a.supplier_nama
		           FROM supplier_m a) supplier_m ON ((validasipoobat_t.supplier_id = supplier_m.supplier_id)))
		     JOIN ( SELECT a.obatalkes_id,
		            a.obatalkes_kode,
		            a.jenisobatalkes_id,
		            a.manufaktur_id,
		            a.obatalkes_nama
		           FROM obatalkes_m a) obatalkes_m ON ((validasipoobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
		     JOIN ( SELECT a.jenisobatalkes_id,
		            a.jenisobatalkes_nama
		           FROM jenisobatalkes_m a) jenisobatalkes_m ON ((obatalkes_m.jenisobatalkes_id = jenisobatalkes_m.jenisobatalkes_id)))
		     LEFT JOIN ( SELECT a.satuankonversi_id,
		            a.satuankecil_id,
		            a.satuanbesar_id
		           FROM satuankonversi_m a) satuankonversi_m ON ((validasipoobatdetail_t.s_konversiobt_id = satuankonversi_m.satuankonversi_id)))
		     LEFT JOIN ( SELECT a.satuanunit_id,
		            a.satuanunit_nama
		           FROM satuanunit_m a) kecil ON ((satuankonversi_m.satuankecil_id = kecil.satuanunit_id)))
		     LEFT JOIN ( SELECT a.satuanunit_id,
		            a.satuanunit_nama
		           FROM satuanunit_m a) besar ON ((satuankonversi_m.satuanbesar_id = besar.satuanunit_id)))
		     LEFT JOIN ( SELECT a.manufaktur_id,
		            a.nama
		           FROM manufaktur_m a) manufaktur_m ON ((obatalkes_m.manufaktur_id = manufaktur_m.manufaktur_id)))
		     LEFT JOIN ( SELECT a.pajak_id,
		            a.pajak_persen
		           FROM pajak_m a) pajak_m ON ((validasipoobat_t.pajak_id = pajak_m.pajak_id)))
		     LEFT JOIN ( SELECT a.satuankonversi_id,
		            a.obatalkes_id,
		            a.satuankecil_id,
		            a.satuanbesar_id,
		            uom_besar.satuanunit_nama AS uom_besar,
		            uom_kecil.satuanunit_nama AS uom_kecil,
		            a.nilai_konversi
		           FROM ((satuankonversi_m a
		             JOIN ( SELECT a1.satuanunit_id,
		                    a1.satuanunit_nama
		                   FROM satuanunit_m a1) uom_besar ON ((a.satuanbesar_id = uom_besar.satuanunit_id)))
		             JOIN ( SELECT a1.satuanunit_id,
		                    a1.satuanunit_nama
		                   FROM satuanunit_m a1) uom_kecil ON ((a.satuankecil_id = uom_kecil.satuanunit_id)))) uom_po ON (((validasipoobatdetail_t.obatalkes_id = uom_po.obatalkes_id) AND (validasipoobatdetail_t.s_konversiobt_id = uom_po.satuankonversi_id))))
		     LEFT JOIN ( SELECT a.validasipoobatdetail_id,
		            max(penerimaanobat_t.tgl_penerimaan) AS tgl,
		            a.obatalkes_id,
		            a.s_konversiobt_id
		           FROM (penerimaanobatdetail_t a
		             JOIN ( SELECT a1.penerimaanobat_id,
		                    a1.tgl_penerimaan
		                   FROM penerimaanobat_t a1) penerimaanobat_t ON ((a.penerimaanobat_id = penerimaanobat_t.penerimaanobat_id)))
		          GROUP BY a.validasipoobatdetail_id, a.obatalkes_id, a.s_konversiobt_id) penerimaan ON ((validasipoobatdetail_t.validasipoobatdetail_id = penerimaan.validasipoobatdetail_id)))
		     LEFT JOIN ( SELECT a.satuankonversi_id,
		            a.obatalkes_id,
		            a.satuankecil_id,
		            a.satuanbesar_id,
		            uom_besar.satuanunit_nama AS uom_besar,
		            uom_kecil.satuanunit_nama AS uom_kecil,
		            a.nilai_konversi
		           FROM ((satuankonversi_m a
		             JOIN ( SELECT a1.satuanunit_id,
		                    a1.satuanunit_nama
		                   FROM satuanunit_m a1) uom_besar ON ((a.satuanbesar_id = uom_besar.satuanunit_id)))
		             JOIN ( SELECT a1.satuanunit_id,
		                    a1.satuanunit_nama
		                   FROM satuanunit_m a1) uom_kecil ON ((a.satuankecil_id = uom_kecil.satuanunit_id)))) uom_terima ON (((penerimaan.obatalkes_id = uom_terima.obatalkes_id) AND (penerimaan.s_konversiobt_id = uom_terima.satuankonversi_id))));");
    
	              $this->execute('
	                  ALTER TABLE public.laporananalisapo_v OWNER TO postgres;');
				  
		    	   	$this->execute('DROP VIEW if exists public.lapanalisapononmedis_v;');
		
		           $this->execute("
		               CREATE VIEW \"public\".\"lapanalisapononmedis_v\" AS
				   SELECT barang_m.barang_kode AS kode_barang,
				      barang_m.barang_nama AS nama_barang,
				      purchasereqbrg_t.no_pr,
				      purchasereqbrg_t.tgl_pr,
				      purchasereqbrgdetail_t.qty_input AS qty_pr,
				          CASE
				              WHEN (uom_pr.satuankonversibrg_id IS NULL) THEN NULL::text
				              ELSE concat('1 ', uom_pr.uom_besar, ' = ', uom_pr.nilai_konversi, ' ', uom_pr.uom_kecil)
				          END AS uom_pr,
				      purchasereqbrgdetail_t.catatan,
				      validasipobarang_t.no_pobarang AS no_po,
				      validasipobarang_t.created_date AS tgl_po,
				      validasipobarang_t.tgl_validasi AS tgl_validasi_po,
				          CASE
				              WHEN (validasipobarang_t.status_penerimaan = 575) THEN validasipobarang_t.last_modified_date
				              ELSE NULL::timestamp without time zone
				          END AS tgl_batal_po,
				      validasipobarang_t.catatan1 AS catatan_batal_po,
				      validasipobarang_t.catatan2 AS catatan_po,
				      validasipobarangdetail_t.qty_input AS qty_po,
				          CASE
				              WHEN (uom_po.satuankonversibrg_id IS NULL) THEN NULL::text
				              ELSE concat('1 ', uom_po.uom_besar, ' = ', uom_po.nilai_konversi, ' ', uom_po.uom_kecil)
				          END AS uom_po,
				          CASE
				              WHEN (validasipobarang_t.status_penerimaan = 575) THEN (0)::double precision
				              ELSE validasipobarangdetail_t.harga
				          END AS harga,
				      validasipobarangdetail_t.discount AS diskon,
				      pajak_m.pajak_persen AS ppn,
				          CASE
				              WHEN (validasipobarang_t.status_penerimaan = 575) THEN (0)::double precision
				              ELSE (validasipobarangdetail_t.harga * (validasipobarangdetail_t.qty_input)::double precision)
				          END AS subtotal,
				          CASE
				              WHEN (validasipobarang_t.status_penerimaan = 575) THEN (0)::double precision
				              ELSE (((validasipobarangdetail_t.harga * (validasipobarangdetail_t.qty_input)::double precision) - validasipobarangdetail_t.discount_rp) + ((((validasipobarangdetail_t.harga * (validasipobarangdetail_t.qty_input)::double precision) - validasipobarangdetail_t.discount_rp) * (pajak_m.pajak_persen)::double precision) / (100)::double precision))
				          END AS total,
				          CASE
				              WHEN (validasipobarang_t.status_penerimaan = 575) THEN (0)::double precision
				              ELSE COALESCE(((validasipobarangdetail_t.jumlah - validasipobarangdetail_t.discount_rp) +
				              CASE
				                  WHEN (COALESCE((validasipobarang_t.ppn_persen)::integer, 0) = 0) THEN (0)::double precision
				                  ELSE ((validasipobarangdetail_t.jumlah - validasipobarangdetail_t.discount_rp) / ((100 / validasipobarang_t.ppn_persen))::double precision)
				              END), (0)::double precision)
				          END AS harga_total,
				      penerimaan.tgl AS tgl_penerimaan,
				      validasipobarangdetail_t.qty_penerimaan,
				          CASE
				              WHEN (uom_terima.satuankonversibrg_id IS NULL) THEN NULL::text
				              ELSE concat('1 ', uom_terima.uom_besar, ' = ', uom_terima.nilai_konversi, ' ', uom_terima.uom_kecil)
				          END AS uom_penerimaan,
				          CASE
				              WHEN (uom_terima.satuankonversibrg_id IS NULL) THEN NULL::text
				              ELSE concat('1 ', uom_terima.uom_besar, ' = ', uom_terima.nilai_konversi, ' ', uom_terima.uom_kecil)
				          END AS uom_sisa_penerimaan,
				      validasipobarangdetail_t.qty_sisa AS sisa_penerimaan,
				      supplier_m.supplier_kode AS kode_supplier,
				      supplier_m.supplier_nama AS nama_supplier,
				      COALESCE((date_part('day'::text, (validasipobarang_t.created_date)::date) - date_part('day'::text, purchasereqbrg_t.tgl_pr)), (0)::double precision) AS pr_jarak_po,
				          CASE
				              WHEN (validasipobarang_t.is_validasi = true) THEN COALESCE((date_part('day'::text, (validasipobarang_t.tgl_validasi)::date) - date_part('day'::text, purchasereqbrg_t.tgl_pr)), (0)::double precision)
				              ELSE COALESCE((date_part('day'::text, (validasipobarang_t.created_date)::date) - date_part('day'::text, purchasereqbrg_t.tgl_pr)), (0)::double precision)
				          END AS pr_jarak_tgl_penerimaan,
				          CASE
				              WHEN (validasipobarang_t.is_validasi = true) THEN (date_part('day'::text, (validasipobarang_t.tgl_validasi)::date) - date_part('day'::text, validasipobarang_t.created_date))
				              ELSE (date_part('day'::text, (validasipobarang_t.created_date)::date) - date_part('day'::text, validasipobarang_t.created_date))
				          END AS po_jarak_validasi_po,
				      0 AS po_jarak_tgl_penerimaan,
				      0 AS po_validasi_tgl_penerimaan,
				      penerimaan.no_penerimaan,
				      penerimaan.no_faktur AS nofaktur_penerimaan,
				      penerimaan.tgl AS tgl_verifikasi_penerimaan,
				      uom_pr.uom_besar AS satuan_pr,
				      uom_po.uom_besar AS satuan_po,
				          CASE
				              WHEN (uom_terima.satuankonversibrg_id IS NULL) THEN NULL::text
				              ELSE concat(uom_terima.uom_besar)
				          END AS penerimaan,
				          CASE
				              WHEN (validasipobarang_t.status_penerimaan = 575) THEN 'Dibatalkan'::text
				              WHEN (validasipobarang_t.status_penerimaan = 572) THEN 'Belum Diterima'::text
				              WHEN (validasipobarang_t.status_penerimaan = 573) THEN 'Belum Semua Diterima'::text
				              WHEN (validasipobarang_t.status_penerimaan = 686) THEN 'Expired'::text
				              WHEN (validasipobarang_t.status_penerimaan = 574) THEN 'Sudah Semua Diterima'::text
				              WHEN (validasipobarang_t.status_penerimaan = 574) THEN 'Sudah Semua Diterima'::text
				              WHEN (validasipobarang_t.status_penerimaan = 580) THEN 'Closing Supplier'::text
				              ELSE NULL::text
				          END AS status_po
				     FROM ((((((((((((((validasipobarang_t
				       JOIN validasipobarangdetail_t ON ((validasipobarang_t.validasipobarang_id = validasipobarangdetail_t.validasipobarang_id)))
				       LEFT JOIN purchasereqbrgdetail_t ON ((validasipobarangdetail_t.purchasereqbrgdetail_id = purchasereqbrgdetail_t.purchasereqbrgdetail_id)))
				       LEFT JOIN purchasereqbrg_t ON ((purchasereqbrgdetail_t.purchasereqbrg_id = purchasereqbrg_t.purchasereqbrg_id)))
				       LEFT JOIN ( SELECT satuankonversibrg_m.satuankonversibrg_id,
				              satuankonversibrg_m.barang_id,
				              satuankonversibrg_m.satuankecil_id,
				              satuankonversibrg_m.satuanbesar_id,
				              uom_besar.satuanunit_nama AS uom_besar,
				              uom_kecil.satuanunit_nama AS uom_kecil,
				              satuankonversibrg_m.nilai_konversi
				             FROM ((satuankonversibrg_m
				               LEFT JOIN satuanunit_m uom_besar ON ((satuankonversibrg_m.satuanbesar_id = uom_besar.satuanunit_id)))
				               LEFT JOIN satuanunit_m uom_kecil ON ((satuankonversibrg_m.satuankecil_id = uom_kecil.satuanunit_id)))
				            WHERE ((satuankonversibrg_m.is_deleted = false) AND (satuankonversibrg_m.is_active = true))
				            GROUP BY satuankonversibrg_m.satuankonversibrg_id, satuankonversibrg_m.barang_id, satuankonversibrg_m.satuankecil_id, satuankonversibrg_m.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, satuankonversibrg_m.nilai_konversi) uom_pr ON (((purchasereqbrgdetail_t.barang_id = uom_pr.barang_id) AND (purchasereqbrgdetail_t.satuan_id = uom_pr.satuanbesar_id) AND (purchasereqbrgdetail_t.satuankonversi_id = uom_pr.satuankecil_id))))
				       LEFT JOIN supplier_m ON ((validasipobarang_t.supplier_id = supplier_m.supplier_id)))
				       JOIN barang_m ON ((validasipobarangdetail_t.barang_id = barang_m.barang_id)))
				       LEFT JOIN kelompokbarang_m ON ((barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id)))
				       LEFT JOIN satuankonversi_m ON ((validasipobarangdetail_t.s_konversibrg_id = satuankonversi_m.satuankonversi_id)))
				       LEFT JOIN satuanunit_m kecil ON ((satuankonversi_m.satuankecil_id = kecil.satuanunit_id)))
				       LEFT JOIN satuanunit_m besar ON ((satuankonversi_m.satuanbesar_id = besar.satuanunit_id)))
				       LEFT JOIN pajak_m ON ((validasipobarang_t.pajak_id = pajak_m.pajak_id)))
				       LEFT JOIN ( SELECT satuankonversi_po.satuankonversibrg_id,
				              satuankonversi_po.barang_id,
				              satuankonversi_po.satuankecil_id,
				              satuankonversi_po.satuanbesar_id,
				              uom_besar.satuanunit_nama AS uom_besar,
				              uom_kecil.satuanunit_nama AS uom_kecil,
				              satuankonversi_po.nilai_konversi
				             FROM ((satuankonversibrg_m satuankonversi_po
				               LEFT JOIN satuanunit_m uom_besar ON ((satuankonversi_po.satuanbesar_id = uom_besar.satuanunit_id)))
				               LEFT JOIN satuanunit_m uom_kecil ON ((satuankonversi_po.satuankecil_id = uom_kecil.satuanunit_id)))
				            WHERE ((satuankonversi_po.is_deleted = false) AND (satuankonversi_po.is_active = true))) uom_po ON (((validasipobarangdetail_t.barang_id = uom_po.barang_id) AND (validasipobarangdetail_t.s_konversibrg_id = uom_po.satuankonversibrg_id))))
				       LEFT JOIN ( SELECT max_penerimaan.validasipobarang_id,
				              penerimaanbarangdetail_t.validasipobarangdetail_id,
				              penerimaanbarang_t.no_penerimaan,
				              penerimaanbarang_t.no_faktur,
				              penerimaanbarang_t.tgl_penerimaan AS tgl,
				              penerimaanbarangdetail_t.barang_id,
				              penerimaanbarangdetail_t.s_konversibrg_id
				             FROM ((penerimaanbarang_t
				               JOIN ( SELECT max(penerimaanbarang_t_1.penerimaanbarang_id) AS max_id,
				                      penerimaanbarang_t_1.validasipobarang_id
				                     FROM penerimaanbarang_t penerimaanbarang_t_1
				                    GROUP BY penerimaanbarang_t_1.validasipobarang_id) max_penerimaan ON (((penerimaanbarang_t.penerimaanbarang_id = max_penerimaan.max_id) AND (penerimaanbarang_t.validasipobarang_id = max_penerimaan.validasipobarang_id))))
				               JOIN penerimaanbarangdetail_t ON ((penerimaanbarang_t.penerimaanbarang_id = penerimaanbarangdetail_t.penerimaanbarang_id)))) penerimaan ON (((validasipobarang_t.validasipobarang_id = penerimaan.validasipobarang_id) AND (validasipobarangdetail_t.barang_id = penerimaan.barang_id))))
				       LEFT JOIN ( SELECT sk_terima.satuankonversibrg_id,
				              sk_terima.barang_id,
				              sk_terima.satuankecil_id,
				              sk_terima.satuanbesar_id,
				              uom_besar.satuanunit_nama AS uom_besar,
				              uom_kecil.satuanunit_nama AS uom_kecil,
				              sk_terima.nilai_konversi
				             FROM ((satuankonversibrg_m sk_terima
				               LEFT JOIN satuanunit_m uom_besar ON ((sk_terima.satuanbesar_id = uom_besar.satuanunit_id)))
				               LEFT JOIN satuanunit_m uom_kecil ON ((sk_terima.satuankecil_id = uom_kecil.satuanunit_id)))
				            WHERE ((sk_terima.is_deleted = false) AND (sk_terima.is_active = true))) uom_terima ON (((penerimaan.barang_id = uom_terima.barang_id) AND (penerimaan.s_konversibrg_id = uom_terima.satuankonversibrg_id))))
				    WHERE (validasipobarangdetail_t.is_deleted = false);");
    
		  	              $this->execute('
		  	                  ALTER TABLE public.lapanalisapononmedis_v OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m211221_050525_migrate_US2359_laporananalisapo_21122021 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m211221_050525_migrate_US2359_laporananalisapo_21122021 cannot be reverted.\n";

        return false;
    }
    */
}
