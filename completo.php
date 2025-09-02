<?php
// --- LÓGICA PHP PARA ANÁLISIS COMPLETO ---

// Inicializamos todas las variables
$fixedCosts = 0; $variableCostPerUnit = 0; $salePrice = 0; $unitsSold = 0;
$taxRate = 0; $financialExpenses = 0; $depreciation = 0;
$initialInvestment = 0; $averageInventory = 0; $equity = 0;
$show_results = false;

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
    // Recogemos datos para KPIs
    $initialInvestment = isset($_POST['initialInvestment']) ? (float)$_POST['initialInvestment'] : 0;
    $averageInventory = isset($_POST['averageInventory']) ? (float)$_POST['averageInventory'] : 0;
    $equity = isset($_POST['equity']) ? (float)$_POST['equity'] : 0;

    if ($unitsSold > 0) {
        // Cálculos de Utilidad
        $totalRevenue = $salePrice * $unitsSold;
        $totalVariableCosts = $variableCostPerUnit * $unitsSold;
        $totalOperatingCosts = $fixedCosts + $totalVariableCosts;
        $profitBeforeTax = $totalRevenue - $totalOperatingCosts;
        $taxAmount = $profitBeforeTax > 0 ? $profitBeforeTax * ($taxRate / 100) : 0;
        $totalNonOperatingExpenses = $taxAmount + $financialExpenses;
        $netProfit = $profitBeforeTax - $totalNonOperatingExpenses - $depreciation;

        // Cálculos de Márgenes
        $grossMargin = $totalRevenue > 0 ? (($totalRevenue - $totalVariableCosts) / $totalRevenue) * 100 : 0;
        $operatingMargin = $totalRevenue > 0 ? ($profitBeforeTax / $totalRevenue) * 100 : 0;
        $netMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;

        // KPIs
        $roi = $initialInvestment > 0 ? ($netProfit / $initialInvestment) * 100 : 0;
        $roe = $equity > 0 ? ($netProfit / $equity) * 100 : 0;
        $cashFlow = $netProfit + $depreciation;
        $inventoryTurnover = $averageInventory > 0 ? $totalVariableCosts / $averageInventory : 0;

        $show_results = true;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análisis Financiero Completo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/feather-icons"></script>
    <script src="https://cdn.jsdelivr.net/npm/feather-icons/dist/feather.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .info-box { background-color: #f9fafb; border-radius: 5px; padding: 12px; margin-top: 8px; font-size: 0.8rem; color: #4b5563; }
        .formula { font-family: monospace; background-color: #eef2ff; padding: 2px 6px; border-radius: 4px; color: #4338ca; }
        .result-card { border-width: 1px; border-radius: 0.75rem; padding: 1.5rem; box-shadow: 0 1px 3px 0 rgba(0,0,0,0.1); background-color: white; height: 100%; }
        .section-title { font-size: 1.5rem; font-weight: 700; color: #111827; border-bottom: 2px solid #3b82f6; padding-bottom: 0.5rem; margin-bottom: 1.5rem; }
    </style>
</head>
<body class="bg-gray-100">
    <?php include 'navbar.php'; ?>

    <main class="max-w-7xl mx-auto px-4 py-8 sm:px-6 lg:px-8">
        <form method="POST" action="completo.php">
            <div class="bg-white p-8 rounded-xl shadow-sm mb-8">
                <h2 class="text-3xl font-bold text-gray-800 mb-6">Panel de Análisis Financiero</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-x-6 gap-y-4">
                    <div><label class="block text-sm font-medium text-gray-700">Costos Fijos</label><input type="number" name="fixedCosts" value="<?= htmlspecialchars($fixedCosts > 0 ? $fixedCosts : '') ?>" class="mt-1 block w-full" placeholder="0.00" step="0.01"></div>
                    <div><label class="block text-sm font-medium text-gray-700">Costo Variable (Unidad)</label><input type="number" name="variableCostPerUnit" value="<?= htmlspecialchars($variableCostPerUnit > 0 ? $variableCostPerUnit : '') ?>" class="mt-1 block w-full" placeholder="0.00" step="0.01"></div>
                    <div><label class="block text-sm font-medium text-gray-700">Precio de Venta (Unidad)</label><input type="number" name="salePrice" value="<?= htmlspecialchars($salePrice > 0 ? $salePrice : '') ?>" class="mt-1 block w-full" placeholder="0.00" step="0.01"></div>
                    <div><label class="block text-sm font-medium text-gray-700">Unidades Vendidas</label><input type="number" name="unitsSold" value="<?= htmlspecialchars($unitsSold > 0 ? $unitsSold : '') ?>" class="mt-1 block w-full" placeholder="0"></div>
                    <div><label class="block text-sm font-medium text-gray-700">Tasa de Impuestos (%)</label><input type="number" name="taxRate" value="<?= htmlspecialchars($taxRate > 0 ? $taxRate : '') ?>" class="mt-1 block w-full" placeholder="e.g., 21"></div>
                    <div><label class="block text-sm font-medium text-gray-700">Gastos Financieros</label><input type="number" name="financialExpenses" value="<?= htmlspecialchars($financialExpenses > 0 ? $financialExpenses : '') ?>" class="mt-1 block w-full" placeholder="0.00" step="0.01"></div>
                    <div><label class="block text-sm font-medium text-gray-700">Depreciación</label><input type="number" name="depreciation" value="<?= htmlspecialchars($depreciation > 0 ? $depreciation : '') ?>" class="mt-1 block w-full" placeholder="0.00" step="0.01"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-6 mt-6 border-t">
                    <div><label class="block text-sm font-medium text-gray-700">Inversión Inicial</label><input type="number" name="initialInvestment" value="<?= htmlspecialchars($initialInvestment > 0 ? $initialInvestment : '') ?>" class="mt-1 block w-full" placeholder="0.00" step="0.01"><div class="info-box text-xs">Capital total invertido para iniciar el negocio.</div></div>
                    <div><label class="block text-sm font-medium text-gray-700">Inventario Promedio</label><input type="number" name="averageInventory" value="<?= htmlspecialchars($averageInventory > 0 ? $averageInventory : '') ?>" class="mt-1 block w-full" placeholder="0.00" step="0.01"><div class="info-box text-xs">Valor promedio del inventario en un período.</div></div>
                    <div><label class="block text-sm font-medium text-gray-700">Capital Propio (Patrimonio)</label><input type="number" name="equity" value="<?= htmlspecialchars($equity > 0 ? $equity : '') ?>" class="mt-1 block w-full" placeholder="0.00" step="0.01"><div class="info-box text-xs">Dinero que los dueños han puesto en el negocio.</div></div>
                </div>
            </div>
            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow-lg">Calcular Análisis Financiero</button>
        </form>

        <?php if ($show_results): ?>
        <div class="mt-10">
            <h3 class="section-title">Márgenes de Rentabilidad</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="result-card"><h4 class="font-semibold">Margen Bruto</h4><p class="text-3xl font-bold"><?= number_format($grossMargin, 2, ',', '.') ?>%</p><div class="info-box"><p>Qué % de cada venta queda para cubrir costos fijos.</p><div class="mt-2 pt-2 border-t"><p class="text-xs font-semibold">Fórmula: <span class="italic">(Ingresos - Costos Variables) / Ingresos</span></p><p class="text-xs font-semibold">Cálculo: <span class="formula">(<?= number_format($totalRevenue, 2) ?> - <?= number_format($totalVariableCosts, 2) ?>) / <?= number_format($totalRevenue, 2) ?></span></p></div></div></div>
                <div class="result-card"><h4 class="font-semibold">Margen Operativo</h4><p class="text-3xl font-bold"><?= number_format($operatingMargin, 2, ',', '.') ?>%</p><div class="info-box"><p>Eficiencia de la operación principal, antes de impuestos y deudas.</p><div class="mt-2 pt-2 border-t"><p class="text-xs font-semibold">Fórmula: <span class="italic">Utilidad Operativa / Ingresos</span></p><p class="text-xs font-semibold">Cálculo: <span class="formula"><?= number_format($profitBeforeTax, 2) ?> / <?= number_format($totalRevenue, 2) ?></span></p></div></div></div>
                <div class="result-card"><h4 class="font-semibold">Margen Neto</h4><p class="text-3xl font-bold"><?= number_format($netMargin, 2, ',', '.') ?>%</p><div class="info-box"><p>El % real de ganancia final después de todo.</p><div class="mt-2 pt-2 border-t"><p class="text-xs font-semibold">Fórmula: <span class="italic">Utilidad Neta / Ingresos</span></p><p class="text-xs font-semibold">Cálculo: <span class="formula"><?= number_format($netProfit, 2) ?> / <?= number_format($totalRevenue, 2) ?></span></p></div></div></div>
            </div>

            <h3 class="section-title">Indicadores Clave (KPIs)</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="result-card"><h4 class="font-semibold">Retorno sobre Inversión (ROI)</h4><p class="text-3xl font-bold"><?= number_format($roi, 2, ',', '.') ?>%</p><div class="info-box"><p>Rendimiento de la inversión inicial.</p><div class="mt-2 pt-2 border-t"><p class="text-xs font-semibold">Fórmula: <span class="italic">Utilidad Neta / Inversión Inicial</span></p><p class="text-xs font-semibold">Cálculo: <span class="formula"><?= number_format($netProfit, 2) ?> / <?= number_format($initialInvestment, 2) ?></span></p></div></div></div>
                <div class="result-card"><h4 class="font-semibold">Rentabilidad sobre Patrimonio (ROE)</h4><p class="text-3xl font-bold"><?= number_format($roe, 2, ',', '.') ?>%</p><div class="info-box"><p>Rentabilidad generada sobre el dinero de los dueños.</p><div class="mt-2 pt-2 border-t"><p class="text-xs font-semibold">Fórmula: <span class="italic">Utilidad Neta / Capital Propio</span></p><p class="text-xs font-semibold">Cálculo: <span class="formula"><?= number_format($netProfit, 2) ?> / <?= number_format($equity, 2) ?></span></p></div></div></div>
                <div class="result-card"><h4 class="font-semibold">Flujo de Caja (Estimado)</h4><p class="text-3xl font-bold">$<?= number_format($cashFlow, 2, ',', '.') ?></p><div class="info-box"><p>Dinero real generado (Utilidad + gasto no monetario de depreciación).</p><div class="mt-2 pt-2 border-t"><p class="text-xs font-semibold">Fórmula: <span class="italic">Utilidad Neta + Depreciación</span></p><p class="text-xs font-semibold">Cálculo: <span class="formula"><?= number_format($netProfit, 2) ?> + <?= number_format($depreciation, 2) ?></span></p></div></div></div>
                <div class="result-card"><h4 class="font-semibold">Rotación de Inventario</h4><p class="text-3xl font-bold"><?= number_format($inventoryTurnover, 2, ',', '.') ?></p><div class="info-box"><p>Veces que el inventario se vende en un período.</p><div class="mt-2 pt-2 border-t"><p class="text-xs font-semibold">Fórmula: <span class="italic">Costo de Ventas / Inventario Promedio</span></p><p class="text-xs font-semibold">Cálculo: <span class="formula"><?= number_format($totalVariableCosts, 2) ?> / <?= number_format($averageInventory, 2) ?></span></p></div></div></div>
            </div>

            <h3 class="section-title">Desglose de Utilidad</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="result-card"><h4 class="font-semibold">Ingresos Totales</h4><p class="text-3xl font-bold text-green-600">$<?= number_format($totalRevenue, 2, ',', '.') ?></p><div class="info-box"><p>El total de dinero facturado por las ventas.</p><div class="mt-2 pt-2 border-t"><p class="text-xs font-semibold">Fórmula: <span class="italic">Precio Venta &times; Unidades</span></p><p class="text-xs font-semibold">Cálculo: <span class="formula"><?= number_format($salePrice, 2) ?> &times; <?= $unitsSold ?></span></p></div></div></div>
                <div class="result-card"><h4 class="font-semibold">(-) Costos Operativos</h4><p class="text-3xl font-bold text-red-500">$<?= number_format($totalOperatingCosts, 2, ',', '.') ?></p><div class="info-box"><p>La suma de costos fijos y variables.</p><div class="mt-2 pt-2 border-t"><p class="text-xs font-semibold">Fórmula: <span class="italic">Costos Fijos + Costos Variables</span></p><p class="text-xs font-semibold">Cálculo: <span class="formula"><?= number_format($fixedCosts, 2) ?> + <?= number_format($totalVariableCosts, 2) ?></span></p></div></div></div>
                <div class="result-card"><h4 class="font-semibold">(=) Utilidad Operativa</h4><p class="text-3xl font-bold text-blue-600">$<?= number_format($profitBeforeTax, 2, ',', '.') ?></p><div class="info-box"><p>La ganancia de la operación principal del negocio.</p><div class="mt-2 pt-2 border-t"><p class="text-xs font-semibold">Fórmula: <span class="italic">Ingresos - Costos Operativos</span></p><p class="text-xs font-semibold">Cálculo: <span class="formula"><?= number_format($totalRevenue, 2) ?> - <?= number_format($totalOperatingCosts, 2) ?></span></p></div></div></div>
                <div class="result-card"><h4 class="font-semibold">(-) Impuestos y Deudas</h4><p class="text-3xl font-bold text-red-500">$<?= number_format($totalNonOperatingExpenses, 2, ',', '.') ?></p><div class="info-box"><p>Suma de impuestos y gastos financieros.</p><div class="mt-2 pt-2 border-t"><p class="text-xs font-semibold">Fórmula: <span class="italic">Monto Impuestos + Gastos Financieros</span></p><p class="text-xs font-semibold">Cálculo: <span class="formula"><?= number_format($taxAmount, 2) ?> + <?= number_format($financialExpenses, 2) ?></span></p></div></div></div>
                <div class="result-card"><h4 class="font-semibold">(-) Depreciación</h4><p class="text-3xl font-bold text-red-500">$<?= number_format($depreciation, 2, ',', '.') ?></p><div class="info-box"><p>Pérdida de valor de activos. Es un costo no monetario.</p></div></div>
                <div class="result-card"><h4 class="font-semibold">(=) Utilidad Neta Final</h4><p class="text-3xl font-bold <?= $netProfit >= 0 ? 'text-green-600' : 'text-red-600' ?>">$<?= number_format($netProfit, 2, ',', '.') ?></p><div class="info-box"><p>La ganancia final y real después de todos los gastos.</p></div></div>
            </div>
        </div>
        <?php endif; ?>
    </main>

    <script>
        feather.replace();
    </script>
</body>
</html>