### Change Log ###

#### version 0.2.0 ####
- New widget: Category explorer allows viewing bulletins by category ('Events', 'Services')
- Bulletin post now has categories
- **Keyring > Import** has built-in certificate lookup when given node URI
- **Keyring > Certificate** has pull request option with signed certificate

#### patch 0.2.1 ####
- Critical fix of there being no public AJAX handler exposed for Explorer Widget
- Fix styling issues on Explorer widget
- Fix unterminated widget template

#### patch 0.2.2 ####
- Clean-up Latest Bulletin gateway interface
- Latest Bulletin now uses pop-ups for viewing profiles
- Share the pop-up system between the widgets

#### patch 0.2.3 ####
- Automatically catch or rewrite **spring/** page request to override as point of service without needing to create the new page in the installation -- this is the correct behavior
- Use SPRING_URL to correctly reference resources from document root