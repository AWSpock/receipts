<?php

require_once(__DIR__ . "/../../models/Account.php");

class AccountRepository
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

    public function getRecordById($id)
    {
        if (!array_key_exists($id, $this->records)) {
            $sql = "
                SELECT a.`id`, a.`created`, a.`updated`, a.`name`, if(isnull(b.`account_id`),'No','Yes') AS `favorite`, ifnull(c.`role`,'Owner') AS `role`
                FROM account a
                    LEFT OUTER JOIN account_favorite b
                        ON a.`id` = b.`account_id`
                        AND b.`userid` = ?
                    LEFT OUTER JOIN account_share c
                        ON a.`id` = c.`account_id`
                        AND c.`userid` = ?
                WHERE a.`id` = ? 
                    AND (
                        a.`userid` = ?
                        OR a.`id` IN (
                            SELECT `account_id`
                            FROM account_share
                            WHERE `userid` = ?
                        )
                    )
            ";

            $result = $this->db->query($sql, [
                $this->userid,
                $this->userid,
                $id,
                $this->userid,
                $this->userid,
            ], "iiiii");

            if ($result) {
                $rec = Account::fromDatabase($result->fetch_array(MYSQLI_ASSOC));
                $this->records[$id] = $rec;
            } else {
                $this->records[$id] = null;
            }
        }
        return $this->records[$id];
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
            SELECT a.`id`, a.`created`, a.`updated`, a.`name`, if(isnull(b.`account_id`),'No','Yes') AS `favorite`, ifnull(c.`role`,'Owner') AS `role`
            FROM account a
                LEFT OUTER JOIN account_favorite b
                    ON a.`id` = b.`account_id`
                    AND b.`userid` = ?
                LEFT OUTER JOIN account_share c
                    ON a.`id` = c.`account_id`
                    AND c.`userid` = ?
            WHERE a.`userid` = ?
                OR a.`id` IN (
                    SELECT `account_id`
                    FROM account_share
                    WHERE `userid` = ?
                )
            ORDER BY `favorite` DESC, `name`
        ";

        $result = $this->db->query($sql, [
            $this->userid,
            $this->userid,
            $this->userid,
            $this->userid
        ], "iiii");

        $this->loaded = true;
        $this->records = [];
        foreach ($result->fetch_all(MYSQLI_ASSOC) as $rec) {
            $this->records[$rec['id']] = Account::fromDatabase($rec);
        }
        return $this->records;
    }

    public function insertRecord(Account $rec)
    {
        $this->actionDataMessage = "Failed to insert Account";

        if (empty($rec->name())) {
            $this->actionDataMessage = "Name is required to insert Account";
            return 0;
        }

        $this->db->beginTransaction();

        $sql = "
            INSERT INTO account (`name`,`userid`)
            VALUES (?,?)
        ";

        $result = $this->db->query($sql, [
            $rec->name(),
            $this->userid
        ], "si");

        if (is_int($result) && $result > 0) {
            $this->actionDataMessage = "Account Inserted";
            $this->db->commit();
            return $result;
        }
        $this->db->rollback();
        return 0;
    }

    public function updateRecord(Account $rec)
    {
        $this->actionDataMessage = "Failed to update Account";

        if (empty($rec->name())) {
            $this->actionDataMessage = "Name is required to update Account";
            return 0;
        }

        $this->db->beginTransaction();

        $sql = "
            UPDATE account 
            SET `name` = ?
            WHERE `id` = ? 
            AND `userid` = ?
        ";

        $result = $this->db->query($sql, [
            $rec->name(),
            $rec->id(),
            $this->userid
        ], "sii");

        if ($result !== false) {
            if ($result !== 1) {
                $this->actionDataMessage = "Account Unchanged";
                return 2;
            }
            $this->actionDataMessage = "Account Updated";
            $this->db->commit();
            return 1;
        }

        $this->db->rollback();
        return false;
    }

    public function deleteRecord(Account $rec)
    {
        $this->actionDataMessage = "Failed to delete Account";

        $this->db->beginTransaction();

        $sql = "
            DELETE a, b, c, d
            FROM account a
                LEFT OUTER JOIN account_favorite b ON a.`id` = b.`account_id`
                LEFT OUTER JOIN account_share c ON a.`id` = b.`account_id`
                LEFT OUTER JOIN receipt d ON a.`id` = d.`account_id`
            WHERE a.`id` = ? 
            AND a.`userid` = ?
        ";

        $result = $this->db->query($sql, [
            $rec->id(),
            $this->userid
        ], "ii");

        if (is_int($result) && $result > 0) {
            $this->actionDataMessage = "Account Deleted";
            $this->db->commit();
            return 1;
        }
        $this->db->rollback();
        return 0;
    }

    //

    public function setFavorite($id)
    {
        $this->actionDataMessage = "Failed to Add Favorite Account";

        $this->db->beginTransaction();

        $sql = "
            INSERT INTO account_favorite (`account_id`, `userid`)
            VALUES (?,?)
        ";

        $result = $this->db->query($sql, [
            $id,
            $this->userid
        ], "ii");

        if ($result === true) {
            $this->actionDataMessage = "Added Favorite Account";
            $this->db->commit();
            return true;
        }

        $this->db->rollback();
        return false;
    }

    public function removeFavorite($id)
    {
        $this->actionDataMessage = "Failed to Remove Favorite Account";

        $this->db->beginTransaction();

        $sql = "
            DELETE FROM account_favorite
            WHERE `account_id` = ? 
            AND `userid` = ?
        ";

        $result = $this->db->query($sql, [
            $id,
            $this->userid
        ], "ii");

        if (is_int($result) && $result > 0) {
            $this->actionDataMessage = "Removed Favorite Account";
            $this->db->commit();
            return true;
        }

        $this->db->rollback();
        return false;
    }
}
