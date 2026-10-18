
<?php
require_once 'db.php';
require 'includes/header.php';


$pageTitle = 'About CLOJO';

function clean($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

$talentCount = $pdo->query("
    SELECT COUNT(*) FROM users WHERE role = 'talent'
")->fetchColumn();

$sponsorCount = $pdo->query("
    SELECT COUNT(*) FROM users WHERE role = 'sponsor'
")->fetchColumn();

$opportunityCount = $pdo->query("
    SELECT COUNT(*)
    FROM opportunities
    WHERE deadline IS NULL OR deadline >= CURDATE()
")->fetchColumn();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>About CLOJO Talent Connect</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        navy: '#14243B',
                        gold: '#D9A72E'
                    }
                }
            }
        };
    </script>
</head>

<body class="bg-white text-slate-800">
<main>

    <section class="bg-slate-50 border-b border-slate-200">
        <div class="max-w-6xl mx-auto px-5 py-16 md:py-20">

            <p class="text-sm font-semibold uppercase tracking-widest text-amber-700">
                About CLOJO
            </p>

            <h1 class="text-4xl md:text-5xl font-bold text-navy mt-4 max-w-3xl leading-tight">
                A place to share talent and find support.
            </h1>

            <p class="mt-5 text-slate-600 leading-7 max-w-2xl">
                CLOJO Talent Connect brings talented people and sponsors together.
                Talents can create profiles and look for opportunities, while
                sponsors can post opportunities and review applications.
            </p>

        </div>
    </section>

    <section class="max-w-6xl mx-auto px-5 py-14">

        <div class="grid md:grid-cols-2 gap-12">

            <div>
                <h2 class="text-2xl font-bold text-navy">
                    Why CLOJO?
                </h2>

                <p class="mt-4 text-slate-600 leading-7">
                    Many people have skills in areas such as technology, music,
                    fashion, sports, photography and creative arts. However,
                    finding someone who can provide support is not always easy.
                </p>

                <p class="mt-4 text-slate-600 leading-7">
                    CLOJO provides a place where people can introduce their work
                    and find opportunities that match their interests. Support
                    may include funding, equipment, mentorship, training or exposure.
                </p>
            </div>

            <div class="border border-slate-200 p-6 md:p-8">

                <h2 class="text-2xl font-bold text-navy">
                    How it works
                </h2>

                <div class="mt-6 space-y-6">

                    <div class="flex gap-4">
                        <span class="text-amber-700 font-bold">01</span>
                        <div>
                            <h3 class="font-semibold text-navy">Create an account</h3>
                            <p class="text-sm text-slate-600 mt-1">
                                Register as a talent or a sponsor.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <span class="text-amber-700 font-bold">02</span>
                        <div>
                            <h3 class="font-semibold text-navy">Find or post opportunities</h3>
                            <p class="text-sm text-slate-600 mt-1">
                                Talents can browse opportunities, and sponsors can
                                publish available support.
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <span class="text-amber-700 font-bold">03</span>
                        <div>
                            <h3 class="font-semibold text-navy">Submit applications</h3>
                            <p class="text-sm text-slate-600 mt-1">
                                Talents can apply and check the status of their applications.
                            </p>
                        </div>
                    </div>

                </div>
            </div>

        </div>

    </section>

    <section class="bg-slate-50 border-y border-slate-200">
        <div class="max-w-6xl mx-auto px-5 py-10">

            <h2 class="text-2xl font-bold text-navy">
                CLOJO at a glance
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 mt-8">

                <div>
                    <p class="text-3xl font-bold text-navy">
                        <?= (int) $talentCount ?>
                    </p>
                    <p class="text-sm text-slate-500 mt-2">
                        Registered talents
                    </p>
                </div>

                <div>
                    <p class="text-3xl font-bold text-navy">
                        <?= (int) $sponsorCount ?>
                    </p>
                    <p class="text-sm text-slate-500 mt-2">
                        Registered sponsors
                    </p>
                </div>

                <div>
                    <p class="text-3xl font-bold text-navy">
                        <?= (int) $opportunityCount ?>
                    </p>
                    <p class="text-sm text-slate-500 mt-2">
                        Available opportunities
                    </p>
                </div>

            </div>

        </div>
    </section>

    <section class="max-w-6xl mx-auto px-5 py-14">

        <div class="border-l-4 border-gold pl-6 py-2">

            <h2 class="text-2xl font-bold text-navy">
                About this project
            </h2>

            <p class="mt-4 text-slate-600 leading-7 max-w-3xl">
                CLOJO Talent Connect is a student project by
                <strong>ISHIMWE Claude</strong>. The website is built with
                PHP, MySQL, HTML and Tailwind CSS. It demonstrates how a web
                platform can be used to manage talent profiles, opportunities
                and applications.
            </p>

            <a href="register.php"
               class="inline-block mt-6 bg-navy text-white px-5 py-3 hover:bg-slate-700 transition">
                Create an account &rarr;
            </a>

        </div>

    </section>

</main>


</body>
</html>
