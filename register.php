
<?php
require 'db.php';
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
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        strlen($password) < 6 ||
        !in_array($role, ['talent', 'sponsor'], true)
    ) {
        $error = 'Enter a name, valid email, password of at least 6 characters, and choose a role.';
    } else {
        $q = $pdo->prepare(
            'SELECT id FROM users WHERE email = ?'
        );
        $q->execute([$email]);

        if ($q->fetch()) {
            $error = 'This email is already registered.';
        } else {
            $q = $pdo->prepare("
                INSERT INTO users (name, email, password, role)
                VALUES (?, ?, ?, ?)
            ");

            $q->execute([
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
        }
    }
}

$pageTitle = 'Create account';

require 'includes/header.php';
?>

<section class="max-w-6xl mx-auto px-5 py-14 grid lg:grid-cols-2 gap-10 items-center">

    <!-- Welcome Section -->
    <div class="bg-clojo-navy text-white rounded-3xl p-8 md:p-10 hero-grid">

        <p class="text-clojo-gold text-sm font-bold uppercase tracking-widest">
            Join the community
        </p>

        <h1 class="text-4xl font-extrabold mt-4">
            Your next opportunity starts with one connection.
        </h1>

        <p class="text-slate-300 mt-5 leading-7">
            Create an account to share your talent or support people
            with great ideas.
        </p>

        <div class="mt-8 space-y-4">
            <p>✦ Build a profile people can discover</p>
            <p>✦ Find opportunities for your skills</p>
            <p>✦ Connect talent with support</p>
        </div>

    </div>

    <!-- Registration Form -->
    <form method="post" class="bg-white border shadow-xl rounded-3xl p-7 md:p-9">

        <h2 class="text-2xl font-extrabold">
            Create your account
        </h2>

        <p class="text-slate-500 mt-2 mb-6">
            It only takes a minute.
        </p>

        <?php if ($error): ?>
            <p class="bg-red-50 text-red-700 rounded-xl p-3 mb-5 text-sm">
                <?= e($error) ?>
            </p>
        <?php endif; ?>

        <input
            type="hidden"
            name="csrf_token"
            value="<?= e(csrf_token()) ?>"
        >

        <label class="block font-semibold text-sm mb-2">
            Full name
        </label>

        <input
            name="name"
            required
            value="<?= e($name) ?>"
            class="w-full border rounded-xl px-4 py-3 mb-4"
        >

        <label class="block font-semibold text-sm mb-2">
            Email address
        </label>

        <input
            type="email"
            name="email"
            required
            value="<?= e($email) ?>"
            class="w-full border rounded-xl px-4 py-3 mb-4"
        >

        <label class="block font-semibold text-sm mb-2">
            I want to join as
        </label>

        <select
            name="role"
            required
            class="w-full border rounded-xl px-4 py-3 mb-4 bg-white"
        >
            <option value="">Choose your role</option>

            <option
                value="talent"
                <?= $role === 'talent' ? 'selected' : '' ?>
            >
                Talent — showcase my skills
            </option>

            <option
                value="sponsor"
                <?= $role === 'sponsor' ? 'selected' : '' ?>
            >
                Sponsor — support talent
            </option>
        </select>

        <label class="block font-semibold text-sm mb-2">
            Password (at least 6 characters)
        </label>

        <input
            type="password"
            name="password"
            required
            minlength="6"
            class="w-full border rounded-xl px-4 py-3 mb-6"
        >

        <button class="w-full bg-clojo-navy text-white rounded-xl py-3.5 font-bold">
            Create account →
        </button>

        <p class="text-sm text-center text-slate-500 mt-5">
            Already registered?
            <a href="login.php" class="text-amber-700 font-bold">
                Log in
            </a>
        </p>

    </form>

</section>

<?php require 'includes/footer.php'; ?>