<?php

require_once(__DIR__ . "/../../models/Receipt.php");

class ReceiptRepository
{
    private $db;
    private $account_id;

    private $records = [];
    private $loaded = false;

    public $actionDataMessage;

    private $validTypes = ["application/pdf", "image/jpeg"];

    public function __construct(DatabaseV2 $db, $account_id)
    {
        $this->db = $db;
        $this->account_id = $account_id;
    }

    public function getRecordById($id)
    {
        if (!array_key_exists($id, $this->records)) {
            $sql = "
                SELECT a.`id`, a.`created`, a.`updated`, a.`store`, a.`date`, a.`amount`, a.`file_name`, a.`file_path`, a.`file_size`, a.`file_modified`, a.`file_type`
                FROM receipt a
                WHERE a.`account_id` = ?
                    AND a.`id` = ?
            ";

            $result = $this->db->query($sql, [
                $this->account_id,
                $id
            ], "ii");

            if ($result) {
                $rec = Receipt::fromDatabase($result->fetch_array(MYSQLI_ASSOC));
                $this->records[$id] = $rec;
            } else {
                $this->records[$id] = null;
            }
        }
        return $this->records[$id];
    }

    public function recordExistsByShard($shard1, $shard2, $file)
    {
        $sql = "
            SELECT a.`id`
            FROM receipt a
            WHERE a.`shard1` = ?
                AND a.`shard2` = ?
                AND a.`file_path` LIKE ?
        ";

        $result = $this->db->query($sql, [
            $shard1,
            $shard2,
            "%/" . $file
        ], "sss");

        if ($result) {
            return $result->fetch_array(MYSQLI_ASSOC)['id'];
        } else {
            return false;
        }
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
            SELECT a.`id`, a.`created`, a.`updated`, a.`store`, a.`date`, a.`amount`, a.`file_name`, a.`file_path`, a.`file_size`, a.`file_modified`, a.`file_type`
            FROM receipt a
            WHERE a.`account_id` = ?
            ORDER BY a.`date` DESC, a.`store`
        ";

        $result = $this->db->query($sql, [
            $this->account_id
        ], "i");

        $this->loaded = true;
        $this->records = [];
        foreach ($result->fetch_all(MYSQLI_ASSOC) as $rec) {
            $this->records[$rec['id']] = Receipt::fromDatabase($rec);
        }
        return $this->records;
    }

    public function insertRecord(Receipt $rec)
    {
        $this->actionDataMessage = "Failed to insert Receipt";

        if (empty($rec->store())) {
            $this->actionDataMessage = "Missing required fields to insert Receipt";
            return 0;
        }
        if ($rec->file_error() !== UPLOAD_ERR_OK) {
            $this->actionDataMessage = "File Upload Error: " . returnUploadExceptionMessage($rec->file_error());
            return 0;
        }
        if ($rec->file_size() < 0 && $rec->file_type() !== null) {
            $this->actionDataMessage = "File is required to insert Receipt";
            return 0;
        }
        if (!in_array($rec->file_type(), $this->validTypes)) {
            $this->actionDataMessage = "Invalid Receipt Type: " . $rec->file_type();
            return 0;
        }

        $this->db->beginTransaction();

        $sql = "
            INSERT INTO receipt (`account_id`,`store`,`date`,`amount`,`file_size`,`file_type`)
            VALUES (?,?,?,?,?,?)
        ";

        $result = $this->db->query($sql, [
            $this->account_id,
            $rec->store(),
            $rec->date(),
            $rec->amount(),
            $rec->file_size(),
            $rec->file_type()
        ], "issdis");

        if (is_int($result) && $result > 0) {
            $this->actionDataMessage = "Receipt Inserted";

            $rec->set_id($result);

            if (!$this->saveFile($rec)) {
                return false;
            }

            $sql1 = "
                UPDATE receipt
                SET `file_modified` = now(),
                    `shard1` = ?,
                    `shard2` = ?,
                    `file_name` = ?,
                    `file_path` = ?,
                    `file_size` = ?,
                    `file_modified` = now(),
                    `file_type` = ?
                WHERE `id` = ? 
                AND `account_id` = ?
            ";

            $result1 = $this->db->query($sql1, [
                $rec->shard1(),
                $rec->shard2(),
                $rec->file_name(),
                $this->db->file_dir() . $rec->file_path(),
                $rec->file_size(),
                $rec->file_type(),
                $result,
                $this->account_id
            ], "ssssisii");

            if ($result1 === false) {
                $this->actionDataMessage = "Failed to save Receipt";
                $this->db->rollback();
                return 0;
            }

            $this->db->commit();
            return $result;
        }
        $this->db->rollback();
        return 0;
    }

