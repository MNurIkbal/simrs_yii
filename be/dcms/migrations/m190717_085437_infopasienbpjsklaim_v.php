<?php

use yii\db\Migration;

/**
 * Class m190717_085437_infopasienbpjsklaim_v
 */
class m190717_085437_infopasienbpjsklaim_v extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         $this->execute('
         DROP VIEW IF exists public.infopasienbpjsklaim_v;
        ');

          $this->execute("
          CREATE OR REPLACE VIEW public.infopasienbpjsklaim_v AS 
 SELECT 'TINDAKAN'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
        CASE
            WHEN tindakanpelayanan_t.instalasi_id = 3 THEN pendaftaran_t.pasienadmisi_id
            ELSE NULL::integer
        END AS pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    tindakanpelayanan_t.instalasi_id,
    tindakanpelayanan_t.tindakanpelayanan_id,
    daftartindakan_m.groupinacbg_id,
    groupinacbg_m.groupinacbg_nama,
    tindakanpelayanan_t.tarif_tindakan
   FROM pendaftaran_t
     JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN groupinacbg_m ON daftartindakan_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
  WHERE tindakanpelayanan_t.carabayar_id = 6 AND tindakanpelayanan_t.pasienmasukpenunjang_id IS NULL
UNION ALL
 SELECT 'TINDAKAN'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
        CASE
            WHEN ruangan_m.instalasi_id = 3 THEN pendaftaran_t.pasienadmisi_id
            ELSE NULL::integer
        END AS pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    ruangan_m.instalasi_id,
    tindakanpelayanan_t.tindakanpelayanan_id,
    daftartindakan_m.groupinacbg_id,
    groupinacbg_m.groupinacbg_nama,
    tindakanpelayanan_t.tarif_tindakan
   FROM pendaftaran_t
     JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     JOIN daftartindakan_m ON tindakanpelayanan_t.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN groupinacbg_m ON daftartindakan_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
     LEFT JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
  WHERE tindakanpelayanan_t.carabayar_id = 6 AND tindakanpelayanan_t.pasienmasukpenunjang_id IS NOT NULL
UNION ALL
 SELECT 'PAKET'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
        CASE
            WHEN tindakanpelayanan_t.instalasi_id = 3 THEN pendaftaran_t.pasienadmisi_id
            ELSE NULL::integer
        END AS pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    tindakanpelayanan_t.instalasi_id,
    tindakanpelayanan_t.tindakanpelayanan_id,
    daftartindakan_m.groupinacbg_id,
    groupinacbg_m.groupinacbg_nama,
    tindakanpelayanan_t.tarif_tindakan
   FROM pendaftaran_t
     JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
     JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN groupinacbg_m ON daftartindakan_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
  WHERE tindakanpelayanan_t.carabayar_id = 6 AND tindakanpelayanan_t.pasienmasukpenunjang_id IS NULL
UNION ALL
 SELECT 'PAKET'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
        CASE
            WHEN tindakanpelayanan_t.instalasi_id = 3 THEN pendaftaran_t.pasienadmisi_id
            ELSE NULL::integer
        END AS pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    tindakanpelayanan_t.instalasi_id,
    tindakanpelayanan_t.tindakanpelayanan_id,
    daftartindakan_m.groupinacbg_id,
    groupinacbg_m.groupinacbg_nama,
    tindakanpelayanan_t.tarif_tindakan
   FROM pendaftaran_t
     JOIN tindakanpelayanan_t ON pendaftaran_t.pendaftaran_id = tindakanpelayanan_t.pendaftaran_id
     JOIN paketpelayanan_mp ON tindakanpelayanan_t.tipepaket_id = paketpelayanan_mp.tipepaket_id
     JOIN daftartindakan_m ON paketpelayanan_mp.daftartindakan_id = daftartindakan_m.daftartindakan_id
     JOIN groupinacbg_m ON daftartindakan_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
     LEFT JOIN pasienmasukpenunjang_t ON tindakanpelayanan_t.pasienmasukpenunjang_id = pasienmasukpenunjang_t.pasienmasukpenunjang_id
     LEFT JOIN ruangan_m ON pasienmasukpenunjang_t.ruanganasal_id = ruangan_m.ruangan_id
  WHERE tindakanpelayanan_t.carabayar_id = 6 AND tindakanpelayanan_t.pasienmasukpenunjang_id IS NOT NULL
UNION ALL
 SELECT 'OBAT'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    ruangan_m.instalasi_id,
    obatalkespasien_t.obatalkespasien_id AS tindakanpelayanan_id,
    obatalkes_m.groupinacbg_id,
    groupinacbg_m.groupinacbg_nama,
    obatalkespasien_t.hargajual_oa AS tarif_tindakan
   FROM pendaftaran_t
     JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
     JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN groupinacbg_m ON obatalkes_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
     LEFT JOIN ruangan_m ON obatalkespasien_t.ruangan_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
  WHERE obatalkespasien_t.carabayar_id = 6 AND obatalkespasien_t.resepturdetail_id IS NULL
UNION ALL
 SELECT 'OBAT'::text AS jenis,
    pendaftaran_t.pendaftaran_id,
    pendaftaran_t.pasienadmisi_id,
    pendaftaran_t.no_pendaftaran,
    ruangan_m.instalasi_id,
    obatalkespasien_t.obatalkespasien_id AS tindakanpelayanan_id,
    obatalkes_m.groupinacbg_id,
    groupinacbg_m.groupinacbg_nama,
    obatalkespasien_t.hargajual_oa AS tarif_tindakan
   FROM pendaftaran_t
     JOIN obatalkespasien_t ON pendaftaran_t.pendaftaran_id = obatalkespasien_t.pendaftaran_id
     JOIN obatalkes_m ON obatalkespasien_t.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN groupinacbg_m ON obatalkes_m.groupinacbg_id = groupinacbg_m.groupinacbg_id
     LEFT JOIN resepturdetail_t ON obatalkespasien_t.resepturdetail_id = resepturdetail_t.resepturdetail_id
     LEFT JOIN reseptur_t ON resepturdetail_t.reseptur_id = reseptur_t.reseptur_id
     LEFT JOIN ruangan_m ON reseptur_t.ruanganreseptur_id = ruangan_m.ruangan_id
     LEFT JOIN instalasi_m ON ruangan_m.instalasi_id = instalasi_m.instalasi_id
  WHERE obatalkespasien_t.resepturdetail_id IS NOT NULL;
        ");

           $this->execute('
         ALTER TABLE public.infopasienbpjsklaim_v
  OWNER TO postgres;
        ');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190717_085437_infopasienbpjsklaim_v cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190717_085437_infopasienbpjsklaim_v cannot be reverted.\n";

        return false;
    }
    */
}
