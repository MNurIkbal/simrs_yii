<?php

use yii\db\Migration;

/**
 * Class m210810_082535_migrate_improve_purchasereq
 */
class m210810_082535_migrate_improve_purchasereq extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."purchasereqbrgdetail_t" ADD COLUMN if not exists "qty_final" numeric(15,0);');
        $this->execute('ALTER TABLE "public"."purchasereqbrgdetail_t" ADD COLUMN if not exists "satuan_final_id" int4;');
        $this->execute('ALTER TABLE "public"."purchasereqbrgdetail_t" ADD COLUMN if not exists "qty_pr" numeric;');
        $this->execute('ALTER TABLE "public"."purchasereqbrgdetail_t" ADD COLUMN if not exists "qty_saatini" numeric;');
        $this->execute('ALTER TABLE "public"."purchasereqbrgdetail_t" ADD COLUMN if not exists "doi" numeric;');
        $this->execute('ALTER TABLE "public"."purchasereqbrgdetail_t" ADD COLUMN if not exists "ssmin" numeric;');
        $this->execute('ALTER TABLE "public"."purchasereqbrgdetail_t" ADD COLUMN if not exists "qty_sugesstion" numeric;');

        $this->execute('ALTER TABLE "public"."purchasereqdetail_t" ADD COLUMN if not exists "qty_final" numeric(15,0);');
        $this->execute('ALTER TABLE "public"."purchasereqdetail_t" ADD COLUMN if not exists "satuan_final_id" int4;');
        $this->execute('ALTER TABLE "public"."purchasereqdetail_t" ADD COLUMN if not exists "qty_pr" numeric;');
        $this->execute('ALTER TABLE "public"."purchasereqdetail_t" ADD COLUMN if not exists "qty_saatini" numeric;');

        $this->execute('ALTER TABLE "public"."purchasereq_t" ADD COLUMN if not exists "peg_approve_id" int4;');
        $this->execute('ALTER TABLE "public"."purchasereq_t" ADD COLUMN if not exists "tgl_approve" timestamp(6);');

        $this->execute('ALTER TABLE "public"."purchasereqbrg_t" ADD COLUMN if not exists "tgl_approve" timestamp(6);');
        $this->execute('ALTER TABLE "public"."purchasereqbrg_t" ADD COLUMN if not exists "peg_approve_id" int4;');

        $this->execute('DROP VIEW if exists "public"."infopurchasereqgabung_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopurchasereqgabung_v\" AS  SELECT 'OBAT'::text AS tipe,
    purchasereq_t.purchasereq_id,
    purchasereq_t.no_pr,
    purchasereq_t.tgl_pr,
    purchasereq_t.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan,
    purchasereq_t.pegawai_id,
    pegawai_m.nama_pegawai AS pegawai,
    purchasereq_t.reference,
    purchasereq_t.status,
    fgetnamalookup(purchasereq_t.status::integer) AS status_pr,
    purchasereq_t.is_prcyto,
        CASE
            WHEN purchasereq_t.is_prcyto = true THEN 'Cyto'::text
            ELSE 'Non Cyto'::text
        END AS pr_cyto,
    purchasereq_t.tgl_approve,
    peg_approve.nama_pegawai AS pegawai_approve
   FROM purchasereq_t
     JOIN ruangan_m ON purchasereq_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pegawai_m ON purchasereq_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN pegawai_m peg_approve ON purchasereq_t.peg_approve_id = peg_approve.pegawai_id
  WHERE purchasereq_t.is_deleted = false
UNION ALL
 SELECT 'BARANG'::text AS tipe,
    purchasereqbrg_t.purchasereqbrg_id AS purchasereq_id,
    purchasereqbrg_t.no_pr,
    purchasereqbrg_t.tgl_pr,
    purchasereqbrg_t.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan,
    purchasereqbrg_t.pegawai_id,
    pegawai_m.nama_pegawai AS pegawai,
    purchasereqbrg_t.reference,
    purchasereqbrg_t.status,
    fgetnamalookup(purchasereqbrg_t.status::integer) AS status_pr,
    purchasereqbrg_t.is_prcyto,
        CASE
            WHEN purchasereqbrg_t.is_prcyto = true THEN 'Cyto'::text
            ELSE 'Non Cyto'::text
        END AS pr_cyto,
    purchasereqbrg_t.tgl_approve,
    peg_approve.nama_pegawai AS pegawai_approve
   FROM purchasereqbrg_t
     JOIN ruangan_m ON purchasereqbrg_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pegawai_m ON purchasereqbrg_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN pegawai_m peg_approve ON purchasereqbrg_t.peg_approve_id = peg_approve.pegawai_id
  WHERE purchasereqbrg_t.is_deleted = false;");

        $this->execute('DROP VIEW if exists "public"."infopurchasereq_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopurchasereq_v\" AS  SELECT purchasereq_t.purchasereq_id,
    purchasereq_t.no_pr,
    purchasereq_t.tgl_pr,
    purchasereq_t.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan,
    purchasereq_t.pegawai_id,
    pegawai_m.nama_pegawai AS pegawai,
    purchasereq_t.reference,
    purchasereq_t.status,
    fgetnamalookup(purchasereq_t.status::integer) AS status_pr,
    purchasereq_t.is_prcyto,
        CASE
            WHEN purchasereq_t.is_prcyto = true THEN 'Cyto'::text
            ELSE 'Non Cyto'::text
        END AS pr_cyto,
    purchasereq_t.tgl_approve,
    peg_approve.nama_pegawai AS pegawai_approve
   FROM purchasereq_t
     JOIN ruangan_m ON purchasereq_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pegawai_m ON purchasereq_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN pegawai_m peg_approve ON purchasereq_t.peg_approve_id = peg_approve.pegawai_id
  WHERE purchasereq_t.is_deleted = false;");

        $this->execute('DROP VIEW if exists "public"."infopurchasereqbrg_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopurchasereqbrg_v\" AS  SELECT purchasereqbrg_t.purchasereqbrg_id,
    purchasereqbrg_t.no_pr,
    purchasereqbrg_t.tgl_pr,
    purchasereqbrg_t.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan,
    purchasereqbrg_t.pegawai_id,
    pegawai_m.nama_pegawai AS pegawai,
    purchasereqbrg_t.reference,
    purchasereqbrg_t.status,
    fgetnamalookup(purchasereqbrg_t.status::integer) AS status_pr,
    purchasereqbrg_t.is_prcyto,
        CASE
            WHEN purchasereqbrg_t.is_prcyto = true THEN 'Cyto'::text
            ELSE 'Non Cyto'::text
        END AS pr_cyto,
    purchasereqbrg_t.tgl_approve,
    peg_approve.nama_pegawai AS pegawai_approve
   FROM purchasereqbrg_t
     JOIN ruangan_m ON purchasereqbrg_t.ruangan_id = ruangan_m.ruangan_id
     JOIN pegawai_m ON purchasereqbrg_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN pegawai_m peg_approve ON purchasereqbrg_t.peg_approve_id = peg_approve.pegawai_id
  WHERE purchasereqbrg_t.is_deleted = false;");

        $this->execute('DROP VIEW if exists "public"."infopurchasereqbrgdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopurchasereqbrgdetail_v\" AS  SELECT purchasereqbrg_t.purchasereqbrg_id,
    purchasereqbrgdetail_t.purchasereqbrgdetail_id,
    purchasereqbrg_t.no_pr,
    purchasereqbrgdetail_t.barang_id,
    barang_m.barang_nama,
    purchasereqbrgdetail_t.qty_input,
    purchasereqbrgdetail_t.qty_konversi,
    purchasereqbrgdetail_t.satuan_id,
    uom.uom_besar AS satuan,
    purchasereqbrgdetail_t.satuankonversi_id,
    uom.uom_kecil AS satuan_konversi,
    purchasereqbrgdetail_t.catatan,
    purchasereqbrgdetail_t.qty_saatini::double precision AS stok,
    satuan_stok.satuanunit_nama AS satuan_stok,
    fgetnamalookup(purchasereqbrgdetail_t.status::integer) AS status,
    purchasereqbrgdetail_t.status AS status_id,
    uom.nilai_konversi,
    concat('1 ', uom.uom_besar, ' = ', uom.nilai_konversi, ' ', uom.uom_kecil) AS uom,
    purchasereqbrgdetail_t.alasan,
    kontrak_supplier.kontraksupplierbrgdetail_id,
        CASE
            WHEN kontrak_supplier.kontraksupplierbrgdetail_id IS NOT NULL THEN kontrak_supplier.satuankonv1_id
            WHEN kontrak_supplier.kontraksupplierbrgdetail_id IS NULL THEN uom.satuanbesar_id
            ELSE NULL::integer
        END AS satuanbesar_id,
        CASE
            WHEN kontrak_supplier.kontraksupplierbrgdetail_id IS NOT NULL THEN kontrak_supplier.satuan_besar
            WHEN kontrak_supplier.kontraksupplierbrgdetail_id IS NULL THEN uom.uom_besar
            ELSE NULL::character varying
        END AS satuan_besar,
    barang_m.barang_kode AS kode_barang,
    po.no_pobarang AS nomor_po,
    purchasereqbrgdetail_t.doi,
    purchasereqbrgdetail_t.ssmin,
    purchasereqbrgdetail_t.qty_sugesstion,
    purchasereqbrgdetail_t.qty_saatini
   FROM purchasereqbrg_t
     JOIN purchasereqbrgdetail_t ON purchasereqbrg_t.purchasereqbrg_id = purchasereqbrgdetail_t.purchasereqbrg_id
     LEFT JOIN barang_m ON purchasereqbrgdetail_t.barang_id = barang_m.barang_id
     LEFT JOIN satuanunit_m satuan_stok ON barang_m.satuankecil_id = satuan_stok.satuanunit_id
     LEFT JOIN ( SELECT satuankonversibrg_m.barang_id,
            satuankonversibrg_m.satuankecil_id,
            satuankonversibrg_m.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            satuankonversibrg_m.nilai_konversi
           FROM satuankonversibrg_m
             LEFT JOIN satuanunit_m uom_besar ON satuankonversibrg_m.satuanbesar_id = uom_besar.satuanunit_id
             LEFT JOIN satuanunit_m uom_kecil ON satuankonversibrg_m.satuankecil_id = uom_kecil.satuanunit_id
          WHERE satuankonversibrg_m.is_deleted = false AND satuankonversibrg_m.is_active = true
          GROUP BY satuankonversibrg_m.barang_id, satuankonversibrg_m.satuankecil_id, satuankonversibrg_m.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, satuankonversibrg_m.nilai_konversi) uom ON purchasereqbrgdetail_t.barang_id = uom.barang_id AND purchasereqbrgdetail_t.satuan_id = uom.satuanbesar_id AND purchasereqbrgdetail_t.satuankonversi_id = uom.satuankecil_id
     LEFT JOIN ( SELECT stokbarang_r.barang_id,
            stokbarang_r.ruangan_id,
            sum(stokbarang_r.qty_sisa) AS qty_sisa
           FROM stokbarang_r
          GROUP BY stokbarang_r.barang_id, stokbarang_r.ruangan_id) obat_sisa ON purchasereqbrgdetail_t.barang_id = obat_sisa.barang_id AND purchasereqbrg_t.ruangan_id = obat_sisa.ruangan_id
     LEFT JOIN ( SELECT kontraksupplierbrgdetail_m.barang_id,
            kontraksupplierbrgdetail_m.satuankecil_id,
            kontraksupplierbrgdetail_m.satuankonv1_id,
            array_agg(kontraksupplierbrgdetail_m.kontraksupplierbrgdetail_id) AS kontraksupplierbrgdetail_id,
            satuan_besar.satuanunit_nama AS satuan_besar
           FROM kontraksupplierbrgdetail_m
             LEFT JOIN satuanunit_m satuan_besar ON kontraksupplierbrgdetail_m.satuankonv1_id = satuan_besar.satuanunit_id
          WHERE kontraksupplierbrgdetail_m.is_deleted = false AND kontraksupplierbrgdetail_m.is_active = true
          GROUP BY kontraksupplierbrgdetail_m.barang_id, kontraksupplierbrgdetail_m.satuankecil_id, kontraksupplierbrgdetail_m.satuankonv1_id, satuan_besar.satuanunit_nama) kontrak_supplier ON purchasereqbrgdetail_t.barang_id = kontrak_supplier.barang_id AND purchasereqbrgdetail_t.satuan_id = kontrak_supplier.satuankonv1_id AND purchasereqbrgdetail_t.satuankonversi_id = kontrak_supplier.satuankecil_id
     LEFT JOIN ( SELECT validasipobarang_t.no_pobarang,
            validasipobarangdetail_t.purchasereqbrgdetail_id
           FROM validasipobarang_t
             JOIN validasipobarangdetail_t ON validasipobarang_t.validasipobarang_id = validasipobarangdetail_t.validasipobarang_id
          WHERE validasipobarang_t.is_deleted = false AND validasipobarangdetail_t.is_deleted = false) po ON purchasereqbrgdetail_t.purchasereqbrgdetail_id = po.purchasereqbrgdetail_id
  WHERE purchasereqbrg_t.is_deleted = false AND purchasereqbrgdetail_t.is_deleted = false;");

        $this->execute('DROP VIEW if exists "public"."infopurchasereqdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopurchasereqdetail_v\" AS  SELECT purchasereq_t.purchasereq_id,
    purchasereqdetail_t.purchasereqdetail_id,
    purchasereq_t.no_pr,
    purchasereqdetail_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    purchasereqdetail_t.qty_input,
    purchasereqdetail_t.qty_konversi,
    purchasereqdetail_t.satuan_id,
    satuan_1.satuanunit_nama AS satuan,
    purchasereqdetail_t.satuankonversi_id,
    satuan_2.satuanunit_nama AS satuan_konversi,
    purchasereqdetail_t.catatan,
    purchasereqdetail_t.qty_saatini::double precision AS stok,
    satuan_stok.satuanunit_nama AS satuan_stok,
    fgetnamalookup(purchasereqdetail_t.status::integer) AS status,
    purchasereqdetail_t.status AS status_id,
    uom.nilai_konversi,
    concat('1 ', uom.uom_besar, ' = ', uom.nilai_konversi, ' ', uom.uom_kecil) AS uom,
    purchasereqdetail_t.alasan,
    kontrak_supplier.kontraksupplierdetail_id,
        CASE
            WHEN kontrak_supplier.kontraksupplierdetail_id IS NOT NULL THEN kontrak_supplier.satuankonv1_id
            WHEN kontrak_supplier.kontraksupplierdetail_id IS NULL THEN uom.satuanbesar_id
            ELSE NULL::integer
        END AS satuanbesar_id,
        CASE
            WHEN kontrak_supplier.kontraksupplierdetail_id IS NOT NULL THEN kontrak_supplier.satuan_besar
            WHEN kontrak_supplier.kontraksupplierdetail_id IS NULL THEN uom.uom_besar
            ELSE NULL::character varying
        END AS satuan_besar,
    obatalkes_m.obatalkes_kode AS kode_obat,
    po.no_poobat AS nomor_po,
    purchasereqdetail_t.doi,
    purchasereqdetail_t.ssmin,
    purchasereqdetail_t.qty_sugesstion,
    purchasereqdetail_t.qty_saatini
   FROM purchasereq_t
     JOIN purchasereqdetail_t ON purchasereq_t.purchasereq_id = purchasereqdetail_t.purchasereq_id
     LEFT JOIN obatalkes_m ON purchasereqdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN satuanunit_m satuan_1 ON purchasereqdetail_t.satuan_id = satuan_1.satuanunit_id
     LEFT JOIN satuanunit_m satuan_2 ON purchasereqdetail_t.satuankonversi_id = satuan_2.satuanunit_id
     LEFT JOIN satuanunit_m satuan_stok ON obatalkes_m.satuankecil_id = satuan_stok.satuanunit_id
     LEFT JOIN ( SELECT satuankonversi_m.obatalkes_id,
            satuankonversi_m.satuankecil_id,
            satuankonversi_m.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            satuankonversi_m.nilai_konversi
           FROM satuankonversi_m
             LEFT JOIN satuanunit_m uom_besar ON satuankonversi_m.satuanbesar_id = uom_besar.satuanunit_id
             LEFT JOIN satuanunit_m uom_kecil ON satuankonversi_m.satuankecil_id = uom_kecil.satuanunit_id
          WHERE satuankonversi_m.is_deleted = false AND satuankonversi_m.is_active = true
          GROUP BY satuankonversi_m.obatalkes_id, satuankonversi_m.satuankecil_id, satuankonversi_m.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, satuankonversi_m.nilai_konversi) uom ON purchasereqdetail_t.obatalkes_id = uom.obatalkes_id AND purchasereqdetail_t.satuan_id = uom.satuanbesar_id AND purchasereqdetail_t.satuankonversi_id = uom.satuankecil_id
     LEFT JOIN ( SELECT stokobatalkes_r.obatalkes_id,
            stokobatalkes_r.ruangan_id,
            sum(stokobatalkes_r.qty_sisa) AS qty_sisa
           FROM stokobatalkes_r
          GROUP BY stokobatalkes_r.obatalkes_id, stokobatalkes_r.ruangan_id) obat_sisa ON purchasereqdetail_t.obatalkes_id = obat_sisa.obatalkes_id AND purchasereq_t.ruangan_id = obat_sisa.ruangan_id
     LEFT JOIN ( SELECT kontraksupplierdetail_m.obatalkes_id,
            kontraksupplierdetail_m.satuankecil_id,
            kontraksupplierdetail_m.satuankonv1_id,
            array_agg(kontraksupplierdetail_m.kontraksupplierdetail_id) AS kontraksupplierdetail_id,
            satuan_besar.satuanunit_nama AS satuan_besar
           FROM kontraksupplierdetail_m
             LEFT JOIN satuanunit_m satuan_besar ON kontraksupplierdetail_m.satuankonv1_id = satuan_besar.satuanunit_id
          WHERE kontraksupplierdetail_m.is_deleted = false AND kontraksupplierdetail_m.is_active = true
          GROUP BY kontraksupplierdetail_m.obatalkes_id, kontraksupplierdetail_m.satuankecil_id, kontraksupplierdetail_m.satuankonv1_id, satuan_besar.satuanunit_nama) kontrak_supplier ON purchasereqdetail_t.obatalkes_id = kontrak_supplier.obatalkes_id AND purchasereqdetail_t.satuan_id = kontrak_supplier.satuankonv1_id AND purchasereqdetail_t.satuankonversi_id = kontrak_supplier.satuankecil_id
     LEFT JOIN ( SELECT validasipoobat_t.no_poobat,
            validasipoobatdetail_t.purchasereqdetail_id
           FROM validasipoobat_t
             JOIN validasipoobatdetail_t ON validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id
          WHERE validasipoobat_t.is_deleted = false AND validasipoobatdetail_t.is_deleted = false) po ON purchasereqdetail_t.purchasereqdetail_id = po.purchasereqdetail_id
  WHERE purchasereq_t.is_deleted = false AND purchasereqdetail_t.is_deleted = false;");

        $this->execute('DROP VIEW if exists "public"."infopurchasereqgabungdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopurchasereqgabungdetail_v\" AS  SELECT 'OBAT'::text AS tipe,
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
    purchasereqdetail_t.qty_saatini::double precision AS stok,
    satuan_stok.satuanunit_nama AS satuan_stok,
    fgetnamalookup(purchasereqdetail_t.status::integer) AS status,
    purchasereqdetail_t.status AS status_id,
    uom.nilai_konversi,
    concat('1 ', uom.uom_besar, ' = ', uom.nilai_konversi, ' ', uom.uom_kecil) AS uom,
    purchasereqdetail_t.alasan,
    kontrak_supplier.kontraksupplierdetail_id,
        CASE
            WHEN kontrak_supplier.kontraksupplierdetail_id IS NOT NULL THEN kontrak_supplier.satuankonv1_id
            WHEN kontrak_supplier.kontraksupplierdetail_id IS NULL THEN uom.satuanbesar_id
            ELSE NULL::integer
        END AS satuanbesar_id,
        CASE
            WHEN kontrak_supplier.kontraksupplierdetail_id IS NOT NULL THEN kontrak_supplier.satuan_besar
            WHEN kontrak_supplier.kontraksupplierdetail_id IS NULL THEN uom.uom_besar
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
    purchasereqdetail_t.qty_pr
   FROM purchasereq_t
     JOIN purchasereqdetail_t ON purchasereq_t.purchasereq_id = purchasereqdetail_t.purchasereq_id
     LEFT JOIN obatalkes_m ON purchasereqdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN supplier_m defaultsupplier ON defaultsupplier.supplier_id = obatalkes_m.supplier_id
     LEFT JOIN pajak_m defaultsupplierpajak ON defaultsupplierpajak.pajak_id = defaultsupplier.pajak_id
     LEFT JOIN satuanunit_m satuan_1 ON purchasereqdetail_t.satuan_id = satuan_1.satuanunit_id
     LEFT JOIN satuanunit_m satuan_2 ON purchasereqdetail_t.satuankonversi_id = satuan_2.satuanunit_id
     LEFT JOIN satuanunit_m satuan_stok ON obatalkes_m.satuankecil_id = satuan_stok.satuanunit_id
     LEFT JOIN ( SELECT satuankonversi_m.obatalkes_id,
            satuankonversi_m.satuankecil_id,
            satuankonversi_m.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            satuankonversi_m.nilai_konversi
           FROM satuankonversi_m
             LEFT JOIN satuanunit_m uom_besar ON satuankonversi_m.satuanbesar_id = uom_besar.satuanunit_id
             LEFT JOIN satuanunit_m uom_kecil ON satuankonversi_m.satuankecil_id = uom_kecil.satuanunit_id
          WHERE satuankonversi_m.is_deleted = false AND satuankonversi_m.is_active = true
          GROUP BY satuankonversi_m.obatalkes_id, satuankonversi_m.satuankecil_id, satuankonversi_m.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, satuankonversi_m.nilai_konversi) uom ON purchasereqdetail_t.obatalkes_id = uom.obatalkes_id AND purchasereqdetail_t.satuan_id = uom.satuanbesar_id AND purchasereqdetail_t.satuankonversi_id = uom.satuankecil_id
     LEFT JOIN ( SELECT stokobatalkes_r.obatalkes_id,
            stokobatalkes_r.ruangan_id,
            konfigrak_m.min_stok,
            konfigrak_m.max_stok,
            sum(stokobatalkes_r.qty_sisa) AS qty_sisa
           FROM stokobatalkes_r
             JOIN konfigrak_m ON stokobatalkes_r.stokobatr_id = konfigrak_m.stokobatr_id
          GROUP BY stokobatalkes_r.obatalkes_id, stokobatalkes_r.ruangan_id, konfigrak_m.min_stok, konfigrak_m.max_stok) obat_sisa ON purchasereqdetail_t.obatalkes_id = obat_sisa.obatalkes_id AND purchasereq_t.ruangan_id = obat_sisa.ruangan_id
     LEFT JOIN ( SELECT kontraksupplierdetail_m.obatalkes_id,
            kontraksupplierdetail_m.satuankecil_id,
            kontraksupplierdetail_m.satuankonv1_id,
            array_agg(kontraksupplierdetail_m.kontraksupplierdetail_id) AS kontraksupplierdetail_id,
            satuan_besar.satuanunit_nama AS satuan_besar
           FROM kontraksupplierdetail_m
             LEFT JOIN satuanunit_m satuan_besar ON kontraksupplierdetail_m.satuankonv1_id = satuan_besar.satuanunit_id
          WHERE kontraksupplierdetail_m.is_deleted = false AND kontraksupplierdetail_m.is_active = true
          GROUP BY kontraksupplierdetail_m.obatalkes_id, kontraksupplierdetail_m.satuankecil_id, kontraksupplierdetail_m.satuankonv1_id, satuan_besar.satuanunit_nama) kontrak_supplier ON purchasereqdetail_t.obatalkes_id = kontrak_supplier.obatalkes_id AND purchasereqdetail_t.satuan_id = kontrak_supplier.satuankonv1_id AND purchasereqdetail_t.satuankonversi_id = kontrak_supplier.satuankecil_id
     LEFT JOIN ( SELECT validasipoobat_t.no_poobat,
            validasipoobatdetail_t.purchasereqdetail_id,
            validasipoobat_t.status_penerimaan
           FROM validasipoobat_t
             JOIN validasipoobatdetail_t ON validasipoobat_t.validasipoobat_id = validasipoobatdetail_t.validasipoobat_id) po ON purchasereqdetail_t.purchasereqdetail_id = po.purchasereqdetail_id
  WHERE purchasereq_t.is_deleted = false AND purchasereqdetail_t.is_deleted = false
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
    purchasereqbrgdetail_t.qty_saatini::double precision AS stok,
    satuan_stok.satuanunit_nama AS satuan_stok,
    fgetnamalookup(purchasereqbrgdetail_t.status::integer) AS status,
    purchasereqbrgdetail_t.status AS status_id,
    uom.nilai_konversi,
    concat('1 ', uom.uom_besar, ' = ', uom.nilai_konversi, ' ', uom.uom_kecil) AS uom,
    purchasereqbrgdetail_t.alasan,
    kontrak_supplier.kontraksupplierbrgdetail_id AS kontraksupplierdetail_id,
        CASE
            WHEN kontrak_supplier.kontraksupplierbrgdetail_id IS NOT NULL THEN kontrak_supplier.satuankonv1_id
            WHEN kontrak_supplier.kontraksupplierbrgdetail_id IS NULL THEN uom.satuanbesar_id
            ELSE NULL::integer
        END AS satuanbesar_id,
        CASE
            WHEN kontrak_supplier.kontraksupplierbrgdetail_id IS NOT NULL THEN kontrak_supplier.satuan_besar
            WHEN kontrak_supplier.kontraksupplierbrgdetail_id IS NULL THEN uom.uom_besar
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
    purchasereqbrgdetail_t.qty_pr
   FROM purchasereqbrg_t
     JOIN purchasereqbrgdetail_t ON purchasereqbrg_t.purchasereqbrg_id = purchasereqbrgdetail_t.purchasereqbrg_id
     LEFT JOIN barang_m ON purchasereqbrgdetail_t.barang_id = barang_m.barang_id
     LEFT JOIN supplier_m defaultsupplier ON defaultsupplier.supplier_id = barang_m.supplier_id
     LEFT JOIN pajak_m defaultsupplierpajak ON defaultsupplierpajak.pajak_id = defaultsupplier.pajak_id
     LEFT JOIN satuanunit_m satuan_stok ON barang_m.satuankecil_id = satuan_stok.satuanunit_id
     LEFT JOIN ( SELECT satuankonversibrg_m.barang_id,
            satuankonversibrg_m.satuankecil_id,
            satuankonversibrg_m.satuanbesar_id,
            uom_besar.satuanunit_nama AS uom_besar,
            uom_kecil.satuanunit_nama AS uom_kecil,
            satuankonversibrg_m.nilai_konversi
           FROM satuankonversibrg_m
             LEFT JOIN satuanunit_m uom_besar ON satuankonversibrg_m.satuanbesar_id = uom_besar.satuanunit_id
             LEFT JOIN satuanunit_m uom_kecil ON satuankonversibrg_m.satuankecil_id = uom_kecil.satuanunit_id
          WHERE satuankonversibrg_m.is_deleted = false AND satuankonversibrg_m.is_active = true
          GROUP BY satuankonversibrg_m.barang_id, satuankonversibrg_m.satuankecil_id, satuankonversibrg_m.satuanbesar_id, uom_besar.satuanunit_nama, uom_kecil.satuanunit_nama, satuankonversibrg_m.nilai_konversi) uom ON purchasereqbrgdetail_t.barang_id = uom.barang_id AND purchasereqbrgdetail_t.satuan_id = uom.satuanbesar_id AND purchasereqbrgdetail_t.satuankonversi_id = uom.satuankecil_id
     LEFT JOIN ( SELECT stokbarang_r.barang_id,
            stokbarang_r.ruangan_id,
            sum(stokbarang_r.qty_sisa) AS qty_sisa
           FROM stokbarang_r
          GROUP BY stokbarang_r.barang_id, stokbarang_r.ruangan_id) obat_sisa ON purchasereqbrgdetail_t.barang_id = obat_sisa.barang_id AND purchasereqbrg_t.ruangan_id = obat_sisa.ruangan_id
     LEFT JOIN ( SELECT kontraksupplierbrgdetail_m.barang_id,
            kontraksupplierbrgdetail_m.satuankecil_id,
            kontraksupplierbrgdetail_m.satuankonv1_id,
            array_agg(kontraksupplierbrgdetail_m.kontraksupplierbrgdetail_id) AS kontraksupplierbrgdetail_id,
            satuan_besar.satuanunit_nama AS satuan_besar
           FROM kontraksupplierbrgdetail_m
             LEFT JOIN satuanunit_m satuan_besar ON kontraksupplierbrgdetail_m.satuankonv1_id = satuan_besar.satuanunit_id
          WHERE kontraksupplierbrgdetail_m.is_deleted = false AND kontraksupplierbrgdetail_m.is_active = true
          GROUP BY kontraksupplierbrgdetail_m.barang_id, kontraksupplierbrgdetail_m.satuankecil_id, kontraksupplierbrgdetail_m.satuankonv1_id, satuan_besar.satuanunit_nama) kontrak_supplier ON purchasereqbrgdetail_t.barang_id = kontrak_supplier.barang_id AND purchasereqbrgdetail_t.satuan_id = kontrak_supplier.satuankonv1_id AND purchasereqbrgdetail_t.satuankonversi_id = kontrak_supplier.satuankecil_id
     LEFT JOIN ( SELECT validasipobarang_t.no_pobarang,
            validasipobarangdetail_t.purchasereqbrgdetail_id,
            validasipobarang_t.status_penerimaan
           FROM validasipobarang_t
             JOIN validasipobarangdetail_t ON validasipobarang_t.validasipobarang_id = validasipobarangdetail_t.validasipobarang_id) po ON purchasereqbrgdetail_t.purchasereqbrgdetail_id = po.purchasereqbrgdetail_id
  WHERE purchasereqbrg_t.is_deleted = false AND purchasereqbrgdetail_t.is_deleted = false;");
        
      

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210810_082535_migrate_improve_purchasereq cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210810_082535_migrate_improve_purchasereq cannot be reverted.\n";

        return false;
    }
    */
}
