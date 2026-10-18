
<!-- Page heading -->
<section class="bg-slate-50 border-b border-slate-200">
    <div class="max-w-6xl mx-auto px-5 py-12">

        <p class="text-sm font-semibold uppercase tracking-widest text-amber-700">
            Administration
        </p>

        <h1 class="text-3xl md:text-4xl font-bold text-navy mt-3">
            Admin panel
        </h1>

        <p class="text-slate-600 mt-3">
            Manage registered users, talent profiles and opportunities.
        </p>

    </div>
</section>

<section class="max-w-6xl mx-auto px-5 py-10">

    <?php if ($message): ?>
        <div class="bg-green-50 border border-green-200 text-green-800 p-4 mb-6">
            <?= e($message) ?>
        </div>
    <?php endif; ?>

    <!-- Overview -->
    <div class="grid sm:grid-cols-3 gap-5 mb-10">

        <div class="border border-slate-200 p-5">
            <p class="text-sm text-slate-500">Registered users</p>
            <p class="text-3xl font-bold text-navy mt-2">
                <?= count($users) ?>
            </p>
        </div>

        <div class="border border-slate-200 p-5">
            <p class="text-sm text-slate-500">Talent profiles</p>
            <p class="text-3xl font-bold text-navy mt-2">
                <?= count($profiles) ?>
            </p>
        </div>

        <div class="border border-slate-200 p-5">
            <p class="text-sm text-slate-500">Opportunities</p>
            <p class="text-3xl font-bold text-navy mt-2">
                <?= count($opps) ?>
            </p>
        </div>

    </div>

    <!-- Registered users -->
    <section class="mb-10">

        <div class="flex flex-wrap items-end justify-between gap-3 mb-5">
            <div>
                <h2 class="text-2xl font-bold text-navy">
                    Registered users
                </h2>
                <p class="text-sm text-slate-500 mt-2">
                    View account details and user roles.
                </p>
            </div>
        </div>

        <div class="border border-slate-200 overflow-x-auto">
            <table class="w-full text-sm text-left">

                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-5 py-4 font-semibold">Name</th>
                        <th class="px-5 py-4 font-semibold">Email</th>
                        <th class="px-5 py-4 font-semibold">Role</th>
                        <th class="px-5 py-4 font-semibold">Date joined</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-slate-200">

                    <?php if ($users): ?>

                        <?php foreach ($users as $u): ?>
                            <tr class="hover:bg-slate-50">

                                <td class="px-5 py-4 font-semibold text-navy">
                                    <?= e($u['name']) ?>
                                </td>

                                <td class="px-5 py-4 text-slate-600">
                                    <?= e($u['email']) ?>
                                </td>

                                <td class="px-5 py-4">
                                    <?php
                                    $roleClass = 'bg-slate-100 text-slate-700';

                                    if ($u['role'] === 'admin') {
                                        $roleClass = 'bg-amber-100 text-amber-800';
                                    } elseif ($u['role'] === 'sponsor') {
                                        $roleClass = 'bg-blue-50 text-blue-700';
                                    } elseif ($u['role'] === 'talent') {
                                        $roleClass = 'bg-green-50 text-green-700';
                                    }
                                    ?>

                                    <span class="inline-block px-2.5 py-1 text-xs font-semibold <?= $roleClass ?>">
                                        <?= e(ucfirst($u['role'])) ?>
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-slate-500 whitespace-nowrap">
                                    <?= e($u['created_at']) ?>
                                </td>

                            </tr>
                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="4" class="px-5 py-8 text-center text-slate-500">
                                No users have registered yet.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>
            </table>
        </div>

    </section>

    <!-- Profiles and opportunities -->
    <div class="grid lg:grid-cols-2 gap-8">

        <!-- Talent profiles -->
        <section class="border border-slate-200">

            <div class="p-5 border-b border-slate-200">
                <h2 class="text-xl font-bold text-navy">
                    Talent profiles
                </h2>

                <p class="text-sm text-slate-500 mt-2">
                    Review profiles created by talents.
                </p>
            </div>

            <?php if ($profiles): ?>

                <?php foreach ($profiles as $p): ?>

                    <div class="p-5 border-b border-slate-200 last:border-b-0 flex flex-col sm:flex-row sm:items-center justify-between gap-4">

                        <div>
                            <p class="font-semibold text-navy">
                                <?= e($p['name']) ?>
                            </p>

                            <p class="text-sm text-slate-500 mt-1">
                                <?= e($p['location'] ?: 'Location not set') ?>
                            </p>
                        </div>

                        <form method="post"
                              onsubmit="return confirm('Delete this talent profile?')">

                            <input type="hidden"
                                   name="csrf_token"
                                   value="<?= e(csrf_token()) ?>">

                            <input type="hidden" name="type" value="profile">

                            <input type="hidden"
                                   name="id"
                                   value="<?= (int) $p['user_id'] ?>">

                            <button type="submit"
                                    class="border border-red-200 text-red-700 px-3 py-2 text-sm font-semibold hover:bg-red-50 transition">
                                Delete profile
                            </button>

                        </form>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <p class="p-5 text-sm text-slate-500">
                    No talent profiles have been created yet.
                </p>

            <?php endif; ?>

        </section>

        <!-- Opportunities -->
        <section class="border border-slate-200">

            <div class="p-5 border-b border-slate-200">
                <h2 class="text-xl font-bold text-navy">
                    Opportunities
                </h2>

                <p class="text-sm text-slate-500 mt-2">
                    Manage opportunities posted by sponsors.
                </p>
            </div>

            <?php if ($opps): ?>

                <?php foreach ($opps as $o): ?>

                    <div class="p-5 border-b border-slate-200 last:border-b-0 flex flex-col sm:flex-row sm:items-center justify-between gap-4">

                        <div class="min-w-0">
                            <p class="font-semibold text-navy">
                                <?= e($o['title']) ?>
                            </p>

                            <p class="text-sm text-slate-500 mt-1">
                                Posted by <?= e($o['sponsor_name']) ?>
                            </p>
                        </div>

                        <form method="post"
                              onsubmit="return confirm('Delete this opportunity and its applications?')">

                            <input type="hidden"
                                   name="csrf_token"
                                   value="<?= e(csrf_token()) ?>">

                            <input type="hidden"
                                   name="type"
                                   value="opportunity">

                            <input type="hidden"
                                   name="id"
                                   value="<?= (int) $o['id'] ?>">

                            <button type="submit"
                                    class="border border-red-200 text-red-700 px-3 py-2 text-sm font-semibold hover:bg-red-50 transition">
                                Delete
                            </button>

                        </form>

                    </div>

                <?php endforeach; ?>

            <?php else: ?>

                <p class="p-5 text-sm text-slate-500">
                    No opportunities have been posted yet.
                </p>

            <?php endif; ?>

        </section>

    </div>

</section>

<?php require 'includes/footer.php'; ?>