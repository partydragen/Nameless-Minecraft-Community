<?php 
/*
 *  Made by Partydragen
 *  https://partydragen.com
 *  https://mc-server-list.com/
 *
 *  Giveaway module initialisation file
 */

class Minecraft_Community_Module extends Module {

    private Language $_language;
    private Language $_mccommunity_language;

    public function __construct($language, $mccommunity_language, $pages, Endpoints $endpoints){
        $this->_language = $language;
        $this->_mccommunity_language = $mccommunity_language;

        $name = 'Minecraft Community';
        $author = '<a href="https://www.mccommunity.net/" target="_blank" rel="nofollow noopener">Minecraft Community</a>, <a href="https://partydragen.com" target="_blank" rel="nofollow noopener">Partydragen</a>';
        $module_version = '1.0.0';
        $nameless_version = '2.1.2';

        parent::__construct($this, $name, $author, $module_version, $nameless_version);
        
        Integrations::getInstance()->registerIntegration(new MinecraftCommunityIntegration($language));
        
        NamelessOAuth::getInstance()->registerProvider('minecraft-community', 'Minecraft Community', [
            'class' => MinecraftCommunityProvider::class,
            'user_id_name' => 'id',
            'scope_id_name' => 'identify',
            'icon' => 'fa-solid fa-globe',
            'verify_email' => static fn () => true,
        ]);

        $endpoints->loadEndpoints(ROOT_PATH . '/modules/Minecraft Community/includes/endpoints');
    }

    public function onInstall() {
        // No actions necessary
    }

    public function onUninstall() {
        // No actions necessary
    }

    public function onEnable() {
        // No actions necessary
    }

    public function onDisable() {
        // No actions necessary
    }

    public function onPageLoad($user, $pages, $cache, $smarty, $navs, $widgets, $template) {
        // Check for module updates
        if (isset($_GET['route']) && $user->isLoggedIn() && $user->hasPermission('admincp.update')) {
            // Page belong to this module?
            $page = $pages->getActivePage();
            if ($page['module'] == 'Minecraft Community') {

                $cache->setCache('giveaway_module_cache');
                if ($cache->isCached('update_check')) {
                    $update_check = $cache->retrieve('update_check');
                } else {
                    $update_check = Giveaway_Module::updateCheck();
                    $cache->store('update_check', $update_check, 3600);
                }

                $update_check = json_decode($update_check);
                if (!isset($update_check->error) && !isset($update_check->no_update) && isset($update_check->new_version)) {
                    $smarty->assign(array(
                        'NEW_UPDATE' => (isset($update_check->urgent) && $update_check->urgent == 'true') ? $this->mccommunity_language->get('general', 'new_urgent_update_available_x', ['module' => $this->getName()]) : $this->mccommunity_language->get('general', 'new_update_available_x', ['module' => $this->getName()]),
                        'NEW_UPDATE_URGENT' => (isset($update_check->urgent) && $update_check->urgent == 'true'),
                        'CURRENT_VERSION' => $this->mccommunity_language->get('general', 'current_version_x', [
                            'version' => Output::getClean($this->getVersion())
                        ]),
                        'NEW_VERSION' => $this->mccommunity_language->get('general', 'new_version_x', [
                            'new_version' => Output::getClean($update_check->new_version)
                        ]),
                        'NAMELESS_UPDATE' => $this->mccommunity_language->get('general', 'view_resource'),
                        'NAMELESS_UPDATE_LINK' => Output::getClean($update_check->link)
                    ));
                }
            }
        }
    }

    public function getDebugInfo(): array {
        return [];
    }

    private static function updateCheck() {
        $current_version = Util::getSetting('nameless_version');
        $uid = Util::getSetting('unique_id');

        $enabled_modules = Module::getModules();
        foreach ($enabled_modules as $enabled_item) {
            if ($enabled_item->getName() == 'Minecraft Community') {
                $module = $enabled_item;
                break;
            }
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL, 'https://api.partydragen.com/stats.php?uid=' . $uid . '&version=' . $current_version . '&module=Minecraft Community&module_version='.$module->getVersion() . '&domain='. URL::getSelfURL());

        $update_check = curl_exec($ch);
        curl_close($ch);

        $info = json_decode($update_check);
        if (isset($info->message)) {
            die($info->message);
        }

        return $update_check;
    }
}