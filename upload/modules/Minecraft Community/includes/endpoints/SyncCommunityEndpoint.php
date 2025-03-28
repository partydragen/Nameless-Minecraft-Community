<?php
class SyncCommunityEndpoint extends KeyAuthEndpoint {

    public function __construct() {
        $this->_route = 'minecraftcommunity/sync';
        $this->_module = 'Minecraft Community';
        $this->_description = 'Sync with Minecraft Community';
        $this->_method = 'POST';
    }

    public function execute(Nameless2API $api): void {
        $api->validateParams($_POST, ['client_id', 'client_secret']);

        // Update oauth details
        $oauth = DB::getInstance()->query('SELECT * FROM `nl2_oauth` WHERE provider = ?', ['minecraft-community']);
        if ($oauth->count()) {
            DB::getInstance()->update("oauth", ['provider', $oauth->first()->provider], [
                'enabled' => 1,
                'client_id' => $_POST['client_id'],
                'client_secret' => $_POST['client_secret'],
            ]);
        } else {
            DB::getInstance()->insert("oauth", [
                'provider' => 'minecraft-community',
                'enabled' => 1,
                'client_id' => $_POST['client_id'],
                'client_secret' => $_POST['client_secret'],
            ]);
        }

        // Update client application
        Settings::set('client_id', $_POST['client_id'], 'Minecraft Community');
        Settings::set('client_secret', $_POST['client_secret'], 'Minecraft Community');

        // Update referral code
        if (isset($_POST['referral_code'])) {
            Settings::set('referral_code', $_POST['referral_code'], 'Minecraft Community');
        }
    }
}
