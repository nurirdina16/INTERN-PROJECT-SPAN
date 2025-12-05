<head>
  <link rel="stylesheet" href="css/header.css">
</head>

<div class="header">
  <!-- Nama pengguna -->
  <h3><?= htmlspecialchars($_SESSION['userlog']['nama']) ?></h3>

  <!-- Profile + Dropdown -->
  <div class="profile-wrapper">
      <!-- Profile Icon -->
      <div class="profile-icon" id="profileToggle">
        <i class="bi bi-person-fill"></i>
      </div>

      <!-- Dropdown -->
      <div class="profile-dropdown" id="profileDropdown">
        <a href="logout.php">Logout</a>
      </div>
  </div>
</div>

<!-- Dropdown Script -->
<script>
  document.addEventListener("DOMContentLoaded", function() {
    const toggle = document.getElementById("profileToggle");
    const dropdown = document.getElementById("profileDropdown");

    toggle.addEventListener("click", function(e) {
      dropdown.classList.toggle("show");
      e.stopPropagation(); // prevent closing immediately
    });

    // Close dropdown when clicking outside
    document.addEventListener("click", function() {
      dropdown.classList.remove("show");
    });
  });
</script>
