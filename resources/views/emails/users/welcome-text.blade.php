Bienvenue sur {{ $appName }}
District Autonome de Yamoussoukro
=====================================

Bonjour {{ $user->nom_complet }},

Nous avons le plaisir de vous informer qu'un compte utilisateur a été créé pour vous sur la plateforme {{ $appName }}.

VOS IDENTIFIANTS DE CONNEXION
-----------------------------
Email : {{ $user->email }}
Mot de passe temporaire : {{ $plainPassword }}
@if($user->role)
Rôle attribué : {{ $user->role->name }}
@endif

⚠️  IMPORTANT : Pour des raisons de sécurité, nous vous recommandons fortement de modifier votre mot de passe dès votre première connexion.

CONNEXION
---------
Accédez à l'application : {{ $loginUrl }}

PREMIERS PAS
------------
1. Connectez-vous avec les identifiants ci-dessus
2. Changez votre mot de passe depuis votre profil
3. Explorez les fonctionnalités disponibles selon votre rôle
4. Contactez l'administrateur en cas de problème

@if($createdBy)
Compte Enregistré par : {{ $createdBy->nom_complet }}
@endif

---
Cet email a été envoyé automatiquement par {{ $appName }}
© {{ date('Y') }} District Autonome de Yamoussoukro. Tous droits réservés.

Si vous n'êtes pas à l'origine de cette demande, veuillez ignorer cet email ou contacter l'administrateur.
