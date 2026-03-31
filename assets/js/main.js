// assets/js/main.js

document.addEventListener('DOMContentLoaded', () => {
    
    // 1. EFFET 3D SUR LA CARTE DE RENCONTRE (TILT EFFECT)
    const card = document.querySelector('.profile-card');
    if (card) {
        card.addEventListener('mousemove', (e) => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left; 
            const y = e.clientY - rect.top;  
            
            // Calcul de la rotation
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const rotateX = ((y - centerY) / centerY) * -10; 
            const rotateY = ((x - centerX) / centerX) * 10;
            
            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
            card.style.transition = 'none';
        });

        // Quand la souris sort, la carte revient au centre
        card.addEventListener('mouseleave', () => {
            card.style.transform = `perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)`;
            card.style.transition = 'transform 0.5s ease';
        });
    }

    // 2. EFFET DE PARTICULES (RIPPLE) SUR LES BOUTONS NÉON
    const buttons = document.querySelectorAll('.btn, .btn-submit, .btn-post');
    buttons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            let ripple = document.createElement('span');
            ripple.classList.add('ripple-effect'); // On utilise une classe CSS maintenant
            
            ripple.style.left = e.offsetX + 'px';
            ripple.style.top = e.offsetY + 'px';
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => { ripple.remove(); }, 600);
        });
    });
});