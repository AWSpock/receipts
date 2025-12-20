<div class="header">
    <h1>Add Account</h1>
</div>

<nav class="breadcrumbs">
    <ul>
        <li><a href="/">Accounts</a></li>
        <li>Add Account</li>
    </ul>
</nav>

<div class="content">
    <form method="post" action="" id="frm" class="form-group main-form">
        <div class="input-group">
            <label for="account.name" class="form-control">Name</label>
            <input type="text" id="account.name" name="account.name" class="form-control" required="required" value="<?php echo htmlentities($recAccount->name()); ?>" />
        </div>
        <div class="button-group">
            <button type="submit" class="button primary"><i class="fa-solid fa-save"></i>Save</button>
            <a href="/" class="button secondary"><i class="fa-solid fa-ban"></i>Cancel</a>
        </div>
    </form>
</div>