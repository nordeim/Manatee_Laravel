# after creating project files in steps 2.1 to 2.3

composer dump-autoload

./vendor/bin/sail artisan migrate

On branch main
Your branch is up to date with 'origin/main'.

Changes not staged for commit:
  (use "git add/rm <file>..." to update what will be committed)
  (use "git restore <file>..." to discard changes in working directory)
	deleted:    app/Models/User.php
	modified:   app/Providers/DomainServiceProvider.php
	modified:   config/auth.php
	modified:   database/factories/UserFactory.php
	deleted:    oo1
	modified:   vendor/composer/autoload_classmap.php
	modified:   vendor/composer/autoload_psr4.php
	modified:   vendor/composer/autoload_static.php

Untracked files:
  (use "git add <file>..." to include in what will be committed)
	1_deployment_guide_4.md
	app/Domain/Catalog/
	app/Domain/Inventory/
	app/Domain/Shared/ValueObjects/EmailAddress.php
	app/Domain/Shared/ValueObjects/FullName.php
	app/Domain/UserManagement/
	app/Infrastructure/
	database/factories/AddressFactory.php
	database/factories/CategoryFactory.php
	database/factories/InventoryMovementFactory.php
	database/factories/ProductAttributeFactory.php
	database/factories/ProductFactory.php
	database/factories/UserFactory.php.bak
	database/migrations/0001_01_02_000001_create_addresses_table.php
	database/migrations/0001_01_02_000002_create_categories_table.php
	database/migrations/0001_01_02_000003_create_products_table.php
	database/migrations/0001_01_02_000004_create_product_attributes_table.php
	database/migrations/0001_01_02_000005_create_inventory_movements_table.php

no changes added to commit (use "git add" and/or "git commit -a")
