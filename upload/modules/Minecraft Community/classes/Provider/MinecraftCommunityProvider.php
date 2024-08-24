<?php
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

class MinecraftCommunityProvider extends AbstractProvider {
    protected Application $_application;

    public function __construct(array $options) {
        parent::__construct($options);
    }

    use BearerAuthorizationTrait;

    /**
     * Get authorization URL to begin OAuth flow
     *
     * @return string
     */
    public function getBaseAuthorizationUrl() {
        $referral_code = Settings::get('referral_code', null, 'Minecraft Community');

        return 'https://mccommunity.net/oauth2/authorize/' . (!empty($referral_code) ? '?ref=' . $referral_code : '');
    }

    /**
     * Get access token URL to retrieve token
     *
     * @param  array $params
     *
     * @return string
     */
    public function getBaseAccessTokenUrl(array $params) {
        return 'https://mccommunity.net/index.php?route=/api/v2/oauth2/token';
    }

    /**
     * Get provider URL to retrieve user details
     *
     * @param  AccessToken $token
     *
     * @return string
     */
    public function getResourceOwnerDetailsUrl(AccessToken $token) {
        return 'https://mccommunity.net/index.php?route=/api/v2/oauth2/user';
    }

    /**
     * Returns the string that should be used to separate scopes when building
     * the URL for requesting an access token.
     *
     * Nameless's scope separator is space (%20)
     *
     * @return string Scope separator
     */
    protected function getScopeSeparator() {
        return ' ';
    }

    /**
     * Get the default scopes used by this provider.
     *
     * This should not be a complete list of all scopes, but the minimum
     * required for the provider user interface!
     *
     * @return array
     */
    protected function getDefaultScopes() {
        return [
            'identity'
        ];
    }

    /**
     * Check a provider response for errors.
     *
     * @throws IdentityProviderException
     * @param  ResponseInterface @response
     * @param  array $data Parsed response data
     * @return void
     */
    protected function checkResponse(ResponseInterface $response, $data) {
        if ($response->getStatusCode() >= 400) {
            throw MinecraftCommunityIdentityProviderException::clientException($response, $data);
        }
    }

    /**
     * Generate a user object from a successful user details request.
     *
     * @param array $response
     * @param AccessToken $token
     * @return \League\OAuth2\Client\Provider\ResourceOwnerInterface
     */
    protected function createResourceOwner(array $response, AccessToken $token) {
        return new MinecraftCommunityResourceOwner($response);
    }
}