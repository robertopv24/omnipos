<?php

use OmniPOS\Core\App;

/**
 * Definición de Rutas
 * @var App $app
 */

$router = $app->getRouter();

// Home
$router->get('/', 'HomeController@index');

// Auth Routes
$router->get('/login', 'AuthController@loginForm', ['GuestMiddleware']);
$router->post('/login', 'AuthController@login', ['GuestMiddleware']);
$router->get('/logout', 'AuthController@logout', ['AuthMiddleware', 'LocalizationMiddleware']);
$router->get('/register', 'AuthController@registerForm', ['GuestMiddleware', 'LocalizationMiddleware']);
$router->post('/register', 'AuthController@register', ['GuestMiddleware']);

// Dashboard
// Dashboard requiere Auth, pero no un permiso específico más allá de entrar
$router->get('/dashboard', 'DashboardController@index', ['AuthMiddleware', 'LocalizationMiddleware']);
$router->get('/master-dashboard', 'MasterDashboardController@index', ['AuthMiddleware', 'LocalizationMiddleware']);

// Platform Dashboard & Management (Super Admin)
$router->get('/platform/dashboard', 'PlatformDashboardController@index', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/platform/accounts', 'PlatformAccountController@index', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/platform/plans', 'PlatformSettingsController@plans', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/platform/languages', 'PlatformSettingsController@languages', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/platform/languages/get', 'PlatformSettingsController@getTranslations', ['AuthMiddleware', 'RbacMiddleware']);
$router->post('/platform/languages/save', 'PlatformSettingsController@saveLanguage', ['AuthMiddleware', 'RbacMiddleware']);
$router->post('/platform/languages/delete', 'PlatformSettingsController@deleteLanguage', ['AuthMiddleware', 'RbacMiddleware']);
$router->post('/platform/languages/update-key', 'PlatformSettingsController@updateTranslationKey', ['AuthMiddleware', 'RbacMiddleware']);
$router->get('/platform/ads', 'PlatformSettingsController@advertisements', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/platform/settings', 'PlatformSettingsController@settings', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/platform/menus', 'PlatformSettingsController@menus', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->post('/platform/menus/save', 'PlatformSettingsController@saveMenu', ['AuthMiddleware', 'RbacMiddleware']);
$router->post('/platform/menus/delete', 'PlatformSettingsController@deleteMenu', ['AuthMiddleware', 'RbacMiddleware']);
$router->post('/platform/menus/reorder', 'PlatformSettingsController@reorderMenus', ['AuthMiddleware', 'RbacMiddleware']);



// Usuarios (Requiere RBAC)
$router->get('/users', 'UserController@index', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/users/create', 'UserController@create', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->post('/users', 'UserController@store', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/users/edit', 'UserController@edit', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->post('/users/update', 'UserController@update', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/users/delete', 'UserController@delete', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);

// Negocios (Cuenta)
$router->get('/account/businesses', 'AccountController@businesses', ['AuthMiddleware', 'LocalizationMiddleware']);
$router->get('/account/switch', 'AccountController@switch', ['AuthMiddleware', 'LocalizationMiddleware']);
$router->get('/account/business/create', 'AccountController@create', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->post('/account/business/store', 'AccountController@store', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/account/settings', 'BusinessSettingsController@index', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->post('/account/settings/update', 'BusinessSettingsController@update', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);

// Restauración
$router->get('/restoration/menu', 'RestorationController@digitalMenu', ['LocalizationMiddleware']); // Público?
$router->get('/restoration/kds', 'RestorationController@kds', ['AuthMiddleware', 'LocalizationMiddleware']); // Cocina
$router->post('/restoration/item/status', 'RestorationController@updateItemStatus', ['AuthMiddleware', 'LocalizationMiddleware']);

// Productos
$router->get('/products', 'ProductController@index', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/products/create', 'ProductController@create', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->post('/products', 'ProductController@store', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/products/edit', 'ProductController@edit', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->post('/products/update', 'ProductController@update', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/products/delete', 'ProductController@delete', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);

// Trazabilidad
$router->get('/inventory/traceability', 'TraceabilityController@index', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);

// Auditoría
$router->get('/admin/audit/authorizations', 'AuditController@authorizations', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);

// Configuración de Impuestos
$router->get('/admin/taxes', 'TaxController@index', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->post('/admin/taxes', 'TaxController@store', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->post('/admin/taxes/igtf', 'TaxController@updateIgtf', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/admin/taxes/delete', 'TaxController@delete', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);

// Finanzas
$router->get('/finance/cxc', 'FinanceController@cxc', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->post('/finance/cxc/pay', 'FinanceController@payCxc', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/finance/cxp', 'FinanceController@cxp', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->post('/finance/cxp/pay', 'FinanceController@payCxp', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/finance/payroll', 'FinanceController@payroll', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->post('/finance/payroll/pay', 'FinanceController@payPayroll', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/finance/ledger', 'FinanceController@ledger', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/finance/petty-cash', 'FinanceController@pettyCash', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);

// Caja y Sesiones
$router->get('/cash', 'CashController@index', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/cash/open', 'CashController@open', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->post('/cash/open', 'CashController@open', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/cash/close', 'CashController@close', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->post('/cash/close', 'CashController@close', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/cash/movement', 'CashController@movement', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->post('/cash/movement', 'CashController@movement', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);

// Manufactura
$router->get('/manufacture/recipes', 'ManufactureController@recipes', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/manufacture/recipes/create', 'ManufactureController@createRecipe', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->post('/manufacture/recipes', 'ManufactureController@storeRecipe', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/manufacture/recipes/edit', 'ManufactureController@editRecipe', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->post('/manufacture/recipes/update', 'ManufactureController@updateRecipe', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/manufacture/recipes/delete', 'ManufactureController@deleteRecipe', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/manufacture/orders/create', 'ManufactureController@createOrder', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->post('/manufacture/orders', 'ManufactureController@storeOrder', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);

// POS y Ventas
$router->get('/pos', 'SalesController@pos', ['AuthMiddleware', 'LocalizationMiddleware']);
$router->get('/sales', 'SalesController@index', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/sales/show', 'SalesController@show', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/sales/edit', 'SalesController@edit', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->post('/sales/update', 'SalesController@update', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/sales/search-products', 'SalesController@searchProducts', ['AuthMiddleware']);
$router->get('/sales/search-clients', 'SalesController@searchClients', ['AuthMiddleware']);
$router->post('/sales/checkout', 'SalesController@checkout', ['AuthMiddleware', 'LocalizationMiddleware']);

// Compras y Proveedores (**Aquí aplicamos RBAC**)
$router->get('/purchases', 'PurchaseController@index', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/purchases/create', 'PurchaseController@create', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->post('/purchases', 'PurchaseController@store', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/purchases/receive', 'PurchaseController@receive', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->post('/purchases/receive', 'PurchaseController@processReceive', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);

$router->get('/suppliers', 'PurchaseController@suppliers', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/suppliers/create', 'PurchaseController@createSupplier', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->post('/suppliers', 'PurchaseController@storeSupplier', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/suppliers/edit', 'PurchaseController@editSupplier', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->post('/suppliers/update', 'PurchaseController@updateSupplier', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/suppliers/delete', 'PurchaseController@deleteSupplier', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);

// Reportes y Exportación
$router->get('/reports', 'ReportController@index', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/reports/profitability', 'ReportController@profitability', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/reports/export-tax', 'ReportController@exportTaxLedger', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);

// Restauración y Mesas
$router->get('/restoration/tables', 'TableController@index', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->post('/restoration/tables', 'TableController@store', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/restoration/tables/delete', 'TableController@delete', ['AuthMiddleware', 'LocalizationMiddleware', 'RbacMiddleware']);
$router->get('/restoration/kds/data', 'RestorationController@kdsData', ['AuthMiddleware']);
$router->post('/restoration/item/status', 'RestorationController@updateItemStatus', ['AuthMiddleware']);

// Tienda Pública (Shop) - ACCESO PÚBLICO (Demo)
$router->get('/shop', 'ShopController@index', ['LocalizationMiddleware']);
$router->get('/shop/cart', 'ShopController@cart', ['LocalizationMiddleware']);
$router->get('/shop/checkout', 'ShopController@checkout', ['LocalizationMiddleware']);
$router->get('/shop/order-status', 'ShopController@orderStatus', ['LocalizationMiddleware']);
$router->post('/shop/cart/add', 'ShopController@addToCart');

// Páginas Estáticas (Públicas)
$router->get('/about', 'PageController@about', ['LocalizationMiddleware']);
$router->get('/contact', 'PageController@contact', ['LocalizationMiddleware']);
$router->get('/terms', 'PageController@terms', ['LocalizationMiddleware']);
$router->get('/privacy', 'PageController@privacy', ['LocalizationMiddleware']);

// Constructor Visual (Platform Builder)
$router->get('/platform/builder', 'BuilderController@index', ['AuthMiddleware', 'RbacMiddleware']);
$router->get('/platform/builder/pages', 'BuilderController@pages', ['AuthMiddleware', 'RbacMiddleware']);
$router->get('/platform/builder/load', 'BuilderController@load', ['AuthMiddleware', 'RbacMiddleware']);
$router->post('/platform/builder/save', 'BuilderController@save', ['AuthMiddleware', 'RbacMiddleware']);
$router->get('/platform/builder/load/{id}', 'BuilderController@load', ['AuthMiddleware', 'RbacMiddleware']);

// Componentes Dinámicos
$router->get('/platform/builder/components', 'BuilderController@components', ['AuthMiddleware', 'RbacMiddleware']);
$router->post('/platform/builder/components/save', 'BuilderController@saveComponent', ['AuthMiddleware', 'RbacMiddleware']);
$router->post('/platform/builder/components/delete', 'BuilderController@deleteComponent', ['AuthMiddleware', 'RbacMiddleware']);

// Importador de Vistas Huérfanas
$router->get('/platform/importer', 'BuilderController@importer', ['AuthMiddleware', 'RbacMiddleware']);
$router->post('/platform/importer/scan', 'BuilderController@scanOrphans', ['AuthMiddleware', 'RbacMiddleware']);
$router->post('/platform/importer/bind', 'BuilderController@bindView', ['AuthMiddleware', 'RbacMiddleware']);

// Rutas Dinámicas (Wildcard) - DEBE IR AL FINAL
// Captura cualquier ruta no definida previamente y busca en dynamic_pages o route_mappings
$router->get('/{slug}', 'DynamicPageController@handleRequest');
