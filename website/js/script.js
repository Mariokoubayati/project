
document.addEventListener("DOMContentLoaded", () => {
  const slider = document.getElementById("teamSlider");
  const nextBtn = document.getElementById("nextBtn");
  const prevBtn = document.getElementById("prevBtn");

  // Keep your original DOM nodes so we can rebuild on-the-fly
  const originals = Array.from(slider.children);
  let cards, visible, index, cardWidth;

  function getVisibleCount() {
    // 1 card if under 768px, otherwise 3 cards
    return window.innerWidth < 768 ? 1 : 3;
  }

  function buildCarousel() {
    // 1) clear slider
    slider.innerHTML = "";

    // 2) determine how many should show
    visible = getVisibleCount();
    //   +1 so that clones give us a buffer beyond the first real card
    index   = visible + 1;

    // 3) re-append originals
    originals.forEach(el => slider.appendChild(el.cloneNode(true)));

    // 4) collect & clone buffer
    cards = Array.from(slider.children);
    const prepend = cards.slice(-visible - 1).map(el => el.cloneNode(true));
    const append  = cards.slice(0, visible + 1).map(el => el.cloneNode(true));
    prepend.forEach(el => slider.insertBefore(el, slider.firstChild));
    append.forEach(el => slider.appendChild(el));

    // 5) final list & initial position
    cards = Array.from(slider.children);
    updateLayout();
  }

  function updateLayout() {
    cardWidth = cards[0].offsetWidth;
    slider.style.transition = "none";
    slider.style.transform  = `translateX(-${index * cardWidth}px)`;
  }

  function slide(dir) {
    index += dir;
    slider.style.transition = "transform 0.5s ease";
    slider.style.transform  = `translateX(-${index * cardWidth}px)`;

    slider.addEventListener("transitionend", () => {
      const total     = cards.length,
            realStart = visible + 1,
            realEnd   = total - (visible + 1);

      // wrapped past the end → jump back to realStart
      if (index >= realEnd) {
        index = realStart;
        slider.style.transition = "none";
        slider.style.transform  = `translateX(-${index * cardWidth}px)`;
      }
      // wrapped before the start → jump to realEnd-1
      else if (index < realStart) {
        index = realEnd - 1;
        slider.style.transition = "none";
        slider.style.transform  = `translateX(-${index * cardWidth}px)`;
      }
    }, { once: true });
  }

  // hook up controls
  nextBtn.addEventListener("click", () => slide(1));
  prevBtn.addEventListener("click", () => slide(-1));

  // rebuild on resize so mobile/desktop switch works
  window.addEventListener("resize", buildCarousel);

  // initial build
  buildCarousel();
});

