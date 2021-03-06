<?php

require_once 'Framework/Model.php';

/**
 * Class defining the Unit model for consomable module
 *
 * @author Sylvain Prigent
 */
class SeOrigin extends Model {

    public function createTable() {
        $sql = "CREATE TABLE IF NOT EXISTS `se_origin` (
		`id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) NOT NULL DEFAULT '',
                `display_order` int(11) NOT NULL DEFAULT 0,
                `id_space` int(11) NOT NULL DEFAULT 0,
		PRIMARY KEY (`id`)
		);";
        $this->runRequest($sql);
        $this->addColumn('se_origin', 'display_order', 'int(11)', 0);
    }
    
    public function getName($id){
        $sql = "SELECT name FROM se_origin WHERE id=?";
        $req = $this->runRequest($sql, array($id));
        if( $req->rowCount() > 0){
            $tmp = $req->fetch();
            return $tmp[0];
        }
        return "";
    }

    public function getIdFromName($name, $id_space){
        $sql = "SELECT id FROM se_origin WHERE name=? AND id_space=?";
        $req = $this->runRequest($sql, array($name, $id_space));
        if ($req->rowCount() > 0){
            $tmp = $req->fetch();
            return $tmp[0];
        }
        return 0;
    }
    
    /**
     * 
     * @param type $id
     * @param type $name
     */
    public function set($id, $name, $display_order, $id_space) {
        if ($id == 0) {
            $sql = "INSERT INTO se_origin (id, name, display_order, id_space) VALUES (?,?,?,?)";
            $this->runRequest($sql, array($id, $name, $display_order, $id_space));
        } else {
            $sql = "UPDATE se_origin SET name=?, display_order=?, id_space=? WHERE id=?";
            $this->runRequest($sql, array($name, $display_order, $id_space, $id));
        }
    }

    public function getAll($id_space) {
        $sql = "SELECT * FROM se_origin WHERE id_space=?";
        return $this->runRequest($sql, array($id_space))->fetchAll();
    }

    public function get($id) {
        $sql = "SELECT * FROM se_origin WHERE id=?";
        return $this->runRequest($sql, array($id))->fetch();
    }
    
    public function getForList($id_space){
        $sql = "SELECT * FROM se_origin WHERE id_space=? ORDER BY display_order";
        $data = $this->runRequest($sql, array($id_space))->fetchAll();
        $ids = array();
        $names = array();
        $ids[] = "";
        $names[] = "";
        foreach($data as $dat){
            $ids[] = $dat['id'];
            $names[] = $dat['name'];
        }
        return array('ids' => $ids, 'names' => $names);
    }

    /**
     * Delete a unit
     * @param number $id Unit ID
     */
    public function delete($id) {

        $sql = "DELETE FROM se_origin WHERE id = ?";
        $this->runRequest($sql, array($id));
    }

}
