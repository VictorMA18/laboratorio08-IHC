<?php
require('ln.php'); 

$servidor = "localhost";
$usuario = "cursophp";
$password = "123"; 
$base_datos = "lindavista";

// Conexión a la base de datos
$conexion = new mysqli($servidor, $usuario, $password, $base_datos);
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}


$conexion -> set_charset("utf8");
$mensaje = '';
$resultado = []; 

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $consulta = $_POST['consulta'] ?? ''; 

    if (empty($consulta)) {
        $mensaje = "Debe introducir una consulta.";
    } else {
        $sql = ''; // Variable donde se almacenará la sentencia SQL

        // Procesar la consulta usando la función del archivo ln.php
        if (procesa_consulta($consulta, $conexion, $sql)) {
            // Ejecutar la consulta SQL generada
            $query_result = $conexion->query($sql);

            if ($query_result && $query_result->num_rows > 0) {
                // Almacenar los resultados en un arreglo
                $resultado = $query_result->fetch_all(MYSQLI_ASSOC);
            } else {
                $mensaje = "No hay viviendas disponibles.";
            }
        } else {
            $mensaje = "La consulta no es correcta.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscador de Viviendas</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-green-500 min-w-96">
    <div class="container mx-auto px-4 py-8 mt-10">
        <div class="bg-white shadow-md rounded-lg px-6 py-3 pb-6">
            <h1 class="text-4xl font-bold my-7 flex items-center justify-center text-center">Buscador de viviendas</h1>
            <!-- Formulario de búsqueda -->
            <form method="POST" action="" class="flex mb-4 items-center justify-center py-2">
                <input type="text" name="consulta" placeholder="Escriba su búsqueda aquí..." class="flex w-1/2 px-4 py-2 rounded-l-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" value="<?= htmlspecialchars($_POST['consulta'] ?? '') ?>">
                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white font-bold mx-2 px-4 py-2 rounded-r-lg">Buscar</button>
            </form>
            <?php if (!empty($mensaje)): ?>
                <p class="text-red-500 mb-4"><?= htmlspecialchars($mensaje) ?></p>
            <?php endif; ?>
            <?php if (!empty($resultado)): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <?php foreach ($resultado as $vivienda): ?>
                        <div class="bg-white drop-shadow-xl rounded-lg overflow-hidden h-max">
                            <?php if (!empty($vivienda['foto'])): ?>
                                <img src="./fotos/<?= htmlspecialchars($vivienda['foto']) ?>" 
                                    alt="Imagen de la vivienda" 
                                    class="w-full h-96 object-cover">
                            <?php else: ?>
                                <div class="w-full h-58 bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-500">No hay imagen disponible</span>
                                </div>
                            <?php endif; ?>
                            <div class="p-4">
                                <h3 class="text-lg font-bold mb-2"><?= htmlspecialchars($vivienda['tipo']) ?></h3>
                                <p class="text-gray-600 mb-2"><?= htmlspecialchars($vivienda['zona']) ?></p>
                                <p class="text-gray-600 mb-2">Dormitorios: <?= $vivienda['ndormitorios'] ?></p>
                                <p class="text-gray-600 mb-2">Metros cuadrados: <?= $vivienda['metros_cuadrados'] ?> m²</p>
                                <p class="text-blue-500 font-bold">$<?= number_format($vivienda['precio'], 2) ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php
// Cerrar la conexión a la base de datos
$conexion->close();
?>
