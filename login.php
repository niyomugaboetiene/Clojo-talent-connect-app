
<?php
require 'db.php';
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

    $q = $pdo->prepare(
        'SELECT * FROM users WHERE email = ?'
    );
    $q->execute([$email]);

    $user = $q->fetch();

    if (
        $user &&
        password_verify(
            $_POST['password'] ?? '',
            $user['password']
        )
    ) {
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

<section class="max-w-5xl mx-auto px-5 py-16 grid md:grid-cols-2 gap-10 items-center">

    <!-- Welcome Section -->
    <div>
        <p class="text-amber-700 uppercase tracking-widest font-bold text-sm">
            Welcome back
        </p>

        <h1 class="text-4xl md:text-5xl font-extrabold text-clojo-navy mt-3">
            Good things happen when talent connects.
        </h1>

        <p class="text-slate-600 mt-5 leading-7">
            Log in to manage your profile, explore opportunities,
            or support talent.
        </p>
    </div>

    <!-- Login Form -->
    <form method="post" class="bg-white border shadow-xl rounded-3xl p-8">

        <h2 class="text-2xl font-extrabold">
            Login to CLOJO
        </h2>

        <p class="text-slate-500 mt-2 mb-6">
            Enter your account details.
        </p>

        <?php if ($error): ?>
            <p class="bg-red-50 text-red-700 rounded-xl p-3 mb-4 text-sm">
                <?= e($error) ?>
            </p>
        <?php endif; ?>

        <input
            type="hidden"
            name="csrf_token"
            value="<?= e(csrf_token()) ?>"
        >

        <label class="block text-sm font-semibold mb-2">
            Email
        </label>

        <input
            type="email"
            name="email"
            required
            value="<?= e($email) ?>"
            class="w-full border rounded-xl px-4 py-3 mb-4"
        >

        <label class="block text-sm font-semibold mb-2">
            Password
        </label>

        <input
            type="password"
            name="password"
            required
            class="w-full border rounded-xl px-4 py-3 mb-6"
        >

        <button class="w-full bg-clojo-navy text-white rounded-xl py-3.5 font-bold">
            Login →
        </button>

        <p class="text-sm text-center text-slate-500 mt-5">
            New to CLOJO?
            <a
                href="register.php"
                class="font-bold text-amber-700"
            >
                Create an account
            </a>
        </p>

    </form>

</section>

<?php require 'includes/footer.php'; ?>
