<?php
require_once(__DIR__ . "/Database.php");
require_once(__DIR__ . "/Repositories/AccountRepository.php");
require_once(__DIR__ . "/Repositories/StoreRepository.php");
require_once(__DIR__ . "/Repositories/ReceiptRepository.php");

class DataAccess
{
    private $db;
    private $accountRepository = [];
    private $storeRepository = [];
    private $receiptRepository = [];

    public function __construct(mysqli $db = null)
    {
        $this->db = $db ?? new DatabaseV2();
    }

    public function file_dir()
    {
        return $this->db->file_dir();
    }

    public function accounts($userid)
    {
        if (!array_key_exists($userid, $this->accountRepository)) {
            $this->accountRepository[$userid] = new AccountRepository($this->db, $userid);
        }
        return $this->accountRepository[$userid];
    }

    public function stores($userid)
    {
        if (!array_key_exists($userid, $this->storeRepository)) {
            $this->storeRepository[$userid] = new StoreRepository($this->db, $userid);
        }
        return $this->storeRepository[$userid];
    }

    public function receipts($account_id)
    {
        if (!array_key_exists($account_id, $this->receiptRepository)) {
            $this->receiptRepository[$account_id] = new ReceiptRepository($this->db, $account_id);
        }
        return $this->receiptRepository[$account_id];
    }

    //

    public function beginTransaction()
    {
        $this->db->beginTransaction();
    }
    public function commit()
    {
        $this->db->commit();
    }
    public function rollback()
    {
        $this->db->rollback();
    }
    public function close()
    {
        $this->db->close();
    }
    public function getDb()
    {
        return $this->db;
    }
}
