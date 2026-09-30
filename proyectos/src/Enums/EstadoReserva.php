<?php

declare(strict_types=1);

namespace App\Enums;

enum EstadoReserva: string{
    case PENDIENTE = 'pendiente';
    case APROBADA = 'Aprobada';
    case CANCELADA = 'Cancelada';

    public function obtenerEtiqueta(): string
    {
        return match ($this){
            self::PENDIENTE =>'Pendiente de confirmacion',
            self::APROBADA =>'Reserva Aprobada',
            self::CANCELADA => 'Reserva Cancelada'
        };
    }
}





?>