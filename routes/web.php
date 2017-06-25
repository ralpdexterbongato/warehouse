<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::resource('/','ItemsController');
Route::get('/SearchByItemCode','ItemsController@searchByItemCode')->name('search.code');
Route::get('/MCT-add','MCTController@MCTIndex')->name('mct.control');
Route::get('/findMasterItem','ItemsController@searchItemMaster')->name('searchItemMaster');
Route::post('/sessionMCTitem','MCTController@addingSessionItem')->name('selecting.item');
Route::DELETE('/removeSessions/{id}','MCTController@deletePartSession')->name('delete.session');
Route::post('mirs-printable','MCTController@StoringMIRS')->name('mirs.printable');
Route::get('download-pdf','PDFController@pdf');
