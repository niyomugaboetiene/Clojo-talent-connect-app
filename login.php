
<?php
require_once 'db.php';
require_once 'includes/functions.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_csrf();

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("
        SELECT id, name, email, password, role
        FROM users
        WHERE email = ?
        LIMIT 1
    ");

    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);

        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['role'] = $user['role'];

        header('Location: dashboard.php');
        exit;
    }

    $error = 'Email or password is incorrect.';
}

$pageTitle = 'Login';
require 'includes/header.php';
?>

<section class="max-w-6xl mx-auto px-5 py-12 md:py-16">

    <div class="grid md:grid-cols-2 gap-12 lg:gap-20 items-start">

        <!-- Welcome text -->
        <div class="md:pt-8">
            <p class="text-sm font-semibold uppercase tracking-widest text-amber-700">
                CLOJO Talent Connect
            </p>

            <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mt-4 leading-tight">
                Welcome back.
                <span class="block mt-1">Your next opportunity starts here.</span>
            </h1>

            <p class="text-slate-600 mt-5 leading-7 max-w-md">
                Sign in to manage your talent profile, check your applications
                or connect with people looking to support talent.
            </p>

            <div class="border-t border-slate-200 mt-8 pt-6 max-w-md">
                <p class="text-sm text-slate-500">
                    New to CLOJO?
                    <a href="register.php"
                       class="font-semibold text-amber-700 hover:underline">
                        Create an account
                    </a>
                </p>
            </div>
        </div>

        <!-- Login form -->
        <div class="border border-slate-200 bg-white p-6 md:p-8">

            <h2 class="text-2xl font-bold text-slate-900">
                Login
            </h2>

            <p class="text-sm text-slate-600 mt-2 mb-7">
                Enter your email and password to continue.
            </p>

            <?php if ($error): ?>
                <div class="border border-red-200 bg-red-50 text-red-700 p-3 mb-5 text-sm">
                    <?= e($error) ?>
                </div>
            <?php endif; ?>

            <form method="post" action="login.php">

                <input
                    type="hidden"
                    name="csrf_token"
                    value="<?= e(csrf_token()) ?>"
                >

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
                        autocomplete="email"
                        value="<?= e($email) ?>"
                        placeholder="you@example.com"
                        class="w-full border border-slate-300 px-4 py-3 text-slate-900 focus:outline-none focus:border-amber-600"
                    >
                </div>

                <div class="mb-6">
                    <label for="password"
                           class="block text-sm font-semibold text-slate-700 mb-2">
                        Password
                    </label>

                    <input
                        id="password"
                        type="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        class="w-full border border-slate-300 px-4 py-3 text-slate-900 focus:outline-none focus:border-amber-600"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full bg-slate-900 hover:bg-slate-800 text-white py-3 font-semibold transition-colors"
                >
                    Login to your account
                </button>

            </form>

            <p class="text-sm text-slate-500 mt-6 text-center">
                Don't have an account?
                <a href="register.php"
                   class="font-semibold text-amber-700 hover:underline">
                    Register here
                </a>
            </p>

        </div>

    </div>

</section>

<?php require 'includes/footer.php'; ?>
