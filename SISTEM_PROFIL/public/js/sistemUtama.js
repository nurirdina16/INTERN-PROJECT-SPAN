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

// DELETE POPUP HANDLER
document.addEventListener("DOMContentLoaded", function () {
  const deletePopup = document.getElementById("deletePopup");

  if (deletePopup) {
    // Stay for 3 seconds
    setTimeout(() => {
      deletePopup.classList.add("fade");
      deletePopup.classList.remove("show");
    }, 3000);

    // Fully remove after fade animation
    setTimeout(() => deletePopup.remove(), 3500);
  }
});



