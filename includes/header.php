
<?php
require_once __DIR__ . '/functions.php';

$pageTitle = $pageTitle ?? 'CLOJO Talent Connect';

$isLoggedIn = !empty($_SESSION['user_id']);
$userRole = $_SESSION['role'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title><?= e($pageTitle) ?> | CLOJO Talent Connect</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: '#14243B',
                        gold: '#D9A72E',
                        clojo: {
                            navy: '#14243B',
                            gold: '#D9A72E'
                        }
                    }
                }
            }
        };
    </script>

    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;600;700;800&display=swap"
        rel="stylesheet"
    >

    <style>
        body {
            font-family: 'DM Sans', sans-serif;
        }

        h1, h2, h3, .brand {
            font-family: 'Manrope', sans-serif;
        }
    </style>
</head>

<body class="bg-white text-slate-800 min-h-screen flex flex-col">

<header class="bg-white border-b border-slate-200 sticky top-0 z-40">
    <nav class="max-w-6xl mx-auto w-full px-5 py-4 flex items-center justify-between gap-5">

        <!-- Logo -->
        <a href="index.php" class="flex items-center gap-3 shrink-0">
            <span class="bg-navy text-gold font-bold text-xl w-10 h-10 flex items-center justify-center">
                C
            </span>

            <span>
                <span class="brand block text-navy font-bold tracking-wide">
                    CLOJO
                </span>

                <span class="block text-xs text-slate-500">
                    TALENT CONNECT
                </span>
            </span>
        </a>

        <!-- Mobile menu button -->
        <button
            type="button"
            id="menuButton"
            class="md:hidden border border-slate-300 text-navy px-3 py-2 text-sm hover:bg-slate-50"
            aria-label="Toggle navigation"
            aria-controls="mobileLinks"
            aria-expanded="false"
        >
            Menu
        </button>

        <!-- Desktop navigation -->
        <div class="hidden md:flex items-center gap-7 text-sm">

            <a href="index.php"
               class="text-navy font-semibold hover:text-gold transition-colors">
                Home
            </a>

            <a href="talents.php"
               class="text-slate-600 hover:text-gold transition-colors">
                Talents
            </a>

            <a href="opportunities.php"
               class="text-slate-600 hover:text-gold transition-colors">
                Opportunities
            </a>

            <a href="about.php"
               class="text-slate-600 hover:text-gold transition-colors">
                About
            </a>

            <?php if ($isLoggedIn): ?>

                <a href="dashboard.php"
                   class="text-slate-600 hover:text-gold transition-colors">
                    Dashboard
                </a>

                <?php if ($userRole === 'admin'): ?>
                    <a href="admin.php"
                       class="text-slate-600 hover:text-gold transition-colors">
                        Admin
                    </a>
                <?php endif; ?>

                <form action="logout.php" method="post">
                    <input
                        type="hidden"
                        name="csrf_token"
                        value="<?= e(csrf_token()) ?>"
                    >

                    <button
                        type="submit"
                        class="bg-navy text-white px-4 py-2.5 hover:bg-slate-700 transition"
                    >
                        Logout
                    </button>
                </form>

            <?php else: ?>

                <a href="login.php"
                   class="text-slate-600 hover:text-gold transition-colors">
                    Log in
                </a>

                <a href="register.php"
                   class="bg-navy text-white px-4 py-2.5 hover:bg-slate-700 transition">
                    Join CLOJO
                </a>

            <?php endif; ?>

        </div>
    </nav>

    <!-- Mobile navigation -->
    <div
        id="mobileLinks"
        class="hidden md:hidden border-t border-slate-200 bg-white px-5 py-4 space-y-4 text-sm"
    >

        <a class="block text-slate-700 hover:text-gold" href="index.php">
            Home
        </a>

        <a class="block text-slate-700 hover:text-gold" href="talents.php">
            Talents
        </a>

        <a class="block text-slate-700 hover:text-gold" href="opportunities.php">
            Opportunities
        </a>

        <a class="block text-slate-700 hover:text-gold" href="about.php">
            About
        </a>

        <?php if ($isLoggedIn): ?>

            <a class="block text-slate-700 hover:text-gold" href="dashboard.php">
                Dashboard
            </a>

            <?php if ($userRole === 'admin'): ?>
                <a class="block text-slate-700 hover:text-gold" href="admin.php">
                    Admin panel
                </a>
            <?php endif; ?>

            <form action="logout.php" method="post">
                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e(csrf_token()) ?>"
                >

                <button
                    type="submit"
                    class="bg-navy text-white px-4 py-2.5 hover:bg-slate-700 transition"
                >
                    Logout
                </button>
            </form>

        <?php else: ?>

            <a class="block text-slate-700 hover:text-gold" href="login.php">
                Log in
            </a>

            <a
                class="inline-block bg-navy text-white px-4 py-2.5 hover:bg-slate-700 transition"
                href="register.php"
            >
                Join CLOJO
            </a>

        <?php endif; ?>

    </div>
</header>

<main class="flex-1">

<script>
    const menuButton = document.getElementById('menuButton');
    const mobileLinks = document.getElementById('mobileLinks');

    if (menuButton && mobileLinks) {
        menuButton.addEventListener('click', function () {
            mobileLinks.classList.toggle('hidden');

            const isExpanded = !mobileLinks.classList.contains('hidden');

            menuButton.setAttribute('aria-expanded', isExpanded);
        });
    }
</script>
