
<?php
require_once 'db.php';

// Get the number of registered talents.
$talentCount = $pdo->query("
    SELECT COUNT(*) 
    FROM users 
    WHERE role = 'talent'
")->fetchColumn();

// Get the number of sponsors.
$sponsorCount = $pdo->query("
    SELECT COUNT(*) 
    FROM users 
    WHERE role = 'sponsor'
")->fetchColumn();

// Get the number of available opportunities.
$opportunityCount = $pdo->query("
    SELECT COUNT(*) 
    FROM opportunities
")->fetchColumn();

// Get some talent profiles for the homepage.
$sql = "SELECT users.name, talent_profiles.location,
               talent_profiles.skills, categories.name AS category
        FROM talent_profiles
        JOIN users ON talent_profiles.user_id = users.id
        LEFT JOIN categories
            ON talent_profiles.category_id = categories.id
        ORDER BY talent_profiles.id DESC
        LIMIT 3";

$talents = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Get the latest opportunities.
$opportunities = $pdo->query("
    SELECT title, category, support_type, deadline
    FROM opportunities
    ORDER BY id DESC
    LIMIT 3
")->fetchAll(PDO::FETCH_ASSOC);

// Safely display text from the database.
function clean($value) {
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CLOJO Talent Connect | Discover Talent</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: '#14243B',
                        gold: '#D9A72E'
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-white text-slate-800">

<!-- Navigation -->
<header class="border-b border-slate-200">
    <nav class="max-w-6xl mx-auto px-5 py-4 flex items-center
                justify-between gap-5">

        <a href="index.php" class="flex items-center gap-3">
            <span class="bg-navy text-gold font-bold text-xl
                         w-10 h-10 flex items-center justify-center">
                C
            </span>

            <span>
                <span class="block text-navy font-bold tracking-wide">
                    CLOJO
                </span>
                <span class="block text-xs text-slate-500">
                    TALENT CONNECT
                </span>
            </span>
        </a>

        <div class="hidden md:flex items-center gap-7 text-sm">
            <a href="index.php" class="text-navy font-semibold">
                Home
            </a>
            <a href="talents.php" class="hover:text-gold">
                Discover Talent
            </a>
            <a href="opportunities.php" class="hover:text-gold">
                Opportunities
            </a>
            <a href="about.php" class="hover:text-gold">
                About
            </a>
        </div>

        <div class="flex items-center gap-3 text-sm">
            <a href="login.php"
               class="hidden sm:inline hover:text-gold">
                Log in
            </a>

            <a href="register.php"
               class="bg-navy text-white px-4 py-2.5
                      hover:bg-slate-700 transition">
                Join CLOJO
            </a>
        </div>
    </nav>
</header>

<main>

    <!-- Hero section -->
    <section class="bg-slate-50 border-b border-slate-200">
        <div class="max-w-6xl mx-auto px-5 py-16 md:py-24
                    grid md:grid-cols-2 gap-12 items-center">

            <div>
                <p class="text-sm font-semibold uppercase
                          tracking-widest text-amber-700 mb-5">
                    A platform for emerging talent
                </p>

                <h1 class="text-4xl md:text-5xl font-bold
                           text-navy leading-tight">
                    Good talent deserves an opportunity.
                </h1>

                <p class="mt-5 text-slate-600 leading-7 max-w-lg">
                    Discover talented people, showcase your skills,
                    and find opportunities that can help turn your
                    passion into progress.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="talents.php"
                       class="bg-navy text-white px-5 py-3
                              hover:bg-slate-700 transition">
                        Explore talents &rarr;
                    </a>

                    <a href="opportunities.php"
                       class="border border-slate-300 px-5 py-3
                              hover:border-navy transition">
                        Find opportunities
                    </a>
                </div>

                <p class="mt-6 text-sm text-slate-500">
                    Built for talent. Connected through opportunity.
                </p>
            </div>

            <div>
                <img
                    src="https://images.unsplash.com/photo-1521737711867-e3b97375f902?auto=format&fit=crop&w=1000&q=85"
                    alt="Young professionals working together"
                    class="w-full h-72 md:h-96 object-cover"
                >

                <p class="mt-3 text-xs text-slate-500">
                    CLOJO TALENT CONNECT &mdash; Project by ISHIMWE
                </p>
            </div>

        </div>
    </section>

    <!-- Statistics -->
    <section class="border-b border-slate-200">
        <div class="max-w-6xl mx-auto px-5 py-8
                    grid grid-cols-1 sm:grid-cols-3 gap-6">

            <div>
                <p class="text-3xl font-bold text-navy">
                    <?= (int) $talentCount ?>
                </p>
                <p class="mt-1 text-sm text-slate-500">
                    Registered talents
                </p>
            </div>

            <div>
                <p class="text-3xl font-bold text-navy">
                    <?= (int) $opportunityCount ?>
                </p>
                <p class="mt-1 text-sm text-slate-500">
                    Opportunities listed
                </p>
            </div>

            <div>
                <p class="text-3xl font-bold text-navy">
                    <?= (int) $sponsorCount ?>
                </p>
                <p class="mt-1 text-sm text-slate-500">
                    Registered sponsors
                </p>
            </div>

        </div>
    </section>

    <!-- Featured talents -->
    <section class="max-w-6xl mx-auto px-5 py-16">

        <div class="flex flex-wrap justify-between items-end gap-4 mb-8">
            <div>
                <p class="text-sm text-amber-700 font-semibold">
                    MEET THE COMMUNITY
                </p>

                <h2 class="text-2xl md:text-3xl font-bold
                           text-navy mt-2">
                    Talent worth discovering
                </h2>

                <p class="mt-2 text-slate-500">
                    Meet people with skills, ideas and ambition.
                </p>
            </div>

            <a href="talents.php"
               class="text-sm font-semibold text-navy
                      hover:text-amber-700">
                View all talents &rarr;
            </a>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">

            <?php if (count($talents) > 0): ?>

                <?php foreach ($talents as $talent): ?>
                    <article class="border border-slate-200 p-6
                                    hover:border-slate-400 transition">

                        <div class="w-12 h-12 bg-slate-100
                                    text-navy flex items-center
                                    justify-center font-bold text-lg">
                            <?= clean(strtoupper(
                                substr($talent['name'], 0, 1)
                            )) ?>
                        </div>

                        <h3 class="mt-5 font-bold text-lg text-navy">
                            <?= clean($talent['name']) ?>
                        </h3>

                        <p class="text-sm text-amber-700 mt-1">
                            <?= clean($talent['category'] ?: 'Talent') ?>
                        </p>

                        <p class="text-sm text-slate-500 mt-3">
                            <?= clean($talent['location']) ?>
                        </p>

                        <p class="text-sm text-slate-600 mt-3 leading-6">
                            <?= clean($talent['skills']) ?>
                        </p>

                        <a href="talents.php"
                           class="inline-block mt-5 text-sm
                                  font-semibold text-navy
                                  hover:text-amber-700">
                            Discover profile &rarr;
                        </a>

                    </article>
                <?php endforeach; ?>

            <?php else: ?>

                <div class="sm:col-span-2 lg:col-span-3
                            border border-dashed border-slate-300
                            p-8 text-center">

                    <h3 class="font-semibold text-navy">
                        Be among the first talents
                    </h3>

                    <p class="text-sm text-slate-500 mt-2">
                        Register and create a profile to appear here.
                    </p>

                    <a href="register.php"
                       class="inline-block mt-4 text-sm
                              font-semibold text-amber-700">
                        Create your profile &rarr;
                    </a>

                </div>

            <?php endif; ?>

        </div>
    </section>

    <!-- Opportunities -->
    <section class="bg-slate-50 border-y border-slate-200">
        <div class="max-w-6xl mx-auto px-5 py-16">

            <div class="mb-8">
                <p class="text-sm text-amber-700 font-semibold">
                    TAKE YOUR NEXT STEP
                </p>

                <h2 class="text-2xl md:text-3xl font-bold
                           text-navy mt-2">
                    Opportunities to grow
                </h2>

                <p class="mt-2 text-slate-500">
                    Explore support and sponsorship opportunities
                    shared by our community.
                </p>
            </div>

            <div class="grid md:grid-cols-3 gap-5">

                <?php if (count($opportunities) > 0): ?>

                    <?php foreach ($opportunities as $item): ?>
                        <article class="bg-white border border-slate-200
                                        p-6">

                            <p class="text-xs uppercase tracking-wide
                                      text-amber-700 font-semibold">
                                <?= clean($item['category']) ?>
                            </p>

                            <h3 class="text-lg font-bold text-navy mt-3">
                                <?= clean($item['title']) ?>
                            </h3>

                            <p class="text-sm text-slate-600 mt-3">
                                Support:
                                <?= clean($item['support_type']) ?>
                            </p>

                            <p class="text-sm text-slate-500 mt-2">
                                Deadline:
                                <?= clean($item['deadline']) ?>
                            </p>

                            <a href="opportunities.php"
                               class="inline-block mt-5 text-sm
                                      font-semibold text-navy">
                                View opportunity &rarr;
                            </a>

                        </article>
                    <?php endforeach; ?>

                <?php else: ?>

                    <p class="text-slate-500 text-sm md:col-span-3">
                        No opportunities have been published yet.
                        Check back when sponsors add new listings.
                    </p>

                <?php endif; ?>

            </div>

            <a href="opportunities.php"
               class="inline-block mt-8 bg-navy text-white
                      px-5 py-3 text-sm hover:bg-slate-700">
                Browse all opportunities
            </a>

        </div>
    </section>

    <!-- Call to action -->
    <section class="max-w-6xl mx-auto px-5 py-16">
        <div class="bg-navy text-white p-8 md:p-12
                    flex flex-col md:flex-row
                    justify-between items-start md:items-center gap-6">

            <div>
                <p class="text-sm text-amber-300 font-semibold">
                    YOUR NEXT CHAPTER STARTS HERE
                </p>

                <h2 class="text-2xl md:text-3xl font-bold mt-3">
                    Your skills could open a new door.
                </h2>

                <p class="text-slate-300 mt-3 max-w-xl">
                    Create a profile, share what you do,
                    and start exploring opportunities.
                </p>
            </div>

            <a href="register.php"
               class="bg-gold text-navy font-semibold
                      px-5 py-3 hover:bg-amber-300 whitespace-nowrap">
                Join the community
            </a>
        </div>
    </section>

</main>

<!-- Footer -->
<footer class="border-t border-slate-200">
    <div class="max-w-6xl mx-auto px-5 py-8
                flex flex-col sm:flex-row
                justify-between gap-4 text-sm">

        <p class="text-slate-500">
            &copy; <?= date('Y') ?> CLOJO TALENT CONNECT.
            Project by ISHIMWE CLAUDE.
        </p>

        <div class="flex gap-5">
            <a href="about.php" class="hover:text-amber-700">
                About
            </a>

            <a href="talents.php" class="hover:text-amber-700">
                Talents
            </a>

            <a href="opportunities.php"
               class="hover:text-amber-700">
                Opportunities
            </a>
        </div>

    </div>
</footer>

</body>
</html>