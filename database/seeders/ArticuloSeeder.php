<?php

namespace Database\Seeders;

use App\Models\Articulo;
use App\Models\Rubro;
use Illuminate\Database\Seeder;

class ArticuloSeeder extends Seeder
{
    public function run(): void
    {
        $rubrosNombres = [
            'Electrónica',
            'Periféricos',
            'Mobiliario',
            'Oficina',
            'Papelería',
        ];

        $rubros = [];
        foreach ($rubrosNombres as $nombre) {
            $rubros[$nombre] = Rubro::firstOrCreate(['titulo' => $nombre])->id;
        }

        $articulos = [
            ['Laptop Pro 14"', 'Portátil 16GB RAM SSD 512GB', 'Electrónica', 18500.00, 12, 'Disponible'],
            ['Monitor IPS 24"', 'Pantalla Full HD 75Hz con HDMI', 'Electrónica', 3250.00, 18, 'Disponible'],
            ['Monitor Curvo 27"', 'Pantalla QHD 144Hz para diseño', 'Electrónica', 6400.00, 0, 'Agotado'],
            ['Tablet Gráfica 10"', 'Tableta digitalizadora con lápiz óptico', 'Electrónica', 2490.00, 9, 'Disponible'],
            ['Proyector LED HD', 'Proyector portátil 3500 lúmenes', 'Electrónica', 5120.00, 4, 'Disponible'],
            ['Router Wi-Fi 6', 'Enrutador doble banda de alta velocidad', 'Electrónica', 1890.00, 15, 'Disponible'],
            ['Disco SSD Externo 1TB', 'Almacenamiento portátil USB-C', 'Electrónica', 1650.00, 0, 'Agotado'],

            ['Teclado Mecánico RGB', 'Switches táctiles en español', 'Periféricos', 1290.00, 22, 'Disponible'],
            ['Mouse Inalámbrico Ergonómico', 'Sensor óptico 2400 DPI recargable', 'Periféricos', 540.00, 35, 'Disponible'],
            ['Audífonos Diadema USB', 'Micrófono con cancelación de ruido', 'Periféricos', 780.00, 19, 'Disponible'],
            ['Webcam Full HD 1080p', 'Cámara con enfoque automático', 'Periféricos', 890.00, 0, 'Agotado'],
            ['Hub USB-C 7 en 1', 'Adaptador con HDMI, Ethernet y SD', 'Periféricos', 620.00, 27, 'Disponible'],
            ['Micrófono Condensador USB', 'Ideal para clases y presentaciones', 'Periféricos', 1450.00, 7, 'Disponible'],
            ['Base Enfriadora Laptop', 'Soporte metálico con doble ventilador', 'Periféricos', 430.00, 14, 'Disponible'],

            ['Silla Ergonómica Malla', 'Soporte lumbar ajustable y ruedas', 'Mobiliario', 3400.00, 10, 'Disponible'],
            ['Escritorio Minimalista 120cm', 'Cubierta de madera y estructura de acero', 'Mobiliario', 2890.00, 6, 'Disponible'],
            ['Archivero Metálico 3 Cajones', 'Con cerradura de seguridad', 'Mobiliario', 2650.00, 0, 'Agotado'],
            ['Lámpara de Escritorio LED', 'Intensidad regulable y puerto USB', 'Mobiliario', 490.00, 24, 'Disponible'],
            ['Estante Modular 4 Niveles', 'Organizador resistente para oficina', 'Mobiliario', 1750.00, 8, 'Disponible'],
            ['Silla Visitante Plegable', 'Estructura ligera acolchada', 'Mobiliario', 680.00, 30, 'Disponible'],
            ['Mesa Plegable Auxiliar', 'Superficie lavable de 150cm', 'Mobiliario', 1520.00, 5, 'Disponible'],

            ['Impresora Multifuncional', 'Inyección continua Wi-Fi a color', 'Oficina', 4390.00, 11, 'Disponible'],
            ['Trituradora de Papel', 'Corte en partículas hasta 8 hojas', 'Oficina', 1320.00, 0, 'Agotado'],
            ['Engrapadora Metálica Uso Rudo', 'Capacidad de hasta 50 hojas', 'Oficina', 260.00, 40, 'Disponible'],
            ['Calculadora Científica', '240 funciones con pantalla de 2 líneas', 'Oficina', 310.00, 28, 'Disponible'],
            ['Pizarrón Blanco 90x60cm', 'Marco de aluminio con charola', 'Oficina', 590.00, 13, 'Disponible'],
            ['Organizador de Escritorio', 'Bandejas metálicas para documentos', 'Oficina', 295.00, 21, 'Disponible'],
            ['Perforadora Doble Ajustable', 'Base antideslizante metálica', 'Oficina', 185.00, 0, 'Agotado'],

            ['Paquete 500 Hojas Carta', 'Papel bond blanco 75g', 'Papelería', 115.00, 80, 'Disponible'],
            ['Cuaderno Profesional Cuadro', '100 hojas espiral metálico', 'Papelería', 58.00, 65, 'Disponible'],
            ['Set 12 Plumones Pizarrón', 'Colores surtidos borrado en seco', 'Papelería', 195.00, 34, 'Disponible'],
            ['Caja 12 Bolígrafos Gel', 'Punto fino 0.5mm tinta negra', 'Papelería', 140.00, 50, 'Disponible'],
            ['Carpeta Registradora Carta', 'Pasta rígida lomo ancho', 'Papelería', 85.00, 42, 'Disponible'],
            ['Paquete Notas Adhesivas', '6 bloques de 100 hojas colores pastel', 'Papelería', 95.00, 0, 'Agotado'],
            ['Juego de Marcatextos Pastel', 'Estuche con 6 tonos suaves', 'Papelería', 110.00, 38, 'Disponible'],
        ];

        $total = count($articulos);
        foreach ($articulos as $indice => [$titulo, $detalle, $rubro, $precio, $stock, $disponibilidad]) {
            $fecha = now()->subDays($total - $indice);

            Articulo::create([
                'titulo' => $titulo,
                'detalle' => $detalle,
                'rubro_id' => $rubros[$rubro],
                'precio' => $precio,
                'stock' => $stock,
                'disponibilidad' => $disponibilidad,
                'created_at' => $fecha,
                'updated_at' => $fecha,
            ]);
        }
    }
}
