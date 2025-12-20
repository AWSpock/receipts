<?php

class User
{
    protected $id;
    protected $expires;

    protected $email;
    protected $name;
    protected $token;

    public function __construct($rec = null)
    {
        $this->id = $rec['id'] ? $rec['id'] : -1;
        $this->email = $rec['email'] ? $rec['email'] : null;
        $this->name = $rec['name'] ? $rec['name'] : null;
        $this->expires = $rec['expires'] ? $rec['expires'] : null;
        $this->token = $rec['token'] ? $rec['token'] : null;
    }

    public static function fromModel($rec)
    {
        // error_log("fromModel: " . json_encode($rec), 0);
        $rec1['id'] = $rec->id;
        $rec1['email'] = $rec->email;
        $rec1['name'] = $rec->name;
        $rec1['expires'] = $rec->expires;
        $rec1['token'] = $rec->token;
        $new = new static($rec1);
        return $new;
    }

    public function id()
    {
        return $this->id;
    }
    public function expires($display = false)
    {
        if ($display === true) {
            return gmdate('Y-m-d H:i:s', $this->expires);
        } else {
            return $this->expires;
        }
    }
    public function token()
    {
        return $this->token;
    }

    // public function email($display = false)
    // {
    //     if ($display === true) {
    //         return $this->client_id;
    //     } else {
    //         return $this->client_id;
    //     }
    // }

    public function toString($pretty = false)
    {
        if ($pretty === true)
            return json_encode(get_object_vars($this), JSON_PRETTY_PRINT);

        return json_encode(get_object_vars($this));
    }
}
