<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Models\Usuario;
use App\Models\Equipo;
use App\Models\Reserva;

interface ReservableInterface
{
    public function reservar(Usuario $usuario, Equipo $equipo, int $cantidad, int $horas): Reserva;
}