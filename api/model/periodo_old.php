<?php
class Periodo{
	public function getPeriodos(){
		$query= "SELECT * FROM la_periodo ORDER BY id_periodo ASC";
		$periodos= getFn($query, 'all');
		return $periodos;
	}
	public function getPeriodoArray($id_periodo){
		$query= "SELECT * FROM la_periodo WHERE id_periodo = $id_periodo";
		$periodo= getFn($query, 'all');
		return $periodo;
	}
}
?>