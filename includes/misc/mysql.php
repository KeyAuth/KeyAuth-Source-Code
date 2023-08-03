<?php

namespace misc\mysql;

function query($query, $args = [], $types = null)
{
        static $connection = null;
        static $readOnlyConnection = null;

        global $mysqlRequireSSL;
        global $databaseHost;
        global $databaseUsername;
        global $databasePassword;
        global $databaseName;
        global $readOnlyRegions;

        // use read-only database replica, for lower latency in regions far away from the primary database region
        if(strtolower(substr($query, 0, 6)) == "select" && isset($readOnlyRegions[$_SERVER['SERVER_ADDR']])) {
                if (!$readOnlyConnection) {
                        $readOnlyConnection = new \mysqli();

                        if ($mysqlRequireSSL) {
                                $readOnlyConnection->ssl_set(NULL, NULL, "/etc/ssl/certs/ca-bundle.crt", NULL, NULL);
                        }

                        $readOnlyConnection->real_connect($readOnlyRegions[$_SERVER['SERVER_ADDR']]["host"], $readOnlyRegions[$_SERVER['SERVER_ADDR']]["username"], $readOnlyRegions[$_SERVER['SERVER_ADDR']]["password"], $databaseName);

                        if (!$readOnlyConnection)
                                die($readOnlyConnection->connect_error);

                        $readOnlyConnection->set_charset('utf8');
                }
                $connectionToUse = $readOnlyConnection;
        }
        else {
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
                $connectionToUse = $connection;
        }

        if ($types === null && $args !== [])
                $types = str_repeat('s', count($args)); // unless otherwise specified, set type to string

        $stmt = $connectionToUse->prepare($query);

        if (!$stmt)
                die($connectionToUse->error);

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
