<?php

/**
 * @copyright   Léandro Tijink
 * @license     MIT
 */

namespace Rovota\Framework\Auth\Adapters;

use Rovota\Framework\Auth\Events\IdentityAuthenticated;
use Rovota\Framework\Auth\Interfaces\ProviderAdapterInterface;
use Rovota\Framework\Facades\Cookie;
use Rovota\Framework\Facades\Registry;
use Rovota\Framework\Facades\Response;
use Rovota\Framework\Http\Cookie\CookieObject;
use Rovota\Framework\Identity\Models\Session;
use Rovota\Framework\Identity\Models\User;
use Rovota\Framework\Support\Config;
use Rovota\Framework\Support\Str;

class StandardAdapter implements ProviderAdapterInterface
{

	protected Config $parameters;

	// -----------------

	public Session|null $session = null;

	public User|null $user = null;

	// -----------------

	public function __construct(Config $parameters)
	{
		$this->parameters = $parameters;
	}

	// -----------------

	public function initialize(): void
	{
		$cookie = request()->cookies->get(Registry::string('security.auth.cookie_name', 'account'));

		if ($cookie instanceof CookieObject) {
			if (Str::length($cookie->value) !== 80) {
				$cookie->expire();
				return;
			}

			$session = Session::whereAfter('expiration', now())->find($cookie->value, 'hash');

			if ($session instanceof Session) {

				$user = $this->getUserClass()::find($session->user_id);

				if ($user instanceof User && $user->suspension === null) {
					IdentityAuthenticated::dispatch($user);
					$this->session = $session;
					$this->user = $user;
					return;
				}
			}

			$cookie->expire();
		}
	}

	// -----------------

	public function startSession(array $attributes = []): bool
	{
		$session = $this->getSessionClass()::for($this->user, $attributes);

		if ($session->save()) {
			$this->session = $session;
			Response::attachCookie(Cookie::create('account', $session->hash, [
				'expires' => now()->addDays(7)
			]));
			return true;
		}
		return false;
	}

	public function endSession(): bool
	{
		if ($this->session instanceof Session && $this->session->expire()) {
			$cookie = request()->cookies->get(Registry::string('security.auth.cookie_name', 'account'));
			if ($cookie instanceof CookieObject) {
				return $cookie->expire();
			}
		}
		return false;
	}

	public function verifySession(): bool
	{
		if ($this->session instanceof Session) {
			return $this->session->verify();
		}
		return false;
	}

	// -----------------

	public function validate(array $credentials): User|false
	{
		$user = $this->getUserClass()::where([$credentials['username'], $credentials['password']])->first();

		if ($user instanceof User && $user->suspension === null) {
			return $user;
		}

		return false;
	}

	// -----------------

	protected function getUserClass(): User|string
	{
		return $this->parameters->get('models.user', User::class);
	}

	protected function getSessionClass(): Session|string
	{
		return $this->parameters->get('models.session', Session::class);
	}

}