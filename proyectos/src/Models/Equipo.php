<?php

declare(strict_types=1);

namespace App\Models;

use App\Exceptions\ReservaException;

abstract class Equipo
{
    public function __construct(
        protected string $codigo,
        protected string $nombre,
        protected string $categoria,
        protected float $precioPorHora,
        protected int $existencias
    ) {}

    // Método de creación heredable con static::
    public static function crearDesdeArreglo(array $datos): static
    {
        return new static(
            $datos['codigo'],
            $datos['nombre'],
            $datos['categoria'],
            (float)$datos['precio_por_hora'],
            (int)$datos['existencias']
        );
    }

    public function verificarExistencias(int $cantidad): bool
    {
        return $this->existencias >= $cantidad;
    }

    public function reducirExistencias(int $cantidad): void
    {
        if (!$this->verificarExistencias($cantidad)) {
            throw new ReservaException("No hay suficientes unidades del equipo '{$this->nombre}'. Disponibles: {$this->existencias}");
        }
        $this->existencias -= $cantidad;
    }

    public function getCodigo(): string { return $this->codigo; }
    public function getNombre(): string { return $this->nombre; }
    public function getCategoria(): string { return $this->categoria; }
    public function getPrecioPorHora(): float { return $this->precioPorHora; }
    public function getExistencias(): int { return $this->existencias; }
}