
<?php
require_once 'db.php';

function clean($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

// Homepage statistics
$talentCount = $pdo->query("
    SELECT COUNT(*) FROM users WHERE role = 'talent'
")->fetchColumn();

$sponsorCount = $pdo->query("
    SELECT COUNT(*) FROM users WHERE role = 'sponsor'
")->fetchColumn();

$opportunityCount = $pdo->query("
    SELECT COUNT(*)
    FROM opportunities
    WHERE deadline IS NULL OR deadline >= CURDATE()
")->fetchColumn();

// Latest talent profiles
$sql = "
    SELECT
        users.id,
        users.name,
        talent_profiles.location,
        talent_profiles.skills,
        talent_profiles.bio,
        categories.name AS category
    FROM talent_profiles
    JOIN users ON talent_profiles.user_id = users.id
    LEFT JOIN categories ON talent_profiles.category_id = categories.id
    WHERE users.role = 'talent'
    ORDER BY talent_profiles.id DESC
    LIMIT 3
";

$talents = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Latest opportunities
$sql = "
    SELECT
        opportunities.id,
        opportunities.title,
        opportunities.description,
        opportunities.support_type,
        opportunities.deadline,
        categories.name AS category,
        users.name AS sponsor_name
    FROM opportunities
    JOIN users ON opportunities.sponsor_id = users.id
    LEFT JOIN categories ON opportunities.category_id = categories.id
    WHERE opportunities.deadline IS NULL
       OR opportunities.deadline >= CURDATE()
    ORDER BY opportunities.id DESC
    LIMIT 3
";

$opportunities = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>CLOJO Talent Connect | Find Opportunities</title>

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
        };
    </script>
</head>

<body class="bg-white text-slate-800">

<!-- Navigation -->
<header class="border-b border-slate-200">
    <nav class="max-w-6xl mx-auto px-5 py-4 flex items-center justify-between gap-5">

        <a href="index.php" class="flex items-center gap-3">
            <span class="bg-navy text-gold font-bold text-xl w-10 h-10 flex items-center justify-center">
                C
            </span>

            <span>
                <span class="block text-navy font-bold tracking-wide">CLOJO</span>
                <span class="block text-xs text-slate-500">TALENT CONNECT</span>
            </span>
        </a>

        <div class="hidden md:flex items-center gap-7 text-sm">
            <a href="index.php" class="text-navy font-semibold">Home</a>
            <a href="talents.php" class="hover:text-gold">Talents</a>
            <a href="opportunities.php" class="hover:text-gold">Opportunities</a>
            <a href="about.php" class="hover:text-gold">About</a>
        </div>

        <div class="flex items-center gap-3 text-sm">
            <a href="login.php" class="hidden sm:inline hover:text-gold">Log in</a>

            <a href="register.php"
               class="bg-navy text-white px-4 py-2.5 hover:bg-slate-700 transition">
                Join CLOJO
            </a>
        </div>

    </nav>
</header>

<main>

    <!-- Main introduction -->
    <section class="bg-slate-50 border-b border-slate-200">
        <div class="max-w-6xl mx-auto px-5 py-16 md:py-24 grid md:grid-cols-2 gap-12 items-center">

            <div>
                <p class="text-sm font-semibold uppercase tracking-widest text-amber-700 mb-4">
                    Talent starts with an opportunity
                </p>

                <h1 class="text-4xl md:text-5xl font-bold text-navy leading-tight">
                    Show your talent. Find your next opportunity.
                </h1>

                <p class="mt-5 text-slate-600 leading-7 max-w-lg">
                    CLOJO connects talented people with sponsors who can
                    support their ideas, skills and ambitions through funding,
                    equipment, training and mentorship.
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="talents.php"
                       class="bg-navy text-white px-5 py-3 hover:bg-slate-700 transition">
                        Explore talents &rarr;
                    </a>

                    <a href="opportunities.php"
                       class="border border-slate-300 px-5 py-3 hover:border-navy transition">
                        Find opportunities
                    </a>
                </div>
            </div>

            <div>
                <img
                    src="https://images.unsplash.com/photo-1521737711867-e3b97375f902?auto=format&fit=crop&w=1000&q=85"
                    alt="A team working together"
                    class="w-full h-72 md:h-96 object-cover"
                >

                <p class="mt-3 text-xs text-slate-500">
                    CLOJO TALENT CONNECT — Project by ISHIMWE CLAUDE
                </p>
            </div>

        </div>
    </section>

    <!-- Statistics -->
    <section class="border-b border-slate-200">
        <div class="max-w-6xl mx-auto px-5 py-8 grid grid-cols-1 sm:grid-cols-3 gap-6">

            <div>
                <p class="text-3xl font-bold text-navy">
                    <?= (int) $talentCount ?>
                </p>
                <p class="mt-1 text-sm text-slate-500">Registered talents</p>
            </div>

            <div>
                <p class="text-3xl font-bold text-navy">
                    <?= (int) $opportunityCount ?>
                </p>
                <p class="mt-1 text-sm text-slate-500">Current opportunities</p>
            </div>

            <div>
                <p class="text-3xl font-bold text-navy">
                    <?= (int) $sponsorCount ?>
                </p>
                <p class="mt-1 text-sm text-slate-500">Registered sponsors</p>
            </div>

        </div>
    </section>

    <!-- Talents -->
    <section class="max-w-6xl mx-auto px-5 py-16">

        <div class="flex flex-wrap justify-between items-end gap-4 mb-8">
            <div>
                <p class="text-sm text-amber-700 font-semibold">OUR COMMUNITY</p>

                <h2 class="text-2xl md:text-3xl font-bold text-navy mt-2">
                    Meet our talents
                </h2>

                <p class="mt-2 text-slate-500">
                    Discover people and the skills they bring.
                </p>
            </div>

            <a href="talents.php" class="text-sm font-semibold text-navy hover:text-amber-700">
                View all talents &rarr;
            </a>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">

            <?php if (count($talents) > 0): ?>

                <?php foreach ($talents as $talent): ?>
                    <article class="border border-slate-200 p-6 hover:border-slate-400 transition">

                        <div class="w-12 h-12 bg-slate-100 text-navy flex items-center justify-center font-bold text-lg">
                            <?= clean(strtoupper(substr($talent['name'], 0, 1))) ?>
                        </div>

                        <h3 class="mt-5 font-bold text-lg text-navy">
                            <?= clean($talent['name']) ?>
                        </h3>

                        <p class="text-sm text-amber-700 mt-1">
                            <?= clean($talent['category'] ?: 'Other') ?>
                        </p>

                        <p class="text-sm text-slate-500 mt-3">
                            <?= clean($talent['location'] ?: 'Location not added') ?>
                        </p>

                        <p class="text-sm text-slate-600 mt-3 leading-6">
                            <?= clean($talent['skills'] ?: 'Skills have not been added yet.') ?>
                        </p>

                        <a href="talents.php"
                           class="inline-block mt-5 text-sm font-semibold text-navy hover:text-amber-700">
                            Discover talent &rarr;
                        </a>

                    </article>
                <?php endforeach; ?>

            <?php else: ?>

                <div class="sm:col-span-2 lg:col-span-3 border border-slate-200 p-8">
                    <h3 class="font-semibold text-navy">No talent profiles yet</h3>

                    <p class="text-sm text-slate-500 mt-2">
                        Create a profile to introduce your skills to potential sponsors.
                    </p>

                    <a href="register.php"
                       class="inline-block mt-4 text-sm font-semibold text-amber-700">
                        Register as a talent &rarr;
                    </a>
                </div>

            <?php endif; ?>

        </div>
    </section>

    <!-- Opportunities -->
    <section class="bg-slate-50 border-y border-slate-200">
        <div class="max-w-6xl mx-auto px-5 py-16">

            <div class="flex flex-wrap justify-between items-end gap-4 mb-8">
                <div>
                    <p class="text-sm text-amber-700 font-semibold">TAKE THE NEXT STEP</p>

                    <h2 class="text-2xl md:text-3xl font-bold text-navy mt-2">
                        Latest opportunities
                    </h2>

                    <p class="mt-2 text-slate-500">
                        Find support that matches your goals.
                    </p>
                </div>

                <a href="opportunities.php"
                   class="text-sm font-semibold text-navy hover:text-amber-700">
                    View all opportunities &rarr;
                </a>
            </div>

            <div class="grid md:grid-cols-3 gap-5">

                <?php if (count($opportunities) > 0): ?>

                    <?php foreach ($opportunities as $item): ?>
                        <article class="bg-white border border-slate-200 p-6">

                            <p class="text-xs uppercase tracking-wide text-amber-700 font-semibold">
                                <?= clean($item['category'] ?: 'General') ?>
                            </p>

                            <h3 class="text-lg font-bold text-navy mt-3">
                                <?= clean($item['title']) ?>
                            </h3>

                            <p class="text-sm text-slate-600 mt-3 leading-6">
                                <?php
                                $description = $item['description'];
                                if (strlen($description) > 120) {
                                    $description = substr($description, 0, 120) . '...';
                                }
                                echo clean($description);
                                ?>
                            </p>

                            <p class="text-sm text-slate-600 mt-4">
                                <span class="font-semibold">Support:</span>
                                <?= clean($item['support_type']) ?>
                            </p>

                            <p class="text-sm text-slate-500 mt-2">
                                <span class="font-semibold">Sponsor:</span>
                                <?= clean($item['sponsor_name']) ?>
                            </p>

                            <p class="text-sm text-slate-500 mt-2">
                                <span class="font-semibold">Deadline:</span>
                                <?= $item['deadline']
                                    ? clean(date('d M Y', strtotime($item['deadline'])))
                                    : 'No deadline set' ?>
                            </p>

                            <a href="opportunities.php"
                               class="inline-block mt-5 text-sm font-semibold text-navy hover:text-amber-700">
                                View opportunities &rarr;
                            </a>

                        </article>
                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="md:col-span-3 border border-slate-200 bg-white p-8">
                        <h3 class="font-semibold text-navy">
                            No opportunities available right now
                        </h3>

                        <p class="text-sm text-slate-500 mt-2">
                            Check back later to see what sponsors have posted.
                        </p>
                    </div>

                <?php endif; ?>

            </div>

        </div>
    </section>

    <!-- Join section -->
    <section class="max-w-6xl mx-auto px-5 py-16">
        <div class="bg-navy text-white p-8 md:p-12 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">

            <div>
                <p class="text-sm text-amber-300 font-semibold">
                    GET STARTED
                </p>

                <h2 class="text-2xl md:text-3xl font-bold mt-3">
                    Have a skill or an opportunity to share?
                </h2>

                <p class="text-slate-300 mt-3 max-w-xl">
                    Join CLOJO to showcase your talent or offer support to
                    someone working towards their goals.
                </p>
            </div>

            <a href="register.php"
               class="bg-gold text-navy font-semibold px-5 py-3 hover:bg-amber-300 whitespace-nowrap">
                Create an account
            </a>

        </div>
    </section>

</main>

<!-- Footer -->
<footer class="border-t border-slate-200">
    <div class="max-w-6xl mx-auto px-5 py-8 flex flex-col sm:flex-row justify-between gap-4 text-sm">

        <p class="text-slate-500">
            &copy; <?= date('Y') ?> CLOJO TALENT CONNECT.
            Project by ISHIMWE CLAUDE.
        </p>

        <div class="flex gap-5">
            <a href="about.php" class="hover:text-amber-700">About</a>
            <a href="talents.php" class="hover:text-amber-700">Talents</a>
            <a href="opportunities.php" class="hover:text-amber-700">Opportunities</a>
        </div>

    </div>
</footer>

</body>
</html>