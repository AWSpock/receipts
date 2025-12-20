<div class="header">
    <h1>Delete Account</h1>
</div>

<nav class="breadcrumbs">
    <ul>
        <li><a href="/">Accounts</a></li>
        <li><a href="/account/<?php echo htmlentities($recAccount->id()); ?>/summary"><?php echo htmlentities($recAccount->name()); ?></a></li>
        <li><a href="/account/<?php echo htmlentities($recAccount->id()); ?>/edit">Edit</a></li>
        <li>Delete</li>
    </ul>
</nav>

<div class="content">
    <form method="post" action="" id="frm" class="form-group main-form">
        <input type="hidden" id="warranty.id" name="warranty.id" value="<?php echo htmlentities($recAccount->id()); ?>" />
        <p>Are you sure you wish to delete this Account?</p>
        <div class="input-group">
            <label class="form-control">Name</label>
            <div><samp><?php echo htmlentities($recAccount->name()); ?></samp></div>
        </div>
        <div class="input-group">
            <label class="form-control">Receipts</label>
            <div><samp data-numberformatter><?php echo htmlentities(count($recAccount->receipts())); ?></samp></div>
        </div>
        <div class="button-group">
            <button type="submit" class="button remove"><i class="fa-solid fa-trash"></i>Confirm Delete</button>
            <a href="/account/<?php echo htmlentities($recAccount->id()); ?>/edit" class="button secondary"><i class="fa-solid fa-ban"></i>Cancel</a>
        </div>
    </form>
</div>