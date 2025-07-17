<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyStaticApiToken
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Récupérer le token attendu depuis les variables d'environnement
        $expectedToken = env('INTERNAL_API_TOKEN');

        // 2. Récupérer le token envoyé dans la requête (standard : "Authorization: Bearer <token>")
        $sentToken = $request->bearerToken();

        // 3. Vérifier que le token attendu est bien configuré et que le token envoyé existe
        if (!$expectedToken || !$sentToken) {
            // Si le token n'est pas configuré sur le serveur ou si le client n'en envoie pas, refuser l'accès.
            return response()->json(['message' => 'Unauthorized.'], 401);
        }

        // 4. Comparer les deux tokens de manière sécurisée pour éviter les attaques temporelles (timing attacks)
        if (!hash_equals($expectedToken, $sentToken)) {
            return response()->json(['message' => 'Forbidden.'], 403);
        }

        // 5. Si les tokens correspondent, autoriser la requête à continuer
        return $next($request);
    }
}
