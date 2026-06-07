/* =========================================
   Controlador JavaScript de Login / Splash
========================================= */

let matrixInterval;

document.addEventListener('DOMContentLoaded', function() {
    // Lógica de Matrix Canvas en el Splash
    const canvas = document.getElementById('matrix-canvas');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight;
        
        const letters = '01';
        const fontSize = 16;
        const columns = canvas.width / fontSize;
        
        const drops = [];
        for (let x = 0; x < columns; x++) {
            drops[x] = 1;
        }
        
        matrixInterval = setInterval(() => {
            ctx.fillStyle = 'rgba(0, 0, 0, 0.05)';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            ctx.fillStyle = '#3b82f6';
            ctx.font = fontSize + 'px monospace';
            
            for (let i = 0; i < drops.length; i++) {
                const text = letters.charAt(Math.floor(Math.random() * letters.length));
                ctx.fillText(text, i * fontSize, drops[i] * fontSize);
                
                if (drops[i] * fontSize > canvas.height && Math.random() > 0.975) {
                    drops[i] = 0;
                }
                drops[i]++;
            }
        }, 50);

        window.addEventListener('resize', () => {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        });
    }

    // Lógica del Slideshow del fondo
    let currentSlide = 0;
    const slides = document.querySelectorAll('.slideshow-bg');
    if (slides.length > 0) {
        setInterval(() => {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
        }, 4000);
    }

    // Lógica del Matrix Financiero en el formulario
    const financeCanvas = document.getElementById('finance-matrix-canvas');
    if (financeCanvas) {
        const fCtx = financeCanvas.getContext('2d');
        
        const resizeFCanvas = () => {
            financeCanvas.width = financeCanvas.offsetWidth;
            financeCanvas.height = financeCanvas.offsetHeight;
        };
        resizeFCanvas();
        window.addEventListener('resize', resizeFCanvas);
        
        const fSymbols = ['$', '€', '£', '¥', '%', '#', '@', '&', '+', '-', '↑', '↓', '¢', '฿'];
        const fFontSize = 14;
        const fColumns = 100;
        const fDrops = [];
        for(let x = 0; x < fColumns; x++) fDrops[x] = Math.random() * 50; 

        function drawFinanceMatrix() {
            fCtx.fillStyle = 'rgba(15, 23, 42, 0.15)'; 
            fCtx.fillRect(0, 0, financeCanvas.width, financeCanvas.height);
            fCtx.fillStyle = 'rgba(59, 130, 246, 0.8)'; 
            fCtx.font = fFontSize + 'px monospace';
            
            const activeCols = Math.floor(financeCanvas.width / fFontSize) + 1;
            
            for(let i = 0; i < activeCols; i++) {
                const text = fSymbols[Math.floor(Math.random() * fSymbols.length)];
                fCtx.fillText(text, i * fFontSize, fDrops[i] * fFontSize);
                
                if(fDrops[i] * fFontSize > financeCanvas.height && Math.random() > 0.95) {
                    fDrops[i] = 0;
                }
                fDrops[i] += 0.5;
            }
        }
        setInterval(drawFinanceMatrix, 50);
    }

    // Animación secuencial de los módulos
    const moduleItems = document.querySelectorAll('.module-list-item');
    if (moduleItems.length > 0) {
        let currentModule = 0;
        setInterval(() => {
            const activeItem = moduleItems[currentModule];
            activeItem.classList.add('scale-105', 'text-white', 'font-normal', 'translate-x-1');
            activeItem.querySelector('i').classList.add('text-blue-200', 'scale-125');
            
            setTimeout(() => {
                activeItem.classList.remove('scale-105', 'text-white', 'font-normal', 'translate-x-1');
                activeItem.querySelector('i').classList.remove('text-blue-200', 'scale-125');
            }, 800);
            
            currentModule = (currentModule + 1) % moduleItems.length;
        }, 1200);
    }
});

function startLogin() {
    const splash = document.getElementById('splash-screen');
    if(splash) {
        splash.classList.add('splash-hidden');
        setTimeout(() => {
            splash.style.display = 'none';
            if (typeof matrixInterval !== 'undefined') clearInterval(matrixInterval);
            document.getElementById('email').focus();
        }, 1000);
    }
}

function togglePassword() {
    const passwordInput = document.getElementById('password');
    const icon = document.getElementById('togglePasswordIcon');
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Hacemos que estas variables y funciones sean globales si es necesario
window.matrixInterval = matrixInterval;
window.startLogin = startLogin;
window.togglePassword = togglePassword;