    public function updateRecord(Receipt $rec)
    {
        $this->actionDataMessage = "Failed to update Receipt";

        if (empty($rec->store())) {
            $this->actionDataMessage = "Missing required fields to insert Receipt";
            return 0;
        }

        if ($rec->file_error() !== UPLOAD_ERR_OK && $rec->file_error() !== UPLOAD_ERR_NO_FILE) {
            $this->actionDataMessage = "File Upload Error: " . returnUploadExceptionMessage($rec->file_error());
            return 0;
        }

        $this->db->beginTransaction();

        if ($rec->file_error() === UPLOAD_ERR_OK) {
            if ($rec->file_size() < 0 && $rec->file_type() !== null) {
                $this->actionDataMessage = "File is required to insert Receipt";
                return 0;
            }
            if (!in_array($rec->file_type(), $this->validTypes)) {
                $this->actionDataMessage = "Invalid Receipt Type: " . $rec->file_type();
                return 0;
            }

            if (!$this->saveFile($rec)) {
                return false;
            }

            $sql = "
                UPDATE receipt
                SET `store` = ?,
                    `date` = ?,
                    `amount` = ?,
                    `shard1` = ?,
                    `shard2` = ?,
                    `file_name` = ?,
                    `file_size` = ?,
                    `file_type` = ?,
                    `file_modified` = now(),
                    `file_path` = ?
                WHERE `id` = ? 
                AND `account_id` = ?
            ";

            $fields = [
                $rec->store(),
                $rec->date(),
                $rec->amount(),
                $rec->shard1(),
                $rec->shard2(),
                $rec->file_name(),
                $rec->file_size(),
                $rec->file_type(),
                $this->db->file_dir() . $rec->file_path(),
                $rec->id(),
                $this->account_id
            ];
            $types = "ssdsssissii";
        } else {

            $sql = "
                UPDATE receipt
                SET `store` = ?,
                    `date` = ?,
                    `amount` = ?
                WHERE `id` = ? 
                AND `account_id` = ?
            ";

            $fields = [
                $rec->store(),
                $rec->date(),
                $rec->amount(),
                $rec->id(),
                $this->account_id
            ];
            $types = "ssdii";
        }

        $result = $this->db->query($sql, $fields, $types);

        if ($result !== false) {
            if ($result !== 1) {
                $this->actionDataMessage = "Receipt Unchanged";
                return 2;
            }
            $this->actionDataMessage = "Receipt Updated";
            $this->db->commit();
            return 1;
        }

        $this->db->rollback();
        return false;
    }

    public function deleteRecord(Receipt $rec)
    {
        $this->actionDataMessage = "Failed to delete Receipt";

        $this->db->beginTransaction();

        $sql = "
            DELETE FROM receipt 
            WHERE `id` = ? 
            AND `account_id` = ?
        ";

        $result = $this->db->query($sql, [
            $rec->id(),
            $this->account_id
        ], "ii");

        if (is_int($result) && $result > 0) {
            $this->actionDataMessage = "Receipt Deleted";
            $this->db->commit();
            return 1;
        }
        $this->db->rollback();
        return 0;
    }

    //

    private function saveFile($rec)
    {
        $rec->generate_new_file_name();

        $new_path = $rec->shard1() . "/" . $rec->shard2() . "/" . $rec->new_file_name();
        $rec->set_file_path($new_path);

        $full_path = $this->db->file_dir() . $new_path;

        if (!is_dir(dirname($full_path))) {
            if (!mkdir(dirname($full_path), 0770, true)) {
                $this->actionDataMessage = "Failed to create directories";
                return false;
            }
        }
        if (!move_uploaded_file($rec->file()['tmp_name'], $full_path)) {
            $this->actionDataMessage = "Failed to create file";
            return false;
        }

        return true;
    }
}
