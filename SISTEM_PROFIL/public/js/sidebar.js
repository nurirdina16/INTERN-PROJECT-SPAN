// public/js/sidebar.js
document.addEventListener("DOMContentLoaded", function () {
  const dropdownBtns = document.querySelectorAll(".dropdown-btn");

  dropdownBtns.forEach(btn => {
    btn.addEventListener("click", function () {
      // toggle active class on button
      this.classList.toggle("active");

      // toggle the next sibling dropdown-container
      const container = this.nextElementSibling;
      if (!container) return;

      if (container.style.display === "block") {
        container.style.display = "none";
      } else {
        container.style.display = "block";
      }
    });
  });

  // Optional: close dropdowns when clicking outside sidebar
  document.addEventListener("click", function (e) {
    const sidebar = document.querySelector(".sidebar");
    if (!sidebar) return;
    if (!sidebar.contains(e.target)) {
      document.querySelectorAll(".dropdown-container").forEach(c => c.style.display = "none");
      document.querySelectorAll(".dropdown-btn").forEach(b => b.classList.remove("active"));
    }
  });
});
