<?php
class InfoCommunityEndpoint extends KeyAuthEndpoint {

    public function __construct() {
        $this->_route = 'minecraftcommunity/info';
        $this->_module = 'Minecraft Community';
        $this->_description = 'Share basic community details';
        $this->_method = 'GET';
    }

    public function execute(Nameless2API $api): void {
        $api->returnArray([
            'users_registered' => DB::getInstance()->query('SELECT COUNT(*) as c FROM nl2_users')->first()->c
        ]); 
    }
}