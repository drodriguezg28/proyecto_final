<?php

    class MeteoModel {

        private string $apiKey;


        public function __construct(string $apiKey) {
        $this->apiKey = $apiKey;
        }

        # Obtención de Coordenadas
        public function ObtenerCoordenadas(string $ciudad): ?array {
            $ciudadCodificada = urlencode($ciudad);
            $url = "http://api.openweathermap.org/geo/1.0/direct?q={$ciudadCodificada}&limit=1&appid={$this->apiKey}";

            $respuestaapi = file_get_contents($url);
            $datos = json_decode($respuestaapi, true);

            if (!empty($datos)) {
                return [
                    'lat' => $datos[0]['lat'],
                    'lon' => $datos[0]['lon']
                ];
            }

            return null;
        }

        # Datos Climaticos actualmente
        public function DatosClimaticos(float $lat, float $lon): ?array {
            $url = "https://api.openweathermap.org/data/2.5/weather?lat={$lat}&lon={$lon}&appid={$this->apiKey}&units=metric&lang=es";

            $respuestaapi = file_get_contents($url);
            $datos = json_decode($respuestaapi, true);

            if (!empty($datos)) {
                return [
                    'temperatura' => $datos['main']['temp'],
                    'descripcion' => $datos['weather'][0]['description'],
                    'icono' => $datos['weather'][0]['icon']
                ];
            }

            return null;
        }
        
        # Datos Climaticos por Horas
        public function DatosClimaticosHoras(float $lat, float $lon): ?array {
            $url = "https://api.openweathermap.org/data/2.5/forecast?lat={$lat}&lon={$lon}&appid={$this->apiKey}&units=metric&lang=es&cnt=8";

            $respuestaapi = file_get_contents($url);
            $datos = json_decode($respuestaapi, true);

            if (empty($datos['list'])) {
                return null;
            }

            $lista = [];
            foreach ($datos['list'] as $item) {
                $lista[] = [
                    'hora'        => date('H:i', $item['dt']),
                    'temperatura' => round($item['main']['temp']),
                    'descripcion' => $item['weather'][0]['description'],
                    'icono'       => $item['weather'][0]['icon'],
                ];
            }

            return ['lista_horas' => $lista];
        }

        # Datos Climaticos de la semana
        public function DatosClimaticosSemanales(float $lat, float $lon): ?array {
            $url = "https://api.openweathermap.org/data/2.5/forecast?lat={$lat}&lon={$lon}&appid={$this->apiKey}&units=metric&lang=es&cnt=40";

            $respuestaapi = file_get_contents($url);
            $datos = json_decode($respuestaapi, true);

            if (empty($datos['list'])) {
                return null;
            }

            $diasAgrupados = [];
            foreach ($datos['list'] as $item) {
                $fecha = date('d/m', $item['dt']);

                if (!isset($diasAgrupados[$fecha])) {
                    $diasAgrupados[$fecha] = [
                        'temps'       => [],
                        'descripcion' => $item['weather'][0]['description'],
                        'icono'       => $item['weather'][0]['icon'],
                    ];
                }

                $diasAgrupados[$fecha]['temps'][] = $item['main']['temp'];
            }

            $lista = [];
            foreach ($diasAgrupados as $fecha => $info) {
                $lista[] = [
                    'fecha'       => $fecha,
                    'temperatura' => round(array_sum($info['temps']) / count($info['temps'])),
                    'descripcion' => $info['descripcion'],
                    'icono'       => $info['icono'],
                ];
            }

            return ['lista_dias' => array_slice($lista, 0, 7)];
        }

        # Muestra los resultados de ciudades encontradas (Máximo 5)
        public function BuscarOpcionesCiudad(string $ciudad): ?array {
            $ciudadCodificada = urlencode($ciudad);
    
            $url = "http://api.openweathermap.org/geo/1.0/direct?q={$ciudadCodificada}&limit=5&appid={$this->apiKey}";

            $respuestaapi = file_get_contents($url);
            $datos = json_decode($respuestaapi, true);

            if (!empty($datos)) {
                return $datos;
            }

            return null;
        }
    }
    


?>