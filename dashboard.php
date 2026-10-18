
<?php
require 'db.php';
require_once 'includes/functions.php';

require_login();

$uid = (int)$_SESSION['user_id'];
$role = $_SESSION['role'];
$flash = $_SESSION['flash'] ?? '';
unset($_SESSION['flash']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    require_role('sponsor');
    check_csrf();

    $appId = (int)($_POST['application_id'] ?? 0);
    $status = $_POST['status'] ?? '';

    if (in_array($status, ['pending', 'accepted', 'rejected'], true)) {
        $q = $pdo->prepare(
            'UPDATE applications a
             JOIN opportunities o ON a.opportunity_id = o.id
             SET a.status = ?
             WHERE a.id = ? AND o.sponsor_id = ?'
        );

        $q->execute([$status, $appId, $uid]);

        $flash = $q->rowCount()
            ? 'Application status updated.'
            : 'No application was changed.';
    }
}

$pageTitle = 'Dashboard';
require 'includes/header.php';
?>

<section class="bg-clojo-navy text-white hero-grid">
    <div class="max-w-7xl mx-auto px-5 py-10">
        <p class="text-clojo-gold text-sm font-bold uppercase tracking-widest">
            Your CLOJO space
        </p>

        <h1 class="text-3xl md:text-4xl font-extrabold mt-2">
            Welcome, <?= e($_SESSION['name']) ?>.
        </h1>

        <p class="text-slate-300 mt-2">
            Here is what is happening with your talent connections.
        </p>
    </div>
</section>

