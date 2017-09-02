<?php
class Parametros{
	public function getParametros(){
		$parametros= [];
		$parametros["marcas"]= $this->getMarca();
		$parametros["tipos_anuncio"]= $this->getTipoAnuncio();
		$parametros["formatos"]= $this->getFormato();
		$parametros["lineas_producto"]= $this->getLineaProducto();
		$parametros["tipos_elemento_grande"]= $this->getTipoElementoGrande();
		$parametros["tipos_elemento_frecuencia"]= $this->getTipoElementoFrecuencia();
		$parametros["provincias"]= $this->getProvincias();
		return $parametros;
	}

	public function getAngulo(){
		$query= "SELECT * FROM la_angulo";
		$angulo= getFn($query, 'all');
		return $angulo;
	}
	public function getCalidadIluminacion(){
		$query= "SELECT * FROM la_calidad_iluminacion";
		$calidad_iluminacion= getFn($query, 'all');
		return $calidad_iluminacion;
	}
	public function getCarril(){
		$query= "SELECT * FROM la_carril";
		$carril= getFn($query, 'all');
		return $carril;
	}
	public function getCategoria(){
		$query= "SELECT * FROM la_categoria";
		$categoria= getFn($query, 'all');
		return $categoria;
	}
	public function getDominacion(){
		$query= "SELECT * FROM la_dominacion";
		$dominacion= getFn($query, 'all');
		return $dominacion;
	}
	public function getDuracionMirada(){
		$query= "SELECT * FROM la_duracion_mirada";
		$duracion_mirada= getFn($query, 'all');
		return $duracion_mirada;
	}
	public function getFlujoPeatonal(){
		$query= "SELECT * FROM la_flujo_peatonal";
		$flujo_peatonal= getFn($query, 'all');
		return $flujo_peatonal;
	}
	public function getFlujoVehicular(){
		$query= "SELECT * FROM la_flujo_vehicular";
		$flujo_vehicular= getFn($query, 'all');
		return $flujo_vehicular;
	}
	public function getFormato(){
		$query= "SELECT * FROM la_formato";
		$formato= getFn($query, 'all');
		return $formato;
	}
	public function getIluminacion(){
		$query= "SELECT * FROM la_iluminacion";
		$iluminacion= getFn($query, 'all');
		return $iluminacion;
	}
	public function getLado(){
		$query= "SELECT * FROM la_lado";
		$lado= getFn($query, 'all');
		return $lado;
	}
	public function getLineaProducto(){
		$query= "SELECT * FROM la_linea_producto";
		$linea_producto= getFn($query, 'all');
		return $linea_producto;
	}
	public function getMarca(){
		$query= "SELECT * FROM la_marca";
		$marca= getFn($query, 'all');
		return $marca;
	}
	public function getObstruccion(){
		$query= "SELECT * FROM la_obstruccion";
		$obstruccion= getFn($query, 'all');
		return $obstruccion;
	}
	public function getOrientacion(){
		$query= "SELECT * FROM la_orientacion";
		$orientacion= getFn($query, 'all');
		return $orientacion;
	}
	public function getSaturacion(){
		$query= "SELECT * FROM la_saturacion";
		$saturacion= getFn($query, 'all');
		return $saturacion;
	}
	public function getTipoAnuncio(){
		$query= "SELECT * FROM la_tipo_anuncio";
		$tipo_anuncio= getFn($query, 'all');
		return $tipo_anuncio;
	}
	public function getTipoElementoGrande(){
		$query= "SELECT * FROM la_tipo_elemento WHERE id_formato = 1";
		$tipo_elemento= getFn($query, 'all');
		return $tipo_elemento;
	}
	public function getTipoElementoFrecuencia(){
		$query= "SELECT * FROM la_tipo_elemento WHERE id_formato = 2";
		$tipo_elemento= getFn($query, 'all');
		return $tipo_elemento;
	}
	public function getVelocidadTransito(){
		$query= "SELECT * FROM la_velocidad_transito";
		$velocidad_transito= getFn($query, 'all');
		return $velocidad_transito;
	}
	public function getProvincias(){
		$query= "SELECT sp.* FROM sys_provincia sp INNER JOIN la_zona lz WHERE lz.id_sys_provincia = sp.id_sys_provincia ORDER BY sp.nombre";
		$provincia= getFn($query, 'all');
		return $provincia;
	}
}
?>