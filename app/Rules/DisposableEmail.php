<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class DisposableEmail implements ValidationRule
{
    /**
     * Liste des domaines d'emails temporaires/jetables bloqués.
     * Cette liste peut être étendue selon les besoins.
     */
    protected array $blockedDomains = [
        // Yopmail et variantes
        'yopmail.com',
        'yopmail.fr',
        'yopmail.net',
        'cool.fr.nf',
        'jetable.fr.nf',
        'nospam.ze.tc',
        'nomail.xl.cx',
        'mega.zik.dj',
        'speed.1s.fr',
        'courriel.fr.nf',
        'moncourrier.fr.nf',
        'monemail.fr.nf',
        'monmail.fr.nf',

        // Guerrilla Mail
        'guerrillamail.com',
        'guerrillamail.net',
        'guerrillamail.org',
        'guerrillamail.biz',
        'guerrillamail.de',
        'guerrillamailblock.com',
        'sharklasers.com',
        'grr.la',
        'pokemail.net',
        'spam4.me',

        // 10 Minute Mail et similaires
        '10minutemail.com',
        '10minutemail.net',
        '10minutemail.org',
        '10minmail.com',
        '10mail.org',
        'tempmail.com',
        'tempmail.net',
        'temp-mail.org',
        'temp-mail.io',
        'tempmailo.com',

        // Mailinator et variantes
        'mailinator.com',
        'mailinator2.com',
        'mailinator.net',
        'mailinater.com',
        'mailinator.org',
        'sogetthis.com',
        'mailin8r.com',
        'mailinator.us',

        // Throwaway Mail
        'throwawaymail.com',
        'throam.com',
        'wegwerfmail.de',
        'wegwerfmail.net',
        'wegwerfmail.org',

        // Trash Mail
        'trashmail.com',
        'trashmail.net',
        'trashmail.org',
        'trashmail.me',
        'trashmail.ws',
        'trashemail.de',

        // Fake Mail / Temp Mail
        'fakeinbox.com',
        'fakemailgenerator.com',
        'fakemail.fr',
        'tempr.email',
        'discard.email',
        'discardmail.com',
        'disposablemail.com',
        'disposable.com',
        'emailondeck.com',
        'getnada.com',
        'nada.email',

        // Autres services populaires
        'maildrop.cc',
        'mailnesia.com',
        'mailsac.com',
        'mohmal.com',
        'tempail.com',
        'emailfake.com',
        'crazymailing.com',
        'tempinbox.com',
        'mytemp.email',
        'tmpmail.org',
        'tmpmail.net',
        'burnermail.io',
        'getairmail.com',
        'mintemail.com',
        'mt2009.com',
        'spamgourmet.com',
        'spambox.us',
        'spamfree24.org',
        'antispam.de',
        'mailcatch.com',
        'mailnull.com',
        'e4ward.com',
        'spamex.com',
        'inbox.lv',
        'mail-temporaire.fr',
        'jetable.org',
        'emailtemporaire.fr',
        'emailtemporaire.org',
        'mail-jetable.com',

        // Services africains et francophones connus
        'jetable.com',
        'tempmail.fr',
        'poubelle.com',
        'email-jetable.fr',
    ];

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty($value)) {
            return;
        }

        $email = strtolower(trim($value));
        $domain = $this->extractDomain($email);

        if ($domain && $this->isDisposableDomain($domain)) {
            $fail('Les adresses email temporaires ou jetables ne sont pas autorisées. Veuillez utiliser une adresse email permanente.');
        }
    }

    /**
     * Extrait le domaine d'une adresse email.
     */
    protected function extractDomain(string $email): ?string
    {
        $parts = explode('@', $email);

        if (count($parts) !== 2) {
            return null;
        }

        return strtolower($parts[1]);
    }

    /**
     * Vérifie si le domaine est dans la liste des domaines jetables.
     */
    protected function isDisposableDomain(string $domain): bool
    {
        // Vérification exacte
        if (in_array($domain, $this->blockedDomains, true)) {
            return true;
        }

        // Vérification des sous-domaines (ex: mail.yopmail.com)
        foreach ($this->blockedDomains as $blockedDomain) {
            if (str_ends_with($domain, '.' . $blockedDomain)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Permet d'ajouter dynamiquement des domaines à bloquer.
     */
    public function addBlockedDomains(array $domains): self
    {
        $this->blockedDomains = array_unique(
            array_merge($this->blockedDomains, array_map('strtolower', $domains))
        );

        return $this;
    }

    /**
     * Retourne la liste des domaines bloqués.
     */
    public function getBlockedDomains(): array
    {
        return $this->blockedDomains;
    }
}
