<?php

declare(strict_types=1);

namespace App\Models;

class Docente extends Usuario{
    public function getPorcentajeDescuento(): float{
        return 0.10;
    }
}

?>