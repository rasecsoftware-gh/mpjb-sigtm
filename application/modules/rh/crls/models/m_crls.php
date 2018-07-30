<?php
class M_CRLS extends CI_Model{
	
	public function __construct() { 
		parent::__construct();
	}

	public function get_list ($search_by='all', $search_text='', $search_ubig='all', $size=100, $start=0) {
		$rows = sys2009_trabajador_list($search_by, $search_text, $search_ubig); 
		$total_count = count($rows);
		if ($start > $total_count) {
			$start = 0;
		}
		$i = 0;
		$list = array();
		foreach ($rows as $r) {
			if ($i >= $start) {
				if ($i < $start+$size) {
					//$r['periodo_count'] = 0;
					//$r['servicio_count'] = 0;
					//$r['traba_estado'] = '';
					$list[] = $r;
				} else {
					break;
				}
			}
			$i++;
		}
		$ret = array(
			'data'=>$list,
			'total'=>$total_count
		);
		return $ret;
	}

	public function get_planilla_list ($traba_cod) {
		$rows = sys2009_planilla_list($traba_cod);
		$total_count = count($rows);
		
		$list = array();
		foreach ($rows as $r) {
			$list[] = $r;
		}
		$ret = array(
			'data'=>$list,
			'total'=>$total_count
		);
		return $ret;
	}

	public function get_plani_ingreso_list ($plani_anio, $plani_cod, $traba_cod) {
		$rows = sys2009_plani_ingreso_list($plani_anio, $plani_cod, $traba_cod);
		$total_count = count($rows);
		
		$ret = array(
			'data'=>$rows,
			'total'=>$total_count
		);
		return $ret;
	}

	public function get_plani_descuento_list ($plani_anio, $plani_cod, $traba_cod) {
		$rows = sys2009_plani_descuento_list($plani_anio, $plani_cod, $traba_cod);
		$total_count = count($rows);
		
		$ret = array(
			'data'=>$rows,
			'total'=>$total_count
		);
		return $ret;
	}

	public function get_plani_aporte_list ($plani_anio, $plani_cod, $traba_cod) {
		$rows = sys2009_plani_aporte_list($plani_anio, $plani_cod, $traba_cod);
		$total_count = count($rows);
		
		$ret = array(
			'data'=>$rows,
			'total'=>$total_count
		);
		return $ret;
	}

	public function get_servicio_list ($traba_cod) {
		$rows = sys2009_servicio_list($traba_cod);
		$rows_siga = $this->siga_servicio_list($traba_cod);

		$total_count = count($rows)+count($rows_siga);
		$rows = array_merge($rows_siga,$rows);
		
		$list = array();
		foreach ($rows as $r) {
			$list[] = $r;
		}
		$ret = array(
			'data'=>$list,
			'total'=>$total_count
		);
		return $ret;
	}

	public function prepare_print_resumen ($traba_cod, $report_key) {
		$t_list = sys2009_trabajador_list('traba_cod', $traba_cod);
		$this->db->trans_begin();
		
		foreach ($t_list as $t) {
			$p_list = sys2009_planilla_list($traba_cod);
			$s_list = sys2009_servicio_list($t['traba_dni']);
			$anios = array();
			$am = array(); //anio_mes
			foreach ($p_list as $r) {
				if (!in_array($r['plani_anio'], $anios)) {
					$anios[] = $r['plani_anio'];
				}
				$am[$r['plani_anio']][intval($r['plani_mes'])]['P'][] = $r;
			}
			foreach ($s_list as $r) {
				if (!in_array($r['os_anio'], $anios)) {
					$anios[] = $r['os_anio'];
				}
				$am[$r['os_anio']][intval($r['os_mes'])]['S'][] = $r;
			}
			rsort($anios);

			foreach ($anios as $anio) {
				$data = array(
					'trep_rls_key'=>$report_key,
					'traba_dni'=>$t['traba_dni'],
					'traba_nomape'=>$t['traba_nomape'],
					'anio'=>$anio,
					'traba_ubigeo'=>(strlen($t['traba_ubigeo']) > 0 ? substr($t['traba_ubigeo'],0,99) : ''),
				);
				for ($mes=1; $mes<=12; $mes++) {
					$data['m'.$mes] = '';
					if (isset($am[$anio][$mes]['P'])) {
						foreach($am[$anio][$mes]['P'] as $p) {
							$info = str_replace(' ', '', $p['plani_num'])."\n({$p['reglab_abrev']})\n";
							if ($p['plani_traba_aguinaldo']>0) {
								$info .= "AGUINALDO\n";
							}
							$data['m'.$mes] .= $info;
						}
					}
					if (isset($am[$anio][$mes]['S'])) {
						foreach($am[$anio][$mes]['S'] as $p) {
							$data['m'.$mes] .= 'OS-'.intval($p['os_numero'])."\n";
						}
					}
				}
				$this->db->insert('rh.trep_rls', $data);
			}
		}
		
		if ($this->db->trans_status() === FALSE){
        	$this->db->trans_rollback();
        	return false;
		} else {
        	$this->db->trans_commit();
        	return true;
		}
	}
	
	public function siga_servicio_list($dni_trabajador) {
		$this->siga = $this->load->database('siga', true);
		$rows = $this->siga
		->select("
			o.ANO_EJE AS os_anio
			, FORMAT(CONVERT(integer, o.NRO_ORDEN), 'D8') AS os_numero
			, o.PROVEEDOR AS prove_cod
			, p.NRO_RUC AS prove_ruc
			, p.NOMBRE_PROV AS prove_desc
			, CONVERT(varchar(10), o.FECHA_ORDEN, 103) AS os_fecha
			, o.MES_CALEND AS os_mes
			, (CASE o.ESTADO WHEN '4' THEN 'SI' ELSE 'NO' END) AS os_anulado
			, o.DOCUM_REFERENCIA AS os_referencia
			, o.CONCEPTO AS os_glosa
			, o.TOTAL_FACT_SOLES AS os_total
			")
		->from('dbo.SIG_ORDEN_ADQUISICION AS o')
		->join('dbo.SIG_CONTRATISTAS AS p', 'p.PROVEEDOR = o.PROVEEDOR', 'inner')
		->where('o.TIPO_BIEN', 'S')
		->where('o.ANO_EJE >=', 2017)
		->where("p.NRO_RUC LIKE '%{$dni_trabajador}%'")
		->order_By('o.ano_eje')
		->order_By('o.nro_orden')
		->get()->result_array();
		return $rows;
	}
}
?>