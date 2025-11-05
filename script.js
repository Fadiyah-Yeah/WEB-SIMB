// Theme Management
function initializeTheme() {
    const savedTheme = localStorage.getItem('theme') || 'day';
    setTheme(savedTheme);
}

function toggleTheme() {
    console.log('Toggle theme clicked!'); // Debug log
    const currentTheme = document.documentElement.getAttribute('data-theme') || 'day';
    const newTheme = currentTheme === 'day' ? 'night' : 'day';
    console.log('Current theme:', currentTheme, 'New theme:', newTheme); // Debug log
    setTheme(newTheme);
}

function setTheme(theme) {
    console.log('Setting theme to:', theme); // Debug log
    document.documentElement.setAttribute('data-theme', theme);
    localStorage.setItem('theme', theme);
    
    // Update UI elements based on theme
    updateThemeUI(theme);
}

function updateThemeUI(theme) {
    // Update theme label
    const themeLabel = document.querySelector('.theme-label');
    if (themeLabel) {
        themeLabel.textContent = theme === 'day' ? 'Day Mode' : 'Night Mode';
        themeLabel.style.color = theme === 'day' ? '#333' : '#fff';
    }
    
    // Update toggle button visual state
    const toggle = document.querySelector('.theme-toggle');
    if (toggle) {
        if (theme === 'night') {
            toggle.style.background = 'hsl(0 0% 20%)';
            toggle.style.borderColor = 'hsl(0 100% 50% / 0.5)';
        } else {
            toggle.style.background = 'hsl(0 0% 60%)';
            toggle.style.borderColor = 'hsl(0 0% 75%)';
        }
    }
    
    console.log('Theme updated to:', theme); // Debug log
}

// Fungsi untuk scroll ke section dengan offset navbar
function scrollToSection(href) {
    if (href === '#beranda') {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    } else {
        const element = document.querySelector(href);
        if (element) {
            const offset = 80; // Height navbar
            const elementPosition = element.getBoundingClientRect().top;
            const offsetPosition = elementPosition + window.pageYOffset - offset;

            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });
        }
    }
}

// Update navigation dots berdasarkan scroll position
function updateNavDots() {
    const sections = document.querySelectorAll('section');
    const navDots = document.querySelectorAll('.nav-dot');
    
    let currentSection = '';
    
    sections.forEach(section => {
        const sectionTop = section.offsetTop - 100;
        const sectionHeight = section.clientHeight;
        if (window.scrollY >= sectionTop && window.scrollY < sectionTop + sectionHeight) {
            currentSection = section.id;
        }
    });
    
    navDots.forEach(dot => {
        dot.classList.remove('active');
        if (dot.getAttribute('onclick').includes(currentSection)) {
            dot.classList.add('active');
        }
    });
}

// Efek scroll pada navbar
window.addEventListener('scroll', function() {
    const navbar = document.getElementById('navbar');
    if (window.scrollY > 20) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
    
    updateNavDots();
});

// Inisialisasi saat halaman dimuat
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - initializing theme...'); // Debug log
    
    // Initialize theme
    initializeTheme();
    
    const navbar = document.getElementById('navbar');
    if (window.scrollY > 20) {
        navbar.classList.add('scrolled');
    }

    // Update tahun otomatis
    document.getElementById('currentYear').textContent = new Date().getFullYear();

    // Smooth scroll untuk anchor links di footer
    document.querySelectorAll('footer a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            scrollToSection(this.getAttribute('href'));
        });
    });

    // Buat navigation dots
    createNavDots();
    
    // Initial update nav dots
    updateNavDots();
    
    // Add event listener untuk theme toggle
    const themeToggle = document.querySelector('.theme-toggle');
    if (themeToggle) {
        console.log('Theme toggle element found:', themeToggle); // Debug log
        themeToggle.addEventListener('click', toggleTheme);
        
        // Juga tambahkan event listener untuk container jika perlu
        const themeContainer = document.querySelector('.theme-toggle-container');
        if (themeContainer) {
            themeContainer.addEventListener('click', function(e) {
                e.stopPropagation();
                toggleTheme();
            });
        }
    } else {
        console.log('Theme toggle element NOT found!'); // Debug log
    }
    
    // Auto-detect system theme preference (optional)
    autoDetectTheme();
});

