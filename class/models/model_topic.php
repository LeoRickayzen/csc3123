<?php
/**
 * A model class for the RedBean object area
 *
 * @author Leo Rickayzen <l.rickayzen1@newcastle.ac.uk>
 * @copyright 2016 Newcastle University
 *
 */
/**
 * A class implementing a RedBean model for area beans
 */
    class Model_Area extends RedBean_SimpleModel
    {
    	public function getAllAreas(){
    		$areas = R::findAll("area");
    		return $areas;
    	}

    	public function getArea($area){
    		$areas = R::findOne("area", "name = '" . $area . "'");
    		return $areas;
    	}
   
    }
?>