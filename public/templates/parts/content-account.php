<section class="account">
    <h2>My Account</h2>
    <div class="content">
        <?php
            wc_get_template( 'myaccount/form-edit-account.php', array(
                'user' => $user, // Pass the $user variable to the template
            ) );
        ?>
    </div>
</section>
