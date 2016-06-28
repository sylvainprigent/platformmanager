<?php

require_once 'Framework/Routing.php';

class BookingRouting extends Routing{
    
    public function listRouts(){
        
        // config
        $this->addRoute("bookingconfig", "bookingconfig", "bookingconfig", "index", array("id_site", "id_area", "id_resource"), array("", "", ""));
        
        // add here the module routes
        $this->addRoute("booking", "booking", "booking", "index");
        
        $this->addRoute("bookingsettings", "bookingsettings", "bookingsettings", "index");
        $this->addRoute("bookingscheduling", "bookingscheduling", "bookingscheduling", "index");
        $this->addRoute("bookingpackages", "bookingpackages", "bookingpackages", "index");
        $this->addRoute("bookingsups", "bookingsups", "bookingsups", "index");
        $this->addRoute("bookingcolorcodes", "bookingcolorcodes", "bookingcolorcodes", "index");
        $this->addRoute("bookingcolorcodeedit", "bookingcolorcodeedit", "bookingcolorcodes", "edit", array("id"), array(""));
        $this->addRoute("bookingcolorcodedelete", "bookingcolorcodedelete", "bookingcolorcodes", "delete", array("id"), array(""));
        $this->addRoute("bookingblock", "bookingblock", "bookingblock", "index");
        
        $this->addRoute("bookingdayarea", "bookingdayarea", "booking", "dayarea");
        
        
    }
}