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
Route::get('/register','AccountController@GetRegister')->name('Registration');
Route::get('/settings-accounts-list','AccountController@GMAccountsList')->name('AccountsListGM');
Route::get('/settings-admin-list','AccountController@AdminAccountslist')->name('Admin-list');
Route::get('/settings-other-acc','AccountController@otheraccountslist')->name('otherAccounts');
Route::get('/settings-manager-accounts-list','AccountController@ManagerAccountsList')->name('AccountlistManagers');
Route::get('/get-general-managers','AccountController@getallGMAccounts')->name('getGMaccts');
Route::get('/get-all-managers','AccountController@getallManagers');
Route::get('/getallAdmin','AccountController@getallAdmin');
Route::get('/get-other-accounts','AccountController@getOtherAccounts');
Route::get('/show-my-history','AccountController@ShowMyHistoryPage')->name('ViewMyHistory');
Route::get('/search-my-mirs-history','AccountController@MyMIRSHistoryandSearch');
Route::get('/search-my-mct-history','AccountController@MyMCTHistoryandSearch');
Route::post('/registerStore','AccountController@registrationStore')->name('StoreRegister');
Route::get('/login','AccountController@loginpage')->name('login')->middleware('guest');
Route::post('/logout','AccountController@logoutAccount')->name('Logging.out');
Route::post('/login-submit','AccountController@loginSubmit')->name('login-submit');
Route::resource('/','ItemsController');
Route::get('/SearchByItemCode','ItemsController@searchByItemCode')->name('search.code');
Route::get('/MIRS-add','MIRSController@MIRScreate')->name('mirs.add');
Route::get('/findMasterItem','ItemsController@searchItemMaster')->name('searchItemMaster');
Route::get('/fetchSessionMIRS','MIRSController@fetchSessionMIRS');
Route::post('/sessionMIRSitem','MIRSController@addingSessionItem')->name('selecting.item');
Route::delete('/removeSessions/{id}','MIRSController@deletePartSession')->name('delete.session');
Route::post('/mirs-storedata','MIRSController@StoringMIRS')->name('mirs.store');
Route::post('download-pdf','PDFController@mirspdf')->name('mirs-download');
Route::post('/download-mctsummary-print','PDFController@MCTsummaryprint')->name('mct-summary-print');
Route::get('previewFullMIRS/{id}','MIRSController@fullMIRSview')->name('full-mirs');
Route::post('/deniedmirs/{id}','MIRSController@DeniedMIRS')->name('DeniedMIRS');
Route::post('MCTstore','MCTController@StoreMCT')->name('Storing.MCT');
Route::get('MCTpreview/{id}','MCTController@previewMCT')->name('previewMCT');
Route::get('create-mct/{MIRSNo}','MCTController@CreateMCT')->name('toRecordingMCT.Page');
Route::post('mct-session-saving','MCTController@MCTSessionSaving')->name('sessionSaveMCT');
Route::delete('delete-session-mct/{id}','MCTController@deleteASession')->name('delete.a.session.mct');
Route::post('mct-download','PDFController@mctpdf')->name('print-mct');
Route::get('/MCTofMIRS/{id}','MCTController@MCTofMIRS')->name('MCTofMIRS');
Route::get('MRT-create','MRTController@CreateMRT')->name('create.mrt');
Route::post('MRT-store','MRTController@StoreMRT')->name('storing.mrt');
Route::post('MRT-session','MRTController@addToSession')->name('Session.addings');
Route::delete('MRT-delete/{id}','MRTController@deletePartSession')->name('mrtSession.deleting');
Route::get('MIRS-index','MIRSController@Indexgrid')->name('MIRSgridview');
Route::get('findmirs','MIRSController@searchMIRSNo')->name('finding.mirs');
Route::get('summary-mrt','MRTController@summaryMRT')->name('summary.mrt');
Route::get('mrt-find-date','MRTController@MRTSearchdate')->name('mrt.summary.find');
Route::get('mrt-print-summary','PDFController@mrtpdf')->name('mrt-summary-print');
Route::get('mrt-viewer/{id}','MRTController@mrtviewing')->name('mrt-viewer');
Route::get('mct-summary','MCTController@summaryMCT')->name('mct-summary');
Route::post('MIRS-Signature','MIRSController@MIRSSignature')->name('MIRSSign');
Route::get('/Items-ByDescription','ItemsController@ItemMasterbyDescription')->name('ItemsearchDescription');
Route::post('Signature-for-mct','MCTController@SignatureMCT')->name('MCTsignature');
Route::get('mirs-signature-list','MIRSController@mirsRequestcheck')->name('checkmyMIRSrequest');
Route::get('mct-signature-request','MCTController@mctRequestcheck')->name('checkmyMCTrequest');
Route::get('ready-mirs','MIRSController@readyForMCT')->name('mirs-ready')->middleware('IsWarehouse');
Route::put('mirs-signature-if-gm-isabsent/{id}','MIRSController@ApproveMIRSinBehalf')->name('GmIsAbsent');
Route::put('cancel-request-toadmin/{id}','MIRSController@CancelApproveMIRSinBehalf')->name('cancel-request-toadmin');
Route::put('deny-manager-request-mirs/{id}','MIRSController@DenyRequestofManagerMIRS')->name('denyManagerRequest.MIRS');
Route::put('confirm-manager-toreplace-gm-signature/{id}','MIRSController@letManagerSignatureGM')->name('letManagerApprove');
Route::get('mct-summary-search','MCTController@searchMCTsummary')->name('mct-search-date');
Route::delete('DeleteSession-RR/{id}','RRController@deleteSessionStored')->name('RRDeleteSession');
Route::get('RRsearchitembyCode','RRController@searchbyItemMasterCode')->name('RRSearchItemCode');
Route::get('RRsearchitembydescription','RRController@ItemMasterbyDescription')->name('SearchbyDescriptionRR');
Route::post('/rr-storing-session-no-po','RRController@StoreSessionRRNoPO')->name('storeSessionRR');
Route::post('/rr-storing-session-with-po','RRController@StoreSessionRRWithPO');
Route::get('/show-rr-session-data','RRController@showSessionRRData')->name('showing.currentsession.rr');
Route::post('/save-rr-no-po-to-db','RRController@StoreRRtoTableNoPO')->name('SaveRRNoPO');
Route::post('/save-rr-with-po-to-db','RRController@StoreRRtoTableWithPO')->name('SaveRRWithPO');
Route::get('displayRRcreateSession','RRController@displayRRcurrentSession');
Route::get('RR-index','RRController@RRindex')->name('RRindexview');
Route::get('/RR-fullpreview/{id}','RRController@previewRR')->name('RRfullpreview');
Route::post('RR-signature','RRController@signatureRR')->name('RRsigning');
Route::get('RR-search-byRRNo','RRController@RRindexSearchbyRRNo')->name('RRSearchNo');
Route::get('checkout-rr-request','RRController@RRsignatureRequest')->name('checkmyRRrequest');
Route::POST('printRRpdf','PDFController@rrdownload')->name('RR-printing');
Route::post('decline-this-RR','RRController@declineRR')->name('RRdecline');
Route::get('create-rr-wo-po/{id}','RRController@CreateRRNoPO')->name('CreatingRR.without.po')->middleware('IfAlreadyHavePO');
Route::get('/create-rr-w-po/{id}','RRController@CreateRRWithPO')->name('CreateingRR.with.po');
Route::get('/rr-of-rv-list/{id}','RRController@RRofRVlist')->name('showRR-ofRV');
Route::get('RV-create','RVController@RVcreate')->name('Creating.RV');
Route::post('SessionSave','RVController@SaveSession')->name('SavingSessionRV');
Route::delete('DeleteSession/{id}','RVController@DeleteSession')->name('DeletingSessionRV');
Route::post('SavetoDBRV','RVController@savingToTable')->name('SavingRVtoDB');
Route::get('RVindex','RVController@RVindexView')->name('RVindexView');
Route::get('indexRVVUE','RVController@RVIndexSearch');
Route::get('RVfullview/{id}','RVController@RVfullPreview')->name('RVfullpreviewing');
Route::post('RVsignature','RVController@Signature')->name('SignatureThisRV');
Route::get('myRVrequest','RVController@RVrequest')->name('MyRVrequestlist');
Route::put('declineRV/{id}','RVController@declineRV')->name('DeclineRV');
Route::get('search-rv','RVController@searchRV')->name('SearchingRV');
Route::get('search-rv-forstock','RVController@searchRVforStock')->name('SearchDescriptionRVstock')->middleware('IsWarehouse');
Route::post('addtoStockSession','RVController@addtoStockSession')->name('AddRVforStockSession')->middleware('IsWarehouse');
Route::post('RVdownload','PDFController@RVdownload')->name('downloadRVprint');
Route::put('rv-signature-in-behalf/{id}','RVController@SignatureApproveInBehalf')->name('sending.approvebehalf.admin');
Route::put('rv-signature-in-behalf-cancel/{id}','RVController@SignatureApproveInBehalfCancel')->name('cancel-rv-approve-inbehalf');
Route::put('rv-signature-behalf-denied-byadmin/{id}','RVController@SignatureBehalfDenybyadmin')->name('rv-behalf-denied.byadmin')->middleware('IsAdmin');
Route::put('rv-approve-behalf-confirm/{id}','RVController@confirmSignatureBehalf')->name('ConfirmSignatureinBehalfRV');
Route::get('CanvassCreate/{id}','CanvassController@TocanvassPage')->name('TocanvassPage')->middleware('IfAlreadyHaveRR');
Route::get('/canvass-suppliers/{id}','CanvassController@getSupplierRecords');
Route::delete('/deleteCanvassRecord/{id}','CanvassController@deleteCanvassRecord');
Route::post('supplier-save-canvass','CanvassController@saveCanvass')->name('SupplierCanvasSaving');
Route::post('generate-po','POController@GeneratePOfromCanvass')->name('generatePO')->middleware('IsWarehouse');
Route::get('/search-supplier/{id}','CanvassController@searchSupplier')->name('search.supplier');
Route::put('/update-canvass/{id}','CanvassController@canvassUpdate');
Route::get('po-list-view/of-rv/{id}','POController@POListView')->name('POListView');
Route::get('po-full-preview/{id}','POController@POFullpreview')->name('POFullView');
Route::post('gm-signature-po','POController@GMSignaturePO')->name('GMSignatureOrder');
Route::post('gm-decline-po','POController@GMDeclined')->name('GMDeclining');
Route::get('my-PO-request','POController@MyPOrequestlist')->name('viewPOrequest')->middleware('IsGM','auth');
Route::put('signature-po-inbehalf/{id}','POController@POAuthorizeInBehalf')->name('AuthorizedInBehalf');
Route::put('cancel-authorize-inbehalf/{id}','POController@CancelAuthorizeInBehalf')->name('AuthorizeInBehalfCancel');
Route::put('authorize-in-behalf-confirmed/{id}','POController@AuthorizeInBehalfconfirmed')->name('authorizeinbehalf-confirm')->middleware('IsAdmin');
Route::get('waiting-to-be-purchased-rv','RVController@UnpurchaseList')->name('pending-purchase-rv');
Route::put('declined-Authorize-inbehalf/{id}','POController@AuthorizeInBehalfdeclined')->name('autorizeInBehalf.denybyadmin')->middleware('IsAdmin');
Route::put('update-budget/{id}','RVController@updateBudgetAvailable')->name('Update.budget');
Route::post('po-download-print','PDFController@POdownload')->name('downloadPO');
Route::post('save-mr','MRController@SaveMR')->name('saveMR');
Route::get('view-list-MR-of-RR/{id}','MRController@MRofRRlist')->name('ViewMR.ofRR');
Route::get('full-preview-MR/{id}','MRController@previewFullMR')->name('fullMR');
Route::post('mr-print','PDFController@MRprinting')->name('printMR');
Route::get('create-mr/{id}','MRController@createMR')->name('create-mr');
Route::post('addSession-MR','MRController@addSessionForMR');
Route::get('displaySessionMR','MRController@displayMRSessions');
Route::delete('deletemrSession/{id}','MRController@removingSession');
Route::post('signature-MR','MRController@SignatureMR')->name('SignatureMR');
Route::post('Decline-MR','MRController@DeclineMR')->name('DeclineMR');
Route::get('my-mr-request','MRController@myMRrequest')->name('myMR.signature.Request')->Middleware('IsManagerOrGM');
Route::put('mr-approve-inbehalf/{id}','MRController@approveinBehalfMRsent')->name('ApproveMR.inbehalf');
Route::put('mr-approve-inbehalf-canceled/{id}','MRController@approveinBehalfcancel')->name('ApproveinBehalfCancel');
Route::put('mr-approve-inbehalf-deny/{id}','MRController@denyApproveinBehalf')->name('DenyApproveinBehalf')->middleware('IsAdmin');
Route::put('confirmApproveinBehalf/{id}','MRController@confirmApproveinBehalf')->name('confirmApproveInBehalf')->middleware('IsAdmin');
Route::get('MIRS-gm-signature-replace-request','GMSignatureReplaceController@ViewAllReplaceSignatureRequest')->name('view.request.gm-signature.replace');
