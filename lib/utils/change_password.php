<?php

if(!defined('ABSPATH')) {
    exit;
}

class ChangePassword {
    public function new_random_password() {
        $numbers = range(1, 9);
        shuffle($numbers);
        $chooseNumbers = array_slice($numbers, 0, 6);
        $password = implode('', $chooseNumbers) . ucfirst(strtolower(get_bloginfo())) . '!';
        return $password;
    }
}