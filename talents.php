
<?php
require_once 'db.php';
require_once 'includes/functions.php';

$search = trim($_GET['search'] ?? '');
$category = (int) ($_GET['category'] ?? 0);

$categories = $pdo->query("
    SELECT id, name
    FROM categories
    ORDER BY name
")->fetchAll();

$sql = "
    SELECT
        tp.*,
        u.name,
        u.id AS user_id,
        c.name AS category_name
    FROM talent_profiles tp
    JOIN users u ON tp.user_id = u.id
    LEFT JOIN categories c ON tp.category_id = c.id
    WHERE u.role = 'talent'
";

$params = [];

if ($search !== '') {
    $sql .= "
        AND (
            u.name LIKE ?
            OR tp.skills LIKE ?
            OR tp.location LIKE ?
            OR tp.bio LIKE ?
            OR c.name LIKE ?
        )
    ";

    $term = "%$search%";
    array_push($params, $term, $term, $term, $term, $term);
}

if ($category > 0) {
    $sql .= " AND tp.category_id = ?";
    $params[] = $category;
}

$sql .= " ORDER BY tp.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$talents = $stmt->fetchAll();

$pageTitle = 'Discover talent';

require 'includes/header.php';
?>

<section class="bg-slate-50 border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-5 py-10 md:py-12">

        <p class="text-sm font-semibold uppercase tracking-widest text-amber-700">
            CLOJO Talent Connect
        </p>

        <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mt-3">
            Discover talent
        </h1>

        <p class="text-slate-600 mt-3 max-w-2xl leading-7">
            Explore profiles, learn about people's skills and find talent
            that matches your interests or opportunities.
        </p>

    </div>
</section>

<section class="max-w-7xl mx-auto px-5 py-10">
    <form method="get" action="talents.php"
          class="bg-white border border-slate-200 p-4 mb-8">

        <div class="grid md:grid-cols-[1fr_240px_auto] gap-3">

            <div>
                <label for="search"
                       class="block text-sm font-semibold text-slate-700 mb-2">
                    Search talents
                </label>

                <input
                    id="search"
                    type="search"
                    name="search"
                    value="<?= e($search) ?>"
                    placeholder="Name, skill, location..."
                    class="w-full border border-slate-300 px-4 py-3 focus:outline-none focus:border-amber-600"
                >
            </div>

            <div>
                <label for="category"
                       class="block text-sm font-semibold text-slate-700 mb-2">
                    Category
                </label>

                <select
                    id="category"
                    name="category"
                    class="w-full border border-slate-300 bg-white px-4 py-3 focus:outline-none focus:border-amber-600"
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
            </div>

            <div class="flex items-end gap-3">
                <button
                    type="submit"
                    class="bg-slate-900 hover:bg-slate-800 text-white px-6 py-3 font-semibold"
                >
                    Search
                </button>

                <?php if ($search !== '' || $category > 0): ?>
                    <a
                        href="talents.php"
                        class="border border-slate-300 hover:bg-slate-50 px-4 py-3 text-slate-700 text-sm font-semibold"
                    >
                        Clear
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </form>

    <div class="flex flex-wrap justify-between items-center gap-3 mb-6">

        <div>
            <h2 class="text-2xl font-bold text-slate-900">
                Talent directory
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Browse profiles from the CLOJO community.
            </p>
        </div>

        <p class="text-sm text-slate-500">
            <?= count($talents) ?> profile<?= count($talents) === 1 ? '' : 's' ?> found
        </p>

    </div>

    <?php if ($talents): ?>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">

            <?php foreach ($talents as $t): ?>

                <article class="bg-white border border-slate-200 overflow-hidden flex flex-col">
                    <div class="h-48 bg-slate-100 border-b border-slate-200 flex items-center justify-center overflow-hidden">

                        <?php if (!empty($t['image_url'])): ?>

                            <img
                                src="<?= e($t['image_url']) ?>"
                                alt="<?= e($t['name']) ?>"
                                loading="lazy"
                                class="w-full h-full object-cover"
                            >

                        <?php else: ?>

                            <div class="w-20 h-20 flex items-center justify-center bg-slate-900 text-white text-3xl font-bold">
                                <?= e(strtoupper(substr($t['name'], 0, 1))) ?>
                            </div>

                        <?php endif; ?>

                    </div>

                    <div class="p-5 flex flex-col flex-1">

                        <p class="text-sm font-semibold text-amber-700">
                            <?= e($t['category_name'] ?? 'Talent') ?>
                        </p>

                        <h3 class="text-xl font-bold text-slate-900 mt-2">
                            <?= e($t['name']) ?>
                        </h3>

                        <p class="text-sm text-slate-500 mt-2">
                            <?= e($t['location'] ?: 'Rwanda') ?>
                        </p>

                        <p class="text-slate-600 text-sm leading-6 mt-4">
                            <?php
                            $description = trim($t['bio'] ?? '');

                            if ($description === '') {
                                $description = trim($t['skills'] ?? '');
                            }

                            if ($description === '') {
                                $description = 'This talent has not added a description yet.';
                            }

                            if (function_exists('mb_strimwidth')) {
                                echo e(mb_strimwidth($description, 0, 130, '...'));
                            } else {
                                echo e(strlen($description) > 130
                                    ? substr($description, 0, 127) . '...'
                                    : $description);
                            }
                            ?>
                        </p>

                        <?php if (!empty($t['skills'])): ?>
                            <div class="mt-4 pt-4 border-t border-slate-200">
                                <p class="text-xs uppercase tracking-wide font-semibold text-slate-500 mb-2">
                                    Skills
                                </p>

                                <p class="text-sm text-slate-700">
                                    <?= e($t['skills']) ?>
                                </p>
                            </div>
                        <?php endif; ?>

                        <div class="mt-auto pt-5">
                            <a
                                href="profile.php?id=<?= (int) $t['user_id'] ?>"
                                class="inline-flex items-center gap-2 text-amber-700 hover:text-amber-800 font-semibold text-sm"
                            >
                                View profile
                                <span aria-hidden="true">&rarr;</span>
                            </a>
                        </div>

                    </div>
                </article>

            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div class="bg-white border border-slate-200 p-8 md:p-12 text-center">

            <h3 class="text-xl font-bold text-slate-900">
                No talents found
            </h3>

            <p class="text-slate-600 mt-3 max-w-md mx-auto leading-6">
                Try a different name, skill or category. You can also join
                CLOJO and create your own talent profile.
            </p>

            <a
                href="register.php"
                class="inline-block mt-6 bg-slate-900 hover:bg-slate-800 text-white px-6 py-3 font-semibold"
            >
                Join as a talent
            </a>

        </div>

    <?php endif; ?>

</section>

<?php require 'includes/footer.php'; ?>