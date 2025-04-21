<?php
session_start();
include '../../config/database.php';

if (!isset($_SESSION['username'])) {
    header("Location: ../../modules/auth/login.php");
    exit();
}

// Initialize variables for search and sort
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$sort_column = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$sort_order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Validate sort column to prevent SQL injection
$allowed_columns = ['id', 'kode_penggajian', 'nama', 'status', 'total_gaji'];
if (!in_array($sort_column, $allowed_columns)) {
    $sort_column = 'id';
}

// Validate sort order
$sort_order = strtoupper($sort_order) === 'DESC' ? 'DESC' : 'ASC';

// Build the query with search and sort
$query = "SELECT * FROM tbl_penggajian";
if (!empty($search)) {
    $query .= " WHERE kode_penggajian LIKE '%$search%' 
                OR nama LIKE '%$search%' 
                OR status LIKE '%$search%'
                OR total_gaji LIKE '%$search%'";
}
$query .= " ORDER BY $sort_column $sort_order";

$result = mysqli_query($conn, $query);
?>

<?php include '../../includes/header.php'; ?>

<script>
// Function for real-time search
function realTimeSearch() {
    const searchValue = document.getElementById('searchInput').value;
    const currentUrl = new URL(window.location.href);
    
    // Update search parameter in URL
    currentUrl.searchParams.set('search', searchValue);
    
    // Remove search parameter if empty
    if (searchValue === '') {
        currentUrl.searchParams.delete('search');
    }
    
    // Update browser history without reloading
    history.pushState({}, '', currentUrl);
    
    // Send AJAX request
    const xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            // Parse the response
            const parser = new DOMParser();
            const doc = parser.parseFromString(this.responseText, "text/html");
            
            // Update the table body
            const newTableBody = doc.querySelector('tbody');
            document.querySelector('tbody').innerHTML = newTableBody.innerHTML;
            
            // Update alerts if any
            const alerts = doc.querySelectorAll('.alert');
            const alertContainer = document.querySelector('.card-body');
            
            // Remove existing alerts
            document.querySelectorAll('.alert').forEach(alert => alert.remove());
            
            // Add new alerts
            alerts.forEach(alert => {
                alertContainer.insertBefore(alert, alertContainer.firstChild);
            });
        }
    };
    
    // Get current sort parameters
    const sort = currentUrl.searchParams.get('sort') || 'id';
    const order = currentUrl.searchParams.get('order') || 'ASC';
    
    // Prepare AJAX request
    xhr.open("GET", `?ajax=true&search=${encodeURIComponent(searchValue)}&sort=${sort}&order=${order}`, true);
    xhr.send();
}

// Debounce function to limit how often the search executes
function debounce(func, timeout = 300) {
    let timer;
    return (...args) => {
        clearTimeout(timer);
        timer = setTimeout(() => { func.apply(this, args); }, timeout);
    };
}

// Format currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {style: 'currency', currency: 'IDR'}).format(amount);
}

