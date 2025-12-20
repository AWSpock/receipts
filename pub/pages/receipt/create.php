<div class="header">
    <h1>Add Receipt</h1>
</div>

<nav class="breadcrumbs">
    <ul>
        <li><a href="/">Accounts</a></li>
        <li><a href="/account/<?php echo htmlentities($recAccount->id()); ?>/summary"><?php echo htmlentities($recAccount->name()); ?></a></li>
        <!-- <li><a href="/account/<?php //echo htmlentities($recAccount->id()); 
                                    ?>/receipt">Receipts</a></li> -->
        <li>Add Receipt</li>
    </ul>
</nav>

<div class="content">
    <form method="post" action="" id="frm" class="form-group main-form" enctype="multipart/form-data">
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
            <span class="validation-error <?php echo ($recReceipt->file_error() !== UPLOAD_ERR_OK && $recReceipt->store() !== null) ? "" : "hidden"; ?>" data-id="receipt.file">Please choose a file.</span>
            <span id="receipt.file_name"></span>
        </div>
        <div class="button-group">
            <button type="submit" class="button primary"><i class="fa-solid fa-save"></i>Save</button>
            <!-- <a href="/account/<?php //echo htmlentities($recAccount->id()); 
                                    ?>/receipt" class="button secondary"><i class="fa-solid fa-ban"></i>Cancel</a> -->
            <a href="/account/<?php echo htmlentities($recAccount->id()); ?>/summary" class="button secondary"><i class="fa-solid fa-ban"></i>Cancel</a>
        </div>
    </form>
</div>