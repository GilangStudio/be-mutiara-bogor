<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;

class EmailService {
    public static function send($to, $subject, $view, $data) {
        try {
            Mail::send($view, $data, function($message) use ($to, $subject) {
                $message->to($to)->subject($subject);
            });
        } catch (\Throwable $th) {
            return $th->getMessage();
        }

        return true;
    }
}