// Mobile Navigation Toggle with Animation
const hamburger = document.querySelector('.hamburger');
const navMenu = document.querySelector('.nav-menu');

if (hamburger && navMenu) {
    hamburger.addEventListener('click', () => {
        hamburger.classList.toggle('active');
        navMenu.classList.toggle('active');

        // Prevent body scroll when menu is open
        if (navMenu.classList.contains('active')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = 'auto';
        }
    });

    // Close mobile menu when clicking on a link
    document.querySelectorAll('.nav-link').forEach(n => n.addEventListener('click', () => {
        hamburger.classList.remove('active');
        navMenu.classList.remove('active');
        document.body.style.overflow = 'auto';
    }));

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!hamburger.contains(e.target) && !navMenu.contains(e.target)) {
            hamburger.classList.remove('active');
            navMenu.classList.remove('active');
            document.body.style.overflow = 'auto';
        }
    });
}

// Smooth scrolling for anchor links
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

// Fade in animation on scroll
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('visible');
        }
    });
}, observerOptions);

// Observe elements for fade-in animation
document.addEventListener('DOMContentLoaded', () => {
    const fadeElements = document.querySelectorAll('.feature-card, .quick-link-card, .room-card, .service-card');
    fadeElements.forEach(el => {
        el.classList.add('fade-in');
        observer.observe(el);
    });
});

// Form validation and submission
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;

    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.style.borderColor = '#e74c3c';
            isValid = false;
        } else {
            field.style.borderColor = '#e9ecef';
        }
    });

    // Email validation
    const emailFields = form.querySelectorAll('input[type="email"]');
    emailFields.forEach(field => {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (field.value && !emailRegex.test(field.value)) {
            field.style.borderColor = '#e74c3c';
            isValid = false;
        }
    });

    return isValid;
}

// Show loading state
function showLoading(button) {
    const originalText = button.innerHTML;
    button.innerHTML = '<span class="loading"></span> Processing...';
    button.disabled = true;

    // Simulate processing time
    setTimeout(() => {
        button.innerHTML = originalText;
        button.disabled = false;
    }, 2000);
}

// Booking form submission
function handleBookingSubmission(event) {
    event.preventDefault();

    if (!validateForm('booking-form')) {
        alert('Please fill in all required fields correctly.');
        return;
    }

    const submitBtn = event.target.querySelector('button[type="submit"]');
    showLoading(submitBtn);

    // Simulate booking processing
    setTimeout(() => {
        alert('Booking request submitted successfully! We will contact you shortly to confirm your reservation.');
        event.target.reset();
    }, 2000);
}

// Contact form submission
function handleContactSubmission(event) {
    event.preventDefault();

    if (!validateForm('contact-form')) {
        alert('Please fill in all required fields correctly.');
        return;
    }

    const submitBtn = event.target.querySelector('button[type="submit"]');
    showLoading(submitBtn);

    // Simulate form processing
    setTimeout(() => {
        alert('Thank you for your message! We will get back to you within 24 hours.');
        event.target.reset();
    }, 2000);
}

// Room availability checker
function checkAvailability() {
    const checkIn = document.getElementById('checkin');
    const checkOut = document.getElementById('checkout');
    const roomType = document.getElementById('room-type');

    if (checkIn && checkOut && roomType) {
        const checkInDate = new Date(checkIn.value);
        const checkOutDate = new Date(checkOut.value);

        if (checkInDate >= checkOutDate) {
            alert('Check-out date must be after check-in date.');
            return false;
        }

        // Simulate availability check
        const availableRooms = Math.floor(Math.random() * 10) + 1;
        alert(`${availableRooms} rooms available for your selected dates!`);
        return true;
    }
    return false;
}

