<?php

declare(strict_types=1);

namespace App\Services;

use App\Interfaces\ReservableInterface;
use App\Models\Usuario;
use App\Models\Equipo;
use App\Models\Reserva;
use App\Exceptions\ReservaException;

class ReservaService implements ReservableInterface
{
    #[\Override]
    public function reservar(Usuario $usuario, Equipo $equipo, int $cantidad, int $horas): Reserva
    {
        // Verificar y reducir existencias (lanza ReservaException si falla)[cite: 1]
        $equipo->reducirExistencias($cantidad);
        return new Reserva($usuario, $equipo, $cantidad, $horas);
    }
}