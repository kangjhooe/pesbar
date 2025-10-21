// Portal Berita Kabupaten Pesisir Barat - JavaScript

document.addEventListener('DOMContentLoaded', function() {
    // Mobile Menu Toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const navMenu = document.querySelector('.nav-menu');
    
    if (mobileMenuToggle && navMenu) {
        mobileMenuToggle.addEventListener('click', function() {
            navMenu.classList.toggle('active');
            mobileMenuToggle.classList.toggle('active');
        });
    }

    // Search Functionality
    const searchInput = document.querySelector('.search-input');
    const searchBtn = document.querySelector('.search-btn');
    
    if (searchBtn) {
        searchBtn.addEventListener('click', function(e) {
            e.preventDefault();
            performSearch();
        });
    }
    
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performSearch();
            }
        });
    }

    // Newsletter Subscription
    const newsletterForm = document.querySelector('.newsletter-form');
    if (newsletterForm) {
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleNewsletterSubscription();
        });
    }


    // Smooth Scrolling for Anchor Links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Add Animation Classes on Scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    }, observerOptions);

    // Observe news items for animation
    document.querySelectorAll('.news-item, .featured-article, .widget').forEach(el => {
        observer.observe(el);
    });

    // Weather Widget (Simulated)
    updateWeatherWidget();

    // Breaking News Ticker
    startBreakingNewsTicker();

    // Initialize Tooltips
    initializeTooltips();
});

// Search Function
function performSearch() {
    const searchInput = document.querySelector('.search-input');
    const searchTerm = searchInput.value.trim();
    
    if (searchTerm.length < 3) {
        showNotification('Masukkan minimal 3 karakter untuk pencarian', 'warning');
        return;
    }
    
    // Simulate search - in real implementation, this would make an API call
    showNotification(`Mencari: "${searchTerm}"`, 'info');
    
    // Here you would typically redirect to search results page
    // window.location.href = `search.html?q=${encodeURIComponent(searchTerm)}`;
}

// Newsletter Subscription
function handleNewsletterSubscription() {
    const emailInput = document.querySelector('.newsletter-input');
    const email = emailInput.value.trim();
    
    if (!isValidEmail(email)) {
        showNotification('Masukkan alamat email yang valid', 'error');
        return;
    }
    
    // Simulate subscription - in real implementation, this would make an API call
    showLoadingState('.newsletter-btn');
    
    setTimeout(() => {
        hideLoadingState('.newsletter-btn');
        showNotification('Terima kasih! Anda telah berlangganan newsletter kami.', 'success');
        emailInput.value = '';
    }, 2000);
}


// Weather Widget Update
function updateWeatherWidget() {
    const weatherTemp = document.querySelector('.weather-temp');
    const weatherDesc = document.querySelector('.weather-desc');
    const weatherIcon = document.querySelector('.weather-icon i');
    
    // Simulate weather data - in real implementation, this would come from weather API
    const weatherData = {
        temp: Math.floor(Math.random() * 10) + 25, // 25-35°C
        desc: ['Cerah', 'Berawan', 'Hujan Ringan', 'Mendung'][Math.floor(Math.random() * 4)],
        icon: ['fa-sun', 'fa-cloud', 'fa-cloud-rain', 'fa-cloud'][Math.floor(Math.random() * 4)]
    };
    
    if (weatherTemp) weatherTemp.textContent = weatherData.temp + '°C';
    if (weatherDesc) weatherDesc.textContent = weatherData.desc;
    if (weatherIcon) {
        weatherIcon.className = 'fas ' + weatherData.icon;
    }
}

