<?php
class BASE_DTO {
    public
        $first_count, $second_count, $third_count, $fourth_count,
        $fifth_count, $sixth_count,
        $result, $return_body, $total_count;

    function set_value($data, $is_card) {
        $first = 0;
        $second = 0;
        $third = 0 ;
        $fourth = 0;
        $fifth = 0;
        $sixth = 0;

        if ($is_card) {
            $items = array( 'first' => array(), 'second' => array(), 'third' => array(), 'fourth' => array(), 'fifth' => array(), 'sixth' => array());
            for($i = 0; $i < count($data); $i++) {
                if ($i % 6 == 1) {
                    array_push($items['second'], ($data[$i]));
                    $second++;
                } else if ($i % 6 == 2) {
                    array_push($items['third'], ($data[$i]));
                    $third++;
                } else if ($i % 6 == 3) {
                    array_push($items['fourth'], ($data[$i]));
                    $fourth++;
                } else if ($i % 6 == 4) {
                    array_push($items['fifth'], ($data[$i]));
                    $fifth++;
                } else if ($i % 6 == 5) {
                    array_push($items['sixth'], ($data[$i]));
                    $sixth++;
                } else if ($i % 6 == 0) {
                    array_push($items['first'], ($data[$i]));
                    $first++;
                }

            }
        }

        $this->result = TRUE;
        $this->return_body = $is_card ? $items : $data;
        $this->total_count = count($data);
        $this->first_count = $first;
        $this->second_count = $second;
        $this->third_count = $third;
        $this->fourth_count = $fourth;
        $this->fifth_count = $fifth;
        $this->sixth_count = $sixth;
    }
}     
