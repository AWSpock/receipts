<?php

require_once(__DIR__ . "/../../models/Store.php");

class StoreRepository
{
    private $db;
    private $userid;

    private $records = [];
    private $loaded = false;

    public $actionDataMessage;

    public function __construct(DatabaseV2 $db, $userid)
    {
        $this->db = $db;
        $this->userid = $userid;
    }

    public function getRecords()
    {
        if ($this->loaded) {
            $recs = [];
            foreach ($this->records as $key => $rec) {
                if ($rec->id() > 0)
                    $recs[$key] = $rec;
            }
            return $recs;
        }

        $sql = "
            SELECT DISTINCT a.`store`
            FROM receipt a
            WHERE account_id IN (
                SELECT `account_id`
                FROM account
                WHERE `userid` = ?
            )
                OR account_id IN (
                    SELECT `account_id`
                    FROM account_share
                    WHERE `userid` = ?
                )
            ORDER BY `store`
        ";

        $result = $this->db->query($sql, [
            $this->userid,
            $this->userid
        ], "ii");

        $this->loaded = true;
        $this->records = [];
        foreach ($result->fetch_all(MYSQLI_ASSOC) as $rec) {
            $this->records[$rec['store']] = Store::fromDatabase($rec);
        }
        return $this->records;
    }
}
