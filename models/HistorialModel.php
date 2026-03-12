<?php
require_once '../db/db.php';

class HistorialModel {
    private $db;

    public function __construct (){
        $this->db=db::connect();
    }

    #Añadir a la BD cada busqueda realizada 
    public function anadirdato($ciudad, $lat, $lon, $tipo) {
        try {
            $stmt = $this->db->prepare("Insert into historial (ciudad, latitud, longitud, tipo) values (:ciudad, :lat, :lon, :tipo)");
            
            $stmt->bindParam(':ciudad', $ciudad);
            $stmt->bindParam(':lat', $lat);
            $stmt->bindParam(':lon', $lon);
            $stmt->bindParam(':tipo', $tipo);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            $e->getMessage();
        }

    }

    # Ver todo el historial de busquedas
    public function VerHistorial(){
        $stmt= $this->db->prepare("Select * from historial");
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $stmt->execute();

        return $stmt->fetchAll();
    }
}