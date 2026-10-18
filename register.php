
<?php
require_once 'db.php';
require_once 'includes/functions.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$name = '';
$email = '';
$role = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();

    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role = $_POST['role'] ?? '';
    $password = $_POST['password'] ?? '';

    if (
        $name === '' ||
        strlen($name) > 100 ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($email) > 150 ||
        strlen($password) < 6 ||
        !in_array($role, ['talent', 'sponsor'], true)
    ) {
        $error = 'Please check your name, email, password and account type.';
    } else {
        $stmt = $pdo->prepare("
            SELECT id FROM users WHERE email = ? LIMIT 1
        ");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $error = 'This email is already registered.';
        } else {
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO users (name, email, password, role)
                    VALUES (?, ?, ?, ?)
                ");

                $stmt->execute([
                    $name,
                    $email,
                    password_hash($password, PASSWORD_DEFAULT),
                    $role
                ]);

                session_regenerate_id(true);

                $_SESSION['user_id'] = (int) $pdo->lastInsertId();
                $_SESSION['name'] = $name;
                $_SESSION['role'] = $role;

                header('Location: dashboard.php');
                exit;
            } catch (PDOException $e) {
                // Handle a duplicate email if two requests arrive together.
                if ($e->getCode() === '23000') {
                    $error = 'This email is already registered.';
                } else {
                    throw $e;
                }
            }
        }
    }
}

$pageTitle = 'Create account';
require 'includes/header.php';
?>

<section class="bg-slate-50 border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-5 py-10">
        <p class="text-sm font-semibold uppercase tracking-widest text-amber-700">
            CLOJO Talent Connect
        </p>

        <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mt-3">
            Create an account
        </h1>

        <p class="text-slate-600 mt-3 max-w-2xl">
            Join CLOJO to showcase your skills or find people to support your work.
        </p>
    </div>
</section>

<section class="max-w-6xl mx-auto px-5 py-10 md:py-14">
    <div class="grid lg:grid-cols-5 gap-10 lg:gap-14 items-start">

        <!-- Information section -->
        <div class="lg:col-span-2">
            <h2 class="text-2xl font-bold text-slate-900">
                Be part of the community
            </h2>

            <p class="text-slate-600 leading-7 mt-4">
                CLOJO Talent Connect brings talented people and potential
                supporters together. Create your account to get started.
            </p>

            <div class="mt-8 border-t border-slate-200">

                <div class="py-5 border-b border-slate-200">
                    <h3 class="font-semibold text-slate-900">
                        For talents
                    </h3>
                    <p class="text-sm text-slate-600 mt-2 leading-6">
                        Share your skills, introduce yourself and apply for
                        opportunities that match your interests.
                    </p>
                </div>

                <div class="py-5 border-b border-slate-200">
                    <h3 class="font-semibold text-slate-900">
                        For sponsors
                    </h3>
                    <p class="text-sm text-slate-600 mt-2 leading-6">
                        Publish opportunities and connect with people whose
                        talents fit your goals.
                    </p>
                </div>

            </div>

            <p class="text-sm text-slate-500 mt-6">
                Already have an account?
                <a href="login.php"
                   class="text-amber-700 font-semibold hover:underline">
                    Log in here
                </a>
            </p>
        </div>

        <!-- Registration form -->
        <div class="lg:col-span-3 bg-white border border-slate-200 p-6 md:p-9">

            <h2 class="text-2xl font-bold text-slate-900">
                Your details
            </h2>

            <p class="text-slate-500 text-sm mt-2 mb-7">
                Fill in the information below to register.
            </p>

            <?php if ($error): ?>
                <div class="border border-red-200 bg-red-50 text-red-700 p-4 mb-6 text-sm">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="register.php">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e(csrf_token()) ?>"
                >

                <div class="mb-5">
                    <label for="name"
                           class="block text-sm font-semibold text-slate-700 mb-2">
                        Full name
                    </label>

                    <input
                        id="name"
                        type="text"
                        name="name"
                        required
                        maxlength="100"
                        autocomplete="name"
                        value="<?= e($name) ?>"
                        placeholder="Enter your full name"
                        class="w-full border border-slate-300 px-4 py-3 focus:outline-none focus:border-amber-600"
                    >
                </div>

                <div class="mb-5">
                    <label for="email"
                           class="block text-sm font-semibold text-slate-700 mb-2">
                        Email address
                    </label>

                    <input
                        id="email"
                        type="email"
                        name="email"
                        required
                        maxlength="150"
                        autocomplete="email"
                        value="<?= e($email) ?>"
                        placeholder="you@example.com"
                        class="w-full border border-slate-300 px-4 py-3 focus:outline-none focus:border-amber-600"
                    >
                </div>

                <div class="mb-5">
                    <label for="role"
                           class="block text-sm font-semibold text-slate-700 mb-2">
                        Account type
                    </label>

                    <select
                        id="role"
                        name="role"
                        required
                        class="w-full border border-slate-300 bg-white px-4 py-3 focus:outline-none focus:border-amber-600"
                    >
                        <option value="">Choose your account type</option>

                        <option
                            value="talent"
                            <?= $role === 'talent' ? 'selected' : '' ?>
                        >
                            Talent — I want to showcase my skills
                        </option>

                        <option
                            value="sponsor"
                            <?= $role === 'sponsor' ? 'selected' : '' ?>
                        >
                            Sponsor — I want to support talent
                        </option>
                    </select>
                </div>

                <div class="mb-7">
                    <label for="password"
                           class="block text-sm font-semibold text-slate-700 mb-2">
                        Password
                    </label>

                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        minlength="6"
                        autocomplete="new-password"
                        placeholder="At least 6 characters"
                        class="w-full border border-slate-300 px-4 py-3 focus:outline-none focus:border-amber-600"
                    >

                    <p class="text-xs text-slate-500 mt-2">
                        Use a password that you do not use on other websites.
                    </p>
                </div>

                <button
                    type="submit"
                    class="w-full bg-slate-900 hover:bg-slate-800 text-white px-6 py-3 font-semibold transition-colors"
                >
                    Create account
                </button>

                <p class="text-xs text-slate-500 mt-4 leading-5">
                    By creating an account, you agree to use the platform
                    respectfully and provide accurate information.
                </p>

            </form>
        </div>

    </div>
</section>

<?php require 'includes/footer.php'; ?>