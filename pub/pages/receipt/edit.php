<div class="header">
    <h1>Edit Document</h1>
</div>

<nav class="breadcrumbs">
    <ul>
        <li><a href="/">Accounts</a></li>
        <li><a href="/account/<?php echo htmlentities($recAccount->id()); ?>/summary"><?php echo htmlentities($recAccount->name()); ?></a></li>
        <!-- <li><a href="/account/<?php //echo htmlentities($recAccount->id()); 
                                    ?>/receipt">Documents</a></li> -->
        <li>Edit Document: <span><?php echo htmlentities($recReceipt->store()); ?> - <?php echo htmlentities($recReceipt->date()); ?></span></li>
    </ul>
</nav>

<div class="content">
    <form method="post" action="" id="frm" class="form-group main-form" enctype="multipart/form-data">
        <input type="hidden" id="receipt.id" name="receipt.id" value="<?php echo htmlentities($recReceipt->id()); ?>" />
        <div class="input-group date">
            <label for="receipt.date" class="form-control">Date</label>
            <input type="date" id="receipt.date" name="receipt.date" class="form-control" required="required" value="<?php echo htmlentities($recReceipt->date()); ?>" />
        </div>
        <div class="input-group store">
            <label for="receipt.store" class="form-control">Store</label>
            <input type="text" id="receipt.store" name="receipt.store" class="form-control" required="required" value="<?php echo htmlentities($recReceipt->store()); ?>" list="store-list" />
            <datalist id="store-list">
                <?php
                foreach ($stores as $store) {
                ?>
                    <option value="<?php echo htmlentities($store); ?>"></option>
                <?php
                }
                ?>
            </datalist>
        </div>
        <div class="input-group amount">
            <label for="receipt.amount" class="form-control">Amount</label>
            <input type="number" id="receipt.amount" name="receipt.amount" class="form-control" value="<?php echo htmlentities($recReceipt->amount()); ?>" min="0" step=".01" />
        </div>
        <div class="input-group file">
            <label for="receipt.file" class="form-control">File</label>
            <label for="receipt.file" class="form-control fileupload">Choose a File</label>
            <input type="file" id="receipt.file" name="receipt.file" class="form-control" />
            <span id="receipt.file_name"></span>
        </div>
        <div class="button-group">
            <button type="submit" class="button primary"><i class="fa-solid fa-save"></i>Save</button>
            <!-- <a href="/account/<?php //echo htmlentities($recAccount->id()); 
                                    ?>/receipt" class="button secondary"><i class="fa-solid fa-ban"></i>Cancel</a> -->
            <a href="/account/<?php echo htmlentities($recAccount->id()); ?>/summary" class="button secondary"><i class="fa-solid fa-ban"></i>Cancel</a>
            <a href="/account/<?php echo htmlentities($recAccount->id()); ?>/receipt/<?php echo htmlentities($recReceipt->id()); ?>/delete" class="button remove"><i class="fa-solid fa-trash"></i>Delete?</a>
        </div>
        <div class="input-group updated">
            <label class="form-control">Updated</label>
            <div><samp data-dateformatter><?php echo htmlentities($recReceipt->updated()); ?></samp></div>
        </div>
        <div class="input-group created">
            <label class="form-control">Created</label>
            <div><samp data-dateformatter><?php echo htmlentities($recReceipt->created()); ?></samp></div>
        </div>
    </form>
    <div class="preview">
        <iframe data-id="preview" src="/api/account/<?php echo $recAccount->id(); ?>/view-receipt/<?php echo $recReceipt->id(); ?>">
            <p>Browser does not support PDFs</p>
            <p><a href="/api/account/<?php echo $recAccount->id(); ?>/view-receipt/<?php echo $recReceipt->id(); ?>">Download</a></p>
        </iframe>
        <div data-id="show-preview">
            <p>Mobile phone detected</p>
            <p><a href="/api/account/<?php echo $recAccount->id(); ?>/view-receipt/<?php echo $recReceipt->id(); ?>" target="_blank">View</a></p>
        </div>
    </div>
</div>