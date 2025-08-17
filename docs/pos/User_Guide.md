# Gero POS — User Guide

This guide explains how to use the Point de Vente (POS) application embedded in Gero. It covers the main features and day‑to‑day actions for cashiers and managers.

Where it lives
- The POS UI is a React app mounted in the page resources/views/pos/pos.blade.php.
- Typical entry links in the app point to /pos and related pages like /rapports and /demandes.

1. Overview of the screen
- Header bar
  - Back arrow: Returns to your main Gero URL (configured via settings).
  - Clôture: Ends the current POS session/day, with server-side closure operations.
  - Rapports: Opens the Reports area (stock, daily, treasury, client/product sales, payments & credit).
  - Demandes: Opens Transfer Requests (if enabled by your settings).
  - Dépense: Opens the expense entry modal to record cash expenses.
  - Historique: Opens the session history panel (if enabled).
  - Shutdown: Terminates the POS session.
- Main area
  - Cart (left): Lists products added to the current sale with quantities, prices, discounts and totals.
  - Right panel: Depends on the POS type (Classic list, Parfums on-screen keyboard, or Caisse panel).

2. POS modes (Type toggler)
- Classic: Browse/search articles and add to the cart.
- Parfums: Use an on-screen numeric keyboard to search by reference/designation and quickly add items.
- Caisse: Shows a cashier panel with simplified operations tailored for checkout.

3. Finding and adding products
- Product search
  - Use the search input (or the on-screen keyboard in Parfums mode).
  - Search matches reference or designation.
  - If exactly one product matches, it is auto-added to the cart.
  - If no product matches, you hear a small alert and see a notification.
- Articles list (Classic mode)
  - Scroll or paginate through products.
  - Click an item to add it to the cart.

4. Working with the cart
- Change quantity: Increase or decrease using controls in the cart.
- Edit unit price: If allowed by your settings (price editing feature).
- Line discount: Apply percentage or fixed amount per line.
- Global discount: Apply a discount on the cart’s subtotal before tax.
- Remove an item: Remove the line from the cart.
- Totals: The cart displays item totals, reductions, and grand total.

5. Selecting a client
- Use the client selector to attach a customer to the sale.
- Quick-add a client directly from the selector when available.

6. Payments
- Open payment: Press the Pay/Validate button to open the payment modal.
- Choose:
  - Amount: Pre-filled with the cart total (or remaining amount when adding an additional payment).
  - Payment method: e.g., cash, card, cheque, LCN.
  - Account: The cash/bank account receiving the payment.
  - Check reference: For cheques/LCN when applicable.
  - Expected date: For deferred/credit payments when applicable.
  - Note: Optional note for the payment.
- Confirm: Submits to the server and records the sale.
- Additional payment: You can add a payment later to an existing sale (from history or dedicated action).

7. Ticket printing
- If ticket printing is enabled in settings, you can print a receipt after payment.
- Auto printing: If auto-ticket is enabled, the receipt prints automatically.

8. History (session)
- Open Historique to view the sales/logs for the current session.
- From here you can consult or reprint tickets, and sometimes add payments to existing sales.

9. Recording an expense (Dépense)
- Click Dépense to open the expense modal.
- Fill in: category, beneficiary, amount, and optional description.
- Submit to record the expense.

10. Demandes (transfer requests)
- Open Demandes (if enabled) to manage inter-branch or external transfer requests.
- Actions include:
  - Load lists of internal and external demandes.
  - Create a new demande by searching/adding products to a demande cart, setting quantities, and submitting.
  - Cancel or accept a demande (based on your permissions and workflow).
  - Print a demande.
  - Livrer (deliver) a demande with delivered quantities.

11. Rapports (Reports)
- Available reports (availability depends on settings):
  - Stock: Current stock of products.
  - Vente par Produit & Client: Cross tab of quantities and totals by client and product.
  - Produit par Fournisseur: Product totals grouped by supplier.
  - Paiements & Crédits: List of sales, statuses, and outstanding credits.
  - Trésorerie: Aggregates, including cash, cheques, LCN, expenses, remaining cash.
  - Quotidien (Daily): Daily overview.

12. Connectivity
- If the browser goes offline or the server is unreachable, a connection message appears.
- The app retries and updates the status when connectivity returns.

13. Session control
- Clôture: Close the POS session/day and generate the closure data.
- Shutdown: End the active POS session.

13. Barcode scanner
- Use the barcode scanner to quickly add products to the cart.
- Add F9 key as prefix for your scanner to enable it.
- Easy to use, once you add the F9 prefix the scanner will automaticlly add the product to the cart.

Tips & Troubleshooting
- If product search doesn’t find items, check your spelling or try scanning the barcode/reference.
- If you can’t edit prices or apply reductions, check with an admin to enable the feature in POS settings.
- If printing fails, verify printer configuration and browser print permissions.

