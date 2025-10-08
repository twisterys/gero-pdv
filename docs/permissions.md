# Permissions Overview (Simple Language)

This page explains how the main permissions work in Gero. It is written in simple terms for non‑technical users.

## What “Liste” (.liste) means
- Purpose: Allows the user to see the list of items in a module.
- Effect: The user can open pages that show lists (tables) and search or pick items from those lists.
- Examples:
  - client.liste lets you open the clients list.
  - article.liste lets you open the articles list and use article search/pick dialogs.

Important: article.liste is also used in other places where you need to choose a product (article):
- In Vente (sales), the product selection modal uses article.liste.
- In Achat (purchases), the product selection modal uses article.liste.
If the user does not have article.liste, these product pickers will not work.

## What “Affichage” (.afficher) means
- Purpose: Allows the user to view the details of a specific item.
- Effect: The user can open the detail page or popup of one record to see its full information.
- Examples:
  - client.afficher allows seeing the full client card.
  - article.afficher allows viewing an article’s detailed information (e.g., details popups, price history modal).

## Other common permissions (in simple words)
These actions appear in many modules (clients, articles, ventes, achats, etc.).
- .sauvegarder = Add/Create. Lets the user add a new record. Example: vente.sauvegarder allows creating a new sale.
- .mettre_a_jour = Edit/Update. Lets the user modify an existing record. Example: article.mettre_a_jour allows changing price, name, etc.
- .supprimer = Delete. Lets the user remove a record. Some records cannot be deleted if already used.

## Typical extra actions by module
These are common special permissions you may see. The exact meaning depends on the module, but here is the idea in plain language:
- vente.valider / achat.valider = Validate/Confirm the document (lock important parts, move stock or accounting as designed).
- vente.devalider / achat.devalider = Un‑validate (re‑open) the document to allow changes, when allowed.
- vente.convertir / achat.convertir = Convert a document to the next step (example: quote ➜ delivery note or invoice). There is also .convertir_mass for doing several at once.
- vente.cloner / achat.cloner = Duplicate an existing document to start a new one quickly.
- vente.telecharger / achat.telecharger = Download the document (usually PDF, sometimes Excel).
- vente.historique / achat.historique = See the history/changes of the document.
- vente.date / achat.date = Allow changing document dates (issue date, delivery date, etc.).
- vente.controler = Mark as checked/controlled (quality or admin control step) if your process uses it.
- vente.piece_jointe_attacher / vente.piece_jointe_supprimer = Attach or remove attachments (files) on a document.

Other modules follow the same logic:
- paiement.* (payments) = Manage payments. Example: paiement.vente to register a payment for a sale, paiement.depense to pay an expense, paiement.operation_bancaire for bank operations.
- rapport = Access reports screens.
- pos.* = POS actions (e.g., pos.historique to view POS history, pos.demande_transfert to request stock transfer).
- article.* / famille.* / marque.* = Manage products, families, brands (same verbs: .liste, .sauvegarder, .mettre_a_jour, .supprimer). Note: article.afficher shows details; article.liste also powers product pickers in Vente and Achat.

## How permissions combine (quick guide)
- To access a module page: give .liste.
- To open a record’s details: add .afficher.
- To add new records: add .sauvegarder.
- To edit existing records: add .mettre_a_jour.
- To delete records: add .supprimer (use with care).
- For workflow actions (validate, convert, clone, download, etc.): add the specific action permission.

Tip: Start with the minimum (usually .liste and .afficher), then add more as needed. Test the user’s daily tasks to ensure nothing is blocked.
