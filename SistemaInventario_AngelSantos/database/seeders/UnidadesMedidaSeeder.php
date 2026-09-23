<?php

namespace Database\Seeders;

use App\Models\UnidadMedida;
use Illuminate\Database\Seeder;

class UnidadesMedidaSeeder extends Seeder
{
    public function run(): void
    {
        $unidades = [
            ['nombre' => 'Unidad', 'abreviatura' => 'UND'],
            ['nombre' => 'Caja', 'abreviatura' => 'CJ'],
            ['nombre' => 'Kilogramo', 'abreviatura' => 'KG'],
            ['nombre' => 'Gramo', 'abreviatura' => 'G'],
            ['nombre' => 'Litro', 'abreviatura' => 'L'],
            ['nombre' => 'Mililitro', 'abreviatura' => 'ML'],
            ['nombre' => 'Metro', 'abreviatura' => 'M'],
            ['nombre' => 'Paquete', 'abreviatura' => 'PAQ'],
            ['nombre' => 'Docena', 'abreviatura' => 'DOC'],
            ['nombre' => 'Galón', 'abreviatura' => 'GAL'],
        ];

        foreach ($unidades as $u) {
            UnidadMedida::updateOrCreate(
                ['abreviatura' => $u['abreviatura']],
                ['nombre' => $u['nombre']]
            );
        }
    }
}
