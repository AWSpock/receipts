<?php

class Receipt
{
    protected $id;
    protected $created;
    protected $updated;
    protected $store;
    protected $date;
    protected $amount;

    protected $file;
    protected $file_name;
    protected $file_path;
    protected $file_size;
    protected $file_modified;
    protected $file_type;
    protected $file_error;

    protected $new_file_name;

    public function __construct($rec = null)
    {
        $this->id = -1;
        if ($rec !== NULL) {
            $this->id = (array_key_exists("id", $rec) && $rec['id'] !== NULL) ? $rec['id'] : -1;
            $this->created = (array_key_exists("created", $rec) && $rec['created'] !== NULL) ? $rec['created'] : null;
            $this->updated = (array_key_exists("updated", $rec) && $rec['updated'] !== NULL) ? $rec['updated'] : null;
            $this->store = (array_key_exists("store", $rec) && $rec['store'] !== NULL) ? $rec['store'] : null;
            $this->date = (array_key_exists("date", $rec) && $rec['date'] !== NULL) ? $rec['date'] : null;
            $this->amount = (array_key_exists("amount", $rec) && $rec['amount'] !== NULL) ? $rec['amount'] : null;
            $this->file = (array_key_exists("file", $rec) && $rec['file'] !== NULL) ? $rec['file'] : null;
            $this->file_name = (array_key_exists("file_name", $rec) && $rec['file_name'] !== NULL) ? $rec['file_name'] : null;
            $this->file_path = (array_key_exists("file_path", $rec) && $rec['file_path'] !== NULL) ? $rec['file_path'] : null;
            $this->file_size = (array_key_exists("file_size", $rec) && $rec['file_size'] !== NULL) ? $rec['file_size'] : null;
            $this->file_modified = (array_key_exists("file_modified", $rec) && $rec['file_modified'] !== NULL) ? $rec['file_modified'] : null;
            $this->file_type = (array_key_exists("file_type", $rec) && $rec['file_type'] !== NULL) ? $rec['file_type'] : null;
            $this->file_error = (array_key_exists("file_error", $rec) && $rec['file_error'] !== NULL) ? $rec['file_error'] : null;
        }
    }

    public static function fromPost($post, $files)
    {
        $rec1['id'] = !empty($post['receipt_id']) ? $post['receipt_id'] : -1;
        $rec1['store'] = $post['receipt_store'];
        $rec1['date'] = $post['receipt_date'];
        $rec1['amount'] = $post['receipt_amount'];
        if (isset($files["receipt_file"])) {
            $rec1['file'] = $files['receipt_file'];
        }
        $new = new static($rec1);
        return $new;
    }

    public static function fromDatabase($db)
    {
        $rec1['id'] = $db['id'];
        $rec1['created'] = $db['created'];
        $rec1['updated'] = $db['updated'];
        $rec1['store'] = $db['store'];
        $rec1['date'] = $db['date'];
        $rec1['amount'] = $db['amount'];
        $rec1['file_name'] = $db['file_name'];
        $rec1['file_path'] = $db['file_path'];
        $rec1['file_size'] = $db['file_size'];
        $rec1['file_modified'] = $db['file_modified'];
        $rec1['file_type'] = $db['file_type'];
        $new = new static($rec1);
        return $new;
    }

    public function id()
    {
        return intval($this->id);
    }
    public function set_id($id)
    {
        if (is_int($id))
            $this->id = $id;
    }
    public function created()
    {
        return $this->created;
    }
    public function updated()
    {
        return $this->updated;
    }
    public function store()
    {
        return $this->store;
    }
    public function date()
    {
        return $this->date;
    }
    public function amount()
    {
        return $this->amount != null ? floatval($this->amount) : null;
    }

    public function file()
    {
        return $this->file;
    }
    public function file_name()
    {
        return ($this->file !== NULL && $this->file["name"] !== NULL) ? $this->file["name"] : $this->file_name;
    }


    public function file_path()
    {
        return $this->file_path;
    }
    public function set_file_path($path)
    {
        return $this->file_path = $path;
    }

    public function generate_new_file_name()
    {
        $this->new_file_name = bin2hex(random_bytes(16));
    }
    public function new_file_name()
    {
        return $this->new_file_name;
    }
    public function shard1()
    {
        return substr($this->new_file_name, 0, 2);
    }
    public function shard2()
    {
        return substr($this->new_file_name, 2, 2);
    }

    public function file_size()
    {
        return ($this->file !== NULL && $this->file["size"] !== NULL) ? intval($this->file["size"]) : $this->file_size;
    }
    public function file_modified()
    {
        return $this->file_modified;
    }
    public function set_file_modified($modified)
    {
        $this->file_modified = $modified;
    }
    public function file_type()
    {
        return ($this->file !== NULL && $this->file["type"] !== NULL) ? $this->file["type"] : $this->file_type;
    }
    public function file_error()
    {
        return ($this->file !== NULL && $this->file["error"] !== NULL) ? $this->file["error"] : null;
    }

    public function toString($pretty = false)
    {
        $obj = (object) [
            "id" => $this->id(),
            "created" => $this->created(),
            "updated" => $this->updated(),
            "store" => $this->store(),
            "date" => $this->date(),
            "amount" => $this->amount(),
            "file_name" => $this->file_name(),
            "file_path" => $this->file_path(),
            "file_size" => $this->file_size(),
            "file_modified" => $this->file_modified(),
            "file_type" => $this->file_type(),
            "file_error" => $this->file_error()
        ];

        if ($pretty === true)
            return json_encode(get_object_vars($obj), JSON_PRETTY_PRINT);

        return json_encode(get_object_vars($obj));
    }
}
