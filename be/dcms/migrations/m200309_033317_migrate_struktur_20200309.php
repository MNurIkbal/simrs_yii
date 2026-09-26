<?php

use yii\db\Migration;

/**
 * Class m200309_033317_migrate_struktur_20200309
 */
class m200309_033317_migrate_struktur_20200309 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute('ALTER TABLE "public"."obatalkes_m" ADD CONSTRAINT "obatalkes_kode" UNIQUE ("obatalkes_kode");');
        
        $this->execute('DROP VIEW if exists public.inforiwayatresep_v;');
        
        $this->execute("
            CREATE OR REPLACE VIEW public.inforiwayatresep_v AS 
 SELECT obatalkespasien_t.obatalkespasien_id,
    obatalkespasien_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    pasien_m.pasien_id,
    pasien_m.no_rekam_medik,
    pasien_m.nama_pasien,
    obatalkespasien_t.tglpelayanan AS tgl_transaksi,
    penjualanresep_t.noresep AS no_resep,
    signaobat_m.signa_id,
    signaobat_m.signa_nama,
    obatalkespasien_t.qty_oa AS qty,
    satuan_kecil.satuanunit_id,
    satuan_kecil.satuanunit_nama,
    instalasi_m.instalasi_nama,
    ruangan_tujuan.ruangan_nama,
    carabayar_m.carabayar_id,
    carabayar_m.carabayar_nama,
    penjamin_m.penjamin_id,
    penjamin_m.penjamin_nama
   FROM obatalkespasien_t
     JOIN penjualanresep_t ON obatalkespasien_t.penjualanresep_id = penjualanresep_t.penjualanresep_id AND penjualanresep_t.reseptur_id IS NULL
     LEFT JOIN pendaftaran_t ON penjualanresep_t.pendaftaran_id = pendaftaran_t.pendaftaran_id
     LEFT JOIN pasien_m ON penjualanresep_t.pasien_id = pasien_m.pasien_id
     LEFT JOIN pegawai_m ON penjualanresep_t.pegawai_id = pegawai_m.pegawai_id
     LEFT JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     LEFT JOIN satuanunit_m satuan_kecil ON obatalkespasien_t.satuankecil_id = 
     satuan_kecil.satuanunit_id
     LEFT JOIN racikan_m ON obatalkespasien_t.racikan_id = racikan_m.racikan_id
     LEFT JOIN ruangan_m ruangan_tujuan ON penjualanresep_t.ruangan_id = ruangan_tujuan.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_tujuan.instalasi_id = instalasi_m.instalasi_id
     JOIN konfigfarmasi_k ON konfigfarmasi_k.is_deleted = false
     LEFT JOIN signaobat_m ON obatalkespasien_t.signa_oa::integer = signaobat_m.signa_id
     LEFT JOIN penjamin_m ON obatalkespasien_t.penjamin_id = penjamin_m.penjamin_id
     LEFT JOIN carabayar_m ON obatalkespasien_t.carabayar_id = carabayar_m.carabayar_id
  WHERE obatalkespasien_t.is_deleted = false AND obatalkespasien_t.is_active = true AND penjualanresep_t.status_reseptur = 660;");
       
        $this->execute('ALTER TABLE public.inforiwayatresep_v
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.infoadjusmenobat_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infoadjusmenobat_v AS 
 SELECT adjusmenobat_t.adjusmenobat_id,
    adjusmenobat_t.no_adjusmen,
    adjusmenobat_t.tgl_adjusmen,
    adjusmenobat_t.jenis_adjusmen,
        CASE
            WHEN adjusmenobat_t.jenis_adjusmen = 0 THEN 'adjusmen_masuk'::text
            ELSE 'adjusmen_keluar'::text
        END AS jenis_adjusmen_nama,
    adjusmenobat_t.peg_mengetahui_id,
    peg_mengetahui.nama_pegawai AS pegawai_mengetahui,
    adjusmenobat_t.peg_menyetujui_id,
    peg_menyetujui.nama_pegawai AS pegawai_menyetujui,
    adjusmenobat_t.ruangan_adjusmen_id,
    ruangan_m.ruangan_nama
   FROM adjusmenobat_t
     LEFT JOIN pegawai_m peg_mengetahui ON adjusmenobat_t.peg_mengetahui_id = peg_mengetahui.pegawai_id
     LEFT JOIN pegawai_m peg_menyetujui ON adjusmenobat_t.peg_menyetujui_id = peg_menyetujui.pegawai_id
     JOIN ruangan_m ON adjusmenobat_t.ruangan_adjusmen_id = ruangan_m.ruangan_id
  WHERE adjusmenobat_t.is_deleted = false;");

        $this->execute('ALTER TABLE public.infoadjusmenobat_v
  OWNER TO postgres;');

        $this->execute('DROP VIEW if exists public.infoadjusmenobatdetail_v;');

        $this->execute("
            CREATE OR REPLACE VIEW public.infoadjusmenobatdetail_v AS 
 SELECT 'masuk'::text AS jenis,
    adjusmenobat_t.adjusmenobat_id,
    adjusmenobat_t.no_adjusmen,
    adjusmenobatmasuk_t.adjusmenobatmasuk_id AS detail_id,
    adjusmenobatmasuk_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    adjusmenobatmasuk_t.qty AS qty_input,
    adjusmenobatmasuk_t.qty_konversi,
    adjusmenobatmasuk_t.tgl_kadaluarsa,
    adjusmenobatmasuk_t.harga_netto,
    adjusmenobatmasuk_t.no_batch,
    adjusmenobatmasuk_t.keterangan,
    NULL::text AS alasan
   FROM adjusmenobat_t
     JOIN adjusmenobatmasuk_t ON adjusmenobat_t.adjusmenobat_id = adjusmenobatmasuk_t.adjusmenobat_id
     JOIN obatalkes_m ON adjusmenobatmasuk_t.obatalkes_id = obatalkes_m.obatalkes_id
  WHERE adjusmenobat_t.is_deleted = false AND adjusmenobatmasuk_t.is_deleted = false
UNION ALL
 SELECT 'keluar'::text AS jenis,
    adjusmenobat_t.adjusmenobat_id,
    adjusmenobat_t.no_adjusmen,
    adjusmenobatkeluar_t.adjusmenobatkeluar_id AS detail_id,
    adjusmenobatkeluar_t.obatalkes_id,
    obatalkes_m.obatalkes_nama,
    adjusmenobatkeluar_t.qty AS qty_input,
    adjusmenobatkeluar_t.qty_konversi,
    stokobatalkes_t.tglkadaluarsa AS tgl_kadaluarsa,
    NULL::double precision AS harga_netto,
    adjusmenobatkeluar_t.no_batch,
    adjusmenobatkeluar_t.keterangan,
    adjusmenobatkeluar_t.alasan
   FROM adjusmenobat_t
     JOIN adjusmenobatkeluar_t ON adjusmenobat_t.adjusmenobat_id = adjusmenobatkeluar_t.adjusmenobat_id
     JOIN stokobatalkes_t ON adjusmenobatkeluar_t.adjusmenobatkeluar_id = stokobatalkes_t.adjusmenobatkeluar_id AND adjusmenobat_t.ruangan_adjusmen_id = stokobatalkes_t.ruangan_id AND adjusmenobatkeluar_t.obatalkes_id = stokobatalkes_t.obatalkes_id
     JOIN obatalkes_m ON adjusmenobatkeluar_t.obatalkes_id = obatalkes_m.obatalkes_id
  WHERE adjusmenobat_t.is_deleted = false AND adjusmenobatkeluar_t.is_deleted = false;");

        $this->execute('ALTER TABLE public.infoadjusmenobatdetail_v
  OWNER TO postgres;');
      
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200309_033317_migrate_struktur_20200309 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200309_033317_migrate_struktur_20200309 cannot be reverted.\n";

        return false;
    }
    */
}
