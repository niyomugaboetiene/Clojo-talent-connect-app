
<?php
require_once 'includes/functions.php';
$pageTitle = 'About CLOJO';
require 'includes/header.php';
?>

<section class="bg-clojo-navy text-white hero-grid">
    <div class="max-w-5xl mx-auto px-5 py-16 md:py-20">
        <p class="text-clojo-gold font-bold uppercase tracking-widest text-sm">
            About CLOJO
        </p>

        <h1 class="text-4xl md:text-5xl font-extrabold mt-3">
            Talent is everywhere.
            <br>
            <span class="text-clojo-gold">
                Opportunity should be too.
            </span>
        </h1>

        <p class="text-slate-300 text-lg mt-6 max-w-3xl leading-8">
            CLOJO Talent Connect helps talented people share their skills and connect with sponsors who can support their next step.
        </p>
    </div>
</section>

<section class="max-w-6xl mx-auto px-5 py-14 grid md:grid-cols-3 gap-6">
    <article class="bg-white rounded-2xl border p-6">
        <div class="text-3xl">🌟</div>
        <h2 class="text-xl font-extrabold mt-4">Show your talent</h2>
        <p class="text-slate-600 mt-3 leading-7">
            Create a profile and help people discover what you can do.
        </p>
    </article>

    <article class="bg-white rounded-2xl border p-6">
        <div class="text-3xl">🤝</div>
        <h2 class="text-xl font-extrabold mt-4">Find support</h2>
        <p class="text-slate-600 mt-3 leading-7">
            Discover mentorship, training, equipment, funding, and exposure.
        </p>
    </article>

    <article class="bg-white rounded-2xl border p-6">
        <div class="text-3xl">🚀</div>
        <h2 class="text-xl font-extrabold mt-4">Grow together</h2>
        <p class="text-slate-600 mt-3 leading-7">
            Give sponsors and talented people a simple way to start collaborations.
        </p>
    </article>
</section>

<section class="max-w-6xl mx-auto px-5 pb-14">
    <div class="rounded-3xl bg-amber-50 border border-amber-200 p-8 md:p-10">
        <p class="text-amber-800 uppercase tracking-widest text-sm font-bold">
            The project
        </p>

        <h2 class="text-3xl font-extrabold text-clojo-navy mt-2">
            Built for possibility.
        </h2>

        <p class="text-slate-700 mt-4 leading-7 max-w-3xl">
            CLOJO TALENT CONNECT is a student full-stack project owned by
            <strong>ISHIMWE</strong>. It uses Core PHP and MySQL to demonstrate how a website can connect talent with opportunity.
        </p>

        <a href="register.php" class="inline-block mt-6 bg-clojo-navy text-white px-6 py-3 rounded-full font-bold">
            Join CLOJO →
        </a>
    </div>
</section>

<?php
require 'includes/footer.php';
?>
