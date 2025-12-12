// Theme Toggle
const themeToggle = document.getElementById('theme-toggle');
const html = document.documentElement;

// Check for saved theme preference or default to system preference
const savedTheme = localStorage.getItem('theme');
const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

if (savedTheme === 'dark' || (!savedTheme && systemPrefersDark)) {
    html.classList.add('dark');
}

themeToggle.addEventListener('click', () => {
    html.classList.toggle('dark');
    localStorage.setItem('theme', html.classList.contains('dark') ? 'dark' : 'light');
});

// Mobile Menu Toggle
const mobileMenuBtn = document.getElementById('mobile-menu-btn');
const sidebar = document.getElementById('sidebar');
const sidebarOverlay = document.getElementById('sidebar-overlay');

function toggleMobileMenu() {
    sidebar.classList.toggle('-translate-x-full');
    sidebarOverlay.classList.toggle('hidden');
    document.body.classList.toggle('overflow-hidden');
}

mobileMenuBtn.addEventListener('click', toggleMobileMenu);
sidebarOverlay.addEventListener('click', toggleMobileMenu);

// Close mobile menu when clicking a link
document.querySelectorAll('.sidebar-link').forEach(link => {
    link.addEventListener('click', () => {
        if (window.innerWidth < 1024) {
            toggleMobileMenu();
        }
    });
});

// Active Section Highlighting
const sections = document.querySelectorAll('section[id]');
const sidebarLinks = document.querySelectorAll('.sidebar-link');

function highlightActiveSection() {
    const scrollY = window.scrollY;
    
    sections.forEach(section => {
        const sectionTop = section.offsetTop - 100;
        const sectionHeight = section.offsetHeight;
        const sectionId = section.getAttribute('id');
        
        if (scrollY >= sectionTop && scrollY < sectionTop + sectionHeight) {
            sidebarLinks.forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${sectionId}`) {
                    link.classList.add('active');
                }
            });
        }
    });
}

window.addEventListener('scroll', highlightActiveSection);
highlightActiveSection();

// Search Functionality
const searchInput = document.getElementById('search-input');
const endpointCards = document.querySelectorAll('.endpoint-card');

searchInput.addEventListener('input', (e) => {
    const query = e.target.value.toLowerCase();
    
    sections.forEach(section => {
        const text = section.textContent.toLowerCase();
        const card = section.querySelector('.endpoint-card');
        
        if (card) {
            if (text.includes(query) || query === '') {
                section.style.display = 'block';
            } else {
                section.style.display = 'none';
            }
        }
    });
    
    // Also filter sidebar links
    sidebarLinks.forEach(link => {
        const text = link.textContent.toLowerCase();
        if (text.includes(query) || query === '') {
            link.style.display = 'flex';
        } else {
            link.style.display = 'none';
        }
    });
});

// Smooth Scroll for Anchor Links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            const offset = 80; // Account for fixed header
            const targetPosition = target.offsetTop - offset;
            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        }
    });
});

// Copy Code Functionality
document.querySelectorAll('pre code').forEach(block => {
    const wrapper = block.parentElement;
    wrapper.style.position = 'relative';
    
    const copyBtn = document.createElement('button');
    copyBtn.className = 'copy-btn absolute top-2 right-2 px-3 py-1 bg-gray-700 hover:bg-primary-600 text-white text-xs rounded transition-colors';
    copyBtn.innerHTML = '<i class="fas fa-copy"></i> Copy';
    
    copyBtn.addEventListener('click', async () => {
        try {
            await navigator.clipboard.writeText(block.textContent);
            copyBtn.innerHTML = '<i class="fas fa-check"></i> Copied!';
            setTimeout(() => {
                copyBtn.innerHTML = '<i class="fas fa-copy"></i> Copy';
            }, 2000);
        } catch (err) {
            console.error('Failed to copy:', err);
        }
    });
    
    wrapper.appendChild(copyBtn);
});

// Intersection Observer for Fade-in Animation
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('fade-in');
        }
    });
}, observerOptions);

sections.forEach(section => {
    observer.observe(section);
});

// Keyboard Navigation
document.addEventListener('keydown', (e) => {
    // Press '/' to focus search
    if (e.key === '/' && document.activeElement !== searchInput) {
        e.preventDefault();
        searchInput.focus();
    }
    
    // Press 'Escape' to close mobile menu
    if (e.key === 'Escape' && !sidebar.classList.contains('-translate-x-full')) {
        toggleMobileMenu();
    }
});

console.log('JCS API Documentation loaded successfully!');
