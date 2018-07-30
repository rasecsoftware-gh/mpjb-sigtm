<?php
class M_Asistencia extends CI_Model{
	
	public function __construct() { 
		parent::__construct();
	}

	public function get_list ($search_by='all', $search_text='', $size=100, $start=0) {
		$rows = sys2009_trabajador_list($search_by, $search_text);
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

	public function get_marcacion_list ($traba_dni, $desde, $hasta) {
		$rows = att_list($desde, $hasta, $traba_dni);
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

	public function get_asistencia_list ($traba_dni, $desde, $hasta) {
		$rows = att_list($desde, $hasta, $traba_dni);
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
					'anio'=>$anio
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

}
?>