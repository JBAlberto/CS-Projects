<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.html");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en" x-data="tracker()" x-init="init()">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Main Menu - BoniGlobe Express</title>
  <link rel="stylesheet" href="design/p1.css">
  <link rel="icon" href="design/images/turboLogo.png">
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <style>
    .map-background {
      position: fixed;
      top: 0;
      left: 0;
      width: 100vw;
      height: 100vh;
      z-index: -1;
      pointer-events: none;
    }
  </style>
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
      <a href="dashboard.php">Dashboard</a>
      <a href="sending.php">Send a Package</a>
      <a href="trackpackage.php" class="active">Track a Package</a>
      <a href="information.html">Information</a>
      <form method="POST" action="Backend/logout.php" onsubmit="return confirm('Log out?')">
        <button style="margin-top: 1rem; color: red; background: none; border: none; cursor: pointer;">Logout</button>
      </form>
    </aside>

    <main>
      <div class="map-background">
        <iframe 
          src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d6604.5496882403095!2d120.55946091512126!3d18.05684378012015!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMTjCsDAzJzI5LjEiTiAxMjDCsDMzJzM0LjUiRQ!5e0!3m2!1sen!2sph!4v1748432900417!5m2!1sen!2sph"
          width="100%" height="100%" style="border:0;" allowfullscreen loading="lazy"
          referrerpolicy="no-referrer-when-downgrade">
        </iframe>
      </div>
      <div class="calculator" style="max-width: 600px; margin: auto;">
        <h2 style="text-align: center;">Track a Package</h2>

        <div style="margin-bottom: 20px;">
          <input type="text" x-model="trackingNumber" placeholder="Enter tracking number" class="input" style="width: 100%;" />
          <button class="submit-btn" :disabled="!trackingNumber" @click="track()" style="margin-top: 10px;">Check Status</button>
        </div>

        <template x-if="error">
          <p style="color:red;" x-text="error"></p>
        </template>

        <template x-if="packageFound">
          <div class="tracked">
            <p>Status:
              <strong x-text="approved ? 'On the Way' : (pending ? 'Pending Request' : (rejected ? 'Invalid Request' : 'Package Delivered'))"></strong>
            </p>

            <template x-if="approved"><p style="color:green;">Your package is on the way to reciever.</p></template>
            <template x-if="pending"><p style="color:orange;">A Rider will pick up your Package.</p></template>
            <template x-if="rejected"><p style="color:red;">Invalid Package request (Package rejected).</p></template>
            <template x-if="delivered"><p style="color:rgb(83, 161, 197);">Package Delivered.</p></template>

            <h3>Package Summary</h3>
            <p><strong>Tracking Number:</strong> <span x-text="packageInfo.tracking_number"></span></p>
            <p><strong>Name:</strong> <span x-text="packageInfo.name"></span></p>
            <p><strong>From:</strong> <span x-text="packageInfo.location_1"></span></p>
            <p><strong>To:</strong> <span x-text="packageInfo.location_2"></span></p>
            <p><strong>Package Type:</strong> <span x-text="packageInfo.packtype"></span></p>
          </div>
        </template>
      </div>
    </main>
  </div>

  <div id="footerlogo">
    <img src="design/images/turboLogo.png" height="60" alt="BoniGlobe Logo" />
    <div class="footer-text">BoniGlobe Express</div>
  </div>

<script>
  function tracker() {
    return {
      username: null,
      trackingNumber: '',
      error: '',
      packageFound: false,
      approved: false,
      rejected: false,
      pending: false,
      delivered: false,
      packageInfo: {},

      init() {
        fetch('Backend/session.php')
          .then(res => res.json())
          .then(data => {
            if (data.loggedIn) {
              this.username = data.username;
            } else {
              window.location.href = 'login.html';
            }
          })
          .catch(() => window.location.href = 'login.html');
      },

      track() {
        this.error = '';
        this.packageFound = false;
        this.approved = false;
        this.rejected = false;
        this.pending = false;
        this.delivered = false;

        if (!this.trackingNumber.trim()) {
          this.error = 'Please enter a tracking number.';
          return;
        }

        fetch(`Backend/track-package.php?tracking_number=${encodeURIComponent(this.trackingNumber.trim())}`, {
          method: 'GET',
          credentials: 'include'
        })
        .then(res => {
          if (res.status === 401) throw new Error('Unauthorized. Please log in.');
          return res.json();
        })
        .then(data => {
          if (data.error) {
            this.error = data.error;
            return;
          }

          this.packageInfo = data;
          this.packageFound = true;

          if (data.rejected === 1) this.rejected = true;
          else if (data.approved === 1) this.approved = true;
          else if (data.delivered === 1) this.delivered = true;
          else this.pending = true;
        })
        .catch(err => {
          this.error = err.message || 'Failed to fetch package info.';
        });
      }
    }
  }
</script>
</body>
</html>