// Initialize page-specific functionality
document.addEventListener('DOMContentLoaded', () => {
    // Initialize booking form if it exists
    const bookingForm = document.getElementById('booking-form');
    if (bookingForm) {
        bookingForm.addEventListener('submit', handleBookingSubmission);
    }

    // Initialize contact form if it exists
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', handleContactSubmission);
    }

    // Initialize availability checker if it exists
    const checkAvailabilityBtn = document.getElementById('check-availability');
    if (checkAvailabilityBtn) {
        checkAvailabilityBtn.addEventListener('click', checkAvailability);
    }

    // Initialize date pickers with minimum date as today
    const dateInputs = document.querySelectorAll('input[type="date"]');
    const today = new Date().toISOString().split('T')[0];
    dateInputs.forEach(input => {
        input.min = today;
    });
});

// Admin dashboard functionality
function toggleSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (section) {
        section.style.display = section.style.display === 'none' ? 'block' : 'none';
    }
}

// Staff login simulation
function handleStaffLogin(event) {
    event.preventDefault();

    const username = document.getElementById('username');
    const password = document.getElementById('password');

    if (!username || !password) return;

    // Simple validation (in real app, this would be server-side)
    if (username.value === 'admin' && password.value === 'admin123') {
        alert('Login successful! Redirecting to staff dashboard...');
        // In a real application, redirect to staff dashboard
        window.location.href = 'staff.html';
    } else {
        alert('Invalid credentials. Please try again.');
    }
}

// Initialize staff login if it exists
document.addEventListener('DOMContentLoaded', () => {
    const staffLoginForm = document.getElementById('staff-login');
    if (staffLoginForm) {
        staffLoginForm.addEventListener('submit', handleStaffLogin);
    }
});

// Image gallery lightbox functionality
function openLightbox(imageSrc, imageAlt) {
    const lightbox = document.createElement('div');
    lightbox.className = 'lightbox';
    lightbox.innerHTML = `
        <div class="lightbox-content">
            <span class="lightbox-close">&times;</span>
            <img src="${imageSrc}" alt="${imageAlt}">
        </div>
    `;

    document.body.appendChild(lightbox);
    document.body.style.overflow = 'hidden';

    // Close lightbox
    lightbox.addEventListener('click', (e) => {
        if (e.target === lightbox || e.target.classList.contains('lightbox-close')) {
            document.body.removeChild(lightbox);
            document.body.style.overflow = 'auto';
        }
    });
}

// Add lightbox styles
const lightboxStyles = `
    .lightbox {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
    }
    
    .lightbox-content {
        position: relative;
        max-width: 90%;
        max-height: 90%;
    }
    
    .lightbox-content img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    
    .lightbox-close {
        position: absolute;
        top: -40px;
        right: 0;
        color: white;
        font-size: 30px;
        cursor: pointer;
    }
`;

// Add lightbox styles to head
const styleSheet = document.createElement('style');
styleSheet.textContent = lightboxStyles;
document.head.appendChild(styleSheet);

// Initialize gallery lightbox
document.addEventListener('DOMContentLoaded', () => {
    const galleryImages = document.querySelectorAll('.gallery-item img');
    galleryImages.forEach(img => {
        img.addEventListener('click', () => {
            openLightbox(img.src, img.alt);
        });
        img.style.cursor = 'pointer';
    });
});

// ================= Signup: Real-time validation & Password strength =================
function setFieldError(input, message) {
    const errorId = input.getAttribute('aria-describedby');
    if (!errorId) return;
    const ids = errorId.split(' ');
    const errorEl = document.getElementById(ids.find(id => id.startsWith('error-')));
    if (errorEl) errorEl.textContent = message || '';
    input.style.borderColor = message ? '#e74c3c' : '#e9ecef';
}

function validateEmailValue(value) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(value);
}

function getPasswordStrength(password) {
    let score = 0;
    if (password.length >= 8) score++;
    if (/[A-Z]/.test(password)) score++;
    if (/[a-z]/.test(password)) score++;
    if (/[0-9]/.test(password)) score++;
    if (/[^A-Za-z0-9]/.test(password)) score++;
    return Math.min(score, 4); // 0-4 scale for 4 bars
}

