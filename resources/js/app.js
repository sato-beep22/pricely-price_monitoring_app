import './bootstrap';
import { gsap } from 'gsap';

import Alpine from 'alpinejs';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import ApexCharts from 'apexcharts';

window.Alpine = Alpine;
window.L = L;
window.ApexCharts = ApexCharts;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReduced) return;

    const tl = gsap.timeline();

    // 1. Generic Navbar slide down
    if (document.querySelector('.gsap-navbar')) {
        tl.fromTo('.gsap-navbar', 
            { y: -60, opacity: 0 },
            { y: 0, opacity: 1, duration: 0.9, ease: 'power3.out' }
        );
    }

    // 2. Page Header elements (for interior pages)
    if (document.querySelector('.page-header')) {
        tl.fromTo('.page-header',
            { y: 20, opacity: 0 },
            { y: 0, opacity: 1, duration: 0.8, ease: 'power3.out' },
            '-=0.5'
        );
    }

    // 3. Staggered generic elements (.animate-fade-in, .animate-slide-up, .content-card, specific hero elements)
    const staggeredElements = document.querySelectorAll('.gsap-hero-badge, .gsap-hero-title, .gsap-hero-subtitle, .gsap-hero-cta, .animate-fade-in, .animate-slide-up, .content-card, .feature-card, .interactive-card');
    
    if (staggeredElements.length > 0) {
        tl.fromTo(staggeredElements,
            { y: 35, opacity: 0 },
            { y: 0, opacity: 1, duration: 1.0, stagger: 0.1, ease: 'power4.out' },
            '-=0.5'
        );
    }

    // 4. Delegated Hover Micro-Interactions for any card
    document.body.addEventListener('mouseenter', (e) => {
        const card = e.target.closest('.feature-card, .interactive-card');
        if (!card) return;

        const icon = card.querySelector('.icon-bg');
        gsap.to(card, {
            y: -10,
            scale: 1.025,
            boxShadow: '0 25px 40px -12px rgba(16, 185, 129, 0.1), 0 8px 20px -8px rgba(0, 0, 0, 0.05)',
            borderColor: 'rgba(16, 185, 129, 0.3)',
            duration: 0.4,
            ease: 'power3.out',
            overwrite: 'auto'
        });
        
        if (icon) {
            gsap.to(icon, {
                scale: 1.15,
                rotation: 6,
                duration: 0.45,
                ease: 'elastic.out(1.2, 0.5)',
                overwrite: 'auto'
            });
        }
    }, true); // Use capture phase for mouseenter delegation

    document.body.addEventListener('mouseleave', (e) => {
        const card = e.target.closest('.feature-card, .interactive-card');
        if (!card) return;

        const icon = card.querySelector('.icon-bg');
        gsap.to(card, {
            y: 0,
            scale: 1,
            boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.03), 0 2px 4px -2px rgba(0, 0, 0, 0.03)',
            borderColor: 'rgb(241, 245, 249)',
            duration: 0.4,
            ease: 'power3.out',
            overwrite: 'auto'
        });

        if (icon) {
            gsap.to(icon, {
                scale: 1,
                rotation: 0,
                duration: 0.4,
                ease: 'power3.out',
                overwrite: 'auto'
            });
        }
    }, true); // Use capture phase for mouseleave delegation
});
