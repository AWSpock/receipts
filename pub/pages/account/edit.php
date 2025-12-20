<div class="header">
    <h1>Edit Account</h1>
</div>

<nav class="breadcrumbs">
    <ul>
        <li><a href="/">Accounts</a></li>
        <li><a href="/account/<?php echo htmlentities($recAccount->id()); ?>/summary"><?php echo htmlentities($recAccount->name()); ?></a></li>
        <li>Edit</li>
    </ul>
</nav>

<div class="content">
    <form method="post" action="" id="frm" class="form-group main-form">
        <input type="hidden" id="account.id" name="account.id" value="<?php echo htmlentities($recAccount->id()); ?>" />
        <div class="input-group">
            <label for="account.name" class="form-control">Name</label>
            <input type="text" id="account.name" name="account.name" class="form-control" required="required" value="<?php echo htmlentities($recAccount->name()); ?>" />
        </div>
        <div class="button-group">
            <button type="submit" class="button primary"><i class="fa-solid fa-save"></i>Save</button>
            <a href="/account/<?php echo htmlentities($recAccount->id()); ?>/summary" class="button secondary"><i class="fa-solid fa-ban"></i>Cancel</a>
            <a href="/account/<?php echo htmlentities($recAccount->id()); ?>/delete" class="button remove"><i class="fa-solid fa-trash"></i>Delete?</a>
        </div>
    </form>
</div>