// Initialize after page load
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    
    // Add event listener with debounce
    searchInput.addEventListener('input', debounce(realTimeSearch));
    
    // Handle browser back/forward buttons
    window.addEventListener('popstate', function() {
        const urlParams = new URLSearchParams(window.location.search);
        searchInput.value = urlParams.get('search') || '';
        realTimeSearch();
    });
    
    // Format all currency cells
    document.querySelectorAll('.currency').forEach(cell => {
        const amount = parseFloat(cell.textContent.replace(/[^\d.-]/g, ''));
        if (!isNaN(amount)) {
            cell.textContent = formatCurrency(amount);
        }
    });
});
</script>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Data Penggajian</h4>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <a href="create.php" class="btn btn-success mb-3"><i class="bi bi-plus-circle"></i> Tambah Data</a>
                        </div>
                        <div class="col-md-6">
                            <div class="float-end">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="searchInput" placeholder="Cari..." value="<?php echo htmlspecialchars($search); ?>">
                                    <button class="btn btn-outline-secondary" type="button" onclick="realTimeSearch()"><i class="bi bi-search"></i></button>
                                    <?php if (!empty($search)): ?>
                                        <a href="?" class="btn btn-outline-danger"><i class="bi bi-x"></i></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success"><?php echo $_GET['success']; ?></div>
                    <?php endif; ?>
                    
                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-danger"><?php echo $_GET['error']; ?></div>
                    <?php endif; ?>
                    
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>No</th>
                                    <th>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Kode Gaji</span>
                                            <div class="btn-group">
                                                <a href="?sort=kode_penggajian&order=ASC&search=<?php echo urlencode($search); ?>" class="btn btn-sm btn-outline-light <?php echo ($sort_column == 'kode_penggajian' && $sort_order == 'ASC') ? 'active' : ''; ?>">
                                                    <i class="bi bi-sort-alpha-down"></i>
                                                </a>
                                                <a href="?sort=kode_penggajian&order=DESC&search=<?php echo urlencode($search); ?>" class="btn btn-sm btn-outline-light <?php echo ($sort_column == 'kode_penggajian' && $sort_order == 'DESC') ? 'active' : ''; ?>">
                                                    <i class="bi bi-sort-alpha-up"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Nama</span>
                                            <div class="btn-group">
                                                <a href="?sort=nama&order=ASC&search=<?php echo urlencode($search); ?>" class="btn btn-sm btn-outline-light <?php echo ($sort_column == 'nama' && $sort_order == 'ASC') ? 'active' : ''; ?>">
                                                    <i class="bi bi-sort-alpha-down"></i>
                                                </a>
                                                <a href="?sort=nama&order=DESC&search=<?php echo urlencode($search); ?>" class="btn btn-sm btn-outline-light <?php echo ($sort_column == 'nama' && $sort_order == 'DESC') ? 'active' : ''; ?>">
                                                    <i class="bi bi-sort-alpha-up"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Status</span>
                                            <div class="btn-group">
                                                <a href="?sort=status&order=ASC&search=<?php echo urlencode($search); ?>" class="btn btn-sm btn-outline-light <?php echo ($sort_column == 'status' && $sort_order == 'ASC') ? 'active' : ''; ?>">
                                                    <i class="bi bi-sort-alpha-down"></i>
                                                </a>
                                                <a href="?sort=status&order=DESC&search=<?php echo urlencode($search); ?>" class="btn btn-sm btn-outline-light <?php echo ($sort_column == 'status' && $sort_order == 'DESC') ? 'active' : ''; ?>">
                                                    <i class="bi bi-sort-alpha-up"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </th>
                                    <th>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span>Total Gaji</span>
                                            <div class="btn-group">
                                                <a href="?sort=total_gaji&order=ASC&search=<?php echo urlencode($search); ?>" class="btn btn-sm btn-outline-light <?php echo ($sort_column == 'total_gaji' && $sort_order == 'ASC') ? 'active' : ''; ?>">
                                                    <i class="bi bi-sort-numeric-down"></i>
                                                </a>
                                                <a href="?sort=total_gaji&order=DESC&search=<?php echo urlencode($search); ?>" class="btn btn-sm btn-outline-light <?php echo ($sort_column == 'total_gaji' && $sort_order == 'DESC') ? 'active' : ''; ?>">
                                                    <i class="bi bi-sort-numeric-up"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($result) > 0): ?>
                                    <?php $no = 1; ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo htmlspecialchars($row['kode_penggajian']); ?></td>
                                        <td><?php echo htmlspecialchars($row['nama']); ?></td>
                                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                                        <td class="currency"><?php echo $row['total_gaji']; ?></td>
                                        <td>
                                            <a href="edit.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                            <a href="delete.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin ingin menghapus?')"><i class="bi bi-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center">Tidak ada data ditemukan</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>