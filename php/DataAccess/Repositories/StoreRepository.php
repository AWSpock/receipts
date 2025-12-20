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

    public function getRecordByName($name)
    {
        if ($this->loaded) {
            $recs = [];
            foreach ($this->records as $key => $rec) {
                if ($rec->name() !== null)
                    $recs[$key] = $rec;
            }
            return $recs;
        }

        $sql = "
            SELECT a.`store`
            FROM receipt a
            WHERE (
                account_id IN (
                    SELECT `account_id`
                    FROM account
                    WHERE `userid` = ?
                )
                    OR account_id IN (
                        SELECT `account_id`
                        FROM account_share
                        WHERE `userid` = ?
                    )
            )
                AND REPLACE(REPLACE(a.`store`,' ','-'),'/','') = ?
        ";

        $result = $this->db->query($sql, [
            $this->userid,
            $this->userid,
            $name
        ], "iis");

        if ($result) {
            $rec = Store::fromDatabase($result->fetch_array(MYSQLI_ASSOC));
            $this->records[$name] = $rec;
        } else {
            $this->records[$name] = null;
        }

        return $this->records[$name];
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
            SELECT DISTINCT a.`store`, REPLACE(REPLACE(a.`store`,' ','-'),'/','') AS name
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
            $this->records[$rec['name']] = Store::fromDatabase($rec);
        }
        return $this->records;
    }

    public function getReceiptsByName($name)
    {
        $sql = "
            SELECT a.`id`, a.`created`, a.`updated`, a.`store`, a.`date`, a.`amount`, a.`file_name`, a.`file_path`, a.`file_size`, a.`file_modified`, a.`file_type`, b.`id` AS account_id, b.`name` AS account
            FROM receipt a
                INNER JOIN account b on a.`account_id`=b.`id`
            WHERE (
                b.`userid` = ?
                OR b.`id` IN (
                    SELECT `account_id`
                    FROM account_share
                    WHERE `userid` = ?
                )
            )
                AND REPLACE(REPLACE(a.`store`,' ','-'),'/','') = ?
            ORDER BY a.`date` DESC
        ";

        $result = $this->db->query($sql, [
            $this->userid,
            $this->userid,
            $name
        ], "iis");

        $records = [];
        foreach ($result->fetch_all(MYSQLI_ASSOC) as $rec) {
            $records[$rec['id']] = Receipt::fromDatabase($rec);
        }
        return $records;
    }
}
