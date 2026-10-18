
<?php
require_once 'db.php';
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
            ($image !== '' && !filter_var($image, FILTER_VALIDATE_URL))
        ) {
            $error = 'Please check your information and image URL.';
        } else {
            // Make sure the selected category exists.
            if ($cat > 0) {
                $stmt = $pdo->prepare("
                    SELECT id FROM categories WHERE id = ?
                ");
                $stmt->execute([$cat]);

                if (!$stmt->fetch()) {
                    $error = 'Please select a valid category.';
                }
            }

            if ($error === '') {
                $stmt = $pdo->prepare("
                    INSERT INTO talent_profiles (
                        user_id,
                        bio,
                        skills,
                        location,
                        category_id,
                        image_url
                    )
                    VALUES (?, ?, ?, NULLIF(?, ''), NULLIF(?, 0), NULLIF(?, ''))
                    ON DUPLICATE KEY UPDATE
                        bio = VALUES(bio),
                        skills = VALUES(skills),
                        location = NULLIF(VALUES(location), ''),
                        category_id = NULLIF(VALUES(category_id), 0),
                        image_url = NULLIF(VALUES(image_url), '')
                ");

                $stmt->execute([
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
}

$stmt = $pdo->prepare("
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

$stmt->execute([$viewId]);
$profile = $stmt->fetch();

if (!$profile) {
    http_response_code(404);
    exit('Talent profile not found.');
}

$categories = $pdo->query("
    SELECT id, name
    FROM categories
    ORDER BY name
")->fetchAll();

$pageTitle = $editing ? 'Edit my profile' : $profile['name'];

require 'includes/header.php';
?>

<section class="bg-slate-50 border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-5 py-10">
        <p class="text-sm font-semibold uppercase tracking-widest text-amber-700">
            CLOJO Talent Connect
        </p>

        <?php if ($editing): ?>
            <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mt-3">
                Your talent profile
            </h1>

            <p class="text-slate-600 mt-3">
                Add your skills and experience so people can learn more about you.
            </p>
        <?php else: ?>
            <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mt-3">
                Talent profile
            </h1>

            <p class="text-slate-600 mt-3">
                Get to know the person behind the talent.
            </p>
        <?php endif; ?>
    </div>
</section>

<section class="max-w-6xl mx-auto px-5 py-10">

    <?php if ($editing): ?>

        <div class="max-w-3xl mx-auto">

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

            <form method="post" action="profile.php"
                  class="bg-white border border-slate-200 p-6 md:p-8">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e(csrf_token()) ?>"
                >

                <div class="mb-6">
                    <label for="category_id"
                           class="block text-sm font-semibold text-slate-700 mb-2">
                        Talent category
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
                                <?= (int) ($profile['category_id'] ?? 0) === (int) $c['id'] ? 'selected' : '' ?>
                            >
                                <?= e($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-6">
                    <label for="location"
                           class="block text-sm font-semibold text-slate-700 mb-2">
                        Location
                    </label>

                    <input
                        id="location"
                        name="location"
                        maxlength="100"
                        value="<?= e($profile['location'] ?? '') ?>"
                        placeholder="Kigali, Rwanda"
                        class="w-full border border-slate-300 px-4 py-3 focus:outline-none focus:border-amber-600"
                    >
                </div>

                <div class="mb-6">
                    <label for="skills"
                           class="block text-sm font-semibold text-slate-700 mb-2">
                        Skills
                    </label>

                    <input
                        id="skills"
                        name="skills"
                        maxlength="500"
                        value="<?= e($profile['skills'] ?? '') ?>"
                        placeholder="Video editing, singing, web development..."
                        class="w-full border border-slate-300 px-4 py-3 focus:outline-none focus:border-amber-600"
                    >

                    <p class="text-xs text-slate-500 mt-2">
                        List the skills you have or the areas you work in.
                    </p>
                </div>

                <div class="mb-6">
                    <label for="image_url"
                           class="block text-sm font-semibold text-slate-700 mb-2">
                        Profile image URL (optional)
                    </label>

                    <input
                        id="image_url"
                        type="url"
                        name="image_url"
                        value="<?= e($profile['image_url'] ?? '') ?>"
                        placeholder="https://example.com/photo.jpg"
                        class="w-full border border-slate-300 px-4 py-3 focus:outline-none focus:border-amber-600"
                    >
                </div>

                <div class="mb-7">
                    <label for="bio"
                           class="block text-sm font-semibold text-slate-700 mb-2">
                        About you
                    </label>

                    <textarea
                        id="bio"
                        name="bio"
                        maxlength="1500"
                        rows="6"
                        placeholder="Tell people about your experience, talent and goals."
                        class="w-full border border-slate-300 px-4 py-3 focus:outline-none focus:border-amber-600"
                    ><?= e($profile['bio'] ?? '') ?></textarea>
                </div>

                <div class="flex flex-wrap items-center gap-4">
                    <button
                        type="submit"
                        class="bg-slate-900 hover:bg-slate-800 text-white px-6 py-3 font-semibold"
                    >
                        Save profile
                    </button>

                    <a href="dashboard.php"
                       class="text-slate-600 hover:text-slate-900 text-sm font-semibold">
                        Back to dashboard
                    </a>

                    <?php if (!empty($profile['id'])): ?>
                        <a href="profile.php?id=<?= $viewId ?>"
                           class="text-amber-700 hover:underline text-sm font-semibold">
                            View public profile
                        </a>
                    <?php endif; ?>
                </div>

            </form>
        </div>

    <?php else: ?>

        <!-- Public profile -->
        <div class="bg-white border border-slate-200">

            <div class="p-6 md:p-10">
                <div class="flex flex-col sm:flex-row sm:items-start gap-6">

                    <div class="w-28 h-28 shrink-0 border border-slate-200 bg-slate-100 flex items-center justify-center overflow-hidden text-3xl font-bold text-slate-700">
                        <?php if (!empty($profile['image_url'])): ?>
                            <img
                                src="<?= e($profile['image_url']) ?>"
                                alt="<?= e($profile['name']) ?>"
                                class="w-full h-full object-cover"
                            >
                        <?php else: ?>
                            <?= e(strtoupper(substr($profile['name'], 0, 1))) ?>
                        <?php endif; ?>
                    </div>

                    <div class="flex-1">
                        <p class="text-sm font-semibold text-amber-700">
                            <?= e($profile['category_name'] ?? 'CLOJO Talent') ?>
                        </p>

                        <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mt-2">
                            <?= e($profile['name']) ?>
                        </h1>

                        <p class="text-slate-600 mt-3">
                            <?= e($profile['location'] ?: 'Rwanda') ?>
                        </p>

                        <?php if (
                            !empty($_SESSION['user_id']) &&
                            (int) $_SESSION['user_id'] === $viewId
                        ): ?>
                            <a
                                href="profile.php"
                                class="inline-block mt-5 bg-slate-900 hover:bg-slate-800 text-white px-5 py-3 text-sm font-semibold"
                            >
                                Edit my profile
                            </a>
                        <?php endif; ?>
                    </div>

                </div>

                <div class="grid md:grid-cols-2 gap-10 mt-10 pt-8 border-t border-slate-200">

                    <div>
                        <h2 class="text-xl font-bold text-slate-900">
                            About
                        </h2>

                        <p class="text-slate-600 leading-7 mt-4 whitespace-pre-line"><?= e($profile['bio'] ?: 'This talent has not added a biography yet.') ?></p>
                    </div>

                    <div>
                        <h2 class="text-xl font-bold text-slate-900">
                            Skills and interests
                        </h2>

                        <p class="text-slate-600 leading-7 mt-4 whitespace-pre-line"><?= e($profile['skills'] ?: 'No skills have been added yet.') ?></p>

                        <div class="border-t border-slate-200 mt-6 pt-5">
                            <p class="text-sm font-semibold text-slate-700">
                                Contact
                            </p>

                            <p class="text-slate-600 mt-2 break-words">
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
