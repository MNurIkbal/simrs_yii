<?php

use yii\db\Migration;

/**
 * Class m220203_093255_migrate_CDH25_schemaview
 */
class m220203_093255_migrate_CDH25_schemaview extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		 $this->execute('ALTER TABLE "public"."konfigfarmasi_k" ADD COLUMN if not exists "batal_pesan_by" varchar(255);');
		 
		 $this->execute("COMMENT ON COLUMN public.konfigfarmasi_k.batal_pesan_by IS 'value pemesan/tujuan';");
		 
         $this->execute('DROP VIEW if exists public.infodistribusiobatalkes_v;');
		 
         $this->execute("
             CREATE VIEW \"public\".\"infodistribusiobatalkes_v\" AS  SELECT pesanobatalkes_t.pesanobatalkes_id,
    pesanobatalkes_t.tglpemesanan,
    pesanobatalkes_t.ruangan_id,
    ruangan_m.ruangan_nama AS ruangan_tujuan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_tujuan,
    pesanobatalkes_t.nopemesanan,
    pesanobatalkes_t.ruanganpemesan_id,
    ruangpemesan.ruangan_id AS ruangan_pemesan_id,
    ruangpemesan.ruangan_nama AS ruangan_pemesan,
    instalasipesan.instalasi_id AS instalasi_pemesan_id,
    instalasipesan.instalasi_nama AS instalasi_pemesan,
    pesanobatalkes_t.mutasiobatruangan_id,
    pesanobatalkes_t.statuspesan,
    fgetnamalookup(pesanobatalkes_t.statuspesan::integer) AS status_pengiriman,
    pesanobatalkes_t.tglmintadikirim,
    pesanobatalkes_t.keterangan_pesan,
    mutasiobatruangan_t.nomutasioa,
    mutasiobatruangan_t.tglmutasioa,
    mutasiobatruangan_t.status_mutasi,
    fgetnamalookup(mutasiobatruangan_t.status_mutasi) AS status_penerimaan,
    terimamutasiobat_t.noterimamutasi,
    terimamutasiobat_t.tglterima,
    concat(instalasi_m.instalasi_nama, ' - ', ruangan_m.ruangan_nama) AS instalasi_ruangan,
        CASE
            WHEN pesanobatalkes_t.statuspesan::text = '398'::text THEN 'Belum Dikirim'::text
            WHEN mutasiobatruangan_t.status_mutasi = 401 THEN 'Sudah Dikirim'::text
            WHEN mutasiobatruangan_t.status_mutasi = 400 THEN 'Diterima'::text
            WHEN pesanobatalkes_t.statuspesan::text = '602'::text THEN fgetnamalookup(pesanobatalkes_t.statuspesan::integer)::text
            ELSE '-'::text
        END AS status_distribusi,
    concat(mutasiobatruangan_t.nomutasioa,
        CASE
            WHEN terimamutasiobat_t.noterimamutasi IS NULL THEN ''::text
            ELSE concat(',', terimamutasiobat_t.noterimamutasi)
        END) AS reference,
        CASE
            WHEN pesanobatalkes_t.mutasiobatruangan_id IS NULL THEN pesanobatalkes_t.statuspesan::integer
            WHEN pesanobatalkes_t.mutasiobatruangan_id IS NOT NULL THEN mutasiobatruangan_t.status_mutasi
            WHEN terimamutasiobat_t.mutasiobatruangan_id IS NOT NULL THEN mutasiobatruangan_t.status_mutasi
            ELSE NULL::integer
        END AS status_id,
    pesanobatalkes_t.status_verifikasi,
        CASE
            WHEN pesanobatalkes_t.status_verifikasi IS NULL THEN 'Tanpa Verifkasi'::character varying
            ELSE fgetnamalookup(pesanobatalkes_t.status_verifikasi::integer)
        END AS status_verifikasi_nama,
    pemesan.nama_pegawai AS pemesan,
    pengirim.nama_pegawai AS pengirim,
    penerima.nama_pegawai AS penerima
   FROM pesanobatalkes_t
     JOIN ruangan_m ON pesanobatalkes_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN ruangan_m ruangpemesan ON pesanobatalkes_t.ruanganpemesan_id = ruangpemesan.ruangan_id
     JOIN instalasi_m instalasipesan ON ruangpemesan.instalasi_id = instalasipesan.instalasi_id
     LEFT JOIN mutasiobatruangan_t ON pesanobatalkes_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id
     LEFT JOIN terimamutasiobat_t ON mutasiobatruangan_t.mutasiobatruangan_id = terimamutasiobat_t.mutasiobatruangan_id
     LEFT JOIN pegawai_m pemesan ON pesanobatalkes_t.pegawaipemesan_id = pemesan.pegawai_id
     LEFT JOIN pegawai_m pengirim ON mutasiobatruangan_t.pegawaimenyetujui_id = pengirim.pegawai_id
     LEFT JOIN pegawai_m penerima ON terimamutasiobat_t.pegawaipenerima_id = penerima.pegawai_id
  WHERE pesanobatalkes_t.is_active = true AND pesanobatalkes_t.is_deleted = false; ");
  
  $this->execute('DROP VIEW if exists public.detailpemesananobatalkes_v;');
  
       $this->execute("
           CREATE VIEW \"public\".\"detailpemesananobatalkes_v\" AS  SELECT hit.pesanobatalkes_id,
    hit.tglpemesanan,
    hit.ruangan_id,
    hit.ruangan_tujuan,
    hit.instalasi_id,
    hit.instalasi_tujuan,
    hit.nopemesanan,
    hit.keterangan_pesan,
    hit.ruanganpemesan_id,
    hit.ruangan_pemesan_id,
    hit.ruangan_pemesan,
    hit.instalasi_pemesan_id,
    hit.instalasi_pemesan,
    hit.obatalkes_id,
    hit.obatalkes_namalain,
    hit.obatalkes_nama,
    hit.jumlah_pesan,
    hit.jumlah_diterima,
    hit.tglmintadikirim,
    hit.mutasiobatdetail_id,
    hit.satuankecil_id,
    hit.pesanobatdetail_id,
    hit.satuanbesar_id,
    hit.satuan_besar,
    hit.satuan_kecil,
    hit.qty_besar,
    hit.harganetto,
    hit.hargamaksimum,
    hit.hargaminimum,
    hit.hargaratarata,
    hit.discount,
    hit.satuan_pemesanan,
    hit.satuan_pemesanan_nama,
    hit.qty_kecil,
    hit.statuspesan,
    hit.total_stok - (COALESCE(hit.jml_mutasi, 0::double precision) + COALESCE(hit.jml_resep_farmasi, 0::double precision) + COALESCE(hit.jml_resep_dokter, 0::double precision)) AS stok_pemesan,
    hit.total_stok2 - (COALESCE(hit.jml_mutasi2, 0::double precision) + COALESCE(hit.jml_resep_farmasi2, 0::double precision) + COALESCE(hit.jml_resep_dokter2, 0::double precision)) AS stok_pengirim,
    hit.jumlah_mutasi,
    hit.satuan_kirim,
    hit.jumlah_input_mutasi,
    hit.pesan_is_deleted
   FROM ( SELECT pesanobatalkes_t.pesanobatalkes_id,
            pesanobatalkes_t.tglpemesanan,
            pesanobatalkes_t.ruangan_id,
            ruangan_m.ruangan_nama AS ruangan_tujuan,
            instalasi_m.instalasi_id,
            instalasi_m.instalasi_nama AS instalasi_tujuan,
            pesanobatalkes_t.nopemesanan,
            pesanobatalkes_t.keterangan_pesan,
            pesanobatalkes_t.ruanganpemesan_id,
            ruangpemesan.ruangan_id AS ruangan_pemesan_id,
            ruangpemesan.ruangan_nama AS ruangan_pemesan,
            instalasipesan.instalasi_id AS instalasi_pemesan_id,
            instalasipesan.instalasi_nama AS instalasi_pemesan,
            pesanobatdetail_t.obatalkes_id,
            obatalkes_m.obatalkes_nama AS obatalkes_namalain,
            obatalkes_m.obatalkes_nama,
            pesanobatdetail_t.jumlah_pesan,
            COALESCE(qty_diterima.qty_diterima, 0::double precision) AS jumlah_diterima,
            pesanobatalkes_t.tglmintadikirim,
            pesanobatdetail_t.mutasiobatdetail_id,
            pesanobatdetail_t.satuankecil_id,
            pesanobatdetail_t.pesanobatdetail_id,
            pesanobatdetail_t.satuanbesar_id,
            satuan_besar.satuanunit_nama AS satuan_besar,
            satuan_kecil.satuanunit_nama AS satuan_kecil,
            pesanobatdetail_t.jumlah_input AS qty_besar,
            obatalkes_m.harganetto,
            obatalkes_m.hargamaksimum,
            obatalkes_m.hargaminimum,
            obatalkes_m.hargaratarata,
            obatalkes_m.discount,
            pesanobatdetail_t.satuan_pemesanan,
            sat_pemesanan.satuanunit_nama AS satuan_pemesanan_nama,
            pesanobatdetail_t.jumlah_pesan AS qty_kecil,
            pesanobatalkes_t.statuspesan,
            COALESCE(mutasi.jml, 0::double precision) AS jml_mutasi,
            COALESCE(resepfarmasi.jml, 0::double precision) AS jml_resep_farmasi,
            COALESCE(resepdokter.jml, 0::double precision) AS jml_resep_dokter,
            COALESCE(mutasi2.jml, 0::double precision) AS jml_mutasi2,
            COALESCE(resepfarmasi2.jml, 0::double precision) AS jml_resep_farmasi2,
            COALESCE(resepdokter2.jml, 0::double precision) AS jml_resep_dokter2,
            kartustok.total AS total_stok,
            kartustok2.total AS total_stok2,
            mutasi3.jumlah_mutasi,
            mutasi3.satuan_kirim,
            COALESCE(mutasi3.jumlah_mutasi, 0::double precision) / (COALESCE(NULLIF(pesanobatdetail_t.jumlah_pesan, 0::double precision), 1::double precision) / COALESCE(NULLIF(pesanobatdetail_t.jumlah_input, 0::double precision), 1::double precision)) AS jumlah_input_mutasi,
            pesanobatdetail_t.is_deleted AS pesan_is_deleted
           FROM pesanobatdetail_t
             JOIN pesanobatalkes_t ON pesanobatdetail_t.pesanobatalkes_id = pesanobatalkes_t.pesanobatalkes_id
             JOIN ruangan_m ON pesanobatalkes_t.ruangan_id = ruangan_m.ruangan_id
             JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
             JOIN ruangan_m ruangpemesan ON pesanobatalkes_t.ruanganpemesan_id = ruangpemesan.ruangan_id
             JOIN instalasi_m instalasipesan ON ruangpemesan.instalasi_id = instalasipesan.instalasi_id
             JOIN obatalkes_m ON pesanobatdetail_t.obatalkes_id = obatalkes_m.obatalkes_id
             LEFT JOIN satuanunit_m satuan_besar ON pesanobatdetail_t.satuanbesar_id = satuan_besar.satuanunit_id
             LEFT JOIN satuanunit_m satuan_kecil ON pesanobatdetail_t.satuankecil_id = satuan_kecil.satuanunit_id
             LEFT JOIN satuanunit_m sat_pemesanan ON pesanobatdetail_t.satuan_pemesanan = sat_pemesanan.satuanunit_id
             LEFT JOIN ( SELECT stokobatalkes_r.ruangan_id,
                    stokobatalkes_r.stokobatr_id,
                    stokobatalkes_r.obatalkes_id,
                    stokobatalkes_r.qty_tersedia
                   FROM stokobatalkes_r
                  WHERE stokobatalkes_r.is_deleted = false) stok_pemesan ON pesanobatalkes_t.ruanganpemesan_id = stok_pemesan.ruangan_id AND pesanobatdetail_t.obatalkes_id = stok_pemesan.obatalkes_id
             LEFT JOIN ( SELECT mutasiobatdetail_t.pesanobatdetail_id,
                    terimamutasiobatdetail_t.jmlterima AS qty_diterima
                   FROM terimamutasiobatdetail_t
                     JOIN mutasiobatdetail_t ON terimamutasiobatdetail_t.mutasiobatdetail_id = mutasiobatdetail_t.mutasiobatdetail_id
                     JOIN mutasiobatruangan_t ON mutasiobatdetail_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id
                  WHERE mutasiobatruangan_t.status_mutasi = 400) qty_diterima ON pesanobatdetail_t.pesanobatdetail_id = qty_diterima.pesanobatdetail_id
             LEFT JOIN ( SELECT mutasiobatdetail_t.pesanobatdetail_id,
                    mutasiobatdetail_t.jumlah_input,
                    mutasiobatdetail_t.jumlah_mutasi,
                    mutasiobatdetail_t.satuankecil_id,
                    satuanunit_m.satuanunit_nama AS satuan_kirim
                   FROM mutasiobatdetail_t
                     LEFT JOIN satuanunit_m ON mutasiobatdetail_t.satuankecil_id = satuanunit_m.satuanunit_id) mutasi3 ON pesanobatdetail_t.pesanobatdetail_id = mutasi3.pesanobatdetail_id
             LEFT JOIN ( SELECT mt2.ruanganasal_id AS ruangan_id,
                    mt.obatalkes_id,
                    sum(mt.jumlah_mutasi) AS jml,
                    string_agg(mt2.nomutasioa::text, ','::text) AS reference
                   FROM mutasiobatdetail_t mt
                     LEFT JOIN mutasiobatruangan_t mt2 ON mt2.mutasiobatruangan_id = mt.mutasiobatruangan_id
                  WHERE mt.is_deleted IS FALSE AND mt2.is_deleted IS FALSE AND mt2.status_mutasi = 401
                  GROUP BY mt2.ruanganasal_id, mt.obatalkes_id) mutasi ON mutasi.ruangan_id = stok_pemesan.ruangan_id AND mutasi.obatalkes_id = stok_pemesan.obatalkes_id
             LEFT JOIN konfigrak_m ON stok_pemesan.stokobatr_id = konfigrak_m.stokobatr_id AND stok_pemesan.obatalkes_id = konfigrak_m.obatalkes_id
             LEFT JOIN ( SELECT st.ruangan_id,
                    st.obatalkes_id,
                    sum(st.qtystok_in - st.qtystok_out) AS total
                   FROM stokobatalkes_t st
                  GROUP BY st.ruangan_id, st.obatalkes_id) kartustok ON kartustok.ruangan_id = stok_pemesan.ruangan_id AND kartustok.obatalkes_id = stok_pemesan.obatalkes_id AND konfigrak_m.is_deleted = false
             LEFT JOIN ( SELECT pt.ruangan_id,
                    ot.obatalkes_id,
                    sum(COALESCE(ot.det_konversi, ot.qty_konversi)) AS jml,
                    string_agg(pt.noresep::text, ','::text) AS reference
                   FROM obatalkespasien_t ot
                     LEFT JOIN obatalkes_m om ON om.obatalkes_id = ot.obatalkes_id
                     LEFT JOIN penjualanresep_t pt ON pt.penjualanresep_id = ot.penjualanresep_id
                  WHERE ot.is_deleted IS FALSE AND pt.is_deleted IS FALSE AND pt.status_reseptur <> 660 AND pt.status_reseptur <> 432 AND pt.reseptur_id IS NULL
                  GROUP BY pt.ruangan_id, ot.obatalkes_id) resepfarmasi ON resepfarmasi.ruangan_id = stok_pemesan.ruangan_id AND resepfarmasi.obatalkes_id = stok_pemesan.obatalkes_id
             LEFT JOIN ( SELECT rt.ruangan_id,
                    dt.obatalkes_id,
                    sum(COALESCE(dt.det_konversi, dt.qty_konversi)) AS jml,
                    string_agg(rt.noresep::text, ','::text) AS reference
                   FROM reseptur_t rt
                     JOIN resepturdetail_t dt ON dt.reseptur_id = rt.reseptur_id
                  WHERE rt.status_reseptur <> 660 AND rt.status_reseptur <> 432 AND rt.is_deleted IS FALSE AND dt.is_deleted IS FALSE
                  GROUP BY rt.ruangan_id, dt.obatalkes_id) resepdokter ON resepdokter.ruangan_id = stok_pemesan.ruangan_id AND resepdokter.obatalkes_id = stok_pemesan.obatalkes_id
             LEFT JOIN ( SELECT stokobatalkes_r.ruangan_id,
                    stokobatalkes_r.obatalkes_id,
                    stokobatalkes_r.stokobatr_id,
                    stokobatalkes_r.qty_tersedia
                   FROM stokobatalkes_r
                  WHERE stokobatalkes_r.is_deleted = false) stok_pengirim ON pesanobatalkes_t.ruangan_id = stok_pengirim.ruangan_id AND pesanobatdetail_t.obatalkes_id = stok_pengirim.obatalkes_id
             LEFT JOIN ( SELECT mt2.ruanganasal_id AS ruangan_id,
                    mt.obatalkes_id,
                    sum(mt.jumlah_mutasi) AS jml,
                    string_agg(mt2.nomutasioa::text, ','::text) AS reference
                   FROM mutasiobatdetail_t mt
                     LEFT JOIN mutasiobatruangan_t mt2 ON mt2.mutasiobatruangan_id = mt.mutasiobatruangan_id
                  WHERE mt.is_deleted IS FALSE AND mt2.is_deleted IS FALSE AND mt2.status_mutasi = 401
                  GROUP BY mt2.ruanganasal_id, mt.obatalkes_id) mutasi2 ON mutasi2.ruangan_id = stok_pengirim.ruangan_id AND mutasi2.obatalkes_id = stok_pengirim.obatalkes_id
             LEFT JOIN konfigrak_m konfigrak_m2 ON stok_pengirim.stokobatr_id = konfigrak_m2.stokobatr_id AND stok_pengirim.obatalkes_id = konfigrak_m2.obatalkes_id
             LEFT JOIN ( SELECT st.ruangan_id,
                    st.obatalkes_id,
                    sum(st.qtystok_in - st.qtystok_out) AS total
                   FROM stokobatalkes_t st
                  GROUP BY st.ruangan_id, st.obatalkes_id) kartustok2 ON kartustok2.ruangan_id = stok_pengirim.ruangan_id AND kartustok2.obatalkes_id = stok_pengirim.obatalkes_id AND konfigrak_m2.is_deleted = false
             LEFT JOIN ( SELECT pt.ruangan_id,
                    ot.obatalkes_id,
                    sum(COALESCE(ot.det_konversi, ot.qty_konversi)) AS jml,
                    string_agg(pt.noresep::text, ','::text) AS reference
                   FROM obatalkespasien_t ot
                     LEFT JOIN obatalkes_m om ON om.obatalkes_id = ot.obatalkes_id
                     LEFT JOIN penjualanresep_t pt ON pt.penjualanresep_id = ot.penjualanresep_id
                  WHERE ot.is_deleted IS FALSE AND pt.is_deleted IS FALSE AND pt.status_reseptur <> 660 AND pt.status_reseptur <> 432 AND pt.reseptur_id IS NULL
                  GROUP BY pt.ruangan_id, ot.obatalkes_id) resepfarmasi2 ON resepfarmasi2.ruangan_id = stok_pengirim.ruangan_id AND resepfarmasi2.obatalkes_id = stok_pengirim.obatalkes_id
             LEFT JOIN ( SELECT rt.ruangan_id,
                    dt.obatalkes_id,
                    sum(COALESCE(dt.det_konversi, dt.qty_konversi)) AS jml,
                    string_agg(rt.noresep::text, ','::text) AS reference
                   FROM reseptur_t rt
                     JOIN resepturdetail_t dt ON dt.reseptur_id = rt.reseptur_id
                  WHERE rt.status_reseptur <> 660 AND rt.status_reseptur <> 432 AND rt.is_deleted IS FALSE AND dt.is_deleted IS FALSE
                  GROUP BY rt.ruangan_id, dt.obatalkes_id) resepdokter2 ON resepdokter2.ruangan_id = stok_pengirim.ruangan_id AND resepdokter2.obatalkes_id = stok_pengirim.obatalkes_id
          WHERE pesanobatdetail_t.is_active = true) hit; ");

   $this->execute('DROP VIEW if exists public.infodistribusibarang_v;');
   
    $this->execute("
        CREATE VIEW \"public\".\"infodistribusibarang_v\" AS  SELECT pesanbarang_t.pesanbarang_id,
    pesanbarang_t.tgl_pesanbarang,
    pesanbarang_t.ruangantujuan_id,
    ruangan_m.ruangan_nama AS ruangan_tujuan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_tujuan,
    pesanbarang_t.no_pemesanan,
    pesanbarang_t.ruanganpemesan_id,
    ruangpemesan.ruangan_id AS ruangan_pemesan_id,
    ruangpemesan.ruangan_nama AS ruangan_pemesan,
    instalasipesan.instalasi_id AS instalasi_pemesan_id,
    instalasipesan.instalasi_nama AS instalasi_pemesan,
    mutasibarang_t.mutasibarang_id,
    pesanbarang_t.statuspesan,
    fgetnamalookup(pesanbarang_t.statuspesan::integer) AS status_pengiriman,
    pesanbarang_t.tgl_mintadikirim,
    pesanbarang_t.keterangan_pesan,
    mutasibarang_t.nomutasi_barang,
    mutasibarang_t.tgl_mutasibarang,
    mutasibarang_t.status_mutasi,
    fgetnamalookup(mutasibarang_t.status_mutasi) AS status_penerimaan,
    terimamutasibarang_t.noterimamutasi,
    terimamutasibarang_t.tglterima,
    concat(instalasi_m.instalasi_nama, ' - ', ruangan_m.ruangan_nama) AS instalasi_ruangan,
        CASE
            WHEN pesanbarang_t.statuspesan::text = '398'::text THEN 'Belum Dikirim'::text
            WHEN mutasibarang_t.status_mutasi = 401 THEN 'Sudah Dikirim'::text
            WHEN mutasibarang_t.status_mutasi = 400 THEN 'Diterima'::text
            WHEN pesanbarang_t.statuspesan::text = '602'::text THEN fgetnamalookup(pesanbarang_t.statuspesan::integer)::text
            ELSE '-'::text
        END AS status_distribusi,
    concat(mutasibarang_t.nomutasi_barang,
        CASE
            WHEN terimamutasibarang_t.noterimamutasi IS NULL THEN ''::text
            ELSE concat(',', terimamutasibarang_t.noterimamutasi)
        END) AS reference,
        CASE
            WHEN mutasibarang_t.pesanbarang_id IS NULL THEN pesanbarang_t.statuspesan::integer
            WHEN mutasibarang_t.pesanbarang_id IS NOT NULL THEN mutasibarang_t.status_mutasi
            WHEN terimamutasibarang_t.mutasibarang_id IS NOT NULL THEN mutasibarang_t.status_mutasi
            ELSE NULL::integer
        END AS status_id,
    NULL::text AS status_verifikasi,
    NULL::text AS status_verifikasi_nama,
    pemesan.nama_pegawai AS pemesan,
    pengirim.nama_pegawai AS pengirim,
    penerima.nama_pegawai AS penerima
   FROM pesanbarang_t
     JOIN ruangan_m ON pesanbarang_t.ruangantujuan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN ruangan_m ruangpemesan ON pesanbarang_t.ruanganpemesan_id = ruangpemesan.ruangan_id
     JOIN instalasi_m instalasipesan ON ruangpemesan.instalasi_id = instalasipesan.instalasi_id
     LEFT JOIN mutasibarang_t ON pesanbarang_t.pesanbarang_id = mutasibarang_t.pesanbarang_id
     LEFT JOIN terimamutasibarang_t ON mutasibarang_t.mutasibarang_id = terimamutasibarang_t.mutasibarang_id
     LEFT JOIN pegawai_m pemesan ON pesanbarang_t.pegawaipemesan_id = pemesan.pegawai_id
     LEFT JOIN pegawai_m pengirim ON mutasibarang_t.pegawaimengetahui_id = pengirim.pegawai_id
     LEFT JOIN pegawai_m penerima ON terimamutasibarang_t.pegawaipenerima_id = penerima.pegawai_id
  WHERE pesanbarang_t.is_active = true AND pesanbarang_t.is_deleted = false ; ");
		 
  $this->execute('DROP VIEW if exists public.detailpemesananbarang_v;');
  
   $this->execute("
       CREATE VIEW \"public\".\"detailpemesananbarang_v\" AS   SELECT pesanbarang_t.pesanbarang_id,
    pesanbarang_t.tgl_pesanbarang,
    pesanbarang_t.ruangantujuan_id,
    ruangan_m.ruangan_nama AS ruangan_tujuan,
    instalasi_m.instalasi_id,
    instalasi_m.instalasi_nama AS instalasi_tujuan,
    pesanbarang_t.no_pemesanan,
    pesanbarang_t.keterangan_pesan,
    pesanbarang_t.ruanganpemesan_id,
    ruangpemesan.ruangan_id AS ruangan_pemesan_id,
    ruangpemesan.ruangan_nama AS ruangan_pemesan,
    instalasipesan.instalasi_id AS instalasi_pemesan_id,
    instalasipesan.instalasi_nama AS instalasi_pemesan,
    pesanbarangdetail_t.barang_id,
    barang_m.barang_namalainnya,
    barang_m.barang_nama,
    pesanbarangdetail_t.qty_pesan,
    COALESCE(qty_diterima.qty_diterima, 0::double precision) AS jumlah_diterima,
    pesanbarang_t.tgl_mintadikirim,
    mutasi.mutasibarangdetail_id,
    pesanbarangdetail_t.satuankecil_id,
    pesanbarangdetail_t.pesanbarangdetail_id,
    pesanbarangdetail_t.satuanbesar_id,
    satuan_besar.satuanunit_nama AS satuan_besar,
    satuan_kecil.satuanunit_nama AS satuan_kecil,
    pesanbarangdetail_t.jumlah_input AS qty_besar,
    barang_m.barang_harganetto AS harga_netto,
    barang_m.barang_max AS hargamaksimum,
    barang_m.barang_min AS hargaminimum,
    barang_m.barang_average AS hargaratarata,
    barang_m.barang_persendiskon,
    pesanbarangdetail_t.qty_pesan AS qty_kecil,
    pesanbarang_t.statuspesan,
    COALESCE(stok_pengirim.qty_tersedia::double precision, 0::double precision) AS stok_pengirim,
    COALESCE(stok_pemesan.qty_tersedia::double precision, 0::double precision) AS stok_pemesan,
    mutasi.jumlah_mutasi,
    mutasi.satuan_kirim,
    COALESCE(mutasi.jumlah_mutasi, 0::double precision) / (COALESCE(NULLIF(pesanbarangdetail_t.qty_pesan, 0::double precision), 1::double precision) / COALESCE(NULLIF(pesanbarangdetail_t.jumlah_input, 0::double precision), 1::double precision)) AS jumlah_input_mutasi,
    pesanbarangdetail_t.is_deleted AS pesan_is_deleted
   FROM pesanbarangdetail_t
     JOIN pesanbarang_t ON pesanbarangdetail_t.pesanbarang_id = pesanbarang_t.pesanbarang_id
     JOIN ruangan_m ON pesanbarang_t.ruangantujuan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN ruangan_m ruangpemesan ON pesanbarang_t.ruanganpemesan_id = ruangpemesan.ruangan_id
     JOIN instalasi_m instalasipesan ON ruangpemesan.instalasi_id = instalasipesan.instalasi_id
     JOIN barang_m ON pesanbarangdetail_t.barang_id = barang_m.barang_id
     LEFT JOIN satuanunit_m satuan_besar ON pesanbarangdetail_t.satuanbesar_id = satuan_besar.satuanunit_id
     LEFT JOIN satuanunit_m satuan_kecil ON pesanbarangdetail_t.satuankecil_id = satuan_kecil.satuanunit_id
     LEFT JOIN ( SELECT stokbarang_r.ruangan_id,
            stokbarang_r.barang_id,
            stokbarang_r.qty_tersedia
           FROM stokbarang_r
          WHERE stokbarang_r.is_deleted = false) stok_pengirim ON pesanbarang_t.ruangantujuan_id = stok_pengirim.ruangan_id AND pesanbarangdetail_t.barang_id = stok_pengirim.barang_id
     LEFT JOIN ( SELECT stokbarang_r.ruangan_id,
            stokbarang_r.barang_id,
            stokbarang_r.qty_tersedia
           FROM stokbarang_r
          WHERE stokbarang_r.is_deleted = false) stok_pemesan ON pesanbarang_t.ruanganpemesan_id = stok_pemesan.ruangan_id AND pesanbarangdetail_t.barang_id = stok_pemesan.ruangan_id AND pesanbarangdetail_t.barang_id = stok_pemesan.barang_id
     LEFT JOIN ( SELECT mutasibarangdetail_t.pesanbarangdetail_id,
            terimamutasibarangdetail_t.jmlterima AS qty_diterima
           FROM terimamutasibarangdetail_t
             JOIN mutasibarangdetail_t ON terimamutasibarangdetail_t.mutasibarangdetail_id = mutasibarangdetail_t.mutasibarangdetail_id
             JOIN mutasibarang_t ON mutasibarangdetail_t.mutasibarang_id = mutasibarang_t.mutasibarang_id
          WHERE mutasibarang_t.status_mutasi = 400) qty_diterima ON pesanbarangdetail_t.pesanbarangdetail_id = qty_diterima.pesanbarangdetail_id
     LEFT JOIN ( SELECT mutasibarangdetail_t.pesanbarangdetail_id,
            mutasibarangdetail_t.mutasibarangdetail_id,
            mutasibarangdetail_t.jumlah_input,
            mutasibarangdetail_t.qty_mutasi AS jumlah_mutasi,
            mutasibarangdetail_t.satuankecil_id,
            satuanunit_m.satuanunit_nama AS satuan_kirim
           FROM mutasibarangdetail_t
             LEFT JOIN satuanunit_m ON mutasibarangdetail_t.satuankecil_id = satuanunit_m.satuanunit_id) mutasi ON pesanbarangdetail_t.pesanbarangdetail_id = mutasi.pesanbarangdetail_id
  WHERE pesanbarangdetail_t.is_active = true ; ");
	 
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220203_093255_migrate_CDH25_schemaview cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220203_093255_migrate_CDH25_schemaview cannot be reverted.\n";

        return false;
    }
    */
}
