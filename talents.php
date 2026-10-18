
<?php

require 'db.php';
require_once 'includes/functions.php';

$search = trim($_GET['search'] ?? '');
$category = (int) ($_GET['category'] ?? 0);

$categories = $pdo->query("
    SELECT *
    FROM categories
    ORDER BY name
")->fetchAll();

$sql = "
    SELECT
        tp.*,
        u.name,
        c.name AS category_name
    FROM talent_profiles tp
    JOIN users u ON tp.user_id = u.id
    LEFT JOIN categories c ON tp.category_id = c.id
    WHERE 1 = 1
";

$params = [];

if ($search !== '') {
    $sql .= "
        AND (
            u.name LIKE ?
            OR tp.skills LIKE ?
            OR tp.location LIKE ?
        )
    ";

    $term = "%$search%";
    array_push($params, $term, $term, $term);
}

if ($category > 0) {
    $sql .= " AND tp.category_id = ?";
    $params[] = $category;
}

$sql .= " ORDER BY tp.id DESC";

$q = $pdo->prepare($sql);
$q->execute($params);

$talents = $q->fetchAll();

$pageTitle = 'Discover talent';

require 'includes/header.php';

?>

<section class="bg-clojo-navy text-white">
    <div class="max-w-7xl mx-auto px-5 py-12">

        <p class="text-clojo-gold uppercase tracking-widest text-sm font-bold">
            Discover creators
        </p>

        <h1 class="text-4xl font-extrabold mt-2">
            Talent has no limits.
        </h1>

        <p class="text-slate-300 mt-3">
            Meet people with skills, ideas, and stories worth discovering.
        </p>

    </div>
</section>

<section class="max-w-7xl mx-auto px-5 py-10">

    <!-- Search and Category Filter -->
    <form class="bg-white border rounded-2xl p-4 grid md:grid-cols-[1fr_240px_auto] gap-3 mb-8">

        <input
            name="search"
            value="<?= e($search) ?>"
            placeholder="Search name, skill, or location..."
            class="border rounded-xl px-4 py-3"
        >

        <select
            name="category"
            class="border rounded-xl px-4 py-3 bg-white"
        >
            <option value="0">All categories</option>

            <?php foreach ($categories as $c): ?>
                <option
                    value="<?= (int) $c['id'] ?>"
                    <?= $category === (int) $c['id'] ? 'selected' : '' ?>
                >
                    <?= e($c['name']) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button class="bg-clojo-navy text-white rounded-xl px-6 py-3 font-bold">
            Search
        </button>

    </form>

    <!-- Directory Heading -->
    <div class="flex justify-between mb-6">

        <h2 class="text-2xl font-extrabold text-clojo-navy">
            Talent directory
        </h2>

        <span class="text-slate-500 text-sm">
            <?= count($talents) ?> profile(s)
        </span>

    </div>

    <?php if ($talents): ?>

        <!-- Talent Cards -->
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">

            <?php foreach ($talents as $t): ?>

                <article class="bg-white rounded-2xl border overflow-hidden card-hover">

                    <div class="h-48 bg-gradient-to-br from-clojo-navy to-blue-900">

                        <?php if ($t['image_url']): ?>

                            <img
                                src="<?= e($t['image_url']) ?>"
                                alt="<?= e($t['name']) ?>"
                                class="w-full h-full object-cover"
                            >

                        <?php else: ?>

                            <div class="h-full flex items-center justify-center text-clojo-gold text-6xl">
                                ✦
                            </div>

                        <?php endif; ?>

                    </div>

                    <div class="p-5">

                        <span class="text-xs bg-amber-50 text-amber-800 px-3 py-1 rounded-full font-bold">
                            <?= e($t['category_name'] ?? 'Talent') ?>
                        </span>

                        <h3 class="text-xl font-extrabold mt-3">
                            <?= e($t['name']) ?>
                        </h3>

                        <p class="text-sm text-slate-500 mt-1">
                            📍 <?= e($t['location'] ?: 'Rwanda') ?>
                        </p>

                        <p class="text-slate-600 mt-3">
                            <?= e(mb_strimwidth($t['bio'] ?: $t['skills'], 0, 120, '...')) ?>
                        </p>

                        <a
                            href="profile.php?id=<?= (int) $t['user_id'] ?>"
                            class="inline-block mt-5 font-bold text-amber-700"
                        >
                            View profile →
                        </a>

                    </div>

                </article>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <!-- Empty Search Results -->
        <div class="text-center bg-white border border-dashed rounded-2xl p-12">

            <h3 class="font-bold text-xl">
                No talents found
            </h3>

            <p class="text-slate-500 mt-2">
                Try another search, or create a profile.
            </p>

            <a
                href="register.php"
                class="inline-block mt-5 bg-clojo-navy text-white px-5 py-3 rounded-full"
            >
                Join as a talent
            </a>

        </div>

    <?php endif; ?>

</section>

<?php require 'includes/footer.php'; ?>
