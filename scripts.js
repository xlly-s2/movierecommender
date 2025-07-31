document.addEventListener('DOMContentLoaded', function() {
    // Smooth scroll for cast scroller
    const castScrollers = document.querySelectorAll('.cast-scroller');
    castScrollers.forEach(scroller => {
        let isDown = false;
        let startX;
        let scrollLeft;
        
        scroller.addEventListener('mousedown', (e) => {
            isDown = true;
            startX = e.pageX - scroller.offsetLeft;
            scrollLeft = scroller.scrollLeft;
            scroller.style.cursor = 'grabbing';
        });
        
        scroller.addEventListener('mouseleave', () => {
            isDown = false;
            scroller.style.cursor = 'grab';
        });
        
        scroller.addEventListener('mouseup', () => {
            isDown = false;
            scroller.style.cursor = 'grab';
        });
        
        scroller.addEventListener('mousemove', (e) => {
            if(!isDown) return;
            e.preventDefault();
            const x = e.pageX - scroller.offsetLeft;
            const walk = (x - startX) * 2;
            scroller.scrollLeft = scrollLeft - walk;
        });
    });
    
    // Filter form submission with animation
    const filterForm = document.getElementById('filter-form');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Add loading animation
            const container = document.querySelector('.container');
            container.style.opacity = '0.5';
            container.style.transition = 'opacity 0.3s ease';
            
            // Small delay to show the transition
            setTimeout(() => {
                this.submit();
            }, 300);
        });
    }
    
    // Lazy loading for images
    const lazyLoadImages = () => {
        const images = document.querySelectorAll('img[data-src]');
        const options = {
            root: null,
            rootMargin: '0px',
            threshold: 0.1
        };
        
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src;
                    img.removeAttribute('data-src');
                    observer.unobserve(img);
                    
                    // Add fade-in effect when image loads
                    img.onload = () => {
                        img.style.opacity = '0';
                        img.style.transition = 'opacity 0.5s ease';
                        setTimeout(() => {
                            img.style.opacity = '1';
                        }, 50);
                    };
                }
            });
        }, options);
        
        images.forEach(img => imageObserver.observe(img));
    };
    
    lazyLoadImages();
    
    // Add smooth scroll to top when navigating
    window.addEventListener('beforeunload', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
});