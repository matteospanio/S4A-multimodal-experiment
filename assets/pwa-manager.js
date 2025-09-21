// PWA offline functionality and UI enhancements

class PWAManager {
    constructor() {
        this.isOnline = navigator.onLine;
        this.installPrompt = null;
        this.init();
    }

    init() {
        this.setupOfflineDetection();
        this.setupInstallPrompt();
        this.enhanceFormsForOffline();
        this.setupPWAStatusIndicators();
    }

    setupOfflineDetection() {
        // Create offline indicator
        const offlineIndicator = document.createElement('div');
        offlineIndicator.className = 'offline-indicator';
        offlineIndicator.innerHTML = '📡 You are currently offline. Some features may be limited.';
        document.body.appendChild(offlineIndicator);

        // Handle online/offline events
        window.addEventListener('online', () => {
            this.isOnline = true;
            offlineIndicator.classList.remove('show');
            console.log('Back online');
            this.syncOfflineData();
        });

        window.addEventListener('offline', () => {
            this.isOnline = false;
            offlineIndicator.classList.add('show');
            console.log('Gone offline');
        });

        // Show indicator if already offline
        if (!navigator.onLine) {
            offlineIndicator.classList.add('show');
        }
    }

    setupInstallPrompt() {
        // Listen for install prompt
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            this.installPrompt = e;
            this.showInstallButton();
        });

        // Handle successful install
        window.addEventListener('appinstalled', () => {
            console.log('PWA installed successfully');
            this.hideInstallButton();
            this.installPrompt = null;
        });
    }

    showInstallButton() {
        // Create install prompt
        let installBanner = document.querySelector('.pwa-install-prompt');
        if (!installBanner) {
            installBanner = document.createElement('div');
            installBanner.className = 'pwa-install-prompt';
            installBanner.innerHTML = `
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong>Install S4A Experiment</strong>
                        <br>Add to your home screen for easier access
                    </div>
                    <div>
                        <button class="btn btn-light btn-sm me-2" onclick="pwaManager.installApp()">Install</button>
                        <button class="btn btn-outline-light btn-sm" onclick="pwaManager.hideInstallButton()">×</button>
                    </div>
                </div>
            `;
            document.body.appendChild(installBanner);
        }
        
        setTimeout(() => {
            installBanner.classList.add('show');
        }, 2000); // Show after 2 seconds
    }

    hideInstallButton() {
        const installBanner = document.querySelector('.pwa-install-prompt');
        if (installBanner) {
            installBanner.classList.remove('show');
        }
    }

    async installApp() {
        if (!this.installPrompt) return;

        const result = await this.installPrompt.prompt();
        console.log('Install prompt result:', result);
        
        this.installPrompt = null;
        this.hideInstallButton();
    }

    enhanceFormsForOffline() {
        // Store form data locally when offline
        const forms = document.querySelectorAll('form[data-offline-storage]');
        
        forms.forEach(form => {
            form.addEventListener('submit', (e) => {
                if (!this.isOnline) {
                    e.preventDefault();
                    this.storeFormDataOffline(form);
                    this.showOfflineSubmissionMessage();
                }
            });
        });
    }

    storeFormDataOffline(form) {
        const formData = new FormData(form);
        const data = Object.fromEntries(formData);
        data.timestamp = Date.now();
        data.formId = form.id || 'unknown';

        // Store in localStorage
        let offlineSubmissions = JSON.parse(localStorage.getItem('offlineSubmissions') || '[]');
        offlineSubmissions.push(data);
        localStorage.setItem('offlineSubmissions', JSON.stringify(offlineSubmissions));

        console.log('Form data stored offline:', data);
    }

    showOfflineSubmissionMessage() {
        const message = document.createElement('div');
        message.className = 'alert alert-info alert-dismissible fade show';
        message.innerHTML = `
            <strong>Offline Submission</strong> Your response has been saved locally and will be submitted when you're back online.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const container = document.querySelector('.main-container') || document.body;
        container.insertBefore(message, container.firstChild);
    }

    async syncOfflineData() {
        const offlineSubmissions = JSON.parse(localStorage.getItem('offlineSubmissions') || '[]');
        
        if (offlineSubmissions.length === 0) return;

        console.log('Syncing offline submissions:', offlineSubmissions.length);

        // Attempt to submit each stored form
        for (const submission of offlineSubmissions) {
            try {
                await this.submitStoredData(submission);
            } catch (error) {
                console.error('Failed to sync submission:', error);
                // Keep the data for next sync attempt
                return;
            }
        }

        // Clear stored data after successful sync
        localStorage.removeItem('offlineSubmissions');
        this.showSyncSuccessMessage();
    }

    async submitStoredData(submission) {
        // This would need to be adapted based on the actual form submission endpoints
        const formData = new FormData();
        Object.keys(submission).forEach(key => {
            if (key !== 'timestamp' && key !== 'formId') {
                formData.append(key, submission[key]);
            }
        });

        // Find the original form action URL
        const form = document.getElementById(submission.formId);
        const action = form ? form.action : '/submit';

        const response = await fetch(action, {
            method: 'POST',
            body: formData
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        return response;
    }

    showSyncSuccessMessage() {
        const message = document.createElement('div');
        message.className = 'alert alert-success alert-dismissible fade show';
        message.innerHTML = `
            <strong>Sync Complete</strong> Your offline responses have been submitted successfully.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        
        const container = document.querySelector('.main-container') || document.body;
        container.insertBefore(message, container.firstChild);

        // Auto-dismiss after 5 seconds
        setTimeout(() => {
            message.remove();
        }, 5000);
    }

    setupPWAStatusIndicators() {
        // Add PWA indicator to show when running as installed app
        if (this.isPWA()) {
            document.body.classList.add('pwa-mode');
            console.log('Running in PWA mode');
        }

        // Add device type classes for responsive styling
        if (this.isMobile()) {
            document.body.classList.add('mobile-device');
        }
        if (this.isTablet()) {
            document.body.classList.add('tablet-device');
        }
    }

    isPWA() {
        return window.matchMedia('(display-mode: standalone)').matches || 
               window.navigator.standalone || 
               document.referrer.includes('android-app://');
    }

    isMobile() {
        return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
    }

    isTablet() {
        return /iPad|Android(?!.*Mobile)/i.test(navigator.userAgent) || 
               (this.isMobile() && window.innerWidth > 768);
    }
}

// Initialize PWA manager when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.pwaManager = new PWAManager();
});

// Export for global access
if (typeof module !== 'undefined' && module.exports) {
    module.exports = PWAManager;
}