<?php
// --- LÓGICA DE PHP ---

// Inicializamos las variables
$fixedCosts = 0;
$variableCostPerUnit = 0;
$salePrice = 0;
$unitsSold = 0;
$show_results = false;
$feedback_title = '';
$feedback_message = '';
$feedback_icon = 'activity';
$feedback_color = 'indigo';

// Verificamos si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recogemos y limpiamos los datos del formulario.
    $fixedCosts = isset($_POST['fixedCosts']) ? (float)$_POST['fixedCosts'] : 0;
    $variableCostPerUnit = isset($_POST['variableCostPerUnit']) ? (float)$_POST['variableCostPerUnit'] : 0;
    $salePrice = isset($_POST['salePrice']) ? (float)$_POST['salePrice'] : 0;
    $unitsSold = isset($_POST['unitsSold']) ? (int)$_POST['unitsSold'] : 0;

    if ($salePrice > 0 || $unitsSold > 0 || $fixedCosts > 0) {
        // Realizamos los cálculos
        $totalVariableCosts = $variableCostPerUnit * $unitsSold;
        $totalCosts = $fixedCosts + $totalVariableCosts;
        $totalRevenue = $salePrice * $unitsSold;
        $profit = $totalRevenue - $totalCosts;

        // Calculamos el punto de equilibrio
        $breakEvenPoint = 0;
        $marginPerUnit = $salePrice - $variableCostPerUnit;
        if ($marginPerUnit > 0) {
            $breakEvenPoint = ceil($fixedCosts / $marginPerUnit);
        }

        // --- LÓGICA DEL DIAGNÓSTICO ---
        if ($profit > 0) {
            $feedback_title = '¡Excelente! Negocio Rentable';
            $feedback_icon = 'trending-up';
            $feedback_color = 'green';
            $units_over_break_even = $unitsSold - $breakEvenPoint;
            $feedback_message = "Estás generando ganancias. Has superado tu punto de equilibrio por <strong>{$units_over_break_even} unidades</strong>. Sigue así, tu estrategia de precios y control de costos es efectiva.";
        } elseif ($profit == 0 && $fixedCosts > 0) {
            $feedback_title = 'Punto de Equilibrio Alcanzado';
            $feedback_icon = 'flag';
            $feedback_color = 'blue';
            $feedback_message = "Has vendido exactamente lo necesario para cubrir todos tus costos (<strong>{$breakEvenPoint} unidades</strong>). No hay ganancias ni pérdidas. El siguiente paso es superar esta meta para empezar a ser rentable.";
        } else { // $profit < 0
            if ($marginPerUnit <= 0) {
                $feedback_title = 'Alerta Crítica de Precios';
                $feedback_icon = 'alert-triangle';
                $feedback_color = 'red';
                $feedback_message = "Tu costo variable por unidad es mayor o igual a tu precio de venta. <strong>Pierdes dinero con cada venta</strong>. Es urgente que revises y aumentes tu precio o reduzcas drásticamente tus costos variables.";
            } else {
                $feedback_title = 'Necesitas Ajustes';
                $feedback_icon = 'tool';
                $feedback_color = 'yellow';
                $units_to_break_even = $breakEvenPoint - $unitsSold;
                $feedback_message = "Actualmente tienes pérdidas. Para empezar a ser rentable, necesitas vender <strong>{$units_to_break_even} unidades más</strong>. Considera estrategias para aumentar ventas, optimizar costos o ajustar tu precio.";
            }
        }

        $show_results = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calculadora de Rentabilidad para Negocios</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/animejs/lib/anime.iife.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .result-card { transition: all 0.3s ease; }
        .result-card:hover { transform: translateY(-5px); box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1); }
        .info-box { background-color: #f9fafb; border-radius: 5px; padding: 12px; margin-top: 8px; font-size: 0.8rem; color: #4b5563; }
        .formula { font-family: monospace; background-color: #eef2ff; padding: 2px 6px; border-radius: 4px; color: #4338ca; }
    </style>
</head>
<body class="bg-gray-50">
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900"><span class="text-blue-600">Biz</span>Calc</h1>
                <button class="p-2 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100"><i data-feather="help-circle"></i></button>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <form method="POST" action="index.php" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Input Section -->
            <div class="lg:col-span-2 space-y-8">
                <div class="bg-white rounded-xl shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center"><i data-feather="dollar-sign" class="mr-2 text-blue-500"></i>Costos de Operación</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="fixedCosts" class="block text-sm font-medium text-gray-700 mb-1">Costos Fijos Mensuales</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><span class="text-gray-500 sm:text-sm">$</span></div>
                                <input type="number" id="fixedCosts" name="fixedCosts" value="<?= htmlspecialchars($fixedCosts > 0 ? $fixedCosts : '') ?>" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md py-2" placeholder="0.00" step="0.01">
                            </div>
                            <div class="info-box">Son los gastos que tienes vendas o no, como el alquiler, sueldos base o servicios. No cambian con la producción.</div>
                        </div>
                        <div>
                            <label for="variableCostPerUnit" class="block text-sm font-medium text-gray-700 mb-1">Costo Variable por Unidad</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><span class="text-gray-500 sm:text-sm">$</span></div>
                                <input type="number" id="variableCostPerUnit" name="variableCostPerUnit" value="<?= htmlspecialchars($variableCostPerUnit > 0 ? $variableCostPerUnit : '') ?>" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md py-2" placeholder="0.00" step="0.01">
                            </div>
                            <div class="info-box">Es lo que te cuesta producir una sola unidad de tu producto (materia prima, empaque, comisiones por venta).</div>
                        </div>
                    </div>
                </div>
                
                <div class="bg-white rounded-xl shadow p-6">
                    <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center"><i data-feather="trending-up" class="mr-2 text-blue-500"></i>Información de Ventas</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="salePrice" class="block text-sm font-medium text-gray-700 mb-1">Precio de Venta por Unidad</label>
                            <div class="relative rounded-md shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><span class="text-gray-500 sm:text-sm">$</span></div>
                                <input type="number" id="salePrice" name="salePrice" value="<?= htmlspecialchars($salePrice > 0 ? $salePrice : '') ?>" class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-7 pr-12 sm:text-sm border-gray-300 rounded-md py-2" placeholder="0.00" step="0.01">
                            </div>
                            <div class="info-box">La cantidad de dinero que recibes de un cliente por cada producto o servicio vendido.</div>
                        </div>
                        <div>
                            <label for="unitsSold" class="block text-sm font-medium text-gray-700 mb-1">Unidades Vendidas (Estimado)</label>
                            <input type="number" id="unitsSold" name="unitsSold" value="<?= htmlspecialchars($unitsSold > 0 ? $unitsSold : '') ?>" class="focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md py-2" placeholder="0">
                            <div class="info-box">La cantidad de productos o servicios que planeas vender en el período que estás calculando.</div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg shadow-md transition duration-150 ease-in-out transform hover:scale-[1.01] flex items-center justify-center">
                    <i data-feather="calculator" class="mr-2"></i>Calcular Rentabilidad
                </button>
            </div>
            
            <!-- Results Section -->
            <div class="space-y-6">
                <div id="feedbackCard" class="result-card bg-white rounded-xl shadow p-6 border-l-4 border-<?= $feedback_color ?>-500 <?= $show_results ? '' : 'hidden' ?>">
                    <h3 class="text-lg font-medium text-gray-800 mb-3 flex items-center"><i data-feather="<?= $feedback_icon ?>" class="mr-2 text-<?= $feedback_color ?>-500"></i>Diagnóstico del Negocio</h3>
                    <p class="text-md font-semibold text-gray-700"><?= $feedback_title ?></p>
                    <p class="text-sm text-gray-600 mt-2"><?= $feedback_message ?></p>
                </div>

                <div id="totalRevenueCard" class="result-card bg-white rounded-xl shadow p-6 border-l-4 border-green-500 <?= $show_results ? '' : 'hidden' ?>">
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Ingresos Totales</h3>
                    <p class="text-3xl font-bold text-gray-900"><?= $show_results ? '$' . number_format($totalRevenue, 2, ',', '.') : '$0,00' ?></p>
                    <div class="info-box">
                        <p class="text-gray-600">Es el dinero total que entra a tu negocio por las ventas, antes de descontar cualquier costo.</p>
                        <?php if ($show_results): ?>
                            <div class="mt-2 pt-2 border-t border-gray-200 space-y-1">
                                <p class="text-xs font-semibold text-gray-700">Fórmula General: <span class="font-normal italic text-gray-500">Precio de Venta &times; Unidades Vendidas</span></p>
                                <p class="text-xs font-semibold text-gray-700">Cálculo Aplicado: <span class="formula"><?= number_format($salePrice, 2, ',', '.') ?> &times; <?= $unitsSold ?></span></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div id="totalCostsCard" class="result-card bg-white rounded-xl shadow p-6 border-l-4 border-blue-500 <?= $show_results ? '' : 'hidden' ?>">
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Costos Totales</h3>
                    <p class="text-3xl font-bold text-gray-900"><?= $show_results ? '$' . number_format($totalCosts, 2, ',', '.') : '$0,00' ?></p>
                    <div class="info-box">
                        <p class="text-gray-600">La suma de todos tus gastos, tanto fijos como variables, para el nivel de ventas actual.</p>
                        <?php if ($show_results): ?>
                            <div class="mt-2 pt-2 border-t border-gray-200 space-y-1">
                                <p class="text-xs font-semibold text-gray-700">Fórmula General: <span class="font-normal italic text-gray-500">Costos Fijos + (Costo Variable &times; Unidades)</span></p>
                                <p class="text-xs font-semibold text-gray-700">Cálculo Aplicado: <span class="formula"><?= number_format($fixedCosts, 2, ',', '.') ?> + (<?= number_format($variableCostPerUnit, 2, ',', '.') ?> &times; <?= $unitsSold ?>)</span></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div id="profitCard" class="result-card bg-white rounded-xl shadow p-6 border-l-4 border-purple-500 <?= $show_results ? '' : 'hidden' ?>">
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Utilidad Neta</h3>
                    <p class="text-3xl font-bold <?= $show_results && $profit < 0 ? 'text-red-600' : 'text-green-600' ?>"><?= $show_results ? '$' . number_format(abs($profit), 2, ',', '.') : '$0,00' ?></p>
                    <div class="info-box">
                        <p class="text-gray-600">Esta es tu ganancia (o pérdida) real. Lo que queda después de que todos los costos se restan de los ingresos.</p>
                        <?php if ($show_results): ?>
                            <div class="mt-2 pt-2 border-t border-gray-200 space-y-1">
                                <p class="text-xs font-semibold text-gray-700">Fórmula General: <span class="font-normal italic text-gray-500">Ingresos Totales - Costos Totales</span></p>
                                <p class="text-xs font-semibold text-gray-700">Cálculo Aplicado: <span class="formula"><?= number_format($totalRevenue, 2, ',', '.') ?> - <?= number_format($totalCosts, 2, ',', '.') ?></span></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div id="breakEvenCard" class="result-card bg-white rounded-xl shadow p-6 border-l-4 border-yellow-500 <?= $show_results ? '' : 'hidden' ?>">
                    <h3 class="text-lg font-medium text-gray-700 mb-2">Punto de Equilibrio</h3>
                    <p class="text-3xl font-bold text-gray-900"><?= $show_results ? number_format($breakEvenPoint, 0, ',', '.') : '0' ?></p>
                    <div class="info-box">
                        <p class="text-gray-600">La cantidad de unidades que necesitas vender solo para cubrir tus costos. A partir de aquí, cada venta genera una ganancia.</p>
                        <?php if ($show_results && $marginPerUnit > 0): ?>
                            <div class="mt-2 pt-2 border-t border-gray-200 space-y-1">
                                <p class="text-xs font-semibold text-gray-700">Fórmula General: <span class="font-normal italic text-gray-500">Costos Fijos / (Precio Venta - Costo Variable)</span></p>
                                <p class="text-xs font-semibold text-gray-700">Cálculo Aplicado: <span class="formula"><?= number_format($fixedCosts, 2, ',', '.') ?> / (<?= number_format($salePrice, 2, ',', '.') ?> - <?= number_format($variableCostPerUnit, 2, ',', '.') ?>)</span></p>
                            </div>
                        <?php elseif ($show_results): ?>
                            <div class="mt-2 pt-2 border-t border-gray-200 space-y-1">
                                <p class="text-xs font-semibold text-gray-700">Cálculo Aplicado: <span class="formula">N/A (El precio debe ser mayor al costo variable)</span></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </form>
    </main>

    <footer class="bg-white border-t mt-12">
        <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
            <p class="text-center text-sm text-gray-500">Herramienta diseñada para emprendedores y pequeños negocios</p>
        </div>
    </footer>

    <script>
        feather.replace();
        <?php if ($show_results): ?>
        document.addEventListener('DOMContentLoaded', function() {
            anime({
                targets: '.result-card:not(.hidden)',
                opacity: [0, 1],
                translateY: [20, 0],
                duration: 800,
                easing: 'easeOutExpo',
                delay: anime.stagger(100, {start: 100})
            });
        });
        <?php endif; ?>
    </script>
</body>
</html>