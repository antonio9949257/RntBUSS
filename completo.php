<?php
// --- LÓGICA PHP PARA ANÁLISIS COMPLETO ---

// Inicializamos todas las variables
$fixedCosts = 0; $variableCostPerUnit = 0; $salePrice = 0; $unitsSold = 0;
$taxRate = 0; $financialExpenses = 0; $depreciation = 0;
$show_results = false;
$feedback_message = '';

// Verificamos si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recogemos datos básicos
    $fixedCosts = isset($_POST['fixedCosts']) ? (float)$_POST['fixedCosts'] : 0;
    $variableCostPerUnit = isset($_POST['variableCostPerUnit']) ? (float)$_POST['variableCostPerUnit'] : 0;
    $salePrice = isset($_POST['salePrice']) ? (float)$_POST['salePrice'] : 0;
    $unitsSold = isset($_POST['unitsSold']) ? (int)$_POST['unitsSold'] : 0;
    // Recogemos datos avanzados
    $taxRate = isset($_POST['taxRate']) ? (float)$_POST['taxRate'] : 0;
    $financialExpenses = isset($_POST['financialExpenses']) ? (float)$_POST['financialExpenses'] : 0;
    $depreciation = isset($_POST['depreciation']) ? (float)$_POST['depreciation'] : 0;

    if ($salePrice > 0 || $unitsSold > 0 || $fixedCosts > 0) {
        // Cálculos básicos
        $totalRevenue = $salePrice * $unitsSold;
        $totalVariableCosts = $variableCostPerUnit * $unitsSold;
        $totalCosts = $fixedCosts + $totalVariableCosts;
        $profitBeforeTax = $totalRevenue - $totalCosts;

        // Cálculos avanzados
        $taxAmount = $profitBeforeTax > 0 ? $profitBeforeTax * ($taxRate / 100) : 0;
        $profitAfterTax = $profitBeforeTax - $taxAmount;
        $netProfit = $profitAfterTax - $financialExpenses - $depreciation;

        // Márgenes
        $contributionMarginUnit = $salePrice - $variableCostPerUnit;
        $netMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;

        // Mensaje de diagnóstico
        if ($netProfit > 0) {
            $feedback_message = "<strong class=\"text-green-600\">¡Felicidades!</strong> Tu negocio es rentabe a un nivel profundo. Después de cubrir todos los costos operativos, impuestos y gastos no operativos, todavía te queda una ganancia real.";
        } else {
            $feedback_message = "<strong class=\"text-red-600\">Atención:</strong> Tu negocio no está generando una ganancia neta real. Revisa los gastos no operativos (impuestos, deudas) y la rentabilidad bruta para identificar áreas de mejora.";
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
    <title>Análisis Completo de Rentabilidad</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .info-box { background-color: #f9fafb; border-radius: 5px; padding: 12px; margin-top: 8px; font-size: 0.8rem; color: #4b5563; }
        .formula { font-family: monospace; background-color: #eef2ff; padding: 2px 6px; border-radius: 4px; color: #4338ca; }
        .result-card { border-width: 1px; border-radius: 0.75rem; padding: 1.5rem; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.1); background-color: white; }
    </style>
</head>
<body class="bg-gray-100">
    <?php include 'navbar.php'; ?>

    <main class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <form method="POST" action="completo.php">
            <div class="bg-white p-6 rounded-xl shadow-sm mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Análisis de Rentabilidad Completo</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
                    <div><label class="block text-sm font-medium text-gray-700">Costos Fijos</label><input type="number" name="fixedCosts" value="<?= htmlspecialchars($fixedCosts > 0 ? $fixedCosts : '') ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm py-2" placeholder="0.00" step="0.01"><div class="info-box text-xs">Gastos que no cambian con las ventas (alquiler, sueldos).</div></div>
                    <div><label class="block text-sm font-medium text-gray-700">Costo Variable (Unidad)</label><input type="number" name="variableCostPerUnit" value="<?= htmlspecialchars($variableCostPerUnit > 0 ? $variableCostPerUnit : '') ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm py-2" placeholder="0.00" step="0.01"><div class="info-box text-xs">Costo de producir una unidad (materiales, etc.).</div></div>
                    <div><label class="block text-sm font-medium text-gray-700">Precio de Venta (Unidad)</label><input type="number" name="salePrice" value="<?= htmlspecialchars($salePrice > 0 ? $salePrice : '') ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm py-2" placeholder="0.00" step="0.01"><div class="info-box text-xs">Precio por unidad que paga el cliente.</div></div>
                    <div><label class="block text-sm font-medium text-gray-700">Unidades Vendidas</label><input type="number" name="unitsSold" value="<?= htmlspecialchars($unitsSold > 0 ? $unitsSold : '') ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm py-2" placeholder="0"><div class="info-box text-xs">Total de unidades que planeas vender.</div></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-6 border-t">
                     <div><label class="block text-sm font-medium text-gray-700">Tasa de Impuestos (%</label><input type="number" name="taxRate" value="<?= htmlspecialchars($taxRate > 0 ? $taxRate : '') ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm py-2" placeholder="e.g., 21"><div class="info-box">Porcentaje de impuestos a pagar sobre la utilidad bruta.</div></div>
                    <div><label class="block text-sm font-medium text-gray-700">Gastos Financieros</label><input type="number" name="financialExpenses" value="<?= htmlspecialchars($financialExpenses > 0 ? $financialExpenses : '') ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm py-2" placeholder="0.00" step="0.01"><div class="info-box">Intereses de préstamos o deudas que el negocio debe pagar.</div></div>
                    <div><label class="block text-sm font-medium text-gray-700">Depreciación y Amortización</label><input type="number" name="depreciation" value="<?= htmlspecialchars($depreciation > 0 ? $depreciation : '') ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm py-2" placeholder="0.00" step="0.01"><div class="info-box">Pérdida de valor de activos como maquinaria o equipos.</div></div>
                </div>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg">Calcular Análisis Completo</button>
        </form>

        <?php if ($show_results): ?>
        <div class="mt-10">
            <h3 class="text-2xl font-bold text-gray-800 mb-6">Resultados del Análisis Detallado</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="result-card lg:col-span-3 border-l-8 <?= $netProfit > 0 ? 'border-green-500' : 'border-red-500' ?>">
                    <h4 class="text-lg font-semibold text-gray-600">Diagnóstico: Utilidad Neta Real</h4>
                    <p class="text-5xl font-extrabold text-gray-900 my-2"><?= '$' . number_format($netProfit, 2, ',', '.') ?></p>
                    <div class="info-box">
                        <p class="text-gray-600"><?= $feedback_message ?></p>
                        <div class="mt-2 pt-2 border-t border-gray-200 space-y-1">
                            <p class="text-xs font-semibold text-gray-700">Fórmula General: <span class="font-normal italic text-gray-500">Utilidad Bruta - Impuestos - Gastos Financieros - Depreciación</span></p>
                            <p class="text-xs font-semibold text-gray-700">Cálculo Aplicado: <span class="formula"><?= number_format($profitBeforeTax, 2, ',', '.') ?> - <?= number_format($taxAmount, 2, ',', '.') ?> - <?= number_format($financialExpenses, 2, ',', '.') ?> - <?= number_format($depreciation, 2, ',', '.') ?></span></p>
                        </div>
                    </div>
                </div>

                <div class="result-card">
                    <h4 class="text-md font-semibold text-gray-600">Margen Neto</h4>
                    <p class="text-3xl font-bold text-gray-900"><?= number_format($netMargin, 2, ',', '.') ?>%</p>
                    <div class="info-box">
                        <p class="text-gray-600">Indica el % de cada venta que se convierte en ganancia neta. Un margen alto es señal de eficiencia.</p>
                        <div class="mt-2 pt-2 border-t border-gray-200 space-y-1">
                            <p class="text-xs font-semibold text-gray-700">Fórmula: <span class="font-normal italic text-gray-500">(Utilidad Neta / Ingresos) &times; 100</span></p>
                            <p class="text-xs font-semibold text-gray-700">Cálculo: <span class="formula">(<?= number_format($netProfit, 2, ',', '.') ?> / <?= number_format($totalRevenue, 2, ',', '.') ?>) &times; 100</span></p>
                        </div>
                    </div>
                </div>

                <div class="result-card">
                    <h4 class="text-md font-semibold text-gray-600">Margen de Contribución (Unidad)</h4>
                    <p class="text-3xl font-bold text-gray-900"><?= '$' . number_format($contributionMarginUnit, 2, ',', '.') ?></p>
                    <div class="info-box">
                        <p class="text-gray-600">Dinero que ganas por unidad vendida para cubrir costos fijos.</p>
                        <div class="mt-2 pt-2 border-t border-gray-200 space-y-1">
                            <p class="text-xs font-semibold text-gray-700">Fórmula: <span class="font-normal italic text-gray-500">Precio Venta - Costo Variable</span></p>
                            <p class="text-xs font-semibold text-gray-700">Cálculo: <span class="formula"><?= number_format($salePrice, 2, ',', '.') ?> - <?= number_format($variableCostPerUnit, 2, ',', '.') ?></span></p>
                        </div>
                    </div>
                </div>

                <div class="result-card">
                    <h4 class="text-md font-semibold text-gray-600">Utilidad Bruta</h4>
                    <p class="text-3xl font-bold text-gray-900"><?= '$' . number_format($profitBeforeTax, 2, ',', '.') ?></p>
                    <div class="info-box">
                        <p class="text-gray-600">Ganancia antes de descontar impuestos y gastos no operativos.</p>
                        <div class="mt-2 pt-2 border-t border-gray-200 space-y-1">
                            <p class="text-xs font-semibold text-gray-700">Fórmula: <span class="font-normal italic text-gray-500">Ingresos - Costos Totales</span></p>
                            <p class="text-xs font-semibold text-gray-700">Cálculo: <span class="formula"><?= number_format($totalRevenue, 2, ',', '.') ?> - <?= number_format($totalCosts, 2, ',', '.') ?></span></p>
                        </div>
                    </div>
                </div>

                 <div class="result-card">
                    <h4 class="text-md font-semibold text-gray-600">Impuestos (<?= number_format($taxRate, 1, ',', '.') ?>%)</h4>
                    <p class="text-3xl font-bold text-red-500">- <?= '$' . number_format($taxAmount, 2, ',', '.') ?></p>
                     <div class="info-box">
                        <p class="text-gray-600">Monto a pagar según la tasa de impuestos sobre la utilidad bruta.</p>
                        <div class="mt-2 pt-2 border-t border-gray-200 space-y-1">
                            <p class="text-xs font-semibold text-gray-700">Fórmula: <span class="font-normal italic text-gray-500">Utilidad Bruta &times; Tasa</span></p>
                            <p class="text-xs font-semibold text-gray-700">Cálculo: <span class="formula"><?= number_format($profitBeforeTax, 2, ',', '.') ?> &times; <?= number_format($taxRate, 2, ',', '.') ?>%</span></p>
                        </div>
                    </div>
                </div>

                <div class="result-card">
                    <h4 class="text-md font-semibold text-gray-600">Gastos Financieros</h4>
                    <p class="text-3xl font-bold text-red-500">- <?= '$' . number_format($financialExpenses, 2, ',', '.') ?></p>
                    <div class="info-box"><p>Este es un valor de entrada directa y se resta a la utilidad.</p></div>
                </div>

                <div class="result-card">
                    <h4 class="text-md font-semibold text-gray-600">Depreciación</h4>
                    <p class="text-3xl font-bold text-red-500">- <?= '$' . number_format($depreciation, 2, ',', '.') ?></p>
                    <div class="info-box"><p>Este es un valor de entrada directa y se resta a la utilidad.</p></div>
                </div>

            </div>
        </div>
        <?php endif; ?>
    </main>

    <script>
        feather.replace();
    </script>
</body>
</html>