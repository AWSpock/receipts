<div class="header">
    <h1>Delete Document</h1>
</div>

<nav class="breadcrumbs">
    <ul>
        <li><a href="/">Accounts</a></li>
        <li><a href="/account/<?php echo htmlentities($recAccount->id()); ?>/summary"><?php echo htmlentities($recAccount->name()); ?></a></li>
        <!-- <li><a href="/account/<?php //echo htmlentities($recAccount->id()); ?>/document">Documents</a></li> -->
        <li><a href="/account/<?php echo htmlentities($recAccount->id()); ?>/document/<?php echo htmlentities($recDocument->id()); ?>/edit">Edit Document: <span><?php echo htmlentities($recDocument->name()); ?></span></a></li>
        <li>Delete</li>
    </ul>
</nav>

<div class="content">
    <form method="post" action="" id="frm" class="form-group main-form">
        <input type="hidden" id="document.id" name="document.id" value="<?php echo htmlentities($recDocument->id()); ?>" />
        <p>Are you sure you wish to delete this Document?</p>
        <div class="input-group">
            <label class="form-control">Name</label>
            <div><samp><?php echo htmlentities($recDocument->name()); ?></samp></div>
        </div>
        <div class="input-group">
            <label class="form-control">Description</label>
            <div><samp><?php echo htmlentities($recDocument->description()); ?></samp></div>
        </div>
        <div class="input-group">
            <label class="form-control">Updated</label>
            <div><samp data-dateformatter><?php echo htmlentities($recDocument->updated()); ?></samp></div>
        </div>
        <div class="input-group">
            <label class="form-control">Created</label>
            <div><samp data-dateformatter><?php echo htmlentities($recDocument->created()); ?></samp></div>
        </div>
        <div class="button-group">
            <button type="submit" class="button remove"><i class="fa-solid fa-trash"></i>Confirm Delete</button>
            <a href="/account/<?php echo htmlentities($recAccount->id()); ?>/document/<?php echo htmlentities($recDocument->id()); ?>/edit" class="button secondary"><i class="fa-solid fa-ban"></i>Cancel</a>
        </div>
    </form>
</div>