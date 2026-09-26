<?php

use yii\db\Migration;

/**
 * Class m200619_022816_migrate_mhkn_20200619
 */
class m200619_022816_migrate_mhkn_20200619 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."konfigsystem_k" ADD COLUMN "default_kelasambulan" int4;');

        $this->execute('DROP VIEW if exists "public"."infopemakaianambulan_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopemakaianambulan_v\" AS  SELECT pemakaianambulan_t.pemakaianambulan_id,
    ambulan_m.ambulan_id,
    ambulan_m.no_polisi,
    pesanambulan_t.tgl_pesanambulan,
    pemakaianambulan_t.tgl_pemakaiandari,
    pemakaianambulan_t.tgl_pemakaiansampai,
    pesanambulan_t.no_pesanambulan,
    pemakaianambulan_t.durasi_pemakaian,
    pemakaianambulan_t.pendaftaran_id,
    pasien_m.no_rekam_medik,
        CASE
            WHEN (pesanambulan_t.pasien_id IS NULL) THEN pesanambulan_t.pemesan
            ELSE pasien_m.nama_pasien
        END AS nama_pemesan,
        CASE
            WHEN (pesanambulan_t.pasien_id IS NULL) THEN fgetnamalookup((pesanambulan_t.jenis_kelamin)::integer)
            ELSE fgetnamalookup((pasien_m.jeniskelamin)::integer)
        END AS jns_kelamin,
        CASE
            WHEN (ambulan_m.is_emergency IS TRUE) THEN 'EMERGENCY'::text
            ELSE 'NON EMERGENCY'::text
        END AS jenis_ambulan,
    pesanambulan_t.status_ambulan,
    pesanambulan_t.asal_pasien,
    pesanambulan_t.keluhan,
    fgetnamalookup(pemakaianambulan_t.pelayanan_ambulan) AS pelayanan,
    COALESCE(pemakaianambulan_t.km_awal, 0) AS km_awal,
    COALESCE(pemakaianambulan_t.km_akhir, 0) AS km_akhir,
    (COALESCE(pemakaianambulan_t.km_akhir, 0) - COALESCE(pemakaianambulan_t.km_awal, 0)) AS jarak_pemakian,
    COALESCE(pemakaianambulan_t.total_biaya, (0)::double precision) AS nominal_tagihan,
    fgetnamalookup(pesanambulan_t.status_ambulan) AS status_ambulan_nama,
    (pegawai.nama_pegawai)::character varying AS supir,
    pesanambulan_t.umur,
    pesanambulan_t.is_sadar,
    pesanambulan_t.is_nafas,
    pesanambulan_t.is_nadi,
    pesanambulan_t.nama_pj,
    pesanambulan_t.kontak_pj,
    pemakaianambulan_t.created_date,
    pemakaianambulan_t.biaya_pemakaian,
    pemakaianambulan_t.tgl_realisasikembali,
    pemakaianambulan_t.lama_pemakaian,
    pesanambulan_t.status_pesan,
    pendaftaran_t.kelaspelayanan_id,
    pendaftaran_t.penjamin_id
   FROM (((((pemakaianambulan_t
     JOIN pesanambulan_t ON ((pemakaianambulan_t.pemakaianambulan_id = pesanambulan_t.pemakaianambulan_id)))
     LEFT JOIN pasien_m ON ((pesanambulan_t.pasien_id = pasien_m.pasien_id)))
     JOIN ambulan_m ON ((pesanambulan_t.ambulan_id = ambulan_m.ambulan_id)))
     LEFT JOIN ( SELECT pemakaianambulandetail_t.pemakaianambulan_id,
            string_agg((pegawai_m.nama_pegawai)::text, ' ,'::text) AS nama_pegawai
           FROM (pemakaianambulandetail_t
             JOIN pegawai_m ON (((pemakaianambulandetail_t.petugas_id = pegawai_m.pegawai_id) AND (pegawai_m.jabatan_id = 38))))
          GROUP BY pemakaianambulandetail_t.pemakaianambulan_id) pegawai ON ((pemakaianambulan_t.pemakaianambulan_id = pegawai.pemakaianambulan_id)))
     LEFT JOIN pendaftaran_t ON ((pemakaianambulan_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)));");

        $this->execute('DROP VIEW if exists "public"."infopermintaanbmhpdetail_v";');

        $this->execute("
            CREATE VIEW \"public\".\"infopermintaanbmhpdetail_v\" AS  SELECT pendaftaran_t.pendaftaran_id,
    pendaftaran_t.no_pendaftaran,
    obatalkespasien_t.obatalkespasien_id,
    obatalkespasien_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    obatalkespasien_t.qty_oa AS qty_obat,
    obatalkespasien_t.qty_konversi,
    ((obatalkespasien_t.additional_data)::json ->> 'satuan_input'::text) AS satuan_input,
    ((obatalkespasien_t.additional_data)::json ->> 'satuan_konversi'::text) AS satuan_konversi
   FROM ((((pendaftaran_t
     LEFT JOIN pasienadmisi_t ON ((pendaftaran_t.pasienadmisi_id = pasienadmisi_t.pasienadmisi_id)))
     JOIN obatalkespasien_t ON ((pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id)))
     JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     JOIN ruangan_m ruangan_tujuan ON ((obatalkespasien_t.ruangan_id = ruangan_tujuan.ruangan_id)))
  WHERE ((obatalkespasien_t.penjualanresep_id IS NULL) AND (obatalkespasien_t.is_deleted = false) AND (ruangan_tujuan.instalasi_id = 6));");

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
    ((resepturdetail_t.additional_data)::json ->> 'satuan_input'::text) AS satuan_input,
    ((resepturdetail_t.additional_data)::json ->> 'satuan_konversi'::text) AS satuan_konversi,
    obatalkespasien_t.etiket,
    obatalkes_m.is_oral,
    stokobatalkes_t.tglkadaluarsa
   FROM ((((((((reseptur_t
     JOIN penjualanresep_t ON ((reseptur_t.reseptur_id = penjualanresep_t.reseptur_id)))
     JOIN obatalkespasien_t ON ((penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id)))
     JOIN stokobatalkes_t ON (((obatalkespasien_t.obatalkespasien_id = stokobatalkes_t.obatalkespasien_id) AND (obatalkespasien_t.obatalkes_id = stokobatalkes_t.obatalkes_id))))
     JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN racikan_m ON ((obatalkespasien_t.racikan_id = racikan_m.racikan_id)))
     JOIN ruangan_m ruangan_asal ON ((reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id)))
     LEFT JOIN signaobat_m ON (((obatalkespasien_t.signa_oa)::integer = signaobat_m.signa_id)))
     JOIN ( SELECT resepturdetail_t_1.reseptur_id,
            resepturdetail_t_1.obatalkes_id,
            resepturdetail_t_1.additional_data
           FROM resepturdetail_t resepturdetail_t_1) resepturdetail_t ON (((reseptur_t.reseptur_id = resepturdetail_t.reseptur_id) AND (obatalkes_m.obatalkes_id = resepturdetail_t.obatalkes_id))))
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
    NULL::date AS tglkadaluarsa
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
    obatalkespasien_t.qty_oa AS qty_obat,
    obatalkespasien_t.qty_konversi,
    ((obatalkespasien_t.additional_data)::json ->> 'satuan_input'::text) AS satuan_input,
    ((obatalkespasien_t.additional_data)::json ->> 'satuan_konversi'::text) AS satuan_konversi,
    obatalkespasien_t.etiket,
    obatalkes_m.is_oral,
    stokobatalkes_t.tglkadaluarsa
   FROM (((((penjualanresep_t
     JOIN obatalkespasien_t ON ((penjualanresep_t.penjualanresep_id = obatalkespasien_t.penjualanresep_id)))
     JOIN obatalkes_m ON ((obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id)))
     LEFT JOIN racikan_m ON ((obatalkespasien_t.racikan_id = racikan_m.racikan_id)))
     LEFT JOIN signaobat_m ON (((obatalkespasien_t.signa_oa)::integer = signaobat_m.signa_id)))
     LEFT JOIN stokobatalkes_t ON (((obatalkespasien_t.obatalkespasien_id = stokobatalkes_t.obatalkespasien_id) AND (obatalkespasien_t.obatalkes_id = stokobatalkes_t.obatalkes_id))))
  WHERE (penjualanresep_t.reseptur_id IS NULL);");

        $this->execute('DROP VIEW if exists "public"."worklistresep_v";');

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
    fgetnamalookup((penjualanresep_t.status_reseptur)::integer) AS status_reseptur
   FROM ((((((reseptur_t
     JOIN pendaftaran_t ON ((reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     JOIN penjualanresep_t ON ((reseptur_t.reseptur_id = penjualanresep_t.reseptur_id)))
     LEFT JOIN pegawai_m ON ((penjualanresep_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ruangan_asal ON ((reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id)))
     LEFT JOIN anamnesa_t ON ((pendaftaran_t.pendaftaran_id = anamnesa_t.pendaftaran_id)))
  WHERE (ruangan_asal.instalasi_id = 1)
  GROUP BY reseptur_t.noresep, penjualanresep_t.noresep, pendaftaran_t.no_pendaftaran, pasien_m.no_rekam_medik, pasien_m.nama_pasien, pasien_m.tanggal_lahir, pegawai_m.nama_pegawai, reseptur_t.status_worklist, ruangan_asal.instalasi_id, penjualanresep_t.tglpenjualan, penjualanresep_t.status_bayar, penjualanresep_t.penjualanresep_id, reseptur_t.reseptur_id, penjualanresep_t.status_reseptur
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
    fgetnamalookup(reseptur_t.status_reseptur) AS status_reseptur
   FROM ((((reseptur_t
     JOIN pendaftaran_t ON ((reseptur_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m ON ((reseptur_t.pegawai_id = pegawai_m.pegawai_id)))
     JOIN ruangan_m ruangan_asal ON ((reseptur_t.ruanganreseptur_id = ruangan_asal.ruangan_id)))
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
    NULL::date AS tanggal_lahir,
    pegawai_m.nama_pegawai AS dokter,
    NULL::text[] AS alergi,
    penjualanresep_t.status_worklist AS status_worklist_id,
    fgetnamalookup((penjualanresep_t.status_worklist)::integer) AS status_worklist,
    NULL::integer AS instalasi_id,
    penjualanresep_t.jenispenjualan AS jenispenjualan_id,
    fgetnamalookup((penjualanresep_t.jenispenjualan)::integer) AS jenis_penjualan,
    penjualanresep_t.tglpenjualan AS tanggal,
    penjualanresep_t.status_bayar AS status_bayar_id,
    fgetnamalookup((penjualanresep_t.status_bayar)::integer) AS status_bayar,
    penjualanresep_t.status_reseptur AS status_reseptur_id,
    fgetnamalookup((penjualanresep_t.status_reseptur)::integer) AS status_reseptur
   FROM ((((penjualanresep_t
     LEFT JOIN pendaftaran_t ON ((penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id)))
     LEFT JOIN pasien_m ON ((pendaftaran_t.pasien_id = pasien_m.pasien_id)))
     LEFT JOIN pegawai_m ON ((penjualanresep_t.pegawai_id = pegawai_m.pegawai_id)))
     LEFT JOIN pegawai_m karyawan ON ((penjualanresep_t.karyawan_id = karyawan.pegawai_id)))
  WHERE (penjualanresep_t.reseptur_id IS NULL);");

        $this->execute('CREATE TABLE "public"."konfigtarif_k" (
  "konfigtarif_id" int4 NOT NULL,
  "default_penjamin" int4,
  "default_kelas" int4,
  "default_ruangan" int4,
  CONSTRAINT "konfigtarif_k_pkey" PRIMARY KEY ("konfigtarif_id")
)
;');

        $this->execute("
            CREATE VIEW \"public\".\"layarantrian_v\" AS  SELECT layarantrian_m.layarantrian_id,
    layarantrian_m.layarantrian_nama,
    layarantrian_m.layarantrian_judul,
    layarantrian_m.jenisantrian_id,
    fgetnamalookup(layarantrian_m.jenisantrian_id) AS jenis_antrian,
    jenisantriandetail_m.jenisantriandetail_id,
    jenisantriandetail_m.nama AS lantai,
    loket_m.loket_id,
    loket_m.loket_nama AS loket,
    layarantrian_m.konfigantrian_id,
    layarantrian_m.layarantrian_latarbelakang,
    layarantrian_m.layarantrian_maxitem,
    layarantrian_m.layarantrian_itemhigh,
    layarantrian_m.layarantrian_itemwidth,
    layarantrian_m.layarantrian_intrefresh,
    layarantrian_m.is_active,
    layarantrian_m.is_deleted
   FROM ((((loket_m
     JOIN loketjenisantrian_mp ON ((loket_m.loket_id = loketjenisantrian_mp.loket_id)))
     JOIN jenisantriandetail_m ON ((loketjenisantrian_mp.jenisantriandetail_id = jenisantriandetail_m.jenisantriandetail_id)))
     JOIN layarantriandetail_m ON ((loketjenisantrian_mp.loket_id = layarantriandetail_m.loket_id)))
     JOIN layarantrian_m ON ((layarantriandetail_m.layarantrian_id = layarantrian_m.layarantrian_id)))
  WHERE ((layarantriandetail_m.is_deleted = false) AND (layarantrian_m.is_deleted = false));");
      

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200619_022816_migrate_mhkn_20200619 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200619_022816_migrate_mhkn_20200619 cannot be reverted.\n";

        return false;
    }
    */
}
