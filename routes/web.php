<?php
use App\News;
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
Auth::routes();

Route::get('/', function () {
    if(Auth::check()) {
        return redirect('/dashboard');
    } else {
        $news = News::all();
        return view('index',compact('news'));
    }
});

// Route::get('/index', function () {
//     $news = News::all();
//     return view('index',compact('news'));
// });


Route::any('clans', 'HomePageController@clans')->name("clans");

Route::any('download', 'HomePageController@download')->name("download");

Route::any('check', 'HomePageController@check')->name("check");

Route::get('/relic', function () {
    return view('relic');
});

Route::get('/li', function () {
    return view('li');
});

Route::get('/town', function () {
    return view('town');
});

Route::get('/verify/{nonce}', 'Auth\RegisterController@verifyUser');

Route::group(['middleware' => 'App\Http\Middleware\AdminMiddleware'], function()
    {
    Route::resource('Admin/Dashboard','Admin\DashboardController');

    Route::get('Admin/profile', 'Admin\ProfileController@profile');
    Route::post('Admin/profile/updateProfile/{id}', 'Admin\ProfileController@updateProfile')->name("Admin.profile.updateProfile");

    Route::get('Admin/Add/news', 'Admin\AddController@news');
    Route::get('Admin/Add/boost', 'Admin\AddController@boost');
    Route::get('Admin/Add/addboost', 'Admin\AddController@addboost');
    Route::get('Admin/Add/addItem', 'Admin\AddController@addItem');
    Route::post('Admin/Add/storeItem', 'Admin\AddController@storeItem')->name("Admin.Add.storeItem");
    Route::post('Admin/Add/storeBoost', 'Admin\AddController@storeBoost')->name("Admin.Add.storeBoost");
    Route::delete('Admin/Add/destroyBoost/{id}', 'Admin\AddController@destroyBoost')->name('Admin.Add.destroyBoost');
    Route::get('Admin/Add/editBoost/{id}', 'Admin\AddController@editBoost')->name("Admin.Add.editBoost");
    Route::post('Admin/Add/updateBoost/{id}', 'Admin\AddController@updateBoost')->name("Admin.Add.updateBoost");
    Route::get('Admin/Add/reward', 'Admin\AddController@reward');
    Route::get('Admin/Add/addreward', 'Admin\AddController@addreward');
    Route::get('Admin/Add/addDownload', 'Admin\AddController@addDownload');
    Route::get('Admin/Add/addnews', 'Admin\AddController@addnews');
    Route::get('Admin/Add/download', 'Admin\AddController@download');
    Route::get('Admin/Add/item', 'Admin\AddController@item');
    Route::get('Admin/Add/itemCheck', 'Admin\AddController@itemCheck')->name('Admin.Add.itemCheck');
    Route::post('Admin/Add/itemCheck', 'Admin\AddController@itemCheck');
    Route::get('Admin/Add/itemDetail/{id}', 'Admin\AddController@itemDetail')->name("Admin.Add.itemDetail");
    Route::post('Admin/Add/storeReward', 'Admin\AddController@storeReward')->name("Admin.Add.storeReward");
    Route::delete('Admin/Add/destroyReward/{id}', 'Admin\AddController@destroyReward')->name('Admin.Add.destroyReward');
    Route::get('Admin/Add/editReward/{id}', 'Admin\AddController@editReward')->name("Admin.Add.editReward");
    //Route::get('Admin/Add/editReward/{id}', 'Admin\AddController@editReward')->name("Admin.Add.editReward");
    Route::post('Admin/Add/updateReward/{id}', 'Admin\AddController@updateReward')->name("Admin.Add.updateReward");
    Route::post('Admin/Add/updateItemDetail/{id}', 'Admin\AddController@updateItemDetail')->name("Admin.Add.updateItemDetail");
    Route::post('Admin/Add/store', 'Admin\AddController@store')->name("Admin.Add.store");
    Route::get('Admin/Add/edit/{id}', 'Admin\AddController@edit')->name("Admin.Add.edit");
    Route::post('Admin/Add/update/{id}', 'Admin\AddController@update')->name("Admin.Add.update");
    Route::delete('Admin/Add/destroy/{id}', 'Admin\AddController@destroy')->name('Admin.Add.destroy');
    Route::delete('Admin/Add/destroyDownload/{id}', 'Admin\AddController@destroyDownload')->name('Admin.Add.destroyDownload');
    Route::post('Admin/Add/storeDownload', 'Admin\AddController@storeDownload')->name("Admin.Add.storeDownload");
    Route::post('Admin/Add/storeItemMall', 'Admin\AddController@storeItemMall')->name("Admin.Add.storeItemMall");
    Route::get('Admin/Add/editDownload/{id}', 'Admin\AddController@editDownload')->name("Admin.Add.editDownload");
    Route::post('Admin/Add/updateDownload/{id}', 'Admin\AddController@updateDownload')->name("Admin.Add.updateDownload");


    Route::get('Admin/Log/PurchaseItem', 'Admin\LogController@PurchaseItem');
    Route::get('Admin/Log/PurchaseCredit', 'Admin\LogController@PurchaseCredit');
    Route::get('Admin/Log/TransferCredit', 'Admin\LogController@TransferCredit');
    Route::get('Admin/Log/Unstuck', 'Admin\LogController@Unstuck');
    Route::get('Admin/Log/ChangeNickname', 'Admin\LogController@ChangeNickname');

    Route::get('Admin/List/player', 'Admin\ListController@player');
    Route::delete('Admin/List/destroy{id}', 'Admin\ListController@destroy')->name('Admin.List.destroy');
    Route::get('Admin/List/edit/{id}', 'Admin\ListController@edit')->name("Admin.List.edit");
    Route::post('Admin/List/update/{id}', 'Admin\ListController@update')->name("Admin.List.update");
    Route::get('Admin/List/item/{id}', 'Admin\ListController@item');
    Route::delete('Admin/List/destroyItemMall/{id}', 'Admin\ListController@destroyItemMall')->name('Admin.List.destroyItemMall');
    Route::get('Admin/List/editItemMall/{id}', 'Admin\ListController@editItemMall')->name("Admin.List.editItemMall");
    Route::post('Admin/List/updateItemMall/{id}', 'Admin\ListController@updateItemMall')->name("Admin.List.updateItemMall");

    Route::get('Admin/Tool/npc', 'Admin\ToolController@npc')->name('Admin.Tool.npc');
    Route::post('Admin/Tool/npc', 'Admin\ToolController@npc');
    Route::get('Admin/Tool/npcDetail/{id}', 'Admin\ToolController@npcDetail')->name("Admin.Tool.npcDetail");
    Route::post('Admin/Tool/npcDetail/{id}', 'Admin\ToolController@npcDetail')->name("Admin.Tool.npcDetail");
    Route::get('Admin/Tool/editNpcDetail/{id}', 'Admin\ToolController@editNpcDetail')->name("Admin.Tool.editNpcDetail");
    Route::get('Admin/Tool/trap', 'Admin\ToolController@trap')->name('Admin.Tool.trap');
    Route::get('Admin/Tool/effect', 'Admin\ToolController@effect')->name('Admin.Tool.effect');
    Route::post('Admin/Tool/effect', 'Admin\ToolController@effect')->name('Admin.Tool.effect');
    Route::get('Admin/Tool/effectDetail/{id}', 'Admin\ToolController@effectDetail')->name("Admin.Tool.effectDetail");
    Route::post('Admin/Tool/updateNpcDetail/{id}', 'Admin\ToolController@updateNpcDetail')->name("Admin.Tool.updateNpcDetail");
    Route::post('Admin/Tool/updateEffectDetail/{id}', 'Admin\ToolController@updateEffectDetail')->name("Admin.Tool.updateEffectDetail");
    Route::delete('Admin/Tool/destroy/{id}', 'Admin\ToolController@destroy')->name('Admin.Tool.destroy');
    Route::get('Admin/Tool/editTreasure/{id}', 'Admin\ToolController@editTreasure')->name("Admin.Tool.editTreasure");
    Route::post('Admin/Tool/updateTreasure/{id}', 'Admin\ToolController@updateTreasure')->name("Admin.Tool.updateTreasure");
    Route::get('Admin/Tool/relic', 'Admin\ToolController@relic')->name('Admin.Tool.relic');
    Route::post('Admin/Tool/relicUpdate', 'Admin\ToolController@relicUpdate')->name("Admin.Tool.relicUpdate");
    Route::get('Admin/Tool/motd/{id}', 'Admin\ToolController@motd')->name('Admin.Tool.motd');

    //Route::get('Admin/Tool/playerDetail/{id}', 'Admin\ToolController@playerDetail')->name("Admin.Tool.playerDetail");
    //Route::post('Admin/Tool/playerDetail/{id}', 'Admin\ToolController@playerDetail')->name("Admin.Tool.playerDetail");
    Route::post('Admin/Tool/storeMOTD', 'Admin\ToolController@storeMOTD')->name('Admin.Tool.storeMOTD');
    Route::get('Admin/Tool/player', 'Admin\ToolController@player')->name('Admin.Tool.player');
    Route::post('Admin/Tool/player', 'Admin\ToolController@player');

    Route::get('Admin/Detail/playerDetail/{id}', 'Admin\DetailController@playerDetail')->name("Admin.Detail.playerDetail");
    Route::get('Admin/Detail/statsDetail/{id}', 'Admin\DetailController@statsDetail')->name("Admin.Detail.statsDetail");
    Route::get('Admin/Detail/inventoryDetail/{id}', 'Admin\DetailController@inventoryDetail')->name("Admin.Detail.inventoryDetail");
    Route::get('Admin/Detail/stashDetail/{id}', 'Admin\DetailController@stashDetail')->name("Admin.Detail.stashDetail");
    Route::get('Admin/Detail/powerDetail/{id}', 'Admin\DetailController@powerDetail')->name("Admin.Detail.powerDetail");
    Route::get('Admin/Detail/skillDetail/{id}', 'Admin\DetailController@skillDetail')->name("Admin.Detail.skillDetail");
    Route::get('Admin/Detail/effectDetail/{id}', 'Admin\DetailController@effectDetail')->name("Admin.Detail.effectDetail");
    Route::get('Admin/Detail/stanceDetail/{id}', 'Admin\DetailController@stanceDetail')->name("Admin.Detail.stanceDetail");
    Route::get('Admin/Detail/questDetail/{id}', 'Admin\DetailController@questDetail')->name("Admin.Detail.questDetail");
    Route::get('Admin/Detail/logDetail/{id}', 'Admin\DetailController@logDetail')->name("Admin.Detail.logDetail");

    });





