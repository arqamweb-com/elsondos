//Offer Countdown Timer
const offersSection = document.querySelector("#offers");

if (offersSection) {
  const hoursEl = document.getElementById("hours");
  const minutesEl = document.getElementById("minutes");
  const secondsEl = document.getElementById("seconds");

  let endDate = localStorage.getItem("offerEndDate");

  if (!endDate) {
    endDate = new Date().getTime() + 24 * 60 * 60 * 1000;
    localStorage.setItem("offerEndDate", endDate);
  } else {
    endDate = parseInt(endDate);
  }

  function pad(n) {
    return n.toString().padStart(2, "0");
  }

  function resetTimer() {
    endDate = new Date().getTime() + 24 * 60 * 60 * 1000;
    localStorage.setItem("offerEndDate", endDate);
  }

  function updateTimer() {
    const now = new Date().getTime();
    let diff = endDate - now;

    if (diff <= 0) {
      resetTimer(); // 🔥 يعيد من الأول
      diff = endDate - now;
    }

    const hours = Math.floor(diff / (1000 * 60 * 60));
    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    const seconds = Math.floor((diff % (1000 * 60)) / 1000);

    hoursEl.textContent = pad(hours);
    minutesEl.textContent = pad(minutes);
    secondsEl.textContent = pad(seconds);
  }

  setInterval(updateTimer, 1000);
  updateTimer();
}

// Why Us Section
const counters = document.querySelectorAll(".counter");

const observer = new IntersectionObserver(
  (entries) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        const el = entry.target;
        const target = parseFloat(el.dataset.target);
        const suffix = el.dataset.suffix || "";
        const isDecimal = el.dataset.decimal === "true";

        let current = 0;
        const duration = 2000;
        const steps = 60;
        const increment = target / steps;

        const timer = setInterval(() => {
          current += increment;

          if (current >= target) {
            current = target;
            clearInterval(timer);
          }

          el.textContent = isDecimal
            ? current.toFixed(1) + suffix
            : Math.floor(current).toLocaleString() + suffix;
        }, duration / steps);

        observer.unobserve(el);
      }
    });
  },
  { threshold: 0.5 },
);

counters.forEach((counter) => observer.observe(counter));

// Slider
document.addEventListener("DOMContentLoaded", () => {
  const el = document.querySelector(".blaze-slider");

  if (!el || typeof BlazeSlider === "undefined") return;

  new BlazeSlider(el, {
    all: {
      slidesToShow: 1,
      slidesToScroll: 1,
      enableAutoplay: true,
      autoplayInterval: 2000,
      loop: true,
    },
  });
});
