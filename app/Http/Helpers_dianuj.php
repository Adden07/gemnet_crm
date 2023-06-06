<?php

use Illuminate\Support\Facades\Storage;

if (!function_exists('get_full_time')) {
    function get_full_time($date, $format = 'd, M Y @ h:i A')
    {
        $new_date = new \DateTime($date);
        return $new_date->format($format);
    }
}

if (!function_exists('get_complete_time')) {
    function get_complete_time($date)
    {
        return get_full_time($date);//. ' ' . config('app.timezone_name');
    }
}

if (!function_exists('get_date')) {
    function get_date($date)
    {
        return get_full_time($date, 'd, M Y');
    }
}

if (!function_exists('get_price')) {
    function get_price($price, $symbol = null)
    {
        // $price = str_replace(',', '', $price);
        return  '$' . number_format($price, 2);
    }
}

if (!function_exists("newCount")) {

    function newCount($array)
    {
        if (is_array($array) || is_object($array)) {
            return count($array);
        } else {
            return 0;
        }
    }
}


if (!function_exists('get_title')) {
    function get_title($str)
    {
        return ucwords(str_replace('_', ' ', $str));
    }
}

if (!function_exists('dummy_image')) {
    function dummy_image($type = null)
    {
        switch ($type) {
            case 'categories':
                return get_asset('admin_assets/images/category_dummy.jpg');
            case 'user':
                return get_asset('frontend_assets/images/dummy_user.png');
            case 'blog':
                return get_asset('frontend_assets/images/dummy_blog.jpg');
            case 'shipment':
                return get_asset('frontend_assets/images/shipment_not_image_not_found.png');
            default:
                return get_asset('frontend_assets/images/dummy_user.png');
        }
    }
}

if (!function_exists('get_asset')) {
    function get_asset($file)
    {
        if (app()->environment() == 'production') {
            return config('app.cdn_url') . $file;
        }
        return asset($file);
    }
}

if (!function_exists('check_file')) {
    function check_file($file = null, $type = null, $diff_type_pic = null)
    {
        if ($file && Storage::has($file)) {
            return get_asset($file);
        } else {
            if($diff_type_pic != null){
                return check_file($diff_type_pic, $type);
            }
            return dummy_image($type);
        }
    }
}

if (!function_exists('dateTimeInterval')) {
    function dateTimeInterval($start, $end, $asArray = false, $format = 'Y-m-d', $interval = '1 day',  $arr_format = null, $separator = '|')
    {
        $begin = new DateTime($start);
        $end = new DateTime($end);
        if ($arr_format == null) {
            $arr_format = $format;
        }
        $data = array();
        for ($dt = $begin; $dt <= $end; $dt->modify($interval)) {
            $data[$dt->format($arr_format)] = (string) $dt->format($format);
        }
        if ($asArray) {
            return $data;
        } else {
            return implode($separator, $data);
        }
    }
}

if (!function_exists('hashids_encode')) {
    function hashids_encode($str)
    {
        return \Hashids::encode($str);
    }
}

if (!function_exists('hashids_decode')) {
    function hashids_decode($str)
    {
        try {
            return \Hashids::decode($str)[0];
        } catch (Exception $e) {
            return abort(404);
        }
    }
}

if (!function_exists('convertNumberToWord')) {
    function convertNumberToWord($num = false)
    {
        $num = str_replace(array(',', ' '), '', trim($num));
        if (! $num) {
            return false;
        }
        $num = (int) abs($num);
        $words = array();
        $list1 = array('', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine', 'ten', 'eleven',
                'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen', 'seventeen', 'eighteen', 'nineteen'
            );
        $list2 = array('', 'ten', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety', 'hundred');
        $list3 = array('', 'thousand', 'million', 'billion', 'trillion', 'quadrillion', 'quintillion', 'sextillion', 'septillion',
                'octillion', 'nonillion', 'decillion', 'undecillion', 'duodecillion', 'tredecillion', 'quattuordecillion',
                'quindecillion', 'sexdecillion', 'septendecillion', 'octodecillion', 'novemdecillion', 'vigintillion'
            );
        $num_length = strlen($num);
        $levels = (int) (($num_length + 2) / 3);
        $max_length = $levels * 3;
        $num = substr('00' . $num, -$max_length);
        $num_levels = str_split($num, 3);
        for ($i = 0; $i < count($num_levels); $i++) {
            $levels--;
            $hundreds = (int) ($num_levels[$i] / 100);
            $hundreds = ($hundreds ? ' ' . $list1[$hundreds] . ' hundred' . ' ' : '');
            $tens = (int) ($num_levels[$i] % 100);
            $singles = '';
            if ($tens < 20) {
                $tens = ($tens ? ' ' . $list1[$tens] . ' ' : '');
            } else {
                $tens = (int)($tens / 10);
                $tens = ' ' . $list2[$tens] . ' ';
                $singles = (int) ($num_levels[$i] % 10);
                $singles = ' ' . $list1[$singles] . ' ';
            }
            $words[] = $hundreds . $tens . $singles . (($levels && ( int ) ($num_levels[$i])) ? ' ' . $list3[$levels] . ' ' : '');
        } //end for loop
        $commas = count($words);
        if ($commas > 1) {
            $commas = $commas - 1;
        }
        return implode(' ', $words);
    }

    function bytesToGb($total_bytes){
        return number_format($total_bytes/pow(1024,3));
    }
}
