<?php
class SelectBoxes{

     static function makeSelects($selects = array())
     {
        $select = new stdClass();
        foreach($selects as $k => $className){
            

            $selectOptions = array();
            $table = new $className();
            $option = $table->selectBoxes;
            $where = isset($option['where']) ? $option['where'] : '1=1';
            $order = isset($option['orderBy']) ? $option['orderBy'] : $option['displayField'];
            $limit = isset($option['limit']) ? $option['limit'] : null;
			
            if($option['firstOption']){
                $firstOption = $option['firstOption'] == 'blank' ? '' : $option['firstOption'];
                $selectOptions[""] = $firstOption;
            }
            $id = isset($option['id']) ? $option['id'] : 'id';
            foreach($table->fetchAll($where,$order, $limit) as $row):
                if(is_array($option))
                    $selectOptions[$row->$id] = $row->$option['displayField'];
            endforeach;
            $select->$option['varName'] = $selectOptions;
        }
        return $select;
    }


}
