<?php

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('MIRSChannel.{NotifyName}', function ($newmirs) {
 return Auth::check();
});

Broadcast::channel('WarehouseRole', function ($role) {
    return Auth::check();
});

Broadcast::channel('MCTchannel.{ReceiverName}', function ($ReceiverName) {
    return Auth::check();
});
Broadcast::channel('MRTchannel.{notifythis}', function ($notifythis) {
    return Auth::check();
});
Broadcast::channel('RVchannel.{notifythisname}', function ($NotifableName) {
    return Auth::check();
});
Broadcast::channel('NewRVApprovedchannel', function ($notifyWarehouse) {
    return Auth::check();
});
Broadcast::channel('POchannel.{ApproveName}', function ($NotifyName) {
    return Auth::check();
});
Broadcast::channel('RRchannel.{Notifyname}', function ($nameToNotify) {
    return Auth::check();
});
Broadcast::channel('MRchannel.{notifythis}', function ($notifythis) {
    return Auth::check();
});
