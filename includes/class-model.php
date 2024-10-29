<?php

/**
 * Model Class
 *
 */
class AutoHadsModel {

    private $db;
    private $table_name;
    private $columns;
    private $primary_key;

    /**
     * Constructor
     *
     */
    public function AutoHadsModel($table_name,$collums,$key) {
        global $wpdb;
        $this->db = $wpdb;
        $this->table_name = $table_name;
        $this->columns = $collums;
        $this->primary_key = $key;
    }


    /**
     * Returns primary key
     *
     */
    public function get_primary_key() {
        return $this->primary_key;
    }

    /**
     * Returns table name
     * 
     */
    public function get_table_name() {
        return $this->table_name;
    }

    /**
     * Returns array of column names & types
     * 
     */
    public function get_columns() {
        return $this->columns;
    }

    /**
     * Select all data
     * 
     */
    public function select_all() {
        return $this->db->get_results("SELECT * FROM `$this->table_name`");
    }

    /**
     * Select certain data
     * 
     */
    public function select($key_word, $order_by, $order, $begin_row, $end_row) {

        $where_qry = $this->generate_where_query($key_word);
        $order_qry = $this->generate_order_query($order_by, $order);
        $sql = "SELECT * FROM `$this->table_name` $where_qry $order_qry LIMIT $begin_row, $end_row";

        return $this->db->get_results($sql);
    }

    /**
     * Returns total row count
     * 
     */
    public function count_rows($key_word = "") {

        $where_qry = $this->generate_where_query($key_word);
        $sql = "SELECT COUNT(*) FROM `$this->table_name` $where_qry";

        return $this->db->get_var($sql);
    }

    /**
     * Generates where sql query
     * 
     */
    private function generate_where_query($key_word) {
        $qry = "";
        if ($key_word != "") {
            $like_statements = array();
            foreach ($this->columns as $name) {
                $like_statements[] = $this->db->prepare(" `$name` LIKE '%%%s%%'", $key_word);
            }
            $qry = " WHERE " . implode(" OR ", $like_statements);
        }
        return $qry;
    }

    /**
     * Generates order by sql query
     * 
     */
    private function generate_order_query($order_by, $order) {
        $qry = "";
        if ($order_by != "") {
            $order = esc_sql($order);
            $order_by = esc_sql($order_by);
            $qry = " ORDER BY `$order_by` $order";
        }
        return $qry;
    }

    /**
     * Returns single row
     *
     */
    public function get_row($id) {
        global $wpdb;
        $sql = $wpdb->prepare("SELECT * FROM `$this->table_name` WHERE `$this->primary_key` = %d", $id);
        return $wpdb->get_row($sql);
    }

    /**
     * Adds new record
     *
     */
    public function insert($vals) {

        // collect insert values and strip slashes
        $insert_vals = array();
        foreach ($this->columns as $name) {
            $insert_vals[$name] = stripslashes_deep($vals[$name]);
        }
        // insert
        $id = $this->db->insert($this->table_name, $insert_vals);
        if ($id) {
            return $id;
        } else {
            return 0;
        }
    }

    /**
     * Updates record
     *
     */
    public function update($vals) {

        // collect update values and strip slashes
        $update_vals = array();
        foreach ($this->columns as $name) {
            $update_vals[$name] = stripslashes_deep($vals[$name]);
        }

        // update
        if ($this->db->update($this->table_name, $update_vals, array($this->primary_key => $vals[$this->primary_key]))) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Deletes record
     *
     */
    public function delete($id) {
        $sql = $this->db->prepare("DELETE FROM `$this->table_name` WHERE `$this->primary_key` = %d", $id);
        if ($this->db->query($sql)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Checks validity of a table
     *
     */
    public function validate($table_name) {

        // gather column information
        $this->db->get_results("SHOW KEYS FROM `$table_name` WHERE `Key_name` = 'PRIMARY'");
        $num_of_pks = $this->db->num_rows;
        $results = $this->db->get_results("SHOW FIELDS FROM `$table_name`");

        // verify errors if any
        $err_msg = "";
        if (1 < $num_of_pks) {
            $err_msg = "Error: table $table_name has multiple primary keys";
        } else if ($results[0]->Key != 'PRI') {
            $err_msg = "Error: table $table_name's primary key is not set at first column";
        } else if (!stristr($results[0]->Type, 'int')) {
            $err_msg = "Error: table $table_name's primary key is not an int data type";
        }

        return $err_msg;
    }


}
