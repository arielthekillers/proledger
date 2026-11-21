    <!-- Global ProLedger App & Modal System -->
    <script src="<?= APP_URL ?>/js/app.js"></script>
    <script>
    (function() {
        if (window.parent && window.parent !== window && window.ProModal) {
            // Beritahu parent modal frame ketika ada modal internal (seperti upload BAST/edit item) dibuka/ditutup
            var origOpen = window.ProModal.open;
            window.ProModal.open = function(modalId) {
                origOpen.call(window.ProModal, modalId);
                if (typeof window.parent.setPaketModalHeight === 'function') {
                    window.parent.setPaketModalHeight(window.innerHeight * 0.90);
                }
            };

            var origClose = window.ProModal.close;
            window.ProModal.close = function(modalId) {
                origClose.call(window.ProModal, modalId);
                setTimeout(function() {
                    if (typeof window.parent.autoResizeModalDynamic === 'function') {
                        window.parent.autoResizeModalDynamic();
                    }
                }, 220);
            };
        }
    })();
    </script>
</body>
</html>
