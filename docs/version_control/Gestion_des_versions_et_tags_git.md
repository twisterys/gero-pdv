### Fonctionnement des nouveaux tags et gestion des versions

1. **Types de tags**

   Nous utilisons désormais un versioning sémantique sous la forme `vX.Y.Z` où :

- `X` = version majeure
- `Y` = version mineure
- `Z` = version patch (correction)

1. **Automatisation via GitHub Actions**

   Un workflow automatisé se charge désormais de :

- Lire le dernier tag version présent sur le dépôt
- Incrémenter automatiquement la version selon le type d’évolution détecté dans le message du commit (`[major]`, `[minor]`, ou `[patch]`)
- Créer et pousser un nouveau tag correspondant à cette nouvelle version

1. **Impact sur la gestion du code**
- Chaque nouveau tag reflète une version précise et stable de l’application
- Les versions majeures indiquent des changements importants ou ruptures de compatibilité
- Les versions mineures ajoutent des fonctionnalités tout en restant compatibles
- Les patchs corrigent des bugs sans ajouter de fonctionnalités

---

### Comment contribuer ?

- Pensez à bien mentionner dans vos messages de commit la nature de l’évolution, par exemple :
    - `[major]` pour un changement majeur
    - `[minor]` pour une nouvelle fonctionnalité
    - `[patch]` **[patch]** ou même **aucun tag du tout** → ça sera considéré comme une correction (patch)
- Cela permettra au système d’incrémenter la version de manière appropriée.

### Description des versions

- Vous pouvez aussi ajouter un message de description pour la version avec `[desc: Votre description ici]` dans le commit.
- Cette description sera ajoutée dans le message du tag, et elle sera visible dans les releases GitHub, ce qui aide à mieux comprendre ce que contient chaque version.
