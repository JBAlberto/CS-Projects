<?php
session_start();
if (!isset($_SESSION['user_id'])) {
  header("Location: login.html");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en" x-data="packageViewer()" x-init="fetchPackages()">
<head>
  <meta charset="UTF-8" />
  <title>Admin Panel - Package Management</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="design/admin.css">
  <link rel="icon" href="design/images/turboLogo.png">
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <link rel="stylesheet" href="design/homedesign.css">
</head>
<body>

<!-- Admin Header -->
<header class="admin-header">
  <div>
    <img src="design/images/turboLogo.png" alt="Logo">
    <span style="font-size: 1.5rem; margin-left: 10px;">BoniGlobe Admin Panel</span>
  </div>
  <form method="POST" action="Backend/logout.php" class="logout-form"onsubmit="return confirm('Log out?')">
    <button type="submit">Logout</button>
  </form>
</header>

<!-- Main Admin Content -->
<main class="dashboard-container">
  <h2>All Sent Packages</h2>

  <template x-if="loading">
    <p>Loading packages...</p>
  </template>

  <template x-if="!loading && packages.length === 0">
    <p>No packages found.</p>
  </template>

  <template x-if="packages.length > 0">
    <div style="overflow-x:auto;">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Sender ID</th>
            <th>Receiver</th>
            <th>Contact</th>
            <th>From</th>
            <th>To</th>
            <th>Type</th>
            <th>Packaging</th>
            <th>Created</th>
            <th>Tracking #</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <template x-for="pkg in packages" :key="pkg.id">
            <tr>
              <td x-text="pkg.id"></td>
              <td x-text="pkg.sender_id"></td>
              <td x-text="pkg.contact"></td>
              <td x-text="pkg.number"></td>
              <td x-text="pkg.location_1"></td>
              <td x-text="pkg.location_2"></td>
              <td x-text="pkg.packtype"></td>
              <td x-text="pkg.packagingType"></td>
              <td x-text="pkg.created_at"></td>
              <td x-text="pkg.tracking_number"></td>
              <td>
                <template x-if="pkg.approved">
                  <div>
                    <p class="status-approved">Approved</p>
                    <template x-if="!pkg.delivered">
                      <button class="action btn-confirm" @click="confirmDelivery(pkg.id)">Confirm Delivery</button>
                    </template>
                  </div>
                </template>

                <template x-if="pkg.rejected">
                  <p class="status-rejected">Rejected</p>
                </template>

                <template x-if="!pkg.approved && !pkg.rejected && !pkg.delivered">
                  <div>
                    <button class="action btn-approve" @click="approvePackage(pkg.id)">Approve</button>
                    <button class="action btn-reject" @click="rejectPackage(pkg.id)">Reject</button>
                  </div>
                </template>

                <button class="action btn-delete" @click="deletePackage(pkg.id)">Delete</button>
              </td>
            </tr>
          </template>
        </tbody>
      </table>
    </div>
  </template>
</main>

<!-- AlpineJS Logic -->
<script>
function packageViewer() {
  return {
    packages: [],
    loading: true,
    fetchPackages() {
      fetch('Backend/fetchpackage.php')
        .then(res => res.json())
        .then(data => {
          this.packages = data;
          this.loading = false;
        })
        .catch(error => {
          console.error("Error fetching packages:", error);
          this.loading = false;
        });
        
    },
    approvePackage(id) {
      fetch('Backend/adminaproval.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) this.fetchPackages();
        else alert("Approval failed: " + data.message);
      });
    },
    rejectPackage(id) {
      fetch('Backend/adminreject.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) this.fetchPackages();
        else alert("Rejection failed: " + data.message);
      });
    },
    confirmDelivery(id) {
      if (!confirm("Are you sure you want to mark this package as delivered?")) return;
      fetch('Backend/confirmdelivery.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert("Package marked as delivered.");
          this.fetchPackages();
        } else {
          alert("Delivery confirmation failed: " + data.message);
        }
      });
    },
    deletePackage(id) {
      if (!confirm("Are you sure you want to delete this package? This cannot be undone.")) return;
      fetch('Backend/deletepackage.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id })
      })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          alert("Package deleted.");
          this.fetchPackages();
        } else {
          alert("Deletion failed: " + data.message);
        }
      });
    }
  }
}
</script>
</body>
</html>
