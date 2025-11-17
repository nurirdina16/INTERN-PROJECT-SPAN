// ADD BUTTON
document.addEventListener("DOMContentLoaded", () => {
  const addBtn = document.querySelector(".add-btn");
  if (addBtn) {
    addBtn.addEventListener("click", () => {
      addBtn.classList.add("clicked");
      setTimeout(() => addBtn.classList.remove("clicked"), 300);
    });
  }
});

// DELETE BUTTON
document.addEventListener("DOMContentLoaded", function() {
  const popup = document.getElementById("successPopup") || document.getElementById("deletePopup");
  if (popup) {
    setTimeout(() => {
      popup.classList.remove("show");
      popup.classList.add("fade");
    }, 3000);
    setTimeout(() => popup.remove(), 300);
  }
});

// Auto-redirect after delete success
document.addEventListener("DOMContentLoaded", function() {
  const deletePopup = document.getElementById("deletePopup");
  if (deletePopup && deletePopup.classList.contains("alert-success")) {
    setTimeout(() => {
      window.location.href = "sistemUtama.php";
    }, 300); // wait 1.5s to let user see popup
  }
});


