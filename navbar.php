<?php
$current_page = basename($_SERVER['PHP_SELF']);

$menu_items = [
    'Análisis Básico' => 'index.php',
    'Análisis Completo' => 'completo.php'
];
?>
<nav class="bg-white shadow-md">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <h1 class="text-2xl font-bold text-gray-900"><span class="text-blue-600">Biz</span>Calc</h1>
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <?php foreach ($menu_items as $title => $url): ?>
                            <?php
                            $is_active = ($current_page == $url);
                            $active_classes = 'bg-blue-600 text-white';
                            $inactive_classes = 'text-gray-500 hover:bg-blue-500 hover:text-white';
                            $classes = $is_active ? $active_classes : $inactive_classes;
                            ?>
                            <a href="<?= $url ?>" class="<?= $classes ?> px-3 py-2 rounded-md text-sm font-medium"><?= $title ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="hidden md:block">
                <div class="ml-4 flex items-center md:ml-6">
                    <button class="p-2 rounded-full bg-blue-50 text-blue-600 hover:bg-blue-100">
                        <i data-feather="help-circle"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</nav>
