<div id="offline-overlay" style="display: none; position: fixed; inset: 0; z-index: 99999; align-items: center; justify-content: center; padding: 1.5rem; font-family: 'Outfit', 'Roboto', sans-serif;">
    <!-- Blur Background -->
    <div style="position: absolute; inset: 0; background: rgba(131, 24, 67, 0.2); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);"></div>
    
    <!-- Content Card -->
    <div style="position: relative; background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px); padding: 2.5rem; border-radius: 2.5rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15); max-width: 400px; w: 100%; text-align: center; border: 1px solid rgba(255, 255, 255, 0.5);">
        <div style="margin-bottom: 1.5rem; display: flex; justify-content: center;">
            <div style="width: 80px; height: 80px; background: #fce7f3; border-radius: 1.25rem; display: flex; align-items: center; justify-content: center; color: #ec4899;">
                <svg style="width: 40px; height: 40px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.243 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3m8.293 8.293l1.414 1.414"></path>
                </svg>
            </div>
        </div>
        
        <h2 style="font-size: 1.5rem; font-weight: 700; color: #111827; margin-bottom: 0.75rem;">Koneksi Terputus</h2>
        <p style="color: #4b5563; line-height: 1.625; margin-bottom: 2rem;">
            Sepertinya Anda sedang tidak terhubung ke internet. Silakan periksa koneksi internet Anda untuk melanjutkan.
        </p>
        
        <button onclick="window.location.reload()" style="cursor: pointer; width: 100%; background: #db2777; color: white; padding: 1rem 2rem; border-radius: 9999px; font-weight: 600; border: none; transition: all 0.2s; box-shadow: 0 10px 15px -3px rgba(219, 39, 119, 0.3);">
            Segarkan Halaman
        </button>
    </div>
</div>

<script>
    (function() {
        function updateOnlineStatus() {
            const overlay = document.getElementById('offline-overlay');
            if (navigator.onLine) {
                overlay.style.display = 'none';
            } else {
                overlay.style.display = 'flex';
            }
        }

        window.addEventListener('online', updateOnlineStatus);
        window.addEventListener('offline', updateOnlineStatus);

        // Initial check
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', updateOnlineStatus);
        } else {
            updateOnlineStatus();
        }
    })();
</script>
