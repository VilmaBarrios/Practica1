<?php

declare(strict_types=1);

namespace App\Models;

abstract class Usuario
{
    // Uso de readonly en una propiedad
    public readonly string $id;

    public function __construct(
        string $id,
        protected string $nombre,
        protected string $correo
    ) {
        $this->id = $id;
        $this->correo = $this->normalizarCorreo($correo);
    }

    public function normalizarCorreo(string $correo): string
    {
        return strtolower(trim($correo));
    }

    public function getNombre(): string
    {
        return $this->nombre;
    }

    public function getCorreo(): string
    {
        return $this->correo;
    }

    // Método polimórfico para obtener porcentaje de descuento
    abstract public function getPorcentajeDescuento(): float;
}