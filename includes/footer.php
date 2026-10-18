
</main>
<footer class="bg-white border-t border-slate-200 mt-16">
    <div class="max-w-6xl mx-auto px-5 py-10">

        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-8">

            <div>
                <a href="index.php" class="brand text-xl font-bold">
                    <span class="text-navy">CLOJO</span>
                    <span class="text-gold">TALENT CONNECT</span>
                </a>

                <p class="text-slate-600 text-sm leading-6 mt-3 max-w-xs">
                    Helping talented people find opportunities and connect
                    with those who can support their work.
                </p>
            </div>

            <div>
                <h3 class="font-semibold text-navy">
                    Explore
                </h3>

                <div class="mt-4 space-y-3 text-sm text-slate-600">
                    <a href="talents.php"
                       class="block hover:text-gold transition-colors">
                        Discover talents
                    </a>

                    <a href="opportunities.php"
                       class="block hover:text-gold transition-colors">
                        Opportunities
                    </a>

                    <a href="about.php"
                       class="block hover:text-gold transition-colors">
                        About CLOJO
                    </a>
                </div>
            </div>

            <div>
                <h3 class="font-semibold text-navy">
                    About this project
                </h3>

                <p class="text-slate-600 text-sm mt-4 leading-6">
                    CLOJO Talent Connect is a project by
                    <span class="text-navy font-semibold">
                        ISHIMWE Claude
                    </span>.
                </p>
            </div>

        </div>

        <div class="border-t border-slate-200 mt-8 pt-5 flex flex-col sm:flex-row justify-between gap-3 text-xs text-slate-500">

            <p>
                &copy; <?= date('Y') ?> CLOJO Talent Connect.
                All rights reserved.
            </p>

            <a href="index.php"
               class="text-navy hover:text-gold transition-colors">
                Back to home &uarr;
            </a>

        </div>

    </div>

</footer>

</body>
</html>
