<?php
    require_once '../views/View.php';
    class MeteoController {

        private MeteoModel $MeteoModel;
        private HistorialModel $HistorialModel;

        public function __construct(MeteoModel $MeteoModel, HistorialModel $HistorialModel) {
            $this->MeteoModel = $MeteoModel;
            $this->HistorialModel = $HistorialModel;
        }

        # Cargar resultados.php con las opciones encontradas del nombre de ciudad buscado
        public function buscarOpciones(string $ciudad) {
            $opciones = $this->MeteoModel->BuscarOpcionesCiudad($ciudad);

            if ($opciones === null || empty($opciones)) {
                $mensajeError = "No hemos podido encontrar ninguna ciudad llamada: ".htmlspecialchars($ciudad);
                $this->cargarVista('resultados', null, $ciudad, $mensajeError);
                return;
            }

            $this->cargarVista('resultados', $opciones, $ciudad);
        }

        # Función que procesa la busqueda a través de las coordenadas, llama a una función u otra de MeteoModel y añade a la base de datos la busqueda
        public function procesarBusqueda(string $ciudad, string $vistaDestino = 'actual', $lat = null, $lon = null) {

            if ($lat === null || $lon === null) {
                $coordenadas = $this->MeteoModel->ObtenerCoordenadas($ciudad);

                if ($coordenadas === null) {
                    $mensajeError = "No hemos podido encontrar la ciudad: ".htmlspecialchars($ciudad);
                    $this->cargarVista('actual', null, $ciudad, $mensajeError);
                    return; 
                }
                $lat = $coordenadas['lat'];
                $lon = $coordenadas['lon'];
            }

            $datosClima = match($vistaDestino) {
                'horas'  => $this->MeteoModel->DatosClimaticosHoras($lat, $lon),
                'semana' => $this->MeteoModel->DatosClimaticosSemanales($lat, $lon),
                'actual'  => $this->MeteoModel->DatosClimaticos($lat, $lon),
            };  
            
            $this->HistorialModel->anadirdato($ciudad, $lat, $lon, $vistaDestino);
            
            $this->cargarVista($vistaDestino, $datosClima, $ciudad);
            }
                
                
        # Carga una vista u otra
        private function cargarVista(string $vista, $datosClima, string $ciudad, $mensajeError = null) {
            $data = [
                'datosClima'    => $datosClima,
                'ciudad'        => $ciudad,
                'mensajeError'  => $mensajeError,
            ];

            switch ($vista) {
                case 'horas':
                    View::show('horas', $data);
                    break;
                case 'semana':
                    View::show('semana', $data);
                    break;
                case 'resultados':
                    View::show('resultados', $data);
                    break;
                case 'actual':
                default:
                    View::show('actual', $data);
                    break;
            }
        }
    }
?>