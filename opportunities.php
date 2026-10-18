
<?php
require 'db.php';
require_once 'includes/functions.php';

$error = '';
$success = '';

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['create_opportunity'])
) {
    require_role('sponsor');
    check_csrf();

    $title = trim($_POST['title'] ?? '');
    $desc = trim($_POST['description'] ?? '');
    $type = $_POST['support_type'] ?? 'Mentorship';
    $cat = (int) ($_POST['category_id'] ?? 0);
    $deadline = $_POST['deadline'] ?? '';

    $types = [
        'Funding',
        'Equipment',
        'Mentorship',
        'Training',
        'Exposure'
    ];

    if (
        $title === '' ||
        $desc === '' ||
        !in_array($type, $types, true)
    ) {
        $error = 'Complete the title, description, and support type.';
    } elseif (
        $deadline !== '' &&
        $deadline < date('Y-m-d')
    ) {
        $error = 'The deadline cannot be in the past.';
    } else {
        $q = $pdo->prepare("
            INSERT INTO opportunities (
                sponsor_id,
                title,
                description,
                support_type,
                category_id,
                deadline
            )
            VALUES (?, ?, ?, ?, NULLIF(?, 0), NULLIF(?, ''))
        ");

        $q->execute([
            $_SESSION['user_id'],
            $title,
            $desc,
            $type,
            $cat,
            $deadline
        ]);

        $success = 'Your opportunity has been published.';
    }
}

$search = trim($_GET['search'] ?? '');

$sql = "
    SELECT
        o.*,
        u.name AS sponsor_name,
        c.name AS category_name
    FROM opportunities o
    JOIN users u ON o.sponsor_id = u.id
    LEFT JOIN categories c ON o.category_id = c.id
    WHERE (o.deadline IS NULL OR o.deadline >= CURDATE())
";

$params = [];

if ($search !== '') {
    $sql .= "
        AND (
            o.title LIKE ?
            OR o.description LIKE ?
            OR o.support_type LIKE ?
        )
    ";

    $term = "%$search%";
    array_push($params, $term, $term, $term);
}

$sql .= " ORDER BY o.id DESC";

$q = $pdo->prepare($sql);
$q->execute($params);

$opps = $q->fetchAll();

$categories = $pdo->query("
    SELECT *
    FROM categories
    ORDER BY name
")->fetchAll();

$pageTitle = 'Opportunities';

require 'includes/header.php';
?>

<!-- Hero Section -->
<section class="bg-clojo-navy text-white hero-grid">
    <div class="max-w-7xl mx-auto px-5 py-12 flex flex-col md:flex-row md:items-end justify-between gap-6">

        <div>
            <p class="text-clojo-gold uppercase tracking-widest text-sm font-bold">
                Your next step
            </p>

            <h1 class="text-4xl font-extrabold mt-2">
                Opportunities are waiting.
            </h1>

            <p class="text-slate-300 mt-3">
                Find support, mentorship, equipment, and new ways to grow.
            </p>
        </div>

        <?php if (($_SESSION['role'] ?? '') === 'sponsor'): ?>
            <a
                href="#create"
                class="bg-clojo-gold text-clojo-navy font-bold px-5 py-3 rounded-full"
            >
                + Post opportunity
            </a>
        <?php endif; ?>

    </div>
</section>

<!-- Opportunities Content -->
<section class="max-w-7xl mx-auto px-5 py-10">

    <?php if ($error): ?>
        <p class="bg-red-50 text-red-700 p-4 rounded-xl mb-4">
            <?= e($error) ?>
        </p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="bg-green-50 text-green-700 p-4 rounded-xl mb-4">
            <?= e($success) ?>
        </p>
    <?php endif; ?>

    <!-- Search Form -->
    <form class="flex flex-col sm:flex-row gap-3 mb-8">
        <input
            name="search"
            value="<?= e($search) ?>"
            placeholder="Search opportunities..."
            class="flex-1 border rounded-xl px-4 py-3"
        >

        <button class="bg-clojo-navy text-white rounded-xl px-6 py-3 font-bold">
            Search
        </button>
    </form>

    <!-- Create Opportunity Form -->
    <?php if (($_SESSION['role'] ?? '') === 'sponsor'): ?>

        <div id="create" class="bg-white border rounded-2xl p-6 md:p-8 mb-10">

            <h2 class="text-2xl font-extrabold">
                Post a new opportunity
            </h2>

            <form method="post" class="grid md:grid-cols-2 gap-4 mt-5">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e(csrf_token()) ?>"
                >

                <input
                    type="hidden"
                    name="create_opportunity"
                    value="1"
                >

                <div>
                    <label class="block text-sm font-semibold mb-2">
                        Title
                    </label>

                    <input
                        name="title"
                        required
                        maxlength="150"
                        class="w-full border rounded-xl p-3"
                    >
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">
                        Support type
                    </label>

                    <select
                        name="support_type"
                        class="w-full border rounded-xl p-3 bg-white"
                    >
                        <?php foreach ([
                            'Funding',
                            'Equipment',
                            'Mentorship',
                            'Training',
                            'Exposure'
                        ] as $type): ?>
                            <option><?= e($type) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">
                        Category
                    </label>

                    <select
                        name="category_id"
                        class="w-full border rounded-xl p-3 bg-white"
                    >
                        <option value="0">All categories</option>

                        <?php foreach ($categories as $c): ?>
                            <option value="<?= (int) $c['id'] ?>">
                                <?= e($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold mb-2">
                        Deadline (optional)
                    </label>

                    <input
                        type="date"
                        name="deadline"
                        min="<?= date('Y-m-d') ?>"
                        class="w-full border rounded-xl p-3"
                    >
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold mb-2">
                        Description and requirements
                    </label>

                    <textarea
                        name="description"
                        required
                        rows="3"
                        class="w-full border rounded-xl p-3"
                    ></textarea>
                </div>

                <div class="md:col-span-2">
                    <button class="bg-clojo-navy text-white px-6 py-3 rounded-xl font-bold">
                        Publish opportunity
                    </button>
                </div>

            </form>
        </div>

    <?php endif; ?>

    <!-- Open Opportunities Heading -->
    <div class="flex justify-between mb-6">
        <h2 class="text-2xl font-extrabold text-clojo-navy">
            Open opportunities
        </h2>

        <span class="text-sm text-slate-500">
            <?= count($opps) ?> result(s)
        </span>
    </div>

    <!-- Opportunities List -->
    <?php if ($opps): ?>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">

            <?php foreach ($opps as $o): ?>
                <article class="bg-white rounded-2xl border p-6 card-hover flex flex-col">

                    <div class="flex justify-between gap-3">
                        <span class="bg-amber-50 text-amber-800 text-xs font-bold px-3 py-1.5 rounded-full">
                            <?= e($o['support_type']) ?>
                        </span>

                        <span class="text-xs text-slate-500">
                            <?= e($o['category_name'] ?? 'All categories') ?>
                        </span>
                    </div>

                    <h3 class="text-xl font-extrabold mt-5">
                        <?= e($o['title']) ?>
                    </h3>

                    <p class="text-slate-500 text-sm mt-2">
                        By <?= e($o['sponsor_name']) ?>
                    </p>

                    <p class="text-slate-600 mt-4 flex-1 whitespace-pre-line">
                        <?= e(mb_strimwidth($o['description'], 0, 190, '...')) ?>
                    </p>

                    <p class="text-xs text-slate-500 mt-5 mb-4">
                        Deadline: <?= e($o['deadline'] ?: 'Not specified') ?>
                    </p>

                    <?php if (($_SESSION['role'] ?? '') === 'talent'): ?>

                        <form method="post" action="apply.php">
                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= e(csrf_token()) ?>"
                            >

                            <input
                                type="hidden"
                                name="opportunity_id"
                                value="<?= (int) $o['id'] ?>"
                            >

                            <button class="w-full bg-clojo-navy text-white rounded-xl py-3 font-bold">
                                Apply now →
                            </button>
                        </form>

                    <?php elseif (empty($_SESSION['user_id'])): ?>

                        <a
                            href="login.php"
                            class="text-center bg-clojo-navy text-white rounded-xl py-3 font-bold"
                        >
                            Login to apply →
                        </a>

                    <?php else: ?>

                        <p class="text-sm text-slate-500 bg-slate-50 rounded-xl p-3">
                            Talent accounts can apply.
                        </p>

                    <?php endif; ?>

                </article>
            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div class="bg-white border border-dashed rounded-2xl p-12 text-center">
            <h3 class="font-extrabold text-xl">
                No matching opportunities
            </h3>

            <p class="text-slate-500 mt-2">
                Try another search or check back later.
            </p>
        </div>

    <?php endif; ?>

</section>

<?php require 'includes/footer.php'; ?>