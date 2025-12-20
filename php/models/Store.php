<?php

class Store
{
    protected $store;

    // protected $receipts;

    public function __construct($rec = null)
    {
        if ($rec !== NULL) {
            $this->store = (array_key_exists("store", $rec) && $rec['store'] !== NULL) ? $rec['store'] : null;
        }
    }

    public static function fromDatabase($db)
    {
        $rec1['store'] = $db['store'];
        $new = new static($rec1);
        return $new;
    }

    public function store()
    {
        return $this->store;
    }

    public function store_url()
    {
        return $this->store !== null ? str_replace(" ", "-", str_replace("/", "", $this->store)) : null;
    }

    public function toString($pretty = false)
    {
        $obj = (object) [
            "store" => $this->store(),
            "store_url" => $this->store_url()
        ];

        if ($pretty === true)
            return json_encode(get_object_vars($obj), JSON_PRETTY_PRINT);

        return json_encode(get_object_vars($obj));
    }

    //

    // public function receipts()
    // {
    //     return $this->receipts;
    // }
    // public function store_receipt(Receipt $rec)
    // {
    //     if ($this->receipts === null)
    //         $this->receipts = [];
    //     $this->receipts[$rec->id()] = $rec;
    // }
    // public function store_receipts(array $recs)
    // {
    //     $this->receipts = [];

    //     foreach ($recs as $rec) {
    //         if (!$rec instanceof Receipt)
    //             throw new InvalidArgumentException("Array must contain only instances of Receipt");
    //         $this->receipts[$rec->id()] = $rec;
    //     }
    // }
}
