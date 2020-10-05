<p>
    <?php echo $editor_first_name?> <?php echo $editor_last_name?> has updated your account info on <?php echo $root?>. Your current account info is:<br/>
    <br/>
    <b>Name:</b> <?php echo $first_name?> <?php echo $last_name?><br/>
    <b>Email:</b> <?php echo $email?><br/>
    <?php if ($password) : ?><b>Password:</b> <?php echo $password?> (you should change this ASAP)
    <?php else: ?><b>Password:</b> Unchanged (and cannot be displayed because it is one-way encrypted)
    <?php endif ?>
</p>
