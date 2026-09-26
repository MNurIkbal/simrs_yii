<?php

use yii\db\Migration;

/**
 * Class m200323_104930_migrate_20200323
 */
class m200323_104930_migrate_20200323 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    /*tandabuktibayar_t*/    
        $this->execute('COMMENT ON COLUMN "public"."tandabuktibayar_t"."penerimaanumum_id" IS \'jenis transaksi = pemasukan\';');
    
    /*tandabuktikeluar_t*/
        $this->execute('ALTER TABLE "public"."tandabuktikeluar_t" ADD COLUMN "pembayarantransaksi_id" int4;');

     /*fpemberianpiutang_v_t*/
        $this->execute('DROP VIEW if exists public.fpemberianpiutang_v;');
        $this->execute("
            CREATE OR REPLACE VIEW public.fpemberianpiutang_v
 AS
 SELECT 'tagihan_rs'::text AS jenis,
    pendaftaran_t.no_pendaftaran,
    NULL::character varying AS no_resep,
    pasien_m.no_rekam_medik,
    pendaftaran_t.pendaftaran_id,
    NULL::integer AS penjualanresep_id,
    pasien_m.nama_pasien,
    COALESCE(total_tagihan.total_tagihan, 0::double precision) + COALESCE(reseptur.total_adm, 0::double precision) AS total_tagihan,
    COALESCE(pemberianpiutang_t.total_bayarpiutang, 0::double precision) AS piutang_sudahbayar,
    pasien_m.tanggal_lahir,
    pendaftaran_t.umur,
    pendaftaran_t.tgl_pendaftaran,
    COALESCE(pemberianpiutang_t.total_piutang, 0::double precision) AS total_piutang,
    konfigsystem_k.adm_persen,
    adm.tarif_max,
    COALESCE(tagihan_ranap.total_tagihan, 0::double precision) AS tagihan_ranap,
    bayaruangmuka_t.jumlah_uangmuka AS uang_muka,
    konfigsystem_k.is_pembulatankeatas,
    konfigsystem_k.satuanpembulatan
   FROM pendaftaran_t
     LEFT JOIN pasienadmisi_t ON pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id
     JOIN pasien_m ON pendaftaran_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN ( SELECT tagihan.pendaftaran_id,
            sum(tagihan.tagihan) AS total_tagihan
           FROM ( SELECT tindakanpelayanan_t.pendaftaran_id,
                    sum(tindakanpelayanan_t.tarif_tindakan) AS tagihan
                   FROM tindakanpelayanan_t
                  WHERE tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL
                  GROUP BY tindakanpelayanan_t.pendaftaran_id
                UNION ALL
                 SELECT obatalkespasien_t.pendaftaran_id,
                    sum(obatalkespasien_t.hargajual_oa) AS tagihan
                   FROM obatalkespasien_t
                  WHERE obatalkespasien_t.is_deleted = false AND obatalkespasien_t.obatsudahbayar_id IS NULL
                  GROUP BY obatalkespasien_t.pendaftaran_id) tagihan
          GROUP BY tagihan.pendaftaran_id) total_tagihan ON pendaftaran_t.pendaftaran_id = total_tagihan.pendaftaran_id
     LEFT JOIN pemberianpiutang_t ON pendaftaran_t.pendaftaran_id = pemberianpiutang_t.pendaftaran_id
     LEFT JOIN bayaruangmuka_t ON pendaftaran_t.pendaftaran_id = bayaruangmuka_t.pendaftaran_id
     LEFT JOIN konfigsystem_k ON konfigsystem_k.is_deleted = false
     LEFT JOIN ( SELECT tariftindakan_m.tariftindakan_id,
            tariftindakan_m.daftartindakan_id,
            tariftindakan_m.kelaspelayanan_id,
            tariftindakan_m.penjamin_id,
            tariftindakan_m.harga_tariftindakan AS tarif_max
           FROM tariftindakan_m
             JOIN konfigsystem_k konfig_tarif ON tariftindakan_m.daftartindakan_id = konfig_tarif.adm_tindakan_id
          WHERE tariftindakan_m.is_deleted = false AND tariftindakan_m.komponentarif_id = 6) adm ON adm.kelaspelayanan_id = pasienadmisi_t.kelaspelayanan_id AND adm.penjamin_id = pasienadmisi_t.penjamin_id
     LEFT JOIN ( SELECT tagihan.pendaftaran_id,
            sum(tagihan.tagihan) AS total_tagihan
           FROM ( SELECT tindakanpelayanan_t.pendaftaran_id,
                    sum(tindakanpelayanan_t.tarif_tindakan) AS tagihan
                   FROM tindakanpelayanan_t
                     JOIN ruangan_m ON ruangan_m.ruangan_id = tindakanpelayanan_t.ruangan_id
                  WHERE tindakanpelayanan_t.is_deleted = false AND tindakanpelayanan_t.tindakansudahbayar_id IS NULL AND tindakanpelayanan_t.pasienadmisi_id IS NOT NULL AND ruangan_m.instalasi_id = 3
                  GROUP BY tindakanpelayanan_t.pendaftaran_id
                UNION ALL
                 SELECT obatalkespasien_t.pendaftaran_id,
                    sum(obatalkespasien_t.hargajual_oa) AS tagihan
                   FROM obatalkespasien_t
                     JOIN ruangan_m ON ruangan_m.ruangan_id = obatalkespasien_t.ruangan_id
                  WHERE obatalkespasien_t.is_deleted = false AND obatalkespasien_t.obatsudahbayar_id IS NULL AND obatalkespasien_t.pasienadmisi_id IS NOT NULL AND ruangan_m.instalasi_id = 3
                  GROUP BY obatalkespasien_t.pendaftaran_id) tagihan
          GROUP BY tagihan.pendaftaran_id) tagihan_ranap ON pendaftaran_t.pendaftaran_id = tagihan_ranap.pendaftaran_id
     LEFT JOIN ( SELECT sum(penjualanresep_t.biayaadministrasi) AS total_adm,
            penjualanresep_t.pendaftaran_id
           FROM penjualanresep_t
          WHERE penjualanresep_t.status_bayar = 349
          GROUP BY penjualanresep_t.pendaftaran_id) reseptur ON reseptur.pendaftaran_id = pendaftaran_t.pendaftaran_id
  WHERE pendaftaran_t.is_deleted = false
UNION ALL
 SELECT 'resep_bebas'::text AS jenis,
    penjualanresep_t.noresep AS no_pendaftaran,
    penjualanresep_t.noresep AS no_resep,
    NULL::character varying AS no_rekam_medik,
    penjualanresep_t.penjualanresep_id AS pendaftaran_id,
    penjualanresep_t.penjualanresep_id,
    penjualanresep_t.nama_pembeli AS nama_pasien,
    tagihan_resep.tagihan_obat + COALESCE(penjualanresep_t.biayaadministrasi, 0::double precision) AS total_tagihan,
    COALESCE(pemberianpiutang_t.total_bayarpiutang, 0::double precision) AS piutang_sudahbayar,
    NULL::date AS tanggal_lahir,
    NULL::character varying AS umur,
    penjualanresep_t.tglresep AS tgl_pendaftaran,
    COALESCE(pemberianpiutang_t.total_piutang, 0::double precision) AS total_piutang,
    0 AS adm_persen,
    0 AS tarif_max,
    0 AS tagihan_ranap,
    0 AS uang_muka,
    konfigsystem_k.is_pembulatankeatas,
    konfigsystem_k.satuanpembulatan
   FROM penjualanresep_t
     LEFT JOIN ( SELECT obatalkespasien_t.penjualanresep_id,
            sum(obatalkespasien_t.hargajual_oa) AS tagihan_obat
           FROM obatalkespasien_t
          WHERE obatalkespasien_t.is_deleted = false AND obatalkespasien_t.obatsudahbayar_id IS NULL
          GROUP BY obatalkespasien_t.penjualanresep_id) tagihan_resep ON penjualanresep_t.penjualanresep_id = tagihan_resep.penjualanresep_id
     LEFT JOIN pemberianpiutang_t ON penjualanresep_t.penjualanresep_id = pemberianpiutang_t.penjualanresep_id
     LEFT JOIN konfigsystem_k ON konfigsystem_k.is_deleted = false
  WHERE penjualanresep_t.status_bayar = 349 AND penjualanresep_t.status_reseptur <> 432 AND penjualanresep_t.is_deleted = false AND penjualanresep_t.pendaftaran_id IS NULL;
");
        $this->execute('ALTER TABLE public.fpemberianpiutang_v
    OWNER TO postgres;');

    /*konfigfarmasi_k*/   
        $this->execute('ALTER TABLE "public"."konfigfarmasi_k" ADD COLUMN "penjaminkaryawan_id" int4;');

    /*penerimaansupp_t*/  
        $this->execute('ALTER TABLE "public"."penerimaansupp_t" ADD COLUMN "is_verifikasi" bool DEFAULT false;');
        $this->execute('ALTER TABLE "public"."penerimaansupp_t" ADD COLUMN "tgl_verifikasi" date;');

     /*pesanobatalkes_t*/  
        $this->execute('ALTER TABLE "public"."pesanobatalkes_t" ALTER COLUMN "status_verifikasi" SET DEFAULT 666;');

    /*infopenerimaansupp_v*/ 
        $this->execute('DROP VIEW if exists public.infopenerimaansupp_v;');
        $this->execute("
            CREATE OR REPLACE VIEW public.infopenerimaansupp_v
 AS
 SELECT penerimaansupp_t.penerimaansupp_id,
    penerimaansupp_t.no_penerimaan,
    penerimaansupp_t.tgl_penerimaan,
    penerimaansupp_t.no_faktur,
    penerimaansupp_t.supplier_id,
    supplier_m.supplier_nama,
    penerimaansupp_t.peg_menyetujui,
    peg_menyetujui.nama_pegawai AS peg_menyetujui_nama,
    penerimaansupp_t.peg_mengetahui,
    peg_mengetahui.nama_pegawai AS peg_mengetahui_nama,
    penerimaansupp_t.ruanganpenerima_id,
    ruangan_m.ruangan_nama,
    concat(pajak_m.pajak_name, ' (', pajak_m.pajak_persen, '%)') AS pajak_label,
    payterm_m.payterm_nama,
    penerimaansupp_t.is_verifikasi,
        CASE
            WHEN penerimaansupp_t.is_verifikasi = true THEN 'Sudah Verifikasi'::text
            ELSE 'Belum Verifikasi'::text
        END AS status_verifikasi,
    penerimaansupp_t.tgl_verifikasi
   FROM penerimaansupp_t
     JOIN supplier_m ON penerimaansupp_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN pegawai_m peg_menyetujui ON penerimaansupp_t.peg_menyetujui = peg_menyetujui.pegawai_id
     LEFT JOIN pegawai_m peg_mengetahui ON penerimaansupp_t.peg_mengetahui = peg_mengetahui.pegawai_id
     JOIN ruangan_m ON penerimaansupp_t.ruanganpenerima_id = ruangan_m.ruangan_id
     LEFT JOIN pajak_m ON pajak_m.pajak_id = penerimaansupp_t.pajak_id
     LEFT JOIN payterm_m ON payterm_m.payterm_id = penerimaansupp_t.payterm_id
  WHERE penerimaansupp_t.is_tipe = 0;");
        $this->execute('ALTER TABLE public.infopenerimaansupp_v
    OWNER TO postgres;');

    /*infopemesananobatalkes_v*/    
        $this->execute('DROP VIEW if exists public.infopemesananobatalkes_v;');
        $this->execute("
            CREATE OR REPLACE VIEW public.infopemesananobatalkes_v
 AS
 SELECT pesanobatalkes_t.pesanobatalkes_id,
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
    pesanobatalkes_t.status_verifikasi,
    fgetnamalookup(pesanobatalkes_t.status_verifikasi::integer) AS status_verifikasi_nama
   FROM pesanobatalkes_t
     JOIN ruangan_m ON pesanobatalkes_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN ruangan_m ruangpemesan ON pesanobatalkes_t.ruanganpemesan_id = ruangpemesan.ruangan_id
     JOIN instalasi_m instalasipesan ON ruangpemesan.instalasi_id = instalasipesan.instalasi_id
     LEFT JOIN mutasiobatruangan_t ON pesanobatalkes_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id
  WHERE pesanobatalkes_t.is_active = true AND pesanobatalkes_t.is_deleted = false;");
        $this->execute('ALTER TABLE public.infopemesananobatalkes_v
    OWNER TO postgres;');

    /*infodistribusiobatalkes_v*/
        $this->execute('DROP VIEW if exists public.infodistribusiobatalkes_v;');
        $this->execute("
            CREATE OR REPLACE VIEW public.infodistribusiobatalkes_v
 AS
 SELECT pesanobatalkes_t.pesanobatalkes_id,
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
    fgetnamalookup(pesanobatalkes_t.status_verifikasi::integer) AS status_verifikasi_nama
   FROM pesanobatalkes_t
     JOIN ruangan_m ON pesanobatalkes_t.ruangan_id = ruangan_m.ruangan_id
     JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
     JOIN ruangan_m ruangpemesan ON pesanobatalkes_t.ruanganpemesan_id = ruangpemesan.ruangan_id
     JOIN instalasi_m instalasipesan ON ruangpemesan.instalasi_id = instalasipesan.instalasi_id
     LEFT JOIN mutasiobatruangan_t ON pesanobatalkes_t.mutasiobatruangan_id = mutasiobatruangan_t.mutasiobatruangan_id
     LEFT JOIN terimamutasiobat_t ON mutasiobatruangan_t.mutasiobatruangan_id = terimamutasiobat_t.mutasiobatruangan_id
  WHERE pesanobatalkes_t.is_active = true AND pesanobatalkes_t.is_deleted = false;");
        $this->execute('ALTER TABLE public.infodistribusiobatalkes_v
    OWNER TO postgres;');

        $this->execute('DROP TABLE if exists public.pembayarantransaksi_t;');
        $this->execute('CREATE TABLE public.pembayarantransaksi_t
(
    pembayarantransaksi_id serial8 NOT NULL,
    jenis_transaksi smallint NOT NULL,
    tgl_transaksi date NOT NULL,
    no_transaksi character varying(100) COLLATE pg_catalog."default",
    tipe_transaksi smallint,
    supplier_id integer,
    pegawai_id integer,
    pasien_id integer,
    metode_pembayaran smallint,
    jumlah double precision,
    kategoritransaksi_id integer,
    deskripsi text COLLATE pg_catalog."default",
    referensi character varying(255) COLLATE pg_catalog."default",
    additional_data text COLLATE pg_catalog."default",
    created_date timestamp(6) without time zone NOT NULL DEFAULT (\'now\'::text)::date,
    created_by integer,
    modified_count integer,
    last_modified_date timestamp(6) without time zone,
    last_modified_by integer,
    is_deleted boolean DEFAULT false,
    is_active boolean DEFAULT true,
    deleted_date timestamp(6) without time zone,
    deleted_by integer,
    CONSTRAINT pembayarantransaksi_t_pkey PRIMARY KEY (pembayarantransaksi_id)
)
WITH (
    OIDS = FALSE
)
TABLESPACE pg_default;');

        $this->execute('ALTER TABLE public.pembayarantransaksi_t
    OWNER to postgres;');

        $this->execute('COMMENT ON COLUMN public.pembayarantransaksi_t.jenis_transaksi
    IS \'lookup_type = jenis_transaksi\';');

        $this->execute('COMMENT ON COLUMN public.pembayarantransaksi_t.tipe_transaksi
    IS \'lookup_type =  tipe_transaksi\';');

        $this->execute('COMMENT ON COLUMN public.pembayarantransaksi_t.supplier_id
    IS \'tipe_transaksi = 700\';');

        $this->execute('COMMENT ON COLUMN public.pembayarantransaksi_t.pegawai_id
    IS \'tipe_transaksi = 701\';');

        $this->execute('COMMENT ON COLUMN public.pembayarantransaksi_t.pasien_id
    IS \'tipe_transaksi = 702\';');

        $this->execute('COMMENT ON COLUMN public.pembayarantransaksi_t.metode_pembayaran
    IS \'lookup_type = metode_bayar\';');

        $this->execute("CREATE OR REPLACE FUNCTION public.no_pembayarantransaksi()
  RETURNS pg_catalog.trigger AS \$BODY\$DECLARE
    vId integer; 
    vPrefix varchar;
    vLast varchar;
    vYear varchar;
    vMonth varchar;
    v_Nomor varchar;
    vjenis_transaksi integer;
    
BEGIN
    vjenis_transaksi := NEW.jenis_transaksi;
    
--  jenis_transaksi = 668 —> penomoran_id = 36
--  jika jenis_transaksi = 669 —> penomoran_id 37
    
    IF(vjenis_transaksi = 668)
    THEN 
        vId := 36;
    ELSE
        vId := 37;
    END IF;
    
    SELECT 
        prefix,
        date_part('YEAR',now()) as year, 
        date_part('month',now()) as month,
        (
            SELECT CONCAT(LPAD(CAST(COALESCE(CAST(RIGHT(MAX(last_number), 4) AS INT), 0) + 1 AS VARCHAR(4)), 4, '0')) last_no
                FROM penomoran_k where penomoran_id = vId
        )
    INTO
        vPrefix,
        vYear,
        vMonth,
        vLast
        
    FROM penomoran_k WHERE penomoran_id = vId;
    v_Nomor = vPrefix || vYear || vMonth || vLast;

    UPDATE penomoran_k SET
        last_number = vLast,
        last_generate = V_Nomor
    WHERE penomoran_id = vId;

    NEW.no_transaksi = v_Nomor;

    RETURN NEW;
END
\$BODY\$
  LANGUAGE plpgsql VOLATILE
  COST 100;
");
        
        $this->execute('ALTER FUNCTION "public"."no_pembayarantransaksi"() OWNER TO "postgres";');

        $this->execute('CREATE TRIGGER no_pembayarantransaksi
    BEFORE INSERT
    ON public.pembayarantransaksi_t
    FOR EACH ROW
    EXECUTE PROCEDURE public.no_pembayarantransaksi();');

        $this->execute('DROP VIEW if exists public.infopembayarantransaksi_v;');
        $this->execute("
            CREATE OR REPLACE VIEW public.infopembayarantransaksi_v
 AS
 SELECT pembayarantransaksi_t.pembayarantransaksi_id,
    pembayarantransaksi_t.jenis_transaksi,
    fgetnamalookup(pembayarantransaksi_t.jenis_transaksi::integer) AS jenis,
    pembayarantransaksi_t.tgl_transaksi,
    pembayarantransaksi_t.no_transaksi,
    pembayarantransaksi_t.tipe_transaksi,
    fgetnamalookup(pembayarantransaksi_t.tipe_transaksi::integer) AS tipe,
    pembayarantransaksi_t.supplier_id,
    supplier_m.supplier_nama,
    pembayarantransaksi_t.pegawai_id,
    pegawai_m.nama_pegawai,
    pembayarantransaksi_t.pasien_id,
    pasien_m.nama_pasien,
    pembayarantransaksi_t.metode_pembayaran,
    pembayarantransaksi_t.jumlah,
    pembayarantransaksi_t.kategoritransaksi_id,
    kategoritransaksi_m.kategoritransaksi_nama,
    pembayarantransaksi_t.deskripsi,
    pembayarantransaksi_t.referensi
   FROM pembayarantransaksi_t
     LEFT JOIN pegawai_m ON pembayarantransaksi_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN pasien_m ON pembayarantransaksi_t.pasien_id = pasien_m.pegawai_id
     LEFT JOIN supplier_m ON pembayarantransaksi_t.supplier_id = supplier_m.supplier_id
     LEFT JOIN kategoritransaksi_m ON pembayarantransaksi_t.kategoritransaksi_id = kategoritransaksi_m.kategoritransaksi_id
  WHERE pembayarantransaksi_t.is_deleted = false;");
        $this->execute('ALTER TABLE public.infopembayarantransaksi_v
    OWNER TO postgres;');
     
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200323_104930_migrate_20200323 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200323_104930_migrate_20200323 cannot be reverted.\n";

        return false;
    }
    */
}
