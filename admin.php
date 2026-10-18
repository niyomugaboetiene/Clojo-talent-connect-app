
<section class="bg-clojo-navy text-white">
    <div class="max-w-7xl mx-auto px-5 py-10">
        <p class="text-clojo-gold uppercase tracking-widest text-sm font-bold">
            Manage the platform
        </p>
        <h1 class="text-4xl font-extrabold mt-2">
            Admin panel
        </h1>
    </div>
</section>

<section class="max-w-7xl mx-auto px-5 py-10">

    <?php if ($message): ?>
        <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl p-4 mb-6">
            <?= e($message) ?>
        </div>
    <?php endif; ?>

    <div class="grid sm:grid-cols-3 gap-4 mb-8">
        <?php foreach ([
            ['Users', count($users)],
            ['Talent profiles', count($profiles)],
            ['Opportunities', count($opps)]
        ] as $stat): ?>

            <div class="bg-white border rounded-2xl p-5">
                <p class="text-slate-500 text-sm">
                    <?= e($stat[0]) ?>
                </p>
                <p class="text-3xl font-extrabold mt-2">
                    <?= (int) $stat[1] ?>
                </p>
            </div>

        <?php endforeach; ?>
    </div>

    <div class="bg-white border rounded-2xl overflow-hidden mb-8">
        <div class="p-5 border-b">
            <h2 class="text-xl font-extrabold">
                Registered users
            </h2>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="p-4">Name</th>
                        <th class="p-4">Email</th>
                        <th class="p-4">Role</th>
                        <th class="p-4">Joined</th>
                    </tr>
                </thead>

                <tbody>
                    <?php foreach ($users as $u): ?>
                        <tr class="border-t">
                            <td class="p-4 font-semibold">
                                <?= e($u['name']) ?>
                            </td>
                            <td class="p-4">
                                <?= e($u['email']) ?>
                            </td>
                            <td class="p-4">
                                <?= e($u['role']) ?>
                            </td>
                            <td class="p-4">
                                <?= e($u['created_at']) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="grid lg:grid-cols-2 gap-6">

        <!-- Talent Profiles -->
        <div class="bg-white border rounded-2xl overflow-hidden">
            <div class="p-5 border-b">
                <h2 class="text-xl font-extrabold">
                    Talent profiles
                </h2>
            </div>

            <?php if ($profiles): ?>
                <?php foreach ($profiles as $p): ?>
                    <div class="p-4 border-b flex justify-between items-center gap-3">
                        <div>
                            <p class="font-bold">
                                <?= e($p['name']) ?>
                            </p>
                            <p class="text-sm text-slate-500">
                                <?= e($p['location'] ?: 'Location not set') ?>
                            </p>
                        </div>

                        <form
                            method="post"
                            onsubmit="return confirm('Delete this talent profile?')"
                        >
                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= e(csrf_token()) ?>"
                            >
                            <input
                                type="hidden"
                                name="type"
                                value="profile"
                            >
                            <input
                                type="hidden"
                                name="id"
                                value="<?= (int) $p['user_id'] ?>"
                            >

                            <button class="text-red-700 border border-red-200 rounded-lg px-3 py-2 text-sm font-bold">
                                Delete profile
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="p-5 text-slate-500 text-sm">
                    No profiles yet.
                </p>
            <?php endif; ?>
        </div>

        <!-- Opportunities -->
        <div class="bg-white border rounded-2xl overflow-hidden">
            <div class="p-5 border-b">
                <h2 class="text-xl font-extrabold">
                    Opportunities
                </h2>
            </div>

            <?php if ($opps): ?>
                <?php foreach ($opps as $o): ?>
                    <div class="p-4 border-b flex justify-between items-center gap-3">
                        <div>
                            <p class="font-bold">
                                <?= e($o['title']) ?>
                            </p>
                            <p class="text-sm text-slate-500">
                                By <?= e($o['sponsor_name']) ?>
                            </p>
                        </div>

                        <form
                            method="post"
                            onsubmit="return confirm('Delete this opportunity and its applications?')"
                        >
                            <input
                                type="hidden"
                                name="csrf_token"
                                value="<?= e(csrf_token()) ?>"
                            >
                            <input
                                type="hidden"
                                name="type"
                                value="opportunity"
                            >
                            <input
                                type="hidden"
                                name="id"
                                value="<?= (int) $o['id'] ?>"
                            >

                            <button class="text-red-700 border border-red-200 rounded-lg px-3 py-2 text-sm font-bold">
                                Delete
                            </button>
                        </form>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="p-5 text-slate-500 text-sm">
                    No opportunities yet.
                </p>
            <?php endif; ?>
        </div>

    </div>
</section>

<?php require 'includes/footer.php'; ?>
