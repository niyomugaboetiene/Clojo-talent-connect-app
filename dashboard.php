
<?php
require_once 'db.php';
require_once 'includes/functions.php';

require_login();

$uid = (int) $_SESSION['user_id'];
$role = $_SESSION['role'];
$flash = $_SESSION['flash'] ?? '';

unset($_SESSION['flash']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    require_role('sponsor');
    check_csrf();

    $appId = (int) ($_POST['application_id'] ?? 0);
    $status = $_POST['status'] ?? '';

    if (in_array($status, ['pending', 'accepted', 'rejected'], true)) {
        $stmt = $pdo->prepare("
            UPDATE applications a
            JOIN opportunities o ON a.opportunity_id = o.id
            SET a.status = ?
            WHERE a.id = ? AND o.sponsor_id = ?
        ");

        $stmt->execute([$status, $appId, $uid]);

        $flash = $stmt->rowCount()
            ? 'Application status updated.'
            : 'No application was changed.';
    } else {
        $flash = 'Please select a valid application status.';
    }
}

$pageTitle = 'Dashboard';
require 'includes/header.php';
?>

<section class="bg-slate-50 border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-5 py-12">
        <p class="text-sm font-semibold uppercase tracking-widest text-amber-700">
            CLOJO Talent Connect
        </p>

        <h1 class="text-3xl md:text-4xl font-bold text-slate-900 mt-3">
            Welcome, <?= e($_SESSION['name']) ?>.
        </h1>

        <p class="text-slate-600 mt-3">
            Manage your profile, applications and talent opportunities.
        </p>
    </div>
</section>

<section class="max-w-6xl mx-auto px-5 py-10">

    <?php if ($flash): ?>
        <div class="border border-slate-200 bg-white p-4 mb-8 text-slate-700">
            <?= e($flash) ?>
        </div>
    <?php endif; ?>


    <?php if ($role === 'talent'): ?>

        <?php
        $stmt = $pdo->prepare("
            SELECT *
            FROM talent_profiles
            WHERE user_id = ?
        ");
        $stmt->execute([$uid]);
        $profile = $stmt->fetch();

        $stmt = $pdo->prepare("
            SELECT a.*, o.title, o.support_type, u.name AS sponsor_name
            FROM applications a
            JOIN opportunities o ON a.opportunity_id = o.id
            JOIN users u ON o.sponsor_id = u.id
            WHERE a.talent_id = ?
            ORDER BY a.id DESC
        ");
        $stmt->execute([$uid]);
        $apps = $stmt->fetchAll();
        ?>

        <div class="mb-8">
            <h2 class="text-2xl font-bold text-slate-900">Your dashboard</h2>
            <p class="text-slate-600 mt-2">
                Keep your profile up to date and follow your applications.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-5 mb-10">

            <div class="bg-white border border-slate-200 p-6">
                <p class="text-sm text-slate-500">My profile</p>

                <h3 class="text-2xl font-bold text-slate-900 mt-3">
                    <?= $profile ? 'Started' : 'Not created' ?>
                </h3>

                <a href="profile.php"
                   class="inline-block mt-5 text-amber-700 font-semibold text-sm hover:underline">
                    <?= $profile ? 'Edit profile' : 'Create profile' ?> →
                </a>
            </div>

            <div class="bg-white border border-slate-200 p-6">
                <p class="text-sm text-slate-500">Applications sent</p>

                <h3 class="text-2xl font-bold text-slate-900 mt-3">
                    <?= count($apps) ?>
                </h3>

                <a href="opportunities.php"
                   class="inline-block mt-5 text-amber-700 font-semibold text-sm hover:underline">
                    Find opportunities →
                </a>
            </div>

            <div class="bg-white border border-slate-200 p-6">
                <p class="text-sm text-slate-500">Your public profile</p>

                <h3 class="text-xl font-bold text-slate-900 mt-3">
                    Show your skills
                </h3>

                <a href="profile.php?id=<?= $uid ?>"
                   class="inline-block mt-5 text-amber-700 font-semibold text-sm hover:underline">
                    View profile →
                </a>
            </div>

        </div>

        <div class="bg-white border border-slate-200">
            <div class="p-6 border-b border-slate-200">
                <h2 class="text-xl font-bold text-slate-900">
                    My applications
                </h2>
                <p class="text-sm text-slate-500 mt-1">
                    Check the latest status of each application.
                </p>
            </div>

            <?php if ($apps): ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-slate-50 text-slate-600">
                            <tr>
                                <th class="p-4 font-semibold">Opportunity</th>
                                <th class="p-4 font-semibold">Sponsor</th>
                                <th class="p-4 font-semibold">Support</th>
                                <th class="p-4 font-semibold">Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($apps as $a): ?>
                                <tr class="border-t border-slate-200">
                                    <td class="p-4 font-semibold text-slate-900">
                                        <?= e($a['title']) ?>
                                    </td>

                                    <td class="p-4 text-slate-600">
                                        <?= e($a['sponsor_name']) ?>
                                    </td>

                                    <td class="p-4 text-slate-600">
                                        <?= e($a['support_type']) ?>
                                    </td>

                                    <td class="p-4">
                                        <?php
                                        $statusClass = match ($a['status']) {
                                            'accepted' => 'bg-green-50 text-green-700',
                                            'rejected' => 'bg-red-50 text-red-700',
                                            default => 'bg-amber-50 text-amber-800',
                                        };
                                        ?>
                                        <span class="inline-block px-3 py-1 text-xs font-semibold <?= $statusClass ?>">
                                            <?= e(ucfirst($a['status'])) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="p-8 text-center">
                    <p class="text-slate-600">You have not applied for any opportunities yet.</p>
                    <a href="opportunities.php"
                       class="inline-block mt-4 text-amber-700 font-semibold hover:underline">
                        Browse opportunities →
                    </a>
                </div>
            <?php endif; ?>
        </div>


    <?php elseif ($role === 'sponsor'): ?>

        <?php
        $stmt = $pdo->prepare("
            SELECT o.*,
                   c.name AS category_name,
                   (
                       SELECT COUNT(*)
                       FROM applications a
                       WHERE a.opportunity_id = o.id
                   ) AS applicant_count
            FROM opportunities o
            LEFT JOIN categories c ON o.category_id = c.id
            WHERE o.sponsor_id = ?
            ORDER BY o.id DESC
        ");
        $stmt->execute([$uid]);
        $opps = $stmt->fetchAll();

        $totalApplicants = array_sum(
            array_column($opps, 'applicant_count')
        );
        ?>

        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
            <div>
                <h2 class="text-2xl font-bold text-slate-900">
                    Your opportunities
                </h2>
                <p class="text-slate-600 mt-2">
                    Review applicants and update their application status.
                </p>
            </div>

            <a href="opportunities.php#create"
               class="inline-block bg-amber-500 hover:bg-amber-600 text-slate-950 px-5 py-3 text-sm font-bold text-center">
                + Post opportunity
            </a>
        </div>

        <div class="grid sm:grid-cols-2 gap-5 mb-8">
            <div class="bg-white border border-slate-200 p-5">
                <p class="text-sm text-slate-500">Opportunities posted</p>
                <p class="text-3xl font-bold text-slate-900 mt-2">
                    <?= count($opps) ?>
                </p>
            </div>

            <div class="bg-white border border-slate-200 p-5">
                <p class="text-sm text-slate-500">Applications received</p>
                <p class="text-3xl font-bold text-slate-900 mt-2">
                    <?= (int) $totalApplicants ?>
                </p>
            </div>
        </div>

        <?php if ($opps): ?>
            <div class="space-y-6">
                <?php foreach ($opps as $o): ?>
                    <div class="bg-white border border-slate-200">

                        <div class="p-6 border-b border-slate-200">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                <div>
                                    <h3 class="text-xl font-bold text-slate-900">
                                        <?= e($o['title']) ?>
                                    </h3>

                                    <p class="text-sm text-slate-500 mt-2">
                                        <?= e($o['support_type']) ?>

                                        <?php if (!empty($o['category_name'])): ?>
                                            <span class="mx-1">·</span>
                                            <?= e($o['category_name']) ?>
                                        <?php endif; ?>
                                    </p>
                                </div>

                                <span class="text-sm text-slate-600">
                                    <?= (int) $o['applicant_count'] ?>
                                    applicant(s)
                                </span>
                            </div>
                        </div>

                        <?php
                        $stmt = $pdo->prepare("
                            SELECT a.*, u.name AS talent_name, u.email, tp.skills
                            FROM applications a
                            JOIN users u ON a.talent_id = u.id
                            LEFT JOIN talent_profiles tp ON tp.user_id = u.id
                            WHERE a.opportunity_id = ?
                            ORDER BY a.id DESC
                        ");
                        $stmt->execute([$o['id']]);
                        $applicants = $stmt->fetchAll();
                        ?>

                        <?php if ($applicants): ?>
                            <?php foreach ($applicants as $a): ?>
                                <div class="p-6 border-b last:border-b-0 border-slate-200">
                                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">

                                        <div>
                                            <p class="font-semibold text-slate-900">
                                                <?= e($a['talent_name']) ?>
                                            </p>

                                            <p class="text-sm text-slate-600 mt-1">
                                                <?= e($a['email']) ?>
                                            </p>

                                            <p class="text-sm text-slate-500 mt-1">
                                                <?= e($a['skills'] ?: 'Skills not added') ?>
                                            </p>

                                            <p class="text-xs text-slate-500 mt-3">
                                                Current status:
                                                <span class="font-semibold">
                                                    <?= e(ucfirst($a['status'])) ?>
                                                </span>
                                            </p>
                                        </div>

                                        <form method="post" class="flex flex-wrap items-center gap-2">
                                            <input type="hidden" name="csrf_token"
                                                   value="<?= e(csrf_token()) ?>">

                                            <input type="hidden" name="update_status" value="1">

                                            <input type="hidden" name="application_id"
                                                   value="<?= (int) $a['id'] ?>">

                                            <select name="status"
                                                    class="border border-slate-300 bg-white px-3 py-2 text-sm"
                                                    aria-label="Application status">
                                                <option value="pending"
                                                    <?= $a['status'] === 'pending' ? 'selected' : '' ?>>
                                                    Pending
                                                </option>

                                                <option value="accepted"
                                                    <?= $a['status'] === 'accepted' ? 'selected' : '' ?>>
                                                    Accept
                                                </option>

                                                <option value="rejected"
                                                    <?= $a['status'] === 'rejected' ? 'selected' : '' ?>>
                                                    Reject
                                                </option>
                                            </select>

                                            <button type="submit"
                                                    class="bg-slate-900 hover:bg-slate-700 text-white px-4 py-2 text-sm font-semibold">
                                                Update
                                            </button>
                                        </form>

                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="p-6 text-sm text-slate-500">
                                No applications have been received for this opportunity yet.
                            </p>
                        <?php endif; ?>

                    </div>
                <?php endforeach; ?>
            </div>

        <?php else: ?>
            <div class="bg-white border border-slate-200 p-10 text-center">
                <h3 class="text-xl font-bold text-slate-900">
                    You have not posted an opportunity yet.
                </h3>

                <p class="text-slate-600 mt-2">
                    Create an opportunity to connect with talented people.
                </p>

                <a href="opportunities.php#create"
                   class="inline-block mt-5 bg-slate-900 hover:bg-slate-700 text-white px-5 py-3 font-semibold">
                    Create opportunity →
                </a>
            </div>
        <?php endif; ?>


    <?php else: ?>

        <div class="bg-white border border-slate-200 p-8">
            <p class="text-sm font-semibold uppercase tracking-widest text-amber-700">
                Administration
            </p>

            <h2 class="text-2xl font-bold text-slate-900 mt-3">
                Manage CLOJO
            </h2>

            <p class="text-slate-600 mt-3">
                Manage registered users, talent profiles and opportunities from the admin panel.
            </p>

            <a href="admin.php"
               class="inline-block mt-6 bg-slate-900 hover:bg-slate-700 text-white px-5 py-3 font-semibold">
                Open admin panel →
            </a>
        </div>

    <?php endif; ?>

</section>

<?php require 'includes/footer.php'; ?>
