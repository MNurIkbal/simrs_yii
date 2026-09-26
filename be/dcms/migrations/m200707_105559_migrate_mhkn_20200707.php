<?php

use yii\db\Migration;

/**
 * Class m200707_105559_migrate_mhkn_20200707
 */
class m200707_105559_migrate_mhkn_20200707 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute("CREATE OR REPLACE FUNCTION \"public\".\"update_penerimaan_to_obat\"()
  RETURNS \"pg_catalog\".\"trigger\" AS \$BODY\$-- author yaya

DECLARE
    ruanganGudang INTEGER := 25;  
    validasiDetailId INTEGER;
    obatAlkesId  INTEGER;
    konversiId INTEGER;
    nilaiKonversi FLOAT;
    jumlahHarga FLOAT;
    qtyPO FLOAT;

    hargaSatuan FLOAT;

    qtyPenerimaan FLOAT;
    konversiPenerimaan FLOAT;

    penerimaanObatDetailId INTEGER;
    satuanKecilId INTEGER;
    vhargaRataRata FLOAT;
BEGIN
        

        IF (NEW.additional_data != 'is_verifikasi')
            THEN
                    RETURN NEW;
        END IF;

        NEW.additional_data := NULL;
        validasiDetailId := NEW.validasipoobatdetail_id;
        konversiId := NEW.s_konversiobt_id;
        obatAlkesId := NEW.obatalkes_id;
        penerimaanObatDetailId := NEW.penerimaanobatdetail_id;

        qtyPenerimaan := NEW.qty_diterima;
    -- Konver Ke satuan terkecil
        SELECT 
                nilai_konversi,
                satuankecil_id
        INTO
                nilaiKonversi,
                satuanKecilId
        FROM satuankonversi_m
        WHERE satuankonversi_id = konversiId;

        -- Prepare untuk mengurangi QTY PO di master obat alkes
        konversiPenerimaan := nilaiKonversi *  qtyPenerimaan;
      -- Mencari harga netto menggunakan attribute qty_po = qty yang sudah di konversi dan jumlah harga
        SELECT
            qty_po,
            jumlah + COALESCE(discount_rp,0)
        INTO
            qtyPO,
            jumlahHarga
        FROM validasipoobatdetail_t
        WHERE validasipoobatdetail_id = validasiDetailId;

        hargaSatuan := 0;
        IF (qtyPO > 0) THEN
            hargaSatuan := jumlahHarga / qtyPO;
        END IF;

        -- Update ke master obat alkes harga max,min, net dan avg sertan on_po
        
        UPDATE obatalkes_m SET
            on_po = (on_po - konversiPenerimaan),
            -- 2019-03-08 Perubahan atas best price dan update jika nilah 0 maka set harga satuan
--          harganetto = 
--              CASE WHEN harganetto = 0 THEN 0 ELSE hargaSatuan END, 
            hargamaksimum = (CASE 
                            WHEN hargamaksimum = 0 THEN hargaSatuan
                            ELSE
                                CASE WHEN hargaSatuan > hargamaksimum  
                                THEN
                                        hargaSatuan
                                ELSE
                                        hargamaksimum
                                END
                            END),
            hargaminimum = (
            CASE WHEN hargaminimum = 0 THEN hargaSatuan
            ELSE
                CASE 
                    WHEN hargaminimum < hargaSatuan  
                    THEN
                            hargaminimum
                    ELSE
                            hargaSatuan
                END
            END),
            hargaratarata = (CASE WHEN hargaratarata = 0 THEN hargaSatuan ELSE (hargaterakhir + hargaSatuan) / 2 END),
            hargaterakhir = hargaSatuan 
        WHERE obatalkes_id = obatAlkesId;
        
        -- Update validasi detail untuk penerimaan 
        UPDATE validasipoobatdetail_t SET 
        qty_penerimaan = COALESCE(qty_penerimaan,0) + qtyPenerimaan,
        is_completed = (CASE WHEN (COALESCE(qty_penerimaan,0) + qtyPenerimaan) = qty_input 
                                THEN 
                                        true 
                                ELSE 
                                        false 
        END),
        qty_sisa = qty_input - (COALESCE(qty_penerimaan,0) + qtyPenerimaan)
        WHERE validasipoobatdetail_id = validasiDetailId;
        
        SELECT hargaratarata
        INTO vhargaRataRata
        FROM obatalkes_m 
        WHERE obatalkes_id = obatAlkesId;
        
        -- Insert ke stokobatalkes_t 
             INSERT INTO stokobatalkes_t (
                        ruangan_id,
                        penerimaanobatdetail_id,
                        obatalkes_id,
                        tglkadaluarsa,
                        nobatch,
                        tglstok_in,
                        qtystok_in,
                        harganetto,
                        stokoa_aktif,
                        satuankecil_id,
                        tglterima,
                        total_persediaan,
                        harga_netto_avg
                ) VALUES (
                        ruanganGudang,
                        NEW.penerimaanobatdetail_id,
                        obatAlkesId,
                        NEW.tgl_kadaluarsa,
                        NEW.no_batch,
                        NEW.created_date,
                        konversiPenerimaan,
                        hargaSatuan,
                        true,
                        satuanKecilId,
                        NEW.created_date,
                        konversiPenerimaan * hargaSatuan,
                        COALESCE(vhargaRataRata,0)
                );      

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;");

         $this->execute('ALTER FUNCTION "public"."update_penerimaan_to_obat"() OWNER TO "postgres";');

         $this->execute('DROP VIEW IF EXISTS worklistresep_v;');

         $this->execute("         
CREATE VIEW \"public\".\"worklistresep_v\" AS  SELECT penjualanresep_t.penjualanresep_id,
    reseptur_t.reseptur_id,
    reseptur_t.noresep AS no_reseptur,
    penjualanresep_t.noresep AS no_resep,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik AS no_rm,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    pegawai_m.nama_pegawai AS dokter,
    array_agg(anamnesa_t.riwayat_alergiobat) AS alergi,
    reseptur_t.status_worklist AS status_worklist_id,
    fgetnamalookup((reseptur_t.status_worklist)::integer) AS status_worklist,
    ruangan_asal.instalasi_id,
    NULL::text AS jenispenjualan_id,
    NULL::text AS jenis_penjualan,
    penjualanresep_t.tglpenjualan AS tanggal,
    penjualanresep_t.status_bayar AS status_bayar_id,
    fgetnamalookup((penjualanresep_t.status_bayar)::integer) AS status_bayar,
    penjualanresep_t.status_reseptur AS status_reseptur_id,
    fgetnamalookup((penjualanresep_t.status_reseptur)::integer) AS status_reseptur,
    periksa_fisik_rj.tinggi AS tinggi_badan,
    periksa_fisik_rj.berat AS berat_badan,
    reseptur_t.additional_data AS add_reseptur,
    penjualanresep_t.additional_data AS add_penjualaanresep
   FROM (((((((reseptur_t
     JOIN pendaftaran_t ON ((reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN penjualanresep_t ON ((reseptur_t.reseptur_id = penjualanresep_t.reseptur_id)))
     LEFT JOIN pegawai_m ON ((penjualanresep_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ruangan_asal ON ((reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id)))
     LEFT JOIN anamnesa_t ON ((pendaftaran_t.pendaftaran_id = anamnesa_t.pendaftaran_id)))
     LEFT JOIN ( SELECT pemeriksaanfisik_t.pendaftaran_id,
            pemeriksaanfisik_t.tinggibadan_cm AS tinggi,
            pemeriksaanfisik_t.beratbadan_kg AS berat
           FROM pemeriksaanfisik_t
          WHERE (pemeriksaanfisik_t.is_deleted = false)) periksa_fisik_rj ON ((pendaftaran_t.pendaftaran_id = periksa_fisik_rj.pendaftaran_id)))
  WHERE (ruangan_asal.instalasi_id = 1)
  GROUP BY reseptur_t.noresep, penjualanresep_t.noresep, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pasien_m.tanggal_lahir, pegawai_m.nama_pegawai, reseptur_t.status_worklist, ruangan_asal.instalasi_id, penjualanresep_t.tglpenjualan, penjualanresep_t.status_bayar, penjualanresep_t.penjualanresep_id, reseptur_t.reseptur_id, penjualanresep_t.status_reseptur, periksa_fisik_rj.tinggi, periksa_fisik_rj.berat
UNION ALL
 SELECT NULL::integer AS penjualanresep_id,
    reseptur_t.reseptur_id,
    reseptur_t.noresep AS no_reseptur,
    NULL::character varying AS no_resep,
    pendaftaran_t.no_pendaftaran,
    pasien_m.no_rekam_medik AS no_rm,
    pasien_m.nama_pasien,
    pasien_m.tanggal_lahir,
    pegawai_m.nama_pegawai AS dokter,
    NULL::text[] AS alergi,
    reseptur_t.status_worklist AS status_worklist_id,
    fgetnamalookup((reseptur_t.status_worklist)::integer) AS status_worklist,
    ruangan_asal.instalasi_id,
    NULL::text AS jenispenjualan_id,
    NULL::text AS jenis_penjualan,
    reseptur_t.tglreseptur AS tanggal,
    NULL::smallint AS status_bayar_id,
    NULL::character varying AS status_bayar,
    reseptur_t.status_reseptur AS status_reseptur_id,
    fgetnamalookup(reseptur_t.status_reseptur) AS status_reseptur,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN periksa_fisik_rd.tinggi
            ELSE periksa_fisik_ri.tinggi
        END AS tinggi_badan,
        CASE
            WHEN (pendaftaran_t.pasienadmisi_id IS NULL) THEN periksa_fisik_rd.berat
            ELSE periksa_fisik_ri.berat
        END AS berat_badan,
    reseptur_t.additional_data AS add_reseptur,
    NULL::text AS add_penjualaanresep
   FROM ((((((reseptur_t
     JOIN pendaftaran_t ON ((reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m ON ((reseptur_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ruangan_asal ON ((reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id)))
     LEFT JOIN ( SELECT asesmenperawatrd_t.pendaftaran_id,
            asesmenperawatrd_t.tinggi_badan AS tinggi,
            asesmenperawatrd_t.berat_badan AS berat
           FROM asesmenperawatrd_t
          WHERE (asesmenperawatrd_t.is_deleted = false)) periksa_fisik_rd ON ((pendaftaran_t.pendaftaran_id = periksa_fisik_rd.pendaftaran_id)))
     LEFT JOIN ( SELECT asesmenmedis_t.pendaftaran_id,
            asesmenmedis_t.tinggi_badan AS tinggi,
            asesmenmedis_t.berat_badan AS berat
           FROM asesmenmedis_t
          WHERE (asesmenmedis_t.is_deleted = false)) periksa_fisik_ri ON ((pendaftaran_t.pendaftaran_id = periksa_fisik_ri.pendaftaran_id)))
  WHERE (ruangan_asal.instalasi_id <> 1)
UNION ALL
 SELECT penjualanresep_t.penjualanresep_id,
    NULL::integer AS reseptur_id,
    NULL::character varying AS no_reseptur,
    penjualanresep_t.noresep AS no_resep,
    pendaftaran_t.no_pendaftaran,
    NULL::character varying AS no_rm,
        CASE
            WHEN ((penjualanresep_t.jenispenjualan)::text = '343'::text) THEN penjualanresep_t.nama_pembeli
            WHEN ((penjualanresep_t.jenispenjualan)::text = '344'::text) THEN pasien_m.nama_pasien
            WHEN ((penjualanresep_t.jenispenjualan)::text = '345'::text) THEN karyawan.nama_pegawai
            ELSE NULL::character varying
        END AS nama_pasien,
        CASE
            WHEN (penjualanresep_t.pasien_id IS NOT NULL) THEN pasien_m.tanggal_lahir
            ELSE NULL::date
        END AS tanggal_lahir,
    pegawai_m.nama_pegawai AS dokter,
    NULL::text[] AS alergi,
    penjualanresep_t.status_worklist AS status_worklist_id,
    fgetnamalookup((penjualanresep_t.status_worklist)::integer) AS status_worklist,
    pendaftaran_t.instalasi_id,
    penjualanresep_t.jenispenjualan AS jenispenjualan_id,
    fgetnamalookup((penjualanresep_t.jenispenjualan)::integer) AS jenis_penjualan,
    penjualanresep_t.tglpenjualan AS tanggal,
    penjualanresep_t.status_bayar AS status_bayar_id,
    fgetnamalookup((penjualanresep_t.status_bayar)::integer) AS status_bayar,
    penjualanresep_t.status_reseptur AS status_reseptur_id,
    fgetnamalookup((penjualanresep_t.status_reseptur)::integer) AS status_reseptur,
    NULL::integer AS tinggi_badan,
    NULL::integer AS berat_badan,
    NULL::text AS add_reseptur,
    penjualanresep_t.additional_data AS add_penjualaanresep
   FROM ((((penjualanresep_t
     LEFT JOIN pendaftaran_t ON ((penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m ON ((penjualanresep_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN pegawai_m karyawan ON ((penjualanresep_t.karyawan_id = karyawan.pegawai_id)))
  WHERE (penjualanresep_t.reseptur_id IS NULL);
");
         $this->execute('ALTER TABLE "public"."worklistresep_v" OWNER TO "postgres";');

         $this->execute('DROP VIEW if exists "public"."worklistresepdetail_v";');

         $this->execute("            
CREATE VIEW \"public\".\"worklistresepdetail_v\" AS  SELECT reseptur_t.noresep AS no_reseptur,
    penjualanresep_t.noresep AS no_resep,
    racikan_m.racikan_nama AS racikan,
    obatalkespasien_t.rke,
    obatalkes_m.obatalkes_nama AS nama_obat,
    signaobat_m.signa_nama AS signa,
    obatalkespasien_t.qty_oa AS qty_obat,
    obatalkespasien_t.qty_konversi,
    ((obatalkespasien_t.additional_data)::json ->> 'satuan_input'::text) AS satuan_input,
    ((obatalkespasien_t.additional_data)::json ->> 'satuan_konversi'::text) AS satuan_konversi,
    obatalkespasien_t.etiket,
    obatalkes_m.is_oral,
    NULL::date AS tglkadaluarsa,
        CASE
            WHEN (obatalkespasien_t.det IS NULL) THEN obatalkespasien_t.qty_oa
            ELSE obatalkespasien_t.det
        END AS det,
    obatalkespasien_t.is_deleted AS detail_is_deleted,
    obatalkespasien_t.racikan_id
   FROM ((((((reseptur_t
     JOIN penjualanresep_t ON ((reseptur_t.reseptur_id = penjualanresep_t.reseptur_id)))
     JOIN obatalkespasien_t ON ((penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id)))
     JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN racikan_m ON ((obatalkespasien_t.racikan_id = racikan_m.racikan_id)))
     JOIN ruangan_m ruangan_asal ON ((reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id)))
     LEFT JOIN signaobat_m ON (((obatalkespasien_t.signa_oa)::integer = signaobat_m.signa_id)))
  WHERE (ruangan_asal.instalasi_id = 1)
UNION ALL
 SELECT reseptur_t.noresep AS no_reseptur,
    NULL::text AS no_resep,
    racikan_m.racikan_nama AS racikan,
    resepturdetail_t.rke,
    obatalkes_m.obatalkes_nama AS nama_obat,
    signaobat_m.signa_nama AS signa,
    resepturdetail_t.qty_reseptur AS qty_obat,
    resepturdetail_t.qty_konversi,
    ((resepturdetail_t.additional_data)::json ->> 'satuan_input'::text) AS satuan_input,
    ((resepturdetail_t.additional_data)::json ->> 'satuan_konversi'::text) AS satuan_konversi,
    resepturdetail_t.etiket,
    obatalkes_m.is_oral,
    NULL::date AS tglkadaluarsa,
        CASE
            WHEN (resepturdetail_t.det IS NULL) THEN resepturdetail_t.qty_reseptur
            ELSE resepturdetail_t.det
        END AS det,
    resepturdetail_t.is_deleted AS detail_is_deleted,
    resepturdetail_t.racikan_id
   FROM (((((reseptur_t
     JOIN resepturdetail_t ON ((reseptur_t.reseptur_id = resepturdetail_t.reseptur_id)))
     JOIN obatalkes_m ON ((resepturdetail_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN ruangan_m ruangan_asal ON ((reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id)))
     LEFT JOIN racikan_m ON ((resepturdetail_t.racikan_id = racikan_m.racikan_id)))
     LEFT JOIN signaobat_m ON ((resepturdetail_t.signa_id = signaobat_m.signa_id)))
  WHERE (ruangan_asal.instalasi_id <> 1)
UNION ALL
 SELECT NULL::character varying AS no_reseptur,
    penjualanresep_t.noresep AS no_resep,
    racikan_m.racikan_nama AS racikan,
    obatalkespasien_t.rke,
    obatalkes_m.obatalkes_nama AS nama_obat,
    signaobat_m.signa_nama AS signa,
    (((obatalkespasien_t.additional_data)::json ->> 'qty_input'::text))::double precision AS qty_obat,
    obatalkespasien_t.qty_konversi,
    ((obatalkespasien_t.additional_data)::json ->> 'satuan_input'::text) AS satuan_input,
    ((obatalkespasien_t.additional_data)::json ->> 'satuan_konversi'::text) AS satuan_konversi,
    obatalkespasien_t.etiket,
    obatalkes_m.is_oral,
    stokobatalkes_t.tglkadaluarsa,
        CASE
            WHEN (obatalkespasien_t.det IS NULL) THEN obatalkespasien_t.qty_oa
            ELSE obatalkespasien_t.det
        END AS det,
    obatalkespasien_t.is_deleted AS detail_is_deleted,
    obatalkespasien_t.racikan_id
   FROM (((((penjualanresep_t
     JOIN obatalkespasien_t ON ((penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id)))
     JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN racikan_m ON ((obatalkespasien_t.racikan_id = racikan_m.racikan_id)))
     LEFT JOIN signaobat_m ON (((obatalkespasien_t.signa_oa)::integer = signaobat_m.signa_id)))
     LEFT JOIN stokobatalkes_t ON (((obatalkespasien_t.obatalkespasien_id = stokobatalkes_t.obatalkespasien_id) AND (obatalkespasien_t.obatalkes_id = stokobatalkes_t.obatalkes_id))))
  WHERE (penjualanresep_t.reseptur_id IS NULL);");

         $this->execute('ALTER TABLE "public"."worklistresepdetail_v" OWNER TO "postgres";');

         $this->execute('DROP VIEW if exists "public"."infoclosingkasir_v";');

         $this->execute("
            CREATE VIEW \"public\".\"infoclosingkasir_v\" AS  SELECT 'PEMBAYARAN'::text AS tipe,
    closingkasir_t.closingkasir_id,
    closingkasir_t.shift_id,
    shift_m.shift_nama,
    closingkasir_t.pegawai_id,
    pegawai_m.nama_pegawai,
    tandabuktibayar_t.tglbuktibayar AS tgl_closingkasir,
    closingkasir_t.no_closingkasir, 
    closingkasir_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    closingkasir_t.nilai_closingtransaksi,
    tandabuktibayar_t.uangditerima AS total_setoran,
    NULL::integer AS setorbank_id,
    NULL::character varying AS no_struksetor,
    NULL::date AS tgl_disetor,
    NULL::character varying AS nama_bank,
    NULL::character varying AS no_rekening,
    NULL::double precision AS jumlah_setoran,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.pasien_id,
    pasien_m.nama_pasien,
    tandabuktibayar_t.uangditerima AS total_terbayar,
    closingkasir_t.tgl_closingkasir AS tgl_pembayaran,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    ((pembayaran_t.total_tagihan + pembayaran_t.total_administrasi) - (pembayaran_t.total_discount + pembayaran_t.total_discountpembayaran)) AS total_tagihan,
    (pembayaran_t.total_tunai - pembayaran_t.total_kembalian) AS total_tunai,
    pembayaran_t.total_nontunai,
    (COALESCE(pembayaran_t.total_dijamin, (0)::double precision) + COALESCE(pemberianpiutang_t.total_piutang, (0)::double precision)) AS total_dijamin
   FROM ((((((((((((closingkasir_t
     JOIN tandabuktibayar_t ON ((closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id)))
     JOIN pembayaranpelayanan_t ON ((tandabuktibayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
     JOIN pembayaran_t ON (((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id) AND (pembayaran_t.is_deleted = false))))
     JOIN pendaftaran_t ON ((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m ON ((closingkasir_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ON ((closingkasir_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pemberianpiutang_t ON ((pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id)))
     LEFT JOIN shift_m ON ((closingkasir_t.shift_id = shift_m.shift_id)))
UNION ALL
 SELECT 'UANG_MASUK'::text AS tipe,
    closingkasir_t.closingkasir_id,
    closingkasir_t.shift_id,
    shift_m.shift_nama,
    closingkasir_t.pegawai_id,
    pegawai_m.nama_pegawai,
    tandabuktibayar_t.tglbuktibayar AS tgl_closingkasir,
    closingkasir_t.no_closingkasir,
    closingkasir_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    closingkasir_t.nilai_closingtransaksi,
    tandabuktibayar_t.uangditerima AS total_setoran,
    NULL::integer AS setorbank_id,
    NULL::character varying AS no_struksetor,
    NULL::date AS tgl_disetor,
    NULL::character varying AS nama_bank,
    NULL::character varying AS no_rekening,
    NULL::double precision AS jumlah_setoran,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.pasien_id,
    pasien_m.nama_pasien,
    bayaruangmuka_t.jumlah_uangmuka AS total_terbayar,
    closingkasir_t.tgl_closingkasir AS tgl_pembayaran,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    0 AS total_tagihan,
        CASE
            WHEN (bayaruangmuka_t.metode_pembayaran = 27) THEN tandabuktibayar_t.uangditerima
            ELSE (0)::double precision
        END AS total_tunai,
        CASE
            WHEN (bayaruangmuka_t.metode_pembayaran = 28) THEN tandabuktibayar_t.uangditerima
            ELSE (0)::double precision
        END AS total_nontunai,
    0 AS total_dijamin
   FROM ((((((((((closingkasir_t
     JOIN tandabuktibayar_t ON ((closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id)))
     JOIN bayaruangmuka_t ON ((tandabuktibayar_t.bayaruangmuka_id = bayaruangmuka_t.bayaruangmuka_id)))
     JOIN pendaftaran_t ON ((bayaruangmuka_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m ON ((closingkasir_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ON ((closingkasir_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN shift_m ON ((closingkasir_t.shift_id = shift_m.shift_id)))
UNION ALL
 SELECT 'RETUR'::text AS tipe,
    closingkasir_t.closingkasir_id,
    closingkasir_t.shift_id,
    shift_m.shift_nama,
    closingkasir_t.pegawai_id,
    pegawai_m.nama_pegawai,
    closingkasir_t.tgl_closingkasir,
    closingkasir_t.no_closingkasir,
    closingkasir_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    closingkasir_t.nilai_closingtransaksi,
    tandabuktikeluar_t.jml_pembayaran AS total_setoran,
    NULL::integer AS setorbank_id,
    NULL::character varying AS no_struksetor,
    NULL::date AS tgl_disetor,
    NULL::character varying AS nama_bank,
    NULL::character varying AS no_rekening,
    NULL::double precision AS jumlah_setoran,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    pasien_m.pasien_id,
    pasien_m.nama_pasien,
    tandabuktikeluar_t.uang_diterima AS total_terbayar,
    closingkasir_t.tgl_closingkasir AS tgl_pembayaran,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    0 AS total_tagihan,
    (- returbayarpelayanan_t.total_biayaretur) AS total_tunai,
    (- returbayarpelayanan_t.total_nontunai) AS total_nontunai,
    0 AS total_dijamin
   FROM (((((((((((((closingkasir_t
     JOIN tandabuktikeluar_t ON ((closingkasir_t.closingkasir_id = tandabuktikeluar_t.closingkasir_id)))
     JOIN returbayarpelayanan_t ON ((tandabuktikeluar_t.returbayarpelayanan_id = returbayarpelayanan_t.returbayarpelayanan_id)))
     JOIN tandabuktibayar_t ON ((returbayarpelayanan_t.tandabuktibayar_id = tandabuktibayar_t.tandabuktibayar_id)))
     JOIN pembayaranpelayanan_t ON ((tandabuktibayar_t.tandabuktibayar_id = pembayaranpelayanan_t.tandabuktibayar_id)))
     JOIN pembayaran_t ON (((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id) AND (pembayaran_t.is_deleted = false))))
     JOIN pendaftaran_t ON ((pembayaranpelayanan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m ON ((closingkasir_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ON ((closingkasir_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN shift_m ON ((closingkasir_t.shift_id = shift_m.shift_id)))
UNION ALL
 SELECT 'PEMBAYARAN_PIUTANG'::text AS tipe,
    closingkasir_t.closingkasir_id,
    closingkasir_t.shift_id,
    shift_m.shift_nama,
    closingkasir_t.pegawai_id,
    pegawai_m.nama_pegawai,
    closingkasir_t.tgl_closingkasir,
    closingkasir_t.no_closingkasir,
    closingkasir_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    closingkasir_t.nilai_closingtransaksi,
    tandabuktibayar_t.jmlpembayaran AS total_setoran,
    NULL::integer AS setorbank_id,
    NULL::character varying AS no_struksetor,
    NULL::date AS tgl_disetor,
    NULL::character varying AS nama_bank,
    NULL::character varying AS no_rekening,
    NULL::double precision AS jumlah_setoran,
        CASE
            WHEN (pemberianpiutang_t.pendaftaran_id IS NULL) THEN pemberianpiutang_t.penjualanresep_id
            ELSE pemberianpiutang_t.pendaftaran_id
        END AS pendaftaran_id,
    pemberianpiutang_t.no_pemberianpiutang AS no_pendaftaran,
    pasien_m.pasien_id,
        CASE
            WHEN (pemberianpiutang_t.pendaftaran_id IS NULL) THEN penjualanresep_t.nama_pembeli
            ELSE pasien_m.nama_pasien
        END AS nama_pasien,
    tandabuktibayar_t.uangditerima AS total_terbayar,
    closingkasir_t.tgl_closingkasir AS tgl_pembayaran,
        CASE
            WHEN (pemberianpiutang_t.pendaftaran_id IS NULL) THEN carabayar_resep.carabayar_nama
            ELSE carabayar_m.carabayar_nama
        END AS carabayar_nama,
        CASE
            WHEN (pemberianpiutang_t.pendaftaran_id IS NULL) THEN penjamin_resep.penjamin_nama
            ELSE penjamin_m.penjamin_nama
        END AS penjamin_nama,
    0 AS total_tagihan,
        CASE
            WHEN (pembayaranpiutang_t.metode_pembayaran = 27) THEN pembayaranpiutang_t.total_bayarpiutang
            ELSE (0)::double precision
        END AS total_tunai,
        CASE
            WHEN (pembayaranpiutang_t.metode_pembayaran = 28) THEN pembayaranpiutang_t.total_bayarpiutang
            ELSE (0)::double precision
        END AS total_nontunai,
    0 AS total_dijamin
   FROM ((((((((((((((closingkasir_t
     JOIN tandabuktibayar_t ON ((closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id)))
     JOIN pembayaranpiutang_t ON ((tandabuktibayar_t.pembayaranpiutang_id = pembayaranpiutang_t.pembayaranpiutang_id)))
     JOIN pemberianpiutang_t ON ((pembayaranpiutang_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id)))
     LEFT JOIN pendaftaran_t ON ((pemberianpiutang_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN penjualanresep_t ON ((pemberianpiutang_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
     LEFT JOIN carabayar_m ON ((pendaftaran_t.carabayar_id = carabayar_m.carabayar_id)))
     LEFT JOIN carabayar_m carabayar_resep ON ((penjualanresep_t.carabayar_id = carabayar_resep.carabayar_id)))
     LEFT JOIN penjamin_m ON ((pendaftaran_t.penjamin_id = penjamin_m.penjamin_id)))
     LEFT JOIN penjamin_m penjamin_resep ON ((penjualanresep_t.penjamin_id = penjamin_resep.penjamin_id)))
     LEFT JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN pegawai_m ON ((closingkasir_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ON ((closingkasir_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN shift_m ON ((closingkasir_t.shift_id = shift_m.shift_id)))
UNION ALL
 SELECT 'PEMBAYARAN_RESEP_BEBAS'::text AS tipe,
    closingkasir_t.closingkasir_id,
    closingkasir_t.shift_id,
    shift_m.shift_nama,
    closingkasir_t.pegawai_id,
    pegawai_m.nama_pegawai,
    closingkasir_t.tgl_closingkasir,
    closingkasir_t.no_closingkasir,
    closingkasir_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    closingkasir_t.nilai_closingtransaksi,
    tandabuktibayar_t.jmlpembayaran AS total_setoran,
    NULL::integer AS setorbank_id,
    NULL::character varying AS no_struksetor,
    NULL::date AS tgl_disetor,
    NULL::character varying AS nama_bank,
    NULL::character varying AS no_rekening,
    NULL::double precision AS jumlah_setoran,
    penjualanresep_t.penjualanresep_id AS pendaftaran_id,
    penjualanresep_t.noresep AS no_pendaftaran,
    NULL::integer AS pasien_id,
    penjualanresep_t.nama_pembeli AS nama_pasien,
    tandabuktibayar_t.uangditerima AS total_terbayar,
    closingkasir_t.tgl_closingkasir AS tgl_pembayaran,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_nama,
    ((pembayaran_t.total_tagihan + pembayaran_t.total_administrasi) - (pembayaran_t.total_discount + pembayaran_t.total_discountpembayaran)) AS total_tagihan,
    (pembayaran_t.total_tunai - pembayaran_t.total_kembalian) AS total_tunai,
    pembayaran_t.total_nontunai,
    (COALESCE(pembayaran_t.total_dijamin, (0)::double precision) + COALESCE(pemberianpiutang_t.total_piutang, (0)::double precision)) AS total_dijamin
   FROM (((((((((((closingkasir_t
     JOIN tandabuktibayar_t ON ((closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id)))
     JOIN pembayaranpelayanan_t ON ((tandabuktibayar_t.pembayaranpelayanan_id = pembayaranpelayanan_t.pembayaranpelayanan_id)))
     JOIN pembayaran_t ON ((pembayaranpelayanan_t.pembayaran_id = pembayaran_t.pembayaran_id)))
     JOIN penjualanresep_t ON ((pembayaranpelayanan_t.penjualanresep_id = penjualanresep_t.penjualanresep_id)))
     JOIN penjamin_m ON ((penjualanresep_t.penjamin_id = penjamin_m.penjamin_id)))
     JOIN carabayar_m ON ((penjamin_m.carabayar_id = carabayar_m.carabayar_id)))
     JOIN pegawai_m ON ((closingkasir_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ON ((closingkasir_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pemberianpiutang_t ON ((pembayaran_t.pemberianpiutang_id = pemberianpiutang_t.pemberianpiutang_id)))
     LEFT JOIN shift_m ON ((closingkasir_t.shift_id = shift_m.shift_id)))
UNION ALL
 SELECT 'penerimaan'::text AS tipe,
    closingkasir_t.closingkasir_id,
    closingkasir_t.shift_id,
    shift_m.shift_nama,
    closingkasir_t.pegawai_id,
    pegawai_m.nama_pegawai,
    closingkasir_t.tgl_closingkasir,
    closingkasir_t.no_closingkasir,
    tandabuktibayar_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    closingkasir_t.nilai_closingtransaksi,
    tandabuktibayar_t.jmlpembayaran AS total_setoran,
    setorbank_t.setorbank_id,
    setorbank_t.no_struksetor,
    setorbank_t.tgl_disetor,
    setorbank_t.nama_bank,
    setorbank_t.no_rekening,
    setorbank_t.jumlah_setoran,
    penerimaan.pembayarantransaksi_id AS pendaftaran_id,
        CASE
            WHEN (penerimaan.tipe_transaksi = 700) THEN supplier_m.supplier_kode
            WHEN (penerimaan.tipe_transaksi = 701) THEN peg_penerimaan.nomorindukpegawai
            WHEN (penerimaan.tipe_transaksi = 702) THEN pasien_m.no_rekam_medik
            ELSE NULL::character varying
        END AS no_pendaftaran,
        CASE
            WHEN (penerimaan.tipe_transaksi = 700) THEN penerimaan.supplier_id
            WHEN (penerimaan.tipe_transaksi = 701) THEN penerimaan.pegawai_id
            WHEN (penerimaan.tipe_transaksi = 702) THEN penerimaan.pasien_id
            ELSE NULL::integer
        END AS pasien_id,
        CASE
            WHEN (penerimaan.tipe_transaksi = 700) THEN supplier_m.supplier_nama
            WHEN (penerimaan.tipe_transaksi = 701) THEN peg_penerimaan.nama_pegawai
            WHEN (penerimaan.tipe_transaksi = 702) THEN pasien_m.nama_pasien
            ELSE NULL::character varying
        END AS nama_pasien,
    tandabuktibayar_t.uangditerima AS total_terbayar,
    closingkasir_t.tgl_closingkasir AS tgl_pembayaran,
    NULL::character varying AS carabayar_nama,
    NULL::character varying AS penjamin_nama,
    0 AS total_tagihan,
        CASE
            WHEN (penerimaan.metode_pembayaran = 27) THEN penerimaan.jumlah
            ELSE (0)::double precision
        END AS total_tunai,
        CASE
            WHEN (penerimaan.metode_pembayaran = 28) THEN penerimaan.jumlah
            ELSE (0)::double precision
        END AS total_nontunai,
    0 AS total_dijamin
   FROM ((((((((((closingkasir_t
     JOIN tandabuktibayar_t ON ((closingkasir_t.closingkasir_id = tandabuktibayar_t.closingkasir_id)))
     JOIN pembayarantransaksi_t penerimaan ON ((tandabuktibayar_t.penerimaanumum_id = penerimaan.pembayarantransaksi_id)))
     JOIN pegawai_m ON ((closingkasir_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ON ((tandabuktibayar_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pegawai_m peg_penerimaan ON ((penerimaan.pegawai_id = peg_penerimaan.pegawai_id)))
     LEFT JOIN pasien_m ON ((penerimaan.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN supplier_m ON ((penerimaan.supplier_id = supplier_m.supplier_id)))
     LEFT JOIN shift_m ON ((closingkasir_t.shift_id = shift_m.shift_id)))
     LEFT JOIN setorbank_t ON ((closingkasir_t.setorbank_id = setorbank_t.setorbank_id)))
UNION ALL
 SELECT 'pengeluaran'::text AS tipe,
    closingkasir_t.closingkasir_id,
    closingkasir_t.shift_id,
    shift_m.shift_nama,
    closingkasir_t.pegawai_id,
    pegawai_m.nama_pegawai,
    closingkasir_t.tgl_closingkasir,
    closingkasir_t.no_closingkasir,
    tandabuktikeluar_t.ruangan_id,
    ruangan_m.ruangan_nama,
    ruangan_m.instalasi_id,
    instalasi_m.instalasi_nama,
    closingkasir_t.nilai_closingtransaksi,
    tandabuktikeluar_t.jml_pembayaran AS total_setoran,
    setorbank_t.setorbank_id,
    setorbank_t.no_struksetor,
    setorbank_t.tgl_disetor,
    setorbank_t.nama_bank,
    setorbank_t.no_rekening,
    setorbank_t.jumlah_setoran,
    pengeluaran.pembayarantransaksi_id AS pendaftaran_id,
        CASE
            WHEN (pengeluaran.tipe_transaksi = 700) THEN supplier_m.supplier_kode
            WHEN (pengeluaran.tipe_transaksi = 701) THEN peg_pengeluaran.nomorindukpegawai
            WHEN (pengeluaran.tipe_transaksi = 702) THEN pasien_m.no_rekam_medik
            ELSE NULL::character varying
        END AS no_pendaftaran,
        CASE
            WHEN (pengeluaran.tipe_transaksi = 700) THEN pengeluaran.supplier_id
            WHEN (pengeluaran.tipe_transaksi = 701) THEN pengeluaran.pegawai_id
            WHEN (pengeluaran.tipe_transaksi = 702) THEN pengeluaran.pasien_id
            ELSE NULL::integer
        END AS pasien_id,
        CASE
            WHEN (pengeluaran.tipe_transaksi = 700) THEN supplier_m.supplier_nama
            WHEN (pengeluaran.tipe_transaksi = 701) THEN peg_pengeluaran.nama_pegawai
            WHEN (pengeluaran.tipe_transaksi = 702) THEN pasien_m.nama_pasien
            ELSE NULL::character varying
        END AS nama_pasien,
    tandabuktikeluar_t.uang_diterima AS total_terbayar,
    closingkasir_t.tgl_closingkasir AS tgl_pembayaran,
    NULL::character varying AS carabayar_nama,
    NULL::character varying AS penjamin_nama,
    0 AS total_tagihan,
        CASE
            WHEN (pengeluaran.metode_pembayaran = 27) THEN (- pengeluaran.jumlah)
            ELSE (0)::double precision
        END AS total_tunai,
        CASE
            WHEN (pengeluaran.metode_pembayaran = 28) THEN (- pengeluaran.jumlah)
            ELSE (0)::double precision
        END AS total_nontunai,
    0 AS total_dijamin
   FROM ((((((((((closingkasir_t
     JOIN tandabuktikeluar_t ON ((closingkasir_t.closingkasir_id = tandabuktikeluar_t.closingkasir_id)))
     JOIN pembayarantransaksi_t pengeluaran ON ((tandabuktikeluar_t.pembayarantransaksi_id = pengeluaran.pembayarantransaksi_id)))
     JOIN pegawai_m ON ((closingkasir_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ON ((tandabuktikeluar_t.ruangan_id = ruangan_m.ruangan_id)))
     JOIN instalasi_m ON ((ruangan_m.instalasi_id = instalasi_m.instalasi_id)))
     LEFT JOIN pegawai_m peg_pengeluaran ON ((pengeluaran.pegawai_id = peg_pengeluaran.pegawai_id)))
     LEFT JOIN pasien_m ON ((pengeluaran.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN supplier_m ON ((pengeluaran.supplier_id = supplier_m.supplier_id)))
     LEFT JOIN shift_m ON ((closingkasir_t.shift_id = shift_m.shift_id)))
     LEFT JOIN setorbank_t ON ((closingkasir_t.setorbank_id = setorbank_t.setorbank_id)));");

         $this->execute('ALTER TABLE "public"."infoclosingkasir_v" OWNER TO "postgres";');

    

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200707_105559_migrate_mhkn_20200707 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200707_105559_migrate_mhkn_20200707 cannot be reverted.\n";

        return false;
    }
    */
}
