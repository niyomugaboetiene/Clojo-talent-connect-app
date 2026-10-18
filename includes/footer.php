
</main>

<footer class="bg-clojo-navy text-white mt-16">

    <div class="max-w-6xl mx-auto px-5 py-10">

        <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-8">

            <!-- Project name -->
            <div>
                <a href="index.php"
                   class="brand text-xl font-bold">
                    CLOJO
                    <span class="text-clojo-gold">TALENT CONNECT</span>
                </a>

                <p class="text-slate-300 text-sm leading-6 mt-3 max-w-xs">
                    Helping talented people find opportunities and connect
                    with those who can support their work.
                </p>
            </div>

            <!-- Navigation links -->
            <div>
                <h3 class="font-semibold text-white">
                    Explore
                </h3>

                <div class="mt-4 space-y-3 text-sm text-slate-300">
                    <a href="talents.php"
                       class="block hover:text-clojo-gold transition-colors">
                        Discover talents
                    </a>

                    <a href="opportunities.php"
                       class="block hover:text-clojo-gold transition-colors">
                        Opportunities
                    </a>

                    <a href="about.php"
                       class="block hover:text-clojo-gold transition-colors">
                        About CLOJO
                    </a>
                </div>
            </div>

            <!-- Project information -->
            <div>
                <h3 class="font-semibold text-white">
                    About this project
                </h3>

                <p class="text-slate-300 text-sm mt-4 leading-6">
                    CLOJO Talent Connect is a project by
                    <span class="text-clojo-gold font-semibold">
                        ISHIMWE Claude
                    </span>.
                </p>
            </div>

        </div>

        <!-- Bottom line -->
        <div class="border-t border-white/15 mt-8 pt-5 flex flex-col sm:flex-row justify-between gap-3 text-xs text-slate-400">

            <p>
                &copy; <?= date('Y') ?> CLOJO Talent Connect.
                All rights reserved.
            </p>

            <a href="index.php"
               class="hover:text-white transition-colors">
                Back to home &uarr;
            </a>

        </div>

    </div>

</footer>

</body>
</html>