function updateStrengthMeter(passwordInput) {
    const describedBy = passwordInput.getAttribute('aria-describedby') || '';
    const parts = describedBy.split(' ');
    const strengthTextId = parts.find(id => id === 'strength-text');
    const container = passwordInput.parentElement.querySelector('.strength-meter');
    if (!container) return;

    const bars = container.querySelectorAll('.strength-bars span');
    const label = strengthTextId ? document.getElementById(strengthTextId) : null;

    const strength = getPasswordStrength(passwordInput.value);
    bars.forEach((b, i) => {
        b.className = i < strength ? 'on' : '';
    });

    if (label) {
        const labels = ['very weak', 'weak', 'fair', 'good', 'strong'];
        label.textContent = `Password strength: ${labels[strength]}`;
    }
}

function attachSignupValidation() {
    const form = document.getElementById('signup-form');
    if (!form) return;

    const firstName = document.getElementById('first-name');
    const lastName = document.getElementById('last-name');
    const email = document.getElementById('signup-email');
    const phone = document.getElementById('phone');
    const password = document.getElementById('signup-password');
    const confirm = document.getElementById('confirm-password');

    function validateName(input) {
        if (!input.value.trim()) {
            setFieldError(input, 'This field is required');
            return false;
        }
        if (!new RegExp(input.pattern).test(input.value)) {
            setFieldError(input, 'Please enter a valid name');
            return false;
        }
        setFieldError(input, '');
        return true;
    }

    function validateEmail(input) {
        if (!input.value.trim()) {
            setFieldError(input, 'Email is required');
            return false;
        }
        if (!validateEmailValue(input.value)) {
            setFieldError(input, 'Please enter a valid email');
            return false;
        }
        setFieldError(input, '');
        return true;
    }

    function validatePhone(input) {
        if (!input.value) {
            setFieldError(input, '');
            return true; // optional
        }
        if (input.pattern && !new RegExp(input.pattern).test(input.value)) {
            setFieldError(input, 'Please enter a valid phone number');
            return false;
        }
        setFieldError(input, '');
        return true;
    }

    function validatePassword(input) {
        const value = input.value;
        if (!value) {
            setFieldError(input, 'Password is required');
            return false;
        }
        if (value.length < 8) {
            setFieldError(input, 'Password must be at least 8 characters');
            return false;
        }
        if (!/[A-Z]/.test(value) || !/[0-9]/.test(value)) {
            setFieldError(input, 'Include at least 1 uppercase letter and 1 number');
            return false;
        }
        setFieldError(input, '');
        return true;
    }

    function validateConfirmPassword() {
        if (!confirm.value) {
            setFieldError(confirm, 'Please confirm your password');
            return false;
        }
        if (confirm.value !== password.value) {
            setFieldError(confirm, 'Passwords do not match');
            return false;
        }
        setFieldError(confirm, '');
        return true;
    }

    // Real-time events
    firstName.addEventListener('input', () => validateName(firstName));
    lastName.addEventListener('input', () => validateName(lastName));
    email.addEventListener('input', () => validateEmail(email));
    phone.addEventListener('input', () => validatePhone(phone));
    password.addEventListener('input', () => {
        updateStrengthMeter(password);
        validatePassword(password);
        validateConfirmPassword();
    });
    confirm.addEventListener('input', validateConfirmPassword);

    // Enhance submit handling
    form.addEventListener('submit', (e) => {
        const ok = [
            validateName(firstName),
            validateName(lastName),
            validateEmail(email),
            validatePhone(phone),
            validatePassword(password),
            validateConfirmPassword()
        ].every(Boolean);
        if (!ok) {
            e.preventDefault();
            const firstError = form.querySelector('.error-text:not(:empty)');
            if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });

    // Initialize meter state
    updateStrengthMeter(password);
}

// Attach on DOM ready
document.addEventListener('DOMContentLoaded', attachSignupValidation);

// ================= Interactivity: FAQ accordion =================
document.addEventListener('DOMContentLoaded', () => {
    const faqItems = document.querySelectorAll('.faq-item h4');
    faqItems.forEach(h => {
        h.addEventListener('click', () => {
            const parent = h.parentElement;
            parent.classList.toggle('open');
        });
    });
});

// ================= Simple Slider =================
function initSlider(root) {
    const slidesWrap = root.querySelector('.slides');
    const slides = root.querySelectorAll('.slide');
    const prev = root.querySelector('[data-dir="prev"]');
    const next = root.querySelector('[data-dir="next"]');
    const dotsWrap = root.querySelector('.slider-dots');
    let index = 0;

    slides.forEach((_, i) => {
        const b = document.createElement('button');
        b.setAttribute('aria-label', `Go to slide ${i + 1}`);
        b.addEventListener('click', () => go(i));
        dotsWrap.appendChild(b);
    });

    function update() {
        slidesWrap.style.transform = `translateX(-${index * 100}%)`;
        const dots = dotsWrap.querySelectorAll('button');
        dots.forEach((d, i) => d.classList.toggle('active', i === index));
    }

    function go(i) {
        index = (i + slides.length) % slides.length;
        update();
    }

    prev.addEventListener('click', () => go(index - 1));
    next.addEventListener('click', () => go(index + 1));

    // Auto-play
    let timer = setInterval(() => go(index + 1), 5000);
    root.addEventListener('mouseenter', () => clearInterval(timer));
    root.addEventListener('mouseleave', () => timer = setInterval(() => go(index + 1), 5000));

    update();
}

document.addEventListener('DOMContentLoaded', () => {
    const slider = document.getElementById('home-slider');
    if (slider) initSlider(slider);
});

// ================= Reusable Modal =================
function openModal() {
    const overlay = document.getElementById('modal-overlay');
    if (!overlay) return;
    overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    const overlay = document.getElementById('modal-overlay');
    if (!overlay) return;
    overlay.style.display = 'none';
    document.body.style.overflow = 'auto';
}

document.addEventListener('DOMContentLoaded', () => {
    const openBtn = document.getElementById('open-offer-modal');
    const closeBtn = document.getElementById('modal-close');
    const overlay = document.getElementById('modal-overlay');

    if (openBtn) openBtn.addEventListener('click', openModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (overlay) overlay.addEventListener('click', (e) => {
        if (e.target === overlay) closeModal();
    });
});

// ================= Notification Banner =================
function showBanner(message, duration = 10000) {
    if (document.querySelector('.notify-banner')) return; // one at a time
    const banner = document.createElement('div');
    banner.className = 'notify-banner';
    banner.innerHTML = `<span>${message}</span><button class="close-banner" aria-label="Close">✕</button>`;
    document.body.appendChild(banner);

    const closer = banner.querySelector('.close-banner');
    closer.addEventListener('click', () => banner.remove());

    if (duration > 0) {
        setTimeout(() => banner.remove(), duration);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Example banner on home page only
    if (document.body.classList && document.querySelector('.hero')) {
        showBanner('Limited-time: Get 15% off Deluxe Rooms. Book today!');
    }
});

// ================= JSON utilities and pagination =================
function parseJSON(jsonString) {
    try { return JSON.parse(jsonString); } catch { return []; }
}

function paginate(items, page, perPage) {
    const total = items.length;
    const totalPages = Math.max(1, Math.ceil(total / perPage));
    const safePage = Math.min(Math.max(1, page), totalPages);
    const start = (safePage - 1) * perPage;
    const end = start + perPage;
    return { page: safePage, totalPages, slice: items.slice(start, end) };
}

function renderPagination(container, current, total, onChange) {
    container.innerHTML = '';
    const prev = document.createElement('button');
    prev.textContent = 'Prev';
    prev.disabled = current <= 1;
    prev.addEventListener('click', () => onChange(current - 1));
    container.appendChild(prev);

    for (let i = 1; i <= total; i++) {
        const b = document.createElement('button');
        b.textContent = String(i);
        if (i === current) b.classList.add('active');
        b.addEventListener('click', () => onChange(i));
        container.appendChild(b);
    }

    const next = document.createElement('button');
    next.textContent = 'Next';
    next.disabled = current >= total;
    next.addEventListener('click', () => onChange(current + 1));
    container.appendChild(next);
}

// ================= Renderers =================
function renderEventsList(rootId = 'events-list', pagerId = 'events-pagination') {
    const root = document.getElementById(rootId);
    const pager = document.getElementById(pagerId);
    if (!root || !pager) return;

    // Example JSON data (could be fetched from an API)
    const json = `[
        {"id":1,"title":"Business Summit","date":"2025-11-12","location":"Grand Ballroom","image":"https://images.unsplash.com/photo-1492684223066-81342ee5ff30?auto=format&fit=crop&w=1400&q=80","desc":"Annual business leadership summit with keynote speakers."},
        {"id":2,"title":"Wedding Expo","date":"2025-11-20","location":"Garden Pavilion","image":"https://images.unsplash.com/photo-1519167758481-83f142bb4b4a?auto=format&fit=crop&w=1400&q=80","desc":"Explore wedding vendors and décor inspirations."},
        {"id":3,"title":"Tech Conference","date":"2025-12-02","location":"Conference Center","image":"https://images.unsplash.com/photo-1518779578993-ec3579fee39f?auto=format&fit=crop&w=1400&q=80","desc":"Cutting-edge technology presentations and workshops."},
        {"id":4,"title":"Gala Night","date":"2025-12-24","location":"Grand Ballroom","image":"https://images.unsplash.com/photo-1519741497674-611481863552?auto=format&fit=crop&w=1400&q=80","desc":"Black-tie gala dinner with live entertainment."},
        {"id":5,"title":"Startup Pitch Day","date":"2026-01-08","location":"Executive Boardroom","image":"https://images.unsplash.com/photo-1551836022-d5d88e9218df?auto=format&fit=crop&w=1400&q=80","desc":"Early-stage startups pitch to investors."},
        {"id":6,"title":"Healthcare Forum","date":"2026-01-21","location":"Conference Center","image":"https://images.unsplash.com/photo-1519305129429-0eece9e796ff?auto=format&fit=crop&w=1400&q=80","desc":"Healthcare innovation and policy discussions."}
    ]`;

    const data = parseJSON(json);
    let current = 1;
    const perPage = 6;

    function render() {
        const { page, totalPages, slice } = paginate(data, current, perPage);
        root.innerHTML = slice.map(e => `
            <div class="card">
                <img src="${e.image}" alt="${e.title}">
                <div class="card-body">
                    <div class="card-title">${e.title}</div>
                    <div class="card-meta">${e.date} • ${e.location}</div>
                    <div class="card-text">${e.desc}</div>
                </div>
            </div>
        `).join('');
        renderPagination(pager, page, totalPages, (p) => { current = p; render(); });
    }

    render();
}

function renderStudentsList(rootId = 'students-list', pagerId = 'students-pagination') {
    const root = document.getElementById(rootId);
    const pager = document.getElementById(pagerId);
    if (!root || !pager) return;

    // Example student JSON
    const json = `[
        {"id":101,"name":"Aarav Sharma","dept":"Hospitality Management","year":"Final","image":"https://images.unsplash.com/photo-1502685104226-ee32379fefbe?auto=format&fit=crop&w=800&q=80"},
        {"id":102,"name":"Priya Verma","dept":"Culinary Arts","year":"Third","image":"https://images.unsplash.com/photo-1544723795-3fb6469f5b39?auto=format&fit=crop&w=800&q=80"},
        {"id":103,"name":"Rahul Mehta","dept":"Tourism Studies","year":"Second","image":"https://images.unsplash.com/photo-1541534401786-2077eed87a62?auto=format&fit=crop&w=800&q=80"},
        {"id":104,"name":"Neha Gupta","dept":"Event Management","year":"Final","image":"https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=800&q=80"},
        {"id":105,"name":"Karan Patel","dept":"Culinary Arts","year":"First","image":"https://images.unsplash.com/photo-1527980965255-d3b416303d12?auto=format&fit=crop&w=800&q=80"},
        {"id":106,"name":"Simran Kaur","dept":"Hospitality Management","year":"Second","image":"https://images.unsplash.com/photo-1508214751196-bcfd4ca60f91?auto=format&fit=crop&w=800&q=80"},
        {"id":107,"name":"Vikram Singh","dept":"Event Management","year":"Third","image":"https://images.unsplash.com/photo-1547425260-76bcadfb4f2c?auto=format&fit=crop&w=800&q=80"}
    ]`;

    const students = parseJSON(json);
    let current = 1;
    const perPage = 6;

    function render() {
        const { page, totalPages, slice } = paginate(students, current, perPage);
        root.innerHTML = slice.map(s => `
            <div class="card">
                <img src="${s.image}" alt="${s.name}">
                <div class="card-body">
                    <div class="card-title">${s.name}</div>
                    <div class="card-meta">${s.dept} • ${s.year} Year</div>
                    <div class="card-text">ID: ${s.id}</div>
                </div>
            </div>
        `).join('');
        renderPagination(pager, page, totalPages, (p) => { current = p; render(); });
    }

    render();
}

document.addEventListener('DOMContentLoaded', () => {
    renderEventsList();
    renderStudentsList();
});

// ================= Local Storage User Auth =================
function getUsersStore() {
    const raw = localStorage.getItem('hms_users');
    try { return raw ? JSON.parse(raw) : {}; } catch { return {}; }
}

function setUsersStore(store) {
    localStorage.setItem('hms_users', JSON.stringify(store));
}

function saveUser(user) {
    const store = getUsersStore();
    store[user.email] = user; // overwrite/update
    setUsersStore(store);
}

function getUserByEmail(email) {
    const store = getUsersStore();
    return store[email] || null;
}

function handleSignupLocalStorage(event) {
    event.preventDefault();
    const form = event.target;

    // reuse existing validation
    if (!validateForm('signup-form')) {
        alert('Please fill in all required fields correctly.');
        return;
    }

    const firstName = document.getElementById('first-name').value.trim();
    const lastName = document.getElementById('last-name').value.trim();
    const email = document.getElementById('signup-email').value.trim().toLowerCase();
    const phone = (document.getElementById('phone') && document.getElementById('phone').value.trim()) || '';
    const password = document.getElementById('signup-password').value;
    const confirm = document.getElementById('confirm-password').value;

    if (password !== confirm) {
        alert('Passwords do not match.');
        return;
    }

    const existing = getUserByEmail(email);
    if (existing) {
        alert('An account with this email already exists. Please login.');
        window.location.href = 'login.html';
        return;
    }

    const user = {
        name: `${firstName} ${lastName}`.trim(),
        firstName,
        lastName,
        email,
        phone,
        password
    };

    saveUser(user);

    alert('Registration successful! Please login.');
    window.location.href = 'login.html';
}

function handleLoginLocalStorage(event) {
    event.preventDefault();
    const emailEl = document.getElementById('login-email');
    const passEl = document.getElementById('login-password');
    const email = emailEl ? emailEl.value.trim().toLowerCase() : '';
    const password = passEl ? passEl.value : '';

    if (!email || !password) {
        alert('Please enter email and password.');
        return;
    }

    const user = getUserByEmail(email);
    if (!user) {
        alert('No account found with this email. Please sign up.');
        return;
    }

    if (user.password !== password) {
        alert('Invalid credentials.');
        return;
    }

    // Mark simple session
    localStorage.setItem('hms_logged_in', JSON.stringify({ email, name: user.name }));
    alert('Login successful!');
    window.location.href = 'index.html';
}

document.addEventListener('DOMContentLoaded', () => {
    // Bind signup to localStorage-based registration (override prior)
    const signupForm = document.getElementById('signup-form');
    if (signupForm) {
        const action = (signupForm.getAttribute('action') || '').toLowerCase();
        const method = (signupForm.getAttribute('method') || '').toLowerCase();
        const postsToPhp = action.endsWith('.php') && method === 'post';
        if (!postsToPhp) {
            signupForm.addEventListener('submit', handleSignupLocalStorage);
        }
    }

    // Bind login to localStorage-based auth
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        // Only bind localStorage login if the form is not posting to PHP
        const action = (loginForm.getAttribute('action') || '').toLowerCase();
        const method = (loginForm.getAttribute('method') || '').toLowerCase();
        const postsToPhp = action.endsWith('.php') && method === 'post';
        if (!postsToPhp) {
            loginForm.addEventListener('submit', handleLoginLocalStorage);
        }
    }
});

document.addEventListener('DOMContentLoaded', () => {

    // Function to fetch and display the latest 5 events for the Upcoming Events section
    function fetchUpcomingEvents() {
        fetch('events.php?action=read_latest')
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(events => {
                const eventsList = document.getElementById('events-list');
                eventsList.innerHTML = ''; // Clear previous content

                if (events.length > 0) {
                    events.forEach(event => {
                        const eventCard = document.createElement('div');
                        eventCard.className = 'card';
                        eventCard.innerHTML = `
                            <div class="card-content">
                                <h3>${event.title}</h3>
                                <p><i class="fas fa-calendar-alt"></i> Date: ${event.date}</p>
                                <p><i class="fas fa-clock"></i> Time: ${event.time}</p>
                                <p><i class="fas fa-map-marker-alt"></i> Location: ${event.location}</p>
                            </div>
                        `;
                        eventsList.appendChild(eventCard);
                    });
                } else {
                    eventsList.innerHTML = '<p>No upcoming events at the moment. Check back soon!</p>';
                }
            })
            .catch(error => {
                console.error('Error fetching upcoming events:', error);
            });
    }

    // Function to fetch and display all events for the management dashboard
    function fetchAllEvents() {
        fetch('events.php?action=read_all')
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(events => {
                const eventsTableBody = document.querySelector('#events-management-table tbody');
                eventsTableBody.innerHTML = ''; // Clear previous rows

                if (events.length > 0) {
                    events.forEach(event => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${event.id}</td>
                            <td>${event.title}</td>
                            <td>${event.date}</td>
                            <td>${event.time}</td>
                            <td>${event.location}</td>
                            <td>${event.capacity}</td>
                            <td>
                                <button class="btn btn-danger" onclick="deleteEvent(${event.id})">Delete</button>
                            </td>
                        `;
                        eventsTableBody.appendChild(row);
                    });
                } else {
                    eventsTableBody.innerHTML = '<tr><td colspan="7">No events found.</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error fetching all events:', error);
            });
    }

    // Function to handle the form submission for adding a new event
    const addEventForm = document.getElementById('add-event-form');
    if (addEventForm) {
        addEventForm.addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent default form submission

            const formData = new FormData(addEventForm);

            fetch('events.php?action=add', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Event added successfully!');
                        addEventForm.reset();
                        // Refresh both event lists after a successful addition
                        fetchUpcomingEvents();
                        fetchAllEvents();
                    } else {
                        alert('Failed to add event: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
        });
    }

    // Global function to delete an event
    window.deleteEvent = function (id) {
        if (confirm('Are you sure you want to delete this event?')) {
            fetch(`events.php?action=delete&id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert('Event deleted successfully!');
                        // Refresh both event lists after a successful deletion
                        fetchUpcomingEvents();
                        fetchAllEvents();
                    } else {
                        alert('Failed to delete event: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
        }
    };

    // Initial calls to load the events when the page loads
    fetchUpcomingEvents();
    fetchAllEvents();
});
