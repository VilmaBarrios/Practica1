<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EstadoReserva;
use App\Traits\ConFechaTrait;

class Reserva
{
    use ConFechaTrait;

    // Constante fija de mantenimiento
    public const CARGO_MANTENIMIENTO = 2.50;

    // Contador static con self::[cite: 1]
    private static int $contadorReservas = 0;

    private int $idReserva;
    private float $subtotal;
    private float $descuento;
    private float $total;
    private EstadoReserva $estado;

    public function __construct(
        private Usuario $usuario,
        private Equipo $equipo,
        private int $cantidad,
        private int $horas
    ) {
        self::$contadorReservas++;
        $this->idReserva = self::$contadorReservas;
        $this->estado = EstadoReserva::APROBADA;
        $this->registrarFecha();
        $this->calcularCostos();
    }

    private function calcularCostos(): void
    {
        $this->subtotal = $this->equipo->getPrecioPorHora() * $this->cantidad * $this->horas;
        $this->descuento = $this->subtotal * $this->usuario->getPorcentajeDescuento();
        $this->total = ($this->subtotal - $this->descuento) + self::CARGO_MANTENIMIENTO;
    }

    // Uso de método mágico __clone()[cite: 1]
    public function __clone()
    {
        self::$contadorReservas++;
        $this->idReserva = self::$contadorReservas;
    }

    // Uso del método mágico __toString()[cite: 1]
    public function __toString(): string
    {
        return "Reserva #{$this->idReserva} - Usuario: {$this->usuario->getNombre()} | Equipo: {$this->equipo->getNombre()} | Total: $" . number_format($this->total, 2);
    }

    // Getters
    public function getIdReserva(): int { return $this->idReserva; }
    public function getUsuario(): Usuario { return $this->usuario; }
    public function getEquipo(): Equipo { return $this->equipo; }
    public function getCantidad(): int { return $this->cantidad; }
    public function getHoras(): int { return $this->horas; }
    public function getSubtotal(): float { return $this->subtotal; }
    public function getDescuento(): float { return $this->descuento; }
    public function getTotal(): float { return $this->total; }
    public function getEstado(): EstadoReserva { return $this->estado; }
    public static function getContadorReservas(): int { return self::$contadorReservas; }
}