// Breaking News Ticker
function startBreakingNewsTicker() {
    const breakingNewsContent = document.querySelector('.breaking-news-content p');
    if (!breakingNewsContent) return;
    
    const newsItems = [
        'Bupati Pesisir Barat Resmikan Pembangunan Jembatan Penghubung Antar Desa',
        'Festival Budaya Pesisir Barat Sukses Digelar dengan Ribuan Pengunjung',
        'Program Beasiswa untuk Mahasiswa Berprestasi Dibuka Hingga Akhir Tahun',
        'Pembangunan Pusat Kesehatan Masyarakat Baru Dimulai Awal Tahun Depan',
        'Inovasi Teknologi Pertanian di Pesisir Barat Raih Penghargaan Nasional'
    ];
    
    let currentIndex = 0;
    
    setInterval(() => {
        currentIndex = (currentIndex + 1) % newsItems.length;
        breakingNewsContent.style.opacity = '0';
        
        setTimeout(() => {
            breakingNewsContent.textContent = newsItems[currentIndex];
            breakingNewsContent.style.opacity = '1';
        }, 300);
    }, 5000);
}

// Initialize Tooltips
function initializeTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

// Show Tooltip
function showTooltip(e) {
    const tooltipText = e.target.getAttribute('data-tooltip');
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = tooltipText;
    tooltip.style.cssText = `
        position: absolute;
        background: #333;
        color: white;
        padding: 8px 12px;
        border-radius: 4px;
        font-size: 12px;
        z-index: 1000;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.3s;
    `;
    
    document.body.appendChild(tooltip);
    
    const rect = e.target.getBoundingClientRect();
    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
    tooltip.style.top = rect.top - tooltip.offsetHeight - 8 + 'px';
    
    setTimeout(() => {
        tooltip.style.opacity = '1';
    }, 10);
    
    e.target._tooltip = tooltip;
}

// Hide Tooltip
function hideTooltip(e) {
    if (e.target._tooltip) {
        e.target._tooltip.remove();
        delete e.target._tooltip;
    }
}

// Utility Functions
function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 10000;
        transform: translateX(100%);
        transition: transform 0.3s ease;
        max-width: 300px;
        word-wrap: break-word;
    `;
    
    // Set background color based on type
    const colors = {
        success: '#27ae60',
        error: '#e74c3c',
        warning: '#f39c12',
        info: '#3498db'
    };
    notification.style.backgroundColor = colors[type] || colors.info;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 10);
    
    // Auto remove after 4 seconds
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    }, 4000);
}

function showLoadingState(selector) {
    const element = document.querySelector(selector);
    if (element) {
        element.disabled = true;
        element.innerHTML = '<span class="loading"></span> Memproses...';
    }
}

function hideLoadingState(selector) {
    const element = document.querySelector(selector);
    if (element) {
        element.disabled = false;
        element.innerHTML = element.getAttribute('data-original-text') || 'Memproses...';
    }
}

// Generate Mock News Items
function generateMockNewsItems(count) {
    const categories = ['Politik', 'Ekonomi', 'Sosial', 'Olahraga', 'Teknologi', 'Kesehatan', 'Pendidikan'];
    const titles = [
        'Pembangunan Infrastruktur Baru di Pesisir Barat',
        'Program Pemberdayaan Masyarakat Desa',
        'Festival Seni dan Budaya Lokal',
        'Peningkatan Kualitas Pendidikan',
        'Inovasi Teknologi untuk Pertanian',
        'Pelayanan Kesehatan Gratis untuk Masyarakat',
        'Pengembangan Wisata Bahari'
    ];
    
    const items = [];
    
    for (let i = 0; i < count; i++) {
        const category = categories[Math.floor(Math.random() * categories.length)];
        const title = titles[Math.floor(Math.random() * titles.length)];
        const date = new Date();
        date.setDate(date.getDate() - Math.floor(Math.random() * 30));
        const formattedDate = date.toLocaleDateString('id-ID');
        
        const article = document.createElement('article');
        article.className = 'news-item';
        article.innerHTML = `
            <div class="news-image">
                <img src="assets/images/news-${Math.floor(Math.random() * 6) + 1}.jpg" alt="Berita ${i + 1}">
                <div class="news-category">${category}</div>
            </div>
            <div class="news-content">
                <h3 class="news-title">
                    <a href="berita/berita-${i + 1}.html">${title}</a>
                </h3>
                <p class="news-excerpt">
                    Berita terbaru dari Kabupaten Pesisir Barat yang membahas perkembangan terkini 
                    dalam berbagai aspek kehidupan masyarakat.
                </p>
                <div class="news-meta">
                    <span class="news-date">${formattedDate}</span>
                    <span class="news-views">${Math.floor(Math.random() * 1000) + 100} views</span>
                </div>
            </div>
        `;
        
        items.push(article);
    }
    
    return items;
}

// Social Media Share Functions
function shareOnFacebook(url, title) {
    const shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`;
    window.open(shareUrl, '_blank', 'width=600,height=400');
}

