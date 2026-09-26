<?php

use yii\db\Migration;

/**
 * Class m200622_025152_migrate_mhkn_20200622
 */
class m200622_025152_migrate_mhkn_20200622 extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
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
    NULL::date AS tglkadaluarsa
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
    (((obatalkespasien_t.additional_data)::json ->> 'qty_input'::text))::double precision AS qty_obat,
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

        $this->execute('ALTER TABLE "public"."resepturdetail_t" ADD COLUMN "det" float8;');
        $this->execute('ALTER TABLE "public"."pemakaianambulandetail_t" ADD COLUMN "daftartindakan_id" int4;');
        $this->execute('ALTER TABLE "public"."pemakaianambulandetail_t" ADD COLUMN "harga" float8;');
     

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200622_025152_migrate_mhkn_20200622 cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200622_025152_migrate_mhkn_20200622 cannot be reverted.\n";

        return false;
    }
    */
}
