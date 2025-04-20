<aside class="admin-sidebar">
    <div class="admin-profile">
        <h3>Welcome, <?php echo $_SESSION['user_name']; ?></h3>
        <p>Administrator</p>
    </div>
    
    <nav class="admin-nav">
        <ul>
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li class="active"><a href="tips.php"><i class="fas fa-clipboard-list"></i> All Reports</a></li>
            <li><a href="pending-tips.php"><i class="fas fa-hourglass-half"></i> Pending</a></li>
            <li><a href="investigation-tips.php"><i class="fas fa-search"></i> Under Investigation</a></li>
            <li><a href="resolved-tips.php"><i class="fas fa-check-circle"></i> Resolved</a></li>
            <li><a href="users.php"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
        </ul>
    </nav>
</aside>