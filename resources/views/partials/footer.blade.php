<footer class="pc-footer border-top bg-white py-3 mt-4">
    <div class="footer-wrapper container-fluid px-4">
        <div class="row align-items-center">
            <div class="col-sm-6 text-center text-sm-start">
                <p class="mb-0 text-muted" style="font-size: 0.85rem;">
                    <span class="fw-bold text-primary">Indah Sari Salon</span> &copy; {{ date('Y') }}. 
                    <span class="d-none d-md-inline ms-1 text-muted opacity-75">| All rights reserved.</span>
                </p>
            </div>
            <div class="col-sm-6 text-center text-sm-end mt-2 mt-sm-0">
                <div class="d-inline-flex align-items-center bg-light-primary px-3 py-1 rounded-pill">
                    <i class="ti ti-clock me-2 text-primary" style="font-size: 1rem;"></i>
                    <span id="currentDateTime" class="text-primary fw-medium" style="font-size: 0.8rem; letter-spacing: 0.5px;"></span>
                </div>
            </div>
        </div>
    </div>
</footer>

<script>
    function updateDateTime() {
        const now = new Date();

        // Indonesia Locale for Day and Month
        const optionsDate = { 
            weekday: 'long', 
            day: 'numeric', 
            month: 'long', 
            year: 'numeric' 
        };
        const formattedDate = now.toLocaleDateString('id-ID', optionsDate);

        // Standard 12-hour format for Time
        const optionsTime = { 
            hour: '2-digit', 
            minute: '2-digit', 
            second: '2-digit', 
            hour12: true 
        };
        const formattedTime = now.toLocaleTimeString('en-US', optionsTime);

        const el = document.getElementById('currentDateTime');
        if (el) {
            el.textContent = `${formattedDate} • ${formattedTime}`;
        }
    }

    // Update immediately and then every second
    if (typeof dateTimeInterval !== 'undefined') clearInterval(dateTimeInterval);
    var dateTimeInterval = setInterval(updateDateTime, 1000);
    updateDateTime();
</script>

<style>
    .pc-footer {
        margin-left: 0 !important;
        margin-right: 0 !important;
        background: rgba(255, 255, 255, 0.9) !important;
        backdrop-filter: blur(10px);
    }
    
    [data-bs-theme="dark"] .pc-footer {
        background: rgba(30, 30, 30, 0.9) !important;
        border-color: #333 !important;
    }
    
    .bg-light-primary {
        background-color: #fcebed !important;
    }
    
    [data-bs-theme="dark"] .bg-light-primary {
        background-color: rgba(234, 130, 144, 0.1) !important;
    }
</style>