// Buat navigation dots untuk setiap section
function createNavDots() {
    const sections = document.querySelectorAll('section');
    const sectionNav = document.createElement('div');
    sectionNav.className = 'section-nav';
    
    sections.forEach(section => {
        if (section.id !== '') {
            const dot = document.createElement('div');
            dot.className = 'nav-dot';
            dot.setAttribute('data-section', section.id.replace('-', ' '));
            dot.setAttribute('onclick', `scrollToSection('#${section.id}')`);
            sectionNav.appendChild(dot);
        }
    });
    
    document.body.appendChild(sectionNav);
}

// Keyboard navigation
document.addEventListener('keydown', function(e) {
    if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
        e.preventDefault();
        const sections = Array.from(document.querySelectorAll('section'));
        const currentScroll = window.scrollY + 100; // Offset untuk navbar
        
        let currentIndex = -1;
        sections.forEach((section, index) => {
            if (section.offsetTop <= currentScroll && section.offsetTop + section.clientHeight > currentScroll) {
                currentIndex = index;
            }
        });
        
        if (e.key === 'ArrowDown' && currentIndex < sections.length - 1) {
            scrollToSection(`#${sections[currentIndex + 1].id}`);
        } else if (e.key === 'ArrowUp' && currentIndex > 0) {
            scrollToSection(`#${sections[currentIndex - 1].id}`);
        }
    }
    
    // Toggle theme dengan shortcut 't'
    if (e.key === 't' || e.key === 'T') {
        toggleTheme();
    }
});

// Touch swipe support untuk mobile
let touchStartY = 0;
let touchEndY = 0;

document.addEventListener('touchstart', e => {
    touchStartY = e.changedTouches[0].screenY;
});

document.addEventListener('touchend', e => {
    touchEndY = e.changedTouches[0].screenY;
    handleSwipe();
});

function handleSwipe() {
    const swipeThreshold = 50;
    
    if (touchStartY - touchEndY > swipeThreshold) {
        // Swipe up - next section
        const sections = Array.from(document.querySelectorAll('section'));
        const currentScroll = window.scrollY + 100;
        
        let currentIndex = -1;
        sections.forEach((section, index) => {
            if (section.offsetTop <= currentScroll && section.offsetTop + section.clientHeight > currentScroll) {
                currentIndex = index;
            }
        });
        
        if (currentIndex < sections.length - 1) {
            scrollToSection(`#${sections[currentIndex + 1].id}`);
        }
    }
    
    if (touchEndY - touchStartY > swipeThreshold) {
        // Swipe down - previous section
        const sections = Array.from(document.querySelectorAll('section'));
        const currentScroll = window.scrollY + 100;
        
        let currentIndex = -1;
        sections.forEach((section, index) => {
            if (section.offsetTop <= currentScroll && section.offsetTop + section.clientHeight > currentScroll) {
                currentIndex = index;
            }
        });
        
        if (currentIndex > 0) {
            scrollToSection(`#${sections[currentIndex - 1].id}`);
        }
    }
}

// Auto theme based on system preference
function autoDetectTheme() {
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        // Only set to night if user hasn't manually chosen a theme
        if (!localStorage.getItem('theme')) {
            setTheme('night');
        }
    }
}

// Listen for system theme changes
if (window.matchMedia) {
    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
        if (!localStorage.getItem('theme')) { // Only auto-detect if user hasn't manually set theme
            autoDetectTheme();
        }
    });
}

// Contact Form Functions
function showContactForm() {
    const modal = document.getElementById('contactModal');
    if (modal) {
        modal.style.display = 'block';
        document.addEventListener('keydown', handleEscapeKey);
    }
}

function closeContactForm() {
    const modal = document.getElementById('contactModal');
    if (modal) {
        modal.style.display = 'none';
        document.removeEventListener('keydown', handleEscapeKey);
    }
}

function handleEscapeKey(event) {
    if (event.key === 'Escape') {
        closeContactForm();
    }
}

// Close modal when clicking outside
window.addEventListener('click', function(event) {
    const modal = document.getElementById('contactModal');
    if (event.target === modal) {
        closeContactForm();
    }
});

// Contact form submission
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form data
            const formData = new FormData(this);
            const name = formData.get('name');
            const email = formData.get('email');
            const message = formData.get('message');
            
            // Simulate form submission
            console.log('Form submitted:', { name, email, message });
            
            // Show success message
            alert('Terima kasih! Pesan Anda telah dikirim. Kami akan menghubungi Anda segera.');
            
            // Close modal and reset form
            closeContactForm();
            this.reset();
        });
    }
});