function shareOnTwitter(url, title) {
    const shareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`;
    window.open(shareUrl, '_blank', 'width=600,height=400');
}

function shareOnWhatsApp(url, title) {
    const shareUrl = `https://wa.me/?text=${encodeURIComponent(title + ' ' + url)}`;
    window.open(shareUrl, '_blank');
}

// Print Function
function printArticle() {
    window.print();
}

// Back to Top Function
function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

// Add back to top button
window.addEventListener('scroll', function() {
    const backToTopBtn = document.querySelector('.back-to-top');
    if (window.pageYOffset > 300) {
        if (!backToTopBtn) {
            const btn = document.createElement('button');
            btn.className = 'back-to-top';
            btn.innerHTML = '<i class="fas fa-arrow-up"></i>';
            btn.onclick = scrollToTop;
            btn.style.cssText = `
                position: fixed;
                bottom: 20px;
                right: 20px;
                width: 50px;
                height: 50px;
                background: #3498db;
                color: white;
                border: none;
                border-radius: 50%;
                cursor: pointer;
                font-size: 18px;
                z-index: 1000;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                transition: all 0.3s;
            `;
            document.body.appendChild(btn);
        }
    } else {
        const backToTopBtn = document.querySelector('.back-to-top');
        if (backToTopBtn) {
            backToTopBtn.remove();
        }
    }
});

// Keyboard Navigation
document.addEventListener('keydown', function(e) {
    // ESC key to close mobile menu
    if (e.key === 'Escape') {
        const navMenu = document.querySelector('.nav-menu');
        const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
        if (navMenu && navMenu.classList.contains('active')) {
            navMenu.classList.remove('active');
            mobileMenuToggle.classList.remove('active');
        }
    }
    
    // Ctrl + K for search focus
    if (e.ctrlKey && e.key === 'k') {
        e.preventDefault();
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            searchInput.focus();
        }
    }
});

// Performance Optimization - Lazy Loading Images
function lazyLoadImages() {
    const images = document.querySelectorAll('img[data-src]');
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });
    
    images.forEach(img => imageObserver.observe(img));
}

// Initialize lazy loading when DOM is ready
document.addEventListener('DOMContentLoaded', lazyLoadImages);

// Copy to Clipboard Function
function copyToClipboard(text) {
    if (navigator.clipboard) {
        navigator.clipboard.writeText(text).then(function() {
            showNotification('Link berhasil disalin!', 'success');
        }, function(err) {
            console.error('Could not copy text: ', err);
            showNotification('Gagal menyalin link', 'error');
        });
    } else {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            showNotification('Link berhasil disalin!', 'success');
        } catch (err) {
            showNotification('Gagal menyalin link', 'error');
        }
        document.body.removeChild(textArea);
    }
}

// Service Worker Registration (for PWA features)
if ('serviceWorker' in navigator) {
    window.addEventListener('load', function() {
        navigator.serviceWorker.register('/sw.js')
            .then(function(registration) {
                console.log('ServiceWorker registration successful');
            })
            .catch(function(err) {
                console.log('ServiceWorker registration failed');
            });
    });
}
