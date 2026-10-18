
<?php
require_once 'db.php';
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
        (
            !preg_match('/^\d{4}-\d{2}-\d{2}$/', $deadline) ||
            !checkdate(
                (int) substr($deadline, 5, 2),
                (int) substr($deadline, 8, 2),
                (int) substr($deadline, 0, 4)
            ) ||
            $deadline < date('Y-m-d')
        )
    ) {
        $error = 'Please enter a valid deadline that is today or later.';
    } else {
        $stmt = $pdo->prepare("
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

        $stmt->execute([
            (int) $_SESSION['user_id'],
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
            OR c.name LIKE ?
            OR u.name LIKE ?
        )
    ";

    $term = "%$search%";
    array_push($params, $term, $term, $term, $term, $term);
}

$sql .= " ORDER BY o.id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$opps = $stmt->fetchAll();

$categories = $pdo->query("
    SELECT id, name
    FROM categories
    ORDER BY name
")->fetchAll();

$pageTitle = 'Opportunities';
require 'includes/header.php';
?>

<section class="bg-slate-50 border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-5 py-12">
        <p class="text-sm font-semibold uppercase tracking-widest text-amber-700">
            CLOJO Talent Connect
        </p>

        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mt-3">
                    Opportunities
                </h1>

                <p class="text-slate-600 mt-3 max-w-2xl">
                    Find funding, equipment, mentorship and other support
                    to help you develop your talent.
                </p>
            </div>

            <?php if (($_SESSION['role'] ?? '') === 'sponsor'): ?>
                <a href="#create"
                   class="inline-block bg-slate-900 hover:bg-slate-800 text-white px-5 py-3 text-sm font-semibold text-center">
                    Post an opportunity
                </a>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="max-w-6xl mx-auto px-5 py-10">

    <?php if ($error): ?>
        <div class="border border-red-200 bg-red-50 text-red-700 p-4 mb-5 text-sm">
            <?= e($error) ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="border border-green-200 bg-green-50 text-green-800 p-4 mb-5 text-sm">
            <?= e($success) ?>
        </div>
    <?php endif; ?>


    <form method="get" action="opportunities.php"
          class="flex flex-col sm:flex-row gap-3 mb-10">

        <div class="flex-1">
            <label for="search" class="sr-only">
                Search opportunities
            </label>

            <input
                id="search"
                type="search"
                name="search"
                value="<?= e($search) ?>"
                placeholder="Search by title, category or support type..."
                class="w-full border border-slate-300 bg-white px-4 py-3 focus:outline-none focus:border-amber-600"
            >
        </div>

        <button type="submit"
                class="bg-slate-900 hover:bg-slate-800 text-white px-6 py-3 font-semibold">
            Search
        </button>

        <?php if ($search !== ''): ?>
            <a href="opportunities.php"
               class="border border-slate-300 px-5 py-3 text-center text-slate-700 hover:bg-slate-50">
                Clear
            </a>
        <?php endif; ?>
    </form>


    <?php if (($_SESSION['role'] ?? '') === 'sponsor'): ?>

        <div id="create" class="bg-white border border-slate-200 p-6 md:p-8 mb-12">
            <h2 class="text-2xl font-bold text-slate-900">
                Post a new opportunity
            </h2>

            <p class="text-slate-600 text-sm mt-2">
                Provide the details so interested talents can understand what you offer.
            </p>

            <form method="post" action="opportunities.php#create"
                  class="grid md:grid-cols-2 gap-5 mt-7">

                <input type="hidden" name="csrf_token"
                       value="<?= e(csrf_token()) ?>">

                <input type="hidden" name="create_opportunity" value="1">

                <div>
                    <label for="title"
                           class="block text-sm font-semibold text-slate-700 mb-2">
                        Opportunity title
                    </label>

                    <input
                        id="title"
                        name="title"
                        required
                        maxlength="150"
                        value="<?= e($_POST['title'] ?? '') ?>"
                        class="w-full border border-slate-300 px-4 py-3 focus:outline-none focus:border-amber-600"
                    >
                </div>

                <div>
                    <label for="support_type"
                           class="block text-sm font-semibold text-slate-700 mb-2">
                        Type of support
                    </label>

                    <select
                        id="support_type"
                        name="support_type"
                        class="w-full border border-slate-300 bg-white px-4 py-3 focus:outline-none focus:border-amber-600"
                    >
                        <?php foreach ([
                            'Funding',
                            'Equipment',
                            'Mentorship',
                            'Training',
                            'Exposure'
                        ] as $supportType): ?>
                            <option
                                value="<?= e($supportType) ?>"
                                <?= ($_POST['support_type'] ?? 'Mentorship') === $supportType ? 'selected' : '' ?>
                            >
                                <?= e($supportType) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="category_id"
                           class="block text-sm font-semibold text-slate-700 mb-2">
                        Category
                    </label>

                    <select
                        id="category_id"
                        name="category_id"
                        class="w-full border border-slate-300 bg-white px-4 py-3 focus:outline-none focus:border-amber-600"
                    >
                        <option value="0">Choose a category</option>

                        <?php foreach ($categories as $c): ?>
                            <option
                                value="<?= (int) $c['id'] ?>"
                                <?= (int) ($_POST['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>
                            >
                                <?= e($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label for="deadline"
                           class="block text-sm font-semibold text-slate-700 mb-2">
                        Application deadline (optional)
                    </label>

                    <input
                        id="deadline"
                        type="date"
                        name="deadline"
                        min="<?= date('Y-m-d') ?>"
                        value="<?= e($_POST['deadline'] ?? '') ?>"
                        class="w-full border border-slate-300 px-4 py-3 focus:outline-none focus:border-amber-600"
                    >
                </div>

                <div class="md:col-span-2">
                    <label for="description"
                           class="block text-sm font-semibold text-slate-700 mb-2">
                        Description and requirements
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        required
                        rows="5"
                        class="w-full border border-slate-300 px-4 py-3 focus:outline-none focus:border-amber-600"
                    ><?= e($_POST['description'] ?? '') ?></textarea>
                </div>

                <div class="md:col-span-2">
                    <button type="submit"
                            class="bg-slate-900 hover:bg-slate-800 text-white px-6 py-3 font-semibold">
                        Publish opportunity
                    </button>
                </div>

            </form>
        </div>

    <?php endif; ?>


    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-900">
                Open opportunities
            </h2>

            <p class="text-sm text-slate-500 mt-1">
                Browse the opportunities currently available.
            </p>
        </div>

        <p class="text-sm text-slate-500">
            <?= count($opps) ?> result(s)
        </p>
    </div>


    <?php if ($opps): ?>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">

            <?php foreach ($opps as $o): ?>
                <article class="bg-white border border-slate-200 p-6 flex flex-col">

                    <div class="flex flex-wrap items-center gap-2 mb-4">
                        <span class="text-xs font-semibold text-amber-800 bg-amber-50 px-3 py-1.5">
                            <?= e($o['support_type']) ?>
                        </span>

                        <span class="text-xs text-slate-500">
                            <?= e($o['category_name'] ?? 'Uncategorised') ?>
                        </span>
                    </div>

                    <h3 class="text-xl font-bold text-slate-900">
                        <?= e($o['title']) ?>
                    </h3>

                    <p class="text-sm text-slate-500 mt-2">
                        Posted by <?= e($o['sponsor_name']) ?>
                    </p>

                    <p class="text-slate-600 text-sm leading-6 mt-4 flex-1 whitespace-pre-line"><?= e(mb_strimwidth($o['description'], 0, 190, '...')) ?></p>

                    <div class="border-t border-slate-200 mt-5 pt-4 mb-4">
                        <p class="text-xs text-slate-500">
                            Deadline
                        </p>

                        <p class="text-sm text-slate-700 mt-1">
                            <?= e($o['deadline'] ?: 'No deadline specified') ?>
                        </p>
                    </div>

                    <?php if (($_SESSION['role'] ?? '') === 'talent'): ?>

                        <form method="post" action="apply.php">
                            <input type="hidden" name="csrf_token"
                                   value="<?= e(csrf_token()) ?>">

                            <input type="hidden" name="opportunity_id"
                                   value="<?= (int) $o['id'] ?>">

                            <button type="submit"
                                    class="w-full bg-slate-900 hover:bg-slate-800 text-white py-3 text-sm font-semibold">
                                Apply now →
                            </button>
                        </form>

                    <?php elseif (empty($_SESSION['user_id'])): ?>

                        <a href="login.php"
                           class="block text-center bg-slate-900 hover:bg-slate-800 text-white py-3 text-sm font-semibold">
                            Login to apply
                        </a>

                    <?php else: ?>

                        <p class="text-sm text-slate-500 border-t border-slate-200 pt-4">
                            Applications are available to talent accounts.
                        </p>

                    <?php endif; ?>

                </article>
            <?php endforeach; ?>

        </div>

    <?php else: ?>

        <div class="bg-white border border-slate-200 p-10 text-center">
            <h3 class="text-xl font-bold text-slate-900">
                No matching opportunities
            </h3>

            <p class="text-slate-600 mt-2">
                Try a different search or come back later.
            </p>

            <?php if ($search !== ''): ?>
                <a href="opportunities.php"
                   class="inline-block mt-4 text-amber-700 font-semibold hover:underline">
                    View all opportunities →
                </a>
            <?php endif; ?>
        </div>

    <?php endif; ?>

</section>

<?php require 'includes/footer.php'; ?>
