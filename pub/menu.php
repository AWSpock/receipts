<?php

if ($userAuth->checkToken()) {
    $favorite = null;
    foreach ($data->accounts($userAuth->user()->id())->getRecords() as $account) {
        if (is_null($favorite)) {
            $favorite = true;
?>
            <div class="menu-title">Favorite Accounts</div>
        <?php
        }
        if ($account->favorite() === "No" && $favorite) {
            $favorite = false;
        ?>
            <div class="menu-title">Other Accounts</div>
        <?php
        }
        ?>
        <li>
            <a href="/account/<?php echo $account->id(); ?>/summary"><i class="fa-solid fa-receipt"></i><?php echo htmlentities($account->name()); ?></a>
        </li>
        <?php
        /*if (isset($account_id) && $account->id() == $account_id) {
        ?>
            <!-- <ul> -->
            <li class="menu-sub"><a href="/account/<?php echo $account->id(); ?>/receipt"><i class="fa-solid fa-file"></i>Documents</a></li>
            <!-- </ul> -->
        <?php
        }*/
        ?>
<?php
    }
}
