<?php

declare(strict_types=1);

namespace App\Models;

class Estudiante extends Usuario{
    public function getPorcentajeDescuento(): float{
        return 0.20;
    }
}

?>