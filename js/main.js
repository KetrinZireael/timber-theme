document.addEventListener('DOMContentLoaded', () => {
    // Handle header removal (remove second header if exists)
    const headers = document.querySelectorAll('.header');
    if (headers.length > 1) headers[1].remove();

    // Handle footer removal (remove first footer if exists)
    const footers = document.querySelectorAll('.footer');
    if (footers.length > 0) footers[0].remove();

    // Initialize slider
    const wrapper = document.querySelector('.slider-wrapper');
    const slides = document.querySelectorAll('.slide');
    const paginationContainer = document.querySelector('.slider-pagination');

    const rowsPerSlide = 4;
    const totalRows = Math.ceil(slides.length / 3); // 3 items per row
    const totalSlides = Math.ceil(totalRows / rowsPerSlide);
    let currentSlide = 0;

    // Create pagination dots dynamically and set click handlers
    const createPaginationDots = () => {
        for (let i = 0; i < totalSlides; i++) {
            const dot = document.createElement('div');
            dot.classList.add('pagination-dot');
            if (i === 0) dot.classList.add('active');

            // Add event listener to each dot
            dot.addEventListener('click', () => {
                currentSlide = i;
                updateSliderPosition();
            });

            paginationContainer.appendChild(dot);
        }
    };

    // Update the active pagination dot
    const updatePagination = () => {
        document.querySelectorAll('.pagination-dot').forEach((dot, index) => {
            dot.classList.toggle('active', index === currentSlide);
        });
    };

    // Update the slider's vertical position based on current slide
    const updateSliderPosition = () => {
        const translateY = currentSlide * rowsPerSlide * 150;
        wrapper.style.transform = `translateY(-${translateY}px)`;
        updatePagination();
    };

    createPaginationDots(); // Initialize pagination dots
});
