<?php
namespace App\Security;

use App\Services\User\TokenServices;
use App\Services\User\UserServices;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class ApiKeyAuthenticator extends AbstractAuthenticator
{
    CONST TOKEN_REFRESH_ROUTE = 'api_token_refresh';

    protected TokenServices $tokenServices;
    protected UserServices $userServices;

    public function __construct(TokenServices $tokenServices, UserServices $userServices)
    {
        $this->tokenServices = $tokenServices;
        $this->userServices = $userServices;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning `false` will cause this authenticator
     * to be skipped.
     */
    public function supports(Request $request): ?bool
    {
        //исключение для роута обновления токена
        if ($request->get('_route') == self::TOKEN_REFRESH_ROUTE)
            return false;

        return true;
    }

    public function authenticate(Request $request): PassportInterface
    {
        $tokenData = $this->tokenServices->getRawToken($request);

        if ($tokenData === null)
            throw new CustomUserMessageAuthenticationException('Отсутствует авторизационный токен');

        //$tokenData = $this->tokenServices->getTokenData($headerToken, $bearer_token);

        $user_identifier = $this->tokenServices->checkUserToken($tokenData['user_id'], $tokenData['token']);

        if ($user_identifier === null)
            throw new CustomUserMessageAuthenticationException('Не удалось авторизироваться по авторизационному токену');

        if ($user_identifier->getUser()->getIsBlocked())
            throw new CustomUserMessageAuthenticationException('Пользователь заблокирован');

        //update last visit time
        $this->userServices->updateLastVisit($user_identifier->getUser());

        return new SelfValidatingPassport(new UserBadge($user_identifier->getUser()->getUserIdentifier()));
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        // on success, let the request continue
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }


}
