# after creating project files in steps 2.1 to 2.3

composer dump-autoload

./vendor/bin/sail artisan migrate

	config/auth.php
	app/Domain/Shared/ValueObjects/EmailAddress.php
	app/Domain/Shared/ValueObjects/FullName.php
	app/Providers/DomainServiceProvider.php
	database/factories/UserFactory.php
	database/factories/AddressFactory.php
	database/factories/CategoryFactory.php
	database/factories/InventoryMovementFactory.php
	database/factories/ProductAttributeFactory.php
	database/factories/ProductFactory.php
	database/factories/AuditLogFactory.php
	database/factories/CartItemFactory.php
	database/factories/CouponFactory.php
	database/factories/EmailLogFactory.php
	database/factories/NewsletterSubscriberFactory.php
	database/factories/OrderFactory.php
	database/factories/OrderItemFactory.php
	database/factories/QuizResultFactory.php
	database/factories/TaxRateFactory.php
	database/factories/TaxRateHistoryFactory.php
	database/migrations/0001_01_02_000001_create_addresses_table.php
	database/migrations/0001_01_02_000002_create_categories_table.php
	database/migrations/0001_01_02_000003_create_products_table.php
	database/migrations/0001_01_02_000004_create_product_attributes_table.php
	database/migrations/0001_01_02_000005_create_inventory_movements_table.php
	database/migrations/0001_01_03_000001_create_coupons_table.php
	database/migrations/0001_01_03_000002_create_orders_table.php
	database/migrations/0001_01_03_000003_create_order_items_table.php
	database/migrations/0001_01_03_000004_create_cart_items_table.php
	database/migrations/0001_01_04_000001_create_quiz_results_table.php
	database/migrations/0001_01_04_000002_create_newsletter_subscribers_table.php
	database/migrations/0001_01_04_000003_create_audit_log_table.php
	database/migrations/0001_01_04_000004_create_email_log_table.php
	database/migrations/0001_01_04_000005_create_tax_rates_table.php
	database/migrations/0001_01_04_000006_create_tax_rate_history_table.php

    resources/js/app.js
    resources/views/frontend/home.blade.php
    routes/web.php
    app/Http/Controllers/Frontend/
    app/View/
    resources/js/testimonial-observer.js
    resources/views/components/frontend/
    resources/views/frontend/partials/
    resources/views/frontend/static/

