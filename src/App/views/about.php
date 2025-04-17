<?php include $this->resolve('/partials/_header.php'); ?>

<!-- Start main content Area-->

<section data-barba="container" data-barba-namespace="home" class="container mx-auto mt-12 p-4 bg-white shadow-md border border-gray-200 rounded">
    <!-- Page title -->

    <h3>About Page</h3>

    <hr />

    <!-- Escaping Data -->

    <p>Escaping Data : <?php echo e($danger); ?></p>
</section>

<!-- End main content Area -->

<?php include $this->resolve('/partials/_footer.php'); ?>