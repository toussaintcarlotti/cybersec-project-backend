<?php

namespace Src\controllers;

require_once "src/models/Invitation.php";
use Src\models\Invitation;

class InvitaionController
{
    public static function store($from, $to){

        $invitation = new Invitation();
        if ($from !== $to){
            $res = $invitation->create($from, $to);
        }else{
            $res = [
                'status' => 400,
                'message' => "Impossible"
            ];
        }
        echo json_encode($res);
    }

    public static function getAll($token){

        $invitation = new Invitation();
        $res = $invitation->getAll($token);

        echo json_encode($res);
    }

    public static function destroy($from, $to){

        $invitation = new Invitation();
        $res = $invitation->delete($from, $to);

        echo json_encode($res);
    }
}