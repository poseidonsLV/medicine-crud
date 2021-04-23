<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="account.php">Medicine</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/medicine-app/account.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/medicine-app/add/medicine.php">Add Medicine</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/medicine-app/view/medicine.php">View Medicines</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<script>
    $(document).ready(function() {
        $('.navbar-toggler').click(function() {
            $('.navbar-collapse').toggleClass('show');
        })
    })
</script>