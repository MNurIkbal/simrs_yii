CREATE OR REPLACE VIEW public.satusehat_obatalkescvx_v
AS
 SELECT obatalkes_m.obatalkes_id,
    obatalkes_m.obatalkes_kode,
    obatalkes_m.obatalkes_nama,
    satusehat_cvx_m.satusehat_cvx_id,
    satusehat_cvx_m.cvx_group_code,
    satusehat_cvx_m.cvx_group_display,
    cvxmp.cvx_reasoncode,
    cvxmp.cvx_name_code,
    cvxmp.cvx_name_display
   FROM obatalkes_m
     JOIN satusehat_cvx_obatalkes_mp cvxmp ON cvxmp.obatalkes_id = obatalkes_m.obatalkes_id
     JOIN satusehat_cvx_m ON satusehat_cvx_m.satusehat_cvx_id = cvxmp.satusehat_cvx_id
  WHERE cvxmp.is_deleted = false AND obatalkes_m.is_deleted = false AND obatalkes_m.is_active = true
  ORDER BY obatalkes_m.obatalkes_id;