document.addEventListener("DOMContentLoaded", () => {
    const flash = document.querySelector("[data-flash-message]");

    if (flash) {
        setTimeout(() => {
            flash.classList.add("opacity-0", "translate-y-2");
        }, 3200);
    }
});
