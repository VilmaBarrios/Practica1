<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

session_start();

use App\Models\Estudiante;
use App\Models\Docente;
use App\Models\Visitante;
use App\Models\Laptop;
use App\Models\Proyector;
use App\Models\Camara;
use App\Services\ReservaService;
use App\Exceptions\ReservaException;

// Arreglos asociativos y multidimensionales iniciales de equipos[cite: 1]
if (!isset($_SESSION['catalog_equipos'])) {
    $_SESSION['catalog_equipos'] = [
        'LAP' => ['codigo' => 'LAP', 'nombre' => 'Laptop Dell XPS', 'categoria' => 'Computo', 'precio_por_hora' => 5.00, 'existencias' => 10, 'clase' => Laptop::class],
        'PRO' => ['codigo' => 'PRO', 'nombre' => 'Proyector Epson 4K', 'categoria' => 'Audiovisual', 'precio_por_hora' => 3.50, 'existencias' => 5, 'clase' => Proyector::class],
        'CAM' => ['codigo' => 'CAM', 'nombre' => 'Cámara Canon EOS', 'categoria' => 'Fotografía', 'precio_por_hora' => 8.00, 'existencias' => 3, 'clase' => Camara::class],
    ];
}

if (!isset($_SESSION['historial_reservas'])) {
    $_SESSION['historial_reservas'] = [];
}

$mensajeError = null;
$mensajeExito = null;

// Procesamiento del Formulario[cite: 1]
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $correo = filter_input(INPUT_POST, 'correo', FILTER_VALIDATE_EMAIL);
    $tipoUsuario = $_POST['tipo_usuario'] ?? '';
    $codigoEquipo = $_POST['codigo_equipo'] ?? '';
    $cantidad = filter_input(INPUT_POST, 'cantidad', FILTER_VALIDATE_INT);
    $horas = filter_input(INPUT_POST, 'horas', FILTER_VALIDATE_INT);

    // Validación en el servidor[cite: 1]
    if (!$nombre || !$correo || !$tipoUsuario || !$codigoEquipo || !$cantidad || $cantidad <= 0 || !$horas || $horas <= 0) {
        $mensajeError = "Por favor, complete todos los campos requeridos con valores válidos.";
    } elseif (!array_key_exists($codigoEquipo, $_SESSION['catalog_equipos'])) {
        $mensajeError = "El equipo seleccionado no existe.";
    } else {
        try {
            // Instanciar usuario según tipo[cite: 1]
            $idUsuario = uniqid('USR-');
            $usuarioObj = match ($tipoUsuario) {
                'estudiante' => new Estudiante($idUsuario, $nombre, $correo),
                'docente'    => new Docente($idUsuario, $nombre, $correo),
                'visitante'  => new Visitante($idUsuario, $nombre, $correo),
                default      => throw new InvalidArgumentException("Tipo de usuario no válido")
            };

            // Instanciar equipo desde arreglo asociativo[cite: 1]
            $datosEquipo = $_SESSION['catalog_equipos'][$codigoEquipo];
            $claseEquipo = $datosEquipo['clase'];
            $equipoObj = $claseEquipo::crearDesdeArreglo($datosEquipo);

            // Servicio de Reserva[cite: 1]
            $service = new ReservaService();
            $reserva = $service->reservar($usuarioObj, $equipoObj, $cantidad, $horas);

            // Actualizar existencias en la sesión
            $_SESSION['catalog_equipos'][$codigoEquipo]['existencias'] = $equipoObj->getExistencias();

            // Guardar en historial de sesión[cite: 1]
            $_SESSION['historial_reservas'][] = $reserva;
            $mensajeExito = "¡Reserva realizada con éxito!";

        } catch (ReservaException $e) {
            // Captura de excepción sin detener la aplicación[cite: 1]
            $mensajeError = "Error en la reserva: " . $e->getMessage();
        } catch (\Throwable $e) {
            $mensajeError = "Error procesando la solicitud: " . $e->getMessage();
        } finally {
            // Log o acción final opcional[cite: 1]
            $logProceso = "Procesamiento finalizado a las " . date('H:i:s');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Reservas Universitarias</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f4f4f9; }
        .container { max-width: 900px; margin: auto; background: white; padding: 20px; border-radius: 8px; }
        .alert { padding: 10px; margin-bottom: 15px; border-radius: 4px; }
        .danger { background: #f8d7da; color: #721c24; }
        .success { background: #d4edda; color: #155724; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
<div class="container">
    <h2>Gestión de Reservas de Equipos Tecnológicos</h2>

    <?php if ($mensajeError): ?>
        <div class="alert danger"><?= htmlspecialchars($mensajeError) ?></div>
    <?php endif; ?>
    <?php if ($mensajeExito): ?>
        <div class="alert success"><?= htmlspecialchars($mensajeExito) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <h3>Formulario de Reserva</h3>
        <label>Nombre Completo:</label><br>
        <input type="text" name="nombre" required><br><br>

        <label>Correo Electrónico:</label><br>
        <input type="email" name="correo" required><br><br>

        <label>Tipo de Usuario:</label><br>
        <select name="tipo_usuario" required>
            <option value="estudiante">Estudiante (20% Desc.)</option>
            <option value="docente">Docente (10% Desc.)</option>
            <option value="visitante">Visitante (Tarifa Completa)</option>
        </select><br><br>

        <label>Equipo:</label><br>
        <select name="codigo_equipo" required>
            <?php foreach ($_SESSION['catalog_equipos'] as $eq): ?>
                <option value="<?= $eq['codigo'] ?>">
                    <?= $eq['nombre'] ?> - $<?= number_format($eq['precio_por_hora'], 2) ?>/hr (Disponibles: <?= $eq['existencias'] ?>)
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label>Cantidad:</label><br>
        <input type="number" name="cantidad" min="1" value="1" required><br><br>

        <label>Horas de Uso:</label><br>
        <input type="number" name="horas" min="1" value="1" required><br><br>

        <button type="submit">Procesar Reserva</button>
    </form>

    <hr>

    <h3>Historial de Reservas (Almacenado en Sesión)</h3>
    <?php if (!empty($_SESSION['historial_reservas'])): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Usuario</th>
                    <th>Tipo</th>
                    <th>Equipo</th>
                    <th>Cant.</th>
                    <th>Horas</th>
                    <th>Precio/h</th>
                    <th>Subtotal</th>
                    <th>Descuento</th>
                    <th>Mant.</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($_SESSION['historial_reservas'] as $r): ?>
                    <tr>
                        <td><?= $r->getIdReserva() ?></td>
                        <td><?= htmlspecialchars($r->getUsuario()->getNombre()) ?></td>
                        <td><?= (new \ReflectionClass($r->getUsuario()))->getShortName() ?></td>
                        <td><?= htmlspecialchars($r->getEquipo()->getNombre()) ?></td>
                        <td><?= $r->getCantidad() ?></td>
                        <td><?= $r->getHoras() ?></td>
                        <td>$<?= number_format($r->getEquipo()->getPrecioPorHora(), 2) ?></td>
                        <td>$<?= number_format($r->getSubtotal(), 2) ?></td>
                        <td>$<?= number_format($r->getDescuento(), 2) ?></td>
                        <td>$<?= number_format(\App\Models\Reserva::CARGO_MANTENIMIENTO, 2) ?></td>
                        <td><strong>$<?= number_format($r->getTotal(), 2) ?></strong></td>
                        <td><?= $r->getEstado()->obtenerEtiqueta() ?></td>
                        <td><?= $r->getFechaRegistro() ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No se han registrado reservas aún.</p>
    <?php endif; ?>
</div>
</body>
</html>