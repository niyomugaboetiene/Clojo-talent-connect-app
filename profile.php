
<?php
require 'db.php';
require_once 'includes/functions.php';

$viewId = (int) ($_GET['id'] ?? 0);
$editing = $viewId === 0;

$error = '';
$success = '';

if ($editing) {
    require_role('talent');

    $viewId = (int) $_SESSION['user_id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        check_csrf();

        $bio = trim($_POST['bio'] ?? '');
        $skills = trim($_POST['skills'] ?? '');
        $location = trim($_POST['location'] ?? '');
        $cat = (int) ($_POST['category_id'] ?? 0);
        $image = trim($_POST['image_url'] ?? '');

        if (
            strlen($bio) > 1500 ||
            strlen($skills) > 500 ||
            strlen($location) > 100 ||
            (
                $image !== '' &&
                !filter_var($image, FILTER_VALIDATE_URL)
            )
        ) {
            $error = 'Check your information. The image must be a valid URL.';
        } else {
            $q = $pdo->prepare("
                INSERT INTO talent_profiles (
                    user_id,
                    bio,
                    skills,
                    location,
                    category_id,
                    image_url
                )
                VALUES (?, ?, ?, ?, NULLIF(?, 0), ?)
                ON DUPLICATE KEY UPDATE
                    bio = VALUES(bio),
                    skills = VALUES(skills),
                    location = VALUES(location),
                    category_id = NULLIF(VALUES(category_id), 0),
                    image_url = VALUES(image_url)
            ");

            $q->execute([
                $viewId,
                $bio,
                $skills,
                $location,
                $cat,
                $image
            ]);

            $success = 'Your profile has been saved.';
        }
    }
}

$q = $pdo->prepare("
    SELECT
        tp.*,
        u.name,
        u.email,
        c.name AS category_name
    FROM users u
    LEFT JOIN talent_profiles tp ON tp.user_id = u.id
    LEFT JOIN categories c ON tp.category_id = c.id
    WHERE u.id = ? AND u.role = 'talent'
");

$q->execute([$viewId]);
$profile = $q->fetch();

if (!$profile) {
    http_response_code(404);
    exit('Talent profile not found.');
}

$categories = $pdo->query("
    SELECT *
    FROM categories
    ORDER BY name
")->fetchAll();

$pageTitle = $editing ? 'Edit my profile' : $profile['name'];

require 'includes/header.php';
?>

<section class="max-w-5xl mx-auto px-5 py-12">

    <?php if ($editing): ?>

        <!-- Edit Profile Section -->
        <div class="max-w-3xl mx-auto">

            <p class="text-amber-700 uppercase tracking-widest text-sm font-bold">
                Your creative identity
            </p>

            <h1 class="text-3xl font-extrabold text-clojo-navy mt-2">
                Build your talent profile
            </h1>

            <p class="text-slate-500 mt-2 mb-7">
                Tell sponsors and the community what you can do.
            </p>

            <?php if ($error): ?>
                <p class="bg-red-50 text-red-700 rounded-xl p-3 mb-4">
                    <?= e($error) ?>
                </p>
            <?php endif; ?>

            <?php if ($success): ?>
                <p class="bg-green-50 text-green-700 rounded-xl p-3 mb-4">
                    <?= e($success) ?>
                </p>
            <?php endif; ?>

            <form method="post" class="bg-white border rounded-2xl p-6 md:p-8">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e(csrf_token()) ?>"
                >

                <label class="block font-semibold mb-2">
                    Talent category
                </label>

                <select
                    name="category_id"
                    class="w-full border rounded-xl p-3 mb-5 bg-white"
                >
                    <option value="0">Choose a category</option>

                    <?php foreach ($categories as $c): ?>
                        <option
                            value="<?= (int) $c['id'] ?>"
                            <?= (int) ($profile['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>
                        >
                            <?= e($c['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label class="block font-semibold mb-2">
                    Location
                </label>

                <input
                    name="location"
                    value="<?= e($profile['location'] ?? '') ?>"
                    placeholder="Kigali, Rwanda"
                    class="w-full border rounded-xl p-3 mb-5"
                >

                <label class="block font-semibold mb-2">
                    Skills
                </label>

                <input
                    name="skills"
                    maxlength="500"
                    value="<?= e($profile['skills'] ?? '') ?>"
                    placeholder="Video editing, singing, PHP..."
                    class="w-full border rounded-xl p-3 mb-5"
                >

                <label class="block font-semibold mb-2">
                    Portfolio image URL (optional)
                </label>

                <input
                    type="url"
                    name="image_url"
                    value="<?= e($profile['image_url'] ?? '') ?>"
                    placeholder="https://example.com/photo.jpg"
                    class="w-full border rounded-xl p-3 mb-5"
                >

                <label class="block font-semibold mb-2">
                    About you
                </label>

                <textarea
                    name="bio"
                    maxlength="1500"
                    rows="5"
                    class="w-full border rounded-xl p-3 mb-6"
                    placeholder="Tell people about your talent and goals..."
                ><?= e($profile['bio'] ?? '') ?></textarea>

                <button class="bg-clojo-navy text-white rounded-xl px-6 py-3 font-bold">
                    Save profile
                </button>

                <a href="dashboard.php" class="ml-3 text-slate-600">
                    Back to dashboard
                </a>

            </form>
        </div>

    <?php else: ?>

        <!-- Public Talent Profile -->
        <div class="bg-white rounded-3xl border overflow-hidden">

            <div class="bg-clojo-navy h-44 hero-grid"></div>

            <div class="px-6 md:px-10 pb-10">

                <div class="-mt-16 w-32 h-32 rounded-3xl bg-clojo-gold border-4 border-white overflow-hidden flex items-center justify-center text-5xl font-extrabold">

                    <?php if ($profile['image_url']): ?>
                        <img
                            src="<?= e($profile['image_url']) ?>"
                            alt="<?= e($profile['name']) ?>"
                            class="w-full h-full object-cover"
                        >
                    <?php else: ?>
                        <?= e(strtoupper(substr($profile['name'], 0, 1))) ?>
                    <?php endif; ?>

                </div>

                <div class="mt-5 flex flex-wrap justify-between gap-4">

                    <div>
                        <p class="text-amber-700 text-sm font-bold">
                            <?= e($profile['category_name'] ?? 'CLOJO Talent') ?>
                        </p>

                        <h1 class="text-3xl md:text-4xl font-extrabold text-clojo-navy mt-1">
                            <?= e($profile['name']) ?>
                        </h1>

                        <p class="text-slate-500 mt-2">
                            📍 <?= e($profile['location'] ?: 'Rwanda') ?>
                        </p>
                    </div>

                    <?php
                    if (
                        !empty($_SESSION['user_id']) &&
                        (int) $_SESSION['user_id'] === $viewId
                    ):
                    ?>
                        <a
                            href="profile.php"
                            class="bg-clojo-gold text-clojo-navy px-5 py-3 rounded-full font-bold"
                        >
                            Edit my profile
                        </a>
                    <?php endif; ?>

                </div>

                <div class="grid md:grid-cols-[1.4fr_.8fr] gap-10 mt-10">

                    <div>
                        <h2 class="text-xl font-extrabold">
                            About me
                        </h2>

                        <p class="text-slate-600 leading-7 mt-3 whitespace-pre-line">
                            <?= e($profile['bio'] ?: 'This talent is still writing their story.') ?>
                        </p>
                    </div>

                    <div class="bg-slate-50 rounded-2xl p-6">

                        <h2 class="font-extrabold text-xl">
                            Skills &amp; interests
                        </h2>

                        <p class="text-slate-600 mt-3">
                            <?= e($profile['skills'] ?: 'Skills have not been added yet.') ?>
                        </p>

                        <div class="border-t mt-5 pt-5">
                            <p class="text-xs uppercase tracking-widest text-slate-400 font-bold">
                                Contact
                            </p>

                            <p class="mt-2">
                                <?= e($profile['email']) ?>
                            </p>
                        </div>

                    </div>

                </div>

            </div>
        </div>

    <?php endif; ?>

</section>

<?php require 'includes/footer.php'; ?>
