<?php

use yii\db\Migration;

/**
 * Class m220103_065708_migrate_infostokbarang_vpemesananbarangdetail_vmutasibarangdetail_v_stokbarang_f
 */
class m220103_065708_migrate_infostokbarang_vpemesananbarangdetail_vmutasibarangdetail_v_stokbarang_f extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('DROP FUNCTION if exists "public"."stokbarang_t()";');

        $this->execute("CREATE OR REPLACE FUNCTION public.stokbarang_t()
  RETURNS pg_catalog.trigger AS \$BODY\$
-- @created by yaya
-- before insert
--- Sok mangga mun bade di edit mah
-- 26 Maret 2018
-- 16 Januari 2019 by Ikbal, edit case mutasi barang mengurangi qty_sisa dan qty_tersedia dan menambahkan qty_keluar
DECLARE
	-- Prepare data untuk inser or update ke stokbarang_r
  	vQtyAwal INTEGER;
  	vQtyMasuk INTEGER;
  	vQtyKeluar INTEGER;
  	vQtySisa INTEGER;
  	vIdPeriode INTEGER;
  	vQtyTersedia INTEGER;
  	vQtyPesan INTEGER;
  	vIdStokBarang INTEGER;
	-- additional attributes for condition
	vIsFlag BOOLEAN;
   	vQSisa INTEGER;
   	vQKeluar INTEGER;
	-- attribute untuk periode dari periodeposting_m
   	vPeriodeId INTEGER; -- @alias periodeposting_id

	-- Untuk Nampung dari stokbarang_t
	vRuanganId INTEGER; -- @alias ruangan_id
  	vBarangId INTEGER; -- @alias barang_id
  	vMutasiBarangDetail INTEGER; -- @alias mutasibarangdetail_id
  	vTerimaMutasiDetailId INTEGER; -- @alias terimamutasidetail_id
  	vQtyIn INTEGER; -- @alias qtystok_in
  	vQtyOut INTEGER; -- @alias qtystok_out

	-- Kondisi untuk Stok Opname
   	vKondisiSo INTEGER;
   
   	-- Prepare data untuk insert after penerimaan
	vtIdStokBarang INTEGER;
	vtIdMutasiBarang INTEGER;
	vtQtyAwal INTEGER;
	vtQtyMasuk INTEGER;
	vtQtyKeluar INTEGER;
	vtQtySisa INTEGER;
	vtQtyPesan INTEGER;
	vtQtyTersedia INTEGER;
	vtIdPeriode INTEGER;
	vtRuanganId INTEGER;
	vtQtyDiPesan INTEGER;
	v_stok FLOAT;
	v_stok_tersedia FLOAT;
	v_tgl_kadaluarsa DATE;

BEGIN
  	-- ini untuk redeclare varaiable yang di perlukan untuk kondisi
  	vRuanganId := NEW.ruangan_id;
  	vBarangId := NEW.barang_id;
  	vMutasiBarangDetail := NEW.mutasibarangdetail_id;
  	vTerimaMutasiDetailId := NEW.terimamutasibarangdetail_id;
  	vQtyIn := NEW.qtystok_in;
  	vQtyOut := NEW.qtystok_out;

	-- field stok di stokbarang_t
    SELECT (COALESCE(SUM(qtystok_in) ,0) + vQtyIn) - (COALESCE(SUM(qtystok_out) ,0) + vQtyOut)  INTO v_stok
	FROM stokbarang_t
	WHERE ruangan_id = vRuanganId 
	AND barang_id = vBarangId;
	
	NEW.stok = v_stok;
 	
	-- declare untuk ke tabel stokbarang_r
    SELECT 
       	stokbarangr_id,
       	qty_awal,
       	qty_masuk,
       	qty_keluar,
       	COALESCE(qty_sisa,0),
       	qty_dipesan,
       	qty_tersedia
    INTO
       	vIdStokBarang,
       	vQtyAwal,
       	vQtyMasuk,
       	vQtyKeluar,
       	vQtySisa,
       	vQtyPesan,
       	vQtyTersedia
    FROM stokbarang_r
    WHERE barang_id = vBarangId AND ruangan_id = vRuanganId;
    
	-- Kondisi untuk ada mutasi maka Qty Pemesanan Bertambah
  	vQSisa := ((vQtyMasuk + vQtyIn) - (vQtyKeluar + vQtyOut));
  	vQKeluar := vQtyKeluar + vQtyOut;
  	IF (NEW.mutasibarangdetail_id IS NOT NULL)
	THEN 
		--update case mutasi barang
  		vQtyPesan := vQtyPesan + vQtyOut;
        vQtyTersedia := vQtyTersedia;
        vQSisa := vQtySisa;
        --vQKeluar := vQtyKeluar + vQtyOut;
    ELSEIF (NEW.terimamutasibarangdetail_id IS NOT NULL)
  	THEN
		-- Ketika mutasi tsb sudah di terimamutasibarangdetail_t
   		--vQtyPesan := vQtyIn - vQtyIn;
   		vQtyTersedia := vQtyTersedia + vQtyIn;
   		
   		-- Proses update qty setelah terima di ruangan asal mutasi
		SELECT mutasibarangdetail_id
		INTO vtIdMutasiBarang 
		FROM terimamutasibarangdetail_t
		WHERE terimamutasibarangdetail_id = NEW.terimamutasibarangdetail_id;
   		
		SELECT 
			stokbarang_t.ruangan_id, 
			mutasibarangdetail_t.qty_dipesan  
   		INTO 
   			vtRuanganId, 
   			vtQtyDiPesan
   		FROM stokbarang_t
   		JOIN mutasibarangdetail_t on stokbarang_t.mutasibarangdetail_id = mutasibarangdetail_t.mutasibarangdetail_id
		WHERE stokbarang_t.mutasibarangdetail_id = vtIdMutasiBarang;
   	
   		SELECT 
	       	stokbarangr_id,
	       	COALESCE(qty_sisa,0),
	       	qty_dipesan
	    INTO
	       	vtIdStokBarang,
	       	vtQtySisa,
	       	vtQtyPesan
	    FROM stokbarang_r
	    WHERE barang_id = vBarangId AND ruangan_id = vtRuanganId AND is_periode = true;
	   
   		-- Update qty di ruangan asal mutasi
   		UPDATE stokbarang_r SET 
			qty_sisa = vtQtySisa - vQtyIn,
			qty_dipesan = vtQtyPesan - vQtyIn
		WHERE stokbarangr_id = vtIdStokBarang;
	ELSEIF (NEW.stokopnamebarangdetail_id IS NOT NULL)
    THEN
	-- Ketika melakukan Stok opname engan kondisi Stok Awal
       	SELECT 
			h.jenisstokopname
		INTO
        	vKondisiSo
		FROM stokopnamebarangdetail_t c
		INNER JOIN stokopnamebarang_t h ON c.stokopnamebarang_id = h.stokopnamebarang_id
       	WHERE c.stokopnamebarangdetail_id = NEW.stokopnamebarangdetail_id;
       	
       	IF (vKondisiSo = 139) THEN
        	vQtyTersedia := vQtyIn - vQtyPesan;
          	vQtyMasuk := 0;
          	vQtyAwal := vQtyIn;
          	vQKeluar := 0;
          	vQSisa := vQtyIn;
		END IF;
	ELSE
		vQtyTersedia := ((vQtyMasuk + vQtyIn) - (vQtyKeluar + vQtyOut));
	END IF;

	-- Data sudah ada berarti di update datanya execute data
    IF (vIdStokBarang  != 0)
	THEN
		IF (vQtyOut != 0)
 		THEN
			vQtyTersedia = ((vQtyMasuk + vQtyIn) - (vQtyKeluar + vQtyOut));
		END IF;
		
		UPDATE stokbarang_r SET 
			qty_awal = vQtyAwal,
			qty_masuk = vQtyMasuk + vQtyIn, 
			qty_keluar = vQKeluar, 
			qty_sisa = vQSisa, 
			qty_tersedia = vQtyTersedia, 
			qty_dipesan = vQtyPesan 
		WHERE stokbarangr_id = vIdStokBarang;
                
		-- SET value baru buat stok
--	 	NEW.stok := vQSisa;
    ELSEIF (vQtyIn != 0)
    THEN
		-- SET value baru buat stok
--		NEW.stok := vQtyIn;
		-- INSERT data baru ini dari pengadaan atau mutasi barang alkes dan belum ada recordnya
        -- Membuat Stok awal yang baru
		INSERT INTO stokbarang_r 
			(ruangan_id,barang_id,qty_awal,qty_masuk,qty_keluar,qty_sisa,qty_tersedia,qty_dipesan)
		VALUES 
			 (vRuanganId, vBarangId, vQtyIn, vQtyIn, 0, vQtyIn,vQtyIn,0);
	END IF;

       
RETURN NEW;

END;\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100");
  
  
 	$this->execute('DROP VIEW if exists public.infostokbarang_v;');

   $this->execute("
       CREATE VIEW \"public\".\"infostokbarang_v\" AS
   SELECT hit.periodestok_id,
      hit.periodestok_nama,
      hit.instalasi_id,
      hit.instalasi_nama,
      hit.ruangan_id,
      hit.ruangan_nama,
      hit.barang_id,
      hit.barang_nama,
      hit.qty_masuk,
      hit.qty_keluar,
      COALESCE(hit.jml_mutasi, (0)::double precision) AS qty_dipesan,
      (hit.total - COALESCE(hit.jml_mutasi, (0)::double precision)) AS qty_tersedia,
      hit.total AS qty_stok,
      hit.tglperiodestok_awal,
      hit.tglperiodestok_akhir,
      hit.barang_kode,
      hit.ppn,
      hit.harga_jual,
      hit.satuankecil_id,
      hit.satuankecil_nama,
      hit.satuansedang_id,
      hit.satuansedang_nama,
      hit.satuanbesar_id,
      hit.satuanbesar_nama,
      hit.on_ro,
      hit.on_po,
      hit.nilai_ro,
      hit.is_kadaluarsa,
      hit.ppn_konf,
      hit.margin,
      hit.disc,
      hit.hargaygdigunakan,
      hit.harga_netto,
      hit.a1 AS hn_last_margin,
      hit.a2 AS hn_last_diskon,
      hit.a3 AS hn_last_margin_diskon,
      hit.a4 AS hn_last_ppn,
      hit.a5 AS hargajual_last,
      hit.harga_min,
      hit.b1 AS hn_min_margin,
      hit.b2 AS hn_min_diskon,
      hit.b3 AS hn_min_margin_diskon,
      hit.b4 AS hn_min_ppn,
      hit.b5 AS hargajual_min,
      hit.harga_max,
      hit.c1 AS hn_max_margin,
      hit.c2 AS hn_max_diskon,
      hit.c3 AS hn_max_margin_diskon,
      hit.c4 AS hn_max_ppn,
      hit.c5 AS hargajual_max,
      hit.barang_average,
      hit.d1 AS hn_avg_margin,
      hit.d2 AS hn_avg_diskon,
      hit.d3 AS hn_avg_margin_diskon,
      hit.d4 AS hn_avg_ppn,
      hit.d5 AS hargajual_avg,
          CASE
              WHEN ((hit.hargaygdigunakan)::text = 'MAX'::text) THEN hit.c5
              WHEN ((hit.hargaygdigunakan)::text = 'MIN'::text) THEN hit.b5
              WHEN ((hit.hargaygdigunakan)::text = 'AVG'::text) THEN hit.d5
              ELSE hit.a5
          END AS hargaygdipakai,
          CASE
              WHEN ((hit.hargaygdigunakan)::text = 'MAX'::text) THEN hit.harga_max
              WHEN ((hit.hargaygdigunakan)::text = 'MIN'::text) THEN hit.harga_min
              WHEN ((hit.hargaygdigunakan)::text = 'AVG'::text) THEN hit.barang_average
              ELSE hit.harga_netto
          END AS harganetto_ygdipakai,
          CASE
              WHEN ((hit.hargaygdigunakan)::text = 'MAX'::text) THEN hit.c1
              WHEN ((hit.hargaygdigunakan)::text = 'MIN'::text) THEN hit.b1
              WHEN ((hit.hargaygdigunakan)::text = 'AVG'::text) THEN hit.d1
              ELSE hit.a1
          END AS hn_margin,
          CASE
              WHEN ((hit.hargaygdigunakan)::text = 'MAX'::text) THEN hit.c2
              WHEN ((hit.hargaygdigunakan)::text = 'MIN'::text) THEN hit.b2
              WHEN ((hit.hargaygdigunakan)::text = 'AVG'::text) THEN hit.d2
              ELSE hit.a2
          END AS hn_diskon,
          CASE
              WHEN ((hit.hargaygdigunakan)::text = 'MAX'::text) THEN hit.c4
              WHEN ((hit.hargaygdigunakan)::text = 'MIN'::text) THEN hit.b4
              WHEN ((hit.hargaygdigunakan)::text = 'AVG'::text) THEN hit.d4
              ELSE hit.a4
          END AS hn_ppn,
      hit.kelompokbarang_id,
      hit.kelompokbarang_nama,
      hit.reference_mutasi
     FROM ( SELECT periodestokobat_m.periodestokobat_id AS periodestok_id,
              periodestokobat_m.periodestok_nama,
              instalasi_m.instalasi_id,
              instalasi_m.instalasi_nama,
              stokbarang_r.ruangan_id,
              ruangan_m.ruangan_nama,
              stokbarang_r.barang_id,
              barang_m.barang_nama,
              stokbarang_r.qty_masuk,
              stokbarang_r.qty_keluar,
              stokbarang_r.qty_dipesan,
              stokbarang_r.qty_tersedia,
              stokbarang_r.qty_sisa,
              kartustok.total,
              periodestokobat_m.tglperiodestok_awal,
              periodestokobat_m.tglperiodestok_akhir,
              barang_m.barang_kode,
              barang_m.barang_ppn AS ppn,
              barang_m.barang_hargajual AS harga_jual,
              barang_m.barang_harganetto AS harga_netto,
              barang_m.barang_max AS harga_max,
              barang_m.barang_min AS harga_min,
              barang_m.barang_average,
              barang_m.on_ro,
              barang_m.on_po,
              barang_m.satuankecil_id,
              satuanunit_m.satuanunit_nama AS satuankecil_nama,
              NULL::text AS satuansedang_id,
              NULL::text AS satuansedang_nama,
              NULL::text AS satuanbesar_id,
              NULL::text AS satuanbesar_nama,
              barang_m.nilai_ro,
              barang_m.is_kadaluarsa,
              konfigfarmasi_k.persenppn AS ppn_konf,
              konfigfarmasi_k.persenmargin AS margin,
              konfigfarmasi_k.persen_diskon AS disc,
              konfigfarmasi_k.hargaygdigunakan,
              (barang_m.barang_harganetto + ((barang_m.barang_harganetto * konfigfarmasi_k.persenmargin) / (100)::double precision)) AS a1,
              (((barang_m.barang_harganetto + ((barang_m.barang_harganetto * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision) AS a2,
              ((barang_m.barang_harganetto + ((barang_m.barang_harganetto * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_harganetto + ((barang_m.barang_harganetto * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) AS a3,
              ((((barang_m.barang_harganetto + ((barang_m.barang_harganetto * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_harganetto + ((barang_m.barang_harganetto * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision) AS a4,
              (((barang_m.barang_harganetto + ((barang_m.barang_harganetto * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_harganetto + ((barang_m.barang_harganetto * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) + ((((barang_m.barang_harganetto + ((barang_m.barang_harganetto * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_harganetto + ((barang_m.barang_harganetto * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision)) AS a5,
              (barang_m.barang_min + ((barang_m.barang_min * konfigfarmasi_k.persenmargin) / (100)::double precision)) AS b1,
              (((barang_m.barang_min + ((barang_m.barang_min * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision) AS b2,
              ((barang_m.barang_min + ((barang_m.barang_min * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_min + ((barang_m.barang_min * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) AS b3,
              ((((barang_m.barang_min + ((barang_m.barang_min * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_min + ((barang_m.barang_min * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision) AS b4,
              (((barang_m.barang_min + ((barang_m.barang_min * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_min + ((barang_m.barang_min * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) + ((((barang_m.barang_min + ((barang_m.barang_min * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_min + ((barang_m.barang_min * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision)) AS b5,
              (barang_m.barang_max + ((barang_m.barang_max * konfigfarmasi_k.persenmargin) / (100)::double precision)) AS c1,
              (((barang_m.barang_max + ((barang_m.barang_max * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision) AS c2,
              ((barang_m.barang_max + ((barang_m.barang_max * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_max + ((barang_m.barang_max * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) AS c3,
              ((((barang_m.barang_max + ((barang_m.barang_max * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_max + ((barang_m.barang_max * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision) AS c4,
              (((barang_m.barang_max + ((barang_m.barang_max * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_max + ((barang_m.barang_max * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) + ((((barang_m.barang_max + ((barang_m.barang_max * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_max + ((barang_m.barang_max * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision)) AS c5,
              (barang_m.barang_average + ((barang_m.barang_average * konfigfarmasi_k.persenmargin) / (100)::double precision)) AS d1,
              (((barang_m.barang_average + ((barang_m.barang_average * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision) AS d2,
              ((barang_m.barang_average + ((barang_m.barang_average * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_average + ((barang_m.barang_average * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) AS d3,
              ((((barang_m.barang_average + ((barang_m.barang_average * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_average + ((barang_m.barang_average * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision) AS d4,
              (((barang_m.barang_average + ((barang_m.barang_average * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_average + ((barang_m.barang_average * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) + ((((barang_m.barang_average + ((barang_m.barang_average * konfigfarmasi_k.persenmargin) / (100)::double precision)) - (((barang_m.barang_average + ((barang_m.barang_average * konfigfarmasi_k.persenmargin) / (100)::double precision)) * konfigfarmasi_k.persen_diskon) / (100)::double precision)) * konfigfarmasi_k.persenppn) / (100)::double precision)) AS d5,
              barang_m.kelompokbarang_id,
              kelompokbarang_m.kelompokbarang_nama,
              mutasi.jml AS jml_mutasi,
              mutasi.reference AS reference_mutasi
             FROM (((((((((stokbarang_r
               JOIN ruangan_m ON ((stokbarang_r.ruangan_id = ruangan_m.ruangan_id)))
               JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
               JOIN barang_m ON ((stokbarang_r.barang_id = barang_m.barang_id)))
               LEFT JOIN periodestokobat_m ON ((stokbarang_r.periodestokbarang_id = periodestokobat_m.periodestokobat_id)))
               JOIN konfigfarmasi_k ON ((konfigfarmasi_k.is_deleted = false)))
               JOIN satuanunit_m ON ((barang_m.satuankecil_id = satuanunit_m.satuanunit_id)))
               LEFT JOIN kelompokbarang_m ON ((barang_m.kelompokbarang_id = kelompokbarang_m.kelompokbarang_id)))
               LEFT JOIN ( SELECT st.ruangan_id,
                      st.barang_id,
                      sum((st.qtystok_in - st.qtystok_out)) AS total
                     FROM stokbarang_t st
                    GROUP BY st.ruangan_id, st.barang_id) kartustok ON (((kartustok.ruangan_id = stokbarang_r.ruangan_id) AND (kartustok.barang_id = stokbarang_r.barang_id))))
               LEFT JOIN ( SELECT mt2.ruanganasal_id AS ruangan_id,
                      mt.barang_id,
                      sum(mt.qty_mutasi) AS jml,
                      string_agg((mt2.nomutasi_barang)::text, ','::text) AS reference
                     FROM (mutasibarangdetail_t mt
                       LEFT JOIN mutasibarang_t mt2 ON ((mt2.mutasibarang_id = mt.mutasibarang_id)))
                    WHERE ((mt.is_deleted IS FALSE) AND (mt2.is_deleted IS FALSE) AND (mt2.status_mutasi = 401))
                    GROUP BY mt2.ruanganasal_id, mt.barang_id) mutasi ON (((mutasi.ruangan_id = stokbarang_r.ruangan_id) AND (mutasi.barang_id = stokbarang_r.barang_id))))
            WHERE ((barang_m.is_active = true) AND (barang_m.is_deleted = false) AND (stokbarang_r.is_periode = true))) hit;");

       $this->execute('
           ALTER TABLE public.infostokbarang_v OWNER TO postgres;');
	   
	   
      $this->execute('DROP VIEW if exists public.infopemesananbarangdetail_v;');

      $this->execute("
          CREATE VIEW \"public\".\"infopemesananbarangdetail_v\" AS
	  SELECT pesanbarangdetail_t.pesanbarangdetail_id,
	     pesanbarangdetail_t.pesanbarang_id,
	     pesanbarang_t.tgl_pesanbarang,
	     pesanbarang_t.tgl_mintadikirim,
	     pesanbarang_t.no_pemesanan,
	     pesanbarang_t.ruanganpemesan_id,
	     ruanganpemesan.ruangan_nama AS ruangan_pemesan,
	     pesanbarang_t.ruangantujuan_id,
	     ruangantujuan.ruangan_nama AS ruangan_tujuan,
	     pesanbarangdetail_t.barang_id,
	     barang_m.barang_nama,
	     pesanbarangdetail_t.jumlah_input,
	     pesanbarangdetail_t.qty_pesan,
	     (kartustok.total - COALESCE(mutasi.jml, (0)::double precision)) AS qty_tersedia,
	     pesanbarangdetail_t.satuankecil_id,
	     satuan_kecil.satuanunit_nama AS satuan_kecil,
	     pesanbarangdetail_t.satuanbesar_id,
	     satuan_besar.satuanunit_nama AS satuan_besar,
	     pesanbarangdetail_t.is_deleted,
	     satuankonversibrg_m.nilai_konversi,
	     fgetharganettobarang(pesanbarangdetail_t.barang_id) AS harga_netto,
	     COALESCE(stokpemesan.qty_tersedia, 0) AS stok_pemesan,
	     kartustok.total AS stok_tujuan
	    FROM (((((((((((pesanbarangdetail_t
	      JOIN pesanbarang_t ON ((pesanbarangdetail_t.pesanbarang_id = pesanbarang_t.pesanbarang_id)))
	      LEFT JOIN stokbarang_r ON (((pesanbarangdetail_t.barang_id = stokbarang_r.barang_id) AND (pesanbarang_t.ruangantujuan_id = stokbarang_r.ruangan_id))))
	      LEFT JOIN stokbarang_r stokpemesan ON (((pesanbarangdetail_t.barang_id = stokpemesan.barang_id) AND (pesanbarang_t.ruanganpemesan_id = stokpemesan.ruangan_id))))
	      JOIN barang_m ON ((pesanbarangdetail_t.barang_id = barang_m.barang_id)))
	      JOIN ruangan_m ruangantujuan ON ((pesanbarang_t.ruangantujuan_id = ruangantujuan.ruangan_id)))
	      JOIN ruangan_m ruanganpemesan ON ((pesanbarang_t.ruanganpemesan_id = ruanganpemesan.ruangan_id)))
	      JOIN satuanunit_m satuan_kecil ON ((pesanbarangdetail_t.satuankecil_id = satuan_kecil.satuanunit_id)))
	      JOIN satuanunit_m satuan_besar ON ((pesanbarangdetail_t.satuanbesar_id = satuan_besar.satuanunit_id)))
	      JOIN satuankonversibrg_m ON (((pesanbarangdetail_t.satuanbesar_id = satuankonversibrg_m.satuanbesar_id) AND (pesanbarangdetail_t.satuankecil_id = satuankonversibrg_m.satuankecil_id) AND (pesanbarangdetail_t.barang_id = satuankonversibrg_m.barang_id))))
	      LEFT JOIN ( SELECT st.ruangan_id,
	             st.barang_id,
	             sum((st.qtystok_in - st.qtystok_out)) AS total
	            FROM stokbarang_t st
	           GROUP BY st.ruangan_id, st.barang_id) kartustok ON (((kartustok.ruangan_id = pesanbarang_t.ruangantujuan_id) AND (kartustok.barang_id = pesanbarangdetail_t.barang_id))))
	      LEFT JOIN ( SELECT mt2.ruanganasal_id AS ruangan_id,
	             mt.barang_id,
	             sum(mt.qty_mutasi) AS jml,
	             string_agg((mt2.nomutasi_barang)::text, ','::text) AS reference
	            FROM (mutasibarangdetail_t mt
	              LEFT JOIN mutasibarang_t mt2 ON ((mt2.mutasibarang_id = mt.mutasibarang_id)))
	           WHERE ((mt.is_deleted IS FALSE) AND (mt2.is_deleted IS FALSE) AND (mt2.status_mutasi = 401))
	           GROUP BY mt2.ruanganasal_id, mt.barang_id) mutasi ON (((mutasi.ruangan_id = pesanbarang_t.ruangantujuan_id) AND (mutasi.barang_id = pesanbarangdetail_t.barang_id))))
	   WHERE (pesanbarangdetail_t.is_active = true);");

          $this->execute(' ALTER TABLE public.infopemesananbarangdetail_v OWNER TO postgres;');
		
		
	      $this->execute('DROP VIEW if exists public.infomutasibarangdetail_v;');

	      $this->execute("
	          CREATE VIEW \"public\".\"infomutasibarangdetail_v\" AS
		  SELECT mutasibarangdetail_t.mutasibarangdetail_id,
		     mutasibarang_t.mutasibarang_id,
		     mutasibarang_t.nomutasi_barang,
		     mutasibarang_t.tgl_mutasibarang,
		     instalasi_tujuan.instalasi_id AS instalasi_tujuan_id,
		     instalasi_tujuan.instalasi_nama,
		     ruangan_tujuan.ruangan_id AS ruangan_tujuan_id,
		     ruangan_tujuan.ruangan_nama,
		     instalasi_m.instalasi_id AS instalasi_asal_id,
		     instalasi_m.instalasi_nama AS instalasi_asal,
		     ruangan_m.ruangan_id AS ruangan_asal_id,
		     ruangan_m.ruangan_nama AS ruangan_asal,
		     mutasibarangdetail_t.qty_mutasi,
		     barang_m.barang_id,
		     barang_m.barang_nama,
		     mutasibarangdetail_t.satuankecil_id AS satuanbrg,
		     satuan_kecil.satuanunit_nama AS lookup_value,
		     mutasibarangdetail_t.satuankecil_id,
		     mutasibarangdetail_t.satuanbesar_id,
		     satuan_kecil.satuanunit_nama AS satuankecil_nama,
		     satuan_besar.satuanunit_nama AS satuanbesar_nama,
		     mutasibarangdetail_t.harga_netto,
		     mutasibarangdetail_t.jumlah_input AS qty_input,
		     pesanbarangdetail_t.jumlah_input AS qty_dipesan,
		     pesanbarang_t.no_pemesanan,
		     pesanbarang_t.pesanbarang_id
		    FROM ((((((((((mutasibarangdetail_t
		      JOIN mutasibarang_t ON ((mutasibarangdetail_t.mutasibarang_id = mutasibarang_t.mutasibarang_id)))
		      JOIN barang_m ON ((mutasibarangdetail_t.barang_id = barang_m.barang_id)))
		      JOIN ruangan_m ON ((mutasibarang_t.ruanganasal_id = ruangan_m.ruangan_id)))
		      JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
		      JOIN ruangan_m ruangan_tujuan ON ((mutasibarang_t.ruangantujuan_id = ruangan_tujuan.ruangan_id)))
		      JOIN instalasi_m instalasi_tujuan ON ((ruangan_tujuan.instalasi_id = instalasi_tujuan.instalasi_id)))
		      JOIN satuanunit_m satuan_besar ON ((mutasibarangdetail_t.satuanbesar_id = satuan_besar.satuanunit_id)))
		      JOIN satuanunit_m satuan_kecil ON ((mutasibarangdetail_t.satuankecil_id = satuan_kecil.satuanunit_id)))
		      JOIN pesanbarangdetail_t ON ((mutasibarangdetail_t.pesanbarangdetail_id = pesanbarangdetail_t.pesanbarangdetail_id)))
		      JOIN pesanbarang_t ON ((pesanbarang_t.pesanbarang_id = pesanbarangdetail_t.pesanbarang_id)))
		   WHERE ((mutasibarang_t.is_active = true) AND (mutasibarangdetail_t.is_deleted = false));");

	          $this->execute(' ALTER TABLE public.infomutasibarangdetail_v OWNER TO postgres;');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220103_065708_migrate_infostokbarang_vpemesananbarangdetail_vmutasibarangdetail_v_stokbarang_f cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220103_065708_migrate_infostokbarang_vpemesananbarangdetail_vmutasibarangdetail_v_stokbarang_f cannot be reverted.\n";

        return false;
    }
    */
}
