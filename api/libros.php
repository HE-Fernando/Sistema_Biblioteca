<<?php
include("../config/database.php");

$q = $_GET['q'] ?? '';

$stmt = $pdo->prepare("SELECT id, titulo, autor, isbn, estado FROM libros WHERE titulo LIKE :q OR autor LIKE :q LIMIT 20");
$stmt->bindValue(':q', "%$q%");
$stmt->execute();
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
<?php
include("../config/database.php");

// Captura de filtros
$titulo = $_GET['titulo'] ?? '';
$autor = $_GET['autor'] ?? '';
$isbn = $_GET['isbn'] ?? '';
$categoria = $_GET['categoria'] ?? '';

// Construcción dinámica del WHERE
$condiciones = [];
$params = [];

if ($titulo !== '') { $condiciones[] = "titulo LIKE :titulo"; $params[':titulo'] = "%$titulo%"; }
if ($autor !== '') { $condiciones[] = "autor LIKE :autor"; $params[':autor'] = "%$autor%"; }
if ($isbn !== '') { $condiciones[] = "isbn LIKE :isbn"; $params[':isbn'] = "%$isbn%"; }
if ($categoria !== '') { $condiciones[] = "categoria LIKE :categoria"; $params[':categoria'] = "%$categoria%"; }

$where = $condiciones ? 'WHERE ' . implode(' AND ', $condiciones) : '';

$sql = "SELECT id, titulo, autor, isbn, categoria, estado FROM libros $where ORDER BY id DESC LIMIT 30";
$stmt = $pdo->prepare($sql);

foreach ($params as $key => $val) {
  $stmt->bindValue($key, $val);
}

$stmt->execute();
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($resultados);
