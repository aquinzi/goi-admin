<?php

/*
* Database
*/

namespace aq\db;

/**
 * Taken from https://phpdelusions.net/pdo/pdo_wrapper
 */
class Database {

    public $pdo;

    public function __construct($db, $username=NULL, $password=NULL, $host='127.0.0.1', $port=3306, $options = []) {
        
        $charset = 'utf8mb4';
        $default_options = [
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => true,  // porque sino da problemas con las vistas (?)
           // \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
        ];

        $options = array_replace($default_options, $options);
        $dsn = "mysql:host=$host;dbname=$db;port=$port;charset=$charset";

        try {
            $this->pdo = new \PDO($dsn, $username, $password, $options);
        } catch (\PDOException $e) {
            throw new \PDOException($e->getMessage(), (int)$e->getCode());
        }
    }


    public function run($sql, $args=NULL) {
        if (!$args) {
            return $this->pdo->query($sql);
        }

        /* NOTE:
        // insert
        pdo($pdo, "INSERT INTO users VALUES (null, ?,?,?)", [$name, $email, $password]);

        // named placeholders are also welcome though I find them a bit too verbose
        pdo($pdo, "UPDATE users SET name=:name WHERE id=:id", ['id'=>$id, 'name'=>$name]);
        */

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($args);
        return $stmt;
    }



}