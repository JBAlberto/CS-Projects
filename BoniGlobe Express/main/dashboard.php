<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.html");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en" x-data="dashboard()" x-init="init()">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Main Menu - BoniGlobe Express</title>
  <link rel="stylesheet" href="design/p1.css">
  <link rel="icon" href="design/images/turboLogo.png">
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

</head>
<body>
  <header>
    <h1>BoniGlobe Dashboard</h1>
    <div class="nav-user">
      <span x-text="username"></span>
      <img src="design/images/profile.png" alt="User">
    </div>
  </header>

  <div class="layout">
    <aside>
      <h2>Navigation</h2>
      <a href="#" class="active">Dashboard</a>
      <a href="sending.php">Send a Package</a>
      <a href="trackpackage.php">Track a Package</a>
      <a href="information.html">Information</a>
      <form method="POST" action="Backend/logout.php" onsubmit="return confirm('Log out?')">
        <button style="margin-top: 1rem; color: red; background: none; border: none; cursor: pointer;">Logout</button>
      </form>
    </aside>

    <main>
      <div class="dashboard-header">
        <h2>Welcome, <span x-text="username"></span></h2>
        <p>Today is <span x-text="new Date().toDateString()"></span></p>
      </div>
        <span class="mes">
  <h2>My Sent Packages</h2>
  
  <div x-show="loading" class="spinner"></div>

  <template x-if="!loading && packages.length === 0">
    <p>No packages found.</p>
  </template>
</span>

  <div class="card-grid" x-show="!loading">
    <template x-for="pkg in packages" :key="pkg.id">
      <div class="package-card" x-transition>
        <p><strong>Receiver:</strong> <span x-text="pkg.contact"></span></p>
        <p><strong>To:</strong> <span x-text="pkg.location_2"></span></p>
        <p><strong>Type:</strong> <span x-text="pkg.packtype"></span></p>
        <p><strong>Packaging:</strong> <span x-text="pkg.packagingType"></span></p>
        <p><strong>Status:</strong>
          <span x-text="pkg.delivered ? 'Delivered' : (pkg.approved ? 'Approved' : (pkg.rejected ? 'Rejected' : 'Pending'))"></span>
        </p>
        <button class="del"@click="deletePackage(pkg.id)">Delete</button>
      </div>
    </template>
  </div>

  </div>

  <div id="footerlogo">
    <img src="design/images/turboLogo.png" height="60" alt="BoniGlobe Logo" />
    <div class="footer-text">BoniGlobe Express</div>
  </div>

<script>
  function dashboard() {
    return {
      username: null,
      packages: [],
      loading: true,

      init() {
        // 1. Get session
        fetch('Backend/session.php')
          .then(res => res.json())
          .then(data => {
            if (data.loggedIn) {
              this.username = data.username;
              this.loadPackages(); // Load packages after verifying session
            } else {
              window.location.href = 'login.html';
            }
          })
          .catch(() => window.location.href = 'login.html');
      },

      loadPackages() {
        // 2. Fetch packages for the logged-in user
        fetch('Backend/dash.php')
          .then(res => res.json())
          .then(data => {
            this.packages = data;
            this.loading = false;
          })
          .catch(err => {
            console.error("Failed to load packages:", err);
            this.loading = false;
          });
      },

      deletePackage(id) {
        if (!confirm("Are you sure you want to delete this package?")) return;

        fetch('Backend/deletepackage.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ id: id })
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            this.packages = this.packages.filter(pkg => pkg.id !== id);
            alert("Package deleted.");
          } else {
            alert("Deletion failed: " + data.message);
          }
        })
        .catch(err => {
          console.error("Error deleting:", err);
          alert("An error occurred.");
        });
      }
    };
  }
</script>

</body>
</html>
