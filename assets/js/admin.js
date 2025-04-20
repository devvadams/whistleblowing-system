document.addEventListener('DOMContentLoaded', function() {
    // Toggle mobile menu
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const adminSidebar = document.querySelector('.admin-sidebar');
    
    if (mobileMenuToggle) {
        mobileMenuToggle.addEventListener('click', function() {
            adminSidebar.classList.toggle('active');
        });
    }
    
    // Confirm before deleting
    const deleteButtons = document.querySelectorAll('.btn-action.delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });
    });
    
    // Chart.js integration for analytics (optional)
    if (typeof Chart !== 'undefined') {
        const ctx = document.getElementById('analyticsChart').getContext('2d');
        const analyticsChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Reports Submitted',
                    data: [12, 19, 3, 5, 2, 3],
                    backgroundColor: 'rgba(54, 162, 235, 0.5)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }
});