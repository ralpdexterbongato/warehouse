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
Route::get('/register','LoginController@GetRegister')->name('Registration');
Route::post('/registerStore','LoginController@registrationStore')->name('StoreRegister');
Route::get('/login','LoginController@loginpage')->name('login')->middleware('guest');
Route::post('/logout','LoginController@logoutAccount')->name('Logging.out');
Route::post('/login-submit','LoginController@loginSubmit')->name('login-submit');
Route::resource('/','ItemsController');
Route::get('/SearchByItemCode','ItemsController@searchByItemCode')->name('search.code');
Route::get('/MIRS-add','MIRSController@MIRScreate')->name('mirs.add');
Route::get('/findMasterItem','ItemsController@searchItemMaster')->name('searchItemMaster');
Route::post('/sessionMIRSitem','MIRSController@addingSessionItem')->name('selecting.item');
Route::DELETE('/removeSessions/{id}','MIRSController@deletePartSession')->name('delete.session');
Route::post('mirs-storedata','MIRSController@StoringMIRS')->name('mirs.store');
Route::post('download-pdf','PDFController@pdf')->name('mirs-download');
Route::get('search-mirs','MIRSController@fullMIRSNo')->name('full-mirs');
Route::post('denied','MIRSController@DeleteDenied')->name('DeleteDenied');
Route::post('MCTstore','MCTController@StoreMCT')->name('Storing.MCT');
Route::get('MCTpreview','MCTController@previewMCT')->name('previewMCT');
Route::post('mct-download','PDFController@mctpdf')->name('print-mct');
Route::get('MRT-create','MRTController@CreateMRT')->name('create.mrt');
Route::post('MRT-store','MRTController@StoreMRT')->name('storing.mrt');
Route::post('MRT-session','MRTController@addToSession')->name('Session.addings');
Route::delete('MRT-delete/{id}','MRTController@deletePartSession')->name('mrtSession.deleting');
Route::get('MIRS-index','MIRSController@Indexgrid')->name('MIRSgridview');
Route::get('findmirs','MIRSController@searchMIRSNo')->name('finding.mirs');
Route::get('summary-mrt','MRTController@summaryMRT')->name('summary.mrt');
Route::get('mrt-find-date','MRTController@MRTSearchdate')->name('mrt.summary.find');
Route::get('mrt-print-summary','PDFController@mrtpdf')->name('mrt-summary-print');
Route::get('mrt-viewer','MRTController@mrtviewing')->name('mrt-viewer');
Route::get('mct-summary','MCTController@summaryMCT')->name('mct-summary');
Route::post('MIRS-Signature','MIRSController@MIRSSignature')->name('MIRSSign');
Route::post('Items-ByDescription','ItemsController@ItemMasterbyDescription')->name('ItemsearchDescription');
Route::post('Signature-for-mct','MCTController@SignatureMCT')->name('MCTsignature');
