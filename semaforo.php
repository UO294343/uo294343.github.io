<?php 
class Record {
    private $server;
    private $user;
    private $pass;
    private $dbname;
    private $conn;

    public function __construct() {
        $this->server = "localhost";
        $this->user = "DBUSER2024";
        $this->pass = "DBPSWD2024";
        $this->dbname = "records";
        $this->connectToDatabase();
    }

    private function connectToDatabase() {
        $this->conn = new mysqli($this->server, $this->user, $this->pass, $this->dbname);
    }

    public function saveRecord($nombre, $apellidos, $nivel, $tiempo) {
        $stmt = $this->conn->prepare("INSERT INTO registro (nombre, apellidos, nivel, tiempo) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssdd", $nombre, $apellidos, $nivel, $tiempo);
        $stmt->execute();
        $stmt->close();
    }

    public function getTopRecords($nivel) {
        $stmt = $this->conn->prepare("SELECT nombre, apellidos, tiempo FROM registro WHERE nivel = ? ORDER BY tiempo ASC LIMIT 10");
        $stmt->bind_param("d", $nivel);
        $stmt->execute();
        $result = $stmt->get_result();

        $records = [];
        while ($row = $result->fetch_assoc()) {
            $records[] = $row;
        }

        $stmt->free_result();
        $stmt->close();
        return $records;
    }

    public function __destruct() {
        $this->conn->close();
    }
}

$topRecordsHTML = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $nivel = $_POST['nivel'] ?? '';
    $tiempo = isset($_POST['tiempo_reaccion']) ? floatval($_POST['tiempo_reaccion']) : 0;
    
    $record = new Record();
    $record->saveRecord($nombre, $apellidos, $nivel, $tiempo);

    // Obtener los top records
    $topRecords = $record->getTopRecords($nivel);

    // Generar solo la lista HTML para los top records
    $topRecordsHTML = "<section><h2>Top 10 Records</h2><ol>";
    foreach ($topRecords as $record) {
        $topRecordsHTML .= "<li>{$record['nombre']} {$record['apellidos']} - {$record['tiempo']} segundos</li>";
    }
    $topRecordsHTML .= "</ol></section>";
}   
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="author" content="Sergio Riesco Collar"/>
    <meta name="description" content="Juego de Tiempo de Reacción"/>
    <meta name ="keywords" content ="f1, juego" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <link rel="stylesheet" href="estilo/estilo.css"/>
    <link rel="stylesheet" href="estilo/layout.css"/>
    <link rel="stylesheet" href="estilo/semaforo_grid.css"/>
    <link rel="icon" href="multimedia/imagenes/favicon.ico"/>
    <script src="js/semaforo.js"></script>
    <title>Juego de Tiempo de Reacción - Semáforo</title>
</head>
<body>
    <header> 
        <a href="index.html"><h1>F1 Desktop</h1></a>
        <nav>
            <a href="index.html">Inicio</a> 
            <a href="piloto.html">Piloto</a> 
            <a href="noticias.html">Noticias</a> 
            <a href="calendario.html">Calendario</a> 
            <a href="metereologia.html">Metereología</a> 
            <a href="circuito.html">Circuito</a> 
            <a href="viajes.php">Viajes</a> 
            <a href="juegos.html" class="active">Juegos</a> 
        </nav>   
    </header>
    <p>Estás en: <a href="index.html">Inicio</a> >> <a href="juegos.html">Juegos</a> >> Reacción</p>
    <main>
        <section>
            <h2>Explora nuestros juegos</h2>
            <ul>
                <li><a href="memoria.html">Juego de Memoria</a></li>
                <li><a href="semaforo.php">Juego de Tiempo de Reacción</a></li>
                <li><a href="quiz.html">Quiz F1</a></li>
                <li><a href="php/strategy_manager.php">F1 Strategy Manager</a></li>
            </ul>
        </section>
        <script> s = new Semaforo(); </script>
        <?php echo $topRecordsHTML;?>
    </main>

    
</body>
</html>