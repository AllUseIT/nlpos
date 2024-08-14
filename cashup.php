<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cashup Template</title>
    <link rel="stylesheet" href="./styles/cashup.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Daily Cashup</h1>
            <p>Date: <span id="currentDate"></span></p>
        </header>
        
        <section class="cashup-section">
            <h2>Sales Information</h2>
            <form id="cashup-form">
                <div class="form-group">
                    <label for="totalSales">Total Sales:</label>
                    <input type="number" id="totalSales" name="totalSales" required>
                </div>
                <div class="form-group">
                    <label for="cashSales">Cash Sales:</label>
                    <input type="number" id="cashSales" name="cashSales" required>
                </div>
                <div class="form-group">
                    <label for="cardSales">Card Sales:</label>
                    <input type="number" id="cardSales" name="cardSales" required>
                </div>
                <div class="form-group">
                    <label for="otherSales">Other Sales:</label>
                    <input type="number" id="otherSales" name="otherSales">
                </div>
                <div class="form-group">
                    <label for="cashInDrawer">Cash in Drawer:</label>
                    <input type="number" id="cashInDrawer" name="cashInDrawer" required>
                </div>
                <div class="form-group">
                    <label for="cashBanked">Cash Banked:</label>
                    <input type="number" id="cashBanked" name="cashBanked">
                </div>
                <div class="form-group">
                    <label for="cashDiscrepancy">Discrepancy:</label>
                    <input type="number" id="cashDiscrepancy" name="cashDiscrepancy" readonly>
                </div>
                <button type="submit" class="submit-btn">Submit Cashup</button>
            </form>
        </section>

        <footer class="footer">
            <p>&copy; 2024 Your Company Name</p>
        </footer>
    </div>

    <script>
        // Display current date
document.getElementById('currentDate').textContent = new Date().toLocaleDateString();

// Calculate discrepancy
document.getElementById('cashup-form').addEventListener('input', function() {
    const totalSales = parseFloat(document.getElementById('totalSales').value) || 0;
    const cashSales = parseFloat(document.getElementById('cashSales').value) || 0;
    const cardSales = parseFloat(document.getElementById('cardSales').value) || 0;
    const otherSales = parseFloat(document.getElementById('otherSales').value) || 0;
    const cashInDrawer = parseFloat(document.getElementById('cashInDrawer').value) || 0;
    const cashBanked = parseFloat(document.getElementById('cashBanked').value) || 0;

    const discrepancy = (cashSales + cardSales + otherSales) - (cashInDrawer + cashBanked);
    document.getElementById('cashDiscrepancy').value = discrepancy.toFixed(2);
});

    </script>
</body>
</html>
