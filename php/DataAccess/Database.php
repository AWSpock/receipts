<?php

require_once(__DIR__ . '/DatabaseException.php');

class DatabaseV2
{
    private $conn;
    protected $file_dir;
    protected $sql;
    protected $params;
    protected $stmt;

    public function __construct($env = null)
    {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        if (!isset($env))
            $env = parse_ini_file(__DIR__ . '/.env');

        try {
            $this->conn = new mysqli($env["DB_HOST"], $env["DB_USER"], $env["DB_PASS"], $env["DB_NAME"]);
        } catch (mysqli_sql_exception $e) {
            throw new DatabaseException("Database connection failed: " . $e->getMessage(), $e->getCode(), $e);
        }

        if (array_key_exists("FILE_DIR", $env))
            $this->file_dir = $env["FILE_DIR"];
    }

    public function file_dir()
    {
        return $this->file_dir;
    }

    public function set_sql($sql)
    {
        $this->sql = trim(preg_replace('/\s\s+/', ' ', $sql));
        // error_log("Query: " . $this->sql, 0);
    }

    public function prepare()
    {
        try {
            $this->stmt = $this->conn->prepare($this->sql);
            if ($this->stmt === false) {
                throw new DatabaseException("Failed to prepare statement for query: $this->sql");
            }
        } catch (mysqli_sql_exception $e) {
            throw new DatabaseException("Query failed: " . $e->getMessage(), $e->getCode(), $e, $this->sql);
        }
    }

    public function execute($params = [], $types = "")
    {
        try {
            if (empty($params)) {
                $result = $this->conn->query($this->sql);
                return $result;
            }

            if (!empty($types)) {
                $this->stmt->bind_param($types, ...$params);
            }

            $this->stmt->execute();

            // for select, return result set
            if (stripos($this->sql, "SELECT") === 0) {
                return $this->stmt->get_result();
            }

            // for update or delete, return affected rows
            if (stripos($this->sql, "UPDATE") === 0 || stripos($this->sql, "DELETE") === 0) {
                return $this->stmt->affected_rows;
            }

            // for insert, return insert id or TRUE if no id
            if (stripos($this->sql, "INSERT") === 0) {
                return $this->conn->insert_id === 0 ? true : $this->conn->insert_id;
            }

            // unknown
            return "IDK";
        } catch (mysqli_sql_exception $e) {
            throw new DatabaseException("Query failed: " . $e->getMessage(), $e->getCode(), $e, $this->sql);
        }
    }

    public function query($sql, $params = [], $types = "")
    {
        try {
            $this->set_sql($sql);

            if (!empty($params)) {
                $this->prepare();
            }

            // if (!empty($types)) {
            //     $this->stmt->bind_param($types, ...$params);
            // }

            // $this->stmt->execute();

            // // for select, return result set
            // if (stripos($this->sql, "SELECT") === 0) {
            //     return $this->stmt->get_result();
            // }

            // // for update, return affected rows
            // if (stripos($this->sql, "UPDATE") === 0 || stripos($this->sql, "DELETE") === 0) {
            //     return $this->stmt->affected_rows;
            // }

            // // for update, return insert id or TRUE if no id
            // if (stripos($this->sql, "INSERT") === 0) {
            //     return $this->conn->insert_id === 0 ? true : $this->conn->insert_id;
            // }

            // // unknown
            // return "IDK";

            return $this->execute($params, $types);
        } catch (mysqli_sql_exception $e) {
            throw new DatabaseException("Query failed: " . $e->getMessage(), $e->getCode(), $e, $this->sql);
        }
    }

    public function beginTransaction()
    {
        try {
            $this->conn->begin_transaction();
        } catch (mysqli_sql_exception $e) {
            throw new DatabaseException("Failed to start transaction" . $e->getMessage(), $e->getCode(), $e);
        }
    }
    public function commit()
    {
        try {
            $this->conn->commit();
        } catch (mysqli_sql_exception $e) {
            throw new DatabaseException("Failed to commit transaction" . $e->getMessage(), $e->getCode(), $e);
        }
    }
    public function rollback()
    {
        try {
            $this->conn->rollback();
        } catch (mysqli_sql_exception $e) {
            throw new DatabaseException("Failed to rollback transaction" . $e->getMessage(), $e->getCode(), $e);
        }
    }

    public function getConnection()
    {
        return $this->conn;
    }

    public function close()
    {
        if ($this->conn) {
            $this->conn->close();
        }
    }
}
