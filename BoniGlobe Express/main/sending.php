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
  <div class="map-background">
    <iframe 
      src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d6604.5496882403095!2d120.55946091512126!3d18.05684378012015!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMTjCsDAzJzI5LjEiTiAxMjDCsDMzJzM0LjUiRQ!5e0!3m2!1sen!2sph!4v1748432900417!5m2!1sen!2sph"
      width="100%"
      height="100%"
      style="border:0;"
      allowfullscreen=""
      loading="lazy"
      referrerpolicy="no-referrer-when-downgrade">
    </iframe>
  </div>
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
      <a href="sending.php" class="active">Send a Package</a>
      <a href="trackpackage.php">Track a Package</a>
      <a href="information.html">Information</a>
      <form method="POST" action="Backend/logout.php" onsubmit="return confirm('Log out?')">
        <button style="margin-top: 1rem; color: red; background: none; border: none; cursor: pointer;">Logout</button>
      </form>
    </aside>

  <main style="display: flex; justify-content: center; padding: 30px;">
    <div class="calculator">
      <h2 style="text-align: center;">Send a Package</h2>
      <form @submit.prevent="submitInfo">
        <label>Name:</label>
        <input type="text" x-model="form.name" required placeholder="Sender's Name"/>

        <label>Receiver's Name:</label>
        <input type="text" x-model="form.contact" required placeholder="Receiver's Full Name"/>

        <label>Receiver's Contact Number:</label>
        <input type="tel" x-model="form.number" pattern="^09\d{9}$" minlength="11" maxlength="11" placeholder="e.g. 09691234567" required/>

        <label>Sender's Address:</label>
        <input type="text" x-model="form.location_1" required placeholder="Add more details if necessary"/>

        <label>Receiver's Address:</label>
        <input type="text" x-model="form.location_2" required placeholder="Add more details if necessary"/>

        <label>Package Type:</label>
        <label><input type="radio" name="packtype" value="Fragile" x-model="form.packtype" /> Fragile</label>
        <label><input type="radio" name="packtype" value="Normal" x-model="form.packtype" /> Normal</label>

        <p>Choose Packaging:</p>
        <label><input type="radio" name="packaging" value="Primary" x-model="form.packagingType" /> Primary</label><br/>
        <label><input type="radio" name="packaging" value="Secondary" x-model="form.packagingType" /> Secondary</label><br/>
        <label><input type="radio" name="packaging" value="Tertiary" x-model="form.packagingType" /> Tertiary</label><br/>

        <button type="submit" class="facebook" style="margin-top: 10px;" :disabled="loading">
          <span x-show="!loading">Submit</span>
          <span x-show="loading">Submitting...</span>
        </button>
      </form>

      <template x-if="message">
        <p class="result" x-text="message"></p>
      </template>
    </div>
  </main>


  </div>

  <div id="footerlogo">
    <img src="design/images/turboLogo.png" height="60" alt="BoniGlobe Logo" />
    <div class="footer-text">BoniGlobe Express</div>
  </div>

<script>
  function dashboard() {
    return {
      // User display and session handling
      username: null,
      // Form handling
      loading: false,
      message: '',
      form: {
        user_id: '',
        name: '',
        contact: '',
        number: '',
        location_1: '',
        location_2: '',
        packtype: 'Normal',
        packagingType: ''
      },
      init() {
        // Fetch session info
        fetch('Backend/session.php')
          .then(res => res.json())
          .then(data => {
            if (data.loggedIn) {
              this.username = data.username;
              this.form.user_id = data.user_id; // Populate user_id from session
            } else {
              window.location.href = 'login.html';
            }
          })
          .catch(() => window.location.href = 'login.html');
      },
      submitInfo() {
        this.loading = true;
        this.message = '';

        fetch('Backend/sending.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(this.form)
        })
        .then(res => res.json())
        .then(data => {
          this.message = data.message || 'Submitted successfully!';
        })
        .catch(error => {
          console.error(error);
          this.message = 'Submission failed.';
        })
        .finally(() => {
          this.loading = false;
        });
      }
    };
  }
</script>

</body>
</html>