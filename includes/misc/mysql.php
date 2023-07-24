<?php

namespace misc\mysql;

function query($query, $args = [], $types = null)
{
        error_reporting(0);

        static $connection = null;

        global $mysqlRequireSSL;
        global $databaseHost;
        global $databaseUsername;
        global $databasePassword;
        global $databaseName;

        if (!$connection) {
                $connection = new \mysqli();

                if ($mysqlRequireSSL) {
                        $connection->ssl_set(NULL, NULL, "/etc/ssl/certs/ca-bundle.crt", NULL, NULL);
                }

                $connection->real_connect($databaseHost, $databaseUsername, $databasePassword, $databaseName);

                if (!$connection)
                        die($connection->connect_error);

                $connection->set_charset('utf8');
        }

        if ($types === null && $args !== [])
                $types = str_repeat('s', count($args)); // unless otherwise specified, set type to string

        $stmt = $connection->prepare($query);

        if (!$stmt)
                die($connection->error);

        if (strpos($query, '?') !== false)
                $stmt->bind_param($types, ...$args);

        $stmt->execute();

        $query = new \stdClass();
        $query->result = $stmt->get_result();
        $query->num_rows = $query->result->num_rows;
        $query->affected_rows = $stmt->affected_rows;

        $stmt->close();

        return $query;
}
