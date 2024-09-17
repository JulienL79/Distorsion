<?php

class Utilities {
    
    static public function getInitials($name) {
        $words = explode(' ', $name);
        $initials = '';
        foreach ($words as $word) {
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
            }
            if (strlen($initials) >= 2) {
                break;
            }
        }
        return $initials;
    }
}


?>