<section class="max-w-7xl mx-auto px-5 py-10">
    <?php if ($flash): ?>
        <div class="bg-green-50 text-green-800 border border-green-200 rounded-xl p-4 mb-6">
            <?= e($flash) ?>
        </div>
    <?php endif; ?>

    <?php if ($role === 'talent'): ?>

        <?php
        $q = $pdo->prepare(
            'SELECT * FROM talent_profiles WHERE user_id = ?'
        );
        $q->execute([$uid]);
        $profile = $q->fetch();

        $q = $pdo->prepare(
            'SELECT a.*, o.title, o.support_type, u.name sponsor_name
             FROM applications a
             JOIN opportunities o ON a.opportunity_id = o.id
             JOIN users u ON o.sponsor_id = u.id
             WHERE a.talent_id = ?
             ORDER BY a.id DESC'
        );
        $q->execute([$uid]);
        $apps = $q->fetchAll();
        ?>

        <div class="grid md:grid-cols-3 gap-5 mb-8">
            <div class="bg-white border rounded-2xl p-6">
                <p class="text-slate-500 text-sm">My profile</p>

                <h2 class="text-2xl font-extrabold mt-2">
                    <?= $profile ? 'Started' : 'Not created' ?>
                </h2>

                <a class="text-amber-700 font-bold text-sm inline-block mt-4" href="profile.php">
                    <?= $profile ? 'Edit profile →' : 'Create profile →' ?>
                </a>
            </div>

            <div class="bg-white border rounded-2xl p-6">
                <p class="text-slate-500 text-sm">Applications</p>

                <h2 class="text-2xl font-extrabold mt-2">
                    <?= count($apps) ?>
                </h2>

                <a class="text-amber-700 font-bold text-sm inline-block mt-4" href="opportunities.php">
                    Find opportunities →
                </a>
            </div>

            <div class="bg-white border rounded-2xl p-6">
                <p class="text-slate-500 text-sm">Public profile</p>

                <h2 class="text-2xl font-extrabold mt-2">
                    Show your skills
                </h2>

                <a class="text-amber-700 font-bold text-sm inline-block mt-4" href="profile.php?id=<?= $uid ?>">
                    View profile →
                </a>
            </div>
        </div>

        <div class="bg-white border rounded-2xl overflow-hidden">
            <div class="p-6 border-b">
                <h2 class="text-xl font-extrabold">My applications</h2>
            </div>

            <?php if ($apps): ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="p-4">Opportunity</th>
                                <th class="p-4">Sponsor</th>
                                <th class="p-4">Support</th>
                                <th class="p-4">Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($apps as $a): ?>
                                <tr class="border-t">
                                    <td class="p-4 font-bold">
                                        <?= e($a['title']) ?>
                                    </td>

                                    <td class="p-4">
                                        <?= e($a['sponsor_name']) ?>
                                    </td>

                                    <td class="p-4">
                                        <?= e($a['support_type']) ?>
                                    </td>

                                    <td class="p-4">
                                        <span class="rounded-full px-3 py-1 text-xs font-bold <?= $a['status'] === 'accepted' ? 'bg-green-100 text-green-800' : ($a['status'] === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') ?>">
                                            <?= e(ucfirst($a['status'])) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="p-10 text-center text-slate-500">
                    No applications yet.
                    <a href="opportunities.php" class="text-amber-700 font-bold">
                        Explore opportunities →
                    </a>
                </div>
            <?php endif; ?>
        </div>

    <?php elseif ($role === 'sponsor'): ?>

        <?php
        $q = $pdo->prepare(
            'SELECT o.*, c.name category_name,
                (SELECT COUNT(*)
                 FROM applications a
                 WHERE a.opportunity_id = o.id) applicant_count
             FROM opportunities o
             LEFT JOIN categories c ON o.category_id = c.id
             WHERE o.sponsor_id = ?
             ORDER BY o.id DESC'
        );

        $q->execute([$uid]);
        $opps = $q->fetchAll();
        ?>

        <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
            <div>
                <h2 class="text-2xl font-extrabold text-clojo-navy">
                    Your opportunities
                </h2>

                <p class="text-slate-500 mt-1">
                    Review applications and update their status.
                </p>
            </div>

            <a href="opportunities.php#create" class="bg-clojo-gold text-clojo-navy px-5 py-3 rounded-full font-bold">
                + Post opportunity
            </a>
        </div>

        <?php if ($opps): ?>
            <div class="space-y-6">
                <?php foreach ($opps as $o): ?>
                    <div class="bg-white border rounded-2xl overflow-hidden">
                        <div class="p-5 border-b">
                            <h3 class="text-xl font-extrabold">
                                <?= e($o['title']) ?>
                            </h3>

                            <p class="text-sm text-slate-500 mt-1">
                                <?= e($o['support_type']) ?> ·
                                <?= (int)$o['applicant_count'] ?> applicant(s)
                            </p>
                        </div>

                        <?php
                        $q = $pdo->prepare(
                            'SELECT a.*, u.name talent_name, u.email, tp.skills
                             FROM applications a
                             JOIN users u ON a.talent_id = u.id
                             LEFT JOIN talent_profiles tp ON tp.user_id = u.id
                             WHERE a.opportunity_id = ?
                             ORDER BY a.id DESC'
                        );

                        $q->execute([$o['id']]);
                        $applicants = $q->fetchAll();
                        ?>

                        <?php if ($applicants): ?>
                            <?php foreach ($applicants as $a): ?>
                                <div class="p-5 border-b last:border-0 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                                    <div>
                                        <p class="font-bold">
                                            <?= e($a['talent_name']) ?>
                                        </p>

                                        <p class="text-sm text-slate-500">
                                            <?= e($a['email']) ?> ·
                                            <?= e($a['skills'] ?? 'Skills not added') ?>
                                        </p>

                                        <span class="text-xs mt-2 inline-block text-slate-500">
                                            Current status: <?= e(ucfirst($a['status'])) ?>
                                        </span>
                                    </div>

                                    <form method="post" class="flex gap-2">
                                        <input type="hidden" name="csrf_token" value="<?= e(csrf_token()) ?>">
                                        <input type="hidden" name="update_status" value="1">
                                        <input type="hidden" name="application_id" value="<?= (int)$a['id'] ?>">

                                        <select name="status" class="border rounded-lg p-2 text-sm">
                                            <option value="pending" <?= $a['status'] === 'pending' ? 'selected' : '' ?>>
                                                Pending
                                            </option>

                                            <option value="accepted" <?= $a['status'] === 'accepted' ? 'selected' : '' ?>>
                                                Accept
                                            </option>

                                            <option value="rejected" <?= $a['status'] === 'rejected' ? 'selected' : '' ?>>
                                                Reject
                                            </option>
                                        </select>

                                        <button class="bg-clojo-navy text-white rounded-lg px-4 py-2 text-sm font-bold">
                                            Update
                                        </button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="p-5 text-sm text-slate-500">
                                No applications yet.
                            </p>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="bg-white border border-dashed rounded-2xl p-12 text-center">
                <h3 class="text-xl font-bold">
                    Your first opportunity starts here
                </h3>

                <a href="opportunities.php#create" class="inline-block mt-5 bg-clojo-navy text-white rounded-full px-5 py-3 font-bold">
                    Create opportunity
                </a>
            </div>
        <?php endif; ?>

    <?php else: ?>

        <div class="bg-white border rounded-2xl p-8">
            <h2 class="text-2xl font-extrabold">
                Administrator account
            </h2>

            <p class="text-slate-600 mt-2">
                Use the admin page to manage demo content.
            </p>

            <a href="admin.php" class="inline-block mt-5 bg-clojo-navy text-white px-5 py-3 rounded-xl font-bold">
                Open admin panel →
            </a>
        </div>

    <?php endif; ?>
</section>

<?php
require 'includes/footer.php';
?>