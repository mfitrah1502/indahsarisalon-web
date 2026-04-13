<footer class="pc-footer">
    <div class="footer-wrapper container-fluid">
        <div class="row">
            <div class="col-sm-6 my-1">
                <p class="m-0">
                    Indah Sari Salon, All rights reserved
                    <a href="https://themeforest.net/user/codedthemes" target="_blank"></a>
                </p>
            </div>
            <div class="col-sm-6 ms-auto my-1 text-end">
                <p class="m-0">
                    <span id="currentDateTime"></span>
                </p>
            </div>
        </div>
    </div>
</footer>

<script>
    function updateDateTime() {
        const now = new Date();

        // Opsi format tanggal, misal: Senin, 9 Maret 2026
        const optionsDate = { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' };
        const formattedDate = now.toLocaleDateString('id-ID', optionsDate);

        // Format waktu 12 jam dengan AM/PM, contoh: 02:05:09 PM
        const optionsTime = { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: true };
        const formattedTime = now.toLocaleTimeString('en-US', optionsTime);

        // Gabungkan tanggal dan waktu
        document.getElementById('currentDateTime').textContent = `${formattedDate} - ${formattedTime}`;
    }

    setInterval(updateDateTime, 1000);
    updateDateTime();
</script>