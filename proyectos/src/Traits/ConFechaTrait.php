<?php

declare(strict_types=1);

namespace App\Traits;

use DateTimeImmutable;

trait ConFechaTrait{
    private DateTimeImmutable $fechaRegistro;

    public function registrarFecha(): void{
        $this->fechaRegistro = new DateTimeImmutable();

    }
    public function getFechaRegistro(): string{
        return $this->fechaRegistro->format('Y-m-d H:i:s');
    }
}


?>