Route::any('Credit/storeCredit/ywpynygzkg', 'CreditController@LSNMEBKHOT')->name("Credit.storeCredit"); // xyah login kena buka url ni panggil

Route::group(['middleware' => 'auth'], function () {
    // Dashboard
    Route::resource('Dashboard','DashboardController');
    Route::any('Credit/storeCredit/lkrqcatujz', 'CreditController@YUERHBITKS')->name("Credit.storeCredit"); //kena login baru blh buka url ni kembali
    //Credit
    Route::get('Credit/purchase', 'CreditController@purchase');
    Route::get('Credit/transfer', 'CreditController@transfer');
    Route::get('Credit/storeCredit', 'CreditController@storeCredit')->name("Credit.storeCredit");
    Route::post('Credit/storeCredit', 'CreditController@storeCredit')->name("Credit.storeCredit");
    Route::post('Credit/store', 'CreditController@store')->name("Credit.store");
    //Route::resource('Credit','CreditController');

    // Start Profile
    Route::get('profile', 'ProfileController@profile');
    Route::post('profile/updateProfile/{id}', 'ProfileController@updateProfile')->name("profile.updateProfile");
    // End Profile

    // Start Store
    Route::get('Store/item/{id}', 'StoreController@item');
    Route::get('Store/boost', 'StoreController@boost');
    Route::get('Store/cart', 'StoreController@cart');
    Route::post('storeBoost', 'StoreController@storeBoost')->name("Store.storeBoost");
    Route::post('Store', 'StoreController@store')->name("Store.store");
    Route::post('storeCart', 'StoreController@storeCart')->name("Store.storeCart");
    Route::get('Store/destroyCartItem/{id}', 'StoreController@destroyCartItem')->name('Store.destroyCartItem');
    // End Store

    // Start Market
    Route::get('Market/market/{id}', 'MarketController@market');
    Route::get('Market/sell_item', 'MarketController@sell_item');
    Route::get('Market/sell_character', 'MarketController@sell_character');
    Route::post('Market/sell_character', 'MarketController@sell_character')->name("Market.sell_character");
    Route::get('Market/buy', 'MarketController@buy');
    Route::get('Market/sell_forge', 'MarketController@sell_forge');
    Route::get('Market/sell_bind', 'MarketController@sell_bind');
    Route::post('Market/storeCharacter', 'MarketController@storeCharacter')->name("Market.storeCharacter");
    Route::post('Market/buyCharacter', 'MarketController@buyCharacter')->name("Market.buyCharacter");
    Route::post('Market/storeForge', 'MarketController@storeForge')->name("Market.storeForge");
    Route::post('Market/buyFt', 'MarketController@buyFt')->name("Market.buyFt");
    Route::post('Market/cancelFt', 'MarketController@cancelFt')->name("Market.cancelFt");
    Route::post('Market/storeBind', 'MarketController@storeBind')->name("Market.storeBind");
    Route::post('Market/buyBt', 'MarketController@buyBt')->name("Market.buyBt");
    Route::post('Market/cancelBt', 'MarketController@cancelBt')->name("Market.cancelBt");
    Route::post('Market/storeSellItem', 'MarketController@storeSellItem')->name("Market.storeSellItem");
    Route::post('Market/cancelItem', 'MarketController@cancelItem')->name("Market.cancelItem");
    Route::post('Market/updateSellItem/{id}', 'MarketController@updateSellItem')->name("Market.updateSellItem");
    Route::get('Market/editSellItem/{id}', 'MarketController@editSellItem')->name("Market.editSellItem");
    Route::post('Market/store', 'MarketController@store')->name("Market.store");
    Route::post('Market/buySoloCharacter', 'MarketController@buySoloCharacter')->name("Market.buySoloCharacter");
    Route::post('Market/cancelSoloCharacter', 'MarketController@cancelSoloCharacter')->name("Market.cancelSoloCharacter");
    // End Market

    // Start Support
    Route::get('Support/unstuck', 'SupportController@unstuck');
    Route::get('Support/my_boost', 'SupportController@my_boost');
    Route::get('Support/forge', 'SupportController@forge');
    Route::get('Support/nickname', 'SupportController@nickname');
    Route::get('Support/claim', 'SupportController@claim');
    Route::get('Support/bind', 'SupportController@bind');
    Route::post('Support/store', 'SupportController@store')->name("Support.store");
    Route::post('Support/changeNickname', 'SupportController@changeNickname')->name("Support.changeNickname");
    Route::post('Support/unstuckSubmit', 'SupportController@unstuckSubmit')->name("Support.unstuckSubmit");
    Route::post('Support/claimStore', 'SupportController@claimStore')->name("Support.claimStore");
    Route::post('Support/useBoost/{id}', 'SupportController@useBoost')->name("Support.useBoost");
    Route::post('Support/storeForge', 'SupportController@storeForge')->name("Support.storeForge");

    //End Support

    


});