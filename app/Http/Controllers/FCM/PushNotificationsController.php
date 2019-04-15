<?php

namespace App\Http\Controllers\FCM;

use Edujugon\PushNotification\PushNotification;
use App\Http\Controllers\Controller;

class PushNotificationsController extends Controller
{
    public function sendNotification($employee, $visitor, $deviceToken)
    {
        $push = new PushNotification('fcm');
        $push->setMessage(['data' => [
            'body' => 'Hi ' . $employee->name . ', you have a new visitor by the name ' . $visitor->fname . ' ' . $visitor->lname,
            "title" => 'You have a visitor']
        ])
            ->setDevicesToken($deviceToken)
            ->setApiKey('AAAA6Jrh4p0:APA91bHIMDaoYucdId8_eJs6rumKpsVFAQQ3q4IiU4tvfSJn2DX2d1Stw9cUc8vZPufuBF75UWI2M1mha2zgEACMs06x_x3uYOjTeCjGoerGnX5kXBp3Fy0YvVKfo4FFaEk5_xSutkAG')
            ->setConfig(['dry_run' => false])
            ->sendByTopic('visitor')
            ->setDevicesToken([$deviceToken])
            ->send();

       
    }
    
      public function getNotification($applicant, $reliever, $deviceToken)
    {
        $push = new PushNotification('fcm');
        $push->setMessage(['data' => [
            'body' => 'Hi ' . $applicant->employee_no . ' ' . $reliever . 'approved your reliever request' ,
            "title" => 'Leave approval report']
        ])
            ->setDevicesToken($deviceToken)
            ->setApiKey('AAAA6Jrh4p0:APA91bHIMDaoYucdId8_eJs6rumKpsVFAQQ3q4IiU4tvfSJn2DX2d1Stw9cUc8vZPufuBF75UWI2M1mha2zgEACMs06x_x3uYOjTeCjGoerGnX5kXBp3Fy0YvVKfo4FFaEk5_xSutkAG')
            ->setConfig(['dry_run' => false])
            ->sendByTopic('visitor')
            ->setDevicesToken([$deviceToken])
            ->send();
    }

    public function test()
    {

        $push = new PushNotification('fcm');
        $push->setMessage(['data' => [
            "body" => "Api backend",
            "title" => "Backend says bye"],
            'notification' => [
            "body" => "Api backend",
            "title" => "Backend says bye"]
        ])
            ->setApiKey('AAAA6Jrh4p0:APA91bHIMDaoYucdId8_eJs6rumKpsVFAQQ3q4IiU4tvfSJn2DX2d1Stw9cUc8vZPufuBF75UWI2M1mha2zgEACMs06x_x3uYOjTeCjGoerGnX5kXBp3Fy0YvVKfo4FFaEk5_xSutkAG')
            ->setConfig(['dry_run' => false])
            ->sendByTopic('dogs')
            ->setDevicesToken(['d8a3f8srpRs:APA91bHEV1eugazNa7HvdIyzaGVmnjmq54xvOJtvoxiMpiJlXyAcTqzcQAdwKwiBf9-XbeOYBAGjHezXMT86oWhNLBj30eEx2I7ejsXhtgue3W7Bea-18tQ5zJuzbU_3vhpRGUSYB_HU'])
            ->send();
    return "success";
    }
}
