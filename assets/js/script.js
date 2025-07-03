document.addEventListener('DOMContentLoaded', function() {
    // Sidebar elements
    const sidebar = document.querySelector('.sidebar');
    const content = document.getElementById('content');
    const sidebarCollapse = document.getElementById('sidebarCollapse');
    
    // Function to toggle sidebar
    function toggleSidebar() {
        sidebar.classList.toggle('active');
        content.classList.toggle('active');
        
        // Save state in localStorage
        const isCollapsed = sidebar.classList.contains('active');
        localStorage.setItem('sidebarCollapsed', isCollapsed);
    }
    
    // Toggle sidebar when button is clicked
    if (sidebarCollapse) {
        sidebarCollapse.addEventListener('click', function(e) {
            e.stopPropagation(); // Prevent event from bubbling to document
            toggleSidebar();
        });
    }
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        // Check if sidebar is open and we're on mobile (or screen is small)
        const isMobile = window.innerWidth <= 768; // Standard mobile breakpoint
        const isSidebarOpen = sidebar.classList.contains('active');
        
        // Check if click is outside the sidebar and not on the toggle button
        const isClickInsideSidebar = sidebar.contains(e.target);
        const isClickOnToggleButton = sidebarCollapse && sidebarCollapse.contains(e.target);
        
        if (isMobile && isSidebarOpen && !isClickInsideSidebar && !isClickOnToggleButton) {
            toggleSidebar();
        }
    });
    
    // Prevent clicks inside sidebar from closing it
    if (sidebar) {
        sidebar.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
    
    // Check saved state on page load
    const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
    if (isCollapsed) {
        sidebar.classList.add('active');
        content.classList.add('active');
    }
    
    // Highlight active menu item
    const currentPath = window.location.pathname;
    const menuItems = document.querySelectorAll('.sidebar a');
    
    menuItems.forEach(item => {
        const itemPath = new URL(item.href).pathname;
        if (currentPath.includes(itemPath)) {
            item.classList.add('active');
        }
    });
    
    // Auto close alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s ease-out';
            alert.style.opacity = '0';
            setTimeout(() => {
                alert.remove();
            }, 500);
        }, 5000);
    });
    
    // Print functionality
    const printButtons = document.querySelectorAll('.btn-print');
    printButtons.forEach(button => {
        button.addEventListener('click', function() {
            window.print();
        });
    });
    
    // Handle window resize to adjust sidebar behavior
    window.addEventListener('resize', function() {
        // Optional: You can add logic here to handle sidebar on resize
        // For example, automatically close sidebar when switching to mobile
        // or ensure it's open when switching to desktop
    });
});